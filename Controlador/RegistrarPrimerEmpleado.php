<?php
session_start();
require_once 'conexion/conexion.php';
require_once 'Funciones/RegistrarHistorial.php';

// Verificar si ya existe un empleado registrado
try {
    $verificar = $pdo->query("SELECT COUNT(*) AS total FROM empleados");
    if ($verificar->fetch(PDO::FETCH_ASSOC)['total'] > 0) {
        header("Location: ../index.php");
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['errors'] = ['general' => "Error al verificar empleados: " . $e->getMessage()];
    $_SESSION['old_input'] = $_POST;
    header("Location: ../Vista/Vistas/RegistrarPrimerEmpleado.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $_SESSION['old_input'] = $_POST;

    // Captura y limpia los datos del formulario
    $cedula            = trim($_POST['cedula'] ?? '');
    $nombres           = trim($_POST['nombres'] ?? '');
    $apellidos         = trim($_POST['apellidos'] ?? '');
    $correo            = trim($_POST['correo'] ?? '');
    $direccion         = trim($_POST['direccion'] ?? '');
    $telefono          = trim($_POST['telefono'] ?? '');
    $contraseña        = trim($_POST['contraseña'] ?? '');
    $repetirContraseña = trim($_POST['repetircontraseña'] ?? '');
    $pin               = trim($_POST['pin'] ?? '');
    $cargo             = "Administrador";

    $errors = [];

    // Validación básica de campos obligatorios
    if (empty($cedula))             $errors['cedula'] = "La cédula es obligatoria";
    if (empty($nombres))            $errors['nombres'] = "Los nombres son obligatorios";
    if (empty($apellidos))          $errors['apellidos'] = "Los apellidos son obligatorios";
    if (empty($correo))             $errors['correo'] = "El correo es obligatorio";
    if (empty($direccion))          $errors['direccion'] = "La dirección es obligatoria";
    if (empty($telefono))           $errors['telefono'] = "El teléfono es obligatorio";
    if (empty($contraseña))         $errors['contraseña'] = "La contraseña es obligatoria";
    if (empty($repetirContraseña))  $errors['repetircontraseña'] = "Debe repetir la contraseña";
    if (empty($pin))                $errors['pin'] = "El PIN es obligatorio";

    // Si hay errores de campos vacíos, redirigir
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../Vista/Vistas/RegistrarPrimerEmpleado.php");
        exit;
    }

    // Validaciones específicas
    $regexLetras = '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/';

    if (!preg_match($regexLetras, $nombres) || strlen($nombres) < 3 || strlen($nombres) > 30) {
        $errors['nombres'] = "El nombre debe tener entre 3 y 30 letras";
    }

    if (!preg_match($regexLetras, $apellidos) || strlen($apellidos) < 3 || strlen($apellidos) > 30) {
        $errors['apellidos'] = "El apellido debe tener entre 3 y 30 letras";
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errors['correo'] = "Correo electrónico no válido";
    } else {
        $correo = strtolower($correo);
        $dominio_gmail = substr($correo, -10) === '@gmail.com';
        $dominio_hotmail = substr($correo, -12) === '@hotmail.com';
        if (!$dominio_gmail && !$dominio_hotmail) {
            $errors['correo'] = "El correo debe ser @gmail.com o @hotmail.com";
        }
    }

    if (strlen($direccion) < 3 || strlen($direccion) > 100) {
        $errors['direccion'] = "La dirección debe tener entre 3 y 100 caracteres";
    }

    if (!preg_match('/^04(12|14|16|24|26)\d{7}$/', $telefono)) {
        $errors['telefono'] = "Teléfono inválido. Debe tener 11 dígitos y comenzar con 0412, 0414, 0416, 0424 o 0426";
    }

    if (!ctype_digit($cedula) || strlen($cedula) < 7 || strlen($cedula) > 8 || (int)$cedula < 2000000) {
        $errors['cedula'] = "La cédula debe tener entre 7 y 8 dígitos y ser mayor a 2 millones";
    }

    if (!ctype_digit($pin) || strlen($pin) !== 4) {
        $errors['pin'] = "El PIN debe tener exactamente 4 números";
    }

    if (strlen($contraseña) < 8 || 
        !preg_match('/[A-Z]/', $contraseña) ||
        !preg_match('/[a-z]/', $contraseña) ||
        !preg_match('/\d/', $contraseña) ||
        !preg_match('/[\W_]/', $contraseña)) {
        $errors['contraseña'] = "La contraseña debe tener al menos 8 caracteres, incluyendo mayúscula, minúscula, número y carácter especial";
    }

    if ($contraseña !== $repetirContraseña) {
        $errors['repetircontraseña'] = "Las contraseñas no coinciden";
    }

    // Si hay errores, redirigir
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../Vista/Vistas/RegistrarPrimerEmpleado.php");
        exit;
    }

    // Hash de contraseña y pin
    $hashedPassword = password_hash($contraseña, PASSWORD_DEFAULT);
    $hashedPin = password_hash($pin, PASSWORD_DEFAULT);

    try {
        // Insertar en tabla usuarios
        $sql = "INSERT INTO usuarios (cedula, nombres, apellidos, cargo, contraseña, pin, estado)
                VALUES (?, ?, ?, ?, ?, ?, 1)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cedula, $nombres, $apellidos, $cargo, $hashedPassword, $hashedPin]);

        // Insertar en tabla empleados
        $sql = "INSERT INTO empleados (cedula, nombres, apellidos, correo, direccion, telefono, estado)
                VALUES (?, ?, ?, ?, ?, ?, 1)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cedula, $nombres, $apellidos, $correo, $direccion, $telefono]);

        // Registrar historial y limpiar sesión
        registrarHistorial($pdo, $cedula, "Se registró en el sistema como primer Administrador");
        unset($_SESSION['old_input'], $_SESSION['errors']);

        $_SESSION['mensaje'] = "Empleado registrado exitosamente";
        header("Location: ../index.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['errors'] = ['general' => "Error al registrar el empleado: " . $e->getMessage()];
        header("Location: ../Vista/Vistas/RegistrarPrimerEmpleado.php");
        exit;
    }
}

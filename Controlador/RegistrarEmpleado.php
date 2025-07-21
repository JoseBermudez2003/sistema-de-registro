<?php
session_start(); // Inicia la sesión para usar variables de sesión (como mensajes de error o éxito).
include_once 'Conexion/conexion.php';
include 'Funciones/RegistrarHistorial.php';
$mensaje = "";
$errors = [];

// Verificar que el método sea POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Guardar old input para repoblar formulario si hay errores
    $_SESSION['old_input'] = $_POST;

    // Captura y limpieza de datos
    $cedula             = trim($_POST['cedula'] ?? '');
    $nombres            = trim($_POST['nombres'] ?? '');
    $apellidos          = trim($_POST['apellidos'] ?? '');
    $correo             = trim($_POST['correo'] ?? '');
    $direccion          = trim($_POST['direccion'] ?? '');
    $telefono           = trim($_POST['telefono'] ?? '');
    $cargo              = trim($_POST['cargo'] ?? '');
    $contraseña         = trim($_POST['contraseña'] ?? '');
    $repetirContraseña  = trim($_POST['repetircontraseña'] ?? '');
    $pin                = trim($_POST['pin'] ?? '');

    // Validación campos vacíos
    if (empty($cedula)) $errors['cedula'] = "La cédula es obligatoria";
    if (empty($nombres)) $errors['nombres'] = "Los nombres son obligatorios";
    if (empty($apellidos)) $errors['apellidos'] = "Los apellidos son obligatorios";
    if (empty($correo)) $errors['correo'] = "El correo es obligatorio";
    if (empty($direccion)) $errors['direccion'] = "La dirección es obligatoria";
    if (empty($telefono)) $errors['telefono'] = "El teléfono es obligatorio";
    if (empty($cargo)) $errors['cargo'] = "El cargo es obligatorio";
    if (empty($contraseña)) $errors['contraseña'] = "La contraseña es obligatoria";
    if (empty($repetirContraseña)) $errors['repetircontraseña'] = "Debe repetir la contraseña";
    if (empty($pin)) $errors['pin'] = "El PIN es obligatorio";

    // Si hay errores, redirigir inmediatamente
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../Vista/Vistas/RegistrarEmpleado.php");
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
        $correoDominioGmail = substr($correo, -10);
        $correoDominioHotmail = substr($correo, -12);
        if ($correoDominioGmail !== '@gmail.com' && $correoDominioHotmail !== '@hotmail.com') {
            $errors['correo'] = "El correo debe ser @gmail.com o @hotmail.com";
        }
    }

    if (strlen($direccion) < 5 || strlen($direccion) > 100) {
        $errors['direccion'] = "La dirección debe tener entre 5 y 100 caracteres";
    }

    if (!preg_match('/^04(12|14|16|24|26)[0-9]{7}$/', $telefono)) {
        $errors['telefono'] = "El teléfono debe tener 11 dígitos y comenzar con 0412, 0414, 0416, 0424 o 0426";
    }

    if (!preg_match($regexLetras, $cargo) || strlen($cargo) < 3 || strlen($cargo) > 30) {
        $errors['cargo'] = "El cargo debe tener entre 3 y 30 letras";
    }

    if (!ctype_digit($cedula) || strlen($cedula) < 7 || strlen($cedula) > 8 || $cedula < 2000000) {
        $errors['cedula'] = "La cédula debe tener entre 7 y 8 dígitos y ser mayor a 2 millones";
    }

    if (!ctype_digit($pin) || strlen($pin) !== 4) {
        $errors['pin'] = "El PIN debe tener exactamente 4 números";
    }

    if (strlen($contraseña) < 8 || !preg_match('/[A-Z]/', $contraseña) || 
        !preg_match('/[a-z]/', $contraseña) || !preg_match('/[0-9]/', $contraseña) || 
        !preg_match('/[\W]/', $contraseña)) {
        $errors['contraseña'] = "La contraseña debe tener al menos 8 caracteres, incluyendo mayúscula, minúscula, número y carácter especial";
    }

    if ($contraseña !== $repetirContraseña) {
        $errors['repetircontraseña'] = "Las contraseñas no coinciden";
    }

    // Verificar cédula única solo si no hay error en cédula
    if (!isset($errors['cedula'])) {
        $sql = "SELECT cedula FROM usuarios WHERE cedula = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cedula]);
        if ($stmt->rowCount() > 0) {
            $errors['cedula'] = "La cédula ya está registrada";
        }
    }

    // Si hay errores tras validaciones, redirigir
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../Vista/Vistas/RegistrarEmpleado.php");
        exit;
    }

    // Proceder con registro si pasa validaciones
    $hashedPassword = password_hash($contraseña, PASSWORD_DEFAULT);
    $hashedPin = password_hash($pin, PASSWORD_DEFAULT);

    try {
        // Insertar en usuarios
        $sql = "INSERT INTO usuarios (cedula, nombres, apellidos, cargo, contraseña, pin, estado) 
                VALUES (?, ?, ?, ?, ?, ?, 1)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cedula, $nombres, $apellidos, $cargo, $hashedPassword, $hashedPin]);

        // Insertar en empleados
        $sql = "INSERT INTO empleados (cedula, nombres, apellidos, correo, direccion, telefono, estado) 
                VALUES (?, ?, ?, ?, ?, ?, 1)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cedula, $nombres, $apellidos, $correo, $direccion, $telefono]);

        // Registrar historial y limpiar sesiones
        registrarHistorial($pdo, $cedula, "Se registró en el sistema");
        unset($_SESSION['old_input'], $_SESSION['errors']);

        // Mensaje éxito
        //el error no se si esta aqui
         $_SESSION['mensaje'] = "Empleado registrado exitosamente.";
        header("Location: ../Vista/Vistas/RegistrarEmpleado.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['errors'] = ['general' => "Error al registrar el empleado: " . $e->getMessage()];
        header("Location: ../Vista/Vistas/RegistrarEmpleado.php");
        exit;
    }
}
?>

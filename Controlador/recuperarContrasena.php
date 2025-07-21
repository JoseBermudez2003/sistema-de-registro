<?php
if (session_status() == PHP_SESSION_NONE) session_start();

require 'Conexion/conexion.php';
include_once 'Funciones/RegistrarHistorial.php';

$errors = [];
$mensaje = "";

// Guardar old input para repoblar formulario
$_SESSION['old_input'] = $_POST;

$cedula = trim($_POST['cedula'] ?? '');
$pin_ingresado = trim($_POST['pin'] ?? '');

// Validación de campos obligatorios
if (empty($cedula)) {
    $errors['cedula'] = "La cédula es obligatoria";
}
if (empty($pin_ingresado)) {
    $errors['pin'] = "El PIN es obligatorio";
}

// Validar formato de cédula
if (!empty($cedula) && (!preg_match('/^[1-9][0-9]{6,7}$/', $cedula) || $cedula < 2000000)) {
    $errors['cedula'] = "La cédula debe tener entre 7 y 8 dígitos y ser mayor a 2 millones";
}

// Validar formato de PIN
if (!empty($pin_ingresado) && !preg_match('/^\d{4}$/', $pin_ingresado)) {
    $errors['pin'] = "El PIN debe tener 4 dígitos numéricos";
}

// Si hay errores, redirigir con los mensajes
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: ../Vista/vistas/RecuperarContrasena.php");
    exit;
}

try {
    // Activar errores PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Buscar usuario por cédula
    $stmt = $pdo->prepare("SELECT cedula, pin FROM usuarios WHERE cedula = ?");
    $stmt->execute([$cedula]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        if (password_verify($pin_ingresado, $usuario['pin'])) {
            // PIN correcto
            $_SESSION['cedula_validada'] = $usuario['cedula'];

            registrarHistorial($pdo, $usuario['cedula'], "Validó PIN para recuperación de contraseña");

            // Limpiar errores y old input
            unset($_SESSION['errors'], $_SESSION['old_input']);

            $_SESSION['mensaje'] = "PIN validado correctamente. Ahora puedes cambiar tu contraseña.";
            header("Location: ../Vista/vistas/EditarContrasena.php");
            exit;
        } else {
            // PIN incorrecto
            $errors['pin'] = "El PIN ingresado es incorrecto";
            registrarHistorial($pdo, $cedula, "Falló validación de PIN para recuperación de contraseña");
        }
    } else {
        // Cédula no registrada
        $errors['cedula'] = "La cédula ingresada no está registrada";
    }

    // Guardar errores y redirigir
    $_SESSION['errors'] = $errors;
    header("Location: ../Vista/vistas/RecuperarContrasena.php");
    exit;

} catch (PDOException $e) {
    error_log("Error en recuperación de contraseña: " . $e->getMessage());
    $_SESSION['errors']['general'] = "Ocurrió un error al procesar tu solicitud. Inténtalo más tarde.";
    header("Location: ../Vista/vistas/RecuperarContrasena.php");
    exit;
}

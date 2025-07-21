<?php
if (session_status() == PHP_SESSION_NONE) session_start();

require 'Conexion/conexion.php';
include_once 'Funciones/RegistrarHistorial.php';

$mensaje = "";

// Verificar que el usuario haya pasado la validación inicial
if (!isset($_SESSION['cedula_validada'])) {
    $_SESSION['mensaje'] = "Acceso no autorizado.";
    header("Location: ../Vista/vistas/RecuperarContrasena.php");
    exit;
}

// Guardar datos enviados para repoblar el formulario en caso de error
$_SESSION['old_input'] = $_POST;

// Obtener datos del formulario
$nuevaContraseña   = trim($_POST['nuevacontraseña'] ?? '');
$repetirContraseña = trim($_POST['repetircontraseña'] ?? '');
$cedula            = $_SESSION['cedula_validada'];

$errors = [];

// Validaciones
if (empty($nuevaContraseña)) {
    $errors['nuevacontraseña'] = "La nueva contraseña es obligatoria";
}
if (empty($repetirContraseña)) {
    $errors['repetircontraseña'] = "Debe repetir la nueva contraseña";
}
if (!empty($nuevaContraseña) && !empty($repetirContraseña) && $nuevaContraseña !== $repetirContraseña) {
    $errors['repetircontraseña'] = "Las contraseñas no coinciden";
}
if (!empty($nuevaContraseña) && (strlen($nuevaContraseña) < 8 || strlen($nuevaContraseña) > 12)) {
    $errors['nuevacontraseña'] = "La contraseña debe tener entre 8 y 12 caracteres";
}
if (!empty($nuevaContraseña) && preg_match('/\s/', $nuevaContraseña)) {
    $errors['nuevacontraseña'] = "La contraseña no debe contener espacios";
}
if (!empty($nuevaContraseña) &&
    (!preg_match('/[A-Z]/', $nuevaContraseña) || 
     !preg_match('/[a-z]/', $nuevaContraseña) || 
     !preg_match('/\d/', $nuevaContraseña) || 
     !preg_match('/[\W_]/', $nuevaContraseña))) {
    $errors['nuevacontraseña'] = "Debe contener mayúscula, minúscula, número y símbolo especial";
}

// Si hay errores, redirigir al formulario con mensajes
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: ../Vista/vistas/EditarContrasena.php");
    exit;
}

try {
    // Verificar que no sea igual a la contraseña anterior
    $stmt = $pdo->prepare("SELECT contraseña FROM usuarios WHERE cedula = ?");
    $stmt->execute([$cedula]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($nuevaContraseña, $usuario['contraseña'])) {
        $errors['nuevacontraseña'] = "La nueva contraseña no puede ser igual a la anterior";
        $_SESSION['errors'] = $errors;
        header("Location: ../Vista/vistas/EditarContrasena.php");
        exit;
    }

    // Actualizar contraseña en la base de datos
    $hashedPassword = password_hash($nuevaContraseña, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE usuarios SET contraseña = ? WHERE cedula = ?");
    $stmt->execute([$hashedPassword, $cedula]);

    if ($stmt->rowCount() > 0) {
        // Registrar acción en historial
        registrarHistorial($pdo, $cedula, "Actualizó su contraseña");

        // Limpiar variables de sesión relacionadas
        unset($_SESSION['cedula_validada'], $_SESSION['old_input'], $_SESSION['errors']);

        $_SESSION['mensaje'] = "Contraseña actualizada con éxito.";
        header("Location: ../");
        exit;
    } else {
        $_SESSION['mensaje'] = "No se pudo actualizar la contraseña. Intenta de nuevo.";
        header("Location: ../Vista/vistas/EditarContrasena.php");
        exit;
    }

} catch (PDOException $e) {
    error_log("Error al actualizar contraseña: " . $e->getMessage());
    $_SESSION['errors']['general'] = "Ocurrió un error en el sistema. Intenta más tarde.";
    header("Location: ../Vista/vistas/EditarContrasena.php");
    exit;
}

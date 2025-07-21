

<?php
// Este código verifica que el usuario exista en la sesión y tenga los permisos adecuados

// Verificar si la sesión no está activa y luego iniciarla
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si la sesión está activa y el usuario tiene los permisos necesarios
if (!isset($_SESSION['cedula']) || 
    !isset($_SESSION['cargo']) || 
    !isset($_SESSION['estado']) || 
    $_SESSION['cargo'] != "Administrador" || 
    $_SESSION['estado'] != "1") {
    // Si no cumple los requisitos, redirigir al login
    header('Location: Index.php');
    exit();
}

// Obtener los mensajes después de realizar una acción del sistema
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']); // Limpiar el mensaje de la sesión
} else {
    $mensaje = "";
}  

// Asignar variables de sesión con verificación (evita warnings si no existen)
$nombres = $_SESSION['nombres'] ?? '';
$apellidos = $_SESSION['apellidos'] ?? '';
$cedula = $_SESSION['cedula'] ?? '';
$cargo = $_SESSION['cargo'] ?? '';
?>
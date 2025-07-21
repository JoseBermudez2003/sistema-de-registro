<?php
// Verificar el estado de la sesión actual
// PHP_SESSION_NONE significa que las sesiones están habilitadas pero no hay ninguna activa
if (session_status() == PHP_SESSION_NONE) {
    // Iniciar una nueva sesión o reanudar la existente
    session_start();
}

// Verificación de seguridad para el acceso
// Comprueba si no está definida la cédula en sesión O no está definido el estado O el estado no es "1" (activo)
if (!isset($_SESSION['cedula']) || !isset($_SESSION['estado']) || $_SESSION['estado'] != "1") {
    // Redireccionar al directorio raíz (generalmente la página de login) si no cumple los requisitos
    header('Location: ../../');
    // Terminar la ejecución del script inmediatamente después de la redirección
    exit();
}

// Manejo de mensajes del sistema (feedback para el usuario)
// Verifica si existe un mensaje almacenado en la variable de sesión
if (isset($_SESSION['mensaje'])) {
    // Asigna el mensaje a una variable local para su uso en la vista
    $mensaje = $_SESSION['mensaje'];
    // Elimina el mensaje de la sesión para que no persista después de mostrarse
    unset($_SESSION['mensaje']);
} else {
    // Si no hay mensaje, asigna una cadena vacía para evitar errores
    $mensaje = "";
}

// Asignación de variables de sesión a variables locales con operador de fusión null (??)
// Esto evita errores si las variables no están definidas en la sesión

// Asigna el nombre del usuario o cadena vacía si no existe
$nombres = $_SESSION['nombres'] ?? '';
// Asigna el apellido del usuario o cadena vacía si no existe
$apellidos = $_SESSION['apellidos'] ?? '';
// Asigna la cédula del usuario o cadena vacía si no existe
$cedula = $_SESSION['cedula'] ?? '';
// Asigna el cargo del usuario o cadena vacía si no existe
$cargo = $_SESSION['cargo'] ?? '';
?>
<?php
// Conexión a la base de datos
include 'Conexion/Conexion.php';

// Inicializa variables
$historiales = [];
$mensaje = '';

try {
    // Consulta SQL explícita para evitar conflictos de nombres y obtener solo los campos necesarios
    $state = $pdo->prepare("
        SELECT 
            historial.id,
            historial.accion,
            historial.fecha,
            empleados.nombres AS empleado_nombre,
            empleados.apellidos AS empleado_apellido
        FROM historial
        INNER JOIN empleados ON historial.cedula_empleado = empleados.cedula
        WHERE historial.estado = 1
        ORDER BY historial.fecha DESC
    ");

    // Ejecuta la consulta
    $state->execute();

    // Almacena todos los resultados como arreglo asociativo
    $historiales = $state->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si hay un error, guarda mensaje de error
    $mensaje = "Error al obtener el historial: " . $e->getMessage();
}

// Cierra la conexión
$pdo = null;
?>

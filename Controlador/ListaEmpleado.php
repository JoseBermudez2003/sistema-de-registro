<?php
// Incluye el archivo de conexión a la base de datos
include_once 'Conexion/conexion.php'; 

try {
    // Consulta a la tabla empleados
    $statement = $pdo->prepare("SELECT cedula, nombres, apellidos, correo, direccion, telefono 
    FROM empleados WHERE estado = 1");
    $statement->execute();
    $empleados = $statement->fetchAll(PDO::FETCH_ASSOC); 
    // Usamos FETCH_ASSOC para acceder a los datos con claves asociativas
} catch (PDOException $e) {
    $empleados = [];
    $mensaje = "Error al obtener los empleados: " . $e->getMessage();
}

// Cerrar la conexión a la base de datos
$pdo = null;
?>

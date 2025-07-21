<?php
// Este script se conecta a la base de datos y obtiene todos los informes activos (estado = 1) ordenados por fecha descendente (los más recientes primero). Si ocurre un error en la consulta, se captura la excepción y se almacena un mensaje de error.

include_once 'Conexion/conexion.php'; 

try {
    // Prepara la consulta SQL para seleccionar todos los informes activos, ordenados por fecha descendente.
    $statement = $pdo->prepare("SELECT * FROM informes WHERE estado = 1 ORDER BY fecha DESC");

    // Ejecuta la consulta preparada.
    $statement->execute();

    // Obtiene todos los resultados como un arreglo asociativo.
    $informes = $statement->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si ocurre un error durante la ejecución, se asigna un arreglo vacío y un mensaje de error.
    $informes = [];
    $mensaje = "Error al obtener los informes: " . $e->getMessage();
}

// Cierra la conexión a la base de datos.
$pdo = null;
?>

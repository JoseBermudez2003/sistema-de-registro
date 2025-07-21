<?php
session_start();
include 'Conexion/Conexion.php';
include 'Funciones/RegistrarHistorial.php';

if (!empty($_POST["id"])) {
    $id = $_POST["id"];

    try {
        // Iniciar la transacción
        $pdo->beginTransaction();

        // Preparar la consulta para actualizar el estado del informe
        $stmt = $pdo->prepare("UPDATE informes SET estado = 0 WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Registrar la acción en el historial
        registrarHistorial($pdo, $_SESSION['cedula'], "Eliminó un informe con el ID $id");

        // Confirmar la transacción
        $pdo->commit();

        // Establecer mensaje de éxito
        $_SESSION['mensaje'] = "Informe eliminado correctamente.";
    } catch (PDOException $e) {
        // En caso de error, revertir la transacción
        $pdo->rollBack();
        
        // Establecer mensaje de error
        $_SESSION['mensaje'] = "Error al eliminar el informe: " . $e->getMessage();
    }
} else {
    // Si no se proporciona el ID, establecer un mensaje de error
    $_SESSION['mensaje'] = "ID no proporcionado para la eliminación.";
}

// Redirigir a la página de informes
header("Location: ../Vista/Vistas/verinformes.php");
exit();
?>

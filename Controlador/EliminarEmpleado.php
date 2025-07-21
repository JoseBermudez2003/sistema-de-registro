<?php
// Inicia la sesión para poder usar variables de sesión
session_start();

// Incluye la conexión a la base de datos
require 'conexion/conexion.php';

// Incluye la función para registrar en el historial
include 'Funciones/RegistrarHistorial.php';

// Verifica si se recibió una petición POST y que el campo 'cedula' no esté vacío
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['cedula'])) {
    
    // Limpia espacios en blanco de la cédula recibida
    $cedula_empleado = trim($_POST['cedula']);

    // Verifica si el usuario está intentando eliminarse a sí mismo
    if (isset($_SESSION['cedula']) && $_SESSION['cedula'] == $cedula_empleado) {
        $_SESSION['mensaje'] = "No puedes eliminar tu propio usuario.";
        header("Location: ../Vista/Vistas/AdministrarEmpleado.php");
        exit();
    }

    // Verifica que la cédula sea solo numérica (previene inyecciones u otros caracteres maliciosos)
    if (!preg_match('/^\d+$/', $cedula_empleado)) {
        $_SESSION['mensaje'] = "Cédula inválida.";
        header("Location: ../Vista/Vistas/AdministrarEmpleado.php");
        exit();
    }
    
    try {
        // Inicia una transacción en la base de datos
        $pdo->beginTransaction();

        // Consulta los datos del empleado para usarlos en el historial
        $sql = "SELECT cedula, nombres, apellidos FROM empleados WHERE cedula = :cedula";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':cedula' => $cedula_empleado]);
        $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si no se encontró el empleado, cancela la transacción y muestra un mensaje
        if (!$empleado) {
            $_SESSION['mensaje'] = "Empleado no encontrado.";
            $pdo->rollBack();
            header("Location: ../Vista/Vistas/AdministrarEmpleado.php");
            exit();
        }

        // Marca al empleado como inactivo (eliminación lógica)
        $sqlEmpleados = "UPDATE empleados SET estado = 0 WHERE cedula = :cedula";
        $stmtEmpleados = $pdo->prepare($sqlEmpleados);
        $stmtEmpleados->execute([':cedula' => $cedula_empleado]);

        // Marca al usuario relacionado como inactivo (eliminación lógica)
        $sqlUsuarios = "UPDATE usuarios SET estado = 0 WHERE cedula = :cedula";
        $stmtUsuarios = $pdo->prepare($sqlUsuarios);
        $stmtUsuarios->execute([':cedula' => $cedula_empleado]);

        // Confirma la transacción
        $pdo->commit();

        // Prepara el nombre completo del empleado eliminado
        $nombreCompleto = "{$empleado['nombres']} {$empleado['apellidos']}";

        // Registra la acción en el historial
        registrarHistorial($pdo, $_SESSION['cedula'], "Eliminó al empleado: $nombreCompleto - Cédula: {$empleado['cedula']}");

        // Mensaje de éxito
        $_SESSION['mensaje'] = "Empleado eliminado exitosamente.";
        header("Location: ../Vista/Vistas/AdministrarEmpleado.php");
        exit();

    } catch (PDOException $e) {
        // Si ocurre un error, revierte la transacción
        $pdo->rollBack();
        $_SESSION['mensaje'] = "Error al eliminar: " . $e->getMessage();
        header("Location: ../Vista/Vistas/AdministrarEmpleado.php");
        exit();
    }

} else {
    // Si no se recibió una cédula válida, muestra un mensaje de error
    $_SESSION['mensaje'] = "No se recibió una cédula válida.";
    header("Location: ../Vista/Vistas/AdministrarEmpleado.php");
    exit();
}

// Cierra la conexión a la base de datos
$pdo = null;
?>

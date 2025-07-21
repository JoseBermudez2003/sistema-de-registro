<?php
// Inicia la sesión para manejar mensajes y datos del usuario
session_start();

// Conecta con la base de datos
include_once 'Conexion/conexion.php';

// Incluye la función para registrar acciones en el historial
include 'Funciones/RegistrarHistorial.php';

// Verifica que la solicitud sea POST y que se haya enviado una cédula
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cedula'])) {
    // Limpia espacios de la cédula
    $cedula = trim($_POST['cedula']);

    // Valida que la cédula sea un número y no esté vacía
    if (!is_numeric($cedula) || empty($cedula)) {
        $_SESSION['mensaje'] = "Cédula de beneficiario no válida.";
        header("Location: ../Vista/Vistas/listaClientes.php");
        exit();
    }

    try {
        // Consulta para obtener los datos del cliente antes de eliminarlo
        $sql = "SELECT cedula, nombres, apellidos FROM clientes WHERE cedula = :cedula";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':cedula' => $cedula]);

        // Almacena el resultado como array asociativo
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente) {
            // Actualiza el estado del cliente a 0 (eliminado lógicamente)
            $sqlUpdateClientes = "UPDATE clientes SET estado = 0 WHERE cedula = :cedula";
            $stmtUpdateClientes = $pdo->prepare($sqlUpdateClientes);
            $stmtUpdateClientes->execute([':cedula' => $cedula]);

            // También actualiza el estado de todos los casos relacionados a 0
            // Ahora también actualiza los campos `fecha_atendido` y `fecha_creado` de los casos
            $sqlUpdateCasos = "UPDATE casos SET estado = 0, fecha_atendido = NOW() WHERE cedula_cliente = :cedula";
            $stmtUpdateCasos = $pdo->prepare($sqlUpdateCasos);
            $stmtUpdateCasos->execute([':cedula' => $cedula]);

            // Registra esta acción en el historial con los datos del cliente
            registrarHistorial(
                $pdo,
                $_SESSION['cedula'],
                "Eliminó al beneficiario: {$cliente['nombres']} {$cliente['apellidos']} - Cédula: {$cliente['cedula']}"
            );

            // Verifica si alguna fila fue realmente modificada
            if ($stmtUpdateClientes->rowCount() > 0 || $stmtUpdateCasos->rowCount() > 0) {
                $_SESSION['mensaje'] = "El beneficiario y sus casos fueron marcados como eliminados correctamente.";
            } else {
                $_SESSION['mensaje'] = "No se realizaron cambios, el beneficiario ya pudo haber sido eliminado.";
            }
        } else {
            // Si no se encontró el cliente
            $_SESSION['mensaje'] = "No se encontró un beneficiario con esa cédula.";
        }
    } catch (PDOException $e) {
        // En caso de error de base de datos
        $_SESSION['mensaje'] = "Error al eliminar beneficiario: " . $e->getMessage();
    }

    // Redirige a la vista de lista de clientes
    header("Location: ../Vista/Vistas/listaClientes.php");
    exit();
}
?>

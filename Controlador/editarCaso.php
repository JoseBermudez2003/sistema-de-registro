<?php
// Inicia sesión para manejo de mensajes y datos de usuario
session_start();

// Incluye la conexión PDO y la función para registrar historial de cambios
include_once 'Conexion/conexion.php';
include 'Funciones/RegistrarHistorial.php';

// Verifica que el método de la solicitud sea POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtiene y limpia los datos enviados desde el formulario
    $id = $_POST['id'] ?? null;
    $motivo = trim($_POST['motivo'] ?? '');
    $tipo_caso = trim($_POST['tipo_caso'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $atendido = trim($_POST['atendido'] ?? '');
    $fecha_atendido = $_POST['fecha_atendido'] ?? null;
    $resumen_atencion = trim($_POST['resumen_atencion'] ?? '');
    $atendido_por = $_POST['atendido_por'] ?? null;

    // Valida que los campos obligatorios estén completos
    if (!$id || !$motivo || !$tipo_caso || !$descripcion || !$atendido) {
        $_SESSION['mensaje'] = "Todos los campos obligatorios deben completarse.";
        header("Location: ../Vista/Vistas/listaCasos.php");
        exit();
    }

    // Validación adicional si el caso está marcado como atendido
    if ($atendido === "Si") {
        if (!$fecha_atendido || !$resumen_atencion || !$atendido_por) {
            $_SESSION['mensaje'] = "Para casos atendidos, debe completar fecha, resumen y responsable.";
            header("Location: ../Vista/Vistas/listaCasos.php");
            exit();
        }
    }

    try {
        // Inicia una transacción para garantizar integridad de datos
        $pdo->beginTransaction();

        // Consulta para obtener el estado actual del caso y su fecha de creación
        $sqlCheck = "SELECT atendido, fecha_creado FROM casos WHERE id = :id";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $id]);
        $casoActual = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        
        $anterior = $casoActual['atendido'];
        $fecha_creado = $casoActual['fecha_creado'];

        // Validar que la fecha de atención no sea anterior a la fecha de creación
        if ($atendido === "Si" && $fecha_atendido) {
            if (strtotime($fecha_atendido) < strtotime($fecha_creado)) {
                $_SESSION['mensaje'] = "No se actualizó el caso, ya que la 
                fecha de atención no puede ser menora la fecha de creación (" . $fecha_creado . ").";
                header("Location: ../Vista/Vistas/listaCasos.php");
                exit();
            }
        }

        // Determina qué campos actualizar según el estado
        if ($atendido === "Si") {
            $sql = "UPDATE casos SET 
                        motivo = :motivo,
                        tipo_caso = :tipo_caso,
                        descripcion = :descripcion,
                        atendido = :atendido,
                        fecha_atendido = :fecha_atendido,
                        resumen_atencion = :resumen_atencion,
                        atendido_por = :atendido_por
                    WHERE id = :id";
            
            $params = [
                ':motivo' => $motivo,
                ':tipo_caso' => $tipo_caso,
                ':descripcion' => $descripcion,
                ':atendido' => $atendido,
                ':fecha_atendido' => $fecha_atendido,
                ':resumen_atencion' => $resumen_atencion,
                ':atendido_por' => $atendido_por,
                ':id' => $id
            ];
        } else {
            // Si no está atendido, limpiamos los campos de atención
            $sql = "UPDATE casos SET 
                        motivo = :motivo,
                        tipo_caso = :tipo_caso,
                        descripcion = :descripcion,
                        atendido = :atendido,
                        fecha_atendido = NULL,
                        resumen_atencion = NULL,
                        atendido_por = NULL
                    WHERE id = :id";
            
            $params = [
                ':motivo' => $motivo,
                ':tipo_caso' => $tipo_caso,
                ':descripcion' => $descripcion,
                ':atendido' => $atendido,
                ':id' => $id
            ];
        }

        // Prepara y ejecuta la consulta de actualización
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Registra la acción en el historial con la cédula del usuario actual
        registrarHistorial($pdo, $_SESSION['cedula'], "Editó el caso con ID: $id");

        // Confirma los cambios en la base de datos
        $pdo->commit();

        // Mensaje de éxito para mostrar al usuario
        $_SESSION['mensaje'] = "Caso actualizado correctamente.";

    } catch (PDOException $e) {
        // Si ocurre un error, revierte la transacción y guarda el mensaje de error
        $pdo->rollBack();
        $_SESSION['mensaje'] = "Error al actualizar el caso: " . $e->getMessage();
    }

    // Redirige nuevamente a la lista de casos
    header("Location: ../Vista/Vistas/listaCasos.php");
    exit();
}
?>
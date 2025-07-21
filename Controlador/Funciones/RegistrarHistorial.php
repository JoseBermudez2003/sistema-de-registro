<?php

function registrarHistorial($pdo, $cedula_empleado, $accion) {
    try {
        $consultaHistorial = "INSERT INTO historial (cedula_empleado, accion, fecha, estado) 
                              VALUES (:cedula, :accion, NOW(), 1)";
        $stmt = $pdo->prepare($consultaHistorial);
        $stmt->execute([
            ':cedula' => $cedula_empleado,
            ':accion' => $accion
        ]);
    } catch (PDOException $e) {
        error_log("Error al registrar historial: " . $e->getMessage());
    }
}

?>
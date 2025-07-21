<?php
include "../../controlador/Conexion/conexion.php";

/**
 * Obtiene conteos de casos registrados, atendidos y no atendidos según un filtro temporal.
 */
function obtenerDatosPorFiltro($pdo, $filtro) {
    // Validar filtro
    if (!in_array($filtro, ['hoy', 'semana', 'mes', 'todos'])) {
        throw new InvalidArgumentException("Filtro no válido: " . $filtro);
    }

    $datos = ['registrados' => 0, 'atendidos' => 0, 'no_atendidos' => 0];

    try {
        // Base de la consulta
        $sql = "SELECT 
                COUNT(*) as registrados,
                SUM(atendido = 'Si') as atendidos,
                SUM(atendido = 'No') as no_atendidos
                FROM casos WHERE estado = 1";

        // Agregar condiciones según el filtro
        switch ($filtro) {
            case 'hoy':
                $sql .= " AND DATE(fecha_creado) = CURDATE()";
                break;
            case 'semana':
                $sql .= " AND YEARWEEK(fecha_creado, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'mes':
                $sql .= " AND MONTH(fecha_creado) = MONTH(CURDATE()) AND YEAR(fecha_creado) = YEAR(CURDATE())";
                break;
            case 'todos':
                // No se agregan condiciones adicionales
                break;
        }

        $stmt = $pdo->query($sql);
        if ($stmt) {
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resultado) {
                $datos = [
                    'registrados' => (int)$resultado['registrados'],
                    'atendidos' => (int)$resultado['atendidos'],
                    'no_atendidos' => (int)$resultado['no_atendidos']
                ];
            }
        }
    } catch (PDOException $e) {
        error_log("Error al obtener datos ($filtro): " . $e->getMessage());
        throw new RuntimeException("Error al obtener datos estadísticos");
    }

    return $datos;
}

try {
    $datos = [
        'hoy' => obtenerDatosPorFiltro($pdo, 'hoy'),
        'semana' => obtenerDatosPorFiltro($pdo, 'semana'),
        'mes' => obtenerDatosPorFiltro($pdo, 'mes'),
        'todos' => obtenerDatosPorFiltro($pdo, 'todos')
    ];
} catch (Exception $e) {
    error_log("Error general en CasosGraficas: " . $e->getMessage());
    $datos = [
        'hoy' => ['registrados' => 0, 'atendidos' => 0, 'no_atendidos' => 0],
        'semana' => ['registrados' => 0, 'atendidos' => 0, 'no_atendidos' => 0],
        'mes' => ['registrados' => 0, 'atendidos' => 0, 'no_atendidos' => 0],
        'todos' => ['registrados' => 0, 'atendidos' => 0, 'no_atendidos' => 0]
    ];
}
?>

<script>
    const datosGraficas = <?php echo json_encode($datos); ?>;
</script>
<?php 
    // Incluye el archivo de conexión a la base de datos (crea el objeto $pdo)
    include 'Conexion/Conexion.php';

    // Prepara la consulta SQL para obtener todos los casos activos (estado = 1)
    // Se realiza un INNER JOIN con las tablas clientes y empleados para obtener los datos relacionados
    $state = $pdo->prepare("
        SELECT *, 
            empleados.nombres as empleado_nombre,
            empleados.apellidos as empleado_apellido,
            clientes.nombres as clientes_nombre,
            clientes.apellidos as clientes_apellido,
            casos.motivo as caso_motivo,
            casos.descripcion as caso_descrip,
            casos.atendido as caso_atentido
        FROM casos 
        INNER JOIN clientes ON casos.cedula_cliente = clientes.cedula 
        INNER JOIN empleados ON empleados.cedula = casos.cedula_empleado
        WHERE casos.estado = 1
        ORDER BY casos.fecha_creado DESC
    ");

    // Ejecuta la consulta preparada
    $state->execute();

    // Obtiene todos los resultados en un arreglo asociativo
    $casos = $state->fetchAll();

    // Cierra la conexión a la base de datos
    $pdo = null; 
?>

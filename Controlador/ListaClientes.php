<?php 
//conexion a la bdd
    include 'Conexion/Conexion.php';

    //se seleccionan todos los clientes activos, es decir estado = 1
    $state = $pdo->prepare("SELECT * FROM clientes WHERE estado = 1");
    $state->execute();
    //se ejecuta y se obtienen los datos
    $clientes = $state->fetchAll();
    $pdo = null; 
?>

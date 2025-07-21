<?php

// Iniciar la sesión para mantener la sesión activa entre páginas
// Verificar si la sesión no está activa y luego iniciarla
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$mensaje = "";  // Variable para almacenar mensajes de error o éxito


//define la cadena de conexion con la bdd, al escribir mysql decimos que estamos usando ese gestor
$link = 'mysql:host=localhost;dbname=regis_corvi';
$usuario =  'root'; //nombre de usuario para acceder a la bdd, en xampp este es el user definido
$pass = '';//es la contraseña, y en entornos locales suele estar vacia


try { //esto se usa para manehar errores en la conexion, si es exitosa se ejecuta, sino manda error
    
    $pdo = NEW PDO($link,$usuario,$pass);//objeto pdo creado, esto representa a la bdd
    $pdo->exec("set names utf8mb4");

}

catch (PDOException $e) { //captura cualquier error en la bdd, pdoexception maneja errores en la bdd
    $_SESSION['mensaje'] = "¡Error!: " . $e->getMessage() . "<br/>";
    die(); //finaliza la ejecucion del script en caso de fallar
}

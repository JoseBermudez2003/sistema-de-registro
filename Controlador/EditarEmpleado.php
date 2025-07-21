<?php
// Inicia la sesión para poder utilizar variables de sesión (como mensajes)
session_start();

// Incluye el archivo de conexión a la base de datos
include_once 'Conexion/conexion.php'; 

// Incluye la función para registrar acciones en el historial
include 'Funciones/RegistrarHistorial.php';

// Variable para posibles mensajes (no se usa directamente en este código)
$mensaje = "";

// Verifica que el formulario se haya enviado mediante POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Recoge y limpia los datos enviados desde el formulario
    $cedula = trim($_POST['cedula']);
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $correo = trim($_POST['correo']);
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);

    // Validación: asegúrate de que ningún campo esté vacío
    if (
        empty($_POST['cedula']) || empty($_POST['nombres']) || empty($_POST['apellidos']) ||
        empty($_POST['correo']) || empty($_POST['direccion']) || empty($_POST['telefono'])
    ) {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios.";
        header("Location: ../Vista/Vistas/AdministrarEmpleado.php");
        exit();
    }

    // Validación del correo válido
    if (!filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['mensaje'] = "El correo electrónico no es válido.";
        header("Location: ../Vista/Vistas/AdministrarEmpleado.php");
        exit();
    }

    // Validación para los correos
    $correo = strtolower($correo); // Convertir el correo a minúsculas para comparación
    $correoDominioGmail = substr($correo, -10);  // Obtiene los últimos 10 caracteres del correo
    $correoDominioHotmail = substr($correo, -12); // Obtiene los últimos 12 caracteres del correo
    if ($correoDominioGmail !== '@gmail.com' && $correoDominioHotmail !== '@hotmail.com') {
        $_SESSION['mensaje'] = "El correo debe ser gmail/hotmail y tener un formato válido.";
        header("Location: ../Vista/Vistas/AdministrarEmpleado.php");
        exit();
    }

    // Validación: el teléfono debe tener al menos 11 dígitos
    if (strlen($_POST['telefono']) < 11) {
        $_SESSION['mensaje'] = "El teléfono debe tener al menos 11 dígitos.";
        header("Location: ../Vista/Vistas/AdministrarEmpleado.php");
        exit();
    }

    // Validación: nombre y apellido deben tener al menos 3 caracteres
    if (strlen($_POST['nombres']) < 3 || strlen($_POST['apellidos']) < 3) {
        $_SESSION['mensaje'] = "El nombre y el apellido deben tener al menos 3 caracteres.";
        header("Location: ../Vista/Vistas/AdministrarEmpleado.php");
        exit();
    }

    // Regex para validar solo letras y espacios
    $regexLetras = '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/';

    // Validación de nombres
    if (!preg_match($regexLetras, $nombres) || strlen($nombres) < 3 || strlen($nombres) > 30) {
        $_SESSION['mensaje'] = "El nombre debe tener entre 3 y 30 letras.";
        header("Location: ../Vista/Vistas/AdministrarEmpleado.php");
        exit();
    }

    // Validación de apellidos
    if (!preg_match($regexLetras, $apellidos) || strlen($apellidos) < 3 || strlen($apellidos) > 30) {
        $_SESSION['mensaje'] = "El apellido debe tener entre 3 y 30 letras.";
        header("Location: ../Vista/Vistas/AdministrarEmpleado.php");
        exit();
    }

    //  Validación 4: Dirección con al menos 5 caracteres
    if (strlen($direccion) < 5 || strlen($direccion) > 100) {
        $_SESSION['mensaje'] = "La dirección debe tener entre 5 y 100 caracteres";
        header("Location: ../Vista/Vistas/AdministrarEmpleado.php");
        exit();
    }

    // Validación de la cédula (debe tener entre 6 y 8 dígitos, ser numérica y mayor a 2 millones)
    if (!ctype_digit($cedula) || strlen($cedula) < 7 || strlen($cedula) > 8 || $cedula < 2000000) {
        $_SESSION['mensaje'] = "La cédula debe tener entre 7 y 8 dígitos, y ser mayor a 2 millones.";
        header("Location: ../Vista/Vistas/AdministrarEmpleado.php");
        exit();
    }

    // Validación del teléfono (debe tener 11 dígitos y comenzar con 0412, 0414, 0416, 0424 o 0426)
    if (!preg_match('/^04(12|14|16|24|26)[0-9]{7}$/', $telefono)) {
        $_SESSION['mensaje'] = "El teléfono debe tener 11 dígitos y comenzar con 0412, 0414, 0416, 0424 o 0426.";
        header("Location: ../Vista/Vistas/AdministrarEmpleado.php");
        exit();
    }

    try {
        // Inicia una transacción para agrupar cambios
        $pdo->beginTransaction();

        // Actualiza la tabla "usuarios" con los nuevos nombres y apellidos
        $usuariosUpdate = "UPDATE usuarios SET 
            nombres = :nombres, 
            apellidos = :apellidos 
            WHERE cedula = :cedula";
        $stmtUsuarios = $pdo->prepare($usuariosUpdate);
        $stmtUsuarios->execute([
            ':nombres' => $nombres,
            ':apellidos' => $apellidos,
            ':cedula' => $cedula
        ]);

        // Actualiza la tabla "empleados" con todos los datos
        $empleadosUpdate = "UPDATE empleados SET 
            nombres = :nombres, 
            apellidos = :apellidos, 
            correo = :correo, 
            direccion = :direccion, 
            telefono = :telefono
            WHERE cedula = :cedula";
        $stmtEmpleados = $pdo->prepare($empleadosUpdate);
        $stmtEmpleados->execute([
            ':nombres' => $nombres,
            ':apellidos' => $apellidos,
            ':correo' => $correo,
            ':direccion' => $direccion,
            ':telefono' => $telefono,
            ':cedula' => $cedula
        ]);

        // Si todo fue exitoso, se confirma la transacción
        $pdo->commit();

        // Se registra la acción en el historial
        registrarHistorial($pdo, $_SESSION['cedula'], "Modificó al Empleado: $nombres $apellidos");

        // Se guarda un mensaje de éxito
        $_SESSION['mensaje'] = "Empleado editado exitosamente.";

        // Redirige a la página de administración de empleados
        header("Location: ../Vista/Vistas/AdministrarEmpleado.php");
        exit();

    } catch (PDOException $e) {
        // Si ocurre un error, se revierte la transacción
        $pdo->rollBack();

        // Se guarda el mensaje de error
        $_SESSION['mensaje'] = "Error al actualizar: " . $e->getMessage();

        // Redirige a la misma página con el mensaje
        header("Location: ../Vista/Vistas/AdministrarEmpleado.php");
        exit();
    }
}
?>

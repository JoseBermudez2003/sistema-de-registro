<?php
session_start(); // Inicia la sesión para usar variables de sesión
date_default_timezone_set('America/Caracas'); //esto esta aqui por seguridad, para evitar errores
//mentira, esto esta aqui porque cuando se hace un registro de noche lo toma como si fuera el dia siguiente
include_once 'Conexion/conexion.php';
include_once 'Funciones/RegistrarHistorial.php';

$errors = [];
$mensaje = "";

// Activar errores PDO
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Guardar datos anteriores para repoblar el formulario
    $_SESSION['old_input'] = $_POST;

    // Datos del formulario
    $cedula_empleado = $_SESSION['cedula'] ?? null;
    $cedula = trim($_POST['cedula'] ?? '');
    $nombres = trim($_POST['nombres'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $motivo = trim($_POST['motivo'] ?? '');
    $tipo_caso = trim($_POST['tipo'] ?? 'General'); // Tomar el tipo del select
    $descripcion = trim($_POST['descripcion'] ?? '');

    // Valores por defecto para el caso
    $estado = 1;
    $atendido = "No";
    $fecha_creado = date('Y-m-d'); // Fecha actual
    $fecha_atendido = null;
    $resumen_atencion = null;
    $atendido_por = null;

    // Expresiones regulares
    $regexLetras = '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/';
    $regexDireccion = '/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,#-]+$/';
    $regexMotivo = '/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,-]+$/';

    // Validaciones
    if (!preg_match($regexLetras, $nombres) || strlen($nombres) < 3 || strlen($nombres) > 50) {
        $errors['nombres'] = "El nombre debe tener entre 3 y 50 letras";
    }

    if (!preg_match($regexLetras, $apellidos) || strlen($apellidos) < 3 || strlen($apellidos) > 50) {
        $errors['apellidos'] = "El apellido debe tener entre 3 y 50 letras";
    }

    if (!ctype_digit($cedula) || strlen($cedula) < 7 || strlen($cedula) > 8 || $cedula < 2000000) {
        $errors['cedula'] = "La cédula debe tener entre 7 y 8 dígitos, y ser mayor a 2 millones";
    }

    if (!preg_match('/^04(12|14|16|24|26)[0-9]{7}$/', $telefono)) {
        $errors['telefono'] = "El teléfono debe tener 11 dígitos y comenzar con 0412, 0414, 0416, 0424 o 0426";
    }

    if (!preg_match($regexDireccion, $direccion) || strlen($direccion) < 5 || strlen($direccion) > 100) {
        $errors['direccion'] = "La dirección debe tener entre 5 y 100 caracteres (letras, números y algunos símbolos)";
    }

    if (!preg_match($regexMotivo, $motivo) || strlen($motivo) < 5 || strlen($motivo) > 50) {
        $errors['motivo'] = "El motivo debe tener entre 5 y 50 caracteres (letras, números y algunos símbolos)";
    }

    if (!in_array($tipo_caso, ['Crítico', 'Grande', 'Normal', 'Bajo', 'General'])) {
        $errors['tipo'] = "Tipo de caso no válido";
    }

    if (empty($descripcion) || strlen($descripcion) < 5 || strlen($descripcion) > 200) {
        $errors['descripcion'] = "La descripción debe tener entre 5 y 200 caracteres";
    }

    // Si hay errores, redirigir
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../Vista/Vistas/RegistrarCliente.php");
        exit;
    }

    try {
        // Iniciar transacción
        $pdo->beginTransaction();

        // Verificar si el cliente ya existe
        $stmt = $pdo->prepare("SELECT cedula FROM clientes WHERE cedula = ?");
        $stmt->execute([$cedula]);

        if ($stmt->rowCount() > 0) {
            // Cliente existente: actualizar sus datos
            $sql_update = "UPDATE clientes SET nombres = ?, apellidos = ?, direccion = ?, telefono = ? WHERE cedula = ?";
            $pdo->prepare($sql_update)->execute([$nombres, $apellidos, $direccion, $telefono, $cedula]);
            $accion_historial = "Se actualizó datos del beneficiario e insertó un nuevo caso";
        } else {
            // Cliente nuevo
            $sql_cliente = "INSERT INTO clientes (cedula, nombres, apellidos, direccion, telefono, estado) 
                            VALUES (?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sql_cliente)->execute([$cedula, $nombres, $apellidos, $direccion, $telefono, $estado]);
            $accion_historial = "Se Registró un nuevo beneficiario y caso";
        }

        // Insertar nuevo caso con todos los campos
        $sql_caso = "INSERT INTO casos (
                        cedula_cliente, 
                        cedula_empleado, 
                        motivo, 
                        descripcion, 
                        tipo_caso, 
                        fecha_creado, 
                        fecha_atendido, 
                        resumen_atencion, 
                        atendido_por, 
                        atendido, 
                        estado
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $pdo->prepare($sql_caso)->execute([
            $cedula,
            $cedula_empleado,
            $motivo,
            $descripcion,
            $tipo_caso,
            $fecha_creado,
            $fecha_atendido,
            $resumen_atencion,
            $atendido_por,
            $atendido,
            $estado
        ]);

        // Registrar en el historial
        registrarHistorial($pdo, $cedula_empleado, $accion_historial);

        // Confirmar transacción
        $pdo->commit();

        // Mensaje de éxito
        $_SESSION['mensaje'] = $accion_historial . " exitosamente";

        // Limpiar errores y entradas previas
        unset($_SESSION['errors'], $_SESSION['old_input']);

        // Redirigir para evitar reenvío
        header("Location: ../Vista/Vistas/RegistrarCliente.php");
        exit;

    } catch (PDOException $e) {
        // Revertir transacción en caso de error
        $pdo->rollBack();
        
        error_log("Error al registrar beneficiario/caso: " . $e->getMessage());
        $_SESSION['mensaje']= "Ocurrió un error al procesar su solicitud. Intente más tarde.";
        header("Location: ../Vista/Vistas/RegistrarCliente.php");
        exit;
    }
}
?>
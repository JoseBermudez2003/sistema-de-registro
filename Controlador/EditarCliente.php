<?php
session_start(); // Inicia sesión para usar variables de sesión

require_once 'Conexion/conexion.php'; // Conexión a la base de datos
require_once 'Funciones/RegistrarHistorial.php'; // Función para registrar historial

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Limpieza básica de entradas
    $cedula = trim($_POST['cedula']);
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);

    // Validación 1: Campos vacíos
    if (!$cedula || !$nombres || !$apellidos || !$direccion || !$telefono) {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios.";
        header("Location: ../Vista/Vistas/listaClientes.php");
        exit();
    }

    // Validación 2: Nombres y apellidos solo letras y espacios
    if (!preg_match('/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/', $nombres)) {
        $_SESSION['mensaje'] = "Nombre inválido. Solo letras y espacios.";
        header("Location: ../Vista/Vistas/listaClientes.php");
        exit();
    }

    if (!preg_match('/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/', $apellidos)) {
        $_SESSION['mensaje'] = "Apellido inválido. Solo letras y espacios.";
        header("Location: ../Vista/Vistas/listaClientes.php");
        exit();
    }

    // Validación 3: Longitud mínima para nombres y apellidos
    if (strlen($nombres) < 3 || strlen($apellidos) < 3) {
        $_SESSION['mensaje'] = "Nombre y apellido deben tener al menos 3 caracteres.";
        header("Location: ../Vista/Vistas/listaClientes.php");
        exit();
    }

    //  Validación 4: Dirección con al menos 5 caracteres
    if (strlen($direccion) < 5 || strlen($direccion) > 100) {
            $_SESSION['mensaje'] = "La dirección debe tener entre 5 y 100 caracteres.";
        header("Location: ../Vista/Vistas/listaClientes.php");
        exit();
    }

    // Validación 5: Cédula válida
    if (!ctype_digit($cedula) || strlen($cedula) < 7 || strlen($cedula) > 8 || $cedula < 2000000) {
        $_SESSION['mensaje'] = "La cédula debe tener entre 7 y 8 dígitos, y ser mayor a 2 millones.";
        header("Location: ../Vista/Vistas/listaClientes.php");
        exit();
    }

    // Validación 6: Teléfono venezolano válido
    if (!preg_match('/^04(12|14|16|24|26)[0-9]{7}$/', $telefono)) {
        $_SESSION['mensaje'] = "El teléfono debe tener 11 dígitos y comenzar con 0412, 0414, 0416, 0424 o 0426.";
        header("Location: ../Vista/Vistas/listaClientes.php");
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Actualiza datos del cliente
        $sql = "UPDATE clientes SET 
                    nombres = :nombres,
                    apellidos = :apellidos,
                    direccion = :direccion,
                    telefono = :telefono
                WHERE cedula = :cedula";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombres' => $nombres,
            ':apellidos' => $apellidos,
            ':direccion' => $direccion,
            ':telefono' => $telefono,
            ':cedula' => $cedula
        ]);

        // Verifica si tiene un caso activo
        $sql_ultimo_caso = "SELECT id FROM casos WHERE cedula_cliente = :cedula AND estado = 1 ORDER BY id DESC LIMIT 1";
        $stmt_caso = $pdo->prepare($sql_ultimo_caso);
        $stmt_caso->execute([':cedula' => $cedula]);

        $ultimo_caso = $stmt_caso->fetch(PDO::FETCH_ASSOC);

        if ($ultimo_caso) {
            // Actualiza la fecha de creación del último caso activo
            $sql_update_caso = "UPDATE casos SET fecha_creado = NOW() WHERE id = :id";
            $pdo->prepare($sql_update_caso)->execute([':id' => $ultimo_caso['id']]);
        }

        // Guarda historial de la edición
        registrarHistorial($pdo, $_SESSION['cedula'], "Editó al beneficiario: $nombres $apellidos");

        $pdo->commit(); // Confirma cambios
        $_SESSION['mensaje'] = "beneficiario actualizado correctamente.";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['mensaje'] = "Error al actualizar el beneficiario: " . $e->getMessage();
    }

    header("Location: ../Vista/Vistas/listaClientes.php");
    exit();
}
?>

<?php
if (session_status() == PHP_SESSION_NONE) session_start();

require 'Conexion/conexion.php';
include_once 'Funciones/RegistrarHistorial.php';

$errors = [];
$mensaje = "";

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Guardar datos para repoblar formulario en caso de error
        $_SESSION['old_input'] = $_POST;

        // Limpieza de datos
        $cedula = trim($_POST['cedula'] ?? '');
        $contraseña = trim($_POST['contraseña'] ?? '');

        // Validación de campos obligatorios
        if (empty($cedula)) {
            $errors['cedula'] = "La cédula es obligatoria";
        }
        if (empty($contraseña)) {
            $errors['contraseña'] = "La contraseña es obligatoria";
        }

        // Validar formato de cédula
        if (!empty($cedula) && (!preg_match('/^[0-9]{6,8}$/', $cedula) || (int)$cedula < 2000000)) {
            $errors['cedula'] = "La cédula debe tener entre 6 y 8 dígitos y ser mayor a 2 millones";
        }

        // Validar contraseña segura
        if (!empty($contraseña) && (
            strlen($contraseña) < 8 ||
            !preg_match('/[A-Z]/', $contraseña) ||
            !preg_match('/[a-z]/', $contraseña) ||
            !preg_match('/[0-9]/', $contraseña) ||
            !preg_match('/[\W_]/', $contraseña)
        )) {
            $errors['contraseña'] = "La contraseña debe tener al menos 8 caracteres, incluyendo una mayúscula, una minúscula, un número y un símbolo";
        }

        // Si hay errores, guardarlos y redirigir
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ../');
            exit;
        }

        // Buscar usuario por cédula
        $sql = "SELECT * FROM usuarios WHERE cedula = :cedula LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':cedula', $cedula, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            if (password_verify($contraseña, $usuario['contraseña'])) {
                if ($usuario['estado'] == "0") {
                    $_SESSION['mensaje'] = "Su usuario está inactivo. Por favor, contacte al administrador.";
                    header('Location: ../');
                    exit;
                }

                // Crear sesión del usuario
                $_SESSION['nombres'] = $usuario['nombres'];
                $_SESSION['apellidos'] = $usuario['apellidos'];
                $_SESSION['cedula'] = $usuario['cedula'];
                $_SESSION['cargo'] = $usuario['cargo'];
                $_SESSION['estado'] = $usuario['estado'];

                // Limpiar errores y old_input previos
                unset($_SESSION['errors'], $_SESSION['old_input']);

                // Registrar historial
                registrarHistorial($pdo, $usuario['cedula'], "Inició sesión");

                $_SESSION['mensaje'] = "Bienvenido al Sistema. Ha iniciado sesión exitosamente.";
                header('Location: ../Vista/Vistas/');
                exit;
            } else {
                $errors['contraseña'] = "La contraseña ingresada es incorrecta. Por favor, inténtelo de nuevo.";
            }
        } else {
            $errors['cedula'] = "Usuario no encontrado. Verifique la cédula e inténtelo nuevamente.";
        }

        // Guardar errores y redirigir en caso de fallo de autenticación
        $_SESSION['errors'] = $errors;
        header('Location: ../');
        exit;

    } else {
        $_SESSION['mensaje'] = "Acceso no permitido. Debe enviar datos mediante el formulario de inicio de sesión.";
        header('Location: ../');
        exit;
    }
} catch (PDOException $e) {
    error_log("Error en inicio de sesión: " . $e->getMessage());
    $_SESSION['mensaje'] = "Ocurrió un error de conexión. Por favor, inténtelo de nuevo más tarde o contacte al soporte técnico si el problema persiste.";
    header('Location: ../');
    exit;
}
?>

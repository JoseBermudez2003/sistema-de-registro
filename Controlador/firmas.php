<?php 
session_start(); // Iniciar sesión

// Inclusión de dependencias necesarias
require_once 'Conexion/conexion.php';
require_once 'Funciones/RegistrarHistorial.php';
require_once 'Funciones/FuncionesFirmas.php';

define('RUTA_FIRMAS', __DIR__ . '/../vista/Recursos/Img'); // Carpeta donde se guardan las firmas

// Determinar la acción
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

switch ($accion) {
    case 'listar_firmas_digitales':
        $firmas = listarFirmasDigitales($pdo);
        header('Content-Type: application/json');
        echo json_encode($firmas);
        break;

    case 'guardar_firma_digital':
        if ($_SESSION['cargo'] !== 'Administrador') {
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para realizar esta acción.']);
            break;
        }

        $nombre = strtoupper($_POST['nombre_firmante']);
        $cargo  = strtoupper($_POST['cargo_firmante']);

        if (existeFirmaConNombre($pdo, $nombre)) {
            echo json_encode(['success' => false, 'message' => "Ya existe una firma registrada para: $nombre."]);
            break;
        }

        if (!isset($_FILES['imagen_firma']) || $_FILES['imagen_firma']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'No se ha subido ninguna imagen o hubo un error en la subida.']);
            break;
        }

        $extension = pathinfo($_FILES['imagen_firma']['name'], PATHINFO_EXTENSION);
        $nombreImagen = uniqid('firma_') . '.' . $extension;
        $rutaDestino = RUTA_FIRMAS . '/' . $nombreImagen;

        if (!move_uploaded_file($_FILES['imagen_firma']['tmp_name'], $rutaDestino)) {
            echo json_encode(['success' => false, 'message' => 'Error al mover el archivo de la firma.']);
            break;
        }

        if (guardarFirmaDigital($pdo, $nombre, $cargo, $rutaDestino)) {
            registrarHistorial($pdo, $_SESSION['cedula'], "Registro de nueva firma digital");
            echo json_encode(['success' => true, 'message' => 'Firma guardada con éxito.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar la firma en la base de datos.']);
        }
        break;

    case 'eliminar_firma_digital':
        if ($_SESSION['cargo'] !== 'Administrador') {
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para realizar esta acción.']);
            break;
        }

        $id_firma = $_POST['id_firma'] ?? '';
        if (empty($id_firma)) {
            echo json_encode(['success' => false, 'message' => 'ID de firma no válido.']);
            break;
        }

        $firma = obtenerFirmaDigitalPorId($pdo, $id_firma);
        if (!$firma) {
            echo json_encode(['success' => false, 'message' => 'No se encontró la firma digital con el ID proporcionado.']);
            break;
        }

        if (!eliminarArchivo($firma['ruta_imagen'])) {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la imagen de la firma del servidor. La firma no fue eliminada.']);
            break;
        }

        if (eliminarFirmaDigital($pdo, $id_firma)) {
            registrarHistorial($pdo, $_SESSION['cedula'], "Eliminó firma digital con id: $id_firma");
            echo json_encode(['success' => true, 'message' => 'Firma digital y su imagen eliminadas con éxito.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la firma digital de la base de datos.']);
        }
        break;

    default:
        header('Location: ../Vista/Vistas/GenerarInformes.php');
        break;
}
?>

<?php
date_default_timezone_set('America/Caracas'); //esto esta aqui por seguridad, para evitar errores de fecha
require '../vista/Recursos/Librerias/dompdf/autoload.inc.php';
require 'Conexion/conexion.php';
include 'Funciones/FuncionesFirmas.php';
use Dompdf\Dompdf;
use Dompdf\Options;

if (session_status() == PHP_SESSION_NONE) session_start();

// Solo permitir POST y usuario autenticado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['cedula'])) {
    // Recibir y limpiar datos
    $titulo = trim($_POST['titulo'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    $destinatario = trim($_POST['destinatario'] ?? '');
    $incluir_firma = isset($_POST['incluir_firma_opcion']) && $_POST['incluir_firma_opcion'] === 'si';
    
    // **MODIFICADO**: Aseguramos que firma_digital_id sea null si no se selecciona o no se incluye firma
    $firma_digital_id = ($incluir_firma && isset($_POST['firma_digital_seleccion'])) ? intval($_POST['firma_digital_seleccion']) : null;

    if (empty($titulo) || empty($contenido)) {
        echo "<p style='color:red;'>El título y el contenido son obligatorios.</p>";
        exit;
    }

    // Definir si se incluye firma digital para el PDF
    $firmaHTML = '';
    if ($incluir_firma && !empty($firma_digital_id)) {
        $firmaData = obtenerFirmaDigitalPorId($pdo, $firma_digital_id);
        if ($firmaData && file_exists($firmaData['ruta_imagen'])) {
            $firmaBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($firmaData['ruta_imagen']));
            $firmaHTML = <<<HTML
            <br><br><br>
            <p style="font-size: 16px; margin-bottom: -20px; text-align:center;"><strong>ATENTAMENTE</strong></p>
            <div style="text-align: center; margin-top: 30px;">
                <img src="{$firmaBase64}" alt="Firma Digital" style="padding-bottom: 2px;border-bottom: 2px solid black;width: 80px; height: 40px;">
                <p style="font-size: 16px; margin-top: 5px; text-align:center; margin-top: -5px;"><strong>{$firmaData['nombre_firmante']}</strong></p>
                <p style="font-size: 16px; margin-top: -15px;text-align:center;"><strong>{$firmaData['cargo_firmante']}</strong></p>
            </div>
            HTML;
        } else {
             error_log("Error: No se encontró la imagen de la firma con ID: " . $firma_digital_id);
        }
    }

    // Limpieza de seguridad para el contenido
    $contenido_permitido = strip_tags($contenido, '<p><br><b><i><strong><em><u><span><div><ul><ol><li><a>');
    $contenido_pdf = preg_replace('/<[^>]+(on\w+|style=["\'].*?(expression|javascript).*?)["\'][^>]*>/i', '', $contenido_permitido);
    $destinatario_pdf = nl2br(htmlspecialchars($destinatario, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));

    $cedula_empleado = $_SESSION['cedula'];
    $estado = 1;

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO informes (titulo, contenido, firma_id, destinatario, fecha, cedula_empleado, estado) 
             VALUES (?, ?, ?, ?, NOW(), ?, ?)"
        );
        $stmt->execute([$titulo, $contenido, $firma_digital_id, $destinatario, $cedula_empleado, $estado]);

    } catch (Exception $e) {
        error_log("Error al guardar informe: " . $e->getMessage());
        echo "<p style='color:red;'>Error al guardar el informe. 
        Por favor, inténtelo nuevamente. Verifique que no haya olvidado incluir su firma digital si es requerida.</p>";
        exit;
    }

    // Cargar logos y convertir a base64
    $logoPath = __DIR__ . '/../vista/Recursos/Img/logo.png';
    $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
    if (!$logoBase64) {
        echo "<p style='color:red;'>No se encontró el logo principal.</p>"; exit;
    }

    $logopath2 = __DIR__ . '/../vista/Recursos/Img/gobierno.png';
    $logoBase64Right = file_exists($logopath2) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logopath2)) : '';
    if (!$logoBase64Right) {
        echo "<p style='color:red;'>No se encontró el logo secundario.</p>"; exit;
    }

    $fecha_actual = date('d/m/Y');

    // Construir el HTML del PDF
    $html = '
    <html lang="es">
    <head>
      <meta charset="UTF-8" />
      <style>
        body { font-family: "DejaVu Sans", sans-serif; font-size: 12pt; margin: 50px 80px; line-height: 1.6; }
        .fecha { text-align: right; font-size: 10pt; color: #333; margin-bottom: 10px; }
        .titulo { text-align: center; font-size: 17pt; font-weight: bold; margin-bottom: 20px; }
        .encabezado-tabla { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .encabezado-tabla td { vertical-align: top; }
        .logo { width: 120px; }
        .encabezado-texto { text-align: center; font-size: 10pt; font-weight: bold; }
        .destinatario { font-size: 11pt; margin-bottom: 20px; text-align: justify; }
        .contenido { text-align: justify; margin-bottom: 50px; white-space: pre-wrap; }
        .contenido p, .contenido div { margin-bottom: 15px; text-align: inherit; white-space: pre-wrap; }
      </style>
    </head>
    <body>
      <table class="encabezado-tabla">
        <tr>
          <td style="width: 20%; text-align: left;"><img src="' . $logoBase64 . '" width="120" alt="Logo Corvisucre"></td>
          <td class="encabezado-texto" style="width: 60%;">
            REPÚBLICA BOLIVARIANA DE VENEZUELA<br>
            GOBIERNO BOLIVARIANO DEL ESTADO SUCRE<br>
            CORPORACIÓN DE VIVIENDA DEL ESTADO SUCRE<br>
            RIF G-20016449-2
          </td>
          <td style="width: 20%; text-align: right;"><img src="' . $logoBase64Right . '" width="90" alt="Logo Gobierno"></td>
        </tr>
      </table>
      <div class="fecha">Fecha: ' . $fecha_actual . '</div>
      <div class="destinatario">' . $destinatario_pdf . '</div>
      <div class="titulo">' . htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') . '</div>
      <div class="contenido">' . $contenido_pdf . '</div>
      <div class="firma">' . $firmaHTML . '</div>
    </body>
    </html>';

    // Generar PDF con Dompdf
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('letter', 'portrait');
    $dompdf->render();
    $dompdf->stream("Informe_" . date("Ymd_His") . ".pdf", ["Attachment" => false]);
    exit;

} else {
    header('HTTP/1.1 403 Forbidden');
    echo "<p style='color:red;'>Acceso no autorizado.</p>";
    exit;
}
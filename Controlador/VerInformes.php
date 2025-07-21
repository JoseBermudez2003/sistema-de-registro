<?php
require '../vista/Recursos/Librerias/dompdf/autoload.inc.php';
require 'Conexion/conexion.php';
// Es crucial que esta función exista y funcione correctamente
include 'Funciones/FuncionesFirmas.php';
use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    if (!$id) {
        exit("ID de informe inválido.");
    }

    try {
        $stmt = $pdo->prepare("SELECT titulo, contenido, firma_id, destinatario, fecha FROM informes WHERE id = ? AND estado = 1");
        $stmt->execute([$id]);
        $informe = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$informe) {
            exit("Informe no encontrado o ha sido eliminado.");
        }

      
        $firmaHTML = '';
        if (!empty($informe['firma_id'])) {
            $firmaData = obtenerFirmaDigitalPorId($pdo, $informe['firma_id']);
            if ($firmaData && file_exists($firmaData['ruta_imagen'])) {
                $firmaBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($firmaData['ruta_imagen']));
                
                // Construimos el bloque HTML para la firma
                $firmaHTML = <<<HTML
                <br><br><br>
                <p style="font-size: 16px; margin-bottom: -20px; text-align:center;"><strong>ATENTAMENTE</strong></p>
                <div style="text-align: center; margin-top: 30px;">
                    <img src="{$firmaBase64}" alt="Firma Digital" style="padding-bottom: 2px;border-bottom: 2px solid black;width: 80px; height: 40px;">
                    <p style="font-size: 16px; margin-top: 5px; text-align:center; margin-top: -5px;"><strong>{$firmaData['nombre_firmante']}</strong></p>
                    <p style="font-size: 16px; margin-top: -15px;text-align:center;"><strong>{$firmaData['cargo_firmante']}</strong></p>
                </div>
                HTML;
            }
        }

        $fecha_formateada = date('d/m/Y', strtotime($informe['fecha']));

        // Cargar logos
        $logoPath = __DIR__ . '/../vista/Recursos/Img/logo.png';
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
        if (!$logoBase64) exit("No se encontró el logo principal.");

        $logoPath2 = __DIR__ . '/../vista/Recursos/Img/gobierno.png';
        $logoBase64Right = file_exists($logoPath2) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath2)) : '';
        if (!$logoBase64Right) exit("No se encontró el logo secundario.");

        // Procesar destinatario y contenido
        $destinatario_pdf = nl2br(htmlspecialchars($informe['destinatario'], ENT_QUOTES, 'UTF-8'));
        $contenido_permitido = strip_tags($informe['contenido'], '<p><br><b><i><strong><em><u><span><div><ul><ol><li><a>');
        $contenido_pdf = preg_replace('/<[^>]+(on\w+|style=["\'].*?(expression|javascript).*?)["\'][^>]*>/i', '', $contenido_permitido);

        // Construir HTML para PDF
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
          <div class="fecha">Fecha: ' . $fecha_formateada . '</div>
          <div class="destinatario">' . $destinatario_pdf . '</div>
          <div class="titulo">' . htmlspecialchars($informe['titulo'], ENT_QUOTES, 'UTF-8') . '</div>
          <div class="contenido">' . $contenido_pdf . '</div>
          
          <div class="firma">' . $firmaHTML . '</div>
        </body>
        </html>';

        // Configuración y renderizado de DOMPDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();
        $dompdf->stream("Informe_" . $id . ".pdf", ["Attachment" => false]);
        exit;

    } catch (Exception $e) {
        error_log("Error al generar PDF para el informe ID $id: " . $e->getMessage());
        exit("Ocurrió un error al generar el PDF. Por favor, contacte al administrador.");
    }
} else {
    exit("Acceso no autorizado o ID de informe no especificado.");
}
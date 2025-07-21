<?php include "../../controlador/verificarsesion.php"; ?>
<?php require_once '../../controlador/Conexion/conexion.php';?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Crear Informe</title>

  <!-- Estilos -->
  <link rel="stylesheet" href="../Recursos/Librerias/bootstrap-5.0.2-dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../Recursos/Css/style.css" />
  <link rel="icon" href="../Recursos/Iconos/house.svg" />
  <link rel="stylesheet" href="../Recursos/Librerias/icons-1.13.1/font/bootstrap-icons.css" />

  <style>
    .form-container {
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
      padding: 2rem;
    }
    .editor-container {
      border: 1px solid #dee2e6;
      border-radius: 4px;
    }
  </style>
</head>

<body class="bg-light">
  <header>
    <?php include '../../Controlador/Menu.php'; ?>
  </header>

  <main class="container py-4">
    <div class="row justify-content-center">
      <div class="col-lg-10 col-xl-8">
        <div class="form-container">
          <h2 class="text-center mb-4">Crear Informe</h2>

          <form action="../../controlador/generarinformes.php" method="POST" id="formInforme" autocomplete="off" target="_blank">
            <!-- Tipo de Informe -->
            <div class="mb-4">
              <label class="form-label fw-bold">Tipo de Informe</label>
              <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownTipoInforme" data-bs-toggle="dropdown" aria-expanded="false">
                  Informe Normal
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownTipoInforme">
                  <li><a class="dropdown-item tipo-informe-option" href="#" data-value="normal">Informe Normal</a></li>
                  <li><a class="dropdown-item tipo-informe-option" href="#" data-value="con_destinatario">Informe con Destinatario</a></li>
                </ul>
              </div>
              <input type="hidden" name="tipo_informe" id="tipo_informe" value="normal" />
            </div>

            <!-- Contenido destinatario -->
            <div class="mb-4" id="destinatarioContainer" style="display: none;">
              <label for="destinatario" class="form-label fw-bold">Contenido para el Destinatario</label>
              <textarea name="destinatario" id="destinatario" rows="5" class="form-control form-control-md"></textarea>
            </div>

            <!-- Título -->
            <div class="mb-4">
              <label for="titulo" class="form-label fw-bold">Título del Informe</label>
              <input type="text" name="titulo" id="titulo" class="form-control form-control-lg" required />
            </div>

            <!-- Contenido -->
            <div class="mb-4">
              <label for="contenido" class="form-label fw-bold">Contenido del Informe</label>
              <textarea name="contenido" id="contenido" class="d-none"></textarea>
              <div id="editorContenido" class="editor-container"></div>
            </div>
            <div class="mb-4 opciones_firmas">
                <label class="form-label"><b>Incluir firma digital:</b></label>
                <div class="form-check form-check-inline check-firma">
                    <input class="form-check-input" type="radio" name="incluir_firma_opcion" id="firma_no" value="no" checked>
                    <label class="form-check-label" for="firma_no">No</label>
                </div>
                <div class="form-check form-check-inline check-firma">
                    <input class="form-check-input" type="radio" name="incluir_firma_opcion" id="firma_si" value="si">
                    <label class="form-check-label" for="firma_si">Sí</label>
                </div>
                <div id="firma_digital_opciones" style="display: none; align-items: center;">
                    <select class="form-select ms-2" id="firma_digital_seleccion" name="firma_digital_seleccion">
                        <option value="">Seleccionar firma</option>
                        </select>
                    <?php if ($_SESSION['cargo'] === 'Administrador'): ?>
                        <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#modalAgregarFirma"><i class="bi bi-plus"></i></button>
                        <button type="button" class="btn btn-sm btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#modalEliminarFirma"><i class="bi bi-trash"></i></button>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Botones -->
            <div class="d-flex justify-content-between align-items-center border-top pt-4">
              <button type="button" class="btn btn-secondary" id="btnLimpiar">
                <i class="bi bi-trash"></i> Limpiar
              </button>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-file-earmark-pdf"></i> Generar PDF
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Modal para registrar firma -->
<div class="modal fade" id="modalAgregarFirma" tabindex="-1" aria-labelledby="agregarFirmaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="agregarFirmaLabel"><i class="bi bi-pen"></i>Registrar nueva firma digital</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formAgregarFirma">
                    <div class="mb-3">
                        <label for="nombre_firmante" class="form-label">Nombre del firmante:</label>
                        <input type="text" class="form-control" id="nombre_firmante" name="nombre_firmante" required>
                    </div>
                    <div class="mb-3">
                        <label for="cargo_firmante" class="form-label">Cargo del firmante:</label>
                        <input type="text" class="form-control" id="cargo_firmante" name="cargo_firmante" required>
                    </div>
                    <div class="mb-3">
                        <label for="imagen_firma" class="form-label">Imagen de la firma:</label>
                        <input type="file" class="form-control" id="imagen_firma" name="imagen_firma" accept="image/*" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarNuevaFirmaBtn">Guardar firma</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal para eliminar firma -->
<div class="modal fade" id="modalEliminarFirma" tabindex="-1" aria-labelledby="eliminarFirmaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="eliminarFirmaLabel"><i class="bi bi-pen"></i>Eliminar firma digital</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="firma_a_eliminar" class="form-label">Seleccione la firma a eliminar:</label>
                <select class="form-select" id="firma_a_eliminar" name="firma_a_eliminar">
                    <option value="">Seleccionar firma</option>
                    </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminarFirmaBtn">Eliminar firma</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal para confirmar eliminar firma -->
<div class="modal fade" id="EliminarFirma" tabindex="-1" aria-labelledby="eliminarFirmaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-pen"></i>Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="informe_id" id="eliminarIdFirma">
                <p>¿Estás seguro de que deseas eliminar esta firma?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminarFirma">Sí, Eliminar</button>
            </div>
        </div>
    </div>
</div>
  </main>
<?php include'../../controlador/Mensajes.php';?> <!-- Archivo mensajes -->
  <!-- Scripts -->
  <script src="../Recursos/Librerias/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
  <script src="../Recursos/Librerias/tinymce/js/tinymce/tinymce.min.js"></script>
  <script src="../Recursos/Librerias/tinymce/langs/es.js"></script>
  <script src="../JavaScript/tiny.js"></script>
  <script src="../Recursos/Librerias/jquery/jquery-3.3.1.min.js"></script>
  <script src="../JavaScript/firmas.js"></script>
</body>
</html>



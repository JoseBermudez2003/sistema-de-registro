<?php
// Incluye el controlador que lista los informes y verifica la sesión del usuario
include "../../controlador/ListaInformes.php";
include "../../controlador/verificarsesion.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ver informes</title>
  
  <!-- Estilos CSS -->
  <link rel="stylesheet" href="../Recursos/Css/style.css" />
  <link rel="stylesheet" href="../Recursos/Css/normalize.css" />
  <link rel="stylesheet" href="../Recursos/Librerias/datatables.min.css" />
  <link rel="stylesheet" href="../Recursos/Librerias/bootstrap-5.0.2-dist/css/bootstrap.min.css" />
  
  <link rel="icon" href="../Recursos/Iconos/house.svg" />
</head>
<body>

<header>
  <?php 
  // Menú de navegación común para la aplicación
  include '../../Controlador/Menu.php'; 
  ?>
</header>

<main class="container mt-5 pt-5">
  <h2 class="text-center mb-4">Lista de Informes Generados</h2>

  <div class="table-responsive">
    <!-- Tabla con la lista de informes -->
    <table id="myTable" class="table table-striped table-bordered">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Título</th>
          <th>Fecha</th>
          <th class="no-export no-order">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($informes as $informe): ?>
        <tr>
          <!-- Mostrar datos del informe con protección contra XSS -->
          <td><?= htmlspecialchars($informe['id']) ?></td>
          <td><?= htmlspecialchars($informe['titulo']) ?></td>
          <td><?= htmlspecialchars($informe['fecha']) ?></td>
          <td>
            <div class="d-flex justify-content-center flex-wrap gap-2">
              <!-- Botón para abrir el PDF en una nueva pestaña -->
              <form action="../../controlador/VerInformes.php" method="POST" target="_blank" class="d-inline">
                <input type="hidden" name="id" value="<?= htmlspecialchars($informe['id']) ?>" />
                <button class="btn btn-primary btn-sm" title="Ver PDF">
                  <img src="../Recursos/Iconos/pdf.svg" alt="PDF" width="20" />
                </button>
              </form>
              
              <!-- Botón para abrir modal de confirmación de eliminación -->
              <button 
                class="btn btn-danger btn-sm btnEliminar" 
                data-id="<?= htmlspecialchars($informe['id']) ?>" 
                data-bs-toggle="modal" 
                data-bs-target="#EliminarInforme" 
                title="Eliminar"
              >
                <img src="../Recursos/Iconos/trash.svg" alt="Eliminar" width="20" />
              </button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>

<!-- Modal para confirmar eliminación de informe -->
<div class="modal fade" id="EliminarInforme" tabindex="-1" aria-labelledby="eliminarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formEliminarInforme" action="../../controlador/EliminarInformes.php" method="POST" class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="eliminarLabel">Confirmar eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <!-- Campo oculto para enviar el ID del informe a eliminar -->
        <input type="hidden" name="id" id="eliminarId" />
        <p>¿Estás seguro de que deseas eliminar este informe?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger">Sí, Eliminar</button>
      </div>
    </form>
  </div>
</div>

<?php 
// Mostrar mensajes (alertas) si existen
if (!empty($mensaje)) include '../../Controlador/Mensajes.php'; 
?>

<!-- Scripts JS -->
<script src="../Recursos/Librerias/jquery/jquery-3.3.1.min.js"></script>
<script src="../Recursos/Librerias/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
<script src="../Recursos/Librerias/datatables.min.js"></script>
<script src="../JavaScript/funciones.js"></script>
 <script src="../JavaScript/tablaClientes.js"></script> <!--cuidado aqui, IMPORTANTE VER -->

<script>
  // Script para pasar el ID del informe a eliminar al modal
  $(document).ready(function () {
    $('.btnEliminar').on('click', function () {
      let informeId = $(this).data('id');
      $('#eliminarId').val(informeId);
    });
  });
</script>

</body>
</html>

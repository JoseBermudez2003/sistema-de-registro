<?php 
// Controladores necesarios para obtener el historial y verificar sesión/cargo
include "../../controlador/ListaHistorial.php"; 
include "../../controlador/VerificarSesionCargo.php"; 
?>  

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Historial</title>

  <!-- Estilos -->
  <link rel="stylesheet" href="../Recursos/Css/style.css">
  <link rel="stylesheet" href="../Recursos/Css/normalize.css">
  <link rel="stylesheet" href="../Recursos/Librerias/datatables.min.css">
  <link rel="stylesheet" href="../Recursos/Librerias/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="icon" href="../Recursos/Iconos/house.svg">
</head>

<body>
<!-- Menú principal -->
<header>
  <?php include '../../Controlador/Menu.php'; ?>
</header>

<!-- Contenido principal -->
<main class="container mt-5 pt-5">
  <h2 class="text-center mb-4">Historial de Acciones</h2>

  <!-- Tabla responsiva con DataTables -->
  <div class="table-responsive">
    <table id="myTable" class="table table-striped table-bordered">
      <thead class="table-dark text-center">
        <tr>
          <th>ID</th>
          <th>Nombres y Apellidos</th>
          <th>Acción</th>
          <th>Fecha</th>
        </tr>
      </thead>
      <tbody class="text-center">
        <?php foreach ($historiales as $historial): ?>
          <tr>
            <td><?= htmlspecialchars($historial['id'] ?? '') ?></td>
            <td>
              <?php 
              $nombre = $historial['empleado_nombre'] ?? $historial['empleado_nonbrez'] ?? $historial['nombres'] ?? 'N/A';
              $apellido = $historial['empleado_apellido'] ?? $historial['apellidos'] ?? $historial['apellido'] ?? '';
              echo htmlspecialchars($nombre) . " " . htmlspecialchars($apellido);
              ?>
            </td>
            <td><?= htmlspecialchars($historial['accion'] ?? $historial['action'] ?? '') ?></td>
            <td><?= htmlspecialchars($historial['fecha'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>

<!-- Mensajes de notificación -->
<?php if (!empty($mensaje)): ?>
  <?php include '../../Controlador/Mensajes.php'; ?>
<?php endif; ?>

<!-- Scripts -->
<script src="../Recursos/Librerias/jquery/jquery-3.3.1.min.js"></script>
<script src="../Recursos/Librerias/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
<script src="../Recursos/Librerias/datatables.min.js"></script>
<script src="../JavaScript/TablaClientes.js"></script>
<script src="../JavaScript/funciones.js"></script>

</body>
</html>

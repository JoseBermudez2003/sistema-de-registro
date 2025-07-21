<?php
// Controlador que obtiene la lista de clientes
include "../../controlador/ListaClientes.php";
// Verificación de sesión activa
require "../../controlador/verificarsesion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Lista de Beneficiarios</title>

  <!-- Estilos CSS -->
  <link rel="stylesheet" href="../Recursos/Css/style.css" />
  <link rel="stylesheet" href="../Recursos/Css/normalize.css" />
  <link rel="stylesheet" href="../Recursos/Librerias/datatables.min.css" />
  <link rel="stylesheet" href="../Recursos/Librerias/bootstrap-5.0.2-dist/css/bootstrap.min.css" />
  <link rel="icon" href="../Recursos/Iconos/house.svg" />
</head>
<body>

<header>
  <?php include '../../Controlador/Menu.php'; // Menú principal ?>
</header>

<main class="container mt-5 pt-5">
  <h2 class="text-center mb-4">Lista de Beneficiarios</h2>

  <div class="table-responsive">
    <table id="myTable" class="table table-striped table-bordered">
      <thead class="table-dark">
        <tr>
          <th>Cédula</th>
          <th>Nombres</th>
          <th>Apellidos</th>
          <th class="no-order">Dirección</th>
          <th class="no-order">Teléfono</th>
          <th class="no-export no-order">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <!-- Recorre la lista de clientes para mostrar en la tabla -->
        <?php foreach ($clientes as $cliente): ?>
        <tr>
          <td><?= htmlspecialchars($cliente['cedula']) ?></td>
          <td><?= htmlspecialchars($cliente['nombres']) ?></td>
          <td><?= htmlspecialchars($cliente['apellidos']) ?></td>
          <td><?= htmlspecialchars($cliente['direccion']) ?></td>
          <td><?= htmlspecialchars($cliente['telefono']) ?></td>
          <td>
            <div class="d-flex justify-content-center flex-wrap gap-2">
              <!-- Botón para abrir modal de edición -->
              <button 
                class="btn btn-primary btn-sm btnEditar" 
                data-bs-toggle="modal" 
                data-bs-target="#EditarCliente" 
                data-id="<?= $cliente['cedula'] ?>" 
                title="Editar"
              >
                <img src="../Recursos/Iconos/editar.svg" alt="Editar" width="20" />
              </button>
              <!-- Botón para abrir modal de eliminación -->
              <button 
                class="btn btn-danger btn-sm btnEliminar" 
                data-bs-toggle="modal" 
                data-bs-target="#EliminarCliente" 
                data-id="<?= $cliente['cedula'] ?>" 
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

<!-- Modal para editar cliente -->
<div class="modal fade" id="EditarCliente" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form 
      id="formEditarCliente" 
      action="../../controlador/EditarCliente.php" 
      method="POST" 
      class="modal-content" 
      autocomplete="off"
    >
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Editar beneficiario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <!-- Campo oculto para la cédula (identificador) -->
        <input type="hidden" name="cedula" id="modalId" />

        <!-- Campos para editar datos del cliente -->
        <div class="mb-3">
          <label for="modalNombre" class="form-label">Nombres</label>
          <input 
            type="text" 
            class="form-control" 
            name="nombres" 
            id="modalNombre" 
            required 
            minlength="3" 
            maxlength="50" 
            oninput="validarSoloLetras?.(this)" 
          />
        </div>
        <div class="mb-3">
          <label for="modalApellido" class="form-label">Apellidos</label>
          <input 
            type="text" 
            class="form-control" 
            name="apellidos" 
            id="modalApellido" 
            required 
            minlength="3" 
            maxlength="50" 
            oninput="validarSoloLetras?.(this)" 
          />
        </div>
        <div class="mb-3">
          <label for="modalDireccion" class="form-label">Dirección</label>
          <input 
            type="text" 
            class="form-control" 
            name="direccion" 
            id="modalDireccion" 
            required 
            minlength="5" 
            maxlength="100" 
            oninput="validarInputLetNum?.(this)" 
          />
        </div>
        <div class="mb-3">
          <label for="modalTelefono" class="form-label">Teléfono</label>
          <input 
            type="text" 
            class="form-control" 
            name="telefono" 
            id="modalTelefono" 
            required 
            minlength="11" 
            maxlength="11" 
            oninput="validateInput?.(this)" 
          />
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal para eliminar beneficiario-->
<div class="modal fade" id="EliminarCliente" tabindex="-1" aria-labelledby="eliminarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form 
      id="formEliminarCliente" 
      action="../../controlador/EliminarCliente.php" 
      method="POST" 
      class="modal-content"
    >
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirmar eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <!-- Campo oculto para la cédula (identificador) -->
        <input type="hidden" name="cedula" id="eliminarId" />
        <p>¿Estás seguro de que deseas eliminar a este beneficiario?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger">Sí, Eliminar</button>
      </div>
    </form>
  </div>
</div>

<!-- Mensajes de alerta -->
<?php if (!empty($mensaje)) { include '../../Controlador/Mensajes.php'; } ?>

<!-- Scripts JS -->
<script src="../Recursos/Librerias/jquery/jquery-3.3.1.min.js"></script>
<script src="../Recursos/Librerias/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
<script src="../Recursos/Librerias/datatables.min.js"></script>
<script src="../JavaScript/Funciones.js"></script>
<script src="../JavaScript/Tablas.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Asignar datos al modal de edición cuando se presiona el botón editar
    document.querySelectorAll('.btnEditar').forEach(btn => {
      btn.addEventListener('click', () => {
        const row = btn.closest('tr').children;
        document.getElementById('modalId').value = row[0].textContent.trim();
        document.getElementById('modalNombre').value = row[1].textContent.trim();
        document.getElementById('modalApellido').value = row[2].textContent.trim();
        document.getElementById('modalDireccion').value = row[3].textContent.trim();
        document.getElementById('modalTelefono').value = row[4].textContent.trim();
      });
    });

    // Asignar cédula al modal de eliminación cuando se presiona el botón eliminar
    document.querySelectorAll('.btnEliminar').forEach(btn => {
      btn.addEventListener('click', () => {
        const cedula = btn.closest('tr').children[0].textContent.trim();
        document.getElementById('eliminarId').value = cedula;
      });
    });
  });
</script>

</body>
</html>

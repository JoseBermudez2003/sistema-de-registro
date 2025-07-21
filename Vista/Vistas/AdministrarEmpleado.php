<?php
// Controladores necesarios
include "../../controlador/ListaEmpleado.php";
include "../../controlador/VerificarSesionCargo.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Administrar Empleados</title>

  <!-- CSS -->
  <link rel="stylesheet" href="../Recursos/Css/style.css">
  <link rel="stylesheet" href="../Recursos/Css/normalize.css">
  <link rel="stylesheet" href="../Recursos/Librerias/datatables.min.css">
  <link rel="stylesheet" href="../Recursos/Librerias/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="icon" href="../Recursos/Iconos/house.svg">
</head>

<body>
  <!-- Menú de navegación -->
  <header>
    <?php include '../../Controlador/Menu.php'; ?>
  </header>

  <!-- Contenido principal -->
  <main class="container mt-5 pt-5">
    <h2 class="text-center mb-4">Administrar Empleados</h2>

    <div class="table-responsive">
      <table id="myTable" class="table table-striped table-bordered">
        <thead class="table-dark">
          <tr>
            <th>Cédula</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th class="no-order">Correo</th>
            <th class="no-order">Dirección</th>
            <th class="no-order">Teléfono</th>
            <th class="no-export no-order">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($empleados as $empleado): ?>
            <tr>
              <td><?= htmlspecialchars($empleado['cedula']) ?></td>
              <td><?= htmlspecialchars($empleado['nombres']) ?></td>
              <td><?= htmlspecialchars($empleado['apellidos']) ?></td>
              <td><?= htmlspecialchars($empleado['correo']) ?></td>
              <td><?= htmlspecialchars($empleado['direccion']) ?></td>
              <td><?= htmlspecialchars($empleado['telefono']) ?></td>
              <td>
                <div class="d-flex justify-content-center flex-wrap gap-2">
                  <!-- Botón Editar -->
                  <button class="btn btn-primary btn-sm btnEditar" title="Editar">
                    <img src="../Recursos/Iconos/editar.svg" alt="Editar" width="20">
                  </button>
                  <!-- Botón Eliminar -->
                  <button class="btn btn-danger btn-sm btnEliminar" title="Eliminar">
                    <img src="../Recursos/Iconos/trash.svg" alt="Eliminar" width="20">
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

  <!-- Modal: Editar Empleado -->
  <div class="modal fade" id="EditarEmpleado" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="formEditarEmpleado" action="../../controlador/EditarEmpleado.php" method="POST" class="modal-content" autocomplete="off">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalLabel">Editar Empleado</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <!-- Campo oculto para la cédula -->
          <input type="hidden" name="cedula" id="modalCedula" />

          <!-- Campos editables -->
          <div class="mb-3">
            <label for="modalNombre" class="form-label">Nombres</label>
            <input type="text" class="form-control" name="nombres" id="modalNombre" required oninput="validarSoloLetras?.(this)" minlength="3" maxlength="50" />
          </div>

          <div class="mb-3">
            <label for="modalApellido" class="form-label">Apellidos</label>
            <input type="text" class="form-control" name="apellidos" id="modalApellido" required oninput="validarSoloLetras?.(this)" minlength="3" maxlength="50" />
          </div>

          <div class="mb-3">
            <label for="modalCorreo" class="form-label">Correo</label>
            <input type="email" class="form-control" name="correo" id="modalCorreo" required oninput="validarCorreoInput(this)" />
          </div>

          <div class="mb-3">
            <label for="modalDireccion" class="form-label">Dirección</label>
            <input type="text" class="form-control" name="direccion" id="modalDireccion" required oninput="validarInputLetNum?.(this)" minlength="5" maxlength="100" />
          </div>

          <div class="mb-3">
            <label for="modalTelefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" name="telefono" id="modalTelefono" required oninput="validateInput?.(this)" minlength="11" maxlength="11" />
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal: Confirmar Eliminación -->
  <div class="modal fade" id="EliminarEmpleado" tabindex="-1" aria-labelledby="eliminarLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="formEliminarEmpleado" action="../../controlador/EliminarEmpleado.php" method="POST" class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="eliminarLabel">Confirmar eliminación</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="cedula" id="eliminarCedula" />
          <p>¿Estás seguro de que deseas eliminar a este empleado?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger">Sí, Eliminar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Mostrar mensajes si existen -->
  <?php if (!empty($mensaje)) { include '../../Controlador/Mensajes.php'; } ?>

  <!-- Script de funcionalidad -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Selecciona botones de edición y eliminación
      const editarBotones = document.querySelectorAll('.btnEditar');
      const eliminarBotones = document.querySelectorAll('.btnEliminar');
      const modalEditar = new bootstrap.Modal(document.getElementById('EditarEmpleado'));
      const modalEliminar = new bootstrap.Modal(document.getElementById('EliminarEmpleado'));

      // Asignar eventos a botones de editar
      editarBotones.forEach(btn => {
        btn.addEventListener('click', function () {
          const fila = this.closest('tr');
          const celdas = fila.querySelectorAll('td');

          // Llenar el modal con los datos del empleado
          document.getElementById('modalCedula').value = celdas[0].textContent.trim();
          document.getElementById('modalNombre').value = celdas[1].textContent.trim();
          document.getElementById('modalApellido').value = celdas[2].textContent.trim();
          document.getElementById('modalCorreo').value = celdas[3].textContent.trim();
          document.getElementById('modalDireccion').value = celdas[4].textContent.trim();
          document.getElementById('modalTelefono').value = celdas[5].textContent.trim();

          modalEditar.show();
        });
      });

      // Asignar eventos a botones de eliminar
      eliminarBotones.forEach(btn => {
        btn.addEventListener('click', function () {
          const fila = this.closest('tr');
          const cedula = fila.querySelector('td').textContent.trim();
          document.getElementById('eliminarCedula').value = cedula;
          modalEliminar.show();
        });
      });
    });
  </script>

  <!-- Librerías JS -->
  <script src="../Recursos/Librerias/jquery/jquery-3.3.1.min.js"></script>
  <script src="../Recursos/Librerias/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
  <script src="../Recursos/Librerias/datatables.min.js"></script>
  <script src="../JavaScript/Funciones.js"></script>
  <script src="../JavaScript/TablaEmpleados.js"></script>
</body>
</html>

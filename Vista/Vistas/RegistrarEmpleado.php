<?php 
// Verificar sesión y permisos del usuario para acceso a esta página

//Equipo que gana no se toca
// Verificar sesión y permisos del usuario para acceso a esta página
session_start();
include "../../controlador/VerificarsesionCargo.php";

// Recuperar datos anteriores y errores
$oldInput = $_SESSION['old_input'] ?? [];
$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? '';

// Limpiar los datos de sesión después de usarlos
unset($_SESSION['old_input']);
unset($_SESSION['errors']);
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registrar Empleado</title>

  <!-- Estilos CSS personalizados y librerías -->
  <link rel="stylesheet" href="../Recursos/Css/style.css">
  <link rel="stylesheet" href="../Recursos/Css/normalize.css">
  <link rel="stylesheet" href="../Recursos/Librerias/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../Recursos/Librerias/icons-1.13.1/font/bootstrap-icons.min.css">


  <link rel="icon" href="../Recursos/Iconos/house.svg">

  <style>
    /* Añade un espacio superior para que el contenido no quede pegado al header */
    main {
      padding-top: 100px;
    }
    .error-message {
      color: #dc3545;
      font-size: 0.875em;
      margin-top: 0.25rem;
    }
    .is-invalid {
      border-color: #dc3545 !important;
    }
    .success-message {
      color: #198754;
      font-size: 1em;
      margin-bottom: 1rem;
      text-align: center;
    }
  </style>
</head>
<body>

<header>
  <!-- Menú de navegación -->
  <?php include '../../Controlador/Menu.php'; ?>
</header>

<main class="container">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      <!-- Card Bootstrap para formulario -->
      <div class="card shadow-lg rounded-4 p-4">

        <h2 class="text-center mb-4">Registro de Empleado</h2>

        

        <!-- Formulario para registrar empleado -->
        <form action="../../Controlador/RegistrarEmpleado.php" method="POST" class="row g-3" autocomplete="off">

          <!-- Cédula -->
          <div class="col-md-6">
            <label for="cedula" class="form-label">Cédula <b class="text-danger">*</b></label>
            <input type="text" id="cedula" name="cedula" class="form-control <?= isset($errors['cedula']) ? 'is-invalid' : '' ?>" 
                   placeholder="Ingresa la cédula" required oninput="validateInput(this)" 
                   minlength="7" maxlength="8" value="<?= htmlspecialchars($oldInput['cedula'] ?? '') ?>">
            <?php if (isset($errors['cedula'])): ?>
              <div class="error-message"><?= $errors['cedula'] ?></div>
            <?php endif; ?>
          </div>

          <!-- Nombres -->
          <div class="col-md-6">
            <label for="nombres" class="form-label">Nombres <b class="text-danger">*</b></label>
            <input type="text" id="nombres" name="nombres" class="form-control <?= isset($errors['nombres']) ? 'is-invalid' : '' ?>" 
                   placeholder="Ingresa los nombres" required minlength="3" maxlength="30" 
                   oninput="validarSoloLetras(this)" value="<?= htmlspecialchars($oldInput['nombres'] ?? '') ?>">
            <?php if (isset($errors['nombres'])): ?>
              <div class="error-message"><?= $errors['nombres'] ?></div>
            <?php endif; ?>
          </div>

          <!-- Apellidos -->
          <div class="col-md-6">
            <label for="apellidos" class="form-label">Apellidos <b class="text-danger">*</b></label>
            <input type="text" id="apellidos" name="apellidos" class="form-control <?= isset($errors['apellidos']) ? 'is-invalid' : '' ?>" 
                   placeholder="Ingresa los apellidos" required minlength="3" maxlength="30" 
                   oninput="validarSoloLetras(this)" value="<?= htmlspecialchars($oldInput['apellidos'] ?? '') ?>">
            <?php if (isset($errors['apellidos'])): ?>
              <div class="error-message"><?= $errors['apellidos'] ?></div>
            <?php endif; ?>
          </div>

          <!-- Correo electrónico -->
          <div class="col-md-6">
            <label for="correo" class="form-label">Correo <b class="text-danger">*</b></label>
            <input type="email" id="correo" name="correo" class="form-control <?= isset($errors['correo']) ? 'is-invalid' : '' ?>" 
                   placeholder="Ingresa el correo electrónico" required oninput="validarCorreoInput(this)" 
                   value="<?= htmlspecialchars($oldInput['correo'] ?? '') ?>">
            <?php if (isset($errors['correo'])): ?>
              <div class="error-message"><?= $errors['correo'] ?></div>
            <?php endif; ?>
          </div>

          <!-- Dirección -->
          <div class="col-md-6">
            <label for="direccion" class="form-label">Dirección <b class="text-danger">*</b></label>
            <input type="text" id="direccion" name="direccion" class="form-control <?= isset($errors['direccion']) ? 'is-invalid' : '' ?>" 
                   placeholder="Ingresa la dirección" required oninput="validarInputLetNum(this)" 
                   minlength="5" maxlength="100" value="<?= htmlspecialchars($oldInput['direccion'] ?? '') ?>">
            <?php if (isset($errors['direccion'])): ?>
              <div class="error-message"><?= $errors['direccion'] ?></div>
            <?php endif; ?>
          </div>

          <!-- Teléfono -->
          <div class="col-md-6">
            <label for="telefono" class="form-label">Teléfono <b class="text-danger" style="font-size: 14px;">(solo móvil) *</b></label>
            <input type="text" id="telefono" name="telefono" class="form-control <?= isset($errors['telefono']) ? 'is-invalid' : '' ?>" 
                   placeholder="Ingresa el número de teléfono" required minlength="11" maxlength="11" 
                   oninput="validateInput(this)" value="<?= htmlspecialchars($oldInput['telefono'] ?? '') ?>">
            <?php if (isset($errors['telefono'])): ?>
              <div class="error-message"><?= $errors['telefono'] ?></div>
            <?php endif; ?>
          </div>

          <!-- Cargo -->
          <div class="col-md-6">
            <label for="cargo" class="form-label">Cargo <b class="text-danger">*</b></label>
            <select name="cargo" id="cargo" class="form-select <?= isset($errors['cargo']) ? 'is-invalid' : '' ?>" required>
              <option value="" disabled selected>Selecciona un cargo</option>
              <option value="Administrador" <?= (isset($oldInput['cargo']) && $oldInput['cargo'] == 'Administrador') ? 'selected' : '' ?>>Administrador</option>
              <option value="Usuario" <?= (isset($oldInput['cargo']) && $oldInput['cargo'] == 'Usuario') ? 'selected' : '' ?>>Usuario</option>
            </select>
            <?php if (isset($errors['cargo'])): ?>
              <div class="error-message"><?= $errors['cargo'] ?></div>
            <?php endif; ?>
          </div>

          <!-- PIN -->
          <div class="col-md-6">
            <label for="pin" class="form-label">Pin <b class="text-danger">*</b></label>
            <input type="text" id="pin" name="pin" class="form-control <?= isset($errors['pin']) ? 'is-invalid' : '' ?>" 
                   placeholder="Ingresa un PIN de 4 dígitos" required minlength="4" maxlength="4" 
                   oninput="validateInput(this)" value="<?= htmlspecialchars($oldInput['pin'] ?? '') ?>">
            <?php if (isset($errors['pin'])): ?>
              <div class="error-message"><?= $errors['pin'] ?></div>
            <?php endif; ?>
          </div>

          <!-- Contraseña -->
          <div class="col-md-6">
            <label for="contraseña" class="form-label">Contraseña <b class="text-danger">*</b></label>
            <div class="input-group">
              <input type="password" id="contraseña" name="contraseña" class="form-control <?= isset($errors['contraseña']) ? 'is-invalid' : '' ?>" 
                     placeholder="Crea una contraseña segura" required minlength="8" maxlength="12" 
                     oninput="validarInputSinEspacio(this)" value="<?= htmlspecialchars($oldInput['contraseña'] ?? '') ?>">
              <span class="input-group-text" data-bs-toggle="tooltip" data-bs-placement="right"
                title="Requisitos: 8-12 caracteres, 1 mayúscula, 1 número, 1 carácter especial">
                <img src="../Recursos/Iconos/question.svg" alt="Tooltip info">
              </span>
            </div>
            <?php if (isset($errors['contraseña'])): ?>
              <div class="error-message"><?= $errors['contraseña'] ?></div>
            <?php endif; ?>
          </div>

          <!-- Repetir Contraseña -->
          <div class="col-md-6">
            <label for="repetircontraseña" class="form-label">Repetir Contraseña <b class="text-danger">*</b></label>
            <div class="input-group">
              <input type="password" id="repetircontraseña" name="repetircontraseña" class="form-control <?= isset($errors['repetircontraseña']) ? 'is-invalid' : '' ?>" 
                     placeholder="Confirma la contraseña" required minlength="8" maxlength="12" 
                     oninput="validarInputSinEspacio(this)" value="<?= htmlspecialchars($oldInput['repetircontraseña'] ?? '') ?>">
              <span class="input-group-text" data-bs-toggle="tooltip" data-bs-placement="right"
                title="Requisitos: 8-12 caracteres, 1 mayúscula, 1 número, 1 carácter especial">
                <img src="../Recursos/Iconos/question.svg" alt="Tooltip info">
              </span>
            </div>
            <?php if (isset($errors['repetircontraseña'])): ?>
              <div class="error-message"><?= $errors['repetircontraseña'] ?></div>
            <?php endif; ?>
          </div>

          <!-- Mostrar error general si existe -->
          <?php if (isset($errors['general'])): ?>
            <div class="col-12">
              <div class="alert alert-danger"><?= $errors['general'] ?></div>
            </div>
          <?php endif; ?>

          <!-- Botones Limpiar y Registrar -->
          <div class="d-flex justify-content-between mt-4">
            <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary bi bi-trash"> Limpiar</a>
            <button type="submit" class="btn btn-primary">Registrar</button>
          </div>

        </form>

      </div>
    </div>
  </div>
</main>

<!-- Mensajes de notificación -->
<?php if (!empty($mensaje)): ?>
  <?php include '../../Controlador/Mensajes.php'; ?>
<?php endif; ?>

<!-- Scripts JavaScript -->
<script src="../Recursos/Librerias/jquery/jquery-3.3.1.min.js"></script>
<script src="../Recursos/Librerias/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
<script src="../Recursos/Librerias/datatables.min.js"></script>
<script src="../JavaScript/Funciones.js"></script>
<script src="../JavaScript/tooltips.js"></script>

</body>
</html>
<?php
session_start();

// Verificar que el usuario haya validado sus datos primero
if (!isset($_SESSION['cedula_validada'])) {
    $_SESSION['mensaje'] = "Debes validar tus datos primero.";
    header('Location: RecuperarContrasena.php');
    exit();
}

// Obtener mensajes y datos guardados en sesión
$mensaje  = $_SESSION['mensaje']  ?? "";
$errors   = $_SESSION['errors']   ?? [];
$oldInput = $_SESSION['old_input'] ?? [];
$success  = $_SESSION['success']  ?? "";

// Limpiar los datos de sesión después de usarlos
unset($_SESSION['mensaje'], $_SESSION['errors'], $_SESSION['old_input'], $_SESSION['success']);
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Contraseña</title>
  <link rel="stylesheet" href="../Recursos/Css/normalize.css">
  <link rel="stylesheet" href="../Recursos/Css/style.css">
  <link rel="stylesheet" href="../Recursos/Librerias/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../Recursos/Librerias/icons-1.13.1/font/bootstrap-icons.min.css">
  <link rel="icon" href="../Recursos/Iconos/house.svg">
<style type="text/css">
    /* Estilos para el header */
    header {
        background-color: black;
        color: white;
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 0.3vw;
    }

    header .logo img {
        background-color: var(--header-logo-bg);
        width: 4vw; /* Ajusta el tamaño del logo según necesites */
        height: auto;
        border-radius: 0.5vw;
    }

    header .titulo h1 {
        font-size: 2vw;
        margin: 0;
        text-align: center;
        color: white;
    }

    header .fecha p {
        font-size: 1vw;
        margin: 0;
        color: white; 
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
    /* Media queries para la responsividad */
    @media (max-width: 768px) {
        header {
            flex-direction: column;
            text-align: center;
            padding: 2vw;
        }
        header .logo img {
            width: 15vw;
        }
        header .titulo h1 {
            font-size: 4vw;
            margin-top: 1vw;
            margin-bottom: 1vw;
        }
        header .fecha p {
            font-size: 2vw;
        }
    }

    @media (max-width: 576px) {
        header .logo img {
            width: 20vw;
        }
        header .titulo h1 {
            font-size: 3vw;
        }
        header .fecha p {
            font-size: 2.5vw;
        }
    }
  </style>
</head>
<body>
<header>
    <div class="logo">
        <img src="../Recursos/Img/gobierno.png" alt="logo">
    </div>
    <div class="titulo">
        <h1>Sistema de Registro CorviSucre REGIS-Corvi</h1>
    </div>
    <div class="fecha">
        <p><?php echo date('d/m/Y'); ?></p>
    </div>
</header>
  <div class="d-flex justify-content-center align-items-center vh-100 bg-dark">
    <div class="text-white rounded shadow-lg p-5 position-relative" style="width: 400px; background-color: #000 !important;">

      <img src="../Recursos/Img/logo.png" alt="Logo" class="rounded-circle position-absolute"
           style="width: 100px; height: 100px; top: -50px; left: 50%; transform: translateX(-50%);">

      <h2 class="text-center mt-4">Editar Contraseña</h2>

      <form action="../../Controlador/EditarContrasena.php" method="POST"
            onsubmit="return validarContraseñas();" autocomplete="off">
        
        <div class="mb-3">
          <label for="nuevacontraseña" class="form-label">Nueva Contraseña</label>
          <div class="input-group">
            <input type="text" id="nuevacontraseña" name="nuevacontraseña" 
                   class="form-control <?= isset($errors['nuevacontraseña']) ? 'is-invalid' : '' ?>"
                   placeholder="Nueva contraseña" required maxlength="12" minlength="8"
                   value="<?= htmlspecialchars($oldInput['nuevacontraseña'] ?? '') ?>">
            <span class="input-group-text" data-bs-toggle="tooltip" data-bs-placement="right"
                  title="Requisitos: 8-12 caracteres, 1 mayúscula, 1 número, 1 carácter especial">
              <img src="../Recursos/Iconos/question.svg" alt="Requisitos">
            </span>
          </div>
          <?php if (isset($errors['nuevacontraseña'])): ?>
            <div class="error-message"><?= $errors['nuevacontraseña'] ?></div>
          <?php endif; ?>
        </div>

        <div class="mb-3">
          <label for="repetircontraseña" class="form-label">Repite tu Contraseña</label>
          <div class="input-group">
            <input type="text" id="repetircontraseña" name="repetircontraseña" 
                   class="form-control <?= isset($errors['repetircontraseña']) ? 'is-invalid' : '' ?>"
                   placeholder="Repite tu contraseña" required maxlength="12" minlength="8"
                   value="<?= htmlspecialchars($oldInput['repetircontraseña'] ?? '') ?>">
            <span class="input-group-text" data-bs-toggle="tooltip" data-bs-placement="right"
                  title="Requisitos: 8-12 caracteres, 1 mayúscula, 1 número, 1 carácter especial">
              <img src="../Recursos/Iconos/question.svg" alt="Requisitos">
            </span>
          </div>
          <?php if (isset($errors['repetircontraseña'])): ?>
            <div class="error-message"><?= $errors['repetircontraseña'] ?></div>
          <?php endif; ?>
        </div>

        <div class="d-flex justify-content-between mt-4">
          <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary bi bi-trash"> Limpiar</a>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>

        <div class="text-center mt-3">
          <a href="../../" class="text-white" style="text-decoration: none;">Volver al menú de inicio</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Mostrar mensaje desde archivo externo -->
  <?php if (!empty($mensaje)) { 
      include '../../Controlador/Mensajes.php';
  } ?>

  <script src="../Recursos/Librerias/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
  <script src="../JavaScript/Funciones.js"></script>
  <script src="../JavaScript/tooltips.js"></script>
</body>
</html>

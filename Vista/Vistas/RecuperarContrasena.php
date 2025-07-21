<?php 
session_start();

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
  <title>Recuperar contraseña</title>
  <link rel="stylesheet" href="../Recursos/Css/style.css">
  <link rel="stylesheet" href="../Recursos/Css/normalize.css">
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

<div class="d-flex justify-content-center align-items-center vh-100 bg-dark position-relative">
  <div class="text-white rounded shadow-lg p-5 position-relative" style="width: 400px; background-color: #000 !important;">
    
    <img src="../Recursos/Img/logo.png" alt="Logo" 
         class="rounded-circle position-absolute" 
         style="width: 100px; height: 100px; top: -50px; left: 50%; transform: translateX(-50%);">

    <h2 class="text-center mt-4">Recuperar Contraseña</h2>
    
    <form action="../../Controlador/recuperarContrasena.php" method="POST" autocomplete="off">

      <div class="mb-3">
        <label for="username" class="form-label">Cédula</label>
        <input 
          type="text" 
          id="username" 
          name="cedula" 
          class="form-control <?= isset($errors['cedula']) ? 'is-invalid' : '' ?>"
          placeholder="Ingresa tu Cédula" 
          required 
          oninput="validateInput(this)" 
          minlength="7" 
          maxlength="8"
          value="<?= htmlspecialchars($oldInput['cedula'] ?? '') ?>"
        />
        <?php if (isset($errors['cedula'])): ?>
            <div class="error-message"><?= htmlspecialchars($errors['cedula']) ?></div>
        <?php endif; ?>
      </div>

        <div class="mb-3">
    <label for="pin" class="form-label">Pin</label>
    <input 
        type="text" 
        id="pin" 
        name="pin" 
        class="form-control <?= isset($errors['pin']) ? 'is-invalid' : '' ?>" 
        placeholder="Ingresa tu Pin" 
        required 
        minlength="4" 
        maxlength="4" 
        oninput="validateInput(this)" 
        autocomplete="off"
        value="<?= htmlspecialchars($oldInput['pin'] ?? '') ?>"
    />
    <?php if (isset($errors['pin'])): ?>
        <div class="error-message"><?= htmlspecialchars($errors['pin']) ?></div>
    <?php endif; ?>
    </div>


      <div class="d-flex justify-content-between mt-4">
          <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary bi bi-trash"> Limpiar</a>
        <button type="submit" class="btn btn-primary">Comprobar</button>
      </div>

      <div class="text-center mt-3">
        <a href="../../" class="text-white" style="text-decoration:none;">Volver al menú de inicio</a>
      </div>
    </form>

  </div>
</div>

<?php if (!empty($mensaje)) { ?>
  <?php include '../../Controlador/Mensajes.php'; ?> 
<?php } ?>

<script src="../Recursos/Librerias/jquery/jquery-3.3.1.min.js"></script>
<script src="../Recursos/Librerias/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
<script src="../Recursos/Librerias/datatables.min.js"></script>
<script src="../JavaScript/Funciones.js"></script>

</body>
</html>
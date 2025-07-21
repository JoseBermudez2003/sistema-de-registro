<?php
// Iniciar sesión si no está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir conexión a la base de datos
include_once 'controlador/conexion/conexion.php';

/**
 * Verifica si hay usuarios activos
 * @param PDO $pdo
 * @return bool
 */
function hayUsuarios($pdo) {
    $sql = "SELECT COUNT(*) as count FROM usuarios WHERE estado = 1";
    $stmt = $pdo->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
}

/**
 * Verifica si hay empleados activos
 * @param PDO $pdo
 * @return bool
 */
function hayEmpleados($pdo) {
    $sql = "SELECT COUNT(*) as count FROM empleados WHERE estado = 1";
    $stmt = $pdo->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
}

// Obtener mensajes y datos guardados en sesión
$mensaje = $_SESSION['mensaje'] ?? "";
$errors = $_SESSION['errors'] ?? [];
$oldInput = $_SESSION['old_input'] ?? [];
$success = $_SESSION['success'] ?? "";

// Limpiar variables de sesión para evitar que se repitan los mensajes
unset($_SESSION['mensaje'], $_SESSION['errors'], $_SESSION['old_input'], $_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>REGIS-Corvi</title>
  <link rel="stylesheet" href="Vista/Recursos/Css/normalize.css" />
  <link rel="stylesheet" href="Vista/Recursos/Css/style.css" />
  <link rel="stylesheet" href="Vista/Recursos/Librerias/bootstrap-5.0.2-dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="Vista/Recursos/Librerias/icons-1.13.1/font/bootstrap-icons.css" />
  <link rel="icon" href="Vista/Recursos/Iconos/house.svg" />
  <style>
    header {
        background-color: black;
        color: white;
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 0.3vw;
    }
    header .logo img {
        width: 4vw;
        height: auto;
        border-radius: 0.5vw;
    }
    header .titulo h1 {
        font-size: 2vw;
        margin: 0;
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
            margin: 1vw 0;
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
        <img src="Vista/Recursos/Img/gobierno.png" alt="logo" />
    </div>
    <div class="titulo">
        <h1>Sistema de Registro CorviSucre REGIS-Corvi</h1>
    </div>
    <div class="fecha">
        <p><?= date('d/m/Y') ?></p>
    </div>
</header>

<div class="d-flex justify-content-center align-items-center vh-100 bg-dark position-relative">

  <div class="text-white rounded shadow-lg p-5 position-relative" style="width: 400px; background-color: #000 !important;">
    
    <img src="Vista/Recursos/Img/logo.png" alt="Logo" 
         class="rounded-circle position-absolute" 
         style="width: 100px; height: 100px; top: -50px; left: 50%; transform: translateX(-50%);" />

    <h2 class="text-center mt-4">Inicia sesión</h2>


    <form action="Controlador/IniciarSesion.php" method="POST" autocomplete="off">
      
      <div class="mb-3">
  <label for="cedula" class="form-label">Cédula</label>
  <input 
    type="text" 
    id="cedula" 
    name="cedula" 
    class="form-control <?= isset($errors['cedula']) ? 'is-invalid' : '' ?>"
    placeholder="Ingresa tu Cédula" 
    required 
    minlength="7" 
    maxlength="8"
    oninput="validateInput(this)"
    value="<?= htmlspecialchars($oldInput['cedula'] ?? '') ?>"
    autocomplete="off" 
  />
  <?php if (isset($errors['cedula'])): ?>
      <div class="error-message" role="alert"><?= htmlspecialchars($errors['cedula']) ?></div>
  <?php endif; ?>
</div>

<div class="mb-3">
  <label for="password" class="form-label">Contraseña</label>
  <div class="input-group">
    <input 
      type="password" 
      id="password" 
      name="contraseña" 
      class="form-control <?= isset($errors['contraseña']) ? 'is-invalid' : '' ?>"
      placeholder="Ingresa tu contraseña" 
      required 
      minlength="8" 
      maxlength="12"
      autocomplete="current-password" 
      oninput="validarInputSinEspacio(this)" 
    />
    <button 
      type="button" 
      class="btn btn-light" 
      title="Ver contraseña" 
      aria-label="Mostrar/ocultar contraseña" 
      onclick="togglePassword()" 
    >
      <i id="toggle-icon" class="bi bi-eye-fill" style="font-size: 1.1rem;"></i>
    </button>
  </div>
  <?php if (isset($errors['contraseña'])): ?>
    <div class="error-message" role="alert"><?= htmlspecialchars($errors['contraseña']) ?></div>
  <?php endif; ?>
</div>

<div class="d-flex justify-content-between mt-4">
  <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary bi bi-trash"> Limpiar</a>
  <button type="submit" class="btn btn-primary ">Ingresar</button>
</div>


      <div class="text-center mt-3">
        <a href="Vista/Vistas/RecuperarContrasena.php" class="text-white text-decoration-none">
          ¿Olvidaste tu contraseña?
        </a>
      </div>

      <?php if (!hayUsuarios($pdo) && !hayEmpleados($pdo)): ?>
        <div class="text-center mt-2">
          <a href="Vista/Vistas/RegistrarPrimerEmpleado.php" class="text-white text-decoration-none">
            ¿Primer registro?
          </a>
        </div>
      <?php endif; ?>
    </form>
  </div>
</div>

<!-- Mostrar mensajes si existen -->
  <?php if (!empty($mensaje)) { include 'controlador/mensajesIndex.php'; } ?>

<script src="Vista/JavaScript/Funciones.js"></script>
<script src="Vista/Recursos/Librerias/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>

<script>
  // Alternar visibilidad de contraseña
  function togglePassword() {
    const passwordInput = document.getElementById("password");
    const icon = document.getElementById("toggle-icon");

    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      icon.classList.replace("bi-eye-fill", "bi-eye-slash-fill");
    } else {
      passwordInput.type = "password";
      icon.classList.replace("bi-eye-slash-fill", "bi-eye-fill");
    }
  }

  // Validar que no haya espacios en el input
  function validarInputSinEspacio(input) {
    input.value = input.value.replace(/\s/g, '');
  }

  // Validar formato de cédula (solo números)
  function validateInput(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
  }
</script>

</body>
</html>
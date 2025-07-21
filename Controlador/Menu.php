<!-- Estilo CSS para los iconos blancos con transición al pasar el mouse -->
<style>
  .icon-white {
    filter: invert(1); /* Iconos blancos */
    width: 18px;       /* Tamaño consistente de los iconos */
    margin-right: 8px; /* Espaciado a la derecha del icono para separar del texto */
    transition: transform 0.2s; /* Animación para el zoom al pasar el mouse */
  }
  .nav-item:hover .icon-white {
    transform: scale(1.1); /* Efecto sutil de aumentar el tamaño del icono al pasar el mouse */
  }
</style>

<!-- Barra de navegación -->
<nav class="navbar navbar-dark bg-dark fixed-top shadow-sm">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    
    <!-- Botón de menú hamburguesa, muestra un menú lateral (offcanvas) en pantallas pequeñas -->
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar"
      aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span> <!-- Icono del menú hamburguesa -->
    </button>

    <!-- Título del sistema, visible solo en pantallas medianas y grandes -->
    <p class="navbar-brand ms-2 d-none d-md-block">
      SISTEMA DE GESTION DEL DEPARTAMENTO SOCIAL EN CORVISCURE
    </p>

    <!-- Menú desplegable para mostrar la imagen del usuario -->
    <div class="dropdown">
      <a href="#" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="../Recursos/Iconos/person.svg" class="rounded-circle bg-white me-2" style="width: 2em;" alt="Usuario"> <!-- Imagen del usuario -->
      </a>
      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark text-center p-3" aria-labelledby="dropdownUser" style="min-width: 200px;">
        <!-- Información del usuario -->
        <li class="fw-bold text-white">Información</li>
        <li><hr class="dropdown-divider"></li> <!-- Separador -->
        <li class="fw-bold text-white"><?php echo $_SESSION['nombres']?></li> <!-- Nombre del usuario -->
        <li class="fw-bold text-white"><?php echo $_SESSION['cedula']?></li> <!-- Nombre del usuario -->
        <li class="fw-bold text-info mb-3">Cargo: <?php echo $_SESSION['cargo']; ?></li> <!-- Cargo del usuario -->
        <li><hr class="dropdown-divider"></li> <!-- Separador -->
        <!-- Opción para cerrar sesión -->
        <li><a class="btn btn-danger btn-sm w-100" href="../../Controlador/CerrarSesion.php">Cerrar sesión</a></li>
      </ul>
    </div>
  </div>

  <!-- Menú lateral (offcanvas) para pantallas pequeñas -->
  <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar"
    aria-labelledby="offcanvasDarkNavbarLabel" style="width: 18em; background: linear-gradient(to bottom right, #000000, #1a1a1a);">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title text-info" id="offcanvasDarkNavbarLabel">Menú</h5>
      <!-- Botón para cerrar el menú lateral -->
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <!-- Cuerpo del menú lateral -->
    <div class="offcanvas-body d-flex flex-column justify-content-between">
      <ul class="navbar-nav flex-column w-100">
        <!-- Opción "Inicio" -->
        <li class="nav-item mb-2">
          <a class="btn btn-outline-info w-100 d-flex align-items-center" href="index.php">
            <img src="../Recursos/Iconos/house.svg" class="icon-white" alt="Inicio"> <span>Inicio</span> <!-- Icono de inicio -->
          </a>
        </li>

        <!-- Menú para el rol ADMINISTRADOR -->
        <?php if ($cargo == "Administrador"): ?>
          <li class="nav-item mb-2">
            <div class="dropdown w-100">
              <button class="btn btn-outline-info w-100 dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="../Recursos/Iconos/empleados.svg" class="icon-white" alt="Empleados"> <span>Empleados</span> <!-- Menú de empleados -->
              </button>
              <ul class="dropdown-menu dropdown-menu-dark">
                <li><a class="dropdown-item d-flex align-items-center" href="RegistrarEmpleado.php"><img src="../Recursos/Iconos/empleados.svg" class="icon-white" alt="Registrar"> Registrar Empleados</a></li>
                <li><a class="dropdown-item d-flex align-items-center" href="AdministrarEmpleado.php"><img src="../Recursos/Iconos/empleados.svg" class="icon-white" alt="Administrar"> Administrar Empleados</a></li>
              </ul>
            </div>
          </li>

          <li class="nav-item mb-2">
            <a class="btn btn-outline-info w-100 d-flex align-items-center" href="historial.php">
              <img src="../Recursos/Iconos/historial.svg" class="icon-white" alt="Historial"> <span>Historial</span> <!-- Menú de historial -->
            </a>
          </li>
        <?php endif; ?>

        <!-- Menú de CLIENTES -->
        <li class="nav-item mb-2">
          <div class="dropdown w-100">
            <button class="btn btn-outline-info w-100 dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="../Recursos/Iconos/clientes.svg" class="icon-white" alt="Clientes"> <span>Beneficiarios</span> <!-- Menú de clientes -->
            </button>
            <ul class="dropdown-menu dropdown-menu-dark">
              <li><a class="dropdown-item d-flex align-items-center" href="RegistrarCliente.php"><img src="../Recursos/Iconos/clientes.svg" class="icon-white" alt="Registrar"> Registrar beneficiarios</a></li>
              <li><a class="dropdown-item d-flex align-items-center" href="ListaClientes.php"><img src="../Recursos/Iconos/clientes.svg" class="icon-white" alt="Lista"> Lista de beneficiarios</a></li>
              <li><a class="dropdown-item d-flex align-items-center" href="ListaCasos.php"><img src="../Recursos/Iconos/casos.svg" class="icon-white" alt="Casos"> Lista de Casos</a></li>
            </ul>
          </div>
        </li>

        <!-- Menú de INFORMES -->
        <li class="nav-item mb-2">
          <div class="dropdown w-100">
            <button class="btn btn-outline-info w-100 dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="../Recursos/Iconos/verInforme.svg" class="icon-white" alt="Informes"> <span>Informes</span> <!-- Menú de informes -->
            </button>
            <ul class="dropdown-menu dropdown-menu-dark">
              <li><a class="dropdown-item d-flex align-items-center" href="GenerarInformes.php"><img src="../Recursos/Iconos/verInforme.svg" class="icon-white" alt="Crear"> Crear Informe</a></li>
              <li><a class="dropdown-item d-flex align-items-center" href="VerInformes.php"><img src="../Recursos/Iconos/verInforme.svg" class="icon-white" alt="Ver"> Ver Informes</a></li>
            </ul>
          </div>
        </li>

      </ul>
    </div>
  </div>
</nav>

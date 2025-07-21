<?php 
// Verificar sesión activa y permisos
include "../../controlador/verificarsesion.php"; 

// Obtener datos para las gráficas de casos según filtros de tiempo
include "../../controlador/CasosGraficas.php"; 

// Inicializar mensaje si no existe
$mensaje = $mensaje ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Principal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Estilos CSS -->
    <link rel="stylesheet" href="../Recursos/Css/normalize.css" />
    <link rel="stylesheet" href="../Recursos/Css/style.css">
    <link rel="stylesheet" href="../Recursos/Librerias/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <!-- Icono de la pestaña -->
    <link rel="icon" href="../Recursos/Iconos/house.svg">
    <style>
        #graficaCasos {
            max-height: 300px;
        }
    </style>
</head>
<body>
<header>
    <!-- Menú principal -->
    <?php include '../../Controlador/Menu.php'; ?>
</header>

<main class="container-fluid mt-5 pt-5">
    <!-- Encabezado de bienvenida -->
    <div class="bg-primary text-white rounded p-4 mb-4 text-center">
        <h2 class="fw-bold">Bienvenido al Sistema de Gestión de Casos</h2>
        <p class="mb-0">Visualización de casos registrados y atendidos</p>
    </div>

    <!-- Botones para filtrar la información por período -->
    <div class="text-center mb-4">
        <button class="btn btn-outline-primary me-2 filtro-btn active" data-filtro="hoy">Hoy</button>
        <button class="btn btn-outline-secondary me-2 filtro-btn" data-filtro="semana">Esta Semana</button>
        <button class="btn btn-outline-success me-2 filtro-btn" data-filtro="mes">Este Mes</button>
        <button class="btn btn-outline-dark filtro-btn" data-filtro="todos">Todos</button>
    </div>

    <!-- Tarjetas resumen y gráfica -->
    <div class="row g-4">
        <!-- Tarjeta casos registrados -->
        <div class="col-md-6">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <h5 id="titulo-registrados" class="card-title">Hoy se ha registrado un total de:</h5>
                    <p id="registrados" class="display-5 fw-bold text-primary">0 casos</p>
                </div>
            </div>
        </div>

        <!-- Tarjeta casos atendidos -->
        <div class="col-md-6">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <h5 id="titulo-atendidos" class="card-title">Hoy se han atendido un total de:</h5>
                    <p id="atendidos" class="display-5 fw-bold text-success">0 casos</p>
                </div>
            </div>
        </div>

        <!-- Gráfica de casos atendidos vs no atendidos -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 id="titulo-grafica" class="card-title text-center">Gráfica de Casos de Hoy</h5>
                    <canvas id="graficaCasos"></canvas>
                    <div class="text-center mt-3">
                        <span id="badgeAtendidos" class="badge bg-info me-2">Atendidos: 0</span>
                        <span id="badgeNoAtendidos" class="badge bg-danger">No Atendidos: 0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

    <!-- Mensajes de notificación -->
<?php if (!empty($mensaje)): ?>
  <?php include '../../Controlador/Mensajes.php'; ?>
<?php endif; ?>

<!-- Scripts JavaScript -->
<script src="../Recursos/Librerias/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
<script src="../Recursos/Librerias/package/dist/chart.umd.js"></script>
<script src="../JavaScript/Funciones.js"></script>

<script>
    // Verificar que los datos estén cargados
    if (typeof datosGraficas === 'undefined') {
        console.error('Error: No se cargaron los datos para las gráficas');
        document.getElementById('titulo-grafica').textContent = 'Error al cargar los datos';
    } else {
        console.log('Datos cargados correctamente:', datosGraficas);
    }

    // Textos para títulos según filtro aplicado
    const titulos = {
        hoy: {
            registrados: "Hoy se ha registrado un total de:",
            atendidos: "Hoy se han atendido un total de:",
            grafica: "Gráfica de Casos de Hoy"
        },
        semana: {
            registrados: "Esta semana se ha registrado un total de:",
            atendidos: "Esta semana se han atendido un total de:",
            grafica: "Gráfica de Casos de la Semana"
        },
        mes: {
            registrados: "Este mes se ha registrado un total de:",
            atendidos: "Este mes se han atendido un total de:",
            grafica: "Gráfica de Casos del Mes"
        },
        todos: {
            registrados: "En total se ha registrado un total de:",
            atendidos: "En total se han atendido un total de:",
            grafica: "Gráfica de Todos los Casos"
        }
    };

    let chart = null;  // Variable global para almacenar la instancia de la gráfica
    const ctx = document.getElementById('graficaCasos').getContext('2d');

    /**
     * Actualiza la vista: títulos, números, badges y gráfica
     * @param {string} filtro - Filtro seleccionado: 'hoy', 'semana', 'mes' o 'todos'
     */
    function actualizarVista(filtro) {
        if (!datosGraficas || !datosGraficas[filtro]) {
            console.error(`Datos no disponibles para el filtro: ${filtro}`);
            return;
        }

        const datos = datosGraficas[filtro];

        // Actualizar textos de títulos
        document.getElementById('titulo-registrados').textContent = titulos[filtro].registrados;
        document.getElementById('titulo-atendidos').textContent = titulos[filtro].atendidos;
        document.getElementById('titulo-grafica').textContent = titulos[filtro].grafica;

        // Actualizar números en tarjetas y badges
        document.getElementById('registrados').textContent = `${datos.registrados} casos`;
        document.getElementById('atendidos').textContent = `${datos.atendidos} casos`;
        document.getElementById('badgeAtendidos').textContent = `Atendidos: ${datos.atendidos}`;
        document.getElementById('badgeNoAtendidos').textContent = `No Atendidos: ${datos.no_atendidos}`;

        // Destruir gráfica anterior si existe
        if (chart) {
            chart.destroy();
        }

        // Crear nueva gráfica de pastel (pie chart)
        chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Atendidos', 'No Atendidos'],
                datasets: [{
                    data: [datos.atendidos, datos.no_atendidos],
                    backgroundColor: ['rgba(75,192,192,0.7)', 'rgba(255,99,132,0.7)'],
                    borderColor: ['rgba(75,192,192,1)', 'rgba(255,99,132,1)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Actualizar clase activa en botones filtro
        document.querySelectorAll('.filtro-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.filtro === filtro) {
                btn.classList.add('active');
            }
        });
    }

    // Asignar evento click a cada botón para cambiar el filtro
    document.querySelectorAll('.filtro-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (datosGraficas && datosGraficas[btn.dataset.filtro]) {
                actualizarVista(btn.dataset.filtro);
            } else {
                console.error(`Datos no disponibles para el filtro: ${btn.dataset.filtro}`);
            }
        });
    });

    // Inicializar vista con filtro 'hoy' cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
        if (datosGraficas && datosGraficas.hoy) {
            actualizarVista('hoy');
        } else {
            console.error('No se pudieron cargar los datos iniciales');
        }
    });
</script>
</body>
</html>
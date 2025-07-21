<?php
// Incluye controlador que obtiene la lista de casos y verifica sesión activa
include "../../controlador/ListaCasos.php";
require "../../controlador/verificarsesion.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lista de Casos</title>

    <link rel="stylesheet" href="../Recursos/Css/normalize.css" />
    <link rel="stylesheet" href="../Recursos/Css/style.css" />
    <link rel="stylesheet" href="../Recursos/Librerias/datatables.min.css" />
    <link rel="stylesheet" href="../Recursos/Librerias/bootstrap-5.0.2-dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../Recursos/Librerias/icons-1.13.1/font/bootstrap-icons.css" />
    <link rel="icon" href="../Recursos/Iconos/house.svg" />
</head>

<body>
    <header>
        <?php include '../../Controlador/Menu.php'; ?>
    </header>

    <main class="container mt-5 pt-5">
        <h2 class="text-center mb-4">Administrar Casos</h2>

       <!-- Tarjeta de Opciones de Filtrado -->
<div class="card mb-4 shadow-sm">
  <!-- Encabezado con botón de colapsar -->
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <h5 class="mb-0 fw-bold text-primary">
      <i class="bi bi-funnel-fill me-2"></i>Opciones de Filtrado
    </h5>
    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="true" aria-controls="filtersCollapse">
      <i class="bi bi-chevron-down toggle-icon"></i>
    </button>
  </div>

  <!-- Contenido colapsable -->
  <div class="collapse" id="filtersCollapse">
    <div class="card-body">
      <div class="row g-3">

        <!-- Filtro por tipo de caso -->
        <div class="col-12 col-lg-6">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
              <h6 class="card-title fw-bold text-dark mb-3">
                <i class="bi bi-tag-fill me-2"></i>Filtrar por prioridad:
              </h6>
              <div class="d-flex flex-wrap gap-2 justify-content-center">
                <button class="btn btn-primary filter-btn active" data-filter="">
                  <i class="bi bi-collection me-2"></i>Todos
                </button>
                <button class="btn btn-outline-danger filter-btn" data-filter="Crítico">
                  <i class="bi bi-exclamation-triangle-fill me-2"></i>Crítico
                </button>
                <button class="btn btn-outline-warning filter-btn" data-filter="Grande">
                  <i class="bi bi-exclamation-circle-fill me-2"></i>Grande
                </button>
                <button class="btn btn-outline-primary filter-btn" data-filter="Normal">
                  <i class="bi bi-info-circle-fill me-2"></i>Normal
                </button>
                <button class="btn btn-outline-success filter-btn" data-filter="Bajo">
                  <i class="bi bi-check-circle-fill me-2"></i>Bajo
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Filtros adicionales -->
        <div class="col-12 col-lg-6">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column justify-content-between">
              <div>
                <h6 class="card-title fw-bold text-dark mb-3">
                  <i class="bi bi-sliders me-2"></i>Filtros Adicionales:
                </h6>
                <div class="row g-3 align-items-end">
                  <div class="col-md-6">
                    <label for="filtroAtendido" class="form-label fw-bold">Atendido:</label>
                    <select id="filtroAtendido" class="form-select form-select-sm">
                      <option value="">Todos</option>
                      <option value="Si">Sí</option>
                      <option value="No">No</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="filtroFecha" class="form-label fw-bold">Fecha de Registro:</label>
                    <input type="date" id="filtroFecha" class="form-control form-control-sm" />
                  </div>
                </div>
              </div>
              <!-- Botón para limpiar filtros -->
              <div class="mt-4 d-grid">
                <button id="clearFiltersBtn" class="btn btn-secondary">
                  <i class="bi bi-x-circle me-2"></i>Limpiar Filtros
                </button>
              </div>
            </div>
          </div>
        </div>

      </div> <!-- Fin row -->
    </div> <!-- Fin card-body -->
  </div> <!-- Fin collapse -->
</div> <!-- Fin card principal -->


        <div class="table-responsive">
            <table id="myTable" class="table table-striped table-bordered">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Id</th>
                        <th>Información del Empleado</th>
                        <th>Nombre del Beneficiario</th>
                        <th class="no-order">Motivo</th>
                        <th class="no-order">Atendido</th>
                        <th class="no-order">Fecha de registro</th>
                        <th class="no-export no-order">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($casos as $caso): ?>
                        <tr>
                            <td><?= htmlspecialchars($caso['id']) ?></td>
                            <td><?= htmlspecialchars($caso['empleado_nombre']) . " " . htmlspecialchars($caso['empleado_apellido']) ?></td>
                            <td><?= htmlspecialchars($caso['clientes_nombre']) . " " . htmlspecialchars($caso['clientes_apellido']) ?></td>
                            <td><?= htmlspecialchars($caso['caso_motivo']) ?></td>
                            <td><?= htmlspecialchars($caso['caso_atentido']) ?></td>
                            <td><?= htmlspecialchars($caso['fecha_creado']) ?></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center flex-wrap gap-2">
                                    <button
                                        class="btn btn-info btn-sm btnDescripcion"
                                        data-bs-toggle="modal"
                                        data-bs-target="#ModalDescripcion"
                                        data-descripcion="<?= htmlspecialchars($caso['caso_descrip']) ?>"
                                        data-tipo="<?= htmlspecialchars($caso['tipo_caso']) ?>"
                                        data-atendido="<?= htmlspecialchars($caso['caso_atentido']) ?>"
                                        data-fecha_atendido="<?= isset($caso['fecha_atendido']) ? htmlspecialchars($caso['fecha_atendido']) : '' ?>"
                                        data-resumen="<?= isset($caso['resumen_atencion']) ? htmlspecialchars($caso['resumen_atencion']) : '' ?>"
                                        data-por="<?= isset($caso['atendido_por']) ? htmlspecialchars($caso['atendido_por']) : '' ?>"
                                        data-cedula_empleado="<?= htmlspecialchars($caso['cedula_empleado']) ?>"
                                        data-cedula_cliente="<?= htmlspecialchars($caso['cedula_cliente']) ?>"
                                        data-direccion="<?= htmlspecialchars($caso['direccion']) ?>"
                                        title="Ver información completa"
                                    >
                                        <img src="../Recursos/Iconos/descripcion.svg" alt="Descripción" width="20" height="20" />
                                    </button>

                                    <button
                                        class="btn btn-primary btn-sm btnEditar"
                                        data-bs-toggle="modal"
                                        data-bs-target="#EditarCaso"
                                        data-id="<?= $caso['id'] ?>"
                                        data-motivo="<?= htmlspecialchars($caso['caso_motivo']) ?>"
                                        data-tipo="<?= htmlspecialchars($caso['tipo_caso']) ?>"
                                        data-atendido="<?= htmlspecialchars($caso['caso_atentido']) ?>"
                                        data-descripcion="<?= htmlspecialchars($caso['caso_descrip']) ?>"
                                        data-fecha_atendido="<?= isset($caso['fecha_atendido']) ? htmlspecialchars($caso['fecha_atendido']) : '' ?>"
                                        data-resumen="<?= isset($caso['resumen_atencion']) ? htmlspecialchars($caso['resumen_atencion']) : '' ?>"
                                        data-por="<?= isset($caso['atendido_por']) ? htmlspecialchars($caso['atendido_por']) : '' ?>"
                                        title="Editar caso"
                                    >
                                        <img src="../Recursos/Iconos/editar.svg" alt="Editar" width="20" height="20" />
                                    </button>

                                    <button
                                        class="btn btn-danger btn-sm btnEliminar"
                                        data-bs-toggle="modal"
                                        data-bs-target="#EliminarCaso"
                                        data-id="<?= $caso['id'] ?>"
                                        title="Eliminar caso"
                                    >
                                        <img src="../Recursos/Iconos/trash.svg" alt="Eliminar" width="20" height="20" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="ModalDescripcion" tabindex="-1" aria-labelledby="descripcionLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fs-4" id="descripcionLabel">
                            <i class="bi bi-file-earmark-text me-2"></i>Información Completa del Caso
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6 d-flex flex-column gap-3">
                                <div class="card shadow-sm flex-fill" style="min-height: 180px;">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fs-6"><i class="bi bi-person-badge me-2"></i>Empleado que registró</h6>
                                    </div>
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0 fw-semibold fs-5" id="infoEmpleado"></h5>
                                            <small class="text-muted fs-6" id="infoCedulaEmpleado"></small>
                                        </div>
                                        <span class="badge bg-info text-white fs-6">
                                            <i class="bi bi-person-fill me-1"></i>Registrador
                                        </span>
                                    </div>
                                </div>

                                <div class="card shadow-sm flex-fill" style="min-height: 180px;">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fs-6"><i class="bi bi-person-vcard me-2"></i>Beneficiario</h6>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="mb-0 fw-semibold fs-5" id="infoCliente"></h5>
                                        <small class="text-muted fs-6" id="infoCedulaCliente"></small>
                                        <div class="mt-3">
                                            <h6 class="mb-1 fs-6">Dirección:</h6>
                                            <p class="mb-0 fs-6" id="infoDireccion"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 d-flex flex-column gap-3">
                                <div class="card shadow-sm flex-fill border border-3 border-danger" style="min-height: 180px;">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="mb-0 fs-6"><i class="bi bi-clipboard-data me-2"></i>Detalles del Caso</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <h6 class="mb-1 fs-6">Motivo:</h6>
                                            <div class="p-2 bg-light rounded mb-2 fs-6" id="infoMotivo" style="word-break: break-word;"></div>
                                        </div>
                                        
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <h6 class="mb-1 fs-6">Tipo:</h6>
                                                <span class="badge bg-danger text-white fs-6" id="infoTipo"></span>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="mb-1 fs-6">Registro:</h6>
                                                <span class="badge bg-secondary fs-6" id="infoFechaRegistro"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="infoAtendidoContainer" class="flex-fill" style="display: none; min-height: 180px;">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0 fs-6"><i class="bi bi-check-circle me-2"></i>Información de Atención</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <h6 class="mb-1 fs-6">Fecha Atención:</h6>
                                                    <p class="mb-0 fs-6" id="infoFechaAtencion"></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="mb-1 fs-6">Atendido por:</h6>
                                                    <p class="mb-0 fs-6" id="infoAtendidoPor"></p>
                                                </div>
                                                <div class="col-12">
                                                    <h6 class="mb-1 fs-6">Resumen:</h6>
                                                    <div class="p-2 bg-light rounded fs-6" id="infoResumen"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm mt-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fs-6"><i class="bi bi-chat-square-text me-2"></i>Descripción Detallada</h6>
                            </div>
                            <div class="card-body">
                                <div id="descripcionContenido" class="p-3 bg-light rounded fs-6" style="min-height: 100px; max-height: 250px; overflow-y: auto;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary fs-6" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="EditarCaso" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalLabel">Editar Caso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <form id="formEditarCaso" action="../../controlador/EditarCaso.php" method="POST" autocomplete="off">
                        <div class="modal-body">
                            <input type="hidden" name="id" id="modalId" />

                            <div class="mb-3">
                                <label for="modalMotivo" class="form-label">Motivo</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="modalMotivo" 
                                    name="motivo" 
                                    placeholder="Ej: Documentación" 
                                    required 
                                    minlength="5" 
                                    maxlength="50" 
                                    oninput="validarSoloLetras(this)"
                                >
                            </div>

                            <div class="mb-3">
                                <label for="modalTipoCaso" class="form-label">Prioridad </label>
                                <select name="tipo_caso" id="modalTipoCaso" class="form-select" required>
                                    <option value="" disabled>Seleccione el tipo</option>
                                    <option value="Crítico">Crítico</option>
                                    <option value="Grande">Grande</option>
                                    <option value="Normal">Normal</option>
                                    <option value="Bajo">Bajo</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="modalAtendido" class="form-label">Caso atendido </label>
                                <select class="form-select" name="atendido" id="modalAtendido" required>
                                    <option value="" disabled selected>Selecciona una opción</option>
                                    <option value="Si">Sí</option>
                                    <option value="No">No</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea
                                    class="form-control"
                                    name="descripcion"
                                    id="descripcion"
                                    minlength="5"
                                    maxlength="200"
                                    rows="3"
                                    oninput="validarInputLetNum(this)"
                                    required
                                ></textarea>
                            </div>

                            <div id="camposAdicionales" style="display: none;">
                                <div class="mb-3">
                                    <label for="fechaAtendido" class="form-label">Fecha de atención </label>
                                    <input type="date" class="form-control" id="fechaAtendido" name="fecha_atendido" required>
                                </div>

                                <div class="mb-3">
                                    <label for="resumenAtencion" class="form-label">Resumen de atención </label>
                                    <textarea class="form-control" name="resumen_atencion" id="resumenAtencion" rows="3" maxlength="200" required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="atendidoPor" class="form-label">Atendido por:</label>
                                    <textarea class="form-control" name="atendido_por" id="atendidoPor" rows="2" maxlength="100" required oninput="validarLetrasComasEspacios(this)"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="EliminarCaso" tabindex="-1" aria-labelledby="eliminarLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="eliminarLabel">Confirmar eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <form id="formEliminarCaso" action="../../controlador/EliminarCaso.php" method="POST" autocomplete="off">
                        <div class="modal-body">
                            <input type="hidden" name="id" id="eliminarId" />
                            <p>¿Estás seguro de que deseas eliminar este caso?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Sí, Eliminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php if (!empty($mensaje)) { include '../../Controlador/Mensajes.php'; } ?>

    <script src="../Recursos/Librerias/jquery/jquery-3.3.1.min.js"></script>
    <script src="../Recursos/Librerias/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../Recursos/Librerias/datatables.min.js"></script>
    <script src="../JavaScript/Funciones.js"></script>
    <script src="../JavaScript/TablaCasos.js"></script>

    <script>
        $(document).ready(function () {
            // Inicializa DataTable
            const table = $('#myTable').DataTable();
            
            // Variable para guardar el filtro de tipo
            let tipoFilter = '';

            // Función para aplicar todos los filtros
            function applyAllFilters() {
                // Limpiar búsquedas personalizadas anteriores
                $.fn.dataTable.ext.search = [];

                // Aplicar filtro de Atendido
                const atendidoValue = $('#filtroAtendido').val();
                if (atendidoValue) {
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        // data[4] es la columna 'Atendido'
                        return data[4] === atendidoValue;
                    });
                }
                
                // Aplicar filtro de Fecha de registro
                const fechaValue = $('#filtroFecha').val();
                if (fechaValue) {
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        // data[5] es la columna 'Fecha de registro'
                        return data[5] === fechaValue;
                    });
                }
                
                // Aplicar filtro de Tipo de Caso (desde los botones)
                if (tipoFilter) {
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        const row = table.row(dataIndex).node();
                        const tipo = $(row).find('.btnDescripcion').data('tipo');
                        return tipo === tipoFilter;
                    });
                }
                
                // Redibujar la tabla con todos los filtros
                table.draw();
            }

            // Evento para el ícono de colapso para rotar
            $('#filtersCollapse').on('show.bs.collapse', function () {
                $('.toggle-icon').removeClass('bi-chevron-down').addClass('bi-chevron-up');
            }).on('hide.bs.collapse', function () {
                $('.toggle-icon').removeClass('bi-chevron-up').addClass('bi-chevron-down');
            });


            // Evento para los botones de filtro por tipo de caso
            $('.filter-btn').on('click', function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                tipoFilter = $(this).data('filter') || ''; // Si es 'Todos', será un string vacío
                applyAllFilters();
            });

            // Evento para el filtro por Atendido
            $('#filtroAtendido').on('change', function () {
                applyAllFilters();
            });

            // Evento para el filtro por Fecha de registro
            $('#filtroFecha').on('change', function () {
                applyAllFilters();
            });

            // Evento para limpiar todos los filtros
            $('#clearFiltersBtn').on('click', function() {
                $('.filter-btn').removeClass('active');
                $('.filter-btn[data-filter=""]').addClass('active'); // Activa el botón 'Todos'
                tipoFilter = ''; // Resetea la variable del filtro de tipo
                $('#filtroAtendido').val(''); // Resetea el selector de atendido
                $('#filtroFecha').val(''); // Resetea el input de fecha
                applyAllFilters(); // Aplica los filtros (ahora vacíos)
            });

            // Mostrar información completa del caso
            $(document).on('click', '.btnDescripcion', function () {
                const row = $(this).closest('tr');
                const rowData = table.row(row).data();
                
                const tipo = $(this).data('tipo');
                const descripcion = $(this).data('descripcion');
                const fechaAtendido = $(this).data('fecha_atendido');
                const resumen = $(this).data('resumen');
                const atendidoPor = $(this).data('por');
                const cedulaEmpleado = $(this).data('cedula_empleado');
                const cedulaCliente = $(this).data('cedula_cliente');
                const direccion = $(this).data('direccion');
                
                $('#infoEmpleado').text(rowData[1]);
                $('#infoCedulaEmpleado').text(`Cédula: ${cedulaEmpleado || 'No disponible'}`);
                $('#infoCliente').text(rowData[2]);
                $('#infoCedulaCliente').text(`Cédula: ${cedulaCliente || 'No disponible'}`);
                $('#infoDireccion').text(direccion || 'No disponible');
                $('#infoMotivo').text(rowData[3]);
                $('#infoTipo').text(tipo);
                $('#infoFechaRegistro').text(rowData[5]);
                $('#descripcionContenido').text(descripcion);
                
                if ($(this).data('atendido') === 'Si') {
                    $('#infoAtendidoContainer').show();
                    $('#infoFechaAtencion').text(fechaAtendido || 'No especificada');
                    $('#infoAtendidoPor').text(atendidoPor || 'No especificado');
                    $('#infoResumen').text(resumen || 'No hay resumen');
                } else {
                    $('#infoAtendidoContainer').hide();
                }
            });

            // Rellenar modal de edición
            $(document).on('click', '.btnEditar', function () {
                $('#modalId').val($(this).data('id'));
                $('#modalMotivo').val($(this).data('motivo'));
                $('#modalTipoCaso').val($(this).data('tipo'));
                $('#modalAtendido').val($(this).data('atendido'));
                $('#descripcion').val($(this).data('descripcion'));
                
                $('#fechaAtendido').val($(this).data('fecha_atendido') || '');
                $('#resumenAtencion').val($(this).data('resumen') || '');
                $('#atendidoPor').val($(this).data('por') || '');

                if ($(this).data('atendido') === 'Si') {
                    $('#camposAdicionales').slideDown();
                    $('#fechaAtendido, #resumenAtencion, #atendidoPor').prop('required', true);
                } else {
                    $('#camposAdicionales').slideUp();
                    $('#fechaAtendido, #resumenAtencion, #atendidoPor').prop('required', false);
                }
            });

            // Mostrar campos según selección
            $('#modalAtendido').on('change', function () {
                const valor = $(this).val();
                if (valor === 'Si') {
                    $('#camposAdicionales').slideDown();
                    $('#fechaAtendido, #resumenAtencion, #atendidoPor').prop('required', true);
                } else {
                    $('#camposAdicionales').slideUp();
                    $('#fechaAtendido, #resumenAtencion, #atendidoPor').prop('required', false);
                }
            });

            // Rellenar modal eliminar
            $(document).on('click', '.btnEliminar', function () {
                $('#eliminarId').val($(this).data('id'));
            });
        });
    </script>
</body>
</html>
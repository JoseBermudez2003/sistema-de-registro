<?php
session_start();

// Incluir controladores necesarios
include "../../controlador/ListaClientes.php";    // Obtiene lista de clientes
include "../../controlador/verificarsesion.php"; // Verifica que la sesión esté activa


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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Beneficiarios</title>

    <!-- Estilos CSS -->
    <link rel="stylesheet" href="../Recursos/Css/style.css">
    <link rel="stylesheet" href="../Recursos/Css/normalize.css">
    <link rel="stylesheet" href="../Recursos/Librerias/datatables.min.css">
    <link rel="stylesheet" href="../Recursos/Librerias/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Recursos/Librerias/icons-1.13.1/font/bootstrap-icons.min.css">
    <link rel="icon" href="../Recursos/Iconos/house.svg">

    <style>
        main {
            padding-top: 100px; /* Espacio para header fijo */
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
    <!-- Encabezado y menú de navegación -->
    <header>
        <?php include '../../Controlador/Menu.php'; ?>
    </header>

    <!-- Contenido principal -->
    <main class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow-lg rounded-4 p-4">
                    <h2 class="text-center mb-4">Registro de Beneficiarios</h2>

                    <!-- Mostrar mensaje de éxito si existe -->
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success success-message"><?= $success ?></div>
                    <?php endif; ?>

                    <!-- Botón para mostrar lista de clientes registrados -->
                    <div class="text-center mb-4">
                        <button type="button" class="btn btn-outline-info w-75" data-bs-toggle="modal" data-bs-target="#InfoCliente">
                            <img src="../Recursos/Iconos/search.svg" class="me-2" style="width: 1.8em;" alt="Buscar">
                            Buscar un beneficiario ya registrado
                        </button>
                    </div>

                    <!-- Formulario de registro -->
                    <form action="../../Controlador/registrarCliente.php" method="POST" autocomplete="off">

                        <!-- Fila: Cédula y Teléfono -->
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="cedula" class="form-label">Cédula <b class="text-danger">*</b></label>
                                <input type="text" class="form-control <?= isset($errors['cedula']) ? 'is-invalid' : '' ?>" 
                                       id="cedula" name="cedula" placeholder="Ingrese la cédula" 
                                       required minlength="7" maxlength="8" oninput="validateInput(this)"
                                       value="<?= htmlspecialchars($oldInput['cedula'] ?? '') ?>">
                                <?php if (isset($errors['cedula'])): ?>
                                    <div class="error-message"><?= $errors['cedula'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="telefono" class="form-label">Teléfono <b class="text-danger" style="font-size: 14px;">(solo móvil) *</b></label>
                                <input type="tel" class="form-control <?= isset($errors['telefono']) ? 'is-invalid' : '' ?>" 
                                       id="telefonoCliente" name="telefono" placeholder="Ingrese el teléfono" 
                                       required minlength="11" maxlength="11" oninput="validateInput(this)"
                                       value="<?= htmlspecialchars($oldInput['telefono'] ?? '') ?>">
                                <?php if (isset($errors['telefono'])): ?>
                                    <div class="error-message"><?= $errors['telefono'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Fila: Nombres y Apellidos -->
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="nombres" class="form-label">Nombres <b class="text-danger">*</b></label>
                                <input type="text" class="form-control <?= isset($errors['nombres']) ? 'is-invalid' : '' ?>" 
                                       id="nombresClientes" name="nombres" placeholder="Ingrese los nombres" 
                                       required minlength="3" maxlength="50" oninput="validarSoloLetras(this)"
                                       value="<?= htmlspecialchars($oldInput['nombres'] ?? '') ?>">
                                <?php if (isset($errors['nombres'])): ?>
                                    <div class="error-message"><?= $errors['nombres'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="apellidos" class="form-label">Apellidos <b class="text-danger">*</b></label>
                                <input type="text" class="form-control <?= isset($errors['apellidos']) ? 'is-invalid' : '' ?>" 
                                       id="apellidosClientes" name="apellidos" placeholder="Ingrese los apellidos" 
                                       required minlength="3" maxlength="50" oninput="validarSoloLetras(this)"
                                       value="<?= htmlspecialchars($oldInput['apellidos'] ?? '') ?>">
                                <?php if (isset($errors['apellidos'])): ?>
                                    <div class="error-message"><?= $errors['apellidos'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección <b class="text-danger">*</b></label>
                            <input type="text" class="form-control <?= isset($errors['direccion']) ? 'is-invalid' : '' ?>" 
                                   id="direccionCliente" name="direccion" placeholder="Ingrese la dirección" 
                                   required minlength="5" maxlength="100" oninput="validarInputLetNum(this)"
                                   value="<?= htmlspecialchars($oldInput['direccion'] ?? '') ?>">
                            <?php if (isset($errors['direccion'])): ?>
                                <div class="error-message"><?= $errors['direccion'] ?></div>
                            <?php endif; ?>
                        </div>
<!-- Línea divisoria decorativa -->
<hr style=" height: 4px; background-color:red; border: none; margin: 30px auto 15px auto; border-radius: 3px;">

                        <!-- Fila: Motivo, Tipo y Descripción -->
<div class="row">
    <!-- Motivo -->
    <div class="mb-3 col-md-4">
        <label for="motivo" class="form-label">Motivo <b class="text-danger">*</b></label>
        <input type="text" class="form-control <?= isset($errors['motivo']) ? 'is-invalid' : '' ?>" 
            id="motivo" name="motivo" placeholder="Ej: Documentación" 
            required minlength="5" maxlength="50" oninput="validarInputLetNum(this)"
            value="<?= htmlspecialchars($oldInput['motivo'] ?? '') ?>">
        <?php if (isset($errors['motivo'])): ?>
            <div class="invalid-feedback"><?= $errors['motivo'] ?></div>
        <?php endif; ?>
    </div>

    <!-- Tipo de caso -->
    <div class="mb-3 col-md-4">
        <label for="tipo" class="form-label">Prioridad <b class="text-danger">*</b></label>
        <select name="tipo" id="tipo" class="form-select <?= isset($errors['tipo']) ? 'is-invalid' : '' ?>" required>
            <option value="" disabled selected>Seleccione el tipo</option>
            <option value="Crítico" <?= (isset($oldInput['tipo']) && $oldInput['tipo'] === 'Crítico') ? 'selected' : '' ?>>Crítico</option>
            <option value="Grande" <?= (isset($oldInput['tipo']) && $oldInput['tipo'] === 'Grande') ? 'selected' : '' ?>>Grande</option>
            <option value="Normal" <?= (isset($oldInput['tipo']) && $oldInput['tipo'] === 'Normal') ? 'selected' : '' ?>>Normal</option>
            <option value="Bajo" <?= (isset($oldInput['tipo']) && $oldInput['tipo'] === 'Bajo') ? 'selected' : '' ?>>Bajo</option>
        </select>
        <?php if (isset($errors['tipo'])): ?>
            <div class="invalid-feedback"><?= $errors['tipo'] ?></div>
        <?php endif; ?>
    </div>

    <!-- Descripción -->
    <div class="mb-3 col-md-12">
        <label for="descripcion" class="form-label">Descripción <b class="text-danger">*</b></label>
        <textarea class="form-control <?= isset($errors['descripcion']) ? 'is-invalid' : '' ?>" 
            id="descripcion" name="descripcion" placeholder="Describe brevemente el caso" 
            rows="4" required maxlength="200"><?= htmlspecialchars($oldInput['descripcion'] ?? '') ?></textarea>
        <?php if (isset($errors['descripcion'])): ?>
            <div class="invalid-feedback"><?= $errors['descripcion'] ?></div>
        <?php endif; ?>
    </div>
</div>



                        <!-- Mostrar error general si existe -->
                        <?php if (isset($errors['general'])): ?>
                            <div class="col-12">
                                <div class="alert alert-danger"><?= $errors['general'] ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Botones del formulario -->
                        <div class="d-flex justify-content-between mt-3">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary bi bi-trash"> Limpiar</a>
                            <button type="submit" class="btn btn-primary">Registrar</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Modal: Lista de Clientes -->
        <div class="modal fade" id="InfoCliente" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content bg-white rounded-4 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Lista de Beneficiarios</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="table-responsive">
                            <table id="myTable" class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="no-order">Seleccionar</th>
                                        <th>Cédula</th>
                                        <th>Nombres</th>
                                        <th>Apellidos</th>
                                        <th class="no-order">Dirección</th>
                                        <th class="no-order">Teléfono</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <tr>
                                            <td>
                                                <button class="btn btn-outline-primary seleccionar-cliente"
                                                    data-cedula="<?= htmlspecialchars($cliente[0]) ?>"
                                                    data-nombres="<?= htmlspecialchars($cliente[1]) ?>"
                                                    data-apellidos="<?= htmlspecialchars($cliente[2]) ?>"
                                                    data-direccion="<?= htmlspecialchars($cliente[3]) ?>"
                                                    data-telefono="<?= htmlspecialchars($cliente[4]) ?>">
                                                    Seleccionar
                                                </button>
                                            </td>
                                            <td><?= htmlspecialchars($cliente[0]) ?></td>
                                            <td><?= htmlspecialchars($cliente[1]) ?></td>
                                            <td><?= htmlspecialchars($cliente[2]) ?></td>
                                            <td><?= htmlspecialchars($cliente[3]) ?></td>
                                            <td><?= htmlspecialchars($cliente[4]) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

<!-- Mostrar mensajes de alerta si existe alguno -->
<?php if (!empty($mensaje)) include '../../Controlador/Mensajes.php'; ?>

    <!-- Scripts JS -->
    <script src="../Recursos/Librerias/jquery/jquery-3.3.1.min.js"></script>
    <script src="../Recursos/Librerias/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../Recursos/Librerias/datatables.min.js"></script>
    <script src="../JavaScript/Funciones.js"></script>
    <script src="../JavaScript/TablaClientes.js"></script>

    <!-- Rellenar formulario con datos del cliente seleccionado -->
    <script>
        $(document).ready(function () {
            $('.seleccionar-cliente').click(function () {
                $('#cedula').val($(this).data('cedula'));
                $('#nombresClientes').val($(this).data('nombres'));
                $('#apellidosClientes').val($(this).data('apellidos'));
                $('#direccionCliente').val($(this).data('direccion'));
                $('#telefonoCliente').val($(this).data('telefono'));

                // Limpiar motivo y descripción
                $('#motivo').val('');        // Limpiar motivo
                $('#descripcion').val('');   // Limpiar textarea

                $('#InfoCliente').modal('hide');
            });
        });
    </script>

</body>
</html>
/*¿Qué hace este código?
Muestra/oculta opciones para incluir firma digital.

Carga y muestra firmas desde la base de datos vía fetch.

Permite agregar nuevas firmas y eliminarlas con confirmación.

Usa modales de Bootstrap para gestión visual.

Muestra mensajes de éxito/error con Toast.*/


// Espera a que el contenido del DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const incluirFirmaNoRadio = document.getElementById('firma_no');
    const incluirFirmaSiRadio = document.getElementById('firma_si');
    const firmaDigitalOpcionesDiv = document.getElementById('firma_digital_opciones');
    const modalEliminarEl = document.getElementById('modalEliminarFirma');
    const modalConfirmarEliminarFirmaEl = document.getElementById('EliminarFirma');
    const modalAgregarEl = document.getElementById('modalAgregarFirma');
    const guardarNuevaFirmaBtn = document.getElementById('guardarNuevaFirmaBtn');
    const confirmarEliminarFirmaBtn = document.getElementById('confirmarEliminarFirmaBtn');
    const confirmarEliminarFirmaRealBtn = document.getElementById('confirmarEliminarFirma');
    const firmaSeleccion = document.getElementById('firma_digital_seleccion');
    const firmaAEliminarSelect = document.getElementById('firma_a_eliminar');
    const eliminarIdFirmaInput = document.getElementById('eliminarIdFirma');

    // Inicialización de los modales con jQuery
    const $modalAgregar = $(modalAgregarEl);
    const $modalEliminar = $(modalEliminarEl);
    const $modalConfirmarEliminarFirma = $(modalConfirmarEliminarFirmaEl);

    // Mostrar u ocultar opciones de firma digital según el radio seleccionado
    incluirFirmaNoRadio.addEventListener('change', function() {
        firmaDigitalOpcionesDiv.style.display = 'none'; // Oculta opciones si elige "No"
    });

    incluirFirmaSiRadio.addEventListener('change', function() {
        firmaDigitalOpcionesDiv.style.display = 'flex'; // Muestra opciones si elige "Sí"
        cargarFirmasDigitales(); // Carga las firmas desde el servidor
    });

    // Función para obtener las firmas digitales disponibles desde el servidor
    function cargarFirmasDigitales() {
        fetch('../../controlador/firmas.php?accion=listar_firmas_digitales')
            .then(response => response.json())
            .then(data => {
                // Limpiar los select antes de añadir opciones
                firmaSeleccion.innerHTML = '<option value="">Seleccionar firma</option>';
                firmaAEliminarSelect.innerHTML = '<option value="">Seleccionar firma</option>';

                // Agregar cada firma a los selects de selección y eliminación
                data.forEach(firma => {
                    const optionSeleccion = document.createElement('option');
                    optionSeleccion.value = firma.id;
                    optionSeleccion.textContent = `${firma.nombre_firmante} (${firma.cargo_firmante})`;
                    firmaSeleccion.appendChild(optionSeleccion);

                    const optionEliminar = document.createElement('option');
                    optionEliminar.value = firma.id;
                    optionEliminar.textContent = `${firma.nombre_firmante} (${firma.cargo_firmante})`;
                    firmaAEliminarSelect.appendChild(optionEliminar);
                });
            })
            .catch(error => {
                console.error('Error al cargar las firmas digitales:', error);
            });
    }

    // Guardar una nueva firma digital al hacer clic en "Guardar"
    guardarNuevaFirmaBtn.addEventListener('click', function() {
        const form = document.getElementById('formAgregarFirma');
        const formData = new FormData(form); // Recolecta los datos del formulario

        fetch('../../controlador/firmas.php?accion=guardar_firma_digital', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $modalAgregar.modal('hide'); // Cierra el modal de agregar firma
                form.reset(); // Limpia el formulario
                cargarFirmasDigitales(); // Actualiza la lista de firmas
                mostrarMensaje(data.message); // Muestra mensaje de éxito
            } else {
                mostrarMensaje(data.message); // Muestra mensaje de error
            }
        })
        .catch(error => {
            console.error('Error al guardar la firma digital:', error);
            mostrarMensaje('Error al guardar la firma.');
        });
    });

    // Mostrar modal de confirmación para eliminar una firma
    confirmarEliminarFirmaBtn.addEventListener('click', function() {
        const idFirmaEliminar = firmaAEliminarSelect.value;

        if (idFirmaEliminar) {
            eliminarIdFirmaInput.value = idFirmaEliminar; // Guarda el ID en un input oculto
            $modalEliminar.modal('hide'); // Oculta el modal de selección
            $modalConfirmarEliminarFirma.modal('show'); // Muestra el de confirmación
        } else {
            alert('Por favor, seleccione una firma para eliminar.');
        }
    });

    // Si se cierra el modal de confirmación sin eliminar, vuelve al modal anterior
    $modalConfirmarEliminarFirma.on('hidden.bs.modal', function() {
        if ($modalEliminar.length) {
            $modalEliminar.modal('show');
        }
    });

    // Confirmar eliminación definitiva de la firma
    confirmarEliminarFirmaRealBtn.addEventListener('click', function() {
        const idFirmaEliminar = eliminarIdFirmaInput.value;

        if (idFirmaEliminar) {
            fetch('../../controlador/firmas.php?accion=eliminar_firma_digital', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id_firma=' + encodeURIComponent(idFirmaEliminar)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $modalConfirmarEliminarFirma.modal('hide');
                    $modalEliminar.modal('hide');
                    cargarFirmasDigitales(); // Actualiza la lista de firmas
                    mostrarMensaje(data.message); // Muestra mensaje de éxito
                } else {
                    $modalConfirmarEliminarFirma.modal('hide');
                    if ($modalEliminar.length) {
                        $modalEliminar.modal('show'); // Vuelve a mostrar el modal si falla
                    }
                    mostrarMensaje(data.message); // Muestra mensaje de error
                }
            })
            .catch(error => {
                console.error('Error al eliminar la firma digital:', error);
                $modalConfirmarEliminarFirma.modal('hide');
                if ($modalEliminar.length) {
                    $modalEliminar.modal('show');
                }
                mostrarMensaje('Error al eliminar la firma.');
            });
        }
    });

    // Al abrir el modal de eliminar firma, cargar la lista actualizada
    $modalEliminar.on('show.bs.modal', function() {
        cargarFirmasDigitales();
    });
});

// Función para mostrar un mensaje usando Toast de Bootstrap
function mostrarMensaje(mensaje) {
    const toastLiveExample = document.getElementById('liveToast');
    const toastBody = toastLiveExample.querySelector('.toast-body > div');

    if (toastBody) {
        toastBody.textContent = mensaje; // Inserta el mensaje en el toast
        const toast = new bootstrap.Toast(toastLiveExample); // Inicializa el toast
        toast.show(); // Muestra el mensaje
    } else {
        console.error('No se encontró el elemento .toast-body > div para mostrar el mensaje.');
    }
}


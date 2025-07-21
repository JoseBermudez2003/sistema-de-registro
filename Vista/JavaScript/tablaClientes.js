$(document).ready(function () {
    // Esta tabla se usa en la vista de registrar clientes, sin botones de exportación
    // Esta tabla se usa tambien en la vista de ver informes sin botones de exportación
    $('#myTable').DataTable({
        language: {
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "No hay datos disponibles en la tabla",
            info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            infoFiltered: "(filtrado de un total de _MAX_ registros)",
            search: "Buscar:",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            },
            processing: "Procesando..."
        },
        //visca al barsa y visca catalunya
        responsive: true,
        dom: "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +  // Layout de controles arriba
             "<'row'<'col-sm-12'tr>>" +                  // Tabla
             "<'row mt-3'<'col-sm-12 text-center'p>>",   // Paginación centrada
        columnDefs: [
            { targets: 'th.no-order', orderable: false }, //esto es para que no tenga el boton de ordenar 
                                                          // ciertas columnas
            { targets: '_all', className: 'text-center' } //Centra todo el contenido de las columnas
        ]
    });
});

$(document).ready(function () {
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
        responsive: true,
        dom: "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
             "<'row mb-3'<'col-sm-12 text-end'B>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3'<'col-sm-12 text-center'p>>",
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<img src="../Recursos/Iconos/excel.svg" width="20" alt="Excel">',
                titleAttr: 'Exportar a Excel',
                className: 'btn btn-success btn-xs me-1',
                exportOptions: {
                    columns: ':not(.no-export)'
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<img src="../Recursos/Iconos/pdf.svg" width="20" alt="PDF">',
                titleAttr: 'Exportar a PDF',
                className: 'btn btn-danger btn-xs me-1',
                title: function() {
                    return document.title;
                },
                customize: function (doc) {
                    // Configuración del encabezado del PDF
                    doc.content.splice(0, 0, {
                        margin: [0, 0, 0, 12],
                        alignment: 'center',
                        columns: [
                            {
                                image: 'data:image/png;base64,' + getBase64Image('../Recursos/Img/logo.png'),
                                width: 100,
                                alignment: 'left'
                            },
                            {
                                text: [
                                    'REPÚBLICA BOLIVARIANA DE VENEZUELA\n',
                                    'GOBIERNO BOLIVARIANO DEL ESTADO SUCRE\n',
                                    'CORPORACIÓN DE VIVIENDA DEL ESTADO SUCRE\n',
                                    'RIF G-20016449-2'
                                ],
                                alignment: 'center',
                                fontSize: 10,
                                bold: true
                            },
                            {
                                image: 'data:image/png;base64,' + getBase64Image('../Recursos/Img/gobierno.png'),
                                width: 80,
                                alignment: 'right'
                            }
                        ]
                    });
                    
                    // Fecha de generación
                    doc.content.splice(1, 0, {
                        text: 'Fecha: ' + new Date().toLocaleDateString(),
                        alignment: 'right',
                        margin: [0, 0, 0, 10],
                        fontSize: 9
                    });
                    
                    // Centrar la tabla
                    doc.content.forEach(function(item) {
                        if (item.table) {
                            item.alignment = 'center';
                            item.table.widths = Array(item.table.body[0].length + 1).join('*').split('');
                        }
                    });
                    
                    // Estilo para el contenido
                    doc.styles.tableHeader = {
                        fillColor: '#2c3e50',
                        color: '#ffffff',
                        bold: true,
                        fontSize: 10,
                        alignment: 'center'
                    };
                    
                    // Estilo para las celdas
                    doc.styles.tableBodyEven = {
                        alignment: 'center'
                    };
                    doc.styles.tableBodyOdd = {
                        alignment: 'center'
                    };
                },
                exportOptions: {
                    columns: ':not(.no-export)',
                    stripHtml: true
                }
            },
            {
        extend: 'print',
        text: '<img src="../Recursos/Iconos/print.svg" width="20" alt="Imprimir">',
        titleAttr: 'Imprimir',
        className: 'btn btn-info btn-xs',
        exportOptions: {
            columns: ':not(.no-export)'
        }
    }
        ],
        columnDefs: [
            { targets: 'th.no-order', orderable: false },
            { targets: '_all', className: 'text-center' }
        ]
    });
});

// Función auxiliar para convertir imágenes a base64
function getBase64Image(path) {
    var img = new Image();
    img.src = path;
    var canvas = document.createElement("canvas");
    canvas.width = img.width;
    canvas.height = img.height;
    var ctx = canvas.getContext("2d");
    ctx.drawImage(img, 0, 0);
    return canvas.toDataURL("image/png").split(',')[1];
}
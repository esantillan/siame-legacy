var init_datatable = function () {
    $('#' + TABLA_ID).dataTable(
            {
                "autoWidth": false,
                "bJQueryUI": true,
                "bPaginate": false,
                "bSort": true,
                "ajax": {
                    "method": "post",
                    "url": BASE_URL + 'usuario/listar_ajax'
                },
                "columns": [{"data": "apellido"},{"data": "nombre"},{"data": "documento"},{"data": "fecha_nacimiento"},{"data": "user"},{"data": "telefono_fijo"}, {"data": "telefono_movil"},{"data": "direccion_id"},{"data": "direccion"},{"data": "rol_id"},{"data": "rol"},{"data": "email"},{"data": "acciones"}],
                "columnDefs": [
                    {"targets": 7, "searcheable": false, "orderable": false, "visible": false},
                    {"targets": 9, "searcheable": false, "orderable": false, "visible": false},
                    {"targets": 12, "searcheable": false, "orderable": false},
                ],
                "scrollY": "400px",
                "scrollX": true,
                "scrollCollapse": false,
        /*
         l - length changing input control
         f - filtering input
         t - The table!
         i - Table information summary
         p - pagination control
         r - row
         */
                "dom": "<'ui-toolbar ui-widget-header top_round ui-helper-clearfix'f><'w3-border't><'bottom_round fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix'i<'w3-row'<'w3-left'B>>>",
                "buttons": [
                    {
                        "extend": "print",
                        "text": "Imprimir",
                        "autoPrint": true,
                        "className": "w3-margin-top",
                        "exportOptions": {
                            "columns": [0, 1, 2, 3, 4, 5, 6, 8, 10, 11]
                        }
                    },
                    {
                        "extend": "pdfHtml5",
                        "text": "Generar documento PDF",
                        "className": "w3-margin-top",
                        "exportOptions": {
                            "columns": [0, 1, 2, 3, 4, 5, 6, 8, 10, 11]
                        }
                    },
                    {
                        "extend": "excelHtml5",
                        "text": "Generar documento Excel",
                        "className": "w3-margin-top",
                        "exportOptions": {
                            "columns": [0, 1, 2, 3, 4, 5, 6, 8, 10, 11]
                        }
                    }
                ],
                "orderMulti": true,
                "fixedColumns": {"leftColumns": 1}
            }
    ).parent().scrollTop(9999);
}
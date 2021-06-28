var init_datatable = function () {
    $('#' + TABLA_ID).dataTable(
            {
                "autoWidth": false,
                "bJQueryUI": true,
                "bPaginate": false,
                "bSort": true,
                "ajax": {
                    "method": "post",
                    "url": BASE_URL + 'plan_estudio/listar_ajax'
                },
                "columns": [
                    {"data": "numero_resolucion"},
                    {"data": "nombre_carrera"},
                    {"data": "nombre_titulo"},
                    {"data": "modalidad"},
                    {"data": "duracion"},
                    {"data": "condiciones_ingreso"},
                    {"data": "articulaciones"},
                    {"data": "horas_catedra"},
                    {"data": "horas_reloj"},
                    {"data": "fecha_alta"},
                    {"data": "operador_alta"},
                    {"data": "fecha_modificacion"},
                    {"data": "operador_modificacion"},
                    {"data": "baja"},
                    {"data": "acciones"}],
//                "columnDefs": [
//                    {"targets": 7, "searcheable": false, "orderable": false, "visible": false},
//                    {"targets": 9, "searcheable": false, "orderable": false, "visible": false},
//                    {"targets": 16, "searcheable": false, "orderable": false},
//                    {"targets": 17, "searcheable": false, "orderable": false}
//                ],
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
                            "columns": [0, 1, 2, 3, 4, 5, 6, 8, 10, 11, 12, 13, 14, 15, 16]
                        }
                    },
                    {
                        "extend": "pdfHtml5",
                        "text": "Generar documento PDF",
                        "className": "w3-margin-top",
                        "exportOptions": {
                            "columns": [0, 1, 2, 3, 4, 5, 6, 8, 10, 11, 12, 13, 14, 15, 16]
                        }
                    },
                    {
                        "extend": "excelHtml5",
                        "text": "Generar documento Excel",
                        "className": "w3-margin-top",
                        "exportOptions": {
                            "columns": [0, 1, 2, 3, 4, 5, 6, 8, 10, 11, 12, 13, 14, 15, 16]
                        }
                    }
                ],
                "orderMulti": true,
                "fixedColumns": {"leftColumns": 1}
            }
    ).parent().scrollTop(9999);
}
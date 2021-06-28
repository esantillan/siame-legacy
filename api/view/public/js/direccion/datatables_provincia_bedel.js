var init_datatable = function () {
    $('#' + TABLA_ID).dataTable(
            {
                "autoWidth": false,
                "bJQueryUI": true,
                "bPaginate": false,
                "bSort": true,
                "ajax": {
                    "method": "post",
                    "url": BASE_URL + 'provincia/listar_ajax'
                },
                "columns": [{"data": "nombre"},{"data": "acciones"}],
                "columnDefs": [{"targets": 1, "searcheable": false, "orderable": false, "width": "5px"}],
                "scrollY": "400px",
                "scrollX": true,
                "scrollCollapse": false,
        /*
         l - length changing input control
         f - filtering input
         t - The table!
         i - Table information summary
         p - pagination control
         r
         */
                "dom": "<'ui-toolbar ui-widget-header top_round ui-helper-clearfix'f><'w3-border't><'bottom_round fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix'i<'w3-row'<'w3-left'B>>>",
                "buttons": [
                    {
                        "extend": "print",
                        "text": "Imprimir",
                        "autoPrint": true,
                        "className": "w3-margin-top",
                        "exportOptions": {
                            "columns": [0]
                        }
                    },
                    {
                        "extend": "pdfHtml5",
                        "text": "Generar documento PDF",
                        "className": "w3-margin-top",
                        "exportOptions": {
                            "columns": [0]
                        }
                    },
                    {
                        "extend": "excelHtml5",
                        "text": "Generar documento Excel",
                        "className": "w3-margin-top",
                        "exportOptions": {
                            "columns": [0]
                        }
                    }
                ],
                "orderMulti": true,
//                "fixedColumns": {"leftColumns": 1}
            }
    ).parent().scrollTop(9999);
}
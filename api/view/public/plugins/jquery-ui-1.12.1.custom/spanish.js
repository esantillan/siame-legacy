jQuery(function ($) {
    var cur_date = new Date();
    var min_date = cur_date.getFullYear() - 70;
    var max_date = cur_date.getFullYear() + 1;
    var yearRange = min_date + ':' + max_date;
    $.datepicker.regional['es'] = {
        showOn: "button",
        buttonText: "<i class='fa fa-calendar' aria-hidden='true'></i>",
        minDate: new Date(min_date, 1 - 1, 1 - 1),
        closeText: 'Cerrar',
        changeMonth: true,
        changeYear: true,
        prevText: ' < Mes Anterior',
        nextText: 'Mes Siguiente > ',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
            'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié;', 'Juv', 'Vie', 'Sáb'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: true,
        selectOtherMonths: true,
        yearRange: yearRange,
        yearSuffix: '',
        showButtonPanel: true
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
});/*showOn: "button",
 buttonText: "<i class='fa fa-calendar' aria-hidden='true'></i>",*/
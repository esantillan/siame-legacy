var table = '';
var TABLA_ID = '';
var CONTROLLER = '';
var ID_SELECTS2 = [];
var TITTLE_FORM_NEW = '';
var carga_continua = false;
var id = 0;//id del elemento actual (según sea el caso, ej, si estoy en departamento, el id del departamento XD)
var form_cosole = document.getElementById('form_console');
var close_form_console = document.getElementById('close_form_console');
var form_console_info = document.getElementById('form_console_info');

var show_form_error = function (msg) {
    form_console_info.innerHTML = msg;
    form_console.classList.remove('w3-green');
    form_console.classList.add('w3-red');
    close_form_console.classList.remove('w3-green');
    close_form_console.classList.add('w3-red');
    form_console.classList.remove('hide');
    form_console.classList.add('show');
}
var show_form_success = function (msg) {
    form_console_info.innerHTML = msg;
    form_console.classList.remove('w3-red');
    form_console.classList.add('w3-green');
    close_form_console.classList.remove('w3-red');
    close_form_console.classList.add('w3-green');
    form_console.classList.remove('hide');
    form_console.classList.add('show');
}
var eliminar_ajax = function () {
    var id = $(this).attr('id').split('_')[1];
    $.ajax({
        type: "POST",
        url: BASE_URL + CONTROLLER + '/delete/' + id,
        data: {"id": id},
        dataType: "json",
        beforeSend: function () {
            document.getElementById('modal').style.display = 'block';
        },
        success: function (respuesta) {
            if (respuesta.state) {
                reload_table(false);
                show_success(respuesta.msg);
            } else {
                show_error(respuesta.msg);
            }
        }
        , error: function (e) {
            console.log(e);
//            window.location = BASE_URL + 'error/error_500/Se_ha_producido_un_error_al_intentar_eliminar_el_registro';
        },
        complete: function () {
            document.getElementById('modal').style.display = 'none';
        }
    });
}
var form_create = function (c_continua) {
    carga_continua = c_continua;
    id = 0;
    var values = {};
    show_form_modal('modal_' + CONTROLLER, TITTLE_FORM_NEW + ' ' + (CONTROLLER[0].toUpperCase() + CONTROLLER.slice(1)), 'Enviar', values);
}
var send_form = function (e) {
    if (e)
        e.preventDefault();
    var btn_submit = document.getElementById('submit');
    btn_submit.disabled = true;
    var url = BASE_URL + CONTROLLER + '/' + (id ? 'edit' : 'create');
    var error = false;
    $.ajax({
        type: "POST",
        url: url,
        data: get_form_info(),
        dataType: "json",
        beforeSend: function () {
//            document.getElementById('modal').style.display = 'block';
        },
        success: function (respuesta) {
            if (respuesta.state) {
                reset_inputs();
                if (id) {
                    reload_table(false);
                } else if (!carga_continua) {
                    reload_table(true);
                }
                show_success(respuesta.msg);
                show_form_success(respuesta.msg);
            } else {
                error = true;
                show_error(respuesta.msg);
                show_form_error(respuesta.msg);
            }
        }
        , error: function (e) {
            console.log(e);
//            window.location = BASE_URL + 'error/error_500/Se_ha_producido_un_error_al_intentar_enviar_el_formulario';
        },
        complete: function () {
            if (carga_continua) {
                id = 0;
            } else {
                if (!error) {
                    hide_form_console();
                    close_form_modal('modal_' + CONTROLLER);
                    function_exists('reset_selects') ? reset_selects() : null;
                    id = 0;
                }
            }
            btn_submit.disabled = false;
        }
    });
}
/**
 * Recibe el título del formulario, el texto que será visible para el botón del 
 * formulario y un objeto, en el cual las claves son ID de los elementos del
 * formulario y, sus valores son el contenido de los elementos que tienen el id
 * proporcionado (esto es para el "editar").<br>Muestra el modal, con el priemer
 * campo con "focus"
 * 
 * @param {String} title título del formulario
 * @param {String} text_btn Texto que aparecerá visible para el botón del form
 * @param {Object} values  Objeto en el cual las claves son ID de los elementos del
 * formulario y, sus valores son el contenido de los elementos que tienen el id
 * proporcionado (esto es para el "editar")
 * @return {void}
 */
var show_form_modal = function (id_modal, title, text_btn, values) {
    document.getElementById('form_title').innerHTML = title.replace(/_/,' ').replace(/\b\w/g, function(l){ return l.toUpperCase() });;
    document.getElementById("submit").innerHTML = text_btn;

    for (indx in values) {
        console.log(indx);
        console.log(indx.indexOf('select'));
        if(indx.indexOf('select') !== -1){//si es un select
            document.getElementById(indx).selected = true;
        }else if(indx.indexOf('input') !== -1){//si es un input
            document.getElementById(indx).value = values[indx];
        }else if(indx.indexOf('textarea') !== -1){//si es un textarea
            document.getElementById(indx).value = values[indx];
        }
        
    }
    for (var i = 0; i < document.forms[0].elements.length; i++) {
        var campo = document.forms[0].elements[i];
        if (campo.type != "hidden") {
            campo.focus();
            break;
        }
    }
    document.getElementById(id_modal).style.display = 'block';
}
/**
 * Recibe el ID del modal activo y limpia el formulario que contiene, luego lo
 * oculta
 * @param {String} id_modal ID del modal a "cerrar" (ocultar)
 * @return {void}
 */
var close_form_modal = function (id_modal) {
    document.querySelector("form").reset();
    function_exists('reset_selects') ? reset_selects() : null;
    //oculto el formulario
    document.getElementById(id_modal).style.display = 'none';
    carga_continua = false;
    hide_form_console();
}
var reset_inputs = function () {
    var inputs = document.querySelectorAll("form input");
    var length = inputs.length;
    for (var i = 0; i < length; i++) {
        inputs[i].value = '';
    }

}
var reload_table = function (reset_filters) {
    if (reset_filters) {
        var filtros = document.getElementsByClassName('datatable_filter_input');
        var length = filtros.length;
        for (var i = 0; i < length; i++) {
            filtros[i].value = '';
        }
        table.search('').columns().search('').draw();
    }
    $('#' + TABLA_ID).DataTable().ajax.reload(null, true);
}
var hide_form_console = function () {
    form_console.classList.add('hide');
}
var init_abm = function () {
    document.forms[0].onsubmit = send_form;
    document.getElementById('submit').onclick = send_form;//IE no "reconoce" correctamente el elemento <button>

    function_exists('habilitar_ajax') ? $('table').on('click', '.habilitar', habilitar_ajax) : null;
    $('table').on('click', '.eliminar', eliminar_ajax);
    $('table').on('click', '.editar', form_edit);
    document.getElementById('recargar_tabla').onclick = function () {
        reload_table(true);
    };
    document.getElementById('alta').onclick = function () {
        form_create(false);
    };
    document.getElementById('carga_continua').onclick = function () {
        form_create(true);
    };
    close_form_console.onclick = hide_form_console;
    init_datatable();
    table = $('#' + TABLA_ID).DataTable();
    $(table.table().container()).on('keyup', 'thead input', function () {
        table
                .column($(this).data('index'))
                .search(this.value)
                .draw();
        $('input', table.column().header()).on('click', function (e) {
            e.stopPropagation();
        });
    });
}
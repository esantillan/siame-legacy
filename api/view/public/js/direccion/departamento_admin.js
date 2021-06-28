var provincia_select2 = '';

var habilitar_ajax = function () {
    var id = $(this).attr('id').split('_')[1];
    $.ajax({
        type: "POST",
        url: BASE_URL + "departamento/enable/",
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
            window.location = BASE_URL + 'error/error_500/Se_ha_producido_un_error_al_intentar_habilitar_el_registro';
        },
        complete: function(){
            document.getElementById('modal').style.display = 'none';
        }
    });
}
var load_provincias = function () {
    $.ajax({
        type: "POST",
        url: BASE_URL + "departamento/listar_provincias",
        dataType: "json",
        success: function (respuesta) {
            provincia_select2 = $("#provincia_select").select2({
                placeholder: "Seleccione una Provincia",
                data: respuesta,
                escapeMarkup: function (text) {
                    return text;
                }//los strings ya se encuentran almacenados en la bd correctamente escapados
            });
            reset_selects();
        }
        , error: function (e) {
            console.log(e);
//            window.location = BASE_URL + 'error/error_500/Se_ha_producido_un_error_al_intentar_obtener_el_listado_de_provincias';
        }
    });
}
var form_edit = function () {
    id = this.id.split('_')[1];
    var row = this.parentNode.parentNode;
    var nombre_actual = row.childNodes[0].innerHTML;
    var provincia_id = row.childNodes[1].innerHTML.replace(/ /g, '-') + '_' + table.cell(row, 2).data();
    provincia_select2.val(provincia_id).trigger("change");
    var values = {"input_nombre": nombre_actual};
    show_form_modal('modal_departamento', 'Editar Departamento', 'Editar', values);
}
var get_form_info = function (e) {
    var nuevo_nombre = document.getElementById("input_nombre").value;
    var provincia_id = document.getElementById("provincia_select").value.split('_')[1];
    var data = '';
    if (id == 0) {//alta
        data = {"nombre": nuevo_nombre, "provincia_id": provincia_id};
    } else {
        data = {"id": id, "nombre": nuevo_nombre, "provincia_id": provincia_id};
    }
    return data;
}
var reset_selects = function () {
    document.getElementById('provincia_select').value = '';
    provincia_select2.val(null).trigger("change");
}
window.onload = function () {
    TABLA_ID = 'tabla_departamento';
    CONTROLLER = 'departamento';
    ID_SELECTS2 = ['provincia_select'];
    TITTLE_FORM_NEW = 'Nuevo';
    
    init_general();
    init_abm();
    
    load_provincias();
};
var provincia_select2 = '';
var departamento_select2 = '';

var load_provincias = function () {
    $.ajax({
        type: "POST",
        url: BASE_URL + "localidad/listar_provincias",
        dataType: "json",
        success: function (respuesta) {
            provincia_select2 = $("#provincia_select").select2({
                placeholder: "Seleccione una Provincia",
                data: respuesta,
                escapeMarkup: function (text) {
                    return text;
                }//los strings ya se encuentran almacenados en la bd correctamente escapados
            });
            provincia_select2.on('select2:select', function (e) {
                var prov_id = document.getElementById("provincia_select").value.split('_')[1];
                load_departamentos(prov_id);
            });
            reset_selects();
        }
        , error: function (e) {
            window.location = BASE_URL + 'error/error_500/Se_ha_producido_un_error_al_intentar_obtener_el_listado_de_provincias';
        }
    });
}
var load_departamentos = function (provincia_id) {
    $.ajax({
        type: "POST",
        url: BASE_URL + "localidad/listar_departamentos/provincia/" + provincia_id,
        dataType: "json",
        success: function (respuesta) {
            $("#departamento_select option").remove();
            departamento_select2.select2({
                placeholder: "Seleccione un Departamento",
                data: respuesta.departamentos,
                escapeMarkup: function (text) {
                    return text;
                }//los strings ya se encuentran almacenados en la bd correctamente escapados
            });
            document.getElementById('departamento_select').value = '';
            departamento_select2.val(null).trigger("change");
        }
        , error: function (e) {
            window.location = BASE_URL + 'error/error_500/Se_ha_producido_un_error_al_intentar_obtener_el_listado_de_departamentos';
        }
    });
}
var form_edit = function () {
    var row = this.parentNode.parentNode;
    $("#departamento_select option").remove();
    id = this.id.split('_')[1];
    var nombre_actual = row.childNodes[0].innerHTML;;
    var departamento_id = table.cell(row, 2).data();
    var departamento_nombre = table.cell(row, 1).data().split(' - ')[0].replace(/ /g,'-');
    var departamento_id_html = departamento_nombre + '_' + departamento_id;//obtengo para seleccionar el departamento en el select2
    $.ajax({
        type: "POST",
        url: BASE_URL + "localidad/listar_departamentos/departamento/" + departamento_id,
        dataType: "json",
        success: function (respuesta) {
            provincia_select2.val(respuesta.provincia_id).trigger("change");
            departamento_select2.select2({
                placeholder: "Seleccione un Departamento",
                data: respuesta.departamentos,
                escapeMarkup: function (text) {
                    return text;
                }//los strings ya se encuentran almacenados en la bd correctamente escapados
            });
            departamento_select2.val(departamento_id_html).trigger("change");
        }
        , error: function (e) {
            window.location = BASE_URL + 'error/error_500/Se_ha_producido_un_error_al_intentar_obtener_el_listado_de_departamentos';
        }
    });
    var values = {"input_nombre": nombre_actual};
    show_form_modal('modal_localidad', 'Editar Localidad', 'Editar', values);
}
var get_form_info = function () {
    var nuevo_nombre = document.getElementById("input_nombre").value;
    var departamento_id = document.getElementById("departamento_select").value.split('_')[1];
    var data = '';
    if (id == 0) {//alta
        data = {"nombre": nuevo_nombre, "departamento_id": departamento_id};
    } else {
        data = {"id": id, "nombre": nuevo_nombre, "departamento_id": departamento_id};
    }
    return data;
}
var reset_selects = function () {
    document.getElementById('provincia_select').value = '';
    document.getElementById('departamento_select').value = '';
    provincia_select2.val(null).trigger("change");
    departamento_select2.val(null).trigger("change");
}
window.onload = function () {
    TABLA_ID = 'tabla_localidad';
    CONTROLLER = 'localidad';
    ID_SELECTS2 = ['provincia_select', 'departamento_select'];
    TITTLE_FORM_NEW = 'Nueva';
    
    init_general();
    init_abm();
    
    load_provincias();
    departamento_select2 = $("#departamento_select").select2({
        placeholder: "Seleccione un Departamento",
        escapeMarkup: function (text) {
            return text;
        }//los strings ya se encuentran almacenados en la bd correctamente escapados
    });
};
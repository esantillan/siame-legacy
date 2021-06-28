var provincia_select2 = '';
var departamento_select2 = '';
var localidad_select2 = '';
var rol_select2 = '';
var direccion_id = 0;
var fecha_nacimiento_datepicker = '';
var load_provincias = function () {
    $.ajax({
        type: "POST",
        url: BASE_URL + "usuario/listar_provincias",
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
            console.log(e);
//            window.location = BASE_URL + 'error/error_500/Se_ha_producido_un_error_al_intentar_obtener_el_listado_de_provincias';
        }
    });
}
var load_departamentos = function (provincia_id) {
    $.ajax({
        type: "POST",
        url: BASE_URL + "usuario/listar_departamentos/provincia/prov_" + provincia_id,
        dataType: "json",
        success: function (respuesta) {
            $("#departamento_select option").remove();
            $("#localidad_select option").remove();
            departamento_select2.select2({
                placeholder: "Seleccione un Departamento",
                data: respuesta.departamentos,
                escapeMarkup: function (text) {
                    return text;
                }//los strings ya se encuentran almacenados en la bd correctamente escapados
            });
            document.getElementById('departamento_select').value = '';
            departamento_select2.val(null).trigger("change");
            document.getElementById('localidad_select').value = '';
            localidad_select2.val(null).trigger("change");
            departamento_select2.on('select2:select', function (e) {
                var dpto_id = document.getElementById("departamento_select").value.split('_')[1];
                load_localidades(dpto_id);
            });
        }
        , error: function (e) {
            console.log(e);
//            window.location = BASE_URL + 'error/error_500/Se_ha_producido_un_error_al_intentar_obtener_el_listado_de_departamentos';
        }
    });
}
var load_localidades = function (departamento_id) {
    $.ajax({
        type: "POST",
        url: BASE_URL + "usuario/listar_localidades/departamento/dpto_" + departamento_id,
        dataType: "json",
        success: function (respuesta) {
            $("#localidad_select option").remove();
            localidad_select2.select2({
                placeholder: "Seleccione una Localidad",
                data: respuesta.departamentos,
                escapeMarkup: function (text) {
                    return text;
                }//los strings ya se encuentran almacenados en la bd correctamente escapados
            });
            document.getElementById('localidad_select').value = '';
            localidad_select2.val(null).trigger("change");
        }
        , error: function (e) {
            console.log(e);
//            window.location = BASE_URL + 'error/error_500/Se_ha_producido_un_error_al_intentar_obtener_el_listado_de_departamentos';
        }
    });
}
var load_roles = function () {
    $.ajax({
        type: "POST",
        url: BASE_URL + "usuario/listar_roles",
        dataType: "json",
        success: function (respuesta) {
            rol_select2 = $("#rol_select").select2({
                placeholder: "Seleccione un Rol",
                data: respuesta,
                escapeMarkup: function (text) {
                    return text;
                }//los strings ya se encuentran almacenados en la bd correctamente escapados
            });
            document.getElementById('localidad_select').value = '';
            rol_select2.val(null).trigger("change");
        }
        , error: function (e) {
            console.log(e);
//            window.location = BASE_URL + 'error/error_500/Se_ha_producido_un_error_al_intentar_obtener_el_listado_de_provincias';
        }
    });
}
var reset_selects = function () {
    document.getElementById('provincia_select').value = '';
    document.getElementById('departamento_select').value = '';
    document.getElementById('localidad_select').value = '';
    document.getElementById('rol_select').value = '';
    provincia_select2.val(null).trigger("change");
    departamento_select2.val(null).trigger("change");
    localidad_select2.val(null).trigger("change");
    rol_select2.val(null).trigger("change");
}
var form_edit = function () {
    var row = this.parentNode.parentNode;
    $("#departamento_select option").remove();
    $("#localidad_select option").remove();
    id = this.id.split('_')[1];
    var apellido = row.childNodes[0].innerHTML;
    var nombre = row.childNodes[1].innerHTML;
    var documento = row.childNodes[2].innerHTML;
    var f_nac = row.childNodes[3].innerHTML.replace(/-/g,'/');
    var username = row.childNodes[4].innerHTML;
    var telefono_fijo = row.childNodes[5].innerHTML;
    var telefono_movil = row.childNodes[6].innerHTML;
    var direccion = row.childNodes[7].innerHTML;
    var rol = row.childNodes[8].innerHTML;
    var email = row.childNodes[9].innerHTML;
    direccion_id = table.cell(row, 7).data();
    var rol_id = table.cell(row, 9).data();
    $.ajax({
        type: "POST",
        url: BASE_URL + "usuario/get_info_selects/" + direccion_id,
        dataType: "json",
        success: function (respuesta) {
            provincia_select2.val(respuesta.provincia_id).trigger("change");
            departamento_select2.select2({
                placeholder: "Seleccione un Departamento",
                data: respuesta.departamentos,
                escapeMarkup: function (text) {
                    return text;
                }
            });
            departamento_select2.val(respuesta.departamento_id).trigger("change");
            departamento_select2.on('select2:select', function (e) {
                var dpto_id = document.getElementById("departamento_select").value.split('_')[1];
                load_localidades(dpto_id);
            });
            localidad_select2.select2({
                placeholder: "Seleccione una Localidad",
                data: respuesta.localidades,
                escapeMarkup: function (text) {
                    return text;
                }
            });
            localidad_select2.val(respuesta.localidad_id).trigger("change");
            document.getElementById('input_numero').value = respuesta.numero;
            document.getElementById('input_calle').value = respuesta.calle;
        }
        , error: function (e) {
            console.log(e);
//            window.location = BASE_URL + 'error/error_500/Se_ha_producido_un_error_al_intentar_obtener_el_listado_de_departamentos';
        }
    });
    rol_select2.val('rol_' + rol_id).trigger("change");
    var values = {"input_apellido": apellido, "input_nombre": nombre, "input_documento": documento, "input_fecha_nacimiento": f_nac, "input_username": username, "input_telefono_fijo": telefono_fijo, "input_telefono_movil": telefono_movil, "input_email": email};
    show_form_modal('modal_usuario', 'Editar Usuario', 'Editar', values);
}
var get_form_info = function () {
    var apellido = document.getElementById("input_apellido").value;
    var nombre = document.getElementById("input_nombre").value;
    var documento = document.getElementById("input_documento").value;
    var username = document.getElementById("input_username").value;
    var fecha_nacimiento = document.getElementById("input_fecha_nacimiento").value;
    var telefono_fijo = document.getElementById("input_telefono_fijo").value;
    var telefono_movil = document.getElementById("input_telefono_movil").value;
    var calle = document.getElementById("input_calle").value;
    var numero = document.getElementById("input_numero").value;
    var email = document.getElementById("input_email").value;
    var localidad_id = document.getElementById("localidad_select").value.split('_')[1];
    var rol_id = document.getElementById("rol_select").value.split('_')[1];
    var data = '';
    if (id == 0) {//alta
        data = {"apellido": apellido, "nombre": nombre, "documento": documento, "user": username, "fecha_nacimiento": fecha_nacimiento, "telefono_fijo": telefono_fijo, "telefono_movil": telefono_movil, "calle": calle, "numero": numero, "localidad_id": localidad_id, "rol_id": rol_id, 'email': email};
    } else {
        data = {"id": id, "direccion_id": direccion_id, "apellido": apellido, "nombre": nombre, "documento": documento, "user": username, "fecha_nacimiento": fecha_nacimiento, "telefono_fijo": telefono_fijo, "telefono_movil": telefono_movil, "calle": calle, "numero": numero, "localidad_id": localidad_id, "rol_id": rol_id, 'email': email};
    }
    return data;
}
var reset_pass = function () {
    var id = $(this).attr('id').split('_')[1];
    $.ajax({
        type: "POST",
        url: BASE_URL + CONTROLLER + '/reset_pass/' + id,
        data: {"id": id},
        dataType: "json",
        beforeSend: function () {
            document.getElementById('modal').style.display = 'block';
        },
        success: function (respuesta) {
            if (respuesta.state) {
                reload_table(true);
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
window.onload = function () {
    TABLA_ID = 'tabla_usuario';
    CONTROLLER = 'usuario';
    ID_SELECTS2 = ['provincia_select', 'departamento_select', 'localidad_select', 'rol_select'];
    TITTLE_FORM_NEW = 'Nuevo';

    init_general();
    init_abm();
    
    $('table').on('click', '.reset-pass', reset_pass);
    departamento_select2 = $("#departamento_select").select2({
        placeholder: "Seleccione un Departamento",
        escapeMarkup: function (text) {
            return text;
        }//los strings ya se encuentran almacenados en la bd correctamente escapados
    });
    localidad_select2 = $("#localidad_select").select2({
        placeholder: "Seleccione una Localidad",
        escapeMarkup: function (text) {
            return text;
        }//los strings ya se encuentran almacenados en la bd correctamente escapados
    });
    rol_select2 = $("#rol_select").select2({
        placeholder: "Seleccione un Rol",
        escapeMarkup: function (text) {
            return text;
        }//los strings ya se encuentran almacenados en la bd correctamente escapados
    });

    fecha_nacimiento_datepicker = $("#input_fecha_nacimiento").datepicker({showOn: "button",buttonText: "<i class='fa fa-calendar' aria-hidden='true'></i>"});
    $(".ui-datepicker-trigger").addClass('w3-button');
    load_provincias();
    load_roles();
};
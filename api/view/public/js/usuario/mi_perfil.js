var provincia_select2 = '';
var departamento_select2 = '';
var localidad_select2 = '';
var direccion_id = 0;
var user_id = 0;
var fecha_nacimiento_datepicker = '';
var load_datos_perfil = function () {
    $.ajax({
        type: "POST",
        url: BASE_URL + "usuario/get_datos_perfil",
        dataType: "json",
        success: function (respuesta) {
            direccion_id = respuesta.direccion_id;
            user_id = respuesta.id;
            document.getElementById('input_apellido').value = respuesta.apellido;
            document.getElementById('input_nombre').value = respuesta.nombre;
            document.getElementById('input_documento').value = respuesta.documento;
            document.getElementById('input_fecha_nacimiento').value = respuesta.input_fecha_nacimiento;
            fecha_nacimiento_datepicker.datepicker("setDate", respuesta.fecha_nacimiento);
            document.getElementById('input_username').value = respuesta.user;
            document.getElementById('input_pass').value = respuesta.pass;
            document.getElementById('input_pass_repeat').value = respuesta.pass;
            document.getElementById('input_telefono_fijo').value = respuesta.telefono_fijo == 0 ? '' : respuesta.telefono_fijo;
            document.getElementById('input_telefono_movil').value = respuesta.telefono_movil == 0 ? '' : respuesta.telefono_movil;
            document.getElementById('input_calle').value = respuesta.calle;
            document.getElementById('input_numero').value = respuesta.numero;
            document.getElementById('input_email').value = respuesta.email;
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
                    localidad_select2.select2({
                        placeholder: "Seleccione una Localidad",
                        data: respuesta.localidades,
                        escapeMarkup: function (text) {
                            return text;
                        }
                    });
                    localidad_select2.val(respuesta.localidad_id).trigger("change");
                }
            });
        }
        , error: function (e) {
            console.log(e);
//            window.location = BASE_URL + 'error/error_500/Se_ha_producido_un_error_al_intentar_obtener_el_listado_de_provincias';
        }
    });
}
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
var reset_selects = function () {
    document.getElementById('provincia_select').value = '';
    document.getElementById('departamento_select').value = '';
    document.getElementById('localidad_select').value = '';
    provincia_select2.val(null).trigger("change");
    departamento_select2.val(null).trigger("change");
    localidad_select2.val(null).trigger("change");
}
var get_form_info = function () {
}
var send_form = function (e) {
    if (e)
        e.preventDefault();
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
    
    var btn_submit = document.getElementById('submit');
    btn_submit.disabled = true;
    $.ajax({
        type: "POST",
        url: BASE_URL + 'usuario/edit',
        data: data = {"id": user_id, "direccion_id": direccion_id, "apellido": apellido, "nombre": nombre, "documento": documento, "user": username, "fecha_nacimiento": fecha_nacimiento, "telefono_fijo": telefono_fijo, "telefono_movil": telefono_movil, "calle": calle, "numero": numero, "localidad_id": localidad_id, 'email': email},
        dataType: "json",
        beforeSend: function () {
//            document.getElementById('modal').style.display = 'block';
        },
        success: function (respuesta) {
            if (respuesta.state) {
                show_success(respuesta.msg);
            } else {
                show_error(respuesta.msg);
            }
        }
        , error: function (e) {
            console.log(e);
//            window.location = BASE_URL + 'error/error_500/Se_ha_producido_un_error_al_intentar_enviar_el_formulario';
        },
        complete: function () {
            btn_submit.disabled = false;
        }
    });
}
window.onload = function () {
    init_general();

    document.getElementById('submit').onclick = send_form;
    document.getElementById('volver').onclick = function () {
        window.location = BASE_URL
    }
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
//
    fecha_nacimiento_datepicker = $("#input_fecha_nacimiento").datepicker({showOn: "button", buttonText: "<i class='fa fa-calendar' aria-hidden='true'></i>"});
    $(".ui-datepicker-trigger").addClass('w3-button');
    load_provincias();
    load_datos_perfil();
};
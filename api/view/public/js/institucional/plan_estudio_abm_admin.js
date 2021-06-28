var habilitar_ajax = function () {
    var id = $(this).attr('id').split('_')[1];
    $.ajax({
        type: "POST",
        url: BASE_URL + "plan_estudio/enable/",
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
//            window.location = BASE_URL + 'error/error_500/Se_ha_producido_un_error_al_intentar_habilitar_el_registro';
        },
        complete: function () {
            document.getElementById('modal').style.display = 'none';
        }
    });
}
var reset_selects = function () {
}
var form_edit = function () {
    var row = this.parentNode.parentNode;
    id = this.id.split('_')[1];
    var numero_resolucion = row.childNodes[0].innerHTML;
    var nombre_carrera = row.childNodes[1].innerHTML;
    var nombre_titulo = row.childNodes[2].innerHTML;
    var modalidad = row.childNodes[3].innerHTML;
    var duracion = row.childNodes[4].innerHTML;
    var condiciones_ingreso = row.childNodes[5].innerHTML;
    var articulaciones = row.childNodes[6].innerHTML;
    var horas_catedra = row.childNodes[7].innerHTML;
    var horas_reloj = row.childNodes[8].innerHTML;
    
    var values = {"input_numero_resolucion": numero_resolucion, "input_nombre_carrera": nombre_carrera, "input_nombre_titulo": nombre_titulo, "select_modalidad": modalidad, "input_duracion": duracion, "textarea_condiciones_ingreso": condiciones_ingreso, "textarea_articulaciones": articulaciones, "input_horas_catedra": horas_catedra, "input_horas_reloj": horas_reloj};
    show_form_modal('modal_plan_estudio', 'Editar Plan Estudio', 'Editar', values);
}
var get_form_info = function () {
    var numero_resolucion = document.getElementById("input_numero_resolucion").value;
    var nombre_carrera = document.getElementById("input_nombre_carrera").value;
    var nombre_titulo = document.getElementById("input_nombre_titulo").value;
    var modalidad = document.getElementById("select_modalidad").value;
    var duracion = document.getElementById("input_duracion").value;
    var condiciones_ingreso = document.getElementById("textarea_condiciones_ingreso").value;
    var articulaciones = document.getElementById("textarea_articulaciones").value;
    var horas_catedra = document.getElementById("input_horas_catedra").value;
    var horas_reloj = document.getElementById("input_horas_reloj").value;
    var data = '';
    if (id == 0) {//alta
        data = {"numero_resolucion": numero_resolucion, "nombre_carrera": nombre_carrera, "nombre_titulo": nombre_titulo, "modalidad": modalidad, "duracion": duracion, "condiciones_ingreso": condiciones_ingreso, "articulaciones": articulaciones, "horas_catedra": horas_catedra, "horas_reloj": horas_reloj};
    } else {
        data = {"id": id, "numero_resolucion": numero_resolucion, "nombre_carrera": nombre_carrera, "nombre_titulo": nombre_titulo, "modalidad": modalidad, "duracion": duracion, "condiciones_ingreso": condiciones_ingreso, "articulaciones": articulaciones, "horas_catedra": horas_catedra, "horas_reloj": horas_reloj};
    }
    return data;
}
window.onload = function () {
    TABLA_ID = 'tabla_plan_estudio';
    CONTROLLER = 'plan_estudio';
    TITTLE_FORM_NEW = 'Nuevo';

    init_general();
    init_abm();
};
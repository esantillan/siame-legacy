var habilitar_ajax = function () {
    var id = $(this).attr('id').split('_')[1];
    $.ajax({
        type: "POST",
        url: BASE_URL + "provincia/enable/",
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
        },
        error: function (e) {
            window.location = BASE_URL + 'error/error_500/Se_ha_producido_un_error_al_intentar_habilitar_el_registro';
        },
        complete: function(){
            document.getElementById('modal').style.display = 'none';
        }
    });
}
var form_edit = function () {
    id = this.id.split('_')[1];
    var nombre_actual = this.parentNode.parentNode.childNodes[0].innerHTML;
    var values = {"input_nombre": nombre_actual};
    show_form_modal('modal_provincia', 'Editar Provincia', 'Editar', values);
}
var get_form_info = function () {
    var nuevo_nombre = document.getElementById("input_nombre").value;
    var data = '';
    if (id == 0) {//alta
        data = {"nombre": nuevo_nombre};
    } else {
        data = {"id": id, "nombre": nuevo_nombre};
    }
    return data;
}
window.onload = function () {
    TABLA_ID = 'tabla_provincia';
    CONTROLLER = 'provincia';
    ID_SELECTS2 = [];
    TITTLE_FORM_NEW = 'Nueva';
    
    init_general();
    init_abm();
    
};
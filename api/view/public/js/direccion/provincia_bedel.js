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
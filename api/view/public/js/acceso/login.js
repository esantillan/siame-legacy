var send_form = function (e) {
    if (e)
        e.preventDefault();
    document.getElementById("user_menu").classList.remove('w3-show');
    $.ajax({
        type: "POST",
        url: BASE_URL + "login/log_in",
        data: {"user": document.getElementById('user').value, "pass": hex_sha512(document.getElementById('pass').value)},
        dataType: "json",
        beforeSend: function () {
            document.getElementById('modal').style.display = 'block';
        },
        success: function (respuesta) {
            document.getElementById('modal').style.display = 'none';
            if (respuesta.msg) {
                window.location = respuesta.url;
            } else {
                show_error('<u>ERROR</u>:<br> Usuario o Clave incorrectos.');
            }
        }
        , error: function (e) {
            window.location = BASE_URL + 'error/error_500/Se_ha_producido_un_error_al_intentar_inciar_sesión';
        }
    });
}
window.onload = function () {
    init_general();
    document.getElementById('user').focus();
    document.getElementById('submit').onclick = send_form;
    document.form_login.onsubmit = send_form;
};
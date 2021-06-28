var main_menu = document.getElementById("sidebar");
var open_nav = document.getElementById('open_nav');
var sidebar_open = false;
var mod = null;
var crud = null;
var dropdown_open = false;
var console_info = document.getElementById('console_info');
var console_container = document.getElementById('console');
var close_console = document.getElementById('close_console');

var sidebar = function () {
    if (dropdown_open) {
        dropdown_open.classList.remove('w3-show');
        dropdown_open = '';
    }
    if (sidebar_open) {
        main_menu.style.display = "none";
        open_nav.style.display = "inline-block";
        sidebar_open = false;
    } else {
        main_menu.style.display = "block";
        main_menu.style.width = "25%";
        sidebar_open = true;
    }
}
var accordion = function (id) {
    var x = document.getElementById(id);
    if (id.search('mod') != -1) {
        if (mod != null && mod != x) {
            mod.classList.remove('w3-show');
            mod.previousElementSibling.classList.remove('w3-black');
        }
        mod = x;
        x.previousElementSibling.classList.toggle('w3-black');
        x.classList.toggle('w3-show');
    } else {
        if (crud != null && crud != x) {
            crud.classList.remove('w3-show');
            crud.previousElementSibling.classList.remove('w3-gray');
        }
        crud = x;
        x.previousElementSibling.classList.toggle('w3-gray');
        x.previousElementSibling.classList.toggle('w3-text-white');
        x.classList.toggle('w3-show');
    }
}
var dropdown = function (id) {
    if (sidebar_open) {
        main_menu.style.display = "none";
        sidebar_open = false;
    }
    if (dropdown_open.id == id) {
        dropdown_open.classList.remove('w3-show');
        dropdown_open = '';
    } else {
        if (dropdown_open) {
            dropdown_open.classList.remove('w3-show');
            dropdown_open = '';
        }
        dropdown_open = document.getElementById(id);
        var x = document.getElementById(id);
        x.classList.add('w3-show');
    }
}
var show_error = function (msg) {
    console_info.innerHTML = msg;
    console_container.classList.remove('w3-green');
    console_container.classList.add('w3-red');
    close_console.classList.remove('w3-green');
    close_console.classList.add('w3-red');
    console_container.classList.remove('hide');
    console_container.classList.add('show');
}
var show_success = function (msg) {
    console_info.innerHTML = msg;
    console_container.classList.remove('w3-red');
    console_container.classList.add('w3-green');
    close_console.classList.remove('w3-red');
    close_console.classList.add('w3-green');
    console_container.classList.remove('hide');
    console_container.classList.add('show');
}
var function_exists = function (funcName) { // eslint-disable-line camelcase
    //  discuss at: http://locutus.io/php/function_exists/
    // original by: Kevin van Zonneveld (http://kvz.io)
    // improved by: Steve Clay
    // improved by: Legaev Andrey
    // improved by: Brett Zamir (http://brett-zamir.me)
    //   example 1: function_exists('isFinite')
    //   returns 1: true
    //        test: skip-1
    var $global = (typeof window !== 'undefined' ? window : global)
    if (typeof funcName === 'string') {
        funcName = $global[funcName];
    }
    return typeof funcName === 'function';
}
var init_general = function () {
    open_nav.onclick = sidebar;
    close_console.onclick = function () {
        console_container.classList.add('hide');
    }
    var menu_items = document.getElementsByClassName('user_menu_item');
    for (var i = 0; i < menu_items.length; i++) {
        menu_items[i].onclick = user_menu;
    }
    var iniciar_sesion = document.getElementById('iniciar_sesion');
    if (iniciar_sesion) {
        iniciar_sesion.onclick = function () {
            window.location = BASE_URL + 'login'
        };
    }
    var cerrar_sesion = document.getElementById('cerrar_sesion');
    if (cerrar_sesion) {
        cerrar_sesion.onclick = function () {
            window.location = BASE_URL + 'login/log_out'
        };
    }
}
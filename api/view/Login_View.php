<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace view;
use view\General_View as General_View;
use config\Constants as Constants;
/**
 * Description of Login_View
 *
 * @author Esteban
 */
class Login_View extends General_View{    
    public function listar(): void {
        header("Location: /siame/error/error_403/No_posee_permisos_para_acceder_a_esta_sección");
    }

    public function listar_ajax(array $result): void {
        header("Location: /siame/error/error_403/No_posee_permisos_para_acceder_a_esta_sección");
    }

    
    public function __construct() {
        parent::__construct();
        $this->load_css(array('acceso/login'));
        $this->load_script(array('acceso/login'));
        $this->load_plugin_js(array('jshash-2.2/sha512-min'));
        $template = 'acceso' . Constants::$DS . 'login';
        $this->load_template($template);
        $this->load_navbar(array());
        $this->load_sidebar(array());
        $this->add_links(array(
            '__BANNER__' => Constants::$URL . 'api/view/public/img/cabezal_blogMB1.png',
            '__LOGIN_CONTROL__' => Constants::$URL . 'login/log_in',
            '__FORGOT_PASSWORD__' => Constants::$URL . 'forgot_password'
        ));
        $this->generate_view();
    }
}

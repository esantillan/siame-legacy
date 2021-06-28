<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace view;

use view\General_View as General_View;
use config\Constants as Constants;
use helper\Session_Manager as Session_Manager;

/**
 * Description of Main_View
 *
 * @author Esteban
 */
class Inicio_View extends General_View {

    public function __construct() {
        parent::__construct();
        $template = '';
        switch (Session_Manager::get_instance()->get_rol_id()) {
            case Session_Manager::ADMINISTRADOR:
                $template = 'inicio' . Constants::$DS . 'inicio_admin';
                break;
            case Session_Manager::BEDEL:
                $template = 'inicio' . Constants::$DS . 'inicio_bedel';
                break;
        }
        $this->load_css(array('inicio/inicio'));
        $this->load_script(array('inicio/inicio'));
        $this->load_plugin_css(array('intro.js-2.5.0/introjs'));
        $this->load_plugin_js(array('intro.js-2.5.0/intro'));
        $this->load_template($template);
        $this->load_navbar(array());
        $this->load_sidebar(array(array('fa-globe', 'Provincia', 'provincia')));
        $this->load_navbar([['fa-sign-out', 'Cerrar Sesión', 'login/log_out', 'cerrar_sesion'],['fa-id-card', 'Mi Pefil','usuario/mi_perfil','mi_perfil']]);
        $this->set_title('Inicio');
        $this->add_links(array(
            '__BANNER__' => Constants::$URL . 'api/view/public/img/cabezal_blogMB1.png',
            '__LOGIN_CONTROL__' => Constants::$URL . 'login/log_in',
            '__FORGOT_PASSWORD__' => Constants::$URL . 'forgot_password'
        ));
        $this->generate_breadcrumb('Inicio');
        $this->generate_view();
    }

    public function listar(): void {
        
    }

    public function listar_ajax(array $result): void {
        
    }

}

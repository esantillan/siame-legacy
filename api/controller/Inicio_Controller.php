<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace controller;

use controller\General_Controller as General_Controller;
use view\Inicio_View as Inicio_View;
use helper\Session_Manager as Session_Manager;
use config\{
    Router as Router,
    Request as Request
};

/**
 * Description of Inicio_Controller
 *
 * @author Esteban
 */
class Inicio_Controller extends General_Controller {

    public function __construct() {
        parent::__construct();
        $this->check_session();
    }

    public function index() {
        new Inicio_View();
    }

    public function listar_ajax() {
        
    }

    /**
     * @Override
     * Como éste es el controlador por defecto, si el usuario no está autenticado
     * debería enviarlo al login (en vez de a un 403)
     */
    protected function check_session(array $roles = null) {
        $sm = Session_Manager::get_instance();
        if ($sm->get_id() == 0 || $sm->get_rol_id() == Session_Manager::USUARIO_SIN_AUTENTICAR) {
            $request = Request::get_instance('login');
            Router::call_controller($request);
        }
    }

}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace controller;

use controller\General_Controller as General_Controller;
use view\Provincia_View as Provincia_View;
use model\persistence\direccion\Provincia_Model as Provincia_Model;
use helper\Session_Manager as Session_Manager;

/**
 * Description of Provincia_Controller
 *
 * @author Esteban
 */
class Provincia_Controller extends General_Controller {

    public function __construct() {
        parent::__construct();
        $this->check_session([Session_Manager::ADMINISTRADOR, Session_Manager::BEDEL]);
        $this->view = new Provincia_View();
        $this->model = Provincia_Model::get_instance();
    }

    public function index() {
        $this->view->listar();
    }

    public function listar_ajax() {
        switch (Session_Manager::get_instance()->get_rol_id()) {
            case Session_Manager::ADMINISTRADOR:
                $result = $this->model->get_list(array(), array(), true);
                break;
            case Session_Manager::BEDEL:
                $result = $this->model->get_list();
                break;
        }
        $this->view->listar_ajax($result);
    }

}

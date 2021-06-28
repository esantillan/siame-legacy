<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace controller;

use controller\General_Controller as General_Controller;
use view\Departamento_View as Departamento_View;
use model\persistence\direccion\{
    Departamento_Model as Departamento_Model,
    Provincia_Model as Provincia_Model
};
use helper\Session_Manager as Session_Manager;

/**
 * Description of Departamento_Controller
 *
 * @author Esteban
 */
class Departamento_Controller extends General_Controller {

    public function __construct() {
        parent::__construct();
        $this->check_session([Session_Manager::ADMINISTRADOR, Session_Manager::BEDEL]);
        $this->view = new Departamento_View();
        $this->model = Departamento_Model::get_instance();
    }

    public function index(): void {
        $this->view->listar();
    }

    public function listar_ajax() {
        switch (Session_Manager::get_instance()->get_rol_id()) {
            case Session_Manager::ADMINISTRADOR:
                $fields = ['tp.id', 'tp.nombre', 'taux1.id AS provincia_id', 'taux1.nombre AS provincia', 'date_format(tp.fecha_alta,"%d-%m-%Y %H:%i:%s") AS fecha_alta', '(SELECT u.user FROM usuario u where tp.operador_alta = u.id) as operador_alta', 'date_format(tp.fecha_modificacion,"%d-%m-%Y %H:%i:%s") AS fecha_modificacion', '(SELECT u.user FROM usuario u where tp.operador_modificacion = u.id) as operador_modificacion', 'tp.baja'];
                $result = $this->model->get_list($fields, array(), true);
                break;
            case Session_Manager::BEDEL:
                $fields = ['tp.id', 'tp.nombre', 'taux1.id AS provincia_id', 'taux1.nombre AS provincia', 'tp.baja'];
                $result = $this->model->get_list($fields, array());
                break;
        }
        $this->view->listar_ajax($result);
    }

    public function listar_provincias() {
        $this->provincia_model = Provincia_Model::get_instance();
        /*
         * Defino un alias para el campo nombre, ya que select2 requiere que 
         * los parámetros se llamen así
         */
        $ret = $this->provincia_model->get_list(array("CONCAT(tp.nombre,'_',id) AS id", 'tp.nombre AS text'), array(), false);
        $this->view->listar_provincias($ret);
    }

}

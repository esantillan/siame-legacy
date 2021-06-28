<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace controller;

use controller\General_Controller as General_Controller;
use view\Localidad_View as Localidad_View;
use model\persistence\direccion\{
    Localidad_Model as Localidad_Model,
    Departamento_Model as Departamento_Model,
    Provincia_Model as Provincia_Model
};
use helper\{
    Session_Manager as Session_Manager,
    Sanitizer as Sanitizer
};

/**
 * Description of Localidad_Controller
 *
 * @author Esteban
 */
class Localidad_Controller extends General_Controller {

    public function __construct() {
        parent::__construct();
        $this->check_session([Session_Manager::ADMINISTRADOR, Session_Manager::BEDEL]);
        $this->view = new Localidad_View();
        $this->model = Localidad_Model::get_instance();
        $this->aux_models = ['provincia_model', 'departamento_model'];
    }

    public function index(): void {
        $this->view->listar();
    }

    public function listar_ajax() {
        switch (Session_Manager::get_instance()->get_rol_id()) {
            case Session_Manager::ADMINISTRADOR:
                $aux = '(SELECT CONCAT(d.nombre," - ",p.nombre) FROM departamento d JOIN provincia p ON p.id = d.provincia_id WHERE d.id = tp.departamento_id) AS departamento';
                $fields = ['tp.id', 'tp.nombre', 'taux1.id AS departamento_id', $aux, 'date_format(tp.fecha_alta,"%d-%m-%Y %H:%i:%s") AS fecha_alta', '(SELECT u.user FROM usuario u where tp.operador_alta = u.id) as operador_alta', 'date_format(tp.fecha_modificacion,"%d-%m-%Y %H:%i:%s") AS fecha_modificacion', '(SELECT u.user FROM usuario u where tp.operador_modificacion = u.id) as operador_modificacion', 'tp.baja'];
                $result = $this->model->get_list($fields, array(), true);
                break;
            case Session_Manager::BEDEL:
                $aux = '(SELECT CONCAT(d.nombre," - ",p.nombre) FROM departamento d JOIN provincia p ON p.id = d.provincia_id WHERE d.id = tp.departamento_id) AS departamento';
                $fields = ['tp.id', 'tp.nombre', $aux, 'taux1.id AS departamento_id', 'tp.baja'];
                $result = $this->model->get_list($fields, array(), true);
                break;
        }
        $this->view->listar_ajax($result);
    }

    public function listar_provincias() {
        $this->aux_models['provincia_model'] = Provincia_Model::get_instance();
        /*
         * Defino un alias para el campo nombre, ya que select2 requiere que 
         * los parámetros se llamen así
         */
        $ret = $this->aux_models['provincia_model']->get_list(array("CONCAT(tp.nombre,'_',tp.id) AS id", 'tp.nombre AS text'), array(), false);
        $this->view->listar_provincias($ret);
    }

    public function listar_departamentos($type = '', $id = 0) {
        $ret = null;
        $provincia_id = null;
        switch ($type) {
            case 'provincia':
                Sanitizer::validate_number($id, Sanitizer::INTEGER);
                $this->aux_models['departamento_model'] = Departamento_Model::get_instance();
                $ret = $this->aux_models['departamento_model']->get_list(array("CONCAT(tp.nombre,'_',tp.id) AS id", 'tp.nombre AS text'), array('tp.provincia_id = ' => $id));
                $provincia_id = $id;
                break;
            case 'departamento':
                Sanitizer::validate_number($id, Sanitizer::INTEGER);
                $this->aux_models['departamento_model'] = Departamento_Model::get_instance();
                $this->aux_models['provincia_model'] = Provincia_Model::get_instance();
                $provincia_id = '';
                $provincia_id = $this->aux_models['provincia_model']->get_list(array("CONCAT(tp.nombre,'_',tp.id) AS id"), array("tp.id = (SELECT d.provincia_id FROM departamento d WHERE d.id = '{$id}')"));
                $ret = $this->aux_models['departamento_model']->get_list(
                        array("CONCAT(tp.nombre,'_',tp.id) AS id", 'tp.nombre AS text'), array('provincia_id = ' => explode('_', $provincia_id[0]['id'])[1]));
                $provincia_id = array_shift($provincia_id)['id'];
                break;
        }
        $this->view->listar_departamentos($ret, $provincia_id);
    }

}

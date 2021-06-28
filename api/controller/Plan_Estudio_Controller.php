<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace controller;

use controller\General_Controller as General_Controller;
use helper\Session_Manager as Session_Manager;
use helper\Logger as Logger;
use model\persistence\institucional\Plan_Estudio_Model as Plan_Estudio_Model;
use view\Plan_Estudio_View as Plan_Estudio_View;
use helper\File_Handler as File_Handler;
use config\Constants as Constants;

/**
 * Description of Plan_Estudio_Controller
 *
 * @author Esteban
 */
class Plan_Estudio_Controller extends General_Controller {

    public function __construct() {
        parent::__construct([
            'condiciones_ingreso' => '<ul><ol><li><u><i><a><img><p><h1><h2><h3><h4><h5><h6><blockquote><div><pre><span><sub><sup><code><em>',
            'articulaciones' => '<ul><ol><li><u><i><a><img><p><h1><h2><h3><h4><h5><h6><blockquote><div><pre><span><sub><sup><code><em>'
        ]);
        $this->check_session([Session_Manager::ADMINISTRADOR]);
        $this->view = new Plan_Estudio_View();
        $this->model = Plan_Estudio_Model::get_instance();
    }

    public function index(): void {
        $this->view->listar();
    }

    public function listar_ajax() {
        $fields = ['tp.id', 'tp.numero_resolucion', 'tp.nombre_carrera', 'tp.nombre_titulo', 'modalidad', 'duracion', 'condiciones_ingreso', 'articulaciones', 'horas_catedra', 'horas_reloj', 'tp.path', 'date_format(tp.fecha_alta,"%d-%m-%Y %H:%i:%s") AS fecha_alta', '(SELECT u.user FROM usuario u where tp.operador_alta = u.id) as operador_alta', 'date_format(tp.fecha_modificacion,"%d-%m-%Y %H:%i:%s") AS fecha_modificacion', '(SELECT u.user FROM usuario u where tp.operador_modificacion = u.id) as operador_modificacion', 'tp.baja'];
        $result = $this->model->get_list([], [], true);
        $this->view->listar_ajax($result);
    }

    public function create(array $params = array()) {
        $ret = ['msg' => 'hola', 'state' => FALSE];
        $required_missing = $this->check_required($_POST);
        if ($required_missing) {
            $ret['msg'] = nl2br($required_missing);
        } else {
            $cols = array();
            foreach ($this->model->get_columns() as $col => $val) {
                if (isset($_POST[$col])) {
                    $cols[$col] = (String) $_POST[$col];
                }
            }
            try {
                if ($_FILES['archivo']['size'] > 0) {
                    if ($_FILES['archivo']['size'] <= 10000000) {
                        $path = 'carreras/resoluciones/';
                        $file = new File_Handler();
                        $path .= $file->upload_file('archivo', 'carreras' . Constants::$DS . 'resoluciones', [File_Handler::PDF, File_Handler::DOC, File_Handler::IMG]);
                        $cols['path'] = $path;
                    } else {
                        $ret['msg'] = 'ERROR: El archivo supera el tamaño máximo permitido (2MB)';
                    }
                }
                $id = $this->model->create($this->user_id, $cols);
                $ret['msg'] = 'Se ha creado correctamente el registro.';
                $ret['state'] = TRUE;
            } catch (\Exception $ex) {
                $ret['msg'] = nl2br($ex->getMessage());
            }
        }
        echo json_encode($ret);
    }

    public function descargar($id = '') {
        $plan_estudio = $this->model->read($id);
        $path = $plan_estudio['path'];
        $file = new File_Handler();
        $file->download_file($path);
    }
    
}

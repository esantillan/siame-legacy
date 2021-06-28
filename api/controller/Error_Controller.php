<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace controller;

use controller\General_Controller as General_Controller;
use helper\Logger as Logger;
use view\Error_View as Error_View;
/**
 * Description of Error_Controller
 *
 * @author Esteban
 */
class Error_Controller extends General_Controller{
    
    public function __construct() {
        parent::__construct();
        $this->view = new Error_View();
    }
    
    public function index() {
        $this->view->display_error(500,'Error interno del Servidor');
    }
    
    public function error_403(String $msg = 'Acceso prohibido') {
        Logger::save_log("Acceso prohibido.\n" . $msg);
        $msg = str_replace('_', ' ', $msg) . '.';
        $this->view->display_error(403,$msg);
    }
    
    public function error_404(String $msg = 'No se ha encontrado el recurso: ',$url = '') {
        Logger::save_log("No se ha encontrado el recurso.\n" . $_GET['url']);
        $msg = str_replace('_', ' ', $msg);
        $this->view->display_error(404,$msg,$url);
    }
    
    public function error_500(String $msg = 'Error interno del servidor') {
        Logger::save_log("Error interno del servidor.\n" . $msg);
        $msg = str_replace('_', ' ', $msg) . '.';
        $this->view->display_error(500,$msg);
    }

    public function listar_ajax() {
        echo json_encode('');
    }

}

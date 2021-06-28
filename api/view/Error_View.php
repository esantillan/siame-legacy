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
 * Description of Error_View
 *
 * @author Esteban
 */
class Error_View extends General_View {

    private $error_num;
    private $msg;

    public function __construct() {
        parent::__construct();
    }

    public function listar() {
        $this->display_error(404, '');
    }

    public function display_error($error_num, $msg, $url = '') {
        if(!empty($url)){
            $msg .= '<span style="color: #444444">' . $url . '</span>';
        }
        $this->error_num = $error_num;
        $this->msg = $msg;
        $this->load_navbar(array(array('fa-home', 'Inicio', 'inicio', 'inicio')));
        $this->load_template($error_num);
        $this->generate_view();
    }

    public function listar_ajax(array $result): void {
        echo json_encode('');
    }

    protected function load_template($file_name): void {
        $this->elements_to_generate['_TEMPLATE_'] = str_replace('_MSG_', $this->msg, file_get_contents(Constants::$ROOT . 'view' . Constants::$DS . 'public' . Constants::$DS . 'html' . Constants::$DS . 'error' . Constants::$DS . 'error_' . $file_name . '.html'));
        $this->elements_to_generate['_TEMPLATE_'] = str_replace('_EMAIL_', $_SERVER['SERVER_ADMIN'], $this->elements_to_generate['_TEMPLATE_']);
    }

}

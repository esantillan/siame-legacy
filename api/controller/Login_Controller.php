<?php

namespace controller;

use controller\General_Controller as General_Controller;
use view\Login_View as Login_View;
use model\persistence\acceso\Access_Model as Access_Model;
use config\{
    Constants as Constants,
    Request as Request,
    Router as Router
};
use helper\{
    Sanitizer as Sanitizer,
    Logger as Logger,
    Session_Manager as Session_Manager
};

/**
 * Description of Login_Controller
 *
 * @author Esteban
 */
class Login_Controller extends General_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $view = new Login_View();
    }

    public function log_in() {
        $user = $_POST['user'];
        $pass = $_POST['pass'];
        if (Access_Model::log_in($user, $pass)) {
            $respuesta["msg"] = true;
            $respuesta["url"] = Constants::$URL . 'inicio';
        } else {
            $respuesta["msg"] = false;
        }
        $jsonData = json_encode($respuesta);
        echo $jsonData;
    }

    public function log_out() {
        $sm = Session_Manager::get_instance();
        $sm->destroy_session();
        $request = Request::get_instance('Login','index',[]);
        Router::call_controller($request);
    }

    public function listar_ajax() {
        //do nothing
    }

}

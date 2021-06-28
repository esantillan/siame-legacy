<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace controller;

use controller\General_Controller as General_Controller;
use model\persistence\direccion\Direccion_Model as Direccion_Model;
use helper\{
    Session_Manager as Session_Manager,
    Sanitizer as Sanitizer
};

/**
 * Description of Direccion_Controller
 *
 * @author Esteban
 */
class Direccion_Controller extends General_Controller {

    /**
     * Notar que no compruebo la sesión, porque este controlador no debe estar 
     * "disponible" o ser accesible para el usuario
     */
    public function __construct() {
        parent::__construct();
        $this->model = Direccion_Model::get_instance();
    }

    public function index(): void {
        throw new \Exception("NO DEBERÍA VER ESTO");
    }

}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace model\persistence\acceso;

use model\persistence\acceso\{
    Access_Model as Access_Model,
    Rol_Permiso_Tabla_Model as Rol_Permiso_Tabla_Model
};

/**
 * Description of Rol_Model
 *
 * @author Esteban
 */
class Rol_Model extends Access_Model {

    private $rol_permiso_tabla_model;

    protected function __construct() {
        parent::__construct('rol');
        $this->columns = ['id' => 'integer', 'descripcion' => 'string'];
        $this->rol_permiso_tabla_model = Rol_Permiso_Tabla_Model::get_instance();
    }

}

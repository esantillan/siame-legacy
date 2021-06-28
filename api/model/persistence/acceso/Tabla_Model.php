<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace model\persistence\acceso;

use model\persistence\acceso\Access_Model as Access_Model;
/**
 * Description of Tabla_Model
 *
 * @author Esteban
 */
class Tabla_Model extends Access_Model{
    protected function __construct() {
        parent::__construct('tabla');
        $this->columns = ['id' => 'integer', 'nombre' => 'string'];
    }
}

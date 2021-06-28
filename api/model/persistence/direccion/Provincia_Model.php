<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace model\persistence\direccion;

use model\persistence\Simple_Model as Simple_Model;
/**
 * Description of Provincia_Model
 *
 * @author Esteban
 */
class Provincia_Model extends Simple_Model{

    protected function __construct() {
        parent::__construct('provincia');
        $this->columns = ['id' => 'integer','nombre' => 'string'];
        $this->uniques = ['id','nombre'];
        $this->required = ['nombre'];
        $this->order_by = ['nombre' => 'asc'];
        $this->dependent_models = ['model\persistence\direccion\Departamento_Model'];
//        $this->max_string_size = 50;
    }
}

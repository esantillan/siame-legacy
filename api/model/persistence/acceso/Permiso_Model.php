<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
declare(strict_types = 1);

namespace model\persistence\acceso;

use model\persistence\acceso\Access_Model as Access_Model;

/**
 * Description of Permiso_Model
 *
 * @author Esteban
 */
class Permiso_Model extends Access_Model{
    protected function __construct() {
        parent::__construct('permiso');
        $this->columns = ['id' => 'integer', 'descripcion' => 'string'];
    }
}

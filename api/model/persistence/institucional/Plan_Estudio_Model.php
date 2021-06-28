<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace model\persistence\institucional;

use model\persistence\Simple_Model as Simple_Model;

/**
 * Description of Plan_Eestudio_Controller
 *
 * @author Esteban
 */
class Plan_Estudio_Model extends Simple_Model {

    public function __construct() {
        parent::__construct('plan_estudio');
        $this->columns = ['id' => 'integer', 'numero_resolucion' => 'string', 'nombre_carrera' => 'string', 'nombre_titulo' => 'string', 'modalidad' => 'string', 'duracion' => 'integer', 'condiciones_ingreso' => 'string', 'articulaciones' => 'string', 'horas_catedra' => 'integer', 'horas_reloj' => 'integer', 'path' => 'string'];
        $this->uniques = ['id', 'numero_resolucion','path'];
        $this->required = ['numero_resolucion', 'nombre_carrera', 'nombre_titulo', 'modalidad', 'duracion', 'horas_catedra', 'horas_reloj'];
        $this->order_by = ['nombre_carrera' => 'asc', 'nombre_titulo' => 'asc'];
//        $this->dependent_models = ['model\persistence\direccion\Departamento_Model'];//@PENDING
        $this->max_string_size = 1000;
    }

}

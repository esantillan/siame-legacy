<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace model\persistence\direccion;

use model\persistence\Compose_Model as Compose_Model;
use model\persistence\direccion\Departamento_Model as Departamento_Model;

/**
 * Description of Localidad_Model
 *
 * @author Esteban
 */
class Localidad_Model extends Compose_Model {

    public function __construct() {
        parent::__construct('localidad');
        $this->columns = ['id' => 'integer', 'nombre' => 'string', 'departamento_id' => 'integer'];
        $this->required = ['nombre', 'departamento_id'];
        $this->uniques = [array('nombre', 'departamento_id')];
        $this->order_by = ['nombre' => 'asc'];
        $this->assoc_models['departamento_model'] = Departamento_Model::get_instance();
        $this->fk_columns = ['departamento_model' => 'departamento_id'];
        $this->dependent_models = ['model\persistence\direccion\Direccion_Model'];
//        $this->max_string_size = 50;
    }

    protected function check_create(array $values = array()) {
        $ret = null;
        if (isset($values['departamento_id'])){
            if(!$this->assoc_models['departamento_model']->exists(array('id' => $values['departamento_id']))) {
                throw new \Exception('Error al comprobar el valor correspondiente a Departamento.' . PHP_EOL . 
                        'Por favor acutalice la tabla y verifique que el Departamento no esté dado de baja');
            }
        }else{
            throw new \Exception('No se ha proporcionado el Departamento (requerido) a la cual pertence');
        }
        return $ret;
    }

    protected function check_update(array $values = array()) {
        $ret = null;
        if (isset($values['departamento_id'])) {
            if (!$this->assoc_models['departamento_model']->exists(array('id' => $values['departamento_id']))) {
                throw new \Exception('Error al comprobar el valor correspondiente a Departamento.' . PHP_EOL . 
                        'Por favor acutalice la tabla y verifique que el Departamento no esté dado de baja');
            }
        } 
        return $ret;
    }

}

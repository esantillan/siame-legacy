<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace model\persistence\direccion;

use model\persistence\Compose_Model as Compose_Model;
use model\persistence\direccion\Localidad_Model as Localidad_Model;
/**
 * Description of Direccion_Model
 *
 * @author Esteban
 */
class Direccion_Model extends Compose_Model{
    /*
     * parent::__construct('localidad');
        $this->columns = ['id' => 'integer', 'nombre' => 'string', 'departamento_id' => 'integer'];
        $this->required = ['nombre', 'departamento_id'];
        $this->uniques = [array('nombre', 'departamento_id')];
        $this->order_by = ['nombre' => 'asc'];
        $this->assoc_models['departamento_model'] = Departamento_Model::get_instance();
        $this->fk_columns = ['departamento_model' => 'departamento_id'];
        $this->dependent_models = ['model\persistence\direccion\Direccion_Model'];
        $this->max_string_size = 50;
     */
    public function __construct() {
        parent::__construct('direccion');
        $this->columns = ['id' => 'integer','calle' => 'string', 'numero' => 'integer','localidad_id' => 'integer'];
        $this->required = ['calle','localidad_id'];//puede no tener número
        $this->order_by = ['calle' => 'asc','numero'=>'asc'];
        $this->assoc_models['localidad_model'] = Localidad_Model::get_instance();
        $this->fk_columns = ['localidad_model' => 'localidad_id'];
//        $this->max_string_size = 50;
    }

    protected function check_create(array $values){
        $ret = null;
        if (isset($values['localidad_id'])){
            if(!$this->assoc_models['localidad_model']->exists(array('id' => $values['localidad_id']))) {
                throw new \Exception('Error al comprobar el valor correspondiente a Localidad.' . PHP_EOL . 
                        'Por favor acutalice la tabla y verifique que la Localidad no esté dada de baja');
            }
        }else{
            throw new \Exception('No se ha proporcionado la Localidad (requerida) a la cual pertence');
        }
        return $ret;
    }

    protected function check_update(array $values) {
        $ret = null;
        if (isset($values['localidad_id'])) {
            if (!$this->assoc_models['localidad_model']->exists(array('id' => $values['localidad_id']))) {
                throw new \Exception('Error al comprobar el valor correspondiente a Localidad.' . PHP_EOL . 
                        'Por favor acutalice la tabla y verifique que la Localidad no esté dada de baja');
            }
        } 
        return $ret;
    }
}

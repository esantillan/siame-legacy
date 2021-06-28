<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace model\persistence\direccion;

use model\persistence\Compose_Model as Compose_Model;
use model\persistence\direccion\Provincia_Model as Provincia_Model;

/**
 * Description of Departamento_Model
 *
 * @author Esteban
 */
class Departamento_Model extends Compose_Model {

    public function __construct() {
        parent::__construct('departamento');
        $this->columns = ['id' => 'integer', 'nombre' => 'string', 'provincia_id' => 'integer'];
        $this->required = ['nombre', 'provincia_id'];
        $this->uniques = [['nombre', 'provincia_id']]; //no incluir el ID
        $this->order_by = ['nombre' => 'asc'];
        $this->assoc_models['provincia_model'] = Provincia_Model::get_instance();
        $this->fk_columns = ['provincia_model' => 'provincia_id'];
        $this->dependent_models = ['model\persistence\direccion\Localidad_Model'];
//        $this->max_string_size = 50;
    }

    protected function check_create(array $values) {
        $ret = null;
        if (isset($values['provincia_id'])){
            if(!$this->assoc_models['provincia_model']->exists(array('id' => $values['provincia_id']))) {
                throw new \Exception('Error al comprobar el valor correspondiente a Provincia.' . PHP_EOL . 
                        'Por favor acutalice la tabla y verifique que la provincia no esté dada de baja');
            }
        }else{
            throw new \Exception('No se ha proporcionado la provincia (requerida) a la cual pertence');
        }
        return $ret;
    }

    protected function check_update(array $values) {
        $ret = null;
        if (isset($values['provincia_id'])) {//si se va actualizar la provincia (a la que "pertenecería" el departamento)
            if (!$this->assoc_models['provincia_model']->exists(array('id' => $values['provincia_id']))) {//compruebo que el id de la "nueva provincia" exista
                throw new \Exception('Error al comprobar el valor correspondiente a Provincia.' . PHP_EOL . 
                        'Por favor acutalice la tabla y verifique que la provincia no esté dada de baja');
            }
        } 
        return $ret;
    }

}

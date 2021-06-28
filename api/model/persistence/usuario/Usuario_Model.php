<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace model\persistence\usuario;

use model\persistence\Compose_Model as Compose_Model;
use model\persistence\direccion\Direccion_Model as Direccion_Model;
use model\persistence\acceso\Rol_Model as Rol_Model;

/**
 * Description of Usuario_Model
 *
 * @author Esteban
 */
class Usuario_Model extends Compose_Model {

    public function __construct() {
        parent::__construct('usuario');
        $this->columns = ['id' => 'integer', 'documento' => 'integer','fecha_nacimiento' => 'date', 'nombre' => 'string', 'apellido' => 'string', 'user' => 'string', 'pass' => 'string', 'email' => 'string', 'telefono_fijo' => 'integer', 'telefono_movil' => 'integer', 'direccion_id' => 'integer', 'rol_id' => 'integer'];
        $this->required = ['documento', 'nombre', 'apellido', 'fecha_nacimiento','user', 'pass', 'email', 'direccion_id', 'rol_id'];
        $this->uniques = [['documento', 'nombre', 'apellido', 'rol_id'], ['email', 'rol_id'],['user','rol_id']];
        $this->order_by = ['apellido' => 'asc', 'nombre' => 'asc'];
        $this->assoc_models['direccion_model'] = Direccion_Model::get_instance();
        $this->assoc_models['rol_model'] = Rol_Model::get_instance();
        $this->fk_columns = ['direccion_model' => 'direccion_id', 'rol_model' => 'rol_id'];
        $this->dependent_models = []; //@PENDING
        $this->min_string_size = 1;
        $this->max_string_size = 150;
    }

    protected function check_create(array $values) {
        $ret = null;
        if (isset($values['direccion_id'])){
            if(!$this->assoc_models['direccion_model']->exists(array('id' => $values['direccion_id']))) {
                throw new \Exception('Error al comprobar el valor correspondiente a Localidad.' . PHP_EOL . 
                        'Por favor acutalice la tabla y verifique que la Localidad no esté dada de baja');
            }
        }else{
            throw new \Exception('No se ha proporcionado la Direccion (requerida) a la cual pertence');
        }
        if(isset($values['rol_id'])){
            if(!$this->assoc_models['rol_model']->exists(array('id' => $values['rol_id']))) {
                throw new \Exception('Error al comprobar el valor correspondiente a Rol');
            }
        }else{
            throw new \Exception('No se ha proporcionado la Rol (requerido) al cual pertence');
        }
        return $ret;
    }

    protected function check_update(array $values) {
        $ret = null;
        if (isset($values['direccion_id'])){
            if(!$this->assoc_models['direccion_model']->exists(array('id' => $values['direccion_id']))) {
                throw new \Exception('Error al comprobar el valor correspondiente a Localidad.' . PHP_EOL . 
                        'Por favor acutalice la tabla y verifique que la Localidad no esté dada de baja');
            }
        }
        if(isset($values['rol_id'])){
            if(!$this->assoc_models['rol_model']->exists(array('id' => $values['rol_id']))) {
                throw new \Exception('Error al comprobar el valor correspondiente a Rol');
            }
        }
        return $ret;
    }

}
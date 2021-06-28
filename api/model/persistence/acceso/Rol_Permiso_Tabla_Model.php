<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace model\persistence\acceso;

use model\persistence\acceso\{
    Access_Model as Access_Model,
    Permiso_Model as Permiso_Model,
    Tabla_Model as Tabla_Model
};

/**
 * Description of Rol_Permiso_Tabla_Model
 *
 * @author Esteban
 */
class Rol_Permiso_Tabla_Model extends Access_Model{
    /*
     * Referencias a los modelos con los que interactúa
     */

    private $permiso_model;
    private $tabla_model;

    protected function __construct() {
        parent::__construct('rol_permiso_tabla');
        $this->columns = ['id' => 'integer', 'permiso_id' => 'integer','rol_id' => 'integer','tabla_id' => 'integer'];
        $this->permiso_model = Permiso_Model::get_instance();
        $this->tabla_model = Tabla_Model::get_instance();
    }

    public function get_list(array $fields = array('rol_id','permiso_id','tabla_id'), array $cond = array(), bool $include_removed = false):array{
        $ret = parent::get_list($fields, $cond, true);
        if(!empty($ret)){
            foreach ($ret as $rpt) {
                //reemplazo los id de la FK por los registros correspondientes
                //(excepto rol_id, ya que obtendría un loop infinito -además,
                //esta clase no debe "conocer" al rol por como lo he diseñado)
                $rpt['permiso_id'] = $this->permiso_model->read($rpt['permiso_id']);
                $rpt['tabla_id'] = $this->tabla_model->read($rpt['tabla_id']);
            }
        }
        return $ret;
    }
    
    public function read(int $id, bool $include_removed = false) {
        $ret = parent::read($id, $include_removed);
        if(!empty($ret)){
            $ret['permiso_id'] = $this->permiso_model->read($ret['permiso_id']);
            $ret['tabla_id'] = $this->tabla_model->read($ret['tabla_id']);
        }
        return $ret;
    }
}

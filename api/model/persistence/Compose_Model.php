<?php

namespace model\persistence;

use model\persistence\Simple_Model as Simple_Model;
use model\persistence\Model_Exception as Model_Exception;
use model\persistence\acceso\Access_Model as Access_Model;
use helper\Sanitizer as Sanitizer;

/**
 * De esta clase derivarán todos los modelos que necesitan "un proceso más complejo"
 * para instanciar sus dto, es decir, que necesiten de un método de apoyo
 *
 * @author Esteban
 */
abstract class Compose_Model extends Simple_Model {
    /*
     * aquí se guardarán las referencias a los modelos con los que necista
     * interactuar cada subclase, visto desde el diagrama de clases, las asociaciones
     * (punteros) o clases con las que ésta interactúa (perdón por el nombre, pero
     * no se me ocurrió algo mejor)
     */
    protected $assoc_models = array();
    protected $fk_columns = array(); //arreglo asociativo 'nombre_model'=>'nombre_campo_que_es_fk'

    public function get_list(array $fields = array(), array $cond = array(), bool $include_removed = false): array {
        Access_Model::check_perms($this->table, Access_Model::READ);
        $sql = 'SELECT ';
        if (empty($fields)) {//si no he recibido los campos por los cuales proyectar, traigo todos
            $max = count($this->columns);
            $count = 1;
            foreach ($this->columns as $col_name => $type) {
                $sql .= "tp.{$col_name}, ";
            }
            $sql .= 'date_format(tp.fecha_alta,"%d-%m-%Y %H:%i:%s") AS fecha_alta,(SELECT u.user FROM usuario u where tp.operador_alta = u.id) as operador_alta,date_format(tp.fecha_modificacion,"%d-%m-%Y %H:%i:%s") AS fecha_modificacion,(SELECT u.user FROM usuario u where tp.operador_modificacion = u.id) as operador_modificacion, tp.baja ';
        } else {//proyecto sobre los campos pasados como parámetro
            $max = count($fields);
            $count = 0;
            foreach ($fields as $colum) {
                $sql .= $colum . (( ++$count < $max) ? ', ' : ' ');
            }
        }
        $sql .= "FROM $this->table AS tp "; //establezco el alias como "tp" que significa "tabla principal" (la cual estoy consultando)
        //Consulto que las FK "apunten" a registros "válidos"
        $max = count($fields);
        $count = 1;
        foreach ($this->fk_columns as $aux_model_name => $fk_column_name) {
            $alias = 'taux' . $count++; //establezco el alias como "taux" que significa "tabla auxiliar[numero]"
            $sql .= " JOIN {$this->assoc_models[$aux_model_name]->get_table_name()} AS {$alias} ON tp.{$fk_column_name} = {$alias}.id AND {$alias}.baja = false ";
        }
        //establezco la condición
        $sql .= ' WHERE ' . ($include_removed ? '' : ' tp.baja = 0 AND ');
        if (empty($cond)) {//si no recibí una condición
            $sql .= '1 = 1';
        } else {//establezco condición
            $max = count($cond);
            $count = 0;
            foreach ($cond as $column => $value) {
                /*
                 * si no he recibido el nombre de la columna (por lo que es
                 * un intero), significa que es una subconsulta
                 */
                if(is_integer($column)){
                    $sql .= "{$value} " . (( ++$count < $max) ? 'AND ' : '');
                }else{
                    $sql .= "{$column} '{$value}' " . (( ++$count < $max) ? 'AND ' : '');
                }
                
            }
        }
        //agrego ordenación
        $sql .= ' ORDER BY ';
        $max = count($this->order_by);
        $count = 1;
        foreach ($this->order_by as $field => $order) {
            $sql .= "tp.{$field} {$order}" . (( $count++ < $max) ? ', ' : '');
        }
        $ret = self::$conexion->query($sql);
        return $ret;
    }

    public function read(int $id, bool $include_removed = false) {
        Access_Model::check_perms($this->table, Access_Model::READ);
        //agrego los campos a consultar
        $sql = "SELECT ";
        $max = count($this->columns);
        $count = 1;
        foreach ($this->columns as $col_name => $type) {
            $sql .= "tp.{$col_name}, ";
        }
        $sql .= 'tp.fecha_alta,(SELECT u.user FROM usuario u where tp.operador_alta = u.id) as operador_alta,tp.fecha_modificacion,(SELECT u.user FROM usuario u where tp.operador_modificacion = u.id) as operador_modificacion, tp.baja ';
        //establezco el alias como "tp" que significa "tabla principal" (la cual estoy consultando)
        $sql .= "FROM $this->table AS tp "; 
        //Consulto que las FK "apunten" a registros "válidos"
        $max = count($this->fk_columns);
        $count = 1;
        foreach ($this->fk_columns as $aux_model_name => $fk_column_name) {
            $alias = 'taux' . $count++; //establezco el alias como "taux" que significa "tabla auxiliar[numero]"
            $sql .= " JOIN {$this->assoc_models[$aux_model_name]->get_table_name()} AS {$alias} ON tp.{$fk_column_name} = {$alias}.id AND {$alias}.baja = false ";
        }
        //agrego condición
        $sql .= "WHERE tp.id = {$id} " . ($include_removed ? '' : 'AND tp.baja = 0 ');
        if (Sanitizer::validate_number($id, Sanitizer::INTEGER)) {
            $ret = self::$conexion->query($sql);
        } else {
            throw new \Exception("El ID recibido '{$id}' no ha pasado el filtro de saneamiento" . PHP_EOL);
        }
        return array_shift($ret);
    }

    public function create(int $operador, array $values = array()) {
        $this->check_create($values);
        return parent::create($operador, $values);
    }

    public function update(int $id, int $operador, array $values = array()) {
        $this->check_update($values);
        return parent::update($id, $operador, $values);
    }

    protected abstract function check_create(array $values);

    protected abstract function check_update(array $values);
}

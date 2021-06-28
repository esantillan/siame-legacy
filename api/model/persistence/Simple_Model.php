<?php

namespace model\persistence;

use model\persistence\{
    Conexion as Conexion,
    Model_Class as Model_Class,
    Simple_DTO as Simple_DTO,
    Model_Exception as Model_Exception
};
use helper\Sanitizer as Sanitizer;
use model\persistence\acceso\Access_Model as Access_Model;

/**
 * Clase abstracta que engloba los métodos comunes de los Modelos, que son los 
 * correspondientes a las operaciones CRUD.
 * 
 * Los métodos check_create y check_update, deben realizar comprobaciones específicas 
 * según cada modelo (largo de las columnas, not null, etc.), por lo que delego 
 * la implementación de dichos métodos a las subclases.
 *
 * @author Esteban
 * @version 1.0
 * @package Model
 */
abstract class Simple_Model implements Model_Class {

    protected static $conexion;
    /*
     * nombre de la tabla
     */
    protected $table; //nombre de la tabla
    /*
     * nombre de la columna PK
     * @FIXME La PK debe estar formada por una sóla columna, esto se puede "subsanar"
     * utilizando la propiedad "uniques" que defino más abajo
     * 
     */
    protected $pk_column = 'id';
    /*
     * Campos que posee la tabla. @SEE NO incluir operador_*, fecha_* ni baja
     */
    protected $columns = array();
    /*
     * Arreglo que contiene el nombre de los campos que deben ser únicos.
     * Se puede anidar para claves únicas "compuestas". Ej:
     *      ['campo1',['campo2','campo3'],'campo4']
     * NOTA: NO incluir el campo el mismo campo que se encuentra en $pk_column
     */
    protected $uniques = array();
    /*
     * Arreglo que contiene el nombre de los campos requeridos (NOT NULL)
     */
    protected $required = array();
    /*
     * Arreglo asociativo que contiene los campos por los cuales se ordenará.
     * Ej: ['nombre' => 'asc','apellido' => 'desc']
     */
    protected $order_by = array();
    /*
     * Tamaño mínimo que deben ser los campos de tipo cadena
     */
    protected $min_string_size = 3;
    /*
     * Tamaño máximo que pueden tener los campos de tipo cadena
     */
    protected $max_string_size = 100;
    /* array que contendrá los modelos auxiliares con los que interactuará. 
     * tendrá la forma de 'nombre_modelo' => 'intancia'
     */
    protected $dependent_models = array();
    //no utilizo constantes porque los modificadores de visibilidad para éstas están soportados apartir de php 7.1
    protected static $CHECK_CREATE = 1;
    protected static $CHECK_ENABLE = 2;
    protected static $CHECK_UPDATE = 3;

    /**
     * @SEE Array asociativo que contendrá los SINGLETON's de los modelos
     */
    protected static $instances = array();

    protected function __construct($table) {
        self::$conexion = Conexion::get_instance();
        $this->table = $table;
    }

    /**
     * Método que implementa el SINGLETON.<br>
     * Según la clase (subclase) que lo invoque, "revisa" si existe una instancia
     * de ella en la propiedad estática "instances", si no existe la crea y almacena
     * en dicha propiedad luego, en ambos casos devuelve la instance de la clase invocada.
     * 
     * @package void
     * @return Object<Model_Exception> $instance Objeto de la clase requerida
     */
    public static function get_instance(): Model_Class {
        $class = get_called_class(); //obtengo el nombre de la subclase desde la cual fue invocado
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class();
        }
        return self::$instances[$class];
    }

    /*
     * Métodos ABM
     */

    /**
     * Inserta un nuevo registro 
     * @SEE este método en conjunto con check_create, implementan el patrón 
     * "Template Method"
     * 
     * @param Array $values Arreglo asociativo NOMBRE_COLUMNA => VALOR
     * @return void
     */
    public function create(int $operador, array $values = array()) {
        Access_Model::check_perms($this->table, Access_Model::CREATE);
        if (isset($operador) && $this->check_user($operador) && !empty($values)) {
            $this->check_params(null, $values, self::$CHECK_CREATE); //@SEE puede lanzar una Excepcion
            $sql = 'INSERT INTO ' . $this->table;
            $columns_names = ' (operador_alta,';
            $col_values = " VALUES ('{$operador}',";
            $max = count($values);
            $count = 0;
            foreach ($values as $column => $val) {
                $count++;
                $columns_names .= $column . (($count < $max) ? ', ' : ') ');
                $col_values .= "'{$val}'" . (($count < $max) ? ', ' : ');');
            }
            $sql .= $columns_names . $col_values;
            return self::$conexion->simple_query($sql, true);
        } else {
            $output = '';
            if (empty($values))
                $output .= 'No se han proporcionado valores';
            if (!($this->check_user($operador)))
                $output .= "El OPERADOR '{$operador}' no es válido";
            throw new \Exception($output);
        }
    }

    /**
     * Actualiza un registro 
     * @SEE este método en conjunto con check_update, implementan el patrón "Template Method"
     * 
     * @param Array $values Arreglo asociativo NOMBRE_COLUMNA => VALOR
     * @return void
     */
    public function update(int $id, int $operador, array $values = array()) {
        Access_Model::check_perms($this->table, Access_Model::UPDATE);
        if (isset($id) && isset($operador) && $this->check_user($operador) && Sanitizer::validate_number($id, Sanitizer::INTEGER) && !empty($values)) {
            $this->check_params((int) $id, $values, self::$CHECK_UPDATE);
            $sql = "UPDATE {$this->table} SET operador_modificacion = {$operador}, fecha_modificacion = '{$this->get_timestamp()}', ";
            $max = count($values);
            $count = 0;
            if (in_array('id', $values))
                unset($values['id']); //evito que cambien el ID
            foreach ($values as $column => $value) {
                $sql .= "{$column} = '{$value}' " . (( ++$count < $max) ? ', ' : ' ');
            }
            $sql .= 'WHERE id = ' . $id;
            self::$conexion->simple_query($sql, true);
        } else {
            $output = '';
            if (empty($values))
                $output .= 'No se han proporcionado valores';
            if (!($this->check_user($operador)))
                $output .= "El operador '{$operador}' no es válido";
            throw new \Exception($output);
        }
    }

    /**
     * Habilita un registro que esté "de baja"
     * 
     * @param int $id ID del registro a habilitar
     * @param int $operador ID del operador
     * @return void
     */
    public function enable(int $id, int $operador) {
        Access_Model::check_perms($this->table, Access_Model::UPDATE);
        if (isset($id) && isset($operador) && $this->check_user($operador) && Sanitizer::validate_number($id, Sanitizer::INTEGER)) {
            $this->check_params((int) $id, array(), self::$CHECK_ENABLE);
            $sql = "UPDATE {$this->table} SET operador_modificacion = {$operador}, fecha_modificacion = '{$this->get_timestamp()}', baja = false WHERE id = {$id}";
            self::$conexion->simple_query($sql, true);
        } else {
            $output = "El ID '{$id}' o el OPERADOR '{$operador}' no son válidos";
            throw new \Exception($output);
        }
    }

    /**
     * Recibe el ID del registro a dar de baja y el id del usuario (operador),
     * establece la <code>fecha_modificacion, operador_modificacion</code> y setea
     * <code>baja</code> en true
     * 
     * @param int $id ID del registro a dar de baja
     * @param int $operador ID del usuario(operador) que realiza la accion
     * @return void
     */
    public function delete(int $id, int $operador) {
        Access_Model::check_perms($this->table, Access_Model::DELETE);
        if (isset($id) && isset($operador) && Sanitizer::validate_number($id, Sanitizer::INTEGER) && $this->check_user($operador) && $this->exists(array('id' => $id))) {
            if ($this->check_assoc($id)) {
                self::$conexion->simple_query("UPDATE {$this->table} SET operador_modificacion = {$operador}, fecha_modificacion = '{$this->get_timestamp()}', baja = 1 WHERE id = {$id}", true);
            } else {
                throw new \Exception('Revise que no existan registros asociados.');
            }
        } else {
            throw new \Exception("El ID '{$id}' y/o el OPERADOR '{$operador}' no son válidos." . PHP_EOL);
        }
    }

    /**
     * Comprueba que no existan registros asociados (para poder dar de baja).
     * NOTA: La declaro como <code>protected</code> porque quizás necesite redefinir
     * su comportamiento en algún modelo
     * 
     * @param void
     * @return boolean Retorna un TRUE en caso de que no existan registros asociados,
     * FALSE caso contrario
     */
    protected function check_assoc($id) {
        $r = false;
        $fk_name = $this->table . '_id';
        foreach ($this->dependent_models as $model_name) {
            $a = $model_name::get_instance();
            $r = $a->get_list(array('tp.id', "tp.{$fk_name}"), array("{$fk_name} = " => $id));
        }
        return (empty($r) ? true : false);
    }

    /*
     * Métodos de Lectura
     */

    /**
     * A partir de un ID devuelve un arreglo asociativo
     * correspondiente con el registro que se encuentra en la BD.<br>
     * <b>NOTA:</b> Este método devuelve <b>un sólo registro</b>
     * 
     * @param int $id ID del registro buscado
     * @param bool $include_removed Si es true incluye registros que hayan sido 
     * dados de baja
     * @return Array Devuelve un Array sociativo que representa el registro en la
     * bd
     */
    public function read(int $id, bool $include_removed = false) {
        Access_Model::check_perms($this->table, Access_Model::READ);
        $sql = "SELECT * FROM {$this->table} WHERE id = {$id} " . ($include_removed ? '' : 'AND baja = 0 ');
        if (Sanitizer::validate_number($id, Sanitizer::INTEGER)) {
            $ret = self::$conexion->query($sql);
        } else {
            throw new \Exception("El ID recibido '{$id}' no ha pasado el filtro de saneamiento" . PHP_EOL);
        }
        return array_shift($ret);
    }

    /**
     * ADVERTENCIA: NO UTILIZAR ESTE MÉTODO PARA REALIZAR OPERACIONES ABM.
     * Este método en realidad ELIMINA(no lo da de baja) el registro de la tabla.
     * Esto es porque en un controlador de un modelo compuesto, si primero creé el
     * modelo auxiliar (sin errores) pero fallo al crear el modelo "principal", debería
     * "deshacer" esa acción (eliminar el registro creado) para evitar "llenar la tabla
     * con registros inutiles"
     * @param type $id
     */
    public function rollback($id) {
        if (isset($id) && isset($operador) && Sanitizer::validate_number($id, Sanitizer::INTEGER)) {
            $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
        }
    }

    /**
     * Devuelve todos los registros de la tabla que cumplan con la condicion pasada
     * como parámetro. Si no se le pasa ninguna, devuelve todos los registros.<br>
     * <b>NOTA:</b><br>
     * *Si se quiere aplicar alguna función SQL a los campos a proyectar,
     * se puede pasar algo así <code>array('CONCAT(tp.nombre,tp.id) AS nombre')</code>  
     * tener en cuenta que el alias <b>tp</b> significa "tabla principal" (esto
     * es porque un modelo puede interactuar con otros modelos)<br>
     * *Para establecer una subconsulta, proporcionarla en el array $where de manera
     * quede <code>['condicion'=>'valor','subconsulta']</code> (el orden no es importante)
     * aunque hay que tener en cuenta que en este caso <b>NO SE SANEA LA SUBCONSULTA</b>
     * por lo que debería sanear los parámetros correspondientes al momento de realizar
     * la llamada
     * 
     * @param array $fields Array que contiene el nombre de los campos, si no se 
     * proporciona uno, traerá todos los campos. <b>NOTA:</b> para obtener un listado
     * completo, no pasar <code>array('*')</code>, sino: <b><code>array()</code></b>
     * @param array $cond Array asociativo en el cual cada par CLAVE => VALOR
     * son condiciones del filtro where. Ej: array('ID <>' => 1). NOTA: la columna 
     * "clave" debe contener el tipo de operador (ej. =, <,>, etc).<br>
     * @param bool $include_removed si es true incluye los registros dados de baja
     * @return Array<Array> Devuelve un arreglo anidado, en el cual cada sub-arreglo
     * representa a un registro en la bd.
     * 
     * @SEE prefiero utilizar dos parámetros (en vez de pasar 'baja' => false)
     * porque este refleja mejor mi idea
     */
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
                $sql .= '' . $colum . (( ++$count < $max) ? ', ' : ' ');
            }
        }
        $sql .= "FROM $this->table AS tp "; //establezco el alias como "tp" que significa "tabla principal" (la cual estoy consultando)
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
                if (is_integer($column)) {
                    $sql .= "{$value} " . (( ++$count < $max) ? 'AND ' : '');
                } else {
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
        //ejecuto la consulta
        $ret = self::$conexion->query($sql);
        return $ret;
    }

    /*
     * Métodos de soporte
     */

    /**
     * Compruebo los valores a insertar/actualizar
     * 
     * @param int $id [opcional] ID del registro a modificar
     * @param array $values valores a insertar/actualizar
     * @param int $type_operation <b>"constante" definida en esta misma clase</b>,
     * según la cual se aplicarán validaciones para CREATE o UPDATE
     * @return void No retorna nada, pero lanza <b>Model_Exception</b> en caso de 
     * no pasar todas las validaciones
     */
    protected function check_params(int $id = null, array $values = array(), int $type_operation = self::CHECK_CREATE) {
        \helper\Logger::save_log($values);
        $output = '';
        $throw_excep = false;
        $req_missing = array();
        $check_unique = true;
        /*
         * Compruebo que los valores recibidos sean del tipo y precisión (tamaño)
         * requeridos
         */
        if ($type_operation == self::$CHECK_CREATE || $type_operation == self::$CHECK_UPDATE) {
            foreach ($this->columns as $name => $type) {
                if (array_key_exists($name, $values) && !empty($values[$name])) {
                    switch ($type) {
                        case 'string':
                            if (strlen($values[$name]) < $this->min_string_size) {
                                $throw_excep = true;
                                $output .= "La cadena recibida '{$values[$name]}' es menor al mínimo permitido ({$this->min_string_size})." . PHP_EOL;
                            }
                            if (strlen($values[$name]) > $this->max_string_size) {//compruebo que la cadena sea menor al máximo
                                $throw_excep = true;
                                $output .= "La cadena recibida '{$values[$name]}' es Mayor al máximo permitido ({$this->max_string_size})." . PHP_EOL;
                            }
                            break;
                        case 'integer':
                            try {
                                $aux = $values[$name];
                                Sanitizer::validate_number($values[$name], Sanitizer::INTEGER);
                                if (empty($values[$name]) && $values[$name] !== 0) {
                                    throw new \Exception();
                                }
                            } catch (\Exception $exc) {
                                $throw_excep = true;
                                $output .= "El valor '{$values[$name]}' (" . gettype($aux) . ") no es de tipo '{$type}'." . PHP_EOL;
                            }
                            break;
                        case 'float':
                            try {
                                $aux = $values[$name];
                                Sanitizer::validate_number($values[$name], Sanitizer::FLOAT);
                                if (empty($values[$name]) && $values[$name] !== 0)
                                    throw new \Exception();
                            } catch (\Exception $exc) {
                                $throw_excep = true;
                                $output .= "El valor '{$values[$name]}' (" . gettype($aux) . ") no es de tipo '{$type}'." . PHP_EOL;
                            }
                            break;
                        case 'boolean':
                            if (!settype($values[$name], 'boolean')) {
                                $throw_excep = true;
                                $output .= "El valor recibido '{$values[$name]}' no se ha podido convertir a booleano." . PHP_EOL;
                            }
                        case 'date':
                            if (!Sanitizer::validate_date($values[$name], 'Y-m-d')) {
                                $throw_excep = true;
                                $output .= "El valor recibido '{$values[$name]}' no posee el formato esperado ('Y-m-d') para una fecha." . PHP_EOL;
                            }
                            break;
                        default ://Técnicamente, nunca debería llegar a este punto, pero por si acaso meto mal "un dedazo..."
                            throw new Model_Exception("El tipo '{$type}' no es válido." . PHP_EOL);
                    }
                }
            }
        }
        if ($type_operation == self::$CHECK_CREATE) {//si es un create, compuebo lo campos requeridos
            foreach ($this->required as $field) {
                if (!array_key_exists($field, $values) || empty($values[$field])) {
                    $throw_excep = true;
                    $req_missing[] = $field;
                }
            }
        }
        /*
         * Compruebo los campos que deben ser únicos
         * NOTA: No me sirve el método exists, ya que necesito aplicar filtros 
         * más complejos
         */
        $query = "SELECT id FROM {$this->table} ";
        $where = '';
        $max = count($this->uniques);
        $count = 0;
        // si es un update, me traigo el registro que voy a modificar
        if ($type_operation != self::$CHECK_CREATE && !empty($id)) {
            $registry = self::$conexion->query("SELECT * FROM {$this->table} WHERE id = '{$id}'");
            $registry = array_shift($registry);
        }
        if (count($this->uniques) != 0) {//si no está vacía la propiedad UNIQUES
            switch ($type_operation) {
                case self::$CHECK_CREATE:
                    $or = false; //bandera para detectar cuando puse un OR
                    foreach ($this->uniques as $field) {
                        if ($count != 0 && !empty(trim($where))) {
                            $where .= ' OR ';
                            $or = true;
                        } else {
                            $or = false;
                        }
                        if (is_array($field)) {//es una clave compuesta
                            $max_1 = count($field);
                            $count_1 = 0;
                            foreach ($field as $f) {
                                /*
                                 * @SEE & FIXME en el caso de una clave COMPUESTA,ÚNICA
                                 * pero que PUEDE SER NULO ALGUNO DE SUS CAMPOS, coloco
                                 * este campo igual a null (esto no funcionará "como
                                 * se espera" si se han definido valores por defecto
                                 * al crear la tabla)
                                 * OPCION: saltear la comprobación en estos casos
                                 */
                                if (array_key_exists($f, $values) && !empty($values[$f])) {
                                    $where .= (($count_1++ != 0 && !$or) ? 'AND ' : '') . "{$f} = '{$values[$f]}' ";
                                } else {
                                    $where .= (($count_1++ != 0 && !$or) ? 'AND ' : '') . "({$f} = NULL OR {$f} = '' OR {$f} = 0 OR {$f} = false OR {$f} = '0000-00-00 00:00:00')";
                                }
                                $or = $or ? false : $or; //reseteo la "bandera"
                            }
                        } else {
                            if (array_key_exists($field, $values)) {
                                $where .= "{$field} = '{$values[$field]}' ";
                            } else {
                                $where .= empty($registry) ? '' : (($count != 0 && !$or) ? 'AND ' : '') . "{$field} = '{$registry[$field]}' ";
                            }
                            $or = $or ? false : $or; //reseteo la "bandera"
                        }
                        $where = trim($where);
                        if (!empty($where)) {//si no es la primera condición
                            $length = strlen($where);
                            $is_end_mpty = substr($where, $length - 2);//para comprobar si $where termina en 'OR'
                            if($is_end_mpty == 'OR'){
                                $where = substr($where, 0, $length - 2);
                            }else{
                                $where .= ' AND baja = false ';
                            }
                        }
                        $count++;
                    }
                    break;
                case self::$CHECK_UPDATE:
                    $or = false; //bandera para detectar cuando puse un OR
                    foreach ($this->uniques as $field) {
                        if ($count != 0 && !empty(trim($where))) {
                            $where .= ' OR ';
                            $or = true;
                        } else {
                            $or = false;
                        }
                        if (is_array($field)) {//es una clave compuesta
                            $max_1 = count($field);
                            $count_1 = 0;
                            foreach ($field as $f) {
                                if (array_key_exists($f, $values)) {
                                    /*
                                     * si se encuentra dentro de los valores recibidos,
                                     * lo comparo com ese
                                     */
                                    $where .= (($count_1++ != 0 && !$or) ? 'AND ' : '') . "{$f} = '{$values[$f]}' ";
                                } else {
                                    /*
                                     * si no lo comparo con los valores que ya tenía
                                     * (para evitar por ej., dar de alta un registro
                                     * cuyos campos únicos coincidan con un registro
                                     * que se encuentra habilitado)
                                     */
                                    $where .= empty($registry) ? '' : (($count_1++ != 0) ? 'AND ' : '') . "{$f} = '{$registry[$f]}' ";
                                }
                                $or = $or ? false : $or; //reseteo la "bandera"
                            }
                        } else {
                            if (array_key_exists($field, $values)) {
                                $where .= "{$field} = '{$values[$field]}' ";
                            } else {
                                $where .= empty($registry) ? '' : (($count != 0 && !$or) ? 'AND ' : '') . "{$field} = '{$registry[$field]}' ";
                            }
                            $or = $or ? false : $or; //reseteo la "bandera"
                        }
                        if (!empty(trim($where))) {//si no es la primera condición
                            $where .= " AND baja = false AND {$this->pk_column} <> {$id}";
                        }
                        $count++;
                    }
                    break;
                case self::$CHECK_ENABLE:
                    $or = false; //bandera para detectar cuando puse un OR
                    foreach ($this->uniques as $field) {
                        if ($count != 0 && !empty(trim($where))) {
                            $where .= ' OR ';
                            $or = true;
                        } else {
                            $or = false;
                        }
                        if (is_array($field)) {
                            $max_1 = count($field);
                            $count_1 = 0;
                            foreach ($field as $f) {
                                if (array_key_exists($f, $values)) {
                                    $where .= "{$field} = '{$registry[$f]}' " . (($count_1++ != 0 && !$or) ? 'AND ' : '') . "{$f} = '{$values[$f]}' ";
                                } else {
                                    $where .= empty($registry) ? '' : (($count_1++ != 0 && !$or) ? 'AND ' : '') . "{$f} = '{$registry[$f]}' ";
                                }
                                $or = $or ? false : $or; //reseteo la "bandera"
                            }
                        } else {
                            if (array_key_exists($field, $values)) {
                                $where .= "{$field} = '{$values[$field]}' ";
                            } else {
                                $where .= empty($registry) ? '' : (($count != 0 && !$or) ? 'AND ' : '') . "{$field} = '{$registry[$field]}' ";
                            }
                            $or = $or ? false : $or; //reseteo la "bandera"
                        }
                        if (!empty(trim($where))) {//si no es la primera condición
                            $where .= " AND baja = false AND {$this->pk_column} <> {$id}";
                        }
                        $count++;
                    }
                    break;
                default:
                    throw new \Exception('EL TIPO DE OPERACION ES INVALIDO: ' . $type_operation);
                    break;
            }

            $query .= empty($where) ? '' : "WHERE {$where}";
            //armo el mensaje de error(excepcion)
            if (!empty(self::$conexion->query($query))) {
                $throw_excep = true;
                $output .= 'Los campos: ' . PHP_EOL;
                $count = 1;
                $max = count($this->uniques);
                foreach ($this->uniques as $field) {
                    if (is_array($field)) {
                        foreach ($field as &$value) {
                            if ($value === 'id')
                                unset($field[$value]);
                            $value = ucfirst(str_replace('_', ' ', str_replace('_id', '', $value)));
                        }
                        $output .= '.';
                        $output .= implode(', ', $field) . PHP_EOL;
                    } else {
                        if ($field === 'id')
                            continue;
                        $output .= '.';
                        $output .= $field . PHP_EOL;
                    }
                }
                $output .= ' deben ser únicos.' . PHP_EOL;
            }
        }
        if (!empty($req_missing)) {
            $count = 0;
            $max = count($req_missing);
            $output .= 'Los campos: ';
            foreach ($this->required as $field) {
                if ($field === 'id')
                    continue;
                $field = ucfirst(str_replace('_', ' ', str_replace('_id', '', $field)));
                $output .= "'$field'" . (( ++$count < $max) ? ', ' : ' ');
            }
            $output .= ' son requeridos.' . PHP_EOL;
        }
        if ($throw_excep) {
            throw new Model_Exception($output);
        }
    }

    /**
     * Devuelve true si existe un registro (que no está dado de baja), false en
     * caso contrario.<br>
     * <b>NOTA:</b> Este método no sanea los parámetros recibidos
     * 
     * @param array $condicion Array asociativo en el cual cada para CLAVE => VALOR
     * son condiciones del filtro where. Ej: array('ID' => 1)
     * @return bool
     */
    public function exists(array $cond = array()): bool {
        if (empty($cond)) {
            throw new \Exception('ERROR: No se han proporcionado parámetros al método "exists" del archivo Simple_Model.' . PHP_EOL);
        } else {
            $sql = "SELECT id FROM {$this->table} WHERE baja = 0 AND ";
            $max = count($cond);
            $count = 0;
            foreach ($cond as $column => $value) {
                $sql .= " {$column} = '{$value}' " . (( ++$count < $max) ? 'AND' : '');
            }
            return !empty(self::$conexion->query($sql));
        }
    }

    /**
     * Comprueba que exista el usuario (ID) y que éste no esté dado de baja.
     * 
     * @param int $user ID de usuario a comprobar
     * @return bool 
     */
    public function check_user(int $user): bool {
        $ret = Sanitizer::validate_number($user, Sanitizer::INTEGER);
        $result = self::$conexion->query('SELECT id FROM usuario WHERE baja = 0 AND id = ' . $user);
        if (!empty($result) && $ret == true) {
            $ret = true;
        } else {
            $ret = false;
        }
        return $ret;
    }

    /**
     * Retorna el timeStamp con el formato Y-m-d H:i:s
     * 
     * @parama void
     * @return String TimeStamp
     */
    public function get_timestamp(): String {
        return date('Y-m-d H:i:s', time());
    }

    /*     * * Getter's & Setter's ** */

    public function get_columns(): array {
        return $this->columns;
    }

    public function get_uniques(): array {
        return $this->uniques;
    }

    public function get_required(): array {
        return $this->required;
    }

    public function get_min_string_size(): int {
        return (int) $this->min_string_size;
    }

    public function get_max_string_size(): int {
        return (int) $this->max_string_size;
    }

    public function get_table_name() {
        return $this->table;
    }

    public function get_pk_column_name() {
        return $this->pk_column;
    }

}

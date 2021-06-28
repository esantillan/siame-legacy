<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
declare(strict_types = 1);

namespace model\persistence\acceso;

use config\Request as Request;
use config\Router as Router;
use model\persistence\{
    Model_Class as Model_Class,
    Conexion as Conexion
};
use model\persistence\acceso\{
    Rol_Permiso_Tabla_Model as Rol_Permiso_Tabla_Model,
    Permiso_Model as Permiso_Model,
    Tabla_Model as Tabla_Model,
    Rol_Model as Rol_Model
};
use helper\{
    Sanitizer as Sanitizer,
    Session_Manager as Session_Manager,
    Logger as Logger
};

/**
 * Description of Access_Model
 *
 * @author Esteban
 */
abstract class Access_Model implements Model_Class {

    //tipo de permiso
    const READ = array(1, 2, 3, 4, 5);
    const CREATE = array(1, 3);
    const UPDATE = array(1, 2, 4);
    const DELETE = array(1);

    private static $session_manager;
    protected static $conexion;
    protected $table;

    /**
     * @SEE Array asociativo que contendrá los SINGLETON's de los modelos
     */
    protected static $instances = array();

    protected function __construct($table) {
        self::$conexion = Conexion::get_instance();
        $this->table = $table;
    }

    public static function get_instance(): Model_Class {
        $class = get_called_class(); //obtengo el nombre de la subclase desde la cual fue invocado
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class();
        }
        return self::$instances[$class];
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
    public function create(int $operador, array $values = array()) {
        $request = Request::get_instance('error','error_403',['No puede CREAR registros pertenecientes al módulo de acceso']);
        Router::call_controller($request);
    }

    public function delete(int $id, int $operador) {
        $request = Request::get_instance('error','error_403',['No puede BORRAR registros pertenecientes al módulo de acceso']);
        Router::call_controller($request);
    }

    public function enable(int $id, int $operador) {
        $request = Request::get_instance('error','error_403','No puede HABILITAR registros pertenecientes al módulo de acceso');
        Router::call_controller($request);
    }

    public function update(int $id, int $operador, array $values = array()) {
        $request = Request::get_instance('error','error_403',['No puede ACTUALIZAR registros pertenecientes al módulo de acceso']);
        Router::call_controller($request);
    }

    /**
     * Devuelve todos los registros de la tabla que cumplan con la condicion pasada
     * como parámetro. Si no se le pasa ninguna, devuelve todos los registros.<br>
     * <b>NOTA:</b> Si se quiere aplicar alguna función SQL a los campos a proyectar,
     * se puede pasar algo así <code>array('CONCAT(tp.nombre,tp.id) AS nombre')</code>  
     * tener en cuenta que el alias <b>tp</b> significa "tabla principal" (esto
     * es porque un modelo puede interactuar con otros modelos).<br>
     * Este método difiere del de Simple_DAO ya que las tablas de "acceso" no
     * cuentan con el campo "baja", así que quité $include_removed (aunque debo
     * dejarlo en la firma del método para implementar la interface)
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
        $sql = 'SELECT ';
        if (empty($fields)) {//si no he recibido los campos por los cuales proyectar, traigo todos
            $max = count($this->columns);
            $count = 0;
            foreach ($this->columns as $col_name => $type) {
                $sql .= "tp.{$col_name} " . (( ++$count < $max) ? ', ' : ' ');
            }
        } else {//proyecto sobre los campos pasados como parámetro
            $max = count($fields);
            $count = 0;
            foreach ($fields as $colum) {
                $sql .= '' . $colum . (( ++$count < $max) ? ', ' : ' ');
            }
        }
        $sql .= 'FROM ' . $this->table . ' AS tp WHERE ';
        if (empty($cond)) {
            $sql .= '1 = 1';
        } else {
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
        $ret = self::$conexion->query($sql);
        return $ret;
    }

    /**
     * Este método difiere del de Simple_DAO ya que las tablas de "acceso" no
     * cuentan con el campo "baja", así que quité $include_removed (aunque debo
     * dejarlo en la firma del método para implementar la interface)
     * 
     * @param int $id ID del registro a leer
     */
    public function read(int $id, bool $include_removed = false) {
        $sql = "SELECT * FROM {$this->table} WHERE id = {$id} ";
        $ret = self::$conexion->query($sql);
        return array_shift($ret);
    }

    /**
     * Recibe el nombre de la tabla y una constante definida en esta clase, y 
     * en caso de no tener permisos para la acción solicitada, redirecciona a
     * 403
     */
    public static function check_perms(String $table_name, array $perms) {
        if (!isset(self::$session_manager) || empty(self::$session_manager)) {
            self::$session_manager = Session_Manager::get_instance();
        }
        $grant_access = false;

        $rol_permiso_tabla_model = Rol_Permiso_Tabla_Model::get_instance();
        $tabla_model = Tabla_Model::get_instance();

        $table_id = $tabla_model->get_list(array('id'), array('nombre = ' => $table_name), true);
        $table_id = array_shift($table_id)['id'];
        $rol_id = self::$session_manager->get_rol_id();
        $cond = array(
            'rol_id = ' => $rol_id,
            'tabla_id = ' => $table_id
        );
        $result = $rol_permiso_tabla_model->get_list(array(), $cond, true);
        $permiso = array_shift($result)['permiso_id'];
        if (!isset($permiso) || empty($permiso)) {
            $request = Request::get_instance('error','error_500',['Error al intentar leer los permisos en la tabla']);
            Router::call_controller($request);
        }
        switch ($perms) {
            case self::CREATE:
                in_array($permiso, self::CREATE) ? $grant_access = true : $grant_access = false;
                break;
            case self::READ:
                in_array($permiso, self::READ) ? $grant_access = true : $grant_access = false;
                break;
            case self::UPDATE:
                in_array($permiso, self::UPDATE) ? $grant_access = true : $grant_access = false;
                break;
            case self::DELETE:
                in_array($permiso, self::DELETE) ? $grant_access = true : $grant_access = false;
                break;
            default:
                $request = Request::get_instance('error','error_500',['Se ha intentado realizar una acción no declarada para la tabla' . $table_name]);
                Router::call_controller($request);
        }
        if (!$grant_access) {
            $request = Request::get_instance('error','error_403',['No tiene permiso para acceder a la tabla ' . $table_name]);
            Router::call_controller($request);
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
            throw new \Exception('ERROR: No se han proporcionado parámetros al método "exists" del archivo General_DAO.' . PHP_EOL);
        } else {
            $sql = "SELECT id FROM {$this->table} WHERE ";
            $max = count($cond);
            $count = 0;
            foreach ($cond as $column => $value) {
                $sql .= " {$column} = '{$value}' " . (( ++$count < $max) ? 'AND' : '');
            }
            return !empty(self::$conexion->query($sql));
        }
    }

    /**
     * Comprueba si existe el usuario y, en caso de que SÍ exista registra el ID
     * y ROL en la SESION y devuelve <code>TRUE</code>, caso contrario, devuelve 
     * <code>FALSE</code>
     * 
     * @param String $user Username
     * @param String $pass Contraseña
     * @return void
     */
    public static function log_in(String $user = '', String $pass = ''): bool {
        self::$conexion = Conexion::get_instance();
        $ret = false;
        if ($user != '' || $pass != '') {
            $usuario = self::$conexion->query("SELECT id,rol_id FROM usuario WHERE user = '{$user}' AND pass = '{$pass}' AND baja = false");
            $usuario = array_shift($usuario);
            if (isset($usuario) && !empty($usuario)) {
                $sm = Session_Manager::get_instance();
                $sm->set_id($usuario['id']);
                $sm->set_rol_id($usuario['rol_id']);
                $ret = true;
            }
        }
        return $ret;
    }

    public function get_table_name() {
        return $this->table;
    }

}

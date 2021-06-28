<?php

namespace controller;

use config\{
    Constants as Constants,
    Security_Layer as Security_Layer,
    Request as Request,
    Router as Router
};
use helper\{
    Session_Manager as Session_Manager,
    Sanitizer as Sanitizer,
    Logger as Logger
};
use model\persistence\Model_Class as Model_Class;

/**
 * Clase abstracta que proporciona la funcionalidad común a todos los controladores
 *
 * @author Esteban
 */
abstract class General_Controller {

    /**
     * path de la vista correspondiente según controlador(para utilizar en las subclases)
     * @var String
     */
    protected $view;

    /**
     * Modelo principal
     * @var Simple_Model|Compose_Model 
     */
    protected $model;

    /**
     * Array que contendrá los Modelos auxiliares con los que interactuará. 
     * Tendrá la forma de 'nombre_model' => 'intancia'
     * @var Simple_Model|Compose_Model 
     */
    protected $aux_models;

    /**
     * Array que contendrá los Controladores auxiliares con los que interactuará. 
     * Tendrá la forma de 'nombre_model' => 'intancia'
     * @var General_Model 
     */
    protected $aux_controllers;

    /**
     *  ID del usuario que realiza la acción
     * @var int
     */
    protected $user_id;

    protected function __construct($tags_permitidos_por_input = array()) {
        $security = new Security_Layer($tags_permitidos_por_input);
        $security->clean_post_data();
        $this->user_id = Session_Manager::get_instance()->get_id();
    }

    /**
     * Método que es invocado cuando no se proporciona en la URL el método, es decir,
     * es el método por defecto. Encargado de renderizar la página.
     * 
     * @param void
     * @return void
     */
    public abstract function index();

    /**
     * Método que para ser llamado por <b>AJAX</b>, invoca al método <code>create() 
     * </code> del Model correspondiente, pasándole los parámetros recibidos por 
     * <code>$_POST</code>
     * @param void En realidad, recibe los parámetros por POST, de la forma de 
     * array asociativo 'nombre_campo' => 'valor'
     * @return void En realidad, hace un <code>echo</code> (lo cual sería un 
     * "return por <code>AJAX</code>") de un arreglo asociativo que tiene dos 
     * elementos:<br>
     * 'state' => <code>true|false</code>
     * 'msg' => <code>Mensaje</code> codificados en JSON
     */
    public function create(array $params = array()) {
        $ret = ['msg' => '', 'state' => FALSE];
        $id = 0;
        $array_to_iterate = array();
        if (empty($params)) {
            $array_to_iterate = $_POST;
        } else {
            $array_to_iterate = $params;
        }
        $required_missing = $this->check_required($array_to_iterate);
        if ($required_missing) {
            $ret['msg'] = nl2br($required_missing);
        } else {
            $cols = array();
            foreach ($this->model->get_columns() as $col => $val) {
                if (isset($array_to_iterate[$col])) {
                    $cols[$col] = (String) $array_to_iterate[$col];
                }
            }
            try {
                $id = $this->model->create($this->user_id, $cols);
//                $id = $this->model->create($this->user_id, $_POST);
                $ret['msg'] = 'Se ha creado correctamente el registro.';
                $ret['state'] = TRUE;
            } catch (\Exception $ex) {
                $ret['msg'] = nl2br($ex->getMessage());
            }
        }
        if (empty($params)) {
            echo json_encode($ret);
        } else {
            $ret['id'] = $id;
            return $ret;
        }
    }

    /**
     * Método que para ser llamado por <b>AJAX</b>, invoca al método <code>edit() 
     * </code> del modelo correspondiente, pasándole los parámetros recibidos por 
     * <code>$_POST</code>
     * @param void En realidad, recibe los parámetros por POST, de la forma
     * 'nombre_campo' => 'valor'
     * @return void En realidad, hace un <code>echo</code> (lo cual sería un 
     * "return por <code>AJAX</code>") de un arreglo asociativo que tiene dos 
     * elementos:<br>
     * 'state' => <code>true|false</code>
     * 'msg' => <code>Mensaje</code>
     */
    public function edit(array $params = array()) {
        $ret = ['msg' => '', 'state' => FALSE];
        $array_to_iterate = array();
        if (empty($params)) {
            $array_to_iterate = $_POST;
        } else {
            $array_to_iterate = $params;
        }
        $required_missing = $this->check_required($array_to_iterate);
        if ($required_missing) {
            $ret['msg'] = nl2br($required_missing);
        } else {
            $cols = array();
            foreach ($this->model->get_columns() as $col => $val) {
                if (isset($array_to_iterate[$col])) {
                    $cols[$col] = (String) $array_to_iterate[$col];
                }
            }
            try {
                $this->model->update($array_to_iterate['id'], $this->user_id, $cols);
                $ret['msg'] = 'Se ha modificado correctamente el registro.';
                $ret['state'] = TRUE;
            } catch (\Exception $ex) {
                $ret['msg'] = nl2br($ex->getMessage());
            }
        }
        if (empty($params)) {
            echo json_encode($ret);
        } else {
            return $ret;
        }
    }

    /**
     * Comprueba los campos requeridos
     * 
     * @param array parámetros a comprobar. Array asociativo del tipo 'nombre_propiedad' => 'valor'
     * @return string Devuelve una cadena vacía si "no hay ningún problema" o 
     * el mensaje de error en caso de no superar todas las pruebas
     */
    protected function check_required(array $params) {
        $ret = '';
        $missing = array();
        foreach ($this->model->get_required() as $property) {
            if (!isset($params[$property]))
                $missing[] = $property;
        }
        if (!empty($missing)) {
            $ret = 'No se ha proporcionado los siguientes valores (requeridos):' . PHP_EOL;
            foreach ($missing as $value) {
                $value = ucfirst(str_replace('_', ' ', str_replace('_id', '', $value)));
                $ret .= '.' . $value . PHP_EOL;
            }
        }
        return $ret;
    }

    /**
     * Método que para ser llamado por <b>AJAX</b>, invoca al método <code>delete() 
     * </code> del modelo correspondiente, pasándole los parámetros recibidos por 
     * <code>$_POST</code>
     * @param void En realidad, recibe los parámetros por POST, de la forma
     * 'nombre_campo' => 'valor'
     * @return void En realidad, hace un <code>echo</code> (lo cual sería un 
     * "return por <code>AJAX</code>") de un arreglo asociativo que tiene dos 
     * elementos:<br>
     * 'state' => <code>true|false</code>
     * 'msg' => <code>Mensaje</code>
     */
    public function delete() {
        $id = isset($_POST['id']) ? (Sanitizer::validate_number($_POST['id'], Sanitizer::INTEGER) ? (int) $_POST['id'] : false) : false;
        if ($id) {
            $sm = Session_Manager::get_instance();
            $ret = array();
            try {
                $this->model->delete($id, $sm->get_id());
                $ret['state'] = TRUE;
                $ret['msg'] = 'Se ha dado de baja correctamente.';
            } catch (\Exception $ex) {
                $ret = false;
                $ret['state'] = FALSE;
                $ret['msg'] = nl2br($ex->getMessage() . PHP_EOL);
            }
        } else {
            $ret['state'] = FALSE;
            $ret['msg'] = 'No se ha proporcionado el identificador del registro.';
        }
        echo json_encode($ret);
    }

    /**
     * Método que para ser llamado por <b>AJAX</b>, invoca al método <code>enable() 
     * </code>del model correspondiente, pasándole los parámetros recibidos por 
     * <code>$_POST</code>
     * @param void En realidad, recibe los parámetros por POST, de la forma
     * 'nombre_campo' => 'valor'
     * @return void En realidad, hace un <code>echo</code> (lo cual sería un 
     * "return por <code>AJAX</code>") de un arreglo asociativo que tiene dos 
     * elementos:<br>
     * 'state' => <code>true|false</code>
     * 'msg' => <code>Mensaje</code>
     */
    public function enable() {
        $id = $_POST['id'];
        $sm = Session_Manager::get_instance();
        $ret = array();
        try {
            $this->model->enable((int) $id, $sm->get_id());
            $ret['state'] = TRUE;
            $ret['msg'] = 'Se ha habilitado nuevamente el registro.';
        } catch (\Exception $ex) {
            $ret = false;
            $ret['state'] = FALSE;
            $ret['msg'] = nl2br($ex->getMessage());
        }
        echo json_encode($ret);
    }

    /**
     * Comprueba si el Usuario ha iniciado sesión.<br>
     * <b>NOTA:</b> Debe ser invocado en el constructor de los controladores (a
     * no ser que sea Login o algún otro que no requiera de esta validación
     * 
     * @param Array roles permitidos 
     * @return void No retorna nada, pero si el usuario no ha iniciado sesión,
     * <b>lo redirecciona a la página de login</b>
     */
    protected function check_session(array $roles = null) {
        $sm = Session_Manager::get_instance();
        if (empty($roles)) {
            if ($sm->get_id() == 0 || $sm->get_rol_id() == Session_Manager::USUARIO_SIN_AUTENTICAR) {
                $request = Request::get_instance('error','error_403',['No tiene permiso para acceder a esta sección']);
                Router::call_controller($request);
            }
        } else {
            if (!in_array($sm->get_rol_id(), $roles)) {
                $request = Request::get_instance('error','error_403',['No tiene permiso para acceder a esta sección']);
                Router::call_controller($request);
            }
        }
    }

    /**
     * Recibe una fecha y opcionalmente, formato de origen y destino. Devuelve
     * la fecha como un String con el formato Y-m-d
     * @param String $date fecha
     * @param String $src_format [opcional] formato origen. Por defecto es d/m/Y
     * @param String $dst_format [opcional] formato destino. Por defecto es Y-m-d
     * @return String fecha formateada
     */
    public function get_date_sql(String $date, String $src_format = 'd/m/Y', String $dst_format = 'Y-m-d'): String {
        $fecha = \DateTime::createFromFormat($src_format, $date);
        if ($fecha) {
            $ret = $fecha->format($dst_format);
        } else {
            throw new \Exception("El formato de la fecha recibida no coincide con el esperado: {$src_format}.");
        }
        return $ret;
    }

}

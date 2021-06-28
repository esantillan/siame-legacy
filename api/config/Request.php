<?php

declare(strict_types = 1);

namespace config;

use helper\Sanitizer;

/**
 * Esta clase es la encargada de servir la url al Router, saneándola primero y luego
 * extrayendo el controlador, método y argumentos.
 *
 * @package config
 * @author Esteban
 */
class Request {

    const DEFAULT_ARGS = array();
    const DEFAULT_CONTROLLER = 'Inicio'; //@PENDING FIXME
    const DEFAULT_METHOD = 'index';
    const CONTROLLERS_LIST = array('test', 'error', 'login', 'inicio', 'usuario', 'provincia', 'departamento', 'localidad', 'plan_estudio'); //lista de controladores

    private $controller;
    private $method;
    private $args; //array
    private static $instance = null;

    private function __construct($controller, $method, $args) {
        $this->controller = self::DEFAULT_CONTROLLER;
        $this->method = self::DEFAULT_METHOD;
        $this->args = self::DEFAULT_ARGS;
        $this->init();
    }

    public static function get_instance($controller = self::DEFAULT_CONTROLLER, $method = self::DEFAULT_METHOD, $args = self::DEFAULT_ARGS) {
        $controller = explode('_', $controller);
        $controller = array_map('ucfirst', $controller);
        $controller = implode('_', $controller);
        if (!isset(self::$instance)) {
            self::$instance = new Request($controller, $method, $args);
        } else {
            self::$instance->controller = $controller;
            self::$instance->method = $method;
            self::$instance->args = $args;
        }
        return self::$instance;
    }

    /**
     * Método responsable de analizar la <code>URL</code> y extraer de ella 
     * el <code>controlador, método y argumentos</code>.
     * 
     * @param void
     * @return void 
     */
    private function init() {
        if (isset($_GET['url']) && !empty($_GET['url'])) {//esta variable la obtengo gracias al archivo .htaccess
            $url = $_GET['url'];
            $url = str_replace('api/', '', $url); //en el caso de que en la url esté 'api/', lo quite @FIXME
            $url = explode('/', $url);
            array_walk($url, 'helper\Sanitizer::sanitize_string');

            if (in_array(strtolower($url[0]), self::CONTROLLERS_LIST)) {
                $this->controller = explode('_', array_shift($url));
                $this->controller = array_map('ucfirst', $this->controller);
                $this->controller = implode('_', $this->controller);
                if (isset($url[0]) && $this->method_exists($url[0])) {
                    $this->method = strtolower(array_shift($url));
                    if (isset($url) && !empty($url)) {
                        $this->args = $url;
                    }
                } elseif (!empty($url[0])) {//si me han pasado cualquier verdura
                    $this->redirect();
                }
            } else {
                $this->redirect();
            }
        }
    }

    /**
     * Verifica si el String pasado como argumento es un método en el controlador
     * actual.
     * 
     * @param String $method_name Nombre del controlador
     * @return boolean Retorna TRUE si existe, FALSE en caso contrario
     */
    private function method_exists(String $method_name = ''): bool {
        $ret = false;

        if (!empty($method_name) && is_readable($this->get_controller(true))) {
            $class = $this->get_controller(false);
            require_once $this->get_controller(true);

            if (in_array($method_name, get_class_methods($class))) {
                $ret = true;
            }
        }

        return $ret;
    }

    /**
     * Esta función es invocada si el CONTROLADOR o MÉTODO NO EXISTEN
     * 
     * @param Array $url URL a la que se quiere acceder
     * @return void
     */
    private function redirect() {
        $this->controller = 'error';
        $this->method = 'error_404';
        $this->args = ['No se ha encontrador el recurso: ', (Constants::$URL . $_GET['url'])];
    }

    /**
     * Devuelve un String que representa al controlador.
     * 
     * @param boolean $require_path Si es true, devuelve el <code>PATH</code> del controlador, <br>
     * sino devuelve el nombre del mismo prefijado por el namespace
     * @return String 
     */
    public function get_controller(bool $require_path = true): String {
        $ret = '';
        if ($require_path) {
            $ret = Constants::$ROOT . 'controller' . Constants::$DS . $this->controller . '_Controller.php';
        } else {
            $ret = 'controller\\' . $this->controller . '_Controller';
        }
        return $ret;
    }

    /**
     * Retorna el nombre del método del controlador acutual
     * 
     * @param void 
     * @return String Nombre del método
     */
    public function get_method(): String {
        return $this->method;
    }

    /**
     * Retorna los argumentos del controlador acutual
     * 
     * @param void
     * @return Array<String> Arreglo de tipo String que contiene los argumentos  
     */
    public function get_args(): array {
        return $this->args;
    }

}

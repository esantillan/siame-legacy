<?php

declare(strict_types = 1);

namespace config;

use helper\Logger as Logger;
use model\persistence\direccion\Localidad_Model as Localidad_Model;
use config\Constants as Constants;
/**
 * Clase encargada de hacer los <b><code>require_once</code></b> e inicializar
 * <b>Constants</b> y <b>Logger</b>
 *
 * @package Config
 * @author Esteban
 */
class Loader {
    public static $PHPMAILER;
    
    /**
     * Registra la función AUTOLOAD e inicializa las 
     * constantes.
     * 
     * @param void
     * @return void
     */
    public static function run() {//No establezco el tipo de retorno como : void porque es una característica de PHP7.1 y en el Hosting tengo php 7.0.6(pero funciona!)
        require 'Constants.php';
        require 'helper/Logger.php';
        require 'Security_Layer.php';
        Constants::init();
        Logger::init();
        self::$PHPMAILER = Constants::$ROOT . 'libs' . Constants::$DS . 'PHPMailer-master' . Constants::$DS . 'PHPMailerAutoload.php';
        /*
         * No consideré incorrecto establecer la función de "cargado" como anónima
         * ya que son dos líneas nomás (y la tarea que desempeña está dentro
         * de las dos que desempeña run())
         */
        spl_autoload_register(function($class) {
            $path = str_replace('\\', '/', $class) . '.php';
            require $path;
        });
        Router::call_controller();
    }
    
    /**
     * Carga una librería. Recibe como parámetro una constante (definida en esta clase)
     * o el PATH absoluto del archivo a cargar.
     */
    public static function load_library($lib) {
        require $lib;
    }
}

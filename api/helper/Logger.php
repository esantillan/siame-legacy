<?php

namespace helper;

use config\Constants as Constants;

/**
 * Registra en un archivo de log, todos los errores,advertencias y excepciones, 
 * también los imprime en el navegador.
 *
 * @package Helper
 * @author Esteban
 */
class Logger {

    private static $file;
    private static $file_name;
    private static $date;
    private static $instance;
    //tipos de errores
    private static $err_types = array(
        E_ERROR => 'Error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parsing Error',
        E_NOTICE => 'Notice',
        E_CORE_ERROR => 'Core Error',
        E_CORE_WARNING => 'Core Warning',
        E_COMPILE_ERROR => 'Compile Error',
        E_COMPILE_WARNING => 'Compile Warning',
        E_USER_ERROR => 'User Error',
        E_USER_WARNING => 'User Warning',
        E_USER_NOTICE => 'User Notice',
        E_STRICT => 'Runtime Notice',
        E_RECOVERABLE_ERROR => 'Catchable Fatal Error'
    );
    private static $err_to_register = E_ALL; //errores a registrar

    private function __construct() {
        self::$instance = $this;
        self::$date = date('d-m-Y H:i:s');
        self::$file_name = Constants::$ROOT . 'log' . Constants::$DS . 'log ' . date('d-m-Y') . '.txt';
        if (!file_exists(self::$file_name)) {//si no existe lo creo y escribo desde el principio
            self::$file = fopen(self::$file_name, 'x+');
            chmod(self::$file_name, 0777); //@TODO en producción, cambiar esta línea
        } else {//si existe, lo abro y escribo desde el final
            self::$file = fopen(self::$file_name, 'a');
        }
    }

    /**
     * Crea la instancia <b>Singleton</b> y registra los manejadores de<br>
     * <b>errores</b><br>
     * <b>excepciones</b>
     */
    public static function init() {
        new Logger();
        error_reporting(self::$err_to_register);
        set_exception_handler('\\helper\\Logger::error_handler');
        set_error_handler('\\helper\\Logger::error_handler', self::$err_to_register);
    }

    /**
     * Función que registra los errores
     * @PENDING & TODO según <code>$err_to_register</code> hacer que registre o no
     * los errores
     * 
     * @param String $err_num Número de error (esto determina si es un WARNING, NOTICE, etc)
     * @param String $msg Mensaje de error
     * @param String $file Archivo donde se produjo el error
     * @param String $line Línea en la que se produjo el error
     * @param String $vars otras variables que php proporciona al momento de lanzar un error
     * @return void No retorna nada, pero imprime en un archivo y en el navegador
     *              los errores.
     */
    public static function error_handler($err_num = '', $msg = '', $file = '', $line = '', $vars = array()) {
        /*
         * Si recibo una excepción en vez de un error, llamo a la función 
         * correspondiente
         */
        if (is_a($err_num, '\Exception') || !is_int($err_num)) {
            self::exception_handler($err_num);
        }
        
        $output = self::$date
                . PHP_EOL . self::$err_types[$err_num]
                . PHP_EOL . "Archivo: '{$file}'"
                . PHP_EOL . "Linea: $line'"
                . PHP_EOL . "Mensaje: '$msg'"
                . PHP_EOL . "IP: '" . $_SERVER['REMOTE_ADDR'] . "'"
                . PHP_EOL . "HTTP_HOST: '" . $_SERVER['HTTP_HOST'] . "'"
                . PHP_EOL . "HTTP_USER_AGENT: '" . $_SERVER['HTTP_USER_AGENT'] . "'"
                . PHP_EOL . "REQUEST_URI: '" . $_SERVER['REQUEST_URI'] . "'"
                . PHP_EOL . "PHP_SELF: '" . $_SERVER['PHP_SELF'] . "'"
                . PHP_EOL . 'REQUEST: ' . print_r($_REQUEST, true)
                . PHP_EOL . 'Ratreo de pila: ' . print_r(debug_backtrace(),true);
        error_log($output, 3, self::$file_name);
        echo $output;
        exit;
    }

    public static function exception_handler(\Throwable $e) {
        $output = self::$date;
        if ($e instanceof \Error) {
            $output .= PHP_EOL . "Error '{$e->getCode()}'";
        } else {
            $output .= PHP_EOL . "Excepcion: '{$e->getCode()}'";
        }
        $output .= PHP_EOL . "Mensaje: '{$e->getMessage()}'"
                . PHP_EOL . 'Archivo: ' . $e->getFile()
                . PHP_EOL . 'Linea: ' . $e->getLine()
                . PHP_EOL . 'Rastreo de pila: ' . PHP_EOL . $e->getTraceAsString();
        self::save_log('Excepción no capturada: ' . $output);
        echo $output;
        exit;
    }

    /**
     * Guarda en el archivo de log el mensaje proporcionado
     * 
     * @param String $msg Mensaje a escribir en el archivo de log
     * 
     * @return void
     */
    public static function save_log($msg) {
        if(is_array($msg)){
            $msg = print_r($msg,true);//@FIXME mejorar XD
        }
        fwrite(self::$file, '=> ');
        fwrite(self::$file, self::$date);
        fwrite(self::$file, PHP_EOL . $msg . PHP_EOL);
    }

    /**
     * Guarda en el archivo de log e imprime en el navegador el mensaje proporcionado
     * 
     * @param String $msg Mensaje a escribir en el archivo de log e imprimir en el navegador
     * @return void
     */
    public function show_msg($msg) {
        self::save_log($msg);
        echo $msg;
    }

    /**
     * Cierro el archivo de log (esta es la razón por la cual esta clase es un
     * Singleton, en vez de ser un helper puramente estático)
     */
    public function __destruct() {
        fclose(self::$file);
    }

}

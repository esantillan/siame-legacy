<?php

declare(strict_types=1);
namespace config;

/**
 * Esta clase contiene todas las constantes "globales" que utilizaré a lo largo
 * de este proyecto.
 * No he utilizado CONST porque para establecer ROOT debo igualarlo a realpath(dirname(__FILE__)) . self::DS;
 * por lo que tuve que establecerlo como PUBLIC STATIC y, para que quede 
 * homogéneo, declaré todas las constantes como propiedades públicas y estáticas.
 * 
 * @package Config
 * @author Esteban
 */
class Constants {
    const LOCAL = 1;
    const PRODUCTION = 2;
    /**
     * DS
     * @var String separador de directorios
     */
    public static $DS = DIRECTORY_SEPARATOR;
    /**
     * APP_NAME = siame
     * @var String nombre de la aplicacion 
     */
    public static $APP_NAME = 'siame';
    /**
     * HOST
     * @var String nombre del host
     */
    public static $HOST;
    /**
     * URL = 'http://' . HOST . '/' . APP_NAME . '/'
     * @var String URL base del proyecto 
     */
    public static $URL;
    /**
     * PATH ejemplo: C:\xampp\htdocs\siame\api\
     * @var String path de la aplicacion 
     */
    public static $ROOT; //es el path
    /**
     * Puede tomar el valor de las siguientes constantes: LOCAL o PRODUCTION
     * @var CONTANTE  
     */
    public static $ENVIROMENT = '';

    /**
     * Inicializa la propiedad <b>ROOT</b>, ya que no puedo hacer<br>
     * <code>const ROOT = realpath(dirname(__FILE__));</code><br>
     * También establece la <b>zona horaria</b>.
     */
    public static function init() {
        self::$ROOT = str_replace('/', self::$DS, $_SERVER['DOCUMENT_ROOT']) . self::$DS . self::$APP_NAME . self::$DS . 'api' . self::$DS;
        self::$HOST = $_SERVER['HTTP_HOST'];
        if(self::$HOST == 'localhost' || self::$HOST == '127.0.0.1' || self::$HOST == '192.168.43.151'){
            self::$URL = 'http://' . self::$HOST . '/' . self::$APP_NAME . '/';
            self::$ENVIROMENT = self::LOCAL;
        }else{
            self::$URL = 'http://' . self::$HOST . '/';
            self::$ENVIROMENT = self::PRODUCTION;
        }
//        $_SERVER['SERVER_SIGNATURE'] = 'S.I.A.M.E versión Alfa';
//        $_SERVER['SERVER_ADMIN'] = 'siame.belgrano@gmail.com';
        date_default_timezone_set('America/Argentina/Mendoza');
    }

}
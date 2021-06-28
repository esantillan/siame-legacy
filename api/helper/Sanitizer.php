<?php

namespace helper;

use config\Constants as Constants;

/**
 * Clase que contiene los métodos para <b>SANEAR</b> y <b>VALIDAR</b> (el nombre
 * puede que no sea 100% descriptivo, pero fue lo mejor que se me ocurrió)
 * @SEE Los métodos "sanitize_*" están deprecados
 * @package Helper
 * @author Esteban
 */
class Sanitizer {

    const INTEGER = 1; //constante que se utilizará para representar números enteros
    const FLOAT = 2;

    /**
     * Codifica caracteres especiales y etiquetas html 
     * @SEE (creo que las etiquetas las "escapa")
     * 
     * @param String $text Texto a sanear
     * @return void no retorna nada, pero modifica la referencia pasada como parámetro
     */
    public static function sanitize_string(&$text) {
        trim($text);
        $text = filter_var($text, FILTER_SANITIZE_STRING, array(FILTER_FLAG_ENCODE_LOW, FILTER_FLAG_ENCODE_HIGH, FILTER_FLAG_ENCODE_AMP));
    }

    /**
     * Sanea la URL.<br>
     * <b>Nota:</b> Recibe una referencia, si intento pasar un literal, obtendré un error (en realidad, una advertencia E_STRICT)
     * 
     * @param String $url <b>URL</b> a comprobar
     * @return String URL <b>saneada</b>
     */
    public static function sanitize_url(&$url) {
        self::sanitize_string($url);
        $url = filter_var($url, FILTER_SANITIZE_URL, FILTER_FLAG_HOST_REQUIRED); //requiere que la URL tenga el nombre del host (http://www.ejeplo.com)
    }

    /**
     * Valida una ruta, si es correcta devuelve TRUE, caso contrario FALSE.<br>
     * <b>Nota:</b><br>
     * 1- Devuelve TRUE en caso de que sea una <code>ruta de archivo válida</code>, no un <code>directorio</code><br>
     * 2- Recibe una referencia, si intento pasar un literal, obtendré un error (en realidad, una advertencia E_STRICT)
     * 
     * @param String $path Ruta a comprobar
     * @return boolean 
     */
    public static function validate_path($path) {
        $DS = & Constants::$DS; //para acortar sintaxis
        $ret = false;
        //si es un entorno Windows
        if ($DS === '\\')
            $DS = '\\\\';

        //compruebo que la ruta sea del tipo: "D:\algo[\otra_cosa]"
        $pattern = "~^([a-zA-Z]+:({$DS}[a-zA-Z0-9_\-][ ]?[a-zA-Z0-9_\-]*))({$DS}[a-zA-Z0-9_\-][ ]?)*~";
        $pattern = preg_match($pattern, $path);

        //como tenía problemas si armaba para armar una regexp en un "String grande", separé la parte de "la extensión del archivo"
        if ($pattern) {
            $pattern = '~\.[a-z0-9]{2,6}~';
            $pattern = preg_match($pattern, $path);
            if ($pattern)
                $ret = true;
        }
        return $ret;
    }
    
    /**
     * Valida si una fecha tiene el formato pasado como parámetro.
     * @param String $date fecha
     * @param String $format [opcional] formato de la fecha. Por defecto es 'Y-m-d' (MySQL)
     * @return boolean
     */
    public static function validate_date($date, $format = 'Y-m-d') {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     * Convierte el <b>path</b> proporcionado a una <b>url</b>
     * <b>Nota:</b> Recibe una referencia, si intento pasar un literal, obtendré un error (en realidad, una advertencia E_STRICT)
     * @param String $path Path a convertir a url
     * @return void no retorna nada, pero modifica la referencia pasada como parámetro
     */
    public static function path_to_url(&$path) {
        $path = str_replace(Constants::$ROOT, Constants::$URL, $path);
        $path = html_entity_decode(str_replace('\\', '/', $path));
    }

    /**
     * Quita los acentos de una cadena y la retorna
     * <b>Nota:</b> Recibe una referencia, si intento pasar un literal, obtendré un error (en realidad, una advertencia E_STRICT)
     *  
     * @param String $string Candena
     * @return void no retorna nada, pero modifica la referencia pasada como parámetro
     */
    public static function remove_accents(&$string) {
        $string = html_entity_decode($string);
        $replace = array('á' => 'a', 'Á' => 'A', 'é' => 'e', 'É' => 'E', 'í' => 'i', 'Í' => 'I', 'ó' => 'o', 'Ó' => 'O', 'ú' => 'u', 'Ú' => 'U');
        foreach ($replace as $vocal_acentuada => $vocal) {
            $string = str_replace($vocal_acentuada, $vocal, $string);
        }
    }

    /**
     * Recibe un número y una constante que determina si ese número es entero o decimal
     * 
     * @param int|long|float|double $num Número a validar
     * @param int $tipo Constante que indica si es un entero o flotante.<br>Se hallan en esta misma clase
     * @return boolean
     * 
     * @SEE puede que lanze una excepcion o error (ver paso por referencia)
     * @SEE como en versiones < PHP 7 o en plataformas x86 los enteros
     * son de 4 Bytes (+/- 2 Millones aprox.) utilizo expresiones regulares
     */
    public static function validate_number(&$num, $type) {
        $ret = false; //variable a retornar
        $num = trim($num);
        switch ($type) {
            case self::INTEGER:
                $ret = preg_match_all('~\D~', $num);
                if ($ret) {
                    Logger::save_log("SANITIZER::El número {$num} no es del tipo INTEGER");
                    throw new \Exception("ERROR: El número {$num} no es del tipo INTEGER");
                }
                break;
            case self::FLOAT:
                $ret = preg_match('~[^0-9,\.]~', $num);
                if ($ret) {
                    Logger::save_log("SANITIZER::El número {$num} no es del tipo INTEGER");
                    throw new \Exception("ERROR: El número {$num} no es del tipo FLOAT");
                }
                break;
            default:
                throw new \Exception("ERROR: El tipo '{$type}' no corresponde con ninguna constante predefinida para la validación de números.");
                break;
        }
        return !$ret;
    }

    /**
     * Comprueba si el email es válido (si "puede existir")
     * 
     * @param String $email E-mail a validar
     * @return boolean
     */
    public static function validate_email($email) {
        return (filter_var($email, FILTER_VALIDATE_EMAIL) == false ? false : true);
    }

    /**
     * Elimina todos los caracteres excepto letras, dígitos y $-_.+!*'(),{}|\\^~[]`<>#%";/?:@&=.
     * 
     * @param String $path Ruta a sanear
     * @return void no retorna nada, pero modifica la referencia pasada como parámetro
     */
    public static function sanitize_path(&$path) {
        $path = filter_var($path, FILTER_SANITIZE_SPECIAL_CHARS);
        $path = filter_var($path, FILTER_SANITIZE_URL);
    }

}

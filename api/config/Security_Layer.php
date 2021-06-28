<?php // 
/*
 * Esta clase la tomé de europio-engine http://bazaar.launchpad.net/~eugeniabahit/europioexperimental/rel-4.0-alfa/files
 * Pienso modificarla un poco (especialmente lo referido al saneamiento de Strings)
 * que si bien creo que está bien como lo hace, pero para este proyecto, me gusta
 * más la manera en cómo lo manejaba en los métodos del helper "Sanitizer" - por
 * esto es que he incluido sanitize_url() y sanitize_string().
 * Probablemente cambie los métodos que escribió eugenia (para sanear cadenas)
 * por los míos
 */

namespace config;

/**
 * Description of Security_Layer
 *
 * @author Eugeni Bahit
 * @link http://www.eugeniabahit.com/
 * clasesconeugenia@gmail.com
 */
class Security_Layer {

    private $tags_permitidos_por_input;

    public function __construct($tags_permitidos_por_input = array()) {
        $this->tags_permitidos_por_input = $tags_permitidos_por_input;
    }

    public function clean_post_data() {
        $this->sanitize_url($_GET['url']);
        foreach ($_POST as $key => &$value) {
            $array = (is_array($value));
            if ($array) {
                foreach ($array as $key => &$value) {
                    if (is_numeric($mocknum)) {
                        $this->sanitize_number($key);
                    } else {
                        if (array_key_exists($key, $this->tags_permitidos_por_input)) {
                            $this->sanitize_string($value, $this->tags_permitidos_por_input[$key]);
                        } else {
                            $this->sanitize_string($value);
                        }
                    }
                }
            } else {
                if (array_key_exists($key, $this->tags_permitidos_por_input)) {
                    $this->sanitize_string($value, $this->tags_permitidos_por_input[$key]);
                } else {
                    $this->sanitize_string($value);
                }
            }
            if (strpos($key, 'mail') !== False)
                $this->purge_email($key);
            $mocknum = str_replace(',', '', $value);
            if (is_numeric($mocknum))
                $this->sanitize_number($key);
        }
        $_POST = array_filter($_POST);
    }

    /**
     * @deprecated 
     */
    public function remove_and_convert($key = '') {
        $_POST[$key] = htmlentities(strip_tags($_POST[$key], '<ul><ol><li><b><p>'));
    }

    /**
     * @deprecated 
     */
    public function encode_string($key = '') {
        $options = array('flags' => FILTER_FLAG_ENCODE_LOW);
        $_POST[$key] = filter_var($_POST[$key], FILTER_SANITIZE_SPECIAL_CHARS, $options);
    }

    public function purge_email($key = '') {
        $_POST[$key] = filter_var($_POST[$key], FILTER_SANITIZE_EMAIL);
    }

    public function sanitize_number($key = '') {
        $pos_colon = strpos($_POST[$key], ',');
        $pos_dot = strpos($_POST[$key], '.');
        $has_colon = ($pos_colon !== False);
        $has_dot = ($pos_dot !== False);
        $is_longinteger = ($_POST[$key] > PHP_INT_MAX);
        $filterid = FILTER_VALIDATE_FLOAT;

        if ($has_colon && $has_dot) {
            if ($pos_colon > $pos_dot) {
                $this->helpernum('.', '', $key);
                $this->helpernum(',', '.', $key);
            } else {
                $this->helpernum(',', '', $key);
            }
        } elseif ($has_colon xor $has_dot) {
            $this->helpernum(',', '.', $key);
            settype($_POST[$key], 'float');
        } elseif ($is_longinteger) {
            $filterid = FILTER_VALIDATE_FLOAT;
        } else {
            settype($_POST[$key], 'integer');
            $filterid = FILTER_VALIDATE_INT;
        }

        $_POST[$key] = filter_var($_POST[$key], $filterid);
    }

    private function helpernum($search, $replace, $key) {
        $_POST[$key] = str_replace($search, $replace, $_POST[$key]);
    }

    private function sanitize_array($key) {
        foreach ($_POST[$key] as &$value)
            settype($value, 'int');
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
     * Codifica caracteres especiales y etiquetas html 
     * @SEE (creo que las etiquetas las "escapa")
     * 
     * @param String $text Texto a sanear
     * @return void no retorna nada, pero modifica la referencia pasada como parámetro
     */
    public static function sanitize_string(&$str, $allowed_tags = '') {
        trim($str);
        if (empty($allowed_tags)) {
            $str = filter_var($str, FILTER_SANITIZE_STRING, array(FILTER_FLAG_STRIP_LOW, FILTER_FLAG_ENCODE_HIGH, FILTER_FLAG_ENCODE_AMP));
        } else {
            $str = strip_tags($str, $allowed_tags);
        }
        $str = addslashes($str);
        $str = filter_var($str, FILTER_DEFAULT, array(FILTER_FLAG_STRIP_LOW, FILTER_FLAG_ENCODE_HIGH, FILTER_FLAG_ENCODE_AMP));
    }

}

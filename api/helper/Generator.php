<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace helper;

use config\Constants as Constants;

/**
 * Description of Generator
 *
 * @author Esteban
 */
class Generator {

    private static $TAG_CSS = '<link rel="stylesheet" type="text/css" href="#">';
    private static $TAG_SCRIPT = '<script type="application/javascript" src="#"></script>';
    private static $TAG_SCRIPT_WITHOUT_SRC = '<script type="application/javascript">{}</script>';
    private static $TAG_NAVBAR_ITEM = '<a _ID_ href="#" class="w3-bar-item w3-button w3-hover-red user_menu_item w3-ripple"><i class="fa . w3-margin-right" aria-hidden="true"></i>{}</a>';
    private static $TAG_SIDEBAR = '<nav class="w3-sidebar w3-bar-block w3-card-2 w3-safety-blue w3-animate-left s6 m2" style="display:none" id="sidebar"><ul>';
    private static $TAG_SIDEBAR_MODULE = '<div><button class="w3-button w3-block w3-left-align w3-ripple navbar_button sidebar_mod" onclick="accordion(\'_ID_MOD_\')"><i class="fa _ICON_"></i>&nbsp;&nbsp;&nbsp;_MOD_NAME_</button><div id="_ID_MOD_" class="w3-hide">';
    private static $TAG_SIDEBAR_CRUD = '<div class="w3-border-bottom"><button class="w3-button w3-block w3-left-align w3-ripple navbar_button sidebar_crud" onclick="accordion(\'_ID_CRUD_\')"><i class="w3-small fa fa-caret-down"></i>&nbsp;&nbsp;&nbsp;_CRUD_NAME_</button><div id="_ID_CRUD_" class="w3-hide">';
    private static $TAG_SIDEBAR_OPERATION = '<div class="w3-border-bottom w3-border-light-gray"><a href="_LINK_" class="w3-bar-item w3-button sidebar_list_item w3-ripple sidebar_op"><i class="fa fa-caret-right  w3-small" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;_OP_NAME_</a></div>';
    private static $TAG_BREADCRUMB = '<div class="w3-row"><p id="breadcrumbs" class="w3-right w3-border-bottom s12 m12 l12">{}</p></div>';
    private static $BREAD_CRUMB_SEPARATOR = '&nbsp;&nbsp;<i class="fa fa-angle-right" aria-hidden="true"></i>&nbsp;&nbsp;';
    private static $COLUMNS_NAMES = '[_COLUMNS_NAMES_]';
    private static $COLUMN = '{"data": "_COL_NAME_"}';
    private static $COLUMNS_DEF = '[_COLMUNS_DEF_]';

    /**
     * Genera un clave aleatoria de 8 caracteres. Retorna un array asociativo:<br>
     * <code>'password' => 'hash'</code><br>
     * Donde <code>password</code> es la contraseña y <code>hash</code> es el 
     * hash generado por <code>sha1</code>
     */
    public static function generate_password():Array {
        
    }
    /**
     * Recibe un arreglo con los nombres de los archivos css y devuelve un String
     * con los tag's <code><link></code> 
     * 
     * @param array $links_css Arreglo con los enlaces a los archivos css
     * @return String Cadena con tantos tag's <code><link></code> como elementos
     * se hayan proporcionado en el array como parámetro
     */
    public static function &generate_tag_css(array $links_css): String {
        $ret = '';
        foreach ($links_css as $link) {
            $ret .= str_replace('#', $link, self::$TAG_CSS);
            $ret .= PHP_EOL;
        }
        return $ret;
    }

    /**
     * Recibe un arreglo con los nombres de los archivos js y devuelve un String
     * con los tag's <code><script></code> 
     * 
     * @param array $links_css Arreglo con los enlaces a los archivos js
     * @return String Cadena con tantos tag's <code><script></code> como elementos
     * se hayan proporcionado en el array como parámetro
     */
    public static function &generate_tag_script(array $links_script): String {
        $ret = '';
        foreach ($links_script as $link) {
            $ret .= str_replace('#', $link, self::$TAG_SCRIPT);
            $ret .= PHP_EOL;
        }
        return $ret;
    }

    /**
     * Recibe un arreglo anidado, con tantos sub-arreglos como enlaces a generar.<br>
     * El formato es el siguiente:<br><code>
     * array(array('clase de <b>font-awesome</b>','nombre del enlace','url','id'))<br>
     * </code>
     * <b>NOTA:</b>El id es opcional
     * @param array $params Arreglo anidado que contiene el nombre de la clase
     * de <b>font-awesome</b>,<b>Texto visible</b>,<b>url</b>,<b>[opcional] id 
     * que tendrá el elemento</b> (para manipulación con js)
     * @return String Cadena con tantos enlace como sub-arreglos se hayan 
     * proporcionado en el array como parámetro
     */
    public static function &generate_tag_navbar(array $params): String {
        $ret = '';
        foreach ($params as $element) {
            $ret .= str_replace('.', $element[0], self::$TAG_NAVBAR_ITEM);
            $ret = str_replace('{}', $element[1], $ret);
            $ret = str_replace('#', $element[2], $ret);
            $ret = (isset($element[3]) && !empty($element[3])) ? str_replace('_ID_', "id='{$element[3]}'", $ret) : str_replace('_ID_', '', $ret);
            $ret .= PHP_EOL;
        }
        return $ret;
    }

    /**
     * Recibe un arreglo anidado con el siguiente formato:<br><code>
     * array(
     *  array(
     *      'ID_MOD',
     *      'MOD_NAME',
     *      '_ICON_',
     *      array(
     *          array(
     *              'ID_CRUD',
     *              'CRUD_NAME',
     *              array(
     *                  'OP_NAME' => 'LINK' 
     *              )
     *          )
     *      )    
     *  )
     * )
     * </code><br>
     * @param array $params arreglo anidado que contendrá todos los módulos a
     * "cargar" en el sidebar, los que a su vez contendrán los CRUD(con "CRUD" me
     * refiero más bien al nombre de la tabla), la que a su vez contendrá las acciones
     * a realizar
     * @return String Código HTML completo, que contiene al navbar
     */
    public static function &generate_tag_sidebar(array $params): String {
        $ret = '';
        if (!empty($params)) {
            $ret = self::$TAG_SIDEBAR;
            foreach ($params as $module) {
                $ret .= self::$TAG_SIDEBAR_MODULE;
                $ret = str_replace('_ID_MOD_', $module[0] . '_mod', $ret);
                $ret = str_replace('_MOD_NAME_', $module[1], $ret);
                $ret = str_replace('_ICON_', $module[2], $ret);
                foreach ($module[3] as $crud) {
                    $ret .= self::$TAG_SIDEBAR_CRUD;
                    $ret = str_replace('_ID_CRUD_', $crud[0] . '_crud', $ret);
                    $ret = str_replace('_CRUD_NAME_', $crud[1], $ret);
                    foreach ($crud[2] as $op_name => $link) {
                        $ret .= self::$TAG_SIDEBAR_OPERATION;
                        $ret = str_replace('_LINK_', Constants::$URL . $link, $ret);
                        $ret = str_replace('_OP_NAME_', $op_name, $ret);
                    }
                    $ret .= '</div></div>'; //fin de la lista de operaciones
                }
                $ret .= '</div></div>'; //fin de la lista de crud 
            }
            $ret .= '</ul></nav>'; //fin de la lista de modulos
        }
        return $ret;
    }

    /**
     * Establece en el archivo <b><code>general.js</code></b> la URL.
     * @PENDING @FIXME
     * @param String $path Path del archivo
     * @return void
     */
    public static function generate_base_js_file_(String $path) {
        $file = file_get_contents($path);
        $file = str_replace('_URL_', Constants::$URL, $file);
        return $file;
    }
    
    /**
     * "Bread Crumbs" o "Migas de Pan" es un label (por llamarlo de alguna forma)
     * que indica al usuario "donde está parado".<br>
     * Muestra el nombre del <b>módulo actual</b>, <b>controlador</b> y <b>método</b>
     * invocados.
     *
     * @param String $module Nombre del módulo actual
     * @param String $controller Nombre del controlador actual
     * @param String $method Nombre del método actual
     * @return String Código HTML del elemento
     */
    public static function &generate_breadcrumbs(String $module = '',String $controller = '',String $method = '') {
        $ret = '';
        if($module){//si no recibo módulo, reemplazo por '' (sin colocar "las migas de pan"). Esto puede notarse en el caso de Login (por ej)
            $aux = $module;
            if($controller){
                $aux .= self::$BREAD_CRUMB_SEPARATOR . $controller;
                if($method){
                    $aux .= self::$BREAD_CRUMB_SEPARATOR . $method;
                }
            }
            $ret = str_replace('{}', $aux, self::$TAG_BREADCRUMB);
        }
        return $ret;
    }

    /**
     * Genera el código JS necesario para datatable.
     * 
     * @param String $path Ruta del archivo <code>datatable_template.js</code>.<br>
     * <b>NOTA:</b> por cuestiones de diseño es que prefiero no "harcodearlo" aquí
     * (ya que conocer la ubicación de los archivos referentes a las vistas es
     * responsabilidad de las Vistas XD )
     * @param String $table_id ID (<code>HTML</html>) de la tabla
     * @param String $url URL de la cual obtendrá los registros mediante AJAX
     * @param array $columns Arreglo que contiene el nombre de todas las columnas.
     * <b>NOTA:</b> Las claves deben coincidir con las devueltas por la petición 
     * AJAX
     * @param array $column_def Arreglo anidado que contiene la definición de las
     * propiedades de las columnas.<b>NOTA:</b> Dentro de los valores proporcionados
     * debe hallarse el <code><b>"targets"</b></code>.<br>
     * EJ:<br>
     * array(<br>
     *      array(<br>
     *              "targets" => [0],<br>
     *              "searcheable" => false,<br>
     *              "orderable" => false<br>
     *            )
     *      )<br>
     * -La primer columna <b>SIEMPRE</b> es "row_id", así que la definición anterior
     * no es necesaria (es a modo de ejemplo). Tampoco es necesaria proprocionarlo
     * en el arrelgo "columns" (cuarto parámetro)
     * @param int $fixed_columns <b>[opcional]</b> Número de columnas fijas
     * @param String $path_reports <b>[opcional]</b> Path del archivo que contiene la
     * "configuración" para "exportButtons".
     *
     * @return void No devuelve el archivo proporcionado en el primer parámetro
     * con los valores
     */
    public static function &generate_datatable(String $path, String $table_id, String $url, array $columns_names, array $column_def, int $fixed_columns = 0, String $path_reports = '') {
        $file = file_get_contents($path);
        //genero el id
        $file = str_replace('_TABLE_ID_', $table_id, $file);
        //genero la url
        $file = str_replace('_URL_AJAX_', "'{$url}'", $file);
        //defino nombre de columnas
        $count = 0;
        $max = count($columns_names);
        $columns = '';
        foreach ($columns_names as $name) {
            $columns .= str_replace('_COL_NAME_', $name, self::$COLUMN) . (($count++ < $max) ? ', ' : '');
        }
        $file = str_replace('_COLUMNS_', str_replace('_COLUMNS_NAMES_', $columns, self::$COLUMNS_NAMES), $file);
        //definición de "atributos" de las columnas
        /*
          private static $COLUMNS_DEF = '[_COLMUNS_DEF_]';
          private static $ATTR_DEF = '{_ATTR_DEF_}';
         */
        $columns = '';
        $count = 1;
        $max = count($column_def);
        foreach ($column_def as $row) {
            $count1 = 1;
            $max1 = count($row);
            $columns .= '{';
            foreach ($row as $attr_name => $value) {
                $columns .= "'{$attr_name}': {$value}" . (($count1++ < $max1) ? ', ' : '');
            }
            $columns .= '}' . (($count++ < $max) ? ', ' : '');
        }
        $columns = str_replace('_COLMUNS_DEF_', $columns, self::$COLUMNS_DEF);
        $file = str_replace('_COLUMN_DEFS_', $columns, $file);
        //establezco las columnas "fijas"
        if ($fixed_columns != 0) {
            $fixed_columns = '"fixedColumns": {"leftColumns": '.$fixed_columns.'}';
            $file = str_replace('_FIXED_', $fixed_columns, $file);
        } else {
            $file = str_replace('_FIXED_', '', $file);
        }
        //configuro botones de exportación
        if ($path_reports){
            $config = file_get_contents($path_reports);
            $file = str_replace('_EXPORT_BUTTONS_', $config, $file);
        }else{
            $file = str_replace('_EXPORT_BUTTONS_', '', $file);
        }
        $file = str_replace('{}', $file, self::$TAG_SCRIPT_WITHOUT_SRC);
        return $file;
    }

}

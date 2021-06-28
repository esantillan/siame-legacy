<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace view;

use config\Constants as Constants;
use helper\{
    Generator as Generator,
    Session_Manager as Session_Manager
};

/**
 * Description of General_View
 *
 * @author Esteban
 */
abstract class General_View {

    private $file;
    /*
     * Arreglo asociativo en el cual las claves son las palabras a reemplazar en el
     * index.html (cuya característica es que empiezan y terminan con '__'), y los
     * valores son String's que contienen las etiquetas(o valores) generad@s dinámicamente.
     */
    protected $elements_to_generate = array();
    protected $sidebar_menu = array(
        Session_Manager::ADMINISTRADOR => array(//administrador
            array(
                'direccion',
                'Direccion',
                'fa-globe',
                array(
                    array('provincia',
                        'Provincia',
                        array(
                            'ABM' => 'provincia'
                        )
                    ),
                    array('departamento',
                        'Departamento',
                        array(
                            'ABM' => 'departamento'
                        )
                    ),
                    array('localidad',
                        'Localidad',
                        array(
                            'ABM' => 'localidad'
                        )
                    )
                )
            ), array(
                'usuarios',
                'Usuarios',
                'fa-users',
                array(
                    array('usuario_crud',
                        'Usuario',
                        array(
                            'ABM' => 'usuario',
                            'Mi Perfil' => 'usuario/mi_perfil'
                        )
                    )
                )
            ), array(
                'institucional',
                'Institucional',
                'fa-university',
                array(
                    array('plan_estudio',
                        'Plan de Estudio',
                        array(
                            'ABM' => 'plan_estudio',
                            'Cargar Materias' => 'usuario/mi_perfil'
                        )
                    )
                )
            )
        ),
        Session_Manager::BEDEL => array(/* bedel */
            array(
                'direccion',
                'Direccion',
                'fa-globe',
                array(
                    array('provincia',
                        'Provincia',
                        array(
                            'ABM' => 'provincia'
                        )
                    ),
                    array('departamento',
                        'Departamento',
                        array(
                            'ABM' => 'departamento'
                        )
                    ),
                    array('localidad',
                        'Localidad',
                        array(
                            'ABM' => 'localidad'
                        )
                    )
                )
            ), array(
                'usuarios',
                'Usuarios',
                'fa-users',
                array(
                    array('abm',
                        'Usuario',
                        array(
                            'ABM' => 'usuario',
                            'Mi Perfil' => 'usuario/mi_perfil'
                        )
                    )
                )
            )
        ),
        Session_Manager::PROFESOR => array(/* profesor */
            array(
                'cursos',
                'Cursos',
                array(
                    array('calificaciones',
                        'Calificaciones',
                        array(
                            'Calificar Alumnos' => 'calificaciones/calificar',
                        )
                    )
                )
            )
        ),
        Session_Manager::ALUMNO => array(/* alumno */
            array(
                'usuario',
                'Usuario',
                array(
                    array('mi_usurio',
                        'Mi Usurio',
                        array(
                            'Administrar' => 'usuario/administrar',
                        )
                    )
                )
            )
        ),
        Session_Manager::USUARIO_SIN_AUTENTICAR => array(/* usuario sin autenticar */)
    );

    public function __construct() {
        $this->file = file_get_contents("view/public/html/template/index.html");
        $this->init();
    }

    private function init() {
        //@FIXME esto es para poder ver 

        $this->elements_to_generate['_W3CSS_'] = Constants::$URL . "api/view/public/css/w3.css";
        $this->elements_to_generate['_CSS_THEME_'] = Constants::$URL . "api/view/public/css/theme/w3-colors-safety.css";
        $this->elements_to_generate['_GENERAL_STYLE_'] = Constants::$URL . "api/view/public/css/styles/general.css";
        $this->elements_to_generate['_OTHER_CONSTANTS_'] = '';
        $this->elements_to_generate['_GENERAL_SCRIPT_'] = Constants::$URL . "api/view/public/js/general.js";
        $this->elements_to_generate['_FAVICON_'] = Constants::$URL . "api/view/public/img/favicon.ico";
        $this->elements_to_generate['_FONT-AWESOME_'] = Constants::$URL . "api/view/public/css/lib/font-awesome-4.7.0/css/font-awesome.min.css";
        $this->elements_to_generate['_JQUERY_'] = Constants::$URL . "api/view/public/plugins/jquery-3.1.0.min.js";
        $this->elements_to_generate['_ICON_'] = Constants::$URL . "api/view/public/img/logo.png";
        if (Constants::$ENVIROMENT == Constants::LOCAL) {
            $this->elements_to_generate['_CONSTANTS_JS_'] = Generator::generate_base_js_file_(Constants::$URL . "api/view/public/js/constants.js");
        } else {
            $this->elements_to_generate['_CONSTANTS_JS_'] = Generator::generate_base_js_file_(Constants::$ROOT . 'view' . Constants::$DS . 'public' . Constants::$DS . 'js' . Constants::$DS . 'constants.js');
        }

        $this->elements_to_generate['_TEMPLATE_'] = '';
        $this->elements_to_generate['_BREADCRUMB_'] = '';
        $this->elements_to_generate['_CSS_TEMPLATES_'] = '';
        $this->elements_to_generate['_PLUGINS_'] = '';
        $this->elements_to_generate['_SCRIPT_TEMPLATES_'] = '';
        $this->elements_to_generate['_TITLE_'] = '';
        $this->elements_to_generate['_NAVBAR_ITEMS_'] = '';
        $this->elements_to_generate['_SIDEBAR_ITEMS_'] = '';
        $this->load_sidebar();
    }

    public function __destruct() {
        
    }

    /**
     * Método que será el encargado de cargar el template correspondiente, así
     * también los archivos css,js y plugins necesarios
     * 
     * @param void
     * @return void
     */
    public abstract function listar();

    /**
     * Método que devuelve por AJAX los registros proporcionados por el controlador
     * 
     * @param array $result Listado completo del dao correspondiente
     * @return void
     */
    public abstract function listar_ajax(array $result);

    /**
     * Método que recibe un String y lo añade al título de la pestaña
     * @param String $title Título
     */
    public function set_title(String $title) {
        $this->elements_to_generate['_TITLE_'] = "- {$title}";
    }

    /**
     * Recibe un arreglo con los <b>nombres</b> de los <code>CSS</code> a cargar
     * (sin la extensión ni ruta)
     * 
     * @param array $files_names Arreglo que contiene los nombres de los archivos CSS a cargar
     * @return void
     */
    protected function load_css(array $files_names) {
        if (!empty($files_names)) {
            foreach ($files_names as $key => &$file_name) {
                $file_name = Constants::$URL . "api/view/public/css/styles/{$file_name}.css";
            }
            $temp = &Generator::generate_tag_css($files_names);
            $this->elements_to_generate['_CSS_TEMPLATES_'] .= $temp;
        }
    }

    /**
     * Recibe un arreglo con los <b>nombres</b> de los <code>Script JS</code> a cargar
     * (sin la extensión ni ruta)
     * 
     * @param array $files_names Arreglo que contiene los nombres de los archivos js a cargar
     * @return void
     */
    protected function load_script(array $files_names) {
        if (!empty($files_names)) {
            foreach ($files_names as $key => &$file_name) {
                $file_name = Constants::$URL . "api/view/public/js/{$file_name}.js";
            }
            $this->elements_to_generate['_SCRIPT_TEMPLATES_'] = & Generator::generate_tag_script($files_names);
        }
    }

    /**
     * Recibe un arreglo con los <b>nombres</b> de los <code>Script JS (plugins)</code> a cargar
     * (sin la extensión ni ruta)
     * 
     * @param array $files_names Arreglo que contiene los nombres de los archivos js a cargar
     * @return void
     */
    public function load_plugin_js(array $plugins) {
        if (!empty($plugins)) {
            foreach ($plugins as $key => &$plugin_name) {
                $plugin_name = Constants::$URL . "api/view/public/plugins/{$plugin_name}.js";
            }
            $temp = & Generator::generate_tag_script($plugins); //@FIXME si recibo una referencia no puedo "añadir" (append) el contenido
            $this->elements_to_generate['_PLUGINS_'] .= $temp;
        }
    }

    /**
     * Recibe un arreglo con los <b>nombres</b> de los <code>links css(plugins)</code> a cargar
     * (sin la extensión ni ruta).<br>
     * <b>NOTA:</b>Puede que para cargar un plugin <b>JS</b> necesite cargar <b>CSS</b>
     * también, por esta razón creé este método
     * 
     * @param array $files_names Arreglo que contiene los nombres de los archivos css a cargar
     * @return void
     */
    public function load_plugin_css(array $plugins) {
        if (empty($plugins)) {
            $this->elements_to_generate['_CSS_TEMPLATES_'] = '';
        } else {
            foreach ($plugins as $key => &$plugin_name) {
                $plugin_name = Constants::$URL . "api/view/public/plugins/{$plugin_name}.css";
            }
            $temp = &Generator::generate_tag_css($plugins);
            $this->elements_to_generate['_CSS_TEMPLATES_'] .= $temp;
        }
    }

    /**
     * Plantilla <b>HTML</b> a cargar
     * 
     * @param String $file_name nombre de la plantilla a cargar (ej: alumno)
     * @return void
     * @SEE modifiqué la fución para que concatene el contenido, en caso de querer
     * "cargar" más de una plantilla, realizar más llamadas
     */
    protected function load_template($file_name) {
        $this->elements_to_generate['_TEMPLATE_'] .= file_get_contents(Constants::$ROOT . 'view' . Constants::$DS . 'public' . Constants::$DS . 'html' . Constants::$DS . $file_name . '.html');
    }

    /**
     * Enlaces a cargar en la barra de navegación (ejemplo: loguot)
     * 
     * @param array $links Arreglo anidado con tantos arreglos como elementos contendrá la barra.
     * El formato es:<br><code>array(array('font-awesome class','texto','enlace'))</code>
     * @return void
     */
    protected function load_navbar(array $elements) {
        if (empty($elements)) {
            $elements[] = array('fa-sign-in', 'Debe iniciar sesión primero', 'login', 'iniciar_sesion');
        } else {
            foreach ($elements as &$e) {
                $e[2] = Constants::$URL . "{$e[2]}";
            }
        }

        $this->elements_to_generate['_NAVBAR_ITEMS_'] = &Generator::generate_tag_navbar($elements);
    }

    /**
     * Secciones a cargar en el sidebar
     * 
     * @param array $links Arreglo anidado con tantos arreglos como elementos contendrá la barra.
     * El formato es:<br><code>array(array('font-awesome class','texto','enlace'))</code>
     * @return void<br>
     * array(<br>
     *  array(<br>
     *      'ID_MOD',<br>
     *      'MOD_NAME',<br>
     *      array(<br>
     *          array(<br>
     *              'ID_CRUD',<br>
     *              'CRUD_NAME',<br>
     *              array(<br>
     *                  'OP_NAME' => 'LINK' <br>
     *              )<br>
     *          )<br>
     *      )    <br>
     *  )<br>
     * )
     */
    protected function load_sidebar() {
        $sm = Session_Manager::get_instance();
        $this->elements_to_generate['_SIDEBAR_ITEMS_'] = & Generator::generate_tag_sidebar($this->sidebar_menu[$sm->get_rol_id()]);
    }

    /**
     * Método que "añade" enlaces a generar
     * @param array $links Arreglo asociativo del tipo '_NOMBRE_ENLACE_' => 'URL'
     * @return void
     */
    protected function add_links(array $links) {
        foreach ($links as $index => $value) {
            $this->elements_to_generate[$index] = $value;
        }
    }

    /**
     * Genera el código JS necesario para datatable. Incluye los archivos js y 
     * css "mínimos" necesarios para el plugin.
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
     *            )<br>
     *      )<br>
     * -La primer columna <b>SIEMPRE</b> es "row_id", así que la definición anterior
     * no es necesaria (es a modo de ejemplo). Tampoco es necesaria proprocionarlo
     * en el arrelgo "columns" (cuarto parámetro)
     * @param int $fixed_columns <b>[opcional]</b> Número de columnas fijas
     * @param String $export_config <b>[opcional]</b> Path del archivo que contiene la
     * "configuración" para "exportButtons".<br>NOTA: ruta A PARTIR de la carpeta
     * "reports" (sin incluirla). Ej.: "direccion/provincia_admin"
     * @return void 
     */
    public function load_datatable(String $path, String $table_id, String $url, array $columns, array $column_def, int $fixed_columns = 0, String $export_config = '') {
        $plugins_js = array();
        $plugins_css = array();
        array_push($plugins_js, 'DataTables/DataTables-1.10.15/js/jquery.dataTables.min', 'DataTables/FixedColumns-3.2.2/js/dataTables.fixedColumns.min', 'DataTables/DataTables-1.10.15/js/dataTables.jqueryui.min');
        array_push($plugins_css, 'DataTables/dataTables.min', 'DataTables/FixedColumns-3.2.2/css/fixedColumns.dataTables.min', 'jquery-ui-1.12.1.custom/jquery-ui.min', 'DataTables/DataTables-1.10.15/css/dataTables.jqueryui.min');
        if ($export_config) {
            $export_config = Constants::$ROOT . 'view' . Constants::$DS . 'public' . Constants::$DS . 'reports' . Constants::$DS . $export_config;
            array_push($plugins_js, 'DataTables/buttons-1.3.1/js/dataTables.buttons.min', 'DataTables/buttons-1.3.1/js/buttons.jqueryui.min', 'DataTables/buttons-1.3.1/js/buttons.print.min', 'DataTables/jszip-3.1.3/jszip.min', 'DataTables/pdfmake-0.1.27/build/pdfmake.min', 'DataTables/pdfmake-0.1.27/build/vfs_fonts', 'DataTables/buttons-1.3.1/js/buttons.html5.min');
            array_push($plugins_css, 'DataTables/Buttons-1.3.1/css/buttons.jqueryui.min');
        }
        $this->load_plugin_js($plugins_js);
        $this->load_plugin_css($plugins_css);
        $path = Constants::$ROOT . 'view' . Constants::$DS . 'public' . Constants::$DS . 'js' . Constants::$DS . $path . '.js';
        $this->elements_to_generate['_OTHER_CONSTANTS_'] = & Generator::generate_datatable($path, $table_id, $url, $columns, $column_def, $fixed_columns, $export_config);
    }

    public function generate_breadcrumb(String $module = '', String $controller = '', String $method = '') {
        $this->elements_to_generate['_BREADCRUMB_'] = & Generator::generate_breadcrumbs($module, $controller, $method);
    }

    /**
     * Reemplaza en el archivo <code>index.html</code> todas las coincidencias 
     * (genera la página web)
     * 
     * @param void
     * @return void
     */
    protected function generate_view() {
        foreach ($this->elements_to_generate as $index => &$content) {
            $this->file = str_replace($index, $content, $this->file);
        }
        echo $this->file;
        exit;
    }

}

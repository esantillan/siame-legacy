<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace view;

use view\General_View as General_View;
use config\Constants as Constants;
use helper\Session_Manager as Session_Manager;
use helper\Cypher as Cypher;

/**
 * Description of Usuario_View
 *
 * @author Esteban
 */
class Usuario_View extends General_View {

    public function __construct() {
        parent::__construct();
    }

    public function mi_perfil() {
        $this->load_template('usuario/mi_perfil');
        $this->set_title('Mi Perfil');
        $this->load_navbar([['fa-home', 'Inicio', '', 'home'], ['fa-sign-out', 'Cerrar Sesión', 'login/log_out', 'cerrar_sesion']]);
        $this->generate_breadcrumb('Usuarios', 'Usuario', 'Mi Perfil');
        $this->load_css(['usuario/mi_perfil']);
        $this->load_script(['usuario/mi_perfil']);
        $this->load_plugin_css(['select2-4.0.0-rc.1/dist/css/select2.min', 'jquery-ui-1.12.1.custom/jquery-ui.min']);
        $this->load_plugin_js(['select2-4.0.0-rc.1/dist/js/select2.min', 'jquery-ui-1.12.1.custom/jquery-ui.min', 'jquery-ui-1.12.1.custom/spanish','jshash-2.2/sha512-min']);
        $this->generate_view();
    }

    public function get_datos_perfil(Array $usuario) {
        $usuario = array_shift($usuario);
        echo json_encode($usuario);
    }

    public function listar() {
        $template = '';
        $script = array('general_abm');
        switch (Session_Manager::get_instance()->get_rol_id()) {
            case Session_Manager::ADMINISTRADOR:
                $template = 'usuario' . Constants::$DS . 'abm_usuario_admin';
                array_push($script, 'usuario/abm_usuario_admin', 'usuario/datatables_usuario_admin');
                break;
            case Session_Manager::BEDEL:
                $template = 'usuario' . Constants::$DS . 'abm_usuario_bedel';
                array_push($script, 'usuario/abm_usuario_bedel', 'usuario/datatables_usuario_bedel');
                break;
        }
        $plugins['js'] = array('DataTables/DataTables-1.10.15/js/jquery.dataTables.min', 'DataTables/FixedColumns-3.2.2/js/dataTables.fixedColumns.min', 'DataTables/DataTables-1.10.15/js/dataTables.jqueryui.min', 'select2-4.0.0-rc.1/dist/js/select2.min');
        $plugins['css'] = array('DataTables/dataTables.min', 'DataTables/FixedColumns-3.2.2/css/fixedColumns.dataTables.min', 'jquery-ui-1.12.1.custom/jquery-ui.min', 'DataTables/DataTables-1.10.15/css/dataTables.jqueryui.min', 'select2-4.0.0-rc.1/dist/css/select2.min');

        array_push($plugins['js'], 'DataTables/buttons-1.3.1/js/dataTables.buttons.min', 'DataTables/buttons-1.3.1/js/buttons.jqueryui.min', 'DataTables/buttons-1.3.1/js/buttons.print.min', 'DataTables/jszip-3.1.3/jszip.min', 'DataTables/pdfmake-0.1.27/build/pdfmake.min', 'DataTables/pdfmake-0.1.27/build/vfs_fonts', 'DataTables/buttons-1.3.1/js/buttons.html5.min', 'jquery-ui-1.12.1.custom/jquery-ui.min', 'jquery-ui-1.12.1.custom/spanish');
        array_push($plugins['css'], 'DataTables/Buttons-1.3.1/css/buttons.jqueryui.min');

        $this->load_template($template);
        $this->set_title('Usuario ABM');
        $this->load_navbar([['fa-home', 'Inicio', '', 'home'], ['fa-sign-out', 'Cerrar Sesión', 'login/log_out', 'cerrar_sesion'], ['fa-id-card', 'Mi Pefil', 'usuario/mi_perfil', 'mi_perfil']]);
        $this->generate_breadcrumb('Usuarios', 'Usuario', 'ABM');
        $this->load_css(array('general_abm', 'usuario/abm_usuario'));
        $this->load_script($script);
        $this->load_plugin_css($plugins['css']);
        $this->load_plugin_js($plugins['js']);
        $this->generate_view();
    }

    public function listar_ajax(array $result) {
        $ret['data'] = array();
        switch (Session_Manager::get_instance()->get_rol_id()) {
            case Session_Manager::ADMINISTRADOR:
                foreach ($result as &$registro) {
                    $registro['operador_alta'] = '<center>' . $registro['operador_alta'] . '</center>';
                    $registro['operador_modificacion'] = '<center>' . $registro['operador_modificacion'] . '</center>';
                    $registro['acciones'] = '<span id="editar_' . $registro['id'] . '" class="editar w3-ripple"><i title="Editar" class="fa fa-pencil"></i></span>';
                    if ($registro['baja']) {
                        $registro['baja'] = '<center>baja</center>';
                        $registro['acciones'] .= '<span id="habilitar_' . $registro['id'] . '" class="habilitar w3-ripple"><i title="Volver a habilitar" class="fa fa-check"></i></span><span id="reset-pass_' . $registro['id'] . '" class="reset-pass w3-ripple"><i title="Reestablecer Contraseña" class="fa fa-key" aria-hidden="true"></i></span>';
                    } else {
                        $registro['baja'] = '';
                        $registro['acciones'] .= '<span id="eliminar_' . $registro['id'] . '" class="eliminar w3-ripple"><i title="Eliminar" class="fa fa-minus"></i></span><span id="reset-pass_' . $registro['id'] . '" class="reset-pass w3-ripple"><i title="Reestablecer Contraseña" class="fa fa-key" aria-hidden="true"></i></span>';
                    }
                    $ret['data'][] = $registro;
                }
                break;
            case Session_Manager::BEDEL:
                foreach ($result as &$registro) {
                    $registro['acciones'] = '<span id="editar_' . $registro['id'] . '" class="editar w3-ripple"><i title="Editar" class="fa fa-pencil"></i></span>';
                    unset($registro['fecha_alta']);
                    unset($registro['operador_alta']);
                    unset($registro['fecha_modificacion']);
                    unset($registro['operador_modificacion']);
                    if (!$registro['baja']) {
                        $registro['acciones'] .= '<span id="eliminar_' . $registro['id'] . '" class="eliminar w3-ripple"><i title="Eliminar" class="fa fa-minus"></i></span><span id="reset-pass_' . $registro['id'] . '" class="reset-pass w3-ripple"><i title="Reestablecer Contraseña" class="fa fa-key" aria-hidden="true"></i></span>';
                        unset($registro['baja']);
                        $ret['data'][] = $registro;
                    }
                }
                break;
            default:
                header("Location: /siame/error/error_403");
                break;
        }
        echo json_encode($ret);
    }

    public function listar_provincias($param) {
        echo json_encode($param);
    }

    public function listar_departamentos(array $departamentos, $provincia_id = '') {
        if ($provincia_id) {
            $departamentos = ['provincia_id' => str_replace(' ', '-', $provincia_id), 'departamentos' => $departamentos];
            foreach ($departamentos['departamentos'] as &$depto) {
                $depto['id'] = str_replace(' ', '-', $depto['id']);
            }
        } else {
            foreach ($departamentos as &$depto) {
                $depto['id'] = str_replace(' ', '-', $depto['id']);
            }
        }
        echo json_encode($departamentos);
    }

    public function listar_localidades($param) {
        echo json_encode($param);
    }

    public function listar_roles($param) {
        echo json_encode($param);
    }

    public function get_info_selects($param) {
        echo json_encode($param);
    }

}

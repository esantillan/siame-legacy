<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace view;

use view\General_View as General_View;
use config\Constants as Constants;
use helper\{
    Session_Manager as Session_Manager
};

/**
 * Description of Provincia_View
 *
 * @author Esteban
 */
class Provincia_View extends General_View {

    public function __construct() {
        parent::__construct();
    }

    public function listar() {
        $template = '';
        $script = array('general_abm');
        switch (Session_Manager::get_instance()->get_rol_id()) {
            case Session_Manager::ADMINISTRADOR:
                $template = 'abm_provincia_admin';
                array_push($script, 'direccion/provincia_admin','direccion/datatables_provincia_admin');
                $template = 'abm_provincia_admin';
                break;
            case Session_Manager::BEDEL:
                $template = 'abm_provincia_bedel';
                array_push($script,'direccion/provincia_bedel','direccion/datatables_provincia_bedel');
                $columns = array('nombre', 'acciones');
                break;
        }
        $plugins['js'] = array('DataTables/DataTables-1.10.15/js/jquery.dataTables.min', 'DataTables/FixedColumns-3.2.2/js/dataTables.fixedColumns.min', 'DataTables/DataTables-1.10.15/js/dataTables.jqueryui.min');
        $plugins['css'] =array('DataTables/dataTables.min', 'DataTables/FixedColumns-3.2.2/css/fixedColumns.dataTables.min', 'jquery-ui-1.12.1.custom/jquery-ui.min', 'DataTables/DataTables-1.10.15/css/dataTables.jqueryui.min');

        array_push($plugins['js'], 'DataTables/buttons-1.3.1/js/dataTables.buttons.min', 'DataTables/buttons-1.3.1/js/buttons.jqueryui.min', 'DataTables/buttons-1.3.1/js/buttons.print.min', 'DataTables/jszip-3.1.3/jszip.min', 'DataTables/pdfmake-0.1.27/build/pdfmake.min', 'DataTables/pdfmake-0.1.27/build/vfs_fonts', 'DataTables/buttons-1.3.1/js/buttons.html5.min');
        array_push($plugins['css'], 'DataTables/Buttons-1.3.1/css/buttons.jqueryui.min');
        
        $this->load_template('direccion' . Constants::$DS . $template);
        $this->set_title('Provincia');
        $this->load_navbar([['fa-home', 'Inicio', '', 'home'], ['fa-sign-out', 'Cerrar Sesión', 'login/log_out', 'cerrar_sesion'],['fa-id-card', 'Mi Pefil','usuario/mi_perfil','mi_perfil']]);
        $this->generate_breadcrumb('Dirección','Provincia','ABM');
        $this->load_css(array('general_abm', 'direccion/provincia'));
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
                        $registro['acciones'] .= '<span id="habilitar_' . $registro['id'] . '" class="habilitar w3-ripple"><i title="Volver a habilitar" class="fa fa-check"></i></span>';
                    } else {
                        $registro['baja'] = '';
                        $registro['acciones'] .= '<span id="eliminar_' . $registro['id'] . '" class="eliminar w3-ripple"><i title="Eliminar" class="fa fa-minus"></i></span>';
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
                        $registro['acciones'] .= '<span id="eliminar_' . $registro['id'] . '" class="eliminar w3-ripple"><i title="Eliminar" class="fa fa-minus"></i></span>';
                        unset($registro['baja']);
                        $ret['data'][] = $registro;
                    }
                }
                break;
        }
        echo json_encode($ret);
    }

}
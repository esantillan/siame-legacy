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

/**
 * Description of Plan_Estudio_View
 *
 * @author Esteban
 */
class Plan_Estudio_View extends General_View {

    public function __construct() {
        parent::__construct();
    }

    public function listar() {
        $template = 'institucional' . Constants::$DS . 'plan_estudio_abm_admin';
        $script = ['general_abm', 'institucional/plan_estudio_abm_admin', 'institucional/datatables_plan_estudio_abm_admin'];
        $plugins['js'] = ['DataTables/DataTables-1.10.15/js/jquery.dataTables.min', 'DataTables/FixedColumns-3.2.2/js/dataTables.fixedColumns.min', 'DataTables/DataTables-1.10.15/js/dataTables.jqueryui.min', 'tinymce/tinymce.min'];
        $plugins['css'] = ['DataTables/datatables.min', 'DataTables/FixedColumns-3.2.2/css/fixedColumns.dataTables.min', 'jquery-ui-1.12.1.custom/jquery-ui.min', 'DataTables/DataTables-1.10.15/css/dataTables.jqueryui.min'];

        $this->load_template($template);
        $this->set_title('Plan de Estudio');
        $this->load_navbar([['fa-home', 'Inicio', '', 'home'], ['fa-sign-out', 'Cerrar Sesión', 'login/log_out', 'cerrar_sesion'], ['fa-id-card', 'Mi Pefil', 'usuario/mi_perfil', 'mi_perfil']]);
        $this->generate_breadcrumb('Institucional', 'Plan de Estudio', 'ABM');
        $this->load_css(array('general_abm', 'institucional/plan_estudio_abm'));
        $this->load_script($script);
        $this->load_plugin_css($plugins['css']);
        $this->load_plugin_js($plugins['js']);
        $this->generate_view();
    }

    public function listar_ajax(array $result) {
        $ret['data'] = array();
        foreach ($result as &$registro) {
            $registro['condiciones_ingreso'] = nl2br($registro['condiciones_ingreso']);
            $registro['articulaciones'] = nl2br($registro['articulaciones']);
            $registro['operador_alta'] = '<center>' . $registro['operador_alta'] . '</center>';
            $registro['operador_modificacion'] = '<center>' . $registro['operador_modificacion'] . '</center>';
            $registro['acciones'] = '<span id="editar_' . $registro['id'] . '" class="editar w3-ripple"><i title="Editar" class="fa fa-pencil"></i></span>';
            if ($registro['baja']) {
                $registro['baja'] = '<center>baja</center>';
                $registro['acciones'] .= '<span id="habilitar_' . $registro['id'] . '" class="habilitar w3-ripple"><i title="Volver a habilitar" class="fa fa-check"></i>';
            } else {
                $registro['baja'] = '';
                $registro['acciones'] .= '<span id="eliminar_' . $registro['id'] . '" class="eliminar w3-ripple"><i title="Eliminar" class="fa fa-minus"></i></span>';
            }
            if (!empty($registro['path'])) {
                $aux = explode('/', $registro['path']);
                $aux = $aux[count($aux) - 1];
                $aux = explode('_', $aux)[1];//<i class="fa fa-arrow-down" aria-hidden="true"></i>
                $registro['path'] = $aux;
                $registro['acciones'] .= '<a href="plan_estudio/descargar/' . $registro['id'] . '" target="blank" class="descargar w3-ripple"><i title="Descargar Archivo" class="fa fa-arrow-down"></i></a>';
            }
            $ret['data'][] = $registro;
        }
        echo json_encode($ret);
    }

}

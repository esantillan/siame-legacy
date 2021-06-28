<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace controller;

use controller\General_Controller as General_Controller;
use controller\Direccion_Controller as Direccion_Controller;
use view\Usuario_View as Usuario_View;
use model\persistence\usuario\Usuario_Model as Usuario_Model;
use model\persistence\direccion\{
    Provincia_Model as Provincia_Model,
    Departamento_Model as Departamento_Model,
    Localidad_Model as Localidad_Model,
    Direccion_Model as Direccion_Model
};
use \model\persistence\acceso\Rol_Model as Rol_Model;
use helper\{
    Session_Manager as Session_Manager,
    Sanitizer as Sanitizer,
    Mail as Mail
};

/**
 * Description of Usuario_Controller
 *
 * @author Esteban
 */
class Usuario_Controller extends General_Controller {

    public function __construct() {
        parent::__construct();
        $this->check_session([Session_Manager::ADMINISTRADOR, Session_Manager::BEDEL]);
        $this->view = new Usuario_View();
        $this->model = Usuario_Model::get_instance();
        $this->aux_models = ['provincia_model', 'departamento_model', 'localidad_model', 'direccion_model', 'rol_model'];
        $this->aux_controllers = ['direccion_controller'];
    }

    public function index(): void {
        $this->view->listar();
    }

    public function mi_perfil() {
        $this->view->mi_perfil();
    }

    public function get_datos_perfil() {
        $usuario_id = Session_Manager::get_instance()->get_id();
        $aux_dir = '(SELECT d.calle FROM direccion d WHERE tp.direccion_id = d.id) as calle';
        $aux_dir .= ',(SELECT d.numero FROM direccion d WHERE tp.direccion_id = d.id) as numero';
        $aux_rol = '(SELECT r.descripcion FROM rol r WHERE tp.rol_id = r.id) rol';
        $fields = ['tp.id', 'tp.documento', 'tp.apellido', 'tp.nombre', 'date_format(tp.fecha_nacimiento,"%d-%m-%Y") AS fecha_nacimiento', 'tp.user', 'tp.telefono_fijo', 'tp.telefono_movil', 'tp.direccion_id', $aux_dir, 'tp.rol_id', $aux_rol, 'tp.email'];
        $result = $this->model->get_list($fields, ['tp.id = ' => $usuario_id]);
        $this->view->get_datos_perfil($result);
    }

    /**
     * Esta función añade una capa de indirección al editar el perfil de usuario.
     * Esto lo hago porque no puedo enviar el ROL_ID y almacenarlo en JS, porque
     * de ser así,por ejemplo: un alumno podría darse modificar su ROL 
     */
    public function editar_perfil() {
        $_POST['rol_id'] = Session_Manager::get_instance()->get_id();
        $this->edit();
    }

    public function listar_ajax() {
        $aux_dir = '(SELECT CONCAT(d.calle, " ", d.numero, " - ", l.nombre, " - ", dto.nombre, " - ", p.nombre) FROM direccion d JOIN localidad l ON d.localidad_id = l.id JOIN departamento dto ON l.departamento_id = dto.id JOIN provincia p ON dto.provincia_id = p.id WHERE tp.direccion_id = d.id) as direccion';
        $aux_rol = '(SELECT r.descripcion FROM rol r WHERE tp.rol_id = r.id) rol';
        $fields = ['tp.id', 'tp.documento', 'tp.apellido', 'tp.nombre', 'date_format(tp.fecha_nacimiento,"%d-%m-%Y") AS fecha_nacimiento', 'tp.user', 'tp.telefono_fijo', 'tp.telefono_movil', 'tp.direccion_id', $aux_dir, 'tp.rol_id', $aux_rol, 'tp.email', 'date_format(tp.fecha_alta,"%d-%m-%Y %H:%i:%s") AS fecha_alta', '(SELECT u.user FROM usuario u where tp.operador_alta = u.id) as operador_alta', 'date_format(tp.fecha_modificacion,"%d-%m-%Y %H:%i:%s") AS fecha_modificacion', '(SELECT u.user FROM usuario u where tp.operador_modificacion = u.id) as operador_modificacion', 'tp.baja'];
        $result = $this->model->get_list($fields, array(), true);
        $this->view->listar_ajax($result);
    }

    public function listar_provincias() {
        $this->aux_models['provincia_model'] = Provincia_Model::get_instance();
        $ret = $this->aux_models['provincia_model']->get_list(array("CONCAT('prov','_',tp.id) AS id", 'tp.nombre AS text'));
        $this->view->listar_provincias($ret);
    }

    public function listar_departamentos($type = '', $id = null) {
        $ret = null;
        $provincia_id = null;
        if ($id) {
//            $id = explode('_', $id)[1];
            switch ($type) {
                case 'provincia'://si recibo el id de provincia (se está realizando una "alta")
                    Sanitizer::validate_number($id, Sanitizer::INTEGER);
                    $this->aux_models['departamento_model'] = Departamento_Model::get_instance();
                    $ret = $this->aux_models['departamento_model']->get_list(array("CONCAT('dpto','_',tp.id) AS id", 'tp.nombre AS text'), array('tp.provincia_id = ' => $id));
                    $provincia_id = $id;
                    break;
                case 'departamento'://si recibo el id de provincia (se está realizando una "modificación")
                    Sanitizer::validate_number($id, Sanitizer::INTEGER);
                    $this->aux_models['departamento_model'] = Departamento_Model::get_instance();
                    $this->aux_models['provincia_model'] = Provincia_Model::get_instance();
                    $provincia_id = '';
                    $provincia_id = $this->aux_models['provincia_model']->get_list(array("CONCAT(tp.nombre,'_',tp.id) AS id"), array("tp.id = (SELECT d.provincia_id FROM departamento d WHERE d.id = '{$id}')"));
                    $ret = $this->aux_models['departamento_model']->get_list(
                            array("CONCAT('dpto','_',tp.id) AS id", 'tp.nombre AS text'), array('provincia_id = ' => explode('_', $provincia_id[0]['id'])[1]));
                    $provincia_id = array_shift($provincia_id)['id'];
                    break;
            }
            $this->view->listar_departamentos($ret, $provincia_id);
        }
    }

    public function listar_localidades($type = '', $id = null) {
        $ret = null;
        $departamento_id = null;
        if ($id) {
            \helper\Logger::save_log($type);
//            $id = explode('_', $id)[1];
            switch ($type) {
                case 'departamento'://si recibo el id de provincia (se está realizando una "alta")
                    Sanitizer::validate_number($id, Sanitizer::INTEGER);
                    $this->aux_models['localidad_model'] = Localidad_Model::get_instance();
                    $ret = $this->aux_models['localidad_model']->get_list(array("CONCAT('loc','_',tp.id) AS id", 'tp.nombre AS text'), array('tp.departamento_id = ' => $id));
                    $departamento_id = $id;
                    break;
                case 'localidad'://si recibo el id de provincia (se está realizando una "modificación")
                    Sanitizer::validate_number($id, Sanitizer::INTEGER);
                    $this->aux_models['localidad_model'] = Localidad_Model::get_instance();
                    $this->aux_models['departamento_model'] = Departamento_Model::get_instance();
                    $departamento_id = $this->aux_models['departamento_model']->get_list(["CONCAT(tp.nombre,'_',tp.id) AS id"], array("tp.id = (SELECT d.provincia_id FROM departamento d WHERE d.id = '{$id}')"));
                    $ret = $this->aux_models['departamento_model']->get_list(
                            array("CONCAT('dpto','_',tp.id) AS id", 'tp.nombre AS text'), array('tp.departamento_id = ' => explode('_', $departamento_id[0]['id'])[1]));
                    $departamento_id = array_shift($departamento_id)['id'];
                    break;
            }
            $this->view->listar_departamentos($ret, $departamento_id);
        }
    }

    public function listar_roles() {
        $this->aux_models['rol_model'] = Rol_Model::get_instance();
        switch (Session_Manager::get_instance()->get_rol_id()) {
            case Session_Manager::ADMINISTRADOR:
                $ret = $this->aux_models['rol_model']->get_list(array("CONCAT('rol', '_', tp.id) AS id", 'tp.descripcion AS text'), array('tp.id < ' => '6'));
                break;
            case Session_Manager::BEDEL:
                $ret = $this->aux_models['rol_model']->get_list(array("CONCAT('rol', '_', tp.id) AS id", 'tp.descripcion AS text'), array('tp.id < ' => '6', "tp.descripcion IN('ALUMNO','PROFESOR')"));
                break;
        }
        $this->view->listar_roles($ret);
    }

    /**
     * @Override
     * @param type $ret_json
     */
    public function create(array $params = null) {
        \helper\Logger::save_log($_POST);
        $ret = ['msg' => '', 'state' => FALSE];
        $required_missing = $this->check_required($_POST);
        $direccion = null;
        if ($required_missing) {
            $ret['msg'] = nl2br($required_missing);
        } else {
            $sm = Session_Manager::get_instance();
            $this->aux_controllers['direccion_controller'] = new Direccion_Controller();
            try {
                $dir_values = ['calle' => $_POST['calle'], 'localidad_id' => $_POST['localidad_id']];
                if (isset($_POST['numero'])) {
                    $dir_values['numero'] = $_POST['numero'];
                    unset($_POST['numero']);
                }
                unset($_POST['calle']);
                unset($_POST['localidad_id']);
                $direccion = $this->aux_controllers['direccion_controller']->create($dir_values);
                if ($direccion['state'] === false) {
                    throw new \Exception($direccion['msg']);
                }
                $_POST['direccion_id'] = $direccion['id'];
                $_POST['fecha_nacimiento'] = $this->get_date_sql($_POST['fecha_nacimiento']);

                $complete_pass = $this->generate_password();
                $_POST['pass'] = $complete_pass['hash'];

                $this->model->create($sm->get_id(), $_POST);
                $ret['msg'] .= "Se ha creado correctamente el usuario.";
                $ret['state'] = TRUE;
            } catch (\Exception $ex) {
                $this->aux_models['direccion_model'] = Direccion_Model::get_instance();
                if (!empty($dir_values)) {
                    $this->aux_models['direccion_model']->rollback($direccion['id']);
                }
                $ret['msg'] .= nl2br($ex->getMessage());
            }
        }
        echo json_encode($ret);
    }

    /**
     * @Override
     * @param type $ret_json
     */
    public function edit(array $params = array()) {
        $direccion = null;
        $ret = ['msg' => '', 'state' => FALSE];
        $required_missing = $this->check_required($_POST);
        if ($required_missing) {
            $ret['msg'] = nl2br($required_missing);
        } else {
            $sm = Session_Manager::get_instance();
            $this->aux_controllers['direccion_controller'] = new Direccion_Controller();
            try {
                $dir_values = ['calle' => $_POST['calle'], 'localidad_id' => $_POST['localidad_id']];
                if (isset($_POST['numero'])) {
                    $dir_values['numero'] = $_POST['numero'];
                    unset($_POST['numero']);
                }
                $dir_values['id'] = $_POST['direccion_id'];
                unset($_POST['calle']);
                unset($_POST['localidad_id']);
                unset($_POST['direccion_id']);
                $this->aux_controllers['direccion_controller']->edit($dir_values); //@TODOver cómo hacer para pasarle parámetros
                $_POST['fecha_nacimiento'] = $this->get_date_sql($_POST['fecha_nacimiento']);
                $id = $_POST['id'];
                unset($_POST['id']);
                
                $this->model->update($id, $sm->get_id(), $_POST);
                $ret['msg'] .= "Se ha modificado correctamente el usuario.";
                $ret['state'] = TRUE;
            } catch (\Exception $ex) {
                if (!empty($dir_values)) {
                    $this->aux_models['direccion_model']->rollback($direccion['id']);
                }
                $ret['msg'] .= nl2br($ex->getMessage());
            }
        }
        echo json_encode($ret);
    }

    /**
     * Comprueba los campos requeridos (como así también su formato)
     * 
     * @param void
     * @return string Devuelve una cadena vacía si "no hay ningún problema" o 
     * el mensaje de error en caso de no superar todas las pruebas
     */
    protected function check_required(array $params) {
        $ret = '';
        $missing = array();
        $this->aux_models['direccion_model'] = Direccion_Model::get_instance();
        if (isset($_POST['email']) && !Sanitizer::validate_email($_POST['email'])) {
            $ret = 'Error: La direccion de correo proporcionada no es v&aacute;lida.';
        }
        foreach ($this->aux_models['direccion_model']->get_required() as $property) {
            if (!isset($params[$property])) {
                $missing[] = $property;
            }
        }
        $req = $this->model->get_required();
        foreach ($req as $property) {
            if ($property == 'pass' || $property == 'direccion_id') {
                continue;
            }
            if (!isset($params[$property]) || empty($params[$property])) {
                $missing[] = $property;
            }
        }
        if (!empty($missing)) {
            $ret = 'No se ha proporcionado los siguientes valores (requeridos):' . PHP_EOL;
            foreach ($missing as $value) {
                $ret .= '.' . $value . PHP_EOL;
            }
        }
        return $ret;
    }

    /**
     * Devuelve un array codificado en json con el siguiente formato:
     * {
     *  "provincia_id": 1,
     *  "departamento_id": 1,
     *  "localidad_id": 1,
     *  "calle": "Ejemplo",
     *  "numero": 123,
     *  "departamentos": [{..},...],
     *  "localidades": [{..},...]
     * }
     */
    public function get_info_selects($direccion_id = 0) {
        if ($direccion_id) {
            $this->aux_models['departamento_model'] = Departamento_Model::get_instance();
            $this->aux_models['localidad_model'] = Localidad_Model::get_instance();
            $this->aux_models['direccion_model'] = Direccion_Model::get_instance();
            $ret = array();

            $aux = $this->aux_models['direccion_model']->get_list(['calle', 'numero', 'localidad_id'], ['tp.id = ' => $direccion_id]);
            $ret['calle'] = $aux[0]['calle'];
            $ret['numero'] = $aux[0]['numero'];
            $ret['localidad_id'] = $aux[0]['localidad_id'];
            $ret['departamento_id'] = $this->aux_models['localidad_model']->get_list(['departamento_id'], ['tp.id = ' => $ret['localidad_id']])[0]['departamento_id'];
            $ret['provincia_id'] = $this->aux_models['departamento_model']->get_list(['provincia_id'], ['tp.id = ' => $ret['departamento_id']])[0]['provincia_id'];
            $aux = $this->aux_models['departamento_model']->get_list(['CONCAT("dpto","_",tp.id) AS id', 'tp.nombre AS text'], ['provincia_id = ' => $ret['provincia_id']]);
            $ret['departamentos'] = $aux;
            $aux = $this->aux_models['localidad_model']->get_list(['CONCAT("loc","_",tp.id) AS id', 'tp.nombre AS text'], ['departamento_id = ' => $ret['departamento_id']]);
            $ret['localidades'] = $aux;
            $ret['provincia_id'] = 'prov_' . $ret['provincia_id'];
            $ret['departamento_id'] = 'dpto_' . $ret['departamento_id'];
            $ret['localidad_id'] = 'loc_' . $ret['localidad_id'];
            $this->view->get_info_selects($ret);
        }
    }

    public function reset_pass($id) {
        $ret['state'] = TRUE;
        $ret['msg'] = '';
        if ($id) {
            $sm = Session_Manager::get_instance();
            $pass = $this->generate_password();
            $this->model->update($id, $sm->get_id(), ['pass' => $pass['hash']]);
            $usuario = $this->model->read($id);
            try {
                $this->enviar_correo($usuario['email'], $usuario['nombre'], $pass['pass']);
                $ret['msg'] .= 'Se ha reestablecido correctamente la contrase&ntilde;a y enviado por correo al usuario.';
            } catch (Exception $ex) {
                $ret['state'] = FALSE;
                $ret['msg'] .= nl2br($ex->getMessage());
            }
        }
        echo json_encode($ret);
    }

    public function enviar_correo($email, $nombre, $user, $pass) {
        $mailer = new Mail(); //'Usuario y Contraseña del Sistema de Inscripci&oacute;n de Alumnos a Mesas de Ex&aacute;menes', 'Usuario: ' . $values['user'] . '<br>Contrase&ntilde;a: ' . $values['pass']
        $mailer->send_email('Reestablecimiento de Contrasenia del Sistema de Inscripcion de Alumnos a Mesas de Examenes (S.I.A.M.E)', [$email => $nombre], NULL, 'Usuario: ' . $user . '<br>Contrase&ntilde;a: ' . $pass);
    }

    public function generate_password(): Array {
        $ret['pass'] = '';
        for ($i = 0; $i < 4; $i++) {
            $ret['pass'] .= chr(rand(65, 90));
            $ret['pass'] .= chr(rand(97, 122));
            $ret['pass'] .= rand(0, 10);
        }
        $ret['pass'] = str_shuffle($ret['pass']);
        $ret['hash'] = $this->encrypt_pass($ret['pass']);
        \helper\Logger::save_log(print_r($ret, true));
        return $ret;
    }

    public function encrypt_pass($pass): String {
        return hash('sha512', $pass);
    }

}

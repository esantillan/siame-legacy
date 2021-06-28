<?php

declare(strict_types = 1);

namespace helper;

/**
 * Clase encargada de "interactuar" con la matriz $_SESSION
 * En esta matriz guardo:
 * -ID Usuario
 * -LEGAJO
 * -ROL_ID
 *
 * @package model
 * @author Esteban
 * @version 1.0
 */
class Session_Manager {
    /*
     * Roles
     */

    const ADMINISTRADOR = 1;
    const BEDEL = 2;
    const PROFESOR = 3;
    const ALUMNO = 4;
    const ADMINISTRATIVO = 5;
    const USUARIO_SIN_AUTENTICAR = 6;

    private static $instance = null;

    private function __construct() {
        @session_start();
    }

    public static function get_instance() {
        if (!isset(self::$instance)) {
            self::$instance = new Session_Manager();
        }
        if (empty($_SESSION)) {
            $_SESSION['id'] = 1;
            $_SESSION['legajo'] = 1;
            $_SESSION['rol_id'] = self::USUARIO_SIN_AUTENTICAR;
        }
        return self::$instance;
    }

    public function get_id() {
        return $_SESSION['id'];
    }

    public function get_legajo() {
        return $_SESSION['legajo'];
    }

    public function get_rol_id() {
        return $_SESSION['rol_id'];
    }

    public function set_id($id) {
        if (!settype($id, 'integer')) {
            throw new \Exception("El ID ( {$id} ) de recibido no es un entero");
        }
        $_SESSION['id'] = $id;
    }

    public function set_legajo($legajo) {
        if (!settype($legajo, 'integer')) {
            throw new \Exception("El legajo recibido ( {$legajo} ) no es un entero");
        }
        $_SESSION['lagajo'] = $legajo;
    }

    public function set_rol_id($rol) {
        if (!settype($rol, 'integer')) {
            throw new \Exception("El rol recibido ( {$rol} ) no es un entero");
        }
        $_SESSION['rol_id'] = $rol;
    }

    public function destroy_session() {
        $_SESSION = array();
        setcookie(session_name(), '', time() - 5600);
        session_destroy();
    }

}

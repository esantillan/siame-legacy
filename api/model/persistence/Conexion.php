<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
declare(strict_types = 1);

namespace model\persistence;

use \PDO;
use \config\Constants as Constants;

/**
 * Description of Conexion
 *
 * @author Esteban
 */
class Conexion {

    private $db;
    private $result_set = null;
    private $driver = 'mysql';
    private $host = '';
    private $db_name = '';
    private $user = '';
    private $pass = '';
    private $sentence = '';
    private static $instance = null;

    private function __construct() {
        /*
         * @FIXME en un entorno de producción, eliminar esto y dejar sólo los 
         * parámetros que realmente se usan
         */
        //@FIXME
        if(Constants::$ENVIROMENT == Constants::LOCAL){
            $this->host = 'localhost';
            $this->db_name = 'siame';
            $this->user = 'root';
            $this->pass = '';
        }else{//@SEE lo comente para que funcione dentro de una red ad-hoc
            $this->host = 'sql206.260mb.net';//'mysql.hostinger.com.ar';
            $this->db_name = 'n260m_19902704_siame';
            $this->user = 'n260m_19902704';
            $this->pass = '39019775';
        }
        try {
            $this->db = new PDO("{$this->driver}:host={$this->host};dbname={$this->db_name};charset=utf8", $this->user, $this->pass, array(PDO::ATTR_PERSISTENT => false));
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
            $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
        } catch (\PDOException $pdo_exc) {
            throw new \Exception("ERROR: No se ha podido conectar a la BD.\nMensaje de Error: {$pdo_exc->getMessage()}\n"
            . "Archivo: '{$pdo_exc->getFile()}'\tLinea: '{$pdo_exc->getLine()}'");
        } catch (\Exception $exc) {
            throw new \Exception("ERROR INESPERADO\nMensaje de Error: {$pdo_exc->getMessage()}\n"
            . "Archivo: '{$pdo_exc->getFile()}'\tLinea: '{$pdo_exc->getLine()}'");
        }
    }

    /**
     * Si la clase no ha sido instanciada, crea una instancia de sí misma y la
     * retorna, en caso de existir una instancia, simplemente la devuelve. (SINGLETON)
     * 
     * @param void
     * @return Conexion Instancia de Conexion
     */
    public static function get_instance():Conexion  {
        if (self::$instance == null) {
            self::$instance = new Conexion();
        }
        return self::$instance;
    }

    /**
     * Ejecuta sentencias SQL que no arrojan resultados, tal como INSERT o UPDATE
     * 
     * @param String $sql Sentencia SQL a ejecutar
     * @param bool $one_row_affect [opcional] si es true (por defecto)m commprobará que
     * se haya afectado sólo una fila (si afectó a más de una, hace roll back y lanza una excepcion)
     * @return int Devuelve el último id insertado
     */
    public function simple_query(String $sql, bool $one_row_affect = true) {
        try {
            $this->db->beginTransaction();
            $this->sentence = $sql;
            $this->result_set = $this->db->query($sql);
            $id = $this->db->lastInsertId($sql);
            if ($one_row_affect) {
                if ($this->result_set->rowCount() != 1) {
                    $this->db->rollBack();
                    throw new \Exception("{$sql} no afectó la cantidad filas esperadas (1) sino:\n{$this->result_set->rowCount()} filas.\nLos cambios se deshicieron.\nPor favor, revise que el ID proporcionado exista en la tabla.");
                } else {
                    $this->db->commit();
                }
            } else {
                $this->db->commit();
            }
            return $id;
        } catch (\PDOException $pdo_ex) {
            if ($this->db->inTransaction())
                $this->db->rollBack();
            \helper\Logger::save_log($this->sentence);
            throw new \Exception("\nHa ocurrido un error de SQL:\nCódigo: {$pdo_ex->getCode()}\nMensage: {$pdo_ex->getMessage()}\nArchivo{$pdo_ex->getFile()}\nLínea: {$pdo_ex->getLine()}\n$this->sentence");
        } catch (\Exception $ex) {
            if ($this->db->inTransaction())
                $this->db->rollBack();
            throw new \Exception("\nHa ocurrido un error:\nCódigo: {$ex->getCode()}\nMensage: {$ex->getMessage()}\nArchivo{$ex->getFile()}\nLínea: {$ex->getLine()}\n$this->sentence");
        }
    }

    /**
     * Ejecuta una consulta SQL (SELECT). Devuelve un arreglo anidado
     * 
     * @param String $sql sentencia SQL a ejecutar.
     * @return array anidado, en el cual cada sub-array representa una registro en
     * la bd, con la forma 'nombre_col' => 'val'.
     */
    public function query(String $sql):array {
        try {
            $this->sentence = $sql;
            $this->result_set = $this->db->query($sql);

            $this->result_set->setFetchMode(PDO::FETCH_ASSOC);
            $retorno = $this->result_set->fetchAll();
            
            return $retorno;
        } catch (\PDOException $pdo_ex) {
            if ($this->db->inTransaction())
                $this->db->rollBack();
            \helper\Logger::save_log($this->sentence);
            throw new \Exception("\nHa ocurrido un error de SQL:\nCódigo: {$pdo_ex->getCode()}\nMensage: {$pdo_ex->getMessage()}\nArchivo{$pdo_ex->getFile()}\nLínea: {$pdo_ex->getLine()}\n$this->sentence");
        } catch (\Exception $ex) {
            if ($this->db->inTransaction())
                $this->db->rollBack();
            throw new \Exception("\nHa ocurrido un error:\nCódigo: {$ex->getCode()}\nMensage: {$ex->getMessage()}\nArchivo{$ex->getFile()}\nLínea: {$ex->getLine()}\n$this->sentence");
        }
    }
    
    /**
     * Devuelve la ultima consulta ejecutada.
     * 
     * @param void
     * @return String Último query ejecutado
     */
    public function get_last_query():String {
        return $this->sentence;
    }

    public function __destruct() {
        /*
         * según la documentación oficial de php, para cerrar una conexion debo
         * igualar el recurso a NULL (aunque ésto la cierra al terminar el script).
         * 
         * no ejecuto mysql_close porque sería contrario a una conexion persistente
         */
        $this->db = null;
        $this->result_set = null;
        $this->sentence = null;
    }
}

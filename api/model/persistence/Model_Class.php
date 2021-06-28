<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace model\persistence;

/**
 *
 * @author Esteban
 */
interface Model_Class {
    static function get_instance(): Model_Class;
    function create(int $operador, array $values = array());
    function read(int $id,bool $include_removed = false);
    function update(int $id,int $operador,array $values = array());
    function delete(int $id,int $operador);
    function enable(int $id, int $operador);
    function get_list(array $fields = array(), array $cond = array(), bool $include_removed = false):array;
}

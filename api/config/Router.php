<?php

declare(strict_types=1);
namespace config;

use config\Request as Request;
use config\Constants as Constants;
use helper\Sanitizer as Sanitizer;
/**
 * Esta clase es la ecargada de "llamar" a los controladores y métodos correspondientes
 * 
 * @package config
 * @author Esteban
 */
class Router {
    
    public static function call_controller($request = null) {
        if(!$request){
            $request = Request::get_instance();
        }
        $method = $request->get_method();
        $args = $request->get_args();
        if(is_readable($request->get_controller(true))){
            require_once $request->get_controller(true);
            $class = $request->get_controller(false);
            $controller = new $class;
            
            if(!empty($args)){
                call_user_func_array(array($controller, $method),$args);
            }else{
                call_user_func(array($controller, $method));
            }
        }
    }
}

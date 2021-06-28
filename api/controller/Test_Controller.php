<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace controller;

use controller\General_Controller as General_Controller;
use model\persistence\acceso\Rol_Model as Rol_Model;
/**
 * Description of Test_Controller
 *
 * @author Esteban
 */
class Test_Controller extends General_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
    }

    public function index(): void {
        $r = Rol_Model::get_instance();
        $r->delete(1,1);exit;
        \helper\Logger::save_log('<!DOCTYPE html>
<html>
    <body><b>NEGRITA</b>
        '.html_entity_decode('&#38;lt;b&#38;gt;&#34;Algo&#34; puede ser culquier &#39;cosa&#39;&#38;lt;/b&#38;gt;').'
    </body>
</html>');
        echo '<!DOCTYPE html>
<html>
    <body><b>NEGRITA</b>
        '.html_entity_decode('&#38;lt;b&#38;gt;&#34;Algo&#34; puede ser culquier &#39;cosa&#39;&#38;lt;/b&#38;gt;').'
    </body>
</html>';
    }
    
    public function validar() {
        print_r($_POST);
        $this->index();
    }

}

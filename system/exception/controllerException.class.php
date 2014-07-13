<?php
/**
 * Created by PhpStorm.
 * User: huangnie
 */
class ControllerException extends Exception {

    function __construct(){
        parent::__construct();
    }

    function redirect($dir="404"){

        echo file_get_contents(PUBLIC_DIR.'/'.$dir);
    }
}
<?php
/**
 * Class Ni_Model
 */
require_once 'Ni_Create.class.php';
class Ni_Model extends Ni_Create {

    private static $daoArr=array();

    function __construct(){
        parent::__construct();
    }

    /**
     * @param $name
     * @return mixed
     * @throws Exception
     */
    function dao($name){
        if(strpos($name,'_')){
            $name=explode('_',$name);
            $dir=implode('/',$name);
            $name=end($name);
        }else{
            $dir=$name;
        }
        $filePath= APPLICATION_DIR."/daos/{$dir}.dao.php";
        $daoClassName=ucfirst($name).'Dao';
        return $this->load($daoClassName,$filePath,self::$daoArr);
	}

}
<?php
/**
 *
 */
/**
 * Class Ni_Create
 */
class Ni_Create{

    private static $libArr=array();

    function __construct(){

    }

    function lib($name){
        $name=explode('_',$name);
        $dir=implode('/',$name);
        $filePath= APPLICATION_DIR."/lib/{$dir}.class.php";
        $className=ucfirst(end($name)).'Lib';
        return $this->load($className,$filePath,self::$libArr);
    }

    protected function load($className,$filePath,&$classVector){
        if (file_exists($filePath)) {
            if(!class_exists($className)) include "{$filePath}";
            //验证该类是否成功导入
            if(!class_exists($className)) throw new Exception("找不到 $className.php ,请检查该文件是否创建在指定的位置");
            try {
                if(isset($classVector[$className])){
                    $lib=$classVector[$className];
                    if($lib instanceof $className) return clone $classVector[$className];
                }
                $rc = new ReflectionClass($className);
                $classVector[$className]=$rc->newInstance();
                return $classVector[$className];
            } catch (LogicException $Exception) {
                throw new ErrorException("LogicException");
            } catch (ReflectionException $Exception) {
                $tip="the class ".$className." requested in $className.php does not exist! it may be the wrong naming of class, such as the not upper of first word of class.";
                throw new ErrorException($tip);
            }
        }
        else throw new Exception("找不到 $className.php ,请检查该文件是否创建在指定的位置");
    }

}
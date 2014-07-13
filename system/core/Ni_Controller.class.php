<?php
require_once 'Ni_Create.class.php';
class Ni_Controller extends Ni_Create {

    private static $modelArr=array();
    private $input=array();

    function __construct(){
        foreach($_GET as &$value)htmlspecialchars($value);
        foreach($_POST as &$value){
            if(is_array($value)){
                foreach($value as &$value2)htmlspecialchars($value2);
            }
            else htmlspecialchars($value);
        }
        $this->input=$_REQUEST['paramArr'] + $_GET + $_POST;
    }

    /**
     * @param string $key
     * @param string $value
     * @return string
     */
    function __PARAM($key="",$value=""){
        if(isset($this->input[$key])){
            return $this->input[$key];
        }
        else if(isset($_ENV[$key])) return htmlspecialchars($_ENV[$key]);
        else return $value;
    }

    /**
     * @return mixed
     */
    function __PARAMS(){
        return $this->input;
    }

    /**
     * @param $name
     * @param $arr
     * @param bool $return
     * @param $dir
     * @return string
     * @throws Exception
     */
    function displayForm($name,array $arr=null,$return=false,$dir=""){
        if($dir!="")$dir.='/';
        $filePath= APPLICATION_DIR."/views/{$dir}{$name}.form.php";
        $this->display($name,$arr,$return,$filePath);
    }

    /**
     * @param $name
     * @param $arr
     * @param bool $return
     * @param $dir
     * @return string
     * @throws Exception
     */
    function displayView($name,array $arr=null,$return=false,$dir=""){
        if($dir!="")$dir.='/';
		$filePath= APPLICATION_DIR."/views/{$dir}{$name}.view.php";
		$this->display($name,$arr,$return,$filePath);
	}

    /**
     * @param $name
     * @param $arr
     * @param bool $return
     * @param $filePath
     * @return string
     * @throws Exception
     */
    private function display($name,array $arr=null,$return=false,$filePath=""){
        if (file_exists($filePath)) {
            is_array($arr) && extract($arr);
            if($return==true){
                ob_start();
                include "{$filePath}";
                $buffer=ob_get_contents();
                ob_end_clean();
                return $buffer;
            }
            else include "{$filePath}";
        }
        else throw new Exception("找不到 ".$name.".view.php ,请检查该文件是否创建在指定的位置");
    }

    /**
     * @param $name
     * @return mixed
     * @throws Exception
     */
    function model($name){
        $name=explode('_',$name);
        $dir=implode('/',$name);
		$filePath= APPLICATION_DIR."/models/{$dir}.model.php";
        $modelClassName=ucfirst(end($name)).'Model';
		if (file_exists($filePath)) {
            if(isset(self::$modelArr[$modelClassName]) && is_object(self::$modelArr[$modelClassName]))return self::$modelArr[$modelClassName];
            if(!class_exists($modelClassName)) include "{$filePath}";
            try {
                if(isset(self::$modelArr[$modelClassName])){
                    $model=self::$modelArr[$modelClassName];
                    if($model instanceof $modelClassName) return clone self::$modelArr[$modelClassName];
                }
                $rc = new ReflectionClass($modelClassName);
                $model=$rc->newInstance();
                self::$modelArr[$modelClassName]=$model;
                return $model;
            } catch (LogicException $Exception) {
                throw new ErrorException("LogicException");
            } catch (ReflectionException $Exception) {
                $tip="the class ".$modelClassName." requested in ".$modelClassName.".Model.php does not exist! it may be the wrong naming of class, such as the not upper of first word of class.";
                throw new ErrorException($tip);
            }
		}
		else throw new Exception("找不到 ".$modelClassName.".model.php ,请检查该文件是否创建在指定的位置");
	}
}
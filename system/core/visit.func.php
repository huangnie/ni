<?php
/**
 * desc 访问控制
 *
 */
function visit($arr=array()){
	$application=$arr[0];
	$controller=$arr[1];
	$method=$arr[2];
	$methodParams=$arr[3];
	// check
	if(!defined("PROJECT_DIR")){
        $tip= 'sorry, the request is not find, it may be the wrong config of router.';
        throw new ErrorException($tip);
    }

    define("APPLICATION_DIR",PROJECT_DIR.'/'.$application);

    if(!is_dir(APPLICATION_DIR)){
        $tip= 'the application (default: index ): '.$application.' is not found, it may be your wrong of the url or name of application.';
        throw new ErrorException($tip);
	}
    include APPLICATION_DIR."/config/db.config.php";           //该应用的数据库配置
    define("CONTROLLER_DIR",APPLICATION_DIR.'/controllers');
    if(!defined("CONTROLLER_DIR")){
        $tip= 'the controller dir "controllers" requested is not found, it may be your wrong of the url or dir name of controller.';
        throw new ErrorException($tip);
    }
	$controller_exe='.controller.php';
	$controllerFilePath=CONTROLLER_DIR.'/'.$controller.$controller_exe;
	if(!file_exists($controllerFilePath)){
        $tip= 'the controller file(default: index ): '.$controller.$controller_exe.' is not found, it may be your wrong of the url or name of file.';
        throw new ErrorException($tip);
	}

    // visit
	include "{$controllerFilePath}";
    try {
        $controllerClassName=ucfirst($controller).'Controller';
        $rc = new ReflectionClass($controllerClassName);
        if(!$rc->hasMethod("{$method}")) throw new Exception("the method named ".$method." in ".$controller.$controller_exe." is not found!");
        else{   // 抛出异常后不会执行下去,但须万无一失
            $obj=$rc->newInstance();
            call_user_func_array(array($obj, $method),$methodParams);
        }
    } catch (LogicException $Exception) {
        throw new ErrorException("LogicException");
    } catch (ReflectionException $Exception) {
        $tip="the class ".$controllerClassName." requested in ".$controller.$controller_exe." does not exist! it may be the wrong naming of class, such as the not upper of first word of class.";
        throw new ErrorException($tip);
    } catch (ControllerException $e) {
        $e->redirect($application);
    }

}
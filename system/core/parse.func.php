<?php
/**
 * desc 
 *
 *
 */
function parse(){
    $application=(defined("DEFAULT_APPLICATION")&&DEFAULT_APPLICATION!="") ? DEFAULT_APPLICATION:'index';
    $controller=(defined("DEFAULT_CONTROLLER")&&DEFAULT_CONTROLLER!="") ?  DEFAULT_CONTROLLER:'index';
    $method=(defined("DEFAULT_METHOD")&&DEFAULT_METHOD!="") ? DEFAULT_METHOD:'index';

	$uri=$_SERVER['REQUEST_URI'];
    $uri=preg_replace('/\/{2,}/','/',$uri); //过滤
	$thePosOfIndex=strpos($uri,'index.php');
    $endThePosOfIndex=$thePosOfIndex+strlen('index.php')+1;
    $wenhaoPos=strrpos($uri,'?');
    $len= strlen($uri);
    if($wenhaoPos <=0) $uriParams=substr($uri,$endThePosOfIndex);
    else  $uriParams=substr($uri,$endThePosOfIndex,($wenhaoPos-$len));
    $arr=explode('/',$uriParams);

    $application= (isset($arr[0])&&$arr[0]!="") ? $arr[0]:$application;
    $controller= (isset($arr[1])&&$arr[1]!="") ? $arr[1]:$controller;
    $method= (isset($arr[2])&&$arr[2]!="") ? $arr[2]:$method;
    $_REQUEST["paramArr"]=array();
    if(count($arr)>=2){
        unset($arr[0]);
        unset($arr[1]);
        foreach($arr as $param){
            $_REQUEST["paramArr"][]=$param;

        }
        $_REQUEST["paramArr"][0]=$method;
        // $__PARAM['param0'] 是控制器方法名，之后是参数
	}
    $methodParamArr=$_REQUEST["paramArr"];
    unset($methodParamArr[0]);
	return array($application,$controller,$method,$methodParamArr);
}
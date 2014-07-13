<?php 
/*
 * desc this is the enter of websit
 * the function as follow：
 * to parse url
 * check the rights
 * call method of the contooller 
 * and 
 *
 */

if(true)error_reporting(E_ALL); //禁用错误报告, 使用异常
//error_reporting(E_ERROR | E_WARNING | E_PARSE); //报告运行时错误
//error_reporting(0);  //报告所有错误
date_default_timezone_set("Asia/Shanghai");

try{
    $routerConfig="config/router.config.php";
    if(!file_exists($routerConfig)) throw new Exception('全局配置文件导入失败');
    include "{$routerConfig}";
    $system="system/include.php";
    if(!file_exists($system)) throw new Exception('系统文件导入失败');
    include "{$system}";
    $arr=parse();
    visit($arr);
}
catch (Exception $e){
    echo '<br><meta charset="utf-8"> tip: '.$e->getMessage();
}











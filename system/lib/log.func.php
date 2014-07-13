<?php
/**
 * Created by PhpStorm.
 * User: huangnie
 */
function writeLog($message, $logFile = null, $dateFormat = null,$type="LOG_INFO",$attr="2"){
    static $types = array(
        'LOG_EMERG' => LOG_EMERG,
        'LOG_ALERT' => LOG_ALERT,
        'LOG_CRIT' => LOG_CRIT,
        'LOG_ERR' => LOG_ERR,
        'LOG_WARNING' =>LOG_WARNING ,
        // windows下，以下三个值是一样的
       'LOG_NOTICE'   => LOG_NOTICE,
        'LOG_DEBUG' => LOG_DEBUG,
        'LOG_INFO' =>  LOG_INFO
    );

    static $attrArr=array(
        '1'=>FILE_USE_INCLUDE_PATH,   //检查 filename 副本的内置路径
        '2'=>FILE_APPEND,             //在文件末尾以追加的方式写入数据
        '3'=>LOCK_EX,                 //对文件上锁
    );
    static $file = null;
    $msg = "\n".date("Y-m-d G:i:s")." [".$types[$type]."]\n".$message.PHP_EOL; //格式化消息
    $msg=str_replace("<br>","\n",$msg);
    if(!is_file($logFile))$logFile.=date('Y-m-d').'.txt';
    $dir=dirname($logFile);
    if(!is_dir($dir)) mkdir($dir,0755,true);
    $file=$logFile;
    if(!file_exists($file)){
        $fp=fopen($file,"w");
        fwrite($fp,$msg);
        fclose($fp);
    }
    else if(!error_log($msg, 3, $file)) file_put_contents($file,$msg,$attrArr[$attr]);
    error_log($msg, 0);
    $type = isset($types[$type]) ? $types[$type] : LOG_INFO;
    return syslog($type, $message);
}

function getLog($file){
    return file_get_contents($file);
}
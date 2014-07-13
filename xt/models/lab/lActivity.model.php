<?php
/**
 *  User: huangnie
 */
require_once dirname(dirname(__FILE__)).'/activity.model.php';
/**
 * 实验室管理
 */
class LActivityModel extends ActivityModel {

    function __construct(){
        parent::__construct();
    }

    /**
     *  配置操作
     * @param int $deskId
     * @param $deviceId
     * @return array
     */
    function deskBindDevice($deskId=0, $deviceId){
        if($deskId == 0) return '请求失败';
        if(!is_array($deviceId) || count($deviceId) ==0) return '输入数据不全';
        $desk=$this->dao('lDesk');
        $desk->setDeviceIds(implode(',',$deviceId));
        return $desk->where_eq('id',$deskId)->modify();
    }

}
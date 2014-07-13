<?php
/**
 *  User: huangnie
 */
require_once dirname(dirname(__FILE__)).'/info.model.php';
/**
 * 实验室信息
 */
class LInfoModel extends InfoModel {

    function __construct(){

    }

    /**
     * 实验室列表
     * @param bool $isViewSql
     * @param null $state
     * @return mixed
     */
    function room($isViewSql=false, $state=null){
        $dao=$this->dao("lRoom");
        $dao->select('lab_room.id');
        $operate['deskViewBtn']=$this->ui->getDeskViewLink();
        $operate['modifyBtn']=$this->ui->getModifyBtn();
        $operate['deleteBtn']=$this->ui->getDeleteBtn();
        $data= $this->table($dao,$isViewSql);
        return $this->toHtmlArr($data,$operate,$this->ui->getAddBtn());
    }

    /**
     * 实验桌列表
     * @param bool $isViewSql
     * @param null $state
     * @param int $roomId
     * @return mixed
     */
    function desk($isViewSql=false, $state=null,$roomId=0){
        $dao=$this->dao("lDesk");
        $dao->select('lab_desk.id');
        if(is_numeric($roomId) && $roomId > 0)$dao->where_eq('roomId',$roomId);
        $operate['deskBindDeviceBtn']=$this->ui->getDeskBindDeviceBtn();
        $operate['modifyBtn']=$this->ui->getModifyBtn();
        $operate['deleteBtn']=$this->ui->getDeleteBtn();
        $data= $this->table($dao,$isViewSql);
        $ids='';
        foreach($data['result'] as $row){
            $ids.=",{$row['deviceIds']}";
        }
        $ids=$this->formatIds($ids);
        $deviceDao=$this->dao('dRepertory')->viewCurSql($isViewSql);
        $device=$deviceDao->where_in('id',$ids)->getResult();
        $result=$device['result'];
        $formatResult=array();
        foreach($result as $row){
            $formatResult[$row['id']]=$row;
        }
        foreach($data['result'] as $key=>$row){
            $data['result'][$key]['deviceIds']=$this->findDeviceInArr($row['deviceIds'], $formatResult);
        }
        return $this->toHtmlArr($data,$operate,$this->ui->getAddBtn());
    }


    /**
     * 获取当前已绑定的设备 id
     * @param int $deskId
     * @return array|null
     */
    function getBindDevice($deskId=0){
        $deskResult=array();
        if(is_numeric($deskId) && $deskId > 0){
            $deskResult=$this->getResultById('lDesk',$deskId);
        }
        return isset($deskResult['deviceIds']) ? explode(',', $deskResult['deviceIds']) : $deskResult;
    }

    /**
     * 获取设备字符串
     * @param $ids
     * @param array $formatResult
     * @return string
     */
    private function findDeviceInArr($ids,array $formatResult){
        $ids=$this->formatIds($ids,2);
        $deviceArr=array();
        foreach($ids as $id){
            if(isset($formatResult["{$id}"]))$deviceArr[] = "{$formatResult["{$id}"]['num']}" ;
        }
        return implode('，',$deviceArr);
    }

    /**
     * 格式化id串
     * @param $ids
     * @param int $returnTye
     * @return array|string
     */
    private function formatIds($ids,$returnTye=1){
        $ids=explode(',',$ids);
        $ids=array_filter($ids);
        $ids=array_unique($ids);
        if($returnTye==2) return  $ids;
        $ids=implode(',',$ids);
        if($returnTye==1) return $ids;
    }

}
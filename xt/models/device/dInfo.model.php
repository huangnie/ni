<?php
/**
 *  User: huangnie
 */
require_once dirname(dirname(__FILE__)).'/info.model.php';
/**
 * 设备所有相关的数据视图.
 */
class DInfoModel extends InfoModel {

    function __construct(){

    }

    /**
     * 设备分类列表
     * @param bool $isViewSql
     * @param null $state
     * @return mixed
     */
    function category($isViewSql=false, $state=null){
        $dao = $this->dao('dCategory');
        $dao->select('device_category.id');
        $data= $this->table($dao,$isViewSql);
        $operate['deviceReserveBtn']=$this->ui->getDeviceReserveBtn();
        $operate['deviceRepertoryViewLink']=$this->ui->getDeviceRepertoryViewLink();
        $operate['modifyBtn']=$this->ui->getModifyBtn();
        $operate['deleteBtn']=$this->ui->getDeleteBtn();
        return $this->toHtmlArr($data,$operate,$this->ui->getAddBtn());
    }

    /**
     * 设备库存列表
     * @param bool $isViewSql
     * @param null $state
     * @param int $categoryId
     * @return mixed
     */
    function repertory($isViewSql=false, $state=null,$categoryId=0){
        $dao=$this->dao("dRepertory");
        $dao->left_join('device_category')->on_eq('device_category.id','device_repertory.categoryId');
        $dao->select('device_repertory.id');
        if(is_numeric($categoryId) && $categoryId > 0)$dao->where_eq('device_category.id',$categoryId);

        $dao->select('device_category.`chName`',2);
        $readFieldsExplain = array( 'device_category.`chName' =>array('value'=>'分类'),);

        $operate['modifyBtn']=$this->ui->getModifyBtn();
        $operate['deleteBtn']=$this->ui->getDeleteBtn();
        $data= $this->table($dao,$isViewSql,$readFieldsExplain);
        return $this->toHtmlArr($data,$operate,$this->ui->getAddBtn());
    }

    /**
     * 设备维修记录
     * @param bool $isViewSql
     * @param null $state
     * @return mixed
     */
    function repair($isViewSql=false, $state=null){
        $dao=$this->dao("dRepair");
        $dao->select('device_repair.id');
        $operate['modifyBtn']=$this->ui->getModifyBtn();
        $operate['deleteBtn']=$this->ui->getDeleteBtn();
        $data= $this->table($dao,$isViewSql);
        return $this->toHtmlArr($data,$operate,$this->ui->getAddBtn());
    }

    /**
     * 设备预约记录，取消记录，撤销记录
     * @param bool $isViewSql
     * @param null $state
     * @return mixed
     */
    function reserve($isViewSql=false, $state=null){
        $dao=$this->dao('dReserve')->select('device_reserve.id');
        $dao->left_join('device_category')->on_eq('device_reserve.categoryId','device_category.id');
        $dao=$this->identityDistinguish($dao);
        $dao->select(array('device_reserve.`id`','device_category.`num`'),2);
        $readFieldsExplain = array( 'device_reserve.`id' =>array('value'=>'预约号'), 'device_category.`num' =>array('value'=>'分类'),);
        $operate=array();
        $addition='';
        switch($state){
            case 2:
                $dao->left_join('device_cancel_repeal')->on_eq('device_cancel_repeal.reserveId','device_reserve.id');
                $dao->where_eq('device_reserve.state',$this->check->deviceCancelState());
                $readFieldsExplain3 = array( 'device_cancel_repeal.`theTime`' =>array('value'=>'取消时间'));
                $readFieldsExplain=$readFieldsExplain+$readFieldsExplain3;
                $dao->select('device_cancel_repeal.`theTime`' ,2);
                break;
            case 3:
                $dao->left_join('device_cancel_repeal')->on_eq('device_cancel_repeal.reserveId','device_reserve.id');
                $dao->where_eq('device_reserve.state',$this->check->deviceRepealState());
                $readFieldsExplain3 = array( 'device_cancel_repeal.`theTime`' =>array('value'=>'撤消时间'));
                $readFieldsExplain=$readFieldsExplain+$readFieldsExplain3;
                $dao->select('device_cancel_repeal.`theTime`' ,2);
                break;
            case 1:
            default:
                $dao->where_eq('device_reserve.state',$this->check->deviceReserveState());
                $operate['cancelBtn']=$this->ui->getCancelBtn();
                $operate['repealBtn']=$this->ui->getRepealBtn();
                $addition=$this->ui->getLendBtn();
                break;
        }
        $data= $this->table($dao,$isViewSql,$readFieldsExplain);
        return $this->toHtmlArr($data,$operate,$addition);
    }

    /**
     * 借出记录
     * @param bool $isViewSql
     * @param null $state
     * @return mixed
     */
    function lend($isViewSql=false, $state=null){
        $dao=$this->dao("dLendRenewRevert");
        $dao->left_join('device_reserve')->on_eq('device_reserve.id','device_lend_renew_revert.reserveId');

        $dao->left_join('device_repertory')->on_eq('device_repertory.id','device_lend_renew_revert.deviceId');
        $dao->select(array('device_repertory.`num`'),2);
        $readFieldsExplain = array( 'device_repertory.`num`' =>array('value'=>'设备编号'), );

        $dao=$this->identityDistinguish($dao);
        $dao->where_lt('device_lend_renew_revert.revertTime','device_lend_renew_revert.lendTime',true);
        $operate=array();
        switch($state){
            case 2:
                $dao->where_eq('renewDays',0); //未续借
                $operate['renewBtn']= $this->ui->getRenewBtn();
                break;
            case 3:
                $dao->where_lt('dayofyear(FROM_UNIXTIME(lendTime)) + renewDays',date('z',time()) - $this->maxLendDays ); //超期
                break;
            case 1:  //全部
            default:
                break;
        }
        $dao->select('device_reserve.id');
        $data= $this->table($dao,$isViewSql,$readFieldsExplain);
        $addition = $this->ui->getRevertBtn();
        return $this->toHtmlArr($data,$operate,$addition);
    }

    /**
     * 归还记录
     * @param bool $isViewSql
     * @param null $state
     * @return mixed
     */
    function revert($isViewSql=false, $state=null){
        $dao=$this->dao("dLendRenewRevert");
        $dao->left_join('device_reserve')->on_eq('device_reserve.id','device_lend_renew_revert.reserveId');

        $dao->left_join('device_repertory')->on_eq('device_repertory.id','device_lend_renew_revert.deviceId');
        $dao->select(array('device_repertory.`num`'),2);
        $readFieldsExplain = array( 'device_repertory.`num' =>array('value'=>'设备编号'), );

        $dao=$this->identityDistinguish($dao);
        $dao->where_gt('revertTime',0);
        switch($state){
            case 2:
                $dao->where_gt('renewDays',0); //续借
                break;
            case 3:
                $dao->where_gt('dayofyear(FROM_UNIXTIME(revertTime))', "dayofyear(FROM_UNIXTIME(lendTime)) + renewDays+{$this->maxLendDays}",true); //超期
                break;
            case 1:  //全部
            default:
                break;
        }
        $dao->select('device_reserve.id');
        $data= $this->table($dao,$isViewSql,$readFieldsExplain);
        return $this->toHtmlArr($data);
    }

    /**
     * 设备 id=>num 映射数组
     * @return array
     */
    function getDevice(){
        $dao=$this->dao('dRepertory');
        $dRepertory=$dao->getResult();
        $result=$dRepertory['result'];
        $newResult=array();
        foreach($result as $row){
            $newResult["{$row['id']}"]=$row['num'];
        }
        return $newResult;
    }

}
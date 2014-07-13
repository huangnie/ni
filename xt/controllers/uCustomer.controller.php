<?php
/**
 * desc controller Device
 */
include "user.controller.php";

/**
 * 普通用户操作控制类
 */
final class UCustomerController extends UserController{

	function __construct(){
        parent::__construct();
    }

    /**
     *  普通用户注册
     */
    function reg(){
        $model= $this->getActivityModel('u_customer_1');
        $rs=$model->add( $this->__PARAM('detail'),2);
        if(is_array($rs)){
            $data['editForm'] = $this->ui->getEditForm($rs['detail'],$rs['fieldsExplain']);
            $data['uri']=$rs['uri'];
            $this->displayForm('edit',$data);
        }
        elseif(is_bool($rs) && $rs==true){
            echo json_encode(array('state'=>1,'tip'=>'恭喜','content'=>'注册成功')); exit();
        }elseif(is_bool($rs) && $rs==false){
            echo json_encode(array('tip'=>'抱歉','content'=>'注册失败')); exit();
        }
        else{
            echo json_encode(array('state'=>0,'tip'=>'警告','content'=>$rs)); exit();
        }
    }

    /**
     * 预约设备
     * @param string $tds
     * @param string $categoryId
     */
    function reserveDevice($tds='d_category_1',$categoryId=''){
        if(!$this->check->isLogin()){
            $this->login();
        }elseif(!$this->check->isReservePower()){
            echo $this->operateTipJson('无预约操作权限');
        }else{
            $model=$this->getActivityModel($tds);
            $rs=$model->reserve($categoryId);
            echo $this->operateTipJson($rs,'预约');
            exit();
        }
    }

    /**
     * 预订耗材
     * @param string $tds
     * @param string $consumeId
     */
    function reserveConsume($tds='d_category_1',$consumeId=''){
        if(!$this->check->isLogin()){
            $this->login();
        }elseif(!$this->check->isReservePower()){
            echo $this->operateTipJson('无预约操作权限');
        }else{
            $editId=$this->__PARAM('editId','');
            if($editId == '' ||  $consumeId != $this->idDecode($editId)){
                $data['editForm'] = $this->ui->getConsumeReserveForm($this->idEncode($consumeId));
                $data['uri']=$_SERVER['REQUEST_URI'];
                $this->displayForm('edit',$data);
            }else{
                $theCount=$this->__PARAM('theCount',0);
                $model=$this->getActivityModel($tds);
                $rs=$model->reserve($consumeId,$theCount);
                echo $this->operateTipJson($rs,'预订');
                exit();
            }
        }
    }

    /**
     * 取消（预约还未审）
     * @param string $tds
     * @param        $reserveId
     */
    function cancel($tds='d_category_1',$reserveId){
        if(!$this->check->isLogin()){
            $this->login();
        }elseif(!$this->check->isCancelPower()){
            echo $this->operateTipJson('无取消操作权限');
        }else{
            $model=$this->getActivityModel($tds);
            $rs=$model->cancel($reserveId);
            echo $this->operateTipJson($rs,'取消');
            exit();
        }
    }

    /**
     * 续借设备
     * @param string $tds
     * @param        $reserveId
     */
    function renew($tds='d_category_1',$reserveId){
        if(!$this->check->isLogin()){
            $this->login();
        }elseif(!$this->check->isRenewPower()){
            echo $this->operateTipJson('无续借操作权限');
        }else{
            $model=$this->getActivityModel($tds);
            $rs = $model->renew($reserveId);
            echo $this->operateTipJson($rs,'续借');
            exit();
        }
    }

}
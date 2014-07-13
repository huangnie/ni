<?php
/**
 *  desc controller Device
 */
include "user.controller.php";
/**
 * 普通用户操作控制类
 */
final class UManagerController extends UserController{

	function __construct(){
        parent::__construct();
    }
	
	/**
     * 软件助手登录
     */
    function appLogin(){
	
		if($this->check->isLogin() && $this->check->isManager()) {
		
			echo '用户已登陆';  exit();
		}

        $userName=$this->__PARAM('name');
		$userPassword=$this->__PARAM('password');
		$userIdentity=$this->__PARAM('identity');
		if(!in_array($userIdentity,array('uCustomer','uManager'))){
			echo '请选择用户身份';  exit();
		}
		if($userName==''){
			echo '用户名不能为空';  exit();
		}
		if($userPassword==''){
			echo '密码不能为空';  exit();
		}
		$activity= $this->model('user_uActivity');
        $user=$activity->loginCheck($userIdentity,$userName,$userPassword);
        if($user==-1) {
            echo '用户不存在'; exit();
        }elseif(!$user){
            echo '密码错误'; exit();
        }else {
            $user['identity']=$userIdentity;
            $user['plainPassword']=$userPassword;
            $_SESSION['loginUser']=$user;
            echo '登录成功';
            exit();
        }
    }

    /**
     * 借出设备
     */
    function appLend(){
        if(!$this->check->isLogin()){
            echo $this->operateTipStr('用户未登录');
        }elseif(!$this->check->isLendPower()){
            echo $this->operateTipStr('无借出操作权限');
        }else{
            $code=$this->__PARAM('code','');
            $num=$this->__PARAM('num','');
            $model=$this->getActivityModel('d_reserve_1');
            $rs =$model->lend('code',$code,$num) ;
            echo  $this->operateTipStr($rs,"借出");
        }
        exit();
    }

    /**
     * 归还（取回）设备
     */
    function appRevert(){
        if(!$this->check->isLogin()){
            echo $this->operateTipStr('用户未登录');
        }elseif(!$this->check->isRevertPower()){
            echo $this->operateTipStr('无归还操作权限');
        }else{
            $code=$this->__PARAM('code','');
            $model=$this->getActivityModel('d_lend_1');
            $rs = $model->revert('code',$code);
            echo  $this->operateTipStr($rs,"归还");
        }
        exit();
    }

    /**
     * 撤销
     * @param string $tds
     * @param        $reserveId
     */
    function repeal($tds='d_category_1',$reserveId){
        if(!$this->check->isLogin()){
            $this->login();
        }elseif(!$this->check->isRepealPower()){
            echo $this->operateTipJson('无撤消操作权限');
        }else{
            $model=$this->getActivityModel($tds);
            $rs=$model->repeal($reserveId);
            echo $this->operateTipJson($rs,'撤销');
            exit();
        }
    }

    /**
     * 借出设备
     * @param string $tds
     * @param        $reserveId
     */
    function lend($tds='d_category_1',$reserveId=0){
        if(!$this->check->isLogin()){
            $this->login();
        }elseif(!$this->check->isLendPower()){
            echo $this->operateTipJson('无借出操作权限');
        }else{
            echo $this->lendOrRevert('lend','借出',$tds,$reserveId);
        }
        exit();
    }

    /**
     * 归还（取回）设备
     * @param string $tds
     * @param        $reserveId
     * @return bool
     */
    function revert($tds='d_category_1',$reserveId){
        if(!$this->check->isLogin()){
            $this->login();
        }elseif(!$this->check->isRevertPower()){
            echo $this->operateTipJson('无归还操作权限');
        }else{
            echo $this->lendOrRevert('revert','归还',$tds,$reserveId);
        }
        exit();
    }

    /**
     * 借出 和 归还 设备
     * @param string $func
     * @param string $name
     * @param string $tds
     * @param int $reserveId
     * @return string
     */
    private function lendOrRevert($func='',$name='',$tds='',$reserveId=0){
        if(!$this->check->isLogin()) $this->login();
        else{
            $editId=$this->__PARAM('editId','');
            if($editId == '' ||   $reserveId != $this->idDecode($editId)){
                $data['editForm'] =  $this->ui->getLendOrRevertForm( $this->idEncode($reserveId),$func);
                $data['uri']=$_SERVER['REQUEST_URI'];
                $this->displayForm('edit',$data);
            }else{
                $num=$this->__PARAM('num','');
                $select=$this->__PARAM('select','');
                $value=$this->__PARAM('value','');
                $model=$this->getActivityModel($tds);
                $rs =$func=='lend' ? $model->$func($select,$value,$num) : $model->$func($select,$value);
                echo  $this->operateTipJson($rs,"{$name}");
                exit();
            }
        }
    }

    /**
     * 售出耗材
     * @param string $tds
     * @param $reserveId
     * @return string
     */
    function sale($tds='d_category_1',$reserveId){
        if(!$this->check->isLogin()){
            $this->login();
        }elseif(!$this->check->isSalePower()){
            echo $this->operateTipJson('无售出操作权限');
        }else{
            $editId=$this->__PARAM('editId','');
            if($editId == '' ||  $reserveId != $this->idDecode($editId)){
                $data['editForm'] = $this->ui->getSaleForm(  $this->idEncode($reserveId) );
                $data['uri']=$_SERVER['REQUEST_URI'];
                $this->displayForm('edit',$data);
            }else{
                $num=$this->__PARAM('num',0);
                $model=$this->getActivityModel($tds);
                $rs = $model->sale($reserveId,$num);
                echo $this->operateTipJson($rs,'售出');
                exit();
            }
        }
    }

    /**
     * 回收（取回）耗材
     * @param string $tds
     * @param        $reserveId
     * @return bool
     */
    function recycle($tds='d_category_1',$reserveId){
        if(!$this->check->isLogin()){
            $this->login();
        }elseif(!$this->check->isRecyclePower()){
            echo $this->operateTipJson('无归还操作权限');
        }else{
            $editId=$this->__PARAM('editId','');
            if($editId == '' || $reserveId != $this->idDecode($editId)){
                $data['editForm'] = $this->ui->getRecycleForm($this->idEncode($reserveId));
                $data['uri']=$_SERVER['REQUEST_URI'];
                $this->displayForm('edit',$data);
            }else{
                $num=$this->__PARAM('num',0);
                $theCount=$this->__PARAM('theCount',0);
                $rate=$this->__PARAM('rate',0);
                $model=$this->getActivityModel($tds);
                $rs=$model->recycle($reserveId,$num,$theCount,$rate);
                echo $this->operateTipJson($rs,'回收');
                exit();
            }
        }
    }

    /**
     * 配置操作
     * @param $tds
     * @param int $deskId
     * @return string
     */
    function deskBindDevice($tds,$deskId=0){
        if(!$this->check->isLogin()){
            $this->login();
        }elseif(!$this->check->isAddPower()){
            echo $this->operateTipJson('无编辑操作权限');
        }else{
            if($tds != 'l_desk_1') return '操作有误';
            $editId=$this->__PARAM('editId','');
            if($editId == '' || $deskId != $this->idDecode($editId)){
                $this->formId=mt_rand(232413,434533);
                $dModel=$this->getInfoModel('d');
                $lModel=$this->getInfoModel('l');
                $data['editForm'] = $this->ui->getDeskBindDeviceForm($this->idEncode($editId), $dModel->getDevice(), $lModel->getBindDevice($deskId));
                $data['uri']=$_SERVER['REQUEST_URI'];
                $this->displayForm('edit',$data);
            }else{
                $model=$this->getActivityModel($tds);
                $rs=$model->deskBindDevice($deskId,$this->__PARAM('deviceId')); // deviceId 是 id 的一维数组
                echo $this->operateTipJson($rs,'配置');
                exit();
            }
        }
    }

    /**
     * 管理员重置任意用户的密码
     * @param $tds
     * @param int $userId
     * @return string
     */
    function resetPassword($tds,$userId=0){
        if(!$this->check->isLogin()){
            $this->login();
        }elseif(!$this->check->isModifyPower()){
            echo $this->operateTipJson('无修改操作权限');
        }else{
            $editId=$this->__PARAM('editId','');
            if($editId == '' ||  $userId != $this->idDecode($editId)){
                $data['editForm'] =  $this->ui->getResetPasswordForm($this->idEncode($userId));
                $data['uri']=$_SERVER['REQUEST_URI'];
                $this->displayForm('edit',$data);
            }else{
                $newPassword=$this->__PARAM('newPassword','');
                if($newPassword=='') {
                    echo json_encode(array('tip'=>'失败','status'=>0,'content'=>'输入不能为空'));
                    exit();
                }
                $model=$this->getActivityModel($tds);
                $rs = $model->resetPassword($userId,$newPassword);
                echo  $this->operateTipJson($rs, "密码修改");
                exit();
            }
        }
    }

    /**
     * 配置管理员权限
     * @param $tds
     * @param int $mangerId
     */
    function  managerPowerConfig($tds,$mangerId=0){
        if(!$this->check->isLogin()){
            $this->login();
        }elseif(!$this->check->isModifyPower()){
            echo $this->operateTipJson('无修改操作权限');
        }else{
            if('u_manager_1' != $tds || $mangerId==0) echo json_encode(array('tip'=>'警告','status'=>0,'content'=>'请求失败'));
            $editId=$this->__PARAM('editId','');
            if($editId == '' ||  $mangerId != $this->idDecode($editId)){
                $oldPower = $this->getInfoModel('u')->getManagerPower($mangerId);
                $data['editForm'] = $this->ui->getManagerPowerConfigForm($this->idEncode($mangerId),$oldPower);
                $data['uri']=$_SERVER['REQUEST_URI'];
                $this->displayForm('edit',$data);
            }else{
                $power=$this->__PARAM('power');
                $model=$this->getActivityModel($tds);
                $rs=$model->managerPowerConfig($mangerId,$power);
                echo $this->operateTipJson($rs,'配置');
                exit();
            }
        }
    }

    function managerHelperConfig($tds){
        $model=$this->getActivityModel($tds);
        $model->managerHelperConfig();
    }

    /**
     * 添加信息
     * @param string $tds
     */
    function add($tds='d_category_1'){
        if(!$this->check->isLogin()){
            $this->login();
        }elseif(!$this->check->isAddPower()){
            echo $this->operateTipJson('无添加操作权限');
        }else{
            $model=$this->getActivityModel($tds);
            $rs=$model->add($this->__PARAM('detail'));
            if(is_array($rs)){  // 不要 count($rs) > 0 判断
                $data['editForm'] = $this->ui->getEditForm($rs['detail'],$rs['fieldsExplain']);
                $data['uri']=$rs['uri'];
                $this->displayForm('edit',$data);
            }else{
                echo $this->operateTipJson($rs,'添加');
                exit();
            }
        }
    }

    /**
     * 修改信息
     * @param string $tds
     * @param int    $id
     */
    function modify($tds='d_category_1',$id=0){
        if(!$this->check->isLogin()){
            $this->login();
        }elseif(!$this->check->isModifyPower()){
            echo $this->operateTipJson('无修改操作权限');
        }else{
            $model=$this->getActivityModel($tds);
            $rs=$model->modify($this->__PARAM('detail'),$id,1);
            if(is_array($rs)){
                $data['editForm'] = $this->ui->getEditForm($rs['detail'],$rs['fieldsExplain']);
                $data['uri']=$rs['uri'];
                $this->displayForm('edit',$data);
            }else{
                echo $this->operateTipJson($rs,'修改');
                exit();
            }
        }
    }

    /**
     * 永久删除
     * @param string $tds
     * @param int    $id
     * @return string
     */
    function delete($tds='d_category_1',$id=0){
        if(!$this->check->isLogin()){
            $this->login();
        }elseif(!$this->check->isDeletePower()){
            echo $this->operateTipJson('无删除操作权限');
        }else{
            $model=$this->getActivityModel($tds);
            $rs=$model->delete($id);
            echo $this->operateTipJson($rs,'永久删除');
            exit();
        }
    }


}
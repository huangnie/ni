<?php
/**
 *  User: huangnie
 */
require_once dirname(dirname(__FILE__)).'/info.model.php';
/**
 * 系统（参与者信息）
 */
class UInfoModel extends InfoModel {

    function __construct(){

    }

    /**
     * 操作日志列表
     * @param bool $isViewSql
     * @param null $state
     * @return mixed
     */
    function log($isViewSql=false, $state=null){
        $dao=$this->dao("uLog");
        $dao->select('user_log.id');
        $operate['modifyBtn']=$this->ui->getModifyBtn();
        $operate['deleteBtn']=$this->ui->getDeleteBtn();
        $data= $this->table($dao,$isViewSql);
        return $this->toHtmlArr($data,$operate,$this->ui->getAddBtn());
    }

    /**
     * 用户群列表
     * @param bool $isViewSql
     * @param null $state
     * @return mixed
     */
    function group($isViewSql=false, $state=null){
        $dao=$this->dao("uGroup");
        $dao->select('user_group.id');
        $operate['modifyBtn']=$this->ui->getModifyBtn();
        $operate['deleteBtn']=$this->ui->getDeleteBtn();
        $data= $this->table($dao,$isViewSql);
        return $this->toHtmlArr($data,$operate,$this->ui->getAddBtn());
    }

    /**
     * 普通用户列表
     * @param bool $isViewSql
     * @param null $state
     * @return mixed
     */
    function customer($isViewSql=false, $state=null){
        $dao=$this->dao("uCustomer");
        $dao->select('user_customer.id');
        $operate['resetPasswordBtn']=$this->ui->getResetPasswordBtn();
        $operate['modifyBtn']=$this->ui->getModifyBtn();
        $operate['deleteBtn']=$this->ui->getDeleteBtn();
        $data= $this->table($dao,$isViewSql);
        return $this->toHtmlArr($data,$operate,$this->ui->getAddBtn());
    }

    /**
     * 管理员列表
     * @param bool $isViewSql
     * @param null $state
     * @return mixed
     */
    function manager($isViewSql=false, $state=null){
        $dao=$this->dao("uManager");
        $dao->select('user_manager.id');
        if($this->check->isConfigPower())$operate['managerPowerConfigBtn']=$this->ui-> getManagerPowerConfigBtn();
        $operate['resetPasswordBtn']=$this->ui->getResetPasswordBtn();
        $operate['modifyBtn']=$this->ui->getModifyBtn();
        $operate['deleteBtn']=$this->ui->getDeleteBtn();
        $data= $this->table($dao,$isViewSql);
        $managerPower=array('配置'=>0,'删除'=>0,'修改'=>0,'添加'=>0,'回收'=>0,'出售'=>0,'归还'=>0,'借出'=>0,'撤销'=>0,'查询'=>1);
        foreach($data['result'] as &$row){
            $power=$row['power'];
            $index=0;
            foreach($managerPower as $key=>$value){
                $realValue=substr($power,$index,1);
                if($realValue == 1) $managerPower["{$key}"]=1;
                else unset( $managerPower["{$key}"]);
                $index++;
            }
            $row['power']=implode('，', array_keys($managerPower));
        }
        return $this->toHtmlArr($data,$operate,$this->ui->getAddBtn());
    }

    /**
     * 获取管理员权限值
     * @param int $managerId
     * @return string
     */
    function getManagerPower($managerId=0){
        $managerPower=array('config'=>0,'delete'=>0,'modify'=>0,'add'=>0,'recycle'=>0,'sale'=>0,'revert'=>0,'lend'=>0,'repeal'=>1,'read'=>1);
        if(is_numeric($managerId) && $managerId > 0){
            $manager=$this->dao('uManager')->where_eq('id',$managerId)->getResult(2);
            $managerResult=$this->getResultById('uManager',$managerId);
            $oldPower = isset($managerResult['power']) ? $managerResult['power'] : '0000000011';
            $index=0;
            foreach($managerPower as $key=>$value){
                $managerPower["{$key}"]=substr($oldPower,$index,1);
                $index++;
            }
        }
        return $managerPower;
    }

}
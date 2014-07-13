<?php
/**
 *  User: huangnie
 */
require_once dirname(dirname(__FILE__)).'/activity.model.php';
/**
 * 系统（参与者管理）
 */
class UActivityModel extends ActivityModel {

    private static $managerHelperConfigFile='xt/config/managerHelper.config.xml';

    function __construct(){
        parent::__construct();
    }

    /**
     * 登录验证
     * @param $userIdentity
     * @param $userName
     * @param $userPassword
     * @param bool $isViewSql
     * @return int
     */
    function loginCheck($userIdentity,$userName,$userPassword,$isViewSql=false){
        $dao= $this->dao($userIdentity);
        $dao->where_eq('chName',$userName);
        $user=$dao->viewCurSql($isViewSql)->getResult(2);
        if(is_array($user['result']) && count($user['result']) > 0){
            if($user['result']['password'] == md5($userName.$userPassword)){
                $event="成功登陆系统";
                $this->addLog($user['result']['num'], $user['result']['chName'] ,"{$event}");
                return $user;
            }
        }else{
            return -1;
        }
    }

    /**
     * 修改密码
     * @param $userId
     * @param $oldPassword
     * @param $newPassword
     * @return string
     */
    function changePassword($userId,$oldPassword,$newPassword){
        $curDaoName =$this->check->getUserIdentity();
        if($userId == $this->check->getUserId()){
            $result=$this->getResultById($curDaoName,$userId);
            if(!is_array($result) || count($result) <= 0 || $result['password'] != md5($result['chName'].$oldPassword)) return '原密码有误';
            $newPassword=md5($result['chName'].$newPassword);
            return $this->dao($curDaoName)->setPassword($newPassword)->where_eq('id',$userId)->modify();
        }else{
            return '你无权修改他人密码！！';
        }
    }

    /**
     * 重置密码
     * @param $userId
     * @param $newPassword
     * @return string
     */
    function resetPassword($userId,$newPassword){
        $curDaoName = $this->getCurDaoName();
        $result=$this->getResultById($curDaoName, $userId);
        if(!is_array($result) || count($result) <= 0 ) return '该用户不存在 ';
        $newPassword=md5($result['chName'].$newPassword);
        return $this->dao($curDaoName)->setPassword($newPassword)->where_eq('id',$userId)->modify();
    }

    /**
	 * 管理员权限配置
     * @param int $managerId
     * @param $power
     * @return bool|string
     */
    function managerPowerConfig($managerId=0,$power){
        if(!is_array($power) || count($power) == 0) return '操作有误';
        $managerPower=array('config'=>0,'delete'=>0,'modify'=>0,'add'=>0,'recycle'=>0,'sale'=>0,'revert'=>0,'lend'=>0,'repeal'=>0,'read'=>1);
        foreach($power as $key=>$value){
            if($value) $managerPower[$key]=1;
        }
        $power=implode('',$managerPower);
        if($this->check->binCheck($power) && strlen($power) == 10){
            $managerDao=$this->dao('uManager')->setPower($power);
            if($managerDao->where_eq('id',$managerId)->modify()){
                if($this->check->isManager() && $managerId == $this->check->getUserId()) $this->updateLoginUser();
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 更新用户登录信息
     * @param bool $isViewSql
     * @return int
     */
    function updateLoginUser($isViewSql=false){
        $user=$this->dao($this->check->getUserIdentity())->where_eq('id',$this->check->getUserId())->getResult(2);
        $user['identity']=$this->check->getUserIdentity();
        $user['plainPassword']=$this->check->getUserPlainPassword();
        $_SESSION['loginUser']=$user;
        $this->check->init();
    }

    function managerHelperConfig(){
        self::$managerHelperConfigFile = PROJECT_DIR.DIRECTORY_SEPARATOR.self::$managerHelperConfigFile;
        $configFile=simplexml_load_file(self::$managerHelperConfigFile);
        print_r($configFile);

        $configFile->param[1]->name='huangnie';

        print_r($configFile);

        $xml=$configFile->saveXML( self::$managerHelperConfigFile);

    }

}
<?php
/**
 * 用户权限，设备和耗材状态验证
 * 用户信息管理
 */
class CheckLib {

    /**
     * 用户登录信息
     * @var null
     */
    private $loginUser=null;

    function __construct(){
		session_start();
       $this->init();
    }

    /**
     * 初始化
     */
    function init(){
        if(isset($_SESSION['loginUser'])){
            $this->loginUser=$_SESSION['loginUser'];
        }
    }

    /**
     * 注销登录信息
     * @return bool
     */
    function logout(){
        session_destroy();
    }

    /**
     * 登录验证
     * @return bool
     */
    function isLogin(){
        return $this->loginUser != null;
    }

    /********     用户信息   ********/

    /**
     * 获取登录密码
     * @return bool
     */
    function getUserPlainPassword(){
        if(!$this->isLogin()) return -1;
        return $this->loginUser['plainPassword'];
    }

    /**
     * 获取登录时间
     * @return bool
     */
    function getUserLoginTime(){
        if(!$this->isLogin()) return -1;
        $visitCount = $this->loginUser['visitCount'];
        $visitTime = $this->loginUser['visitTime'];
        return $visitTime[$visitCount];
    }

    /**
     *  获取身份
     * @return int|string
     */
    function getUserIdentity(){
        if(!$this->isLogin()) return -1;
        return isset( $this->loginUser['identity']) ?  $this->loginUser['identity'] : 'customer';
    }

    /**
     * 获取登录用户的资料（个人信息）
     * @return bool
     */
    function getUser(){
        if(!$this->isLogin()) return -1;
        return isset($this->loginUser['result']) ? $this->loginUser['result'] : array();
    }

    /**
     * 获取用户 id
     * @return bool
     */
    function getUserId(){
        if(!$this->isLogin()) return -1;
        $userInfo=$this->getUser();
        return isset($userInfo['id']) ? $userInfo['id'] : 0;
    }

    /**
     *  获取权限
     * @return int|string
     */
    function getUserPower(){
        if(!$this->isLogin()) return -1;
        $userInfo=$this->getUser();
        return isset($userInfo['power']) ? $userInfo['power'] : '0000000000';
    }

    /**
     * 获取用户名
     * @param int $type
     * @return int|string
     */
    function getUserName($type=1){
        if(!$this->isLogin()) return -1;
        $userInfo=$this->getUser();
        switch($type){
            case 2:
                return isset($userInfo['enName']) ? $userInfo['chName'] : '';
                break;
            case 1:
            default:
                return isset($userInfo['chName']) ? $userInfo['chName'] : '同学';
                break;
        }
    }

    /**
     * 获取用户密码
     * @return int|string
     */
    function getUserPassword(){
        if(!$this->isLogin()) return -1;
        $userInfo=$this->getUser();
        return isset($userInfo['password']) ? $userInfo['password'] : '******';
    }

    /**
     * 获取用户账户余额
     * @return int|string
     */
    function getUserMoney(){
        if(!$this->isLogin()) return -1;
        $userInfo=$this->getUser();
        return isset($userInfo['money']) ? $userInfo['money'] : 0;
    }

    /**
     * 获取用户级别
     * @return int|string
     */
    function getUserLevel(){
        if(!$this->isLogin()) return -1;
        $userInfo=$this->getUser();
        return isset($userInfo['level']) ?  $userInfo['level'] : '成员';
    }

    /**
     * 获取用户编号
     * @return int|string
     */
    function getUserNum(){
        if(!$this->isLogin()) return -1;
        $userInfo=$this->getUser();
        return isset( $userInfo['num']) ? $userInfo['num'] : '000000';
    }

    /****     权限验证         ****/

    /**
     * 普通用户验证
     * @return bool
     */
    function isCustomer(){
        if(!$this->isLogin()) return -1;
        return strlen($this->getUserPower()) <= 4;  // 普通用户的 power 长度为4
    }

    /**
     * 管理员验证
     * @return bool
     */
    function isManager(){
        if(!$this->isLogin()) return -1;
        return strlen($this->getUserPower()) > 4; // 普通用户的 power 长度为 10
    }

    /**
     * 组长级验证
     * @return bool
     */
    function isHeadman(){
        return $this->getUserLevel()=='组长';
    }

    /**
     * 成员级验证
     * @return bool
     */
    function isMember(){
        return $this->getUserLevel()=='成员';
    }

    /**
     * 验证查阅数据的权限
     * @return bool
     */
    function isReadPower(){
        if(!$this->isLogin()) return -1;
        return (bindec($this->getUserPower()) & bindec('1')) > 0;
    }

    /**
     * 验证用户 预约或预定操作的权限
     * @return bool
     */
    function isReservePower(){
        if(!$this->isLogin()) return -1;
        return $this->isCustomer() && (bindec($this->getUserPower()) & bindec('0010')) > 0;
    }

    /**
     * 验证用户 取消操作的权限
     * @return bool
     */
    function isCancelPower(){
        if(!$this->isLogin()) return -1;
        return $this->isCustomer() && (bindec($this->getUserPower()) & bindec('0100')) > 0;
    }

    /**
     * 验证用户 续借设备的权限
     * @return bool
     */
    function isRenewPower(){
        if(!$this->isLogin()) return -1;
        return $this->isCustomer() && (bindec($this->getUserPower()) & bindec('1000')) > 0;
    }

    /**
     * 验证管理员 撤消操作的权限
     * @return bool
     */
    function isRepealPower(){
        if(!$this->isLogin()) return -1;
        return $this->isManager() && (bindec($this->getUserPower()) & bindec('0000000010')) > 0;
    }

    /**
     * 验证管理员 借出设备的权限
     * @return bool
     */
    function isLendPower(){
        if(!$this->isLogin()) return -1;
        return $this->isManager() && (bindec($this->getUserPower()) & bindec('0000000100')) > 0;
    }

    /**
     * 验证管理员 回收设备权限
     * @return bool
     */
    function isRevertPower(){
        if(!$this->isLogin()) return -1;
        return $this->isManager() && (bindec($this->getUserPower()) & bindec('0000001000')) > 0;
    }

    /**
     * 验证管理员 售出耗材的权限
     * @return bool
     */
    function isSalePower(){
        if(!$this->isLogin()) return -1;
        return $this->isManager() && (bindec($this->getUserPower()) & bindec('0000010000')) > 0;
    }

    /**
     * 验证管理员 回收耗材的权限
     * @return bool
     */
    function isRecyclePower(){
        if(!$this->isLogin()) return -1;
        return $this->isManager() && (bindec($this->getUserPower()) & bindec('0000100000')) > 0;
    }

    /**
     * 验证管理员 添加操作的权限
     * @return bool
     */
    function isAddPower(){
        if(!$this->isLogin()) return -1;
        return $this->isManager() && (bindec($this->getUserPower()) & bindec('0001000000')) > 0;
    }

    /**
     * 验证管理员 修改操作的权限
     * @return bool
     */
    function isModifyPower(){
        if(!$this->isLogin()) return -1;
        return $this->isManager() && (bindec($this->getUserPower()) & bindec('0010000000')) > 0;
    }

    /**
     * 验证管理员 删除操作的权限
     * @return bool
     */
    function isDeletePower(){
        if(!$this->isLogin()) return -1;
        return $this->isManager() && (bindec($this->getUserPower()) & bindec('0100000000')) > 0;
    }


    function isConfigPower(){
        if(!$this->isLogin()) return -1;
        return $this->isManager() && (bindec($this->getUserPower()) & bindec('1000000000')) > 0;
    }


    /******      状态验证       *******/

    function getDCState($stateValue=''){
        if(strlen($stateValue) == 5 || $stateValue=='在售' || $stateValue=='屏蔽') return $this->getConsumeState($stateValue);
        else if(strlen($stateValue) == 6 || $stateValue=='在库' || $stateValue=='借出' || $stateValue=='屏蔽') return  $this->getDeviceState($stateValue);
    }

    /**
     * 耗材状态
     * @param string $stateValue
     * @return string
     */
    function getConsumeState($stateValue=''){
        $consumeStateArr=array(
            '00001'=>'预订',
            '00011'=>'取消',
            '00101'=>'撤消',
            '01001'=>'售出',
            '11001'=>'回收',
            '在售'=>'在售',
            '屏蔽'=>'屏蔽'
        );
        if(in_array($stateValue,array_keys($consumeStateArr))) return $consumeStateArr["{$stateValue}"];
        else return '未知';
    }

    /**
     * 设备状态
     * @param string $stateValue
     * @return string
     */
    function getDeviceState($stateValue=''){
        $deviceStateArr=array(
            '000001'=>'预约',
            '000011'=>'取消',
            '000101'=>'撤消',
            '001001'=>'借出',
            '011001'=>'续借',
            '101001'=>'归还',
            '111001'=>'归还',
            '在库'=>'在库',
            '借出'=>'借出',
            '屏蔽'=>'屏蔽'
        );
        if(in_array($stateValue,array_keys($deviceStateArr))) return $deviceStateArr["{$stateValue}"];
        else return '未知';
    }

    /***       耗材         *****/

    /**
     * 耗材预约状态(待审批)
     * @param string $binStr
     * @return bool|string
     */
    function consumeReserveState($binStr=''){
        $stateValue='00001';
        return $binStr=='' ? "{$stateValue}" : (bindec($binStr) ^ bindec("{$stateValue}")) == 0;
    }

    /**
     * 耗材取消状态
     * @param string $binStr
     * @return bool|string
     */
    function consumeCancelState($binStr=''){
        $stateValue='00011';
        return $binStr=='' ? "{$stateValue}" : (bindec($binStr) ^ bindec("{$stateValue}")) == 0;
    }

    /**
     * 耗材撤销状态
     * @param string $binStr
     * @return bool|string
     */
    function consumeRepealState($binStr=''){
        $stateValue='00101';
        return $binStr=='' ? "{$stateValue}" : (bindec($binStr) ^ bindec("{$stateValue}")) == 0;
    }

    /**
     * 耗材售出状态
     * @param string $binStr
     * @return bool|string
     */
    function saleState($binStr=''){
        $stateValue='01001';
        return $binStr=='' ? "{$stateValue}" : (bindec($binStr) ^ bindec("{$stateValue}")) == 0;
    }

    /**
     * 耗材回收状态
     * @param string $binStr
     * @return bool|string
     */
    function recycleState($binStr=''){
        $stateValue='11001';
        return $binStr=='' ? "{$stateValue}" : (bindec($binStr) ^ bindec("{$stateValue}")) == 0;
    }

    /***       设备         *****/

    /**
     * 设备预约状态(待审批)
     * @param string $binStr
     * @return bool|string
     */
    function deviceReserveState($binStr=''){
        $stateValue='000001';
        return $binStr=='' ? "{$stateValue}" : (bindec($binStr) ^ bindec("{$stateValue}")) == 0;
    }

    /**
     * 设备取消状态
     * @param string $binStr
     * @return bool|string
     */
    function deviceCancelState($binStr=''){
        $stateValue='000011';
        return $binStr=='' ? "{$stateValue}" : (bindec($binStr) ^ bindec("{$stateValue}")) == 0;
    }

    /**
     * 设备撤销状态
     * @param string $binStr
     * @return bool|string
     */
    function deviceRepealState($binStr=''){
        $stateValue='000101';
        return $binStr=='' ? "{$stateValue}" : (bindec($binStr) ^ bindec("{$stateValue}")) == 0;
    }

    /**
     * 设备续借状态
     * @param string $binStr
     * @return bool|string
     */
    function renewState($binStr=''){
        $stateValue='011001';
        return $binStr=='' ? "{$stateValue}" : (bindec($binStr) ^ bindec("{$stateValue}")) == 0;
    }

    /**
	 * 设备是否预约
     * @param string $binStr
     * @return bool|string
     */
    function isRenew($binStr=''){
        $stateValue='010000';
        return $binStr=='' ? "{$stateValue}" : (bindec($binStr) & bindec("{$stateValue}")) > 0;
    }

    /**设备借出状态
     * @param string $binStr
     * @return bool|string
     */
    function lendState($binStr=''){
        if($binStr=='' || $binStr==1) return '001001';  //未还，未续借的
        else if($binStr==2) return '011001';  //未还，已续借的
        else{
            if($this->isRenew($binStr)) return (bindec($binStr) ^ bindec('011001')) == 0;
            else  return (bindec($binStr) ^ bindec('001001')) == 0;
        }
    }

    /**
     * 设备归还状态
     * @param string $binStr
     * @return bool|string
     */
    function revertState($binStr=''){
        if($binStr=='' || $binStr==1) return '101001';  //未续借，归还
        else if($binStr==2) return '111001';  //已续借，归还
        else{
            if($this->isRenew($binStr)) return (bindec($binStr) ^ bindec('111001')) == 0;
            else  return (bindec($binStr) ^ bindec('101001')) == 0;
        }
    }

    /**
     * 二进制验证
     * @param string $str
     * @return bool
     */
    function binCheck($str=''){
        $str=trim($str);
        if(strlen($str)==0) return false;
        return strlen($str) == (substr_count($str,'0')+substr_count($str,'1'));
    }
}
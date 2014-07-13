<?php
/**
 *  User: huangnie
 */
require_once dirname(dirname(__FILE__)).'/activity.model.php';
require_once dirname(__FILE__)  . '/device.interface.php';
/**
 * 设备业务逻辑层，即与设备相关的工作流（功能服务）
 */
class DActivityModel extends ActivityModel implements DeviceInterface {

    function __construct(){
        parent::__construct();
    }

    /**
     * 预约设备操作
     * @param int $categoryId
     * @return mixed|string
     */
    function reserve($categoryId=0){
        if(!is_numeric($categoryId) || $categoryId < 0) return '请求失败1';
        $categoryResult=$this->getResultById('dCategory',$categoryId);
        if(!is_array($categoryResult) || count($categoryResult) == 0) return '请求失败2';
        if(intval($categoryResult['reserveCount']) >= intval($categoryResult['theCount'])) return '预约人数已满';
		
        $dReserveDao=$this->dao('dReserve');
		$dReserve=$dReserveDao->where_eq('customerId',$this->check->getUserId())
				->where_eq('categoryId',$categoryId)
				->where_eq('state',$this->check->deviceReserveState())
				->getResult(2);
		$dReserveResult=$dReserve['result'];
		
		if(is_array($dReserveResult) && count($dReserveResult) > 0) return '你已预约该设备，还未领取';
		
		
		//新增预约
        $dReserveDao->setCustomerId($this->check->getUserId());
        $dReserveDao->setState($this->check->deviceReserveState());
        $dReserveDao->setCategoryId($categoryId);
        $dReserveDao->setTheTime(time());
        $dReserveDao->trans();
        $reserveId = $dReserveDao->add();
        if(!is_numeric($reserveId) || $reserveId == 0)  return $reserveId;

        //更新数量
        $categoryDao=$this->dao('dCategory');
        $categoryDao->setReserveCount($categoryResult['reserveCount']+1);
        $rs=$categoryDao->where_eq('id',$categoryId)->modify();
        if(!is_bool($rs) || $rs==false){
            $dReserveDao->rollback();
        }
        else{
            $dReserveDao->commit();
            $event="预约设备 - {$categoryResult['chName']}， 预约号：{$reserveId}";
            $this->addLog($this->check->getUserNum(),$this->check->getUserName(),"{$event}");
        }
        return $rs;
    }

    /**
     * 取 消操作
     * @param int $reserveId
     * @param bool $isViewSql
     * @return bool|string
     */
    function cancel($reserveId=0,$isViewSql=false){
        return $this->cancelRepealOperate('取消',$reserveId,$this->check->deviceCancelState(),$isViewSql);
    }

    /**
     * 撤 消操作
     * @param int $reserveId
     * @param bool $isViewSql
     * @return bool|string
     */
    function repeal($reserveId=0,$isViewSql=false){
        return $this->cancelRepealOperate('撤消',$reserveId,$this->check->deviceRepealState(),$isViewSql,$this->check->getUserId());
    }

    /**
     * 取消 或撤消
     * @param $name
     * @param int $reserveId
     * @param $stateValue
     * @param bool $isViewSql
     * @param $managerId
     * @return bool|string
     */
    private function cancelRepealOperate($name,$reserveId=0,$stateValue,$isViewSql=false,$managerId=-1){
        if(!is_numeric($reserveId)|| $reserveId < 0)  return '请求失败1';
        $dReserveResult=$this->getResultById('dReserve',$reserveId);
        if(!is_array($dReserveResult) || count($dReserveResult) == 0) return '请求失败2';
        if(!$this->check->deviceReserveState($dReserveResult['state'])) return '不是待审批记录';
        if($this->check->isCustomer() && $this->check->getUserId() != $dReserveResult['customerId']) return '你不能取消他人的预约';
        if($this->check->isCustomer() && $this->check->deviceRepealState() == $stateValue) return '普通用户无撤消权限';
        $dCancelRepealDao=$this->dao('dCancelRepeal');
        $dCancelRepealDao->setReserveId($reserveId)->setTheTime(time());
        if($managerId>0) $dCancelRepealDao->setManagerId($managerId);
        $dCancelRepealDao->trans();
        $rs = $dCancelRepealDao->add();
        $event="{$name}了预约， 预约号：{$reserveId}";
        return $this->changeDeviceState($rs,$reserveId,$stateValue,$dCancelRepealDao,$event);
    }

    /**借出设备操作
     * @param string $field
     * @param string $value
     * @param string $customerNum
     * @param bool $isViewSql
     * @return bool|mixed|string
     */
    function lend($field='',$value='',$customerNum='',$isViewSql=false){
        if($customerNum=='') return '用户编号不能为空';
        $uCustomerDao=$this->dao('uCustomer');
        $uCustomer=$uCustomerDao->where_eq('num',$customerNum)->getResult(2);
        if($uCustomer['result']['num'] != $customerNum) return '用户编号错误';
        //
        $device=$this->dRepertoryCheck($field,$value,'在库');
        if(!is_array($device) || count($device) == 0) return $device;


        $dReserveDao=$this->dao('dReserve');
		$dReserve=$dReserveDao->where_eq('customerId',$uCustomer['result']['id'])
				->where_eq('categoryId',$device['categoryId'])
				->where_eq('state',$this->check->deviceReserveState())
				->getResult(2);
		$dReserveResult=$dReserve['result'];
		
		if(!is_array($dReserveResult) || count($dReserveResult) == 0) return "你未预约该类设备（{$value}）";
        $reserveId=$dReserveResult['id'];
		
		//添加借出记录
        $dLendRenewRevertDao=$this->dao('dLendRenewRevert');
        $dLendRenewRevertDao->setReserveId($reserveId)->setLenderId($this->check->getUserId())->setDeviceId($device['id'])->setLendTime(time());
        $dLendRenewRevertDao->trans();
        $rs=$dLendRenewRevertDao->viewCurSql($isViewSql)->add();
        if(!is_numeric($rs) || $rs <= 0){
            $dLendRenewRevertDao->rollback();
            return $rs;
        }else{
			//更新状态
            $dRepertoryDao=$this->dao('dRepertory');
            $dRepertoryDao->trans();
            $dRepertoryDao->setState('借出');
            $rs2 = $dRepertoryDao->where_eq('id',$device['id'])->modify();
            $event="借出设备{$device['num']}, 预约号：{$reserveId}";
            return $this->changeDeviceState($rs2,$reserveId,$this->check->lendState(),$dRepertoryDao, $event);
        }
    }

    /**
     * 续借设备操作
     * @param int $reserveId
     * @return bool|string
     */
    function renew($reserveId=0){
        if(!is_numeric($reserveId)|| $reserveId < 0) return '请求失败';
        $dReserveResult=$this->getResultById('dReserve',$reserveId);
        if(!is_array($dReserveResult) || count($dReserveResult) == 0)  return '请求失败';
        if( !$this->check->lendState($dReserveResult['state'])) return '不满足续借条件';
        if( $this->check->renewState($dReserveResult['state'])) return '之前已续借过了';
        $dLendRenewRevertDao=$this->dao('dLendRenewRevert');
        $dLendRenewRevert=$dLendRenewRevertDao->where_eq('reserveId',$reserveId)->getResult(2);
        if( (date('z',time()) -date('z',$dLendRenewRevert['result']['lendTime']) - $dLendRenewRevert['result']['renewDays']) >  $this->maxLendDays ) return '已经超期了';
        $dLendRenewRevertDao->setRenewDays($this->maxRenewDays);
        $rs = $dLendRenewRevertDao->trans()->where_eq('reserveId',$reserveId)->modify();
        $dLendRenewRevertResult=$this->getResultByReserveId('dLendRenewRevert',$reserveId);
        $event="续借设备{$dLendRenewRevertResult['num']}, 预约号：{$reserveId}";
        return $this->changeDeviceState($rs,$reserveId,$this->check->renewState(),$dLendRenewRevertDao,$event);
    }

    /**
     * 归还设备操作
     * @param string $field
     * @param string $value
     * @param bool $isViewSql
     * @return bool|mixed|string
     */
    function revert($field='',$value='',$isViewSql=false){
        $device=$this->dRepertoryCheck($field,$value,'借出');
        if(!is_array($device) || count($device) == 0) return $device;

        $dLendRenewRevertDao=$this->dao('dLendRenewRevert');
        $dLendRenewRevert = $dLendRenewRevertDao->where_eq('deviceId',$device['id'])->where_lt('revertTime','lendTime',2)->getResult(2);
        $dLendRenewRevertResult=$dLendRenewRevert['result'];
        //借出时未登记， 要禁止该境况
        if(!is_array($dLendRenewRevertResult) || count($dLendRenewRevertResult) == 0){
            $dRepertoryDao=$this->dao('dRepertory');
            $dRepertoryDao->setState('在库');
            return $dRepertoryDao->where_eq('id',$device['id'])->modify();
        }

        //借出时有登记
        $reserveId=$dLendRenewRevertResult['reserveId'];
        $reserveResult=$this->getResultById('dReserve',$reserveId);

        //预约记录被损坏的情况， 要禁止该情况
        if(!is_array($reserveResult) || count($reserveResult) == 0){
            $dRepertoryDao=$this->dao('dRepertory');
            $dRepertoryDao->setState('在库');
            return $dRepertoryDao->where_eq('id',$device['id'])->modify();
        }

        $dLendRenewRevertDao->setReverterId($this->check->getUserId())->setRevertTime(time());
        $dLendRenewRevertDao->trans();
        $rs=$dLendRenewRevertDao->viewCurSql($isViewSql)->where_eq('reserveId',$reserveId)->modify();

        if($rs){
            $dRepertoryDao=$this->dao('dRepertory');
            $dRepertoryDao->setState('在库');
            $rs2 = $dRepertoryDao->where_eq('id',$device['id'])->modify();
            $event="归还设备 {$device['num']} 预约号：{$reserveId}";
            return $this->changeDeviceState($rs2,$reserveId,$this->check->revertState(),$dRepertoryDao,$event);
        }else{
            $dLendRenewRevertDao->rollback();
            return $rs;
        }
    }

    /**
     * 验证 设备是否存在 及状态
     * @param string $field
     * @param string $value
     * @param string $state
     * @return string
     */
    private function dRepertoryCheck($field='',$value='',$state=''){
        $dao=$this->dao('dRepertory');
        if($value=='' || !in_array($field,array('code','num'))) return '请求失败3';
        $dao->where_eq($field,$value);
        $dRepertory=$dao->getResult(2);
        if( !isset($dRepertory['result'][$field]) || $dRepertory['result'][$field] != $value )  return  "该设备（{$value}）不存在";
        if($dRepertory['result']['state'] != "{$state}") return "该设备（{$value}）未{$state}";
        return  $dRepertory['result'];
    }

    /**
     * 更新设备状态
     * @param $rs
     * @param $reserveId
     * @param $stateValue
     * @param $dao
     * @param string $event
     * @return bool|string
     */
    private function changeDeviceState($rs,$reserveId,$stateValue,&$dao,$event=''){
        $rs=is_numeric($rs) ? ( $rs > 0 ) : $rs;
        if(!is_bool($rs) || $rs==false){
            $dao->rollback();
            return $rs;
        }
        $dReserveDao=$this->dao('dReserve');
        $dReserveDao->setState($stateValue);
        $rs2 =$dReserveDao->where_eq('device_reserve.id',$reserveId)->modify();
        if(is_bool($rs2) && $rs2==true){
            $dao->commit();
            if($stateValue == $this->check->lendState()){
                $dReserveDao->left_join('device_category')->on_eq('device_category.id','device_reserve.categoryId');
                $dReserveDao->select(array('device_reserve.categoryId','device_category.lendSum'));
                $reserve=$dReserveDao->where_eq('device_reserve.id',$reserveId)->getResult(2);
                $result = $reserve['result'];
                $this->dao('dCategory')->setLendSum($result['lendSum'] + 1)->where_eq('device_category.id',$result['categoryId'])->modify();
            }
            $this->addLog($this->check->getUserNum(),$this->check->getUserName(),"{$event}");
            return true;
        }else{
            $dao->rollback();
            return $rs2;
        }
    }

}
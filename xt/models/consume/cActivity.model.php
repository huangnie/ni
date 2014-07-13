<?php
/**
 *  User: huangnie
 */
require_once dirname(dirname(__FILE__)).'/activity.model.php';
require_once dirname(__FILE__) . '/consume.interface.php';
/**
 * 耗材业务逻辑层，即与设备相关的工作流（功能服务）
 */
class CActivityModel extends ActivityModel implements ConsumeInterface {

    function __construct(){
        parent::__construct();
    }

    /**
     * 预约操作
     * @param int $consumeId
     * @param int $count
     * @return mixed|string
     */
    function reserve($consumeId=0,$count=1){
        if(!is_numeric($consumeId) || !is_numeric($count) || $consumeId < 0 || $count < 0 ) return '请求失败1';
        $consumeResult=$this->getResultById('cRepertory',$consumeId);
        if(!is_array($consumeResult) || count($consumeResult) == 0) return '请求失败2';
        if($consumeResult['state']!='在售') return  '抱歉，该耗材暂不可售';
        if($consumeResult['theCount'] <= $consumeResult['minCount']) {
            $this->dao('cRepertory')->setAlarmCount($consumeResult['alarmCount'] + 1)->where_eq('id',$consumeId)->modify();
        }
        if($count >= intval($consumeResult['theCount'] - $consumeResult['reserveCount'])) return '预订数太多，库存不足';
        if($consumeResult['price']*$count > $this->check->getUserMoney())  return '预订数太多，你的账户余额 只有 '.$this->check->getUserMoney(). '元';

        //新增订单
        $cReserveDao=$this->dao('cReserve');
        $cReserveDao->setCustomerId($this->check->getUserId());
        $cReserveDao->setState($this->check->consumeReserveState());
        $cReserveDao->setConsumeId($consumeId);
        $cReserveDao->setTheCount($count);
        $cReserveDao->setTheTime(time());
        $cReserveDao->trans();
        $reserveId = $cReserveDao->add();
        if(!is_numeric($reserveId) || $reserveId == 0)  return $reserveId;

        //更新数量
        $cRepertoryDao=$this->dao('cRepertory');
        $cRepertoryDao->setReserveCount(intval($consumeResult['reserveCount']) + $count);
        $rs=$cRepertoryDao->where_eq('id',$consumeId)->modify();
        if(!is_bool($rs) || $rs==false){
            $cReserveDao->rollback();
        }
        else{
            $cReserveDao->commit();
        }
        $event="预订 {$count} 个 {$consumeResult['num']}";
        if(is_bool($rs) && $rs==true) $this->addLog($this->check->getUserNum(),$this->check->getUserName(),"{$event}");
        return $rs;
    }

    /**
     * 取消操作
     * @param int $reserveId
     * @param bool $isViewCurSql
     * @return bool
     */
    function cancel($reserveId=0,$isViewCurSql=false){
        $cancelRepealSaleDao = $this->cancelRepealSaleOperate($reserveId,$isViewCurSql);
        if(!is_object($cancelRepealSaleDao)) return $cancelRepealSaleDao;

        $event="取消 订单：{$reserveId}";
        return $this->changeConsumeState(true,$reserveId,$this->check->consumeCancelState(),$cancelRepealSaleDao,$event);
    }

    /**
     * 撤消操作
     * @param int $reserveId
     * @param bool $isViewCurSql
     * @return bool
     */
    function repeal($reserveId=0,$isViewCurSql=false){
        $cancelRepealSaleDao = $this->cancelRepealSaleOperate($reserveId,$isViewCurSql,$this->check->getUserId());
        if(!is_object($cancelRepealSaleDao)) return $cancelRepealSaleDao;

        $event="撤销 订单：{$reserveId}";
        return $this->changeConsumeState(true,$reserveId,$this->check->consumeRepealState(),$cancelRepealSaleDao,$event);
    }

    /**
     * 出售耗材
     * @param int $reserveId
     * @param string $customerNmm
     * @param bool $isViewCurSql
     * @return bool|mixed|string
     */
    function sale($reserveId=0,$customerNmm='',$isViewCurSql=false){
        if($customerNmm=='') return '用户编号不能为空';
        $uCustomerDao=$this->dao('uCustomer');
        $uCustomer=$uCustomerDao->where_eq('num',$customerNmm)->getResult(2);
        if($uCustomer['result']['num'] != $customerNmm) return '用户编号错误';

        $cancelRepealSaleDao = $this->cancelRepealSaleOperate($reserveId,$isViewCurSql,$this->check->getUserId());
        if(!is_object($cancelRepealSaleDao)) return $cancelRepealSaleDao;

        // 此处 还可优化，有时间再来
        $cReserveResult=$this->getResultById('cReserve',$reserveId);
        $theCount = $cReserveResult['theCount'];
        $consumeId = $cReserveResult['consumeId'];
        $customerId = $cReserveResult['customerId'];
        $cRepertoryResult=$this->getResultById('cRepertory',$consumeId);
        $price = $cRepertoryResult['price'];
        $customerResult=$this->getResultById('uCustomer',$customerId);
        $moneySum=$customerResult['money'];

        //扣费
        $customerDao=$this->dao('uCustomer');
        $customerDao->setMoney($moneySum - $price*$theCount);
        $customerDao->trans();
        $rs2 = $customerDao->where_eq('id',$customerId)->modify();

        $event="售出 {$cReserveResult['theCount']} 个 {$cRepertoryResult['num']} ，订单号：{$cReserveResult['id']}";
        $rs3= $this->changeConsumeState($rs2,$reserveId,$this->check->saleState(),$customerDao,$event);
        if(is_bool($rs3) && $rs3 == true) {
            $this->dao('cRepertory')->setSaleSum($cRepertoryResult['saleSum'] + $theCount)->where_eq('id',$cRepertoryResult['id'])->modify();
        }
        return $rs3;
    }

    /**
     * 取消，或撤销，或售出（扣费）订单
     * @param int $reserveId
     * @param bool $isViewCurSql
     * @param int $managerId
     * @return string
     */
    private function cancelRepealSaleOperate($reserveId=0,$isViewCurSql=false,$managerId=0){
        if(!is_numeric($reserveId) || $reserveId < 0 ) return '请求失败1';
        $cReserveResult=$this->getResultById('cReserve',$reserveId);
        if(!is_array($cReserveResult) || count($cReserveResult) == 0) return '请求失败2';
        if( !$this->check->consumeReserveState($cReserveResult['state'])) return "之前操作过了";
        if($this->check->isCustomer() && $this->check->getUserId() != $cReserveResult['customerId']) return '你不能取消他人的订单';
        $cancelRepealSaleDao=$this->dao('cCancelRepealSale');
        $cancelRepealSaleDao->trans();
        $rs = $cancelRepealSaleDao->setReserveId($reserveId)->setManagerId($managerId)->setTheTime(time())->add();
        if(!is_numeric($rs) || $rs <= 0) {
            $cancelRepealSaleDao->rollback();
            return $rs;
        }
        return $cancelRepealSaleDao;
    }

    /**
     * 回收耗材
     * @param int $reserveId
     * @param string $customerNmm
     * @param int $count
     * @param int $rate
     * @return bool|mixed|string
     */
    function recycle($reserveId=0,$customerNmm='',$count=1,$rate=1){
        if($customerNmm=='') return '用户编号不能为空';
        $uCustomerDao=$this->dao('uCustomer');
        $uCustomer=$uCustomerDao->where_eq('num',$customerNmm)->getResult(2);
        if($uCustomer['result']['num'] != $customerNmm) return '用户编号错误';
        
        if(!is_numeric($reserveId)|| !is_numeric($count)|| $reserveId <= 0 || $count <= 0 || $rate <= 0)  return '请求失败1';
        $cReserveResult=$this->getResultById('cReserve',$reserveId);
        if(!is_array($cReserveResult) || count($cReserveResult) == 0) return '请求失败2';
        if( !$this->check->saleState($cReserveResult['state'])) return '不满操回收条件,未曾售出';

        // 添加回收记录
        $cRecycleDao=$this->dao('cRecycle');
        $cRecycleDao->trans();
        $cRecycleDao->setReserveId($reserveId)
            ->setTheTime(time())
            ->setManagerId($this->check->getUserId())
            ->setTheCount($count)
            ->setRate($rate);
        $cRecycleDao->trans();
        $rs = $cRecycleDao->add();
        $rs=is_numeric($rs) ? ( $rs > 0 ) : $rs;
        if(!is_bool($rs) || $rs==false){
            $cRecycleDao->rollback();
            return $rs;
        }

        //获取当前库存  // 此处 还可优化，有时间再来
        $cReserveResult=$this->getResultById('cReserve',$reserveId);
        $consumeId = $cReserveResult['consumeId'];
        $customerId = $cReserveResult['customerId'];
        $cRepertoryResult=$this->getResultById('cRepertory',$consumeId);
        $price = $cRepertoryResult['price'];
        $theCount = $cRepertoryResult['theCount'];
        $uCustomerResult=$this->getResultById('uCustomer',$customerId);
        $moneySum=$uCustomerResult['money'];

        //更新库存（数量）
        $cRepertoryDao=$this->dao('cRepertory');
        $cRepertoryDao->setTheCount($theCount + $count);
        $rs2 =$cRepertoryDao->where_eq('id',$consumeId)->modify();
        if(is_bool($rs2) && $rs2==true){
            //更新账户（余额）
            $uCustomerDao=$this->dao('uCustomer');
            $uCustomerDao->setMoney($moneySum + ($price*$count)*$rate );
            $rs3=$uCustomerDao->where_eq('id',$customerId)->modify();
            if(is_bool($rs3) && $rs3==true){
                $event="回收 {$count} 个 {$cRepertoryResult['num']} ，损耗率估计：{$rate} ，订单号：{$cReserveResult['id']}";
                return $this->changeConsumeState($rs2,$reserveId,$this->check->recycleState(),$uCustomerDao,$event);
            }else{
                $cRecycleDao->rollback();
                return $rs3;
            }
        }else{
            $cRecycleDao->rollback();
             return $rs2;
        }
    }

    /**
     * 更新耗材状态
     * @param $rs
     * @param $reserveId
     * @param $stateValue
     * @param $dao
     * @param string $event
     * @return bool
     */
    private function changeConsumeState($rs,$reserveId,$stateValue,&$dao,$event=''){
        $rs=is_numeric($rs) ? ( $rs > 0 ) : $rs;
        if(!is_bool($rs) || $rs==false){
            $dao->rollback();
            return $rs;
        }
        $dReserveDao=$this->dao('cReserve');
        $dReserveDao->setState($stateValue);
        $rs2 =$dReserveDao->where_eq('id',$reserveId)->modify();
        if(is_bool($rs2) && $rs2==true){
            $rs3=true;
            //更新数量
            if($stateValue != $this->check->recycleState()){
                $cReserveResult=$this->getResultById('cReserve',$reserveId);
                $cRepertoryResult=$this->getResultById('cRepertory',$cReserveResult['consumeId']);
                $cRepertoryDao=$this->dao('cRepertory');
                $reserveCount=$cRepertoryResult['reserveCount'] > $cReserveResult['theCount'] ? $cRepertoryResult['reserveCount'] - $cReserveResult['theCount'] : '0';
                $cRepertoryDao->setReserveCount($reserveCount);

                if($stateValue == $this->check->saleState()){
                    $theCount=$cRepertoryResult['theCount'] > $cReserveResult['theCount'] ? $cRepertoryResult['theCount'] - $cReserveResult['theCount'] : '0';
                    $cRepertoryDao->setTheCount($theCount);
                }
                $rs3=$cRepertoryDao->where_eq('id',$cReserveResult['consumeId'])->modify();
            }
            if(is_bool($rs3) && $rs3==true){
                $dao->commit();
                $this->addLog($this->check->getUserNum(),$this->check->getUserName(),"{$event}");
            }else{
                $dao->rollback();
            }
            return $rs3;
        }else{
            $dao->rollback();
            return $rs2;
        }
    }

}
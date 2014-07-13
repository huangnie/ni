<?php
/**
 * 耗材活动接口.
 * User: huangnie
 */

/**
 * 耗材活动接口.
 * Interface ConsumeInterface
 */
interface ConsumeInterface {

    /**
     * 预约操作
     * @param int $consumeId
     * @param int $count
     * @return mixed
     */
    function reserve($consumeId=0,$count=1);

    /**
     * 取消操作
     * @param int $reserveId
     */
    function cancel($reserveId=0);

    /**
     * 撤销操作
     * @param int $reserveId
     */
    function repeal($reserveId=0);

    /**
     * 撤销操作
     * @param int $reserveId
     * @param int $customerNmm
     * @return mixed
     */
    function sale($reserveId=0,$customerNmm=0);

    /**
     * 撤销操作（已批准好未借），须在子类中实现
     * @param int $reserveId
     * @param int $count
     * @param int $rate
     * @return mixed
     */
    function recycle($reserveId=0,$count=1,$rate=1);

}
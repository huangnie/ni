<?php
/**
 * 设备活动接口.
 * User: huangnie
 */

/**
 * 设备活动接口.
 * Interface DeviceInterface
 */
interface DeviceInterface {

    /**
     * 预约操作
     * @param int $categoryId
     * @return mixed
     */
    function reserve($categoryId=0);

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
     * 借出操作
     * @param string $field
     * @param string $value
     * @param string $customerNmm
     * @param bool $isViewSql
     * @return mixed
     */
    function lend($field='',$value='',$customerNmm='',$isViewSql=false);

    /**
     * 续借操作
     * @param int $reserveId
     */
    function renew($reserveId=0);

    /**
     * 归还操作
     * @param string $field
     * @param string $value
     * @param bool $isViewSql
     * @return mixed
     */
    function revert($field='',$value='',$isViewSql=false);

}
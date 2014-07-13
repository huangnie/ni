<?php
/**
 *  User: huangnie
 */
require_once dirname(dirname(__FILE__)).'/info.model.php';
/**
 * 耗材所有相关的数据视图.
 */
class CInfoModel extends InfoModel {

    function __construct(){

    }

    /**
     * 耗材分类
     * @param bool $isViewSql
     * @param null $state
     * @return mixed
     */
    function category($isViewSql=false, $state=null){
        $dao = $this->dao('cCategory');
        $dao->select('consume_category.id');
        $data= $this->table($dao,$isViewSql);
        $operate['consumeRepertoryViewLink']=$this->ui->getConsumeRepertoryViewLink();
        return $this->toHtmlArr($data,$operate,$this->ui->getAddBtn());
    }

    /**
     * 耗材库存
     * @param bool $isViewSql
     * @param null $state
     * @param int $categoryId
     * @return mixed
     */
    function repertory($isViewSql=false, $state=null,$categoryId=0){
        $dao=$this->dao("cRepertory");
        $dao->select('consume_repertory.id');
        if(is_numeric($categoryId) && $categoryId > 0) $dao->where_eq('categoryId',$categoryId);
        if($state==2) {
            $dao->where_lt('theCount','minCount',2);
            $dao->order_desc('alarmCount');
        }
        $operate['consumeReserveBtn']=$this->ui->getConsumeReserveBtn();
        $operate['modifyBtn']=$this->ui->getModifyBtn();
        $operate['deleteBtn']=$this->ui->getDeleteBtn();
        $data= $this->table($dao,$isViewSql);
        return $this->toHtmlArr($data,$operate,$this->ui->getAddBtn());
    }

    /**
     * 预约 取消 耗材 撤销 或售出列表
     * @param bool $isViewSql
     * @param null $state
     * @return mixed
     */
    function reserve($isViewSql=false, $state=null){
        $dao=$this->dao("cReserve")->select('consume_reserve.id');;
        $dao->select("consume_reserve.`id`");
        $dao->left_join('consume_repertory')->on_eq('consume_repertory.`id`','consume_reserve.`consumeId`');
        $dao->select(array("consume_reserve.`id`","consume_repertory.`num`"),2);
        $readFieldsExplain = array( 'consume_reserve.`id' =>array('value'=>'订单号'),"consume_repertory.`num`"=>array('value'=>'耗材编号'));
        $operate=array();
        switch($state){
            case 2:
                $dao->left_join('consume_cancel_repeal_sale')->on_eq('consume_reserve.id','consume_cancel_repeal_sale.reserveId');
                $dao->where_eq('consume_reserve.state',$this->check->consumeCancelState());
                $readFieldsExplain3 = array( 'consume_cancel_repeal_sale.`theTime`' =>array('value'=>'取消时间'));
                $dao->select("consume_cancel_repeal_sale.`theTime`",2);
                $readFieldsExplain=$readFieldsExplain+$readFieldsExplain3;
                break;
            case 3:
                $dao->left_join('consume_cancel_repeal_sale')->on_eq('consume_reserve.id','consume_cancel_repeal_sale.reserveId');
                $dao->where_eq('consume_reserve.state',$this->check->consumeRepealState());
                $readFieldsExplain3 = array( 'consume_cancel_repeal_sale.`theTime`' =>array('value'=>'撤消时间'));
                $dao->select("consume_cancel_repeal_sale.`theTime`",2);
                $readFieldsExplain=$readFieldsExplain+$readFieldsExplain3;
                break;
            case 4:
                $dao->left_join('consume_cancel_repeal_sale')->on_eq('consume_reserve.id','consume_cancel_repeal_sale.reserveId');
                $dao->where_eq('consume_reserve.state',$this->check->saleState());
                $readFieldsExplain3 = array( 'consume_cancel_repeal_sale.`theTime`' =>array('value'=>'售出时间'));
                $dao->select("consume_cancel_repeal_sale.`theTime`",2);
                $readFieldsExplain=$readFieldsExplain+$readFieldsExplain3;
                $operate['recycleBtn']=$this->ui->getRecycleBtn();
                break;
            case 1:
            default:
                $dao->where_eq('consume_reserve.state',$this->check->consumeReserveState());
                $operate['cancelReserveBtn']=$this->ui->getCancelBtn();
                $operate['saleBtn']=$this->ui->getSaleBtn();
                $operate['repealReserveBtn']=$this->ui->getRepealBtn();
                break;
        }
        $data = $this->table($dao,$isViewSql,$readFieldsExplain);
        return $this->toHtmlArr($data,$operate);
    }

    /**
     * 回收记录
     * @param bool $isViewSql
     * @param null $state
     * @return mixed
     */
    function recycle($isViewSql=false, $state=null){
        $dao=$this->dao("cRecycle");
        $dao->select('consume_recycle.id');
        $dao->left_join('consume_reserve')->on_eq('consume_reserve.`id`','consume_recycle.`reserveId`');
        $dao->left_join('consume_repertory')->on_eq('consume_repertory.`id`','consume_reserve.`consumeId`');
        if($this->check->isCustomer()){
            $dao->left_join('user_customer')->on_eq('user_customer.id','consume_reserve.customerId');
            $dao->where_eq('user_customer.id',$this->check->getUserId());
        }
        $dao->select(array("consume_repertory.`num`","consume_reserve.`state`"));
        $dao->where_eq('consume_reserve.`state`',$this->check->recycleState());
        $readFieldsExplain = array("consume_repertory.`num`"=>array('value'=>'耗材编号'),"consume_reserve.`state`"=>array('value'=>'状态'));
        $data= $this->table($dao,$isViewSql,$readFieldsExplain);
        return $this->toHtmlArr($data);
    }

}
<?php
/**
 * user:huangnie
 */
require_once 'base.dao.php';

/**
 * Class CrrDao
 */
final class CCancelRepealSaleDao extends BaseDao {

    private $reserveId=null;
    private $managerId=null;
    private $theTime=null;      //拒绝时间，或借出时间
    private $remark=null;       //备注

    function __construct(){
        parent::__construct('xt');
   		$this->init();
 	}

 	function init(){
        parent::init();
        parent::setTable("consume_cancel_repeal_sale");
		$this->reserveId=null;
        $this->managerId=null;
        $this->theTime=null;
        $this->remark=null;
 	}
    
    function setReserveId($reserveId=null){$this->reserveId=$reserveId; return $this;}
    function setManagerId($managerId=null){$this->managerId=$managerId; return $this;}
    function setTheTime($theTime=null){$this->theTime=$theTime; return $this;}
    function setRemark($remark=null){$this->remark=$remark; return $this;}

    /**
     * @param int    $editType
     * @param string $findField
     * @return array
     */
    function getFieldsExplain($editType=1,$findField=''){
        $fieldsExplainArr=array(
            'reserveId'  =>array('value'=>'预约号',     'type'=>'text',   'add'=>'0','modify'=>'0','read'=>'1', 'tip'=>''),
            'managerId'  =>array('value'=>'管理员',     'type'=>'text',   'add'=>'0','modify'=>'0','read'=>'0', 'tip'=>''),
            'theTime'    =>array('value'=>'操作时间',   'type'=>'hidden', 'add'=>'0','modify'=>'0','read'=>'1', 'tip'=>''),
            'remark'     =>array('value'=>'备注',       'type'=>'text',   'add'=>'0','modify'=>'1','read'=>'1', 'tip'=>''),
        );
        return parent::getArrElement($findField,$editType,$fieldsExplainArr);
    }
}


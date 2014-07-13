<?php
/**
 * user:huangnie
 */
require_once 'base.dao.php';

/**
 * Class TradeDao
 */
    final class CRecycleDao extends BaseDao {

    private $reserveId=null;
    private $managerId=null;	    //varchar
    private $theCount=null;
    private $rate=null;
    private $theTime=null;
    private $remark=null;           //备注

    function __construct(){
        parent::__construct('xt');
        $this->init();
 	}
 	
 	function init(){
        parent::init();
        $this->setTable("consume_recycle");
 		$this->reserveId=null;
        $this->managerId=null;
        $this->theCount=null;
        $this->rate=null;
        $this->theTime=null;
        $this->remark=null;
 	}

	function setReserveId($reserveId=null){$this->reserveId=$reserveId; return $this;}
    function setManagerId($managerId=null){$this->managerId=$managerId; return $this;}
    function setRate($rate=null){$this->rate=$rate; return $this;}
    function setTheCount($theCount=null){$this->theCount=$theCount; return $this;}
    function setTheTime($theTime=null){$this->theTime=$theTime; return $this;}
    function setRemark($remark=null){$this->remark=$remark; return $this;}

    /**
     * @param int    $editType
     * @param string $findField
     * @return array
     */
    function getFieldsExplain($editType=1,$findField=''){
        $fieldsExplainArr=array(
            'reserveId'   =>array('value'=>'订单号',     'type'=>'text',        'add'=>'0','modify'=>'0','read'=>'1',  'tip'=>''),
            'managerId'   =>array('value'=>'管理员',     'type'=>'text',        'add'=>'0','modify'=>'0','read'=>'0',  'tip'=>''),
            'theCount'    =>array('value'=>'回收数量',   'type'=>'text',        'add'=>'0','modify'=>'0','read'=>'1',  'tip'=>''),
            'rate'        =>array('value'=>'可用率',     'type'=>'text',        'add'=>'0','modify'=>'0','read'=>'1',  'tip'=>''),
            'theTime'     =>array('value'=>'回收时间',       'type'=>'hidden',      'add'=>'0','modify'=>'0','read'=>'1',  'tip'=>''),
            'remark'      =>array('value'=>'备注',       'type'=>'textarea',    'add'=>'0','modify'=>'1','read'=>'1',  'tip'=>''),
        );
		return parent::getArrElement($findField,$editType,$fieldsExplainArr);
	}
}


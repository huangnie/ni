<?php
/**
 * user:huangnie
 */
require_once 'base.dao.php';

/**
 * Class TradeDao
 */
final class CReserveDao extends BaseDao {

    private $consumeId=null;
    private $customerId=null;
    private $managerId=null;	    //varchar
    private $theCount=null;
    private $state=null;
    private $theTime=null;
    private $remark=null;           //备注

    function __construct(){
        parent::__construct('xt');
        $this->init();
 	}
 	
 	function init(){
        parent::init();
        $this->setTable("consume_reserve");
 		$this->consumeId=null;
        $this->managerId=null;
        $this->state=null;
        $this->theTime=null;
        $this->remark=null;
 	}

	function setConsumeId($consumeId=null){$this->consumeId=$consumeId; return $this;}
	function setCustomerId($customerId=null){$this->customerId=$customerId; return $this;}
    function setManagerId($managerId=null){$this->managerId=$managerId; return $this;}
	function setTheCount($theCount=null){$this->theCount=$theCount; return $this;}
	function setState($state=null){$this->state=$state; return $this;}
    function setTheTime($theTime=null){$this->theTime=$theTime; return $this;}
    function setRemark($remark=null){$this->remark=$remark; return $this;}

    /**
     * @param int    $editType
     * @param string $findField
     * @return array
     */
    function getFieldsExplain($editType=1,$findField=''){
        $fieldsExplainArr=array(
            'consumeId'   =>array('value'=>'耗材Id',     'type'=>'text',        'add'=>'0','modify'=>'0','read'=>'0',  'tip'=>''),
            'customerId'  =>array('value'=>'普通用户',   'type'=>'text',        'add'=>'0','modify'=>'0','read'=>'0',  'tip'=>''),
            'managerId'   =>array('value'=>'管理员',     'type'=>'text',        'add'=>'0','modify'=>'0','read'=>'0',  'tip'=>''),
            'theCount'    =>array('value'=>'数量',       'type'=>'hidden',      'add'=>'0','modify'=>'0','read'=>'1',  'tip'=>''),
            'state'       =>array('value'=>'状态',       'type'=>'hidden',      'add'=>'0','modify'=>'0','read'=>'1',  'tip'=>''),
            'theTime'     =>array('value'=>'预订时间',       'type'=>'hidden',      'add'=>'0','modify'=>'0','read'=>'1',  'tip'=>''),
            'remark'      =>array('value'=>'备注',       'type'=>'textarea',    'add'=>'0','modify'=>'1','read'=>'1',  'tip'=>''),
        );
		return parent::getArrElement($findField,$editType,$fieldsExplainArr);
	}
}


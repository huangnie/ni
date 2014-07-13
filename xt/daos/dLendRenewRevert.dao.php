<?php
/**
 * user:huangnie
 */
require_once 'base.dao.php';

/**
 * Class LendRevertDao
 */
final class DLendRenewRevertDao extends BaseDao {

	private $reserveId=null;	 		//bigint
 	private $deviceId=null; 	//varchar
    private $lenderId=null;
    private $lendTime=null;
    private $renewDays=null;
	private $reverterId=null;       //varchar
    private $revertTime=null;       //varchar
    private $remark=null;           //备注

    function __construct(){
        parent::__construct('xt');
        $this->init();
 	}

 	function init(){
        parent::init();
        parent::setTable("device_lend_renew_revert");
 		$this->reserveId=null;
 		$this->deviceId=null;
		$this->lenderId=null;
		$this->lendTime=null;
        $this->renewDays=null;
		$this->reverterId=null;
        $this->revertTime=null;
		$this->renewDays=null;
        $this->remark=null;
    }

	function setReserveId($reserveId=null){$this->reserveId=$reserveId; return $this;}
	function setDeviceId($deviceId=null){$this->deviceId=$deviceId; return $this;}
	function setLenderId($lenderId=null){$this->lenderId=$lenderId; return $this;}
    function setLendTime($lendTime=null){$this->lendTime=$lendTime; return $this;}
	function setRenewDays($renewDays=false){$this->renewDays=$renewDays; return $this;}
	function setReverterId($reverterId=null){$this->reverterId=$reverterId; return $this;}
	function setRevertTime($revertTime=null){$this->revertTime=$revertTime; return $this;}
    function setRemark($remark=null){$this->remark=$remark; return $this;}

    /**
     * @param int    $editType
     * @param string $findField
     * @return array
     */
    function getFieldsExplain($editType=1,$findField=''){
        $fieldsExplainArr=array(
            'reserveId'     =>array('value'=>'预约号',     'type'=>'text',        'add'=>'0','modify'=>'0','read'=>'1',  'tip'=>''),
            'deviceId'      =>array('value'=>'设备',     'type'=>'text',        'add'=>'0','modify'=>'0','read'=>'0',  'tip'=>''),
            'lenderId'      =>array('value'=>'借出者',   'type'=>'text',        'add'=>'0','modify'=>'0','read'=>'0',  'tip'=>''),
            'lendTime'      =>array('value'=>'借出时间',    'type'=>'text',        'add'=>'0','modify'=>'0','read'=>'1',  'tip'=>''),
            'renewDays'     =>array('value'=>'续借天数',    'type'=>'text',        'add'=>'0','modify'=>'0','read'=>'1',  'tip'=>''),
            'reverterId'    =>array('value'=>'接收者',      'type'=>'text',        'add'=>'0','modify'=>'0','read'=>'0',  'tip'=>''),
            'revertTime'    =>array('value'=>'归还时间',    'type'=>'text',        'add'=>'0','modify'=>'0','read'=>'1',  'tip'=>''),
            'remark'        =>array('value'=>'备注',        'type'=>'textarea',    'add'=>'0','modify'=>'1','read'=>'1',  'tip'=>''),
        );
        return parent::getArrElement($findField,$editType,$fieldsExplainArr);
	}
    
}


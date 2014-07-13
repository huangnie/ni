<?php
/**
 * user:huangnie
 */
require_once 'base.dao.php';

/**
 * Class RepairDao
 */
final class DRepairDao extends BaseDao {

    private $deviceNum=null;    //varchar
    private $serviceMan=null;
	private $spent=null;	    //varchar
	private $theDate=null;		//日期
    private $theTime=null;		//时间戳
    private $remark=null;	    //

    function __construct(){
 		parent::__construct('xt');
        $this->init();
 	}

 	function init(){
        parent::init();
        $this->setTable("device_repair");
        $this->deviceNum=null;
        $this->serviceMan=null;
		$this->spent=null;
        $this->theDate=null;	//
        $this->theTime=null;
        $this->remark=null; //本次申请号
 	}

    function setDeviceNum($deviceNum=null){$this->deviceNum=$deviceNum; return $this;}
	function setServiceMan($serviceMan=null){$this->serviceMan=$serviceMan; return $this;}
    function setSpent($spent=null){$this->spent=$spent; return $this;}
    function setTheDate($theDate=null){$this->theDate=$theDate; return $this;}
    function setTheTime($theTime=null){$this->theTime=$theTime; return $this;}
    function setRemark($remark=null){$this->remark=$remark; return $this;}

    /**
     * @param int    $editType
     * @param string $findField
     * @return array
     */
    function getFieldsExplain($editType=1,$findField=''){
        $fieldsExplainArr=array(
            'deviceNum' =>array('value'=>'设备编号',   'type'=>'text',    'add'=>'1','modify'=>'1','read'=>'1','tip'=>''),
            'serviceMan'=>array('value'=>'维修人员',   'type'=>'text',    'add'=>'1','modify'=>'1','read'=>'1','tip'=>''),
            'spent'     =>array('value'=>'维修费',     'type'=>'text',    'add'=>'1','modify'=>'1','read'=>'1','tip'=>''),
			'theDate'   =>array('value'=>'维修时间',   'type'=>'text',    'add'=>'1','modify'=>'1','read'=>'1','tip'=>''),
			'theTime'   =>array('value'=>'登记时间',   'type'=>'hidden',  'add'=>'1','modify'=>'1','read'=>'1','tip'=>''),
            'remark'    =>array('value'=>'备注',       'type'=>'textarea','add'=>'1','modify'=>'1','read'=>'1','tip'=>''),
		);
        return parent::getArrElement($findField,$editType,$fieldsExplainArr);
	}

}
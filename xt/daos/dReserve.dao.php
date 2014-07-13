<?php
/**
 * user:huangnie
 */
require_once 'base.dao.php';

/**
 * Class ReserveDao
 */
final class DReserveDao extends BaseDao {

	private $categoryId=null; 	//varchar
    private $customerId=null;
    private $state=null;
    private $theTime=null;
    private $remark=null;       //备注

    function __construct(){
        parent::__construct('xt');
   		$this->init();
 	}

 	function init(){
        parent::init();
        parent::setTable("device_reserve");
        $this->categoryId=null;
        $this->customerId=null;
        $this->state=null;
        $this->theTime=null;
        $this->remark=null;
 	}

	function setCategoryId($categoryId=null){$this->categoryId=$categoryId; return $this;}
	function setState($state=null){$this->state=$state; return $this;}
    function setCustomerId($customerId=null){$this->customerId=$customerId; return $this;}
    function setTheTime($theTime=null){$this->theTime=$theTime; return $this;}
    function setRemark($remark=null){$this->remark=$remark; return $this;}

    /**
     * @param int    $editType
     * @param string $findField
     * @return array
     */
    function getFieldsExplain($editType=1,$findField=''){
        $fieldsExplainArr=array(
            'categoryId'   =>array('value'=>'设备分类',   'type'=>'text',     'add'=>'0','modify'=>'0','read'=>'0', 'tip'=>''),
            'customerId'   =>array('value'=>'申请人',     'type'=>'text',     'add'=>'0','modify'=>'0','read'=>'0', 'tip'=>''),
            'state'        =>array('value'=>'状态',       'type'=>'text',     'add'=>'0','modify'=>'1','read'=>'1', 'tip'=>''),
            'theTime'      =>array('value'=>'预约时间',   'type'=>'hidden',   'add'=>'0','modify'=>'0','read'=>'1', 'tip'=>''),
            'remark'       =>array('value'=>'备注',       'type'=>'textarea', 'add'=>'0','modify'=>'1','read'=>'1', 'tip'=>''),
        );
        return parent::getArrElement($findField,$editType,$fieldsExplainArr);
    }
}


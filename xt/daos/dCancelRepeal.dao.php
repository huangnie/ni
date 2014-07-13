<?php
/**
 * user:huangnie
 */
require_once 'base.dao.php';

/**
 * Class ApproveDao
 */
final class DCancelRepealDao extends BaseDao {

    private $reserveId=null;
    private $managerId=null;
    private $theTime=null;      //操作时间
    private $remark=null;       //备注

    function __construct(){
        parent::__construct('xt');
   		$this->init();
 	}

 	function init(){
        parent::init();
        parent::setTable("device_cancel_repeal");
        $this->reserveId=null;
        $this->managerId=null;
        $this->theTime=null;
        $this->remark=null;
 	}

    function getById($dao=null,$id=0){
        return $this->getResultByField($dao,'id',$id);
    }

    function deleteByApplyId($dao=null,$applyId=0){
        if($dao==null ) throw new Exception('未配置用户身份');
        if($applyId!=0){
            $this->deleteByField($dao,'applyId',$applyId);
        }
        else throw new Exception('编号不能为空');
    }

    function setState($state=null){$this->state=$state; return $this;}
    function setReserveId($reserveId=null){$this->reserveId=$reserveId; return $this;}
    function setTheTime($theTime=null){$this->theTime=$theTime; return $this;}
    function setManagerId($managerId=null){$this->managerId=$managerId; return $this;}
    function setRemark($remark=null){$this->remark=$remark; return $this;}

    /**
     * @param int    $editType
     * @param string $findField
     * @return array
     */
    function getFieldsExplain($editType=1,$findField=''){
        $fieldsExplainArr=array(
            'reserveId'  =>array('value'=>'预约号',   'type'=>'text',  'add'=>'0','modify'=>'0','read'=>'1', 'tip'=>''),
            'managerId'  =>array('value'=>'管理员',   'type'=>'text',  'add'=>'0','modify'=>'0','read'=>'1', 'tip'=>''),
            'theTime'    =>array('value'=>'操作时间', 'type'=>'text',  'add'=>'0','modify'=>'0','read'=>'1', 'tip'=>''),
            'remark'     =>array('value'=>'备注',     'type'=>'text',  'add'=>'0','modify'=>'1','read'=>'1', 'tip'=>''),
        );
        return parent::getArrElement($findField,$editType,$fieldsExplainArr);
    }
}


<?php
/**
 * user:huangnie
 */
require_once 'base.dao.php';

/**
 * Class LogDao
 */
final class ULogDao extends BaseDao {

    private $userNum=null;     //varchar
    private $userName=null;
    private $event=null;	    //varchar
    private $theTime=null;
    private $remark=null;           //备注

    function __construct(){
        parent::__construct('xt');
        $this->init();
    }

    function init(){
        parent::init();
        $this->setTable("user_log");
        $this->userNum=null;
        $this->userName=null;
        $this->event=null;
        $this->theTime=null;
        $this->remark=null;
    }

    function setUserNum($userNum=null){$this->userNum=$userNum; return $this;}
    function setUserName($userName=null){$this->userName=$userName; return $this;}
    function setEvent($event=null){$this->event=$event; return $this;}
    function setTheTime($theTime=null){$this->theTime=$theTime; return $this;}
    function setRemark($remark=null){$this->remark=$remark; return $this;}

    /**
     * @param int    $editType
     * @param string $findField
     * @return array
     */
    function getFieldsExplain($editType=1,$findField=''){
        $fieldsExplainArr=array(
            'userNum'   =>array('value'=>'用户编号', 'type'=>'text',       'add'=>'1','modify'=>'1','read'=>'1', 'tip'=>''),
            'userName' =>array('value'=>'用户名',    'type'=>'text',       'add'=>'1','modify'=>'1','read'=>'1', 'tip'=>''),
            'event'    =>array('value'=>'事件',      'type'=>'textarea',   'add'=>'1','modify'=>'1','read'=>'1', 'tip'=>''),
            'theTime'  =>array('value'=>'时间',      'type'=>'hidden',     'add'=>'1','modify'=>'1','read'=>'1', 'tip'=>''),
            'remark'   =>array('value'=>'备注',      'type'=>'textarea',   'add'=>'1','modify'=>'1','read'=>'1', 'tip'=>''),
        );
        return parent::getArrElement($findField,$editType,$fieldsExplainArr);
    }
}


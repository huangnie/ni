<?php
/**
 * user:huangnie
 */
require_once 'base.dao.php';

/**
 * Class DeskDao
 */
final class LDeskDao extends BaseDao {

    private $num=null;     //varchar
    private $roomId=null;	    //varchar
    private $deviceIds=null;
    private $theTime=null;    //时间戳
    private $remark=null;           //备注

    function __construct(){
        parent::__construct('xt');
        $this->init();
    }

    function init(){
        parent::init();
        $this->setTable("lab_desk");
        $this->num=null;
        $this->roomId=null;
        $this->deviceIds=null;
        $this->theTime=null;
        $this->remark=null;
    }

    function setNum($num=null){$this->num=$num; return $this;}
    function setRoomId($roomId=null){$this->roomId=$roomId; return $this;}
    function setDeviceIds($deviceIds=null){$this->deviceIds=$deviceIds; return $this;}
    function setTheTime($theTime=null){$this->theTime=$theTime; return $this;}
    function setRemark($remark=null){$this->remark=$remark; return $this;}

    /**
     * @param int    $editType
     * @param string $findField
     * @return array
     */
    function getFieldsExplain($editType=1,$findField=''){
        $fieldsExplainArr=array(
            'num'           =>array('value'=>'实验桌编号', 'type'=>'text',        'add'=>'1','modify'=>'1','read'=>'1',  'tip'=>''),
            'roomId'        =>array('value'=>'实验室编号' ,'type'=>'select',      'add'=>'1','modify'=>'1','read'=>'0',  'tip'=>'', 'selectArr'=>'getRoom()'),
            'deviceIds'     =>array('value'=>'设备配置',   'type'=>'text',        'add'=>'0','modify'=>'0','read'=>'1',  'tip'=>''),
            'theTime'       =>array('value'=>'更新时间',   'type'=>'hidden',      'add'=>'1','modify'=>'1','read'=>'1',  'tip'=>''),
            'remark'        =>array('value'=>'备注',       'type'=>'textarea',    'add'=>'1','modify'=>'1','read'=>'1',  'tip'=>''),
        );
        return parent::getArrElement($findField,$editType,$fieldsExplainArr);
    }
}


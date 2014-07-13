<?php
/**
 * user:huangnie
 */
require_once 'base.dao.php';

/**
 * Class RoomDao
 */
final class LRoomDao extends BaseDao {

    private $num=null;     //varchar
    private $chName=null;	    //varchar
    private $enName=null;
    private $remark=null;

    function __construct(){
        parent::__construct('xt');
        $this->init();
    }

    function init(){
        parent::init();
        parent::setTable("lab_room");
        $this->num=null;
        $this->chName=null;
        $this->enName=null;
        $this->remark=null;
    }

    function setNum($num=null){$this->num=$num; return $this;}
    function setChName($chName=null){$this->chName=$chName; return $this;}
    function setEnName($enName=null){$this->enName=$enName; return $this;}
    function setRemark($remark=null){$this->remark=$remark; return $this;}

    /**
     * @param int    $editType
     * @param string $findField
     * @return array
     */
    function getFieldsExplain($editType=1,$findField=''){
        $fieldsExplainArr=array(
            'num'     =>array('value'=>'编号',   'type'=>'text',      'add'=>'1','modify'=>'1','read'=>'1',  'tip'=>''),
            'chName'  =>array('value'=>'名称',   'type'=>'text',      'add'=>'1','modify'=>'1','read'=>'1',  'tip'=>''),
            'enName'  =>array('value'=>'英文',   'type'=>'text',      'add'=>'1','modify'=>'1','read'=>'1',  'tip'=>''),
            'remark'  =>array('value'=>'备注',   'type'=>'textarea',  'add'=>'1','modify'=>'1','read'=>'1',  'tip'=>''),
        );
        return parent::getArrElement($findField,$editType,$fieldsExplainArr);
    }
}


<?php
/**
 * user:huangnie
 */
require_once 'base.dao.php';

/**
 * Class GroupDao
 */
final class UGroupDao extends BaseDao {

 	private $num=null; 	        //varchar
    private $type=null;         //varchar
    private $state=null;
    private $deskId=null;	//int   该类 可预约余数量
    private $theTime=null;
    private $remark=null;           //备注

    function __construct(){
        parent::__construct('xt');
        $this->init();
    }
 	
 	function init(){
        parent::init();
        parent::setTable("user_group");
 		$this->num=null;
        $this->type=null;
		$this->state=null;
		$this->deskId=null;
        $this->theTime=null;
        $this->remark=null;
    }

 	function setNum($num=null){$this->num=$num; return $this;}
    function setType($type=null){$this->type=$type; return $this;}
	function setDeskId($deskId=null){$this->deskId=$deskId; return $this;}
    function setTheTime($theTime=null){$this->theTime=$theTime; return $this;}
    function setRemark($remark=null){$this->remark=$remark; return $this;}

    /**
     * @param int    $editType
     * @param string $findField
     * @return array
     */
    function getFieldsExplain($editType=1,$findField=''){
        $fieldsExplainArr=array(
            'num'    =>array('value'=>'分类号',    'type'=>'text',         'add'=>'1','modify'=>'1','read'=>'1', 'tip'=>'' ),
            'type'   =>array('value'=>'类型',      'type'=>'select',       'add'=>'1','modify'=>'1','read'=>'1', 'tip'=>'', 'selectArr'=>array('普通用户'=>'普通用户','管理员'=>'管理员')),
            'state'  =>array('value'=>'状态',      'type'=>'text',         'add'=>'0','modify'=>'1','read'=>'1', 'tip'=>'' ),
            'deskId' =>array('value'=>'实验卓id',  'type'=>'text',         'add'=>'0','modify'=>'0','read'=>'0', 'tip'=>'' ),
            'theTime'=>array('value'=>'注册时间',  'type'=>'hidden',       'add'=>'1','modify'=>'0','read'=>'1' ,'tip'=>'' ),
            'remark' =>array('value'=>'备注',      'type'=>'textarea',     'add'=>'1','modify'=>'1','read'=>'1', 'tip'=>'' ),
        );
        return parent::getArrElement($findField,$editType,$fieldsExplainArr);
	}
 	
}
<?php
/**
 * user:huangnie
 */
require_once 'base.dao.php';

/**
 * Class CategoryDao
 */
final class DCategoryDao extends BaseDao {

 	private $num=null; 	        //varchar	分类号
	private $chName=null;		//varchar 	中文名
	private $enName=null;		//varchar
    private $theCount=null;
    private $reserveCount=null;	//int       已预约数量
    private $lendSum=null;	    //int       历史累计借出总数量
    private $remark=null;       //备注

    function __construct(){
        parent::__construct('xt');
        $this->init();
    }
 	
 	function init(){
        parent::init();
        parent::setTable("device_category");
 		$this->num=null;
		$this->chName=null;
		$this->enName=null;
        $this->theCount=null;
        $this->reserveCount=null;
        $this->lendSum=null;
        $this->remark=null;
    }

 	function setNum($num=null){$this->num=$num; return $this;}
 	function setChName($chName=null){$this->chName=$chName; return $this;}
    function setEnName($enName=null){$this->enName=$enName; return $this;}
    function setTheCount($theCount=null){$this->theCount=$theCount; return $this;}
    function setReserveCount($reserveCount=null){$this->reserveCount=$reserveCount; return $this;}
    function setLendSum($lendSum=null){$this->lendSum=$lendSum; return $this;}
    function setRemark($remark=null){$this->remark=$remark; return $this;}

    /**
     * @param int    $editType
     * @param string $findField
     * @return array
     */
    function getFieldsExplain($editType=1,$findField=''){
        $fieldsExplainArr=array(
            'num'           =>array( 'value'=>'分类号',             'type'=>'text',       'add'=>'1','modify'=>'1','read'=>'1', 'tip'=>''),
            'chName'        =>array( 'value'=>'中文名',             'type'=>'text',       'add'=>'1','modify'=>'1','read'=>'1', 'tip'=>''),
            'enName '       =>array( 'value'=>'英文名',             'type'=>'text',       'add'=>'1','modify'=>'1','read'=>'1', 'tip'=>''),
            'theCount'      =>array( 'value'=>'库存总数',           'type'=>'text',       'add'=>'0','modify'=>'1','read'=>'1', 'tip'=>''),
            'reserveCount'  =>array( 'value'=>'已预约数',           'type'=>'text',       'add'=>'0','modify'=>'1','read'=>'1', 'tip'=>''),
            'lendSum'       =>array( 'value'=>'历史借出累计',       'type'=>'text',       'add'=>'0','modify'=>'1','read'=>'1', 'tip'=>''),
            'remark'        =>array( 'value'=>'备注',               'type'=>'textarea',   'add'=>'1','modify'=>'1','read'=>'1', 'tip'=>''),
        );
        return parent::getArrElement($findField,$editType,$fieldsExplainArr);
	}
 	
}
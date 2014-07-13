<?php
/**
 * user:huangnie
 */
require_once 'base.dao.php';

/**
 * Class CategoryDao
 */
final class CCategoryDao extends BaseDao {

 	private $num=null; 	        //varchar	分类号
	private $chName=null;		//varchar 	中文名
	private $enName=null;		//varchar
    private $remark=null;       //备注

    function __construct(){
        parent::__construct('xt');
        $this->init();
    }
 	
 	function init(){
        parent::init();
        parent::setTable("consume_category");
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
            'num'           =>array( 'value'=>'分类号',             'type'=>'text',       'add'=>'1','modify'=>'1','read'=>'1', 'tip'=>''),
            'chName'        =>array( 'value'=>'中文名',             'type'=>'text',       'add'=>'1','modify'=>'1','read'=>'1', 'tip'=>''),
            'enName '       =>array( 'value'=>'英文名',             'type'=>'text',       'add'=>'1','modify'=>'1','read'=>'1', 'tip'=>''),
            'remark'        =>array( 'value'=>'备注',               'type'=>'textarea',   'add'=>'1','modify'=>'1','read'=>'1', 'tip'=>''),
        );
        return parent::getArrElement($findField,$editType,$fieldsExplainArr);
	}
 	
}
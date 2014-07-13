<?php
/**
 * user:huangnie
 */
require_once 'base.dao.php';

/**
 * Class CRepertoryDao
 */
final class CRepertoryDao extends BaseDao {

    private $num=null;           //varchar
    private $chName=null;
    private $enName=null;
    private $categoryId=null; 	//varchar
    private $packageBrief=null;
    private $state=null;
	private $principal=null;	//varchar
	private $pos=null;		    //varchar
	private $price=null; 		//float ,单位元
    private $minCount=null;
    private $alarmCount=null;
    private $saleSum=null;
    private $theCount=null;
    private $reserveCount=null;
    private $remark=null;       //备注

    function __construct(){
 		parent::__construct('xt');
        $this->init();
 	}

 	function init(){
        parent::init();
        $this->setTable("consume_repertory");
        $this->num=null;
        $this->chName=null;
        $this->enName=null;
        $this->categoryId=null;
        $this->packageBrief=null;
        $this->theCount=null;
        $this->state=null;
		$this->principal=null;
		$this->pos=null;
		$this->price=null;
        $this->minCount=null;
        $this->theCount=null;
        $this->reserveCount=null;
        $this->alarmCount=null;
        $this->saleSum=null;
        $this->remark=null;
 	}

    function setNum($num=null){$this->num=$num; return $this;}
    function setChName($chName){$this->chName=$chName;return $this;}
    function setPinyin($pinyin=null){$this->pinyin=$pinyin; return $this;}
    function setEnName($enName=null){$this->enName=$enName; return $this;}
    function setCategoryId($categoryId=null){$this->categoryId=$categoryId; return $this;}
 	function setPrincipal($principal=null){$this->principal=$principal; return $this;}
	function setPos($pos=null){$this->pos=$pos; return $this;}
	function setPrice($price=null){$this->price=$price; return $this;}
	function setPackageBrief($packageBrief=null){$this->packageBrief=$packageBrief; return $this;}
	function setState($state=null){$this->state=$state; return $this;}
	function setTheCount($theCount=null){$this->theCount=$theCount; return $this;}
	function setReserveCount($reserveCount=null){$this->reserveCount=$reserveCount; return $this;}
	function setAlarmCount($alarmCount=null){$this->alarmCount=$alarmCount; return $this;}
	function setMinCount($minCount=null){$this->minCount=$minCount; return $this;}
	function setSaleSum($saleSum=null){$this->saleSum=$saleSum; return $this;}
    function setRemark($remark=null){$this->remark=$remark; return $this;}

    /**
     * @param int    $editType
     * @param string $findField
     * @return array
     */
    function getFieldsExplain($editType=1,$findField=''){
        $fieldsExplainArr=array(
            'num'           =>array('value'=>'耗材编号',          'type'=>'text',     'read'=>'1', 'add'=>'1', 'modify'=>'1', 'tip'=>'', 'search'=>1 ),
            'chName'        =>array('value'=>'耗材名称',          'type'=>'text',     'read'=>'1', 'add'=>'1', 'modify'=>'1', 'tip'=>'', 'search'=>1 ),
            'enName'        =>array('value'=>'英文名',            'type'=>'text',     'read'=>'1', 'add'=>'1', 'modify'=>'1', 'tip'=>'', 'search'=>1 ),
            'categoryId'    =>array('value'=>'分类号',            'type'=>'select',   'read'=>'0', 'add'=>'1', 'modify'=>'1', 'tip'=>'', 'search'=>1,  'selectArr'=>'getConsumeCategory()'),  //不许将参数引起来
            'principal'     =>array('value'=>'责任人',            'type'=>'text',     'read'=>'1', 'add'=>'1', 'modify'=>'1', 'tip'=>'', 'search'=>1 ),
			'pos'           =>array('value'=>'存放位置',          'type'=>'text',     'read'=>'1', 'add'=>'1', 'modify'=>'1', 'tip'=>'', 'search'=>1 ),
			'price'         =>array('value'=>'单价',              'type'=>'text',     'read'=>'1', 'add'=>'1', 'modify'=>'1', 'tip'=>'', 'search'=>1 ),
			'packageBrief'  =>array('value'=>'封装',              'type'=>'textarea', 'read'=>'1', 'add'=>'1', 'modify'=>'1', 'tip'=>'', 'search'=>1 ),
            'state'         =>array('value'=>'状态',              'type'=>'select',   'read'=>'1', 'add'=>'1', 'modify'=>'1', 'tip'=>'', 'search'=>0,  'selectArr'=>array('在售'=>'在售','屏蔽'=>'屏蔽')),
			'theCount'      =>array('value'=>'库存数量',          'type'=>'text',     'read'=>'1', 'add'=>'1', 'modify'=>'1', 'tip'=>'', 'search'=>1 ),
			'minCount'      =>array('value'=>'最少许可库存',      'type'=>'text',     'read'=>'2', 'add'=>'1', 'modify'=>'1', 'tip'=>'', 'search'=>1 ),
			'alarmCount'    =>array('value'=>'库存报警次数',      'type'=>'text',     'read'=>'2', 'add'=>'1', 'modify'=>'1', 'tip'=>'', 'search'=>1 ),
			'reserveCount'  =>array('value'=>'已预订数量',          'type'=>'text',     'read'=>'1', 'add'=>'1', 'modify'=>'1', 'tip'=>'', 'search'=>1 ),
			'saleSum'       =>array('value'=>'历史售出累计',      'type'=>'text',     'read'=>'1', 'add'=>'1', 'modify'=>'1', 'tip'=>'', 'search'=>1 ),
            'remark'        =>array('value'=>'备注',              'type'=>'textarea', 'read'=>'1', 'add'=>'1', 'modify'=>'1', 'tip'=>'', 'search'=>1 ),
        );
        return parent::getArrElement($findField,$editType,$fieldsExplainArr);
	}

}
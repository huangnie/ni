<?php
/**
 * user:huangnie
 */
require_once 'base.dao.php';

/**
 * Class DRepertoryDao
 */
final class DRepertoryDao extends BaseDao {

    private $code=null; 		//varchar
    private $num=null;          //varchar
    private $chName=null;
    private $enName=null;
    private $categoryId=null; 	//varchar
	private $principal=null;	//varchar
	private $pos=null;		    //varchar
	private $firm=null;			//varchar
	private $seller=null; 		//varchar
	private $buyDate=null;		//字符串
	private $cost=null; 		//float ,单位元
    private $theTime=null;		//时间戳
    private $state=null; 		//状态
    private $remark=null;       //备注

    function __construct(){
 		parent::__construct('xt');
        $this->init();
 	}

 	function init(){
        parent::init();
        $this->setTable("device_repertory");
        $this->code=null;
        $this->num=null;
        $this->chName=null;
        $this->enName=null;
        $this->categoryId=null;
		$this->principal=null;
		$this->pos=null;
		$this->firm=null;		
		$this->seller=null;
		$this->buyDate=null;
		$this->cost=null;
        $this->state=null;
        $this->theTime=null;
        $this->remark=null;
 	}

    function setCode($code=null){$this->code=$code; return $this;}
    function setNum($num=null){$this->num=$num; return $this;}
    function setChName($chName=null){$this->chName=$chName; return $this;}
    function setEnName($enName=null){$this->enName=$enName; return $this;}
    function setCategoryId($categoryId=null){$this->categoryId=$categoryId; return $this;}
 	function setPrincipal($principal=null){$this->principal=$principal; return $this;}
	function setPos($pos=null){$this->pos=$pos; return $this;}
	function setFirm($firm=null){$this->firm=$firm; return $this;}
	function setSeller($seller=null){$this->seller=$seller; return $this;}
	function setBuyDate($buyDate=null){$this->buyDate=$buyDate; return $this;}
	function setCost($cost=null){$this->cost=$cost; return $this;}
    function setState($state=null){$this->state=$state; return $this;}
    function setTheTime($theTime=null){$this->theTime=$theTime; return $this;}
    function setRemark($remark=null){$this->remark=$remark; return $this;}

    /**
     * @param int    $editType
     * @param string $findField
     * @return array
     */
    function getFieldsExplain($editType=1,$findField=''){
        $fieldsExplainArr=array(
            'code'          =>array('value'=>'扫描码',    'type'=>'text',      'read'=>'1',    'add'=>'1','modify'=>'1', 'search'=>1, 'tip'=>''),
            'num'           =>array('value'=>'编 号',     'type'=>'text',      'read'=>'1',    'add'=>'1','modify'=>'1', 'search'=>1, 'tip'=>''),
            'chName'        =>array('value'=>'中文命',    'type'=>'text',      'read'=>'1',    'add'=>'1','modify'=>'1', 'search'=>1, 'tip'=>''),
            'enName'        =>array('value'=>'英文名',    'type'=>'text',      'read'=>'1',    'add'=>'1','modify'=>'1', 'search'=>1, 'tip'=>''),
            'categoryId'    =>array('value'=>'分类号',    'type'=>'select',    'read'=>'0',    'add'=>'1','modify'=>'1', 'search'=>1, 'tip'=>'',  'selectArr'=>'getDeviceCategory()'), //不许将参数引起来
            'principal'     =>array('value'=>'责任人',    'type'=>'text',      'read'=>'1',    'add'=>'1','modify'=>'1', 'search'=>1, 'tip'=>''),
			'pos'           =>array('value'=>'存放位置',  'type'=>'text',      'read'=>'1',    'add'=>'1','modify'=>'1', 'search'=>0, 'tip'=>''),
			'firm'          =>array('value'=>'厂 家',     'type'=>'text',      'read'=>'1',    'add'=>'1','modify'=>'1', 'search'=>0, 'tip'=>''),
			'seller'        =>array('value'=>'供应商',    'type'=>'text',      'read'=>'1',    'add'=>'1','modify'=>'1', 'search'=>0, 'tip'=>''),
			'buyDate'       =>array('value'=>'购置时间',  'type'=>'text',      'read'=>'1',    'add'=>'1','modify'=>'1', 'search'=>0, 'tip'=>''),
			'cost'          =>array('value'=>'单 价',     'type'=>'text',      'read'=>'1',    'add'=>'1','modify'=>'1', 'search'=>0, 'tip'=>''),
            'state'         =>array('value'=>'在库状态',  'type'=>'select',    'read'=>'1',    'add'=>'0','modify'=>'1', 'search'=>0, 'tip'=>'', 'selectArr'=>$this->getSate(-1)),
            'theTime'       =>array('value'=>'登记时间',  'type'=>'hidden',    'read'=>'1',    'add'=>'0','modify'=>'1', 'search'=>0, 'tip'=>''),
            'remark'        =>array('value'=>'备 注',     'type'=>'textarea',  'read'=>'1',    'add'=>'1','modify'=>'1', 'search'=>0, 'tip'=>''),
        );
        return parent::getArrElement($findField,$editType,$fieldsExplainArr);
	}


    function getSate($type=''){
        $stateArr=array('1'=>'在库','2'=>'借出','3'=>'屏蔽');
        if(in_array($type,array(1,2,3))) return $stateArr["{$type}"];
        else return array('在库'=>'在库','借出'=>'借出','屏蔽'=>'屏蔽');
    }
}
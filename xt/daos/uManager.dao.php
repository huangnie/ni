<?php
/**
 * user:huangnie
 */
require_once 'base.dao.php';

/**
 * Class ManagerDao
 */
final class UManagerDao extends BaseDao{

    private $num=null;
    private $state=null;
    private $chName=null;	    //varchar
    private $enName=null;
    private $password=null;
    private $groupId=null;
    private $level=null;
    private $power=null;
    private $group=null;
	private $sex=null;
 	private $tel=null;
 	private $email=null;
 	private $regTime=null;
 	private $theTime=null;
    private $remark=null;           //备注

    function __construct(){
		parent::__construct('xt');
		$this->init();
 	}
 	
 	function init(){
        parent::init();
        parent::setTable("user_manager");
        $this->num=null;
        $this->state=null;
        $this->chName=null;
        $this->enName=null;
		$this->password=null;
        $this->groupId=null;
        $this->level=null;
        $this->power=null;
		$this->group=null;
		$this->sex=null;
	 	$this->tel=null;
	 	$this->email=null;
	 	$this->regTime=null;
	 	$this->theTime=null;
        $this->remark=null;
 	}

    function getNum(){return $this->num;}
    function getChName(){return $this->chName;}
    function getPassword(){return $this->password;}

    function setNum($num=null){$this->num=$num;return $this; }
    function setState($state=null){$this->state=$state;return $this;}  //组号
    function setChName($chName=null){$this->chName=$chName; return $this;}
    function setEnName($enName=null){$this->enName=$enName; return $this;}
	function setPassword($password=null){$this->password=$password;return $this;}
    function setGroupId($groupId=null){$this->groupId=$groupId;return $this;}  //组号
    function setLevel($level=null){$this->level=$level;return $this;}
    function setPower($power=null){$this->power=$power;return $this;}
	function setSex($sex=null){$this->sex=$sex;return $this;}
 	function setTel($tel=null){$this->tel=$tel;return $this;}
 	function setEmail($email=null){$this->email=$email;return $this;}
 	function setTheTime($theTime=null){$this->theTime=$theTime;return $this;}
 	function setRegTime($regTime=null){$this->regTime=$regTime;return $this;}
    function setRemark($remark=null){$this->remark=$remark; return $this;}

    /**
     * @param int    $editType
     * @param string $findField
     * @return array
     */
    function getFieldsExplain($editType=1,$findField=''){
        $fieldsExplainArr=array(
            'num'       =>array('value'=>'编码',           'type'=>'text',     'read'=>'1',  'add'=>'1','modify'=>'1',  'reg'=>0,  'tip'=>''),
            'chName'    =>array('value'=>'名称',           'type'=>'text',     'read'=>'1',  'add'=>'1','modify'=>'1',  'reg'=>1,  'tip'=>''),
            'enName'    =>array('value'=>'英文',           'type'=>'text',     'read'=>'1',  'add'=>'1','modify'=>'1',  'reg'=>1,  'tip'=>''),
            'password'  =>array('value'=>'密码',           'type'=>'text',     'read'=>'0',  'add'=>'1','modify'=>'1',  'reg'=>0,  'tip'=>''),
            'groupId'   =>array('value'=>'分组',           'type'=>'select',   'read'=>'0',  'add'=>'1','modify'=>'1',  'reg'=>0,  'tip'=>'',  'inputCheck'=>'', 'selectArr'=>'getGroup()'),
            'level'     =>array('value'=>'用户群',         'type'=>'select',   'read'=>'1',  'add'=>'0','modify'=>'1',  'reg'=>0,  'tip'=>'',  'selectArr'=>$this->getLevel()),
            'state'     =>array('value'=>'状态',           'type'=>'select',   'read'=>'0',  'add'=>'1','modify'=>'1',  'reg'=>0,  'tip'=>'',  'selectArr'=>array('可见'=>'可见','屏蔽'=>'屏蔽')),
            'power'     =>array('value'=>'权限',           'type'=>'select',   'read'=>'1',  'add'=>'1','modify'=>'1',  'reg'=>0,  'tip'=>'',  'selectArr'=>$this->getPower()),
            'sex'       =>array('value'=>'性别',           'type'=>'select',   'read'=>'1',  'add'=>'1','modify'=>'1',  'reg'=>1,  'tip'=>'',  'selectArr'=>array('男'=>'男','女'=>'女')),
            'tel'       =>array('value'=>'电话',           'type'=>'text',     'read'=>'1',  'add'=>'1','modify'=>'1',  'reg'=>1,  'tip'=>''),
            'email'     =>array('value'=>'邮箱',           'type'=>'text',     'read'=>'1',  'add'=>'1','modify'=>'1',  'reg'=>1,  'tip'=>''),
            'regTime'   =>array('value'=>'注册时间',       'type'=>'hidden',   'read'=>'1',  'add'=>'0','modify'=>'0',  'reg'=>0,  'tip'=>''),
            'theTime'   =>array('value'=>'上次登录时间',   'type'=>'hidden',   'read'=>'1',  'add'=>'1','modify'=>'1',  'reg'=>0,  'tip'=>''),
            'remark'    =>array('value'=>'备注',           'type'=>'textarea', 'read'=>'1',  'add'=>'1','modify'=>'1',  'reg'=>1,  'tip'=>''),
        );

		return $this->getArrElement($findField,$editType,$fieldsExplainArr);
	}

    /**
     * 常用权限配置
     * @return array
     */
    function getPower(){
        return array(
            '0000000001'=>'只查看',
            '0000001111'=>'撤销+借出+归还',
            '0000110011'=>'撤销+售出+回收',
            '0111000001'=>'添加+修改',
            '0111000000'=>'添加+修改+删除',
            '0111111111'=>'除配置外的所有权限',
            '1111111111'=>'所有权限',
        );
    }

    function getLevel(){
        return array(
            '成员'=>'成员',
            '组长'=>'组长',
        );
    }
}
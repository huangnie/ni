<?php
/**
 * user:huangnie
 */
require_once 'base.dao.php';

/**
 * Class CustomerDao
 */
final class UCustomerDao extends BaseDao{

    private $num=null;
    private $state=null;
    private $chName=null;	    //varchar
    private $enName=null;
    private $password=null;
    private $level=null;
    private $power=null;
    private $groupId=null;
    private $money=null;
	private $sex=null;
 	private $tel=null;
 	private $email=null;
    private $academy=null;
    private $grade=null;
    private $class=null;
 	private $regTime=null;
 	private $theTime=null;
    private $remark=null;           //备注

	function __construct(){
		parent::__construct('xt');
		$this->init();
 	}
 	
 	function init(){
        parent::init();
        parent::setTable("user_customer");
		$this->num=null;
		$this->state=null;
        $this->chName=null;
        $this->enName=null;
        $this->level=null;
		$this->password=null;
		$this->power=null;
		$this->groupId=null;
        $this->money=null;
		$this->sex=null;
	 	$this->tel=null;
	 	$this->email=null;
        $this->academy=null;
        $this->grade=null;
        $this->class=null;
	 	$this->regTime=null;
	 	$this->theTime=null;
        $this->remark=null;
    }


    function getByChName($chName='',$type=1){
        $this->select('*')->where('chName',$chName)->getResult($type);
        return $this->chName;
    }

    function getNum(){return $this->num;}
    function getChName(){return $this->chName;}
    function getPassword(){return $this->password;}

	function setNum($num=null){$this->num=$num; return $this;}
    function setState($state=null){$this->state=$state;return $this;}  //组号
    function setChName($chName=null){$this->chName=$chName; return $this;}
    function setPinyin($pinyin=null){$this->pinyin=$pinyin; return $this;}
    function setEnName($enName=null){$this->enName=$enName; return $this;}
    function setLevel($level=null){$this->level=$level;return $this;}
    function setPassword($password=null){$this->password=$password;return $this;}
	function setPower($power=null){$this->power=$power;return $this;}
    function setGroupId($groupId=null){$this->groupId=$groupId;return $this;}  //组号
    function setMoney($money=null){$this->money=$money;return $this;}  //组号
	function setSex($sex=null){$this->sex=$sex;return $this;}
 	function setTel($tel=null){$this->tel=$tel;return $this;}
 	function setEmail($email=null){$this->email=$email;return $this;}
    function setAcademy($academy=null){$this->academy=$academy;return $this;}
    function setGrade($grade=null){$this->grade=$grade;return $this;}
    function setClass($class=null){$this->class=$class;return $this;}
 	function setRegTime($regTime=null){$this->regTime=$regTime;return $this;}
 	function setTheTime($theTime=null){$this->theTime=$theTime;return $this;}
    function setRemark($remark=null){$this->remark=$remark; return $this;}

    /**
     * @param int    $editType
     * @param string $findField
     * @return array
     */
    function getFieldsExplain($editType=1,$findField=''){
        $fieldsExplainArr=array(
            'num'       =>array('value'=>'编号','type'=>'text','length'=>'15', 'add'=>'1','modify'=>'1','read'=>'1', 'reg'=>1,
                                'inputCheck'=>'/[1-9][0-9]{6,12}/','length'=>'15','tip'=>'必填 6-12 位数字(以非零开头)'),

            'chName'    =>array('value'=>'中文名','type'=>'text', 'length'=>'15','add'=>'1','modify'=>'1','read'=>'1', 'reg'=>1,
                                'inputCheck'=>'/^[\x{4e00}-\x{9fa5}]+/u','tip'=>'(须全为中文 ， 且必填)'),

            'enName'    =>array('value'=>'英文名','type'=>'text','length'=>'15', 'add'=>'1','modify'=>'1','read'=>'1',  'reg'=>1,
                                'inputCheck'=>'','tip'=>'可选'),

            'password'  =>array('value'=>'密码','type'=>'text', 'length'=>'15', 'add'=>'1','modify'=>'0','read'=>'0', 'reg'=>1,
                                'inputCheck'=>'/[^\'"]{6,12}/','tip'=>'必填 6-12 位(不能有引号)'),

            'level'     =>array('value'=>'用户群','type'=>'select','length'=>'15','add'=>'0','modify'=>'1','read'=>'1',
                                'inputCheck'=>'','tip'=>'必选','selectArr'=>$this->getLevel()),

            'power'     =>array('value'=>'权限','type'=>'select','length'=>'15','add'=>'1','modify'=>'1','read'=>'1', 'reg'=>0,
                                'inputCheck'=>'/[10]{4}/','tip'=>'必选','selectArr'=>$this->getPower()),

            'groupId'     =>array('value'=>'分组','type'=>'select','length'=>'15',  'add'=>'1','modify'=>'1','read'=>'0', 'reg'=>0,
                                'inputCheck'=>'','tip'=>'' , 'selectArr'=>'getGroup()'),

            'money'     =>array('value'=>'余款','type'=>'text','length'=>'15',  'add'=>'1','modify'=>'1','read'=>'1', 'reg'=>0,
                                 'inputCheck'=>'','tip'=>''),

            'state'    =>array('value'=>'可用','type'=>'select','length'=>'15','length'=>'15','add'=>'1','modify'=>'1','read'=>'0', 'reg'=>0,
                                'inputCheck'=>'/(可见)|(屏敝)/','tip'=>'必选','selectArr'=>array('可见'=>'可见','屏蔽'=>'屏蔽')),

            'sex'       =>array('value'=>'性别','type'=>'select','length'=>'15','add'=>'1','modify'=>'1','read'=>'1', 'reg'=>1,
                                'inputCheck'=>'/男|女/','tip'=>'必选','selectArr'=>array('男'=>'男','女'=>'女')),

            'tel'       =>array('value'=>'电话','type'=>'text', 'length'=>'15', 'add'=>'1','modify'=>'1','read'=>'1', 'reg'=>1,
                                'tip'=>''),

            'email'     =>array('value'=>'邮箱','type'=>'text', 'length'=>'25', 'add'=>'1','modify'=>'1','read'=>'1', 'reg'=>1,
                                'inputCheck'=>'/[\w]{3,15}@[\w]{2,9}\.[\w]{3,9}/','tip'=>''),

            'academy'   =>array('value'=>'学院','type'=>'text', 'length'=>'15', 'add'=>'1','modify'=>'1','read'=>'1', 'reg'=>1,
                                'inputCheck'=>'','tip'=>''),

            'grade'     =>array('value'=>'年级','type'=>'text', 'length'=>'15', 'add'=>'1','modify'=>'1','read'=>'1', 'reg'=>1,
                                'inputCheck'=>'','tip'=>''),

            'class'     =>array('value'=>'班级','type'=>'text', 'length'=>'15', 'add'=>'1','modify'=>'1','read'=>'1', 'reg'=>1,
                                'tip'=>''),

			'regTime'   =>array('value'=>'注册时间','type'=>'hidden','length'=>'15', 'add'=>'0','modify'=>'0','read'=>'1', 'reg'=>0,
                                'inputCheck'=>'','tip'=>''),

			'theTime'=>array('value'=>'上次登录时间','type'=>'hidden','length'=>'15', 'add'=>'1','modify'=>'1','read'=>'1', 'reg'=>0, 'default'=>time(),
                                   'inputCheck'=>'', 'tip'=>''),

            'remark'     =>array('value'=>'备注',      'type'=>'textarea','length'=>'15', 'add'=>'1','modify'=>'1','read'=>'1', 'reg'=>1,
                                   'inputCheck'=>'', 'tip'=>''),
        );
		return $this->getArrElement($findField,$editType,$fieldsExplainArr);
	}

    /**
     * 权限
     * @return array
     */
    function getPower(){
        return array(
            '0001'=>'只可查看',
            '0111'=>'可预约+取消',
            '1111'=>'可预约+取消+续借',
        );
    }

    /**
     * 级别
     * @return array
     */
    function getLevel(){
        return array(
            'member'=>'成员',
            'headman'=>'组长',
        );
    }

}
<?php
/**
 * User: huangnie
 */

/**
 * 用户和管理员基类
 */
class UserController extends Ni_Controller{

    /**
     * ui组件
     * @var null
     */
    protected $ui=null;
    /**
     * 权限控制
     * @var null
     */
    protected $check=null;
    /**
     * 目录映射
     * @var array
     */
    protected  $dirMap=array(
        'c'=>'consume',
        'd'=>'device',
        'l'=>'lab',
        'u'=>'user',
    );

    function __construct(){
        parent::__construct();   
        $this->ui=$this->lib('ui');
        $this->check=$this->lib('check');
    }

    /**
     * 默认到登陆页面
     */
    function index(){
        $data['test']='no';
        $this->displayView('login',$data);
    }

    /**
     * 浏览器 登录
     */
    function login(){
        if($this->__PARAM(1)=='input'){
            $data['editForm']=$this->ui->getLoginForm();
            $data['uri']=BASE_URI."/index.php/xt/user/login/enter";
            $this->displayForm('edit',$data);
        }
        else if($this->__PARAM(1)=='enter'){
            $userName=$this->__PARAM('name');
            $userPassword=$this->__PARAM('password');
            $userIdentity=$this->__PARAM('identity');
            if(!in_array($userIdentity,array('uCustomer','uManager'))){
                echo json_encode(array('tip'=>'警告','status'=>0,'content'=>'请选择用户身份'));  exit();
            }
            if($userName==''){
                echo json_encode(array('tip'=>'警告','status'=>0,'content'=>'用户名不能为空'));  exit();
            }
            if($userPassword==''){
                echo json_encode(array('tip'=>'警告','status'=>0,'content'=>'密码不能为空'));  exit();
            }
            $this->enter($userIdentity,$userName,$userPassword);
        }
        else{
            $this->displayView('login');
        }
    }

    /**
     * 验证用户信息
     * @param $userIdentity
     * @param $userName
     * @param $userPassword
     */
    protected function enter($userIdentity,$userName,$userPassword){
        $activity= $this->model('user_uActivity');
        $user=$activity->loginCheck($userIdentity,$userName,$userPassword);
        if($user==-1) {
            echo json_encode(array('tip'=>'登陆失败1','status'=>0,'content'=>'用户不存在')); exit();
        }elseif(!$user){
            echo json_encode(array('tip'=>'登陆失败2','status'=>0,'content'=>'密码错误')); exit();
        }else {
            $user['identity']=$userIdentity;
            $user['plainPassword']=$userPassword;
            $_SESSION['loginUser']=$user;
            echo json_encode(array('tip'=>'url','status'=>1,'content'=>"http://{$_SERVER['HTTP_HOST']}".BASE_URI."/index.php/xt/{$userIdentity}/table/d_category_1"));
            exit();
        }
    }

    /**
     * id 简单加密
     * @param int $id
     * @return float
     */
    protected function idEncode($id=0){
        return ($id*100)/5;
    }

    /**
     * id 解密
     * @param int $encodeId
     * @return float
     */
    protected function idDecode($encodeId=0){
        return ($encodeId*5)/100;
    }

    /**
     * 登录用户修改个人基本信息
     */
    function changeBaseInfo(){
        if(!$this->check->isLogin()) $this->login();
        else{
            $identity=$this->check->getUserIdentity();
            $model=$this->getActivityModel("u_".strtolower(substr($identity,1)).'_1');
            $rs=$model->modify($this->__PARAM('detail'),$this->check->getUserId(),2);
            if(is_array($rs)){
                $data['editForm'] = $this->ui->getEditForm($rs['detail'],$rs['fieldsExplain']);
                $data['uri']=$rs['uri'];
                $this->displayForm('edit',$data);
            }else{
                echo $this->operateTipJson($rs,'修改');
                exit();
            }
        }
    }

    /**
     * 登录用户修改个人密码
     */
    function changePassword(){
        if(!$this->check->isLogin()) $this->login();
        else{
            $editId=$this->__PARAM('editId','');
            if($editId == ''){
                $data['editForm'] =  $this->ui->getChangePasswordForm($this->check->getUserId());
                $data['uri']=$_SERVER['REQUEST_URI'];
                $this->displayForm('edit',$data);
            }else{
                $oldPassword=$this->__PARAM('oldPassword','');
                $newPassword=$this->__PARAM('newPassword','');
                $checkPassword=$this->__PARAM('checkPassword','');
                if($newPassword != $checkPassword || $oldPassword=='' || $newPassword=='') {
                    echo json_encode(array('tip'=>'失败','status'=>0,'content'=>'输入有误'));
                    exit();
                }
                $identity=$this->check->getUserIdentity();
                $model=$this->getActivityModel("u_".strtolower(substr($identity,1)).'_1');
                $rs = $model->changePassword($this->check->getUserId(),$oldPassword,$newPassword);
                echo $this->operateTipJson($rs,"密码修改");
                exit();
            }
        }
    }

    /**
     * 注销 退出系统
     */
    function logout(){
        $this->check->logout();
        $this->index();
    }

    /**
     * 数据列表
     * @param string $tds
     * @param int $page
     * @param int $pageSize
     * @param int $param1
     */
    function table($tds='d_reserve_1',$page=1,$pageSize=15,$param1=0){
        if(!$this->check->isLogin()) $this->login();
        else{           
            $data['page'] = $page;
            $data['pageSize'] = $pageSize;
            $data['fieldIndex'] = $this->__PARAM('fieldIndex','');
            $data['fieldValue'] = $this->__PARAM('fieldValue','');
            $data['timeStart'] = $this->__PARAM('timeStart','');
            $data['timeEnd'] = $this->__PARAM('timeEnd','');		

			$this->ui->init( $this->check,$tds);
			$data['table']=$this->getTable($data, $tds, $param1);
			$data['navHtml']=$this->ui->getNav();
			$data['menuHtml']=$this->ui->getMenu();
			$data['userHtml']=$this->ui->getUser();
			
	
            $this->displayView('table',$data);
        }
    }

    /**
     * 获取主要数据table（searchFieldArr + theader + tbody + pageBar）
     * @param array $data
     * @param string $tds
     * @param $param1
     * @param bool $isViewSql
     * @return mixed
     */
    private function getTable(array $data, $tds='d_category_1',$param1,$isViewSql=false){
		$arr=explode('_',$tds);
        $index=strtolower($arr[0]);
        $func=strtolower($arr[1]);
        $state=strtolower($arr[2]);
        return $this->getInfoModel($index)->init($this->ui, $data)->$func($isViewSql, $state, $param1);
	}
	
	 /**
     * 获取 info 模型
     * @param string $index
     * @return mixed|string
     */
    protected function getInfoModel($index='d'){
        return $this->getModel($index,'Info');
    }

    /**
     * 获取 activity 模型
     * @param string $tds
     * @return mixed|string
     */
    protected function getActivityModel($tds='d_category_1'){
		$this->ui->init( $this->check,$tds); 
        return $this->getModel(substr($tds,0,1), 'Activity')->init($this->ui);
    }

    /**
     * 获取魔型
     * @param string $index
     * @param string $model
     * @return string
     */
    private function getModel($index='d',$model='Activity'){     
        $modelPath=$this->dirMap[$index]."_{$index}{$model}";
        $model= $this->model($modelPath);
        if(!is_object($model)){
            echo json_encode(array('tip'=>'抱歉','status'=>0,'content'=>"操作有误，URL路径参数 不对"));  exit();
        }else{
			return $model;
		}
    }

    /**
     * 操作结果提示
     * @param $rs
     * @param string $name
     * @return string
     */
    protected function operateTipJson($rs,$name=''){
        if(is_bool($rs) && $rs == true) {
            return json_encode(array('tip'=>'恭喜','status'=>1,'content'=>"{$name}操作成功"));
        }elseif(is_bool($rs) && $rs==false){
            return json_encode(array('tip'=>'抱歉','status'=>0,'content'=>"{$name}操作失败"));
        }else{
            return json_encode(array('tip'=>'警告','status'=>0,'content'=>$rs));
        }
    }

    /**
     * 操作结果提示
     * @param $rs
     * @param string $name
     * @return string
     */
    protected function operateTipStr($rs,$name=''){
        if(is_bool($rs) && $rs == true) {
            return "恭喜 {$name}操作成功";
        }elseif(is_bool($rs) && $rs==false){
            return "抱歉 {$name}操作失败";
        }else{
            return "警告 {$rs}";
        }
    }

}
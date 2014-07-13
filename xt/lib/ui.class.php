<?php
/**
 * UI 组件封装
 */
class UiLib {

	/**
     * 当前菜单(也即 "类别_实体名_状态" )
     * @var string
     */
    private $tds='d_category_1';

    /**
     * 当前页
     * @var int
     */
    private $page=1;

    /**
     * 每页条数
     * @var int
     */
    private $pageSize=15;

    /**
     * 用户导航
     * @var array
     */
    private  $customerNav=array(
        'd'=>array('tds'=>'d_category_1',  'name'=>"设备借用",'class'=>'not_select'),
        'c'=>array('tds'=>'c_category_1',  'name'=>"耗材购买",'class'=>'not_select'),
        'l'=>array('tds'=>'l_room_1',      'name'=>"实验室",  'class'=>'not_select'),
    );

    /**
     * 管理员导航
     * @var array
     */
    private $managerNav=array(
        'd'=>array('tds'=>'d_repertory_1',  'name'=>"设备管理",   'class'=>'not_select'),
        'c'=>array('tds'=>'c_repertory_1',  'name'=>"耗材管理",   'class'=>'not_select'),
        'l'=>array('tds'=>'l_room_1',        'name'=>"实验室管理", 'class'=>'not_select'),
        'u'=>array('tds'=>'u_customer_1',    'name'=>"系统",       'class'=>'not_select'),
    );

    /**
     * 用户菜单
     * @var array
     */
    private  $managerMenu=array(
        'd'=>array(
            '库  存'=>array(  'd_category_1'=>'设备种类',  'd_repertory_1'=>'设备信息',  'd_repair_1'=>'维修记录' ),
            '审  批'=>array(  'd_reserve_1'=>'待 借',      'd_reserve_3'=>'已撤销',       'd_reserve_2'=>'已取消'),
            '未  还'=>array(  'd_lend_1'=>'全 部',         'd_lend_2'=>'未续借',         'd_lend_3'=>'已超期' ),
            '已  还'=>array(  'd_revert_1'=>'全 部',       'd_revert_2'=>'已续借',       'd_revert_3'=>'已超期'),
        ),
        'c'=>array(
            '库  存'=>array(  'c_category_1'=>'耗材种类',  'c_repertory_1'=>'耗材信息' , 'c_repertory_2'=>'紧缺耗材' ),
            '审  批'=>array(  'c_reserve_1'=>'已预订',     'c_reserve_3'=>'已撤消',      'c_reserve_2'=>'已取消', ),
            '交  易'=>array(  'c_reserve_4'=>'已出售',        'c_recycle_1'=>'已回收'  ),
        ),
        'l'=>array(
            '实验室'=>array(  'l_room_1'=>'教 室',        'l_desk_1'=>'实验桌' ),
        ),
        'u'=>array(
            '学  生'=>array(  'u_customer_1'=>'信息列表' ),
            '管理员'=>array( 'u_manager_1'=>'信息列表' ),
            '用户组'=>array(  'u_group_1'=>'信息列表' ),
            '日  志'=>array(  'u_log_1'=>'信息列表' ),
        )
    );

    /**
     * 管理员菜单
     * @var array
     */
    private $customerMenu=array(
        'd'=>array(
            '预  约'=>array(  'd_category_1'=>'可预约',    'd_reserve_1'=>'已预约',    'd_reserve_2'=>'已取消' ,  'd_reserve_3'=>'已撤销',),
            '未  还'=>array(  'd_lend_1'=>'全 部',         'd_lend_2'=>'未续借',       'd_lend_3'=>'已超期' ),
            '已  还'=>array(  'd_revert_1'=>'全 部',       'd_revert_2'=>'已续借',     'd_revert_3'=>'已超期'),
        ),
        'c'=>array(
            '预  订'=>array(  'c_repertory_1'=>'可预订',   'c_reserve_1'=>'已预订',  'c_reserve_2'=>'已取消', 'c_reserve_3'=>'已撤消'),
            '交  易'=>array(  'c_reserve_4'=>'已 购',         'c_recycle_1'=>'已回收' ),
        ),
        'l'=>array(
            '实验室'=>array( 'l_room_1'=>'教 室',         'l_desk_1'=>'实验桌' ),
        ),
    );

    /**
     * 权限验证（obj）
     * @var null
     */
    private $check=null;

    function __construct($check=null,$tds=''){
        $this->init($check, $tds);
    }

    /**
     * 是已否初始化
     * @return bool
     */
    function isInit(){
        return is_object($this->check) && preg_match('/[a-z]+_[a-zA-Z]+_[a-zA-Z0-9+]/',$this->tds);
    }

    /**
     * 初始化类
     * @param null $check
     * @param string $tds
     * @return $this
     */
    function init($check=null,$tds=''){
        $this->check=$check;
        $this->tds=$tds;
        return $this;
    }

    /**
     * 获取当前菜单
     * @return string
     */
    function getTds(){
		return $this->tds;	
	}

    /**
     * 获取验类证实例
     * @return null
     */
    function getCheck(){
		return $this->check;	
	}

    /**
     * 主页导航条
     * @return string
     */
    function getNav(){
		$index=substr($this->tds,0,1);
        if($this->check->isManager())$curNav=$this->managerNav;
        elseif($this->check->isCustomer())$curNav=$this->customerNav;
        else $curNav=$this->managerNav;
        if(in_array($index,array_keys($curNav))) $curNav[$index]['class']='select';
        $navHtml='<ul>';
        foreach($curNav as $nav){
            $navHtml.='<li><a class="'.$nav['class'].' r_t2_3"  id="'.$nav['tds'].'" href="'.BASE_URI.'/index.php/xt/'.$this->check->getUserIdentity().'/table/'.$nav['tds'].'"> '.$nav['name'].' </a></li>';
        }
        $navHtml.= '</ul>';
        return $navHtml;
    }

    /**
     * 用户个人下拉菜单
     * @return string
     */
    function getUser(){
        $userHtml= '<ul class="loginUserCenter">
                        <li>
                            <a title="单击" href="javascript:;">用户 '.$this->check->getUserName().'</a>
                            <ul class="loginUserInfo" style="display: none;">
                                <li>'.$this->createEditBtn('changeBaseInfo','基本信息',false).'</li>
                                <li>'.$this->createEditBtn('changePassword','修改密码',false).'</li>
                                <li>'.$this->createLink('logout','退出系统',false).'</li>
                            </ul>
                        </li>
                    </ul>';
        return $userHtml;
    }

    /**
     * 导航菜单
     * @return null|string
     */
    function getMenu(){
        if($this->check->isCustomer()) $theMenu = $this->customerMenu;
        else if($this->check->isManager()) $theMenu = $this->managerMenu;
        else return null;
        $index=substr($this->tds,0,1);
        if (in_array($index,array_keys($theMenu))){
            $cueMenu=$theMenu[$index];
            $menuHtml='';
            foreach ($cueMenu as $name=>$subMenu){
                $menuHtml.='<ul><li>';
                $menuHtml.='<a class="title r_ltb_3" href="javascript:;">'.$name.'</a></li>';
                foreach($subMenu as $key=>$name2){
                    $css_class=$this->tds==$key?'select':'not_select';
                    $menuHtml.='<li>';
                    $menuHtml.='<a class="'.$css_class.' r_4c_3" href="'.BASE_URI.'/index.php/xt/'.$this->check->getUserIdentity().'/table/'.$key.'">'.$name2.'</a></li>';
                }
                $menuHtml.='</ul>';
            }
            return $menuHtml;
        }
        else return '';
    }

    /**
     * 设备借出或归还表单
     * @param int $reserveId
     * @param string $type
     * @return string
     */
    function getLendOrRevertForm($reserveId=0,$type=''){
        $selectArr=array('code'=>'扫描码','num'=>'设备编号');
        $form=$this->createInput('hidden',"editId",$reserveId);
        if($type=='lend') $form.='<ul><li class="label">用户编号</li><li class="edit"><a title="输入编号">'.$this->createInput('text',"num",'',15).'</a></li></ul>';
        $form.='<ul><li class="label"><a title="选择方式">'.$this->createSelect('select',$selectArr).'</li>';
        $form.='<li class="edit"><a title="输入内容">'.$this->createInput('text',"value",'',15).'</a></li></ul> ';
        return $form;
    }

    /**
     * 普通用户预定耗材表单
     * @param int $consumeId ,还利用该变量可防攻击
     * @return string
     */
    function getConsumeReserveForm($consumeId){
        $form=$this->createInput('hidden',"editId",$consumeId);
        $form.='<ul> <li class="label">数 量</li><li class="edit"><a title="至少为 1 ">'.$this->createInput('text',"theCount",'1',15).'</a></li></ul>';
        return $form;
    }

    /**
     * 管理员回收耗材表单
     * @param int $reserveId ,还利用该变量可防攻击
     * @return string
     */
    function getSaleForm($reserveId){
        $form=$this->createInput('hidden',"editId",$reserveId);
        $form.='<ul><li class="label">提示</li><li class="edit">借出时需验证用户编号</li></ul>';
        $form.='<ul> <li class="label">用户编号</li><li class="edit">'.$this->createInput('text',"num",'',15).'</li></ul>';
        return $form;
    }

    /**
     * 管理员回收耗材表单
     * @param int $reserveId ,还利用该变量可防攻击
     * @return string
     */
    function getRecycleForm($reserveId){
        $form=$this->createInput('hidden',"editId",$reserveId);
        $form.='<ul><li class="label">提示</li><li class="edit">借出时需验证用户编号</li></ul>';
        $form.='<ul> <li class="label">数 量</li><li class="edit">'.$this->createInput('text',"theCount",'',15).'</li></ul>';
        $form.='<ul> <li class="label">可用率</li><li class="edit">'.$this->createInput('text',"rate",'',15).'</li></ul>';
        $form.='<ul> <li class="label">用户编号</li><li class="edit">'.$this->createInput('text',"num",'',15).'</li></ul>';
        return $form;
    }

    /**
     * 实验卓配置表单
     * @param $deskId
     * @param array $deviceArr
     * @param array $bindDevice
     * @return string
     */
    function getDeskBindDeviceForm($deskId,array $deviceArr, array $bindDevice=null){
        $form=$this->createInput('hidden',"editId",$deskId);
        $form.='<ul> <li class="label">设备一</li><li class="edit">'.$this->createSelect('deviceId[1]',$deviceArr, isset($bindDevice[0]) ? $bindDevice[0] : 0).'</li></ul>';
        $form.='<ul> <li class="label">设备二</li><li class="edit">'.$this->createSelect('deviceId[2]',$deviceArr, isset($bindDevice[1]) ? $bindDevice[1] : 0).'</li></ul>';
        $form.='<ul> <li class="label">设备三</li><li class="edit">'.$this->createSelect('deviceId[3]',$deviceArr, isset($bindDevice[2]) ? $bindDevice[2] : 0).'</li></ul>';
        return $form;
    }

    function getChangePasswordForm($userId){
        $form=$this->createInput('hidden',"editId",$userId);
        $form.='<ul> <li class="label">原始密码</li><li class="edit">'.$this->createInput('text',"oldPassword",'输入原始密码',15).'</li></ul>';
        $form.='<ul> <li class="label">重置密码</li><li class="edit">'.$this->createInput('password',"newPassword",'',15).'</li></ul>';
        $form.='<ul> <li class="label">确认密码</li><li class="edit">'.$this->createInput('password',"checkPassword",'',15).'</li></ul>';
        return $form;
    }


    function getResetPasswordForm($userId){
        $form=$this->createInput('hidden',"editId",$userId);
        $form.='<ul> <li class="label">重置密码</li><li class="edit">'.$this->createInput('text',"newPassword",'',15).'</li></ul>';
        return $form;
    }

    /**
     * 管理员权限配置表单
     * @param int $managerId
     * @param array $oldPower
     * @return string
     */
    function getManagerPowerConfigForm($managerId=0,array $oldPower=null){
        $form=$this->createInput('hidden',"editId",$managerId);
        $form.='<ul><li class="space">&nbsp;</li>';
        $form.='<li>撤 销</li><li>'.$this->createCheckbox('power[repeal]',$oldPower['repeal']).'</li>';
        $form.='<li>借 出</li><li>'.$this->createCheckbox('power[lend]',$oldPower['lend']).'</li>';
        $form.='<li>归 还</li><li>'.$this->createCheckbox('power[revert]',$oldPower['revert']).'</li></ul>';

        $form.='<ul><li class="space">&nbsp;</li>';
        $form.='<li>售 出</li><li>'.$this->createCheckbox('power[sale]',$oldPower['sale']).'</li>';
        $form.='<li>回 收</li><li>'.$this->createCheckbox('power[recycle]',$oldPower['recycle']).'</li></ul>';

        $form.='<ul><li class="space">&nbsp;</li>';
        $form.='<li>添 加</li><li>'.$this->createCheckbox('power[add]',$oldPower['add']).'</li>';
        $form.='<li>修 改</li><li>'.$this->createCheckbox('power[modify]',$oldPower['modify']).'</li>';
        $form.='<li>删 除</li><li>'.$this->createCheckbox('power[delete]',$oldPower['delete']).'</li></ul>';

        $form.='<ul><li class="space">&nbsp;</li>';
        $form.='<li>配 置</li><li>'.$this->createCheckbox('power[config]',$oldPower['config']).'</li></ul>';
        return $form;
    }

    /**
     * 编辑表单
     * @param $detail
     * @param $fieldsExplain
     * @return string
     */
    function getEditForm(array $detail,array $fieldsExplain){
        $result=isset($detail)?$detail['result']:'';
        $num=1;
        $form='';
        foreach ($fieldsExplain as $field=>$explain){
            $value= $result=='' ? '' : $result[$field];
            if($explain['type']=='hidden'){
                $value= preg_match('/Time/i',$field) ? time() : '';
                $form.=$this->createInput('hidden',"detail[{$num}]",$value,isset($explain['length'])?$explain['length']:15);
            }else{
                $form.='<ul>';
                $form.='<li class="label">'.$fieldsExplain[$field]['value'].'</li>';
                if($explain['type']=="text"){
                    $css_class= preg_match('/Date/i',$field) ? 'datePicker ' : '';
                    $form.='<li class="edit"><a title="'.$explain['tip'].'">'.$this->createInput('text',"detail[{$num}]",$value,isset($explain['length'])?$explain['length']:15,$css_class).'</a></li>';
                }
                else if($explain['type']=="select"){
                    $form.='<li class="edit">'.$this->createSelect("detail[{$num}]",$explain['selectArr'],$value).'</li>';
                }
                else if($explain['type']=="textarea"){
                    $form.='<li class="edit">'.$this->createTextarea("detail[{$num}]",$value).'</li>';
                }
                $form.='</ul>';
            }
            $num++;
        }
        return $form;
    }

    /**
     * 登录表单
     * @return string
     */
    function getLoginForm(){
        $loginForm= ' <ul><li class="label"> 账 户 </li><li class="edit"><input type="text" class="r_4c_5" name="name" id="name" value=""/></li></ul>';
        $loginForm.= '<ul><li class="label"> 密 码 </li> <li class="edit"><input type="password" class="r_4c_5" name="password" id="password" value=""/></li></ul>';
        $loginForm.= '<ul><li class="label"> 身 份 </li><li class="edit"> <select name="identity" class="r_4c_5"><option value="uCustomer" selected>普通用户 </option><option value="uManager">管理员</option></select></li> </ul>';
        return $loginForm;
    }

    /**
     *  搜索模块 和 其他附加操作
     * @param string $link
     * @param string $uri
     * @param array $searchFieldArr
     * @param string $fieldIndex
     * @param string $timeStart
     * @param string $timeEnd
     * @param string $fieldValue
     * @return string
     */
    function getAddition($link='',$uri='',array $searchFieldArr=null,$fieldIndex='',$timeStart='',$timeEnd='',$fieldValue=''){
        $addition='';
        if($link)$addition.='<aside class="link"> '.$link.'</aside>';
        if($uri){
            $addition.='<article class="search"><form class="searchForm" id="searchForm" method="get" action="'.$uri.'">';
            $addition.='<section class="condition"><ul>';
            $addition.='<li><select name="fieldIndex" id="fieldIndex" class="r_4c_4"> <option value="-1">选择</option> ';
            if(is_array($searchFieldArr) && count($searchFieldArr)>0){
                foreach ($searchFieldArr as $num=>$row){
                    $selected = isset($fieldIndex) && $fieldIndex == $num ? 'selected' : '';
                    $addition.= '<option value="'.$num.'"  '.$selected.'>'.$row['value'].'</option>';
                }
            }
            $addition.= '</select></li>';
            $addition.='<li><a title="开始时间"><input type="text" id="timeStart" class="datePicker r_4c_4 p_l_5" name="timeStart" value="'.$timeStart.'"/></a></li><li>&nbsp;至&nbsp;</li>';
            $addition.='<li><a title="结束时间"><input type="text" id="timeEnd"   class="datePicker r_4c_4 p_l_5" name="timeEnd"    value="'.$timeEnd.'"/></a></li><li>&nbsp;&nbsp;</li>';
            $addition.='</ul></section>';
            $addition.='<section class="searchValue r_4c_10">';
            $addition.='<a title="搜索内容"><span></span> <input type="text" class="searchInput r_c4_5" id="fieldValue" name="fieldValue" value="'.$fieldValue.'"/></a>';
            $addition.='<button class="searchBtn">  <img src="'.BASE_URI.'/public/lib/img/bg/search_red.gif" title="站内搜索">';
            $addition.='</button></section>';
            $addition.='</form></article>';
        }

        return $addition;
    }

    /**
     * 表格标题
     * @param array $fullFieldsExplain
     * @param string $operate
     * @return string
     */
    function getTHeader(array $fullFieldsExplain=null,$operate=''){
        $theader='<tr>';
        foreach ($fullFieldsExplain as $explain) $theader.='<th>'.$explain['value'].'</th>';
        if((is_array($operate) && count(array_filter($operate)) > 0 )) $theader.="<th> 操 作 </th>";
        $theader.='</tr>';
        return $theader;
    }

    /**
     * 表格数据内容
     * @param array $result
     * @param array $operate
     * @return string
     */
    function getTBody(array $result=null,array $operate=null){
        if(is_array($operate)) $operate=array_filter($operate);
        $tbody='';
        foreach ($result as $row){
            $tbody.= '<tr>';
            $id=$row['id'];
            if(isset($row['state']))$row['state'] = $this->check->getDCState($row['state']);
            unset($row['id']);
            foreach ($row as $filed=>$value){
                if(preg_match('/.*Time/i',$filed)) $value= $value > 13241234 ? date('Y-m-d',$value) : '未知';
                $tbody.="<td>{$value}</td>";
            }

            //
            if(is_array($operate) && count($operate) > 0) {
                $tmpOperateArr=$operate;
                if(isset($tmpOperateArr['renewBtn']) && isset($row['renewDays']) && $row['renewDays'] > 0 ) unset($tmpOperateArr['renewBtn']);
                $tmpOperateArr=implode(' | ',$tmpOperateArr);
                if(trim($tmpOperateArr) !='')$tbody.='<td>'.str_replace('row_id',$id,$tmpOperateArr).'</td>';
                else  $tbody.='<td> -- -- </td>';
            }

            $tbody.= '</tr>';
        }
        return $tbody;
    }

    /**
     * 分页导航条
     * @param int $totalCount
     * @param int $page
     * @param int $pageSize
     * @return string
     */
    function getPageBar($totalCount=1,$page=1,$pageSize=15){
        //分页导航	
        $pageBar='<ul>';
        if(is_numeric($totalCount) && $totalCount >0){
            $pageBar.='<li><a class="r_4c_3" href="javascript:;">共 '.$totalCount.' 条记录</a></li>';
            $barSize=10;
            $pageCount=ceil($totalCount/$pageSize); //总页数
			if($pageCount <= 1) return '';
			$page= $pageCount < $page ? $pageCount : $page ;
            $pageBar.='<li><a class="r_4c_3" href="javascript:;"> <span class="t_red">'.$page.'</span>/'.$pageCount.' 页</a></li>';
            $barStart=floor($page/$barSize)*$barSize+1;
            $barEnd=$barStart+$barSize;
            $barEnd=$barEnd<$pageCount?$barEnd:$pageCount;
            $uri=$this->createUri("table/{$this->tds}",false);
            $request=explode('?', $_SERVER['REQUEST_URI']);
            $request=isset($request[1]) && $request[1]!= '' ? "?{$request[1]}" : '';

            if($barStart>1){
                $pageBar.='<li> <a class="r_4c_3" href="'.$uri.'/1'.$request.'"> 首 页 </a></li>';
                $pageBar.='<li> <a class="r_4c_3" href="'.$uri.'/'.($page-1).$request.'"> << </a></li>';
            }
            for($index=$barStart; $index <= $barEnd; $index++){
                if($index==$page)$pageBar.='<li> <a class="r_4c_2 currentPage" href="'.$uri.'/'.$index.$request.'" > '.$index.' </a> </li>';
                else $pageBar.='<li> <a class="r_4c_2" href="'.$uri.'/'.$index.$request.'" > '.$index.' </a> </li>';
            }
            if($barEnd<$pageCount){
                $pageBar.='<li> <a class="r_4c_3" href="'.$uri.'/'.($page+1).$request.'"> >> </a></li>';
                $pageBar.='<li> <a class="r_4c_3" href="'.$uri.'/'.$pageCount.$request.'"> 末 页 </a></li>';
            }
        }
        $pageBar.='</ul>';
        return $pageBar;
    }

    /**
     * 输入框
     * @param string $type
     * @param string $name
     * @param string $value
     * @param string $maxLength
     * @param string $css_class
     * @return string
     */
    function createInput($type='text',$name='text',$value='',$maxLength='30',$css_class=''){
        $css_class=$type=='hidden' ? $css_class.' hide':$css_class.' r_4c_3';
        return '<input type="'.$type.'" maxlength="'.$maxLength.'" class="'.$css_class.'" name="'.$name.'" value="'.$value.'"/>';
    }

    /**
     * 文本框
     * @param string $name
     * @param string $value
     * @param int $col
     * @param int $row
     * @param string $length
     * @return string
     */
    function createTextarea($name='textarea',$value='',$col=25,$row=3,$length=''){
        return '<textarea  maxlength="'.$length.'" class="r_4c_3" name="'.$name.'" cols="'.$col.'" rows="'.$row.'">'.$value.'</textarea>';
    }

    /**
     * 下拉框（选择）
     * @param string $name
     * @param $selectArr
     * @param string $default
     * @return string
     */
    function createSelect($name='select',$selectArr,$default=''){
        $select='<select name="'.$name.'" class="r_4c_3">';
        foreach($selectArr as $value=>$name){
            $selected= ($default!='' && $default==$value)?'selected':'';
            $select.= '<option value="'.$value.'" '.$selected.'>'.$name.'</option>';
        }
        $select.= '</select>';
        return $select;
    }

    /**
     * 复选框
     * @param $name
     * @param string $isSelect
     * @param string $css_class
     * @return string
     */
    function createCheckbox($name, $isSelect='',$css_class=''){
        $isSelect =  $isSelect == 1 ? 'checked' : '';
        return '<input type="checkbox" name="'.$name.'" '.$isSelect.' class="r_4c_3" />';
    }
    /******———————————————————————— 以下普通用户操作按钮 ———————————————————————————*********/

    /**
     * 预约设备按钮
     * @return string
     */
    function getDeviceReserveBtn(){
        if($this->check->isReservePower()){
            return $this->createActionBtn("reserveDevice/{$this->tds}",'预 约');
        }else{
            return '';
        }
    }

    /**
     * 预订耗材按钮
     * @return string
     */
    function getConsumeReserveBtn(){
        if($this->check->isReservePower()){
            return $this->createEditBtn("reserveConsume/{$this->tds}",'预 订');
        }else{
            return '';
        }
    }

    /**
     * 取消按钮
     * @return string
     */
    function getCancelBtn(){
        if($this->check->isCancelPower()){
            return $this->createActionBtn("cancel/{$this->tds}",'取 消');
        }else{
            return '';
        }
    }

    /**
     * 续借按钮
     * @return string
     */
    function getRenewBtn(){
        if($this->check->isRenewPower()){
            return $this->createActionBtn("renew/{$this->tds}",'续 借');
        }else{
            return '';
        }
    }

    /******———————————————————————— 以下管理员操作按钮 ———————————————————————————*********/

    /**
     * 撤消按钮
     * @return string
     */
    function getRepealBtn(){
        if($this->check->isRepealPower()){
            return $this->createActionBtn("repeal/{$this->tds}",'撤 消');
        }else{
            return '';
        }
    }

    /**
     * 借出按钮
     * @return string
     */
    function getLendBtn(){
        if($this->check->isLendPower()){
            return $this->createEditBtn("lend/{$this->tds}",'借 出');
        }else{
            return '';
        }
    }

    /**
     * 归还按钮
     * @return string
     */
    function getRevertBtn(){
        if($this->check->isRevertPower()){
            return $this->createEditBtn("revert/{$this->tds}",'归 还');
        }else
            return '';
    }

    /**
     * 出售按钮(扣费)
     * @return string
     */
    function getSaleBtn(){
        if($this->check->isSalePower()){
            return $this->createEditBtn("sale/{$this->tds}",'出 售');
        }else{
            return '';
        }
    }

    /**
     * 回收耗材按钮
     * @return string
     */
    function getRecycleBtn(){
        if($this->check->isRecyclePower()){
            return $this->createEditBtn("recycle/{$this->tds}",'回 收');
        }else{
            return '';
        }
    }

    /**
     * 配置实验卓的按钮
     * @return string
     */
    function getDeskBindDeviceBtn(){
        if($this->check->isManager()){
            return $this->createEditBtn("deskBindDevice/{$this->tds}",'配置设备');
        }else{
            return '';
        }
    }

    /**
     * 重置密码按钮
     * @return string
     */
    function getResetPasswordBtn(){
        if($this->check->isManager()){
            return $this->createEditBtn("resetPassword/{$this->tds}",'重置密码');
        }else{
            return '';
        }
    }

    /**
     * 管理员权限配置按钮
     * @return string
     */
    function getManagerPowerConfigBtn(){
        if($this->check->isManager()){
            return $this->createEditBtn("managerPowerConfig/{$this->tds}",'配置权限');
        }else{
            return '';
        }
    }

    /*****   编辑按钮   *****/

    /**
     * 添加按钮
     * @return string
     */
    function getAddBtn(){
        if($this->check->isAddPower()){
            return $this->createEditBtn("add/{$this->tds}",'添 加','');
        }else{
            return '';
        }
    }

    /**
     * 修改按钮
     * @return string
     */
    function getModifyBtn(){
        if($this->check->isModifyPower()){
            return $this->createEditBtn("modify/{$this->tds}",'修 改');
        }else{
            return '';
        }
    }

    /**
     * 删除按钮
     * @return string
     */
    function getDeleteBtn(){
        if($this->check->isDeletePower()){
            return $this->createActionBtn("delete/{$this->tds}",'删 除');
        }else{
            return '';
        }
    }

    /*****         ******/

    /**
     * 查看设备链接
     * @return string
     */
    function getDeviceRepertoryViewLink(){
        if($this->check->isReadPower()){
            return '<a href="'.$this->createUri("table/d_repertory_1/{$this->page}/{$this->pageSize}").'" title="查看列表" >查看列表</a>';
        }else{
            return '';
        }
    }

    /**
     * 查看耗材链接
     * @return string
     */
    function getConsumeRepertoryViewLink(){
        if($this->check->isReadPower()){
            return '<a href="'.$this->createUri("table/c_repertory_1/{$this->page}/{$this->pageSize}").'" title="查看列表" >查看列表</a>';
        }else{
            return '';
        }
    }

    /**
     * 查看实验桌链接
     * @return string
     */
    function getDeskViewLink(){
        if($this->check->isReadPower()){
            return '<a href="'.$this->createUri("table/l_desk_1/{$this->page}/{$this->pageSize}").'" title="查看列表" >查看列表</a>';
        }else{
            return '';
        }
    }

    /***     后面是私有方法     ***/

    /**
     * 操作按钮
     * @param $path
     * @param $value
     * @param bool $replace
     * @return string
     */
    private function createActionBtn($path,$value,$replace=true){
        return '<a href="'.$this->createUri($path,$replace).'" title="'.$value.' 操作" class="operate">'.$value.'</a>';
    }

    /**
     * 对话框按钮
     * @param $path
     * @param $value
     * @param bool $replace
     * @return string
     */
    private  function createEditBtn($path,$value,$replace=true){
        return '<a href="'.$this->createUri($path,$replace).'" title="'.$value.'表单" class="gdlg">'.$value.'</a>';
    }

    /**
     *  创建 a 锚点 链接
     * @param $path
     * @param $value
     * @param bool $replace
     * @return string
     */
    function createLink($path, $value, $replace=true){
        return '<a href="'.$this->createUri($path,$replace).'" title="'.$value.'表单" class="link">'.$value.'</a>';
    }

    /**
     * 创建 uri 链接
     * @param $path
     * @param bool $replace
     * @return string
     */
    function createUri($path, $replace=true){
        $curUri=$_SERVER['REQUEST_URI'];
        $identity=$this->check->getUserIdentity();
        $arr=explode(trim($identity),$curUri);
        $uri= $arr[0]."{$identity}/".trim($path);
        if(is_bool($replace) ) $uri.=  $replace==true ? "/row_id":'';
        else{
            $uri.="/{$replace}";
        };
        return $uri;
    }
}
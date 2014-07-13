<?php
/**
 * Created by PhpStorm.
 * User: huangnie
 * Date: 14-5-26
 * Time: 上午10:08
 */

/**
 * 业务逻辑基类
 * Class XtModel
 */
abstract class XtModel extends Ni_Model{

    /**
     * 一天的秒数
     * @var int
     */
    protected $secondsOfOneDay=86400;

    /**
     * 最多可续借天数
     * @var int
     */
    protected  $maxRenewDays=15;

    /**
     * 最多可借天数
     * @var int
     */
    protected  $maxLendDays=30;  

    /**
     * 权限和状态管理类库
     * @var null
     */
    protected $check=null;

    /**
     * UI 组件类库
     * @var null
     */
    protected $ui=null;

    function __construct(){

    }

    /**
     * 初始化
     * @param $ui
     * @param array $data
     * @return $this
     */
    function init($ui, array $data=null){    
        $this->ui = $ui;
		$this->check = $this->ui->getCheck();
        return $this;
    }

    /**
     * 获取记录（数组） by id
     * @param $daoName
     * @param int $id
     * @return null
     */
    protected function getResultById($daoName,$id=0){
        $dao=$this->dao($daoName);
        $reserve=$dao->where_eq('id',$id)->getResult(2);
        return isset( $reserve['result'])? $reserve['result'] : null;
    }

    /**
     * 获取记录（数组） by reserveId
     * @param $daoName
     * @param int $reserveId
     * @return null
     */
    protected function getResultByReserveId($daoName,$reserveId=0){
        $dao=$this->dao($daoName);
        $reserve=$dao->where_eq('reserveId',$reserveId)->getResult(2);
        return isset( $reserve['result'])? $reserve['result'] : null;
    }

    /**
     * 从当前菜单中的解析 daoName
     * @return string
     */
    protected function getCurDaoName(){
        $arr=explode('_',$this->ui->getTds());
        $daoName =  $arr[0].ucfirst($arr[1]);
        return $daoName;
    }

    /**
     * 从当前菜单中的解析 记录类型
     * @return mixed
     */
    protected function getCurRecordType(){
        $arr=explode('_', $this->ui->getTds());
        return $arr[2];
    }

    /**
     * 补全字段, 如 reserveTime --> reserve.`reserveTime`
     * @param string $table
     * @param array $fieldsExplain
     * @param array $groupByArr
     * @return array
     */
    protected function getFullFieldsExplain($table='',array $fieldsExplain,array $groupByArr=null){
        if(is_array($fieldsExplain)){
            $fullFieldExplainArr=array();
            if($table!="")$table.='.';
            foreach($fieldsExplain as $key=>$explain){
                $wholeField="$table`{$key}`";
                $fullFieldExplainArr[$wholeField]=$explain;
            }
            //处理统计字段
            if(is_array($groupByArr) && count($groupByArr)>0){
                foreach($groupByArr as $key){
                    if(in_array($key,array_keys($fullFieldExplainArr))){
                        $wholeField="count({$table}`{$key}`) AS count{$key}";
                        $fullFieldExplainArr[$wholeField]=array();
                        $fullFieldExplainArr[$wholeField]['value']="{$fullFieldExplainArr["{$table}`{$key}`"]['value']}的频数";
                    }
                }
            }
            return $fullFieldExplainArr;
        }
        else return array();
    }

    /**
     * 执行 dao 的回掉函数
     * @param array $fullFieldsExplain
     * @return array
     */
    protected function executeCallback(array $fullFieldsExplain){
        foreach($fullFieldsExplain as &$explain){
            if(isset($explain['selectArr']) && is_string($explain['selectArr'])){
                $callback= $explain['selectArr'];
                preg_match_all('/([a-zA-Z][a-zA-Z0-9]*)\s*\(\s*(.*)\s*\)/',$callback,$matches);
                $callback=$matches[1][0];
                $param=isset( $matches[2][0]) ? $matches[2][0] : '';
                $explain['selectArr']=$this->$callback($param);
            }
        }
        return $fullFieldsExplain;
    }

    /**
     * 回调函数（设备分类）
     * @return array
     */
    protected function getDeviceCategory(){
        return $this->getCategory($this->dao('dCategory'));
    }

    /**
     * 回调函数（耗材分类）
     * @return array
     */
    protected function getConsumeCategory(){
        return $this->getCategory($this->dao('dCategory'));
    }

    /**
     * 获取器材分类
     * @param $dao
     * @return array
     */
    private function getCategory($dao){
        $category=$dao->getResult();
        $selectArr=array('0'=>'选择');
        foreach($category['result'] as $row){
            if($row['chName']!='')$selectArr[$row['id']]=$row['chName'];
        }
        return $selectArr;
    }

    /**
     * 回调函数（教室）
     * @return array
     */
    protected function getRoom(){
        $dao=$this->dao('lRoom');
        $category=$dao->getResult();
        $selectArr=array('0'=>'选择');
        foreach($category['result'] as $row){
            if($row['num']!='')$selectArr[$row['id']]=$row['num'];
        }
        return $selectArr;
    }

    /**
     * 回调函数（分组）
     * @return array
     */
    protected function getGroup(){
        $dao=$this->dao('uGroup');
        $category=$dao->getResult();
        $selectArr=array('0'=>'选择');
        foreach($category['result'] as $row){
            if($row['num']!='')$selectArr[$row['id']]=$row['num'];
        }
        return $selectArr;
    }

    /**
     * 添加日志
     * @param string $userNum
     * @param string $userName
     * @param string $event
     * @param string $remark
     */
    protected function addLog($userNum='',$userName='',$event='',$remark='无'){
        if($userNum || $userName){
            if($event){
                $this->dao('uLog')->setUserNum($userNum)
                    ->setUserName($userName)
                    ->setEvent($event)
                    ->setTheTime(time())
                    ->setRemark($remark)
                    ->add();
            }
        }
    }

} 
<?php
/**
 *  User: huangnie
 */
require 'xt.model.php';
/**
 * 数据视图基类
 */
abstract class InfoModel extends XtModel {

    /**
     * 搜索字段的索引
     * @var null
     */
    protected $fieldIndex=null;

    /**
     * 搜索内容
     * @var null
     */
    protected $fieldValue=null;

    /**
     * 开始时间
     * @var null
     */
    protected $timeStart=null;

    /**
     * 结束时间
     * @var null
     */
    protected $timeEnd=null;

    /**
     * 当前页
     * @var int
     */
    protected $page=1;

    /**
     * 每页条数
     * @var int
     */
    protected $pageSize=15;

    function __construct(){
        parent::__construct();
    }

    /**
     * 初始化
     * @param $ui
     * @param array $data
     * @return $this
     */
    function  init($ui, array $data=null){
        parent::init($ui);
        $this->fieldIndex=isset($data['fieldIndex']) && $data['fieldIndex'] !='' ? $data['fieldIndex'] : $this->fieldIndex;
        $this->fieldValue=isset($data['fieldValue']) && $data['fieldValue'] !='' ? $data['fieldValue'] : $this->fieldValue;
        $this->timeStart=isset($data['timeStart']) && $data['timeStart'] !='' ? $data['timeStart'] : $this->timeStart;
        $this->timeEnd=isset($data['timeEnd']) && $data['timeEnd'] !='' ? $data['timeEnd'] : $this->timeEnd;
        $this->page=isset($data['page']) && is_numeric($data['page']) && $data['page'] > 0 ? $data['page'] : 1;
        $this->pageSize=isset($data['pageSize']) && is_numeric($data['pageSize']) && $data['pageSize'] > 0 ? $data['pageSize'] : 15;	
        return $this;
    }

    /**
     * 普通用户只能查看自己的数据
     * @param $dao
     * @return mixed
     */
    protected function identityDistinguish($dao){
        if($this->check->isCustomer()){
            $dao->left_join('user_customer')->on_eq('user_customer.id','device_reserve.customerId');
            $dao->where_eq('user_customer.id',$this->check->getUserId());
        }
        return $dao;
    }

    /**
     * 数据列表
     * @param $dao
     * @param $readFieldsExplain
     * @param bool $isViewSql
     * @return mixed
     */
    protected function table($dao,$isViewSql=false,$readFieldsExplain=null){
        $readFieldsExplain2=$this->getFullFieldsExplain($dao->getTable(),$dao->getFieldsExplain());
        $readFieldsExplain2=$this->executeCallback($readFieldsExplain2);
        $dao->select(array_keys($readFieldsExplain2));

        if(is_array($readFieldsExplain) && count($readFieldsExplain) > 0){
            $data['readFieldsExplain']=  array_merge($readFieldsExplain, $readFieldsExplain2);
        }else{
            $data['readFieldsExplain']=$readFieldsExplain2;
        }

        $searchFieldArr=array();
        $index=1;
        foreach($data['readFieldsExplain'] as $key=>$row){
            $searchFieldArr["{$index}"]=array('field'=>$key, 'value'=>$row['value']);
            $index++;
        }

        if($this->fieldIndex != null && isset($searchFieldArr[$this->fieldIndex])) {
            $field=$searchFieldArr[$this->fieldIndex]['field'];
            if( $this->fieldValue!=null )$dao->where_like($field, $this->fieldValue);     //模糊搜索
			if($this->timeStart != null && $this->timeEnd !=null){
				$startTime=explode('/',$this->timeStart);
				$endTime= explode('/',$this->timeEnd);
				if(preg_match('/.*Time.*/i',$field)) $dao->where_between("FROM_UNIXTIME({$field})","{$startTime[2]}-{$startTime[0]}-{$startTime[1]}","{$endTime[2]}-{$endTime[0]}-{$endTime[1]}");
				if(preg_match('/.*Date.*/i',$field)) $dao->where_between($field, $this->timeStart,$this->timeEnd );
			}
        }
        $dao->order_desc('id');
        $data['searchFieldArr']=$searchFieldArr;
        $dao->limit(($this->page - 1)*$this->pageSize, $this->pageSize);
		$data['totalCount'] = $dao->viewCurSql($isViewSql)->getTotalCount();
        $rs=$dao->getResult();
        $data=$data+$rs;
        unset($dao);
        return $data;
    }

    /**
     * 生成各个 html 模块
     * @param $data
     * @param array $operate
     * @param string $link
     * @return mixed
     */
    protected function toHtmlArr($data,array $operate=null,$link=''){
        $rs['addition']=$this->ui->getAddition($link, $_SERVER['REQUEST_URI'],$data['searchFieldArr'], $this->fieldIndex, $this->timeStart, $this->timeEnd, $this->fieldValue);
        $rs['theader']=$this->ui->getTheader($data['readFieldsExplain'],$operate);
        $rs['tbody']=$this->ui->getTBody($data['result'],$operate);
        $rs['pageBar']=$this->ui->getPageBar($data['totalCount'],$this->page,$this->pageSize);
        $rs['visitTime']=$data['visitTime']["{$data['visitCount']}"];
        return $rs;
    }

}
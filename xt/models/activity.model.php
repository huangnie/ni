<?php
/**
 *  User: huangnie
 */
require 'xt.model.php';
/**
 * 工作流基类
 */
abstract class ActivityModel extends XtModel {

    function __construct(){
        parent::__construct();
    }

    /**
     * 添加操作
     * @param null $detail
     * @param string $addType
     * @param bool $isViewCurSql
     * @return bool|int|string
     */
    function add($detail=null,$addType='',$isViewCurSql=false){
        $dao=$this->dao($this->getCurDaoName());
        $addType= (is_numeric($addType) && $addType ==2) ? 5 : 2; //5 表示注册
        $addFieldsExplain=$dao->getFieldsExplain($addType);

        if(!is_array($detail) || count($detail)==0) {
            $addFieldsExplain=$this->executeCallback($addFieldsExplain);
            foreach($addFieldsExplain as $field=>$explain) $result[$field]='';
            $rs['detail']['result']=$result; //虚构 detail
            $rs['fieldsExplain']= $addFieldsExplain;
            if(is_bool($addType) && $addType ==true)$rs['uri']=$_SERVER['REQUEST_URI'];  // 注册是个例外
            else $rs['uri']=$this->ui->createUri("add/{$this->ui->getTds()}",'');
            return $rs;
        }else{
            $addFieldValue=$this->getEditFieldValue($detail,$addFieldsExplain);
            if(!is_array($addFieldValue) || count($addFieldValue) == 0 ) return $addFieldValue;
            $rs= $dao->viewCurSql($isViewCurSql)->add($addFieldValue);
            if(is_numeric($rs)) return  $rs > 0;
            else return $rs;
        }
    }

    /**
     * 修改操作
     * @param null $detail
     * @param int $value
     * @param $modifyType  2 为登录用户更改个人基本信息
     * @param string $field
     * @param bool $isViewCurSql
     * @return array|string
     */
    function modify($detail=null,$value=0,$modifyType=1,$field="id",$isViewCurSql=false){
        $dao=$this->dao($this->getCurDaoName());
        $modifyType= (is_numeric($modifyType) && $modifyType == 2) ? 5 : 3; //5 表示注册
        $modifyFieldsExplain=$dao->getFieldsExplain($modifyType);

        if(!is_array($detail) || count($detail)==0) {
            $modifyFieldsExplain=$this->executeCallback($modifyFieldsExplain);
            $dao->select(array_keys($this->getFullFieldsExplain($dao->getTable(),$modifyFieldsExplain)));
            $dao->where_eq($field,$value)->viewCurSql($isViewCurSql);
            $rs['detail']=$dao->getResult(2);
            $rs['fieldsExplain']= $modifyFieldsExplain;
            $rs['uri']=(is_numeric($modifyType) && $modifyType == 5 ) ? $_SERVER['REQUEST_URI'] : $this->ui->createUri("modify/{$this->ui->getTds()}/{$value}",'');
            return $rs;
        }
        else{
            $modifyFieldValue=$this->getEditFieldValue($detail,$modifyFieldsExplain);
            if(!is_array($modifyFieldValue) || count($modifyFieldValue) == 0 ) return $modifyFieldValue;
            return $dao->where_eq($field,$value)->modify($modifyFieldValue);
        }
    }

    /**
     * 删除操作
     * @param int $value
     * @param string $field
     * @param bool $isViewCurSql
     * @return int
     */
    function delete($value=0,$field="id",$isViewCurSql=false){
        $dao=$this->dao($this->getCurDaoName());
        $detail=$dao->where_eq($field,$value)->getResult(2);
        if(!is_array($detail['result']) || count($detail['result']) == 0) return -1;
        return $dao->where_eq($field,$value)->delete();
    }

    /**
     * 格式化编辑数据
     * @param array $value
     * @param $fieldsExplain
     * @return array|string
     */
    private function getEditFieldValue( array $value, array $fieldsExplain){
        foreach($fieldsExplain as $field=>$explain) $editField[]=$field;
        $editField=array_combine(array_values($editField),array_values($value));
        foreach($fieldsExplain as $field => $explain){
            if(isset($explain['inputCheck']) && $explain['inputCheck']!='' && isset( $editField["{$field}"]) && !preg_match($explain['inputCheck'], $editField["{$field}"])){
                return "输入的 {$explain['value']}: {$editField[$field]} 格式有误！{$explain['tip']}";
            }
        }
        if(isset($editField['password'])){
            $editField['password'] = md5($editField['chName'].$editField['password']);
        }
        return $editField;
    }

}
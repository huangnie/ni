<?php
/**
 * User: huangnie
 */

/**
 * dao 基类
 * Class BaseDao
 */
abstract class BaseDao extends Ni_Dao{

    function __construct($index=0){
       parent::__construct($index);
    }

    /**
     * 获取所需的解释的字段
     * @return mixed
     */
    abstract function getFieldsExplain();

    /**
     * 字段许可验证
     * @param string $findField
     * @param int    $type
     * @param array  $fieldsExplainArr
     * @return array
     */
    function getArrElement($findField="",$type=1,array $fieldsExplainArr=null){
        $findField=trim($findField);
        if(!in_array($type,array(1,2,3,4,5))) $type=2;
        $typeArr=array(1=>'read', 2=>'add', 3=>'modify', 4=>'search', 5=>'reg',);
        $type=$typeArr[$type];

        if(in_array($findField,array_keys($fieldsExplainArr))){
            $explain= $fieldsExplainArr["{$findField}"];
            if(isset( $explain["{$type}"]) &&  $explain["{$type}"] >0 ) return array($findField=>$explain);
            else return null;
        }

        $newFieldsExplainArr=array();
        foreach($fieldsExplainArr as $field=>$explain){
            if(isset($explain["{$type}"]) &&  $explain["{$type}"] > 0 ) $newFieldsExplainArr[trim($field)]=$explain;
        }
        return $newFieldsExplainArr;
    }

} 
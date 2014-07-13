<?php
/**
 * Class Ni_Entity
 */
require_once 'Ni_Create.class.php';
abstract class Ni_Dao extends Ni_Create {
    /**
     * 默认配置
     * @var array
     */
    private $config=array(
        'HOST'=>'localhost',
        'USER'=>'root',
        'PASSWORD'=>'',
        'DBNAME'=>'test',
        'DBDRIVER'=>'mysql',
        'DBPREFIX'=>false,
        'CHARSET'=>'utf8',
    );

    /**
     * 数据库 连接池
     * @var array
     */
    private static $dbArr=array();

    /**
     * 当前使用的数据库连接
     * @var null
     */
    private $db=null;

	// sql
    /******  Sql 各部分 *****/
    private $table='';
	private $fields='';
	private $where='';
	private $set='';
	private $joinOn='';	
	private $groupBy='';
	private $orderBy='';
	private $limit='';
	private $having='';

    private static $inTrans=false;

	// data
	private $sql='';                // 当前sql 语句
	private $values=array();        // 当前sql 参数

	function __construct($index=0){
        if(!isset($_ENV['DB_CONFIG'][$index])) throw new Exception('数据库配置索引错误');
        $this->setConfig($_ENV['DB_CONFIG'][$index]);
        $this->connect();
	}

    /**
     * 初始化
     * @return $this
     */
    function init(){
		$this->fields='';
		$this->where='';
		$this->set='';
		$this->joinOn='';	
		$this->groupBy='';
		$this->orderBy='';
		$this->limit='';
		$this->having='';
        return $this;
	}

    function setConfig(array $config=null){
        if($config!=null){
            foreach($config as $key=>$value) $check[strtoupper($key)]=$value;
            $check=array_diff(array_keys($this->config),array_keys($config));
            if(count($check) >0 ) throw new Exception("init fail: 数据库 未配置".implode(',',$check));
            $this->config=$config;
        }
    }

    /**
     * @param string $table
     * @return mix
     */
    function setTable($table=""){
        if($table=="")return false;
        $this->table=$table;
        return $this;
    }

    /**
     * @return bool
     */
    function isDBprefix(){
        if(isset($this->config['DBPREFIX']) && is_bool($this->config['DBPREFIX'])){
            return $this->config['DBPREFIX'];
        }else return false;
    }

	/**
	 * desc 数据库连接（已资源优化）
	 *
	 */
	function connect(){
		try{
            foreach(self::$dbArr as &$tmpDb){
                if($tmpDb==null){
                    unset($tmpDb);
                }
                else if($tmpDb instanceof Ni_Pdo) {
                    if($tmpDb->getDbName()==$this->config['DBNAME'] && $tmpDb->getHost()==$this->config['HOST']) $this->db = clone $tmpDb;
                }
            }
            //
            if($this->db==null || !($this->db instanceof Ni_Pdo)){
                $this->db=$this->load('Ni_Pdo',dirname(dirname(__FILE__)).'/database/Ni_Pdo.class.php',self::$dbArr);
                $this->db->setHost($this->config['HOST']);
                $this->db->setUser($this->config['USER']);
                $this->db->setPassword($this->config['PASSWORD']);
                $this->db->setDbName($this->config['DBNAME']);
                $this->db->setDbDriver($this->config['DBDRIVER']);
                $this->db->setLongConnect(true);
                $this->db->setCharset($this->config['CHARSET']);
                $this->db->connect();
                self::$dbArr['Ni_Pdo']=&$this->db;
            }
			return true;
		}
		catch(Exception $e){
			throw new Exception('database connect fail, because '.$e->getMessage());
		}
        catch (LogicException $Exception) {
            throw new ErrorException("LogicException");
        } catch (ReflectionException $Exception) {
            $tip="the class requested in controller does not exist! it may be the wrong of name of class, such as the not upper of first word of class.";
            throw new ErrorException($tip);
        }
	}

    /**
     * 关闭数据库链接（释放资源）
     * desc
     */
    function close(){
		$this->db->close();
		$this->db=null;
        return $this;
	}

    /**
     * 显示本次的 sql 语句
     * @param bool $flag
     * @return $this
     */
    function viewCurSql($flag=false){
        $this->db->viewCurSql($flag);
        return $this;
    }

    /**
     * 事物开始（安全的启动）
     */
    function trans(){
        try{
//            if(! $this->db->inTrans()) $this->db->trans();
            if(!self::$inTrans) {
                $this->db->trans();
                self::$inTrans=true;
            }
        }catch(Exception $e){
            throw new Exception('trans fail, because of '.$e->getMessage());
        }
        return $this;
    }

    /**
     * 回滚（安全的操作）
     * @return $this
     * @throws Exception
     */
    function rollback(){
        try{
//            if( $this->db->inTrans()) $this->db->rollback();
            if( self::$inTrans){
                $this->db->rollback();
                self::$inTrans=false;
            }
        }catch(Exception $e){
            throw new Exception('rollback fail, because of '.$e->getMessage());
        }
        return $this;
    }

    /**
     * 提交（安全的操作）
     * @return $this
     * @throws Exception
     */
    function commit(){
        try{
//            if( $this->db->inTrans())  $this->db->commit();
            if(self::$inTrans){
                $this->db->commit();
                self::$inTrans=false;
            }
        }catch(Exception $e){
            throw new Exception('commit fail, because of '.$e->getMessage());
        }
        return $this;
    }

    /**
     * @param array $arr
     * @param string $table
     * @return mixed
     * @throws Exception
     */
    function add(array $arr=null,$table=""){
		try{
            if($table=="") $table=trim($this->getTable());
            if($table=="")throw new Exception('worn: 勿忘数据表名');
            if($arr==null || count($arr)==0) $arr=$this->objToArr();

            //去掉空值（ NULL 与 '')
            $arr=array_filter($arr);
            foreach($arr as $field=>$value){
                $this->addField(trim($field)); //这里特别注意，$field 会自带一个 空格，须处理
            }
            $this->fields=$this->createFieldsStr();
            $wenHao=$this->createWenHaoStr(count($arr));
			$this->sql="INSERT INTO {$table}({$this->fields}) VALUES({$wenHao})";
            $this->values=array_values($arr);
            $this->init();
            return $this->db->add($this->sql,$this->values);
		}
        catch(PDOException $e){
            throw new Exception('insert fail:'.$e->getMessage());
        }
		catch(Exception $e2){
			throw new Exception('insert fail:'.$e2->getMessage());
		}
	}

    /**
     * @param int $num
     * @return string
     */
    function createWenHaoStr($num=0){
        $wenHao="";
        for($i=0;$i<$num;$i++)$wenHao.=',?';
        $wenHao=substr($wenHao,1);
        return $wenHao;
    }

    /**
     * @throws Exception
     */
    function modify($arr=array(),$table=""){
		try{
            if(count($arr)==0) $arr=$this->objToArr();
            if($table=="") $table=trim($this->getTable());
            if($table=="")throw new Exception('delete fail: 勿忘数据表名');
            $where=trim($this->createWhereStr());
            if($where=="")throw new Exception('delete fail: 勿忘使用 WHERE 定位');
            if(count($arr)>0){
                foreach($arr as $field=>$value) $this->set(trim($field),"?");
            }
            else throw new Exception('delete fail: 勿忘更改内容');
            $this->values=array_values($arr);
            $set=$this->createSetStr();
            $this->sql="UPDATE {$table} {$set} {$where}";
            $this->init();
            return $this->db->modify($this->sql,$this->values);
		}
		catch(Exception $e){
			throw new Exception('update fail: '.$e->getMessage());
		}
	}

    /**
     * @throws Exception
     */
    function delete(){
		try{
            $table=trim($this->getTable());
            if($table=="")throw new Exception('勿忘数据表名');
            $where=trim($this->createWhereStr());
            if($where=="")throw new Exception('勿忘使用 WHERE 定位');
            else{
                $this->sql="DELETE FROM {$table} ".$this->createWhereStr();
                $this->init();
                return $this->db->delete($this->sql); //执行成功 返回 1
            }
		}
		catch(Exception $e){
			 throw new Exception('delete fail: '.$e->getMessage());
		}
	}

    /**
     * @param int $format
     * @return mixed
     * @throws Exception
     */
    function getResult($format=1){
		try{
            $fields=trim($this->createFieldsStr());
            $table=trim($this->getTable());
            if($fields=="") throw new Exception(' no table field ');
            if($table=="") throw new Exception(' no table name ');
            $joinOn=$this->createJoinOnStr();
            $where=$this->createWhereStr();
            $groupBy=$this->createGroupByStr();
            $orderBy=$this->createOrderByStr();
            $limit=$this->createLimit();
            $having=$this->createHavingStr();
            $this->sql="SELECT {$fields} FROM {$table} {$joinOn} {$where} {$groupBy} {$orderBy} {$limit} {$having}";
            $this->init();
            return $this->db->getResult($this->sql,$this->values,$format);
		}
		catch(Exception $e){
			throw new Exception("fetch  fail:".$e->getLine()." ".$e->getMessage());
		}
	}

    /**
     * 返回总行数（no limit 限制)
     * @param string $field
     * @param string $table
     * @return int
     * @throws Exception
     */
    function getTotalCount($field="",$table=""){
        if($table=="")$table=$table=trim($this->getTable());
        if($table=="") throw new Exception(' no table name ');
        $joinOn=$this->createJoinOnStr();
        $where=$this->createWhereStr();
        $groupBy=$this->createGroupByStr();
        if($field!="")$sql="SELECT COUNT(distinct {$table}.{$field}) as totalCount FROM {$table} {$joinOn} {$where} {$groupBy}";
        else $sql="SELECT COUNT(*) as totalCount FROM {$table} {$joinOn} {$where} {$groupBy}";
        return $this->db->getFirst($sql,$this->values);
    }

    /**
     * @return mixed|string
     */
    function createFieldsStr(){
        $this->fields=trim($this->fields);
        if($this->fields=="")return "*";
        $this->fields=trim($this->fields);
        $this->fields=preg_replace('/^,(.*)$/i', '\1', $this->fields);
        return $this->fields;
    }

    /**
     * @return string
     */
    function createJoinOnStr(){
        return trim($this->joinOn);
    }

    /**
     * @return string
     */
    function createSetStr(){
        $this->set=trim($this->set);
        $this->set=preg_replace('/^,(.*)/i', '\1', $this->set);
        return " SET ".$this->set;
    }

    /**
     * @return string
     */
    function createWhereStr(){
        $this->where=trim($this->where);
        if($this->where=="") return "";
        $this->where=preg_replace('/^AND(.*)$/i', '\1', $this->where);
        $this->where=preg_replace('/^OR(.*)$/i', '\1', $this->where);
        return " WHERE ".$this->where;
    }

    /**
     * @return string
     */
    function createGroupByStr(){
        $this->groupBy=trim($this->groupBy);
        if($this->groupBy=="")return "";
        $this->groupBy=preg_replace('/^,(.*)$/i', '\1', $this->groupBy);
        return " GROUP BY ".$this->groupBy;
    }

    /**
     * @return string
     */
    function createOrderByStr(){
        $this->orderBy=trim($this->orderBy);
        if($this->orderBy=="")return "";
        $this->orderBy=preg_replace('/^,(.*)$/i', '\1', $this->orderBy);
        return " ORDER BY ".$this->orderBy;
    }

    /**
     * @return string
     */
    function createLimit(){
        $this->limit=trim($this->limit);
        if($this->limit=="")return "";
        return " LIMIT ".$this->limit;
    }

    function createHavingStr(){
        $this->having=trim($this->having);
        if($this->having=="")return "";
        $this->having=preg_replace('/^AND(.*)$/i', '\1', $this->having);
        $this->having=preg_replace('/^OR(.*)$/i', '\1', $this->having);
        return " HAVING ".$this->having;
    }

    /**
     * 查询字段映射
     * @param string $field
     * @param string $table
     * @param string $asPrefix
     * @return $this
     */
    protected function addField($field='*',$table="",$asPrefix=''){
        $field=$this->formatField($field,$table);
        if($field!=''){
            $attr=strpos($field,'`') >0 ? preg_replace('/.*`(.*)`/','\1',$field) : $field;
            $as= $asPrefix!='' ? "{$asPrefix}_{$attr}" : '';
            $this->fields.=",{$field} $as ";
        }
        return $this;
	}

    /**
     * 添加查询字段
     * @param string $field
     * @param int $type
     * @return $this
     */
    function select($field='*',$type=1){
        $table=$this->getTable();
        $asPrefix = $type == 2 ? "AS {$table}" : '';
        if(!is_array($field) && strpos($field,',')>0) $field=explode(',',$field);
        if(is_array($field)){
            foreach($field as $value)$this->addField(trim($value),$table,$asPrefix);
        }
        else {
            $this->addField(trim($field),$table,$asPrefix);
        }
        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    function from($table=""){
		if($table!="") $this->table=$table;
        return $this;
	}

    /**
     * @param $field
     * @param $value
     * @param string $table
     * @return $this
     */
    function set($field='',$value='',$table=""){
        $field=$this->formatField($field,$table);
        if($field!="" && $value=='?') $this->set.=",{$field}=$value";
        else  if($field!="" && $value!='?')$this->set.=",{$field}='{$value}'";
        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    function values($values=array()){
		$this->values=$values;
        return $this;
	}

    /**
     * @param        $table
     * @return $this
     */
    function left_join($table){
		$this->joinOn.=" LEFT JOIN {$table}";
        return $this;
	}

    /**
     * @param        $table
     * @return $this
     */
    function right_join($table){
        $this->joinOn.="RIGHT JOIN {$table}";
        return $this;
    }

    /**
     * @param        $table
     * @return $this
     */
    function inner_join($table){
        $this->joinOn.="INNER JOIN {$table}";
        return $this;
    }


    /**
     * @param        $table
     * @return $this
     */
    function union_join($table){
        $this->joinOn.="UNION JOIN {$table}";
        return $this;
    }

    /**
     * @param string $left
     * @param string $right
     * @return $this
     */
    function on_eq($left='',$right=''){
        $right=trim($right);
        if($left!='' && $right!=''){
            $this->joinOn.="    ON {$left} = {$right}";
        }
        return $this;
    }

    /**
     * @param string $left
     * @param string $right
     * @return $this
     */
    function on_lt($left='',$right=''){
        $right=trim($right);
        if($left!='' && $right!=''){
            $this->joinOn.=" ON {$left} < {$right}";
        }
        return $this;
    }

    /**
     * @param string $left
     * @param string $right
     * @return $this
     */
    function on_ne($left='',$right=''){
        $right=trim($right);
        if($left!='' && $right!=''){
            $this->joinOn.=" ON {$left} != {$right}";
        }
        return $this;
    }

    /**
     * @param string $left
     * @param string $right
     * @return $this
     */
    function on_gt($left='',$right=''){
        $right=trim($right);
        if($left!='' && $right!=''){
            $this->joinOn.=" ON {$left} > {$right}";
        }
        return $this;
    }

    /**
     * @param $left
     * @param $right
     * @return $this
     */
    function where_like($left,$right){
        $left=trim($left);
        if($left!=""){
            $this->where.=" AND {$left} LIKE  '%{$right}%'";
        }
        return $this;
    }

    /**
     * @param $left
     * @param $right
     * @return $this
     */
    function or_where_like($left,$right){
        $left=trim($left);
        if($left!=""){
            $this->where.=" OR {$left} LIKE '%{$right}%'";
        }
        return $this;
    }

    /**
     * @param $left
     * @param $right
     * @param bool $noQuote
     * @return $this
     */
    function where_eq($left,$right,$noQuote=false){
        $left=trim($left);
        if($left!=""){
            if(!$noQuote && !is_numeric($right))$right="'{$right}'";
            $this->where.=" AND {$left} = $right";
        }
        return $this;
    }

    /**
     * @param $left
     * @param $right
     * @param bool $noQuote
     * @return $this
     */
    function or_where_eq($left,$right,$noQuote=false){
        $left=trim($left);
        if($left!=""){
            if(!$noQuote && !is_numeric($right))$right="'{$right}'";
            $this->where.=" OR {$left} = $right";
        }
        return $this;
    }

    /**
     * @param $left
     * @param $right
     * @param bool $noQuote
     * @return $this
     */
    function where_ne($left,$right,$noQuote=false){
        $left=trim($left);
        if($left!=""){
            if(!$noQuote && !is_numeric($right))$right="'{$right}'";
            $this->where.=" AND {$left} != $right";
        }
        return $this;
    }

    /**
     * @param $left
     * @param $right
     * @param bool $noQuote  true -> 强制不加单引号 ‘
     * @return $this
     */
    function or_where_ne($left,$right,$noQuote=false){
        $left=trim($left);
        if($left!=""){
            if(!$noQuote && !is_numeric($right))$right="'{$right}'";
            $this->where.=" OR {$left} != $right";
        }
        return $this;
    }


    /**
     * @param $left
     * @param $right
     * @param bool $noQuote
     * @return $this
     */
    function where_lt($left,$right,$noQuote=false){
        $left=trim($left);
        if($left!=""){
            if(!$noQuote && !is_numeric($right))$right="'{$right}'";
            $this->where.=" AND {$left} < $right";
        }
        return $this;
    }

    /**
     * @param $left
     * @param $right
     * @param bool $noQuote
     * @return $this
     */
    function or_where_lt($left,$right,$noQuote=false){
        $left=trim($left);
        if($left!=""){
            if(!$noQuote && !is_numeric($right))$right="'{$right}'";
            $this->where.=" OR {$left} < $right";
        }
        return $this;
    }

    /**
     * @param $left
     * @param $right
     * @param bool $noQuote
     * @return $this
     */
    function where_gt($left,$right,$noQuote=false){
        $left=trim($left);
        if($left!=""){
            if(!$noQuote && !is_numeric($right))$right="'{$right}'";
            $this->where.=" AND {$left} > $right ";
        }
        return $this;
    }

    /**
     * @param $left
     * @param $right
     * @param bool $noQuote
     * @return $this
     */
    function or_where_gt($left,$right,$noQuote=false){
        $left=trim($left);
        if($left!=""){
            if(!$noQuote && !is_numeric($right))$right="'{$right}'";
            $this->where.=" OR {$left} > $right";
        }
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    function where_in($field,$value){
        $field=$this->formatField($field);
        $value=$this-> formatValue($value);
        $this->where.=" OR {$field} IN ({$value})";
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    function or_where_in($field,$value){
        $field=$this->formatField($field);
        $value=$this-> formatValue($value);
        $this->where.=" AND {$field} IN ({$value})";
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    function where_not_in($field,$value){
        $field=$this->formatField($field);
        $value=$this-> formatValue($value);
        $this->where.=" AND {$field} NOT IN ({$value})";
        return $this;
    }

    /**
     * @param $field
     * @param $start
     * @param $end
     * @return $this
     */
    function where_between($field,$start,$end,$table=''){
        $field=trim($field);
        if($field!="" && $start!='' && $end!= ''){
            if(!is_numeric($start)) $start="'{$start}'";
            if(!is_numeric($end)) $end="'{$end}'";
            $field=$this->formatField($field,$table);
            $this->where.=" AND {$field} BETWEEN {$start}  AND  {$end} ";
        }
        return $this;
    }

    /**
     * @param $field
     * @param string $table
     * @return $this
     */
    function groupBy($field='',$table=''){
        $field=trim($field);
        if($field=='') return $this;
        $this->groupBy.=",".$this->formatField($field,$table);
        return $this;
	}

    /**
     * @param $field
     * @param string $sort
     * @param string $table
     * @return $this
     */
    function order_desc($field,$sort="DESC",$table=""){
        $field=trim($field);
        if($field=='') return $this;
        $field=$this->formatField($field,$table);
        $this->orderBy.=",{$field} DESC";
        return $this;
    }

    /**
     * @param $field
     * @param string $table
     * @return $this
     */
    function order_arc($field,$table=""){
        $field=trim($field);
        if($field=='') return $this;
        $field=$this->formatField($field,$table);
        $this->orderBy.=",{$field} ASC";
        return $this;
    }

    /**
     * @param int $start
     * @param int $offset
     * @return $this
     */
    function limit($start=0,$offset=1){
		$this->limit=$start.",".$offset;
        return $this;
	}

    /**
     * @param $onArr
     * @param string $type
     * @return $this
     */
    function having($onArr,$type="AND"){
        return $this;
	}

    /**
     * @return string
     */
    function getTable(){
        return $this->table;
	}

    /**
     * > 格式化字段，若无table 或 ``，则加上
     * @param string $field
     * @param string $table
     * @return mixed|string
     */
    protected function formatField($field='',$table=''){
        $field=trim($field);
        if($field=='') return '';
        if($table=='') $table=$this->getTable();
        if(strpos($field,".")<=0 && $table!="")$table.='.';
        else $table='';
        if(strpos($field,"(")>0) {
            $field=preg_replace('/(.*\()`?([^`]*)`?(\))/', "$1{$table}`$2`$3",$field);
        }
        else if($field=='*')$field="{$table}.*";
        elseif(strpos($field,".")>0 && strpos($field,"`")===false)$field=preg_replace('/(.*\.)([^\.]*)/',"$1`$2`",$field);
        elseif(strpos($field,".")===false && strpos($field,"`")===false)$field="{$table}`{$field}`";
        elseif(strpos($field,".")===false || strpos($field,"`")>0)$field="{$table}{$field}";
        return $field;
    }

    /**
     * @param $value
     * @return array|string
     */
    protected function formatValue($value){
        if(strpos($value,',')<=0) return $value;
        if(is_string($value)) $value=explode(',',$value);
        foreach($value as $key=>$tmpValue){
            if(!is_numeric($tmpValue)) $value[$key]="'{$tmpValue}'";
        }
        $value=implode(',',$value);
        return $value;
    }

    /**
     * @param null $obj
     * @return array
     * @throws Exception
     */
    function objToArr($obj=null){
        if(is_null($obj)) $obj=$this;
        else if(!is_object($obj)) throw new Exception('你传入的不是对像');
        $arr=(array)$obj;
        $newArr=array();
        $className=get_class($obj);
        foreach($arr as $key=>$attr){
            $index=strpos($key,$className);
            if($index>0){
                $var=substr($key,$index+strlen($className));
                $newArr[$var]=$attr;
            }
        }
        //去掉null值,和表名变量, 那么数据库的设计里 是不推荐空值出现的
        foreach($newArr as $key=>$value){
            if($value==null || $value=="table") unset($newArr[$key]);
        }
        return $newArr;
    }

}
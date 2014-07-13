<?php
/**
 * Class Ni_Pdo
 * desc db conduct base class
 */
class Ni_Pdo {
	// obj
	private $pdo=null;
	private $stmt=null;
	// attr
	private $dbDriver="mysql";
	private $host="localhost";
	private $dbName="test";
	private $user="root";
	private $password="";
	private $charset='utf-8';
	private $longConnect=true;
    // state
    private $viewCurSql=false;
    private $viewPreSql=false;


    // data cache (至少可缓存一次操作的数据，可据需要 定义数组 缓存多个数据)
    /**
     * @var null
     * $cacheArr=array(
     *     'index'=>array(
     *          'result'=>array(),       //查询结果，空值不缓存
     *          'count'=>0 ，            //访问频率（次）
     *          'cacheTime'=>time(),    //缓存时间
     *     ),
     * );

     * index 为缓存索引，查询条件的 MD5 值
     */
    private static $cacheArr=null;
    /**
     * @var int 缓存大小
     */
    private static $cacheMaxNum=10;
    /**
     * @var int  有效期（秒）, 默认值文 5
     */
    private $expire=5;

    /**
     * @param string $driver
     * @param string $host
     * @param string $dbName
     * @param string $user
     * @param string $password
     * @param bool $isLongConnect
     * @param int $expire
     * @param string $charset
     */
    function __construct($driver='mysql',$host='localhost',$dbName='test',$user='root',$password='',$isLongConnect=true,$expire=5,$charset='utf-8'){
        $this->setDbDriver($driver);
        $this->setHost($host);
		$this->setDbName($dbName);
        $this->setUser($user);
        $this->setPassword($password);
        $this->setLongConnect($isLongConnect);
        $this->setExpire($expire);
        $this->setCharset($charset);
	}

    /**
     * //$this->pdo=new PDO("mysql:host=localhost;dbname=sbglxt","root","");
     * @throws Exception
     * desc connect to db
     */
    function connect(){
		try{
            if($this->dbDriver=="")throw new Exception("数据库驱动不能为空");
            if($this->host=="")throw new Exception("主机名不能为空");
            if($this->dbName=="")throw new Exception("数据库名不能为空");
            if($this->user=="")throw new Exception("数据库用户名不能为空");

			$this->pdo=new PDO("{$this->dbDriver}:host={$this->host};dbname={$this->dbName}","{$this->user}","{$this->password}");
			$this->pdo->setAttribute(PDO::ATTR_PERSISTENT, $this->longConnect);
			$this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true); //启用数据库自动缓存，以连续查询的需要，性能优化的一种

            /**
			 * desc 指定获取方式，即默认获取方式为：
			 * 将对应结果集中的每一行作为一个由列名索引的数组返回。
			 * 如果结果集中包含多个名称相同的列，则PDO::FETCH_ASSOC每个列名只返回一个值。
			 */
			$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->exec("SET NAMES '{$this->charset}';");  // 设置字符编码 ， 这里出错的话， 轻则乱码，重则不显示数据
            return $this;
		}
		catch(PDOException $e) {
			throw new Exception('the params config for PDO() is wrong.');
		}	
	}

    /**
     * desc close the db connect
     */
    function close(){
		$this->pdo=null;
		$this->stmt=null;
	}

    /**
     * @param       $sql
     * @param array $values
     * @return mixed
     * @throws Exception
     */
    function add($sql,array $values=null){
        try{
            $rs=$this->execute($sql,$values);
            if(is_bool($rs) && $rs==true) return $this->pdo->lastInsertId();
            else return $rs;
        }catch(PDOException $e){
            throw new Exception('add fail:'.$e->getMessage());
        }
    }

    /**
     * @param $sql
     * @param array $values
     * @return bool|mixed
     * @throws Exception
     */
    function modify($sql,array $values=null){
        try{
            return $this->execute($sql,$values); //执行成功 返回 true
        }catch(PDOException $e){
            throw new Exception('modify fail:'.$e->getMessage());
        }
    }

    /**
     * @param $sql
     * @param array $values
     * @return bool|mixed
     * @throws Exception
     */
    function delete($sql,array $values=null){
        try{
            return $this->execute($sql,$values); //执行成功 返回 true
        }catch(Exception $e){
            throw new Exception('delete fail: '.$e->getMessage());
        }
    }

    /**
     * @param $sql
     * @param array $values
     * @return mixed
     * @throws Exception
     */
    function getFirst($sql,$values=array()){
        try{
            if($this->execute($sql,$values)) return $this->stmt->fetchColumn(0);
            else return -1;
        }catch(PDOException $e){
            $this->viewCurSql(false);
            $this->viewPreSql(false);
            throw new Exception("get count fail: ".$e->getMessage());
        }
    }

    /**
     * @param       $sql
     * @param array $values
     * @param int   $format
     * @return mixed
     * @throws Exception
     */
     function getResult($sql,$values=array(),$format=1){
		if($sql=="")throw new Exception('execute: sql should not be empty!');
        $index=md5($sql.implode(',',$values));

        if(isset(self::$cacheArr[$index])){
            if(self::$cacheArr[$index]['visitTime'][1] < (time()-$this->expire) && count(self::$cacheArr[$index]['result'])>0){
                self::$cacheArr[$index]['visitCount']++;
                self::$cacheArr[$index]['visitTime'][self::$cacheArr[$index]['visitCount']]=time();
                return self::$cacheArr[$index];
            }
            else self::$cacheArr[$index]=null;
        }

		try{
            $this->execute($sql,$values);
            if(!in_array($format,array(1,2,3,4)))$format=1;
            switch($format){
                case 1:
                case 2:
                    $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC); //返回一个索引为结果集列名的数组
                    break;
                case 3:
                case 4:
                    $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_OBJ);   //返回一个属性名对应结果集列名的匿名对象
                    break;
            }

            //栈满后,删除 最近最少访问
            if(count(self::$cacheArr) > self::$cacheMaxNum){
                $tmpIndex=0;
                $tmpMaxCount=99999;
                foreach(self::$cacheArr as $key=>$tmpCacheArr){
                    if($tmpCacheArr['visitCount'] < $tmpMaxCount){
                        $tmpMaxCount=$tmpCacheArr['visitCount'];
                        $tmpIndex=$key;
                    }
                }
                unset(self::$cacheArr[$tmpIndex]);
            }

            switch($format){
                case 1:
                case 3:
                    self::$cacheArr[$index]['result']=$this->stmt->fetchAll();
                    break;
                case 2:
                case 4:
                    self::$cacheArr[$index]['result']=$this->stmt->fetch();
                    break;
            }

            self::$cacheArr[$index]['visitCount']=1;
            self::$cacheArr[$index]['visitTime']=array();
            self::$cacheArr[$index]['visitTime'][1]=time();

            return self::$cacheArr[$index];
		}catch(PDOException $e){
            $this->viewCurSql(false);
            $this->viewPreSql(false);
			throw new Exception("get result fail: ".$e->getMessage());
		}	
	}

    /**
     * @param $sql
     * @param array $values
     * @return bool|mixed
     * @throws Exception
     */
    function execute($sql,$values=array()){
        if($sql=="")throw new Exception('the sql should not be empty!');
        try{
            if($this->pdo==null)$this->connect();
            $sql=preg_replace('/\s{2,}/',' ',$sql); //干掉长空格
            if(!is_array($values))$values=(array)$values;
            writeLog($sql.";\n- array = array(".implode(',',$values).")",APPLICATION_DIR.'/log/sql');
            if($this->viewCurSql==true)$this->viewSql("current SQL: ",$sql,$values);

            $this->stmt=$this->pdo->prepare($sql);
            $this->stmt->execute($values);
            if($this->stmt->errorCode()!='00000') {
                $errorInfo=$this->stmt->errorInfo();
                if($errorInfo[0]==23000) {
                    $tip = preg_replace('/(.*)for\s*key.*/','\1 已存在 ',$errorInfo[2]);
                    $tip = str_replace('Duplicate','不能重复',$tip);
                    return str_replace('entry','输入，',$tip);
                }
                $err="- SQL error: ".$errorInfo[0].",".$errorInfo[1].','.$errorInfo[2];
                writeLog($err,APPLICATION_DIR.'/log/error');
                if($this->viewCurSql==true || $this->viewPreSql==true) throw new Exception($err);
            }
            $affectRows= $this->stmt->rowCount();
            return $affectRows > 0;
        }catch (PDOException $e){
            $this->viewCurSql(false);
            $this->viewPreSql(false);
            throw new Exception('dPDO::execute fail: '.$e->getMessage());
        }
    }

    /**
     * 检测是否在同一个事物活动内
     * @throws Exception
     */
    function inTrans(){
        try{
            return $this->pdo->inTransaction();
        }
        catch(PDOException $e) {
            throw new Exception('Trans 异常:'.$e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    function trans(){
		try{
			$this->pdo->beginTransaction();	
		}
		catch(PDOException $e) {
			throw new Exception('beginTransaction fail:'.$e->getMessage());
		}
	}

    /**
     * @throws Exception
     */
    function rollback(){
		try{
			$this->pdo->rollBack();	
		}
		catch(PDOException $e) {
			throw new Exception('rollBack fail:'.$e->getMessage());
		}	
	}

    /**
     * @throws Exception
     */
    function commit(){
		try{
			$this->pdo->commit();	
		}
		catch(PDOException $e) {
			throw new Exception('commit fail:'.$e->getMessage());
		}	
	}

    /**
     * @param bool $flag
     * @return $this
     */
    function viewCurSql($flag=false){
        $this->viewCurSql=$flag;
        return $this;
    }

    /**
     * @param string $text
     * @param string $sql
     * @param array  $values
     * @return $this
     */
    function viewSql($text="the sql is: ",$sql="",$values=array()){
        $valuesArrToStr=count($values)>0?implode(',',$values):'';
        $text.= '<br>&nbsp;&nbsp;the sql is: '.$sql.'<br>';
        if($valuesArrToStr!="")$text.= '<br>&nbsp;&nbsp;values is: array('.$valuesArrToStr.')<br>';
        echo '<br>',$text;
        return $this;
    }

    /**
     * 设置属性
     * @param $attr
     * @param $value
     * @return $this
     * @throws Exception
     */
    function setAttribute($attr,$value){
        try{
            $this->pdo->setAttribute($attr,$value);
            return $this;
        }
        catch(PDOException $e) {
            throw new Exception('setAttribute fail:'.$e->getMessage());
        }
    }

    /**
     * 设置过期时间
     * @param int $expire
     * @return $this
     */
    function setExpire($expire=5){
        if(is_int($expire) && $expire > 0) $this->expire=$expire;
        return $this;
    }

    /**
     * 设置缓存大小
     * @param int $size
     * @return $this
     */
    function setCacheSize($size=10){
        if(is_int($size) && $size > 0) self::$cacheSize=$size;
        return $this;
    }

    /**
     * @param string $host
     * @return $this
     */
    function setHost($host="localhost"){
		$this->host=$host;
        return $this;
	}

    /**
     * @param string $user
     * @return $this
     */
    function setUser($user="root"){
		$this->user=$user;
        return $this;
	}

    /**
     * @param string $password
     * @return $this
     */
    function setPassword($password=""){
		$this->password=$password;
        return $this;
	}

    /**
     * @param string $dbName
     * @return $this
     */
    function setDbName($dbName=""){
		$this->dbName=$dbName;
        return $this;
	}

    /**
     * @param string $dbDriver
     * @return $this
     */
    function setDbDriver($dbDriver="mysql"){
		$this->dbDriver=$dbDriver;
        return $this;
	}

    /**
     * @param bool $bool
     * @return $this
     */
    function setLongConnect($bool=true){
		$this->longConnect=$bool;
        return $this;
	}

    /**
     * @param string $charset
     * @return $this
     */
    function setCharset($charset="utf-8"){
		$this->charset=$charset;
        return $this;
	}

    /**
     * @return string
     */
    function getHost(){
		return $this->host;	
	}

    /**
     * @return string
     */
    function getUser(){
		return $this->user;	
	}

    /**
     * @return string
     */
    function getPassword(){
		return $this->password;		
	}

    /**
     * @return string
     */
    function getDbName(){
		return $this->dbName;		
	}

    /**
     * @return string
     */
    function getDbDriver(){
		return $this->dbDriver;		
	}

    /**
     * @param bool $bool
     * @return bool
     */
    function isLongConnect($bool=true){
		return $this->longConnect=$bool;		
	}

    /**
     * @return string
     */
    function getCharset(){
		return $this->charset;		
	}

}
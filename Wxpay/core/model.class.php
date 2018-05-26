<?php
/**
 *  model.class.php 数据模型基类
 */
class model {
	//当前数据库操作对象
    protected $db = null;
    //数据库名称
    private $dbName  = '';
    //数据表前缀
    protected $tablePrefix  =   '';
    //数据表名（带前缀）
    protected $ftable = '';
    //数据表名（不带前缀）
    protected $table = '';	
	//最近错误信息
    protected $error = '';
    //字段信息
    protected $fields = array();
	//主键名称
    protected $pk  = array();
	//查询的条件(数组)
	private $_query;
	
	/**
     * 架构函数
     * @access public
     */
    public function __construct($config=array(),$table='') {
		if(empty($config)){
			$config=getDbConfig();
		}
		
		if(!empty($config)){
			$this->dbName=$config['database'];
			$this->tablePrefix = $config['table_pre'];
			$this->db=db::getInstance($config);
		}
		if(!empty($table)){
		    $this->ftable = $config['table_pre'].$table;
			$this->table = $table;
			$this->_tableCheck();
		}
   }
   
	/**
	 * 魔术方法：变量赋值
	 * @access public 
	 */
 	public function __set($property,$value){
		if($property=='tablePrefix'){
     		$this->tablePrefix = $value;
		}
    } 

	/**
	 * 魔术方法：变量赋值
	 * @access public 
	 */
 	public function __get($property){
		if($property=='ftable'){
     		return $this->$property;
		}
    } 

    /**
     * 利用__call方法实现一些特殊的Model方法
     * @access public
     * @param string $method 方法名称
     * @param array $args 调用参数
     */
    public function __call($method,$args) {
		if(in_array(strtolower($method),array('select','from','where','order','limit','having','group','union'),true)) {
            //连贯操作
            $this->_query[strtolower($method)] =   $this->db->$method($args[0]);
            return $this;
       }else{
            throw_exception('Class ['.__CLASS__.']: method ('.$method.') not exist');
            return;
       }
    }

    /**
     * 切换模型/Table
     * @access public
     * @param string $table 表名(不带前缀)
     * @return false | integer
    */
  	final public function model($table='') {
		if(empty($table)) return;
		$this->ftable=$this->tablePrefix.$table;
		$this->table=$table;
		$this->_tableCheck();
		return $this;
  	}
	
	/**
     * SQL查询
     * @access public
     * @param mixed $sql  SQL指令
     * @return mixed
     */
    final public function query($sql) {
        return $this->db->query($sql);
    }

    /**
     * 执行SQL语句
     * @access public
     * @param string $sql  SQL指令
     * @return false | integer
     */
    final public function execute($sql) {
        return $this->db->execute($sql);
    }

    /**
     * 根据SQL获取记录数
     * @access public
     * @param string $sql  SQL指令
	 * @param bool $write 是否主执行
     * @return integer
     */
    final public function count($sql, $write=false) {
		$sql=str_replace("\n"," ",$sql); //替换buildQuery中的换行符
		$sql = preg_replace ("/SELECT ([`\w\*\(\)\>\<=, .]+) FROM ([`.\w]+)/is", "SELECT COUNT(*) AS num FROM $2", $sql);
		
		if(preg_match('/([ ]+)group([ ]+)by([ ]+)/is',$sql)){ //含有group by时计算count
			$sql="select count(1) from ($sql) count";
		};
		return $this->getOne($sql, $write);
    }

	/**
	 * 根据PK设置where
	 * @param string $i：主键见值
	 * @return object itself
	 */
	public function wherePk($i)	{
		if(isset($this->_query['from'])) {
			$ftable=$this->_query['from'];
		}else{
			$ftable=$this->ftable;
		}
		$pk = $this->_getPk($ftable);
		$this->_query['where']="`$pk`=".intval($i);
		return $this;
	}

	 /**
	 * 根据主键查找记录
     * @access public
	 * @param $i 主键值
	 * @param bool $write 是否主执行
	 * @return array记录结果
	 */
	final public function getPk($i, $write=false) {
		return $this->wherePk($i)->getRow('', $write);
	}

	/**
	 * 使用sql只查询一个字段：如`name`或count(*)
     * @access public
	 * @param $sql 		查询语句[例:select name from ftable where id=1]
	 * @param bool $write 是否主执行
	 * @return string	查询结果
	 */
	 final public function getOne($sql='', $write=false) {
		if(empty($sql)) $sql=$this->_parseQuery();
		return $this->db->getOne($sql, $write);
	 }
	 
	 /**
	 * 使用sql只查询所有记录集
     * @access public
	 * @param $sql 		查询语句
	 * @param bool $write 是否主执行
	 * @return array	查询结果集数组
	 */
	 final public function getAll($sql='', $write=false) {
		if(empty($sql)) $sql=$this->_parseQuery();
		return $this->db->getAll($sql, $write);
	 }

	 /**
	 * 使用sql只查询一行记录
     * @access public
	 * @param $sql 		查询语句
	 * @param bool $write 是否主执行
	 * @return array	查询单行记录数组
	 */
	final public function getRow($sql='', $write=false) {
		if(empty($sql)) $sql=$this->_parseQuery();
		return $this->db->getRow($sql, $write);
	 }

	 /**
	 * 使用sql只查询一列记录
     * @access public
	 * @param $sql 		查询语句
	 * @param bool $write 是否主执行
	 * @return array	查询单列记录数组
	 */
	 final public function getCol($sql='', $write=false) {
		if(empty($sql)) $sql=$this->_parseQuery();
		return $this->db->getCol($sql,$write);
	 }

	/**
	 * 使用sql查询分页数据
     * @access public
	 * @param string $sql 查询SQL
	 * @param bool $write 是否主执行
	 * @return array [count,data]
	 */
	final public function getPage($sql='', $write=false) {
		//检查当前页码和每页数目
		if(!isset($this->_query['page'])) $this->page();
		$page=$this->_query['page'];
		$size=$this->_query['size'];
		if(empty($sql)) $sql=$this->_parseQuery();
		
		//计算count:分离order by,排除limit
		$order='';
		if($pos=strpos($sql,"ORDER BY")) {
			$order=trim(substr($sql,$pos));
			if($plimit=strpos($order,"LIMIT")) {
				$order=trim(substr($order,0,$plimit));
			}
			$sql=trim(substr($sql,0,$pos));
		}
		//记录个数
		$count = $this->count($sql, $write); 
		
		//获取实际页码数目
		$page = min(intval($page), ceil($count/$size));
		$page = max(intval($page), 1);
		$offset = $size*($page-1);
		
		$data=array();
		if($count>0) {
			$data=$this->getAll($sql.' '.$order." LIMIT $offset, $size", $write);
		}
		return array('count'=>$count,'data'=>$data);
	}

	/**
     * 执行添加记录操作
     * @access public
     * @param array $data （数组key为字段值，数组值为数据取值）
     * @param boolean $replace 是否replace
     * @param boolean $execute 是否立即执行
     * @return mixed
     */
    final public function add($data,$replace=false,$execute=true) {
        if(empty($data)) {
            $this->error = 'data type invalid';
            return false;
        }
        //数据处理
        $data = $this->_facade($data);
        if(empty($data)) {
			$this->error = 'data empty';
			return false;
        }
		return $this->db->insert($data, $this->ftable, $replace,$execute);
    }
	
	/**
     * 生成添加记录SQL语句
     * @access public
     * @param array $data （数组key为字段值，数组值为数据取值）
     * @param boolean $replace 是否replace
     * @return string sql
     */
	public function addSql($data,$replace=false){
		return $this->add($data,$replace,false);
	}
	
	/**
	 * 执行更新记录操作
     * @access public
	 * @param $data: 数组或字符串，建议数组
	 * --数组array('name'=>'val','hits'=>'+=1')=>析为`name`='val',`hits`=`hits`+1
	 * --字符串:`name`='val',`hits`=`hits`+1
     * @param boolean $execute 是否立即执行
	 * @return boolean
	 */ 
	final public function update($data,$execute=true) {
        //数据处理
        $data = $this->_facade($data);
        if(empty($data)) {
			$this->error = 'data empty';
			return false;
        }
		$where='';
		if(isset($this->_query['where'])) {
			$where=$this->_query['where'];
		}
        if(empty($where)) {
            //如果存在主键数据,则自动作为更新条件
            if(is_array($data) && isset($data[$this->_getPk()])) {
                $pk   =  $this->_getPk();
                $where   =  " `$pk`=$data[$pk] ";
                unset($data[$pk]);
            }else{
                // 如果没有任何更新条件则不执行
                $this->error = 'update operation wrong: no where condition';
                return false;
            }
        }
		return $this->db->update($data, $this->ftable, $where,$execute);
    }

	/**
     * 生成更新记录SQL语句
     * @access public
     * @param array $data （数组key为字段值，数组值为数据取值）
     * @return string sql
     */
	public function updateSql($data){
		return $this->update($data,false);
	}

	 /**
	 * 根据主键更新记录
     * @access public
     * @param array $data：同update
	 * @param $i 主键值
	 * @return boolean
	 */
	final public function updatePk($data,$i) {
		$id = $this->_getPk();
		return $this->where("`$id`=".intval($i))->update($data);
	}

	 /**
	 * 执行删除记录操作
     * @access public
     * @param boolean $execute 是否立即执行
	 * @return boolean
	 */
	final public function delete($execute=true) {
		$where='';
		if(isset($this->_query['where'])) {
			$where=$this->_query['where'];
		}
		if(empty($where)) {
            $this->error = 'delete operation wrong: no where condition';
            return false;
		}
		return $this->db->delete($this->ftable, $where, $execute);
	}

	/**
     * 生成删除记录SQL语句
     * @access public
     * @return string sql
     */
	public function deleteSql(){
		return $this->delete(false);
	}

	 /**
	 * 根据主键删除一条记录
     * @access public
	 * @param $i 主键值
	 * @return boolean
	 */
	final public function deletePk($i) {
	  $id = $this->_getPk();
	  return $this->where("`$id`=".intval($i))->delete();
	}

	/**
     * 启动事务
     * @access public
     * @return void
     */
    public function startTrans() {
        $this->db->startTrans();
        return ;
    }

    /**
     * 提交事务
     * @access public
     * @return boolean
     */
    public function commit() {
        return $this->db->commit();
    }

    /**
     * 事务回滚
     * @access public
     * @return boolean
     */
    public function rollback() {
        return $this->db->rollback();
    }
	
	/**
     * 批处理执行SQL语句:execute操作
     * @access public
     * @param array $sql  SQL批处理指令
     * @return boolean
     */
    public function commitTrans($sql=array()) {
        if(!is_array($sql)) return false;
        //自动启动事务支持
        $this->startTrans();
        try{
            foreach ($sql as $_sql) {
                $result   =  $this->execute($_sql);
                if(false === $result) {
                    // 发生错误自动回滚事务
                    $this->rollback();
                    return false;
                }
            }
            // 提交事务
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
			$this->error=$e->getMessage();
        }
        return true;
    }
	
	/**
     * 返回最后插入的ID
     * @access public
     * @return string
     */
    public function getLastID() {
        return $this->db->getLastID();
    }
	
	/**
     * 返回最后执行的sql语句
     * @access public
     * @return string
     */
    public function getLastSql() {
        return $this->db->getLastSql();
    }

	/**
     * 返回模型的错误信息
     * @access public
     * @return string
     */
    public function getError() {
        return $this->error;
    }

    /**
     * 返回数据库的错误信息
     * @access public
     * @return string
     */
    public function getDbError() {
        return $this->db->getError();
    }

	/**
     * 获取主键名称
     * @access protected
     * @param string $ftable 表名(带前缀)
     * @return string
     */
    protected function _getPk($ftable='') {
		$ftable =  ($ftable=='') ? $this->ftable : $ftable;
		if(!isset($this->pk[$ftable]))
			$this->_tableCheck($ftable);
        return $this->pk[$ftable];
    }
	
	 /**
	 * 获得数据表的字段名称
     * @access private
	 * @param $table 表名
	 * @return array 表字段数组
	 */
	private function getFields($table='') {
		$table = ($table=='') ? $this->ftable : $table;
		return isset($this->fields[$table]) ? $this->fields[$table]['_field'] : $this->db->getFields($table);
	}

	 /**
	 * 获得数据表的字段名称
     * @access private
	 * @param $table 表名
	 * @return array 表名数组
	 */
	private function getTables($dbName='') {
		$dbName = ($dbName=='') ? $this->dbName : $dbName;
		return $this->db->getTables($dbName);
	}
	
   /**
     * 自动检测数据表信息
     * @access private
     * @param string $ftable 表名(带前缀)
     * @return void
     */
    private function _tableCheck($ftable='') {
		$ftable=empty($ftable) ? $this->ftable : $ftable;
        if(!isset($this->fields[$ftable])) {
             //每次都会读取数据表信息·
             $this->_flush($ftable);
        }
    }

    /**
     * 获取字段信息并缓存
     * @access private
     * @param string $ftable 表名(带前缀)
     * @return void
     */
    private function _flush($ftable='') {
		$ftable=empty($ftable) ? $this->ftable : $ftable;
        //缓存不存在则查询数据表信息
        $_fields =   $this->db->getFields($ftable);
        if(!$_fields) { //无法获取字段信息
            return false;
        }
        $fields['_autoinc'] = false;
        foreach ($_fields as $key=>$val) {
            //记录字段类型
            $type[$key]    =   $val['type'];
            if($val['primary']) {
                $fields['_pk'] = $key;
				$this->pk[$ftable] = $key;
                if($val['autoinc']) $fields['_autoinc'] =true;
            }
        }
        $fields['_field'] =  $type;
		$this->fields[$ftable]=$fields;
    }
	
	/**
     * 对保存到数据库的数据进行处理
     * @access private
     * @param mixed $data 要操作的数据
     * @return boolean
     */
     private function _facade($data) {
        // 检查非数据字段
        if(!empty($this->fields[$this->ftable]) && is_array($data)) {
            foreach ($data as $key=>$val) {
                if(!in_array($key,array_keys($this->fields[$this->ftable]['_field']),true)) {
                    unset($data[$key]);
                }
            }
        }
        return $data;
     }
	 
	/**
     * 得到完整的数据表名
     * @access private
     * @return string
     */
    private function getTableName() {
        return $this->ftable;
    }

	/**
	 * 将数组转换为SQL语句
     * @access private
	 * @param array $where 要生成的数组
	 * @param string $font 连接串。
	 */
	protected function _sqls($where, $font = ' AND ') {
		if (is_array($where)) {
			$sql = '';
			foreach ($where as $key=>$val) {
				$sql .= $sql ? " $font `$key` = '$val' " : " `$key` = '$val'";
			}
			return $sql;
		} else {
			return $where;
		}
	}

	/**
	 * 连贯操作：设置分页
     * @access public
	 * @param integer $page 当前页码
	 * @param integer $size 每页记录数
	 * @return object itself
	 */
	public function page($page=0,$size=20) {
		if(empty($page)) $page = intval(sget('page')) ? intval(sget('page')) : 1;
		$this->_query['page']=$page;
		$this->_query['size']=$size;
		return $this;
	}

	/**
	 * 连贯操作：设置Inner Join
	 * @param string $table：可包含前缀或使用别名(user u).
	 * @param mixed $conditions：参考where
	 * @return object itself
	 */
	public function join($table, $conditions)
	{
		$this->_query['join'][]=$this->db->join('join', $table, $conditions);
		return $this;
	}

	/**
	 * 连贯操作：设置LEFT OUTER JOIN
	 * @param string $table：可包含前缀或使用别名(user u).
	 * @param mixed $conditions：参考where
	 * @return object itself
	 */
	public function leftjoin($table, $conditions)
	{
		$this->_query['join'][]=$this->db->join('left join', $table, $conditions);
		return $this;
	}

	/**
	 * 连贯操作：设置RIGHT OUTER JOIN
	 * @param string $table：可包含前缀或使用别名(user u).
	 * @param mixed $conditions：参考where
	 * @return object itself
	 */
	public function rightjoin($table, $conditions)
	{
		$this->_query['join'][]=$this->db->join('right join', $table, $conditions);
		return $this;
	}

	/**
	 * 连贯操作：设置DISTINCT
	 * @param mixed $columns：参考select
	 * @return string
	 */
	public function distinct($columns='*')
	{
		$this->_query['distinct']=true;
		return $this->db->select($columns);
	}
	
    /**
     * 连贯操作：分析表达式
     * @access proteced
     * @param array $options 表达式参数
     * @return array
     */
    protected function _parseQuery() {
        $query =  $this->_query;
        $this->_query  =   array(); //查询过后清空
		
		//自动获取表名
        if(!isset($query['from']))
			$query['from'] =$this->getTableName();
		return $this->db->buildQuery($query);
    }
}

/**
 *  db.class.php 数据库操作基类
 */
class db{
    //最后插入ID
    protected $lastID         = null;
    //返回或者影响记录数
    protected $numRows        = 0;
    //返回字段数
    protected $numCols        = 0;
    //事务指令数
    protected $transTimes     = 0;
    //错误信息
    protected $error          = '';
    protected $errno          = '';
    //当前连接ID
    protected $_linkID        = null;
    //当前查询ID
    protected $queryID        = null;
	//当前SQL指令
    protected $queryStr       = '';
    //是否已经连接数据库
    protected $connected      = false;
    //数据库连接参数配置
    protected $config         = '';
	//slave正常
    protected $slave  = true;
	//是否显示调试信息 如果启用会在日志文件记录sql语句
    public $debug = false;
	
	/**
     * 架构函数
     * @access public
     */
    public function __construct() {
    }
   
	/**
	 * 取得数据库类实例
	 * @param $db_config 数据库配置
	 * @return object
	 */
	static public function getInstance($db_config = '') {
		return new dbMysql($db_config); //POS机不能缓存
	}

	 /**
     * 数据库调试 记录当前SQL
     * @access protected
     */
    protected function debug() {
        //记录操作结束时间
        if ($this->debug) {
			 $str=($this->error) ? ' [Error('.$this->errno.'):'.$this->error.']' : '';
			 $str=$this->queryStr.$str.'[RunTime:'.T('queryStartTime','queryEndTime',6).'s]';
			 wlog("debug.log",$str);
        }
    }
	 
	/**
     * 获取最近插入的ID
     * @access public
     * @return string
     */
    public function getLastID() {
        return $this->lastID;
    }
	
	/**
     * 获取最近一次查询的sql语句 
     * @param string $model  模型名
     * @access public
     * @return string
     */
    public function getLastSql() {
        return $this->queryStr;
    }

	/**
     * 获取最近的错误信息
     * @access public
     * @return string
     */
    public function getError() {
        return $this->error;
    }
	
	/**
	 * 执行更新记录操作
	 * @param $data 		数组或字符串，建议数组。
	 * @param $table 		数据表
	 * @param $where 		更新数据时的条件
	 * @return boolean
	 */
	public function update($data, $table, $where = '',$execute=true) {
		if($table == '' || $where == '') return false;
		
		$where = ' WHERE '.$where;
		$field = '';
		if(is_string($data) && $data != '') {
			$field = $data;
		} elseif (is_array($data) && count($data) > 0) {
			$fields = array();
			foreach($data as $k=>$v) {
				switch (substr($v, 0, 2)) {
					case '+=':
						$v = substr($v,2);
						if (is_numeric($v)) {
							$fields[] = $this->quoteColumn($k).'='.$this->quoteColumn($k).'+'.$this->quoteValue($v);
						} else {
							continue;
						}
						
						break;
					case '-=':
						$v = substr($v,2);
						if (is_numeric($v)) {
							$fields[] = $this->quoteColumn($k).'='.$this->quoteColumn($k).'-'.$this->quoteValue($v);
						} else {
							continue;
						}
						break;
					default:
						$fields[] = $this->quoteColumn($k).'='.$this->quoteValue($v);
				}
			}
			$field = implode(',', $fields);
		} else {
			return false;
		}
		$sql = 'UPDATE `'.$table.'` SET '.$field.$where;
		if($execute){
			return $this->execute($sql);
		}else{
			return $sql;	
		}
	}
	
	/**
	 * 执行添加记录操作
     * @param array $data （数组key为字段值，数组值为数据取值）
	 * @param string $table 数据表
	 * @param boolean $replace 是否替换
	 * @param boolean $execute 是否立即执行
	 * @return mix
	 */
	public function insert($data, $table, $replace = false, $execute=true) {
		if(!is_array( $data ) || $table == '' || count($data) == 0) {
			return false;
		}
		
		$fielddata = array_keys($data);
		$valuedata = array_values($data);
		array_walk($fielddata, array($this, 'quoteColumn'));
		array_walk($valuedata, array($this, 'quoteValue'));
		
		$field = implode (',', $fielddata);
		$value = implode (',', $valuedata);
		
		$cmd = $replace ? 'REPLACE INTO ' : 'INSERT INTO ';
		$sql = $cmd.$this->quoteTable($table).' ('.$field.') VALUES ('.$value.')';
		if($execute){
			return $this->execute($sql);
		}else{
			return $sql;	
		}
	}

	/**
	 * 执行删除记录操作
	 * @param string $table  数据表
	 * @param string $where  删除数据条件,不充许为空
	 * @param boolean $execute 是否立即执行
	 * @return boolean
	 */
	public function delete($table, $where, $execute=true) {
		if ($table == '' || $where == '') {
			return false;
		}
		$where = ' WHERE '.$where;
		$sql = 'DELETE FROM `'.$table.'`'.$where;
		if($execute){
			return $this->execute($sql);
		}else{
			return $sql;	
		}
	}
	

	/**
	 * 连贯操作：设置查询的字段
	 * @param mixed $columns：
	 * --字符('id,name')，数组(array('id','name'))，带前缀('m.id')，别名(m.id uid)
	 * @return $string
	 */
	public function select($columns='*')
	{
		$select='';
		if(is_string($columns) && strpos($columns,'(')!==false)
			$select=$columns;
		else
		{
			if(!is_array($columns)){
				$columns=preg_split('/\s*,\s*/',trim($columns),-1,PREG_SPLIT_NO_EMPTY);
			}
			foreach($columns as $i=>$column)
			{
				if(strpos($column,'(')===false)
				{
					if(preg_match('/^(.*?)(?i:\s+as\s+|\s+)(.*)$/',$column,$matches))
						$columns[$i]=$this->quoteColumn($matches[1]).' AS '.$this->quoteColumn($matches[2]);
					else
						$columns[$i]=$this->quoteColumn($column);
				}
			}
			$select=implode(', ',$columns);
		}
		return $select;
	}

	/**
	 * 连贯操作：设置查询的数据表
	 * @param mixed $tables
	 * --可为字符('user')，数组(array('user','admin'))，可使用别名('user u')
	 * @return string
	 */
	public function from($tables)
	{
		$from='';
		if(is_string($tables) && strpos($tables,'(')!==false)
			$from=$tables;
		else
		{
			if(!is_array($tables))
				$tables=preg_split('/\s*,\s*/',trim($tables),-1,PREG_SPLIT_NO_EMPTY);
			foreach($tables as $i=>$table)
			{
				if(strpos($table,'(')===false)
				{
					if(preg_match('/^(.*?)(?i:\s+as\s+|\s+)(.*)$/',$table,$matches)) { // with alias
						$tables[$i]=$this->quoteTable($matches[1]).' '.$this->quoteTable($this->config['table_pre'].$matches[2]);
					}
					else
						$tables[$i]=$this->quoteTable($this->config['table_pre'].$table);
				}
			}
			$from=implode(', ',$tables);
		}
		return $from;
	}

	/**
	 * 连贯操作：设置排序
	 * @param mixed $columns
	 * --字符('id asc,nam desc')或数组(array('id asc','name desc'))
	 * @return string
	 */
	public function order($columns)
	{
		$order='';
		if(is_string($columns) && strpos($columns,'(')!==false)
			$order=$columns;
		else
		{
			if(!is_array($columns))
				$columns=preg_split('/\s*,\s*/',trim($columns),-1,PREG_SPLIT_NO_EMPTY);
			foreach($columns as $i=>$column)
			{
				if(is_object($column))
					$columns[$i]=(string)$column;
				else if(strpos($column,'(')===false)
				{
					if(preg_match('/^(.*?)\s+(asc|desc)$/i',$column,$matches))
						$columns[$i]=$this->quoteColumn($matches[1]).' '.strtoupper($matches[2]);
					else
						$columns[$i]=$this->quoteColumn($column);
				}
			}
			$order=implode(', ',$columns);
		}
		return $order;
	}

	/**
	 * 连贯操作：设置查询条件
	 * @param mixed $conditions：
	 * and/or: array('and', 'id=1', 'id=2')=>'id=1 AND id=2';
	 * : array('and', 'type=1', array('or', 'id=1', 'id=2'))=>'type=1 AND (id=1 OR id=2)'
	 * in/not in: array('in', 'id', array(1,2,3)) => 'id IN (1,2,3)'
	 * like/not like/or like/or not like: array('like', 'name', '%tester%') => "name LIKE '%tester%'"
	 * : array('like', 'name', array('%test%', '%sample%')) => "name LIKE '%test%' AND name LIKE '%sample%'"
	 * @return string
	 */
	public function where($conditions)
	{
		return $this->_parseConditions($conditions);
	}

	/**
	 * 连贯操作：生成Join语句
	 * @param string $type：('join', 'left join', 'right join', 'cross join', 'natural join')
	 * @param mixed $conditions：参考where
	 * @return object itself
	 */
	public function join($type, $table, $conditions='')
	{
		if(strpos($table,'(')===false)
		{
			if(preg_match('/^(.*?)(?i:\s+as\s+|\s+)(.*)$/',$table,$matches))  // with alias
				$table=$this->quoteTable($matches[1]).' '.$this->quoteTable($matches[2]);
			else
				$table=$this->quoteTable($table);
		}

		$conditions=$this->_parseConditions($conditions);
		if($conditions!='')
			$conditions=' ON '.$conditions;

		return strtoupper($type) . ' ' . $table . $conditions;
	}

	/**
	 * 连贯操作：设置GROUP BY
	 * @param mixed $columns：字符('id,name')或数组(array('id','name'))
	 * @return string
	 */
	public function group($columns)
	{
		$group='';
		if(is_string($columns) && strpos($columns,'(')!==false)
			$group=$columns;
		else
		{
			if(!is_array($columns))
				$columns=preg_split('/\s*,\s*/',trim($columns),-1,PREG_SPLIT_NO_EMPTY);
			foreach($columns as $i=>$column)
			{
				if(is_object($column))
					$columns[$i]=(string)$column;
				else if(strpos($column,'(')===false)
					$columns[$i]=$this->quoteColumn($column);
			}
			$group=implode(', ',$columns);
		}
		return $group;
	}

	/**
	 * 连贯操作：设置HAVING
	 * @param mixed $conditions：参考where
	 * @return string
	 */
	public function having($conditions)
	{
		return $this->_parseConditions($conditions);
	}

	/**
	 * 连贯操作：设置LIMIT
	 * @param string $str:['10'或'0,10']
	 * @return object itself
	 */
	public function limit($str)
	{
		$arr=explode(',',$str,2);
		foreach($arr as $k=>$v){
			$arr[$k]=(int)$v;	
		}
		return implode(',',$arr);
	}

	/**
	 * 连贯操作：设置UNION
	 * @param string $sql:SQL语句
	 * @return object itself
	 */
	public function union($sql)
	{
		static $union;
		if(isset($union) && is_string($union)) $union=array($union);
		$union[]=$sql;

		return $union;
	}

	/**
	 * 生成条件
	 * @param mixed $conditions
	 * @return string
	 */
	private function _parseConditions($conditions)
	{
		if(!is_array($conditions)) return $conditions;
		else if($conditions===array()) return '';
		$n=count($conditions);
		$operator=strtoupper($conditions[0]);
		if($operator==='OR' || $operator==='AND')
		{
			$parts=array();
			for($i=1;$i<$n;++$i)
			{
				$condition=$this->_parseConditions($conditions[$i]);
				if($condition!=='')
					$parts[]='('.$condition.')';
			}
			return $parts===array() ? '' : implode(' '.$operator.' ', $parts);
		}

		if(!isset($conditions[1],$conditions[2]))
			return '';

		$column=$conditions[1];
		if(strpos($column,'(')===false)
			$column=$this->quoteColumn($column);

		$values=$conditions[2];
		if(!is_array($values))
			$values=array($values);

		if($operator==='IN' || $operator==='NOT IN')
		{
			if($values===array())
				return $operator==='IN' ? '0=1' : '';
			foreach($values as $i=>$value)
			{
				if(is_string($value))
					$values[$i]=$this->quoteValue($value);
				else
					$values[$i]=(string)$value;
			}
			return $column.' '.$operator.' ('.implode(', ',$values).')';
		}

		if($operator==='LIKE' || $operator==='NOT LIKE' || $operator==='OR LIKE' || $operator==='OR NOT LIKE')
		{
			if($values===array())
				return $operator==='LIKE' || $operator==='OR LIKE' ? '0=1' : '';

			if($operator==='LIKE' || $operator==='NOT LIKE')
				$andor=' AND ';
			else
			{
				$andor=' OR ';
				$operator=$operator==='OR LIKE' ? 'LIKE' : 'NOT LIKE';
			}
			$expressions=array();
			foreach($values as $value)
				$expressions[]=$column.' '.$operator.' '.$this->quoteValue($value);
			return implode($andor,$expressions);
		}
		throw_exception("Unknown operator:".$operator);
	}

	/**
	 * 格式化表名
	 * @param string $name：表名，可包含前缀
	 * @return string
	 */
	public function quoteTable($name)
	{
		if(strpos($name,'.')===false)
			return $this->quoteKey($name);
		$parts=explode('.',$name);
		foreach($parts as $i=>$part)
			$parts[$i]=$this->quoteKey($part);
		return implode('.',$parts);

	}

	/**
	 * 格式化字段名
	 * @param string $name：字段名，可包含前缀
	 * @return string
	 */
	public function quoteColumn($name)
	{
		if(($pos=strrpos($name,'.'))!==false)
		{
			$prefix=$this->quoteTable(substr($name,0,$pos)).'.';
			$name=substr($name,$pos+1);
		}
		else
			$prefix='';
		return $prefix . ($name==='*' ? $name : $this->quoteKey($name));
	}


	/**
	 * 对字段值两边加引号
	 * @param string $name：表/字段名
	 * @return string
	 */
	public function quoteKey(&$key)
	{
		$key = '`'.trim($key).'`';
		return $key;
	}

	/**
	 * 格式化输入的值
	 * @param string $str
	 * @return string
	 */
	public function quoteValue(&$value)
	{
		if(is_int($value) || is_float($value)) return $value;
		$value = "'".$value."'";
		return $value;
	}

	
	/**
	 * 生成SQL statement
	 * @param array $query
	 * @return string the SQL statement
	 */
	public function buildQuery($query)
	{
		$sql=isset($query['distinct']) && $query['distinct'] ? 'SELECT DISTINCT' : 'SELECT';
		$sql.=' '.(isset($query['select']) ? $query['select'] : '*');

		if(isset($query['from']))
			$sql.="\nFROM ".$query['from'];
		else
			throw_exception('The DB query must contain the "from" portion.');

		if(isset($query['join']))
			$sql.="\n".(is_array($query['join']) ? implode("\n",$query['join']) : $query['join']);

		if(isset($query['where']))
			$sql.="\nWHERE ".$query['where'];

		if(isset($query['group']))
			$sql.="\nGROUP BY ".$query['group'];

		if(isset($query['having']))
			$sql.="\nHAVING ".$query['having'];

		if(isset($query['order']))
			$sql.="\nORDER BY ".$query['order'];

		if(isset($query['limit']))
			$sql.="\nLIMIT ".$query['limit'];

		if(isset($query['union']))
			$sql.="\nUNION (\n".(is_array($query['union']) ? implode("\n) UNION (\n",$query['union']) : $query['union']) . ')';

		return $sql;
	}

}

/**
 * DbMysql数据库驱动类
 */
class dbMysql extends db{

    /**
     * 架构函数 读取数据库配置信息
     * @access public
     * @param array $config 数据库配置数组
     */
    public function __construct($config=''){
        if(!empty($config)) {
            $this->config   =   $config;
			$this->connect($config);			
        }
    }

    /**
     * 连接数据库方法
     * @access public
     */
    public function connect($config=array(),$linkNum=0,$force=false) {
		if(!empty($config['pconnect'])) {
			$this->_linkID = @mysql_pconnect($config['host'], $config['user'], $config['password']);
		}else{
			$this->_linkID = @mysql_connect($config['host'], $config['user'], $config['password'],true)or die('connect db error');
		}

		if (!empty($config['database']) && !mysql_select_db($config['database'], $this->_linkID)){
			  throw_exception($this->error());
		}
		mysql_query("SET NAMES 'utf8'", $this->_linkID);
		return $this->_linkID;
    }

    /**
     * 释放查询结果
     * @access public
     */
    public function free() {
		if(is_resource($this->queryID)) {
			mysql_free_result($this->queryID);
			$this->queryID = null;
		}
    }

    /**
     * 执行查询 返回数据集
     * @access public
     * @param string $str  sql指令
	 * @param bool $write 是否主执行
     * @return mixed
     */
 	 public function query($str, $write=false) {
		$w=preg_match("/^(update|insert|delete|alter|create|drop)/i",trim($str)) ? true : false;
		if($w || $write){
		   return $this->execute($str);
		}
		
        if (!$this->_linkID) return false;
        $this->queryStr = $str;
		
        //释放前次的查询结果
        if($this->queryID) $this->free();
		
        $this->queryID = mysql_query($str, $this->_linkID);
        if (false === $this->queryID ) {
            $this->error();
            return false;
        } else {
            $this->numRows = @mysql_num_rows($this->queryID);
            return $this->queryID;
        }
  	}
	
	/**
     * 执行语句
     * @access public
     * @param string $str  sql指令
     * @return integer
     */
    public function execute($str) {
        if (!$this->_linkID) return false;
        $this->queryStr = $str;
		
        //释放前次的查询结果
        if ($this->queryID) {$this->free();}
		
        $this->queryID = mysql_query($str, $this->_linkID) ;
        
        if ( false === $this->queryID) {
            $this->error();
            return false;
        } else {
            $this->numRows = @mysql_affected_rows($this->_linkID);
            $this->lastID = @mysql_insert_id($this->_linkID);
            return $this->queryID;
        }
    }

	/**
     * 数据库错误信息,并显示当前的SQL语句
     * @access public
     * @return string
     */
    public function error() {
		$this->errno = @mysql_errno($this->_linkID);
    	$this->error = @mysql_error($this->_linkID);
		$msg="DB_ERROR:".$this->queryStr.' ('.$this->errno.':'.$this->error.")";
		wlog("sql.log",$msg);
        return $this->error;
    }
	
	/**
     * 取得数据表的字段信息
     * @access public
    */
    public function getFields($tableName) {
        $res = $this->query('SHOW COLUMNS FROM '.$tableName);
        $info  = array();
		if($res !== false){
		   while($val = mysql_fetch_assoc($res)){
                $info[$val['Field']] = array(
                    'name'    => $val['Field'],
                    'type'    => $val['Type'],
                    'notnull' => (bool) ($val['Null'] === ''), // not null is empty, null is yes
                    'default' => $val['Default'],
                    'primary' => (strtolower($val['Key']) == 'pri'),
                    'autoinc' => (strtolower($val['Extra']) == 'auto_increment'),
                );
			}
		}
        return $info;
    }
	
	/**
     * 启动事务
     * @access public
     * @return void
     */
    public function startTrans() {
        if (!$this->_linkID) return false;
		$this->transTimes = 0;
        mysql_query('START TRANSACTION', $this->_linkID);
        $this->transTimes++;
        return ;
    }

    /**
     * 用于非自动提交状态下面的查询提交
     * @access public
     * @return boolen
     */
    public function commit() {
        if ($this->transTimes > 0) {
            $result = mysql_query('COMMIT', $this->_linkID);
            $this->transTimes = 0;
            if(!$result){
                throw_exception($this->error());
            }
        }
        return true;
    }

    /**
     * 事务回滚
     * @access public
     * @return boolen
     */
    public function rollback() {
        if ($this->transTimes > 0) {
            $result = mysql_query('ROLLBACK', $this->_linkID);
            $this->transTimes = 0;
            if(!$result){
                throw_exception($this->error());
            }
        }
        return true;
    }

	/**
     * 取得数据库的表信息
     * @access public
     */
    public function getTables($dbName='') {
        if(!empty($dbName)) {
           $sql    = 'SHOW TABLES FROM '.$dbName;
        }else{
           $sql    = 'SHOW TABLES ';
        }
        $result =   $this->getAll($sql);
        $info   =   array();
        foreach ($result as $key => $val) {
            $info[$key] = current($val);
        }
        return $info;
    }

	/**
     * SQL指令安全过滤
     * @access public
     * @param string $str  SQL字符串
     * @return string
     */
    public function escapeString($str) {
        if($this->_linkID) {
            return mysql_real_escape_string($str,$this->_linkID);
        }else{
            return mysql_escape_string($str);
        }
    }

	/**
	 * 使用sql只查询一个字段：如`name`或count(*)
	 * @param $sql 		查询语句[例:select name from ftable where id=1]
	 * @param bool $write 是否主执行
	 * @return string	查询结果
	 */
	public function getOne($sql, $write=false){
	  if(!strpos(strtolower($sql),'limit ')) $sql = trim($sql . ' LIMIT 1');
		 $res = $this->query($sql, $write);
		 if($res !== false){
			 $row = mysql_fetch_row($res);
			 if($row !== false) return $row[0];
			 else return '';
		 }
		 return '';
	 }

	 /**
	 * 使用sql只查询所有记录集
	 * @param $sql 		查询语句
	 * @param bool $write 是否主执行
	 * @return array	查询结果集数组
	 */
	public function getAll($sql, $write=false){
		$res = $this->query($sql, $write);
		if($res !== false){
			$arr = array();
			while ($row = mysql_fetch_assoc($res)){
				$arr[] = $row;
			}
			return $arr;
		}else{
			return array();
		}
	}

	 /**
	 * 使用sql只查询一条记录
	 * @param $sql 		查询语句
	 * @param bool $write 是否主执行
	 * @return array	查询单行记录数组
	 */
	public function getRow($sql, $write=false){
	  if(!strpos(strtolower($sql),'limit ')) $sql = trim($sql . ' LIMIT 1');
			
	  $res = $this->query($sql, $write);
	   if ($res !== false)
		  return mysql_fetch_assoc($res);
	  else
		  return array();
	 }

	 /**
	 * 使用sql只查询一列记录
	 * @param $sql 		查询语句
	 * @param bool $write 是否主执行
	 * @return array	查询单列记录数组
	 */
	public function getCol($sql, $write=false)
	{
		$res = $this->query($sql, $write);
		if ($res !== false){
			$arr = array();
			while ($row = mysql_fetch_row($res)){
				$arr[] = $row[0];
			}
			return $arr;
		}else{
			return array();
		}
	}
    /**
     * 选择数据库
     * @access public
     * @param dbname 数据库名
     * @return true/false
     */
	public function select_db($dbname){
		return mysql_select_db($dbname, $this->_linkID);
	}
	
}

class app {
	/**
     * 自定义错误处理
     * @access public
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     * @return void
     */
    static public function appError($errno, $errstr, $errfile, $errline) {
		$errorStr = "[$errno] $errstr ".basename($errfile)." 第 $errline 行.";
		wlog('appEr.log',$errorStr);
    }
	
	/**
     * 自定义异常处理
     * @access public
     * @param mixed $e 异常对象
     */
    static public function appException($e) {
		if(is_object($e)){
			wlog('appEx.log','appException 异常抛出对象');
			return;
		}
        $error=$e->__toString();
        if (!is_array($error)) {
            $trace = debug_backtrace();
            $e['message'] = $error;
            $e['file'] = $trace[0]['file'];
            $e['class'] = $trace[0]['class'];
            $e['function'] = $trace[0]['function'];
            $e['line'] = $trace[0]['line'];
        } else {
            $e = $error;
        }
		wlog('appEx.log',json_encode($e));
	}
}

//自定义异常处理
function throw_exception($msg, $type='appException', $code=0) {
    if (class_exists($type, false))
        throw new $type($msg, $code, true);
    else
    	wlog("throw.log",$msg);
	
	die();
}
?>
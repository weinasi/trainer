<?php
/**
 * MySQL 数据库操作工具类, 方便数据库操作.
 * @author: 老汉憨憨
 */
class MYSQL {

	private $pdo;
	private $pre;
	private $where;
	private $limit;
	private $order;
	private $group;
	private $field;
    protected $queryStr = '';
	public function __construct($host, $port, $user, $pw, $db, $pre, $charset) {
		if (!$this->pdo) {
			$dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $db;
			$this->pdo = new PDO($dsn, $user, $pw);
			if ($charset) {
				$this->pdo->exec('set names ' . $charset);
			}
			$this->pre = $pre;
		}
	}
	public function __destruct() {
		if ($this->pdo) {
			$this->pdo = null;
		}
	}
	/*
		快捷找到一行记录
	*/
	public function find() {
		$result = $this->query($this->sql());
		if (!$result) {
			return false;
		} else {
			return $result[0];
		}
	}
	/*
		选择所有的资源
	*/
	public function select($f = true) {
		if ($f) {
			return $this->query($this->sql());
		} else {
			$r = $this->query($this->sql());
			if ($r === false) {
				return array();
			} else {
				return $r;
			}
		}
	}
	/*
		获取某行记录的某个字段
	*/
	public function getField($field) {
		$this->field = $field;
		$result = $this->query($this->sql());
		if (!$result) {
			return false;
		}
		return $result[0][$field];
	}
	/*
		设置某行记录的某个字段
	*/
	public function setField($field, $value, $bl = true) {
		if ($bl) {
			$sql = 'update ' . $this->table . ' set `' . $field . '`=' . '\'' . $value . '\' ' . $this->where;
		} else {
			$sql = 'update ' . $this->table . ' set `' . $field . '`=' . $value . ' ' . $this->where;
		}
		return $this->execute($sql);
	}

	/*
		统计
	*/
	public function count() {
		$sql = 'select count(*) as lh_count from ' . $this->table . ' ' . $this->where . ' limit 0,1';
		$result = $this->query($sql);
		return intval($result[0]['lh_count']);
	}
	public function sum($field) {
		$sql = 'select sum(' . $field . ') as lh_sum from ' . $this->table . ' ' . $this->where;
		$result = $this->query($sql);
		return intval($result[0]['lh_sum']);
	}
	public function begin() {
		$this->pdo->beginTransaction();
	}

	public function commit() {
		$this->pdo->commit();
	}
	public function rollback() {
		$this->pdo->rollBack();
	}
	/*
		添加记录
	*/
	private function predata($data) {
		$predata = array();
		$predata['fields'] = array();
		$predata['values'] = array();
		foreach ($data as $k => $v) {
			$predata['fields'][] = '`' . $k . '`';
			$predata['values'][] = '\'' . $v . '\'';
		}
		return $predata;
	}
	public function add($data, $bl = true) {
		$predata = $this->predata($data);
		$sql = "insert " . $this->table . ' (' . implode(',', $predata['fields']) . ') values (' . implode(',', $predata['values']) . ')';
		$result = $this->execute($sql);
		if ($result) {
			$id = $this->pdo->lastInsertId();
			if ($id) {
				return $id;
			}
			return true;
		}
		return false;
	}
	public function error() {
		$error = $this->pdo->errorInfo();
		if (!empty($error[2])) {
			return $error[2];
		} else {
			return '';
		}
	}
	/*
		更新添加记录
	*/
	public function readd($data) {
		$predata = $this->predata($data);
		$sql = "replace into " . $this->table . ' (' . implode(',', $predata['fields']) . ') values (' . implode(',', $predata['values']) . ')';
		$result = $this->execute($sql);
		if ($result) {
			$id = $this->pdo->lastInsertId();
			if ($id) {
				return $id;
			}
			return true;
		} else {
			return false;
		}

	}
	/*
		更新记录
	*/
	public function save($data) {
		$new = array();
		foreach ($data as $k => $v) {
			$new[] = '`' . $k . '`=\'' . $v . '\'';
		}
		$sql = 'update ' . $this->table . ' set ' . implode(',', $new) . ' ' . $this->where;
		return $this->execute($sql);
	}
	/*
		删除记录
	*/
	public function delete() {
		$sql = 'delete from ' . $this->table . ' ' . $this->where;
		return $this->execute($sql);
	}
	public function query($sql) {
        $this->queryStr = $sql;
		$result = $this->pdo->query($sql);
		$this->resetsql();
		if (!$result) {
			return false;
		}
		$data = array();
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$data[] = $row;
		}
		if (count($data) == 0) {
			return false;
		}
		return $data;
	}
	/*
		生成SQL并执行返回结果资源
	*/
	public function execute($sql) {
        $this->queryStr = $sql;
		$result = $this->pdo->exec($sql);
		$this->resetsql();
		if ($result === false) {
			return false;
		}
		return true;
	}
	/*
		设置where
	*/
	public function where($map) {
		$this->where = 'where ' . $map;
		return $this;
	}

	/*
		设置order
	*/
	public function order($order) {
		$this->order = 'order by ' . $order;
		return $this;
	}
	/*
		设置group
	*/
	public function group($field) {
		$this->group = 'group by ' . $field;
		return $this;
	}
	/*
		设置limit
	*/
	public function limit($limit) {
		if (is_numeric($limit)) {
			$this->limit = 'limit 0,' . $limit;
		} else {
			$this->limit = 'limit ' . $limit;
		}
		return $this;
	}
	/*
		设置field
	*/
	public function field($field) {
		$this->field = $field;
		return $this;
	}
	/*
		设置table
	*/
	public function table($table) {
		$this->table = $this->pre . $table;
		return $this;
	}
	/*
		生成查询sql
	*/
	public function sql() {
		$sql = 'select';
		if ($this->field) {
			$sql .= ' ' . $this->field . ' from';
		} else {
			$sql .= ' * from';
		}
		$sql .= ' ' . $this->table;
		if ($this->where) {
			$sql .= ' ' . $this->where;
		}
		if ($this->group) {
			$sql .= ' ' . $this->group;
		}
		if ($this->order) {
			$sql .= ' ' . $this->order;
		}
		if ($this->limit) {
			$sql .= ' ' . $this->limit;
		}
		return $sql;
	}
    public function getLastSql() {
        return $this->queryStr;
    }    
	/*
		查询完毕以后清空
	*/
	Public function resetsql() {
		$this->field = '';
		$this->order = '';
		$this->limit = '';
		$this->where = '';
	}
}
?>

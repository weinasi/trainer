<?php
/**
 * Redis缓存驱动 - 支持Redis集群与读写分离
 * 要求安装phpredis扩展：https://github.com/nicolasff/phpredis
 */
class CacheRedis extends Cache {
	/**
	 * 架构函数
	 * @param array $options 缓存参数
	 * @access public
	 */
	public function __construct($options = array()) {
		if (!extension_loaded('redis')) {
			E('系统不支持:redis');
		}

		$options = array_merge(array(
			'host' => C('REDIS_HOST') ? C('REDIS_HOST') : '127.0.0.1',
			'port' => C('REDIS_PORT') ? C('REDIS_PORT') : 6379,
			'timeout' => C('REDIS_TIMEOUT') ? C('REDIS_TIMEOUT') : false,
			'persistent' => false,
			'auth' => C('REDIS_AUTH') ? C('REDIS_AUTH') : null, //auth认证
			'rw_separate' => C('REDIS_RW_SEPARATE') ? C('REDIS_RW_SEPARATE') : false, //主从分离
		), $options);

		$this->options = $options;
		$this->options['expire'] = isset($options['expire']) ? $options['expire'] : C('DATA_CACHE_TIME');
		$this->options['prefix'] = isset($options['prefix']) ? $options['prefix'] : C('DATA_CACHE_PREFIX');
		$this->options['length'] = isset($options['length']) ? $options['length'] : 0;
		$this->options['func'] = $options['persistent'] ? 'pconnect' : 'connect';
	}

	/**
	 * 主从连接
	 * @access public
	 * @param bool $master true=主连接
	 */
	public function init_master() {
		$host = explode(",", $this->options['host']);

		$func = $this->options['func'];
		$this->handler = new \Redis;
		$this->options['timeout'] === false ?
		$this->handler->$func($host[0], $this->options['port']) :
		$this->handler->$func($host[0], $this->options['port'], $this->options['timeout']);
		if ($this->options['auth'] != null) {
			$this->handler->auth($this->options['auth']);
		}
	}

	public function init_slave() {
		$host = explode(",", $this->options['host']);
		if (count($host) > 1 && $this->options['rw_separate'] == true) {
			array_shift($host);
			shuffle($host);
		}

		$func = $this->options['func'];
		$this->handler = new \Redis;
		$this->options['timeout'] === false ?
		$this->handler->$func($host[0], $this->options['port']) :
		$this->handler->$func($host[0], $this->options['port'], $this->options['timeout']);
		if ($this->options['auth'] != null) {
			$this->handler->auth($this->options['auth']);
		}
	}

	/**
	 * 读取缓存
	 * @access public
	 * @param string $name 缓存变量名
	 * @return mixed
	 */
	public function get($name) {
		N('cache_read', 1);
		$this->init_slave();
		$value = $this->handler->get($this->options['prefix'] . $name);
		$jsonData = json_decode($value, true);
		return ($jsonData === NULL) ? $value : $jsonData; //检测是否为JSON数据 true 返回JSON解析数组, false返回源数据
	}

	/**
	 * 写入缓存
	 * @access public
	 * @param string $name 缓存变量名
	 * @param mixed $value  存储数据
	 * @param integer $expire  有效时间（秒）
	 * @return boolen
	 */
	public function set($name, $value, $expire = null) {
		N('cache_write', 1);
		$this->init_master();
		if (is_null($expire)) {
			$expire = intval($this->options['expire']);
		}
		$name = $this->options['prefix'] . $name;
		//对数组/对象数据进行缓存处理，保证数据完整性
		$value = (is_object($value) || is_array($value)) ? json_encode($value) : $value;
		//删除缓存操作支持
		if ($value === null) {
			return $this->handler->delete($this->options['prefix'] . $name);
		}
		if (is_int($expire)) {
			$result = $this->handler->setex($name, $expire, $value);
		} else {
			$result = $this->handler->set($name, $value);
		}
		if ($result && $this->options['length'] > 0) {
			// 记录缓存队列
			$this->queue($name);
		}
		return $result;
	}

	/**
	 * 删除缓存
	 * @access public
	 * @param string $name 缓存变量名
	 * @return boolen
	 */
	public function rm($name) {
		$this->init_master();
		return $this->handler->delete($this->options['prefix'] . $name);
	}

	/**
	 * 清除缓存
	 * @access public
	 * @return boolen
	 */
	public function clear() {
		$this->init_master();
		return $this->handler->flushDB();
	}

	/**
	 * 析构释放连接
	 * @access public
	 */
	/*
		        public function __destruct() {
		            $this->handler->close();
		        }
	*/
}

<?php
/**
 * 微信异步通知初始化文件
 * @author hupeng
 */
header('Content-type: text/html; charset=utf-8');
function_exists('date_default_timezone_set') && date_default_timezone_set('PRC');
set_time_limit(0); //设置运行时间
define('APP_DEBUG', true);
define('APP_ENV', 'product'); //环境变量的定义--product生产环境，develop开发环境
define('ROOT_PATH', dirname(__FILE__) . '/');
define('CORE_PATH', ROOT_PATH . "core/");
define('CONF_PATH', ROOT_PATH . "conf/");
define('CACHE_PATH', ROOT_PATH . "runtime/");
define('LOG_PATH', ROOT_PATH . 'log/');

//error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
error_reporting(E_ALL ^ E_DEPRECATED);
define('CORE_TIME', time());

//加载配置文件
if (is_file(CONF_PATH . 'config_' . APP_ENV . '.php')) {
	//根据环境不同加载不同的配置文件
	if (APP_ENV == 'product') {
		C(include (CONF_PATH . 'config_product.php'));
	} else {
		C(include (CONF_PATH . 'config_develop.php'));
	}
}

//加载数据库核心文件
include CORE_PATH . 'db.class.php';
include CORE_PATH . 'model.class.php';

wlog('4.log',CORE_PATH . 'model.class.php');
//设定错误和异常处理
set_error_handler(array('app', 'appError'));
set_exception_handler(array('app', 'appException'));

function getDbConfig() {
	return array(
		'database' => C('DB_NAME'),
		'host' => C('DB_HOST'),
		'user' => C('DB_USER'),
		'password' => C('DB_PWD'),
		'table_pre' => C('DB_PREFIX'),
		'charset' => C('DB_CHARSET'),
		'type' => C('DB_TYPE'),
		'pconnect' => 0,
	);
}
/**
 * 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @param mixed $default 默认值
 * @return mixed
 */
function C($name = null, $value = null, $default = null) {
    static $_config = array();
    // 无参数时获取所有
    if (empty($name)) {
        return $_config;
    }
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtoupper($name);
            if (is_null($value)) {
                return isset($_config[$name]) ? $_config[$name] : $default;
            }

            $_config[$name] = $value;
            return null;
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name);
        $name[0] = strtoupper($name[0]);
        if (is_null($value)) {
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : $default;
        }

        $_config[$name[0]][$name[1]] = $value;
        return null;
    }
    // 批量设置
    if (is_array($name)) {
        $_config = array_merge($_config, array_change_key_case($name, CASE_UPPER));
        return null;
    }
    return null; // 避免非法参数
}

function M($table, $database = '', $pre = '') {
	if ($database) {
		C('DB_NAME', $database);
	}
	if ($pre) {
		C('DB_PREFIX', $pre);
	}
	$db = new MYSQL(C('DB_HOST'), 3306, C('DB_USER'), C('DB_PWD'), C('DB_NAME'), C('DB_PREFIX'), C('DB_CHARSET'));
	$table = strtolower($table);
	return $db->table($table);
}



/**
 * 保持文件日志(1M切换)
 * @param string $filename 绝对路径文件名
 * @param string $data 日志内容
 */
function wlog($filename = '', $data = '') {
	$filename = ROOT_PATH . 'log/' . $filename;
	if (!is_dir(dirname($filename))) {
		mkdir(dirname($filename), 0755, true);
	}
	//检测日志文件大小，超过1M则重新生成
	if (is_file($filename) && floor(1024000) <= filesize($filename)) {
		rename($filename, dirname($filename) . '/' . date("YmdHis") . '-' . basename($filename));
	}
	$data = date("Y-m-d H:i:s") . " " . $data . "\r\n";
	return error_log($data, 3, $filename);
}

/**
 * 处理订单
 * @param string $orderno
 * @param string  $pre
 * @return boolean
 */
function handleOrder($orderno,$pre){
   if($pre == 'tr'){
		wlog('notify.log',$orderno);
		wlog('notify.log',$pre);
		$res = M('Order')->where("orderno = '$orderno'")->save(array('status'=>1));
		
	}else if($pre == 'ac'){
		$res = M('Act_order')->where("orderno = '$orderno'")->save(array('status'=>1));
	}
	
	if($res !== false){
		wlog('notify.log','ok');
		return false;
	}else{
	    wlog('notify.log','fail');
	    return true;
	}
    
}

//handleOrder('tr14658935255145','tr');

?> 
<?php
/**
* 	配置账号信息
*/

class WxPayConf_pub
{
	//=======【基本信息设置】=====================================
	//微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	const APPID = 'wx70ba88d99c5c6881'; //你的微信公共号标识
	//受理商ID，身份标识
	const MCHID = '1232864002';//微信支付商户号
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	const KEY = 'dljfkjdkj7876d786fJDKJFSLKJD8797'; //微信支付key 自己设置的
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	const APPSECRET = '4ae34bdc4ba497fac5af700087c2298d'; //公共平台appscecret
	const JS_API_CALL_URL = 'http://www.yixiangit.com/trainer/index.php?m=weixin&c=Pay&a=index'; //微信jsapi地址
	
	
	
	const SSLCERT_PATH = '{$path}/cacert/apiclient_cert.pem';
	const SSLKEY_PATH = '{$path}/cacert/apiclient_key.pem';

	const NOTIFY_URL = 'http://www.yixiangit.com/trainer/index.php?m=weixin&c=Pay&a=notify';//微信异步通知地址
	const CURL_TIMEOUT = 60;
}

	
?>
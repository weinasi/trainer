<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

include dirname(dirname(__FILE__))."/lib/WxPay.Api.php";
include dirname(dirname(__FILE__))."/lib/WxPay.Notify.php";
include 'log.php';

$f = dirname(dirname(__FILE__));
//初始化日志
$logHandler= new CLogFileHandler($f."/logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		
		$out_trade_no = $data['out_trade_no']; //商户系统的订单号，与请求一致。
		$order_pre = substr($out_trade_no,0,2);
		handleOrder($out_trade_no,$order_pre);
	
		
		return true;
	}
}



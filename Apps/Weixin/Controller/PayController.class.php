<?php
/**
* 订单支付处理
* @date: 2016-4-11
* @author: hupeng
*/
namespace Weixin\Controller;
class PayController extends CommonController {
	
	public function _initialize(){
		parent::_initialize();
		
		
	}
	/**
	 * 开始支付
	 * @date: 2016-5-9
	 * @author: hupeng
	 */
	public function index(){
		$order_id=I('get.orderid','');
		$act_order = I('get.acorder','');
		if(empty($order_id) && empty($act_order)){
			$this->error('参数错误');
		}
		if(!empty($act_order)){
			$orderinfo=M('ActOrder')->where('id=%d',$act_order)->find();
			$url = U('Activity/Index/myorder',array('id'=>$act_order));
		}else{
			$orderinfo=M('Order')->where('order_id=%d',$order_id)->find();
			$url = U('User/orderDetail',array('id'=>$order_id));
		}
		
		$this->assign('price',$orderinfo['total_fee']);
	
		//商品相关的数组
		$ordershow = array(
				//"totalfee"=>$orderinfo['order_money']*100,//总费用，分
				"totalfee"=>1,//测试用的总费用，分
				"orderno"=>$orderinfo['orderno'],//订单号
				"id"=>$order_id,//订单id
		);
		$product =array(
				"id"=>1,
				"name"=>"意象支付"
		);
	   
		//正式环境启用他
		$this->parepareweixin($ordershow,$url); 
		$this->display('pay');
	}
	
	/**
	* 支付准备
	* @param string $orderinfo 订单信息
	* @param string $url 支持成功跳转url
	* @date: 2016-5-10
	* @author: hupeng
	*/
	public function parepareweixin($orderinfo,$url){
	    ini_set('date.timezone','Asia/Shanghai');
	    include CMF_ROOT.'/Wxpay/lib/WxPay.Api.php';
	    include CMF_ROOT.'/Wxpay/example/WxPay.JsApiPay.php';
	    include CMF_ROOT.'/Wxpay/example/log.php';
	    //初始化日志
	    $logHandler= new \CLogFileHandler(CMF_ROOT."/Log/".date('Y-m-d').'.log');
	    $log = \Log::Init($logHandler, 15);
	    
	    //①、获取用户openid
	    $tools = new \JsApiPay();
	    $openId = $tools->GetOpenid();
	    
	    //②、统一下单http://jiao.xinjinjin.com/Wxpay/notify.php
	    $input = new \WxPayUnifiedOrder();
	    $input->SetBody("支付订单：".$orderinfo['orderno']);
	    $input->SetAttach("weixin");
	    $input->SetOut_trade_no($orderinfo['orderno']);
	    $input->SetTotal_fee($orderinfo['totalfee']);
	    $input->SetTime_start(date("YmdHis"));
	    $input->SetTime_expire(date("YmdHis", time() + 600));
	    $input->SetGoods_tag("imagery_wx_pay");
	    $input->SetNotify_url("http://jiao.xinjinjin.com/Wxpay/notify.php");
	    $input->SetTrade_type("JSAPI");
	    $input->SetOpenid($openId);
	    $order = \WxPayApi::unifiedOrder($input);
	  
	    $jsApiParameters = $tools->GetJsApiParameters($order);
	    
	    $this->assign('url',$url);
		//wlog('notify.log',$url);
	    $this->assign("jsApiParameters",$jsApiParameters);
	   
	}
	
	
}






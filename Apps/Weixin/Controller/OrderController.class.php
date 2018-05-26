<?php
/**
* 订单
* @date: 2016-4-11
* @author: hupeng
*/
namespace Weixin\Controller;
class OrderController extends CommonController {
	/**
	* 教练详情
	* @date: 2016-4-18
	* @author: hupeng
	*/
	public function confirm(){
		$trainer_id = I('get.id','');
		$text = I('get.dut_date','');
		$uid = session('uid');
		if(empty($trainer_id) || $trainer_id < 0){
			$this->error('参数非法');
		}
		if(empty($text)){
			$this->error('预定时间没选择');
		}
		$trainer_info = M('Trainer')
				->where('trainer_id=%d',$trainer_id)->find();
		$trainer_info['teaching_type'] = $this->_techtype($trainer_info['teaching_type']);
		$ordertime = $this->textTotimes($text);
		$phone = M('Members')->where('id=%d',$uid)->getField('mobile');
		//优惠券
		$coupon_ids = M('Members')->where('id=%d',$uid)->getField('coupon_ids');
		$this_time = time();
		$coupon = M('Coupon')
			->where("coupon_id in ($coupon_ids) and (type=0 or end_time>$this_time)")
			->select();
		//首单免单金额
		//$baseUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']);
		//dump($baseUrl);exit();
		$this->assign('coupon',$coupon);
		$this->assign('phone',$phone);
		$this->assign('trainer_id',$trainer_id);
		$this->assign('timetext',$text);
		$this->assign('ordertime',$ordertime);
		$this->assign('info',$trainer_info);
		$this->display('order_query');
	}
	
	/**
	* 提交订单
	* @date: 2016-4-21
	* @author: hupeng
	*/
	public function saveOrder(){
		if(IS_POST){
			$place = I('post.train_place','');
			$remark = I('post.remark','');
			$usernum = I('post.usernum',0);
			$couponid = I('post.couponid',0);
			$dut_date = I('post.dut_date','');
			$trainer_id = I('post.trainer_id',0);
			$phone = I('post.phone','');
			$price = I('post.actual_pay','');
			if(empty($place)){
				echo json_encode(array('status'=>0,'msg'=>'地点必须填写'));
			}
			if(preg_match('/\d+/',$price,$arr)){
				$price = $arr[0];
			}
			$savedata = array(
					'trainer_id' => $trainer_id,
					'member_id' => session('uid'),
					'time_text' => $dut_date,
					'train_person' => $usernum,
					'place' => $place,
					'phone' => $phone,
					'message' => $remark,
					'total_fee' => $price,
					'add_time' => time(),
					'status' => 0,
					'coupon_id' => $couponid,
					'orderno' => 'tr'.time().rand(1000, 9999)
			);
			
			$res = M('Order')->data($savedata)->add();
			if($res){
			    $url = U('Pay/index',array('orderid'=>$res));
				echo json_encode(array('status'=>1,'url'=>$url));
				exit();
			}else{
				echo json_encode(array('status'=>0,'msg'=>'下单失败,请重试'));
				exit();
			}
		}
	}
	
	
	
	
	
	

}

<?php

namespace Activity\Controller;
use Weixin\Controller\CommonController;
/**
* 活动管理
* @author: hupeng
*/
class IndexController extends CommonController {
	function _initialize() {
		parent::_initialize();
	}

	/**
	* 活动列表
	* @date: 2016-4-22
	* @author: hupeng
	*/
	public function index(){
		$_map['end_time'] = array('egt',time());
		$list = M('Activity')->where($_map)->order('create_time desc')->select();
		$this->assign('list',$list);
		$this->display('activity_list');
	}
	
	/**
	* 详情
	* @date: 2016-5-24
	* @author: hupeng
	*/
	public function detail(){
		$id = I('get.id',0);
		if($id == 0){
			$this->error('参数错误');
		}
		$info = M('Activity')->where('activity_id=%d',$id)->find();
		$info['spec'] = explode('|', $info['spec']);
		$this->assign('info',$info);
		$this->display('activity_detail');
	}
	
	/**
	* 下单
	* @date: 2016-5-25
	* @author: hupeng
	*/
	public function order(){
		$id = I('post.id',0);
		if($id == 0){
			echo json_encode(array('status'=>0,'msg'=>'参数有误'));
			exit();
		}
		$is_order = M('ActOrder')->where('activity_id=%d and member_id=%d and status=1',$id,session('uid'))->count();
		if($is_order > 0){
			echo json_encode(array('status'=>0,'msg'=>'此活动你已经报名过!'));
			exit();
		}
		$act_info = M('Activity')->where('activity_id=%d',$id)->find();
		$data = array(
				'orderno' => 'ac'.time().rand(1000, 9999),
				'activity_id' => $id,
				'total_fee' => $act_info['price'],
				'add_time' => time(),
				'status' => 0,
				'member_id' => session('uid')
		);
		$res = M('ActOrder')->data($data)->add();
		if($res){
			$url = U('Weixin/Pay/index',array('acorder'=>$res));
			
			echo json_encode(array('status'=>1,'url'=>$url));
			exit();
		}else{
			echo json_encode(array('status'=>0,'msg'=>'报名失败'));
			exit();
		}
	}
	
	/**
	* 我的订单
	* @date: 2016-6-4
	* @author: hupeng
	*/
	public function myorder(){
		$uid = session('uid');
		$orderlist = M()
			->table('__ACT_ORDER__ as ao')
			->join('__ACTIVITY__ as a on ao.activity_id = a.activity_id')
			->field('ao.id order_id,ao.add_time,ao.total_fee,ao.status,a.activity_name,a.imgurl,a.place')
			->where('ao.member_id=%d and ao.status=%d',$uid,1)
			->select();
		
		$this->assign('order',$orderlist);
		
		$this->display('my_order');
	}
	
	/**
	* 订单删除
	* @date: 2016-6-4
	* @author: hupeng
	*/
	public function orderCancel(){
		$id = I('post.orderid',0);
		if($id == 0){
			echo json_encode(array('status'=>0,'msg'=>'参数错误'));
			exit();
		}
		
		$res = M('ActOrder')->where('id=%d',$id)->save(array('status'=>-1));
		if($res !== false){
			echo json_encode(array('status'=>1));
			exit();
		}else{
			echo json_encode(array('status'=>0,'msg'=>'删除失败'));
			exit();
		}
	}

	


}

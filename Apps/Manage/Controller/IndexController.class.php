<?php

namespace Manage\Controller;
/**
* 用户中心
* @author: hupeng
*/
class IndexController extends CommonController {
	function _initialize() {
		parent::_initialize();
	}

	/**
	* 我的订单
	* @date: 2016-4-22
	* @author: hupeng
	*/
	public function index(){
		$uid = session('t_uid');
		$status = I('get.status','');
		$_map = "o.trainer_id={$uid}";
		if(empty($status)){
			$_map .= ' and o.status in (1)';
		}else{
			$_map .= " and o.status={$status}";
		}
			
		$orderlist = M()
				->table('__ORDER__ as o')
				->join('left join __TRAINER__ as t on o.trainer_id=t.trainer_id')
				->field('o.order_id,o.add_time,o.time_text,o.status,o.total_fee,t.trainer_id,t.trainer_name,t.teaching_type,t.avatar')
				->where($_map)
				->limit(5)
				->order('add_time desc')
				->select();
		foreach ($orderlist as $k=>$v){
			$orderlist[$k]['teaching_type'] = $this->_techtype($v['teaching_type']);
			$orderlist[$k]['time_text'] = $this->textTotimes($v['time_text']);
			//剩余支付时间
			$orderlist[$k]['left_time'] = $v['add_time']+1800;
			$orderlist[$k]['star'] = getStar($v['trainer_id']);
		}

		$this->assign('orderstatus',$status);
		$this->assign('order',$orderlist);
		$this->display('my_order');
	}
	
	/**
	* 订单详情
	* @date: 2016-4-25
	* @author: hupeng
	*/
	public function orderDetail(){
		$uid = session('uid');
		$orderid = I('get.id','');
		if(empty($orderid)){
			$this->error('参数有误');
		}
		$orderinfo = M()
				->table('__ORDER__ as o')
				->join('left join __TRAINER__ as t on o.trainer_id=t.trainer_id')
				->field('o.*,t.trainer_name,t.teaching_type,t.trainer_id,t.avatar')
				->where('order_id=%d',$orderid)
				->find();
		$orderinfo['teaching_type'] = $this->_techtype($orderinfo['teaching_type']);
		$orderinfo['time_text'] = $this->textTotimes($orderinfo['time_text']);
		//此用户对此教练的评论
		$this->assign('info',$orderinfo);
		$this->display('order_detail');
	}
	
	/**
	 * 获取更多订单
	 * @date: 2016-4-22
	 * @author: hupeng
	 */
	public function getOrder(){
		$uid = session('t_uid');
		$startnum = I('startnum');
		$status = I('status','');
		$_map = "o.trainer_id={$uid}";
		if(empty($status)){
			$_map .= ' and o.status in (1)';
		}else{
			$_map .= " and o.status={$status}";
		}
		$orderlist = M()
			->table('__ORDER__ as o')
			->join('left join __TRAINER__ as t on o.trainer_id=t.trainer_id')
			->field('o.order_id,o.add_time,o.time_text,o.status,o.total_fee,t.trainer_id,t.trainer_name,t.teaching_type,t.avatar')
			->where($_map)
			->limit($startnum,5)
			->order('add_time desc')
			->select();
		foreach ($orderlist as $k=>$v){
			$orderlist[$k]['teaching_type'] = $this->_techtype($v['teaching_type']);
			$orderlist[$k]['time_text'] = $this->textTotimes($v['time_text']);
			//剩余支付时间
			$orderlist[$k]['left_time'] = date('Y/m/d H:i:s',($v['add_time']+1800));
			$orderlist[$k]['add_time'] = date('Y-m-d H:i',$v['add_time']);
			$orderlist[$k]['star'] = getStar($v['trainer_id']);
		}
		if($orderlist){
			$this->ajaxReturn(array('data'=>$orderlist,'status'=>1));
		}else{
		
			$this->ajaxReturn(array('data'=>array(),'status'=>0));
			
		}
		
	}
	
	/**
	* 订单接受
	* @date: 2016-5-5
	* @author: hupeng
	*/
	public function orderReceive(){
		$orderid = I('post.orderid','');
		if(empty($orderid)){
			echo json_encode(array('status'=>0,'msg'=>'参数错误'));
			exit();
		}
		$res = M('Order')->where('order_id=%d',$orderid)->save(array('status'=>2));
		if($res !== false){
			echo json_encode(array('status'=>1));
			exit();
		}else{
			echo json_encode(array('status'=>0,'msg'=>'操作失败'));
			exit();
		}
	}

	
	/**
	 * 订单删除
	 * @date: 2016-4-23
	 * @author: hupeng
	 */
	public function orderDel(){
		$orderid = I('post.orderid','');
		if(empty($orderid)){
			echo json_encode(array('status'=>0,'msg'=>'参数错误'));
			exit();
		}
		$res = M('Order')->where('order_id=%d',$orderid)->save(array('status'=>-2));
		if($res !== false){
			echo json_encode(array('status'=>1));
			exit();
		}else{
			echo json_encode(array('status'=>0,'msg'=>'取消失败'));
			exit();
		}
	}
	
	/**
	* 评价
	* @date: 2016-4-25
	* @author: hupeng
	*/
	public function comment(){
		$content = I('post.content','');
		$memberid = I('post.id','');
		if(empty($memberid)){
			echo json_encode(array('status'=>0,'msg'=>'参数错误'));
			exit();
		}
		if(empty($content)){
			echo json_encode(array('status'=>0,'msg'=>'内容不能为空'));
			exit();
		}
		
		
		$data = array(
				'content' => $content,
				'trainer_id' => session('t_uid'),
				'member_id' => $memberid,
				'star' => 0,
				'time' => time(),
				'type' => 2
		);
		$data1 = array(
				'member_id' =>$memberid,
				'basic_skill' => I('post.basic_skill',0),
				'coordinate' => I('post.coordinate',0),
				'feel' => I('post.feel',0),
				'body' => I('post.body',0),
				'study' => I('post.study',0),
				'trianer_id' =>session('t_uid')
		);
		$res = M('Comment')->data($data)->add();
		$res1 = M('MemberScore')->data($data1)->add();
		if($res && $res1){
			echo json_encode(array('status'=>1,'msg'=>'评价成功'));
			exit();
		}else{
			echo json_encode(array('status'=>0,'msg'=>'评价失败'));
			exit();
		}
	}
	


}

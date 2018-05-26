<?php

namespace Weixin\Controller;
/**
* 用户中心
* @author: hupeng
*/
class UserController extends CommonController {
	function _initialize() {
		parent::_initialize();
	}
	
	/**
	* 个人中心首页
	* @date: 2016-4-23
	* @author: hupeng
	*/
	function index() {
		$uid = session('uid');
		$userinfo = M('Members')->where('id=%d',$uid)->find();
		$couponids = explode(',', $userinfo['coupon_ids']);
		$userinfo['coupon_num'] = count($couponids);
		
		$this->assign('user',$userinfo);
		$this->display('user_center');
	}
	
	/**
	* 信息设置
	* @date: 2016-4-24
	* @author: hupeng
	*/
	public function set(){
		$uid = session('uid');
		if(IS_POST){
			$phone = I('post.phone','');
			$username = I('post.username','');
			$sex = I('post.sex',1);
			if(empty($username)){
				$ret = array('status'=>0,'msg'=>'姓名不能为空');
				echo json_encode($ret);
				exit;
			}
			$userdata = array(
					'user_nickname' => $username,
					'mobile' => $phone,
					'sex' => $sex
			);
			$res = M('Members')->where('id=%d',$uid)->save($userdata);
			if($res !== false){
				$ret = array('status'=>1);
				echo json_encode($ret);
				exit;
			}else{
				$ret = array('status'=>0,'msg'=>'保存失败');
				echo json_encode($ret);
				exit;
			}
			
			
		}else{
			$userinfo = M('Members')->field('user_nickname,mobile,sex')->where('id=%d',$uid)->find();
			$this->assign('user',$userinfo);
			$this->display('my_set');
		}
	}
	
	/**
	* 关于我们
	* @date: 2016-4-24
	* @author: hupeng
	*/
	public function about(){
		$this->display('about_us');
	}
	
	/**
	 * 协议
	 * @date: 2016-4-24
	 * @author: hupeng
	 */
	public function protocol(){
		$info = M('Posts')->where('ID=3')->find();
		$this->assign('info',$info);
		$this->display('protocol');
	}
	
	/**
	* 评分
	* @date: 2016-4-26
	* @author: hupeng
	*/
	public function score(){
		$uid = session('uid');
		$userinfo = M('Members')->field('sex,user_nickname,mobile')->where('id=%d',$uid)->find();
		
		//评分
		$scorelist = M('MemberScore')->where('member_id=%d',$uid)->select();
        $basic_skill = 0;
        $coordinate = 0;
        $feel = 0;
        $body = 0;
        $study = 0;
        if(!empty($scorelist)){
        	foreach ($scorelist as $k=>$v){
        		$basic_skill = $basic_skill + $v['basic_skill'];
        		$coordinate = $coordinate + $v['coordinate'];
        		$feel = $feel + $v['feel'];
        		$body = $body + $v['body'];
        		$study = $study + $v['study'];
        	}
        	$basic_skill = ceil($basic_skill/count($scorelist));
        	$coordinate = ceil($coordinate/count($scorelist));
        	$feel = ceil($feel/count($scorelist));
        	$study = ceil($study/count($scorelist));
        	$body = ceil($body/count($scorelist));
        }
       //教练对用户的评价
        $commentlist = M()
        		->table('__COMMENT__ as c')
        		->join('left join __TRAINER__ as t on c.trainer_id=t.trainer_id')
        		->field('c.*,t.trainer_name')
       			->where('c.member_id=%d and c.type=2',$uid)
        		->limit(5)
        		->select();
        $this->assign('comment',$commentlist);
        $this->assign('basic_skill',$basic_skill);
        $this->assign('coordinate',$coordinate);
        $this->assign('feel',$feel);
        $this->assign('study',$study);
        $this->assign('body',$body);
		$this->assign('userinfo',$userinfo);
		$this->display('my_score');
	}
	
	/**
	* 获取评论
	* @date: 2016-4-26
	* @author: hupeng
	*/
	public function getComment(){
		$startnum = I('post.startnum',5);
		$uid = session('uid');
		$commentlist = M()
				->table('__COMMENT__ as c')
				->join('left join __TRAINER__ as t on c.trainer_id=t.trainer_id')
				->field('c.*,t.trainer_name')
				->where('c.member_id=%d and c.type=2',$uid)
				->limit($startnum,5)
				->select();
		if($commentlist){
			echo json_encode(array('data'=>$commentlist));
			exit();
		}else{
			echo json_encode(array('data'=>array()));
			exit();
		}
		
	}

	/**
	* 建议
	* @date: 2016-4-24
	* @author: hupeng
	*/
	public function feedback(){
		if(IS_POST){
			$content = I('post.content','');
			if(empty($content)){
				$ret = array('status'=>0,'msg'=>'内容不能为空');
				echo json_encode($ret);
				exit;
			}
			$data = array(
					'content' => $content,
					'member_id' => session('uid'),
					'create_time' => time()
			);
			$res = M('Feedback')->data($data)->add();
			if($res){
				$ret = array('status'=>1,'msg'=>'提交成功');
				echo json_encode($ret);
				exit;
			}else{
				$ret = array('status'=>0,'msg'=>'提交失败');
				echo json_encode($ret);
				exit;
			}
		}else{
			$this->display('feedback');
		}
	}
	
	/**
	* 上传头像
	* @date: 2016-4-23
	* @author: hupeng
	*/
	public function uploadAvatar(){
		$uid = session('uid');
		$img = $_POST['img'];
		if(empty($img)){
			return false;
		}
		// 获取图片
		list($type, $data) = explode(',', $img);
		// 判断类型
		if(strstr($type,'image/jpeg')!==''){
			$ext = '.jpg';
		}elseif(strstr($type,'image/gif')!==''){
			$ext = '.gif';
		}elseif(strstr($type,'image/png')!==''){
			$ext = '.png';
		}
		$model = M('Members');		
		// 生成的文件名
		$photo = "./static/weixin/avatar/".time().$ext; 
		// 生成文件
		$res = file_put_contents($photo, base64_decode($data), true);
		if($res !== false){
			//裁剪图片
			$image = new \Think\Image();
			$image->open($photo);
			$image->thumb(255, 255,\Think\Image::IMAGE_THUMB_CENTER)->save($photo);
			$r = $model->where('id='.$uid)->save(array('my_avatar'=>$photo));
			$ret = array('img'=>$photo);
			echo json_encode($ret);
			exit;
		}else{
			$ret = array('msg'=>'上传失败');
			echo json_encode($ret);
			exit;
		}
		
		
	}
	
	/**
	* 我的订单
	* @date: 2016-4-22
	* @author: hupeng
	*/
	public function orderList(){
		$uid = session('uid');
		$status = I('get.status','');
		$_map = "member_id={$uid}";
		if(empty($status)){
			$_map .= ' and status in (0,1)';
		}else{
			$_map .= " and status={$status}";
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
		$uid = session('uid');
		$startnum = I('startnum');
		$status = I('status','');
		$_map = "member_id={$uid}";
		if(empty($status)){
			$_map .= ' and status in (0,1)';
		}else{
			$_map .= " and status={$status}";
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
	* 订单取消
	* @date: 2016-4-23
	* @author: hupeng
	*/
	public function orderCancel(){
		$orderid = I('post.orderid','');
		if(empty($orderid)){
			echo json_encode(array('status'=>0,'msg'=>'参数错误'));
			exit();
		}
		$res = M('Order')->where('order_id=%d',$orderid)->save(array('status'=>-1));
		if($res !== false){
			echo json_encode(array('status'=>1));
			exit();
		}else{
			echo json_encode(array('status'=>0,'msg'=>'取消失败'));
			exit();
		}
	}
	
	/**
	* 确认完成订单
	* @date: 2016-4-23
	* @author: hupeng
	*/
	public function orderFinish(){
		$orderid = I('post.orderid','');
		if(empty($orderid)){
			echo json_encode(array('status'=>0,'msg'=>'参数错误'));
			exit();
		}
		$res = M('Order')->where('order_id=%d',$orderid)->save(array('status'=>3));
		if($res !== false){
			echo json_encode(array('status'=>1));
			exit();
		}else{
			echo json_encode(array('status'=>0,'msg'=>'取消失败'));
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
		$star = I('post.star');
		$trainerid = I('post.id','');
		if(empty($trainerid)){
			echo json_encode(array('status'=>0,'msg'=>'参数错误'));
			exit();
		}
		if(empty($content)){
			echo json_encode(array('status'=>0,'msg'=>'内容不能为空'));
			exit();
		}
		if(empty($star)){
			echo json_encode(array('status'=>0,'msg'=>'评分必选'));
			exit();
		}
		
		$data = array(
				'content' => $content,
				'trainer_id' => $trainerid,
				'member_id' => session('uid'),
				'star' => $star,
				'time' => time(),
				'type' => 1
		);
		$res = M('Comment')->data($data)->add();
		if($res){
			M('Trainer')->where('trainer_id=%d',$trainerid)->setInc('comment',1);
			echo json_encode(array('status'=>1,'msg'=>'评价成功'));
			exit();
		}else{
			echo json_encode(array('status'=>0,'msg'=>'评价失败'));
			exit();
		}
	}
	
	/**
	* 优惠券
	* @date: 2016-4-25
	* @author: hupeng
	*/
	public function coupon(){
		$uid = session('uid');
		$now = time();
		$coupon_ids = M('Members')->where('id=%d',$uid)->getField('coupon_ids');
		$coupons = M('Coupon')->where("coupon_id in ({$coupon_ids})")->select();
		foreach ($coupons as $k=>$v){
			if($v['type'] ==1 && $v['end_time'] < $now){
				unset($coupons[$k]);
			}
		}
		$this->assign('coupon',$coupons);
		$this->display('my_coupon');
	}

}

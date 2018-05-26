<?php

namespace Trainer\Controller;
use Common\Controller\AdminbaseController;
/**
* 教练管理
* @date: 2016-4-10
* @author: hupeng
*/
class TrainerController extends AdminbaseController {
	function _initialize() {
		parent::_initialize();
	}
	
	/**
	* 城市列表
	* @date: 2016-4-9
	* @author: hupeng
	* @return: array
	*/
	function index() {
		$count = M('Trainer')->count();
		$page = $this->page($count, 10);
		$list = M('Trainer')->limit($page->firstRow . ',' . $page->listRows)->order('create_time desc')->select();
		foreach ($list as $k=>$v){
			$list[$k]['city_name'] = M('MerchantCity')
								->where('mc_id=%d',$v['mc_id'])
								->getField('city_name');
		}
		$this->assign("page", $page->show());
		$this->assign('data',$list);
		$this->display('index');
	}
	
	/**
	* 添加
	* @date: 2016-4-9
	* @author: hupeng
	* @return: json
	*/
	function add() {
		if (IS_POST) {
			$info = I('post.info','');
			$plays = I('post.play','');
			$verify = new \Org\Util\Verify();
			if(empty($info['avatar'])){
				$this->error('头像必须上传');
			}
			if(!$verify::length($info['trainer_name'],3,1,10)){
				$this->error('姓名长度必须1到10');
			}
			if(empty($info['mc_id'])){
				$this->error('城市必须选择');
			}
			if(empty($info['service_json'])){
				$this->error('服务区域与时间至少必须选择一个');
			}
			if(empty($plays)){
				$this->error('打法必须至少选择一个');
			}
			if(!$verify::isMobile($info['phone'])){
				$this->error('手机格式不对');
			}
			if(!is_numeric($info['original_price'])){
				$this->error('原价格式不对');
			}
			if(!is_numeric($info['price'])){
				$this->error('促销价格式不对');
			}
			if(empty($info['introduction'])){
				$this->error('简介不能为空');
			}
			$is_phone = M('Trainer')->where('phone='.$info['phone'])->count();
			if($is_phone > 0){
				$this->error('此手机已经存在!');
			}
			$info['create_time'] = time();
			$info['play_ids'] = implode(',', $plays);
			$info['pwd'] = empty($info['pwd']) ? md5('111111') : md5($info['pwd']);
			$info['service_json'] = htmlspecialchars_decode($info['service_json']);
			$res = M('Trainer')->data($info)->add();
			if($res){
				$this->success('添加成功',U('index'));
			}else{
				$this->error('添加失败');
			}
		} else {
			//打法
			$playlist = M('Play')->select();
			$this->assign('play',$playlist);
			//城市
			$citylist = M('MerchantCity')->where('is_open=1')->select();
			$this->assign('city',$citylist);
			
			//区域默认第一个城市的
			$arealist = M('Area')->where('mc_id=%d',$citylist[0]['mc_id'])->select();
			$this->assign('area',$arealist);
			
			$this->display();
		}
	}
	
	/**
	* 重置密码
	* @date: 2016-4-10
	* @author: hupeng
	*/
	public function resetpwd(){
		$id = I("get.id",'');
		if(empty($id)){
			$this->error('参数有误');
		}
		$res = M('Trainer')->where('trainer_id=%d',$id)->save(array('pwd'=>md5('111111')));
		
		if ($res !== false) {
			$this->success("密码已经重置六个1！");
		} else {
			$this->error("操作失败！");
		}
	}
	
	/**
	* ajax获取区域
	* @date: 2016-4-10
	* @author: hupeng
	* @return: 
	*/
	public function getArea(){
		$mc_id = I('post.id','');
		if(empty($mc_id)){
			$this->error('参数有误');
		}
		$arealist = M('Area')->where('mc_id=%d',$mc_id)->select();
		if(empty($arealist)){
			$this->ajaxReturn(array('status'=>0,'info'=>'此城市下没有区域，请添加'));
		}else{
			$this->ajaxReturn(array('status'=>1,'data'=>$arealist));
		}
		
	}
	
	/**
	* 编辑
	* @date: 2016-4-9
	* @author: hupeng
	* @return: json
	*/
	function edit() {
		if (IS_POST) {
			$info = I('post.info','');
			$plays = I('post.play','');
			$id = I('post.id','');
			$verify = new \Org\Util\Verify();
			if(empty($id)){
				$this->error('参数有误');
			}
			if(empty($info['avatar'])){
				$this->error('头像必须上传');
			}
			if(!$verify::length($info['trainer_name'],3,1,10)){
				$this->error('姓名长度必须1到10');
			}
			if(empty($info['mc_id'])){
				$this->error('城市必须选择');
			}
			if(empty($info['service_json'])){
				$this->error('服务区域与时间至少必须选择一个');
			}
			if(empty($plays)){
				$this->error('打法必须至少选择一个');
			}
			if(!$verify::isMobile($info['phone'])){
				$this->error('手机格式不对');
			}
			if(!is_numeric($info['original_price'])){
				$this->error('原价格式不对');
			}
			if(!is_numeric($info['price'])){
				$this->error('促销价格式不对');
			}
			if(empty($info['introduction'])){
				$this->error('简介不能为空');
			}
			$info['play_ids'] = implode(',', $plays);
			//$info['pwd'] = empty($info['pwd']) ? md5('111111') : md5($info['pwd']);
			$info['service_json'] = htmlspecialchars_decode($info['service_json']);
			$res = M('Trainer')->where('trainer_id=%d',$id)->save($info);
			//echo M()->_sql();exit();
			if($res !== false){
				$this->success('操作成功',U('index'));
			}else{
				$this->error('操作失败');
			}
		} else {
			//打法
			$playlist = M('Play')->select();
			$this->assign('play',$playlist);
			//城市
			$citylist = M('MerchantCity')->where('is_open=1')->select();
			$this->assign('city',$citylist);
				
			//区域默认第一个城市的
			$arealist = M('Area')->where('mc_id=%d',$citylist[0]['mc_id'])->select();
			$this->assign('area',$arealist);
			
			$id = I('get.id','');
			if(empty($id)){
				$this->error('参数有误');
			}
			$info = M('Trainer')->where('trainer_id=%d',$id)->find();
			$this->assign('info',$info);
			//dump($info['service_json']);
			//dump(json_decode($info['service_json']));
			$service_json = $this->objectToArray(json_decode($info['service_json']));
			//dump($service_json);
			$this->assign('service_json',$service_json);
				
			$this->display();
		}
	}
	
	/**
	* 对象转换数组
	* @date: 2016-4-10
	* @author: hupeng
	* @param: object $e 对象
	* @return: array
	*/
	private function objectToArray($e){
		$e=(array)$e;
		foreach($e as $k=>$v){
			if( gettype($v)=='resource' ) return;
			if( gettype($v)=='object' || gettype($v)=='array' )
				$e[$k]=(array)objectToArray($v);
		}
		return $e;
	}

	/**
	 *  删除
	 */
	function delete() {
		$id = I("get.id",'');
		if(empty($id)){
			$this->error('参数有误');
		}
		$res = M('Trainer')->where('trainer_id=%d',$id)->delete();
		if ($res !== false) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}
	
	

}

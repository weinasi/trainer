<?php

namespace Trainer\Controller;
use Common\Controller\AdminbaseController;
/**
* 活动管理
* @date: 2016-5-24
* @author: hupeng
*/
class ActivityController extends AdminbaseController {
	function _initialize() {
		parent::_initialize();
	}
	
	/**
	* 城市列表
	* @date: 2016-5-24
	* @author: hupeng
	* @return: array
	*/
	function index() {
		$count = M('Activity')->count();
		$page = $this->page($count, 10);
		$list = M('Activity')->limit($page->firstRow . ',' . $page->listRows)->order('create_time desc')->select();
		
		$this->assign("page", $page->show());
		$this->assign('data',$list);
		$this->display('index');
	}
	
	/**
	* 添加
	* @date: 2016-5-24
	* @author: hupeng
	* @return: json
	*/
	function add() {
		if (IS_POST) {
			$info = I('post.info','');
			$verify = new \Org\Util\Verify();
			if(empty($info['imgurl'])){
				$this->error('缩略图必须上传');
			}
			if(!$verify::length($info['activity_name'],3,1,50)){
				$this->error('名称长度必须1到50');
			}
			if(empty($info['type'])){
				$this->error('类型必须选择');
			}
			if(!is_numeric($info['price'])){
				$this->error('价格式不对');
			}
			if(!$verify::length($info['place'],3,1,50)){
				$this->error('地点长度必须1到50');
			}
			if(!is_numeric($info['num'])){
				$this->error('人数格式不对');
			}
			if(empty($info['start_time'])){
				$this->error('请选择开始时间');
			}
			if(empty($info['end_time'])){
				$this->error('请选择结束时间');
			}
			if(empty($info['spec'])){
				$this->error('简介不能为空');
			}
			
			$info['create_time'] = time();
			$info['start_time'] = strtotime($info['start_time']);
			$info['end_time'] = strtotime($info['end_time']);
			$res = M('Activity')->data($info)->add();
			if($res){
				$this->success('添加成功',U('index'));
			}else{
				$this->error('添加失败');
			}
		} else {
			
			$this->display();
		}
	}
	

	
	/**
	* 编辑
	* @date: 2016-5-24
	* @author: hupeng
	* @return: json
	*/
	function edit() {
		if (IS_POST) {
			$info = I('post.info','');
			$id = I('post.id','');
			$verify = new \Org\Util\Verify();
		    if(empty($info['imgurl'])){
				$this->error('缩略图必须上传');
			}
			if(!$verify::length($info['activity_name'],3,1,50)){
				$this->error('名称长度必须1到50');
			}
			if(empty($info['type'])){
				$this->error('类型必须选择');
			}
			if(!is_numeric($info['price'])){
				$this->error('价格式不对');
			}
			if(!$verify::length($info['place'],3,1,50)){
				$this->error('地点长度必须1到50');
			}
			if(!is_numeric($info['num'])){
				$this->error('人数格式不对');
			}
			if(empty($info['start_time'])){
				$this->error('请选择开始时间');
			}
			if(empty($info['end_time'])){
				$this->error('请选择结束时间');
			}
			if(empty($info['spec'])){
				$this->error('简介不能为空');
			}
			
			$info['start_time'] = strtotime($info['start_time']);
			$info['end_time'] = strtotime($info['end_time']);
			$res = M('Activity')->where('activity_id=%d',$id)->save($info);
			if($res !== false){
				$this->success('操作成功',U('index'));
			}else{
				$this->error('操作失败');
			}
		} else {		
			$id = I('get.id','');
			if(empty($id)){
				$this->error('参数有误');
			}
			$info = M('Activity')->where('activity_id=%d',$id)->find();
			$this->assign('info',$info);
				
			$this->display();
		}
	}
	

	/**
	 *  删除
	 */
	function delete() {
		$id = I("get.id",'');
		if(empty($id)){
			$this->error('参数有误');
		}
		$res = M('Activity')->where('activity_id=%d',$id)->delete();
		if ($res !== false) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}
	
	

}

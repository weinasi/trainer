<?php

namespace Trainer\Controller;
use Common\Controller\AdminbaseController;
/**
* 打法管理
* @date: 2016-4-9
* @author: hupeng
*/
class PlayController extends AdminbaseController {
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
		$count = M('Play')->count();
		$page = $this->page($count, 10);
		$list = M('Play')->limit($page->firstRow . ',' . $page->listRows)->select();
		
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
			$verify = new \Org\Util\Verify();
			
			if(!$verify::length($info['play_name'],3,1,10)){
				$this->error('名称长度必须1到10');
			}
			
			$info['create_time'] = time();
			$res = M('Play')->data($info)->add();
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
	* @date: 2016-4-9
	* @author: hupeng
	* @return: json
	*/
	function edit() {
		if (IS_POST) {
			$info = I('post.info','');
			$id = I('post.id','');
			$verify = new \Org\Util\Verify();
			if(!$verify::length($info['play_name'],3,1,10)){
				$this->error('名称长度必须1到10');
			}
			$res = M('Play')->where('play_id=%d',$id)->save($info);
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
			$info = M('Play')->where('play_id=%d',$id)->find();
			
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
		$res = M('Play')->where('play_id=%d',$id)->delete();
		if ($res !== false) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}
	
	

}

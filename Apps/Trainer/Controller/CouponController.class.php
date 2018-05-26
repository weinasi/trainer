<?php

namespace Trainer\Controller;
use Common\Controller\AdminbaseController;
/**
* 优惠券管理
* @date: 2016-4-28
* @author: hupeng
*/
class CouponController extends AdminbaseController {
	function _initialize() {
		parent::_initialize();
	}
	
	/**
	* 优惠券列表
	* @author: hupeng
	* @return: array
	*/
	function index() {
		$count = M('Coupon')->count();
		$page = $this->page($count, 10);
		$list = M('Coupon')->limit($page->firstRow . ',' . $page->listRows)->select();
		
		$this->assign("page", $page->show());
		$this->assign('data',$list);
		$this->display('index');
	}
	
	/**
	* 添加
	* @author: hupeng
	* @return: json
	*/
	function add() {
		if (IS_POST) {
			$info = I('post.info','');
			if(empty($info['coupon_name'])){
				$this->error('名称必须填写');
			}
			if(empty($info['price'])){
				$this->error('面值必填');
			}
			$info['end_time'] = strtotime($info['end_time']);
			$info['type'] = 1;
			$res = M('Coupon')->add($info);
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
	* @author: hupeng
	* @return: json
	*/
	function edit() {
		if (IS_POST) {
			$info = I('post.info','');
			$id = I('post.id','');
			if(empty($id)){
				$this->error('参数错误');
			}
			if(empty($info['coupon_name'])){
				$this->error('名称必须填写');
			}
			if(empty($info['price'])){
				$this->error('面值必填');
			}
			if(!empty($info['end_time'])){
				$info['end_time'] = strtotime($info['end_time']);
			}
			
			$res = M('Coupon')->where('coupon_id=%d',$id)->save($info);
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
			$info = M('Coupon')->where('coupon_id=%d',$id)->find();;
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
		
		$res = M('Coupon')->where('coupon_id=%d',$id)->delete();
		if ($res !== false) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}
	
	

}

<?php

namespace Trainer\Controller;
use Common\Controller\AdminbaseController;
/**
* 订单管理
* @date: 2016-4-27
* @author: hupeng
*/
class OrderController extends AdminbaseController {
	function _initialize() {
		parent::_initialize();
	}
	
	/**
	* 订单列表
	* @author: hupeng
	* @return: array
	*/
	function index() {
		$count = M('Order')->count();
		$page = $this->page($count, 10);
		$list = M('Order')->limit($page->firstRow . ',' . $page->listRows)->order('add_time desc')->select();
		foreach ($list as $k=>$v){
			$list[$k]['time_text'] = $this->textTotimes($list[$k]['time_text']);
			$list[$k]['trainer_name'] = M('Trainer')
										->where('trainer_id=%d',$v['trainer_id'])
										->getField('trainer_name');
			$list[$k]['user_name'] = M('Members')
										->where('id=%d',$v['member_id'])
										->getField('user_nickname');
		}
		$this->assign("page", $page->show());
		$this->assign('data',$list);
		$this->display('index');
	}
	
	/**
	* 订单状态操作
	* @date: 2016-4-27
	* @author: hupeng
	*/
	public function status(){
		$id = I('get.id','');
		$status = I('get.status','');
		if(empty($id) || empty($status)){
			$this->error('参数有误');
		}
		$res = M('Order')->where('order_id=%d',$id)->setField('status',$status);
		if($res !== false){
			$this->success('操作成功');
		}else{
			$this->error('操作失败');
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

		$res = M('Order')->where('order_id=%d',$id)->delete();
		if ($res !== false) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}
	

}

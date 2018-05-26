<?php

namespace Trainer\Controller;
use Common\Controller\AdminbaseController;
/**
* 订单管理
* @date: 2016-4-27
* @author: hupeng
*/
class ActOrderController extends AdminbaseController {
	function _initialize() {
		parent::_initialize();
	}
	
	/**
	* 订单列表
	* @author: hupeng
	* @return: array
	*/
	function index() {
		$count = M('ActOrder')->count();
		$page = $this->page($count, 10);
		$list = M('ActOrder')->limit($page->firstRow . ',' . $page->listRows)->order('add_time desc')->select();
		foreach ($list as $k=>$v){
			$list[$k]['activity_name'] = M('Activity')
										->where('activity_id=%d',$v['activity_id'])
										->getField('activity_name');
			$list[$k]['place'] = M('Activity')
						->where('activity_id=%d',$v['activity_id'])
						->getField('place');
			$list[$k]['user_name'] = M('Members')
										->where('id=%d',$v['member_id'])
										->getField('user_nickname');
		}
		//dump($list);
		$this->assign("page", $page->show());
		$this->assign('data',$list);
		$this->display('index');
	}
	

	

	/**
	 *  删除
	 */
	function delete() {
		$id = I("get.id",'');
		if(empty($id)){
			$this->error('参数有误');
		}

		$res = M('ActOrder')->where('id=%d',$id)->delete();
		if ($res !== false) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}
	

}

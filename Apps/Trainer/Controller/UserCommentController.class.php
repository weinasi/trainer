<?php

namespace Trainer\Controller;
use Common\Controller\AdminbaseController;
/**
* 用户评论管理
* @date: 2016-4-9
* @author: hupeng
*/
class UserCommentController extends AdminbaseController {
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
		$count = M('Comment')->where('type=1')->count();
		$page = $this->page($count, 10);
		$list = M('Comment')->where('type=1')->limit($page->firstRow . ',' . $page->listRows)->select();
		foreach ($list as $k=>$v){
			$list[$k]['username'] = M('Members')->where('id=%d',$v['member_id'])->getField('user_nickname');
		}
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
		$res = M('Comment')->where('comment_id=%d',$id)->delete();
		if ($res !== false) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}
	
	

}

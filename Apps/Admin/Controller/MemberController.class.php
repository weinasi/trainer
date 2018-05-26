<?php

/**
 * 会员注册登录
 */

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class MemberController extends AdminbaseController {

	function index() {
		$count = M("Members")->count();
		$page = $this->page($count, 10);
		$lists = M("Members")->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign("page", $page->show());
		$this->assign('lists', $lists);
		$this->display();
	}

	function delete() {
		$id = intval($_GET['id']);
		if ($id) {
			$rst = M("Members")->where("id=$id")->delete();
			if ($rst !== false) {
				$this->success("删除成功！", U("admin/member/index"));
			} else {
				$this->error('会员删除失败！');
			}
		} else {
			$this->error('数据传入失败！');
		}
	}

}

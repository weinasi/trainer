<?php

namespace Trainer\Controller;
use Common\Controller\AdminbaseController;
/**
* 企业服务管理
* @date: 2016-4-28
* @author: hupeng
*/
class CompanyController extends AdminbaseController {
	function _initialize() {
		parent::_initialize();
	}
	
	/**
	* 列表
	* @author: hupeng
	* @return: array
	*/
	function index() {
		$count = M('Company')->count();
		$page = $this->page($count, 10);
		$list = M('Company')->limit($page->firstRow . ',' . $page->listRows)->select();
		
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
		
		$res = M('Company')->where('company_id=%d',$id)->delete();
		if ($res !== false) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}
	
	

}

<?php
/**
* 公共
* @date: 2016-5-4
* @author: hupeng
*/
namespace Manage\Controller;
use Common\Controller\HomeBaseController;
class CommonController extends HomeBaseController {
	
	function _initialize() {
		parent::_initialize();
		$uid = session('t_uid');
	    if(empty($uid)){
	    	redirect(U('Login/login'));
	    }
	}
	

}

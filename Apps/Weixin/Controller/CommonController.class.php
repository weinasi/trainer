<?php
/**
* 公共
* @date: 2016-4-11
* @author: hupeng
*/
namespace Weixin\Controller;
use Common\Controller\HomeBaseController;
class CommonController extends HomeBaseController {
	
	function _initialize() {
		parent::_initialize();
		$uid = session('uid');
		//session('uid',$uid);
	    if(empty($uid)){
	    	if(MODULE_NAME == 'Activity'){
	    		cookie('preurl',U('Activity/Index/index'));
	    	}else{
	    		cookie('preurl',$_SERVER['HTTP_REFERER']);
	    	}
	        
	    	redirect(U('Weixin/Login/login'));
	    }
	}
	

}


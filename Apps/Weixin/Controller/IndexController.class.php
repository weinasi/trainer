<?php
/**
* 首页城市
* @date: 2016-4-11
* @author: hupeng
*/
namespace Weixin\Controller;
use Common\Controller\HomeBaseController;
class IndexController extends HomeBaseController {
	
	public function index() {
		$citylist = M('MerchantCity')->order('is_open desc')->select();
		$this->assign('city',$citylist);
		$this->display();
	}

	

}

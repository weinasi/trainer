<?php
/**
* 企业服务
* @author: hupeng
*/
namespace Weixin\Controller;
use Common\Controller\HomeBaseController;
class CompanyController extends HomeBaseController {
	
	/**
	* 首页
	* @author: hupeng
	*/
	public function index() {
		$this->display('index');
			
	}
	
	/**
	* 提交表单
	* @date: 2016-4-28
	* @author: hupeng
	*/
	public function form() {
		if(IS_POST){
			$data = array(
					'name' => I('post.company'),
					'city' => I('post.city'),
					'address' => I('post.address'),
					'contacts' => I('post.username'),
					'phone' => I('post.phone'),
					'type' => I('post.type'),
					'remark' => I('post.remark'),
					'create_time' => time()
			);
			$res = M('Company')->data($data)->add();
			if($res){
				echo json_encode(array('status'=>1,'msg'=>'提交成功'));
				exit();
			}else{
				echo json_encode(array('status'=>0,'msg'=>'提交失败'));
				exit();
			}
		}else{
			$this->display('form');
		}		
	}
	
	

}

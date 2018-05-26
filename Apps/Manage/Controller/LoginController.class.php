<?php
/**
* 登陆
* @author: hupeng
*/
namespace Manage\Controller;
use Common\Controller\HomeBaseController;
class LoginController extends HomeBaseController {
	
	/**
	* 登陆
	* @author: hupeng
	*/
	public function login() {
		if(IS_POST){
			$phone = I('post.phone','');
			$password = I('post.password','');
			$verify = new \Org\Util\Verify();
			if(!$verify::isMobile($phone)){
				echo json_encode(array('code'=>0,'msg'=>'手机号格式错误'));
				exit();
			}
			if(empty($password)){
				echo json_encode(array('code'=>0,'msg'=>'密码不能为空'));
				exit();
			}
			
			$res = M('Trainer')->where("phone='%s' and pwd='%s'",$phone,md5($password))->find();
			//echo M()->_sql();exit();
			if($res){
				session('t_uid',$res['trainer_id']);
				echo json_encode(array('code'=>1));
				exit();
			}else{
				echo json_encode(array('code'=>0,'msg'=>'手机或密码错误'));
				exit();
			}
			
		}else{
			
			$this->display('login');
		}
			
	}

	/**
	* 退出登录
	* @date: 2016-4-24
	* @author: hupeng
	*/
	public function loginOut(){
		session('uid',null);
		redirect(U('/Manage/Login/login'));
	}
	

}

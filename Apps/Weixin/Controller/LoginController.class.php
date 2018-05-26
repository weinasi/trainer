<?php
/**
* 登陆
* @author: hupeng
*/
namespace Weixin\Controller;
use Common\Controller\HomeBaseController;
class LoginController extends HomeBaseController {
	
	/**
	* 登陆
	* @author: hupeng
	*/
	public function login() {
		if(IS_POST){
			$phone = I('post.phone','');
			$captcha = I('post.captcha','');
			$preurl = I('post.preurl','');
			$verify = new \Org\Util\Verify();
			if(!$verify::isMobile($phone)){
				echo json_encode(array('code'=>0,'msg'=>'手机号格式错误'));
				exit();
			}
			if(empty($captcha)){
				echo json_encode(array('code'=>0,'msg'=>'验证码不能为空'));
				exit();
			}
			if (!check_verify($captcha)) {
				echo json_encode(array('code'=>0,'msg'=>'验证码错误'));
				exit();
			}else{
				$last_login_ip = get_client_ip();
				$last_login_time = time();
				
				$count = M('Members')->where('mobile=\'%s\'',$phone)->count();
				//echo M()->_sql();
				if($count == 0){
					$userdata = array(
							'last_login_ip' => $last_login_ip,
							'last_login_time' => $last_login_time,
							'mobile' => $phone,
							'create_time' => time(),
							'coupon_ids' => 1,
							'user_nickname' => '新注册用户'
							//'preurl' => $preurl
					);
					$res = M('Members')->data($userdata)->add();
					if($res){
						//session('uid',$res);
						//S($res.'preurl',$_SERVER['HTTP_REFERER']);
						//dump(S($res.'preurl'));
						echo json_encode(array('code'=>1));
						exit();
					}
				}else{
					$userdata = array(
							'last_login_ip' => $last_login_ip,
							'last_login_time' => $last_login_time,
							//'preurl' => $preurl
					);
					$res1 = M('Members')->where('mobile=\'%s\'',$phone)->save($userdata);
					if($res1 !== false){
						//$uid = M('Members')->where('id=%d',$phone)->getField('id');
						echo json_encode(array('code'=>1));
						exit();
							
					}
				}
				
				
			}
			
		}else{
			//$this->assign('preurl',$_SERVER['HTTP_REFERER']);
			//dump(cookie('preurl'));
			//dump(session('uid'));
			$this->display('person_login');
		}
			
	}
	
	/**
	* 云片发送短信
	* @date: 2016-4-20
	* type参数值 verify：手机验证 register：注册成功 modify：修改手机号 reset：找回密码
	* @author: hupeng
	*/
	public function sendSms() {
		$mobile = I('get.mobile','');
		$type = 'verify';
		$verify = new \Org\Util\Verify();
		if (empty($mobile)) {
			$this->error('手机号为空');
		}
		if (!$verify::isMobile($mobile)) {
			$this->error('手机号格式不对');
		}
		//$test=true 测试环境 false 生成环境
		$test = true;
		if($test){
			$this->assign('testcode','123456');
		}else{
			$mcode = rand(100000, 999999);
			$expire = 1800;
			$res = S($type . '_' . $mobile, $mcode, $expire);
			//dump(S($type . '_' . $mobile));exit();
			if ($res) {
				$content = $this->gettpl($type, $mcode);
				if ($content) {
					$res = $this->send($mobile, $content);
					if ($res) {
							
					} else {
						$this->error('发送失败');
					}
				}
			
			} else {
				$this->error('系统错误，请重试');
			}
		}
		

		$this->assign('phone',$mobile);
		$this->display('person_sms');

		
	}
	
	/**
	* 短信模板
	* @date: 2016-4-20
	* @author: hupeng
	*/
	private function gettpl($type='verify', $code) {
		if ($type == 'verify') {
			$tpl = '【意象网络工作室】您的验证码是' . $code;  //此后模板要跟你云片设置的模板一样
			return $tpl;
		} 
		return false;
	}
	
	/**
	* 云片发送
	* @date: 2016-4-20
	* @author: hupeng
	*/
	private function send($mobile, $info) {
		$curl = new \Common\Lib\Util\Curl();
		$url = 'http://yunpian.com/v1/sms/send.json';
		
		$post['apikey'] = '006781e506ac1c112e0ab0eb6c36b521';  //云片申请的key
		$post['mobile'] = $mobile;
		$post['text'] = $info;
		$post1 = http_build_query($post);
		 //wlog('sms.log',$post1);
		$content = $curl->post($url,$post1);
		if ($content) {
			$content = json_decode($content, true);
			if ($content['code'] == 0) {
				return true;
			}else{
			    wlog('sms.log',$content['msg']);
			    return false;
			}
		}else{
		    wlog('sms.log',$content['msg']);
		    return false;
		}
		
		
	}
	
	/**
	 * 验证短信验证码
	 * @return json
	 */
	public function verifySms() {
		$uid = session('uid');
		$mobile = I('post.mobile');
		$code = I('post.code');
		$type = 'verify';
		$scode = S($type. '_' . $mobile);
		
		$test =true;
		if($test){
			$user = M('Members')->where('mobile=\'%s\'',$mobile)->find();
			//dump($user);
			session('uid',$user['id']);
			$url = cookie('preurl');
			echo json_encode(array('code'=>200,'status'=>1,'info'=>'验证成功','url'=>$url));
			exit();
		}else{
			
			if ($scode == $code) {
				S($type. '_' . $mobile, null);
				$user = M('Members')->where('mobile=\'%s\'',$mobile)->find();
				session('uid',$user['id']);
				$url = cookie('preurl');
				echo json_encode(array('code'=>200,'status'=>1,'info'=>'验证成功','url'=>$url));
				exit();
			} else {
				echo json_encode(array('code'=>505,'status'=>0,'info'=>'验证失败'));
				exit();
			}
		}
		
	}
	
	/**
	* 退出登录
	* @date: 2016-4-24
	* @author: hupeng
	*/
	public function loginOut(){
		session('uid',null);
		$this->redirect(U('Login/login'));
	}
	

}


<?php
/**
* 微信控制器
* @date: 2016-5-9
* @author: hupeng
*/
namespace Weixin\Controller;
use Common\Controller\HomeBaseController;
use Common\Lib\Com\Wechat;
use Common\Lib\Com\WechatAuth;
class WeixinController extends HomeBaseController {
	/**
	 * 微信消息接口入口
	 * 所有发送到微信的消息都会推送到该操作
	 * 所以，微信公众平台后台填写的api地址则为该操作的访问地址
	 */
    public function index(){
    			
        /* 加载微信SDK */
        $wechat = new Wechat();

        /* 获取请求信息 */
        $data = $wechat->request();

        $wechatauth = new WechatAuth();
        //个人预约
        $url1 = 'http://www.yixiangit.com/trainer/index.php';
        //企业服务
        $url2 = 'http://www.yixiangit.com/trainer/index.php?m=Weixin&c=Company';
        //教练入口
        $url3 = 'http://www.yixiangit.com/trainer/index.php?m=Manage&c=Login&a=login';
        //活动
        $url4 = 'http://www.yixiangit.com/trainer/index.php?m=Activity&c=Index&a=index';
                
        $button = array(
        				array('name'=>'约教练',
	       				'sub_button'=>array(
	       							  array('type'=>'view','name'=>'个人预约','url'=>$url1),
	       							  array('type'=>'view','name'=>'企业服务','url'=>$url2),
	       							),
        						), 
		        		array('type'=>'view','name'=>'约活动','url'=>$url4),
        				array('type'=>'view','name'=>'教练入口','url'=>$url3),
// 	       				array('name'=>'更多',
// 	       				'sub_button'=>array(
// 	       							  array('type'=>'click','name'=>'教练招募','key'=>'SUB_MENU_ZHAOMU'),
// 	       							  array('type'=>'click','name'=>'关于我们','key'=>'SUB_MENU_ABOUT'),
// 	       							),
//         						),
        		);
        
        //$wechatauth->menuDelete();
       // $wechatauth->menuCreate($button);  
        
        if($data && is_array($data)){
        	        	
        	//事件
        	if ($data['MsgType'] == Wechat::MSG_TYPE_EVENT) {
        		
        		switch ($data['Event']) {
        			
        			//关注
        			case Wechat::MSG_EVENT_SUBSCRIBE:
        				$wechat->replyText("欢迎来到教练在线预约平台！");
        				break;
        		
        			//取消关注
        			case Wechat::MSG_EVENT_UNSUBSCRIBE:
					
        				break;
        				
        			case Wechat::MSG_EVENT_CLICK:
        					
        					switch ($data['EventKey']){
        						
        						case 'SUB_MENU_CAIPIN':
        								
        							$wechat->replyNewsOnce('title', 'jianjie', 'xiagnqingURL', 'tupianURL');
        							
        							break;
 
        						case 'SUB_MENU_ABOUT':
        						
        							$wechat->replyNewsOnce('title', 'jianjie', 'xiagnqingURL', 'tupianURL');
        							
        							break;
        						
        						
        						case 'SUB_MENU_ZHAOMU':
        									
        							$wechat->replyNewsOnce('title', 'jianjie', 'xiagnqingURL', 'tupianURL');
        							
        									
        							break;
        							
        						default: 
        							break;
        					}
        						
        				break;
        				
        			case Wechat::MSG_EVENT_SCAN:
        				break;
        			
        			default:
        				break;
        		}
        		
        	}

        }
    }
    
	
	

}

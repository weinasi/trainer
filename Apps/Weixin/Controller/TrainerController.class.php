<?php
/**
* 教练
* @date: 2016-4-11
* @author: hupeng
*/
namespace Weixin\Controller;
use Common\Controller\HomeBaseController;
class TrainerController extends HomeBaseController {
	
	/**
	* 教练列表
	* @date: 2016-4-11
	* @author: hupeng
	*/
	public function index() {
		if(IS_POST){
			$mc_id = I('post.mc_id','');
			$startpage = I('post.startpage','');
			$numpage = I('post.numpage','');
			$area = I('post.area','');
			$play = I('post.play','');
			$sex = I('post.sex','');
			$mytime = I('post.mytime','');
			$myprice = I('post.myprice','');
			if(empty($mc_id)){
				$mc_id = M('MerchantCity ')->where('is_open=1')->getField('mc_id');
			}
			$_map = "mc_id={$mc_id}";
			if(!empty($area)){
				$_map .= " and service_json like '%{$area}%'";
			}
			if(!empty($play)){
				$_map .= " and play_ids like '%{$play}%'";
			}
			if(!empty($sex)){
				$_map .= " and sex={$sex}";
			}
			if(!empty($mytime)){
				$_map .= " and service_json like '%{$mytime}%'";
			}
			if(!empty($myprice)){
				switch ($myprice){
					case 1:
						 $_map .= " and price > 200";
						 break;
					case 2:
						 $_map .= " and price >= 100 and price <= 200";
						 break;
				    case 3:
				 	     $_map .= " and price < 100";
				 	     break;
				}
				
			}
			$list = M('Trainer')
				->where($_map)
				->limit($startpage,$numpage)
				->order('create_time desc')
				->select();
			//echo M()->_sql();exit();
			foreach ($list as $k=>$v){
				$list[$k]['service_json'] = json_decode($v['service_json']);
				$list[$k]['original_price'] = (int)$v['original_price'];
				$list[$k]['price'] = (int)$v['price'];
				$list[$k]['total_time'] = (int)$v['total_time'];
				$list[$k]['teaching_type'] = $this->_techtype($v['teaching_type']);
				$list[$k]['level'] = $this->_level($v['level']);
				$list[$k]['play_ids'] = $this->_play($v['play_ids']);
				$list[$k]['star'] = getStar($v['trainer_id']);
			}
			if($list){
				$this->ajaxReturn(array('data'=>$list,'status'=>1));
			}else{
				$this->ajaxReturn(array('data'=>array(),'status'=>0));
			}
			
		}else{
			$mc_id = I('get.mc_id');
			if(empty($mc_id)){
 				$mc_id = M('MerchantCity ')->where('is_open=1')->getField('mc_id');
			}
            //此城市区域
            $arealist = M('Area')->where('mc_id=%d',$mc_id)->select();
            $this->assign('area',$arealist);
            //打法
            $playlist = M('Play')->select();
            $this->assign('play',$playlist);
			$this->assign('mc_id',$mc_id);
			$this->display('trainer_list');
		}
		
	}
	
	/**
	* 教练详情
	* @date: 2016-4-18
	* @author: hupeng
	*/
	public function detail(){
		$id = I('get.id','');
		if(empty($id) || $id < 0){
			$this->error('参数非法');
		}
		$trainer_info = M('Trainer')
				->where('trainer_id=%d',$id)->find();
		$trainer_info['teaching_type'] = $this->_techtype($trainer_info['teaching_type']);
		$trainer_info['play_ids'] = $this->_play($trainer_info['play_ids']);
		$trainer_info['level'] = $this->_level($trainer_info['level']);
		$trainer_info['introduction'] = explode('|', $trainer_info['introduction']);
		$trainer_info['service_json'] = $this->objectToArray(json_decode($trainer_info['service_json']));
		$trainer_info['price'] = $trainer_info['is_promotion'] == 1 ? $trainer_info['price'] : $trainer_info['original_price'];
		//评论
		$list = M()
			->table('__COMMENT__ as c')
			->join('left join __MEMBERS__ as m on c.member_id=m.id')
			->field('c.content,c.star,c.time,m.user_nickname')
			->where('c.trainer_id=%d and c.type=1',$id)
			->limit(1)
			->select();
		$this->assign('comment',$list);
		$this->assign('weeks',$this->getWeeks());
		$this->assign('times',$this->getTimes($id));
		//dump($this->getWeeks());
		//$this->getTimes($id);
		$this->assign('info',$trainer_info);
		
		$this->display('trainer_detail');
	}
	
	/**
	 * 评论列表
	 * @date: 2016-4-18
	 * @author: hupeng
	 */
	public function comment(){
		$id = I('get.id','');
		if(empty($id) || $id < 0){
			$this->error('参数非法');
		}
		$trainer_info = M('Trainer')
				->where('trainer_id=%d',$id)->find();
		$trainer_info['teaching_type'] = $this->_techtype($trainer_info['teaching_type']);
		$trainer_info['price'] = $trainer_info['is_promotion'] == 1 ? $trainer_info['price'] : $trainer_info['original_price'];
		//评论
		$list = M()
			->table('__COMMENT__ as c')
			->join('left join __MEMBERS__ as m on c.member_id=m.id')
			->field('c.content,c.star,c.time,m.user_nickname')
			->where('c.trainer_id=%d and c.type=1',$id)
			->limit(5)
			->select();
		$this->assign('comment',$list);
		$this->assign('info',$trainer_info);
	
		$this->display('trainer_comment');
	}
	
	/**
	 * 获取评论
	 */
	public function getComment(){
		$id = I('post.id');
		$numpage = I('post.numpage','');
		if(empty($id)){
			$this->ajaxReturn(array('status'=>0,'msg'=>'参数有误'));
		}
		$list = M()
			->table('__COMMENT__ as c')
			->join('left join __MEMBERS__ as m on c.member_id=m.id')
			->field('c.content,c.star,c.time,m.user_nickname')
			->where('c.trainer_id=%d and c.type=1',$id)
			->limit($numpage,5)
			->select();
		//echo M()->_sql();
		//dump($list);
		if($list){
			$this->ajaxReturn(array('data'=>$list,'status'=>1));
		}else{
			$this->ajaxReturn(array('data'=>array(),'status'=>0));
		}
		
	}
	
	/**
	* 获取一周信息
	* @date: 2016-4-18
	* @author: hupeng:
	*/
	public function getWeeks(){
		$weeks = array();
		for($i = 0;$i <= 6;$i++){
			$week = strtotime("+$i day",time());
			$weeks[$i+1]['w'] = date('m.d',$week).'('.$this->numTostr(date("w",$week)).')';
			$weeks[$i+1]['d'] = date('m月d日',$week);
			$weeks[$i+1]['y'] = date('Y-n-d',$week);
		}
		
		return $weeks;
	}
	
	/**
	 * 根据教练订单计算空闲时间
	 * @param int $trainer_id
	 */
	public function getTimes($trainer_id){
		$times = M('Order')->where('trainer_id=%d',$trainer_id)->select();
		$weeks = $this->getWeeks();
		$html = '';
		$html_arr = array();
		foreach ($weeks as $k=>$v){
	        $class = $k >=2 ? 'display:none' : '';
			$html = '<div id="con_one_'.$k.'" style="'.$class.'">';
			$html .= '<ul class="scrollTime cf">';
			for($i = 9;$i <= 20;$i++){
				$this_time = $v['y'].':'.$i;
				$this_time1 = strtotime($v['y'].' '.$i.':00');
				$_map = "status in (1,2) and trainer_id={$trainer_id} and time_text like '%{$this_time}%'";
				$times_count = M('Order')->where($_map)->count();
				if($times_count > 0 || $this_time1 < time()){
					$html .= '<li class="disable">'.$i.':00</li>';
				}else{
					$html .= '<li value="'.$v[y].':'.$i.'">'.$i.':00</li>';
				}
				
			}
			
			$html .= '</ul></div>';
			$html_arr[] = $html;
		}
		return $html_arr;
	}
	
	/**
	* 数值转换字符
	* @date: 2016-4-18
	* @author: hupeng
	* @param: int $num
	* @return: string $str
	*/
	public function numTostr($num){
		switch ($num){
			case 0:
				$str = '周日';break;
			case 1:
				$str = '周一';break;
			case 2:
				$str = '周二';break;
			case 3:
				$str = '周三';break;
			case 4:
				$str = '周四';break;
			case 5:
				$str = '周五';break;
			case 6:
				$str = '周六';break;
		}
		
		return $str;
	}
	
	
	
	

}


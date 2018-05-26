<?php

namespace Common\Controller;

use Common\Controller\AppframeController;

class HomeBaseController extends AppframeController {

	function _initialize() {

		parent::_initialize();
		//var_dump(S('1preurl'));
		//site_options
		$site_options = F("site_options");
		if (empty($site_options)) {
			$site_options = get_site_options();
			F("site_options", $site_options);
			$this->assign($site_options);
		} else {
			$this->assign($site_options);
		}
	}
	
	/**
	 * 对象转换数组
	 * @param unknown $e
	 * @return void|array
	 */
	public function objectToArray($e){
		$e=(array)$e;
		foreach($e as $k=>$v){
			if( gettype($v)=='resource' ) return;
			if( gettype($v)=='object' || gettype($v)=='array' )
				$e[$k]=(array)objectToArray($v);
		}
		return $e;
	}
	
	/**
	 * 获取教学类型
	 * @date: 2016-4-11
	 * @author: hupeng
	 * @param:  int $teaching_type
	 * @return:string
	 */
	public function _techtype($teaching_type){
		switch ($teaching_type){
			case 1: $str = '陪练'; break;
			case 2: $str = '比赛指导';break;
			case 3: $str = '陪练 比赛指导';break;
		}
	
		return $str;
	}
	
	/**
	 * 获取级别
	 * @date: 2016-4-11
	 * @author: hupeng
	 * @param:  int $level
	 * @return:string
	 */
	public function _level($level){
		switch ($level){
			case 1: $str = '国家一级运动员'; break;
			case 2: $str = '国家二级运动员';break;
			case 3: $str = '国家三级运动员';break;
			case 4: $str = '普通运动员';break;
		}
	
		return $str;
	}
	
	/**
	 * 获取打法
	 * @date: 2016-4-11
	 * @author: hupeng
	 * @param:  string $level
	 * @return:string
	 */
	public function _play($play){
		$play_arr = M('Play')->where("play_id in ({$play})")->getField('play_name',true);
		$str = implode(',', $play_arr);
		//dump($play_arr);
		// dump($str);exit();
		return $str;
	}
	
	/**
	 * 时间字符串转换特定格式
	 * @date: 2016-4-21
	 * @author: hupeng
	 * @param:  string $text
	 * @return: string
	 */
	public function textTotimes($text){
		$text_arr = explode(',', $text);
		$newtime = array();
		foreach ($text_arr as $v){
			$text_arr1[] = explode(':', $v);
		}
	
		for($i = -15;$i <= 6;$i++){
			$week = date('Y-n-d',strtotime("+$i day",time()));
			//dump($week);
			foreach ($text_arr1 as $k=>$v){
				if($v[0] == $week){
					$time[$week][] = $v[1];
				}
			}
		}
		
	  // dump($text_arr1);
		//重新组合并计算
		foreach ($time as $k=>$v){
			$newtime[] = $k.' '.$v[0].':00-'.end($v).':59  共'.(count($v)).'小时';
		}
	
		//dump('eeeeeeeeeeeee');exit();
	
		return $newtime;
	
	}
	

}

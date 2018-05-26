<?php

namespace Trainer\Controller;
use Common\Controller\AdminbaseController;
/**
* 城市管理
* @date: 2016-4-9
* @author: hupeng
*/
class CityController extends AdminbaseController {
	function _initialize() {
		parent::_initialize();
	}
	
	/**
	* 城市列表
	* @date: 2016-4-9
	* @author: hupeng
	* @return: array
	*/
	function index() {
		$count = M('MerchantCity')->count();
		$page = $this->page($count, 10);
		$list = M('MerchantCity')->limit($page->firstRow . ',' . $page->listRows)->select();
		
		$this->assign("page", $page->show());
		$this->assign('data',$list);
		$this->display('index');
	}
	
	/**
	* 添加
	* @date: 2016-4-9
	* @author: hupeng
	* @return: json
	*/
	function add() {
		if (IS_POST) {
			$info = I('post.info','');
			$verify = new \Org\Util\Verify();
			if(empty($info['img_url'])){
				$this->error('封面必须上传');
			}
			if(!$verify::length($info['city_name'],3,1,10)){
				$this->error('名称长度必须1到10');
			}
			if(!$verify::length($info['remark'],3,1,100)){
				$this->error('说明长度必须1到100之间');
			}
			$info['create_time'] = time();
			$res = M('MerchantCity')->add($info);
			if($res){
				$this->success('添加成功',U('index'));
			}else{
				$this->error('添加失败');
			}
		} else {
			$this->display();
		}
	}
	
	/**
	* 编辑
	* @date: 2016-4-9
	* @author: hupeng
	* @return: json
	*/
	function edit() {
		if (IS_POST) {
			$info = I('post.info','');
			$id = I('post.id','');
			$verify = new \Org\Util\Verify();
			if(empty($id)){
				$this->error('参数有误');
			}
			if(empty($info['img_url'])){
				$this->error('封面必须上传');
			}
			if(!$verify::length($info['city_name'],3,1,10)){
				$this->error('名称长度必须1到10');
			}
			if(!$verify::length($info['remark'],3,1,100)){
				$this->error('说明长度必须1到100之间');
			}
			$res = M('MerchantCity')->where('mc_id=%d',$id)->save($info);
			if($res !== false){
				$this->success('操作成功',U('index'));
			}else{
				$this->error('操作失败');
			}
		} else {
			$id = I('get.id','');
			if(empty($id)){
				$this->error('参数有误');
			}
			$info = M('MerchantCity')->where('mc_id=%d',$id)->find();
			
			$this->assign('info',$info);
			$this->display();
		}
	}

	/**
	 *  删除
	 */
	function delete() {
		$id = I("get.id",'');
		if(empty($id)){
			$this->error('参数有误');
		}
		$count = M('Area')->where('mc_id=%d',$id)->count();
		if($count > 0){
			$this->error('此城市下有区域不能删除，请删除其区域');
		}
		$res = M('MerchantCity')->where('mc_id=%d',$id)->delete();
		if ($res !== false) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}
	
	/**
	 * 区域列表
	 * @date: 2016-4-9
	 * @author: hupeng
	 * @return: array
	 */
	function arealist() {
		$count = M('Area')->count();
		$page = $this->page($count, 10);
		$list = M('Area')
			->limit($page->firstRow . ',' . $page->listRows)
			->order('mc_id')
			->select();
		foreach ($list as $k=>$v){
			$list[$k]['city_name'] = M('MerchantCity')
								->where('mc_id=%d',$v['mc_id'])
								->getField('city_name');
		}
		$this->assign("page", $page->show());
		$this->assign('data',$list);
		$this->display('area');
	}
	
	/**
	* 添加
	* @date: 2016-4-9
	* @author: hupeng
	* @return: json
	*/
	function areaadd() {
		if (IS_POST) {
			$info = I('post.info','');
			$verify = new \Org\Util\Verify();
			if(!$verify::length($info['area_name'],3,1,20)){
				$this->error('名称长度必须1到20');
			}
			
			$res = M('Area')->data($info)->add();
			if($res){
				$this->success('添加成功',U('arealist'));
			}else{
				$this->error('添加失败');
			}
		} else {
			$list = M('MerchantCity')->field('mc_id,city_name')->select();
			$this->assign('city',$list);
			$this->display();
		}
	}
	
	/**
	* 编辑
	* @date: 2016-4-9
	* @author: hupeng
	* @return: json
	*/
	function areaedit() {
		if (IS_POST) {
			$info = I('post.info','');
			$id = I('post.id','');
			$verify = new \Org\Util\Verify();
			if(!$verify::length($info['area_name'],3,1,20)){
				$this->error('名称长度必须1到20');
			}
			$res = M('Area')->where('area_id=%d',$id)->save($info);
			if($res !== false){
				$this->success('操作成功',U('arealist'));
			}else{
				$this->error('操作失败');
			}
		} else {
			$id = I('get.id','');
			if(empty($id)){
				$this->error('参数有误');
			}
			$info = M('Area')->where('area_id=%d',$id)->find();
			$list = M('MerchantCity')->field('mc_id,city_name')->select();
			$this->assign('city',$list);
			$this->assign('info',$info);
			$this->display();
		}
	}

	/**
	 *  删除
	 */
	function areadelete() {
		$id = I("get.id",'');
		if(empty($id)){
			$this->error('参数有误');
		}
		$res = M('Area')->where('area_id=%d',$id)->delete();
		if ($res !== false) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}

}

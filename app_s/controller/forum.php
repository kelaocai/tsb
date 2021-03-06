<?php
class forum extends spController {

	//获取板块信息
	function forum_list() {
		$ob = spClass('forum_forum');
		$conditon = array('status' => '1', 'type' => 'group');
		$rs_group = $ob -> spLinker() -> findAll($conditon, null, 'name,fid,fup,img');
		// foreach ($rs_group as $item) {
		// item[]
		// }
		header('Content-type:text/json');
		echo json_encode($rs_group);
		//dump($rs_group);
	}

	//获板块取主题列表
	function forum_thread_list() {
		$size=10;
		$fid = $this -> spArgs('fid');
		$page=$this->spArgs('page',1);
		$ob = spClass('forum_thread');
		$condition = array('fid' => $fid);
		$rs_thread_list = $ob ->spLinker()->spPager($page,$size)-> findAll($condition, 'dateline DESC', 'tid,author,authorid,subject,replies,dateline');
		$i = 0;
		$base_url = "http://".$_SERVER['HTTP_HOST']."/bbs/uc_server/data/avatar/";
		foreach ($rs_thread_list as $item) {
			$rs_thread_list[$i]['avatar'] = $base_url . $this -> get_avatar($item['authorid']);
			$rs_thread_list[$i]['date'] = $this -> time_tran($item['dateline']);
			//最后回复者头像
			$rs_thread_list[$i]['last_reply']['avatar'] = $base_url . $this -> get_avatar($item['last_reply']['authorid']);
			$i++;
		}
		//获取分页数据
		$pager=$ob->spLinker()->spPager()->getPager();
		$rs=array('pager'=>$pager,'data'=>$rs_thread_list);

		header('Content-type:text/json');
		echo json_encode($rs);
		//dump($rs);
	}

	//获取主题帖子列表
	function forum_post_list() {
		$size=10;
		$tid = $this -> spArgs('tid');
		$page=$this->spArgs('page',1);
		$ob = spClass('forum_post');
		$condition = array('tid' => $tid);
		$rs_post_list = $ob ->spPager($page,$size)->findAll($condition, null, 'tid,author,authorid,subject,message,dateline');
		$i = 0;
		$base_url = "http://".$_SERVER['HTTP_HOST']."/bbs/uc_server/data/avatar/";
		foreach ($rs_post_list as $item) {
			$rs_post_list[$i]['avatar'] = $base_url . $this -> get_avatar($item['authorid']);
			$rs_post_list[$i]['date'] = $this -> time_tran($item['dateline']);
			$rs_post_list[$i]['time']=time();
			$i++;
		}		
		//获取分页数据
		$pager=$ob->spPager()->getPager();
		$rs=array('pager'=>$pager,'data'=>$rs_post_list);
		//dump($rs);
		header('Content-type:text/json');
		echo json_encode($rs);

	}

	//获取头像地址
	function get_avatar($uid, $size = 'small', $type = '') {
		$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
		$uid = abs(intval($uid));
		$uid = sprintf("%09d", $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		$typeadd = $type == 'real' ? '_real' : '';
		return $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($uid, -2) . $typeadd . "_avatar_$size.jpg";
	}

	function test() {
		echo $this -> time_tran(1380424830);
	}

	function time_tran($the_time) {
		$now_time = date("Y-m-d H:i:s", time());
		$now_time = strtotime($now_time);
		//return $now_time;
		$limit = $now_time - $the_time;
		if ($limit < 60) {
			return $limit . '秒钟之前';
		}
		if ($limit >= 60 && $limit < 3600) {
			return floor($limit / 60) . '分钟之前';
		}
		if ($limit >= 3600 && $limit < 86400) {
			return floor($limit / 3600) . '小时之前';
		}
		if ($limit >= 86400&&$limit<2*24*3600) {
			return '昨天';
			//return date('Y-m-d H:i:s', $the_time);
		}
		if ($limit >= 2*24*3600&&$limit<3*24*3600) {
			return '前天';
			//return date('Y-m-d H:i:s', $the_time);
		}
		if ($limit >= 3*24*3600) {
			return date('Y-m-d', $the_time);
		}
	}

}

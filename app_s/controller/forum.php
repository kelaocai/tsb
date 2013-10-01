<?php
class forum extends spController
{
		
	//获取板块信息
	function forum_list(){
		$ob=spClass('forum_forum');
		$conditon=array('status'=>'1','type'=>'group');
		$rs_group=$ob->spLinker()->findAll($conditon,null,'name,fid,fup,img');
		// foreach ($rs_group as $item) {
			// item[]
		// }
		header('Content-type:text/json'); 
		echo json_encode($rs_group);
		//dump($rs_group);
	}
	
	//获板块取主题列表
	function forum_thread_list(){
		$fid=$this->spArgs('fid');
		$ob=spClass('forum_thread');
		$condition=array('fid'=>$fid);
		$rs_post_list=$ob->findAll($condition,null,'author,authorid,subject,dateline');
		header('Content-type:text/json'); 
		echo json_encode($rs_post_list);
		//dump($rs_post_list);
	}
}
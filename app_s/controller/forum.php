<?php
class forum extends spController
{
		
	//获取板块信息
	function forum_list(){
		$ob=spClass('forum_forum');
		$conditon=array('status'=>'1','type'=>'group');
		$rs_group=$ob->spLinker()->findAll($conditon,null,'name,fid,fup');
		// foreach ($rs_group as $item) {
			// item[]
		// }
		header('Content-type:text/json'); 
		echo json_encode($rs_group);
		//dump($rs_group);
	}
}
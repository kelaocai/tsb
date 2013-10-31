<?php
class main extends spController
{
	function index(){
		$db=spDB('tsb_forum_forum');
		$cnt=$db->findCount();
		echo "cnt:$cnt";
	}
	
	function forum_list(){
		$db=spDB('tsb_forum_forum');
		$conditon=array('status'=>1,'type'=>'group');
		$rs_group=$db->findAll($condition);
		dump($rs_group);
	}
}
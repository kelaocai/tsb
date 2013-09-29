<?php

class forum_forum extends spModel {

	public $pk = 'fid';
	public $table = 'tsb_forum_forum';
	
	var $linker=array(
				array(
					'type'=>'hasmany',
					'map'=>'forums',
					'mapkey'=>'fid',
					'fclass'=>'forum_forum',
					'fkey'=>'fup',
					'field'=>'name,fid,fup,status,img',
					'condition'=>'status=1',
					'enabled'=>'true'
				)
	
	);
	
	
}
?>
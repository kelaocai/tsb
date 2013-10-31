<?php

class forum_thread extends spModel {

	public $pk = 'tid';
	public $table = 'tsb_forum_thread';
	
	var $linker=array(
				array(
					'type'=>'hasone',
					'map'=>'last_reply',
					'mapkey'=>'tid',
					'fclass'=>'forum_post',
					'fkey'=>'tid',
					'field'=>'message,authorid',
					'limit'=>'1',
					'condition'=>'first=0',
					'sort'=>'dateline DESC',
					'enabled'=>'true'
				)
	
	);
	
	
	
}
?>
<?php
/*
+--------------------------------------------------------------------------
|   WeCenter [#RELEASE_VERSION#]
|   ========================================
|   by WeCenter Software
|   © 2011 - 2013 WeCenter. All Rights Reserved
|   http://www.wecenter.com
|   ========================================
|   Support: WeCenter@qq.com
|   
+---------------------------------------------------------------------------
*/


if (!defined('IN_ANWSION'))
{
	die;
}

class pinche_class extends AWS_MODEL
{
	public function add_pinche($destination, $leavedate, $current, $max,$car,$contact)
	{
		return $this->insert('pinche', array(
			'destination' => $destination,
			'leavedate' => $leavedate,
			'current' => $current,
			'max' => $max,
			'car' => $car,
			'contact'=>$contact,
			'time'=>time()
		));
	}
}
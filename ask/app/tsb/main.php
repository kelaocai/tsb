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

if (!defined('IN_ANWSION')) {
	die ;
}

class main extends AWS_CONTROLLER {
	
	public function get_access_rule() {
		$rule_action['rule_type'] = 'black';

		$rule_action['actions'] = array();

		return $rule_action;
	}
	
	
	public function test_action()
	{
		TPL::output("tsb/test");
	}

}

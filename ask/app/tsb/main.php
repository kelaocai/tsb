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

	public function test_action() {
		TPL::import_js(array('js/tsb_upload.js'));
		$nn=load_class('core_upload')->prep_filename('侧面.jpg');
		TPL::assign('test', $nn);
		TPL::output("tsb/test");
	}

	

}

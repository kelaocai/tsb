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

	public function setup() {
		HTTP::no_cache_header();
	}

	public function index_action() {
		TPL::import_js('js/tsb/flipsnap.min.js');
		TPL::import_css('css/tsb/tsbm.css');
		TPL::output('tsbm/index');
	}

	public function captcha_action() {
		AWS_APP::captcha() -> generate();
	}

}

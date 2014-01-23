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

define('IN_MOBILE', true);

class webapp extends AWS_CONTROLLER {
	public function get_access_rule() {
		$rule_action['rule_type'] = 'black';
		$rule_action['actions'] = array();
		return $rule_action;
	}

	public function setup() {

		HTTP::no_cache_header();
		// TPL::import_js(array('js/jquery.form.js', 'js/mobile/framework.js', 'js/tsb/tsb_mobile.js', 'js/tsb/tsb-mobile-template.js'));
		TPL::import_css('css/tsb/bootstrap/main/bootstrap.min.css');
		TPL::import_css('css/tsb/webapp.css');
	}

	public function index_action() {
		TPL::import_css('css/tsb/webapp.css');	
		TPL::output('tsbm/pc');
	}

}

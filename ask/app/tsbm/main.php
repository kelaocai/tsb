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
		TPL::import_js(array(
			'js/jquery.2.js',
			'js/jquery.form.js',
			'js/mobile/framework.js',
			'js/mobile/mobile.js',
			'js/mobile/aw-mobile-template.js'
		));
		TPL::import_css('css/tsb/tsbm.css');
	}

	public function index_action() {
		TPL::import_js('js/tsb/flipsnap.min.js');
		
		TPL::output('tsbm/index');
	}

	public function captcha_action() {
		AWS_APP::captcha() -> generate();
	}
	
	public function explore_action()
	{
		
		
		
		$this->crumb(AWS_APP::lang()->_t('热门'), '/tsbm/explore/');
		
		$nav_menu = $this->model('menu')->get_nav_menu_list(null, true);
			
		//TPL::assign('feature_ids', $nav_menu['feature_ids']);
		
		unset($nav_menu['feature_ids']);
		
		TPL::assign('content_nav_menu', $nav_menu);
		
		TPL::assign('sidebar_hot_topics', $this->model('module')->sidebar_hot_topics($_GET['category']));
		$aa=$this->model('module')->sidebar_hot_topics($_GET['category']);
		
		
		if ($_GET['feature_id'])
		{
			TPL::assign('feature_info', $this->model('feature')->get_feature_by_id($_GET['feature_id']));
		}
		
		if ($_GET['category'])
		{
			TPL::assign('category_info', $this->model('system')->get_category_info($_GET['category']));
		}
		
		TPL::output('tsbm/explore');
	}
	
	public function test_action(){
		$question_list = $this->model('question')->get_questions_list($_GET['page'], $per_page, $_GET['sort_type'], $_GET['topic_id'], $_GET['category'], $_GET['answer_count'], $_GET['day']);
		
		fb($question_list);
		echo "hello";
		
	}

}

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

class main extends AWS_CONTROLLER
{
	public function get_access_rule()
	{
		$rule_action['rule_type'] = "white"; //黑名单,黑名单中的检查  'white'白名单,白名单以外的检查
		
		if ($this->user_info['permission']['visit_feature'] AND $this->user_info['permission']['visit_site'])
		{
			$rule_action['actions'] = array(
				'index'
			);
		}
		
		return $rule_action;
	}

	public function setup()
	{
		$this->crumb(AWS_APP::lang()->_t('专题'), '/feature/');
	}

	public function index_action()
	{
		if (is_numeric($_GET['id']))
		{
			if (! $feature_info = $this->model('feature')->get_feature_by_id($_GET['id']))
			{
				H::redirect_msg(AWS_APP::lang()->_t('专题不存在'), '/');
			}
		}
		else if (! $feature_info = $this->model('feature')->get_feature_by_url_token($_GET['id']))
		{
			H::redirect_msg(AWS_APP::lang()->_t('专题不存在'), '/');
		}
		
		if ($feature_info['url_token'] != $_GET['id'] AND !$_GET['sort_type'] AND !$_GET['is_recommend'])
		{
			HTTP::redirect('/feature/' . $feature_info['url_token']);
		}
		
		if (! $topic_list = $this->model('topic')->get_topics_by_ids($this->model('feature')->get_topics_by_feature_id($feature_info['id'])))
		{
			H::redirect_msg(AWS_APP::lang()->_t('专题下必须包含一个以上话题'), '/');
		}
		
		if ($feature_info['seo_title'])
		{
			TPL::assign('page_title', $feature_info['seo_title']);
		}
		else
		{
			$this->crumb($feature_info['title'], '/feature/' . $feature_info['url_token']);		}
		
		TPL::assign('sidebar_hot_topics', $topic_list);
		TPL::assign('feature_info', $feature_info);
		
		$nav_menu = $this->model('menu')->get_nav_menu_list(null, true);
		
		if (is_array($nav_menu['feature_ids']) && in_array($feature_info['id'], $nav_menu['feature_ids']))
		{
			// 导航
			if (TPL::is_output('block/content_nav_menu.tpl.htm', 'home/explore'))
			{
				TPL::assign('feature_ids', $nav_menu['feature_ids']);
				
				unset($nav_menu['feature_ids']);
				
				TPL::assign('content_nav_menu', $nav_menu);
			}
			
			// 边栏热门用户
			if (TPL::is_output('block/sidebar_hot_users.tpl.htm', 'home/explore'))
			{
				$sidebar_hot_users = $this->model('module')->sidebar_hot_users($this->user_id, 5);
				
				TPL::assign('sidebar_hot_users', $sidebar_hot_users);
			}
			
			// 边栏专题
			if (TPL::is_output('block/sidebar_feature.tpl.htm', 'home/explore'))
			{
				$feature_list = $this->model('module')->feature_list();
				TPL::assign('feature_list', $feature_list);
			}
			
			if (TPL::is_output('block/content_question.tpl.htm', 'home/explore'))
			{
				if ($feature_info['id'])
				{
					$_GET['topic_id'] = $this->model('feature')->get_topics_by_feature_id($feature_info['id']);
				}
				
				if (! $_GET['sort_type'])
				{
					$_GET['sort_type'] = 'new';
				}
			
				if ($_GET['sort_type'] == 'unresponsive')
				{
					$_GET['answer_count'] = '0';
				}
				
				if ($_GET['sort_type'] == 'hot')
				{
					$question_list = $this->model('question')->get_hot_question($_GET['category'], $_GET['topic_id'], $_GET['day'], $_GET['page'], get_setting('contents_per_page'));
				}
				else
				{
					$question_list = $this->model('question')->get_questions_list($_GET['page'], get_setting('contents_per_page'), $_GET['sort_type'], $_GET['topic_id'], $_GET['category'], $_GET['answer_count'], $_GET['day'], $_GET['is_recommend']);
				}
				
				if ($question_list)
				{
					foreach ($question_list AS $key => $val)
					{
						if ($val['answer_count'])
						{
							$question_list[$key]['answer_users'] = $this->model('question')->get_answer_users_by_question_id($val['question_id'], 2, $val['published_uid']);
						}
					}
				}
				
				TPL::assign('pagination', AWS_APP::pagination()->initialize(array(
					'base_url' => get_js_url('/feature/id-' . $feature_info['id'] . '__sort_type-' . preg_replace("/[\(\)\.;']/", '', $_GET['sort_type']) . '__day-' . intval($_GET['day']) . '__is_recommend-' . $_GET['is_recommend']), 
					'total_rows' => $this->model('question')->get_questions_list_total(),
					'per_page' => get_setting('contents_per_page')
				))->create_links());
				
				TPL::assign('question_list', $question_list);
				TPL::assign('question_list_bit', TPL::output('question/ajax/list', false));
			}
			
			TPL::output('home/explore');
		}
		else
		{			
			TPL::import_js('js/feature.js');
			
			TPL::output('feature/detail');
		}
	}
}
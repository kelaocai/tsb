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

define('IN_AJAX', TRUE);


if (!defined('IN_ANWSION'))
{
	die;
}

define('IN_MOBILE', true);

class ajax extends AWS_CONTROLLER
{
	var $per_page = 20;
	
	public function get_access_rule()
	{
		$rule_action['rule_type'] = 'black';
		$rule_action['actions'] = array();
		
		return $rule_action;
	}
	
	public function setup()
	{
		HTTP::no_cache_header();
	}
	
	public function inbox_list_action()
	{
		$list = $this->model('message')->list_message($_GET['page'], $this->per_page, $this->user_id);
		
		if ($list['user_list'])
		{
			if ($users_info_query = $this->model('account')->get_user_info_by_uids($list['user_list']))
			{
				foreach ($users_info_query as $user)
				{
					$users_info[$user['uid']] = $user;
				}
			}
		}
		
		if ($list['diag_ids'])
		{
			$last_message = $this->model('message')->get_last_messages($list['diag_ids']);
		}
				
		if ($list['content_list'])
		{
			$data = array();
			
			foreach ($list['content_list'] as $key => $value)
			{
				if ($value['sender_uid'] == $this->user_id && $value['sender_count'] > 0) // 当前处于发送用户
				{
					$tmp['user_name'] = $users_info[$value['recipient_uid']]['user_name'];
					$tmp['url_token'] = $users_info[$value['recipient_uid']]['url_token'];
					
					$tmp['unread'] = $value['sender_unread'];
					$tmp['count'] = $value['sender_count'];
					$tmp['uid'] = $value['recipient_uid'];
				}
				else if ($value['recipient_uid'] == $this->user_id && $value['recipient_count'] > 0) // 当前处于接收用户
				{
					$tmp['user_name'] = $users_info[$value['sender_uid']]['user_name'];
					$tmp['url_token'] = $users_info[$value['sender_uid']]['url_token'];
					
					$tmp['unread'] = $value['recipient_unread'];
					$tmp['count'] = $value['recipient_count'];
					
					$tmp['uid'] = $value['sender_uid'];
				}
				
				$tmp['last_message'] = $last_message[$value['dialog_id']];
				
				$tmp['last_time'] = $value['last_time'];
				$tmp['dialog_id'] = $value['dialog_id'];
				
				$data[] = $tmp;
			}
		}
		
		TPL::assign('list', $data);

		TPL::output('m/ajax/inbox_list');
	}
	
	public function focus_topics_list_action()
	{
		if ($topics_list = $this->model('topic')->get_focus_topic_list($this->user_id, intval($_GET['page']) * 5 . ', ' . 5))
		{
			foreach ($topics_list AS $key => $val)
			{
				$topics_list[$key]['action_list'] = $this->model('question')->get_questions_list(0, 3, 'new', $val['topic_id']);
			}
		}
		
		TPL::assign('topics_list', $topics_list);
		
		TPL::output('m/ajax/focus_topics_list');
	}
	
	public function discuss_action()
	{
			
			
		if ($_GET['feature_id'])
		{
			$_GET['topic_id'] = $this->model('feature')->get_topics_by_feature_id($_GET['feature_id']);
		}
		
		if ($_GET['sort_type'] == 'unresponsive')
		{
			$_GET['answer_count'] = '0';
		}
		
		if ($_GET['per_page'])
		{
			$per_page = intval($_GET['per_page']);
		}
		else
		{
			$per_page = get_setting('contents_per_page');
		}
		
		if ($_GET['sort_type'] == 'hot')
		{
			$question_list = $this->model('question')->get_hot_question($_GET['category'], $_GET['topic_id'], $_GET['day'], $_GET['page'], $per_page);
		}
		else
		{
			$question_list = $this->model('question')->get_questions_list($_GET['page'], $per_page, $_GET['sort_type'], $_GET['topic_id'], $_GET['category'], $_GET['answer_count'], $_GET['day']);
		}
		
		if ($_GET['template'] != 'm' AND $question_list)
		{
			foreach ($question_list AS $key => $val)
			{
				if ($val['answer_count'])
				{
					$question_list[$key]['answer_users'] = $this->model('question')->get_answer_users_by_question_id($val['question_id'], 2, $val['published_uid']);
				}
			}
		}
		
		TPL::assign('question_list', $question_list);
		
		if ($_GET['template'] == 'm')
		{
			TPL::output('tsbm/ajax/question_list');
		}
		else
		{
			TPL::output("question/ajax/list");
		}
	}

}
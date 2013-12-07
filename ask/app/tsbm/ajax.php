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

if (!defined('IN_ANWSION')) {
	die ;
}

define('IN_MOBILE', true);

class ajax extends AWS_CONTROLLER {
	var $per_page = 20;

	public function get_access_rule() {
		$rule_action['rule_type'] = 'black';
		$rule_action['actions'] = array();

		return $rule_action;
	}

	public function setup() {
		HTTP::no_cache_header();
	}

	public function inbox_list_action() {
		$list = $this -> model('message') -> list_message($_GET['page'], $this -> per_page, $this -> user_id);

		if ($list['user_list']) {
			if ($users_info_query = $this -> model('account') -> get_user_info_by_uids($list['user_list'])) {
				foreach ($users_info_query as $user) {
					$users_info[$user['uid']] = $user;
				}
			}
		}

		if ($list['diag_ids']) {
			$last_message = $this -> model('message') -> get_last_messages($list['diag_ids']);
		}

		if ($list['content_list']) {
			$data = array();

			foreach ($list['content_list'] as $key => $value) {
				if ($value['sender_uid'] == $this -> user_id && $value['sender_count'] > 0)// 当前处于发送用户
				{
					$tmp['user_name'] = $users_info[$value['recipient_uid']]['user_name'];
					$tmp['url_token'] = $users_info[$value['recipient_uid']]['url_token'];

					$tmp['unread'] = $value['sender_unread'];
					$tmp['count'] = $value['sender_count'];
					$tmp['uid'] = $value['recipient_uid'];
				} else if ($value['recipient_uid'] == $this -> user_id && $value['recipient_count'] > 0)// 当前处于接收用户
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

	public function focus_topics_list_action() {
		if ($topics_list = $this -> model('topic') -> get_focus_topic_list($this -> user_id, intval($_GET['page']) * 5 . ', ' . 5)) {
			foreach ($topics_list AS $key => $val) {
				$topics_list[$key]['action_list'] = $this -> model('question') -> get_questions_list(0, 3, 'new', $val['topic_id']);
			}
		}

		TPL::assign('topics_list', $topics_list);

		TPL::output('m/ajax/focus_topics_list');
	}

	public function discuss_action() {

		if ($_GET['feature_id']) {
			$_GET['topic_id'] = $this -> model('feature') -> get_topics_by_feature_id($_GET['feature_id']);
		}

		if ($_GET['sort_type'] == 'unresponsive') {
			$_GET['answer_count'] = '0';
		}

		if ($_GET['per_page']) {
			$per_page = intval($_GET['per_page']);
		} else {
			$per_page = get_setting('contents_per_page');
		}

		if ($_GET['sort_type'] == 'hot') {
			$question_list = $this -> model('question') -> get_hot_question($_GET['category'], $_GET['topic_id'], $_GET['day'], $_GET['page'], $per_page);
		} else {
			$question_list = $this -> model('question') -> get_questions_list($_GET['page'], $per_page, $_GET['sort_type'], $_GET['topic_id'], $_GET['category'], $_GET['answer_count'], $_GET['day']);
		}

		if ($_GET['template'] != 'm' AND $question_list) {
			foreach ($question_list AS $key => $val) {
				if ($val['answer_count']) {
					$question_list[$key]['answer_users'] = $this -> model('question') -> get_answer_users_by_question_id($val['question_id'], 2, $val['published_uid']);
				}
			}
		}

		TPL::assign('question_list', $question_list);

		fb($question_list, 'aa');

		if ($_GET['template'] == 'm') {
			TPL::output('tsbm/ajax/question_list');
		} else {
			TPL::output("question/ajax/list");
		}
	}

	public function save_answer_action() {
		if ($this -> user_info['integral'] < 0 and get_setting('integral_system_enabled') == 'Y') {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('你的剩余积分已经不足以进行此操作')));
		}

		if (!$question_info = $this -> model('question') -> get_question_info_by_id($_POST['question_id'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('问题不存在')));
		}

		if ($question_info['lock'] AND !($this -> user_info['permission']['is_administortar'] OR $this -> user_info['permission']['is_moderator'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('已经锁定的问题不能回复')));
		}

		$answer_content = trim($_POST['answer_content'], "\r\n\t");

		if (!$answer_content) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请输入回复内容')));
		}

		// 判断是否是问题发起者
		if (get_setting('answer_self_question') == 'N' and $question_info['published_uid'] == $this -> user_id) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('不能回复自己发布的问题，你可以修改问题内容')));
		}

		// 判断是否已回复过问题
		if ((get_setting('answer_unique') == 'Y') && $this -> model('answer') -> has_answer_by_uid($_POST['question_id'], $this -> user_id)) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('一个问题只能回复一次，你可以编辑回复过的回复')));
		}

		if (strlen($answer_content) < get_setting('answer_length_lower')) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('回复内容字数不得少于 %s 字节', get_setting('answer_length_lower'))));
		}

		if (!$this -> user_info['permission']['publish_url'] && FORMAT::outside_url_exists($answer_content)) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('你所在的用户组不允许发布站外链接')));
		}

		if (human_valid('answer_valid_hour') and !AWS_APP::captcha() -> is_validate($_POST['seccode_verify'])) {
			if ($_POST['_is_mobile']) {
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('你发帖频率太快了, 坐下来喝杯咖啡休息一下吧')));
			}

			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请填写正确的验证码')));
		}

		// !注: 来路检测后面不能再放报错提示
		if (!valid_post_hash($_POST['post_hash'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('表单来路不正确或内容已提交, 请刷新页面重试')));
		}

		$this -> model('draft') -> delete_draft($_POST['question_id'], 'answer', $this -> user_id);

		if ($this -> publish_approval_valid()) {
			$this -> model('publish') -> publish_approval('answer', array('question_id' => intval($_POST['question_id']), 'answer_content' => $answer_content, 'anonymous' => $_POST['anonymous'], 'attach_access_key' => $_POST['attach_access_key'], 'auto_focus' => $_POST['auto_focus']), $this -> user_id, $_POST['attach_access_key']);

			H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url('/publish/wait_approval/question_id-' . intval($_POST['question_id']) . '__is_mobile-' . $_POST['_is_mobile'])), 1, null));
		} else {
			$answer_id = $this -> model('publish') -> publish_answer($_POST['question_id'], $answer_content, $this -> user_id, $_POST['anonymous'], $_POST['attach_access_key'], $_POST['auto_focus']);

			if ($_POST['_is_mobile']) {
				$url = get_js_url('/tsbm/question/id-' . intval($_POST['question_id']) . '__item_id-' . $answer_id . '__rf-false');

				$this -> model('answer') -> set_answer_publish_source($answer_id, 'mobile');
			} else {
				$url = get_js_url('/question/' . intval($_POST['question_id']) . '?item_id=' . $answer_id . '&rf=false');
			}

			H::ajax_json_output(AWS_APP::RSM(array('url' => $url), 1, null));
		}
	}

	public function login_process_action() {
		if (get_setting('ucenter_enabled') == 'Y') {
			if (!$user_info = $this -> model('ucenter') -> login($_POST['user_name'], $_POST['password'])) {
				$user_info = $this -> model('account') -> check_login($_POST['user_name'], $_POST['password']);
			}
		} else {
			$user_info = $this -> model('account') -> check_login($_POST['user_name'], $_POST['password']);
		}

		if (!$user_info) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('请输入正确的帐号或密码')));
		} else {
			if ($user_info['forbidden'] == 1) {
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('抱歉, 你的账号已经被禁止登录')));
			}

			if (get_setting('site_close') == 'Y' AND $user_info['group_id'] != 1) {
				H::ajax_json_output(AWS_APP::RSM(null, -1, get_setting('close_notice')));
			}

			if (!$user_info['valid_email'] AND get_setting('register_email_reqire') == 'Y') {
				AWS_APP::session() -> valid_email = $user_info['email'];

				H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url('/account/valid_email/')), 1, null));
			}

			if ($_POST['net_auto_login']) {
				$expire = 60 * 60 * 24 * 360;
			}

			$this -> model('account') -> update_user_last_login($user_info['uid']);
			$this -> model('account') -> setcookie_logout();

			$this -> model('account') -> setcookie_login($user_info['uid'], $_POST['user_name'], $_POST['password'], $user_info['salt'], $expire);

			if ($_POST['weixin_id'] AND $_POST['_is_mobile']) {
				if (!$this -> model('account') -> get_user_info_by_weixin_id($_POST['weixin_id'])) {
					$this -> model('account') -> update_users_fields(array('weixin_id' => $_POST['weixin_id'], ), $user_info['uid']);
				}

				H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url('/m/weixin_bind_success/')), 1, null));
			}

			if ($user_info['is_first_login'] AND !$_POST['_is_mobile']) {
				$url = get_js_url('/home/first_login-TRUE');
			}

			if ($_POST['return_url'] AND !strstr($_POST['return_url'], '/logout')) {
				$url = strip_tags($_POST['return_url']);

				if ($_POST['_is_mobile'] AND !strstr($_POST['return_url'], '/m/')) {
					unset($url);
				} else if (strstr($_POST['return_url'], '://') AND !strstr($_POST['return_url'], get_setting('base_url'))) {
					unset($url);
				}
			}

			if (!$url AND $_POST['_is_mobile']) {
				$url = get_js_url('/tsbm/');
			}

			if (get_setting('ucenter_enabled') == 'Y') {
				$url = get_js_url('/account/sync_login/url-' . @base64_encode($url));
			}

			H::ajax_json_output(AWS_APP::RSM(array('url' => $url), 1, null));
		}
	}

}

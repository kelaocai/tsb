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

		//fb($question_list ,'$question_list2');

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

			// tsb 移动版上传图片
			if ($_POST['_is_mobile'] && $_POST['_is_attach']) {
				//$file_name = load_class('tsb_common') -> m_upload($_POST['image_data'], $_POST['image_name'], 'answer', $_POST['attach_access_key']);
				$file_name = load_class('tsb_common') -> upload_upyun_img($_POST['image_data'],'answer');
				
				if (!$file_name) {
					H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('图片上传失败')));
				}
				//插入附件数据
				$attach_id = $this -> model('publish') -> add_attach('answer', $_POST['image_name'], $_POST['attach_access_key'], time(), $file_name, '1');
				if (!$attach_id) {
					H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('图片保存失败')));
				}

			}

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

	public function publish_question_action() {
		if (!$this -> user_info['permission']['publish_question']) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('你没有权限发布问题')));
		}

		if ($this -> user_info['integral'] < 0 AND get_setting('integral_system_enabled') == 'Y') {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('你的剩余积分已经不足以进行此操作')));
		}

		if (empty($_POST['question_content'])) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('请输入问题标题')));
		}

		// if (get_setting('category_enable') == 'N' OR $_POST['_is_mobile']) {
		// $_POST['category_id'] = 1;
		// }
		if (get_setting('category_enable') == 'N') {
			$_POST['category_id'] = 1;
		}

		if (!$_POST['category_id']) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('请选择问题分类')));
		}

		if (cjk_strlen($_POST['question_content']) < 5) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('问题标题字数不得少于 5 个字')));
		}

		if (get_setting('question_title_limit') > 0 && cjk_strlen($_POST['question_content']) > get_setting('question_title_limit')) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('问题标题字数不得大于 %s 字节', get_setting('question_title_limit'))));
		}

		if (!$this -> user_info['permission']['publish_url'] && FORMAT::outside_url_exists($_POST['question_detail'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('你所在的用户组不允许发布站外链接')));
		}

		if (human_valid('question_valid_hour') AND !AWS_APP::captcha() -> is_validate($_POST['seccode_verify'])) {
			if ($_POST['_is_mobile']) {
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('你发帖频率太快了, 坐下来喝杯咖啡休息一下吧')));
			}

			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请填写正确的验证码')));
		}

		if ($_POST['topics'] AND get_setting('question_topics_limit') AND sizeof($_POST['topics']) > get_setting('question_topics_limit')) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('单个问题话题数量最多为 %s 个, 请调整话题数量', get_setting('question_topics_limit'))));
		}

		if (get_setting('new_question_force_add_topic') == 'Y' AND !$_POST['topics']) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请为问题添加话题')));
		}

		// !注: 来路检测后面不能再放报错提示
		if (!valid_post_hash($_POST['post_hash'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('表单来路不正确或内容已提交, 请刷新页面重试')));
		}

		$this -> model('draft') -> delete_draft(1, 'question', $this -> user_id);

		if ($this -> publish_approval_valid()) {
			$this -> model('publish') -> publish_approval('question', array('question_content' => $_POST['question_content'], 'question_detail' => $_POST['question_detail'], 'category_id' => $_POST['category_id'], 'topics' => $_POST['topics'], 'anonymous' => $_POST['anonymous'], 'attach_access_key' => $_POST['attach_access_key'], 'ask_user_id' => $_POST['ask_user_id'], 'permission_create_topic' => $this -> user_info['permission']['create_topic']), $this -> user_id, $_POST['attach_access_key']);

			H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url('/publish/wait_approval/')), 1, null));
		} else {
			$question_id = $this -> model('publish') -> publish_question($_POST['question_content'], $_POST['question_detail'], $_POST['category_id'], $this -> user_id, $_POST['topics'], $_POST['anonymous'], $_POST['attach_access_key'], $_POST['ask_user_id'], $this -> user_info['permission']['create_topic']);

			if ($_POST['_is_mobile']) {
				$url = get_js_url('/tsbm/question/' . $question_id);
			} else {
				$url = get_js_url('/question/' . $question_id);
			}

			H::ajax_json_output(AWS_APP::RSM(array('url' => $url), 1, null));
		}
	}

	public function register_process_action() {
		if (HTTP::get_cookie('fromuid')) {
			$fromuid = HTTP::get_cookie('fromuid');
		}

		if (get_setting('invite_reg_only') == 'Y' AND !$_POST['icode']) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('本站只能通过邀请注册')));
		}

		if ($_POST['icode']) {
			$invitation = $this -> model('invitation') -> check_code_available($_POST['icode']);

			if ($invitation && ($_POST['email'] == $invitation['invitation_email'])) {
				$email_valid = true;
			} else {
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('邀请码无效')));
			}
		}

		if (trim($_POST['user_name']) == '') {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('请输入用户名')));
		} else if ($this -> model('account') -> check_username($_POST['user_name'])) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('用户名已经存在')));
		} else if ($check_rs = $this -> model('account') -> check_username_char($_POST['user_name'])) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('用户名包含无效字符')));
		} else if ($this -> model('account') -> check_username_sensitive_words($_POST['user_name']) OR trim($_POST['user_name']) != $_POST['user_name']) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('用户名中包含敏感词或系统保留字')));
		}

		if ($this -> model('account') -> check_email($_POST['email'])) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('E-Mail 已经被使用, 或格式不正确')));
		}

		if (strlen($_POST['password']) < 6 OR strlen($_POST['password']) > 16) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('密码长度不符合规则')));
		}

		if (!$_POST['agreement_chk']) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('你必需同意用户协议才能继续')));
		}

		// 检查验证码
		if (!($_POST['fromuid'] OR $_POST['icode']) AND !AWS_APP::captcha() -> is_validate($_POST['seccode_verify']) AND get_setting('register_seccode') == 'Y') {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('请填写正确的验证码')));
		}

		if (get_setting('ucenter_enabled') == 'Y') {
			$result = $this -> model('ucenter') -> register($_POST['user_name'], $_POST['password'], $_POST['email'], $email_valid);

			if (is_array($result)) {
				$uid = $result['user_info']['uid'];
			} else {
				H::ajax_json_output(AWS_APP::RSM(null, -1, $result));
			}
		} else {
			$uid = $this -> model('account') -> user_register($_POST['user_name'], $_POST['password'], $_POST['email'], $email_valid);
		}

		if ($uid) {
			$this -> model("account") -> setcookie_logout();
			// 清除COOKIE
			$this -> model("account") -> setsession_logout();
			// 清除session;

			if ($fromuid) {
				// 有来源的用户无邀请码
				$follow_users = $this -> model('account') -> get_user_info_by_uid($fromuid);
			} else {
				$follow_users = $this -> model('invitation') -> get_invitation_by_code($_POST['icode']);
			}

			if ($follow_users) {
				$follow_uid = $follow_users['uid'];
			}

			// 发送邀请问答站内信
			if ($_POST['invite_question_id'] and $follow_users) {
				$url = get_js_url('/question/' . $_POST['invite_question_id']);

				$title = $follow_users['user_name'] . ' 邀请你来回复问题';
				$content = $follow_users['user_name'] . "  邀请你来回复问题: " . $url . " \r\n\r\n 邀请你来回复问题期待您的回复";

				$this -> model('message') -> send_message($follow_uid, $uid, $title, $content, 0, 0);
			}

			// 互为关注
			if ($follow_uid) {
				$this -> model('follow') -> user_follow_add($uid, $follow_uid);
				$this -> model('follow') -> user_follow_add($follow_uid, $uid);

				$this -> model('integral') -> process($follow_uid, 'INVITE', get_setting('integral_system_config_invite'), '邀请注册: ' . $_POST['user_name'], $follow_uid);
			}

			if ($_POST['icode']) {
				$this -> model('invitation') -> invitation_code_active($_POST['icode'], time(), fetch_ip(), $uid);
			}

			if ($email_valid OR get_setting('register_email_reqire') == 'N') {
				$user_info = $this -> model('account') -> get_user_info_by_uid($uid);

				$this -> model('account') -> setcookie_login($user_info['uid'], $user_info['user_name'], $_POST['password'], $user_info['salt']);

				if (!$_POST['_is_mobile']) {
					H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url('/home/first_login-TRUE')), 1, null));
				}
			} else {
				AWS_APP::session() -> valid_email = $_POST['email'];

				$this -> model('active') -> new_valid_email($uid);

				H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url('/account/valid_email/')), 1, null));
			}

			if ($_POST['weixin_id'] AND $_POST['_is_mobile']) {
				if (!$this -> model('account') -> get_user_info_by_weixin_id($_POST['weixin_id'])) {
					$this -> model('account') -> update_users_fields(array('weixin_id' => $_POST['weixin_id'], ), $uid);
				}

				H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url('/m/weixin_bind_success/')), 1, null));
			}

			if ($_POST['_is_mobile']) {
				H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url('/tsbm/')), 1, null));
			}
		} else {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('系统错误, 请联系管理员')));
		}
	}

	public function search_result_action() {
		$limit = intval($_GET['page']) * $this -> per_page . ', ' . $this -> per_page;

		switch ($_GET['search_type']) {
			case 'all' :
				$search_result = $this -> model('tsbsearch') -> search($_GET['q'], null, $limit);
				break;

			case 'questions' :
				$search_result = $this -> model('tsbsearch') -> search($_GET['q'], 'questions', $limit);
				break;

			case 'topics' :
				$search_result = $this -> model('tsbsearch') -> search($_GET['q'], 'topics', $limit);
				break;

			case 'users' :
				$search_result = $this -> model('tsbsearch') -> search($_GET['q'], 'users', $limit);
				break;

			case 'articles' :
				$search_result = $this -> model('tsbsearch') -> search($_GET['q'], 'articles', $limit);
				break;
		}

		if ($this -> user_id AND $search_result) {
			foreach ($search_result AS $key => $val) {
				switch ($val['type']) {
					case 'questions' :
						$search_result[$key]['focus'] = $this -> model('question') -> has_focus_question($val['search_id'], $this -> user_id);
						break;

					case 'topics' :
						$search_result[$key]['focus'] = $this -> model('topic') -> has_focus_topic($this -> user_id, $val['search_id']);
						break;

					case 'users' :
						$search_result[$key]['focus'] = $this -> model('follow') -> user_follow_check($this -> user_id, $val['search_id']);
						break;
				}
			}
		}

		TPL::assign('search_result', $search_result);

		if ($_GET['template'] == 'm') {
			TPL::output('tsbm/ajax/search_result');
		} else {
			TPL::output('search/ajax/search_result');
		}
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

		TPL::output('tsbm/ajax/inbox_list');
	}

	public function search_action() {
		if ($result = $this -> model('tsbsearch') -> search($_GET['q'], $_GET['type'], intval($_GET['limit']), $_GET['topic_ids'])) {
			H::ajax_json_output($this -> model('tsbsearch') -> search($_GET['q'], $_GET['type'], intval($_GET['limit']), $_GET['topic_ids']));
		} else {
			H::ajax_json_output(array());
		}
	}

	function modify_question_action() {
		if (!$question_info = $this -> model('question') -> get_question_info_by_id($_POST['question_id'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('问题不存在')));
		}

		if ($question_info['lock'] && !($this -> user_info['permission']['is_administortar'] OR $this -> user_info['permission']['is_moderator'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('问题已锁定, 不能编辑')));
		}

		if (!$this -> user_info['permission']['is_administortar'] AND !$this -> user_info['permission']['is_moderator'] AND !$this -> user_info['permission']['edit_question']) {
			if ($question_info['published_uid'] != $this -> user_id) {
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('你没有权限编辑这个问题')));
			}
		}

		if (!$_POST['category_id'] AND get_setting('category_enable') == 'Y') {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('请选择分类')));
		}

		if (cjk_strlen($_POST['question_content']) < 5) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('问题标题字数不得少于 5 个字')));
		}

		if (get_setting('question_title_limit') > 0 && cjk_strlen($_POST['question_content']) > get_setting('question_title_limit')) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('问题标题字数不得大于') . ' ' . get_setting('question_title_limit') . ' ' . AWS_APP::lang() -> _t('字节')));
		}

		if (!$this -> user_info['permission']['publish_url'] && FORMAT::outside_url_exists($_POST['question_detail'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('你所在的用户组不允许发布站外链接')));
		}

		if (human_valid('question_valid_hour') AND !AWS_APP::captcha() -> is_validate($_POST['seccode_verify'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请填写正确的验证码')));
		}

		// !注: 来路检测后面不能再放报错提示
		if (!valid_post_hash($_POST['post_hash'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('表单来路不正确或内容已提交, 请刷新页面重试')));
		}

		$this -> model('draft') -> delete_draft(1, 'question', $this -> user_id);

		if ($_POST['do_delete'] AND !$this -> user_info['permission']['is_administortar'] AND !$this -> user_info['permission']['is_moderator']) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('对不起, 你没有删除问题的权限')));
		}

		if ($_POST['do_delete']) {
			if ($this -> user_id != $question_info['published_uid']) {
				$this -> model('account') -> send_delete_message($question_info['published_uid'], $question_info['question_content'], $question_info['question_detail']);
			}

			$this -> model('question') -> remove_question($question_info['question_id']);

			H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url('/home/explore/')), 1, null));
		}

		$IS_MODIFY_VERIFIED = TRUE;

		if (!$this -> user_info['permission']['is_administortar'] AND !$this -> user_info['permission']['is_moderator'] AND $question_info['published_uid'] != $this -> user_id) {
			$IS_MODIFY_VERIFIED = FALSE;
		}

		$this -> model('question') -> update_question($question_info['question_id'], $_POST['question_content'], $_POST['question_detail'], $this -> user_id, $IS_MODIFY_VERIFIED, $_POST['modify_reason'], $question_info['anonymous']);

		if ($_POST['category_id']) {
			$this -> model('question') -> update_question_category($question_info['question_id'], $_POST['category_id']);
		}

		if ($this -> user_id != $question_info['published_uid']) {
			$this -> model('question') -> add_focus_question($question_info['question_id'], $this -> user_id);

			$this -> model('notify') -> send($this -> user_id, $question_info['published_uid'], notify_class::TYPE_MOD_QUESTION, notify_class::CATEGORY_QUESTION, $question_info['question_id'], array('from_uid' => $this -> user_id, 'question_id' => $question_info['question_id']));

			$this -> model('email') -> action_email('QUESTION_MOD', $question_info['published_uid'], get_js_url('/question/' . $question_info['question_id']), array('user_name' => $this -> user_info['user_name'], 'question_title' => $question_info['question_content']));
		}

		if ($_POST['category_id'] AND $_POST['category_id'] != $question_info['category_id']) {
			$category_info = $this -> model('system') -> get_category_info(intval($_POST['category_id']));

			ACTION_LOG::save_action($this -> user_id, $question_info['question_id'], ACTION_LOG::CATEGORY_QUESTION, ACTION_LOG::MOD_QUESTION_CATEGORY, $category_info['title'], $category_info['id']);
		}

		if ($_POST['attach_access_key'] AND $IS_MODIFY_VERIFIED) {
			if ($this -> model('publish') -> update_attach('question', $question_info['question_id'], $_POST['attach_access_key'])) {
				ACTION_LOG::save_action($this -> user_id, $question_info['question_id'], ACTION_LOG::CATEGORY_QUESTION, ACTION_LOG::MOD_QUESTION_ATTACH);
			}
		}

		H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url('/tsbm/question/' . $question_info['question_id'] . '?column=log&rf=false')), 1, null));

	}

	public function notifications_list_action() {
		if ($_GET['limit']) {
			$per_page = intval($_GET['limit']);
		} else {
			$per_page = $this -> per_page;
		}

		$list = $this -> model('notify') -> list_notification($this -> user_id, $_GET['flag'], intval($_GET['page']) * $per_page . ', ' . $per_page);

		if (!$list AND $this -> user_info['notification_unread'] != 0) {
			$this -> model('account') -> update_notification_unread($this -> user_id);
		}

		TPL::assign('flag', $_GET['flag']);
		TPL::assign('list', $list);

		// if ($_GET['template'] == 'header_list') {
		// TPL::output("notifications/ajax/header_list");
		// } else if ($_GET['template'] == 'm') {
		// TPL::output('tsbm/ajax/notifications_list');
		// } else {
		// TPL::output("notifications/ajax/list");
		// }

		TPL::output('tsbm/ajax/notifications_list');
	}

}

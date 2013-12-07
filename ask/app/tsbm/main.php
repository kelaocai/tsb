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

class main extends AWS_CONTROLLER {
	public function get_access_rule() {
		$rule_action['rule_type'] = 'black';

		$rule_action['actions'] = array();

		return $rule_action;
	}

	public function setup() {

		HTTP::no_cache_header();
		TPL::import_js(array('js/jquery.2.js', 'js/jquery.form.js', 'js/mobile/framework.js', 'js/mobile/mobile.js', 'js/mobile/aw-mobile-template.js'));
		TPL::import_css('css/tsb/tsbm.css');
	}

	public function index_action() {
		TPL::import_js('js/tsb/flipsnap.min.js');

		TPL::output('tsbm/index');
	}

	public function captcha_action() {
		AWS_APP::captcha() -> generate();
	}

	public function explore_action() {

		$this -> crumb(AWS_APP::lang() -> _t('热门'), '/tsbm/explore/');

		$nav_menu = $this -> model('menu') -> get_nav_menu_list(null, true);

		//TPL::assign('feature_ids', $nav_menu['feature_ids']);

		unset($nav_menu['feature_ids']);

		TPL::assign('content_nav_menu', $nav_menu);

		TPL::assign('sidebar_hot_topics', $this -> model('module') -> sidebar_hot_topics($_GET['category']));

		if ($_GET['feature_id']) {
			TPL::assign('feature_info', $this -> model('feature') -> get_feature_by_id($_GET['feature_id']));
		}

		if ($_GET['category']) {
			TPL::assign('category_info', $this -> model('system') -> get_category_info($_GET['category']));
		}

		TPL::output('tsbm/explore');
	}

	public function test_action() {
		$question_list = $this -> model('question') -> get_questions_list($_GET['page'], $per_page, $_GET['sort_type'], $_GET['topic_id'], $_GET['category'], $_GET['answer_count'], $_GET['day']);

		fb($question_list);
		echo "hello";

	}

	public function question_action() {
		if (!isset($_GET['id'])) {
			HTTP::redirect('/m/explore/');
		}

		if (!$question_id = intval($_GET['id'])) {
			H::redirect_msg(AWS_APP::lang() -> _t('问题不存在或已被删除'), '/m/explore/');
		}

		if ($_GET['notification_id']) {
			$this -> model('notify') -> read_notification($_GET['notification_id'], $this -> user_id);
		}

		if (!$question_info = $this -> model("question") -> get_question_info_by_id($question_id)) {
			H::redirect_msg(AWS_APP::lang() -> _t('问题不存在或已被删除'), '/m/explore/');
		}

		$question_info['redirect'] = $this -> model("question") -> get_redirect($question_info['question_id']);

		if ($question_info['redirect']['target_id']) {
			$target_question = $this -> model("question") -> get_question_info_by_id($question_info['redirect']['target_id']);
		}

		if (is_numeric($_GET['rf']) and $_GET['rf']) {
			if ($from_question = $this -> model("question") -> get_question_info_by_id($_GET['rf'])) {
				$redirect_message[] = AWS_APP::lang() -> _t('从问题') . ' <a href="' . get_js_url('/m/question/' . $_GET['rf'] . '?rf=false') . '">' . $from_question['question_content'] . '</a> ' . AWS_APP::lang() -> _t('跳转而来');
			}
		}

		if ($question_info['redirect'] and !$_GET['rf']) {
			if ($target_question) {
				HTTP::redirect('/m/question/' . $question_info['redirect']['target_id'] . '?rf=' . $question_info['question_id']);
			} else {
				$redirect_message[] = AWS_APP::lang() -> _t('重定向目标问题已被删除, 将不再重定向问题');
			}
		} else if ($question_info['redirect']) {
			if ($target_question) {
				$message = AWS_APP::lang() -> _t('此问题将跳转至') . ' <a href="' . get_js_url('/m/question/' . $question_info['redirect']['target_id'] . '?rf=' . $question_info['question_id']) . '">' . $target_question['question_content'] . '</a>';

				if ($this -> user_id && ($this -> user_info['permission']['is_administortar'] OR $this -> user_info['permission']['is_moderator'] OR (!$this -> question_info['lock'] AND $this -> user_info['permission']['redirect_question']))) {
					$message .= '&nbsp; (<a href="javascript:;" onclick="ajax_request(G_BASE_URL + \'/question/ajax/redirect/\', \'item_id=' . $question_id . '\');">' . AWS_APP::lang() -> _t('撤消重定向') . '</a>)';
				}

				$redirect_message[] = $message;
			} else {
				$redirect_message[] = AWS_APP::lang() -> _t('重定向目标问题已被删除, 将不再重定向问题');
			}
		}

		if ($question_info['has_attach']) {
			$question_info['attachs'] = $this -> model('publish') -> get_attach('question', $question_info['question_id'], 'min');
			$question_info['attachs_ids'] = FORMAT::parse_attachs($question_info['question_detail'], true);
		}

		$this -> model('question') -> update_views($question_id);

		if (get_setting('answer_unique') == 'Y') {
			if ($this -> model('answer') -> has_answer_by_uid($question_id, $this -> user_id)) {
				TPL::assign('user_answered', TRUE);
			}
		}
		//$question_info['question_detail'] = FORMAT::parse_attachs(FORMAT::parse_links(nl2br(FORMAT::parse_markdown($question_info['question_detail']))));
		$str = FORMAT::parse_links(nl2br(FORMAT::parse_markdown($question_info['question_detail'])));
		$question_info['question_detail'] = preg_replace_callback('/\[attach\]([0-9]+)\[\/attach\]/i', 'tsb_parse_attachs_callback', $str);
		TPL::assign('question_id', $question_id);
		TPL::assign('question_info', $question_info);
		TPL::assign('question_focus', $this -> model("question") -> has_focus_question($question_id, $this -> user_id));
		TPL::assign('question_topics', $this -> model('topic') -> get_topics_by_item_id($question_id, 'question'));

		$this -> crumb($question_info['question_content'], '/m/question/' . $question_id);

		TPL::assign('redirect_message', $redirect_message);

		if ($this -> user_id) {
			TPL::assign('question_thanks', $this -> model('question') -> get_question_thanks($question_info['question_id'], $this -> user_id));

			TPL::assign('invite_users', $this -> model('question') -> get_invite_users($question_info['question_id'], array($question_info['published_uid'])));

			//TPL::assign('user_follow_check', $this->model("follow")->user_follow_check($this->user_id, $question_info['published_uid']));

			if ($this -> user_info['draft_count'] > 0) {
				TPL::assign('draft_content', $this -> model('draft') -> get_data($question_info['question_id'], 'answer', $this -> user_id));
			}
		}

		$answer_list = $this -> model('answer') -> get_answer_list_by_question_id($question_info['question_id'], calc_page_limit($_GET['page'], 20), null, 'agree_count DESC, against_count ASC, add_time ASC');
		// 最佳回复预留
		$answers[0] = '';

		if (!is_array($answer_list)) {
			$answer_list = array();
		}

		$answer_ids = array();
		$answer_uids = array();

		foreach ($answer_list as $answer) {
			$answer_ids[] = $answer['answer_id'];
			$answer_uids[] = $answer['uid'];

			if ($answer['has_attach']) {
				$has_attach_answer_ids[] = $answer['answer_id'];
			}
		}

		if (!in_array($question_info['best_answer'], $answer_ids) AND intval($_GET['page']) < 2) {
			$answer_list = array_merge($this -> model('answer') -> get_answer_list_by_question_id($question_info['question_id'], 1, 'answer_id = ' . $question_info['best_answer']), $answer_list);
		}

		if ($answer_ids) {
			$answer_agree_users = $this -> model('answer') -> get_vote_user_by_answer_ids($answer_ids);

			$answer_vote_status = $this -> model('answer') -> get_answer_vote_status($answer_ids, $this -> user_id);

			$answer_users_rated_thanks = $this -> model('answer') -> users_rated('thanks', $answer_ids, $this -> user_id);
			$answer_users_rated_uninterested = $this -> model('answer') -> users_rated('uninterested', $answer_ids, $this -> user_id);
			$answer_attachs = $this -> model('publish') -> get_attachs('answer', $has_attach_answer_ids, 'min');
		}

		foreach ($answer_list as $answer) {
			if ($answer['has_attach']) {
				$answer['attachs'] = $answer_attachs[$answer['answer_id']];

				$answer['insert_attach_ids'] = FORMAT::parse_attachs($answer['answer_content'], true);
			}

			$answer['user_rated_thanks'] = $answer_users_rated_thanks[$answer['answer_id']];
			$answer['user_rated_uninterested'] = $answer_users_rated_uninterested[$answer['answer_id']];

			//$answer['answer_content'] = $this->model('question')->parse_at_user(FORMAT::parse_attachs(FORMAT::parse_markdown($answer['answer_content'])));
			$answer_str = FORMAT::parse_markdown($answer['answer_content']);
			$answer['answer_content'] = preg_replace_callback('/\[attach\]([0-9]+)\[\/attach\]/i', 'tsb_parse_attachs_callback', $answer_str);
			$answer['answer_content'] = $this -> model('question') -> parse_at_user($answer['answer_content']);

			$answer['agree_users'] = $answer_agree_users[$answer['answer_id']];
			$answer['agree_status'] = $answer_vote_status[$answer['answer_id']];

			if ($question_info['best_answer'] == $answer['answer_id'] AND intval($_GET['page']) < 2) {
				$answers[0] = $answer;
			} else {
				$answers[] = $answer;
			}
		}

		if (!$answers[0]) {
			unset($answers[0]);
		}

		if (get_setting('answer_unique') == 'Y') {
			if ($this -> model('answer') -> has_answer_by_uid($question_info['question_id'], $this -> user_id)) {
				TPL::assign('user_answered', TRUE);
			}
		}
		fb($answers, 'aa');
		TPL::assign('answers_list', $answers);

		TPL::assign('question_related_list', $this -> model('question') -> get_related_question_list($question_info['question_id'], $question_info['question_content']));

		$total_page = $question_info['answer_count'] / 20;

		if ($total_page > intval($total_page)) {
			$total_page = intval($total_page) + 1;
		}

		if (!$_GET['page']) {
			$_GET['page'] = 1;
		}

		if ($_GET['page'] < $total_page) {
			$_GET['page'] = $_GET['page'] + 1;

			TPL::assign('next_page', $_GET['page']);
		}

		TPL::output('tsbm/question');
	}

	public function publish_action() {
		if (!$this -> user_id) {
			HTTP::redirect('/tsbm/login/url-' . base64_encode(get_js_url($_SERVER['QUERY_STRING'])));
		}

		if ($_GET['id']) {
			if (!$question_info = $this -> model('question') -> get_question_info_by_id($_GET['id'])) {
				H::redirect_msg(AWS_APP::lang() -> _t('指定问题不存在'));
			}

			if (!$this -> user_info['permission']['is_administortar'] AND !$this -> user_info['permission']['is_moderator'] AND !$this -> user_info['permission']['edit_question']) {
				if ($question_info['published_uid'] != $this -> user_id) {
					H::redirect_msg(AWS_APP::lang() -> _t('你没有权限编辑这个问题'), '/m/question/' . $_GET['id']);
				}
			}

			TPL::assign('question_info', $question_info);
		} else if (!$this -> user_info['permission']['publish_question']) {
			H::redirect_msg(AWS_APP::lang() -> _t('你所在用户组没有权限发布问题'));
		} else if ($this -> is_post() AND $_POST['question_detail']) {
			TPL::assign('question_info', array('question_content' => $_POST['question_content'], 'question_detail' => $_POST['question_detail']));

			$question_info['category_id'] = $_POST['category_id'];
		} else {
			$draft_content = $this -> model('draft') -> get_data(1, 'question', $this -> user_id);

			TPL::assign('question_info', array('question_content' => $_POST['question_content'], 'question_detail' => $draft_content['message']));
		}

		if ($this -> user_info['integral'] < 0 AND get_setting('integral_system_enabled') == 'Y' AND !$_GET['id']) {
			H::redirect_msg(AWS_APP::lang() -> _t('你的剩余积分已经不足以进行此操作'));
		}

		if (!$question_info['category_id'] AND $_GET['category_id']) {
			$question_info['category_id'] = $_GET['category_id'];
		}

		if (get_setting('category_enable') == 'Y') {
			$question_category_list = $this -> model('system') -> build_category_html('question', 0, $question_info['category_id']);
			fb($question_category_list);
			TPL::assign('question_category_list', $question_category_list);
		}

		//TPL::assign('human_valid', human_valid('question_valid_hour'));

		TPL::output('tsbm/publish');
	}

	public function people_action() {

		if (!$this -> user_id) {
			HTTP::redirect('/tsbm/login/url-' . base64_encode(get_js_url($_SERVER['QUERY_STRING'])));
		}

		if (isset($_GET['notification_id'])) {
			$this -> model('notify') -> read_notification($_GET['notification_id'], $this -> user_id);
		}

		//if ((is_numeric($_GET['id']) AND intval($_GET['id']) == $this->user_id AND $this->user_id) OR ($this->user_id AND !$_GET['id']))
		if ($this -> user_id AND !$_GET['id']) {
			$user = $this -> user_info;
		} else {
			if (is_numeric($_GET['id'])) {
				if (!$user = $this -> model('account') -> get_user_info_by_uid($_GET['id'], TRUE)) {
					$user = $this -> model('account') -> get_user_info_by_username($_GET['id'], TRUE);
				}
			} else if ($user = $this -> model('account') -> get_user_info_by_username($_GET['id'], TRUE)) {

			} else {
				$user = $this -> model('account') -> get_user_info_by_url_token($_GET['id'], TRUE);
			}

			if (!$user) {
				H::redirect_msg(AWS_APP::lang() -> _t('用户不存在'), '/m/');
			}

			if (urldecode($user['url_token']) != $_GET['id']) {
				HTTP::redirect('/tsbm/people/' . $user['url_token']);
			}

			$this -> model('people') -> update_views($user['uid']);
		}

		TPL::assign('user', $user);

		TPL::assign('user_follow_check', $this -> model('follow') -> user_follow_check($this -> user_id, $user['uid']));

		TPL::assign('reputation_topics', $this -> model('people') -> get_user_reputation_topic($user['uid'], $user['reputation'], 12));
		TPL::assign('fans_list', $this -> model('follow') -> get_user_fans($user['uid'], 5));
		TPL::assign('friends_list', $this -> model('follow') -> get_user_friends($user['uid'], 5));
		TPL::assign('focus_topics', $this -> model('topic') -> get_focus_topic_list($user['uid'], 10));

		$user_actions_questions = $this -> model('account') -> get_user_actions($user['uid'], 5, ACTION_LOG::ADD_QUESTION, $this -> user_id);
		$user_actions_answers = $this -> model('account') -> get_user_actions($user['uid'], 5, ACTION_LOG::ANSWER_QUESTION, $this -> user_id);
		fb($user_actions_answers, '$user_actions_answers');
		TPL::assign('user_actions_questions', $user_actions_questions);
		TPL::assign('user_actions_answers', $user_actions_answers);

		$this -> crumb(AWS_APP::lang() -> _t('%s 的个人主页', $user['user_name']), '/tsbm/people/' . $user['url_token']);

		TPL::output('tsbm/people');
	}

	public function logout_action($return_url = null) {
		$this -> model('account') -> setcookie_logout();
		// 清除 COOKIE
		$this -> model('account') -> setsession_logout();
		// 清除 Session

		$this -> model('admin') -> admin_logout();

		if ($_GET['return_url']) {
			$url = strip_tags(urldecode($_GET['return_url']));
		} else if (!$return_url) {
			$url = '/tsbm';
		} else {
			$url = $return_url;
		}

		if (get_setting('ucenter_enabled') == 'Y') {
			if ($uc_uid = $this -> model('ucenter') -> is_uc_user($this -> user_info['email'])) {
				$sync_code = $this -> model('ucenter') -> sync_logout($uc_uid);
			}

			H::redirect_msg(AWS_APP::lang() -> _t('您已退出站点, 现在将以游客身份进入站点, 请稍候...') . $sync_code, $url);
		} else {
			HTTP::redirect($url);
		}
	}

	public function login_action() {

		$url = base64_decode($_GET['url']);

		if (($this -> user_id AND !$_GET['weixin_id']) OR $this -> user_info['weixin_id']) {
			if ($url) {
				header('Location: ' . $url);
			} else {
				HTTP::redirect('/tsbm/');
			}
		}

		if ($url) {
			$return_url = $url;
		} else if (strstr($_SERVER['HTTP_REFERER'], '/tsbm/')) {
			$return_url = $_SERVER['HTTP_REFERER'];
		} else {
			$return_url = get_js_url('/tsbm/');
		}

		//TPL::assign('body_class', 'explore-body');
		TPL::assign('return_url', strip_tags($return_url));

		$this -> crumb(AWS_APP::lang() -> _t('登录'), '/m/login/');

		TPL::output('tsbm/login');
	}

	public function register_action() {
		if (($this -> user_id AND !$_GET['weixin_id']) OR $this -> user_info['weixin_id']) {
			if ($url) {
				header('Location: ' . $url);
			} else {
				HTTP::redirect('/tsbm/');
			}
		}

		if ($this -> user_id AND $_GET['invite_question_id']) {
			if ($invite_question_id = intval($_GET['invite_question_id'])) {
				HTTP::redirect('/question/' . $invite_question_id);
			}
		}

		if (get_setting('invite_reg_only') == 'Y' AND !$_GET['icode']) {
			H::redirect_msg(AWS_APP::lang() -> _t('本站只能通过邀请注册'), '/');
		}

		if ($_GET['icode']) {
			if ($this -> model('invitation') -> check_code_available($_GET['icode'])) {
				TPL::assign('icode', $_GET['icode']);
			} else {
				H::redirect_msg(AWS_APP::lang() -> _t('邀请码无效或已经使用，请使用新的邀请码'), '/');
			}
		}

		$this -> crumb(AWS_APP::lang() -> _t('注册'), '/m/register/');

		TPL::assign('body_class', 'explore-body');

		TPL::output('tsbm/register');
	}
	
	public function search_action()
	{
		
		if ($_POST['q'])
		{
			HTTP::redirect('/tsbm/search/q-' . base64_encode($_POST['q']));
			fb('aa');
		}
		
		$keyword = htmlspecialchars(base64_decode($_GET['q']));
		
		$this->crumb($keyword, 'tsbm/search/q-' . urlencode($keyword));
		
		if (!$keyword)
		{
			//HTTP::redirect('/tsbm/search/');
			TPL::output('tsbm/search');	
		}
		
		TPL::assign('keyword', $keyword);
		
		$split_keyword=implode(' ', $this->model('system')->analysis_keyword($keyword));
	
		TPL::assign('split_keyword', $split_keyword);
		
		//fb($aa,'aa');
		
		TPL::output('tsbm/search');
	}

}

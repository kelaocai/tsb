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

class ajax extends AWS_CONTROLLER {
	public function get_access_rule() {
		$rule_action['rule_type'] = 'white';
		//黑名单,黑名单中的检查  'white'白名单,白名单以外的检查

		$rule_action['actions'] = array('check_username', 'check_email', 'check_mobile', 'register_process', 'login_process', 'register_agreement', 'send_valid_mail', 'valid_email_active', 'request_find_password', 'find_password_modify', 'send_mobile_authcode');

		return $rule_action;
	}

	public function setup() {
		HTTP::no_cache_header();
	}

	public function check_username_action() {
		if ($this -> model('account') -> check_username_char($_GET['username'])) {
			switch(get_setting('username_rule')) {
				default :
					{
						H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('用户名不符合规则')));
					}
					break;
			}

			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('用户名不符合规则')));
		}

		if ($this -> model('account') -> check_username_sensitive_words($_GET['username']) || $this -> model('account') -> check_username($_GET['username'])) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('用户名已被注册')));
		}

		H::ajax_json_output(AWS_APP::RSM(null, 1, AWS_APP::lang() -> _t('该用户名可以使用')));
	}

	public function check_mobile_action() {
		if (!$_GET['mobile']) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('请输入手机号码')));
		}

		if ($this -> model('account') -> check_mobile($_GET['mobile'])) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('手机号码已被使用')));
		} else {

			H::ajax_json_output(AWS_APP::RSM(null, 1, AWS_APP::lang() -> _t('该手机号码可以使用')));
		}

	}

	public function check_email_action() {
		if (!$_GET['email']) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('请输入邮箱地址')));
		}

		if ($this -> model('account') -> check_email($_GET['email'])) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('邮箱地址已被使用')));
		}

		H::ajax_json_output(AWS_APP::RSM(null, 1, AWS_APP::lang() -> _t('该邮箱可以使用')));
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

		//非手机端注册需要验证手机号和验证码
		if (!is_mobile()) {

			if (trim($_POST['mobile']) == '') {
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('请输入手机号码')));
			} else if ($this -> model('account') -> check_mobile_char($_POST['mobile'])) {
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('请输入11位有效手机号码')));
			} else if ($this -> model('account') -> check_mobile($_POST['mobile'])) {
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('手机号码已被使用')));
			}
			//检查手机验证码
			if ($_POST['authcode'] != AWS_APP::session() -> authcode) {
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('手机验证码不正确')));
			}

			// 检查验证码
			if (!($_POST['fromuid'] OR $_POST['icode']) AND !AWS_APP::captcha() -> is_validate($_POST['seccode_verify']) AND get_setting('register_seccode') == 'Y') {
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('请填写正确的验证码')));
			}

		}

		//注册环节暂时不提供邮箱
		/*
		 if ($this->model('account')->check_email($_POST['email']))
		 {
		 H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('E-Mail 已经被使用, 或格式不正确')));
		 }
		 */

		if (strlen($_POST['password']) < 6 OR strlen($_POST['password']) > 16) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('密码长度不符合规则')));
		}

		if (!$_POST['agreement_chk']) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('你必需同意用户协议才能继续')));
		}

		//ucenter
		if (get_setting('ucenter_enabled') == 'Y') {
			//提交注册信息
			//手机用户简化注册流程，注册初期不用提供email信息，先置系统默认值 default@tongshibang.com
			//$result = $this->model('ucenter')->register($_POST['user_name'], $_POST['password'], $_POST['email'], $email_valid);
			$result = $this -> model('ucenter') -> register($_POST['user_name'], $_POST['password'], 'default@tongshibang.com', $email_valid);

			if (is_array($result)) {
				$uid = $result['user_info']['uid'];
				//预留：补充一下手机号码信息
				$this -> model('account') -> update_users_fields(array('mobile' => $_POST['mobile']), $uid);
				//清除uc_center注册要求的默认email信息
				$this -> model('account') -> update_users_fields(array('email' => ''), $uid);
			} else {
				H::ajax_json_output(AWS_APP::RSM(null, -1, $result));
			}
		}
		//非ucenter
		else {
			//提交注册信息
			//手机用户简化注册流程，注册初期不用提供email信息，先置系统默认值 default@tongshibang.com
			// $uid = $this->model('account')->user_register($_POST['user_name'], $_POST['password'], $_POST['email'], $email_valid);
			$uid = $this -> model('account') -> user_register($_POST['user_name'], $_POST['password'], '', $_POST['mobile'], $email_valid);
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

			//激活
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

			//微信绑定
			if ($_POST['weixin_id'] AND $_POST['_is_mobile']) {
				if (!$this -> model('account') -> get_user_info_by_weixin_id($_POST['weixin_id'])) {
					$this -> model('account') -> update_users_fields(array('weixin_id' => $_POST['weixin_id'], ), $uid);
				}

				H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url('/m/weixin_bind_success/')), 1, null));
			}

			if ($_POST['_is_mobile']) {
				H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url('/m/')), 1, null));
			}
		} else {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('系统错误, 请联系管理员')));
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

			// 默认记住用户名
			HTTP::set_cookie('r_uname', $_POST['user_name'], time() + 60 * 60 * 24 * 30);

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
				$url = get_js_url('/m/');
			}

			if (get_setting('ucenter_enabled') == 'Y') {
				$url = get_js_url('/account/sync_login/url-' . @base64_encode($url));
			}

			H::ajax_json_output(AWS_APP::RSM(array('url' => $url), 1, null));
		}
	}

	public function register_agreement_action() {
		H::ajax_json_output(AWS_APP::RSM(null, 1, nl2br(get_setting('register_agreement'))));
	}

	public function get_weibo_bind_status_action() {
		if (get_setting('sina_weibo_enabled') == 'Y') {
			if ($sina_weibo = $this -> model("sina_weibo") -> get_users_sina_by_uid($this -> user_id)) {
				$data['sina_weibo']['name'] = $sina_weibo['name'];
			}
		}

		if (get_setting('qq_t_enabled') == 'Y') {
			if ($qq_weibo = $this -> model("qq_weibo") -> get_users_qq_by_uid($this -> user_id)) {
				$data['qq_weibo']['name'] = $qq_weibo['name'];
			}
		}

		if (get_setting('qq_login_enabled') == 'Y') {
			if ($qq = $this -> model("qq") -> get_user_info_by_uid($this -> user_id)) {
				$data['qq']['name'] = $qq['nick'];
			}
		}

		$data['sina_weibo']['enabled'] = get_setting('sina_weibo_enabled');
		$data['qq_weibo']['enabled'] = get_setting('qq_t_enabled');
		$data['qq']['enabled'] = get_setting('qq_login_enabled');

		H::ajax_json_output(AWS_APP::RSM($data, 1, null));
	}

	public function welcome_message_template_action() {
		//职位信息
		TPL::assign('job_list', $this -> model('work') -> get_jobs_list());

		TPL::output('account/ajax/welcome_message_template');
	}

	public function welcome_get_topics_action() {
		if ($topics_list = $this -> model('topic') -> get_topic_list(null, 'RAND()', 8)) {
			foreach ($topics_list as $key => $topic) {
				$topics_list[$key]['has_focus'] = $this -> model('topic') -> has_focus_topic($this -> user_id, $topic['topic_id']);
			}
		}
		TPL::assign('topics_list', $topics_list);

		TPL::output('account/ajax/welcome_get_topics');
	}

	public function welcome_get_users_action() {
		if ($welcome_recommend_users = trim(rtrim(get_setting('welcome_recommend_users'), ','))) {
			$welcome_recommend_users = explode(',', $welcome_recommend_users);

			$users_list = $this -> model('account') -> fetch_all('users', "user_name IN('" . implode("','", $welcome_recommend_users) . "')", 'RAND()', 6);
		}

		if (!$users_list) {
			$users_list = $this -> model('account') -> get_activity_random_users(6);
		}

		if ($users_list) {
			foreach ($users_list as $key => $val) {
				$users_list[$key]['follow_check'] = $this -> model('follow') -> user_follow_check($this -> user_id, $val['uid']);
			}
		}

		TPL::assign('users_list', $users_list);

		TPL::output('account/ajax/welcome_get_users');
	}

	public function clean_first_login_action() {
		$this -> model('account') -> clean_first_login($this -> user_id);

		die('success');
	}

	public function delete_draft_action() {
		if (!$_POST['item_id'] OR !$_POST['type']) {
			die ;
		}

		$this -> model('draft') -> delete_draft($_POST['item_id'], $_POST['type'], $this -> user_id);

		H::ajax_json_output(AWS_APP::RSM(null, 1, null));
	}

	public function save_draft_action() {
		if (!$_GET['item_id'] OR !$_GET['type'] OR !$_POST) {
			die ;
		}

		$this -> model('draft') -> save_draft($_GET['item_id'], $_GET['type'], $this -> user_id, $_POST);

		H::ajax_json_output(AWS_APP::RSM(null, 1, AWS_APP::lang() -> _t('已保存草稿, %s', date('H:i:s', time()))));
	}

	//发送手机验证码
	function send_mobile_authcode_action() {

		$mobile = $_GET['mobile'];
		
		AWS_APP::session()->auth_mobile=$mobile;

		//校验手机号码合法性
		if (!$mobile) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('请输入手机号码')));
		}

		if ($this -> model('account') -> check_mobile_char($mobile)) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('请输入11位有效手机号码')));
		}

		if ($this -> model('account') -> check_mobile($mobile)) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('手机号码已被使用')));
		} else {
			//生成验证码
			$mobile_authcode = load_class('tsb_common') -> genauthcode();

			//发送验证码
			

			if (is_mobile()&!$_GET['resend']) {
				H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url('/m/register_two/')), 1, null));
			}else{
				H::ajax_json_output(AWS_APP::RSM(null, 1, AWS_APP::lang() -> _t('手机验证码: %s', $mobile_authcode)));
			}
		}
	}

	function send_valid_mail_action() {
		if (!$this -> user_id) {
			if (H::valid_email(AWS_APP::session() -> valid_email)) {
				$this -> user_info = $this -> model('account') -> get_user_info_by_email(AWS_APP::session() -> valid_email);
				$this -> user_id = $this -> user_info['uid'];
			}
		}

		if (!H::valid_email($this -> user_info['email'])) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('错误, 用户没有提供 E-mail')));
		}

		if ($this -> user_info['valid_email'] == 1) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('用户邮箱已经认证')));
		}

		if ($this -> model('active') -> new_valid_email($this -> user_id)) {
			H::ajax_json_output(AWS_APP::RSM(null, 1, AWS_APP::lang() -> _t('邮件发送成功')));
		}
	}

	function valid_email_active_action() {
		if (!$active_data = $this -> model('active') -> get_active_code_row($_POST['active_code'], 21)) {
			H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url('/'), ), 1, AWS_APP::lang() -> _t('激活失败, 无效的链接')));
		}

		if ($active_data['active_time'] || $active_data['active_ip'] || $active_data['active_expire']) {
			H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url('/account/login/'), ), 1, AWS_APP::lang() -> _t('帐户已激活, 请返回登录')));
		}

		if (!$user_info = $this -> model('account') -> get_user_info_by_uid($active_data['uid'])) {
			H::ajax_json_output(AWS_APP::RSM(array(), -1, AWS_APP::lang() -> _t('激活失败, 无效的链接')));
		}

		if ($user_info['valid_email']) {
			H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url('/account/login/'), ), 1, AWS_APP::lang() -> _t('帐户已通过邮箱验证，请返回登录')));
		}

		if ($user_id = $this -> model('active') -> active_code_active($_POST['active_code'], 21)) {
			if (AWS_APP::session() -> valid_email) {
				unset(AWS_APP::session() -> valid_email);
			}

			$this -> model('account') -> update_users_fields(array('valid_email' => 1, ), $active_data['uid']);

			if ($user_info['group_id'] == 3) {
				$this -> model('account') -> update_users_fields(array('group_id' => 4, ), $active_data['uid']);
			}

			// 帐户激活成功，切换为登录状态跳转至首页
			$this -> model('account') -> setsession_logout();
			$this -> model('account') -> setcookie_logout();

			$this -> model('account') -> update_user_last_login($user_info['uid']);

			HTTP::set_cookie('r_uname', $user_info['email'], time() + 60 * 60 * 24 * 30);

			$this -> model('account') -> setcookie_login($user_info['uid'], $user_info['user_name'], $user_info['password'], $user_info['salt'], null, false);

			$this -> model('account') -> welcome_message($user_info['uid'], $user_info['user_name'], $user_info['email']);

			$url = $user_info['is_first_login'] ? '/first_login-TRUE' : '/';

			H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url($url)), 1, null));
		}
	}

	function request_find_password_action() {
		if (!H::valid_email($_POST['email'])) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('请填写正确的邮箱地址')));
		}

		if (!AWS_APP::captcha() -> is_validate($_POST['seccode_verify'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请填写正确的验证码')));
		}

		if (!$user_info = $this -> model('account') -> get_user_info_by_email($_POST['email'])) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('邮箱地址错误或帐号不存在')));
		}

		$this -> model('active') -> new_find_password($user_info['uid']);

		AWS_APP::session() -> find_password = $_POST['email'];

		H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url('/account/find_password/process_success/')), 1, null));
	}

	function find_password_modify_action() {
		if (!AWS_APP::captcha() -> is_validate($_POST['seccode_verify'])) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('请填写正确的验证码')));
		}

		$active_data = $this -> model('active') -> get_active_code_row($_POST['active_code'], 11);

		if ($active_data) {
			if ($active_data['active_time'] || $active_data['active_ip'] || $active_data['active_expire']) {
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('链接已失效，请重新找回密码')));
			}
		} else {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('链接已失效，请重新找回密码')));
		}

		if (empty($_POST['password'])) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('请输入密码')));
		}

		if ($_POST['password'] != $_POST['re_password']) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('两次输入的密码不一致')));
		}

		if (!$uid = $this -> model('active') -> active_code_active($_POST['active_code'], 11)) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('链接已失效，请重新找回密码')));
		}

		$user_info = $this -> model('account') -> get_user_info_by_uid($uid);

		$this -> model('account') -> update_user_password_ingore_oldpassword($_POST['password'], $uid, $user_info['salt']);

		$this -> model('account') -> update_users_fields(array('valid_email' => 1), $uid);

		if ($user_info['group_id'] == 3) {
			$this -> model('account') -> update_users_fields(array('group_id' => 4, ), $active_data['uid']);
		}

		$this -> model("account") -> setcookie_logout();

		$this -> model("account") -> setsession_logout();

		unset(AWS_APP::session() -> find_password);

		H::ajax_json_output(AWS_APP::RSM(array('url' => get_js_url('/account/login/'), ), 1, AWS_APP::lang() -> _t('密码修改成功, 请返回登录')));
	}

	function avatar_upload_action() {
		AWS_APP::upload() -> initialize(array('allowed_types' => 'jpg,jpeg,png,gif', 'upload_path' => get_setting('upload_dir') . '/avatar/' . $this -> model('account') -> get_avatar($this -> user_id, '', 1), 'is_image' => TRUE, 'max_size' => get_setting('upload_avatar_size_limit'), 'file_name' => $this -> model('account') -> get_avatar($this -> user_id, '', 2), 'encrypt_name' => FALSE)) -> do_upload('user_avatar');

		if (AWS_APP::upload() -> get_error()) {
			switch (AWS_APP::upload()->get_error()) {
				default :
					H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('错误代码') . ': ' . AWS_APP::upload() -> get_error()));
					break;

				case 'upload_invalid_filetype' :
					H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('文件类型无效')));
					break;

				case 'upload_invalid_filesize' :
					H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('文件尺寸过大, 最大允许尺寸为 %s KB', get_setting('upload_size_limit'))));
					break;
			}
		}

		if (!$upload_data = AWS_APP::upload() -> data()) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('上传失败, 请与管理员联系')));
		}

		if ($upload_data['is_image'] == 1) {
			foreach (AWS_APP::config()->get('image')->avatar_thumbnail AS $key => $val) {
				$thumb_file[$key] = $upload_data['file_path'] . $this -> model('account') -> get_avatar($this -> user_id, $key, 2);

				AWS_APP::image() -> initialize(array('quality' => 90, 'source_image' => $upload_data['full_path'], 'new_image' => $thumb_file[$key], 'width' => $val['w'], 'height' => $val['h'])) -> resize();
			}
		}

		$update_data['avatar_file'] = $this -> model('account') -> get_avatar($this -> user_id, null, 1) . basename($thumb_file['min']);

		// 更新主表
		$this -> model('account') -> update_users_fields($update_data, $this -> user_id);

		if (!$this -> model('integral') -> fetch_log($this -> user_id, 'UPLOAD_AVATAR')) {
			$this -> model('integral') -> process($this -> user_id, 'UPLOAD_AVATAR', round((get_setting('integral_system_config_profile') * 0.2)), '上传头像');
		}

		H::ajax_json_output(AWS_APP::RSM(array('preview' => get_setting('upload_url') . '/avatar/' . $this -> model('account') -> get_avatar($this -> user_id, null, 1) . basename($thumb_file['max'])), 1, null));
	}

	function add_edu_action() {
		$school_name = htmlspecialchars($_POST['school_name']);
		$education_years = intval($_POST['education_years']);
		$departments = htmlspecialchars($_POST['departments']);

		if (empty($_POST['school_name'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请输入学校名称')));
		}

		if (empty($_POST['departments'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请输入院系')));
		}

		if ($_POST['education_years'] == AWS_APP::lang() -> _t('请选择') OR !$_POST['education_years']) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请选择入学年份')));
		}

		if (preg_match('/\//is', $_POST['school_name'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('学校名称不能包含 /')));
		}

		if (preg_match('/\//is', $_POST['departments'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('院系名称不能包含 /')));
		}

		if (get_setting('auto_create_social_topics') == 'Y') {
			$this -> model('topic') -> save_topic(0, $_POST['school_name']);
			$this -> model('topic') -> save_topic(0, $_POST['departments']);
		}

		$edu_id = $this -> model('education') -> add_education_experience($this -> user_id, $school_name, $education_years, $departments);

		if (!$this -> model('integral') -> fetch_log($this -> user_id, 'UPDATE_EDU')) {
			$this -> model('integral') -> process($this -> user_id, 'UPDATE_EDU', round((get_setting('integral_system_config_profile') * 0.2)), AWS_APP::lang() -> _t('完善教育经历'));
		}

		H::ajax_json_output(AWS_APP::RSM(array('id' => $edu_id), 1, null));

	}

	function remove_edu_action() {
		$this -> model('education') -> del_education_experience($_POST['id'], $this -> user_id);

		H::ajax_json_output(AWS_APP::RSM(null, 1, null));

	}

	function add_work_action() {
		$job_id = intval($_POST['job_id']);
		$company_name = htmlspecialchars($_POST['company_name']);

		$start_year = intval($_POST['start_year']);
		$end_year = intval($_POST['end_year']);

		if (!$company_name) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请输入公司名称')));
		}

		if (!$job_id) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请选择职位')));
		}

		if (!$start_year OR !$end_year) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请选择工作时间')));
		}

		if (preg_match('/\//is', $_POST['company_name'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('公司名称不能包含 /')));
		}

		if (get_setting('auto_create_social_topics') == 'Y') {
			$this -> model('topic') -> save_topic(0, $_POST['company_name']);
		}

		$work_id = $this -> model('work') -> add_work_experience($this -> user_id, $start_year, $end_year, $company_name, $job_id);

		if (!$this -> model('integral') -> fetch_log($this -> user_id, 'UPDATE_WORK')) {
			$this -> model('integral') -> process($this -> user_id, 'UPDATE_WORK', round((get_setting('integral_system_config_profile') * 0.2)), AWS_APP::lang() -> _t('完善工作经历'));
		}

		H::ajax_json_output(AWS_APP::RSM(array('id' => $work_id), 1, null));
	}

	function remove_work_action() {
		$this -> model('work') -> del_work_experience($_POST['id'], $this -> user_id);

		H::ajax_json_output(AWS_APP::RSM(null, 1, null));
	}

	//修改教育经历
	function edit_edu_action() {
		if (empty($_POST['school_name'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请输入学校名称')));
		}

		if (empty($_POST['departments'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请输入院系')));
		}

		if (!$_POST['education_years']) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请选择入学年份')));
		}

		$update_data['school_name'] = htmlspecialchars($_POST['school_name']);
		$update_data['education_years'] = intval($_POST['education_years']);
		$update_data['departments'] = htmlspecialchars($_POST['departments']);

		if (preg_match('/\//is', $_POST['school_name'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('学校名称不能包含 /')));
		}

		if (preg_match('/\//is', $_POST['departments'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('院系名称不能包含 /')));
		}

		if (get_setting('auto_create_social_topics') == 'Y') {
			$this -> model('topic') -> save_topic(0, $_POST['school_name']);
			$this -> model('topic') -> save_topic(0, $_POST['departments']);
		}

		$this -> model('education') -> update_education_experience($update_data, $_GET['id'], $this -> user_id);

		H::ajax_json_output(AWS_APP::RSM(null, 1, null));
	}

	function edit_work_action() {
		if (!$_POST['company_name']) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请输入公司名称')));
		}

		if (!$_POST['job_id']) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请选择职位')));
		}

		if (!$_POST['start_year'] OR !$_POST['end_year']) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请选择工作时间')));
		}

		$update_data['job_id'] = intval($_POST['job_id']);
		$update_data['company_name'] = htmlspecialchars($_POST['company_name']);

		$update_data['start_year'] = intval($_POST['start_year']);
		$update_data['end_year'] = intval($_POST['end_year']);

		if (preg_match('/\//is', $_POST['company_name'])) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('公司名称不能包含 /')));
		}

		if (get_setting('auto_create_social_topics') == 'Y') {
			$this -> model('topic') -> save_topic(0, $_POST['company_name']);
		}

		$this -> model('work') -> update_work_experience($update_data, $_GET['id'], $this -> user_id);

		H::ajax_json_output(AWS_APP::RSM(null, 1, null));
	}

	function privacy_setting_action() {
		if ($notify_actions = $this -> model('notify') -> notify_action_details) {
			$notification_setting = array();

			foreach ($notify_actions as $key => $val) {
				if (!isset($_POST['notification_settings'][$key]) && $val['user_setting']) {
					$notification_setting[] = intval($key);
				}
			}
		}

		$email_settings = array('FOLLOW_ME' => 'N', 'QUESTION_INVITE' => 'N', 'NEW_ANSWER' => 'N', 'NEW_MESSAGE' => 'N', 'QUESTION_MOD' => 'N', );

		if ($_POST['email_settings']) {
			foreach ($_POST['email_settings'] AS $key => $val) {
				unset($email_settings[$val]);
			}
		}

		$this -> model('account') -> update_users_fields(array('email_settings' => serialize($email_settings), 'weibo_visit' => intval($_POST['weibo_visit']), 'inbox_recv' => intval($_POST['inbox_recv'])), $this -> user_id);

		$this -> model('account') -> update_notification_setting_fields($notification_setting, $this -> user_id);

		H::ajax_json_output(AWS_APP::RSM(null, 1, null));
	}

	function profile_setting_action() {
		if (!$this -> user_info['user_name'] OR $this -> user_info['user_name'] == $this -> user_info['email'] AND $_POST['user_name']) {
			$update_data['user_name'] = htmlspecialchars(trim($_POST['user_name']));

			if ($check_result = $this -> model('account') -> check_username_char($_POST['user_name'])) {
				H::ajax_json_output(AWS_APP::RSM(null, '-1', $check_result));
			}
		}

		if ($_POST['url_token'] AND $_POST['url_token'] != $this -> user_info['url_token']) {
			if ($this -> user_info['url_token_update'] AND $this -> user_info['url_token_update'] > (time() - 3600 * 24 * 30)) {
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('你距离上次修改个性网址未满 30 天')));
			}

			if (!preg_match("/^(?!__)[a-zA-Z0-9_]+$/i", $_POST['url_token'])) {
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('个性网址只允许输入英文或数字')));
			}

			if ($this -> model('account') -> check_url_token($_POST['url_token'], $this -> user_id)) {
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('个性网址已经被占用请更换一个')));
			}

			if (preg_match("/^[\d]+$/i", $_POST['url_token'])) {
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('个性网址不允许为纯数字')));
			}

			$this -> model('account') -> update_url_token($_POST['url_token'], $this -> user_id);
		}

		if ($update_data['user_name'] and $this -> model('account') -> check_username($update_data['user_name']) and $this -> user_info['user_name'] != $update_data['user_name']) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('已经存在相同的姓名, 请重新填写')));
		}

		if (!H::valid_email($this -> user_info['email'])) {
			if (!H::valid_email($_POST['email'])) {
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请输入正确的 E-Mail 地址')));
			}

			if ($this -> model('account') -> check_email($_POST['email'])) {
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('邮箱已经存在, 请使用新的邮箱')));
			}

			$update_data['email'] = $_POST['email'];

			$this -> model('active') -> new_valid_email($this -> user_id, $_POST['email']);
		}

		if ($_POST['common_email']) {
			if (!H::valid_email($_POST['common_email'])) {
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请输入正确的常用邮箱地址')));
			}

			$update_data['common_email'] = $_POST['common_email'];
		}

		$update_data['sex'] = intval($_POST['sex']);

		$update_data['province'] = htmlspecialchars($_POST['province']);
		$update_data['city'] = htmlspecialchars($_POST['city']);

		if ($_POST['birthday_y']) {
			$update_data['birthday'] = intval(strtotime(intval($_POST['birthday_y']) . '-' . intval($_POST['birthday_m']) . '-' . intval($_POST['birthday_d'])));
		}

		$update_attrib_data['signature'] = htmlspecialchars($_POST['signature']);
		$update_data['job_id'] = intval($_POST['job_id']);

		if ($_POST['signature'] AND !$this -> model('integral') -> fetch_log($this -> user_id, 'UPDATE_SIGNATURE')) {
			$this -> model('integral') -> process($this -> user_id, 'UPDATE_SIGNATURE', round((get_setting('integral_system_config_profile') * 0.1)), AWS_APP::lang() -> _t('完善一句话介绍'));
		}

		$update_attrib_data['qq'] = htmlspecialchars($_POST['qq']);
		$update_attrib_data['homepage'] = htmlspecialchars($_POST['homepage']);
		//手机在注册的时候已经绑定，此处不修改
		//$update_data['mobile'] = htmlspecialchars($_POST['mobile']);

		if (($update_attrib_data['qq'] OR $update_attrib_data['homepage'] OR $update_data['mobile']) AND !$this -> model('integral') -> fetch_log($this -> user_id, 'UPDATE_CONTACT')) {
			$this -> model('integral') -> process($this -> user_id, 'UPDATE_CONTACT', round((get_setting('integral_system_config_profile') * 0.1)), AWS_APP::lang() -> _t('完善联系资料'));
		}

		if (get_setting('auto_create_social_topics') == 'Y') {
			if ($_POST['city']) {
				$this -> model('topic') -> save_topic(0, $_POST['city']);
			}

			if ($_POST['province']) {
				$this -> model('topic') -> save_topic(0, $_POST['province']);
			}
		}

		// 更新主表
		$this -> model('account') -> update_users_fields($update_data, $this -> user_id);

		// 更新从表
		$this -> model('account') -> update_users_attrib_fields($update_attrib_data, $this -> user_id);

		$this -> model('account') -> set_default_timezone($_POST['default_timezone'], $this -> user_id);

		H::ajax_json_output(AWS_APP::RSM(null, 1, null));
	}

	function modify_password_action() {
		if (!$_POST['old_password']) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请输入当前密码')));
		}

		if ($_POST['password'] != $_POST['re_password']) {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请输入相同的确认密码')));
		}

		if (strlen($_POST['password']) < 6 OR strlen($_POST['password']) > 16) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('密码长度不符合规则')));
		}

		if (get_setting('ucenter_enabled') == 'Y') {
			if ($this -> model('ucenter') -> is_uc_user($this -> user_info['email'])) {
				$result = $this -> model('ucenter') -> user_edit($this -> user_id, $this -> user_info['user_name'], $_POST['old_password'], $_POST['password']);

				if ($result !== 1) {
					H::ajax_json_output(AWS_APP::RSM(null, -1, $result));
				}
			}
		}

		if ($this -> model('account') -> update_user_password($_POST['old_password'], $_POST['password'], $this -> user_id, $this -> user_info['salt'])) {
			H::ajax_json_output(AWS_APP::RSM(null, 1, AWS_APP::lang() -> _t('密码修改成功, 请牢记新密码')));
		} else {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请输入正确的当前密码')));
		}
	}

	public function areas_json_data_action() {
		readfile(ROOT_PATH . 'static/js/areas.js');
	}

	public function integral_log_action() {
		if ($log = $this -> model('integral') -> fetch_all('integral_log', 'uid = ' . $this -> user_id, 'time DESC', (intval($_GET['page']) * 50) . ', 50')) {
			foreach ($log AS $key => $val) {
				$parse_items[$val['id']] = array('item_id' => $val['item_id'], 'action' => $val['action']);
			}

			TPL::assign('log', $log);
			TPL::assign('log_detail', $this -> model('integral') -> parse_log_item($parse_items));
		}

		TPL::output('account/ajax/integral_log');
	}

	public function verify_action() {
		if ($this -> is_post() AND !$this -> user_info['verified']) {
			if (trim($_POST['name']) == '') {
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('请输入真实姓名或企业名称')));
			}

			if (trim($_POST['reason']) == '') {
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang() -> _t('请输入申请认证说明')));
			}

			if ($_FILES['attach']['name']) {
				AWS_APP::upload() -> initialize(array('allowed_types' => 'jpg,png,gif', 'upload_path' => get_setting('upload_dir') . '/verify', 'is_image' => FALSE, 'encrypt_name' => TRUE)) -> do_upload('attach');

				if (AWS_APP::upload() -> get_error()) {
					switch (AWS_APP::upload()->get_error()) {
						default :
							H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('错误代码') . ': ' . AWS_APP::upload() -> get_error()));
							break;

						case 'upload_invalid_filetype' :
							H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('文件类型无效')));
							break;
					}
				}

				if (!$upload_data = AWS_APP::upload() -> data()) {
					H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('上传失败, 请与管理员联系')));
				}
			}

			$this -> model('verify') -> add_apply($this -> user_id, $_POST['name'], $_POST['reason'], $_POST['type'], array('id_code' => htmlspecialchars($_POST['id_code']), 'contact' => htmlspecialchars($_POST['contact'])), basename($upload_data['full_path']));

			$recipient_uid = get_setting('report_message_uid') ? get_setting('report_message_uid') : 1;

			$this -> model('message') -> send_message($this -> user_id, $recipient_uid, null, AWS_APP::lang() -> _t('有新的认证请求, 请登录后台查看处理') . ': ' . get_setting('base_url') . '/?/admin/user_manage/verify_approval_list/');
		}

		H::ajax_json_output(AWS_APP::RSM(null, 1, null));
	}

	public function clean_user_recommend_cache_action() {
		AWS_APP::cache() -> delete('user_recommend_' . $this -> user_id);
	}

	public function binding_weixin_action() {
		$valid_code = $this -> model('weixin') -> create_weixin_valid($this -> user_id);

		H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang() -> _t('请发送 BIND%s 到微信公众帐号完成绑定', $valid_code)));
	}

	public function unbinding_weixin_action() {
		$this -> model('weixin') -> weixin_unbind($this -> user_info['weixin_id']);

		H::ajax_json_output(AWS_APP::RSM(null, 1, null));
	}

}
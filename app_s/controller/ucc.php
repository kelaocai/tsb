<?php
class ucc extends spController {
	
	function login() {
		require (APP_PATH . "/include/config.php");
		require (APP_PATH . "/uc_client/client.php");
		if (empty($_POST['submit'])) {
			//登录表单
			echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?c=ucc&a=login">';
			echo '登录:';
			echo '<dl><dt>用户名</dt><dd><input name="username"></dd>';
			echo '<dt>密码</dt><dd><input name="password" type="password"></dd></dl>';
			echo '<input name="submit" type="submit"> ';
			echo '</form>';
		} else {
			//通过接口判断登录帐号的正确性，返回值为数组
			list($uid, $username, $password, $email) = uc_user_login($_POST['username'], $_POST['password']);

			setcookie('Example_auth', '', -86400);
			if ($uid > 0) {
				//用户登陆成功，设置 Cookie，加密直接用 uc_authcode 函数，用户使用自己的函数
				setcookie('Example_auth', uc_authcode($uid . "\t" . $username, 'ENCODE'));
				//生成同步登录的代码
				$ucsynlogin = uc_user_synlogin($uid);
				//echo '登录成功' . $ucsynlogin . '<br><a href="' . $_SERVER['PHP_SELF'] . '">继续</a>';
				echo empty($ucsynlogin);
				echo 'ok'.$ucsynlogin;
				//exit ;
			} elseif ($uid == -1) {
				echo '用户不存在,或者被删除';
			} elseif ($uid == -2) {
				echo '密码错';
			} else {
				echo '未定义';
			}
		}
	}

}

<?php

class tsb_common {

	/**
	 * 生成验证码
	 */
	function genauthcode() {

		srand((double)microtime() * 1000000);
		//create a random number feed.
		//$ychar = "0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
		$ychar = "0,1,2,3,4,5,6,7,8,9";
		$randnum = 0;
		$authnum = 0;
		$list = explode(",", $ychar);
		for ($i = 0; $i < 5; $i++) {
			$randnum = rand(0, 9);
			// 10+26;
			$authnum .= $list[$randnum];
		}
		AWS_APP::session() -> authcode = $authnum;
		return $authnum;

	}

	/**
	 * 移动版上传图片
	 */

	function m_upload($image_data, $image_name, $type, $attach_access_key) {

		switch ($type) {
			case 'question' :
				$item_type = 'questions';
				break;

			default :
				$item_type = 'answer';

				$_POST['id'] = 'answer';
				break;
		}

		$data = $image_data;

		$file_name = md5(rand(1, 99999999) . microtime()) . '.png';

		$file_dir = get_setting('upload_dir') . '/' . $item_type . '/' . date('Ymd');

		$full_path = $file_dir . '/' . $file_name;

		$uri = substr($data, strpos($data, ",") + 1);

		if (!is_dir($file_dir)) {
			if (!make_dir($file_dir)) {
				return FALSE;
			}
		}

		// $fp = @fopen($full_path, 'w');
		// @fwrite($fp, base64_decode($uri));
		// @fclose($fp);

		file_put_contents($full_path, base64_decode($uri));

		//fb($file_name,'$file_name');

		return $file_name;

	}
	
	
	/**
	 * 移动版上传头像
	 */

	function m_upload_avatar($image_data,$full_path) {

		$data = $image_data;

		//$full_path = '/'.get_setting('upload_dir') . '/avatar/' . $this->model('account')->get_avatar($this->user_id, '', 1);

		$uri = substr($data, strpos($data, ",") + 1);

		if (!is_dir($file_dir)) {
			if (!make_dir($file_dir)) {
				return FALSE;
			}
		}

		file_put_contents($full_path, base64_decode($uri));

		return $full_path;

	}

	/**
	 * 又拍云上传图片
	 */

	function upload_upyun_img($image_data, $type) {
		require_once ('system/tsb/upyun.class.php');
		$upyun = new UpYun('tsb-static', 'tongshibang', 'tongshibang');
		$data = $image_data;
		switch ($type) {
			case 'question' :
				$item_type = 'questions';
				break;

			default :
				$item_type = 'answer';

				$_POST['id'] = 'answer';
				break;
		}

		$file_name = md5(rand(1, 99999999) . microtime()) . '.png';

		$file_dir = '/uploads/' . $item_type . '/' . date('Ymd');
		$full_path = $file_dir . '/' . $file_name;
		$uri = substr($data, strpos($data, ",") + 1);
		$rsp = $upyun -> writeFile($full_path, base64_decode($uri), True);
		// 上传图片，自动创建目录
		return $file_name;

	}

	/**
	 * 又拍云上传文件
	 */
	function upload_upyun_file($local_path, $remote_path) {
		require_once ('system/tsb/upyun.class.php');
		$upyun = new UpYun('tsb-static', 'tongshibang', 'tongshibang');
		$fh = fopen($local_path, 'r');
		$upyun -> writeFile($remote_path, $fh, True);
		fclose($fh);
	}

}

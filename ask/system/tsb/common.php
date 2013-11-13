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

		$full_path = get_setting('upload_dir') . '/' . $item_type . '/' . date('Ymd') . '/' . $file_name;

		$uri = substr($data, strpos($data, ",") + 1);

		file_put_contents($full_path, base64_decode($uri));

		return $file_name;

	}

}

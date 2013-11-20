<?php
class main extends spController {
	function index() {
		//echo "Enjoy, Speed of PHP!";
	}

	function soft() {
		
		

	}

	function edit() {

	}

	function save() {
		$htmlData = '';
		if (!empty($_POST['content'])) {
			if (get_magic_quotes_gpc()) {
				$htmlData = stripslashes($_POST['content']);
			} else {
				$htmlData = $_POST['content'];
			}
		}
		
		$title=$this->spArgs('title');
		$author=$this->spArgs('author');
		$pic_url=$this->spArgs('pic_url');
		$music_url=$this->spArgs('music_url');

		
		$db=spDB('soft_content');
		$new=array("title"=>$title,"author"=>$author,"pic_url"=>$pic_url,"music_url"=>$music_url,"content"=>$htmlData);
		
		echo $db->create($new);
		
	}

	function info() {
		
		$db=spDB('soft_content');
		$rs=$db->findAll();
		
		// $text1 = array('title' => 'ttl_aa', 'author' => 'author_aa', 'text' => 'text_aa', 'src' => 'audio/one.mp3');
		// $text2 = array('title' => 'ttl_bb', 'author' => 'author_bb', 'text' => 'text_bb', 'src' => 'audio/two.mp3');
		// $info = array($text1, $text2);
		// //dump($info);
		 echo json_encode($rs);
	}

}

<?
$discuz_url = 'http://tongshibang.com/bbs/';
//论坛地址
$login_url = $discuz_url . 'member.php?mod=logging&action=login&referer=http%3A%2F%2Flocalhost%2Fbbs%2Fforum.php';
//登录页地址

$post_fields = array();
//以下两项不需要修改
$post_fields['loginfield'] = 'username';
$post_fields['loginsubmit'] = 'true';
$post_fields['handlekey'] = 'ls';

//用户名和密码，必须填写
$post_fields['username'] ='admin';
$post_fields['password'] = '3edc4rfv';
//安全提问
$post_fields['questionid'] = 0;
$post_fields['answer'] = '';
//@todo验证码
$post_fields['seccodeverify'] = '';

//获取表单FORMHASH
$ch = curl_init($login_url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$contents = curl_exec($ch);
curl_close($ch);
//echo $contents;
preg_match('/<input\s*type="hidden"\s*name="formhash"\s*value="(.*?)"\s*\/>/i', $contents, $matches);
if (!empty($matches)) {
	$formhash = $matches[1];
} else {
	die('Not found the forumhash1. aaa');
}

//POST数据，获取COOKIE,cookie文件放在网站的temp目录下
$cookie_file = tempnam('/tmp', 'cookie');

$ch = curl_init($login_url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
$aa=curl_exec($ch);
curl_close($ch);
//echo $aa;

//取到了关键的cookie文件就可以带着cookie文件去模拟发帖,fid为论坛的栏目ID
$send_url = $discuz_url . "forum.php?mod=post&action=newthread&fid=2";

$ch = curl_init($send_url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
$contents = curl_exec($ch);
curl_close($ch);

//这里的hash码和登陆窗口的hash码的正则不太一样，这里的hidden多了一个id属性
preg_match('/<input\s*type="hidden"\s*name="formhash"\s*id="formhash"\s*value="(.*?)"\s*\/>/i', $contents, $matches);
if (!empty($matches)) {
	$formhash = $matches[1];
} else {
	die('Not found the forumhash.');
}

$post_data = array();
//帖子标题
$post_data['subject'] = 'test post article';
//帖子内容
$post_data['message'] = 'haha,okok';
$post_data['topicsubmit'] = "yes";
$post_data['extra'] = '';
//帖子标签
$post_data['tags'] = $post_data['subject'];
//帖子的hash码，这个非常关键！假如缺少这个hash码，discuz会警告你来路的页面不正确
$post_data['formhash'] = $formhash;

$ch = curl_init($send_url);
curl_setopt($ch, CURLOPT_REFERER, $send_url);
//伪装REFERER
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
$contents = curl_exec($ch);
curl_close($ch);

//清理cookie文件
unlink($cookie_file);
?>
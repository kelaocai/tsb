<?php
//定义服务器信息

//测试数据库
define("DB_HOST", 'localhost');
define("DB_USER", 'root');//数据库用户名
define("DB_PASSWORD", '3edc4rfv');//数据库密码
define("DB_DATABASE", 'tsbdev');//数据库名称

//DEBUG模式
define("MODE","debug");//调试模式


//uc_center
define('UC_CONNECT', 'mysql');				// 连接 UCenter 的方式: mysql/NULL, 默认为空时为 fscoketopen()
							// mysql 是直接连接的数据库, 为了效率, 建议采用 mysql

//数据库相关 (mysql 连接时, 并且没有设置 UC_DBLINK 时, 需要配置以下变量)
define('UC_DBHOST', 'localhost');			// UCenter 数据库主机
define('UC_DBUSER', 'root');				// UCenter 数据库用户名
define('UC_DBPW', '3edc4rfv');					// UCenter 数据库密码
define('UC_DBNAME', 'tsbdev');				// UCenter 数据库名称
define('UC_DBCHARSET', 'utf8');				// UCenter 数据库字符集
define('UC_DBTABLEPRE', 'tsbdev.tsb_ucenter_');			// UCenter 数据库表前缀

//通信相关
define('UC_KEY', '1qaz2wsx');				// 与 UCenter 的通信密钥, 要与 UCenter 保持一致
define('UC_API', 'http://localhost/bbs/uc_server');	// UCenter 的 URL 地址, 在调用头像时依赖此常量
define('UC_CHARSET', 'utf8');				// UCenter 的字符集
define('UC_IP', '');					// UCenter 的 IP, 当 UC_CONNECT 为非 mysql 方式时, 并且当前应用服务器解析域名有问题时, 请设置此值
define('UC_APPID', 2);				// 当前应用的 ID
define('UC_PPP', '20');


//ucexample_2.php 用到的应用程序数据库连接参数
$dbhost = 'localhost';			// 数据库服务器
$dbuser = 'root';			// 数据库用户名
$dbpw = '3edc4rfv';				// 数据库密码
$dbname = 'tsbdev';			// 数据库名
$pconnect = 0;				// 数据库持久连接 0=关闭, 1=打开
$tablepre = 'tsbdev.tsb_ucenter_';   		// 表名前缀, 同一数据库安装多个论坛请修改此处
$dbcharset = 'utf8';			// MySQL 字符集, 可选 'gbk', 'big5', 'utf8', 'latin1', 留空为按照论坛字符集设定

//同步登录 Cookie 设置
$cookiedomain = ''; 			// cookie 作用域
$cookiepath = '/';			// cookie 作用路径
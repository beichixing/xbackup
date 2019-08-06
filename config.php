<?php
//配置信息
$cfg_dbhost = 'localhost';  //数据库地址
$cfg_dbname = '';	//数据库名称
$cfg_dbuser = 'root';	//数据库用户名
$cfg_dbpwd = '';	//数据库密码
$cfg_db_language = 'utf8'; 	//数据库编码
$date_db = date("Y-m-d H:i:s");
$sql_name = $date_db."-".$cfg_dbname.".sql";
$to_file_name = 'back_log/'.$date_db."-".$cfg_dbname.".sql"; //备份文件命名规则
$cfg_type = '1'; 	//	备份方式：1.邮箱备份 2. 阿里云oss备份


//////////////////////////邮箱备份设置\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$cfg_email = '';	// 接收备份的邮箱  请将 imhzs@sina.com 设置为收信白名单

$email_info = [
	'Host' => '',  // 发件邮箱的SMTP服务器地址
	'Username' => '', //发件邮箱的用户名
	'Password' => '',  //发件邮箱的密码（或授权码）
	'SMTPSecure' => 'ssl', //发件邮箱的认证方式
	'Port' => '465' //发件邮箱端口

];


////////////////////////阿里云oss备份设置\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$cfg_aliyun_id = ''; 	//阿里云开发者ID
$cfg_aliyun_key = '';	//阿里云开发者KEY
$cfg_aliyun_url = 'http://oss-cn-hongkong.aliyuncs.com'; //oss外网访问地址
$cfg_aliyun_bucket = ''; //储存空间名称
// END 配置




?>
<?php
require('config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require_once 'vendor/autoload.php';

use OSS\OssClient;
use OSS\Core\OssException;

header("Content-type:text/html;charset=utf-8");




//链接数据库
$link = mysqli_connect($cfg_dbhost,$cfg_dbuser,$cfg_dbpwd,$cfg_dbname);
//选择编码
mysqli_set_charset($link,$cfg_db_language);
//数据库中有哪些表
$tables = mysqli_query($link,"show tables");
//die(var_dump($tables));
//将这些表记录到一个数组
$tabList = array();
while($row = mysqli_fetch_row($tables)){
$tabList[] = $row[0];
}


echo "运行中，请耐心等待...<br/>";
$info = "-- ----------------------------\r\n";
$info .= "-- 日期：".date("Y-m-d H:i:s",time())."\r\n";
$info .= "-- ----------------------------\r\n\r\n";
file_put_contents($to_file_name,$info,FILE_APPEND);
//将每个表的表结构导出到文件
foreach($tabList as $val){
$sql = "show create table ".$val;
$res = mysqli_query($link,$sql);
$row = mysqli_fetch_array($res);
$info = "-- ----------------------------\r\n";
$info .= "-- Table structure for `".$val."`\r\n";
$info .= "-- ----------------------------\r\n";
$info .= "DROP TABLE IF EXISTS `".$val."`;\r\n";
$sqlStr = $info.$row[1].";\r\n\r\n";
//追加到文件
file_put_contents($to_file_name,$sqlStr,FILE_APPEND);
//释放资源
mysqli_free_result($res);
}
//将每个表的数据导出到文件
foreach($tabList as $val){
$sql = "select * from ".$val;
$res = mysqli_query($link,$sql);
//如果表中没有数据，则继续下一张表
if(mysqli_num_rows($res)<1) continue;
//
$info = "-- ----------------------------\r\n";
$info .= "-- Records for `".$val."`\r\n";
$info .= "-- ----------------------------\r\n";
file_put_contents($to_file_name,$info,FILE_APPEND);
//读取数据
while($row = mysqli_fetch_row($res)){
$sqlStr = "INSERT INTO `".$val."` VALUES (";
foreach($row as $zd){
$sqlStr .= "'".$zd."', ";
}
//去掉最后一个逗号和空格
$sqlStr = substr($sqlStr,0,strlen($sqlStr)-2);
$sqlStr .= ");\r\n";
file_put_contents($to_file_name,$sqlStr,FILE_APPEND);
}
//释放资源
mysqli_free_result($res);
file_put_contents($to_file_name,"\r\n",FILE_APPEND);
}

//发送邮件
function mailSend($mail_rec,$file,$email_info){
    $db_time = date("Y-m-d H:i:s",time());
	$mail = new PHPMailer();
	$mail->isSMTP();// 使用SMTP服务
	$mail->CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码
	$mail->Host = $email_info['Host'];// 发送方的SMTP服务器地址
	$mail->SMTPAuth = true;// 是否使用身份验证
	$mail->Username = $email_info['Username'];// 发送方的邮箱用户名
	$mail->Password = $email_info['Password'];// 发送方的邮箱密码，注意用126邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！
	$mail->SMTPSecure = $email_info['SMTPSecure'];// 使用ssl协议方式
	$mail->Port = $email_info['Port'];// 163邮箱的ssl协议方式端口号是465/994
	$mail->setFrom($email_info['Username'],"数据库自动备份");// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@126.com），Mailer是当做名字显示
	$mail->addAddress( $mail_rec ,'datebase_back');// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@126.com)
	$mail->addReplyTo($email_info['Username'],"Reply");// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址
	//$mail->addCC("aaaa@inspur.com");// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址
	//$mail->addBCC("bbbb@163.com");// 设置秘密抄送人
	$mail->addAttachment($file);// 添加附件
    $mail->IsHTML(true); 
	$mail_title = date("Y-m-d H:i:s",time());
	$mail->Subject = $mail_title;// 邮件标题
	$mail->Body = <<<html

<meta name="viewport" content="width=device-width">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>备份成功提醒</title>


<style type="text/css">
img {
max-width: 100%;
}
body {
-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em;
}
body {
background-color: #f6f6f6;
}
@media only screen and (max-width: 640px) {
  body {
    padding: 0 !important;
  }
  h1 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h2 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h3 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h4 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h1 {
    font-size: 22px !important;
  }
  h2 {
    font-size: 18px !important;
  }
  h3 {
    font-size: 16px !important;
  }
  .container {
    padding: 0 !important; width: 100% !important;
  }
  .content {
    padding: 0 !important;
  }
  .content-wrap {
    padding: 10px !important;
  }
  .invoice {
    width: 100% !important;
  }
}
</style>
<table class="body-wrap" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6"><tbody><tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
		<td class="container" width="600" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
			<div class="content" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
				<table class="main" width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff"><tbody><tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert alert-warning" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #fff; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #009688; margin: 0; padding: 20px;" align="center" bgcolor="#FF9F00" valign="top">
							数据库备份成功
						</td>
					</tr><tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
							<table width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tbody><tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										亲爱的 <strong style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">站长</strong> ：
									</td>
								</tr><tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										数据库备份程序已成功执行，请点击下方附件下载备份文件。<br>执行时间：$db_time
									</td>
								</tr><tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										
									</td>
								</tr><tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										感谢您的使用。
									</td>
								</tr></tbody></table></td>
					</tr></tbody></table><div class="footer" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 20px;">
					<table width="100%" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tbody><tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="aligncenter content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 20px;" align="center" valign="top">此邮件由系统自动发送，请不要直接回复。</td>
						</tr></tbody></table></div></div>
		</td>
		<td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
	</tr></tbody></table>
html;
	//$mail->AltBody = "This is the plain text纯文本";// 这个是设置纯文本方式显示的正文内容，如果不支持Html方式，就会用到这个，基本无用
	if(!$mail->send()){// 发送邮件
	    echo "发送错误.";
	    echo "错误信息: ".$mail->ErrorInfo;// 输出错误信息
	}else{
	    echo '备份成功已执行完成';
	}


	}

function aliyun($sourceFileDir,$cfg_aliyun_id,$cfg_aliyun_key,$cfg_aliyun_url,$cfg_aliyun_bucket,$sql_name){
// 阿里云主账号AccessKey拥有所有API的访问权限，风险很高。强烈建议您创建并使用RAM账号进行API访问或日常运维，请登录 https://ram.console.aliyun.com 创建RAM账号。
$accessKeyId = $cfg_aliyun_id;
$accessKeySecret = $cfg_aliyun_key;
// Endpoint以杭州为例，其它Region请按实际情况填写。
$endpoint = $cfg_aliyun_url;
// 存储空间名称
$backet= $cfg_aliyun_bucket;
// 文件名称
$object = $sql_name;
// <yourLocalFile>由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt
$filePath = $sourceFileDir;

try{
    $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);

    $ossClient->uploadFile($backet, $object, $filePath);
} catch(OssException $e) {
    printf(__FUNCTION__ . ": FAILED\n");
    printf($e->getMessage() . "\n");
    return;
}

};



if($cfg_type == 1){
		//邮箱备份
		mailSend($cfg_email,$to_file_name,$email_info);
		unlink($to_file_name);
}else if($cfg_type == 2){
		//阿里云oss备份
		$response = aliyun($to_file_name,$cfg_aliyun_id,$cfg_aliyun_key,$cfg_aliyun_url,$cfg_aliyun_bucket,$sql_name);
		echo '执行成功';
		unlink($to_file_name);
}else{
	die('错误，未设置备份方式！');
}

?>
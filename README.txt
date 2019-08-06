#一个简单的数据库自动备份小程序，支持将数据库备份到邮箱或阿里云oss！

##你只需要三步即可在你的网站上使用本程序。

##使用步骤：

###1.根据你的环境选择使用方式：
·Linux主机：cd 到网站目录 使用 git clone https://github.com/beichixing/xbackup.git 命令克隆程序。
·虚拟主机：下载zip源码，解压到根目录，将文件夹命名为xbackup


###2.打开config.php文件，更改里面的数据库及配置内容。

###3使用linux定时任务执行 网站域名/xbackup/back.php 即可。
命令 crontab -e

例如 ：
 30 0 * * * php 网站绝对目录/xbackup/back.php  
//	设置为每天0：30自动执行备份任务

crtl+c ：wq 回车  即可

提示：建议在执行命令前手动访问一次back.php文件，确保配置正确，任务可以正常执行。



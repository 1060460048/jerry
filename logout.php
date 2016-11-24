<?php
//这里的-1是指关闭浏览器就清空的意思吗?
//为什么不写这里注册就不成功，因为register.php文件中已经做了判断，如果用户登录就重定向了，而不进行redis的写入操作
//登出的时候把服务器上的redis中存的authsecret也给它清空
$userid=$_COOKIE['userid'];
include('./lib.php');
$r=connredis();
$r->set('user:userid:'.$userid.':authsecret','');
setcookie('username','',-1);
setcookie('userid','',-1);
setcookie('authsecret','',-1);
header('Location: index.php');

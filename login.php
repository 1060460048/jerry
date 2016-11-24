<?php
//登录页面
/*
 * 0.接受POST数据判断合法性
 * 1.查询用户名是否存在
 * 2.查询密码是否匹配
 * 3.如果登录成功了，要设置cookie
 * 所以如果用户名不存在的话，直接定向到注册页面
 * 如果用户名已经存在判断密码是否正确，redis里用user:userid:password取出的值跟用户提交的做比对
 *
 */
include('./lib.php');
include('./header.php');
if(isLogin()!=false){
    header('Location: home.php');
    exit;
}
$username=P('username');
$password=P('password');
if(!$username || !$password){
    error("请输入完整信息");
}

//连接redis
$r=connredis();
$userid=$r->get('user:username:'.$username.':userid');
if(!$userid){
    error("用户名不存在");
}
//根据userid查询用户的存在redis中的密码
$realpass=$r->get('user:userid:'.$userid.':password');
if($password!=$realpass){
    error('密码不对');
}

//设置cookie登录成功
setcookie('username',$username);
setcookie('userid',$userid);
//如果只设置上面两项cookie本地很容易被篡改，而服务器没有校验机制是非常危险的
//先生成一个authsecret
$authsecret=randsecret();
$r->set('user:userid:'.$userid.':authsecret',$authsecret);
//但是你只写到这里还是不行的，你还要记录到redis里做较对，否则登录这个校验不通过就无法成功的进行登陆了
setcookie('authsecret',$authsecret);
header('Location: home.php');

include('./footer.php');

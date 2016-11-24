<?php
/*
 * 设计user表--对应key的规则
 * 注册用户
 * set user:userid:1:username zhangsan
 * set user:userid:1:password 111111
 * set user:username:zhangsan 1 这个目的是维护一个查询，所有应该是一个有限set?
 * 具体步骤
 * 0:接收$_POST参数，判断用户名密码是否完整
 * 1.连接redis查询该用户名是否存在
 * 2.如果不存在写入数据库
 * 3.登录操作
 */
include('./lib.php');
include('./header.php');

if(isLogin()!=false){
    header('Location: home.php');
    exit;
}

$username=P('username');
$password=P('password');
$password2=P('password2');
if(!$username || !$password || !$password2){
    error('请输入完整注册信息');
}

//判断密码是否一致
if($password!==$password2){
    error('2次密码不一样');
}

//连接redis
$r=connredis();

//查询用户名是否已经被注册
if($r->get('user:username:'.$username.':userid')){
    error('用户名已被注册,请更换');
}

//获取userid
$userid=$r->incr('global:userid');
$r->set('user:userid:'.$userid.':username',$username);
$r->set('user:userid:'.$userid.':password',$password);
//$r->set('user:userid:'.$userid.':password2',$password2);
//还可能根据userid查name,根据name查id,所以上边是根据id查name,这里是根据name查id
$r->set('user:username:'.$username.':userid',$userid);

//通过一个链接，维护50个最新的userid
$r->lpush('newuserlink',$userid);
$r->ltrim('newuserlink',0,49);

include('./footer.php');

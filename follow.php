<?php 
include('./lib.php');
include('./header.php'); 
if(($user=isLogin())==false){
   header('Location: index.php');
   exit;
}
/*
 * 1.判断uid,f是否合法
 * 2.uid是否是自己
 */
$uid=G('uid');
$f=G('f');
if(!$uid || !$f){
    error('非法操作');
}

$r=connredis();
if($f==1){
    //集合里的添加,cookie里读到的用户是当前的用户，点进谁的主页就可以关注谁，所以这个uid是从url地址上的u参数get到的，即uid
    $r->sadd('following:'.$user['userid'],$uid);
    //同时在被关注者的粉丝表中就应该多了一个这个用户
    $r->sadd('followed:'.$uid,$user['userid']);
}else{
    $r->srem('following:'.$user['userid'],$uid);
    $r->srem('followed:'.$uid,$user['userid']);
}
//要获取对应的username,所以要根据username来查出userid嘛?不是只是定向页面的时候需要url是对应的用户名而不是uid，所以这里要再做一个username的提取
$uname=$r->get('user:userid:'.$uid.':username');

header('location: profile.php?u='.$uname);


include('./footer.php'); 
?>

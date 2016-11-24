<?php 
include('./lib.php');
include('./header.php'); 

if(($user=isLogin())==false){
    header('Location: index.php');
    exit;
}
/*
 * 思路：
 * 每人有自己的粉丝记录 set
 * 每人有自己的关注记录 set
 * aid 关注 bid
 * 发生了什么
 * following:aid (bid)我关注的人是一个集合
 * follower:bid (aid)
 * 0.获取用户名
 * 1.根据用户名查询id
 * 2.查询此用户名是否在我的following集合里
 */
//获取用户名
$u=G('u');
//获取用户名后我们得获取它的uid
$r=connredis();
$prouid=$r->get('user:username:'.$u.':userid');
if(!$prouid){
    error('非法用户');
    exit;
}
//看我的关注列表里有没有这个人,如果没有就可以关注，有了做什么？
$isf=$r->sismember('following:'.$user['userid'],$prouid);
$isfnum=$isf?'0':'1';
//关注的文字同理
$isfword=$isf?'取消关注':'关注ta';
?>
<div id="navbar">
<a href="index.php">主页</a>
| <a href="timeline.php">热点</a>
| <a href="logout.php">退出</a>
</div>
</div>
<h2 class="username">test</h2>
<a href="follow.php?uid=<?php echo $prouid;?>&f=<?php echo $isfnum; ?>" class="button"><?php echo $isfword; ?></a>

<div class="post">
<a class="username" href="profile.php?u=test">test</a> 
world<br>
<i>11 分钟前 通过 web发布</i>
</div>

<div class="post">
<a class="username" href="profile.php?u=test">test</a>
hello<br>
<i>22 分钟前 通过 web发布</i>
</div>
<?php include('./footer.php'); ?>

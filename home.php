<?php 
include('./lib.php');
include('./header.php');
if(($user=isLogin())==false){
    header('Location index.php');
    exit;
}
//来到这个界面，把推送的消息，即你关注的那些人发的微博给接收过来
//取出自己发的和你所关注的用户发的微博
$r=connredis();
//获取我关注的人
$star=$r->smembers('following:'.$user['userid']);
$star[]=$user['userid'];

$lastpull=$r->get('lastpull:userid:'.$user['userid']);
if(!$lastpull){
    $lastpull=0;
}

//拉取最新数据,每个我关注的人取20条数据但是不一定全要，之前取过的不要，只要最新的
$latest=array();
foreach($star as $s){
    $latest=array_merge($latest,$r->zrangebyscore('starpost:userid:'.$s,$lastpull+1,4294967296));
}
sort($latest,SORT_NUMERIC);
//更新lastpull
if(!empty($latest)){
    $r->set('lastpull:userid:'.$user['userid'],end($latest));
}
//循环把$latest放到自己主页应该收取的微博链接里
foreach($latest as $l){
    $r->lpush('recievepost:'.$user['userid'],$l);
}

//保持个人主页是多收取1000条数据
$r->ltrim('recievepost:'.$user['userid'],0,999);

//假如所有关注的人的信息都更新完毕，我们要更新一下lastpull变量的值
//先做测试只取50条微博信息
$r->ltrim('recievepost:'.$user['userid'],0,49);

//现在这行是用hash结构存储微博
$newpost=$r->sort('recievepost:'.$user['userid'],array('sort'=>'desc'));

//计算几个粉丝，几个关注这个比较简单
//计算集合的元素个数
$myfans=$r->scard('followed:'.$user['userid']);
//我关注的微博账号的个数
$mystar=$r->scard('following:'.$user['userid']);
?>
<div id="navbar">
<a href="index.php">主页</a>
| <a href="timeline.php">热点</a>
| <a href="logout.php">退出</a>
</div>
</div>
<div id="postform">
<form method="POST" action="post.php">
<?php echo $user['username']; ?>, 有啥感想?
<br>
<table>
<tr><td><textarea cols="70" rows="3" name="status"></textarea></td></tr>
<tr><td align="right"><input type="submit" name="doit" value="Update"></td></tr>
</table>
</form>
<div id="homeinfobox">
<?php echo $myfans; ?> 粉丝<br>
<?php echo $mystar; ?> 关注<br>
</div>
</div>
<div class="post">
<?php 
foreach($newpost as $postid){
    $p=$r->hmget('post:postid:'.$postid,array('userid','username','time','content'));
?>
<a class="username" href="profile.php?u=test"><?php echo $p['username']; ?></a> <?php echo $p['content']; ?><br>
<i><?php echo formattime($p['time']); ?> 前 通过 web发布</i>
<?php }?>
</div>


<?php include('./footer.php');?>

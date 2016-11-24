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
//先做测试只取50条微博信息
$r->ltrim('recievepost:'.$user['userid'],0,49);
//我想要的并不是推给你的postid，而是要它具体的内容
//第二个参数用数组指定取数据遵从原则，按倒序取，取什么用get来指定
//改成存hash结构里这里就不能这么来取了
//$newpost=$r->sort('recievepost:'.$user['userid'],array('sort'=>'desc','get'=>'post:postid:*:content'));
//现在这行是用hash结构存储微博
$newpost=$r->sort('recievepost:'.$user['userid'],array('sort'=>'desc'));

foreach($newpost as $postid){
    $r->hmget('post:postid:'.$postid,array('userid','username','time','content'));
}
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

<?php
/*
 * 设计user表--对应key的规则
 * 注册用户 user
 * set user:userid:1:username zhangsan
 * set user:userid:1:password 111111
 * set user:username:zhangsan 1 这个目的是维护一个查询，所有应该是一个有限set?
 *
 * 发微博:post table
 * post:postid:3:time timestamp
 * post:postid:3:userid 5
 * post:postid:3:content 'this is my home'
 * 文章的id也是全局增长的后边的调用以及冷数据写数据库好像都用到了这个自增长的量
 * 好像有点理解那个说法了，如果这个增长值是全局的那么上个时间取到一个值，这个时间点后的，无论是新关注用户还是新发布的都是在取最新时间点的,不对呀。感觉如果这个用户之前没关注，那么，你关注这个时刻，恰好维护的这个时间节点里没有包含这个新关注用户的啊。
 * incr global:postid
 * set post:postid:$postid:time $time 
 * set post:postid:$postid:userid $userid 
 * set post:postid:$postid:content $content
 * 实现思路
 * 0判断是否登陆
 * 1.接收post内容
 * 2.set redis
 *
 */
include('./lib.php');
include('./header.php');

$content=P('status');
if(!$content){
    error("请填写内容");
}
if(($user=isLogin()) == false){
    header('Location: index.php');
    exit;
}

$r=connredis();
//这里少了一步定义全局的postid自增长的部分
$postid=$r->incr('global:postid');
//$r->set('post:postid:'.$postid.':userid',$user['userid']);
//$r->set('post:postid:'.$postid.':time',time());
//$r->set('post:postid:'.$postid.':content',$content);
//把每个数值单独作为一个键，不是最优选型，这个时候用哈希结构来存
$r->hmset('post:postid:'.$postid,array('userid'=>$user['userid'],'username'=>$user['username'],'time'=>time(),'content'=>$content));

//有两种解决方式，一种是把自己的follower都遍历，并取他们发布的内容，第二种方式是发微博的同时，把微博同时推给自己的粉丝
//先把自己的粉丝拿到
$fans=$r->smembers('followed:'.$user['userid']);
//print_r($fans);die;
//知道了自己有哪些粉丝，挨个给他们推
$fans[]=$user['userid'];
//还要维护两张表，一张表是recievepost就是你推给哪些粉丝用户看了你的消息，要把读你消息的这些用户？一定读了吗？不一定读了但是发生了推送行为
foreach($fans as $fansid){
    //由于要存好多数据呢，依然选用链表的形式，之前哪个部分是用了链表的形式？
    $r->lpush('recievepost:'.$fansid,$postid);
}
header('Location: home.php');
exit;
include('./footer.php');

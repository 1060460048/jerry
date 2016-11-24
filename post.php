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
//把自己的发的微博维护在一个有序集合中,只要前20个,有序集合并不合适，用链表，为什么有序集合不合适，与之前存字串没有区别了，是给取数据造成难度了吗？
//难取的理由是什么，是有序集合要设置key，value写的时候有三个参数，而链表只有两个参数，所以用链表吗？
//$r->zadd('user:userid:'.$user['userid'],$postid,$postid);
$r->zadd('starpost:userid:'.$user['userid'],$postid,$postid);
//我们用zcard来度量一下有序集合的长度，如果有序集合的长度大于20的话，那么我们进行删除,什么含义，只保留20条显示嘛?把旧的删掉，是指什么旧的，保留的目的是使哪个部分的数据始终保持最新吗？是因为要删除的数据要写入到mysql中吗？
if($r->zcard('starpost:userid:'.$user['userid']) > 20){
    //后两个参数从0到0是把旧的给删掉
    $r->zremrangebyrank('starpost:userid:'.$user['userid'],0,0);
}

//把自己的微博id,放在一个链表里，1000个，目的是什么？自己看自己的微博用的,1000个之前的旧微博会放到mYsql中存起来
$r->lpush('mypost:userid:'.$user['userid'],$postid);
if($r->llen('mypost:userid:'.$user['userid']) > 10){
    $r->rpoplpush('mypost:userid:'.$user['userid'],'global:store');
}

header('Location: home.php');
exit;
include('./footer.php');













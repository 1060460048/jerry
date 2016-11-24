<?php
include('./lib.php');
$r=connredis();
$sql='insert into post(postid,userid,username,time,content) values ';
$i=0;
while($r->llen('global:store') && $i++<1000){
    $postid=$r->rpop('global:store');
    $post=$r->hmget('post:postid:'.$postid,array('userid','username','time','content'));
    $sql.="($postid,".$post['userid'].",'".$post['username']."','".$post['time']."','".$post['content']."'),";
}
if($i==0){
    echo 'no job';
    exit;
}
$sql=substr($sql,0,-1);

//连接数据库，把旧数据写入数据库
$conn=mysql_connect('localhost','root','');
mysql_query('set names utf8',$conn);
mysql_query('use test',$conn);
mysql_query($sql,$conn);
echo 'ok';

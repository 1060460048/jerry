<?php
//基础函数库
/**
 * 微博项目key设计总结
 * 用户相关的key
 * 自增型的key
 * Global:userid
 */
function P($key){
    return $_POST[$key];
}


function G($key){
    return $_GET[$key];
}

//报错函数
function error($msg){
    echo '<div>';
    echo $msg;
    echo '</div>';
    include('./footer.php');
    //如果直接退出，后边的footer.php引入的部分就执行不了了
    exit;
}

function connredis(){
    static $r=null;
    if($r!=NULL){
        return $r;
    }
    $r=new redis();
    $r->connect('127.0.0.1');
    return $r;
}

//判断用户是否登陆
//安全考虑加authsecret这个时候存到redis只校验用户名密码就不行了,我还要看你存的和redis中读的是否一致
function isLogin(){
    if(!$_COOKIE['userid'] || !$_COOKIE['username']){
        return false;
    }
    if(!$_COOKIE['authsecret']){
        return false;
    }
    $r=connredis();
    $authsecret=$r->get('user:userid:'.$_COOKIE['userid'].':authsecret');
    if($authsecret!=$_COOKIE['authsecret']){
        return false;
    }
    return array('userid'=>$_COOKIE['userid'],'username'=>$_COOKIE['username']);
}
//写一个产生随机数的函数
function randsecret(){
    $str='abcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
    return substr(str_shuffle($str),0,16);
}

//格式化时间
function formattime($time){
    $sec=time()-$time;
    if($sec>86400){
        return floor($sec/86400).'天';
    }else if($sec>3600){
        return floor($sec/3600).'小时';
    }else if($sec>=60){
        return floor($sec/60).'分钟';
    }else{
        return floor($sec).'秒';
    }
}

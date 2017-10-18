<?php
     /**
	 * 用户登录校验页面20170502
	 */
if ( ! defined( 'ROOT' ) ) {
	$scriptName = !empty($_SERVER["REQUEST_URI"]) ? $scriptName = $_SERVER["REQUEST_URI"] : $scriptName = $_SERVER["PHP_SELF"];
	$rootpath = @preg_replace("/\/application\/(I|i)nstall\/index\.php(.*)$/", "", $scriptName);
	define("DIRNAME",strtok(ltrim($rootpath,"/"),"/"));
	if(!empty(DIRNAME)){
	   define("ROOT",$_SERVER['DOCUMENT_ROOT']."/".DIRNAME."/");
	}else{define("ROOT",$_SERVER['DOCUMENT_ROOT']."/");	
	}	
}	 
//判断表单是否提交了
//开启session会话
session_start();
if (isset($_POST['username'])) {
    $user_name = strtolower($_POST["username"]);
//细节：在登录注册界面js用正则表达式校验一下，目的为了 如果用户名格式不正确，不用和数据库中数据对比了，降低服务器压力
    $user_password = strtolower($_POST["p"]);	  
	 //在php5.4以上运行medoo数据库框架20170606
     //ver1.1.3支持各种数据：MySQL, MSSQL, SQLite, MariaDB, Oracle, Sybase, PostgreSQL等等，防止SQL注入，支持各种常见的SQL查询。
	$tbName = 'user';	
	require_once ROOT.'core/Medoo/Medoo.php';   
	$_where="WHERE user = '{$user_name}' AND pass ='{$user_password}'";
	$in = $db->has($tbName,$_where);//是否在表中？返回真假值(bool)
  		if($in){
		//跳转后台主页//提供“记住我”免登录复选框会话值  
		$_SESSION['valid_user'] = $user_name;
  		 echo '<script>location.replace("../main.php")</script>';          		  
	    }else {
		 header("location:../errors/404.html");
        }
}	
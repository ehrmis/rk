<?php
session_start();
if (isset($_SESSION['valid_user'])){
$old_user = $_SESSION['valid_user'];
unset($_SESSION['valid_user']);
if (!empty($old_user))
{$ss_result = session_destroy();

	if ($ss_result)
	{echo "<script>alert($old_user.'已被注销！');</script>"; 
	 exit();
	}else{echo "<script>alert('退出失败！');</script>";	
	}
}else{
	echo "<script>alert('该用户未登录！');</script>"; 
	exit();
}	
}	
	
	
	/*//包含系统的初始化文件
	require_once './core/init.php';
	
	//实例化用户模型类
	$user = new UserModel();
	
	//user类对象调用类中的logOut()方法来实现用户的退出
	$user->logOut();*/
	
	//调转到首页
	//Redirect::go('index.php');
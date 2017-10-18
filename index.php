<?php
/**
 * index.php 
 * 加载初始化文件
 * @author           人可工作室　何朱必 
 * @license          http://www.hezhubi.com
 * @lastmodify       2017-05-30
 # MetInfo Management System 
 # Copyright (C) 7 Studio (http://www.hezhubi.com). All rights reserved.
 */
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.4.0','<'))  die("<script>alert('请将您的PHP升级到5.4以上');history.go(-1)</script>");

// 绝对路径根目录20170621
define("ROOT",str_replace("\\","/",dirname(__FILE__))."/");	

if(!file_exists(ROOT.'core/config/config.php')){
    //安装系统
	 //判断配置文件目录是否存在 若不存在则创建
        if (!file_exists(ROOT.'core/config')) {
            mkdir(ROOT.'core/config'); //新建目录
        }
        //使用写的方式打开配置文件
        $fp=fopen(ROOT."core/config/config.php", "wb");    /*建立一个文字文件只写*/
        //法一：拼装向配置文件中写入的内容的字符串20170516
        $str = "<?php\n";
        $str .= "define('_APP', 'HRmis');\n";
        $str .= "define('_VER', '2017.1.2');\n";  		
        //将内容写入配置文件中
        if (!fwrite($fp, $str)) {
            die('创建配置文件失败！');
        }
     header("location:install/index.php");//相当于是在根目录下直接到子目录跳转，跳转后此前变量将失效20170608
	 exit();
}else{
	//登录系统

    header("location:admin/login.html");//相当于是从根目录下跳转到了admin子目录下运行，跳转后此前变量将失效20170608
	//跳转新页面后，非超全局变量的自定义变量就失效20170607
	exit();
}
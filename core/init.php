<?php
/**
 * Created by PhpStorm.
 * User: rkgzs
 * Date: 2017/5/30
 * Time: 16:36
 */
/* ========================================================================
 * 系统初始化文件 ( init.php 触发器)
 * ======================================================================== */
 
/**
 * 1.强制使用UTF-8编码
**/
header("Content-type: text/html; charset=utf-8");
//防止用户点击后退按钮返回以前浏览过的页面。对于不受安全保护的页面，“pragma: no-cache”被视为与“expires: -1”相同，
//此时浏览器仍旧缓存页面，但把页面标记为立即过期。
header("Cache-control: no-cache,no-store,must-revalidate");
header("Pramga: no-cache"); 
header("Expires: -1");

/**
 * 2.任意子目录下重新定义绝对路径根目录万能函数20170621
**/
if ( ! defined( 'ROOT' ) ) {
	$scriptName = !empty($_SERVER["REQUEST_URI"]) ? $scriptName = $_SERVER["REQUEST_URI"] : $scriptName = $_SERVER["PHP_SELF"];
	$rootpath = @preg_replace("/\/application\/(I|i)nstall\/index\.php(.*)$/", "", $scriptName);
	define("DIRNAME",strtok(ltrim($rootpath,"/"),"/"));
	if(!empty(DIRNAME)){
	   define("ROOT",$_SERVER['DOCUMENT_ROOT']."/".DIRNAME."/");
	}else{define("ROOT",$_SERVER['DOCUMENT_ROOT']."/");	
	}	
}
//3.加载公共函数库
include ROOT.'core/common/function.php';

//公测阶段最佳使用体验还是打开友好报错提示，开启调试模式********20170725**********
define('DEBUG', true);
//define('DEBUG', false);//稳定版本可关闭调试模式****20170804********
//error_reporting(0); //抑制所有错误信息
if(DEBUG) {
    //打开PHP的错误显示
    ini_set('display_error','On');
    //载入友好的错误显示类
//4.加载composer
    require_once ROOT.'vendor/autoload.php';
    $whoops = new \Whoops\Run;
    $errorPage = new \Whoops\Handler\PrettyPageHandler;
    $errorPage->setPageTitle("出错处理!");
    $whoops->pushHandler($errorPage);
    $whoops->register();
} else {
    ini_set('display_error','Off');
}
//5.限制上下页面引用开关
//调用init或跳转页面安全入口限制
session_start();
if (isset($_SESSION['valid_user'])) {
	define("RK", true);
}	
//if (isset($_COOKIE['username']) && $_COOKIE['username']==='admin')

//下一页安全入口限制
//if(!defined("RK")){exit("<h1>Access Denied</h1>");}


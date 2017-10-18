<?php
if ( ! defined( 'ROOT' ) ) {
	$scriptName = !empty($_SERVER["REQUEST_URI"]) ? $scriptName = $_SERVER["REQUEST_URI"] : $scriptName = $_SERVER["PHP_SELF"];
	$rootpath = @preg_replace("/\/application\/(I|i)nstall\/index\.php(.*)$/", "", $scriptName);
	define("DIRNAME",strtok(ltrim($rootpath,"/"),"/"));
	if(!empty(DIRNAME)){
	   define("ROOT",$_SERVER['DOCUMENT_ROOT']."/".DIRNAME."/");
	}else{define("ROOT",$_SERVER['DOCUMENT_ROOT']."/");	
	}	
}
require ROOT.'page/_meta.html'; 
include ROOT.'core/config/config.php';
include ROOT.'core/init.php';
if(!defined("RK")){exit("<h1>Access Denied</h1>");}
require  ROOT.'page/_header.html';
require  ROOT.'page/_menu.html';
require  ROOT.'page/desktop.html';
require  ROOT.'page/_footer.html';

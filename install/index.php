<?php

/**
 * 人力资源管理信息系统（PHP语言及MySQL数据库编写）
 * @package HRmis
 * @author hezhubi.com
 * @copyright 2017 人可工作室
 * @version 1.0　Build20170514
 * @license http://www.hezhubi.com 
 */
// 编码

header('Content-Type:text/html;charset=utf-8'); 
date_default_timezone_set('PRC');

//当前URL地址路由解析
$scriptName = !empty($_SERVER["REQUEST_URI"]) ? $scriptName = $_SERVER["REQUEST_URI"] : $scriptName = $_SERVER["PHP_SELF"];
$rootpath = @preg_replace("/\/application\/(I|i)nstall\/index\.php(.*)$/", "", $scriptName);
$domain = empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
if ((int) $_SERVER['SERVER_PORT'] != 80) {
    $domain .= ":" . $_SERVER['SERVER_PORT'];
}
$domain = $domain . $rootpath;
/**
 * 出错处理20170615
 */
error_reporting(E_ALL & ~E_NOTICE);
function error($tips="",$url="",$time=2)
{
	echo '<!DOCTYPE html>'."\n";
	echo '<html>'."\n";
	echo '<head>'."\n";
	echo '<meta charset="utf-8" />'."\n";
	echo '<meta http-equiv="expires" content="wed, 26 feb 1997 08:21:57 gmt">'."\n";
	echo '<title>安装向导</title>'."\n";
	echo '<style type="text/css">body{font-size:16px;font-family:"Microsoft Yahei","宋体","Tahoma","Arial"}.body{margin:100px auto auto auto;width:550px;border:1px solid #8F8F8F;}.red{color:red;}.body .tips{height:700px;}.body .tips .title{margin-left:70px;font-weight:bold;height:20px;padding-top:15px;}.body .tips .note{margin-left:70px;height:30px;}.body .tips .txt{margin-left:70px;padding-top:10px;line-height:50px;font-weight:bold;}</style>'."\n";
	echo '</head>'."\n";
	echo '<body>'."\n";
	echo '<div class="body"><div class="tips">';
	if($url){
		echo '<div class="title">'.$tips.'</div>';
		echo '<div class="note" ><a href="'.$url.'"><span class="red"></span>请 点 这 里 跳 转 404 页 面</div>';
	}else{
		echo '<div class="txt">'.$tips.'</div>';
	}
	echo '</div></div>';
	echo '</body>'."\n</html>";
	exit;
}
// 检测是否安装过
if(file_exists('./install.lock'))
{
    //打开许可文件读取安装许可信息20170512
	$content = file_get_contents('license.txt');
	//echo nl2br($content); 
    $content=nl2br($content);
    //文本文件换行符处理
    $url = '../errors/404.html';
	$time=10;
   	if(!file_exists($url)){
		exit("提示文件404不存在");
	}

  	error($content,$url,$time);
}

// 参数
$c = isset($_GET['c']) ? trim($_GET['c']) : '';

// 同意协议页面
if($c == 'agreement' || empty($c))
{
    exit(require './agreement.html');
}
// 检测环境页面
if($c == 'test')
{
    exit(require './test.html');
}
// 创建数据库页面

if($c == 'create')
{
    exit(require './create.html');
}
// 安装成功页面
if($c == 'success') {
    // 判断是否为post
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = $_POST;
        //判断配置文件目录是否存在 若不存在则创建
        if (!file_exists('../core/config')) {
            mkdir('../core/config'); //新建目录
        }
	//使用写的方式打开配置文件
/*使用文件的方式共有12种，文件使用方式:

“rt”       只读打开一个文本文件，只允许读数据

“wt”       只写打开或建立一个文本文件，只允许写数据

“at”       追加打开一个文本文件，并在文件末尾写数据

“rb”       只读打开一个二进制文件，只允许读数据

“wb”       只写打开或建立一个二进制文件，只允许写数据

“ab”       追加打开一个二进制文件，并在文件末尾写数据

“rt+”      读写打开一个文本文件，允许读和写

“wt+”      读写打开或建立一个文本文件，允许读写

“at+”      读写打开一个文本文件，允许读，或在文件末追加数据

“rb+”      读写打开一个二进制文件，允许读和写

“wb+”      读写打开或建立一个二进制文件，允许读和写

“ab+”      读写打开一个二进制文件，允许读，或在文件末追加数据*/
		
		
        $fp=fopen("../core/config/config.php", "at");    /*建立一个文字文件只写*/
        //法一：拼装向配置文件中写入的内容的字符串20170516
        $str .= "define('_PREFIX', '" . $_POST['DB_PREFIX'] . "');\n";
        $str .= "define('_PORT', '" . $_POST['DB_PORT'] . "');\n";
		$str .= "define('_TITLE', '" . $_POST['title'] . "');\n";
		$str .= "define('_DOMAIN', '" . $_POST['domain'] . "');\n";
		$str .= "define('_DIR', '" . $_POST['dir'] . "');\n";
        $str .= "define('_host', '" . $_POST['DB_HOST'] . "');\n";
        $str .= "define('_user', '" . $_POST['DB_USER'] . "');\n";
        $str .= "define('_password', '" . $_POST['DB_PWD'] . "');\n";
        $str .= "define('_dbname', '" . $_POST['DB_NAME'] . "');\n";        
        $str .= "?>";

        //将内容写入配置文件中
        if (!fwrite($fp, $str)) {
            die('创建配置文件失败！');
        }

       //导入数据并完成安装
	   exit(require './success.html');
	}
}
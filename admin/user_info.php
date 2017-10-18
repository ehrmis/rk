<?php
//用户注册/信息更新系统（MIS）************法一：20170503测试PHP5.3.29操作PdoMySQL成功！*********
//法二：medoo数据库框架20170606
//ver1.1.3支持各种数据：MySQL, MSSQL, SQLite, MariaDB, Oracle, Sybase, PostgreSQL等等，防止SQL注入，支持各种常见的SQL查询。
//20170608对PHP5.4-7.1操作medoo数据库框架测试成功！！*************************
//Mysql操作调试20160608
header("content-type:text/html;charset=utf-8");
//开启session会话
session_start();
if ( ! defined( 'ROOT' ) ) {
	$scriptName = !empty($_SERVER["REQUEST_URI"]) ? $scriptName = $_SERVER["REQUEST_URI"] : $scriptName = $_SERVER["PHP_SELF"];
	$rootpath = @preg_replace("/\/application\/(I|i)nstall\/index\.php(.*)$/", "", $scriptName);
	define("DIRNAME",strtok(ltrim($rootpath,"/"),"/"));
	if(!empty(DIRNAME)){
	   define("ROOT",$_SERVER['DOCUMENT_ROOT']."/".DIRNAME."/");
	}else{define("ROOT",$_SERVER['DOCUMENT_ROOT']."/");	
	}	
}	 
$user_name = strtolower($_POST["user"]);
$user_password = strtolower($_POST["passwd"]);
$user_qq = strtolower($_POST["qq"]);
//UTC世界标准时间慢北京时间8小时
//设置时区（中国）
date_default_timezone_set('PRC'); 
$cur_time = date("Y-m-d G:i:s");
//测试照片
$pic='../res/pic/201705180001.jpg';
$data=array( 'user' => $user_name, 'pass' => $user_password,'regtime' => $cur_time,'email' => $user_qq,'avatar' => $pic );
$tbName = 'user';
if ( isset( $_POST['add'] ) ) {
			if(version_compare(PHP_VERSION,'5.4.0','<'))
			{
			require_once(ROOT.'core/Medoo/pdomysql.php');
			$tbName = _PREFIX.'user'; 
			$sql = "SELECT * FROM {$tbName} WHERE user = '{$user_name}'";
            $arr = $db->getRow($sql);//真：返回该条记录数组；假：NULL
		    if($arr){
					echo "<script>alert('该用户名已被注册！');</script>"; 
			} else{
				$rows = $db->insert($tbName, $data);//返回1插入记录数或行数20170608				
				if ( $rows>0 ) {
					header("location:../main.html");
				} else {
					echo "<script>alert('用户注册失败！');</script>"; 
					<script type="text/javascript">
					window.location="../index.php";
					</script>
				}
				}			
			}else{	
			require_once(ROOT.'/core/Medoo/Medoo.php');		
			$_where="WHERE user = '{$user_name}'";
		    $in = $db->has($tbName,$_where);//是否在表中？返回真假值(bool)	    
			if ( $in ) {
				echo "<script>alert('该用户名已被注册！');</script>"; 	
			} else{
				$res = $db->insert($tbName, $data);//返回资源20170620		
				if ( $res ) {
				echo "<script>alert('用户注册成功！');</script>"; 
				} else {
				echo "<script>alert('用户注册失败！');</script>";
				<script type="text/javascript">
					window.location="../index.php";
				</script>
				}
            }			
}
}
if( isset(  $_POST['update'] ) ) {			
			if(version_compare(PHP_VERSION,'5.4.0','<'))
			{
			require_once('../core/Medoo/pdomysql.php');
			$tbName = _PREFIX.'user'; 
			$sql = "SELECT * FROM {$tbName} WHERE user = '{$user_name}'";
			$arr = $db->query($sql);//真：返回该条记录数组；假：NULL			
			if ( $arr ) {
			$_where="WHERE user = '{$user_name}'";
            $rows = $db->update($tbName, $data,$_where);//返回1更新记录数或行数20170608					
                if ( $rows>0 ) {
                     echo "<script>alert('信息修改成功！');</script>";
                } else {
                    echo "<script>alert('信息修改失败！');</script>";
                }

			} else {
				 echo "<script>alert('用户名不存在！');</script>";				
			}
			}else{	
			require_once(ROOT.'core/Medoo/Medoo.php');
			$tbName = 'user';	
			$_where="WHERE user = '{$user_name}'";
			$in = $db->has($tbName,$_where);//是否在表中？返回真假值(bool)	    
			if ( $in ) {
			   $res = $db->update($tbName, $data,$_where);	//返回资源20170608			
                
				if ( $res ) {
                    echo "<script>alert('信息修改成功！');</script>";
                } else {
                    echo "<script>alert('信息修改失败！');</script>";
                }

			} else {
				 echo "<script>alert('用户名不存在！');</script>";				
			}
			}
}
if( isset(  $_POST['del'] ) ) {
    if(version_compare(PHP_VERSION,'5.4.0','<'))
		{
			require_once('../core/Medoo/pdomysql.php');
			$tbName = _PREFIX.'user'; 
			$sql = "SELECT * FROM {$tbName} WHERE user = '{$user_name}'";
			$arr = $db->getRow($sql);//真：返回该条记录数组；假：NULL
		if($arr)
			{
			$_where="WHERE user = '{$user_name}'";
			$rows = $db->delete($tbName,$_where);
			if ( $rows>0 ) {
				 echo "<script>alert('信息删除成功！');</script>";
				} else {
				echo "<script>alert('用户删除失败！');</script>";				
			} 
		}else {
			echo "<script>alert('用户名不存在！');</script>";
		}
	}else{	
	require_once(ROOT.'core/Medoo/Medoo.php');
	$tbName = 'user';	
	$_where="WHERE user = '{$user_name}'";
	$in = $db->has($tbName,$_where);//是否在表中？返回真假值(bool)	    
	if ( $in ) {
        $res = $db->delete($tbName,$_where);
        if ( $res ) {
            echo "<script>alert('信息删除成功！');</script>";
        } else {
        echo "<script>alert('用户删除失败！');</script>";
        }

    } else {
        echo "<script>alert('用户名不存在！');</script>";
    }
    }
}	
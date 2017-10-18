<?php
/**
 * Created by PhpStorm.
 * User: rkgzs
 * Date: 2017/7/12
 * Time: 8:31
 *人员状况采集
 */
include_once('../core/init.php');
//提交表单后获取导入电子表文件
require_once ROOT.'core/lib/Upload.class.php';
$arr=$_FILES['Files'];
$upFile=new Upload($arr);
$filePath = $upFile->main();
$filesName = $upFile->getName();
$extName = $upFile->getExtName();
//指定上传目录
if($extName="xls" || $extName="xlsx" ){
$dir = ROOT.'temp/';
$filename = $dir.$filesName;
//将临时保存目录中的上传文件移动到指定上传目录
move_uploaded_file($filePath,$filename);
//电子表导入前处理：1.转换日期格式为"yyyy/mm/dd"文本（用VFP9快速搞定dtoc()）；2.电子表栏目顺序与employee必须相同
//***********3.电子表导入前先调整为“最合适的列宽”才能成功执行下一行代码！*20170720*****************************
if(!file_exists($filename)){
	header("location:../errors/500.html");
    exit("文件导入失败！请先上传电子表（{$filename}）");	
}
}else{
	header("location:../errors/500.html");
	exit("文件类型错误，请先上传电子表格式文件！");
}	
//导入或读取文件
//通过PHPExcel_IOFactory::load方法来载入一个文件，load会自动判断文件的后缀名来导入相应的处理类，读取格式含xlsx/xls/xlsm/ods/slk/csv/xml/gnumeric
require_once ROOT.'core/PHPExcel/PHPExcel.php';
/**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
$fileType=PHPExcel_IOFactory::identify($filename);
$objReader=PHPExcel_IOFactory::createReader($fileType);
//加载文件：自动加载电子表格式类型文件********20170720*****************
$objPHPExcel=$objReader->load($filename);
/*
//*********************法一：全部加载并且全部读取****（简单粗暴，PHP内存开销太大）**************************
//引入读取excel的类文件
//全部读取
//获取excel文件里有多少个sheet
$sheetCount = $objPHPExcel->getSheetCount();
for($i=0; $i<$sheetCount; $i++){
    //读取每个sheet里的数据 全部放入到数组中备用20170712
    $data = $objPHPExcel->getSheet($i)->toArray();
    echo '<pre>';

}
p($data);exit();
*/
//******************法二：循环读取excel文件,读取一条,插入一条*****（优化缓存机制，边读取电子表边存储到MySQL，PHP内存开销小）*******************************


//正式导入数据
//1.先读取电子表
$sheet=$objPHPExcel->getSheet(0);//默认获取第一个工作表
$highestRow=$sheet->getHighestRow();//取得总行数
$highestColumn=$sheet->getHighestColumn(); //取得总列数
//重复采集时最好先清空表再采集数据，并且必须限制数据大小20170717
//人员信息采集：用phpMyAdmin清空人员表employee再导入电子表，确保eid是从1开始记录20170717
    ini_set("memory_limit", "1024M");
    require_once(ROOT.'core/Medoo/Medoo.php');
    $tbName = 'employee';
	//清空后添加导入信息*********20170726***************
	if ( isset( $_POST['selected1'] ) ){
	$_where="WHERE eid>0";
	$res = $db->delete($tbName,$_where);   
	}
for($j=2;$j<=$highestRow;$j++){//从第一行开始读取数据
    $str='';
    for($k='A';$k<=$highestColumn;$k++){            //从A列读取数据
        //这种方法简单，但有不妥，以'\\'合并为数组，再分割\\为字段值插入到数据库,实测在excel中，如果某单元格的值包含了\\导入的数据会为空
        $str.=$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue().'\\';//读取单元格
    }
    //explode:函数把字符串分割为数组。
    $strs=explode("\\",$str);  
//2.再插入数据库****************20170724***********************************	
     $sql="INSERT INTO `"._PREFIX."employee`(`empno`,`compno`,`comp`,`deptno`,`dept`,`classno`,`class`,`poscode`,
    `pos`,`employing`,`eName`,`sex`,`birt`,`workt`,`contr`,`contt`,`educode`,`edu`,
    `procode`,`pro`,`titlecode`,`jobt`,`idcard`,`phone`,`addr`,`poli`) VALUES (
 '{$strs[0]}',
 '{$strs[1]}',
 '{$strs[2]}',
 '{$strs[3]}',
 '{$strs[4]}',
 '{$strs[5]}',
 '{$strs[6]}',
 '{$strs[7]}',
 '{$strs[8]}',
 '{$strs[9]}',
 '{$strs[10]}',
 '{$strs[11]}',
 '{$strs[12]}',
 '{$strs[13]}',
 '{$strs[14]}',
 '{$strs[15]}',
 '{$strs[16]}',
 '{$strs[17]}',
 '{$strs[18]}',
 '{$strs[19]}',
 '{$strs[20]}',
 '{$strs[21]}',
 '{$strs[22]}',
 '{$strs[23]}',
 '{$strs[24]}',
 '{$strs[25]}')";
  $res=$db->query($sql);//这里执行的是插入数据库操作201712
}
if($res){ 
echo "<script>alert('人员信息导入成功！');</script>";
header("location:../main.php");
}
else
{
echo "<script>alert('人员信息导入失败！');</script>";
header("location:../errors/500.html");
}
unlink($filename); //删除excel文件
<?php
/**
 * Created by PhpStorm.
 * User: rkgzs
 * Date: 2017/7/20
 * Time: 14:28
 */
//首先是使用PHP Reader 读取Excle内容（注：使用PHP 读取excel文件内容，一般都是处理整理好格式的csv或者excel，也可以读取xml文件）
include_once('../core/init.php');
$dir = ROOT.'temp';
//导入或读取文件
//通过PHPExcel_IOFactory::load方法来载入一个文件，load会自动判断文件的后缀名来导入相应的处理类，读取格式含xlsx/xls/xlsm/ods/slk/csv/xml/gnumeric
include_once(ROOT.'core/PHPExcel/PHPExcel.php');
$filename = $dir.'/emp.xls';
if(!file_exists($filename)){
    die("请先上传电子表（{$filename}）");
}
$PHPExcel = new PHPExcel();
/**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
$PHPReader = new PHPExcel_Reader_Excel2007();
if(!$PHPReader->canRead($filename)){
    $PHPReader = new PHPExcel_Reader_Excel5();
}
//***********电子表导入前先调整为“最合适的列宽”才能成功执行下一行代码！*20170720*****************************
$PHPExcel = $PHPReader->load($filename);
/**读取excel文件中的第一个工作表*/
$currentSheet = $PHPExcel->getSheet(0);
/**取得最大的列号*/
$allColumn = $currentSheet->getHighestColumn();
/**取得一共有多少行*/
$allRow = $currentSheet->getHighestRow();
/**从第二行开始输出，因为excel表中第一行为列名*/
for($currentRow = 2;$currentRow <= $allRow;$currentRow++){
    /**从第A列开始输出*/
    for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
        $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
        if($currentColumn == 'A')
        {
            echo iconv('utf-8','gb2312', $val)."\t";
            echo $val."\t";
        }else{
//echo $val;
            /**如果输出汉字有乱码，则需将输出内容用iconv函数进行编码转换，如下将gb2312编码转为utf-8编码输出*/
            echo iconv('utf-8','gb2312', $val)."\t";
            echo $val;
        }
    }
    echo "</br>";
}
echo "\n";
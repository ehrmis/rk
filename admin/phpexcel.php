<?php
/**
 * Created by PhpStorm.
 * User: rkgzs
 * Date: 2017/7/18
 * Time: 13:21
 */
//参考文档：**********20170718***************
//（一）导入Excel
//第一，在前台html页面进行上传文件：如：
<form method="post" action="php文件" enctype="multipart/form-data">
         <h3>导入Excel表：</h3><input  type="file" name="file_stu" />
           <input type="submit"  value="导入" />
</form>
//第二，在对应的php文件进行文件的处理
 if (! empty ( $_FILES ['file_stu'] ['name'] ))
 {
     $tmp_file = $_FILES ['file_stu'] ['tmp_name'];
     $file_types = explode ( ".", $_FILES ['file_stu'] ['name'] );
     $file_type = $file_types [count ( $file_types ) - 1];
     /*判别是不是.xls文件，判别是不是excel文件*/
     if (strtolower ( $file_type ) != "xls")
     {
         $this->error ( '不是Excel文件，重新上传' );
     }
     /*设置上传路径*/
     $savePath = SITE_PATH . '/public/upfile/Excel/';
     /*以时间来命名上传的文件*/
     $str = date ( 'Ymdhis' );
     $file_name = $str . "." . $file_type;
     /*是否上传成功*/
     if (! copy ( $tmp_file, $savePath . $file_name ))
     {
         $this->error ( '上传失败' );
     }
     /*
        *对上传的Excel数据进行处理生成编程数据,这个函数会在下面第三步的ExcelToArray类中
       注意：这里调用执行了第三步类里面的read函数，把Excel转化为数组并返回给$res,再进行数据库写入
     */
     $res = Service ( 'ExcelToArray' )->read ( $savePath . $file_name );
     /*
          重要代码 解决Thinkphp M、D方法不能调用的问题
          如果在thinkphp中遇到M 、D方法失效时就加入下面一句代码
      */
     //spl_autoload_register ( array ('Think', 'autoload' ) );
     /*对生成的数组进行数据库的写入*/
     foreach ( $res as $k => $v )
     {
         if ($k != 0)
         {
             $data ['uid'] = $v [0];
             $data ['password'] = sha1 ( '111111' );
             $data ['email'] = $v [1];
             $data ['uname'] = $v [3];
             $data ['institute'] = $v [4];
             $result = M ( 'user' )->add ( $data );
             if (! $result)
             {
                 $this->error ( '导入数据库失败' );
             }
         }
     }
 }
 //第三：ExcelToArrary类,用来引用phpExcel并处理Excel数据的
 class ExcelToArrary extends Service{
     public function __construct() {
         /*导入phpExcel核心类    注意 ：你的路径跟我不一样就不能直接复制*/
         include_once('./Excel/PHPExcel.php');
     }
     /**
      * 读取excel $filename 路径文件名 $encode 返回数据的编码 默认为utf8
      *以下基本都不要修改
      */
     public function read($filename,$encode='utf-8'){
         $objReader = PHPExcel_IOFactory::createReader('Excel5');
         $objReader->setReadDataOnly(true);
         $objPHPExcel = $objReader->load($filename);
         $objWorksheet = $objPHPExcel->getActiveSheet();
         　　　 $highestRow = $objWorksheet->getHighestRow();
　　　 $highestColumn = $objWorksheet->getHighestColumn();
　　    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
 　　   $excelData = array();
 　　　for ($row = 1; $row <= $highestRow; $row++) {
             　　  for ($col = 0; $col < $highestColumnIndex; $col++) {
                 $excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
             }
         }
        return $excelData;
    }
 }
 //（二）Excel的导出（相对于导入简单多了）
//第一，先查出数据库里面要生成Excel的数据，如：
$data= M('User')->findAll();   //查出数据
$name='Excelfile';    //生成的Excel文件文件名
$res=service('ExcelToArrary')->push($data,$name);
//第二，ExcelToArrary类,用来引用phpExcel并处理数据的
class ExcelToArrary extends Service{
    public function __construct() {
        /*导入phpExcel核心类    注意 ：你的路径跟我不一样就不能直接复制*/
        include_once('./Excel/PHPExcel.php');
    }
    /* 导出excel函数*/
    public function push($data,$name='Excel'){
        error_reporting(E_ALL);
        date_default_timezone_set('Europe/London');
        $objPHPExcel = new PHPExcel();
        /*以下是一些设置 ，什么作者  标题啊之类的*/
        $objPHPExcel->getProperties()->setCreator("转弯的阳光")
            ->setLastModifiedBy("转弯的阳光")
            ->setTitle("数据EXCEL导出")
            ->setSubject("数据EXCEL导出")
            ->setDescription("备份数据")
            ->setKeywords("excel")
            ->setCategory("result file");
        /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
        foreach($data as $k => $v){
            $num=$k+1;
            $objPHPExcel->setActiveSheetIndex(0)
                //Excel的第A列，uid是你查出数组的键值，下面以此类推
                ->setCellValue('A'.$num, $v['uid'])
                ->setCellValue('B'.$num, $v['email'])
                ->setCellValue('C'.$num, $v['password'])
            }
        $objPHPExcel->getActiveSheet()->setTitle('User');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    //（三）初始化处理
$filePath = 'test.xlsx';

$PHPExcel = new PHPExcel();

    /**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
$PHPReader = new PHPExcel_Reader_Excel2007();
if(!$PHPReader->canRead($filePath)){
$PHPReader = new PHPExcel_Reader_Excel5();
if(!$PHPReader->canRead($filePath)){
echo 'no Excel';
return ;
}
}

$PHPExcel = $PHPReader->load($filePath);
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
            echo GetData($val)."\t";
        }else{
//echo $val;
            /**如果输出汉字有乱码，则需将输出内容用iconv函数进行编码转换，如下将gb2312编码转为utf-8编码输出*/
            echo iconv('utf-8','gb2312', $val)."\t";
        }
    }
    echo "</br>";
}
echo "\n";
?>

//（四）其它方法
//首先是使用PHP Reader 读取Excle内容（注：使用PHP 读取excel文件内容，一般都是处理整理好格式的csv或者excel，也可以读取xml文件）
require("http://www.jb51.net/PHPExcel/Classes/PHPExcel.php");
$file = "D:\\datas.xlsx";
if(!file_exists($file)){
die("no file found in {$file}");
}
$datasReader = PHPExcel_IOFactory::load($file);
$sheets = $datasReader->getAllSheets();
//如果有多个工作簿
$countSheets = count($sheets);
$sheetsinfo = array();
$sheetData = array();
if($countSheets==1){
$sheet = $sheets[0];
$sheetsinfo["rows"] = $sheet->getHighestRow();
$sheetsinfo["column"] = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());
for($row=1;$row<=$sheetsinfo["rows"];$row++){
for($column=0;$column<$sheetsinfo["column"];$column++){
$sheetData[$column][$row] = $sheet->getCellByColumnAndRow($column, $row)->getValue();
}
}
}else{
foreach ($sheets as $key => $sheet)
{
$sheetsinfo[$key]["rows"] = $sheet->getHighestRow();
$sheetsinfo[$key]["column"] = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());
for($row=1;$row<=$sheetsinfo[$key]["rows"];$row++){
for($column=0;$column<$sheetsinfo[$key]["column"];$column++){
$sheetData[$key][$column][$row] = $sheet->getCellByColumnAndRow($column, $row)->getValue();
}
}
}
}
echo "<pre>";
print_r($sheetData);
echo "</pre>";
//PHPExcel生成Exceel
$sql = sprintf("select * from table where op_id=%d", intval($this->params['id']));
$query = $this->_db->query($sql);
require_once './PHPExcel_1.7.4/Classes/PHPExcel.php';
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->setCellValue('A1', "{$this->_packInfos['o_id']}");
$objPHPExcel->getActiveSheet()->setCellValue('B1', "Volume weight (kg)");
$objPHPExcel->getActiveSheet()->setCellValue('D1', "Actual weight (kg)");


$objPHPExcel->getActiveSheet()->setCellValue('A2', "Box No.");
$objPHPExcel->getActiveSheet()->setCellValue('B2', "Products");
$objPHPExcel->getActiveSheet()->setCellValue('C2', "Shipping Box");
$objPHPExcel->getActiveSheet()->setCellValue('D2', "System");
$objPHPExcel->getActiveSheet()->setCellValue('E2', "Input");
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->mergeCells("B1:C1");
$objActSheet->mergeCells("D1:E1");

$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getStyle('A2'.($i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('B2'.($i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('C2'.($i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('D2'.($i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('E2'.($i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

if($this->_db->num_rows($query)>0)
{
$i=3;
while ($row = $this->_db->fetch_assoc($query))
{
$objPHPExcel->getActiveSheet()->setCellValue('A'.($i),"BOX ".$row['box_num']);
$objPHPExcel->getActiveSheet()->setCellValue('B'.($i),sprintf("%.2f",$row['volume_weight']));
$objPHPExcel->getActiveSheet()->setCellValue('C'.($i),sprintf("%.2f",$row['box_weight']));
$objPHPExcel->getActiveSheet()->setCellValue('D'.($i),sprintf("%.2f",$row['system_weight']));
$objPHPExcel->getActiveSheet()->setCellValue('E'.($i),sprintf("%.2f",$row['real_weight']));

$objPHPExcel->getActiveSheet()->getStyle('A'.($i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('B'.($i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('C'.($i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('D'.($i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('E'.($i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$i++;
}
}

$fileName="exportBox.xls";
$filePath = dirname(dirname("__FILE__"))."/template/".$fileName;
$path = "./template/".$fileName;
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
if(file_exists($path)){
chmod($path, 0777);
unlink($path);
$objWriter->save($path);
header('application/vnd.ms-excel');
header('Content-Disposition: attachment;filename=weight-'.$this->_packInfos["o_id"].".xlsx");
readfile($filePath);
die();
}
else
{
$objWriter->save($path);
header('application/vnd.ms-excel');
header('Content-Disposition: attachment;filename=weight-'.$this->_packInfos["o_id"].".xlsx");
readfile($filePath);
die();
}
//注：上面的php生成excel的方式是直接使用A标签形式的，如果使用ajax，可以不使用header，直接echo $path,前台window.location.href=返回来的path就可以了。
//（五）相比于PHPExcel，PHP_XLSXWriter是一个小而强悍的Excel读写插件，它并没有PHPExcel功能丰富，很多高级操作比如冻结表头，并不具备，但是它导出速度非常快，非常适合于数据量特别大，报表格式不是很复杂的导出需求。
//writer 类

use core/PHP_XLSXWriter；
$writer = new XLSXWriter();
//文件名
$filename = "example.xlsx";
//设置 header，用于浏览器下载
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

//导出的数据
$string = array (
0 =>
array (
'payc_bill_time' => '2017-07-12 16:40:44',
'payt_received_date' => '2017-07-12',
'ci_name' => '租金',
'payt_num' => 'YRZB(2012)A0047',
'payt_scsr_name' => '李巧红',
'payt_received' => '300.00',
'paytd_type' => '现金',
'emp_name' => '郑振标',
),
1 =>
array (
'payc_bill_time' => '2017-07-12 16:39:55',
'payt_received_date' => '2017-07-12',
'ci_name' => '租金',
'payt_num' => 'YRZB(2012)A0046',
'payt_scsr_name' => '22222',
'payt_received' => '45.00',
'paytd_type' => '现金',
'emp_name' => '郑振标',
)
);
//每列的标题头
$title = array (
0 => '开单时间',
1 => '收款时间',
2 => '开票项目',
3 => '票据编号',
4 => '客户名称',
5 => '实收金额',
6 => '收款方式',
7 => '收款人',
);
//工作簿名称
$sheet1 = 'sheet1';

//对每列指定数据类型，对应单元格的数据类型
foreach ($title as $key => $item){
$col_style[] = $key ==5 ? 'price': 'string';
}

//设置列格式，suppress_row: 去掉会多出一行数据；widths: 指定每列宽度
$writer->writeSheetHeader($sheet1, $col_style, ['suppress_row'=>true,'widths'=>[20,20,20,20,20,20,20,20]] );
//写入第二行的数据，顺便指定样式
$writer->writeSheetRow($sheet1, ['xxx财务报表'],
['height'=>32,'font-size'=>20,'font-style'=>'bold','halign'=>'center','valign'=>'center']);

/*设置标题头，指定样式*/
$styles1 = array( 'font'=>'宋体','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee',
'halign'=>'center', 'border'=>'left,right,top,bottom');
$writer->writeSheetRow($sheet1, $title,$styles1);
// 最后是数据，foreach写入
foreach ($data as $value) {
foreach ($value as $item) { $temp[] = $item;}
$rows[] = $temp;
unset($temp);
}
$styles2 = ['height'=>16];
foreach($rows as $row){
$writer->writeSheetRow($sheet1, $row,$styles2);
}

//合并单元格，第一行的大标题需要合并单元格
$writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=7);
//输出文档
$writer->writeToStdOut();
exit(0);
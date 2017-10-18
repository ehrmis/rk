<?php
/**
 * Created by PhpStorm.
 * User: rkgzs
 * Date: 2017/7/11
 * Time: 10:07
 */
include_once('../core/init.php');
$dir = ROOT.'temp';
    require_once(ROOT.'core/Medoo/Medoo.php');
    include_once(ROOT.'core/PHPExcel/PHPExcel.php');
    $objPHPExcel = new PHPExcel();
	set_time_limit(0);  //防止脚本超时
    ini_set("memory_limit", "1024M"); //限制数据大小

$grade = array('1','2');

for($i=0; $i<count($grade); $i++){
    if($i>0){
        //创建新的内置表
        $objPHPExcel->createSheet();
    }
    //把新创建的sheet设定为当前活动sheet
    $objPHPExcel->setActiveSheetIndex($i);
    //获取当前活动sheet
    $objSheet = $objPHPExcel->getActiveSheet();
    //设置表名
    $objSheet->setTitle($grade[$i]);
    //设置表头
    $objSheet->setCellValue('A1','工号')->setCellValue('B1','姓名')->setCellValue('C1','出生')->setCellValue('D1','职称');
    //查询信息
    $data = $db->select("employee", [
        "ename",
        "birt",
        "jobt"
    ], [
        "eid[>]" => 0
    ]);

    $j = 2;
    //存储变量的值非常庞大，内存消耗惊人
    foreach ($data as $value) {
        $objSheet->setCellValue('A'.$j, $j-1)->setCellValue('B'.$j, $value['ename'])->setCellValue('C'.$j, $value['birt'])->setCellValue('D'.$j, $value['jobt']);
        $j++;
    }
}
   //确保用户打开excel看到的是第一个sheet
  $objPHPExcel->getActiveSheet()->setTitle('选矿第二项目部');
  $objPHPExcel->setActiveSheetIndex(0);
    //生成excel文件
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    //保存文件
    //$objWriter->save($dir.'/export_user.xls');
//firefox浏览器输出****20170711*********
$type='Excel5';
$filename=urlencode('员工信息统计表').'_'.date('Y-m-d');
browser_export($type, $filename);
$objWriter->save('php://output');

function browser_export($type, $filename){
    if($type == 'Excel5'){
        //告诉浏览器要输出excel03文件
        header('Content-Type: application/vnd.ms-excel');
    }else{
        //告诉浏览器输出excel07文件
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
    //告诉浏览器输出文件的名称
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    //禁止缓存
    header('Cache-Control: max-age=0');
}
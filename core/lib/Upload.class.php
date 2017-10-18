<?php
//文件上传的操作类
class Upload{
	public $arr;
	function __construct($arr){
		$this->arr = $arr;
	}
	//得到图片或上传文件全名（不含路径）
	function getName(){
		$filesName=$this->arr["name"];       		
		return $filesName;
	}
	//得到图片或文件的扩展名
	function getExtName(){
		$tagArr=explode(".", $this->arr["name"]);		
        $tag_sel = array_shift($tagArr);
		$extName = strtolower(end($tagArr));		
		return $extName;
	}
	function checkType($extName){
		if($extName!="jpg" && $extName!="gif" && $extName!="png" && $extName!="xls" && $extName!="xlsx"){
				 echo "<script>alert('上传文件类型不正确，请重新上传！');</script>";
				exit;
		}
	}
	
	//判断文件的大小
	function checkSize(){
		if($this->arr["size"]>20000000){
			echo "文件过大，请重新处理后上传";
			exit;
		}
	}
	
	//实现文件上传的主方法
	function main(){
		$extName = $this->getExtName();
		$this->checkType($extName);
		$this->checkSize();
		if($extName="xls" || $extName="xlsx" ){
		$filePath = $this->arr["tmp_name"];	
		return $filePath;	
		}else{//创建上传图片目录:
		$fileUpload = "upload/";
		if(!is_dir($fileUpload)){
			mkdir($fileUpload);
		}
		$fileDateDir = $fileUpload.date("Ymd")."/";
		if(!is_dir($fileDateDir)){
			mkdir($fileDateDir);
		}
		//重命名文件 ,执行文件上传
		$fileName = $fileDateDir.time().rand(1000,9999).".".$extName;
		move_uploaded_file($this->arr["tmp_name"], $fileName);
		return $fileName;
		}
	}
}


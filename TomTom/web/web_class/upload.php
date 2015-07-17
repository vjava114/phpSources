<?php

class UploadSvc {

	var $date_dir;
    var $real_dir;
	function InsertUploadList($p, $f, $j, $tname, $cname){

		// 마더보드의 인덱스 구하기
		if (empty($j) == "1"){
			$job_cd = parent::MaxJobCd($tname,$cname);
		}else{
			$job_cd = $j;
		}

		$count = count($p);
		for($i = 0; $i < $count; $i++){

			$arrupload = explode("||", $p[$i]); // (파일인덱스||원본파일||저장파일명||경로)
			if(!empty($arrupload[2]) == "1"){
				if (empty($f[$i]) == "1"){
					$file_idx = parent::MaxUploadIdx();	// 파일디비의 가장 높은 인덱스 구해옴(identity 설정을 안했음.)
				}else{
					$file_idx = $f[$i];
				}

				$file_ext = end(explode('.', $arrupload[2]));	// 확장자

				$file = Array();
				$file[] = $job_cd;
				$file[] .= $file_idx;
				$file[] .= $arrupload[0];
				$file[] .= $file_ext;
				$file[] .= $arrupload[1];
				$file[] .= $arrupload[2];
				$file[] .= $arrupload[3];

				if (!empty($f[$i]) == "1"){
					//update
					parent::UpdateUpload($file);

				}else{
					//insert
					parent::CreateUpload($file);
				}

				unset($file);
			}
		}
	}

	//pns
	// 폴더 생성(폴더명/년/월)
	function make_folder($filefolder)
	{
		global $UP_DIR;
		$this->date_dir = date("Y",time())."/".date("m",time());
		$this->real_dir = $filefolder."/".$this->date_dir;
		$arrayPath = explode("/", $this->real_dir);
	 	$no = sizeof($arrayPath);
	 	$makefoler = $UP_DIR;
	 	
	 	for ($i=0 ; $i<$no ; $i++) {
			$makefoler .= "/".$arrayPath[$i];
			if(!is_dir($makefoler)){
				@mkdir($makefoler, 0777);
			}
		}
		return $this->real_dir;
	}
	//This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
	function let_to_num($v){
	     $l = substr($v, -1);
	     $ret = substr($v, 0, -1);
	     switch(strtoupper($l)){
	     case 'P':
	         $ret *= 1024;
	     case 'T':
	         $ret *= 1024;
	     case 'G':
	         $ret *= 1024;
	     case 'M':
	         $ret *= 1024;
	     case 'K':
	         $ret *= 1024;
	         break;
	     }
	     return $ret;
	 }

	//이미지 확인 함수
	function images_check($files, $max_size = 0 /* MByte */, $allowext = array('gif', 'jpeg', 'png', 'jpg', 'bmp', 'pptx', 'xls'))
	{
		//카운트 수
		$count = count($files["name"]);
		if (!$max_size) {
			$max_size  = $this->let_to_num(ini_get("upload_max_filesize" ));
			$max_size /= 1024*1024;
		}
		$max_size = $max_size * 1024 * 1024;    // 바이트로 계산한다. 1MB = 1024KB = 1048576Byte
		
		//이미지 파일
		for ($i = 0; $i < $count; $i++) {
			if ( !$files['name'][$i]) continue;
			if($files["size"][$i]){

				//파일 사이즈 체크
				if ($files["size"][$i] > $max_size){
					echo "<script language='javascript'>alert('파일 용량이 올바르지 않습니다.');</script>";
					exit;
				}
				/*
				//파일 이미지 체크
				$file_ext = end(explode('.', $files['name'][$i]));

				// Maker: Okayjava  Date : 2012-11-01 20:30:09
				// Desc : 대소문자 구분 안하도록 확장자를 소문자로 변경 후 이미지 확인
				if(!in_array( strtolower($file_ext), $allowext, true )) // 확장자를 검사한다.
				{
					echo "<script language='javascript'>alert('[$file_ext]확장자가 올바르지 않습니다.');$url;</script>";
					exit;
				}

				//파일 업로드시 에러
				if ($files["error"][$i] > 0){
					$msg = "Error: " . $files["error"][$i] . "<br />";
					$url = "history.go(-1)";
					echo "<script language='javascript'>alert('$msg');$url;</script>";
					exit;
				}

				//파일 이미지 체크
				$image_check=false;
				if (($files["type"][$i] == "image/gif")|| ($files["type"][$i] == "image/jpeg")|| ($files["type"][$i] == "image/pjpeg")){
					$image_check=true;
				}
				if ($image_check=false) {
					echo "<script language='javascript'>alert('확장자 올바르지 않습니다.');</script>";
					exit;
				}
				*/
			} else {
				// 파일 용량 초과일 경우 size가 0 2012-11-15 Okayjava
				goURL("단일 파일[".($files['name'][$i])."]용량이 ". ini_get("upload_max_filesize" )."를 넘을 수 없습니다.","back");

			}
		}//for문
	}
	
	//파일 확인 함수
	//aoa
	function files_check($files, $max_size = 0 /* MByte */, $allowext)
	{
		//echo $allowext."<br>";
		//카운트 수
		//$count = count($files["name"]);
		
		if (!$max_size) {
			$max_size  = $this->let_to_num(ini_get("upload_max_filesize" ));
			$max_size /= 1024*1024;
		}
		$max_size = $max_size * 1024 * 1024;    // 바이트로 계산한다. 1MB = 1024KB = 1048576Byte
		
		//echo $files['name']."<br>";
		//echo $files['size']."<br>";
		
		//파일
		//for ($i = 0; $i < $count; $i++) {
			if ( !$files['name']) continue;
			if($files["size"]){
				
				//파일 사이즈 체크
				if ($files["size"] > $max_size){
					echo "파일 용량이 올바르지 않습니다.'";
					exit;
				}
				
				//파일  체크
				$file_ext = end(explode('.', $files['name']));

				// Maker: Okayjava  Date : 2012-11-01 20:30:09
				// Desc : 대소문자 구분 안하도록 확장자를 소문자로 변경 후 이미지 확인
				/*echo strtolower($file_ext)."<br>";
				echo $allowext."<br>";
				
				echo "asdf=".eregi(strtolower($file_ext), $allowext)."<br>";
				echo "asdf=".eregi("png", $allowext)."<br>";*/
				
				//exit;
				if(!eregi(strtolower($file_ext), $allowext)) // 확장자를 검사한다.
				{
					echo "[$file_ext]확장자가 올바르지 않습니다.";
					exit;
				}

				//파일 업로드시 에러
				if ($files["error"] > 0){
					$msg = "Error: " . $files["error"] . "<br />";
					$url = "history.go(-1)";
					echo $msg;
					exit;
				}
			} else {
				// 파일 용량 초과일 경우 size가 0 2012-11-15 Okayjava
				echo "단일 파일[".($files['name'])."]용량이 ". ini_get("upload_max_filesize" )."를 넘을 수 없습니다.";
				exit;
			}
			//exit;
		//}//for문
	}
	
	//이미지 저장 함수
	function upload($files,$formname) {
		$count=count($files["name"]);
		for ($i = 0; $i < $count; $i++){
			if($files["name"][$i]){

				$file_ext = end(explode('.', $files['name'][$i]));
				$file_name = date("YmdHis",time()).'_'.$this->get_filename(5).'.'.$file_ext;
				// 중복된 파일이 업로드 될수 있으므로 time함수를 이용해 unixtime으로 파일이름을 만들어주고
				// 그 후 파일 확장자를 붙여줍니다.

				global $UPLOAD_PATH;

				$path = $UPLOAD_PATH.$this->real_dir.'/'.$file_name;
				// 파일 저장 경로를 만들어 줍니다.

				if (file_exists($path)){
					echo "<script language='javascript'>alert('같은이름의 파일이 존재합니다.');</script>";
					exit;
				}else{
					move_uploaded_file($files["tmp_name"][$i], $path );
					echo "<script language='javascript'>
					parent.document.forms['".$formname."']['upload[]'][".$i."].value = '".($i+1)."||".$files["name"][$i]."||".$file_name."||".$this->real_dir."';
					</script>
					";
				}
			}
		}
		echo "<script language='javascript'>parent.doSaveSubmit();</script>";
	}


	function UploadPreload($files, $idxs)
	{
		$count=count($files["name"]);
		$ret = array();

		for ($i = 0; $i < $count; $i++){

			if($files["name"][$i]){

				// 이전파일 삭제
				if (empty($idxs[$i]) != "1"){
					$result = $this->ReadFileName($idxs[$i]);
					if($result != NULL) {
						$path = "../../../web_upload/".$result[stfile_dr]."/".$result[stfile_nm];
						$this->upload_del($path);
					}
				}

				$file_ext = end(explode('.', $files['name'][$i]));
				$file_name = date("YmdHis",time()).'_'.$this->get_filename(5).'.'.$file_ext;
				// 중복된 파일이 업로드 될수 있으므로 time함수를 이용해 unixtime으로 파일이름을 만들어주고
				// 그 후 파일 확장자를 붙여줍니다.

				global $UPLOAD_PATH;

				$path = $UPLOAD_PATH.$this->real_dir.'/'.$file_name;
				// 파일 저장 경로를 만들어 줍니다.

				if (file_exists($path)){
					//echo "<script language='javascript'>alert('같은이름의 파일이 존재합니다.');</script>";
					$ret = "false";
					break;
				}else{
					move_uploaded_file($files["tmp_name"][$i], $path );
					
					$ret[] = ($i+1)."||".$files["name"][$i]."||".$file_name;
				}
			}else{
				$ret[] = ($i+1)."||||";
			}
		}
		return $ret;
	}
	

	function ExtCheck($file, $ext)
	{
		if ( is_array($file["name"]) > 1 )
		{
			foreach ( $file[name] as $filepath)
			{
				if ( !self::ExtCheck($val, $ext)) return false;
				return true;
			}
		} else {
			$x = split(".",$file['name']);
			$ext = $x[count($x)-1];
			$arExts = split(",", $ext);

			return in_array($ext, $arExts);
		}
	}


	//=====================================================================
	// Maker: Okayjava  Date : 2012-11-25 16:50:36
	// Desc : 확장자 명을 입력받아서 체크 할 수 있도록
	// $image_check : Case 1 ; boolean type : 이미지 체크 할것이냐 말것이냐.
	//				 Case 2 : extend string : 체크할 확장자를 , 로 구분하여 입력  Ex) "xls,xlsx,doc,docx"
	//						: 오류시 "ExtError" return
	//=====================================================================

	// aoa
	function UploadFiles($files,$filefolder,$allowext)
	{
		if($files == NULL) return NULL;
		global $UP_DIR;

		$result = "";
		
		if($allowext) self::files_check($files,1024*1024*5, $allowext);		// 파일 체크
		self::make_folder($filefolder);  			// 폴더 생성
		
		
		
		/*for ($i = 0; $i < count($files[name]); $i++){
			if($files["name"][$i]){
				for($j = 0; $j < 10; $j++) {
					$file_ext = end(explode('.', $files['name'][$i]));
					$file_name = date("YmdHis",time()).'_'.$this->get_filename(5).'.'.$file_ext;
					$path = $UP_DIR.$this->real_dir.'/'.$file_name;
					
					if (!file_exists($path))
						break;
				}
				move_uploaded_file($files["tmp_name"][$i], $path );
				$file_data = array();
				$file_data[orgfile_nm] = $files["name"][$i];
				$file_data[stfile_nm] = $file_name;
				$file_data[stfile_dr] = $this->real_dir;
				$result[] = $file_data;
			} else {
				$result[] = NULL;
			}
		}*/
	
		if($files["name"]){
			for($j = 0; $j < 10; $j++) {
				$file_ext = end(explode('.', $files['name']));
				$file_name = date("YmdHis",time()).'_'.$this->get_filename(5).'.'.$file_ext;
				$path = $UP_DIR."/".$this->real_dir.'/'.$file_name;
				//echo "111=".$path."<br>";
				
				if (!file_exists($path))
					break;
			}
			//echo $path."<br>";
			move_uploaded_file($files["tmp_name"], $path );
			$file_data = array();
			$file_data[orgfile_nm] = $files["name"];
			$file_data[stfile_nm] = $file_name;
			$file_data[stfile_dr] = $this->real_dir;
			$result= $file_data;
		} else {
			$result= NULL;
		}
		
		return $result;
	}

	//이미지 저장 함수
	function uploadupdate($files,$formname) {
		$count=count($files["name"]);
		for ($i = 0; $i < $count; $i++){
			if($files["name"][$i]){

				$file_ext = end(explode('.', $files['name'][$i]));
				$file_name = date("YmdHis",time()).'_'.$this->get_filename(5).'.'.$file_ext;
				// 중복된 파일이 업로드 될수 있으므로 time함수를 이용해 unixtime으로 파일이름을 만들어주고
				// 그 후 파일 확장자를 붙여줍니다.

				global $UPLOAD_PATH;

				$path = $UPLOAD_PATH.$this->real_dir.'/'.$file_name;
				// 파일 저장 경로를 만들어 줍니다.

				if (file_exists($path)){
					echo "<script language='javascript'>alert('같은이름의 파일이 존재합니다.');</script>";
					exit;
				}else{
					//echo $path."<br>";
					move_uploaded_file($files["tmp_name"][$i], $path );
					echo ($i+1)."||".$files["name"][$i]."||".$file_name."||".$this->real_dir."<br>";
					echo "<script language='javascript'>
					parent.document.forms['".$formname."']['upload[]'][".$i."].value = '".($i+1)."||".$files["name"][$i]."||".$file_name."||".$this->real_dir."';
					</script>
					";
				}
			}
		}
		echo "<script language='javascript'>parent.doSaveSubmit();</script>";
	}


	// 파일 삭제 함수
	function upload_del($path) {
		if(is_file($path)){
			unlink($path);
		}
	 }

	function DeleteFiles($files) {
		global $UPLOAD_PATH;

	 	foreach ($files as $key => $value) {
	 		self::upload_del($UPLOAD_PATH.$value[stfile_dr]."/".$value[stfile_nm]);
	 	}
	}

	function DeleteFilesOld($files) {
	 	global $UPLOAD_PATH;
	 	foreach ($files as $key => $value) {
	 		self::upload_del($UPLOAD_PATH.$value[oldfile]);
	 	}
	}

	function DeleteBoardFiles($files) {
	 	global $UPLOAD_PATH;
	 	foreach ($files as $key => $value) {
	 		self::upload_del($UPLOAD_PATH.$value);
	 	}
	}
	
	function DeleteAppVerifyFiles($files) {
	 	global $UPLOAD_PATH;
	 	foreach ($files as $key => $value) {
	 		self::upload_del($UPLOAD_PATH.$value);
	 	}
	}	
	
	// 파일명 새로 만들기
	function get_filename($len){
		$str  = "abcdefghijklmnopqrstuvwxyz";
		$str .= "0123456789";
		$string_generated = "";

        $nmr_loops = $len;
        while ($nmr_loops--)
        {
            $string_generated .= $str[mt_rand(0, strlen($str))];
        }

        return $string_generated;
	}

	/*파일다운로드
	 *@param - file_path:파일전체루트, file_name:다운로드받을 파일명
	 */
	function fileDownload($file_path, $file_name){

	    global $HTTP_USER_AGENT, $isDown;

	 //IE인가 HTTP_USER_AGENT로 확인
	$ie= isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false;
	//IE인경우 한글파일명이 깨지는 경우를 방지하기 위한 코드
	if( $ie ){
	$file_name = iconv('utf-8', 'euc-kr', $file_name);

	}
	    if(is_file($file_path)){
	        if(eregi("(MSIE 5.0|MSIE 5.1|MSIE 5.5|MSIE 6.0)", $HTTP_USER_AGENT)){
	            Header("Content-type: application/octet-stream");
	            Header("Content-Length: ".filesize("$file_path"));
	            Header("Content-Disposition: attachment; filename=$file_name");
	            Header("Content-Transfer-Encoding: binary");
	            Header("Pragma: no-cache");
	            Header("Expires: 0");
	        } else {
	            Header("Content-type: file/unknown");
	            Header("Content-Length: ".filesize("$file_path"));
	            Header("Content-Disposition: attachment; filename=$file_name");
	            Header("Content-Description: PHP3 Generated Data");
	            Header("Pragma: no-cache");
	            Header("Expires: 0");
	        }
	        $fp = fopen("$file_path", "r");
	        if (!fpassthru($fp)) fclose($fp);
	        $isDown = true;
	    }
	    else{
	        $isDown = false;
	        print("<script> alert('파일 다운 받기를 실패하였습니다.');\n");
	        print("    history.go(-1);\n");
	        print("</script>\n");
	    }
	}
}
?>
<?php

class UploadSvc {

	var $date_dir;
    var $real_dir;
	function InsertUploadList($p, $f, $j, $tname, $cname){

		// ���������� �ε��� ���ϱ�
		if (empty($j) == "1"){
			$job_cd = parent::MaxJobCd($tname,$cname);
		}else{
			$job_cd = $j;
		}

		$count = count($p);
		for($i = 0; $i < $count; $i++){

			$arrupload = explode("||", $p[$i]); // (�����ε���||��������||�������ϸ�||���)
			if(!empty($arrupload[2]) == "1"){
				if (empty($f[$i]) == "1"){
					$file_idx = parent::MaxUploadIdx();	// ���ϵ���� ���� ���� �ε��� ���ؿ�(identity ������ ������.)
				}else{
					$file_idx = $f[$i];
				}

				$file_ext = end(explode('.', $arrupload[2]));	// Ȯ����

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
	// ���� ����(������/��/��)
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

	//�̹��� Ȯ�� �Լ�
	function images_check($files, $max_size = 0 /* MByte */, $allowext = array('gif', 'jpeg', 'png', 'jpg', 'bmp', 'pptx', 'xls'))
	{
		//ī��Ʈ ��
		$count = count($files["name"]);
		if (!$max_size) {
			$max_size  = $this->let_to_num(ini_get("upload_max_filesize" ));
			$max_size /= 1024*1024;
		}
		$max_size = $max_size * 1024 * 1024;    // ����Ʈ�� ����Ѵ�. 1MB = 1024KB = 1048576Byte
		
		//�̹��� ����
		for ($i = 0; $i < $count; $i++) {
			if ( !$files['name'][$i]) continue;
			if($files["size"][$i]){

				//���� ������ üũ
				if ($files["size"][$i] > $max_size){
					echo "<script language='javascript'>alert('���� �뷮�� �ùٸ��� �ʽ��ϴ�.');</script>";
					exit;
				}
				/*
				//���� �̹��� üũ
				$file_ext = end(explode('.', $files['name'][$i]));

				// Maker: Okayjava  Date : 2012-11-01 20:30:09
				// Desc : ��ҹ��� ���� ���ϵ��� Ȯ���ڸ� �ҹ��ڷ� ���� �� �̹��� Ȯ��
				if(!in_array( strtolower($file_ext), $allowext, true )) // Ȯ���ڸ� �˻��Ѵ�.
				{
					echo "<script language='javascript'>alert('[$file_ext]Ȯ���ڰ� �ùٸ��� �ʽ��ϴ�.');$url;</script>";
					exit;
				}

				//���� ���ε�� ����
				if ($files["error"][$i] > 0){
					$msg = "Error: " . $files["error"][$i] . "<br />";
					$url = "history.go(-1)";
					echo "<script language='javascript'>alert('$msg');$url;</script>";
					exit;
				}

				//���� �̹��� üũ
				$image_check=false;
				if (($files["type"][$i] == "image/gif")|| ($files["type"][$i] == "image/jpeg")|| ($files["type"][$i] == "image/pjpeg")){
					$image_check=true;
				}
				if ($image_check=false) {
					echo "<script language='javascript'>alert('Ȯ���� �ùٸ��� �ʽ��ϴ�.');</script>";
					exit;
				}
				*/
			} else {
				// ���� �뷮 �ʰ��� ��� size�� 0 2012-11-15 Okayjava
				goURL("���� ����[".($files['name'][$i])."]�뷮�� ". ini_get("upload_max_filesize" )."�� ���� �� �����ϴ�.","back");

			}
		}//for��
	}
	
	//���� Ȯ�� �Լ�
	//aoa
	function files_check($files, $max_size = 0 /* MByte */, $allowext)
	{
		//echo $allowext."<br>";
		//ī��Ʈ ��
		//$count = count($files["name"]);
		
		if (!$max_size) {
			$max_size  = $this->let_to_num(ini_get("upload_max_filesize" ));
			$max_size /= 1024*1024;
		}
		$max_size = $max_size * 1024 * 1024;    // ����Ʈ�� ����Ѵ�. 1MB = 1024KB = 1048576Byte
		
		//echo $files['name']."<br>";
		//echo $files['size']."<br>";
		
		//����
		//for ($i = 0; $i < $count; $i++) {
			if ( !$files['name']) continue;
			if($files["size"]){
				
				//���� ������ üũ
				if ($files["size"] > $max_size){
					echo "���� �뷮�� �ùٸ��� �ʽ��ϴ�.'";
					exit;
				}
				
				//����  üũ
				$file_ext = end(explode('.', $files['name']));

				// Maker: Okayjava  Date : 2012-11-01 20:30:09
				// Desc : ��ҹ��� ���� ���ϵ��� Ȯ���ڸ� �ҹ��ڷ� ���� �� �̹��� Ȯ��
				/*echo strtolower($file_ext)."<br>";
				echo $allowext."<br>";
				
				echo "asdf=".eregi(strtolower($file_ext), $allowext)."<br>";
				echo "asdf=".eregi("png", $allowext)."<br>";*/
				
				//exit;
				if(!eregi(strtolower($file_ext), $allowext)) // Ȯ���ڸ� �˻��Ѵ�.
				{
					echo "[$file_ext]Ȯ���ڰ� �ùٸ��� �ʽ��ϴ�.";
					exit;
				}

				//���� ���ε�� ����
				if ($files["error"] > 0){
					$msg = "Error: " . $files["error"] . "<br />";
					$url = "history.go(-1)";
					echo $msg;
					exit;
				}
			} else {
				// ���� �뷮 �ʰ��� ��� size�� 0 2012-11-15 Okayjava
				echo "���� ����[".($files['name'])."]�뷮�� ". ini_get("upload_max_filesize" )."�� ���� �� �����ϴ�.";
				exit;
			}
			//exit;
		//}//for��
	}
	
	//�̹��� ���� �Լ�
	function upload($files,$formname) {
		$count=count($files["name"]);
		for ($i = 0; $i < $count; $i++){
			if($files["name"][$i]){

				$file_ext = end(explode('.', $files['name'][$i]));
				$file_name = date("YmdHis",time()).'_'.$this->get_filename(5).'.'.$file_ext;
				// �ߺ��� ������ ���ε� �ɼ� �����Ƿ� time�Լ��� �̿��� unixtime���� �����̸��� ������ְ�
				// �� �� ���� Ȯ���ڸ� �ٿ��ݴϴ�.

				global $UPLOAD_PATH;

				$path = $UPLOAD_PATH.$this->real_dir.'/'.$file_name;
				// ���� ���� ��θ� ����� �ݴϴ�.

				if (file_exists($path)){
					echo "<script language='javascript'>alert('�����̸��� ������ �����մϴ�.');</script>";
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

				// �������� ����
				if (empty($idxs[$i]) != "1"){
					$result = $this->ReadFileName($idxs[$i]);
					if($result != NULL) {
						$path = "../../../web_upload/".$result[stfile_dr]."/".$result[stfile_nm];
						$this->upload_del($path);
					}
				}

				$file_ext = end(explode('.', $files['name'][$i]));
				$file_name = date("YmdHis",time()).'_'.$this->get_filename(5).'.'.$file_ext;
				// �ߺ��� ������ ���ε� �ɼ� �����Ƿ� time�Լ��� �̿��� unixtime���� �����̸��� ������ְ�
				// �� �� ���� Ȯ���ڸ� �ٿ��ݴϴ�.

				global $UPLOAD_PATH;

				$path = $UPLOAD_PATH.$this->real_dir.'/'.$file_name;
				// ���� ���� ��θ� ����� �ݴϴ�.

				if (file_exists($path)){
					//echo "<script language='javascript'>alert('�����̸��� ������ �����մϴ�.');</script>";
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
	// Desc : Ȯ���� ���� �Է¹޾Ƽ� üũ �� �� �ֵ���
	// $image_check : Case 1 ; boolean type : �̹��� üũ �Ұ��̳� �����̳�.
	//				 Case 2 : extend string : üũ�� Ȯ���ڸ� , �� �����Ͽ� �Է�  Ex) "xls,xlsx,doc,docx"
	//						: ������ "ExtError" return
	//=====================================================================

	// aoa
	function UploadFiles($files,$filefolder,$allowext)
	{
		if($files == NULL) return NULL;
		global $UP_DIR;

		$result = "";
		
		if($allowext) self::files_check($files,1024*1024*5, $allowext);		// ���� üũ
		self::make_folder($filefolder);  			// ���� ����
		
		
		
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

	//�̹��� ���� �Լ�
	function uploadupdate($files,$formname) {
		$count=count($files["name"]);
		for ($i = 0; $i < $count; $i++){
			if($files["name"][$i]){

				$file_ext = end(explode('.', $files['name'][$i]));
				$file_name = date("YmdHis",time()).'_'.$this->get_filename(5).'.'.$file_ext;
				// �ߺ��� ������ ���ε� �ɼ� �����Ƿ� time�Լ��� �̿��� unixtime���� �����̸��� ������ְ�
				// �� �� ���� Ȯ���ڸ� �ٿ��ݴϴ�.

				global $UPLOAD_PATH;

				$path = $UPLOAD_PATH.$this->real_dir.'/'.$file_name;
				// ���� ���� ��θ� ����� �ݴϴ�.

				if (file_exists($path)){
					echo "<script language='javascript'>alert('�����̸��� ������ �����մϴ�.');</script>";
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


	// ���� ���� �Լ�
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
	
	// ���ϸ� ���� �����
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

	/*���ϴٿ�ε�
	 *@param - file_path:������ü��Ʈ, file_name:�ٿ�ε���� ���ϸ�
	 */
	function fileDownload($file_path, $file_name){

	    global $HTTP_USER_AGENT, $isDown;

	 //IE�ΰ� HTTP_USER_AGENT�� Ȯ��
	$ie= isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false;
	//IE�ΰ�� �ѱ����ϸ��� ������ ��츦 �����ϱ� ���� �ڵ�
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
	        print("<script> alert('���� �ٿ� �ޱ⸦ �����Ͽ����ϴ�.');\n");
	        print("    history.go(-1);\n");
	        print("</script>\n");
	    }
	}
}
?>
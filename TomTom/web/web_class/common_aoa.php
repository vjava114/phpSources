<?php
class Func_Aoa {
	
	var $DB;
	function Func_Aoa()
	{
		global $DB;
		$this->DB = & $DB;
	}
	
	// 공통코드 리스트
	function CommonCodeList($code){

		$SQL = "
				SELECT 	code, code_name
				FROM 	aoa_common_code 
				WHERE 	use_yn = 'Y' AND top_code = ?
				ORDER BY code ASC ";
		$rows = $this->DB->BindFetchAll($SQL, "i", $code);
		return $rows;
	}
	
	// 시간 리스트
	function SelectHourList(){
		$time_hour = array();
		for ($i=0; $i<24; $i++){
			$time_hour[] = str_pad($i, 2, "0", STR_PAD_LEFT);
		}
		return $time_hour;
	}

	// 분 리스트
	function SelectMinuteList(){
		$time_minute = array();
		for ($i=0; $i<60; $i++){
			$time_minute[] = str_pad($i, 2, "0", STR_PAD_LEFT);
		}
		return $time_minute;
	}
}	

function insBR($path, $type, $wmode, $title, $msg, $phone, $intro=null)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
    curl_setopt($ch, CURLOPT_URL, "http://192.168.1.92:10080/ext/doajaxfileupload.php");
    curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,5); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0); 
	curl_setopt($ch, CURLOPT_HTTPAUTH, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-type: multipart/form-data'
    ));

    $post = array(
        "fileToUpload"=>$path
    	,"type" => $type
        ,"title" => $title
        ,"wmode" => $wmode
        ,"msg" => $msg
        ,"phone" => $phone
        ,"intro" => $intro
        ,"name" => "test"
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
    $response = curl_exec($ch);
    
    return $response;
}
?>
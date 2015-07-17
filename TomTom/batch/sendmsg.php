<?
include "../web/_inc/db.php";

$stSQL = "
SELECT  s.*,  e.message, e.senderNo, p.path, p.fileName, c.coupon_seq
FROM tomtom.send_list s
	LEFT JOIN event_list e 	ON s.event_seq = e.seq
	LEFT JOIN pic_list p 	ON s.event_seq = p.event_seq
	LEFT JOIN coupon_list c	ON e.coupon_seq = c.coupon_seq
where s.rtn_msg is null 
limit 100
";
$query = mysql_query($stSQL);

$curl_obj = new Curl_class(); 
$curl_obj->init();
date_default_timezone_set('GMT-8');

// TB
//$curl_obj->url	 = "http://tdev.vmclub.co.kr/newmms/ext_message.html"; 
// PB
$curl_obj->url	 = "http://aoa.sktelecom.com/newmms/ext_message.html"; 

while( $row = mysql_fetch_assoc($query) )
{

	$sphone	= $row[senderNo];
	$rphone	= $row[phoneNo];
	//$msg	= base64_encode(iconv("utf-8", "euc-kr",$row[message]));
	$msg	= base64_encode(iconv("utf-8", "euc-kr",$row[smsg]));
	$type	= $row[type];
	$file	= $row[path];
	$send_key	= $row[seq];

	//$rphone = "01063643265";
	echo ">>>>>>>>>>\n";
	echo "type	= $type\n";
	echo "sphone= $sphone\n";
	echo "rphone= $rphone\n";
	echo "msg	= $msg\n";
	echo "file	= $file\n";
	echo "seq	= $send_key\n";
	echo ">>>>>>>>>>\n";

	$data= array();
	$data[id] = "test1";	// SKT에서 발급 받은 아이디
	$data[pwd] = "test1";	// SKT에서 발급 받은 비밀번호
	$data[gubun] = "S";		// S:SMS L:LMS M:MMS
	switch ( $type)
	{
		case "sms":
			$data[gubun] = 'S';
			break;
		case "lms":
			$data[gubun] = 'L';
			$data[subject] = "";
			break;
		case "mms":
			$data[gubun] = 'M';
			$data[subject] = "";
			if ( $row[fileName] )	// [kang] 
			{
				if ( strpos($row[path],"/") > 0 )
				{
					$aa = dirname($__FILE__)."../web";
					$aa .= str_replace("..","", $row[path]);
					$data[file] = "@".$aa;
				} else {
					$data[file] = "@".$row[path];  // /xxx/xx/xxx/sjsjajs.jpg
				}
			}
			break;
	}
	
	$data[send_key] = $send_key;		// 발송 서버의 Unique Key 받은데로 리턴.
	$data[send_phone] = $sphone;	// 발신자 전화번호
	$data[recv_phone] = $rphone;	// 수신자 전화번호
	$data[txt] = $msg;
	

	$data[returntype] = "TEXT"; // XML , TEXT
	$data[returnurl] = "http://www.naver.com"; // XML , TEXT
	
	echo "\n=== dumpData ===\n";
	var_dump($data);
	echo "\n=== dumpData ===\n";
	

	$curl_obj->parms	 = $data; 
	
	// 1은 POST 값이 있다는 의미 
	$curl_obj->post	 = 1; 
	
	// POST 값이 폼데이터 형식이 아닌 GET 형식으로 전송 
	$curl_obj->parms_type	                = 'post'; 
	
	//로그인 실행 
	$curl_obj->action(); 
	$xml = $curl_obj->recive; 
	
	$i = strpos($xml, "code_msg="); $i+=9;
	$j = strpos($xml, ",", $i);
	$code_msg = substr($xml, $i, $j -$i);
	printf("\nCode_Msg : %s\n", $code_msg);
	
	if ($code_msg == "Success")
	{
		$code_msg = "succ";
	} 
	
	$stSQL = "UPDATE send_list
				 SET rtn_msg = '$code_msg'
					,send_date = now()
			   WHERE seq = $send_key ";
	mysql_query($stSQL);
}
$curl_obj->Close();

// Report Check
echo "Report \n";
	$curl_obj = new Curl_class(); 
	$curl_obj->init();
	$curl_obj->url	 	= "http://aoa.sktelecom.com/newmms/ext_message.html"; 
	$data = array();
	$data[id] 			= "test1";	// SKT에서 발급 받은 아이디
	$data[pwd] 			= "test1";	// SKT에서 발급 받은 비밀번호
	$data[repchk]		=	'1';	
	$data[returntype] 	= "TEXT"; // XML , TEXT
	$curl_obj->parms		= $data;
	$curl_obj->post			= 1;
	$curl_obj->parms_type	= 'post'; 
	$curl_obj->action(); 
	$xml = $curl_obj->recive; 

	$ar = explode("\n", $xml);
	foreach($ar as $val)
	{
		$rerr = $serr = "";
		$tmp =  explode(":", $val);
		if ( count($tmp) != 4 ) continue;
		var_dump($tmp);
		$serr = ($tmp[1] == "12" ) ? "succ":"fail";
		if ($serr == 'succ')
		{
			$rerr = ($tmp[2] == "12" || $tmp[2] == "20" ) ? "succ":"fail";
		}
		$seqno = $tmp[0];
		$stSQL = "UPDATE send_list
					 SET receive_date = NOW()
					    ,rtn_msg = '$serr' 
					    ,receive_msg = '$rerr'
				   WHERE seq = $seqno ";
		echo "$stSQL\n";
		mysql_query($stSQL);
	}

	

?>





<?php

class Curl_class { 
	var $url	 = '';                // 접속 URL 
	var $cookie	 = './cookie.txt';  // 쿠키파일 입니다. 
	var $post	 = 0;                // post 값 여부 
	var $parms	 = '';                // 전송할 파라미터 
	var $parms_type	= '';                // 전송할 파라미터 타입 
	var $recive	 = '';                // 결과값 저장 
	var $return	 = 1;                // Curl 옵션 
	var $timeout	= 30;                // Curl 옵션 
	var $addopt	= '';                  // 추가 Curl 옵션 
	var $err = '';
	var $ch = "";
	function init()
	{
		$this->ch = curl_init(); 
	}
	function Close()
	{
		curl_close($this->ch);	
	}
	function action(){ 
		/* 배열로 저장된 파리미터 값을 Get 타입으로 변경 해줌 */ 
		if($this->parms_type == 'get'){ 
			if(sizeof($this->parms) > 0){ 
				$datas	= ''; 
				foreach ($this->parms as $obj=>$val){ 
					$datas	.= $obj.'='.$val.'&'; 
				} 
				$this->parms	= substr($datas,0,-1); 
			}
		}
		// Curl 실행 
		
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0); // 보안 위험 있음
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0); // 보안 위험 있음
		curl_setopt($this->ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
		curl_setopt ($this->ch, CURLOPT_URL,$this->url); 
		curl_setopt ($this->ch, CURLOPT_POST, $this->post); 
		curl_setopt ($this->ch, CURLOPT_POSTFIELDS, $this->parms); 
		curl_setopt ($this->ch, CURLOPT_COOKIEJAR, $this->cookie); 
		curl_setopt ($this->ch, CURLOPT_COOKIEFILE, $this->cookie); 
		curl_setopt ($this->ch, CURLOPT_TIMEOUT, $this->timeout); 
		curl_setopt ($this->ch, CURLOPT_RETURNTRANSFER, $this->return); 
		
		                                // 추가 Curl옵션이 있을경우 이를 적용 시켜줌 
		if($this->addopt)	curl_setopt_array($this->ch, $this->addopt); 
		
		$this->recive = curl_exec ($this->ch); 
		$this->err = curl_error($this->ch);
		
	}

}

?>

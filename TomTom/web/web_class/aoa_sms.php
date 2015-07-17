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
	
	function init_url($p) {
		$this->url = $p;
    }
	function init_parms($p) {
		$this->parms = $p;
    }
	function init_post($p) {
		$this->post = $p;
    }
	function inst_parms_type($p) {
		$this->parms_type = $p;
    }
	function get_recive() {
		return $this->recive;
    }
	
}

class Aoa_Sms extends Curl_class{ 
	
	var $send_phone;
	var $recv_phone;
	var $gubun;
	var $subject;
	var $txt;
	var $file;
	var $xml;
	
	function __construct(){
		parent::init();
		date_default_timezone_set('GMT-8');
		
		parent::init_url("http://aoa.sktelecom.com/newmms/ext_message.html"); //url
		parent::init_post(1); //post
		parent::inst_parms_type("post"); //parms_type
	}
	
	function Aoa_Action(){
		
		$data= array();
		$data[id] = "test1";	// SKT에서 발급 받은 아이디
		$data[pwd] = "test1";	// SKT에서 발급 받은 비밀번호
		$data[gubun] = $this->gubun;;		// S:SMS L:LMS M:MMS
		$data[send_key] = "949494";		// 발송 서버의 Unique Key 받은데로 리턴.
		$data[send_phone] 	= $this->send_phone;	// 발신자 전화번호
		$data[recv_phone] 	= $this->recv_phone;	// 수신자 전화번호
		$data[subject] 		= $this->subject;		// 제목
		$data[txt] 			= $this->txt; 		// 내용
		$data[file_box]		= $this->file;		// 파일/FullPath
		$data[returntype] = "TEXT"; // XML , TEXT
		$data[returnurl] = "http://www.naver.com"; // XML , TEXT
		
		parent::init_parms($data);
		parent::action();
		
		$this->xml = parent::get_recive(); 
	}
	
	function Aoa_Result(){
		return $this->xml;
	}
}

?>

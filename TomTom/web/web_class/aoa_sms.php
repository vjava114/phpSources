<?php

class Curl_class { 
	
	var $url	 = '';                // ���� URL 
	var $cookie	 = './cookie.txt';  // ��Ű���� �Դϴ�. 
	var $post	 = 0;                // post �� ���� 
	var $parms	 = '';                // ������ �Ķ���� 
	var $parms_type	= '';                // ������ �Ķ���� Ÿ�� 
	var $recive	 = '';                // ����� ���� 
	var $return	 = 1;                // Curl �ɼ� 
	var $timeout	= 30;                // Curl �ɼ� 
	var $addopt	= '';                  // �߰� Curl �ɼ� 
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
		/* �迭�� ����� �ĸ����� ���� Get Ÿ������ ���� ���� */ 
		if($this->parms_type == 'get'){ 
			if(sizeof($this->parms) > 0){ 
				$datas	= ''; 
				foreach ($this->parms as $obj=>$val){ 
					$datas	.= $obj.'='.$val.'&'; 
				} 
				$this->parms	= substr($datas,0,-1); 
			}
		}
		// Curl ���� 
		
		
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0); // ���� ���� ����
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0); // ���� ���� ����
		curl_setopt($this->ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
		curl_setopt ($this->ch, CURLOPT_URL,$this->url); 
		curl_setopt ($this->ch, CURLOPT_POST, $this->post); 
		curl_setopt ($this->ch, CURLOPT_POSTFIELDS, $this->parms); 
		curl_setopt ($this->ch, CURLOPT_COOKIEJAR, $this->cookie); 
		curl_setopt ($this->ch, CURLOPT_COOKIEFILE, $this->cookie); 
		curl_setopt ($this->ch, CURLOPT_TIMEOUT, $this->timeout); 
		curl_setopt ($this->ch, CURLOPT_RETURNTRANSFER, $this->return); 
		
		                                // �߰� Curl�ɼ��� ������� �̸� ���� ������ 
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
		$data[id] = "test1";	// SKT���� �߱� ���� ���̵�
		$data[pwd] = "test1";	// SKT���� �߱� ���� ��й�ȣ
		$data[gubun] = $this->gubun;;		// S:SMS L:LMS M:MMS
		$data[send_key] = "949494";		// �߼� ������ Unique Key �������� ����.
		$data[send_phone] 	= $this->send_phone;	// �߽��� ��ȭ��ȣ
		$data[recv_phone] 	= $this->recv_phone;	// ������ ��ȭ��ȣ
		$data[subject] 		= $this->subject;		// ����
		$data[txt] 			= $this->txt; 		// ����
		$data[file_box]		= $this->file;		// ����/FullPath
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

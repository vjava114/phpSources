<?

// 로코모 공통 함수 모음 PHP
// 암호화 추가 


/*
#=========================================
 a function to subtract from or add  seconds or minutes or hours or days or months or weeks or years to a specified date and return the updated date

=========================================
$newdate = dateadd("d",3,"2006-12-12");    #  add 3 days to date
$newdate = dateadd("s",3,"2006-12-12");    #  add 3 seconds to date
$newdate = dateadd("m",3,"2006-12-12");    #  add 3 minutes to date
$newdate = dateadd("h",3,"2006-12-12");    #  add 3 hours to date
$newdate = dateadd("ww",3,"2006-12-12");    #  add 3 weeks days to date
$newdate = dateadd("m",3,"2006-12-12");    #  add 3 months to date
$newdate = dateadd("yyyy",3,"2006-12-12");    #  add 3 years to date
$newdate = dateadd("d",-3,"2006-12-12");    #  subtract 3 days from date
*/

function dateAdd($interval,$number,$dateTime) {
        
    $dateTime = (strtotime($dateTime) != -1) ? strtotime($dateTime) : $dateTime;       
    $dateTimeArr=getdate($dateTime);
                
    $yr=$dateTimeArr[year];
    $mon=$dateTimeArr[mon];
    $day=$dateTimeArr[mday];
    $hr=$dateTimeArr[hours];
    $min=$dateTimeArr[minutes];
    $sec=$dateTimeArr[seconds];

    switch($interval) {
        case "s"://seconds
            $sec += $number; 
            break;

        case "n"://minutes
            $min += $number; 
            break;

        case "h"://hours
            $hr += $number; 
            break;

        case "d"://days
            $day += $number; 
            break;

        case "ww"://Week
            $day += ($number * 7); 
            break;

        case "m": //similar result "m" dateDiff Microsoft
            $mon += $number; 
            break;

        case "yyyy": //similar result "yyyy" dateDiff Microsoft
            $yr += $number; 
            break;

        default:
            $day += $number; 
         }       
                
        $dateTime = mktime($hr,$min,$sec,$mon,$day,$yr);
        $dateTimeArr=getdate($dateTime);
        
        $nosecmin = 0;
        $min=$dateTimeArr[minutes];
        $sec=$dateTimeArr[seconds];

        if ($hr==0){$nosecmin += 1;}
        if ($min==0){$nosecmin += 1;}
        if ($sec==0){$nosecmin += 1;}
        
        if ($nosecmin>2){     return(date("Y-m-d",$dateTime));} else {     return(date("Y-m-d G:i:s",$dateTime));}
} 



// sms 수신 거부 확인 기능 KT=-IDC 1521 포함 해서 확인 하는 기능임....
function check_sms_reject($tmin)
{
    $fp = fopen("http://lotto.k-merce.com/extern/check_sms_reject.asp?minno=".$tmin, "r");
    $kmsg = fgets($fp);
    $tkn = explode("=", $kmsg);
    fclose($fp);
    
    if($tkn[0] != $tmin)
    {
        $ret = 'E';
        return $ret;
    }
    if($tkn[1] == 'reject')
    {   
        $ret = 'R';
        return $ret;
    }
    
    $ret = 'S';
    return $ret;

}

function string_min($s) {
    if (strlen($s) == 11)
        return substr($s, 0, 3) . '-' . substr($s, 3, 4) . '-' . substr($s, -4);
    else
        return substr($s, 0, 3) . '-' . substr($s, 3, 3) . '-' . substr($s, -4);
}

function min_to_11($s) {
    if (strlen($s) == 10)
        return substr($s, 0, 3) . '0' . substr($s, 3, 7);
    else
        return $s;
}

/*
function min_to_10($phone)
{
	$prefix = substr($phone,0,4);
	if($prefix =="0110" || $prefix =="0160" || $prefix =="0170" || $prefix =="0180" ||  $prefix =="0190")
		$phone = substr($phone,0,3) . substr($phone,4);
	return $phone;
}
*/



function make_image_base64($path)
{
$fp = fopen($path, 'r');
do //we loop until there is no data left
{
       $data = fread($fp, 8192);
       if (strlen($data) == 0) break;
       $content .= $data;
     } while (true);
$content_encode = chunk_split(base64_encode($content));
return $content_encode;
}


function msg_enc($str,$mode,$seed='Rcm',$seed_point='4')
{
    for($i=0;$i<strlen($seed);$i++)
    {
        $new_seed.=chr(ord(substr($seed,$i,1))+$seed_point+$i);
    }

    if($mode=="e")
    {
        $str=base64_encode($str);
        $str=substr($str,0,$seed_point).
        $new_seed.
        substr($str,$seed_point);
        $str=urlencode($str);
    }

    if($mode=="d")
    {
        $str=urldecode($str);
        $read_seed=substr($str,$seed_point,strlen($new_seed));

        if($read_seed==$new_seed)
        {
            $str=substr($str,0,$seed_point).
            substr($str,$seed_point+strlen($new_seed));
        }

        $str=base64_decode($str);
    }

    return $str;
}



function check_jumin_number($resno) {
                     
  // 형태 검사: 총 13자리의 숫자, 7번째는 1..4의 값을 가짐
  if (!ereg('^[[:digit:]]{6}[1-4][[:digit:]]{6}$', $resno))
    return false;
    
  // 날짜 유효성 검사
  $birthYear = ('2' >= $resno[6]) ? '19' : '20';
  $birthYear += substr($resno, 0, 2);
  $birthMonth = substr($resno, 2, 2);
  $birthDate = substr($resno, 4, 2);
  if (!checkdate($birthMonth, $birthDate, $birthYear))
    return false;
            
  // Checksum 코드의 유효성 검사
  for ($i = 0; $i < 13; $i++) $buf[$i] = (int) $resno[$i];
  $multipliers = array(2,3,4,5,6,7,8,9,2,3,4,5);
  for ($i = $sum = 0; $i < 12; $i++) $sum += ($buf[$i] *= $multipliers[$i]);  
  if ((11 - ($sum % 11)) % 10 != $buf[12])
    return false; 
        
  // 모든 검사를 통과하면 유효한 주민등록번호임
  return true;  
}       

function open_page($url,$f=1,$c=2,$r=0,$a=0,$cf=0,$pd=""){
 global $oldheader;
 $url = str_replace("http://","",$url);
 if (preg_match("#/#","$url")){
  $page = $url;
  $url = @explode("/",$url);
  $url = $url[0];
  $page = str_replace($url,"",$page);
  if (!$page || $page == ""){
   $page = "/";
  }
  $ip = gethostbyname($url);
 }else{
  $ip = gethostbyname($url);
  $page = "/";
 }
 $open = fsockopen($ip, 80, $errno, $errstr, 60);
 if ($pd){
  $send = "POST $page HTTP/1.0\r\n";
 }else{
  $send = "GET $page HTTP/1.0\r\n";
 }
 $send .= "Host: $url\r\n";
 if ($r){
  $send .= "Referer: $r\r\n";
 }else{
  if ($_SERVER['HTTP_REFERER']){
   $send .= "Referer: {$_SERVER['HTTP_REFERER']}\r\n";
  }
 }
 if ($cf){
  if (@file_exists($cf)){
   $cookie = urldecode(@file_get_contents($cf));
   if ($cookie){
    $send .= "Cookie: $cookie\r\n";
    $add = @fopen($cf,'w');
    fwrite($add,"");
    fclose($add);
   }
  }
 }
 $send .= "Accept-Language: en-us, en;q=0.50\r\n";
 if ($a){
  $send .= "User-Agent: $a\r\n";
 }else{
  $send .= "User-Agent: {$_SERVER['HTTP_USER_AGENT']}\r\n";
 }
 if ($pd){
  $send .= "Content-Type: application/x-www-form-urlencoded\r\n";  
  $send .= "Content-Length: " .strlen($pd) ."\r\n\r\n";
  $send .= $pd;
 }else{
  $send .= "Connection: Close\r\n\r\n";
 }
 fputs($open, $send);
 while (!feof($open)) {
  $return .= fgets($open, 4096);
 }
 fclose($open);
 $return = @explode("\r\n\r\n",$return,2);
 $header = $return[0];
 if ($cf){
  if (preg_match("/Set\-Cookie\: /i","$header")){
   $cookie = @explode("Set-Cookie: ",$header,2);
   $cookie = $cookie[1];
   $cookie = explode("\r",$cookie);
   $cookie = $cookie[0];
   $cookie = str_replace("path=/","",$cookie[0]);
   $add = @fopen($cf,'a');
   fwrite($add,$cookie,strlen($read));
   fclose($add);
  }
 }
 if ($oldheader){
  $header = "$oldheader<br /><br />\n$header";
 }
 $header = str_replace("\n","<br />",$header);
 if ($return[1]){
  $body = $return[1];
 }else{
  $body = "";
 }
 if ($c === 2){
  if ($body){
   $return = $body;
  }else{
   $return = $header;
  }
 }
 if ($c === 1){
  $return = $header;
 }
 if ($c === 3){
  $return = "$header$body";
 }
 if ($f){
  if (preg_match("/Location\:/","$header")){
   $url = @explode("Location: ",$header);
   $url = $url[1];
   $url = @explode("\r",$url);
   $url = $url[0];
   $oldheader = str_replace("\r\n\r\n","",$header);
   $l = "&#76&#111&#99&#97&#116&#105&#111&#110&#58";
   $oldheader = str_replace("Location:",$l,$oldheader);
   return open_page($url,$f,$c,$r,$a,$cf,$pd);
  }else{
   return $return;
  }
 }else{
  return $return;
 }
}
/*
/////////////
////Usage////
/////////////
$url = "http://www.php.net";
$f = 1;
$c = 2;//1 for header, 2 for body, 3 for both
$r = NULL;
$a = NULL;
$cf = NULL;
$pd = NULL;
$page = open_page($url,$f,$c,$r,$a,$cf,$pd);
print $page;
*/


function httpSocketConnection($host, $method, $path, $data)
   {
        $method = strtoupper($method);

        if ($method == "GET")
        {
            $path.= '?'.$data;
        }  

        $filePointer = fsockopen($host, 80, $errorNumber, $errorString);

        if (!$filePointer)
        {
            printf('Failed opening http socket connection: '.$errorString.' ('.$errorNumber.')<br/>\n');
            return false;
        }

        $requestHeader = $method." ".$path."  HTTP/1.1\r\n";
        $requestHeader.= "Host: ".$host."\r\n";
        $requestHeader.= "User-Agent:     Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20061010 Firefox/2.0\r\n";
        $requestHeader.= "Content-Type: application/x-www-form-urlencoded\r\n";

        if ($method == "POST")
        {
            $requestHeader.= "Content-Length: ".strlen($data)."\r\n";
        }

        $requestHeader.= "Connection: close\r\n\r\n";

        if ($method == "POST")
        {
            $requestHeader.= $data;
        }      

        fwrite($filePointer, $requestHeader);


        $responseHeader = '';
        $responseContent = '';

        do
        {
            $responseHeader.= fread($filePointer, 1);
        }
        while (!preg_match('/\\r\\n\\r\\n$/', $responseHeader));

        if (!strstr($responseHeader, "Transfer-Encoding: chunked"))
        {
            while (!feof($filePointer))
            {
                $responseContent.= fgets($filePointer, 128);
            }
        }
        else
        {
            
            while ($chunk_length = hexdec(fgets($filePointer)))
            {   
                $responseContentChunk = '';
                
                $read_length = 0;
                
                while ($read_length < $chunk_length)
                {   
                    $responseContentChunk .= fread($filePointer, $chunk_length - $read_length);
                    $read_length = strlen($responseContentChunk);
                }
                $responseContent.= $responseContentChunk;
                fgets($filePointer);
             
            }
         
        }
        return chop($responseContent);
  }



function trimall($str, $charlist = " \t\n\r\0\x0B")
{
  return str_replace(array("\r\n", "\n", "\r","\t","\0","\xOB"," "), '', $str);
}
function delete_string($s1,$s2,$s,$offset=0){
    $result = array();
    $index= 0;
    $L1 = strlen($s1);
    $found = false;

    $offset = strpos($s,$s1,$offset);

  do{
        $end = strpos($s,$s2,$offset);
        $start   = strpos($s,$s1,$end);
        if($start <= 0) break;

        $diff = $start - $end;
        //echo $end.":".$start.":".$diff.":". substr($s,$end+1,2)."\n";
        //if( $diff > 1)
        $result[$index++]= substr($s,$end+1,$diff-1);
        $offset = $start +1;

  }while($pos1 !== false);
  return $result;
}

function getStrsBetween($s1,$s2,$s,$offset=0){
  $result = array();
  $index= 0;
  $L1 = strlen($s1);
  $found = false;
  do{
    if($L1>0){
        $pos1 = strpos($s,$s1,$offset);
    }
    else {
        $pos1=$offset;
    }
    if($pos1 !== false){
        if($s2 == '')
            $result[$index++]= substr($s,$pos1+$L1);
        $pos2 = strpos(substr($s,$pos1+$L1),$s2,$L1);
        if($pos2!==false){
            $result[$index++]= substr($s,$pos1+$L1,$pos2);
            $offset += $pos2 + strlen($s2);
        }
        else{
            $pos1 = false;
        }
    }
  }while($pos1 !== false);
  return $result;
}



function get_curl($url)
{   
    $ch = curl_init();
	$header = array();

    array_push($header, 'Connection: Close');

   // curl_setopt($ch, CURLOPT_HEADER, 0 );
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION , 1 );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1 );
    curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
//	curl_setopt($ch, CURLOPT_TIMEOUT, 15); 
    $ret = curl_exec($ch);
    curl_close($ch);
    return $ret;
}       


function sock_get($url,$host)
{
	$header  = "GET ".$url." HTTP/1.1\r\n";
	$header .= "Host: ".$host."\r\n";
	$header .= "Accept: */*\r\n";
	$header .= "Connection: Close\r\n\r\n";

  	if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) < 0) { return -1;};
  	if (($ret = socket_bind($socket, gethostbyname("khub_host"), 0)) < 0) { return -2;};

  	socket_connect($socket,$host, 80);
  	socket_write($socket, $header, strlen($header));
  	$recv_msg = "";

  	while ($in = socket_read($socket, 4096))
  	{
      	$recv_msg .= $in;
    //  	if(strpos($recv_msg,"Content-Length:") > 0) break;
  	};
  	socket_close($socket);
	return $recv_msg;
}


function sock_post($url, $param, $host)
{
    $header  = "POST ".$url." HTTP/1.1\r\n";
    $header .= "Host: ".$host."\r\n";
    $header .= "Accept: */*\r\n";
    $header .= "Content-Length: ".strlen($param)."\r\n";
    $header .= "Connection: Close\r\n\r\n";
    $header .= $param."\r\n";


    if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) < 0) { return -1;};
    if (($ret = socket_bind($socket, gethostbyname("khub_host"), 0)) < 0) { return -2;};

    socket_connect($socket,$host, 80);
    socket_write($socket, $header, strlen($header));
    $recv_msg = "";

    while ($in = socket_read($socket, 4096))
    {
        $recv_msg .= $in;
    };
    socket_close($socket);
    return $recv_msg;

}



function hangul_cut($org,$clen,$other)
{
	$len = strlen($org);
	$s = 0;
	$han = 0;
	$arr = 0;
    for($i=0;$i<$len;$i++)
    {
		if($han >= $clen)
		{
			$ret[$arr++] = substr($org, $s,  $i-$s);
			$s = $i;
			$han = 0;
		}

        $tmp =ord(substr($org,$i,1));

		if($tmp > 128) // 한글 문자
		{
			$i++;
			$han = $han +2;
			continue;
		}
		$han ++;

		if($tmp == ord($other) )
		{
			$ret[$arr++] = substr($org,$s, $i-$s);
			$s = $i+1;
			$han = 0;
			continue;
		}

    }

	$ret[$arr++] = substr($org, $s);
	return $ret;

}

function str2time($mm)
{
	$ct = strtotime($mm);
	$ret= localtime($ct, true);
	$ret['tm_year'] += 1900;
	$ret['tm_mon'] += 1;
	return $ret;
}


function dec_format($m)
{
	
	if($m > 1000000)
		$str = number_format($m/10000). "만";
	else $str = number_format($m);
	return $str;
}

function get_http($name)
{
	/*$ret = $_REQUEST[$name];
	$pat = array('<','>','(',')', "+or","'",'"' );
	$ret = str_replace($pat, "", $ret);
	
	return trim($ret);*/
	$ret = $_REQUEST[$name];
	$pat = array('<','>','(',')', "+or","'",'"' );
	
	if(is_array($ret)){
		$result = array();
		foreach($ret as $key => $val){
			$result[] = trim(str_replace($pat, "", $val));
		}
	}else{
		$result = str_replace($pat, "", $ret);
	}
	return $result;
}

function euc2kr($msg)
{
	return mb_convert_encoding("". $msg,"EUC-KR", "UTF-8");
}


/************* 
$msg = base64_encode("한글asvvdf시험하지01234567891+-===");
$dat = enc($msg);
$ret = base64_decode( dec($dat) );
*/

function bsft($key, $ss)
{
    $k2 = sprintf("%02X", $key >> $ss);
    $m = substr($k2,0,2);
    $mm = hexdec($m);
//  echo $key."/".$ss."==".$mm."\n";
    return intval($mm);
}

function  rcm_encode($in)
{
    $ins = str_split($in);
    $len = count($ins);

    $key = 112; $mkey = 36; $akey = 15;

    $msg = "";

    for($i=0;$i< $len; $i++)
    {
        $tm =  ord($ins[$i]);

        $out[$i] = ( ($tm^bsft($key,2))  ) & 0x000000ff;
//      printf("%X/%x = [%02x] ",ord($ins[$i]),$key, $out[$i]);

        $key =  ( (($out[$i]) + $key) * $mkey + $akey) & 0x000000ff ;
        $msg .= sprintf("%02X", $out[$i]);

//      printf(" newkey = %x\n", $key);

    }
    //print_r($out);
    //echo $msg;
    return  $msg;
}


function  rcm_decode($in)
{
    $ins = str_split($in,2);
    $len = count($ins);

    $key = 112; $mkey = 36; $akey = 15;
    $msg = "";
    for($i=0;$i< $len; $i++)
    {
        $tm =  hexdec($ins[$i]) & 0x000000ff;
        $out[$i] =  $tm^bsft($key,2);
    //  printf("%X/%x = [%02x]== %s\n",$tm,$key,  $out[$i], chr($out[$i]));
        $key =  ( ($tm + $key) * $mkey + $akey) & 0x000000ff ;
        $msg .= chr($out[$i]);
    }
    return $msg;
}









function gift_cript($mode, $msg1="", $msg2="", $msg3="", $msg4="")
{
//  $cmd =  "/usr/java/java/bin/java rcm_enc ".$msg;
//  $ret = system($cmd);

    if($msg1 != "") $param = "msg1=".$msg1."&";
    if($msg2 != "") $param .= "msg2=".$msg2."&";
    if($msg3 != "") $param .= "msg3=".$msg3."&";
    if($msg4 != "") $param .= "msg4=".$msg4."&";

	$url = "http://hwas.vmclub.co.kr:8080/util/gift_auth.jsp?mode=".$mode."&".$param;
    $ret = get_curl($url);
    return  $ret;
}


function get_phone_info($PHONE)
{
	$url = sprintf("http://m.rocomo.co.kr/common/phone_info.php?min=" . $PHONE);
	$fp = fopen($url, "r");
	$tmp = fgets($fp, 255);
	$tmp_arr = split(";", $tmp);
	fclose($fp);

	$ret['mobile'] = $tmp_arr[0];
	$ret['size'] = $tmp_arr[1];
	$ret['model'] = $tmp_arr[2];
	$ret['mode'] = $tmp_arr[3];


	$size = split("x", $ret['size']);

	$ret['width'] = $size[0];
	$ret['height'] = $size[1];

	if($ret['width'] < 1 || $ret['mobile'] == "L")
	{
		// SK 인증 MMS검사 
		$mms_chk = check_mms($PHONE);

		if($mms_chk)
			$ret['chk_mms'] = "M";
		else
			$ret['chk_mms'] = "C";

	}else
	{
		$ret['chk_mms'] = "M";
	}

	if(strpos(strtoupper($ret['model']), 'AIP') > -1)
		$ret['chk_mms'] = "S";


	return $ret;
}

function  myln2br($msg)
{
	$msg = urldecode($msg);
	$msg = str_replace("<br />", "<br>", nl2br($msg) );
	$msg = str_replace("<br/>", "<br>", $msg );
	return $msg;

}

function com_log($msg, $path, $mode)
{
    $msg = date("H:i:s: "). $msg.chr(0x0a);
    $log_dir = $path. "/".$mode."_".date("Ymd").".txt";

    if(is_file($log_dir) != true)
    {
        $fp = fopen($log_dir, "a+");
        chmod($log_dir, 0666);
    }
    else
        $fp = fopen($log_dir, "a+");

    if ($fp) {
        fwrite($fp, $msg);
    }
    fclose($fp);
}



function logcom($svc_name, $mm)
{
    $fname = "/www/com/log/php/".$svc_name."_".date("Ymd").".txt";
    $fp = fopen($fname,"a+", "0777");
    if($fp  == NULL) return ;
    $kkk= date('H:i:s') .":".trim($mm).chr(10);
    fwrite($fp,$kkk);
	fclose($fp);
}
if(!function_exists("param"))
{
function param($add = null, $del = null, $option_flag = "flase"){

	$pat = array('<','>',"+or",'%',"'",'"' );
	foreach($_GET as $key =>$val)
	{
		foreach($pat as $key1=>$val1)
		{
			if(strpos($val,$val1))
			{
				$_GET[$key] = str_replace($pat, "", $val);
			}
		}
	}

        $param_arr = array();

	$str = "";
	$fee_str = "";
	$no_cache = time();
	$glue = "&";

	// 추가값이 없으면 add 초기화 세팅
	if ( is_array($add) == false )
		$add = array();

	// 제거할 값이 없으면 del 초기화 세팅
	if ( is_array($del) == false )
		$del = array();

        // 이전 페이지에서 GET 방식으로 넘어온 파라미터 추가
        if(is_array($_GET)){
            foreach ($_GET as $key => $value)
                $param_arr[$key] = DeleteDumpCode($value);
        }

		// 이전 페이지에서 POST 방식으로 넘어온 파라미터 추가
        if(is_array($_POST)){
            foreach ($_POST as $key => $value)
				if( isset($_POST[$key]))
                $param_arr[$key] = DeleteDumpCode($value);
        }
      // 제거할 값을 unset 한다.
        if (is_array($del)){
            foreach ($del as $key => $value)
                unset($param_arr[$value]);
		}

        // 이번 페이지에서 추가할 파라미터 
        if (is_array($add))
            foreach ($add as $key => $value)
                $param_arr[$key] = $value;

		// option_flag 가  'delete_all' 인경우 지정된 값을 제외하고 모두 삭제
		if ( $option_flag == "delete_all" )
		{
			$str = "";
			$fee_str = "";
			unset($param_arr["0"]);
		}else{		
			foreach ($param_arr as $key => $value)
			{
				if ($str == '')
					$str = $key . '=' . $value;
				else
					$str .= '&' . $key . '=' . $value;
			}
		}

		
		if ($str != '')
			$str = '?' . $str . "&noche=" . $no_cache;
		else
			if($str == '')
				$str = "?noche=" . $no_cache;
			else
				$str = $str . "&noche=" . $no_cache;

		$str = $str . $fee_str;

		return $str;
}
}

if(!function_exists("DeleteDumpCode"))
{
function DeleteDumpCode($name)
{
	return $name;
$ret = $_REQUEST[$name];
$pat = array('<','>',"+or",'%',"'",'"' );
$ret = str_replace($pat, "", $ret);

return trim($ret);
}
}


if(!function_exists("check_mms"))
{
	function check_mms($PHONE)
	{
		return true;

	}

}


function thread_start_host($host,$port,$url)
{
	$fp = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	$result = socket_connect($fp, $address, $port);
		
	if($fp < 0)
	{
		socket_set_nonblock ($fp);
		$header	 = "GET ".$url." HTTP/1.1\r\n";
		$header .= "Host: ".$host."\r\n";
		$header .= "Connection: Close\r\n\r\n";
		socket_write($fp,$header, strlen($header));
	}
	return $fp;
}

function thread_start($url)
{
    $tkn = split("/",$url);
    $tkn2 = split(":", $tkn[2]);

    $host = $tkn2[0];
    if(strlen($tkn2[1]) > 0)
        $port = $tkn2[1];
    else $port = "80";

    $nn = count($tkn);

    $url = "";;
    for($i=3;$i < $nn;$i++)
        $url .= "/".$tkn[$i];

    $fp = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    $result = socket_connect($fp, $host, $port);

    if($fp != NULL)
    {
        socket_set_nonblock ($fp);
        $header  = "GET ".$url." HTTP/1.1\r\n";
        $header .= "Host: ".$host."\r\n";
        $header .= "Connection: Close\r\n\r\n";
        socket_write($fp,$header, strlen($header));
    }
    return $fp;
}

function thread_wait($fds, $tout)
{
    $stime = 1;
    $cnt = count($fds);

    if($cnt  <= 0) return NULL;
    for($i=0;$i<$tout;$i++)
    {
        $ss = 0;
        for($n=0;$n<$cnt;$n++)
        {
            if($fds[$n] > 0)
            {
                $ss++;
                $x = NULL;
                $x = socket_read ($fds[$n], 4096);
                if($x != null)
                {
					$ret[$n] = $x;
                    $fds[$n] = 0;
			//		socket_close($fds[$n]);
                }
            }
        }
        if($ss == 0) break;
        sleep($stime);
    }
    return $ret;
}

function socket_normal_read ($socket, $length) {

        static $sockets = array ();
        static $queues = array ();
        static $sock_num = 0;

        for ($i = 0;  isset ($sockets[$i]) && $socket != $sockets[$i]; $i++);

        if (!isset ($sockets[$i])) {
            $sockets [$sock_num] = $socket;
            $queues [$sock_num++] = "";
        }

        $recv = socket_read ($socket, $length, PHP_BINARY_READ);
        if ($recv === "") {
            if (strpos ($queues[$i], LINE_END) === false)
                return false;
        }
        else if ($recv !== false) {
            $queues[$i] .= $recv;
        }

        $pos = strpos ($queues[$i], LINE_END);
        if ($pos === false)
            return "";
        $ret = substr ($queues[$i], 0, $pos);
        $queues[$i] = substr ($queues[$i], $pos+2);

        return $ret;
    }


function to_utf8($msg)
{
    //if ($msg == null) return "";
     $emsg = mb_convert_encoding($msg,"UTF-8", "EUC-KR");
	 //$emsg = $msg;
     return $emsg;
}
function to_euckr($msg)
{
    //if ($msg == null) return "";
     $emsg = mb_convert_encoding($msg,"EUC-KR", "UTF-8");
     return $emsg;
}

//2011-03-01
function to_date_format($msg)
{
	$leng = strlen($msg);
	
    if ($msg == null) return "";
	if($leng == 14)
    	$emsg = substr($msg,0,4)."-".substr($msg,4,2)."-".substr($msg,6,2)." ".substr($msg,8,2).":".substr($msg,10,2).":".substr($msg,12,2);
	else if( $leng == 8)
    	$emsg = substr($msg,0,4)."-".substr($msg,4,2)."-".substr($msg,6,2);
	else 
    	$emsg = substr($msg,0,4)."-".substr($msg,4,2);
    return $emsg;
}


// ktf db
function ktf_db_decode($msg)
{
    if($msg =="") return $msg;
    $cmd = "/usr/local/bin/kt_encode d ".trim($msg);
    $cmd = "/www/was/bin/kt_encode d ".trim($msg);
    $fp = popen ($cmd,"r");
    $ret = fgets($fp);
    pclose($fp);
    return trim($ret);
}

function ktf_db_encode($msg)
{
    if($msg =="") return $msg;
    $cmd = '/usr/local/bin/kt_encode e "'.trim($msg).'"' ;
    $cmd = '/www/was/bin/kt_encode e "'.trim($msg).'"' ;
    $fp = popen ($cmd,"r");
    $ret = fgets($fp);
    pclose($fp);
    return trim($ret);
}

function ftp_copy($filename,$other_host,  $user, $pass) {
	if($user =="") return ;

    $conn_id = ftp_connect($other_host);
    $login_result = ftp_login($conn_id, $user, $pass);

    if ((!$conn_id) || (!$login_result)) {
        $err_msg = "FTP connection has failed!";
        return "F";
    }

    $upload = ftp_put($conn_id, $filename, $filename, FTP_BINARY);

    if (!$upload) {
        $err_msg = "FTP upload has failed!";
        ftp_close($conn_id);
        return "F";
    }

    ftp_close($conn_id);
    return "Y";
}

function ftp_local_copy($org, $dest, $other_host,  $user, $pass) {
	if($user =="") return ;

    $conn_id = ftp_connect($other_host);
    $login_result = ftp_login($conn_id, $user, $pass);

    if ((!$conn_id) || (!$login_result)) {
        $err_msg = "FTP connection has failed!";
        return "F";
    }

    $upload = ftp_get($conn_id, $dest, $org, FTP_BINARY);
    if (!$upload) {
        $err_msg = "FTP upload has failed!";
        ftp_close($conn_id);
        return "F";
    }

    ftp_close($conn_id);
    return "Y";
}



function micro_time() {
    $temp = explode(" ", microtime());
    return substr(($temp[1].$temp[0]),0,16);
}

function hex_print($data, $leng)
{
    for($m=0;$m<$leng; $m++)
    {
        echo hex_format(ord($data[$m]))." ";
    }

}

function hex_format($o) {
    $h = strtoupper(dechex($o));
    $len = strlen($h);
    if ($len % 2 == 1)
        $h = "0$h";
    return $h;
}


function cvt_resno($resno)
{
	$m1= substr($resno, 0,6);
    $m2 = substr($resno, 0,2);
    $m3 = substr($resno, 6,1);

    if( $m3 == "1" || $m3 == "3")
    {
         $ret[sex] = "M";
    }
    else $ret[sex] = "F";

    if($m3 =='1' || $m3 =='2') $ret[birth]  = "19" . $m1;
    else  $ret[birth] = "20".$m1;

    $tm = substr($ret[birth], 0,4);
    $ret[age] =  date('Y') - $tm;

	return $ret;
}

function xml2array ( $xmlObject, $out = array () )
{
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;
        return $out;
}

function xml_array($xml, $array = "Y")
{
	$info = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
	if($array == "Y") return xml2array($info);
	else return $info;
}


// 웹페이지 Text 출력 그대로 끌어오기 Push : POST
function post_curl($url, $param, $header = null)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);

	if($header != null) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

	curl_setopt($ch, CURLOPT_POST, 1 );
	curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE );
	curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE );
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_TIMEOUT,30);           // REQUEST 에 대한 결과값을 받는 시간타임 설정

   $content = curl_exec ($ch); # This returns HTML
   curl_close ($ch);
   return $content;
}

function wallet_db_query($sql)
{
	$url = "https://mocaapp.adm.mocawallet.com/v2/batch/noti_set.php";
    $sql = rcm_encode($sql);
    $param =  "data=".$sql."&mode=" ;
    $ret = post_curl($url, $param);
    $json = json_decode( ($ret), true);
	return $json;
}

function wallet_db_query_test($sql)
{
	$url = "http://devsmt.vmclub.co.kr/v2/batch/noti_set.php";
    $sql = rcm_encode($sql);
    $param =  "data=".$sql."&mode=" ;
    $ret = post_curl($url, $param);
    $json = json_decode( ($ret), true);
	return $json;
}

function get_one_xml_tag($xml, $tag, $max_len=50)
{
	$pos = strpos($xml, $tag);

	if($pos-$max_len <= 0) $tmp = substr($xml, 0, $max_len);
	else $tmp = substr($xml, $pos-$max_len, $max_len);

//	printf("\nstep1 = [%s]\n", $tmp);

	$pos2 = strrpos($tmp,">");
	$trid  = substr($tmp, $pos2+1, $max_len);

//	printf("step2 = [%s]\n", $trid);
	return trim($trid);
}

function get_one_mms_tag($xml, $tag)
{
	$pos = strpos($xml, $tag);
	if($pos === false) return "";

	$tmp = substr($xml, $pos + strlen($tag));
	$pos2 = strpos($tmp,"</".substr($tag,1));
	$trid  = substr($tmp, 0, $pos2);
	return trim($trid);
}

// 나이 계산 한국 나이. 100이 한계임..
function get_year_from_resno($birth)
{

    // 연령대별 계산 하는 것임.
    $sub_year = substr($birth, 0,6);

    if( $sub_year <= date("ymd") )  $year = '20'.$sub_year;
    else $year = '19'.$sub_year;

    $tm = date("Ymd") - $year;

    $usr[age] =  intval(  $tm  /  10000 ) + 1;
    $usr[year] =  $year;
    $usr[age_w] =  intval($usr[age]/10) *10;

    return $usr;
}
// 인코딩을 자동으로 확인 후 문자셋을 euc-kr로 변경한다.
function check_encoding($msg)
{
    $ary[] = "EUC-KR";
    $ary[] = "UTF-8";
    $m =  mb_detect_encoding($msg, $ary);
    if($m == "UTF-8") $ret = to_euckr($msg);
    else $ret= $msg;
    return $ret;
}


function get_curl_close($url)
{   
    $ch = curl_init();
	$header = array();

    array_push($header, 'Connection: Close');

    curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION , 1 );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1 );
    curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	curl_setopt($ch, CURLOPT_TIMEOUT, 15); 
    $ret = curl_exec($ch);
    curl_close($ch);
    return $ret;
}       
   

function aoa_encrypt ($value)
{
	$key = "ROCOMO01M12XV17Q";
	$iv = "M12ROCOMO01MQ36V";
	if(is_null($value))
	$value = "" ;
	$value = toPkcs7 ($value) ;
	$output = mcrypt_encrypt (MCRYPT_RIJNDAEL_128, $key,$value, MCRYPT_MODE_CBC, $iv) ;
	return base64_encode(urlencode($output));
}
function aoa_decrypt ($value)
{
	$key = "ROCOMO01M12XV17Q";
	$iv = "M12ROCOMO01MQ36V";
	if(is_null($value))
		$value = "" ;
	$value = urldecode(base64_decode($value));
	$output = mcrypt_decrypt (MCRYPT_RIJNDAEL_128, $key,$value, MCRYPT_MODE_CBC, $iv) ;
	return fromPkcs7 ($output) ;
}
if(!function_exists("toPkcs7"))
    {
    function toPkcs7 ($value)
    {
        if ( is_null ($value) )
            $value = "" ;
        $padSize = 16 - (strlen ($value) % 16) ;
        return $value . str_repeat (chr ($padSize), $padSize) ;
    }
    }

    if(!function_exists("fromPkcs7"))
    {
    function fromPkcs7 ($value)
    {
        $valueLen = strlen ($value) ;
        if ( $valueLen % 16 > 0 )
            $value = "";
        $padSize = ord ($value{$valueLen - 1}) ;
        if ( ($padSize < 1) or ($padSize > 16) )
            $value = "";
        // Check padding.
        for ($i = 0; $i < $padSize; $i++)
        {
            if ( ord ($value{$valueLen - $i - 1}) != $padSize )
                $value = "";
        }
        return substr ($value, 0, $valueLen - $padSize) ;
    }
    }


// 2013.06.11 by seo.
		
	function sqlfilter($str) {
		if (!get_magic_quotes_gpc()) $str = addslashes($str);
		
		$search = array("--","#",";");
		$replace = array("\--","\#","\;");
		$str = str_replace($search, $replace, $str);
		return $str;
	}
	
	function InjackCheck($checkString)
	{
		$strDenyString = "\|\|;\&\&;and;between;unicode;substring;select;insert;update;backup;delete;drop;alter;create;innerjoin;from;where;union;groupby;having;table;shutdown;kill;declare;openrowset;opendatasource;pwdencrypt;msdasql;sqloledb;char\(;syslogins;sysxlogins;sysdatabases;sysobjects;syscomments;raiserror;exec;xp_;sp_;xp_cmdshell;xp_reg;xp_servicecontrol;xp_setsqlsecurity;xp_readerrorlog;xp_controlqueueservice;xp_createprivatequeue;xp_decodequeuecommand;xp_deleteprivatequeue;xp_deletequeue;xp_displayqueuemesgs;xp_dsinfo;xp_mergelineages;xp_readpkfromqueue;xp_readpkfromvarbin;xp_repl_encrypt;xp_resetqueue;xp_sqlinventory;xp_unpackcab;xp_sprintf;xp_displayparamstmt;xp_enumresult;xp_showcolv;xp_updatecolvbm;xp_execresultset;xp_printstatements;xp_peekqueue;xp_proxiedmetadata;xp_displayparamstmt;xp_availablemedia;xp_enumdsn;xp_filelist;sp_password;sp_adduser;sp_addextendedproc;sp_dropextendedproc;sp_add_job;sp_start_job;sp_delete_alert;sp_msrepl_startup";  //필요한건 계속 추가 하면됨.
		$arrDenyString = explode(";",$strDenyString);
		$injackCnt = 0;
		foreach ($arrDenyString as $val)
		{
			if (eregi($val,strtolower(str_replace("%","",$checkString)))) {
				$injackCnts = explode($val,strtolower(str_replace("%","",$checkString)));
				if(count($injackCnts)>1){
					$injackCnt += count($injackCnts);
				}
			}
		}
		return $injackCnt;
	}
	
	function nocache()
	{
		header ("Pragma: no-cache");
		header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");// Date in the past
		header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
		header ("Cache-Control: no-cache, must-revalidate");// HTTP/1.1
		header ("Pragma: no-cache");// HTTP/1.0
		header("Content-Type: text/html; charset=euc-kr");
		
	}
	    
    



?>

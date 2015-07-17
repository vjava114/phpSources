<?php

	/*************** tmp_sendlist 를 위한 임시 세션 *****************/
	@session_start();
	$session_id = session_id();
	if ( !$session_id )
	{
		session_regenerate_id();
		$session_id = session_id();
	}	    
	//printf("%s<br>",$session_id);
	/*************** tmp_sendlist 를 위한 임시 세션 *****************/
	
	

	/*********** 디비 커넥션 정보  S. ************/
	if ( !is_dir("/tmp") )
	{
		$conn = mysql_connect("192.168.1.212", "root", "apmsetup");		// 로컬
	} else {
		$conn = mysql_connect("192.168.1.250", "rcs", "rcs123");		// 개발
		//echo "Real";
	}
	
	$mysql = mysql_select_db("TomTom", $conn);
	mysql_query("set names utf8");					//mysql_query("set names utf8");
	//mysql_query("SET character_set_results = 'utf-8', character_set_client = 'utf-8', character_set_connection = 'utf-8', character_set_database = 'utf-8', character_set_server = 'utf-8'");
	/*********** 디비 커넥션 정보  E. ************/
	
	
	
	
	
	/************* 난수발생기 ***************/
    srand( (double)microtime() * 1000000 );
    $rand = rand();

    function getRndInt($start = 1 , $end = 10 )
    {
        return rand($start, $end);
    }

    function MakeSeed()
    {
       $hash = md5(microtime());
       $loWord = substr($hash, -8);
       $seed = hexdec($loWord);
       $seed &= 0x7fffffff;

       return $seed;
    }

    
    function MakeRandString($inLength=16, $isOnlyNum = false){
       mt_srand( MakeSeed() );
       $newstring="";

       if( $inLength > 0){
           while( strlen($newstring) < $inLength ){
            if ( $isOnlyNum ) {
                $newstring.=chr(mt_rand(48,57));
            } else {
               switch( mt_rand(1,3) ){
                   case 1: $newstring.=chr(mt_rand(48,57)); break;  // 0-9
                   case 2: $newstring.=chr(mt_rand(65,90)); break;  // A-Z
                   case 3: $newstring.=chr(mt_rand(97,122)); break; // a-z
               }
           }
          }
       }
       return $newstring;
    }
    /************* 난수발생기 ***************/
    
    
    
    /************* 년월일시분초_난수 ***************/
    function getSaveFileName()
    {
    	$filename = date("YmdHis");			// 년월일시분초
    	$filename .= "_";					// 년월일시분초_
    	$filename .= MakeRandString(4);		// 년월일시분초_난수4자리
    	return $filename;
    }
    /************* 년월일시분초_난수 ***************/
?>

<?php 

	$Real_Path 	= dirname(__FILE__)."/..";

	if ( file_exists("/www/inc/php/mysqli.php")){
		$isReal = true;
		$LIB_PATH	=	"/www/inc/php";
	}else{
		$isReal = false;
		$LIB_PATH	=	$Real_Path."/web_class";
	}
	include "$LIB_PATH/common_util.inc";
	include "$LIB_PATH/upload.php";
	
	// 인젝션 공격 금지 Start
	nocache();
	
	foreach($_POST as $key =>  $val)
	{
		$_POST[$key] =  sqlfilter($val);
		//if ( InjackCheck($_POST[$key]) !== false )
		if ( InjackCheck($_POST[$key]) > 1 )
		{
				die("Hack1 ....");
		}
	}
	
	foreach($_GET as $key =>  $val)
	{
		$_GET[$key] =  sqlfilter($val);
		//if ( InjackCheck($_GET[$key]) !== false )
		if ( InjackCheck($_GET[$key]) > 1 )
		{
				die("Hack2 ....");
		}
	}
		
	/*foreach ($_GET as $secvalue)
	{ 
		if(eregi("<[^>]*script*\"?[^>]*>", $secvalue) || eregi("<[^>]*object*\"?[^>]*>", $secvalue) || eregi("<[^>]*iframe*\"?[^>]*>", $secvalue) || eregi(">", $secvalue)||
	    eregi("<[^>]*applet*\"?[^>]*>", $secvalue) || eregi("<[^>]*meta*\"?[^>]*>", $secvalue)   || eregi("<[^>]*style*\"?[^>]*>", $secvalue)  || eregi("<", $secvalue)||
	    eregi("<[^>]*form*\"?[^>]*>", $secvalue)   || eregi("\([^>]*\"?[^)]*\)", $secvalue)      || eregi("\"", $secvalue) ||  eregi("\'", $secvalue))
	    halt("사용한 문자 혹은 태그는 허용되지 않습니다.");
	}*/
	// 인젝션 공격 금지 End
	
	
	include "$LIB_PATH/mysqli.php";
	
	if ( $isReal ){
		//$conn = new MyDB('aoa_test');
		$DB = new  dbal_mysqli();
		//$DB->sql_connect("220.85.13.250", "rcs", "rcs123", "aoa_test");
		$DB->sql_connect("192.168.1.92", "rcs", "rcs123", "aoa_self");
	} else {
		//$conn = new MyDB("220.85.13.250","rcs","rcs123","aoa_test");
		$DB = new  dbal_mysqli();
		//$DB->sql_connect("220.85.13.250", "rcs", "rcs123", "aoa_test");
		$DB->sql_connect("192.168.1.92", "rcs", "rcs123", "aoa_self");
	}
	
	$err = $DB->_sql_error();
	
	if ($err->code ) {
		echo "DB Conn ErrCode : ".$err->code."<br>";
		echo "DB Conn ErrStr : ".$err->message."<br>";
	    exit(); 
	}
	
	include_once "$LIB_PATH/common_aoa.php";
	
?>

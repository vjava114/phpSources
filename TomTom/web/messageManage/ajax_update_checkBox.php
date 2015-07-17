
<?php
	include_once "../_inc/db.php";
		
	$seqno = $_GET[seqno];
	$checked = $_GET[checked];
	$my_session_id = $_GET[session_id];
	
	//echo $my_session_id;
	
	$query = "
		UPDATE tmp_sendlist 
		   SET check_yn = '$checked'
		WHERE seq = '$seqno';
	";
	
	//$result = mysql_query($query) or die ( mysql_error() );
	$result = mysql_query($query);
	
	

	//체크된 갯수 tmp_sendList 에서 받아오기
	
	$checkCountQuery = "
		SELECT 
			count(*) AS cnt 
		FROM 
			tmp_sendlist 
		WHERE 
			session_id='$my_session_id' 
		AND 
			check_yn=1
	";
	
	
	//echo $checkCountQuery; 
	
	$checkCountResult = mysql_query($checkCountQuery);
	$checkCount = mysql_fetch_array($checkCountResult);
	
	echo $checkCount['cnt'];
	
		
?>


<?
/*********** 디비 끊기 ***********/
	mysql_close($conn);
/*********** 디비 끊기 ***********/
?>
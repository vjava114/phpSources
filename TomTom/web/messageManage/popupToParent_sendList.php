
<?php
	include_once "../_inc/db.php";
	

	$popupToParent_sendListQuery = "
		SELECT mb_cellphone
		FROM   tmp_sendlist
		WHERE  session_id='$session_id'
		AND    check_yn  = 1
	";
	
	//echo "<br>쿼리 출력 : ".$popupToParent_sendListQuery;
	
	$popupToParent_sendListResult = mysql_query($popupToParent_sendListQuery) or die ( mysql_error() );
	
	$phoneArr = Array();
	$phoneTotal = "";
	while($row = mysql_fetch_array($popupToParent_sendListResult))
	{
		$phoneNo = $row['mb_cellphone'];
		$phoneNo = str_replace("-", "", $phoneNo);
		$phoneArr[] = $phoneNo;
	}
	$phoneArr = array_unique($phoneArr);
	$phoneTotal = implode(",", $phoneArr);	
	$cnt =	count($phoneArr);


	//echo "<br>전달할 String : ".$phoneTotal;
	
	/*
	$cnt = "
		 SELECT COUNT(*) AS cnt
		 FROM	tmp_sendlist
		 WHERE  session_id='$session_id' 
		 AND 	check_yn  =1
	 
	 ";
					
	$cntResult = mysql_query($cnt);
	$cntRows = mysql_fetch_array($cntResult);
	$cnt = $cntRows['cnt'];
	*/
?>



<?
/*********** 디비 끊기 ***********/
	mysql_close($conn);
/*********** 디비 끊기 ***********/
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script type="text/javascript">

window.onload = function () {
	
	opener.parent.document.getElementById('phoneNo').value = "<?php echo $phoneTotal ?>";
	opener.parent.document.getElementById('chkCnt').innerText = "<?php echo $cnt ?>";

	alert("중복된 전화번호를 제거하여 발송 대상자는 <?=$cnt?> 명 입니다.");

	self.close();
};


</script>

</head>

</html>
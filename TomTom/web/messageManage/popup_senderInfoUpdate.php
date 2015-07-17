<meta http-equiv="Content-Type" content="text/html; charset= utf-8" />
<?php



	include_once "../_inc/db.php";
	include "../_inc/global.php";



	$listQuery = "UPDATE sender_info SET phone_no='$_POST[phone0]', memo='$_POST[memo0]' where seq=0";
	$listResult = mysql_query($listQuery) or die(mysql_error());
	
	$listQuery = "UPDATE sender_info SET phone_no='$_POST[phone1]', memo='$_POST[memo1]' where seq=1";
	$listResult = mysql_query($listQuery) or die(mysql_error());
	
	$listQuery = "UPDATE sender_info SET phone_no='$_POST[phone2]', memo='$_POST[memo2]' where seq=2";
	$listResult = mysql_query($listQuery) or die(mysql_error());
	
	$listQuery = "UPDATE sender_info SET phone_no='$_POST[phone3]', memo='$_POST[memo3]' where seq=3";
	$listResult = mysql_query($listQuery) or die(mysql_error());
	
	$listQuery = "UPDATE sender_info SET phone_no='$_POST[phone4]', memo='$_POST[memo4]' where seq=4";
	$listResult = mysql_query($listQuery) or die(mysql_error());
	
	$listQuery = "UPDATE sender_info SET phone_no='$_POST[phone5]', memo='$_POST[memo5]' where seq=5";
	$listResult = mysql_query($listQuery) or die(mysql_error());
	
	$listQuery = "UPDATE sender_info SET phone_no='$_POST[phone6]', memo='$_POST[memo6]' where seq=6";
	$listResult = mysql_query($listQuery) or die(mysql_error());
	
	$listQuery = "UPDATE sender_info SET phone_no='$_POST[phone7]', memo='$_POST[memo7]' where seq=7";
	$listResult = mysql_query($listQuery) or die(mysql_error());
	
	$listQuery = "UPDATE sender_info SET phone_no='$_POST[phone8]', memo='$_POST[memo8]' where seq=8";
	$listResult = mysql_query($listQuery) or die(mysql_error());
	
	$listQuery = "UPDATE sender_info SET phone_no='$_POST[phone9]', memo='$_POST[memo9]' where seq=9";
	$listResult = mysql_query($listQuery) or die(mysql_error());



?>


<?
/*********** 디비 끊기 ***********/
	mysql_close($conn);
/*********** 디비 끊기 ***********/
	
	echo "";
?>
<html>
<script>
//parent.layerPopupReload();
//location.href="./layerPopup.php";
parent.layerPopupClose();
</script>
</htm>
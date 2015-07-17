<meta http-equiv="Content-Type" content="text/html; charset= utf-8" />

<?php
	include_once "../_inc/db.php";
	include "../_inc/global.php";
	
	
	$coupon_seq = $_GET["coupon_seq"];
	
	
	$query=	"
			DELETE FROM coupon_list
			 WHERE coupon_seq = $coupon_seq
	";
	
	$result = mysql_query($query);
	
	
	
	
	$Query=	"
			DELETE FROM coupon_pin
			 WHERE coupon_seq = $coupon_seq
	";
	
	$result = mysql_query($query);
	
	
	echo "<script>location.href= 'coupons_inquiry.html'; </script>";
	
	
	
	
	/* 디비 끊기 */
	mysql_close($conn);
	/* 디비 끊기 */
	
?>


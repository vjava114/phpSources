
<?php
	include_once "../_inc/db.php";
	
	
		$shop = $_POST['shop'];
	$min_month = $_POST['min_month'];
	$min_month2 = $_POST['min_month2'];
	//$min_month2 .= '235959';				// between 검색시, (8월1 ~ 8월1 검색) 하면 8월1일자가 검색이 안되는것 해결 
	$age = $_POST['age'];
	$sex = $_POST['sex'];
	$area = $_POST['area'];
	$area_det = $_POST['area_det'];
	$sum = $_POST['sum'];
	$sum2 = $_POST['sum2'];
	$menu = $_POST['menu'];
	
	/*
	echo "<br> 테스트".$shop;
	echo "<br> 테스트".$menu;
	echo "<br> 테스트".$min_month;
	echo "<br> 테스트".$min_month2;
	echo "<br> 테스트".$age;
	echo "<br> 테스트".$sex;
	echo "<br> 테스트".$area;
	echo "<br> 테스트".$area_det;
	*/
	
	/***************************** 검색조건 쿼리 생성 S. *****************************/ 
	$search = "";
	
	// 나이
	if( $age ){
		if( $age < 60 ){
			$search .= " AND mb_age between $age and $age+9";
		}else{
			$search .= " AND  mb_age >= '$age' ";
		}
	}
	//성별
	if($sex){
		$search .= " AND mb_gender  = '$sex' ";
	}
	
	// 기간
	if( $min_month != "" ){
		$search .= " AND sale_date BETWEEN '$min_month' AND '$min_month2' ";
	}
	// 매장
	if( $shop ){
		$search .= " AND  st_name LIKE '%$shop%' ";
	}


	// 메뉴
	if( $menu ){
		$search .= " AND (SELECT mn_korname
	       				FROM    TC_MENU
	       				WHERE   tc_menu.goods_cd = g.goods_cd) LIKE  '%$menu%' ";
	}
	// 금액 ~이상 ~이하
	if( $sum ){
		$search .= " AND sale_amt >= $sum";
	}
	if( $sum2 ){
		$search .= " AND sale_amt <= $sum2";
	}
	
	
	//지역
	if($area && $area_det){
	       $search .= " AND ( mb_addrCiDo =  '$area' AND mb_addrGuGun =  '$area_det' ) ";
	}
	//echo"area:".$area;
	/***************************** 검색조건 쿼리 생성 E. *****************************/
	
	
	
	$popupToParent_sendListQuery = "
		SELECT mb_cellphone
		FROM   tmp_sendlist
		WHERE  session_id='$session_id'
		AND    check_yn  = 1
			$search
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
	$phoneTotal = implode(",", $phoneArr);
	
	//echo "<br>전달할 String : ".$phoneTotal;
	
	
	$cnt = "
		 SELECT COUNT(*) AS cnt
		 FROM tmp_sendlist
		 WHERE  session_id='$session_id' 
		 AND check_yn  =1
		 	$search
	 
	 ";
					
	$cntResult = mysql_query($cnt);
	$cntRows = mysql_fetch_array($cntResult);
	$cnt = $cntRows['cnt'];
	
	
	
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

	self.close();
};


</script>

</head>

</html>
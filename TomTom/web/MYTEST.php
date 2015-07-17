<?php
	include_once "../_inc/db.php";
	include "../_inc/global.php";

    include "../web_class/paging.php";

	if(!$_POST['page']) $page=1;
	else $page = $_POST['page']; 

	//	echo "현재페이지 : ".$page;

	$shop = $_POST['shop'];
	$min_month = $_POST['min_month'];
	$min_month2 = $_POST['min_month2'];
	$min_month2 .= '235959';				// between 검색시, (8월1 ~ 8월1 검색) 하면 8월1일자가 검색이 안되는것 해결 
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
			$search .= "AND (date_format(now(), '%y')+2001) - mb_age between $age and $age+9";
		}else{
			$search .= " AND (date_format(now(), '%y')+2001) - mb_age >= '$age' ";
		}
	}
	//성별
	if($sex){
		$search .= " AND if(mb_gender='M','남','여') = '$sex' ";
	}
	
	// 기간
	if( $min_month != "" ){
		$search .= " AND s.sale_date BETWEEN '$min_month' AND '$min_month2' ";
	}
	// 매장
	if( $shop ){
		$search .= " AND  (SELECT st_name
	       					FROM    TC_STORE
	      					WHERE   tc_store.ms_no = s.ms_no
	       					) LIKE '%$shop%' ";
	}
	// 메뉴
	if( $menu ){
		$search .= " AND (SELECT mn_korname
	       				FROM    TC_MENU
	       				WHERE   tc_menu.goods_cd = g.goods_cd) LIKE  '%$menu%' ";
	}
	// 금액 ~이상 ~이하
	if( $sum ){
		$search .= " AND s.sale_amt >= $sum";
	}
	if( $sum2 ){
		$search .= " AND s.sale_amt <= $sum2";
	}
	
	
	//지역
	if($area && $area_det){
	       $search .= " AND ( mb_addrCiDo =  '$area' AND mb_addrGuGun =  '$area_det' ) ";
	}
	//echo"area:".$area;
	/***************************** 검색조건 쿼리 생성 E. *****************************/
	
	
	
	
	if ($_POST['dojob'] == 'search' )		// 조회버튼을 눌러서 submit 되었을때만 아래 코딩 1,2,3 및 재 submit(); 을 수행.
	{
		echo "dojob = ". $_POST['dojob'] . "<br/>\n";
		
		// 1.tmp_sendlist 테이블 에서 현재 세션ID와 일치하는 row들 삭제
		
		$deleteQuery = "
			DELETE FROM tmp_sendlist
			 WHERE session_id='$session_id';
		";
		$resultdeleteQuery = mysql_query($deleteQuery);
		
		
		// 2. tmp_sendlist 테이블 에서 등록일이 3일 이상 된 row들 삭제
		$deleteQuery2="
			DELETE FROM tmp_sendlist
			 WHERE reg_date < DATE_ADD(now(), INTERVAL -3 DAY)
		";
		$resultdeleteQuery2 = mysql_query($deleteQuery2);
		
		
		//3. 셀렉트인서트 시키기
		
		$selectInsertQuery = "
		
		INSERT INTO tmp_sendlist
		(
			session_id,	reg_date, mb_UID, mb_name, mb_age,mb_gender,mb_cellphone,mb_addrCiDo,mb_addrGuGun, sale_date, st_name, mn_korname, sale_amt 
		)
		
		SELECT 
			   '$session_id' as session_id,
			   now() as reg_date,
			   mb_UID,
			   mb_name,
			   (date_format(now(), '%y')+2001) - mb_age as mb_age,
			   if(mb_gender='M','남','여') as mb_gender,
			   mb_cellphone,
			   mb_addrCiDo,
			   mb_addrGuGun,	   
			   s.sale_date,
		
			   (SELECT st_name
				  FROM    TC_STORE
				 WHERE   tc_store.ms_no = s.ms_no
			   ) AS st_name,
		
			   (SELECT mn_korname
				  FROM    TC_MENU
				 WHERE   tc_menu.goods_cd = g.goods_cd
			   ) AS mn_korname,
			   s.sale_amt
		
		
		FROM   tc_member m
			   JOIN TH_BATCH_SALESINFO s 
			   ON m.CUST_NO = s.CUST_NO
			   LEFT JOIN TH_BATCH_GOODSINFO g 
			   ON m.CUST_NO = g.CUST_NO
		
		";
		$insertResult = mysql_query($selectInsertQuery);
		
		echo "selectInsertQuery 쿼리 : ".$selectInsertQuery;
	
	
	/***************************** 셀렉트인서트 E. *****************************/
	?>
		<html>
		<head>
	
		</head>
		<body>
		<form name="q" id="q" method="post" action="./db_search_popup.html" target="_self">
	<?
			foreach($_POST as $key => $val)
			{
				if ( $key == 'dojob')
				{
					echo "<input type='hidden' name='$key' value=''>\n";
				} else {
					echo "<input type='hidden' name='$key' value='$val'>\n";
				}
			}
	
	?>
	
		</form>
		</body>
			<script type="text/javascript">
				document.q.submit();
			</script>
		</html>
	
	
	<?
		exit;
		
	}	// if ($_POST['dojob'] == 'search' )

	
	
/****** paging S. *****************************************************************************************************/
	$page_row = 10;			//한 화면에 나타날 게시물 수
	$page_scale = 10;		//페이지 블록

	$startLimit = ($page-1) * $page_scale;	// limit의 시작값.
	$endLimit = $page_row; 



	// 갯수 카운트
	$cntQuery = "
			SELECT count(*) as cnt
			  FROM tmp_sendlist
			WHERE session_id = '$session_id'
				  $search
	";
					
	$cntResult = mysql_query($cntQuery);
	$cntRows = mysql_fetch_array($cntResult);
	$cnt = $cntRows[0];
	
	$total_record = $cnt;	

 
   $paging_str =paging($page,$page_row,$page_scale);
/****** paging E. *****************************************************************************************************/



/***************************** 리스트 출력 쿼리 생성 S. *****************************/

	// 리스트 출력
	$query = "
			SELECT
					seq,
					session_id,
					reg_date,
					check_yn,
					mb_UID,
					mb_name,
					mb_age,
					mb_gender,
					mb_cellphone,
					mb_addrCiDo,
					mb_addrGuGun,
					sale_date,
					st_name,
					mn_korname,
					sale_amt
			FROM 
					tmp_sendlist
			
			WHERE 
					session_id = '$session_id'
			
					'$search'
			
			ORDER BY 
					sale_date asc
			LIMIT
					$startLimit,$endLimit
	";
					
	//echo "<br> === 리스트 출력 쿼리 : === <br>".$query;	
	$result = mysql_query($query);
/***************************** 리스트 출력 쿼리 생성 E. *****************************/
	




	// 매장정보 검색
	$shopQuery = "SELECT store FROM customers GROUP BY store ";
	$shopResult = mysql_query($shopQuery);

	//시도
	$sidoQUERY .= " SELECT sido FROM tm_zipcode	GROUP BY sido ";
	$sidoResult = mysql_query($sidoQUERY);
	
	
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TOM N TOMS ADMIN</title>
<link rel="stylesheet" type="text/css" href="../css/default.css" />
<link rel="stylesheet" type="text/css" href="../css/admin.css" />
<link rel="stylesheet" type="text/css" href="../css/ui-lightness/jquery-ui-1.9.2.custom.min.css" />	<!-- jquery-ui 기본이미지 -->
<script type="text/javascript" src="../js/jquery-1.8.3.min.js"></script>								
<script src="../js/jquery-ui-1.9.2.custom.js"></script>													<!-- datepicker -->




<script type="text/javascript" src="../js/my/my_calendar.js"></script>		<!-- 커스텀 달력 -->
<script type="text/javascript" src="../js/my/my_validation.js"></script>	<!-- 날짜 등 유효성 검사 -->


<script type="text/javascript">

//검색하기 이전에 입력했던 검색조건들을 다시 셋팅.
	$(document).ready(function(){		
		$("#age").val('<?= $_POST[age] ?>').attr("selected","selected");			// 연령
		$("#sex").val('<?= $_POST[sex] ?>').attr("selected","selected");			// 성별		
		$("#area").val('<?= $_POST[area] ?>').attr("selected","selected");			// sido
		$("#area_det").val('<?= $_POST[area_det] ?>').attr("selected","selected");	// gugun		
		$("#shop").val('<?= $_POST[shop] ?>').attr("selected","selected");			// 매장		
		$("#menu").val('<?= $_POST[menu] ?>').attr("selected","selected");			// 메뉴			
		$("#min_month").val('<?= $_POST[min_month] ?>');							// 검색 시작일
		$("#min_month2").val('<?= $_POST[min_month2] ?>');							// 검색 종료일
		$("#sum").val('<?= $_POST[sum] ?>');										// 검색 시작일
		$("#sum2").val('<?= $_POST[sum2] ?>');										// 검색 종료일
	});
	
	$(document).ready(function(){
		$("#gosearch").click(function(){
			
			var form = document.frm;
			var sDate = $("#min_month").val();
			var eDate = $("#min_month2").val();
			$("#dojob").val('search');
			
			if(dateChk(sDate, eDate) != true){			// my_validation.js
				//$("#page").val("1");
				frm.action = "db_search_popup.html";
				frm.submit();
			}		
		});		
	});	
	
	
	$(document).ready(function(){
		var form = document.frm;
		$("#goExcel").click(function(){
			form.action = "dataToExcel.php";
			form.submit();
		});
	});
	
	

</script>

<script>
// 모든체크박스 체크하기.
$(document).ready(function(){
	$("#ckAll").click(function(){
		if( $("#ckAll:checked").val() ){
			$("input:checkbox:not(checked)").attr("checked",true);
		}else{
			$("input:checkbox:checked").attr("checked",false);
		}
		
	});
});


// 체크박스 체크된값 구하기.
$(document).ready(function(){
	$("input[type=checkbox]").click(function(){
		var cnt = $("form input[name=myCheckBox]:checked").length;
		document.getElementById('checkCnt').innerText=cnt;
	});
});	
	
	
	
	
// 확인버튼 눌렀을때 이벤트...
function ok(){
	
	var items=[];
	$('input[name="myCheckBox"]:checkbox:checked').each(function(){items.push($(this).val());});
	 
	var size = items.length;	
	var tmp = items.join(',');

	tmp = tmp.replace(',','');
	tmp  = tmp.replace(/-/gi,'');
	
	opener.parent.document.getElementById('phoneNo').value = tmp;
	opener.parent.document.getElementById('chkCnt').innerText = size;
	
	self.close();
}
</script>

<script type="text/javascript">

$(document).ready(function(){
	$("#area option:selected").each(function(){ 
	
	var sido=$(this).val(); 
		$.ajax({ 
			type: "get", 
			url: "ajax_getGugun.php?sido="+encodeURIComponent(sido), 
			success: function(data) { 

			$("select#area_det").html(data);   
			
			},error: function (data) { alert('지역을 다시 선택 해 주세요'); return false;} 
		}); 
	});	 
});

$(document).ready(function(){

 	$("#area").change(function(){ 
 		$("#area option:selected").each(function(){ 
			
 			//alert($(this).val()); 
			
 			var sido=$(this).val(); 
			
			
			
 			$.ajax({ 
 				type: "get", 
 				url: "ajax_getGugun.php?sido="+encodeURIComponent(sido), 
 				success: function(data) {
 					//alert(data);
					$("select#area_det").html(data);
					
 				},error: function (data) { alert('지역을 다시 선택 해 주세요'); return false;} 
 			}); 
			
			
 		});	 
 	}); 
 });



// 체크박스 선택시, tmp_sendlist 업데이트 처리

var doCheckUpdate = false;
function checkUpdate(obj)
{
	if ( doCheckUpdate == true)
	{
		alert('처리 중입니다, 잠시 후 다시 클릭 하시거나,\n페이지를 새로고침 하여주십시요.');
		return;
	}
	doCheckUpdate = true;
	var seqno = obj.value;
	var checked = (obj.checked)?1:0;
	
	
	$.ajax({ 
		type: "get", 
		url: "ajax_update_checkBox.php?seqno=" + seqno + "&checked="+checked, 
		success: function(data) { 
			doCheckUpdate = false;
			return;
		},error: function (data) {
			alert('실패');
			document.frm.submit();
		}
		
	}); 
	
	
}
 </script>
 
  
 
 <script language="javascript">
//페이지 이동
function goPage(curpage) {
	
	//alert(curpage);
	var frm = document.frm;	
	var page = document.getElementById("page");
	page.value = curpage;
	
	frm.submit();	
}
</script>
 

</head>

<body>
<form name="frm" action="db_search_popup.html" method="post" id="frm">
	<input type="hidden" name="dojob" id="dojob" value=""></input>
	<div class="db_popup group">
		<div class="popup_top">
			<h1>발송대상자 검색</h1>
			<span><a href="#"><img src="../images/btn_close.png" width="22" height="22" alt="창닫기" /></a></span>
		</div>
		<div class="section_main padding">
		
		
			<table>
			
			<colgroup>
			<col width="154px" />
			<col width="*" />
			</colgroup>
			<tbody>
			
			<tr>
				<th class="list_row" scope="row">고객정보</th>
				<td class="list_row alt">
					<label for="age" class="mr10">연령</label>
					<select id="age" name="age" class="input mr20" style="width:96px" title="연령대를 선택해주세요.">
						<option value="">전체</option>
						<option value="1">이하 (1~9세)</option>
						<option value="10">10대</option>
						<option value="20">20대</option>
						<option value="30">30대</option>
						<option value="40">40대</option>
						<option value="50">50대</option>
						<option value="60">이상 (60세 이상)</option>
					</select>
					<label for="sex" class="mr10">성별</label>
					<select id="sex" name="sex" class="input mr20" style="width:96px" title="성별을 선택해주세요.">
						<option value="">모든 성별</option>
						<option value="여">여</option>
						<option value="남">남</option>
					</select>
					<label for="area" class="mr10">지역</label>
					<select id="area" name="area" class="input mr10" style="width:96px">
					<option value="">전체지역</option>
					<?
						while($sidoRows = mysql_fetch_array($sidoResult)){
					?>
							<option value="<?= $sidoRows['sido'] ?>"><?= $sidoRows['sido'] ?></option>
					<?
						}
					?>
					</select>
			         <select class="input" id="area_det" name="area_det" style="width:96px" title="지역을 선택해주세요.">
					</select>
				</td>
			</tr>
			<tr>
				<th class="list_row" scope="row">이용정보</th>
				<td class="list_row alt">
					<label for="min_month" class="mr10">기간</label>
					<input type="text" id="min_month" name="min_month" autocomplete="off" title="조회하실 최소기간을 선택해주세요." class="input mr5" id="min_month" style="width:96px" />&nbsp;-&nbsp;
					<input type="text" id="min_month2" name="min_month2" autocomplete="off" title="조회하실 최대기간을 선택해주세요." class="input mr5" style="width:96px" />
					<label for="shop" class="mr10">매장</label>
					<select id="shop"  name="shop" id="shop" class="input mr10" style="width:96px" title="지역을 선택해주세요.">
					<option value="">전체지역</option>
					<?
						while($shopRows = mysql_fetch_array($shopResult)){
					?>
							<option value="<?= $shopRows['store'] ?>"><?= $shopRows['store'] ?></option>
					<?
						}
					?>
					</select>
					<label for="menu">메뉴</label>
					<select id="menu" name="menu" class="input mr20" style="width:96px" title="메뉴를 선택해주세요.">
						<option value="">전체메뉴</option>
						<option value="아메리카노">아메리카노</option>
					</select>
					<label for="sum" class="mr10">금액</label>
					<input type="text" id="sum" name="sum" class="input mr5" style="width:96px" title="최대 금액을 입력해주세요." /> 이상 -&nbsp;
					<input type="text" id="sum2" name="sum2" class="input mr5" style="width:96px" title="최소 금액을 입력해주세요." /> 이하
				</td>
			</tr>
				
			</tbody>			
						
			</table>
	
			<p class="db_inquiry">
				<img  src="../images/btn_inquiry.gif" alt="조회" id="gosearch"/>
			</p>
		
			<div class="scroll">
				<table class="mg0">
				<colgroup>
				<col width="42px" />
				<col width="42px" />
				<col width="133px" />
				<col width="70px" />
				<col width="70px" />
				<col width="152px" />
				<col width="*" />
				<col width="103px" />
				<col width="70px" />
				<col width="136px" />
				<col width="106px" />
				</colgroup>
				<thead>
					<tr>
						<th class="alt" scope="col"><input type="checkbox" id="ckAll"/></th>
						<th class="alt" scope="col">번호</th>
						<th class="alt" scope="col">이름</th>
						<th class="alt" scope="col">나이</th>
						<th class="alt" scope="col">성별</th>
						<th class="alt" scope="col">휴대폰번호</th>
						<th class="alt" scope="col">주소</th>
						<th class="alt" scope="col">이용일자</th>
						<th class="alt" scope="col">이용매장</th>
						<th class="alt" scope="col">이용메뉴</th>
						<th class="alt" scope="col">이용금액</th>
					</tr>
				</thead>
				<tbody>
				<?
					while($row = mysql_fetch_array($result)){
				?>
					<tr>
				<?
						if($row['check_yn'] == 1){
				?>
							<td class="alt"><input type="checkbox" name="myCheckBox" value="<?=$row['seq'] ?>" checked="checked" onclick="checkUpdate(this)"/></td>
				<?
						}else{
				?>
							<td class="alt"><input type="checkbox" name="myCheckBox" value="<?=$row['seq'] ?>" onclick="checkUpdate(this)"/></td>
				<?
						}
				?>
						<td class="alt"><?=$row['mb_UID'] ?></td>
						<td class="alt"><?=$row['mb_name'] ?></td>
						<td class="alt"><?=$row['mb_age'] ?></td>
						<td class="alt"><?=$row['mb_gender'] ?></td>
						<td class="alt"><?=$row['mb_cellphone'] ?></td>
						<td class="alt"><?=$row['mb_addrCiDo'] ?>&nbsp;<?=$row['mb_addrGuGun'] ?></td>
						<td class="alt"><?=$row['sale_date'] ?></td>
						<td class="alt"><?=$row['st_name'] ?></td>
						<td class="alt"><?=$row['mn_korname'] ?></td>
						<td class="alt"><?=$row['sale_amt'] ?></td>
					</tr>		
				<?
					}
				?>
				</tbody>
				</table>
			</div>
				<!-- 페이징 S. -->
					<div class="section_bottom">
						<p class="page_num">
							<span>
								<?=$paging_str?>
							</span>
						</p>
					</div>		
					<!-- 페이징 E. -->
					
			
					<!--  현재페이지 정보 보관하기 위한 hidden 값 S. -->
    				<input type="hidden" name="page" id="page" value="<?= $page ?>" />					
					<!--  현재페이지 정보 보관하기 위한 hidden 값 E. -->
			
			
			<div class="section_btn_2 db">
				<p>총</p><strong class="txt_red"><b id="checkCnt">0</b>&nbsp;</strong><span>명</span>
<!-- 				<input type="image" src="../images/btn_excel.gif" alt="excel down" /> -->
				<img src= "../images/btn_confirm.gif" value="확인버튼" onclick="ok()" >
				<p><input type="image" id="goExcel" name="goExcel" src= "../images/btn_excel.gif" alt="excel down" /></p>
								
			</div>			
		</div>
	</div>
</form>
</body>
</html>


<?
/*********** 디비 끊기 ***********/
	mysql_close($conn);
/*********** 디비 끊기 ***********/
?>
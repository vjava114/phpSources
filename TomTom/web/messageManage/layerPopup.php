<?php 
	include_once "../_inc/db.php";
	include "../_inc/global.php";
	
	$listQuery = "SELECT 
					seq, phone_no, memo
				FROM 
					sender_info
				ORDER BY
					seq asc
				LIMIT
					10
	";
	//echo "<br>쿼리 확인 : ".$query;
	
	$listResult = mysql_query($listQuery) or die(mysql_error());


?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TOM N TOMS ADMIN</title>
<link rel="stylesheet" type="text/css" href="../css/default.css" />
<link rel="stylesheet" type="text/css" href="../css/admin.css" />
<script type="text/javascript" src="../js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/input_file.js"></script>


<script type="text/javascript">


// layer 팝업에서 선택한 번호를 message_send_v1 으로 보내줌.
$(document).ready(function(){

	$(".ckNumber").change(function(){
		if($(this).attr("checked")) 
		{			
			var item = $(this).parent().next().children("input").val();	// <input radio> 버튼의 parent 는 <td> 이다.
			$("#selphone").val(item);

		}
	});	
});
</script>


<script type="text/javascript">

// 레이어팝업창에 메모 옆 x버튼 누를시..
function deleteRow(rowNum){
	var answer = confirm('정말로 삭제 하시겠습니까?')
	if(answer){
		$("#phone"+rowNum).val("");
		$("#memo"+rowNum).val("");		
	}else{
		return false;
	}
}

</script>





</head>

<body>

<!-- 팝업창 S. -->

<div class="db_popup group" >
	<div class="popup_top">
		<h1>발송 번호</h1>
		<span>
			<a href="#"><img src="../images/btn_close.png" width="22" onclick="javascript:parent.layerPopupClose()" height="22" alt="창닫기" id="popup1_Close"/></a>
		</span>
	</div>
	<form action="popup_senderInfoUpdate.php" name="frm" method="post" target="_self">
	<div class="pop_box">
		<table>
		<colgroup>
		<col width="28px"/>
		<col width="173px"/>
		<col width="*"/>		
		</colgroup>
		<thead>
			<tr>
				<th scope="col"><strong>선택</strong></th>
				<th scope="col"><strong>전화번호</strong></th>
				<th scope="col"><strong>메모</strong></th>
			</tr>
		</thead>
		<tbody>

			<?php
			$cnt=0; 
			for($i=0; $i<10; $i++)
			{
				
				$row = mysql_fetch_array($listResult);
				if ( !$row[phone_no] || !$row[memo]) $disable="disabled";
				else $disable = "";
			?>
			<tr id="row<?= $cnt ?>">				
				<td><input type="radio" name="radio"  class="ckNumber" <?=$disable?> /></td>
				<td><input type="text" class="input" id="phone<?=$cnt?>" name="phone<?=$cnt?>" value="<?= $row[phone_no] ?>" style="width:156px"/></td>
				<td>
					<input type="text" class="input" id="memo<?=$cnt?>" name="memo<?=$cnt?>" style="width:115px" value="<?= $row[memo] ?>"/>
					<span><a href="#"><img src="../images/ico_del2.gif" width="6" height="7" alt="삭제" onclick="deleteRow(<?= $cnt ?>);" /></a></span>
				</td>
			</tr>			
			
			
			
			
			<?php
				$cnt++;
			}
			?>
			
		
		</tbody>
		</table>
		
		<p class="pop_btn">
			<img class="btn_pointer" id="btnSave" src="../images/btn_save.gif" onclick="javascript:fnSubmit();" alt="저장 및 적용" />
			<a href="#"><img class="btn_pointer" onclick="javascript:parent.layerPopupClose()" src="../images/btn_cancel_big.gif" alt="취소" id="popup1_Close2"/></a>
		</p>
	</div>	
	<input type="hidden" name="selphone" id="selphone" value=""></input>
	</form>
</div>
<!-- 팝업창 E. -->
<Script type="text/javascript">
function fnSubmit()
{
	parent.document.frm.sender.value = document.frm.selphone.value;					// 현재 문서의 parent 는 iframe 불러왔던 문서 이다.
	document.frm.submit();
	
}
</Script>
</body>
</html>


<?
/*********** 디비 끊기 ***********/
	mysql_close($conn);
/*********** 디비 끊기 ***********/
?>

function dateChk(sDate, eDate){
	if(sDate!="" || eDate!="")
	{
		if(sDate==""){
			alert("발송기간을 선택해 주세요!");
			$("#min_month").focus();
			return false;
		}
		if(eDate==""){
			alert("발송기간을 선택해 주세요!");
			$("#min_month2").focus();
			return false;
		}
		sDate = sDate.replace(/-/g, "");
		eDate = eDate.replace(/-/g, "");

		var sDate = parseInt(sDate);
		var eDate = parseInt(eDate);

		if(eDate < sDate){
		 alert("발송기간이 잘못되었습니다.");
		 return false;
		}
		
	}
}
function dateChk2(sDate, eDate){


		if(sDate=="")
		{
			alert("시작 기간을 선택해 주세요!");
			$("#min_month").focus();
			return false;
		}
		if(eDate=="")
		{
			alert("종료 기간을 선택해 주세요!");
			$("#min_month2").focus();
			return false;
		}

		sDate = sDate.replace(/-/g, "");
		eDate = eDate.replace(/-/g, "");

		var sDate = parseInt(sDate);
		var eDate = parseInt(eDate);

		if(eDate < sDate){
		 alert("발송기간이 잘못되었습니다.");
		 return false;
		}
		
		if (eDate-sDate > 7)
		{
			alert("조회 기간은 일주일 이내만 가능 합니다.");
			return false;
		}


		
	
}
/*
실행 예제!!. 
<script type="text/javascript" src="../js/my/my_validation.js"></script>

$(document).ready(function(){
	$("#gosearch").click(function(){

		var form = document.frm;
		var sDate = $("#min_month").val();
		var eDate = $("#min_month2").val();

		if(dateChk(sDate, eDate) != true){			// my_validation.js		
			frm.action = "db_search_popup.html";
			frm.submit();
		}		
	});		
});	
*/

$(function() {
	//캘린더
	$("#min_month").datepicker({
		dateFormat: 'yymmdd',
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNamesMin: ['일','월','화','수','목','금','토'],
		//weekHeader: 'Wk',
		//changeMonth: true, //월변경가능
		//changeYear: true, //년변경가능
		yearRange:'1988:+0', // 연도 셀렉트 박스 범위(현재와 같으면 1988~현재년)
		showMonthAfterYear: true, //년 뒤에 월 표시
		buttonImageOnly: true, //이미지표시  
		buttonText: '날짜를 선택하세요', 
		autoSize: false, //오토리사이즈(body등 상위태그의 설정에 따른다)
		showOn: "button",
		buttonImage: '../images/btn_calendar.gif' //이미지주소 /wtm/images/egovframework/wtm2/sub/bull_calendar.gif
		//showOn: "both" //엘리먼트와 이미지 동시 사용
	});
	$("#min_month2").datepicker({
		dateFormat: 'yymmdd',
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNamesMin: ['일','월','화','수','목','금','토'],
		//weekHeader: 'Wk',
		//changeMonth: true, //월변경가능
		//changeYear: true, //년변경가능
		yearRange:'1988:+0', // 연도 셀렉트 박스 범위(현재와 같으면 1988~현재년)
		showMonthAfterYear: true, //년 뒤에 월 표시
		buttonImageOnly: true, //이미지표시  
		buttonText: '날짜를 선택하세요', 
		autoSize: false, //오토리사이즈(body등 상위태그의 설정에 따른다)
		showOn: "button",
		buttonImage: '../images/btn_calendar.gif' //이미지주소 /wtm/images/egovframework/wtm2/sub/bull_calendar.gif
		//showOn: "both" //엘리먼트와 이미지 동시 사용		
		
	});
});

$(document).ready(function(){
	$("#gosearch").click(function(){

		var frm = document.frm;
		var sDate = $("#min_month").val();
		var eDate = $("#min_month2").val();

		if(sDate!="" || eDate!=""){
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
		
		frm.action = "coupons_inquiry.html";
		frm.submit();
	});
});



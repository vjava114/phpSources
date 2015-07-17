$(document).ready(function() {
	// 체크박스 선택
	$(".ip_check").change(function() {
		if($(this).hasClass("on")) {
			$(this).removeClass("on");
		}else {
			$(this).addClass("on");
		}
	});

	// 라디오 선택
	$(".ip_radio").change(function() {
		var nm = $(this).find("input").attr("name");
		$("input[name="+nm+"]").parents().removeClass("on");
		$(this).addClass("on");
	});
});
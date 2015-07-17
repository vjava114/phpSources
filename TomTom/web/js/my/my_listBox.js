
/**** li 추가 및 삭제 함수 S. ****/
var cnt = 1;



function removeBtn_all(){
	$("#listBox li").remove();
}

function removeBtn(cnt){
	$("#btn"+cnt).remove();	
}
function appendBtn(){					// btn1, btn2 이런식으로 생성됨
	
	
	var text = $("#addText").val();
	if(text.length>0){
		if($("#listBox li").index()+1 == 0){
			cnt = 1;
			
		} 	
	 	$("#listBox").append("<li id='btn"+cnt+"'><p>"+text+"</p><span><img onclick='javascript:removeBtn("+cnt+");' src='../images/ico_del.gif' width='6' height='7' alt='삭제'/></span></li>");
	 	cnt++;
	 	
	 	$("#addText").val("");	
	}
	
	
}
/**** li 추가 및 삭제 함수 E. ****/
 



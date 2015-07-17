/*
 * 보류... 
function fileButtonClick(){
	document.getElementById('upfile').click();
}
function fileButtonClick2(){
	document.getElementById('upfile2').click();
}
*/

// 엑셀파일업로드
function changeFile(){
	//alert("changeFile");
	var itemName = document.getElementById('upfile').value	

		if (  itemName.indexOf('.xlsx') >0 ){
			alert("파일은 xls 형식 으로만 업로드 가능합니다.");
		} else if(itemName.indexOf('.xls') < 0){
			alert("파일은 xls 형식 으로만 업로드 가능합니다.");
		}else{
			document.getElementById('upfile_value').value = document.getElementById('upfile').value;
		}		
}

// 이미지파일 업로드
function changeFile2(){
	//alert("changeFile2");	
	var itemName = document.getElementById('upfile2').value

	if ( itemName.indexOf('.jpg') == -1 ){
		alert("이미지 파일은 jpg 형식 으로만 업로드 가능합니다.");
	}else{
		document.getElementById('upfile_value2').value = document.getElementById('upfile2').value;
		//document.getElementById('type').innerText="MMS";
	}	
}






function readData(x,y)
{
    var excel=new ActiveXObject("Excel.Application"); 
    excel.workbooks.open("my.xls");
    var data = excel_sheet.cells(x,y).value; //x,y consider the coordinate of row and column or the data 
    return data;
}

<meta http-equiv="Content-Type" content="text/html; charset= utf-8" />

<?php 
	include_once "../_inc/db.php";
	include "../_inc/global.php";
	
	require_once "../_lib/reader.php";
	

	
	
	$coupon_name = $_POST["coupon_name"];
	$memo = $_POST["memo"];
	
	
	/*********** 트랜잭션 셋팅 S. ************/
    $success = true; 										// 트랜잭션 성공 여부
    $result = mysql_query("SET AUTOCOMMIT=0", $conn);		// 트랙잭션을 시작한다.
    $result = mysql_query("BEGIN", $conn);
	/*********** 트랜잭션 셋팅 E. ************/
    
    
	while(true)
	{	
		/*********** coupon_list 테이블에 등록 S. ***********/
		$query = "insert into coupon_list (coupon_name,memo,use_yn) values ('{$coupon_name}', '{$memo }', 'N') ";
		//echo "<br> 쿠폰등록 쿼리 : ".$query;
		$result = mysql_query($query, $conn);
		$maxseq = mysql_insert_id();		// max값 가져옴. 마지막으로 증가된 PK값 가져옴.
		
		if(!$result){
			$success = false;			// 실패시 트랜잭션 false
			//echo "<br> coupon_list 등록 에러";
			break;
		} else {
			//echo "쿠폰 등록 성공";
		}
		/*********** coupon_list 테이블 등록 E. ***********/
		
		
		
		
		/*********** BEGIN : 엑셀파일 읽어들이기 + 디비 등록하기. ***********/
		if($_FILES['upfile']['name'])
		{	
			
			@mkdir($_SERVER["DOCUMENT_ROOT"]."/uploads/pinList");
			@chmod("0701",$_SERVER["DOCUMENT_ROOT"]."/uploads/pinList");
			@mkdir($_SERVER["DOCUMENT_ROOT"]."/uploads/pinList/".date("Ymd"));
			@chmod("0701",$_SERVER["DOCUMENT_ROOT"]."/uploads/pinList/".date("Ymd"));
			
			$upload_dir = $_SERVER["DOCUMENT_ROOT"]."/uploads/pinList/".date("Ymd")."/";	//업로드 경로
			
			$upload_file= $upload_dir.getSaveFileName();			//업로드 경로 + 파일이름
			
			$temp_file = $_FILES['upfile']['tmp_name'];								//임시 파일이름 생성
			
			if( move_uploaded_file($temp_file, $upload_file) == true ){
				@chmod("0600",$upload_file);
				
				//echo "<br> 파일 갖고오기 SUCCESS..<br>";
				//echo "경로".$upload_file."<br>";
			}
			else
			{
				//echo "<br> 파일 갖고오기 FIALED..<br>";
				$success =  false;
				break;
			}
		} else {
			$success =  false;
			//echo "<br> xls 파일 저장 오류";
			break;
		}
			
		$data = new Spreadsheet_Excel_Reader();	 				// 한글깨지면 주석해제 $data->setOutputEncoding("UTF-8//IGNORE");	
		$data->read($upload_file);
		$maxRow = $data->sheets[0]["numRows"];					// 엑셀 최대 ROW값
		$query = "insert into coupon_pin (coupon_seq, pinNo) values ";
		$ins = array();												// values 에 붙여나갈 문자열.
		
		for ($i = 0; $i <= $maxRow; $i++)	// sheets0 의 row 갯수만큼 반복 
		{
			
			$pinNo = $data->sheets[0]["cells"][$i][1];			// sheets0 의 [$i,1] 셀
			if (trim($pinNo)=="") continue;
			//echo "핀넘버 : ".$pinNo;
			
			$ins[] = "('{$maxseq }', '{$pinNo}')";
			if ( $i % 1000 == 0 ) 					// 0, 1000, 2000 번째에 실행된다.
			{
				$stSQL = $query . implode(",", $ins); 	// insert into coupon_pin (coupon_seq, pinNo) values '{$maxseq }', '{$pinNo}'
				
				//echo "<br> stSQL!! : ".$stSQL;
				$rtn = mysql_query($stSQL);			
				if ( !$rtn ) {				
					$success = false;
					//echo "<br> pin등록  쿼리1 : ".$stSQL;
					break;
				}
				$ins = array();
			}
		}
		if ( count($ins) > 0 )
		{
				$stSQL = $query . implode(",", $ins); 
				$rtn = mysql_query($stSQL);
				if ( !$rtn ) {
					$success = false;
					//echo "<br> pin등록  쿼리2 : ".$stSQL;
					break;
				}
		}
		
			
		/*********** END : 엑셀파일 읽어들이기 + 디비 등록하기 ***********/
	
		// *****************************************
		// **** 아랫부분 절대 수정 하면 안됨...  ***
		// *****************************************
			break;
			 
	}// while E.	
	
	
	
	/*********** 트랜잭션 성공여부에 따른 처리 S. ***********/
	//echo "<br> 성공여부? : [ ".$success." ]";
    if(!$success) {
    	$result = mysql_query("ROLLBACK", $conn);
        //echo ("롤백되었습니다.");
        echo "<script>alert('등록 실패.. 다시 시도 해 주세요')</script>";
        echo "<script>location.href='coupons_inquiry.html'; </script>";
    } else {
        $result = mysql_query("COMMIT", $conn);
        //echo ("입력되었습니다.");    	
        echo "<script>alert('등록 되었습니다.')</script>";
        echo "<script>location.href='coupons_inquiry.html'; </script>";
    }
	/*********** 트랜잭션 성공여부에 따른 처리 E. ***********/	

    
	
/*********** 디비 끊기 ***********/
	mysql_close($conn);
/*********** 디비 끊기 ***********/
	
	
	echo "<script>location.href='coupons_inquiry.html'; </script>";
	
?>




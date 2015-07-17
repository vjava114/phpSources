<meta http-equiv="Content-Type" content="text/html; charset= utf-8" />
<?php
///////////////////////////////////////////// 기본셋팅 시작 ///////////////////////////////////////////////
include_once "../_inc/db.php";
include "../_inc/global.php";

require_once "../_lib/reader.php";


$send_mode = $_POST['mode'];
$phoneNo = $_POST['phoneNo'];
$msg = $_POST['msg'];
$eventName = $_POST['event'];
$coupon_seq = $_POST['coupon'];
if(!$coupon_seq or $coupon_seq.length == 0){
	$coupon_seq = 'null';	// DB 등록시, null 로 들어가게끔 하기 위해 문자열로 처리...
	//echo "null.. coupon_seq : ".$coupon_seq;
}


$upfile = $_FILES['upfile']['name'];			// 엑셀
$imgExists = false;
if($_POST['img'] == '1')
{
	$imgExists = true;
	$upfile2 = $_FILES['upfile2']['name'];		// 이미지	
} else {
	unset($_FILES['upfile2']);					//  unset  날려버림...
}


$senderNo = $_POST['senderNo'];
if(!$senderNo){
	$senderNo = 00000000000;					// 발송자번호는 DB가 int형 이어서, null을 허용하지 않음..
}
$byte = $_POST['byte'];
$senderType = "";
if ($_POST['img'] > 0 || $coupon_seq != 'null' )// 이미지가 존재하거나, 쿠폰이 존재한다면
{
	$senderType = "mms";
}
else if($byte > 80){
	$senderType = "lms";
}
else
{
	$senderType = "sms";	
}


/*
echo "<br>================== post로 받은 값 출력==========================";
echo "<br> send_mode (수신자번호를 어떤방식으로 선택했는가?) : ".$send_mode;
echo "<br> phoneNo (수신자 번호 배열) 						: ".$phoneNo;
echo "<br> msg (메시지) 									: ".$msg;
echo "<br> event (이벤트 이름) 								: ".$eventName;
echo "<br> coupon_seq (쿠폰 seq)							: ".$coupon_seq;
echo "<br> img (이미지 사용여부)							: ".$_POST['img'];

echo "<br> upfile (발송 excel파일) 						: ".$upfile;
echo "<br> upfile2 (이미지 파일) 							: ".$upfile2;
echo "<br> senderNo (발신자 번호) 							: ".$senderNo;
echo "<br> byte (발신자 번호) 							: ".$byte;
echo "<br> senderType (타입)								: ".$senderType;
echo "<br>================================================================<br>";
*/

///////////////////////////////////////////// 기본셋팅 끝 ///////////////////////////////////////////////











$returnList = null;		// 발송 LIST 선언... 수신자 전화번호들이 들어간다.
$picSave = false;		// 사진 저장 여부 변수 선언...

/* 어떤방식으로 수신자 목록을 받아왔는지에 따라 '$returnList (발송 list)' 만드는 과정이 다름.  S. */
if($send_mode == "send_direct")
{
	$returnList = array_unique(send_direct());
}
else if($send_mode == "send_db")
{
	//$returnList = send_db();		// send_db 와 send_direct 의 기능이 동일하여 아래처럼 사용..
	$returnList = array_unique(send_direct());
}
else if($send_mode == "send_file")
{
	$returnList = array_unique(send_file());
}else{
	//echo"<br>발송리스트를 받지 못했습니다. 다시 시도해주세요";
}
/* 어떤 방식으로 수신자 목록을 받아왔는지에 따라 '$returnList (발송 list)' 만드는 과정이 다름. E. */



/*
 *  1. 사용자가 수신자선택을 어떤방식으로 입력하였는가에 따른 발송list 만드는 과정... 1-1, 1-2, 1-3 셋중 한가지만 수행하게 됨.
 * 		1-1. '직접입력' 으로 입력 하였을 경우.
 *  	1-2. '팝업 검색창에서 DB 조회' 하여 입력 하였을 경우.
 *  	1-3. '엑셀파일에서 읽어들여서' 입력 하였을 경우.
 *
 *  2. 이미지 파일이 존재 할 경우 처리
 *
 * 	3. DB 처리 부분.
 * 		3-0. 트랜잭션 시작
 * 		3-1. event_list 테이블에 이벤트정보 등록
 * 		3-2. pic_list 테이블에 사진 정보 등록
 * 		3-3. 쿠폰정보 존재시, 쿠폰 사용 업데이트
 * 		3-4. ★★  발송처리 및, 발송 성공여부 리턴메세지 INSERT ★★
 * 		3-0. 트랜잭션 종료
 */






/*************************************************** [ 1. 발송리스트 ($rtn_list) 만들기. ] S. *************************************************/

// [ 1-1. 수신자 목록을 직접 입력 했을 경우] S. ************************/
function send_direct(){
	while($_POST['phoneNo']){
			
		$rtn_list = null;
			
		$phoneNo = $_POST['phoneNo'];
			
		$rtn_list = explode(',', $phoneNo);

		
			
		break;	// end of while;
	}

	return $rtn_list;
}



// [ 1-2. 수신자 목록을 검색해서 입력 했을 경우] S.
function send_db(){ }



// [ 1-3. 수신자 목록을 excel 파일로 갖고올 경우] S.
function send_file()
{

	while($_FILES['upfile']['name'])
	{
		//echo "<br>================== 수신자 목록 excel 파일 존재 ====================";
		$rtn_list = array();
		$todayDir = "../uploads/sendList/".date("Ymd")."/";	// 오늘자 저장할 경로
			
			
		if(!is_dir($todayDir))	// 오늘자가 폴더가 생성되어있지 않으면 생성.
		{
			mkdir($todayDir);
			//echo"<br> [발송대상 excel 파일] 오늘의 폴더 생성 : ".$todayDir;
		}

		
		$upload_file = $todayDir;	// 업로드 경로 설정
		$upload_file .= getSaveFileName();
		
		$temp_file = $_FILES['upfile']['tmp_name'];  								//임시 파일이름 생성
			
		if( move_uploaded_file($temp_file, $upload_file) == true )	// temp_file 이 http post 방식을 통해 넘어온 파일인지 확인하면, upload_file 경로로 이동.
		{
			//echo "<br> [발송대상 excel 파일] SUCCESS 경로".$upload_file."<br>";
		} else {
			//echo "<br> [발송대상 excel 파일] 파일 갖고오기 FIALED..<br>";
			break;
		}
			
		/************ excel to data 변환 S. ************/
		$data = new Spreadsheet_Excel_Reader();	 				// 한글깨지면 주석해제 $data->setOutputEncoding("UTF-8//IGNORE");
		$data->read($upload_file);
		$maxRow = $data->sheets[0]["numRows"];					// 엑셀 최대 ROW값
			
		for ($i = 0; $i <= $maxRow; $i++)	// sheets0 의 row 갯수만큼 반복
		{

			$a = $data->sheets[0]["cells"][$i][1];			// sheets0 의 [$i,1] 셀
			if (!trim($a)) continue;

			if ( substr($a,0,2) == "10" || substr($a,0,2) == "16" || substr($a,0,2) == "17" || substr($a,0,2) == "18" || substr($a,0,2) == "19") {
				$a = "0".$a;
			}
			
			$rtn_list[] = $a;			// 전역 변수로 상단에 선언 해 놓았음.
			//echo "<br> -> [발송대상 excel 파일] sendList 배열에 담고 있습니다. : ".$a;
			

		}
		//echo "<br>==============================================================<br>";
		/************ excel to data 변환 E. ************/
	   
			
			
			
			
		break;	// End Of While
	}			// End Of While

	return $rtn_list;

}

/********************************************** [ 발송리스트 ($rtn_list) 만들기. ] E. ********************************************/




for($i=0; $i<sizeof($returnList); $i++)
{
	//echo "<br>returnList(최종 발송리스트) : ".$returnList[$i];	// 발송 할 번호 체크~! 디버깅용 소스임. 주석처리 가능.
}








/********************************************** [ 2. 이미지파일 존재 할 경우] S. ********************************************/


while($imgExists && $_FILES['upfile2']['name'])
{
	//echo "<br>==================== 이미지파일 존재 ============================";
	$todayDir = "../uploads/sendImage/".date("Ymd")."/";	// 오늘자 저장할 경로

	if(!is_dir($todayDir))	// 오늘자가 폴더가 생성되어있지 않으면 생성.
	{
		mkdir($todayDir);
		//echo"<br> [이미지 파일] 오늘의 폴더 생성 : ".$todayDir;
	}
		
	$upload_file = $todayDir;	// 업로드 경로 설정
	//$upload_file= $upload_dir.basename($_FILES['upfile2']['name']);	// 업로드경로 + 파일이름 (아직 생성 안된상태)
	$upload_file .= getSaveFileName();
	//$upload_file = iconv("utf-8", "euc-kr", $upload_file);
	
	$temp_file = $_FILES['upfile2']['tmp_name'];
	//$temp_file = iconv("utf-8", "euc-kr", $temp_file);
			
	if( move_uploaded_file($temp_file, $upload_file) == true )	// temp_file 이 http post 방식을 통해 넘어온 파일인지 확인하면, upload_file 경로로 이동.
	{
		//echo "<br> [이미지 파일] SUCCESS 경로".$upload_file."<br>";
		$picSave = true;


	} else {
		//echo "<br> [이미지 파일] 갖고오기 FIALED..<br>";
		break;
	}
		
		
	
	//echo "<br> [이미지 파일] 경로 + 파일이름 : ".$upload_file;
	//echo "<br> [이미지 파일] 임시파일 이름 : ".$temp_file;
	//echo "<br>==============================================================<br>";
		
	break;
}
/************************************************** [ 이미지파일 존재 할 경우] E. ************************************************/





/*************************************  [ 3. 여기 아래 부터는 DB 처리하는 곳 입니다 ] S.  *****************************************/




/*********** [ 3-0. 트랜잭션 셋팅 S. ]************/
$success = true; 										// 트랜잭션 성공 여부
$result = mysql_query("SET AUTOCOMMIT=0", $conn);		// 트랙잭션을 시작한다.
$result = mysql_query("BEGIN", $conn);
/*********** [ 트랜잭션 셋팅 E. ] ************/

while(true){


	// 3-1. event_list 등록
	$eventQuery = "INSERT INTO event_list
							(
								event_name, coupon_seq, message, senderNo, reg_date
							)
						VALUES
							(
								'$eventName', $coupon_seq, '$msg', '$senderNo', now() 
							)	
						";
	//echo "<br>========================이벤트 등록 쿼리 확인 ========================<br>".$eventQuery;

	$rtn = mysql_query($eventQuery);

	if( !$rtn ){
		$success = false;
		//echo "db 처리 실패";
		break;
	}
	$evt_seq = mysql_insert_id();		// evt_list 테이블에 마지막으로 증가된 PK값 가져옴.
	//echo "<br> mysql_insert_id : ".$evt_seq;




	// 3-2. 사진 정보 등록
	//echo "<br> picSave : ".$picSave;
	if( $picSave == true )
	{
		$picValues = "(  '$upload_file' , '".$_FILES['upfile2']['name']."' , '".$evt_seq."' )";
		$picInsertQuery = "
				INSERT INTO pic_list
					( path, fileName, event_seq )
				VALUES
				$picValues
			";

				
		$rtn = mysql_query($picInsertQuery);
		
		//echo "<br>========================= 사진 저장 쿼리 확인 ========================= <br>".$picInsertQuery;
		
		if( !$rtn ){
			//echo "사진 저장 쿼리 실패!!!";
			$success = false;
			break;
		}else{
			
		}
	}

	// 3-3. 쿠폰 사용 업데이트
	if( $coupon_seq )
	{
		$stSQL = "SELECT count(*) as cnt
					FROM coupon_list
				   WHERE coupon_seq = '$coupon_seq'
				     AND use_yn = 'N' ";
		$rtn = mysql_fetch_array(mysql_query($stSQL));
		if ( $rtn[cnt] < 1) 
		{
			$stErrMsg = "이미 사용한 쿠폰 입니다.";
			$success = false;
			break;
		}

		$pinCntQuery = "SELECT count(*) as cnt 
						  FROM coupon_pin 
						 WHERE coupon_seq = '$coupon_seq'
						   AND use_yn = 'N' ";
		$pinCnt = mysql_fetch_array(mysql_query($pinCntQuery));

		echo "<br>핀 갯수".$pinCnt[cnt];
		echo "<br>대상자 갯수".count($returnList);
		if ( count($returnList) > intval($pinCnt[cnt]) )
		{
			$stErrMsg = "발송 대상자가 쿠폰갯수보다 많습니다.";
			$success = false;
			break;
		}
		
 
		$cUpdateQuery = "
				UPDATE coupon_list
				   SET use_yn = 'Y'
				 where coupon_seq = '$coupon_seq'
			";
			
		$rtn = mysql_query($cUpdateQuery);
		//echo "<br>===== 쿠폰사용 업데이트 쿼리1 =====<br>".$cUpdateQuery;
		if( !$rtn ){
			//echo "coupon_list UPDATE 쿼리 실패!!!";
			$success = false;
			break;
		}

		$rnd = MakeRandString(10);
		$stSQL = "UPDATE coupon_pin
					 SET use_key = '$rnd' 
					 	,use_yn = 'Y'
				   WHERE coupon_seq = $coupon_seq
				     AND use_yn = 'N' 
				   LIMIT ". count($returnList);
		$rtn = mysql_query($stSQL);
		//echo "<br>===== 쿠폰사용 업데이트 쿼리2 (동시에 같은 쿠폰의 같은 핀번호를 업데이트 될수도 있다. 선점하기 위해 use_key값 추가됨.) =====<br>".$stSQL;
		if( !$rtn ){
			//echo "coupon_pin UPDATE 쿼리 실패!!!";
			$success = false;
			break;
		}
		
		$stSQL = "SELECT pinNo
					FROM coupon_pin
				   WHERE coupon_seq = '$coupon_seq'
				     AND use_key = '$rnd' "; 
		$rtn = mysql_query($stSQL);
		//echo "<br>===== 쿠폰사용 업데이트 쿼리3 =====<br>".$stSQL;
		

		$pinList = array();
		while ($row = mysql_fetch_array($rtn))
		{
			$pinList[] = $row[pinNo];
			
		}
		
	}



	// 3-4. [중요] 발송 및, 발송결과 리턴메세지 포함 한 DB INSERT
	//echo "<br>=============================== 발송 시작 =============================<br>";
	$arVals = array();

	$i = 0;
	$couponidx = 0;
	foreach($returnList as $key => $val)
	{
		
		$smsg = $msg; 

		// 아래 if 문에서 (a, b, c, d),(a, b, c, d)  이런식으로 for문 돌면서 계속 만들어줌.
		if ( $coupon_seq )
		{


			$PinNo = $pinList[$couponidx];
			$smsg = str_replace("[coupon]", "[".$PinNo."]", $smsg);

			$arVals[] = "( '$val', '$evt_seq', '$senderType', '$smsg' )";
			$couponidx ++;
		}

		// 여기 if문에서 0,1000,2000 번째에 insert into 문을 붙여서 진짜 쿼리를 날림.
		if ($i % 1000 == 0)
		{
			


			$stSQL = "INSERT INTO send_list (phoneNo, event_seq, type, smsg) VALUES ";
			$stSQL .= implode(",", $arVals);
			//echo "<br>발송 >>> ".$stSQL;

			$rtn = mysql_query($stSQL);
			if( !$rtn ){
				//echo "send_list INSERT 실패!!!";
				$success = false;
				break;
			}

			$arVals = array();
			$i = 0;
		}
		$i ++;
	}

	// 잔여 쿼리를 날림.
	if (count($arVals) > 0 )
	{

		$stSQL = "INSERT INTO send_list (phoneNo, event_seq, type, smsg) VALUES ";
		$stSQL .= implode(",", $arVals);
		//echo "<br>발송 >>> ".$stSQL;

		$rtn = mysql_query($stSQL);
		if( !$rtn ){
			//echo "send_list INSERT 실패!!!";
			$success = false;
			break;
		}
	}
	

	//*************************************************
	break; // 삭 제 금 지  ****************************
	// ************************************************
}


/*********** [3-0] 트랜잭션 성공여부에 따른 처리 S. ***********/
//echo "<br><br> 트랜잭션 성공여부? : [ ".$success." ]";
if(!$success) {
	if ($stErrMsg )
	{
		AlertErr($stErrMsg);
	}
	$result = mysql_query("ROLLBACK", $conn);
	//echo ("<br>롤백되었습니다.");
	//echo "<script>javascript:alert('입력 실패. 다시 시도 해 주세요.')</script>";
	//echo "<script>javascript:history.back(-1)</script>";
} else {
	$result = mysql_query("COMMIT", $conn);
	AlertErr("발송 처리 완료 되었습니다.");
	MovePage("./confirm.html");
}
/*********** 트랜잭션 성공여부에 따른 처리 E. ***********/


function AlertErr($stErrmsg)
{
	$stErrMsg = addslashes($stErrmsg);
	echo "<html>
			<Script Langauge=JavaScript>
				alert(\"$stErrMsg\");
				parent.changeSubmit(false);
			</Script>
		</html> ";
}
function MovePage($url)
{
	echo "<html>
			<Script Langauge=JavaScript>
				parent.location.href='$url';
			</Script>
		</html> ";
}

?>






<?php
/*********** 디비 끊기 ***********/
mysql_close($conn);
/*********** 디비 끊기 ***********/
?>


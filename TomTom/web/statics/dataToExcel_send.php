<?php 


/*
 *  아래 헤더를 추가 함으로써, 엑셀파일로 바꿔지게 된다.
 *  ★★★ header가 최상단에 있어야 한다... include 보다도!! ★★★
 */
header( "Content-type: application/vnd.ms-excel;charset=UTF-8");
header( "Expires: 0" );
header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
header( "Pragma: public" );
header( "Content-Disposition: attachment; filename=name_".date('Ymd').".xls" );



include_once "../_inc/db.php";
include "../_inc/global.php";

$seq = $_GET['seq'];
$event_name = $_GET['event_name'];



//echo "<br> 테스트".$event_name;




/***************************** 검색조건 쿼리 생성 S. *****************************/ 
	$search = "";
	

/***************************** 검색조건 쿼리 생성 E. *****************************/




/***************************** 리스트 출력 쿼리 생성 S. *****************************/
	// 리스트 출력
	$query = "
			SELECT  *, s.seq as s_seq, e.seq as e_seq , e.event_name
			FROM    event_list as e
					JOIN send_list as s
					ON   e.seq= s.event_seq
			
			WHERE  e.seq = $seq
			
			ORDER BY s.seq
	";
	//echo "<br> === 리스트 출력 쿼리 : === <br>".$query;	
	$result = mysql_query($query);
/***************************** 리스트 출력 쿼리 생성 E. *****************************/


	//현재시간
	$curdate = $month.$day


?>
<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=UTF-8'>
<style>
<!--
.text{
  mso-number-format:"\@";/*force text*/
}
-->
</style>
<table border=1>
<tr>
	<Td colspan='6'><?= date("m") ?>월 <?= date("d") ?>일 <?= $event_name ?> 발송내역 상세조회</Td>
</tr>

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

					<tr>
						<th class="alt" scope="col">No</th>
						<th class="alt" scope="col">수신자번호</th>
						<th class="alt" scope="col">발송결과</th>
						<th class="alt" scope="col">발송시간</th>
					    <th class="alt" scope="col">수신결과</th>
					    <th class="alt" scope="col">수신시간</th>
					</tr>
				<?
					$cnt=1;
					while($row = mysql_fetch_array($result)){
				?>
					<tr>
						<!-- <td align="right"> $row[s_seq] </td> -->
						<td align="right"><?= $cnt++ ?></td>
						<td class="text" align="right"><?= $row[phoneNo] ?></td>
						<td align="right"><?=$row[rtn_msg] ?></td>
						<td align="right"><?=$row[reg_date] ?></td>
						<td align="right"><?=$row[receive_msg]?></td>
						<td align="right"><?=$row[receive_date]?></td>
						
						
					</tr>		
				<?
					}
				?>
</table>

<?
/*********** 디비 끊기 ***********/
	mysql_close($conn);
/*********** 디비 끊기 ***********/
?>
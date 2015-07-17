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
			AND
					check_yn = 1
			
					
			
			ORDER BY 
					sale_date ASC , sale_amt DESC
		";
					
	//echo "<br> === 리스트 출력 쿼리 : === <br>".$query;	
	$result = mysql_query($query);
/***************************** 리스트 출력 쿼리 생성 E. *****************************/




?>
<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=UTF-8'>
<table border=1>
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
				<?
					$cnt = 1;
					while($row = mysql_fetch_array($result)){
				?>
					<tr>
						<td align="right"><?= $cnt++ ?></td>
						<td align="right"><?=$row['mb_name'] ?></td>
						<td align="right"><?=$row['mb_age'] ?></td>
						<td align="center"><?=$row['mb_gender'] ?></td>
						<td align="right"><?=$row['mb_cellphone'] ?></td>
						<td align="left"><?=$row['mb_addrCiDo'] ?>&nbsp;<?=$row['mb_addrGuGun'] ?></td>
						<td align="center"><?=$row['sale_date'] ?></td>
						<td align="left"><?=$row['st_name'] ?></td>
						<td align="left"><?=$row['mn_korname'] ?></td>
						<td align="right"><?=$row['sale_amt'] ?></td>
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
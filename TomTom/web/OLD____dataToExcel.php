



			<!-- 이 파일은 딱히 필요는 없으나, 소스가 참조할게 많아서 삭제하지 않음 -->
			
			
			

<meta http-equiv="Content-Type" content="text/html; charset= utf-8" />
<?

	include_once "../_inc/db.php";
	include "../_inc/global.php";

	$noNeedHeader = true;	
	include_once("../Classes/PHPExcel.php");

	
	/***************************** request S. *****************************/ 
	$shop = $_POST['shop'];
	$min_month = $_POST['min_month'];
	$min_month2 = $_POST['min_month2'];
	$min_month2 .= '235959';				// between 검색시, (8월1 ~ 8월1 검색) 하면 8월1일자가 검색이 안되는것 해결 
	$age = $_POST['age'];
	$sex = $_POST['sex'];
	$area = $_POST['area'];
	$area_det = $_POST['area_det'];
	$sum = $_POST['sum'];
	$sum2 = $_POST['sum2'];
	$menu = $_POST['menu'];
	/***************************** request E. *****************************/
	
/*
echo "<br> 테스트".$menu;
echo "<br> 테스트".$min_month;
echo "<br> 테스트".$min_month2;
echo "<br> 테스트".$age;
echo "<br> 테스트".$sex;
echo "<br> 테스트".$area;
echo "<br> 테스트".$area_det;
*/
	
	/***************************** 검색조건 쿼리 생성 S. *****************************/ 
	$search = "";
	
	// 나이
	if( $age ){
		if( $age < 60 ){
			$search .= " AND age BETWEEN '$age' AND '$age'+9 ";
		}else{
			$search .= " AND age >= '$age' ";
		}
	}
	// 기간
	if( $min_month != "" ){
		$search .= " AND use_date BETWEEN '$min_month' AND '$min_month2' ";
	}
	// 매장
	if( $shop ){
		$search .= " AND store LIKE '%$shop%' ";
	}
	// 메뉴
	if( $menu ){
		$search .= " AND menu LIKE '%$menu%' ";
	}
	// 금액 ~이상 ~이하
	if( $sum ){
		$search .= " AND amount >= $sum";
	}
	if( $sum2 ){
		$search .= " AND amount <= $sum2";
	}
	/***************************** 검색조건 쿼리 생성 E. *****************************/
	
	
	
	
	/***************************** 리스트 출력 쿼리 생성 S. *****************************/
	// 리스트 출력
	$query = "
			SELECT mb_UID,
			       mb_name,
			       (date_format(now(), '%y')+2001) - mb_age as mb_age,
			       if(mb_gender='M','남','여') as mb_gender,
			       mb_cellphone,
			       mb_addrCiDo,
			       mb_addrGuGun,
			       m.CUST_NO AS '고객no',
			       s.sale_date,
			       s.ms_no AS '매장코드',
			       (SELECT st_name
			       FROM    TC_STORE
			       WHERE   tc_store.ms_no = s.ms_no
			       ) AS st_name,
			       g.goods_cd AS '상품코드',
			       (SELECT mn_korname
			       FROM    TC_MENU
			       WHERE   tc_menu.goods_cd = g.goods_cd
			       ) AS mn_korname,
			       s.sale_amt
			FROM   tc_member m
			       JOIN TH_BATCH_SALESINFO s
			       ON     m.CUST_NO = s.CUST_NO
			       LEFT JOIN TH_BATCH_GOODSINFO g
			       ON     m.CUST_NO = g.CUST_NO
			
			WHERE 1=1
					$search
			
			ORDER BY s.sale_date asc
	";
					
	echo "<br> === 리스트 출력 쿼리 : === <br>".$query;
	$result = mysql_query($query);			
	/***************************** 리스트 출력 쿼리 생성 E. *****************************/
			
	
	$objPHPExcel = new PHPExcel();	
	$objPHPExcel->setActiveSheetIndex(0)				
				->SetCellValue('A1', "No.");
	
	


	$objPHPExcel->setActiveSheetIndex(0)


	->SetCellValue('A1', "번호")
	->SetCellValue('B1', "이름")
	->SetCellValue('C1', "나이")
	->SetCellValue('D1', "성별")
	->SetCellValue('E1', "휴대폰 번호")
	->SetCellValue('F1', "주소")
	->SetCellValue('G1', "이용일자")
	->SetCellValue('H1', "이용매장")
	->SetCellValue('I1', "이용메뉴")
	->SetCellValue('J1', "이용금액");
	
	
	$i=2;
	while($row = mysql_fetch_array($result)){
		$objPHPExcel->setActiveSheetIndex(0)

		->SetCellValue("A$i", $row['mb_UID'] )
		->SetCellValue("B$i", $row['mb_name'] )
		->SetCellValue("C$i", $row['mb_age'] )
		->SetCellValue("D$i", $row['mb_gender'] )
		->SetCellValue("E$i", $row['mb_cellphone'] )
		->SetCellValue("F$i", $row['mb_addrCiDo']." ".$row['mb_addrGuGun'] )
		->SetCellValue("G$i", $row['sale_date'] )
		->SetCellValue("H$i", $row['st_name'] )
		->SetCellValue("I$i", $row['mn_korname'] )
		->SetCellValue("J$i", $row['sale_amt'] );
		$i++;
	}
	
	
	// 엑셀 컬럼 width 설정
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);	// 번호
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);	// 이름
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);	// 나이
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);	// 성별
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);	// 휴대폰
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);	// 주소
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);	// 이용일자
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);	// 이용매장
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);	// 이용메뉴
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);	// 이용금액
	

	$objPHPExcel->getActiveSheet()->setTitle('Ad on Air');
	$objPHPExcel->setActiveSheetIndex(0);
	
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="aoa_excel.xls"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	
	
/*********** 디비 끊기 ***********/
	mysql_close($conn);
/*********** 디비 끊기 ***********/
	
?>


<meta http-equiv="Content-Type" content="text/html; charset= utf-8" />
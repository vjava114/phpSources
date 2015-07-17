<?
function paging($page, $page_row, $page_scale){
	
	/*
	 * $page : 현재페이지
	 * $page_row : 한페이지에 몇개의 레코드?
	 * $page_scale : 페이지 블록
	 */

	
	global $total_record;	
	global $id;
	
	//총 페이지 수 구하기
	$total_page = ceil($total_record / $page_scale);

	//페이징의 시작 페이지 구하기
	$start_page = ((ceil($page / $page_scale) - 1) * $page_scale) + 1;

	//페이징의 마지막 페이지 구하기
	$end_page = $start_page + $page_scale - 1;
	if($end_page >= $total_page) $end_page = $total_page;

	//페이지를 표시할 변수 초기화
	$paging_str = "";

	//페이지가 1보다 작으면 1로 세팅
	if($page < 1) $page = 1;

	//1페이지 이상부터는 [처음] 버튼이 보이게
	if($page > 1){
		$paging_str .= "<a href=''/><img src='../images/page_first.gif' width='13' height='11' alt='첫 페이지 바로가기' /></a>";
	}

	//시작페이지번호가 1보다 크면 [이전]버튼 보이게(1페이지는 1-10, 2페이지는 11-20 == 2페이지부터 보인다)
	if($start_page > 1){
		$paging_str .="<a href='javascript:goPage($end_page-1)'><img src='../images/page_prev.gif' width='13' height='11' alt='이전 페이지로 이동' /></a>";
	}

	//총 페이지수가 1보다 크거나 같을경우 페이지번호가 출력되게
	if($total_page >= 1){
		for($i=$start_page;$i<=$end_page;$i++){
			if($page == $i){
				$paging_str .="<strong class='ll'>$i</strong>";
			} else {
				//$paging_str .="&nbsp;[<a href='".$PHP_SELF."?page=".$i."&id=".$id."'>$i</a>]&nbsp;";
				$paging_str .= "<a class='ll2' href='javascript:goPage($i)'> $i </a>";
			}
		}
	}


	//총 페이지가 마지막 페이지보다 작을경우 마지막페이지번호를 총 페이지번호로 넣기
	if($total_page < $end_page) $end_page = $total_page;


	
	
	//마지막페이지하고 총 페이지하고 다를경우 [다음]버튼이 출력
	if($end_page != $total_page){
		$paging_str .="<a href='javascript:goPage($end_page+1)'><img src='../images/page_next.gif' width='13' height='11' alt='다음 페이지로 이동' /></a>";
	}

	//현재페이지와 총 페이지가 같지않을경우 [끝] 버튼이 출력
	
	if($page != $total_page){
		$paging_str .="<a href='javascript:goPage($total_page)'><img src='../images/page_end.gif' width='13' height='11' alt='마지막 페이지 바로가기' /></a>";
	}
	return $paging_str;
}
	/*
	 	echo "<br> 현재 page : ".$page;
		echo "<br> end_page : ".$end_page;
		echo "<br> 토탈".$total_page; 
	 */
?>

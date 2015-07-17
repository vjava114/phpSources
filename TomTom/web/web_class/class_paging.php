<?
global $_INCLUDED_PAGING ;

$_INCLUDED_PAGING = true;

class paging{

	function pagelinkA($arr)
	{
		if (!$arr['totalrow'] ) $arr['totalrow'] = 1;
		if (!$arr['rowcount'] ) $arr['rowcount'] = 10;
		if (!$arr['blockcount'] ) $arr['blockcount'] = 10;
		extract($arr);
		
		//$this->pagelink($rowcount , $blockcount, $curpage, $totalrow , "", $startpage, $endpage, $beforepage, $afterpage);
		$arr['startpage'] = $startpage;	
		$arr['endpage'] = $endpage;	
		$arr['beforepage'] = $beforepage;	
		$arr['afterpage'] = $afterpage;	
		$arr['totalpage'] = intval($totalrow/$rowcount);
		//$arr['urlpar'] = "&curpage=$curpage";
		$arr['linknopage'] = str_replace("&cur_page=$curpage","",$arr['phpself']."?".$arr['urlpar']);
		$arr['linknopage'] = preg_replace("/&cur_page=$/","",$arr['linknopage']);
		$arr['pvar']['cur_page'] = $cur_page;
		$arr['offset'] = ($curpage-1)*$rowcount;
		$arr['page_navigator'] = $this->page_navigator($rowcount , $blockcount, $curpage, $totalrow , "", $startpage, $endpage, $beforepage, $afterpage, $searchParam);
		
		//echo $arr['offset']."<br>";
		return $arr;
		
	}
	
	function page_navigator($limit = 10 , $block = 10, $page = 1 , $t_cnt , $mode = "", &$lo_start, &$lo_end, &$prev_block, &$next_block, $searchParam)
    {
    	
    	$searchParam = urldecode($searchParam);
    	
        $sum_page = ceil($t_cnt/$limit); 

        if ($page==$sum_page) {
        	$pageing=(($sum_page-1)*$limit);
        	$limit= ($t_cnt - $pageing);
        } 
        
        $lo_value = ceil($page/$block);       	
        $lo_start = ($block*($lo_value-1))+1; 	
        $lo_end = ($block*$lo_value);         	
        $next_block = $lo_end+1;              	
        $prev_block = $lo_end-$block;         	
        $lo_block = $lo_value*$block;         	
        $bp = $page-1;  						
        $np = $page+1; 							
        $total_page=intval($t_cnt/$limit); 		

		if ( $lo_end >= $sum_page ) {
			$lo_end = $sum_page;
			$next_block = $lo_end +1;
		}

		if ( $next_block > $sum_page ) $next_block=0;
        if ( $page < $block || $page == $block) $prev_block=0;
        if ($lo_end == 0 ) $lo_end = 1;
        
        $str = "<ul class='paging'>";
        $str .= "<li><a href='".$PHP_SELF."?page=1".$searchParam."' class='icon1 prev_end'>최초 페이지</a></li>";
        
        if ($page < $block || $page == $block) {
	        $str .= "<li><a href='#' class='icon1 prev'>prev</a></li>";
        } else {
        	$str .= "<li><a href='".$PHP_SELF."?page=".$prev_block.$searchParam."' class='icon1 prev'>prev</a></li>";
        }
        
        //$str .= " | $sum_page | $lo_end | ";
        
    	for ($i=$lo_start; $i<$lo_end+1; $i++)
    	{
    		if ($page==$i){
    			$str .= "<li><strong>$i</strong></li>";
    		}else{
    			$str .= "<li><a href='".$PHP_SELF."?page=".$i.$searchParam."'>$i</a></li>";
    		}
    	}

        if ($next_block < $sum_page) {
	        $str .= "<li><a href='#' class='icon1 next_end'>next</a></li>";
        } else {
        	$str .= "<li><a href='".$PHP_SELF."?page=".$next_block.$searchParam."' class='icon1 next_end'>next</a></li>";
        }
        
        $str .= "<li><a href='".$PHP_SELF."?page=".$sum_page.$searchParam."' class='icon1 next'>마지막 페이지</a></li>";
        $str .= "</ul>";
        
        return $str;
	}
}
?>

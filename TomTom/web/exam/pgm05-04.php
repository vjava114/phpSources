<?
	function is_leep_year($y){
		if (($y %4 ==0 && $y %100!=0) || $y %400==0) return 1;
		else return 0;
}

	$year  = $_POST["year"];
	$result = is_leep_year($year);

	if($result) echo "[$year]년은 윤년입니다.<br>";
	else echo "[$year]년은 윤년이 아닙니다 .<br>";
?>

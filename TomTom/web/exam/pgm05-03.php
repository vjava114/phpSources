<html>
	<head><title>프로그램 5-3</title></head>
	<body>
<?
    outer_function(1,2);

	
	inner_function(5);

	function outer_function($x,$y){//ourter_function 이함수는 두개의 매개변수를 외부로부터 받아들임

		function inner_function($z){
			echo "\$z=$z<br>";
		}

		$v = $x + $y;
		inner_function($v);
	}
?>

</body>
</html>

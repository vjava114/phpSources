<html>
	<head><title>���α׷� 5-3</title></head>
	<body>
<?
    outer_function(1,2);

	
	inner_function(5);

	function outer_function($x,$y){//ourter_function ���Լ��� �ΰ��� �Ű������� �ܺηκ��� �޾Ƶ���

		function inner_function($z){
			echo "\$z=$z<br>";
		}

		$v = $x + $y;
		inner_function($v);
	}
?>

</body>
</html>

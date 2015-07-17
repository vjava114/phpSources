<?
	function print_msg($msg,$v)
	{
		echo "$msg :[<b>$v</b>].<br>";
	}

	$promt = $_POST["promt"];
	$value = $_POST["value"];

	print_msg($promt,$value);
?>
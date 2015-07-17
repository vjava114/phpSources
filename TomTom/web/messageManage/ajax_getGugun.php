
<?php
	include_once "../_inc/db.php";
	//include "../_inc/global.php";
	
	
	$sido = $_GET[sido];
	
	
	$query = "
		select gugun from tm_zipcode
		where sido = '". $sido. "'
		group by gugun 
	";
	//echo $query;
	
	$result = mysql_query($query) or die ( mysql_error() );


    while($row =  mysql_fetch_array($result))
    {
        $buff[] = $row['gugun'];
    }

    if (count($buff) > 0 )
    {
        foreach ($buff as $key => $val)
        {
            echo "<option>" . $val . "</option>";
        }
    }
	

?>


<?
/*********** 디비 끊기 ***********/
	mysql_close($conn);
/*********** 디비 끊기 ***********/
?>
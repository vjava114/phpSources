<?php

	function toPkcs7_aoa ($value)
	{
		if ( is_null ($value) )
			$value = "" ;
			$padSize = 16 - (strlen ($value) % 16) ;
		return $value . str_repeat (chr ($padSize), $padSize) ;
	}

	function fromPkcs7_aoa ($value)
	{
	$valueLen = strlen ($value) ;
		if ( $valueLen % 16 > 0 )
			$value = "";
			$padSize = ord ($value{$valueLen - 1}) ;
				if ( ($padSize < 1) or ($padSize > 16) ) 
					$value = "";
					// Check padding.
					for ($i = 0; $i < $padSize; $i++)
					{
						if ( ord ($value{$valueLen - $i - 1}) != $padSize )
							$value = "";
					}
		return substr ($value, 0, $valueLen - $padSize) ; 
	}
	
	function encrypt_aoa ($value,$key,$iv)
	{
		if(is_null($value))
		$value = "" ;
		$value = toPkcs7_aoa ($value) ;
		$output = mcrypt_encrypt (MCRYPT_RIJNDAEL_128, $key,$value, MCRYPT_MODE_CBC, $iv) ;
		return urlencode(base64_encode($output));
	}

	function decrypt_aoa ($value,$key,$iv) 
	{
		if(is_null($value))
		$value = "" ;
		//$value = urldecode(base64_decode($value));
		$value = base64_decode($value);
		$output = mcrypt_decrypt (MCRYPT_RIJNDAEL_128, $key,$value, MCRYPT_MODE_CBC, $iv) ;
		return fromPkcs7_aoa ($output) ;
	} 
						
?>
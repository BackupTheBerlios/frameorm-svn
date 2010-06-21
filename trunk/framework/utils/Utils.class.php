<?php 
class Utils
{
	public static function genRandom($length=10)
	{
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$return   = '';
 
		if ($length > 0) {
			$totalChars = strlen($characters) - 1;
			for ($i = 0; $i <= $length; ++$i) {
				$return .= $characters[rand(0, $totalChars)];
			}
		}
  		return $return;
	}
}
?>
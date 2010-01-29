<?php 
class ACLAccess
{
	public static $users = array(
		'thomas' => 'thomas',
		'lefteris' => 'le7ter15'
	);
	
	public static function BasicAuthenticationCheck()
	{
		//return true;
		$oContext = Context::getInstance();
		$auth = substr($oContext->request->get->HTTP_AUTHORIZATION, 6);
		list($authUser, $authPass) = explode(':', base64_decode($auth));
		if (!in_array($authUser, array_keys(self::$users)))
			return false;
		if($authPass != self::$users[$authUser])
			return false;
		return true;
	}
}
?>
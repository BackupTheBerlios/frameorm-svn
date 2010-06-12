<?php
class Session
{
	CONST IS_IP = '/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/';
	private static $instance;
	public $sessionID;

	private function __construct()
	{
		session_start();
		$sess_name = session_name();
		if(!isset($_COOKIE[$sess_name]) && 
					preg_match(Session::IS_IP, $_SERVER['SERVER_NAME']) !== FALSE){
			$this->set_cookie($sess_name, session_id());
		}
		$this->sessionID = session_id();
		if(!isset($this->userID))
			$this->userID = User::GUEST;
	}
	
	public function set_cookie($name, $value, $expire=0)
	{
		$host = explode('.', $_SERVER['SERVER_NAME']);
		$len = count($host);
		$host = '.'.$host[$len-2].'.'.$host[$len-1];
		setcookie($name, $value, $expire, '/', $host, FALSE, TRUE);
	}
	
	public function regenerate()
	{
		session_regenerate_id(true);
		$this->sessionID = session_id();
		$this->set_cookie(session_name(), session_id());
	}
	
	public function destroy()
	{
		if(count($_COOKIE) > 0){
			foreach($_COOKIE as $key=>$value){
				if($key == 'frmauth'){
					$val = explode('_', $_COOKIE['frmauth']);
					$token = UserToken::getUserToken($val[0], $val[1], $val[2]);
					if($token)
						$token->delete();
				}
				setcookie($key, false, time()-10000, '/', '.'.$_SERVER['SERVER_NAME']);
				setcookie($key, false, time()-10000, '/', $_SERVER['SERVER_NAME']);
			}
		}
		return session_destroy();
	}
	
	public function __get($key)
	{
		if(array_key_exists($key, $_SESSION))
			return $_SESSION[$key];
	}
	
	public function __isset($key)
	{
		return array_key_exists($key, $_SESSION);
	}
	
	public function __set($key, $value)
	{
		$_SESSION[$key] = $value;
	}
	
	public static function getInstance()
	{
		if (!self::$instance)
            self::$instance = new Session();
        return self::$instance; 
	}
}
?>
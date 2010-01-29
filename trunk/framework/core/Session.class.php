<?php
class Session
{
	private static $instance;
	public $sessionID;

	private function __construct()
	{
		session_start();
		$this->sessionID = session_id();
		if(!isset($this->userID))
			$this->userID = User::GUEST;
		if(!isset($this->lang))
			$this->lang = 'el_GR';
	}
	
	private function createGuestSession()
	{
		$this->userID = User::getUserByUsername('guest')->id;
	}
	
	public function destroy()
	{
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
<?php
class Context
{
	private static $instance;
	public $request;
	public $response;
	public $session;
	public $user;

	private function __construct()
	{
		include('settings.php');
		date_default_timezone_set($settings->timezone);
		$this->request = Request::getInstance();
		$this->session = Session::getInstance();
		$this->response = Response::getInstance();
		$this->user = User::getUserById($this->session->userID);
	}
	
	public static function getInstance()
	{
		if (!self::$instance)
            self::$instance = new Context();
        return self::$instance; 
	}
}
?>
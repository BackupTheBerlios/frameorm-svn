<?php
class Request
{
	private static $instance;
	public $files;
	public $get;
	public $post;

	private function __construct()
	{
		$this->files = new Globals($_FILES);
		$this->post = new Globals($_POST);
		$this->get = new Get($_GET);
	}
	
	public static function getInstance()
	{
		if (!self::$instance)
            self::$instance = new Request();
        return self::$instance; 
	}
}

class Get extends Globals
{
	public $queryString;
	
	public function __construct(&$var)
	{
		parent::__construct($var);
		$this->queryString = $_SERVER['QUERY_STRING'];
	}
	
	public function getVars()
	{
		$val = $this->var;
		if(array_key_exists('HTTP_AUTHORIZATION', $val))
			unset($val['HTTP_AUTHORIZATION']);
		unset($val['action']);
		return array_values($val);
	}
}

class Globals
{
	public $var;

	public function __construct(&$var)
	{
		$this->var = &$var;
	}
	
	public function __isset($key)
	{
		return array_key_exists($key, $this->var);
	}
	
	public function __unset($key)
	{
		unset($this->var[$key]);
	}
	
	public function __get($key)
	{
		if(array_key_exists($key, $this->var))
			return $this->var[$key];
		return null;
	}
	
	public function __set($key, $value)
	{
		$this->var[$key] = $value;
	}
}
?>
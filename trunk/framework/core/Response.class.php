<?php 
class Response
{
	private static $instance;
	public $contentType = 'text/html';
	public $charset = 'UTF-8';
	public $body;
	public $httpCode = 200;
	private $headers = array();
	
	private function __construct(){}
	
	public function write()
	{
		ob_start('ob_gzhandler');
		header("Pragma:");
		header("Cache-Control:");
		header("Expires:");
		header('HTTP/1.1 '. $this->httpCode);
		$contentType = sprintf("%s; charset=%s", $this->contentType, $this->charset);
		header('Content-Type: '.$contentType, true);
		foreach($this->headers as $key=>$val)
			header("$key: $val");
		echo $this->body;
		exit();
	}
	
	public function redirect($url)
	{
		header("Location: $url");
		exit();
	}
	
	public function addHeader($name, $value)
	{
		$this->headers[$name] = $value;
	}
	
	public static function getInstance()
	{
		if (!self::$instance)
            self::$instance = new Response();
        return self::$instance; 
	}
}
?>
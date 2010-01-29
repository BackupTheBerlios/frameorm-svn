<?php
class Logger
{
	private static $instance;
	private $file;
	const PATH = "error_log.txt";
	
	private function __construct()
	{
		$log_file = $_SERVER['DOCUMENT_ROOT']."/".self::PATH;
		$this->file = fopen($log_file, 'a');
	}
	
	public static function log($message, $type='ERROR')
	{
		if (!self::$instance)
            self::$instance = new Logger();
        $self = self::$instance;
        $date = date('l, d F Y H:i TP');
        $str = sprintf("[%s] [%s] %s\n", $type, $date, $message);
        fwrite($self->file, $str);
	}
	
	function __destruct()
	{
		fclose($this->file);
		self::$instance = null;
	}
}
?>
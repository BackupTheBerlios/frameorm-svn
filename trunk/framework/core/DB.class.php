<?php
class DB
{
	private $link;
	private static $instance;
	private static $hasActiveTrans = false;

	private function __construct()
	{
		$this->connect();
	}
	
	public static function getInstance()
	{
		if (!self::$instance)
            self::$instance = new DB();
        return self::$instance; 
	}

	private function connect()
	{
		include('settings.php');
		$this->link = mysql_connect($settings->db_host,  $settings->db_username, 
									$settings->db_passwd) or die ("Unable to connect!");
		mysql_select_db($settings->db_database) or die ("Unable to select database!");
		mysql_query("SET names 'utf8'", $this->link);
		mysql_query("SET time_zone = '{$settings->timezone}'", $this->link);
	}
	
	public function db_metadata($table)
	{
		$result = $this->db_query("SELECT * FROM $table limit 1");
		$fields = mysql_num_fields($result);
		$store = array();
		for ($i=0; $i < $fields; $i++) 
		{
		    $type  = mysql_field_type($result, $i);
		    $name  = mysql_field_name($result, $i);
		    $store[$name] = $type;
		}
		return $store;
	}
	
	public function now()
	{
		$q = "SELECT NOW() as n";
		return $this->db_fetch_one($q);
	}
	
	public function db_fetch_array($rs)
	{
		return @mysql_fetch_assoc($rs);
	}
	
	public function db_query($q)
	{
		return mysql_query($q, $this->link);
	}
	
	public function db_seek($rs, $index)
	{
		mysql_data_seek($rs, $index);
	}

	public function db_insert_id()
	{
		return mysql_insert_id($this->link);
	}

	public function db_fetch_one($q)
	{
		$r = $this->db_query($q);
		return mysql_result($r,0);
	}

	public function db_fetch_one_assoc($q)
	{
		$r = $this->db_query($q);
		return mysql_fetch_assoc($r);
	}

	public function db_escape_string($str)
	{
		return mysql_real_escape_string($str);
	}

	public function db_num_rows($rs)
	{
		return @mysql_num_rows($rs);
	}
	
	public function start_transaction()
	{
		if(!self::$hasActiveTrans)
		{
			$this->db_query("START TRANSACTION");
			self::$hasActiveTrans = true;
		}
	}
	
	public function db_errno()
	{
		return mysql_errno($this->link);
	}
	
	public function db_error()
	{
		return mysql_error($this->link);
	}
	
	public function commit()
	{
		$this->db_query("COMMIT");
		self::$hasActiveTrans = false;
	}
	
	public function rollback()
	{
		self::$hasActiveTrans = false;
		$this->db_query("ABORT");
	}
	
	public function __destruct()
	{
		self::$hasActiveTrans = false;
		self::$instance = null;
		mysql_close($this->link);
	}
}
?>
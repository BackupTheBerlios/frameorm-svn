<?php
class Identifiable extends Table
{
	public $id;
	public $name;
	
	public function __construct(array $data=array())
	{		
		parent::__construct($data);
	}
	
	public static function searchName($table, $value, $class)
	{
		$q = sprintf("SELECT * FROM %s 
					  WHERE name LIKE '%s%%'
					  ORDER BY name", $table, $value);
		$db = DB::getInstance();
		$rs = $db->db_query($q);
		if($db->db_num_rows($rs) == 0)
			return array();
		return new ObjectSet($rs, $class);
	}
	
	public function on_create()
	{
		$db = DB::getInstance();
		$this->id = $db->db_insert_id();
	}
	
	protected static function getItemById($id, $table)
	{
		$db = DB::getInstance();
		$rs = $db->db_query("SELECT * FROM $table WHERE id=$id");
		if($db->db_num_rows($rs) == 0)
			throw new EntryNotFound("There is no record with id $id in table $table");
		return $db->db_fetch_array($rs);
	}
	
	protected static function getAllItemsByTable($table, $orderby='name')
	{
		$db = DB::getInstance();
		$rs = $db->db_query("SELECT * FROM $table ORDER BY $orderby");
		if($db->db_num_rows($rs) == 0)
			return null;
		return $rs;
	}
	
	protected function elementExists($column, $value, $table, $exclude)
	{
		$db = DB::getInstance();
		$rs = $db->db_query("SELECT $column 
							 FROM $table
							 WHERE $column='$value'
							 $exclude");
		if($db->db_num_rows($rs) == 0)
			return false;
		return true;
	}
}
?>
<?php
class Identifiable extends Table
{
	public $id;
	public $name;
	
	public function __construct(array $data=array())
	{		
		parent::__construct($data);
	}
	
	public static function getItemByName($table, $value, $class)
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
		parent::on_create();
	}
	
	public static function getItemById($id, Table $instance)
	{
		$query = sprintf("SELECT * FROM %s WHERE id=%s", 
						 $instance->table['table'], $id);
		return Table::query($query, $instance);
	}
	
	protected static function getAllItemsByTable(Table $instance, $orderby='name' )
	{
		$q = sprintf("SELECT * FROM %s ORDER BY %s", 
					 $instance->table['table'], $orderby);
		return Table::query($q, $instance, $is_multi=true);
	}
	
	protected function elementExists($column, $value, $table, $exclude)
	{
		$db = DB::getInstance();
		$rs = $db->db_query("SELECT $column 
							 FROM $table
							 WHERE $column='$value'
							 $exclude");
		return $db->db_num_rows($rs) > 0;
	}
}
?>
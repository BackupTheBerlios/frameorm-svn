<?php
function getPublicObjectVars($obj) 
{
  return get_object_vars($obj);
}

/*
BASE CLASS FOR ALL DB TABLES
*/
class Table implements EventHandler
{	
	/**
	 * Initialize object
	 * Accept an assoc array (table row or posted data), representing
	 * one row of the object's subclass table.
	 * Loop through the class variables and assign
	 * the values of the array
	 *
	 * @param array $data assoc array
	 * @return void
	 */
	public function __construct(array $data) 
	{
		$this->__updateMembers($data);
	}
	
	private function __updateMembers(array $data)
	{
		$members = getPublicObjectVars($this);
		foreach ($members as $k=>$v)
			if (array_key_exists($k, $data))
				$this->$k = $data[$k];
	}
	
	public function updateMembers(array $data)
	{
		$this->__updateMembers($data);
	}
	
	public function on_create(){}
	public function on_update(){}
	public function on_delete(){}
	public function on_after_delete(){}
	
	public function __set($name, $val)
	{
		$attrs = $this->getAttrs();
		if (!in_array($name, $attrs))
		{
			$msg = sprintf("Class %s has no member %s", get_class($this), $name);
			throw new AttributeNotFound($msg);
		}
		$this->$name = $val;
	}
	
	public function getAttrs()
	{
		$data = getPublicObjectVars($this);
		unset($data['table']);
		return $data;
	}
	
	/**
	 * Delete object
	 *
	 * @return bool
	 */
	public function delete()
	{
		$this->on_delete();
		$db = DB::getInstance();
		$q = "DELETE FROM {$this->table['table']} WHERE ". $this->getPkValues();
		if(!$db->db_query($q))
			throw new AppException("Error deleting object. Query [$q] {$db->db_error()}", true);
		$this->on_after_delete();
	}
	
	/**
	 * Save or update object
	 * @param bool $forceSave
	 * @return bool
	 */
	public function save($forceSave = false)
	{
		$key = $this->getFirstPk();
		if ($this->$key == '' || $forceSave)
			$this->doSave();
		else
			$this->update();
	}
	
	/**
	 * Save object to DB
	 * Walk through the object's attrs
	 * and escape them
	 *
	 * @return bool
	 */
	protected function doSave()
	{
		$this->setPkValues();
		$data = $this->getAttrs();
		try{
			array_walk($data,array($this, 'escape'));
		}
		catch(InvalidValue $e){
			throw new AppException($e->getMessage());
		}
		$q = "INSERT INTO {$this->table['table']} (";
		$q .= join(",", array_keys($data)).") VALUES(";
		$q .= join(",", array_values($data)).")";
		
		$db = DB::getInstance();
		if (!$db->db_query($q)){
			if($db->db_errno() == 1062)
				throw new DuplicateEntry($db->db_error());
			else
				throw new AppException("Error saving new object. Query [$q] {$db->db_error()}, true");
		}
		$this->on_create();
	}
	
	/**
	 * Iterate through the table's
	 * PK(s) and assign to each one
	 * the corresponding object value,
	 * imploding them finally to be used
	 * as a WHERE SQL clause
	 *
	 * @return string
	 */
	private function getPkValues()
	{
		$ret = array();
		foreach ($this->table['PK'] as $key=>$val)
		{
			if (DataMapping::getDataType($this->table['table'], $key) == 'string')
				$ret[] = "$key = '".$this->$key."'";
			else 
				$ret[] = "$key = ".$this->$key;
		}
		return implode(" AND ", $ret);
	}
	
	/**
	 * Return the first PK
	 *
	 * @return string
	 */
	private function getFirstPk()
	{
		$keys = array_keys($this->table['PK']);
		return $keys[0];
	}
	
	/**
	 * Set the values of the defined PK(s)
	 * If the pk's value is defined as null
	 * in the object's definition, then
	 * the corresponding value of its
	 * class member is assigned to it
	 *
	 * @return void
	 */
	private function setPkValues()
	{
		foreach ($this->table['PK'] as $key=>$val)
		{
			if (is_null($val))
				$this->$key = $this->$key;
			else 
				$this->$key = $val;
		}
	}
	
	/**
	 * Update object to DB
	 * Walk through the object's attrs
	 * and escape them
	 *
	 * @return bool
	 */
	protected function update()
	{
		$pk = $this->getPkValues();
		$data = $this->getAttrs();
		foreach ($this->table['PK'] as $key=>$val)
			unset($data[$key]);
		try{
			array_walk($data, array($this, 'escape'));
		}
		catch(InvalidValue $e){
			throw new AppException($e->getMessage());
		}
		$k = array_keys($data);
		$v = array_values($data);
		$c = join(",", array_map(array($this, 'joinAll'), $k, $v));
		$q = "UPDATE {$this->table['table']} SET $c WHERE $pk";
		$db = DB::getInstance();
		if (!$db->db_query($q)){
			if($db->db_errno() == 1062)
				throw new DuplicateEntry($db->db_error());
			else
				throw new AppException("Error updating object. Query [$q] {$db->db_error()}", true);
		}
		$this->on_update();
	}
	
	/**
	 * Walk $this->attrs array
	 * and return a set of $k=$v
	 * to be used in an update stmt
	 *
	 * @param $k string array key
	 * @param $v string array value
	 * @return string
	 */
	private function joinAll($k, $v)
	{
		return "$k = $v";
	}
	
	/**
	 * Walk $this->attrs array
	 * and return a set of $k=$v
	 * to be used in an update stmt
	 *
	 * @param $k string array key
	 * @param $v string array value
	 * @return void
	 */
	private function escape(&$value ,$key)
	{
		$db = DB::getInstance();
		$type = DataMapping::getDataType($this->table['table'], $key);
		if ($type == 'string')
		{					
			if ($value == 'null' || $value=='')
				$value = 'null';
			else
				$value = "'".$db->db_escape_string($value)."'";
		}
		else 
			$value = ($value=='' || is_null($value))?'NULL':$value;
	}
	
}

class DataMapping
{
	private static $mapArray = array(
					'integer' => array('int',
									   'numeric',
									   'float'),
					'string' => array('string',
									  'text', 
									  'date',
									  'blob',
									  'datetime',
									  'character', 
									  'time', 
									  'timestamp')
	);
	
	private static $metadata_cache = array();
	
	private static function getDbType($table, $column)
	{
		$db = DB::getInstance();
		if(!array_key_exists($table, self::$metadata_cache))
			self::$metadata_cache[$table] = $db->db_metadata($table);
		return self::$metadata_cache[$table][$column];
	}
	
	public static function getDataType($table, $column)
	{
		$columnType = self::getDbType($table, $column);
		foreach (self::$mapArray as $key=>$val)
			if (in_array($columnType, $val))
				return $key;
		return null;
	}
}

?>
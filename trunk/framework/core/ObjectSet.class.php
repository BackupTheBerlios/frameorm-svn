<?php
class ObjectSet implements ArrayAccess, Iterator, Countable
{
	protected $currentIndex;
	protected $result;
	protected $totalRows = 0;
	protected $class_name;
	protected $oReflection;
	
	public function __construct($rs, $object)
	{
		$db = DB::getInstance();
		$this->currentIndex = 0;
        $this->result = $rs;
        $this->totalRows = $db->db_num_rows($rs);
		$this->class_name = $object;
		$this->oReflection = new ReflectionClass($this->class_name);
	}
	
	public function offsetExists($offset)
	{
		return ($offset < $this->totalRows);
	}
 
	public function offsetGet($offset)
	{
		$db = DB::getInstance();
		$db->db_seek($this->result, $offset);
		if($row = $db->db_fetch_array($this->result))
			return $this->oReflection->newInstance($row);
		throw new Exception("No row");
	}
 
	public function offsetSet($offset,$value)
	{
		throw new Exception("This collection is read only.");
	}
 
	public function offsetUnset($offset)
	{
		throw new Exception("This collection is read only.");
	}
 
	public function count()
	{
		return $this->totalRows;
	}
 
	public function current()
	{
		return $this->offsetGet($this->currentIndex);
	}
 
	public function key()
	{
		return $this->currentIndex;
	}
 
	public function next()
	{
		return $this->currentIndex++;
	}
 
	public function rewind()
	{
		$this->currentIndex = 0;
	}
 
	public function valid()
	{
		return $this->offsetExists($this->currentIndex);
	}
 
	public function append($value)
	{
		throw new Exception("This collection is read only");
	}
}

class SlicedObjectSet extends ObjectSet
{
	public $slice;
	public $range;
	
	function __construct($rs, $object, array $slice)
	{
		parent::__construct($rs, $object);
		$this->currentIndex = $slice[0];
		$this->slice = $slice;
		$this->range = range($slice[0], $slice[1]-1);
	}
	
	public function offsetExists($offset)
	{
		if ($offset > $this->totalRows-1 ||!in_array($offset, $this->range))
			return false;
		return true;
	}
	
	public function rewind()
	{
		$this->currentIndex = $this->slice[0];
	}	
}

class MixedObjectSet extends ObjectSet
{	
	function __construct($rs, $object)
	{
		parent::__construct($rs, $object);
	}
	
	function offsetGet($offset)
	{
		$this->db->db_seek($this->result, $offset);
		if($row = $this->db->db_fetch_array($this->result))
		{
			$this->oReflection = new ReflectionClass($row['class_name']);
			return $this->oReflection->newInstance($row);
		}
        throw new Exception("No row");
	}
}

?>
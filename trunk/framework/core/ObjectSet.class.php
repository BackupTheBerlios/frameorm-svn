<?php
class ObjectSet implements ArrayAccess, Iterator, Countable
{
        protected $currentIndex, $result, $db, $class_name, $oReflection;
        public $totalRows;
 
        function __construct($rs,$object)
        {
            $this->currentIndex = 0;
            $this->result = $rs;
            $this->class_name = $object;
            $this->db = DB::getInstance();
            $this->totalRows = $this->db->db_num_rows($this->result);
            $this->oReflection = new ReflectionClass($this->class_name);
        }
 
        //Region ArrayAccess
        function offsetExists($offset)
        {
        	if ($offset > $this->totalRows-1)
        		return false;
        	return true;
        }
 
        function offsetGet($offset)
        {
        	$this->db->db_seek($this->result, $offset);
            if($row = $this->db->db_fetch_array($this->result))
				return $this->oReflection->newInstance($row);
            throw new Exception("No row");
        }
 
        function offsetSet($offset,$value)
        {
            throw new Exception("This collection is read only.");
        }
 
        function offsetUnset($offset)
        {
            throw new Exception("This collection is read only.");
        }
 
        function count()
        {
            return $this->totalRows;
        }
 
        //Region Iterator
        function current()
        {
            return $this->offsetGet($this->currentIndex);
        }
 
        function key()
        {
            return $this->currentIndex;
        }
 
        function next()
        {
            return $this->currentIndex++;
        }
 
        function rewind()
        {
            $this->currentIndex = 0;
        }
 
        function valid()
        {
            return $this->offsetExists($this->currentIndex);
        }
 
        function append($value)
        {
            throw new Exception("This collection is read only");
        }

        //EndRegion
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
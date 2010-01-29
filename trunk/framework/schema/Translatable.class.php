<?php
class Translatable extends Describable
{
	public $langID;
	
	public function __construct(array $data=array())
	{		
		parent::__construct($data);
	}
	
	public function getLanguages()
	{
		throw new NotImplementedError('Derived classes must implement the method');
	}
	
	/*
	 * Create entries for all the available
	 * languages
	 */
	public function on_create()
	{
		parent::on_create();
		$langs = $this->getLanguages();
		foreach($langs as $lang)
		{
			if($lang->id != $this->langID)
			{
				if(!$this->getEntry($lang->id))
				{
					$oClass = new ReflectionClass(get_class($this));
					$attrs = $this->getAttrs();
					$attrs['langID'] = $lang->id;
					$attrs['name'] = 'PLEASE SET THE TITLE for '.$lang->name;
					
					$oInstance = $oClass->newInstanceArgs(array($attrs));
					$oInstance->save(true);
				}
			}
		}
	}
	
	/* 
	 * Delete all entries for the available
	 * languages with the same id
	 */
	public function on_after_delete()
	{
		parent::on_after_delete();
		$langs = $this->getLanguages();
		$table = $oSpider->table['table'];
		foreach($langs as $lang)
		{
			if($lang->id != $this->langID)
			{
				$row = $this->getEntry($lang->id);
				if(!is_null($row))
				{
					$oClass = new ReflectionClass(get_class($this));
					$oInstance = $oClass->newInstance($row);
					$oInstance->delete();
				}
			}
		}
	}
	
	private function getEntry($langID)
	{
		$db = DB::getInstance();
		$table = $this->table['table'];
		$q = sprintf("SELECT * FROM %s WHERE id=%d AND langID=%d",
					 $table, $this->id, $langID);
		$rs = $db->db_query($q);
		if($db->db_num_rows($rs) == 1)
			return $db->db_fetch_array($rs);
		return null;
	}
}
?>
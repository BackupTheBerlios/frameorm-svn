<?php
class Searchable extends Translatable
{
	public $spiderID;
	
	public function __construct(array $data=array())
	{		
		parent::__construct($data);
	}
	
	public function getSpiderUrl()
	{
		throw new NotImplementedError('Derived classes must implement the method');
	}
	
	public function getSpiderClassName()
	{
		throw new NotImplementedError('Derived classes must implement the method');
	}
	
	/*
	 * After the original object has
	 * been deleted, delete the indexed
	 * one as well
	 */
	public function on_after_delete()
	{
		parent::on_after_delete();
		$oRef = new ReflectionClass($this->getSpiderClassName());
		$oSpider = $oRef->newInstance(array());
		$table = $oSpider->table['table'];
		$rs = Identifiable::getItemById($this->spiderID, $table);
		$oSpider = $oRef->newInstance($rs);
		$oSpider->delete();
	}
	
	/*
	 * Update spider object
	 */
	public function on_update()
	{
		parent::on_update();
		$db = DB::getInstance();
		$oRef = new ReflectionClass($this->getSpiderClassName());
		$oSpider = $oRef->newInstance(array());
		$table = $oSpider->table['table'];
		$rs = Identifiable::getItemById($this->spiderID, $table);
		$oSpider = $oRef->newInstance($rs);
		$oSpider->description = strip_tags($this->description);
		$oSpider->name = $this->name;
		$oSpider->save();
	}
	
	/* 
	 * Add the object to the spider
	 * table, in order for it to
	 * be searchable
	 */
	public function on_create()
	{
		parent::on_create();
		$db = DB::getInstance();
		$data = $this->getAttrs();
		$desc = '';
		$desc .= strip_tags($this->description);
		$data['description'] = $desc;
		$data['url'] = $this->getSpiderUrl();
		unset($data['id']);
		$oRef = new ReflectionClass($this->getSpiderClassName());
		
		$oSpider = $oRef->newInstance($data);
		$oSpider->save();
		$this->spiderID = $oSpider->id;
		$this->save();
	}	
}
?>
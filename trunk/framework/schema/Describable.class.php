<?php
class Describable extends Identifiable 
{
	public $description;
	
	public function __construct(array $data=array())
	{		
		parent::__construct($data);
	}
	
}
?>
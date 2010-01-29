<?php
define('TEMPLATE_DIR', $_SERVER['DOCUMENT_ROOT']."/framework/templates/");
class Template
{
	private $tpl;
	private $attrs = array();
	private $load_from_cache;
	private $file;
	
	public function __construct($tpl, $load_from_cache = false)
	{
		$this->tpl = $tpl;
		$this->load_from_cache = $load_from_cache;
	    $this->file = sprintf("%s%s", TEMPLATE_DIR, $this->tpl);
	}
	
	public function __set($member, $value)
	{
		$this->attrs[$member] = $value;
	}
	
	public function parse() 
	{
	    if(!file_exists($this->file))
	    	throw new NotFound("Template $this->file does not exist");
	    if ($this->load_from_cache)
	    {
	    	$oCache = new Cache($this->tpl);
	    	if($oCache->isValid())
	    		return $oCache->load();
	    	else
	    	{
	    		$content = $this->readTemplate();
	    		$oCache->cache($content);
	    		return $content;
	    	}
	    }
	    return $this->readTemplate();  
	}
	
	private function readTemplate()
	{
		extract($this->attrs);
	    ob_start();
	    if (is_file($this->file)) 
	      include($this->file);
	    $parsed = ob_get_contents();
	    ob_end_clean();
	    return $parsed;
	}
}
?>
<?php
define(CACHE_DIR, $_SERVER['DOCUMENT_ROOT']."/.cache");

class Cache
{
	public $cache_file;
	
	public function __construct($tpl)
	{
		$f = preg_replace("/\//","_",$tpl);
		if(!is_dir(CACHE_DIR))
			mkdir(CACHE_DIR);
		$this->cache_file = sprintf("%s/%s.cache", CACHE_DIR, $f);
	}
	
	public static function clearCache()
	{
		Cache::removeFromCacheByPattern('*');
	}
	
	public static function removeFromCacheByPattern($pattern)
	{
		chdir(CACHE_DIR);
		if ($pattern != '*')
			$pattern = sprintf("*%s*", $pattern);
		$res = glob($pattern);
		foreach($res as $file)
			unlink(CACHE_DIR . $file);
	}
	
	public function isValid($ctime)
	{
		if(file_exists($this->cache_file))
		{
			if($ctime > filemtime($this->cache_file)){
				unlink($this->cache_file);
				return false;
			}
			return true;
		}
		return false;
	}
	
	public function cache($content)
	{
		$w = fopen($this->cache_file, "wb");
		fwrite($w, $content);
		fclose($w);
	}
	
	public function load()
	{
		return file_get_contents($this->cache_file);
	}
}
?>
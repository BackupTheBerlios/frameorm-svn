<?php
define(CACHE_DIR, $_SERVER['DOCUMENT_ROOT']."/.cache/");
define(CACHE_TIME, 30*60);
class Cache
{
	private $tpl;
	public $cache_file;
	
	function __construct($tpl)
	{
		$f = preg_replace("/\//","_",$tpl);
		$this->cache_file = $f.".cache";
		$this->tpl = $tpl;
		if(!is_dir(CACHE_DIR))
			mkdir(CACHE_DIR);
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
		$cached = CACHE_DIR . $this->cache_file;
		if(file_exists($cached))
		{
			if($ctime > filemtime($cached)){
				unlink(CACHE_DIR . $this->cache_file);
				return false;
			}
			return true;
		}
		return false;
	}
	
	public function cache($content)
	{
		$cached = CACHE_DIR . $this->cache_file;
		$w = fopen($cached, "wb");
		fwrite($w, $content);
		fclose($w);
	}
	
	public function load()
	{
		$cached = CACHE_DIR . $this->cache_file;
		ob_start();
	    include($cached);
	    $parsed = ob_get_contents();
	    ob_end_clean();
	    return $parsed;
	}
}
?>
<?php
class DirectoryReader
{	
	public static $ALLOWED = array('jpg','jpeg');
	public static function readDir($path)
	{
		$name = basename($path);
		$images = array();
		$dir = new DirectoryIterator($path);
		foreach($dir as $file )
		{
			$ext = strtolower(substr(strrchr($file->getFilename(), '.'), 1));
			if(!$file->isDot() && !$file->isDir() && in_array($ext, self::$ALLOWED))
		  		$images[] = sprintf("/%s/%s", $name, $file->getFilename());
		}
		return $images;
	}
}
?>
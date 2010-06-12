<?php
include_once($_SERVER['DOCUMENT_ROOT']."/framework/core/Interfaces.php");
include_once($_SERVER['DOCUMENT_ROOT']."/framework/core/AppException.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/framework/core/ObjectSet.class.php");
function scanFilesystem($dir, $class) 
{
	$itemArray = scandir($dir);

 	for($i=2; $i<count($itemArray); $i++) 
 	{
		$item = $itemArray[$i];
 		if (ereg('svn',$item))
 			continue;
		if ($item == $class.".class.php")
		{
			include_once($dir."/".$class.".class.php");
			return;
		}
		$newDir = $dir . "/" . $item;
		if(is_dir($newDir)) 
			scanFilesystem($newDir,$class);
	}
}

function __autoload($class)
{
	$libs = array('framework/core', 'framework/schema', 
				  'framework/core/mutex','framework/servlets', 
				  'framework/utils', 'framework/core/cache');
	$root = $_SERVER['DOCUMENT_ROOT'];
	foreach($libs as $dir)
		scanFilesystem($root."/".$dir, $class);
}
?>
<?php
class ETag
{
	public static function write($path)
	{
		header("Pragma:");
		header("Cache-Control:");
		$last_modified_time = filemtime($path);
		$etag = md5_file($path);
		if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time ||
		    trim($_SERVER['HTTP_IF_NONE_MATCH']) == '"'.$etag.'"') {
		    header('ETag: "'.$etag.'"');
		    header("HTTP/1.1 304 Not Modified");
		    return;
		}
		header("Content-Type: image/jpeg");
		header('ETag: "'.$etag.'"');
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
		readfile($path);
	}
}
?>
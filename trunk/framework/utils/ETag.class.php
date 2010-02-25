<?php
class ETag
{
	public static function send($path)
	{
		$response = Response::getInstance();
		$last_modified_time = filemtime($path);
		$etag = md5_file($path);
		if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time ||
		    trim($_SERVER['HTTP_IF_NONE_MATCH']) == '"'.$etag.'"') {
		    $response->addHeader('ETag', '"'.$etag.'"');
		    $response->httpCode = 304;
		    $response->body = '';
		    return;
		}
		$response->addHeader('ETag', '"'.$etag.'"');
		$response->addHeader("Last-Modified", gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
	}
}
?>
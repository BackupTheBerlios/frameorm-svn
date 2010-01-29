<?php
class Thumbnail
{
	private $imgPath;
	private $imgResource;
	
	function __construct($imagePath)
	{
		$this->imgPath = $imagePath;
	}
	
	public function resize($swidth, $sheight)
	{
		$width  = $swidth;
		$height = $sheight;
		
		list($width_orig, $height_orig) = getimagesize($this->imgPath);
		
		if ($width && ($width_orig < $height_orig)) {
			$width = ($height / $height_orig) * $width_orig;
		} else {
			$height = ($width / $width_orig) * $height_orig;
		}
		$image_p = imagecreatetruecolor($width, $height);
		$image   = imagecreatefromjpeg($this->imgPath);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		$this->imgResource = $image_p;
	}
	
	public function write($path = null)
	{
		if($path)
			imagejpeg($this->imgResource, $path, 100);
		else
			imagejpeg($this->imgResource);
		imagedestroy($this->imgResource);	
	}
	
	public function square($iDims)
	{
		$size = getimagesize($this->imgPath);
		$width = $size[0];
		$height = $size[1];
		if($width> $height) 
		{
			$x = ceil(($width - $height) / 2 );
			$width = $height;
		} 
		elseif($height> $width) 
		{
			$y = ceil(($height - $width) / 2);
			$height = $width;
		}
		$new_im = imagecreatetruecolor($iDims, $iDims);
		$im = imagecreatefromjpeg($this->imgPath);
		imagecopyresampled($new_im,$im,0,0,$x,$y,$iDims,$iDims,$width,$height);
		$this->imgResource = $new_im;
	}
}	
?>
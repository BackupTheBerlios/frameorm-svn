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
			imagejpeg($this->imgResource, $path, 90);
		else
			imagejpeg($this->imgResource);
		imagedestroy($this->imgResource);	
	}
	
	public function crop($width, $height)
	{
		list($width_orig, $height_orig) = getimagesize($this->imgPath);  
	    $myImage = imagecreatefromjpeg($this->imgPath);
	    $ratio_orig = $width_orig/$height_orig;
	   
	    if ($width/$height > $ratio_orig) {
	       $new_height = $width/$ratio_orig;
	       $new_width = $width;
	    } else {
	       $new_width = $height*$ratio_orig;
	       $new_height = $height;
	    }
	   
	    $x_mid = $new_width/2;  //horizontal middle
	    $y_mid = $new_height/2; //vertical middle
	   
	    $process = imagecreatetruecolor(round($new_width), round($new_height));
	   
	    imagecopyresampled($process, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
	    $this->imgResource = imagecreatetruecolor($width, $height);
	    imagecopyresampled($this->imgResource, $process, 0, 0, ($x_mid-($width/2)), ($y_mid-($height/2)), $width, $height, $width, $height);
	
	    imagedestroy($process);
	    imagedestroy($myImage);
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
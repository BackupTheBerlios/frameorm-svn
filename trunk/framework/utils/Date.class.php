<?php
define('LOCALDATE', "d/m/Y");
define('LOCALDATETIME', "d/m/Y H:i:s");
define('TIMEZONE',"Europe/Athens");

class Date
{
	private $sDate;
	private $oDate;
	private $localFormat; 
	
	public static $formats = array(
		'd/m/Y' => array( 
			local_match => "/(\d{2})\/(\d{2})\/(\d{4})/",
			toIso => "$3-$2-$1"
		),
		'm/d/Y' => array( 
			local_match => "/(\d{2})\/(\d{2})\/(\d{4})/",
			toIso => "$3-$1-$2"
		),
		'd-m-Y' => array( 
			local_match => "/(\d{2})-(\d{2})-(\d{4})/",
			toIso => "$3-$2-$1"
		),
		'm-d-Y' => array( 
			local_match => "/(\d{2})-(\d{2})-(\d{4})/",
			toIso => "$3-$1-$2"
		)
	);
	
	public function __construct($date = 'today')
    {
        $this->sDate = $date;
        $this->localFormat = self::$formats[LOCALDATE];
        $this->init();
    }
    
    private function init()
    {
    	if ($this->sDate == 'today')
    	{
    		$this->oDate = new DateTime($this->sDate);
    		return;
    	}
    	if (self::isIso($this->sDate))
    		$this->oDate = new DateTime($this->sDate);
    	else
    		$this->oDate = new Datetime($this->getIsoString());
    }
    
	private function getIsoString()
    {
    	return preg_replace($this->localFormat['local_match'], 
    						$this->localFormat['toIso'], 
    						$this->sDate);
    }
    
    public function format($format)
    {
    	return $this->oDate->format($format);
    }
    
    public static function isIso($date)
    {
    	return preg_match("/(\d{4})-(\d{2})-(\d{2})/", $date);
    }
    
    public static function isLocal($date)
    {
    	return !self::isIso($date);
    }
    
    public function toIso()
    {
    	return $this->format('Y-m-d');
    }
    
    public function toLocal()
    {
    	return $this->format(LOCALDATE);
    }
}


class Timestamp
{
	private $oTimezone;
	private $oDatetime;
	private $sDatetime;
	private $localFormat;

	public static $formats = array(
		'd/m/Y H:i:s' => array( 
			local_match => "/(\d{2})\/(\d{2})\/(\d{4})\s(\d{2}:\d{2}:\d{2})/",
			toIso => "$3-$2-$1 $4"
		),
		'm/d/Y H:i:s' => array( 
			local_match => "/(\d{2})\/(\d{2})\/(\d{4})\s(\d{2}:\d{2}:\d{2})/",
			toIso => "$3-$1-$2 $4"
		),
		'd-m-Y H:i:s' => array( 
			local_match => "/(\d{2})-(\d{2})-(\d{4})\s(\d{2}:\d{2}:\d{2})/",
			toIso => "$3-$2-$1 $4"
		),
		'm-d-Y H:i:s' => array( 
			local_match => "/(\d{2})-(\d{2})-(\d{4})\s(\d{2}:\d{2}:\d{2})/",
			toIso => "$3-$1-$2 $4"
		)
	);
	
	public function __construct($datetime = 'now', 
								$sTimezone = TIMEZONE)
    {
        $this->sDatetime = $datetime;
        $this->localFormat = self::$formats[LOCALDATETIME];
        $this->oTimezone = new DateTimeZone($sTimezone);
        $this->init();
    }
    
	private function init()
    {
    	if (self::isIso($this->sDatetime) || $this->sDatetime == 'now')
    		$this->oDatetime = new DateTime($this->sDatetime, $this->oTimezone);
    	else
    		$this->oDatetime = new Datetime($this->getIsoString(), $this->oTimezone);
    }
    
    private function getIsoString()
    {
    	return preg_replace($this->localFormat['local_match'], 
    						$this->localFormat['toIso'], 
    						$this->sDatetime);
    }
    
    public static function isIso($date)
    {
    	return preg_match("/(\d{4})-(\d{2})-(\d{2})\s(\d{2}:\d{2}:\d{2})/", $date);
    }
    
    public static function isLocal($date)
    {
    	return !self::isIso($date);
    }
    
    public function getAbbr()
    {
    	return "GMT ".$this->oDatetime->format('P');
    }
    
    public function setTimezone($sTimezone)
    {
    	$oZone = new DateTimeZone($sTimezone);
    	$this->oDatetime->setTimezone($oZone);
    }
    
    public function toIso()
    {
    	return $this->format('Y-m-d H:i:s');
    }
    
    public function toLocal()
    {
    	return $this->format(LOCALDATETIME);
    }
    
	public function format($format)
    {
    	return $this->oDatetime->format($format);
    }
}
?>
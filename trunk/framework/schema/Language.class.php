<?php
class Language extends Identifiable
{
	private static $lang = null;
	private static $langs = null;
	public $table = array(
						  "table"=>"languages",
						  "PK"=>array(
						  			   "id"=>null
						  			  )
						 );
	
	public function __construct(array $data=array())
	{
		parent::__construct($data);
	}
	
	public function getStrings()
	{
		$path = sprintf("%s/framework/lang/%s.php", $_SERVER['DOCUMENT_ROOT'], $this->id);
		include($path);
		return $LANGUAGE;
	}
	
	public static function getLanguageById($id)
	{
		if(is_null(Language::$lang) || (Language::$lang->id != $id))
		{
			$row = Identifiable::getItemById("'$id'", 'languages');
			Language::$lang = new Language($row);
		}
		return Language::$lang;
	}
	
	public static function getAllLanguages()
	{
		if(is_null(Language::$langs))
		{
			$oContext = Context::getInstance();
			$oLang = Language::getLanguageById($oContext->session->lang);
			$order = sprintf("(CASE WHEN id=%d THEN 0 ELSE 1 END)", $oLang->id);
			$rs = Identifiable::getAllItemsByTable('languages', $order);
			Language::$langs = new ObjectSet($rs, __CLASS__);
		}
		return Language::$langs;
	}
}
?>
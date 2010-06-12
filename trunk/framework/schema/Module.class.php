<?php
class Module extends Acl
{
	public $table = array(
						  "table"=>"modules",
						  "PK"=>array(
			  			   	"id"=>null
			  			  )
						 );

	
	public function __construct(array $data=array())
	{
		parent::__construct($data);
	}
	
	public static function getModules()
	{
		return parent::getAllItemsByTable(new Module);
	}
	
	public static function getModuleById($id)
	{
		return parent::getItemById($id, new Module);
	}
	
	public static function getModuleByName($name)
	{
		$db = DB::getInstance();
		$q = "SELECT * FROM modules WHERE name='$name'";
		$rs = $db->db_query($q);
		if($db->db_num_rows($rs) > 0)
			return new Module($db->db_fetch_array($rs));
		return null;
	}
}
?>
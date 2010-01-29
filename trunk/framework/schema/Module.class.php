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
		$db = DB::getInstance();
		$q = "SELECT * FROM modules";
		$rs = $db->db_query($q);
		if($db->db_num_rows($rs) > 0)
			return new ObjectSet($rs, __CLASS__);
		return array();
	}
	
	public static function getModuleById($id)
	{
		$row = parent::getItemById($id, 'modules');
		return new Module($row);
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
<?php
class UserGroup extends Table
{
	public $userID;
	public $groupID;
	public $table = array(
						  "table"=>"user_groups",
						  "PK"=>array(
			  			   	"userID"=>null,
							"groupID"=>null
			  			  )
						 );
	
	public function __construct(array $data=array())
	{
		parent::__construct($data);
	}
	
	public static function deleteByUserId($userID)
	{
		$q = sprintf("DELETE FROM user_groups
					  WHERE userID=%d", $userID);
		$db = DB::getInstance();
		$db->db_query($q);
	}
	
	public static function getByUserId($userID)
	{
		$q = sprintf("SELECT * FROM user_groups
					  WHERE userID=%d", $userID);
		$db = DB::getInstance();
		$rs = $db->db_query($q);
		if($db->db_num_rows($rs) > 0)
			return new ObjectSet($rs, __CLASS__);
		return array();
	}
}
?>
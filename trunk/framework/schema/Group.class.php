<?php

class Group extends Identifiable
{
	public $table = array(
						  "table"=>"groups",
						  "PK"=>array(
			  			   	"id"=>null
			  			  )
						 );
	
	CONST EVERYONE = 3;
	CONST AUTHUSERS = 2;
	CONST ADMINS = 1;
	
	public function __construct(array $data=array())
	{
		parent::__construct($data);
	}
	
	public static function getGroupById($id)
	{
		$row = parent::getItemById($id, 'groups');
		return new Group($row);
	}
	
	public static function getGroups()
	{
		$db = DB::getInstance();
		$rs = $db->db_query("SELECT * FROM groups 
							WHERE id NOT IN (2,3)
							ORDER BY name");
		if($db->db_num_rows($rs) == 0)
			return null;
		return new ObjectSet($rs, __CLASS__);
	}	
	
	public function hasMember(User $user)
	{
		if($this->id == Group::EVERYONE)
			return true;
		if($this->id == Group::AUTHUSERS && $user->id != User::GUEST)
			return true;
		$q = sprintf("SELECT * FROM user_groups
					  WHERE userID=%d AND groupID=%d", $user->id,
													   $this->id);
		$db = DB::getInstance();
		$rs = $db->db_query($q);
		return $db->db_num_rows($rs) == 1;
	}
}
?>
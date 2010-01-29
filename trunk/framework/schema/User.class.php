<?php
class User extends Identifiable
{
	public $pass;
	public $username;
	public $surname;
	public $table = array(
						  "table"=>"users",
						  "PK"=>array(
			  			   	"id"=>null
			  			  )
						 );
	const GUEST = 1;
	const ADMIN = 2;
	
	public function __construct(array $data=array())
	{
		parent::__construct($data);
	}
	
	public function isAdmin()
	{
		if($this->id == User::ADMIN)
			return true;
		$group = Group::getGroupById(Group::ADMINS);
		return $group->hasMember($this);
	}
	
	public static function getUsers()
	{
		$all = parent::getAllItemsByTable('users', 'surname');
		return new ObjectSet($all, __CLASS__);
	}
	
	public static function getUserById($id)
	{
		$row = parent::getItemById($id, 'users');
		return new User($row);
	}
	
	public static function getUserByUsername($name)
	{
		$q = sprintf("SELECT * FROM users
					  WHERE username='%s'", $name);
		$db = DB::getInstance();
		$rs = $db->db_query($q);
		if($db->db_num_rows($rs) == 1)
			return new User($db->db_fetch_array($rs));
		return null;
	}
	
	public function authenticate($pass){
		$q = sprintf("SELECT * FROM users
					  WHERE username='%s' AND pass=SHA1('%s')", $this->username, $pass);
		$db = DB::getInstance();
		$rs = $db->db_query($q);
		return $db->db_num_rows($rs) == 1;
	}
	
	private function encodePassword()
	{
		$db = DB::getInstance();
		$q = sprintf("SELECT SHA1('%s') AS s", $this->pass);
		$this->pass = $db->db_fetch_one($q);
	}
	
	protected function doSave()
	{
		$this->encodePassword();
		parent::doSave();
	}
	
	protected function update()
	{
		$user = User::getUserById($this->id);
		if($user->pass != $this->pass)
			$this->encodePassword();
		parent::update();
	}
}
?>
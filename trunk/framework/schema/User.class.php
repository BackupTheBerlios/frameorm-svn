<?php
class User extends Identifiable
{
	public $pass;
	public $username;
	public $surname;
	public $salt;
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
		if($this->salt == '')
			$this->salt = Utils::genRandom();
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
		return parent::getAllItemsByTable(new User);
	}
	
	public static function getUserById($id)
	{
		return parent::getItemById($id, new User);
	}
	
	public static function getUserByUsername($name)
	{
		$q = sprintf("SELECT * FROM users
					  WHERE username='%s'", $name);
		return Table::query($q, new User);
	}
	
	public function authenticate($pass){
		$pass = sha1($this->salt.$pass);
		return $this->pass == $pass;
	}
	
	private function encodePassword()
	{
		$this->pass = sha1($this->salt.$this->pass);
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
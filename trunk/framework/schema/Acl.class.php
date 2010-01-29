<?php
class Acl extends Identifiable
{
	public $permissions;
	CONST NOACCESS = 0;
	CONST READER = 1;
	CONST AUTHOR = 2;
	CONST ADMINISTRATOR = 4;
	protected static $ACLS = array(
		1 => 'Reader',
		2 => 'Author (read, write, delete if owner)',
		4 => 'Administrator (full control)'
	);
	
	public function __construct(array $data=array())
	{
		parent::__construct($data);
		$this->setPermissions();
	}
	
	protected function setPermissions()
	{
		if($this->permissions == ''){
			$context = Context::getInstance();
			$this->permissions = array(
				'users' => array(
					$context->user->id => Acl::AUTHOR 
				),
				'groups' => array(
					Group::EVERYONE => Acl::READER,
					Group::ADMINS => Acl::ADMINISTRATOR
				)
			);
		}else if(is_string($this->permissions)){
			$this->permissions = json_decode($this->permissions, true);
		}
	}
	
	protected function doSave()
	{
		$context = Context::getInstance();
		$role = $this->checkAccess($context->user);
		if($role <= Acl::READER)
			throw new PermissionDenied("No permission to create");
		$this->permissions = json_encode($this->permissions);
		parent::doSave();
	}
	
	protected function update()
	{
		$context = Context::getInstance();
		$role = $this->checkAccess($context->user);
		if($role <= Acl::READER)
			throw new PermissionDenied("No permission to update");
		$this->permissions = json_encode($this->permissions);
		parent::update();
	}
	
	public function delete()
	{
		$context = Context::getInstance();
		$role = $this->checkAccess($context->user);
		if($role <= Acl::READER)
			throw new PermissionDenied("No permission to delete");
		parent::delete();
	}
	
	public function checkAccess(User $user)
	{
		if($user->isAdmin())
			return Acl::ADMINISTRATOR;
			
		$perms = $this->permissions;
		if(array_key_exists($user->id, $perms['users']))
			return $perms['users'][$user->id];
		
		$groups = $perms['groups'];
		$prms = array(0);
		foreach($groups as $group=>$role){
			$oGroup = Group::getGroupById($group);
			if($oGroup->hasMember($user))
				$prms[] = $role;
		}
		return max($prms);
	}
}
?>
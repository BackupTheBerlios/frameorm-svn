<?php 
class Userpage extends Admin
{

	public function __construct()
	{
		parent::__construct();
	}
	
	/*
	 * Return a templated representation of a user
	 */
	public static function getUserTemplate(User $user)
	{
		$template = new Template('admin/ui.generic.tpl');
		$template->name = sprintf("%s %s", $user->name, $user->surname);
		$template->id = $user->id;
		$template->onclick = "user.create(this);";
		$template->onedit = "user.edit(event, this);";
		$template->ondelete = "user.deleteUser(event, this);";
		return $template->parse();
	}
	
	/*
	 * Search a user by name.
	 * Used by autocomplete widgets
	 */
	public function get($value)
	{
		$res = User::searchName('users', $value, 'User');
		$ret = array();
		if(count($res) > 0){
			foreach($res as $r){
				$ret[] = array(
					'id'=>$r->id,
					'name'=>$r->name." ".$r->surname
				);
			}
		}
		return $ret;
	}
	
	/*
	 * Delete the specified (by id) user
	 */
	public function delete($id)
	{
		if($id == User::ADMIN || $id == User::GUEST){
			$context = Context::getInstance();
			$context->response->httpCode = 403;
			$context->response->body = 'Permission denied';
			$context->response->write();
		}
		$user = User::getUserById($id);
		$user->delete();
		return true;
	}
	
	/*
	 * Create a new user
	 */
	public function save($data)
	{
		$db = DB::getInstance();
		$db->start_transaction();
		try{
			$user = new User($data);
			$user->save();
			if(count($data['groups']) > 0){
				foreach($data['groups'] as $id){
					$attrs = array(
						'userID'=>$user->id,
						'groupID'=>$id
					);
					$ug = new UserGroup($attrs);
					$ug->save(true);
				}
			}
			$db->commit();
			return true;
		}catch(AppException $e){
			return $e->getMessage();
			$db->rollback();
			return false;
		}
	}
	
	/*
	 * Update a user
	 */
	public function update($id, $data)
	{
		$db = DB::getInstance();
		$db->start_transaction();
		try{
			$user = User::getUserById($id);
			$user->updateMembers($data);
			$user->save();
			UserGroup::deleteByUserId($user->id);
			if(count($data['groups']) > 0){
				foreach($data['groups'] as $id){
					$attrs = array(
						'userID'=>$user->id,
						'groupID'=>$id
					);
					$ug = new UserGroup($attrs);
					$ug->save(true);
				}
			}
			$db->commit();
			return true;
		}catch(AppException $e){
			return $e->getMessage();
			$db->rollback();
			return false;
		}
	}
	
	/*
	 * Display the form for updating a user
	 */
	public function editForm($id)
	{
		$user = User::getUserById($id);
		$template = new Template('admin/security/ui.userForm.tpl');
		$grp = '';
		$groups = Group::getGroups();
		foreach($groups as $group){
			$selected = '';
			if($group->hasMember($user))
				$selected = 'selected="true"';
			$grp .= sprintf('<option %s value="%d">%s</option>', $selected,
																 $group->id,
															 	 $group->name);
		}
		$template->user = $user;
		$template->action = "/userpage/update/".$user->id;
		$template->groups = $grp;
		return $template->parse();
	}
	
	/*
	 * Display the form for creating a user
	 */
	public function userForm()
	{
		$template = new Template('admin/security/ui.userForm.tpl');
		$grp = '';
		$groups = Group::getGroups();
		foreach($groups as $group){
			$grp .= sprintf('<option value="%d">%s</option>', $group->id,
															  $group->name);
		}
		$template->user = new User();
		$template->action = "/userpage/save";
		$template->groups = $grp;
		return $template->parse();
	}
}
?>
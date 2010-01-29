<?php 
class Modulepage extends Admin
{

	public function __construct()
	{
		parent::__construct();
	}
	
	/*
	 * Return a templated representation of a module
	 */
	public static function getModuleTemplate(Module $module)
	{
		$template = new Template('admin/ui.generic.tpl');
		$template->name = $module->name;
		$template->id = $module->id;
		$template->onclick = "admin.module.create(this);";
		$template->onedit = "admin.module.edit(event, this);";
		$template->ondelete = "admin.module.deleteModule(event, this);";
		return $template->parse();
	}
	
	/*
	 * Delete the specified (by id) module
	 */
	public function delete($id)
	{
		$module = Module::getModuleById(id);
		$module->delete();
		return true;
	}
	
	/*
	 * Create a new user
	 */
	public function save($data)
	{
		$module = new Module($data);
		$module->save();
		return true;
	}
	
	/*
	 * Update a module
	 */
	public function update($id, $data)
	{
		$module = Module::getModuleById($id);
		$module->updateMembers($data);
		$module->save();
	}
	
	/*
	 * Display the form for creating a module
	 */
	public function moduleForm()
	{
		$template = $this->getTemplate(new Module());
		$template->action = "/modulepage/save";
		return $template->parse();
	}
	
	/*
	 * Display the form for updating a module
	 */
	public function editForm($id)
	{
		$module = Module::getModuleById($id);
		$template = $this->getTemplate($module);
		$template->action = "/modulepage/update/".$module->id;
		return $template->parse();
	}
	
	private function getOptions($role)
	{
		$roles = array(
			'Reader' => 1,
			'Author' => 2,
			'Administrator' => 4
		);
		$opts = '';
		foreach($roles as $name=>$perm){
			$selected = '';
			if($perm == $role)
				$selected = 'selected="true"';
			$opts .= sprintf('<option value="%d" %s>%s</option>', $perm, $selected ,$name);
		}
		return $opts;
	}
	
	private function getTemplate(Module $module)
	{
		$template = new Template('admin/security/ui.moduleForm.tpl');
		$permsTpl = new Template('admin/security/ui.permissions.tpl');
		$perms = $module->permissions;
		
		$users = '';
		$groups = '';
		
		foreach($perms['users'] as $userID=>$role){
			$user = User::getUserById($userID);
			$permsTpl->id = $user->id;
			$permsTpl->type = 'users';
			$permsTpl->name = sprintf("%s %s", $user->name, $user->surname);
			$permsTpl->options = $this->getOptions($role);
			$users .= $permsTpl->parse();
		}
		
		foreach($perms['groups'] as $groupID=>$role){
			$group = Group::getGroupById($groupID);
			$permsTpl->type = 'groups';
			$permsTpl->id = $group->id;
			$permsTpl->name = $group->name;
			$permsTpl->options = $this->getOptions($role);
			$groups .= $permsTpl->parse();
		}
		$template->users = $users;
		$template->groups = $groups;
		$template->module = $module;
		return $template;
	}
}
?>
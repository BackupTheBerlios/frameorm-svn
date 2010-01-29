<?php 
class Groupage extends Admin
{

	public function __construct()
	{
		parent::__construct();
	}
	
	/*
	 * Search a group by name.
	 * Used by autocomplete widgets
	 */
	public function get($value)
	{
		$res = Group::searchName('groups', $value, 'Group');
		$ret = array();
		if(count($res) > 0){
			foreach($res as $r){
				$ret[] = array(
					'id'=>$r->id,
					'name'=>$r->name
				);
			}
		}
		return $ret;
	}
	
	/*
	 * Return a templated representation of a group
	 */
	public static function getGroupTemplate(Group $group)
	{
		$template = new Template('admin/ui.generic.tpl');
		$template->name = $group->name;
		$template->id = $group->id;
		$template->onclick = "group.create(this);";
		$template->onedit = "group.edit(event, this);";
		$template->ondelete = "group.deleteGroup(event, this);";
		return $template->parse();
	}
	
	/*
	 * Delete the specified (by id) group
	 */
	public function delete($id)
	{
		if($id == Group::ADMINS || $id == Group::EVERYONE || $id == Group::AUTHUSERS){
			$context = Context::getInstance();
			$context->response->httpCode = 403;
			$context->response->body = 'Permission denied';
			$context->response->write();
		}
		$group = Group::getGroupById($id);
		$group->delete();
		return true;
	}
	
	/*
	 * Create a new group
	 */
	public function save($data)
	{
		$group = new Group($data);
		$group->save();
		return true;
	}
	
	/*
	 * Update a group
	 */
	public function update($id, $data)
	{
		$group = Group::getGroupById($id);
		$group->updateMembers($data);
		$group->save();
		return true;
	}
	
	/*
	 * Display the form for updating a group
	 */
	public function editForm($id)
	{
		$group = Group::getGroupById($id);
		$template = new Template('admin/security/ui.groupForm.tpl');
		$template->group = $group;
		$template->action = "/groupage/update/".$group->id;
		return $template->parse();
	}
	
	/*
	 * Display the form for creating a user
	 */
	public function groupForm()
	{
		$template = new Template('admin/security/ui.groupForm.tpl');
		$template->group = new Group();
		$template->action = "/groupage/save";
		return $template->parse();
	}
}
?>
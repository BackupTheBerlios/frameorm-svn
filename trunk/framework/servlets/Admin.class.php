<?php
class Admin extends Page implements ACLControl
{
	public static $navbar = array();
	public function __construct()
	{
		parent::__construct();
	}
	
	public function i18n(){}
	
	/*
	 * Access control implementation.
	 * Http requests on admin modules undergo
	 * access control, to ensure that unauthorized
	 * or under privileged users cannot call these
	 * methods
	 */
	public function on_request($module)
	{
		$context = Context::getInstance();
		if($module->checkAccess($context->user) == Acl::NOACCESS){
			$url = sprintf("http://%s%s", $_SERVER['SERVER_NAME'],
										  $_SERVER['REQUEST_URI']);
			$context->session->redirect = $url;
			$context->response->redirect($this->get_login_url());
		}
	}
	
	public function get_login_url()
	{
		return "/page/loginForm";
	}
	
	public static function render($body)
	{
		$template = new Template('admin/ui.main.tpl');
		$template->body = $body;
		return $template->parse();
	}
	
	protected static function getNavBar()
	{
		self::$navbar = array_reverse(self::$navbar);
		return join(" >> ", self::$navbar);
	}
	
	public static function getIndexTemplate()
	{
		$context = Context::getInstance();
		$oIndex = new Template('admin/ui.index.tpl');
		$oIndex->navbar = Admin::getNavBar();
		$oIndex->user = $context->user->username;
		$context = Context::getInstance();
		if($context->user->id == User::ADMIN)
			$oIndex->sections = '<li><a href="/admin/modules">Admin Panel</a></li>';
		return $oIndex;
	}
	
	public function index()
	{
		self::$navbar[] = '<a href="/admin/index">Αρχική</a>';
		$oIndex = $this->getIndexTemplate();
		return Admin::render($oIndex->parse());
	}
	
	public function modules()
	{
		self::$navbar[] = '<a href="/admin/modules">Admin Panel</a>';
		$oIndex = $this->getIndexTemplate();
		$template = new Template('admin/ui.modules.tpl');
		
		
		$user_str = '';
		$users = User::getUsers();
		foreach ($users as $user)
			$user_str .= Userpage::getUserTemplate($user);
			
		$group_str = '';
		$groups = Group::getGroups();
		foreach ($groups as $group)
			$group_str .= Groupage::getGroupTemplate($group);
			
		$mod_str = '';
		$modules = Module::getModules();
		foreach ($modules as $module)
			$mod_str .= Modulepage::getModuleTemplate($module);
			
		$template->groups = $group_str;
		$template->users = $user_str;
		$template->modules = $mod_str;
		$oIndex->content = $template->parse();
		return Admin::render($oIndex->parse());
	}
}
?>
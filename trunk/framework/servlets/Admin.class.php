<?php
class Admin extends Page implements ACLControl, PersistentLogin
{
	public static $navbar = array();
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/*
	 * Pre process filter that checks for the frmauth cookie.
	 * If the cookie exists and the user is guest, we extract
	 * the values stored in the cookie (uid, sid, token) and
	 * query the table for the record. If the record exists,
	 * we automatically authenticate the user and reissue a
	 * new token with the same sid.
	 * 
	 * In case the uid and sid exist, but the token does not,
	 * we assume a cookie hijack. Ath this point the user should
	 * be informed and all tokens associated to the uid-sid pair
	 * are deleted.
	 * 
	 */
	public function checkCookieToken()
	{
		$context = Context::getInstance();
		if(isset($_COOKIE['frmauth']) && ($context->session->userID == User::GUEST)){
			$val = explode('_', $_COOKIE['frmauth']);
			$token = UserToken::getUserToken($val[0], $val[1], $val[2]);
			if($token){
				$context->session->userID = (int) $val[0];
				$context->user = User::getUserById($val[0]);
				$token->delete();
				UserToken::setCookieToken($context->user, $val[1]);
			}else{
				$token = UserToken::getByUidAndSid($val[0], $val[1]);
				if($token){
					//possible cookie theft
					UserToken::deleteByUidAndSid($val[0], $val[1]);
					$context->session->destroy();
					echo 'cookie hijacked';
					exit();
				}
			}
		}
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
		print_r($_COOKIE);
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
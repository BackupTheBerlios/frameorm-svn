<?php
class Page implements Translatable
{	
	public function __construct(){}
	
	public function i18n()
	{
		return;
		$context = Context::getInstance();
		$body = $context->response->body;
		$lang = Language::getLanguageById($context->session->lang);
		$lang = (array) $lang->getStrings();
		$translate = array();
		foreach ($lang as $key => $value)
		    $translate['@@' . $key . '@@'] = $value;
		$context->response->body = str_replace(array_keys($translate), 
											   array_values($translate), $body);
	}
	
	protected function authenticate($username, $password, 
								    $remember_me=false)
	{
		$db = DB::getInstance();
		$username = $db->db_escape_string($username);
		$password = $db->db_escape_string($password);
		$user = User::getUserByUsername($username);
		if(!is_null($user) && $user->authenticate($password)){
			$context = Context::getInstance();
			$context->session->regenerate();
			$context->session->userID = (int) $user->id;
			$context->user = $user;
			if($remember_me)
				UserToken::setCookieToken($user, Utils::genRandom(10));
			return true;
		}
		return false;
	}
	
	public function logout()
	{
		$context = Context::getInstance();
		$context->session->destroy();
		return true;
	}
	
	public function loginForm()
	{
		print $_COOKIE['frmauth'];
		$oTemplate = new Template('admin/ui.login.tpl');
		return Admin::render($oTemplate->parse());
	}
	
	public function login($data)
	{
		$rem = false;
		if(array_key_exists('remember', $data))
			$rem = true;
		if($this->authenticate($data['username'], $data['password'], $rem)){
			if(isset($context->session->redirect))
				$url = $context->session->redirect;
			else
				$url = '/admin/index';
			return array(
				'error' => false,
				'url' => $url
			);
		}
		return array(
			'error' => true
		);
	}
	
	public static function getIndexTemplate()
	{
		return new Template('ui.main.tpl');
	}
}
?>
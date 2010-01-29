<?php
class Page implements PostProcessFilter
{	
	public function __construct(){}
	
	public function i18n()
	{
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
	
	public function logout()
	{
		$context = Context::getInstance();
		return $context->session->destroy();
	}
	
	public function loginForm()
	{
		$oTemplate = new Template('admin/ui.login.tpl');
		return Admin::render($oTemplate->parse());
	}
	
	public function login($data)
	{
		$url = $_SERVER['HTTP_REFERER'];
		$user = User::getUserByUsername($data['username']);
		if(!is_null($user) && $user->authenticate($data['password'])){
			$context = Context::getInstance();
			$context->session->userID = (int) $user->id;
			$context->user = $user;
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
		$oTemplate = new Template('ui.main.tpl');
		return $oTemplate;
	}
}
?>
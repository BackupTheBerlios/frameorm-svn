<?php
class Page implements Translatable
{	
	public static $navbar = array();
	protected $css = array();
	protected $js = array();
	
	public function __construct()
	{
		$this->css = array(
			'admin' => array(
			   'framework/admin/css/admin.css',
		 	   'framework/static/css/lib.css',
		 	   'framework/static/css/autocomplete.css',
			   'framework/static/css/window.css',
			   'framework/static/css/button.css'
			)
		);
		
		$this->js = array(
			'admin' => array(
				'framework/static/js/sprintf.js',
				'framework/static/js/utils.js',
				'framework/static/js/json.js',
				'framework/static/js/ajax.js',
				'framework/static/js/autocomplete.js',
				'framework/static/js/drag.js',
				'framework/static/js/form.js',
				'framework/admin/js/admin.js'
			)
		);
	}
	
	public function i18n()
	{
		$context = Context::getInstance();
		$body = $context->response->body;
		$lng = (isset($context->session->lang))?$context->session->lang:'el_GR';
		$lang = Language::getLanguageById($lng);
		$lang = (array) $lang->getStrings();
		$translate = array();
		foreach ($lang as $key => $value)
		    $translate['@@' . $key . '@@'] = $value;
		$context->response->body = str_replace(array_keys($translate), 
											   array_values($translate), $body);
	}
	
	/*
	 * Authenticate user. If the user wishes to be
	 * remembered, we issue an authentication token.
	 */
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
	
	/*
	 * Login form for the administration area.
	 * This method resides here, because the
	 * Admin module implements authentication check.
	 */
	public function loginForm()
	{
		$template = new Template('admin/ui.login.tpl');
		$admin = new Admin();
		return $admin->render($template->parse());
	}
	
	public function login($data)
	{
		$rem = array_key_exists('remember', $data);
		if($this->authenticate($data['username'], $data['password'], $rem)){
			if(isset($context->session->redirect))
				$url = $context->session->redirect;
			else
				$url = 'http://framework.tld/admin/index';
			return array(
				'error' => false,
				'url' => $url
			);
		}
		return array(
			'error' => true
		);
	}
	
	/*
	 * Method responsible for combining multiple css
	 * files into one, minifies them and gzips them.
	 */
	public function css($section, $revision)
	{
		$revision = explode('.', $revision);
		$revision = (int)$revision[0];
		$file = sprintf("%s.css", $section);
		$cache = new Cache($file);
		if($cache->isValid($revision)){
			$content = $cache->load();
		}else{
			$content = '';
			foreach($this->css[$section] as $style)
				$content .= file_get_contents($style)."\n";
			$content = cssmin::minify($content);
			$cache->cache($content);
		}
		$context = Context::getInstance();
		$context->response->contentType = 'text/css';
		$context->response->addHeader("Expires", gmdate('D, d M Y H:i:s', time()+365*24*3600) . ' GMT');
		return ETag::send($cache->cache_file);
	}
	
	/*
	 * Get the link for css or js files.
	 * We get the most recently modified file
	 * and add its timestamp as the revision.
	 */
	protected function getLink($section, $type)
	{
		$mod = array();
		$arr = $this->$type;
		foreach($arr[$section] as $style)
			$mod[] = filemtime($style);
		rsort($mod);
		if($this instanceof Admin)
			$module = 'page';
		else
			$module = strtolower(get_class($this));
		return "/$module/$type/$section/".$mod[0].".".$type;
	}
	
	/*
	 * Method responsible for combining multiple js
	 * files into one, minifies them and gzips them.
	 */
	public function js($section, $revision)
	{
		$revision = explode('.', $revision);
		$revision = (int)$revision[0];
		$file = sprintf("%s.js", $section);
		$cache = new Cache($file);
		if($cache->isValid($revision)){
			$content = $cache->load();
		}else{
			$content = '';
			foreach($this->js[$section] as $style)
				$content .= file_get_contents($style)."\n";
			$content = JSMin::minify($content);
			$cache->cache($content);
		}
		$context = Context::getInstance();
		$context->response->contentType = 'text/javascript';
		$context->response->addHeader("Expires", gmdate('D, d M Y H:i:s', time()+365*24*3600) . ' GMT');
		return ETag::send($cache->cache_file);
	}
}
?>
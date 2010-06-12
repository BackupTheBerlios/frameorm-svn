<?php
class UserToken extends Table
{
	public $uid;
	public $sid;
	public $token;
	public $table = array(
						  "table"=>"user_token",
						  "PK"=>array(
			  			   	"uid"=>null,
							"sid"=>null,
							"token"=>null
			  			  )
						 );
	
	public function __construct(array $data=array())
	{
		parent::__construct($data);
		if($this->token == '')
			$this->token = Utils::genRandom(30);
	}
	
	/*
	 * Send a http cookie to allow automatic login, in a
	 * safe manner.
	 */
	public static function setCookieToken(User $user, $salt)
	{
		$instance = new UserToken();
		$instance->sid = $salt;
		$instance->uid = $user->id;
		$instance->save(true);
		$cookie_val = sprintf('%s_%s_%s', $instance->uid, $instance->sid, 
							              $instance->token);
		$context = Context::getInstance();
		$context->session->set_cookie('frmauth', $cookie_val, time()+60*60*24*30);
	}
	
	public static function getUserToken($uid, $sid, $token)
	{
		$q = "SELECT * FROM user_token
			  WHERE uid=$uid AND sid='$sid'
			  AND token='$token'";
		return Table::query($q, new UserToken);
	}
	
	public static function deleteByUidAndSid($uid, $sid)
	{
		$all = UserToken::getByUidAndSid($uid, $sid);
		if($all instanceof UserToken)
			$all->delete();
		if($all instanceof ObjectSet){
			foreach($all as $a)
				$a->delete();
		}
	}
	
	public static function getByUidAndSid($uid, $sid)
	{
		$q = "SELECT * FROM user_token
			  WHERE uid=$uid AND sid='$sid'";
		return Table::query($q, new UserToken);
	}
}
?>
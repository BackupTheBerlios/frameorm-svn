<?php
interface EventHandler
{
	public function on_create();
	public function on_update($old);
	public function on_delete();
	public function on_after_delete();
}

interface PersistentLogin extends PreProcessFilter
{
	public function checkCookieToken();
}

interface PreProcessFilter{}

interface PostProcessFilter{}

interface Translatable extends PostProcessFilter
{
	public function i18n();
}

interface ACLControl
{
	public function on_request($module);
	public function get_login_url();
}
?>
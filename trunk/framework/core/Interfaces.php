<?php
interface EventHandler
{
	public function on_create();
	public function on_update();
	public function on_delete();
	public function on_after_delete();
}

interface PostProcessFilter
{
	public function i18n();
}

interface ACLControl
{
	public function on_request($module);
	public function get_login_url();
}
?>
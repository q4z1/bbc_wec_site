<?php
/**
 * application_controller_main_logout
 */
class controller_main_logout extends controller_main_base
{
	public function __construct()
	{
		parent::__construct(__CLASS__);
	}


	public function run()
	{
		$_SESSION = array();
		session_unset();
		session_destroy();
		header("Location: " . cfg::$web_root);
	}
}

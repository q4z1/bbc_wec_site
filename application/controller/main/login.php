<?php
/**
 * application_controller_main_login
 */
class controller_main_login extends controller_main_base
{
	public function __construct()
	{
		parent::__construct(__CLASS__);
	}


	public function run()
	{
		view::set_col("maincol", "html/login/login.html");
		$this->generate_html_output();
	}
}

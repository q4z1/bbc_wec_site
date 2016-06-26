<?php
/**
 * application_controller_admin_default
 */
class controller_admin_default extends controller_admin_base
{
	public function __construct()
	{
		parent::__construct(__CLASS__);
	}


	public function run()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");

		view::set_col("maincol", "html/user/admin/main/default.html");
		$this->generate_html_output();
	}
}

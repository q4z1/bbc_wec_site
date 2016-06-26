<?php
/**
 * default controller_main Klasse
 */
class controller_main_default extends controller_main_base
{
	public function __construct()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		parent::__construct(__CLASS__);
	}

	public function run()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		/**
		 * @TODO: default Startseite
		 */
		//view::set_col("maincol", "html/user/admin/main/default.html");
		$this->generate_html_output();
		// stop JS-Timeouts/-Intervals
		if(app::$session == "visitor")
		{
      return;
		}
		else
		{
			header('Location: /' . app::$session . '/');
			exit();
		}
	}
}

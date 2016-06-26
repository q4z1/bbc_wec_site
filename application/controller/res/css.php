<?php
/**
 * application_controller_res_css
 *
 * zur Ausgabe von CSS-Dateien
 */
class controller_res_css extends controller_res_base
{
	public function __construct()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		parent::__construct(__CLASS__);
		$this->type = "css";
	}
	
	public function run()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		$this->fetch_resource($this->type);
	}
}

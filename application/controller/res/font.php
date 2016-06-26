<?php
/**
 * application_controller_res_font
 *
 * zur Ausgabe von font-Dateien
 */
class controller_res_font extends controller_res_base
{
	public function __construct()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		parent::__construct(__CLASS__);
		$this->type = "font";
	}
	
	public function run()
	{
		//debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		$this->fetch_resource($this->type);
	}
}

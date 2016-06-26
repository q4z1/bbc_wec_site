<?php
/**
 * application_controller_main_base
 *
 */
class controller_main_base extends controller_base
{
	public function __construct($class)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		parent::__construct($class);
	}
}

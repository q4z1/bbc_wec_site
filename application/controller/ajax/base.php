<?php
/**
 * application_controller_ajax_base
 *
 */
class controller_ajax_base extends controller_base
{
	public function __construct($class)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		parent::__construct($class);
		header('Content-Type: text/html; charset=utf8');
	}
}

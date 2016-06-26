<?php
/**
 * application_controller_admin_base
 *
 */
class controller_admin_base extends controller_base
{
	public function __construct($class)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		if(app::$session != "admin")
		{
			$location = (app::$session != "visitor") ? app::$session : 'main';
			header('Location: /' . $location . '/');
			exit();
		}
		parent::__construct($class);
	}
}

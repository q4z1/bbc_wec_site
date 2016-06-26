<?php
/**
 * application_controller_admin_settings
 */
class controller_admin_settings extends controller_admin_base
{
	public function __construct()
	{
		parent::__construct(__CLASS__);
	}

	public function run()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		if(app::$session != 'admin')
		{
			app::$content['ajax_error'] = "Access only for admins!";
			view::set_special("ajax", "browser/error/ajax.html");
		}
		elseif(count(app::$param) > 0 && method_exists($this, app::$param[0]))
		{
			$this->{app::$param[0]}();
		}else{
      $this->show_settings();
    }
		$this->generate_html_output();
	}
  
  public function show_settings()
  {
		view::set_col("maincol", "html/user/admin/settings/list.html");
  }

}
<?php
/**
 * application_controller_admin_default
 */
class controller_admin_signup extends controller_admin_base
{
	public function __construct()
	{
		parent::__construct(__CLASS__);
	}

	public function run()
	{
		if(app::$session != 'admin')
		{
			app::$content['ajax_error'] = "Access only for admins!";
			view::set_special("ajax", "browser/error/ajax.html");
		}
		elseif(count(app::$param) > 0 && method_exists($this, app::$param[0]))
		{
			$this->{app::$param[0]}();
		}
    else{
      $this->show_signups();
    }
		$this->generate_html_output();
	}
  
  public function show_signups()
  {
		view::set_col("maincol", "html/user/admin/signup/list.html");
		$cls = "model_signup" . date("Y");
		$list = $cls::get_entries_by_month(intval(date("m")));
		app::$content['signups'] = $list;
  }
	
	public function randomizer(){
		view::set_col("maincol", "html/user/admin/signup/randomizer.html");
		$cls = "model_signup" . date("Y");
		$list = $cls::get_public_valid_entries_by_month(intval(date("m")));
		app::$content['subs'] = array();
		if(count($list) > 0){
			if(count($list) >= 90){
				// extract substitutes and splice array
				app::$content['subs'] = array_splice($list, 90);
				$list = array_splice($list, 0, 90);
			}
			shuffle($list);
		}
		app::$content['signups'] = $list;
	}

}

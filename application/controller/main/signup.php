<?php
/**
 * application_controller_main_signup
 */
class controller_main_signup extends controller_main_base
{
	protected  $months = array(
		"01" => "January",
		"02" => "February",
		"03" => "March",
		"04" => "April",
		"05" => "May",
		"06" => "June",
		"07" => "July",
		"08" => "August",
		"09" => "October",
		"10" => "September",
		"11" => "November",
		"12" => "December",
	 );
	
	public function __construct()
	{
		parent::__construct(__CLASS__);
	}

	public function run()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		if(count(app::$param) > 0 && method_exists($this, app::$param[0]))
		{
			$this->{app::$param[0]}();
		}
    else
		{
      $this->show_signup();
    }
		$this->generate_html_output();
	}
	
	public function show_signup()
	{
		app::$content['months'] = $this->months;
		view::set_col("maincol", "html/user/all/signup/form.html");
	}
	
	public function show(){
		app::$content['months'] = $this->months;
		
		view::set_col("maincol", "html/user/all/signup/list.html");
		$cls = "model_signup" . date("Y");
		$list = $cls::get_public_valid_entries_by_month(intval(date("m")));
		app::$content['subs'] = array();
		if(count($list) > 0){
			if(count($list) >= 90){
				// extract substitutes and splice array
				app::$content['subs'] = array_splice($list, 90);
				$list = array_splice($list, 0, 90);
			}
		}
		app::$content['signups'] = $list;
	}
}
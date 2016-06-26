<?php
/**
 * default controller_main Klasse
 */
class controller_main_shoutbox extends controller_main_base
{
	public function __construct()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		parent::__construct(__CLASS__);
	}

	public function run()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		if(count(app::$param) > 0 && method_exists($this, app::$param[0]))
		{
			$this->{app::$param[0]}();
		}
    else{
      $this->def();
    }
		$this->generate_html_output();
	}
  
  public function def(){
    view::set_col("maincol", "html/user/all/shoutbox/shoutbox.html");
  }
}

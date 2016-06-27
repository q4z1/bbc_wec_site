<?php
/**
 * controller_main_games Klasse
 */
class controller_main_games extends controller_main_base
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
    // @TODO: get upcoming step 1 game & and maybe define links for the game navigation
    app::$content['stepgame'] = "Step 1 - Mon, 27th June 2016 - 19:30 CEST";
    view::set_col("maincol", "html/user/all/games/games.html");
  }
}

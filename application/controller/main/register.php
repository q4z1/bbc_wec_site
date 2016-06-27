<?php
/**
 * register controller_main Klasse
 */
class controller_main_register extends controller_main_base
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
    $step1 = model_gamedates::get_upcoming_dates(1);
    $step2 = model_gamedates::get_upcoming_dates(2);
    $step3 = model_gamedates::get_upcoming_dates(3);
    $step4 = model_gamedates::get_upcoming_dates(4);
    $games = array();
    if(!is_null($step1)) $games = array_merge($games, $step1);
    if(!is_null($step2)) $games = array_merge($games, $step2);
    if(!is_null($step3)) $games = array_merge($games, $step4);
    if(!is_null($step4)) $games = array_merge($games, $step4);
    app::$content['games'] = $games;
    view::set_col("maincol", "html/user/all/register/games.html");
	}
  
  function dereg(){
    die("@TODO: implement dereg");
  }
}

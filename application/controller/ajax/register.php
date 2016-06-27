<?php
/**
 * application_controller_ajax_register
 */
class controller_ajax_register extends controller_ajax_base
{
	protected $output_type;

	public function __construct()
	{
		$this->output_type = "ajax";
	}

	public function run()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
    view::set_special("ajax", "browser/ajax/default.html");
		app::$content['ajax'] = "Method not found!";
		if(count(app::$param) > 0 && method_exists($this, app::$param[0]))
		{
			$this->{app::$param[0]}();
		}
		$this->generate_special_output($this->output_type);
	}
  
  public function games()
  {
			view::set_special("ajax", "browser/ajax/modal.html");
      
      // @XXX: check if fingerprint is empty or banned
      $fp = (array_key_exists("fp", app::$request)) ? app::$request['fp'] : '';
      if($fp == ''){
        app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
        app::$content['modal']["content"] = "Please contact an admin for this issue!";
        return;
      }else if(!is_null(model_banlist::is_fp_banned($fp))){
        app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
        app::$content['modal']["content"] = "You are not allowed to register for a game!";
        return;
      }
      
      // @XXX: check for empty values
      if(!array_key_exists("playername", app::$request) || app::$request['playername'] == '' || !array_key_exists("dates", app::$request) || app::$request['dates'] == ''){
        app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
        app::$content['modal']["content"] = "Empty values posted!";
        return;
      }

      $playername = app::$request['playername'];
      $status = 1;

      // @TODO: 1st check if playername exists - if not: mail to admins - manual accept reg and create player-table entry
      if(is_null(model_player::get_entry_by_playername($playername))){
        // @TODO: send email for manual accept reg
        $status = 0;
      }
      
      $alrdy_reg = array();
      $deregs = array();
      $dates = explode(",", app::$request['dates']);
      foreach($dates as $date_id){
        $gd = model_gamedates::get_entry_by_id($date_id);
        // @XXX: check if reg is already done
        if(!is_null($gr = model_gameregs::get_entry_by_player_date_step($playername, $gd->date, $gd->step))){
          $alrdy_reg[] = "Step " . $gd->step . " - " .date("D, jS \of F Y H:i", strtotime($gd->date)) . " CEST";
        }else{
          $gr = new model_gameregs();
          $gr->playername = $playername;
          $gr->ip = $_SERVER["REMOTE_ADDR"];
          $gr->fingerprint = $fp;
          $gr->date = $gd->date;
          $gr->step = $gd->step;
          $gr->status = $status;
          // pseudo random dereg code.
          $ta = preg_split("//",time());
          shuffle($ta);
          $gr->dereg = substr(md5(implode($ta)), 0, 8);
          $gr->save();
          $deregs[$gr->dereg] = "Step " . $gd->step . " - " .date("D, jS \of F Y H:i", strtotime($gd->date)) . " CEST";
        }
      }
      
      // @TODO: format output = show already registered games - show dereg codes for each newly registered game

      
			app::$content['modal']["heading"] = "<div class='text-success'>Success!</div>";
			app::$content['modal']["content"] = "register fo game(s) called!";
      app::$content['modal']["content"] = var_export($alrdy_reg, true)."<hr/>".var_export($deregs, true);
      app::$content['modal']['footer'] = "button to close"; // @TODO: button to close!
  }
}

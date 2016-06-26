<?php
/**
 * application_controller_ajax_default
 */
class controller_ajax_award extends controller_ajax_base
{
	protected $output_type;

	public function __construct()
	{
		$this->output_type = "ajax";
	}

	public function run()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		app::$content['ajax'] = "";
		if(app::$session != 'admin')
		{
			app::$content['ajax_error'] = "Access only for admins!";
			view::set_special("ajax", "browser/error/ajax.html");
		}
		elseif(count(app::$param) > 0 && method_exists($this, app::$param[0]))
		{
			$this->{app::$param[0]}();
		}
		$this->generate_special_output($this->output_type);
	}
  
  public function delete()
  {
			view::set_special("ajax", "browser/ajax/modal.html");

			$id = app::$request['id'];

      $cls = "model_award" . date("Y");
      $awrd = $cls::get_entry_by_id($id);
      if(is_null($awrd)){
				app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
				app::$content['modal']["content"] = "An Award with id $id not found!";
				return;
      }
      
      // @TODO: remove assignments from each player !!!
      
      $cls::delete_entry_by_id($id);
			app::$content['modal']["heading"] = "<div class='text-success'>Success!</div>";
			app::$content['modal']["content"] = "The award with id $id has been deleted!";
  }
  
  public function assign(){
    view::set_special("ajax", "browser/ajax/modal.html");

    if(!array_key_exists("json", app::$request)){
				app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
				app::$content['modal']["content"] = "No json data given!";
				return;
    }
    
    $jObj = app::$request['json'];
   
    $awrd_id = $jObj->award_id;
    $players = $jObj->players;
    if(count($players) == 0){
				app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
				app::$content['modal']["content"] = "No players selected!";
				return;
    }
    
    $cls = "model_award" . date("Y");
    $awrd = $cls::get_entry_by_id($awrd_id);
    foreach($players as $player){
      $cls = "model_player" . date("Y");
      $ply = $cls::get_entry_by_id($player);
      if(is_null($ply->awards) || $ply->awards == ""){
        $awards = array();
      }
      else{
        $awards = json_decode($ply->awards);
      }
      $do = true;
      if(count($awards) > 0){
        foreach($awards as $aw){
          if($aw->month == $awrd->month && $aw->type == $awrd->type){
            $do = false;
          }
        }
      }
      if($do)
      {
        $awards[] = array("month" => $awrd->month, "type" => $awrd->type);
        $ply->awards = json_encode($awards);
        $ply->save();
      }

    }

    
    app::$content['modal']["heading"] = "<div class='text-success'>Success!</div>";
    app::$content['modal']["content"] = "The award <strong class='text-primary'>{$awrd->filename}</strong> has been assigned!";

  }
	
}

<?php
/**
 * application_controller_ajax_default
 */
class controller_ajax_upload extends controller_ajax_base
{
	protected $output_type;


	
	public function __construct()
	{
		$this->output_type = "ajax";
		$this->points = json_decode(app::$settings['points']);
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
  
  public function def()
  {
			view::set_special("ajax", "browser/ajax/modal.html");

			if(!array_key_exists("logurl", app::$request) || !array_key_exists("step", app::$request) || app::$request['logurl'] == ""){
					app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
					app::$content['modal']["content"] = "Missing parameters!";
					return;
			}
			
			$pth = pthlog::process_log(app::$request['logurl']);
			$res = new model_results();
			$res->date = date("Y-m-d H:i:s"); // @FIXME: datetime has to be set in upload form!!!
			$res->type = app::$request['step'];
			$res->{"1st"} = $pth['result'][1]['player'];
			$res->{"2nd"} = $pth['result'][2]['player'];
			$res->{"3rd"} = $pth['result'][3]['player'];
			for($i=4;$i<=10;$i++){
				$res->{$i."th"} = $pth['result'][$i]['player'];
			}
			$res->log = serialize($pth);
			$res->save();
			$input = '<input type="hidden" name="game_id" id="game_id" value="' . $res->results_id . '" />';
			app::$content['modal']["heading"] = "<div class='text-success'>Success!</div>";
			app::$content['modal']["content"] = $input . "Game <strong>#" . $res->results_id . "</strong> uploaded!";

  }
	
	
	public function award()
  {
		view::set_special("ajax", "browser/ajax/modal.html");
		
		if(!is_array($_FILES) || !array_key_exists("file", $_FILES)){
			app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
			app::$content['modal']["content"] = "No image file received!";
			return;
		}
		
		$blob = addslashes(file_get_contents($_FILES['file']['tmp_name']));
		$filename = $_FILES['file']['name'];
		$mime = $_FILES['file']['type'];
		
		$cls = "model_award" . date("Y");
		
		if(!is_null($cls::get_award_by_month_type(intval(app::$request['month']), app::$request['type'])))
		{
			app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
			app::$content['modal']["content"] = "This award has alread been uploaded!";
			return;
		}
		
		$award = new $cls();
		$award->month = intval(app::$request['month']);
		$award->type = app::$request['type'];
		$award->file = $blob;
		$award->filename = $filename;
		$award->mime = $mime;
		$award->save();
		
		unlink($_FILES['file']['tmp_name']);
		
		app::$content['modal']["heading"] = "<div class='text-success'>Success!</div>";
		app::$content['modal']["content"] = "Award $filename successfully uploaded!";
	}
}

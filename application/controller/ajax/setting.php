<?php
/**
 * application_controller_ajax_setting
 */
class controller_ajax_setting extends controller_ajax_base
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
  
  public function points()
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
  
  public function links(){
    view::set_special("ajax", "browser/ajax/modal.html");

 

    
    app::$content['modal']["heading"] = "<div class='text-success'>Success!</div>";
    app::$content['modal']["content"] = "The award <strong class='text-primary'>{$awrd->filename}</strong> has been assigned!";

  }
  
  public function footer(){
    view::set_special("ajax", "browser/ajax/modal.html");

 

    
    app::$content['modal']["heading"] = "<div class='text-success'>Success!</div>";
    app::$content['modal']["content"] = "The award <strong class='text-primary'>{$awrd->filename}</strong> has been assigned!";

  }
  
  public function dates(){
    view::set_special("ajax", "browser/ajax/modal.html");

 

    
    app::$content['modal']["heading"] = "<div class='text-success'>Success!</div>";
    app::$content['modal']["content"] = "The award <strong class='text-primary'>{$awrd->filename}</strong> has been assigned!";

  }
	
}

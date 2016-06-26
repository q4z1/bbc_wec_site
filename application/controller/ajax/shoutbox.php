<?php
/**
 * application_controller_ajax_shoutbox
 */
class controller_ajax_shoutbox extends controller_ajax_base
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
		if(count(app::$param) > 0 && method_exists($this, app::$param[0]))
		{
			$this->{app::$param[0]}();
		}
		$this->generate_special_output($this->output_type);
	}
  
  public function post()
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
      app::$content['modal']["content"] = "You are not allowed to post here!";
      return;
    }
    // @XXX: if 'to' is posted, check if admin-session - if not: decline post!
    $status = 1;
    if(array_key_exists("to", app::$request) && app::$request['to'] != ''){
      if(app::$session != "admin"){
        app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
        app::$content['modal']["content"] = "It is not allowed to post as an admin, if not logged in as admin!";
        return;
      }
      $status = (app::$request['to'] == "admin") ? 3 : 2;
    }
    // @XXX: validate params
    if(!array_key_exists("msg", app::$request) || app::$request['msg'] == ''){
      app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
      app::$content['modal']["content"] = "No message entered!";
      return;
    }
    if(!array_key_exists("nickname", app::$request) || app::$request['nickname'] == ''){
      app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
      app::$content['modal']["content"] = "No name entered!";
      return;
    }

    $sb = new model_shoutbox();
    $sb->created = date("Y-m-d H:i:s");
    $sb->playername = app::$request['nickname'];
    $sb->ip = $_SERVER["REMOTE_ADDR"];
    $sb->fingerprint = $fp;
    $sb->msg = app::$request['msg'];
    $sb->status = $status;
    $sb->save();
    
    app::$content['modal']["heading"] = "<div class='text-success'>Success!</div>";
    app::$content['modal']["content"] = "Message succesfully posted!";
  }
  
  public function posts(){
    view::set_special("ajax", "browser/ajax/default.html");
    $start = 0;
    $end = 50;
    if(array_key_exists("start", app::$request) && is_numeric(app::$request['start']) && array_key_exists("end", app::$request) && is_numeric(app::$request['end'])){
      $start = app::$request['start'];
      $end = app::$request['end'];
    }
    app::$content['ajax'] = json_encode(model_shoutbox::get_posts_with_limit($start, $end));

  }
	
}

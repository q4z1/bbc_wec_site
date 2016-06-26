<?php
/**
 * application_controller_res_pic
 *
 * zur Ausgabe von PIC-Dateien, die vom webservice geholt werden
 *
 * @XXX: die Basis-Funktionj fetch_resource muss hierbei überschrieben werden
 */
class controller_res_avatar extends controller_res_base
{
	public function __construct()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		parent::__construct(__CLASS__);
		$this->type = "pic";
	}
	
	public function run()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		$this->fetch_resource($this->type);
	}
	
	public function fetch_resource($type)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		$param = app::$param;
    $req = app::$request;
    if(!array_key_exists("playername", $req)){
			header("HTTP/1.0 404 Not Found");
			return;
    }
    $player = $req['playername'];
    
    $cls = "model_player".date("Y");
    $avatar = $cls::get_entry_by_playername($player);
    
    if(is_null($avatar->avatar_mime) || $avatar->avatar_mime == ""){
			header("HTTP/1.0 404 Not Found");
			return;
    }
    
		header("Content-Type: ".$avatar->avatar_mime);
		app::$output = stripslashes($avatar->avatar);
		return;
	}
}

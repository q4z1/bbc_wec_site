<?php
/**
 * application_controller_res_pic
 *
 * zur Ausgabe von PIC-Dateien, die vom webservice geholt werden
 *
 * @XXX: die Basis-Funktionj fetch_resource muss hierbei überschrieben werden
 */
class controller_res_award extends controller_res_base
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
    if(!array_key_exists("type", $req) && !array_key_exists("month", $req)){
			header("HTTP/1.0 404 Not Found");
			return;
    }
    $type = $req['type'];
    $month = intval(date("m"));
    if(array_key_exists("month", $req)){
      $month = intval($req['month']);
    }
    
    $cls = "model_award".date("Y");
    $awrd = $cls::get_entry_by_month_type($month, $type);
    
		header("Content-Type: ".$awrd->mime);
		app::$output = stripslashes($awrd->file);
		return;
	}
}

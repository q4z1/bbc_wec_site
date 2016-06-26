<?php
/**
 * application_controller_ajax_notice
 */
class controller_ajax_notice extends controller_ajax_base
{
	public function __construct()
	{
		//parent::__construct(__CLASS__);
		$this->output_type = "ajax";
	}


	public function run()
	{
		if(count(app::$param) > 0 && method_exists($this, app::$param[0]))
		{
			$this->{app::$param[0]}();
		}else {
      $this->def();
    }
		$this->generate_special_output($this->output_type);
  }
  
  private function def(){
    if(array_key_exists("notice", $_SESSION) && $_SESSION['notice'] != ""){
      app::$content['modal']["heading"] = "<div class='text-warning'>Notice!</div>";
      app::$content['modal']["content"] = $_SESSION['notice'];
      view::set_special("ajax", "browser/ajax/modal.html");
      $_SESSION['notice'] = "";
      return;
    }
    app::$content['ajax'] = "None.";
    view::set_special("ajax", "browser/ajax/default.html");
    return;
  }
}
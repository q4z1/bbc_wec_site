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
  
  public function game()
  {
			view::set_special("ajax", "browser/ajax/modal.html");

			app::$content['modal']["heading"] = "<div class='text-success'>Success!</div>";
			app::$content['modal']["content"] = "register fo a game called!";
  }
}

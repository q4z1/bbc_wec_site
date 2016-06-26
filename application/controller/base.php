<?php
/**
 * application_controller_base
 *
 * Basis Klasse controller
 */
class controller_base extends base
{
	public function __construct($class)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		app::init_controller($class);
	}

	public function __destruct()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
	}

	public function generate_html_output()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		/*
		 * @FIXME: tidy vs font-awesome issues :(
		 * disabled tidy
		 */
		//ob_start();
		//require_once(view::get_special("core"));
		//app::$tidy->parseString(ob_get_contents(), app::$tidy_opts);
		//ob_end_clean();
		//app::$tidy->cleanRepair();
		//app::$output = app::$tidy;
		ob_start();
		require_once(view::get_special("core"));
		app::$output = ob_get_contents();
		ob_end_clean();


	}

	public function generate_special_output($type)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		ob_start();
		require_once(view::get_special($type));
		app::$output = ob_get_contents();
		ob_end_clean();
	}

	public function generate_cli_output()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		ob_start();
		require_once(view::get_special("core"));
		app::$output = ob_get_contents();
		ob_end_clean();
	}

	protected function add_controller_inc($class)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($class) betreten.");
		if(cfg::$template != "default")
		{
			$template_js = model_controllerinc::get_inc_by_template_controller_type
			(
				cfg::$template, $class, 'js'
			);
			app::$inc->js = array_unique(array_merge(app::$inc->js, $template_js));
			$template_css = model_controllerinc::get_inc_by_template_controller_type
			(
				cfg::$template, $class, 'css'
			);
			app::$inc->css = array_unique(array_merge(app::$inc->css, $template_css));
		}
		$template_js = model_controllerinc::get_inc_by_template_controller_type
		(
			"default", $class, 'js'
		);
		app::$inc->js = array_unique(array_merge(app::$inc->js, $template_js));

		$template_css = model_controllerinc::get_inc_by_template_controller_type
		(
			"default", $class, 'css'
		);
		app::$inc->css = array_unique(array_merge(app::$inc->css, $template_css));
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($class):<br /><b>app::\$inc:</b><br /><pre>".var_export(app::$inc, true)."</pre>");
	}

}

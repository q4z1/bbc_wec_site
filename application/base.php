<?php
/**
 * application_base
 *
 * Application Basis-Klasse
 */
class base
{
	public static $session;

	public static $config;
	public static $param;
	public static $request;

	public static $tidy;
	public static $tidy_opts;

	public static $inc;

	public static $notice;

	public static $controller;
	public static $content;
	
	public static $settings;

	public static $output;

	public static function init()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		self::$content = array();
		self::check_session();
		self::set_template();
		self::read_configuration();
	}

	private static function check_session()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		self::$session = "visitor";
		if(session_id() == "")
		{
			session_start();
		}
		if(array_key_exists('type', $_SESSION) === true)
		{
			if($_SESSION['type'] == 'admin')
			{
				self::$session = 'admin';
			}
		}
		// debug::add_info
		(
				"(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."()<br />"
			. "<b>self::\$session:</b><br /><pre>"
			. var_export(self::$session, true) . "</pre>"
		);
		// debug::add_info
		(
				"(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."()<br />"
			. "<b>\$_SESSION:</b><br /><pre>"
			. var_export($_SESSION, true) . "</pre>"
		);
		return;
	}

	public static function read_configuration()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		self::$settings = array();
		$settings = model_settings::get_all_entries();
		foreach($settings as $set){
			self::$settings[$set->type] = $set->value;
		}
		$config_array = model_configuration::get_all_entries();
		if(is_null($config_array))
		{
			throw new Exception("Tabelle configuration enthält keine Einträge!");
		}
		self::$config = new stdClass;
		self::$tidy_opts = array();
		foreach($config_array as $entry)
		{
			/**
			 * @TODO: hier können weitere Konfigurations-Gruppen berücksichtigt werden
			 */
			if($entry->group == "head")
			{
				if(!property_exists(self::$config, $entry->group))
				{
					self::$config->{$entry->group} = new stdClass;
					self::$config->{$entry->group}->{$entry->key} = array();
				}
				self::$config->{$entry->group}->{$entry->key}[] = $entry->value;
			}
			else
			{
				if(!property_exists(self::$config, $entry->group))
				{
					self::$config->{$entry->group} = new stdClass;
				}
				self::$config->{$entry->group}->{$entry->key} = $entry->value;
			}
		}
		// debug::add_info
		(
				"(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."():<br />"
			.	"<b>self::\$config</b>:<br /><pre>"
			. var_export(self::$config, true) . "</pre>"
		);
	}

	/*
	 * init_controller()
	 *
	 * @param String $controller
	 * - $controller ist der aufrufende Controller
	 *
	 * - stellt die Prozess-Art fest
	 * - holt benötigte Einstellungen aus der configuration Tabelle
	 * - setzt erforderliche <head>-Includes bei einem Browser-Prozess
	 */
	public static function init_controller($controller)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($controller) betreten.");

		self::$controller = str_replace("controller_", "", $controller);

		/**
		 * Zugriffs-Kontrolle der Sitzung
		 */
		self::access_control();

		/*
		 * falls es sich um einen Browser-Request handelt:
		 *
		 * setze JS-/CSS-Includes für den <head>
		 *
		 */
		if(cfg::$proc == "browser")
		{
			self::set_controller_inc($controller);
			self::set_base_html_layout();
		}
		elseif(cfg::$proc == "cli")
		{
			self::set_base_cli_layout();
		}
		return;
	}

	private static function set_controller_inc($controller)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($controller) betreten.");
		self::$inc = new stdClass();
		foreach(self::$config->head->css as $css)
		{
			self::$inc->css[] = $css;
		}
		foreach(self::$config->head->js as $js)
		{
			self::$inc->js[] = $js;
		}
		/*
		 * setze JS- und CSS-Einträge für den aufrufenden controllers
		 */
		$default_js = model_controllerinc::get_entries_by_template_controller_type
		(
			"default", $controller, 'js'
		);
		if(!is_null($default_js))
		{
			self::$inc->js = array_unique(array_merge(self::$inc->js, $default_js));
		}
		$default_css = model_controllerinc::get_entries_by_template_controller_type
		(
			"default", $controller, 'css'
		);
		if(!is_null($default_css))
		{
			self::$inc->css = array_unique(array_merge(self::$inc->css, $default_css));
		}
		if(cfg::$template != "default")
		{
			$template_js = model_controllerinc::get_entries_by_template_controller_type
			(
				cfg::$template, $controller, 'js'
			);
			if(!is_null($template_js))
			{
				self::$inc->js = array_unique(array_merge(self::$inc->js, $template_js));
			}
			$template_css =	model_controllerinc::get_entries_by_template_controller_type
			(
				cfg::$template, $controller, 'css'
			);
			if(!is_null($template_css))
			{
				self::$inc->css = array_unique(array_merge(self::$inc->css, $template_css));
			}
		}
		// debug::add_info
		(
				"(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."():<br />"
			.	"<b>self::\$inc</b>:<br /><pre>"
			.	var_export(self::$inc, true) . "</pre>"
		);
	}

	private static function set_base_html_layout()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		/**
		 * @TODO: Standard-Template Elemente werden hier festgelegt
		 */
		view::set_col("head", "html/core/head/head.html");
		view::set_special("logo", "html/core/head/logo.html");
		view::set_special("core", "html/core/core.html");
		view::set_special("inc", "browser/inc/inc.html");
		if(self::$session == "visitor")
		{
			if(self::$controller == "main_default"){
				view::set_col("maincol", "html/user/all/default/default.html");
			}
			view::set_special("righthead", "html/core/head/navi_visitor.html");
		}
		else
		{
			view::set_special("righthead", "html/core/head/navi.html");
		}
		view::set_col("foot",  "html/core/foot/foot.html");
		if(cfg::$debug)
		{
			view::set_special("debug", "browser/debug/debug.html");
		}
	}

	private static function set_base_cli_layout()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		if(cfg::$debug)
		{
			view::set_special("debug", "cli/debug/debug.cli");
		}
	}

	public static function output()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		echo self::$output;
	}

	private static function access_control()
	{
		/*
		 * @TODO: ausreichendes Procedere?
		 */
		$controller = explode("_", self::$controller);
		$group = $controller[0];
		if($group == "webservice" || self::$session == "admin")
		{
			// admin: no access limitation, webservice-call: always allowed
			return;
		}
		if($group != "main" && $group != "siteop" && self::$session == "visitor")
		{
			// main-call: always allowed, visitor: access limited
			$_SESSION['notice']['default'] = "Die Sitzung ist abgelaufen!";
			Header("Location: " . cfg::$web_root);
			exit();
		}
		elseif(self::$session == "moderator" && $group != self::$session && strpos(self::$controller, "_logout") === false)
		{
			// moderator: access only to /moderator/... (or main/logout)
			Header("Location: " . cfg::$web_root . self::$session);
			exit();
		}
		elseif(self::$session == "siteop" && $group != self::$session && strpos(self::$controller, "_logout") === false && strpos(self::$controller, "chart") === false)
		{
			// moderator: access only to /moderator/... (or main/logout)
			Header("Location: " . cfg::$web_root . self::$session);
			exit();
		}
	}

	/**
	 * set_template()
	 *
	 * Legt das Template anhand der Subdomain fest
	 * @XXX: falls keine valide Subdomain vorhanden, wird der Parameter 'template'
	 * abgeprüft und verwendet
	 */
	private static function set_template()
	{
		if(!array_key_exists('template', $_SESSION))
		{
			$invalid_subdomains = array("www", "vcvps2142");
			cfg::$template = "default";
			$num_match = preg_match
			(
				$pattern = "/^([^\.]+)\.([^\.]+)\.([^\.]+)$/",
				$subject = $_SERVER['SERVER_NAME'],
				$match
			);
			if($num_match == 1 && !in_array($match[1], $invalid_subdomains) && is_dir(TMPL_DIR . $match[1]))
			{
				cfg::$template = $match[1];
			}
			$_SESSION['template'] = cfg::$template;
		}
		if(array_key_exists("template", $_REQUEST) && is_dir(TMPL_DIR . $_REQUEST['template']))
		{
			cfg::$template = $_REQUEST['template'];
			$_SESSION['template'] = cfg::$template;
		}
		cfg::$template = $_SESSION['template'];
	}

}

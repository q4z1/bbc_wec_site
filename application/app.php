<?php
/**
 * Main Application class
 */
class app extends base
{

	public static function run($request)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		/**
		 * Auswertung der REQUEST_URI
		 */
		self::$request = $request;
		// debug::add_info("Auswertung REQUEST_URI:");
		self::$param = null;
		$controller = "main";
		$action = "default";
		if(!is_null(self::$request['uri']) && strlen(self::$request['uri']) > 1)
		{
			/*
			 * extrahiere Controller, Aktion und Parameter
			 */
			$uri = explode("/", self::$request['uri']);
			$controller = strtolower($uri[1]);
			if(isset($uri[2]) && strlen($uri[2]) > 0)
			{
				$action = strtolower($uri[2]);
			}
			if(count($uri) > 3)
			{
				self::$param = array();
				for($i = 3; $i < count($uri); $i++)
				{
					self::$param[] = $uri[$i];
				}
			}
		}
		$class_name = 'controller_' . $controller . '_' . $action;
		// debug::add_info("<b>" . $class_name . "</b> als Aktion definiert.");
		self::init();
		unset($controller, $action, $uri, $request);
		$application = new $class_name();
		$application->run();
		/* Ausgabe - je nach $proc (browser|cli) */
		self::output();
		debug::file_output();
		return;
	}
}

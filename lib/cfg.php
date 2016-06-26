<?php
/**
 * lib_cfg
 *
 * Klasse für die Bereitstellung der erforderlichen
 * Konfigurations-Daten
 *
 *
 */
class cfg
{
	/*
	 * Definition der verfügbaren Konfigurations-Werte
	 */
	private static $config_file;
	public static $web_root;
	public static $debug;
	public static $debug_file;
	public static $db_host;
	public static $db_name;
	public static $db_user;
	public static $db_pass;
	public static $proc;
	public static $template;

	public static function init()
	{
		/*
		 * Zuordnung der Prozessart
		 * -> Unterscheidung zwischen CLI-
		 * und Browser-Aufruf
		 */
		self::$proc = "browser";
		if(defined('STDIN'))
		{
			self::$proc = "cli";
		}
		self::$config_file = ROOT_DIR . "etc/config.php";
		if(!(file_exists(self::$config_file)))
		{
			throw new Exception
			(
					"Konfigurations-Datei "
				. self::$config_file
				. " nicht gefunden!"
			);
		}
		else
		{
			require_once(self::$config_file);
		}

		/*
		 * Zuordnung der Konfigurations-Werte
		 */
		self::$debug = $debug;
		self::$debug_file = $debug_file;
		self::$web_root = $web_root;
		self::$db_host = $db_host;
		self::$db_name = $db_name;
		self::$db_user = $db_user;
		self::$db_pass = $db_pass;

		debug::init();
		// debug::add_info("(" . __FILE__ . ")<b>" . __CLASS__ . "</b>::" . __FUNCTION__ . "(): Konfigurations-Datei eingelesen.");
	}


}

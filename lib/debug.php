<?php
/**
 * lib_debug
 *
 * Debug Klasse
 */
class debug
{
	private static $_instance = null;

	private static $debug_info;
	private static $start_time;
	private static $end_time;
	private static $start_mtime;
	private static $end_mtime;

	public static function init()
	{
		if(cfg::$debug || cfg::$debug_file)
		{
			self::$start_time = date("H:i:s");
			self::$start_mtime = microtime($get_as_float=true);
			self::$debug_info = array();
		}
	}

	public static function output()
	{
		if(cfg::$debug)
		{
			echo self::get_debug_output();
		}
	}

	public static function file_output()
	{
		if(cfg::$debug_file)
		{
			self::$end_time = date("H:i:s");
			self::$end_mtime = microtime($get_as_float=true);
			file_put_contents("/tmp/monthly_".date("Y-m-d").".log", self::debug_info_cli(), FILE_APPEND);
		}
	}

	public static function add_info($msg)
	{
		if(cfg::$debug || cfg::$debug_file)
		{
			$mt = microtime($get_as_float=true);
			self::$debug_info[] = array("t" => date("H:i:s"), "mt" => "$mt", "msg" => $msg);
		}
	}

	private static function get_debug_output()
	{
		self::$end_time = date("H:i:s");
		self::$end_mtime = microtime($get_as_float=true);
		$debug_info_string = "";
		switch(cfg::$proc)
		{
			case("browser"):
				$debug_info_string = self::debug_info_browser();
				break;
			case("cli"):
				$debug_info_string = self::debug_info_cli();
				break;
			default:
				$debug_info_string = self::debug_info_browser();
				break;
		}
		return $debug_info_string;
	}

	private static function debug_info_browser()
	{
		if(count(self::$debug_info) < 1)
		{
			return "";
		}
		$debug_info = "<h3 class=\"heading\">Start: <b>" . date("Y-m-d") . " "
								. self::$start_time . "</b></h3>\n<br />\n";
		$debug_info .= "<ol>\n";
		foreach(self::$debug_info as $info)
		{
			$debug_info .= "<li><b class=\"time\">" . $info['t'] . "</b> - <b class=\"microtime\">"
									. $info['mt'] . "</b>: ". $info['msg'] . "</li>\n";
		}
		$debug_info .= "</ol>\n";
		$debug_info .= "<h3 class=\"heading\">Ende: <b>" . self::$end_time . " : "
								. self::$end_mtime . "</b></h3>\n<br />\n";
		$debug_info .= sprintf("<h3 class=\"duration\">Dauer: <b>%f</b></h3>\n",self::duration_ms());
		return $debug_info;
	}

	private static function debug_info_cli()
	{
		if(count(self::$debug_info) < 1)
		{
			return "";
		}
		$debug_info = "Debug:\n----------------------\n";
		$debug_info .= "Start: " . date("Y-m-d") . " " . self::$start_time . "\n";
		$debug_info .= "----------\n";
		$i = 1;
		foreach(self::$debug_info as $info)
		{
			$mt = $info['mt'];
			$info['msg'] = str_replace(array("<br />", "<br>"), array("\n", "\n"), $info['msg']);
			$info['msg'] = strip_tags($info['msg']);
			$debug_info .= sprintf("%03s. ", $i) . $info['t'] . " - " . $info['mt'] . ": "
									. $info['msg'] . "\n";
			$i++;
		}
		$debug_info .= "----------\n";
		$debug_info .= "Ende: " . self::$end_time . " : " . self::$end_mtime . "\n";
		$debug_info .= "----------\n";
		$debug_info .= sprintf("Dauer: %f\n",self::duration_ms());
		$debug_info .= "----------------------\n";
		return $debug_info;
	}

	private static function duration_ms()
	{
		return (self::$end_mtime - self::$start_mtime);
	}

}

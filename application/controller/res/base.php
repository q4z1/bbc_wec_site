<?php
/**
 * application_controller_res_base
 *
 * zur Ausgabe von Dateien, die der Browser zur Laufzeit lädt
 */
class controller_res_base extends controller_base
{
	protected $type = null;
	
	public function __construct()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		$filename = app::$param[count(app::$param)-1];
	}

	protected function fetch_resource($type_folder)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		$param = app::$param;
		$tmpl_dir = "";
		if(count($param) > 1){
			$tmpl_dir = $param[0] . "/";
			$param = array_slice($param, 1);
			$filename = implode("/", $param);
		}else{
			$filename = $param[0];
		}
		$content_type = $this->get_content_type($this->type);
		if(file_exists(TMPL_DIR . $tmpl_dir . $this->type . "/" . $filename))
		{
			$real_filename = TMPL_DIR . $tmpl_dir . $this->type . "/" . $filename;
		}
		elseif(file_exists(TMPL_DIR . "default/" . $this->type . "/" . $filename))
		{
			$real_filename = TMPL_DIR . "default/" . $this->type . "/" . $filename;
		}
		else
		{
			/**
			 * @TODO: ggf. default-Dateien?
			 */
			header("HTTP/1.0 404 Not Found");
			return;
		}
		header("Content-Type: $content_type");
		app::$output = file_get_contents($real_filename);
		return;
	}

	private function get_content_type($type)
	{
		switch($type)
		{
			case("css"): $content_type = "text/css"; break;
			case("js"): $content_type = "text/javascript"; break;
			case("png"): $content_type = "image/png"; break;
			case("jpg"): $content_type = "image/jpeg"; break;
			case("jpeg"): $content_type = "image/jpeg"; break;
			case("gif"): $content_type = "image/gif"; break;
			case("font"):
				$filename = app::$param[count(app::$param)-1];
				if(strpos($filename, ".woff2") !== false){
					$content_type = "application/font-woff2";
				}
				elseif(strpos($filename, ".woff") !== false){
					$content_type = "application/font-woff";
				}if(strpos($filename, ".ttf") !== false){
					$content_type = "application/x-font-truetype";
				}elseif(strpos($filename, ".eot") !== false){
					$content_type = "application/vnd.ms-fontobject";
				}elseif(strpos($filename, ".svg") !== false){
					$content_type = "image/svg+xml";
				}elseif(strpos($filename, ".otf") !== false){
					$content_type = "application/x-font-opentype";
				}
				break;
			default: $content_type = "text/plain"; break;
		}
		return $content_type;
	}
}

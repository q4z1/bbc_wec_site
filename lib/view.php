<?php
/**
 * lib_view
 *
 * stellt Funktionen für die Auswahl der Layout-Blöcke
 * zur Verfügung
 *
 */
class view
{
	private static $col_block = array();
	private static $special_block = array();
	public static $missing = null;
	
	/*
	 * set_special()
	 *
	 * @param String $type = Name des Blocks
	 * @param String $block_file = zugehöriger Dateiname
	 */
	public static function set_special($type, $block_file)
	{
		self::$special_block[$type] = $block_file;
	}
	
	/*
	 * get_special($type)
	 *
	 * @param String $type = Name des Blocks
	 *
	 * @return String $filename = Dateiname des Blocks
	 */
	public static function get_special($type)
	{
		$filename = "blank.php";
		if(array_key_exists($type, self::$special_block))
		{
			$filename = self::get_filename(self::$special_block[$type]);
		}
		else
		{
			self::$missing = "View-Datei $type nicht gesetzt!";
		}
		return $filename;
	}
	
	/*
	 * set_col()
	 *
	 * @param String $col = Name des Blocks
	 * @param String $block_file = zugehöriger Dateiname
	 */
	public static function set_col($col, $block_file)
	{
		if(!array_key_exists($col, self::$col_block))
		{
			self::$col_block[$col] = array();
		}
		self::$col_block[$col][] = $block_file;
	}

	/*
	 * get_col()
	 *
	 * @param String $col = Name des Blocks
	 * @param Integer $index = Index des Blocks
	 *
	 * @return String $filename = Dateiname des Blocks
	 */
	public static function get_col($col, $index)
	{
		$filename = "blank.php";
		if(array_key_exists($col, self::$col_block))
		{
			if(!array_key_exists($index, self::$col_block[$col]))
			{
				self::$missing = "Index $index vom View-Block $col nicht gesetzt!";
				return "blank.php";
			}
			$filename = self::get_filename(self::$col_block[$col][$index]);
		}
		else
		{
			self::$missing = "View-Block $col nicht gesetzt!";
		}
		return $filename;
	}
	
	/**
	 * get_filename()
	 *
	 * @param String $block = Bezeichnung des Blocks
	 *
	 * @return String $filename = Dateiname des Blocks
	 *
	 * Info: zuerst wird im im gesetzten Template-Verzeichnis gesucht,
	 * anschl. im default-Template Verzeichnis
	 * abschl. wird im VIEW_DIR gesucht.
	 * So wird sicher gestellt, dass das gesetzte Template-Verzeichnis Vorrang hat!
	 */
	private static function get_filename($block)
	{
		$filename = "blank.php";
		if(file_exists(TMPL_DIR . cfg::$template . "/" . $block . ".php"))
		{
			$filename = cfg::$template . "/" . $block . ".php";
		}
		elseif(file_exists(TMPL_DIR . "default" . "/" . $block . ".php"))
		{
			$filename = "default" . "/" . $block . ".php";
		}
		elseif(file_exists(VIEW_DIR . $block . ".php"))
		{
			$filename = $block . ".php";
		}
		else
		{
			self::$missing = "View-Datei $block nicht gefunden!";
		}
		return $filename;
	}
	
	/**
	 * num_col()
	 *
	 * @param String $col = Bezeichnung der Reihe
	 *
	 * @return Integer $num = Anzahl der gesetzten Blöcke pro Reihe
	 */
	public static function num_col($col)
	{
		$num = 0;
		if(array_key_exists($col, self::$col_block))
		{
			$num = count(self::$col_block[$col]);
		}
		return $num;
	}
}

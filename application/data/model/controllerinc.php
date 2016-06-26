<?php
/**
 * application_data_model_controllerinc
 *
 * Stellt alle Daten der Tabelle controllerinc zur Verfügung
 *
 */
class model_controllerinc extends model_base
{

	public function __construct()
	{
		parent::__construct('controllerinc');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param Integer $controllerinc_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($controllerinc_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($controllerinc_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'controllerinc',
			$filter = array
			(
				'controllerinc_id' => $controllerinc_id
			),
			$single = true
		);
	}
	
	/*
	 * get_entries_by_template_controller_type()
	 *
	 * @param String $template
	 * @param String $controller
	 * @param String $type
	 *
	 * @return Object
	 */
	public static function get_entries_by_template_controller_type($template, $controller, $type)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($template, $controller, $type) betreten.");
		$controller = str_replace("controller_", "", $controller);
		$objects = data_entry::get_by_filter
		(
			$table = 'controllerinc',
			$filter = array
			(
				'template' => $template,
				'controller' => $controller,
				'type' => $type,
				'active' => 1
			),
			$single = false
		);
		$inc = array();
		if(is_array($objects) && count($objects) > 0)
		{
			foreach($objects as $object)
			{
				$inc[] = $object->filename;
			}
		}
		return $inc;
	}

	/*
	 * get_all_entries()
	 *
	 * @return Object
	 */
	public static function get_all_entries()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		return data_entry::get_all('controllerinc', $class=__CLASS__);
	}

	/*
	 * remove_by_id()
	 *
	 * @param Integer $controllerinc_id
	 *
	 * @return MySQLi Result
	 */
	public static function remove_by_id($controllerinc_id)
	{
		return data_entry::remove_by_filter
		(
			$table = 'controllerinc',
			$filter = array
			(
				'controllerinc_id' => $controllerinc_id
			)
		);
	}
}

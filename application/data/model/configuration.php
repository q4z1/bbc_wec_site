<?php
/**
 * application_data_model_configuration
 *
 * Stellt alle Daten der Tabelle configuration zur Verfügung
 *
 */
class model_configuration extends model_base
{

	public function __construct()
	{
		parent::__construct('configuration');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param Integer $configuration_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($configuration_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		return data_entry::get_by_filter
		(
			$table = 'configuration',
			$filter = array
			(
				'configuration_id' => $configuration_id
			),
			$single = true
		);
	}
	
	/*
	 * get_entries_by_group()
	 *
	 * @param String $group
	 *
	 * @return Array of Objects
	 */
	public static function get_entries_by_group($group)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($group) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'configuration',
			$filter = array
			(
				'group' => $group
			),
			$single = false
		);
	}

	/*
	 * get_all_entries()
	 *
	 * @return Object
	 */
	public static function get_all_entries()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		return data_entry::get_all('configuration', $class=__CLASS__);
	}

	/*
	 * remove_by_id()
	 *
	 * @param Integer $configuration_id
	 *
	 * @return MySQLi Result
	 */
	public static function remove_by_id($configuration_id)
	{
		return data_entry::remove_by_filter
		(
			$table = 'configuration',
			$filter = array
			(
				'configuration_id' => $configuration_id
			)
		);
	}
}

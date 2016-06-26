<?php
/**
 * application_data_model_results
 *
 * Stellt alle Daten der Tabelle Moderator zur Verfügung
 *
 */
class model_results extends model_base
{

	public function __construct()
	{
		parent::__construct('results');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param String $results_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($results_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($results_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'results',
			$filter = array
			(
				'results_id' => $results_id
			),
			$single = true
		);
	}

	/*
	 * get_all_entries()
	 *
	 *
	 * @return Array of Objects
	 */
	public static function get_all_entries()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		return data_entry::get_all('results', __CLASS__);
	}

	/*
	 * delete_entry_by_id()
	 *
	 * @param String $results_id
	 *
	 * @return Object
	 */
	public static function delete_entry_by_id($results_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($results_id) betreten.");
		$db = database::get_instance();
		$results_id = $db->escape($results_id);
		$sql = "
			DELETE FROM `results` WHERE `results_id` = $results_id;
		";
		return $db->query($sql);
	}

}

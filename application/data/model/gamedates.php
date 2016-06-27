<?php
/**
 * application_data_model_gamedates
 *
 * Stellt alle Daten der Tabelle gamedates zur Verfügung
 *
 */
class model_gamedates extends model_base
{

	public function __construct()
	{
		parent::__construct('gamedates');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param String $gamedates_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($gamedates_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($gamedates_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'gamedates',
			$filter = array
			(
				'gamedates_id' => $gamedates_id
			),
			$single = true
		);
	}
 
 	/*
	 * get_entry_by_step_date()
	 *
	 * @param Integer $step
	 * @param String $date
	 *
	 * @return Object
	 */
  public static function get_entry_by_step_date($step, $date){
		return data_entry::get_by_filter
		(
			$table = 'gamedates',
			$filter = array
			(
				'step' => $step,
        'date' => $date
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
		return data_entry::get_all('gamedates', __CLASS__);
	}
  
	/*
	 * get_upcoming_dates()
	 *
	 * @param String $step
	 *
	 * @return Object
	 */
	public static function get_upcoming_dates($step)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($gamedates_id) betreten.");
		$db = database::get_instance();
    $step = $db->escape($step);
    $date_start = date("Y-m-d H:i:s", strtotime("+10 minute")); // registration possible max 10 minutes before game starts
		$sql = "
			SELECT * FROM `gamedates` WHERE `date` > '$date_start' AND `step` = $step
      ORDER BY `date` ASC
      LIMIT 0, 4;
		";
		return $db->get_objects($sql, __CLASS__);
	}

	/*
	 * delete_entry_by_id()
	 *
	 * @param String $gamedates_id
	 *
	 * @return Object
	 */
	public static function delete_entry_by_id($gamedates_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($gamedates_id) betreten.");
		$db = database::get_instance();
		$gamedates_id = $db->escape($gamedates_id);
		$sql = "
			DELETE FROM `gamedates` WHERE `gamedates_id` = $gamedates_id;
		";
		return $db->query($sql);
	}

}

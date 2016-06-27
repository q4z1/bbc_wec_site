<?php
/**
 * application_data_model_gamedates
 *
 * Stellt alle Daten der Tabelle Moderator zur Verfügung
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
  
  public static function get_gamedates_from_this_week(){
    $nextSunday = date("Y-m-d 23:59:00", strtotime('next sunday'));
    $now = date("Y-m-d H:i:s");
    $sql = "
      SELECT * FROM gamedates
      WHERE date > '$now' AND date < '$nextSunday'
      ORDER BY date ASC;
    ";
    $db = database::get_instance();
    return $db->get_objects($sql, __CLASS__);
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

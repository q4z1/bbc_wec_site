<?php
/**
 * application_data_model_dates
 *
 * Stellt alle Daten der Tabelle Moderator zur Verfügung
 *
 */
class model_dates extends model_base
{

	public function __construct()
	{
		parent::__construct('dates');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param String $dates_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($dates_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($dates_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'dates',
			$filter = array
			(
				'dates_id' => $dates_id
			),
			$single = true
		);
	}
  
  public static function get_dates_from_this_week(){
    $nextSunday = date("Y-m-d 23:59:00", strtotime('next sunday'));
    $now = date("Y-m-d H:i:s");
    $sql = "
      SELECT * FROM dates
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
		return data_entry::get_all('dates', __CLASS__);
	}

	/*
	 * delete_entry_by_id()
	 *
	 * @param String $dates_id
	 *
	 * @return Object
	 */
	public static function delete_entry_by_id($dates_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($dates_id) betreten.");
		$db = database::get_instance();
		$dates_id = $db->escape($dates_id);
		$sql = "
			DELETE FROM `dates` WHERE `dates_id` = $dates_id;
		";
		return $db->query($sql);
	}

}

<?php
/**
 * application_data_model_gameregs
 *
 * Stellt alle Daten der Tabelle Moderator zur Verfügung
 *
 */
class model_gameregs extends model_base
{

	public function __construct()
	{
		parent::__construct('gameregs');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param String $gameregs_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($gameregs_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($gameregs_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'gameregs',
			$filter = array
			(
				'gameregs_id' => $gameregs_id
			),
			$single = true
		);
	}
  
  public static function get_gameregs_from_this_week(){
    $nextSunday = date("Y-m-d 23:59:00", strtotime('next sunday'));
    $now = date("Y-m-d H:i:s");
    $sql = "
      SELECT * FROM gameregs
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
		return data_entry::get_all('gameregs', __CLASS__);
	}

	/*
	 * delete_entry_by_id()
	 *
	 * @param String $gameregs_id
	 *
	 * @return Object
	 */
	public static function delete_entry_by_id($gameregs_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($gameregs_id) betreten.");
		$db = database::get_instance();
		$gameregs_id = $db->escape($gameregs_id);
		$sql = "
			DELETE FROM `gameregs` WHERE `gameregs_id` = $gameregs_id;
		";
		return $db->query($sql);
	}

}

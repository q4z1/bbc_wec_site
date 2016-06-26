<?php
/**
 * application_data_model_shoutbox
 *
 * Stellt alle Daten der Tabelle shoutbox zur Verfügung
 *
 */
class model_shoutbox extends model_base
{

	public function __construct()
	{
		parent::__construct('shoutbox');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param String $shoutbox_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($shoutbox_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($shoutbox_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'shoutbox',
			$filter = array
			(
				'shoutbox_id' => $shoutbox_id
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
		return data_entry::get_all('shoutbox', __CLASS__);
	}
  
	/*
	 * get_posts_with_limit()
	 *
	 * @param Integer $start
   * @param Integer $end
	 *
	 * @return Array of Arrays
	 */
	public static function get_posts_with_limit($start=0, $end=50)
	{
		$db = database::get_instance();
    $start = $db->escape($start);
    $end = $db->escape($end);
    $where = "TRUE";
    if(app::$session != "admin"){
      $where = "`status` < 3";
    }
		$sql = "
			SELECT shoutbox_id, playername, msg, created, status FROM `shoutbox`
      WHERE $where
      ORDER BY `created` DESC
      LIMIT $start,$end;
		";
		return $db->get_all_assoc($sql);
	}

	/*
	 * delete_entry_by_id()
	 *
	 * @param String $shoutbox_id
	 *
	 * @return Object
	 */
	public static function delete_entry_by_id($shoutbox_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($shoutbox_id) betreten.");
		$db = database::get_instance();
		$shoutbox_id = $db->escape($shoutbox_id);
		$sql = "
			DELETE FROM `shoutbox` WHERE `shoutbox_id` = $shoutbox_id;
		";
		return $db->query($sql);
	}

}

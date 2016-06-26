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
    $where = "`status` > 0";
    if(app::$session != "admin"){
      $where .= " AND `status` < 3";
    }
		$sql = "
			SELECT shoutbox_id, playername, msg, created, status FROM `shoutbox`
      WHERE $where
      ORDER BY `shoutbox_id` DESC
      LIMIT $start,$end;
		";
		return $db->get_all_assoc($sql);
	}
  
	/*
	 * get_three_posts_by_msgid($shoutbox_id)
	 *
	 * @param String $shoutbox_id
	 *
	 * @return Array of Arrays
	 */
	public static function get_three_posts_by_msgid($shoutbox_id)
	{
		$db = database::get_instance();
    $shoutbox_id = $db->escape($shoutbox_id);
    if($shoutbox_id > 1){
      $shoutbox_id--;
    }
		$sql = "
			SELECT shoutbox_id, playername, msg, created, status FROM `shoutbox`
      WHERE `shoutbox_id` >= $shoutbox_id
      AND `status` > 0 
      ORDER BY `shoutbox_id` ASC
      LIMIT 0,3;
		";
		return $db->get_all_assoc($sql);
	}
  
	/*
	 * get_num_posts()
	 *
	 * @return Array of Arrays
	 */
	public static function get_num_posts()
	{
		$db = database::get_instance();
    $where = "TRUE";
    if(app::$session != "admin"){
      $where = "`status` < 3";
    }
		$sql = "
			SELECT count(shoutbox_id) as num FROM `shoutbox`
      WHERE $where;
		";
		return $db->get_assoc($sql);
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

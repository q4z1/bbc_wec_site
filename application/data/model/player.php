<?php
/**
 * application_data_model_player
 *
 * Stellt alle Daten der Tabelle player zur Verfügung
 *
 */
class model_player extends model_base
{

	public function __construct()
	{
		parent::__construct('player');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param String $player_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($player_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($player_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'player',
			$filter = array
			(
				'player_id' => $player_id
			),
			$single = true
		);
	}

	/*
	 * get_entry_by_playername()
	 *
	 * @param String $player_name
	 *
	 * @return Object
	 */
	public static function get_entry_by_playername($player_name)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($player_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'player',
			$filter = array
			(
				'player_name' => $player_name
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
		return data_entry::get_all('player', __CLASS__);
	}

	/*
	 * delete_entry_by_id()
	 *
	 * @param String $player_id
	 *
	 * @return Object
	 */
	public static function delete_entry_by_id($player_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($player_id) betreten.");
		$db = database::get_instance();
		$player_id = $db->escape($player_id);
		$sql = "
			DELETE FROM `player` WHERE `player_id` = $player_id;
		";
		return $db->query($sql);
	}

}

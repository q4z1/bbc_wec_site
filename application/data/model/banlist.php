<?php
/**
 * application_data_model_banlist
 *
 * Stellt alle Daten der Tabelle banlist zur Verfügung
 *
 */
class model_banlist extends model_base
{

	public function __construct()
	{
		parent::__construct('banlist');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param String $banlist_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($banlist_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($banlist_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'banlist',
			$filter = array
			(
				'banlist_id' => $banlist_id
			),
			$single = true
		);
	}
  
	/*
	 * is_fp_banned()
	 *
	 * @param String $fingerprint
	 *
	 * @return Object
	 */
	public static function is_fp_banned($fingerprint)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($banlist_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'banlist',
			$filter = array
			(
				'fingerprint' => $fingerprint,
        'active' => 1
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
		return data_entry::get_all('banlist', __CLASS__);
	}

	/*
	 * delete_entry_by_id()
	 *
	 * @param String $banlist_id
	 *
	 * @return Object
	 */
	public static function delete_entry_by_id($banlist_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($banlist_id) betreten.");
		$db = database::get_instance();
		$banlist_id = $db->escape($banlist_id);
		$sql = "
			DELETE FROM `banlist` WHERE `banlist_id` = $banlist_id;
		";
		return $db->query($sql);
	}

}

<?php
/**
 * application_data_model_admin
 *
 * Stellt alle Daten der Tabelle admin zur Verfügung
 *
 */
class model_admin extends model_base
{

	public function __construct()
	{
		parent::__construct('admin');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param String $admin_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($admin_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($admin_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'admin',
			$filter = array
			(
				'admin_id' => $admin_id
			),
			$single = true
		);
	}

	/*
	 * get_admin_by_username()
	 *
	 * @param String $username
	 *
	 * @return Object
	 */
	public static function get_admin_by_username($username)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		return data_entry::get_by_filter
		(
			$table = 'admin',
			$filter = array
			(
				'username' => $username
			),
			$single = true
		);
	}

	/*
	 * get_admin_by_email()
	 *
	 * @param String $email
	 *
	 * @return Object
	 */
	public static function get_admin_by_email($email)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		return data_entry::get_by_filter
		(
			$table = 'admin',
			$filter = array
			(
				'email' => $email
			),
			$single = true
		);
	}
	
	public static function get_active_admins()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		return data_entry::get_by_filter
		(
			$table = 'admin',
			$filter = array
			(
				'active' => '1'
			),
			$single = false
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
		return data_entry::get_all('admin', __CLASS__);
	}

	/*
	 * delete_entry_by_id()
	 *
	 * @param String $admin_id
	 *
	 * @return Object
	 */
	public static function delete_entry_by_id($admin_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($admin_id) betreten.");
		$db = database::get_instance();
		$admin_id = $db->escape($admin_id);
		$sql = "
			DELETE FROM `admin` WHERE `admin_id` = $admin_id;
		";
		return $db->query($sql);
	}

}

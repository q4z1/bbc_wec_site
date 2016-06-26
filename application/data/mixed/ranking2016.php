<?php
/**
 * application_data_mixed_ranking2016
 *
 *
 */
class mixed_ranking2016 extends mixed_base
{
	public function __construct()
	{
		parent::__construct();
	}

	/*
	 * cancel_asas_by_customer_moderator()
	 *
	 *
	 * @return Array of Objects
	 */
	public static function get_all_entries_ordered()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		$db = database::get_instance();
		$sql = "
			SELECT r.*, p.awards, p.avatar, p.avatar_mime FROM `ranking2016` r
      LEFT JOIN `player2016` p USING(playername)
      WHERE TRUE
      ORDER by r.crptotal desc;
		";
		//debug::add_info("sql:<br /><pre>$sql</pre>");
		return $db->get_objects($sql, __CLASS__);
	}
}

?>

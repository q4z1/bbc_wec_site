<?php
/**
 * application_data_mixed_base
 *
 * Basis Klasse für Tabellen übergreifende DB-Abfragen
 *
 *
 */
abstract class mixed_base extends data_entry
{
	public function __construct()
	{
		parent::__construct('');
	}

	public function save()
	{
		throw new Exception("trying to save a database view");
	}
}

?>

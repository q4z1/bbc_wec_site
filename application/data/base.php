<?php
class data_base
{
	public $table = '';
	private $changed_entries = array();
	private $entries = array();
	
	public function __construct($table='')
	{
		if($table != "")
		{
			$this->table = $table;
		}
	}

	/*
	 * __set()
	 *
	 * - Füllt das Array $entries mit allen Spalten und ihren Werten
	 * - speichert den Namen der gesetzten Spalten in das Array
	 *	 $changed_entries
	 *
	 *
	 * @param String $name = Name der Spalte
	 * @param String $value = Wert der Spalte
	 */
	public function __set($name, $value)
	{
		if(!in_array($name, $this->changed_entries))
			$this->changed_entries[] = $name;
		$this->entries[$name] = $value;
	}

	/*
	 * __get()
	 *
	 * - gibt den Wert einer Spalte zurück
	 *
	 * @param String $name = Name der Spalte
	 *
	 * @return Wert der Spalte
	 */
	public function __get($name)
	{
		if(!isset($this->entries[$name]))
		{
			return null;
		}
		return $this->entries[$name];
	}

	/*
	 * save()
	 *
	 * - Prüft bei Aufruf, ob Werte neu gesetzt oder geändert worden
	 *	 sind und führt ggf. eine Speicherung des Datensatzes durch
	 * - Beim Anlegen einer neuen Zeile wird die erzeugte id gespeichert
	 * - Abschließend wird reset() aufgerufen
	 *
	 */
	public function save()
	{
		$db = database::get_instance();
		if(count($this->changed_entries) > 0)
		{
			$tmp = array();
			$bool = false;
			foreach($this->changed_entries as $name)
			{
				if($name == "table")
				{
					continue;
				}
				if($name == "{$this->table}_id")
					$bool = true;
				$value = ($this->entries[$name] === 'NULL' || $this->entries[$name] === null) ?
						"NULL" :
						"'" . $db->escape($this->entries[$name]) . "'";
				$tmp[] = "`{$name}` = $value";
			}
			if($bool == false && isset($this->entries["{$this->table}_id"]))
				$tmp[] = "`{$this->table}_id` = '"
							 . $db->escape($this->entries["{$this->table}_id"]) . "'";
			$sql = "INSERT INTO `{$this->table}` SET "
						. implode(",", $tmp) . " ON DUPLICATE
							KEY UPDATE " . implode(",", $tmp) .";";
			debug::add_info
			(
					"(" . __FILE__ . ")<b>" . __CLASS__ . "</b>::" . __FUNCTION__
				. "(): \$sql:<br /><pre>$sql</pre>"
			);
			$result = $db->query($sql);
			if(!isset($this->entries["{$this->table}_id"]))
			{
				$this->entries["{$this->table}_id"] = $db->insert_id();
			}
			$this->reset();
		}
		return;
	}

	/*
	 * reset()
	 *
	 * - Setzt das Array $changed_entries zurück
	 */
	public function reset()
	{
		$this->changed_entries = array();
	}
}

?>

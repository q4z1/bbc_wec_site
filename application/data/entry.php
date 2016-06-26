<?php
/**
 * Klasse application_data_entry
 *
 * Basis Klasse für Tabellenzeilen
 *
 * - gibt die Werte der einzelnen DB-Spalten zurück
 * - es können Bedingungen angegeben werden
 * - beim Setzen eines neuen Wertes wird die Änderung festgehalten
 * 		und der Datensatz kann mit save() gespeichert oder aktualisert werden
 *
 */
class data_entry extends data_base
{
	public function __construct()
	{
	}

	public static function get_all($table, $class=__CLASS__)
	{
		$db = database::get_instance();
		$query =
			"
				SELECT	*
				FROM	$table
				WHERE	TRUE
			";
		return $db->get_objects($query, $class, $table);
	}

	public static function get_by_filter($table, $filter=array(), $single_result=false, $order_by=false, $group_by=false, $class=__CLASS__)
	{
		$db = database::get_instance();
		$query =
			"
				SELECT	*
				FROM	$table
				WHERE	TRUE
			";
		if(count($filter) > 0){
			foreach ($filter as $field => $value)
			{
				if(is_array($value))
				{
					foreach($value['values'] as $val)
					{
						$val = $db->escape($val);
						$values[] = "'".$val."'";
					}
					if($value['type'] == 'IN')
					{
						$option = $field." IN(".implode(',',$values).")";
					}
				}
				else
				{
					$value = $db->escape($value);
					$option = ($value == 'NULL' || is_null($value)) ? "(`$field` IS NULL OR `$field` = '0')" : "`$field` = '$value'";
				}
				$query .= " AND $option";
			}
		}


		if($order_by !== false && array_key_exists('field', $order_by) && array_key_exists('direction', $order_by))
		{
			$query .= " ORDER BY {$order_by['field']} {$order_by['direction']}";
		}

		if ($group_by !== false)
		{
			$query .= ' GROUP BY {$group_by}';
		}

		if ($single_result)
		{
			$query .= ' LIMIT 1';
		}

		$query .= ";";

		//debug::add_info("get_by_filter query:<br />{$query}");

		if ($single_result)
		{
			return $db->get_object($query, $class, $table);
		}
		else
		{
			return $db->get_objects($query, $class, $table);
		}
	}

	public static function remove_by_filter($table, $filter=array())
	{
		$db = database::get_instance();
		$query =
			"
				DELETE
				FROM	$table
				WHERE	TRUE
			";
		foreach ($filter as $field => $value)
		{
			$value = $db->escape($value);
			$option = ($value == 'NULL' || is_null($value)) ? "(`$field` IS NULL OR `$field` = '0')" : "`$field` = '$value'";
			$query .= " AND $option";
		}
		return $db->query($query);
	}
}

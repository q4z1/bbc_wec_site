<?php
/**
 * lib_database
 *
 * stellt die Verbindung zur Datenbank und
 * grundlegende DB-Methoden zur Verfügung
 *
 */
class database
{
	private static $db;
	public static $_instance;
	/*
	 * __construct()
	 *
	 * stelle Verbindung zur Datenbank her
	 */
	private function __construct()
	{
		try
		{
			self::$db = new mysqli
			(
				cfg::$db_host,
				cfg::$db_user,
				cfg::$db_pass,
				cfg::$db_name
			);
			if(mysqli_connect_errno())
			{
				throw new Exception
				(
					printf
					(
							"Verbindung zur Datenbank fehlgeschlagen - "
						. "Fehler: %s",
							mysqli_connect_error()
					)
				 );
			}
			/* Zeichensatz der Verbindung: UTF-8 */
			self::$db->set_charset("utf8");
			//self::query("set names 'utf8';");
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}

	/*
	 * clone()
	 *
	 * - wird als private deklariert, da diese Klasse ein Singleton ist,
	 *	 dass man nicht clonen darf
	 */
	private function __clone(){}

	/*
	 * get_instance()
	 *
	 * - gibt eine Referenz auf die Instanz dieser Klasse zurück
	 *
	 * @return Object-Referenz $_instance
	 */
	public static function get_instance()
	{
		if(!(is_object(self::$_instance )))
		{
			self::$_instance = new database();
		}
		return self::$_instance;
	}

	/*
	 * close()
	 *
	 * - Beendet die DB-verbindung
	 */
	public function __destruct()
	{
		self::$db->close();
	}

	/*
	 * query()
	 *
	 * @param String $sql = SQL-Query
	 *
	 * @return Object $result = mysqli-Result-Objekt der MySQL-Abfrage
	 */
	public function query($sql)
	{
		$result = null;
		if(!($result = self::$db->query($sql)))
		{
			throw new Exception
			(
				printf
				(
						"Die folgende Abfrage verursachte einen Fehler: %s - "
					. "Fehlermeldung: %s",
						$sql,
						self::$db->error
				)
			);
		}
		return $result;
	}

	public function escape($str)
	{
		return self::$db->real_escape_string($str);
	}

	/*
	 * get_object()
	 *
	 * @param String $sql = SQL-Query
	 * @param Object $class = bestimmt das Rückgabe-Objekt
	 *
	 * @return Object $object = Ergebnis-Objekt einer Tabellen-Zeile
	 */
	public function get_object($sql, $class = stdClass, $table = '')
	{
		if($result = self::$db->query($sql))
		{
			$object = $result->fetch_object($class);
			/*
			 * Aufruf von reset(), damit das gefüllte Array
			 * $changed_entries aus der Basis-Klasse
			 * data_base beim Durchlaufen der
			 * __set() Funktion zurückgesetzt wird
			 */
			if(is_object($object))
			{
				$object->table = $table;
				$object->reset();
				return $object;
			}
			else
			{
				return null;
			}
		}
		return null;
	}

	/*
	 * get_objects()
	 *
	 * @param String $sql = der SQL-Query
	 * @param Object $class = bestimmt das Rückgabe-Objekt
	 *
	 * @return Array $objects = Array mit Ergebnis-Objekten aller Zeilen
	 */
	public function get_objects($sql, $class = stdClass, $table = '')
	{
		if($result = self::$db->query($sql))
		{
			$objects = array();
			while($object = $result->fetch_object($class))
			{
				if(is_object($object))
				{
					$object->table = $table;
					$object->reset();
					$objects[] = $object;
				}
			}
			return (is_array($objects) && count($objects) > 0) ? $objects : null;
		}
		return null;
	}

	/*
	 * get_assoc()
	 *
	 * @param String $sql = SQL-Query
	 *
	 * @return Array $array = Assoziatives Array der Ergebniszeile
	 */
	public function get_assoc($sql)
	{
		$result = self::$db->query($sql);
		if($result)
		{
			$array = $result->fetch_assoc();
			if(is_array($array))
			{
				return $array;
			}
			else
			{
				return null;
			}
		}
		return null;
	}

	/*
	 * get_all_assoc()
	 *
	 * @param String $sql = SQL-Query
	 *
	 * @return Array $array = Array mit Assoziativen Arrays der Ergebniszeilen
	 */
	public function get_all_assoc($sql)
	{
		$result = self::$db->query($sql);
		if($result)
		{
			$array = array();
			while($row = $result->fetch_assoc())
			{
				$array[] = $row;
			}
			if(count($array) > 0)
			{
				return $array;
			}
			else
			{
				return null;
			}
		}
		return null;
	}

	/*
	 * insert_id()
	 *
	 * @return automatisch generierte ID der zuletzt gespeicherten Zeile
	 */
	public function insert_id()
	{
		return self::$db->insert_id;
	}
}

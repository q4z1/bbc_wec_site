<?php
/**
 * modtool
 *
 * Version 0.1
 * Beginn:	2013-04-26
 * Ende:
 *
 * www_index
 *
 * index.php
 */

/**
 * Beginn Ablauf index.php
 */

/*
 * @FIXME: debug:
 */
//echo "\$_GET:<br /><pre>" . var_export($_GET, true) . "</pre><hr />";
//echo "\$_POST:<br /><pre>" . var_export($_POST, true) . "</pre><hr />";
//echo "\$_REQUEST:<br /><pre>" . var_export($_REQUEST, true) . "</pre><hr />";
//echo "\$_FILES:<br /><pre>" . var_export($_FILES, true) . "</pre><hr />";
/*
 * end debug
 */

/**
 * ermitteln des Wurzelverzeichnisses,
 * Definition der Verzeichnis-Konstanten
 */
define
(
	'ROOT_DIR', substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), DIRECTORY_SEPARATOR)+1)
);
define('APP_DIR', ROOT_DIR . "application" . DIRECTORY_SEPARATOR);
define('DATA_DIR', ROOT_DIR . "application" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR);
define('VIEW_DIR', APP_DIR . "view" . DIRECTORY_SEPARATOR);
define('LIB_DIR', ROOT_DIR . "lib" . DIRECTORY_SEPARATOR);
define('TMPL_DIR', ROOT_DIR . "template" . DIRECTORY_SEPARATOR);
define('VAR_DIR', ROOT_DIR . "var" . DIRECTORY_SEPARATOR);
define('INC_DIR', ROOT_DIR . "inc" . DIRECTORY_SEPARATOR);
/**
 * setzen der benötigten Include-Verzeichnisse
 * für den Autoloader
 */
set_include_path
(
		get_include_path() . PATH_SEPARATOR . APP_DIR	. PATH_SEPARATOR . LIB_DIR
		. PATH_SEPARATOR . DATA_DIR. PATH_SEPARATOR . VIEW_DIR . PATH_SEPARATOR . TMPL_DIR
		. PATH_SEPARATOR . VAR_DIR. PATH_SEPARATOR . INC_DIR. PATH_SEPARATOR
);

/*
 * Stelle Konfigurations-Variablen bereit
 */
cfg::init();


/*
 * extract $_POST, $_GET and $_SERVER['REQUEST_URI']
 */
$request = array_merge($_GET, $_POST, array("json" => json_decode(file_get_contents('php://input'))) );
$request['uri'] = null;
if(isset($_SERVER['REQUEST_URI']) && strlen($_SERVER['REQUEST_URI']) > 0)
{
	/*
	 * prüfe auf web_root
	 */
	if
	(
		cfg::$web_root != "/" &&
		substr($_SERVER['REQUEST_URI'], 0, +(strlen(cfg::$web_root))) === cfg::$web_root
	)
	{
		$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen(cfg::$web_root)-1);
	}

	/*
	 * prüfe auf "?"-GET Variablen:
	 */
	if(strrpos($_SERVER['REQUEST_URI'], "?") !== false)
	{
		$request['uri'] =
			substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], "?"));
	}
	else
	{
		$request['uri'] = $_SERVER['REQUEST_URI'];
	}
	// debug::add_info
	(
			"(".__FILE__."): REQUEST_URI und GET/POST ausgelesen:<br />"
		. "\$request:<br /><pre>" . var_export($request, true) . "</pre>"
	);
}

/*
 * run application
 */
try
{
	app::run($request);
}
catch(Exception $exception)
{
	/*
	 * lade exception controller
	 */
	$e = new controller_exceptionhandler();
	$e->run($exception);
}
exit();
/*
 * Ende Ablauf index.php
 **/

/**
 * __autoload($class_name)
 *
 * autmatisches Laden von Klassen
 *
 * Konzept:
 *
 * Verzeichnisse können durch Unterstriche getrennt vorangestellt werden -
 * nach dem letzten Unterstrich folgt der Dateiname der Klasse ohne ".php"
 *
 * Ein folgender require_once Aufruf sucht in allen Verzeichnissen
 * des Include-Paths
 */
function __autoload($class)
{
	$class = str_replace("_", "/", $class) . ".php";
	/*
	 * stelle fest, ob die Klassendatei existiert
	 */
	if
	(
		(file_exists(APP_DIR . $class) === false) &&
		(file_exists(LIB_DIR . $class) === false) &&
		(file_exists(DATA_DIR . $class) === false) &&
		(file_exists(VIEW_DIR . $class) === false) &&
		(file_exists(TMPL_DIR . $class) === false)
	)
	{
		$exception = new Exception("Klassen-Datei $class wurde nicht gefunden!");
		/*
		 * lade exception controller
		 */
		$e = new controller_exceptionhandler();
		$e->run($exception);
		exit();
	}
	/*
	 * lade die Klassen-Datei
	 */
	try
	{
		require_once($class);
	}
	catch(Exception $e)
	{
		throw new Exception
		(
			"Das Laden der Klassen-Datei $class schlug fehl! : " . $e->getMessage()
		);
	}
	return;
}

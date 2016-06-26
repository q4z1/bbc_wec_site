<?php
/**
 * Klasse für Auferbereitung des Ausgabe einer Exception
 */
class controller_exceptionhandler extends controller_base
{
	public function __construct(){}

	public function run($exception)
	{
		/*
		 * TODO: bereite die Ausnahme auf
		 */
		$output = "\n-----\n"
					. $exception->getMessage()
					. "\n-----\n"
					. $exception->getTraceAsString()
					. "\n-----\n";
		if(cfg::$proc == "browser")
		{
			$output = str_replace("\n", "<br>", $output);
			require_once("browser/error/exception.html.php");
		}
		else
		{
			require_once("cli/error/exception.cli.php");
		}
	}
}

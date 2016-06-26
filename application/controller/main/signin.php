<?php
/**
 * application_controller_main_login
 */
class controller_main_signin extends controller_main_base
{
	public function __construct()
	{
		parent::__construct(__CLASS__);
	}


	public function run()
	{
		//debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		if(count(app::$request) == 0 || !array_key_exists('username', app::$request) || !array_key_exists('passhash', app::$request))
		{
			$_SESSION['notice'] = "<span class=\"red\"><i class=\"icon-exclamation-sign icon-2x\"></i></span>  Bitte Benutzername und Passwort eingeben!";
			Header("Location: " . cfg::$web_root);
			exit();
		}
		else
		{
			$username = app::$request['username'];
			$password = app::$request['passhash'];
			$admin = model_admin::get_admin_by_username($username);
			if(is_null($admin))
			{
				$_SESSION['notice'] = "<span class=\"red\"><i class=\"icon-exclamation-sign icon-2x\"></i></span>  Der Benutzername ist unbekannt!";
				Header("Location: " . cfg::$web_root);
				exit();
			}
			elseif($password != $admin->password )
			{
				$_SESSION['notice'] = "<span class=\"red\"><i class=\"icon-exclamation-sign icon-2x\"></i></span>  Benutzername und Passwort stimmen nicht überein!";
				Header("Location: " . cfg::$web_root);
				exit();
			}
			elseif($admin->active == 0)
			{
				$_SESSION['notice'] = "<span class=\"red\"><i class=\"icon-exclamation-sign icon-2x\"></i></span>  Login nicht gestattet!";
				Header("Location: " . cfg::$web_root);
				exit();
			}
			else
			{
				// cleanup previous session
				$_SESSION = array();
				$_COOKIE = array();
				session_regenerate_id(true); // unique session_id every login!
				//$_SESSION['notice']['default'] = "Hallo $username!  <i class=\"icon-ok icon-2x\"></i>";
				$_SESSION['admin'] = $username;
				$admin->last_login = date("Y-m-d H:i:s");
				$admin->save();
				$_SESSION['type'] = "admin";
				Header("Location: " . cfg::$web_root . $_SESSION['type'] . "/");
				exit();
			}
		}
	}
}

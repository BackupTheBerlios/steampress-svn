<?php
/*
Plugin Name: SP-Login
Plugin URI: http://somethingunpredictable.com/sp-login/
Version: 0.1
Description: Replaces the current SteamPress login system that uses sloppy redirects and is under constant fire from Thomas W.
Author: Robert Deaton
Author URI: http://anothersadsong.com
*/
//I apologize in advance for this little bit of messy code
//But its not like its messy in contrast with the rest of the SP code

function auth_redirect() {
	logged_in();
}
function logged_in()
{
	global $error, $spdb;
	$usercookie =  $_COOKIE['steampressuser_' . COOKIEHASH];
	$passcookie = $_COOKIE['steampresspass_' . COOKIEHASH];
	@session_start();
	$usersession = $_SESSION['steampressuser_' . COOKIEHASH];
	$passsession = $_SESSION['steampresspass_' . COOKIEHASH];
	if(isset($_POST['loginformsubmit']) && empty($usercookie) && empty($passcookie) && empty($usersession) && empty($passsession))
	{
		$spuser = $_POST['log'];
		$sppass = md5($_POST['pwd']);
		$passfromdb = $spdb->get_row("SELECT user_pass FROM $spdb->users WHERE user_login = '$spuser'", 'ARRAY_A');
		if(!$passfromdb)
		{
			$error = "Incorrect Username. Remember, usernames are CaSe SeNsEtIvE";
			include(ABSPATH . SPINC . '/sp-login.php';
			die();
		}
		if($sppass == $passfromdb['user_pass'])
		{
			if($_POST['usesessions'])
			{
				$_SESSION['steampressuser_' . COOKIEHASH] = $spuser;
				$_SESSION['steampresspass_' . COOKIEHASH] = md5($sppass);
			}
			else
			{
				setcookie('steampressuser_' . COOKIEHASH, $spuser, time() + 31536000, COOKIEPATH);
				setcookie('steampresspass_' . COOKIEHASH, md5($sppass), time() + 31536000, COOKIEPATH);
				//header("Location: " . $_SERVER['PHP_SELF']);
				//die();
			}
		}
		else
		{
			$error = "Incorrect Password.";
			include(ABSPATH . SPINC . '/sp-login.php';
			die();
		}
	}
	else
	{
		if(empty($usercookie) || empty($passcookie))
		{
			include(ABSPATH . SPINC . '/sp-login.php';
			die();
		}
		else
		{
			$passfromdb = $spdb->get_row("SELECT user_pass FROM $spdb->users WHERE user_login = '$usercookie'", 'ARRAY_A');
			if(!$passfromdb)
			{
				$error = "Incorrect Username. Remember, usernames are CaSe SeNsEtIvE";
				include(ABSPATH . SPINC . '/sp-login.php';
				die();
			}
			else
			{
				if($passcookie == md5($passfromdb['user_pass']))
				{
				return true;
				}
				else
				{
					//expire the cookies and kill the sessions
					session_destroy();
				   setcookie('steampressuser_' . COOKIEHASH, ' ', time() - 31536000, COOKIEPATH);
				   setcookie('steampresspass_' . COOKIEHASH, ' ', time() - 31536000, COOKIEPATH);
					include(ABSPATH . SPINC . '/sp-login.php';
					die();
				}
			}
		}
	}
}
?>
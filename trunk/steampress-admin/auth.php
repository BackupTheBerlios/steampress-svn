<?php
require_once(ABSPATH . '/steampress-config.php');

if ( (!empty($_COOKIE['wordpressuser_' . COOKIEHASH]) && !steampress_login($_COOKIE['wordpressuser_' . COOKIEHASH], $_COOKIE['wordpresspass_' . COOKIEHASH], true)) || (empty($_COOKIE['wordpressuser_' . COOKIEHASH])) )
{
	header('Expires: Wed, 5 Jun 1979 23:41:00 GMT'); // Michel's birthday
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: no-cache');

	header('Location: ' . get_settings('siteurl') . '/steampress-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
	exit();
}

?>

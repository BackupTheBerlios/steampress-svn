<?php
require( dirname(__FILE__) . '/sp-config.php');

if ( get_magic_quotes_gpc() )
	$_POST['post_password'] = stripslashes($_POST['post_password']);

// 10 days
setcookie('sp-postpass_' . COOKIEHASH, $_POST['post_password'], time() + 864000, COOKIEPATH);

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>
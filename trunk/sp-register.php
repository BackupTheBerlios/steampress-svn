<?php
require('./sp-config.php');

$spvarstoreset = array('action');
for ($i=0; $i<count($spvarstoreset); $i += 1) {
	$spvar = $spvarstoreset[$i];
	if (!isset($$spvar)) {
		if (empty($_POST["$spvar"])) {
			if (empty($_GET["$spvar"])) {
				$$spvar = '';
			} else {
				$$spvar = $_GET["$spvar"];
			}
		} else {
			$$spvar = $_POST["$spvar"];
		}
	}
}

if (!get_settings('users_can_register')) {
	$action = 'disabled';
}

switch($action) {

case 'register':

	$user_login = $_POST['user_login'];
	$pass1 = $_POST['pass1'];
	$pass2 = $_POST['pass2'];
	$user_email = $_POST['user_email'];
		
	/* checking login has been typed */
	if ($user_login == '') {
		die (__('<strong>ERROR</strong>: Please enter a login.'));
	}

	/* checking the password has been typed twice */
	if ($pass1 == '' || $pass2 == '') {
		die (__('<strong>ERROR</strong>: Please enter your password twice.'));
	}

	/* checking the password has been typed twice the same */
	if ($pass1 != $pass2)	{
		die (__('<strong>ERROR</strong>: Please type the same password in the two password fields.'));
	}
	$user_nickname = $user_login;

	/* checking e-mail address */
	if ($user_email == '') {
		die (__('<strong>ERROR</strong>: Please type your e-mail address.'));
	} else if (!is_email($user_email)) {
		die (__('<strong>ERROR</strong>: The email address isn&#8217;t correct.'));
	}

	/* checking the login isn't already used by another user */
	$result = $spdb->get_results("SELECT user_login FROM $spdb->users WHERE user_login = '$user_login'");
    if (count($result) >= 1) {
		die (__('<strong>ERROR</strong>: This login is already registered, please choose another one.'));
	}

	$user_ip = $_SERVER['REMOTE_ADDR'] ;

	$user_browser = $spdb->escape($_SERVER['HTTP_USER_AGENT']);

	$user_login = $spdb->escape($user_login);
	$pass1 = $spdb->escape($pass1);
	$user_nickname = $spdb->escape($user_nickname);
    $user_nicename = sanitize_title($user_nickname);
	$now = gmdate('Y-m-d H:i:s');
	if (get_settings('new_users_can_blog') >= 1) $user_level = 1;

	$result = $spdb->query("INSERT INTO $spdb->users 
		(user_login, user_pass, user_nickname, user_email, user_ip, user_browser, user_registered, user_level, user_idmode, user_nicename)
	VALUES 
		('$user_login', MD5('$pass1'), '$user_nickname', '$user_email', '$user_ip', '$user_browser', '$now', '$user_level', 'nickname', '$user_nicename')");
	
	if ($result == false) {
		die (sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !'), get_settings('admin_email')));
	}

	$stars = '';
	for ($i = 0; $i < strlen($pass1); $i = $i + 1) {
		$stars .= '*';
	}

	$message  = sprintf(__("New user registration on your blog %1\$s:\n\nLogin: %2\$s \n\nE-mail: %3\$s"), get_settings('blogname'), $user_login, $user_email);

	@sp_mail(get_settings('admin_email'), sprintf(__('[%s] New User Registration'), get_settings('blogname')), $message);

	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <title>SteamPress &raquo; <?php _e('Registration Complete') ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_settings('blog_charset'); ?>" />	
	<link rel="stylesheet" href="sp-admin/sp-admin.css" type="text/css" />
</head>
<body>

<div id="login"> 
	<h2><?php _e('Registration Complete') ?></h2>
	<p><?php _e('Login:') ?> <strong><?php echo $user_login; ?></strong><br />
	<?php _e('Password:') ?> <strong><?php echo $stars; ?></strong><br />
	<?php _e('E-mail:') ?> <strong><?php echo $user_email; ?></strong></p>
	<form action="sp-login.php" method="post" name="login">
		<input type="hidden" name="log" value="<?php echo $user_login; ?>" />
		<input type="submit" value="<?php _e('Login') ?>" name="submit" />
	</form>
</div>
</body>
</html>

	<?php
break;

case 'disabled':

	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>SteamPress &raquo; <?php _e('Registration Currently Disabled') ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_settings('blog_charset'); ?>">
	<link rel="stylesheet" href="sp-admin/sp-admin.css" type="text/css">
</head>

<body>

<div id="login">
	<h2><?php _e('Registration Disabled') ?></h2>
	<p><?php _e('User registration is currently not allowed.') ?><br />
	<a href="<?php echo get_settings('home') .'/'. get_settings('blogfilename'); ?>" title="<?php _e('Go back to the blog') ?>"><?php _e('Home') ?></a>
	</p>
</div>

</body>
</html>

	<?php
break;

default:

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>SteamPress &raquo; <?php _e('Registration Form') ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_settings('blog_charset'); ?>" />
	<link rel="stylesheet" href="sp-admin/sp-admin.css" type="text/css" />
</head>

<body>
<div id="login">
<h2><?php _e('Registration') ?></h2>

<form method="post" action="sp-register.php">
	<input type="hidden" name="action" value="register" />
	<label for="user_login"><?php _e('Login:') ?></label> <input type="text" name="user_login" id="user_login" size="10" maxlength="20" /><br />
	<label for="pass1"><?php _e('Password:') ?></label> <input type="password" name="pass1" id="pass1" size="10" maxlength="100" /><br />
 
	<input type="password" name="pass2" size="10" maxlength="100" /><br />
	<label for="user_email"><?php _e('E-mail') ?></label>: <input type="text" name="user_email" id="user_email" size="15" maxlength="100" /><br />
	<input type="submit" value="<?php _e('OK') ?>" class="search" name="submit" />
</form>
</div>

</body>
</html>
<?php

break;
}
?>
<?php
// Someone please remind me to shoot the SteamPress developers and their defunct naming schemes.
// Is it get_settings('siteurl') or bloginfo('wpurl')
// They really need to make up their minds. I had originally converted this file so that we could get rid of bloginfo()
// But come to find out get_settings() doesn't work here. Why? Who knows?
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>SteamPress &rsaquo; <?php _e('Login') ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
	<link rel="stylesheet" href="<?php bloginfo('wpurl'); ?>/sp-admin/sp-admin.css" type="text/css" />
	<script type="text/javascript">
	function focusit() {
		document.getElementById('log').focus();
	}
	window.onload = focusit;
	</script>
</head>
<body id="sp-login">

<div id="login">
<h1><img src="<?php bloginfo('wpurl'); ?>/sp-images/sp-logo.png" alt="SteamPress" /></h1>
<?php
if ( $error )
	echo "<div id=\"login_error\">$error</div>";
?>

<form name="loginform" id="loginform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p><label><?php _e('Login') ?>: <input type="text" name="log" id="log" value="" size="20" tabindex="1" /></label></p>
<p><label><?php _e('Password') ?>: <input type="password" name="pwd" value="" size="20" tabindex="2" /></label></p>
<p class="submit">
	<input type="submit" name="loginformsubmit" value="<?php _e('Login'); ?> &raquo;" tabindex="3" />
</p>
</form>
<ul>
	<li><a href="<?php bloginfo('home'); ?>" title="<?php _e('Are you lost?') ?>">&laquo; <?php _e('Back to blog') ?></a></li>
<?php if (get_settings('users_can_register')) : ?>
	<li><a href="<?php bloginfo('wpurl'); ?>/sp-register.php"><?php _e('Register') ?></a></li>
<?php endif; ?>
	<li><a href="<?php bloginfo('wpurl'); ?>/sp-login.php?action=lostpassword" title="<?php _e('Password Lost and Found') ?>"><?php _e('Lost your password?') ?></a></li>
</ul>
	</div>

</body>
</html
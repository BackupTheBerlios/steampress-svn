<?php

require('../steampress-config.php');
require_once('auth.php');

get_currentuserinfo();

if (0 == $user_level)
{
	$redirect_to = get_settings('siteurl') . '/steampress-admin/profile.php';
}
else
{
	$redirect_to = get_settings('siteurl') . '/steampress-admin/post.php';
}
header ("Location: $redirect_to");
?>
<?php require_once('admin.php'); ?>
<?php get_admin_page_title(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php bloginfo('name') ?> &rsaquo; <?php echo $title; ?> &#8212; SteamPress</title>
<link rel="stylesheet" href="<?php echo get_settings('siteurl') ?>/sp-admin/sp-admin.css" type="text/css" />
<link rel="shortcut icon" href="../sp-images/steampressmini.png" />
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_settings('blog_charset'); ?>" />

<?php if (isset($xfn)) : ?>
<script type="text/javascript">
//<![CDATA[

function GetElementsWithClassName(elementName, className) {
	var allElements = document.getElementsByTagName(elementName);
	var elemColl = new Array();
	for (i = 0; i < allElements.length; i++) {
		if (allElements[i].className == className) {
			elemColl[elemColl.length] = allElements[i];
		}
	}
	return elemColl;
}

function blurry() {
	if (!document.getElementById) return;
	
	var aInputs = document.getElementsByTagName('input');
	
	for (var i = 0; i < aInputs.length; i++) {      
		aInputs[i].onclick = function() {
			var inputColl = GetElementsWithClassName('input','valinp');
			var rel = document.getElementById('rel');
			var inputs = '';
			for (i = 0; i < inputColl.length; i++) {
				if (inputColl[i].checked) {
				if (inputColl[i].value != '') inputs += inputColl[i].value + ' ';
				}
			}
			inputs = inputs.substr(0,inputs.length - 1);
			if (rel != null) {
				rel.value = inputs;
			}
		}
		
		aInputs[i].onkeyup = function() {
			var inputColl = GetElementsWithClassName('input','valinp');
			var rel = document.getElementById('rel');
			var inputs = '';
			for (i = 0; i < inputColl.length; i++) {
				if (inputColl[i].checked) {
					inputs += inputColl[i].value + ' ';
				}
			}
			inputs = inputs.substr(0,inputs.length - 1);
			if (rel != null) {
				rel.value = inputs;
			}
		}
		
	}
}

window.onload = blurry;
//]]>
</script>
<?php endif; ?>

<?php do_action('admin_head', ''); ?>
</head>
<body>
<ul id="sitemenu">
<li><a href="<?php echo get_settings('home') . '/' . get_settings('blogfilename'); ?>"><?php _e('View site') ?> &raquo;</a></li>
<li class="last"><a href="<?php echo get_settings('siteurl')?>/sp-login.php?action=logout" title="<?php _e('Log out of this account') ?>"><?php printf(__('Logout (%s)'), $user_nickname) ?></a></li>
</ul>

<div id="wphead">
<h1><?php echo sptexturize(get_settings(('blogname'))); ?></h1>
</div>
<?php
require(ABSPATH . '/sp-admin/menu-header.php');

if ( $parent_file == 'options-general.php' ) {
	require(ABSPATH . '/sp-admin/options-head.php');
}
?>
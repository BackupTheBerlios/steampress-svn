<?php require_once('admin.php'); ?>
<?php get_admin_page_title(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php bloginfo('name') ?> &rsaquo; <?php echo $title; ?> &#8212; SteamPress</title>
<link rel="stylesheet" href="<?php echo get_settings('siteurl') ?>/steampress-admin/steampress-admin.css" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_settings('blog_charset'); ?>" />

<?php if ( isset($xfn) ) : ?>
<script type="text/javascript">
//<![CDATA[
function GetElementsWithClassName(elementName, className)
{
	var allElements = document.getElementsByTagName(elementName);
	var elemColl = new Array();
	for (i = 0; i < allElements.length; i++)
	{
		 if (allElements[i].className == className)
		 {
			  elemColl[elemColl.length] = allElements[i];
		 }
	}
	return elemColl;
}

function meChecked()
{
	var undefined;
	var eMe = document.getElementById('me');
	if (eMe == undefined) return false;
	else return eMe.checked;
}

function upit()
{
	var isMe = meChecked(); //document.getElementById('me').checked;
	var inputColl = GetElementsWithClassName('input', 'valinp');
	var results = document.getElementById('rel');
	var linkText, linkUrl, inputs = '';
	for (i = 0; i < inputColl.length; i++)
	{
		inputColl[i].disabled = isMe;
		inputColl[i].parentNode.className = isMe ? 'disabled' : '';
		if (!isMe && inputColl[i].checked && inputColl[i].value != '')
		{
			inputs += inputColl[i].value + ' ';
		}
	}
	inputs = inputs.substr(0,inputs.length - 1);
	if (isMe) inputs='me';
	results.value = inputs;
}

function blurry()
{
	if (!document.getElementById) return;
	var aInputs = document.getElementsByTagName('input');
	for (var i = 0; i < aInputs.length; i++)
	{
		 aInputs[i].onclick = aInputs[i].onkeyup = upit;
	}
}

window.onload = blurry;
//]]>
</script>
<?php endif; ?>

<?php do_action('admin_head', ''); ?>
</head>
<body>

<div id="wphead">
<h1><?php echo wptexturize(get_settings(('blogname'))); ?> <span>(<a href="<?php echo get_settings('home') . '/' . get_settings('blogfilename'); ?>"><?php _e('View site') ?></a>)</span></h1>
</div>

<?php
require(ABSPATH . '/steampress-admin/menu-header.php');

if ( $parent_file == 'options-general.php' )
{
	require(ABSPATH . '/steampress-admin/options-head.php');
}
?>
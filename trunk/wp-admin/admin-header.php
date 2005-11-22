<?php

/*************************************************

SteamPress - Blogging without the Dirt
Author: SteamPress Development Team (developers@steampress.org)
Copyright (c): 2005 ispi, all rights reserved

    This file is part of SteamPress.

    SteamPress is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    SteamPress is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with SteamPress; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

You may contact the authors of Snoopy by e-mail at:
developers@steampress.org

Or, write to:

SteamPress Development Team
c/o Samir M. Nassar
2015 Central Ave. NE, #226
Minneapolis, MN 55418
USA

The latest version of SteamPress can be obtained from:
http://steampress.org/

*************************************************/
 
@header('Content-type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
if (!isset($_GET["page"])) require_once('admin.php'); ?>
<?php get_admin_page_title(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php bloginfo('name') ?> &rsaquo; <?php echo $title; ?> &#8212; WordPress</title>
<link rel="stylesheet" href="<?php echo get_settings('siteurl') ?>/wp-admin/wp-admin.css?version=<?php bloginfo('version'); ?>" type="text/css" />
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_settings('blog_charset'); ?>" />

<script type="text/javascript">
//<![CDATA[
<?php
echo "\n";
?>
function customToggleLink()
{
// TODO: Only show link if there's a hidden row
	document.write('<small>(<a href="javascript:;" id="customtoggle" onclick="toggleHidden()"><?php _e('Show hidden'); ?></a>)</small>');
// TODO: Rotate link to say "show" or "hide"
// TODO: Use DOM
}

function toggleHidden()
{
	var allElements = document.getElementsByTagName('tr');
	for (i = 0; i < allElements.length; i++)
	{
		if ( allElements[i].className.indexOf('hidden') != -1 )
		{
			allElements[i].className = allElements[i].className.replace('hidden', '');
		}
	}
}

<?php if ( isset($xfn) ) : ?>

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

function meChecked() {
var undefined;
var eMe = document.getElementById('me');
if (eMe == undefined) return false;
else return eMe.checked;
}

function upit() {
	var isMe = meChecked(); //document.getElementById('me').checked;
	var inputColl = GetElementsWithClassName('input', 'valinp');
	var results = document.getElementById('rel');
	var linkText, linkUrl, inputs = '';
	for (i = 0; i < inputColl.length; i++) {
		inputColl[i].disabled = isMe;
		inputColl[i].parentNode.className = isMe ? 'disabled' : '';
		if (!isMe && inputColl[i].checked && inputColl[i].value != '') {
			inputs += inputColl[i].value + ' ';
				}
		}
	inputs = inputs.substr(0,inputs.length - 1);
	if (isMe) inputs='me';
	results.value = inputs;
	}


<?php
endif;
echo "\n";
?>

//]]>
</script>

<?php
do_action('admin_head', '');
echo "\n";
?>
</head>
<body>

	<div id="wphead"><span><a href="<?php echo get_settings('home') . '/'; ?>"> <?php _e('View site') ?></a></span>
		<h1>
<?php echo wptexturize(get_settings(('blogname'))); ?>
		</h1>

	</div>

<?php
require(ABSPATH . '/wp-admin/menu-header.php');

if ( $parent_file == 'options-general.php' ) {
	require(ABSPATH . '/wp-admin/options-head.php');
}
?>

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

if ( defined('ABSPATH') )
{
	require_once( ABSPATH . 'wp-config.php');
}
else
{
	require_once('../wp-config.php');
}

require_once(ABSPATH . 'wp-admin/admin-functions.php');
auth_redirect();

header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

update_category_cache();

get_currentuserinfo();

$posts_per_page = get_settings('posts_per_page');
$what_to_show = get_settings('what_to_show');
$date_format = get_settings('date_format');
$time_format = get_settings('time_format');

$wpvarstoreset = array('profile','redirect','redirect_url','a','popuptitle','popupurl','text', 'trackback', 'pingback');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($_POST["$wpvar"])) {
			if (empty($_GET["$wpvar"])) {
				$$wpvar = '';
			} else {
				$$wpvar = $_GET["$wpvar"];
			}
		} else {
			$$wpvar = $_POST["$wpvar"];
		}
	}
}

require(ABSPATH . '/wp-admin/menu.php');

// Handle plugin admin pages.
if (isset($_GET['page'])) {
	$plugin_page = plugin_basename($_GET['page']);
	$page_hook = get_plugin_page_hook($plugin_page, $pagenow);

	if ( $page_hook ) {
		if (! isset($_GET['noheader']))
			require_once(ABSPATH . '/wp-admin/admin-header.php');

		do_action($page_hook);
	} else {
		if ( validate_file($plugin_page) ) {
			die(__('Invalid plugin page'));
		}

		if (! file_exists(ABSPATH . "wp-content/plugins/$plugin_page"))
			die(sprintf(__('Cannot load %s.'), $plugin_page));

		if (! isset($_GET['noheader']))
			require_once(ABSPATH . '/wp-admin/admin-header.php');

		include(ABSPATH . "wp-content/plugins/$plugin_page");
	}

	include(ABSPATH . 'wp-admin/admin-footer.php');

	exit();
}

?>

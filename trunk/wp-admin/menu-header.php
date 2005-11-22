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
?>

<ul id="adminmenu">
<?php
$self = preg_replace('|^.*/wp-admin/|i', '', $_SERVER['PHP_SELF']);
$self = preg_replace('|^.*/plugins/|i', '', $self);

get_admin_page_parent();

foreach ($menu as $item)
{
	$class = '';

	// 0 = name, 1 = user_level, 2 = file
	if (( strcmp($self, $item[2]) == 0 && empty($parent_file)) || ($parent_file && ($item[2] == $parent_file)))
	{
		$class = ' class="current"';
	}

	if ($user_level >= $item[1])
	{
		if ( file_exists(ABSPATH . "wp-content/plugins/{$item[2]}") )
		{
			echo "\n\t<li><a href='" . get_settings('siteurl') . "/wp-admin/admin.php?page={$item[2]}'$class>{$item[0]}</a></li>";
		}
		else
		{
			echo "\n\t<li><a href='" . get_settings('siteurl') . "/wp-admin/{$item[2]}'$class>{$item[0]}</a></li>";
		}
	}
}
echo "\n";
?>
	<li class="last"><a href="<?php echo get_settings('siteurl') ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account') ?>"><?php printf(__('Logout (%s)'), $user_nickname) ?></a></li>
</ul>

<?php
// Sub-menu
if ( isset($submenu["$parent_file"]) )
{
?>
<ul id="adminmenu2">
<?php
	foreach ($submenu["$parent_file"] as $item)
	{
		if ($user_level < $item[1])
		{
			continue;
		}

		if ( isset($submenu_file) )
		{
			if ( $submenu_file == $item[2] )
			{
				$class = ' class="current"';
			}
			else
			{
			$class = '';
			}
		}
		else if ( (isset($plugin_page) && $plugin_page == $item[2]) || (!isset($plugin_page) && $self == $item[2]) )
		{
			$class = ' class="current"';
		}
		else
		{
		$class = '';
		}

		$menu_hook = get_plugin_page_hook($item[2], $parent_file);

		if (file_exists(ABSPATH . "wp-content/plugins/{$item[2]}") || ! empty($menu_hook))
		{
			if ( 'admin.php' == $pagenow )
			{
				echo "\n\t<li><a href='" . get_settings('siteurl') . "/wp-admin/admin.php?page={$item[2]}'$class>{$item[0]}</a></li>";
			}
			else
			{
				echo "\n\t<li><a href='" . get_settings('siteurl') . "/wp-admin/{$parent_file}?page={$item[2]}'$class>{$item[0]}</a></li>";
			}
		}
		else
		{
			echo "\n\t<li><a href='" . get_settings('siteurl') . "/wp-admin/{$item[2]}'$class>{$item[0]}</a></li>";
		}
	}
echo "\n";
?>
</ul>
<?php
}
?>
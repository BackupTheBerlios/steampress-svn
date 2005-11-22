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
 
require_once('admin.php');
$title = __('Pages');
$parent_file = 'edit.php';
require_once('admin-header.php');

get_currentuserinfo();
?>

<div class="wrap">
<h2><?php _e('Page Management'); ?></h2>

<?php
if (isset($user_ID) && ('' != intval($user_ID))) {
	$posts = $wpdb->get_results("
	SELECT $wpdb->posts.*, $wpdb->users.user_level FROM $wpdb->posts
	INNER JOIN $wpdb->users ON ($wpdb->posts.post_author = $wpdb->users.ID)
	WHERE $wpdb->posts.post_status = 'static'
	AND ($wpdb->users.user_level < $user_level OR $wpdb->posts.post_author = $user_ID)
	");
} else {
	$posts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_status = 'static'");
}

if ($posts) {
?>
<table width="100%" cellpadding="3" cellspacing="3">
<tr>
	<th scope="col"><?php _e('ID') ?></th>
	<th scope="col"><?php _e('Title') ?></th>
	<th scope="col"><?php _e('Owner') ?></th>
	<th scope="col"><?php _e('Updated') ?></th>
	<th scope="col"></th>
	<th scope="col"></th>
	<th scope="col"></th>
</tr>
<?php page_rows(); ?>
</table>
<?php
} else {
?>
<p><?php _e('No pages yet.') ?></p>
<?php
} // end if ($posts)
?>
<p><?php _e('Pages are like posts except they live outside of the normal blog chronology. You can use pages to organize and manage any amount of content.'); ?></p>
<h3><a href="page-new.php"><?php _e('Create New Page'); ?> &raquo;</a></h3>
</div>


<?php include('admin-footer.php'); ?>

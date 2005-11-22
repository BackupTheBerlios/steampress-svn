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
$title = __('New Page');
$parent_file = 'post.php';
require_once('admin-header.php');

get_currentuserinfo();
 
if ( isset($_GET['saved']) )
{
?>
<div class="updated"><p><strong><?php _e('Page saved.') ?> <a href="edit-pages.php"><?php _e('Manage pages'); ?> &raquo;</a></strong></p></div>
<?php
}
 
if ($user_level > 0)
{
	$action = 'post';
	get_currentuserinfo();
	//set defaults
	$post_status = 'static';
	$comment_status = get_settings('default_comment_status');
	$ping_status = get_settings('default_ping_status');
	$post_pingback = get_settings('default_pingback_flag');
	$post_parent = 0;
	$page_template = 'default';

	include('edit-page-form.php');
}
  include('admin-footer.php'); ?>
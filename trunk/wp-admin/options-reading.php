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

$title = __('Reading Options');
$parent_file = 'options-general.php';

include('admin-header.php');
?>

<div class="wrap">
<h2><?php _e('Reading Options') ?></h2>
<form name="form1" method="post" action="options.php">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="'posts_per_page','what_to_show','posts_per_rss','rss_use_excerpt','blog_charset','gzipcompression' " />
	<fieldset class="options">
	<legend><?php _e('Blog Pages') ?></legend>
	<table width="100%" cellspacing="2" cellpadding="5" class="editform">
		<tr valign="top">
		<th width="33%" scope="row"><?php _e('Show at most:') ?></th>
		<td>
		<input name="posts_per_page" type="text" id="posts_per_page" value="<?php form_option('posts_per_page'); ?>" size="3" />
		<select name="what_to_show" id="what_to_show" >
			<option value="days" <?php selected('days', get_settings('what_to_show')); ?>><?php _e('days') ?></option>
			<option value="posts" <?php selected('posts', get_settings('what_to_show')); ?>><?php _e('posts') ?></option>
		</select>
		</td>
		</tr>
	</table>
	</fieldset>

	<fieldset class="options">
	<legend><?php _e('Syndication Feeds') ?></legend>
	<table width="100%" cellspacing="2" cellpadding="5" class="editform">
		<tr valign="top">
		<th width="33%" scope="row"><?php _e('Show the most recent:') ?></th>
		<td><input name="posts_per_rss" type="text" id="posts_per_rss" value="<?php form_option('posts_per_rss'); ?>" size="3" /> <?php _e('posts') ?></td>
		</tr>
		<tr valign="top">
		<th scope="row"><?php _e('For each article, show:') ?> </th>
		<td>
		<label><input name="rss_use_excerpt"  type="radio" value="0" <?php checked(0, get_settings('rss_use_excerpt')); ?>  /> <?php _e('Full text') ?></label><br />
		<label><input name="rss_use_excerpt" type="radio" value="1" <?php checked(1, get_settings('rss_use_excerpt')); ?> /> <?php _e('Summary') ?></label>
		</td>
		</tr>
	</table>
	</fieldset>
	<table width="100%" cellspacing="2" cellpadding="5" class="editform">
		<tr valign="top">
		<th width="33%" scope="row"><?php _e('Encoding for pages and feeds:') ?></th>
		<td><input name="blog_charset" type="text" id="blog_charset" value="<?php form_option('blog_charset'); ?>" size="20" class="code" /><br />
		<?php _e('The character encoding you write your blog in (UTF-8 is <a href="http://developer.apple.com/documentation/macos8/TextIntlSvcs/TextEncodingConversionManager/TEC1.5/TEC.b0.html">recommended</a>)') ?></td>
		</tr>
	</table>
	<p>
		<label><input type="checkbox" name="gzipcompression" value="1" <?php checked('1', get_settings('gzipcompression')); ?> />
		<?php _e('WordPress should compress articles (gzip) if browsers ask for them') ?></label>
	</p>
	<p class="submit">
		<input type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" />
	</p>
</form>
</div>
<?php include('./admin-footer.php'); ?>
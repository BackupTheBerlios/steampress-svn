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
 

if (empty($feed))
{
	$blog = 1;
	$feed = 'rss';
	$doing_rss = 1;
	require('wp-blog-header.php');
}

header('Content-type: text/xml; charset=' . get_settings('blog_charset'), true);
$more = 1;

?>
<?php echo '<?xml version="1.0" encoding="'.get_settings('blog_charset').'"?'.'>'; ?>
<!-- generator="steampress/<?php echo $wp_version ?>" -->
<rss version="0.92">
<channel>
	<title><?php bloginfo_rss('name') ?></title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss('description') ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<docs>http://backend.userland.com/rss092</docs>
	<language><?php echo get_option('rss_language'); ?></language>

<?php
$items_count = 0;
if ($posts)
{
	foreach ($posts as $post)
	{
		start_wp();
?>
	<item>
		<title><?php the_title_rss() ?></title>
<?php
		if (get_settings('rss_use_excerpt'))
		{
?>
		<description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
<?php
		}
		else
		{ // use content
?>
		<description><?php the_content_rss('', 0, '', get_settings('rss_excerpt_length')) ?></description>
<?php
		}
?>
		<link><?php permalink_single_rss() ?></link>
	</item>
<?php
		$items_count++;
		if (($items_count == get_settings('posts_per_rss')) && empty($m))
		{
			break;
		}
	}
}
?>
</channel>
</rss>

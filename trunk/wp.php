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
 
// This is an example of a very simple template
require_once('./wp-blog-header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml/DTD/xhtml-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_settings('blog_charset'); ?>" />
	<meta name="generator" content="WordPress <?php $wp_version ?>" /> <!-- leave this for stats -->
	<link rel="alternate" type="text/xml" title="RSS" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
</head>
<body>
	<h1 id="header"><a href="<?php echo get_settings('home'); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>

<!-- // loop start -->
<?php
if (have_posts())
{
	while (have_posts())
	{
		the_post();
?>
<?php the_date('d.m.y', '<h2>','</h2>'); ?>
	<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h3>

<?php the_content();   link_pages('<br />Pages: ', '<br />', 'number') ?>

	<p><em>Posted by <strong><?php the_author() ?></strong> @ <a href="<?php the_permalink() ?>"><?php the_time() ?></a></em></p>
	<p>Filed under: <?php the_category(',') ?></p>

<?php comments_popup_link('comments ?', '1 comment', '% comments')   comments_template(); ?>


<!-- // this is just the end of the motor - don't touch that line either :) -->
<?php
	}
}
else
{
?>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>

<?php
}
?>

	<div align="right"><cite>Powered by <a href="http://steamedpenguin.com/projects/steampress/"><strong>SteamPress</strong></a></cite><br />
		<br />
		<a href="wp-login.php">login</a>
		<br />
		<a href="wp-register.php">register</a>
	</div>

</body>
</html>
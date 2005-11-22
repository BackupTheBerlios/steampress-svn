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
 

if (empty($doing_rss))
{
	$doing_rss = 1;
	require(dirname(__FILE__) . '/wp-blog-header.php');
}

// Remove the pad, if present.
$feed = preg_replace('/^_+/', '', $feed);

if ($feed == '' || $feed == 'feed')
{
	$feed = 'rss2';
}

if ( is_single() || ($withcomments == 1) )
{
	require(ABSPATH . 'wp-commentsrss2.php');
}
else
{
	switch ($feed)
	{
		case 'atom':
			require(ABSPATH . 'wp-atom.php');
			break;
		case 'rdf':
			require(ABSPATH . 'wp-rdf.php');
			break;
		case 'rss':
			require(ABSPATH . 'wp-rss.php');
			break;
		case 'rss2':
			require(ABSPATH . 'wp-rss2.php');
			break;
		case 'comments-rss2':
			require(ABSPATH . 'wp-commentsrss2.php');
			break;
	}
}

?>

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
 
require_once( dirname(__FILE__) . '/wp-config.php' );

if ( empty($doing_trackback) )
{
	$doing_trackback = true;
	$tb = true;
	require_once('wp-blog-header.php');
}

function trackback_response($error = 0, $error_message = '')
{
	header('Content-Type: text/xml; charset=' . get_option('blog_charset') );
	if ($error)
	{
		echo '<?xml version="1.0" encoding="utf-8"?'.">\n";
		echo "<response>\n";
		echo "<error>1</error>\n";
		echo "<message>$error_message</message>\n";
		echo "</response>";
		die();
	}
	else
	{
		echo '<?xml version="1.0" encoding="utf-8"?'.">\n";
		echo "<response>\n";
		echo "<error>0</error>\n";
		echo "</response>";
	}
}

// trackback is done by a POST
$request_array = 'HTTP_POST_VARS';

if ( !$_GET['tb_id'] )
{
	$tb_id = explode('/', $_SERVER['REQUEST_URI']);
	$tb_id = intval( $tb_id[ count($tb_id) - 1 ] );
}

$tb_url    = $_POST['url'];
$title     = $_POST['title'];
$excerpt   = $_POST['excerpt'];
$blog_name = $_POST['blog_name'];
$charset   = $_POST['charset'];

if ($charset)
{
	$charset = strtoupper( trim($charset) );
}
else
{
	$charset = 'ASCII, UTF-8, ISO-8859-1, JIS, EUC-JP, SJIS';
}

if ( function_exists('mb_convert_encoding') ) // For international trackbacks
{
	$title     = mb_convert_encoding($title, get_settings('blog_charset'), $charset);
	$excerpt   = mb_convert_encoding($excerpt, get_settings('blog_charset'), $charset);
	$blog_name = mb_convert_encoding($blog_name, get_settings('blog_charset'), $charset);
}

if ( is_single() || is_page() )
{
	$tb_id = $posts[0]->ID;
}

if ( !intval( $tb_id ) )
{
	trackback_response(1, 'I really need an ID for this to work.');
}

if (empty($title) && empty($tb_url) && empty($blog_name))
{
	// If it doesn't look like a trackback at all...
	header('Location: ' . get_permalink($tb_id));
	exit;
}

if ( !empty($tb_url) && !empty($title) && !empty($tb_url) )
{
	header('Content-Type: text/xml; charset=' . get_option('blog_charset') );

	$pingstatus = $wpdb->get_var("SELECT ping_status FROM $wpdb->posts WHERE ID = $tb_id");

	if ( 'open' != $pingstatus )
	{
		trackback_response(1, 'Sorry, trackbacks are closed for this item.');
	}

	$title =  wp_specialchars( strip_tags( $title ) );
	$title = (strlen($title) > 250) ? substr($title, 0, 250) . '...' : $title;
	$excerpt = strip_tags($excerpt);
	$excerpt = (strlen($excerpt) > 255) ? substr($excerpt, 0, 252) . '...' : $excerpt;

	$comment_post_ID = $tb_id;
	$comment_author = $blog_name;
	$comment_author_email = '';
	$comment_author_url = $tb_url;
	$comment_content = "<strong>$title</strong>\n\n$excerpt";
	$comment_type = 'trackback';

	$dupe = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND comment_author_url = '$comment_author_url'");
	if ( $dupe )
	{
		trackback_response(1, 'We already have a ping from that URI for this post.');
	}

	$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type');

	wp_new_comment($commentdata);

	do_action('trackback_post', $wpdb->insert_id);
	trackback_response(0);
}
?>
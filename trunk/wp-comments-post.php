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
 
require( dirname(__FILE__) . '/wp-config.php' );

$comment_post_ID = (int) $_POST['comment_post_ID'];

$status = $wpdb->get_row("SELECT post_status, comment_status FROM $wpdb->posts WHERE ID = '$comment_post_ID'");

if ( empty($status->comment_status) )
{
	do_action('comment_id_not_found', $comment_post_ID);
	exit;
}
elseif ( 'closed' ==  $status->comment_status )
{
	do_action('comment_closed', $comment_post_ID);
	die( __('Sorry, comments are closed for this item.') );
}
elseif ( 'draft' == $status->post_status )
{
	do_action('comment_on_draft', $comment_post_ID);
	exit;
}

$comment_author       = trim($_POST['author']);
$comment_author_email = trim($_POST['email']);
$comment_author_url   = trim($_POST['url']);
$comment_content      = trim($_POST['comment']);

// If the user is logged in
get_currentuserinfo();
if ( $user_ID )
{
	$comment_author       = addslashes($user_identity);
	$comment_author_email = addslashes($user_email);
	$comment_author_url   = addslashes($user_url);
}
else
{
	if ( get_option('comment_registration') )
	{
		die( __('Sorry, you must be logged in to post a comment.') );
	}
}

$comment_type = '';

if ( get_settings('require_name_email') && !$user_ID )
{
	if ( 6 > strlen($comment_author_email) || '' == $comment_author )
	{
		die( __('Error: please fill the required fields (name, email).') );
	}
	elseif ( !is_email($comment_author_email))
	{
		die( __('Error: please enter a valid email address.') );
	}
}

if ( '' == $comment_content )
{
	die( __('Error: please type a comment.') );
}

$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'user_ID');

wp_new_comment($commentdata);

setcookie('comment_author_' . COOKIEHASH, stripslashes($comment_author), time() + 30000000, COOKIEPATH);
setcookie('comment_author_email_' . COOKIEHASH, stripslashes($comment_author_email), time() + 30000000, COOKIEPATH);
setcookie('comment_author_url_' . COOKIEHASH, stripslashes($comment_author_url), time() + 30000000, COOKIEPATH);

header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$location = (empty($_POST['redirect_to'])) ? $_SERVER["HTTP_REFERER"] : $_POST['redirect_to'];

wp_redirect($location);
?>

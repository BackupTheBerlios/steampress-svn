<?php
require( dirname(__FILE__) . '/sp-config.php' );

$comment_post_ID = (int) $_POST['comment_post_ID'];

$post_status = $spdb->get_var("SELECT comment_status FROM $spdb->posts WHERE ID = '$comment_post_ID'");

if ( empty($post_status) ) {
	do_action('comment_id_not_found', $comment_post_ID);
	exit;
} elseif ( 'closed' ==  $post_status ) {
	do_action('comment_closed', $comment_post_ID);
	die( __('Sorry, comments are closed for this item.') );
}

$comment_author       = $_POST['author'];
$comment_author_email = $_POST['email'];
$comment_author_url   = $_POST['url'];
$comment_content      = $_POST['comment'];

$comment_type = '';

$user_ip    = apply_filters('pre_comment_user_ip', $_SERVER['REMOTE_ADDR']);

if ( get_settings('require_name_email') && ('' == $comment_author_email || '' == $comment_author) )
	die( __('Error: please fill the required fields (name, email).') );

if ( '' == $comment_content )
	die( __('Error: please type a comment.') );

$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type');

sp_new_comment($commentdata);

setcookie('comment_author_' . COOKIEHASH, stripslashes($comment_author), time() + 30000000, COOKIEPATH);
setcookie('comment_author_email_' . COOKIEHASH, stripslashes($comment_author_email), time() + 30000000, COOKIEPATH);
setcookie('comment_author_url_' . COOKIEHASH, stripslashes($comment_author_url), time() + 30000000, COOKIEPATH);

header('Expires: Mon, 11 Jan 1984 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$location = get_permalink($comment_post_ID);

header("Location: $location");

?>
<?php

/**** DB Functions ****/

/*
 * generic function for inserting data into the posts table.
 */
function sp_insert_post($postarr = array()) {
	global $spdb, $post_default_category;
	
	// export array as variables
	extract($postarr);
	
	// Do some escapes for safety
	$post_title = $spdb->escape($post_title);
	$post_name = sanitize_title($post_title);
	$post_excerpt = $spdb->escape($post_excerpt);
	$post_content = $spdb->escape($post_content);
	$post_author = (int) $post_author;

	// Make sure we set a valid category
	if (0 == count($post_category) || !is_array($post_category)) {
		$post_category = array($post_default_category);
	}

	$post_cat = $post_category[0];
	
	if (empty($post_date))
		$post_date = current_time('mysql');
	// Make sure we have a good gmt date:
	if (empty($post_date_gmt)) 
		$post_date_gmt = get_gmt_from_date($post_date);
	if (empty($comment_status))
		$comment_status = get_settings('default_comment_status');
	if (empty($ping_status))
		$ping_status = get_settings('default_ping_status');
	
	$sql = "INSERT INTO $spdb->posts 
		(post_author, post_date, post_date_gmt, post_modified, post_modified_gmt, post_content, post_title, post_excerpt, post_category, post_status, post_name, comment_status, ping_status) 
		VALUES ('$post_author', '$post_date', '$post_date_gmt', '$post_date', '$post_date_gmt', '$post_content', '$post_title', '$post_excerpt', '$post_cat', '$post_status', '$post_name', '$comment_status', '$ping_status')";
	
	$result = $spdb->query($sql);
	$post_ID = $spdb->insert_id;

	// Set GUID
	$spdb->query("UPDATE $spdb->posts SET guid = '" . get_permalink($post_ID) . "' WHERE ID = '$post_ID'");
	
	sp_set_post_cats('', $post_ID, $post_category);
	
	if ($post_status == 'publish') {
		do_action('publish_post', $post_ID);
	}

	pingback($content, $post_ID);

	// Return insert_id if we got a good result, otherwise return zero.
	return $result ? $post_ID : 0;
}

function sp_get_single_post($postid = 0, $mode = OBJECT) {
	global $spdb;

	$sql = "SELECT * FROM $spdb->posts WHERE ID=$postid";
	$result = $spdb->get_row($sql, $mode);
	
	// Set categories
	$result['post_category'] = sp_get_post_cats('',$postid);

	return $result;
}

function sp_get_recent_posts($num = 10) {
	global $spdb;

	// Set the limit clause, if we got a limit
	if ($num) {
		$limit = "LIMIT $num";
	}

	$sql = "SELECT * FROM $spdb->posts ORDER BY post_date DESC $limit";
	$result = $spdb->get_results($sql,ARRAY_A);

	return $result?$result:array();
}

function sp_update_post($postarr = array()) {
	global $spdb;

	// First get all of the original fields
	extract(sp_get_single_post($postarr['ID'],ARRAY_A));	

	// Now overwrite any changed values being passed in
	extract($postarr);
	
	// Make sure we set a valid category
	if (0 == count($post_category) || !is_array($post_category)) {
		$post_category = array($post_default_category);
	}

	// Do some escapes for safety
	$post_title = $spdb->escape($post_title);
	$post_excerpt = $spdb->escape($post_excerpt);
	$post_content = $spdb->escape($post_content);

	$post_modified = current_time('mysql');
	$post_modified_gmt = current_time('mysql', 1);

	$sql = "UPDATE $spdb->posts 
		SET post_content = '$post_content',
		post_title = '$post_title',
		post_category = $post_category[0],
		post_status = '$post_status',
		post_date = '$post_date',
		post_date_gmt = '$post_date_gmt',
		post_modified = '$post_modified',
		post_modified_gmt = '$post_modified_gmt',
		post_excerpt = '$post_excerpt',
		ping_status = '$ping_status',
		comment_status = '$comment_status'
		WHERE ID = $ID";
		
	$result = $spdb->query($sql);

	sp_set_post_cats('',$ID,$post_category);
	
	return $spdb->rows_affected;
}

function sp_get_post_cats($blogid = '1', $post_ID = 0) {
	global $spdb;
	
	$sql = "SELECT category_id 
		FROM $spdb->post2cat 
		WHERE post_id = $post_ID 
		ORDER BY category_id";

	$result = $spdb->get_col($sql);

	return array_unique($result);
}

function sp_set_post_cats($blogid = '1', $post_ID = 0, $post_categories = array()) {
	global $spdb;
	// If $post_categories isn't already an array, make it one:
	if (!is_array($post_categories)) {
		if (!$post_categories) {
			$post_categories = 1;
		}
		$post_categories = array($post_categories);
	}

	$post_categories = array_unique($post_categories);

	// First the old categories
	$old_categories = $spdb->get_col("
		SELECT category_id 
		FROM $spdb->post2cat 
		WHERE post_id = $post_ID");
	
	if (!$old_categories) {
		$old_categories = array();
	} else {
		$old_categories = array_unique($old_categories);
	}


	$oldies = printr($old_categories,1);
	$newbies = printr($post_categories,1);

	// Delete any?
	$delete_cats = array_diff($old_categories,$post_categories);

	if ($delete_cats) {
		foreach ($delete_cats as $del) {
			$spdb->query("
				DELETE FROM $spdb->post2cat 
				WHERE category_id = $del 
					AND post_id = $post_ID 
				");
		}
	}

	// Add any?
	$add_cats = array_diff($post_categories, $old_categories);

	if ($add_cats) {
		foreach ($add_cats as $new_cat) {
			$spdb->query("
				INSERT INTO $spdb->post2cat (post_id, category_id) 
				VALUES ($post_ID, $new_cat)");
		}
	}
}	// sp_set_post_cats()

function sp_delete_post($postid = 0) {
	global $spdb;

	$result = $spdb->query("DELETE FROM $spdb->posts WHERE ID = $postid");

	if (!$result)
		return $result;

	$spdb->query("DELETE FROM $spdb->comments WHERE comment_post_ID = $postid");

	$spdb->query("DELETE FROM $spdb->post2cat WHERE post_id = $postid");

	$spdb->query("DELETE FROM $spdb->postmeta WHERE post_id = $postid");
	
	return $result;
}

/**** /DB Functions ****/

/**** Misc ****/

// get permalink from post ID
function post_permalink($post_id = 0, $mode = '') { // $mode legacy
	return get_permalink($post_id);
}

// Get the name of a category from its ID
function get_cat_name($cat_id) {
	global $spdb;
	
	$cat_id -= 0; 	// force numeric
	$name = $spdb->get_var("SELECT cat_name FROM $spdb->categories WHERE cat_ID=$cat_id");
	
	return $name;
}

// Get the ID of a category from its name
function get_cat_ID($cat_name='General') {
	global $spdb;
	
	$cid = $spdb->get_var("SELECT cat_ID FROM $spdb->categories WHERE cat_name='$cat_name'");

	return $cid?$cid:1;	// default to cat 1
}

// Get author's preferred display name
function get_author_name($auth_id) {
	$authordata = get_userdata($auth_id);

	switch($authordata["user_idmode"]) {
		case "nickname":
			$authorname = $authordata["user_nickname"];

		case "login":
			$authorname = $authordata["user_login"];
			break;
	
		case "firstname":
			$authorname = $authordata["user_firstname"];
			break;

		case "lastname":
			$authorname = $authordata["user_lastname"];
			break;

		case "namefl":
			$authorname = $authordata["user_firstname"]." ".$authordata["user_lastname"];
			break;

		case "namelf":
			$authorname = $authordata["user_lastname"]." ".$authordata["user_firstname"];
			break;

		default:
			$authorname = $authordata["user_nickname"];
			break;
	}

	return $authorname;
}

// get extended entry info (<!--more-->)
function get_extended($post) {
	list($main,$extended) = explode('<!--more-->',$post);

	// Strip leading and trailing whitespace
	$main = preg_replace('/^[\s]*(.*)[\s]*$/','\\1',$main);
	$extended = preg_replace('/^[\s]*(.*)[\s]*$/','\\1',$extended);

	return array('main' => $main, 'extended' => $extended);
}

// do trackbacks for a list of urls
// borrowed from edit.php
// accepts a comma-separated list of trackback urls and a post id
function trackback_url_list($tb_list, $post_id) {
	if (!empty($tb_list)) {
		// get post data
		$postdata = sp_get_single_post($post_id, ARRAY_A);

		// import postdata as variables
		extract($postdata);
		
		// form an excerpt
		$excerpt = strip_tags($post_excerpt?$post_excerpt:$post_content);
		
		if (strlen($excerpt) > 255) {
			$excerpt = substr($excerpt,0,252) . '...';
		}
		
		$trackback_urls = explode(',', $tb_list);
		foreach($trackback_urls as $tb_url) {
		    $tb_url = trim($tb_url);
		    trackback($tb_url, stripslashes($post_title), $excerpt, $post_id);
		}
    }
}


// query user capabilities
// rather simplistic. shall evolve with future permission system overhaul
// $blog_id and $category_id are there for future usage

/* returns true if $user_id can create a new post */
function user_can_create_post($user_id, $blog_id = 1, $category_id = 'None') {
	$author_data = get_userdata($user_id);
	return ($author_data->user_level > 1);
}

/* returns true if $user_id can create a new post */
function user_can_create_draft($user_id, $blog_id = 1, $category_id = 'None') {
	$author_data = get_userdata($user_id);
	return ($author_data->user_level >= 1);
}

/* returns true if $user_id can edit $post_id */
function user_can_edit_post($user_id, $post_id, $blog_id = 1) {
	$author_data = get_userdata($user_id);
	$post_data   = get_postdata($post_id);
	$post_author_data = get_userdata($post_data['Author_ID']);

	if ( ($user_id == $post_author_data->ID)
	     || ($author_data->user_level > $post_author_data->user_level)
	     || ($author_data->user_level >= 10) ) {
		return true;
	} else {
		return false;
	}
}

/* returns true if $user_id can delete $post_id */
function user_can_delete_post($user_id, $post_id, $blog_id = 1) {
	// right now if one can edit, one can delete
	return user_can_edit_post($user_id, $post_id, $blog_id);
}

/* returns true if $user_id can set new posts' dates on $blog_id */
function user_can_set_post_date($user_id, $blog_id = 1, $category_id = 'None') {
	$author_data = get_userdata($user_id);
	return (($author_data->user_level > 4) && user_can_create_post($user_id, $blog_id, $category_id));
}

/* returns true if $user_id can edit $post_id's date */
function user_can_edit_post_date($user_id, $post_id, $blog_id = 1) {
	$author_data = get_userdata($user_id);
	return (($author_data->user_level > 4) && user_can_edit_post($user_id, $post_id, $blog_id));
}

/* returns true if $user_id can edit $post_id's comments */
function user_can_edit_post_comments($user_id, $post_id, $blog_id = 1) {
	// right now if one can edit a post, one can edit comments made on it
	return user_can_edit_post($user_id, $post_id, $blog_id);
}

/* returns true if $user_id can delete $post_id's comments */
function user_can_delete_post_comments($user_id, $post_id, $blog_id = 1) {
	// right now if one can edit comments, one can delete comments
	return user_can_edit_post_comments($user_id, $post_id, $blog_id);
}

function user_can_edit_user($user_id, $other_user) {
	$user  = get_userdata($user_id);
	$other = get_userdata($other_user);
	if ( $user->user_level > $other->user_level || $user->user_level > 8 || $user->ID == $other->ID )
		return true;
	else
		return false;
}


function sp_new_comment( $commentdata ) {
	global $spdb;

	extract($commentdata);

	$comment_post_ID = (int) $comment_post_ID;

	$author  = apply_filters('pre_comment_author_name', $comment_author);
	$email   = apply_filters('pre_comment_author_email', $comment_author_email);
	$url     = apply_filters('pre_comment_author_url', $comment_author_url);
	$comment = apply_filters('pre_comment_content', $comment_content);
	$comment = apply_filters('post_comment_text', $comment); // Deprecated
	$comment = apply_filters('comment_content_presave', $comment_content); // Deprecated

	$user_ip     = apply_filters('pre_comment_user_ip', $_SERVER['REMOTE_ADDR']);
	$user_domain = apply_filters('pre_comment_user_domain', gethostbyaddr($user_ip) );
	$user_agent  = apply_filters('pre_comment_user_agent', $_SERVER['HTTP_USER_AGENT']);

	$now     = current_time('mysql');
	$now_gmt = current_time('mysql', 1);

	// Simple flood-protection
	if ( $lasttime = $spdb->get_var("SELECT comment_date_gmt FROM $spdb->comments WHERE comment_author_IP = '$user_ip' OR comment_author_email = '$email' ORDER BY comment_date DESC LIMIT 1") ) {
		$time_lastcomment = mysql2date('U', $lasttime);
		$time_newcomment  = mysql2date('U', $now_gmt);
		if ( ($time_newcomment - $time_lastcomment) < 15 )
			die( __('Sorry, you can only post a new comment once every 15 seconds. Slow down cowboy.') );
	}

	if( check_comment($author, $email, $url, $comment, $user_ip, $user_agent) )
		$approved = 1;
	else
		$approved = 0;

	$result = $spdb->query("INSERT INTO $spdb->comments 
	(comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_date_gmt, comment_content, comment_approved, comment_agent, comment_type)
	VALUES 
	('$comment_post_ID', '$comment_author', '$comment_author_email', '$comment_author_url', '$user_ip', '$now', '$now_gmt', '$comment_content', '$approved', '$user_agent', '$comment_type')
	");

	$comment_id = $spdb->insert_id;
	do_action('comment_post', $comment_id);

	if ( !$approved )
		sp_notify_moderator($comment_id);

	if ( get_settings('comments_notify') && $approved )
		sp_notify_postauthor($comment_id, 'comment');

	return $result;
}

function do_trackbacks($post_id) {
	global $spdb;

	$post = $spdb->get_row("SELECT * FROM $spdb->posts WHERE ID = $post_id");
	$to_ping = get_to_ping($post_id);
	$pinged  = get_pung($post_id);
	$content = strip_tags($post->post_content);
	$excerpt = strip_tags($post->post_excerpt);
	$post_title = strip_tags($post->post_title);

	if ( $excerpt )
		$excerpt = substr($excerpt, 0, 252) . '...';
	else
		$excerpt = substr($content, 0, 252) . '...';

	if ($to_ping) : foreach ($to_ping as $tb_ping) :
		$tb_ping = trim($tb_ping);
		if ( !in_array($tb_ping, $pinged) )
		 trackback($tb_ping, $post_title, $excerpt, $post_id);
	endforeach; endif;
}

function get_pung($post_id) { // Get URIs already pung for a post
	global $spdb;
	$pung = $spdb->get_var("SELECT pinged FROM $spdb->posts WHERE ID = $post_id");
	$pung = trim($pung);
	$pung = preg_split('/\s/', $pung);
	return $pung;
}

function get_to_ping($post_id) { // Get any URIs in the todo list
	global $spdb;
	$to_ping = $spdb->get_var("SELECT to_ping FROM $spdb->posts WHERE ID = $post_id");
	$to_ping = trim($to_ping);
	$to_ping = preg_split('/\s/', $to_ping);
	return $to_ping;
}

function add_ping($post_id, $uri) { // Add a URI to those already pung
	global $spdb;
	$pung = $spdb->get_var("SELECT pinged FROM $spdb->posts WHERE ID = $post_id");
	$pung = trim($pung);
	$pung = preg_split('/\s/', $pung);
	$pung[] = $uri;
	$new = implode("\n", $pung);
	return $spdb->query("UPDATE $spdb->posts SET pinged = '$new' WHERE ID = $post_id");
}

?>
<?php

require_once(dirname(__FILE__).'/functions-compat.php');

if (!function_exists('_')) {
	function _($string) {
		return $string;
	}
}

function get_profile($field, $user = false) {
	global $spdb;
	if (!$user)
		$user = $spdb->escape($_COOKIE['steampressuser_' . COOKIEHASH]);
	return $spdb->get_var("SELECT $field FROM $spdb->users WHERE user_login = '$user'");
}

function mysql2date($dateformatstring, $mysqlstring, $use_b2configmonthsdays = 1) {
	global $month, $weekday, $month_abbrev, $weekday_abbrev;
	$m = $mysqlstring;
	if (empty($m)) {
		return false;
	}
	$i = mktime(substr($m,11,2),substr($m,14,2),substr($m,17,2),substr($m,5,2),substr($m,8,2),substr($m,0,4)); 
	if (!empty($month) && !empty($weekday) && $use_b2configmonthsdays) {
		$datemonth = $month[date('m', $i)];
		$datemonth_abbrev = $month_abbrev[$datemonth];
		$dateweekday = $weekday[date('w', $i)];
		$dateweekday_abbrev = $weekday_abbrev[$dateweekday]; 		
		$dateformatstring = ' '.$dateformatstring;
		$dateformatstring = preg_replace("/([^\\\])D/", "\\1".backslashit($dateweekday_abbrev), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])F/", "\\1".backslashit($datemonth), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])l/", "\\1".backslashit($dateweekday), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])M/", "\\1".backslashit($datemonth_abbrev), $dateformatstring);
	
		$dateformatstring = substr($dateformatstring, 1, strlen($dateformatstring)-1);
	}
	$j = @date($dateformatstring, $i);
	if (!$j) {
	// for debug purposes
	//	echo $i." ".$mysqlstring;
	}
	return $j;
}

function current_time($type, $gmt = 0) {
	switch ($type) {
		case 'mysql':
			if ($gmt) $d = gmdate('Y-m-d H:i:s');
			else $d = gmdate('Y-m-d H:i:s', (time() + (get_settings('gmt_offset') * 3600)));
			return $d;
			break;
		case 'timestamp':
			if ($gmt) $d = time();
			else $d = time() + (get_settings('gmt_offset') * 3600);
			return $d;
			break;
	}
}

function date_i18n($dateformatstring, $unixtimestamp) {
	global $month, $weekday;
	$i = $unixtimestamp; 
	if ((!empty($month)) && (!empty($weekday))) {
		$datemonth = $month[date('m', $i)];
		$dateweekday = $weekday[date('w', $i)];
		$dateformatstring = ' '.$dateformatstring;
		$dateformatstring = preg_replace("/([^\\\])D/", "\\1".backslashit(substr($dateweekday, 0, 3)), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])F/", "\\1".backslashit($datemonth), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])l/", "\\1".backslashit($dateweekday), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])M/", "\\1".backslashit(substr($datemonth, 0, 3)), $dateformatstring);
		$dateformatstring = substr($dateformatstring, 1, strlen($dateformatstring)-1);
	}
	$j = @date($dateformatstring, $i);
	return $j;
	}

function get_weekstartend($mysqlstring, $start_of_week) {
	$my = substr($mysqlstring,0,4);
	$mm = substr($mysqlstring,8,2);
	$md = substr($mysqlstring,5,2);
	$day = mktime(0,0,0, $md, $mm, $my);
	$weekday = date('w',$day);
	$i = 86400;
	while ($weekday > get_settings('start_of_week')) {
		$weekday = date('w',$day);
		$day = $day - 86400;
		$i = 0;
	}
	$week['start'] = $day + 86400 - $i;
	$week['end']   = $day + 691199;
	return $week;
}

function get_lastpostdate($timezone = 'server') {
	global $cache_lastpostdate, $pagenow, $spdb;
	$add_seconds_blog = get_settings('gmt_offset') * 3600;
	$add_seconds_server = date('Z');
	$now = current_time('mysql', 1);
	if ( !isset($cache_lastpostdate[$timezone]) ) {
		switch(strtolower($timezone)) {
			case 'gmt':
				$lastpostdate = $spdb->get_var("SELECT post_date_gmt FROM $spdb->posts WHERE post_date_gmt <= '$now' AND post_status = 'publish' ORDER BY post_date_gmt DESC LIMIT 1");
				break;
			case 'blog':
				$lastpostdate = $spdb->get_var("SELECT post_date FROM $spdb->posts WHERE post_date_gmt <= '$now' AND post_status = 'publish' ORDER BY post_date_gmt DESC LIMIT 1");
				break;
			case 'server':
				$lastpostdate = $spdb->get_var("SELECT DATE_ADD(post_date_gmt, INTERVAL '$add_seconds_server' SECOND) FROM $spdb->posts WHERE post_date_gmt <= '$now' AND post_status = 'publish' ORDER BY post_date_gmt DESC LIMIT 1");
				break;
		}
		$cache_lastpostdate[$timezone] = $lastpostdate;
	} else {
		$lastpostdate = $cache_lastpostdate[$timezone];
	}
	return $lastpostdate;
}

function get_lastpostmodified($timezone = 'server') {
	global $cache_lastpostmodified, $pagenow, $spdb;
	$add_seconds_blog = get_settings('gmt_offset') * 3600;
	$add_seconds_server = date('Z');
	$now = current_time('mysql', 1);
	if ( !isset($cache_lastpostmodified[$timezone]) ) {
		switch(strtolower($timezone)) {
			case 'gmt':
				$lastpostmodified = $spdb->get_var("SELECT post_modified_gmt FROM $spdb->posts WHERE post_modified_gmt <= '$now' AND post_status = 'publish' ORDER BY post_modified_gmt DESC LIMIT 1");
				break;
			case 'blog':
				$lastpostmodified = $spdb->get_var("SELECT post_modified FROM $spdb->posts WHERE post_modified_gmt <= '$now' AND post_status = 'publish' ORDER BY post_modified_gmt DESC LIMIT 1");
				break;
			case 'server':
				$lastpostmodified = $spdb->get_var("SELECT DATE_ADD(post_modified_gmt, INTERVAL '$add_seconds_server' SECOND) FROM $spdb->posts WHERE post_modified_gmt <= '$now' AND post_status = 'publish' ORDER BY post_modified_gmt DESC LIMIT 1");
				break;
		}
		$lastpostdate = get_lastpostdate($timezone);
		if ($lastpostdate > $lastpostmodified) {
			$lastpostmodified = $lastpostdate;
		}
		$cache_lastpostmodified[$timezone] = $lastpostmodified;
	} else {
		$lastpostmodified = $cache_lastpostmodified[$timezone];
	}
	return $lastpostmodified;
}

function user_pass_ok($user_login,$user_pass) {
	global $cache_userdata;
	if ( empty($cache_userdata[$user_login]) ) {
		$userdata = get_userdatabylogin($user_login);
	} else {
		$userdata = $cache_userdata[$user_login];
	}
	return (md5($user_pass) == $userdata->user_pass);
}

function get_currentuserinfo() { // a bit like get_userdata(), on steroids
	global $user_login, $userdata, $user_level, $user_ID, $user_nickname, $user_email, $user_url, $user_pass_md5;
	// *** retrieving user's data from cookies and db - no spoofing

	if (isset($_COOKIE['steampressuser_' . COOKIEHASH])) 
		$user_login = $_COOKIE['steampressuser_' . COOKIEHASH];
	$userdata = get_userdatabylogin($user_login);
	$user_level = $userdata->user_level;
	$user_ID = $userdata->ID;
	$user_nickname = $userdata->user_nickname;
	$user_email = $userdata->user_email;
	$user_url = $userdata->user_url;
	$user_pass_md5 = md5($userdata->user_pass);
}

function get_userdata($userid) {
	global $spdb, $cache_userdata;
	$userid = (int) $userid;
	if ( empty($cache_userdata[$userid]) ) {
        $cache_userdata[$userid] = 
            $spdb->get_row("SELECT * FROM $spdb->users WHERE ID = '$userid'");
	} 

    return $cache_userdata[$userid];
}

function get_userdatabylogin($user_login) {
	global $cache_userdata, $spdb;
	if ( !empty($user_login) && empty($cache_userdata["$user_login"]) ) {
		$user = $spdb->get_row("SELECT * FROM $spdb->users WHERE user_login = '$user_login'");
		$cache_userdata["$user_login"] = $user;
	} else {
		$user = $cache_userdata["$user_login"];
	}
	return $user;
}

function get_userid($user_login) {
	global $cache_userdata, $spdb;
	if ( !empty($user_login) && empty($cache_userdata["$user_login"]) ) {
		$user_id = $spdb->get_var("SELECT ID FROM $spdb->users WHERE user_login = '$user_login'");

		$cache_userdata["$user_login"] = $user_id;
	} else {
		$user_id = $cache_userdata["$user_login"];
	}
	return $user_id;
}

function get_usernumposts($userid) {
	global $spdb;
	return $spdb->get_var("SELECT COUNT(*) FROM $spdb->posts WHERE post_author = '$userid'");
}

// examine a url (supposedly from this blog) and try to
// determine the post ID it represents.
function url_to_postid($url = '') {
	global $spdb;

	$siteurl = get_settings('home');
	// Take a link like 'http://example.com/blog/something'
	// and extract just the '/something':
	$uri = preg_replace("#$siteurl#i", '', $url);

	// on failure, preg_replace just returns the subject string
	// so if $uri and $siteurl are the same, they didn't match:
	if ($uri == $siteurl) 
		return 0;
		
	// First, check to see if there is a 'p=N' to match against:
	preg_match('#[?&]p=(\d+)#', $uri, $values);
	$p = intval($values[1]);
	if ($p) return $p;
	
	// Match $uri against our permalink structure
	$permalink_structure = get_settings('permalink_structure');
	
	// Matt's tokenizer code
	$rewritecode = array(
		'%year%',
		'%monthnum%',
		'%day%',
		'%hour%',
		'%minute%',
		'%second%',
		'%postname%',
		'%post_id%'
	);
	$rewritereplace = array(
		'([0-9]{4})?',
		'([0-9]{1,2})?',
		'([0-9]{1,2})?',
		'([0-9]{1,2})?',
		'([0-9]{1,2})?',
		'([0-9]{1,2})?',
		'([_0-9a-z-]+)?',
		'([0-9]+)?'
	);

	// Turn the structure into a regular expression
	$matchre = str_replace('/', '/?', $permalink_structure);
	$matchre = str_replace($rewritecode, $rewritereplace, $matchre);

	// Extract the key values from the uri:
	preg_match("#$matchre#",$uri,$values);

	// Extract the token names from the structure:
	preg_match_all("#%(.+?)%#", $permalink_structure, $tokens);

	for($i = 0; $i < count($tokens[1]); $i++) {
		$name = $tokens[1][$i];
		$value = $values[$i+1];

		// Create a variable named $year, $monthnum, $day, $postname, or $post_id:
		$$name = $value;
	}
	
	// If using %post_id%, we're done:
	if (intval($post_id)) return intval($post_id);

	// Otherwise, build a WHERE clause, making the values safe along the way:
	if ($year) $where .= " AND YEAR(post_date) = '" . intval($year) . "'";
	if ($monthnum) $where .= " AND MONTH(post_date) = '" . intval($monthnum) . "'";
	if ($day) $where .= " AND DAYOFMONTH(post_date) = '" . intval($day) . "'";
	if ($hour) $where .= " AND HOUR(post_date) = '" . intval($hour) . "'";
	if ($minute) $where .= " AND MINUTE(post_date) = '" . intval($minute) . "'";
	if ($second) $where .= " AND SECOND(post_date) = '" . intval($second) . "'";
	if ($postname) $where .= " AND post_name = '" . $spdb->escape($postname) . "' ";

	// We got no indication, so we return false:
	if (!strlen($where)) {
		return false;
	}

	// Run the query to get the post ID:
	$id = intval($spdb->get_var("SELECT ID FROM $spdb->posts WHERE 1 = 1 " . $where));

	return $id;
}


/* Options functions */

function get_settings($setting) {
  global $spdb, $cache_settings, $cache_nonexistantoptions;
	if ( strstr($_SERVER['REQUEST_URI'], 'sp-admin/install.php') )
		return false;

	if ( empty($cache_settings) )
		$cache_settings = get_alloptions();

	if ( empty($cache_nonexistantoptions) )
		$cache_nonexistantoptions = array();

	if ('home' == $setting && '' == $cache_settings->home)
		return $cache_settings->siteurl;

	if ( isset($cache_settings->$setting) ) :
		return $cache_settings->$setting;
	else :
		// for these cases when we're asking for an unknown option
		if ( isset($cache_nonexistantoptions[$setting]) )
			return false;

		$option = $spdb->get_var("SELECT option_value FROM $spdb->options WHERE option_name = '$setting'");

		if (!$option) :
			$cache_nonexistantoptions[$setting] = true;
			return false;
		endif;

		@ $kellogs = unserialize($option);
		if ($kellogs !== FALSE)
			return $kellogs;
		else return $option;
	endif;
}

function get_option($option) {
	return get_settings($option);
}

function form_option($option) {
	echo htmlspecialchars( get_option($option), ENT_QUOTES );
}

function get_alloptions() {
	global $spdb, $sp_queries;
	$spdb->hide_errors();
	if (!$options = $spdb->get_results("SELECT option_name, option_value FROM $spdb->options WHERE autoload = 'yes'")) {
		include_once(ABSPATH . '/sp-admin/upgrade-functions.php');
		make_db_current_silent();
		$options = $spdb->get_results("SELECT option_name, option_value FROM $spdb->options");
	}
	$spdb->show_errors();

	foreach ($options as $option) {
		// "When trying to design a foolproof system, 
		//  never underestimate the ingenuity of the fools :)" -- Dougal
		if ('siteurl' == $option->option_name) $option->option_value = preg_replace('|/+$|', '', $option->option_value);
		if ('home' == $option->option_name) $option->option_value = preg_replace('|/+$|', '', $option->option_value);
		if ('category_base' == $option->option_name) $option->option_value = preg_replace('|/+$|', '', $option->option_value);
		@ $value = unserialize($option->option_value);
		if ($value === FALSE)
			$value = $option->option_value;
		$all_options->{$option->option_name} = $value;
	}
	return $all_options;
}

function update_option($option_name, $newvalue) {
	global $spdb, $cache_settings;
	if ( is_array($newvalue) || is_object($value) )
		$newvalue = serialize($newvalue);

	$newvalue = trim($newvalue); // I can't think of any situation we wouldn't want to trim

    // If the new and old values are the same, no need to update.
    if ($newvalue == get_settings($option_name)) {
        return true;
    }

	$newvalue = $spdb->escape($newvalue);
	$spdb->query("UPDATE $spdb->options SET option_value = '$newvalue' WHERE option_name = '$option_name'");
	$cache_settings = get_alloptions(); // Re cache settings
	return true;
}


// thx Alex Stapleton, http://alex.vort-x.net/blog/
function add_option($name, $value = '', $description = '') {
	global $spdb;
	if ( is_array($value) || is_object($value) )
		$value = serialize($value);

	if( !$spdb->get_var("SELECT option_name FROM $spdb->options WHERE option_name = '$name'") ) {
		$name = $spdb->escape($name);
		$value = $spdb->escape($value);
		$description = $spdb->escape($description);
		$spdb->query("INSERT INTO $spdb->options (option_name, option_value, option_description) VALUES ('$name', '$value', '$description')");

		if($spdb->insert_id) {
			global $cache_settings;
			$cache_settings->{$name} = $value;
		}
	}
	return;
}

function delete_option($name) {
	global $spdb;
	// Get the ID, if no ID then return
	$option_id = $spdb->get_var("SELECT option_id FROM $spdb->options WHERE option_name = '$name'");
	if (!$option_id) return false;
	$spdb->query("DELETE FROM $spdb->options WHERE option_name = '$name'");
	return true;
}

function add_post_meta($post_id, $key, $value, $unique = false) {
	global $spdb;
	
	if ($unique) {
		if( $spdb->get_var("SELECT meta_key FROM $spdb->postmeta WHERE meta_key
= '$key' AND post_id = '$post_id'") ) {
			return false;
		}
	}

	$spdb->query("INSERT INTO $spdb->postmeta
                                (post_id,meta_key,meta_value) 
                                VALUES ('$post_id','$key','$value')
                        ");
	
	return true;
}

function delete_post_meta($post_id, $key, $value = '') {
	global $spdb;

	if (empty($value)) {
		$meta_id = $spdb->get_var("SELECT meta_id FROM $spdb->postmeta WHERE
post_id = '$post_id' AND meta_key = '$key'");
	} else {
		$meta_id = $spdb->get_var("SELECT meta_id FROM $spdb->postmeta WHERE
post_id = '$post_id' AND meta_key = '$key' AND meta_value = '$value'");
	}

	if (!$meta_id) return false;

	if (empty($value)) {
		$spdb->query("DELETE FROM $spdb->postmeta WHERE post_id = '$post_id'
AND meta_key = '$key'");
	} else {
		$spdb->query("DELETE FROM $spdb->postmeta WHERE post_id = '$post_id'
AND meta_key = '$key' AND meta_value = '$value'");
	}
        
	return true;
}

function get_post_meta($post_id, $key, $single = false) {
	global $spdb, $post_meta_cache;

	if (isset($post_meta_cache[$post_id][$key])) {
		if ($single) {
			return $post_meta_cache[$post_id][$key][0];
		} else {
			return $post_meta_cache[$post_id][$key];
		}
	}

	$metalist = $spdb->get_results("SELECT meta_value FROM $spdb->postmeta WHERE post_id = '$post_id' AND meta_key = '$key'", ARRAY_N);

	$values = array();
	if ($metalist) {
		foreach ($metalist as $metarow) {
			$values[] = $metarow[0];
		}
	}

	if ($single) {
		if (count($values)) {
			return $values[0];
		} else {
			return '';
		}
	} else {
		return $values;
	}
}

function update_post_meta($post_id, $key, $value, $prev_value = '') {
	global $spdb, $post_meta_cache;

		if(! $spdb->get_var("SELECT meta_key FROM $spdb->postmeta WHERE meta_key
= '$key' AND post_id = '$post_id'") ) {
			return false;
		}

	if (empty($prev_value)) {
		$spdb->query("UPDATE $spdb->postmeta SET meta_value = '$value' WHERE
meta_key = '$key' AND post_id = '$post_id'");
	} else {
		$spdb->query("UPDATE $spdb->postmeta SET meta_value = '$value' WHERE
meta_key = '$key' AND post_id = '$post_id' AND meta_value = '$prev_value'");
	}

	return true;
}

function get_postdata($postid) {
	global $post, $spdb;

	$post = $spdb->get_row("SELECT * FROM $spdb->posts WHERE ID = '$postid'");
	
	$postdata = array (
		'ID' => $post->ID, 
		'Author_ID' => $post->post_author, 
		'Date' => $post->post_date, 
		'Content' => $post->post_content, 
		'Excerpt' => $post->post_excerpt, 
		'Title' => $post->post_title, 
		'Category' => $post->post_category,
		'post_status' => $post->post_status,
		'comment_status' => $post->comment_status,
		'ping_status' => $post->ping_status,
		'post_password' => $post->post_password,
		'to_ping' => $post->to_ping,
		'pinged' => $post->pinged,
		'post_name' => $post->post_name
	);
	return $postdata;
}

function get_catname($cat_ID) {
	global $cache_catnames, $spdb;
	if ( !$cache_catnames ) {
        $results = $spdb->get_results("SELECT * FROM $spdb->categories") or die('Oops, couldn\'t query the db for categories.');
		foreach ($results as $post) {
			$cache_catnames[$post->cat_ID] = $post->cat_name;
		}
	}
	$cat_name = $cache_catnames[$cat_ID];
	return $cat_name;
}

function gzip_compression() {
	if ( strstr($_SERVER['PHP_SELF'], 'sp-admin') ) return false;
	if ( !get_settings('gzipcompression') ) return false;

	if( extension_loaded('zlib') ) {
		ob_start('ob_gzhandler');
	}
}


// functions to count the page generation time (from phpBB2)
// ( or just any time between timer_start() and timer_stop() )

function timer_stop($display = 0, $precision = 3) { //if called like timer_stop(1), will echo $timetotal
	global $timestart, $timeend;
	$mtime = microtime();
	$mtime = explode(' ',$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$timeend = $mtime;
	$timetotal = $timeend-$timestart;
	if ($display)
		echo number_format($timetotal,$precision);
	return $timetotal;
}

function weblog_ping($server = '', $path = '') {

	global $sp_version;
	include_once (ABSPATH . SPINC . '/class-IXR.php');

	// using a timeout of 3 seconds should be enough to cover slow servers
	$client = new IXR_Client($server, ((!strlen(trim($path)) || ('/' == $path)) ? false : $path));
	$client->timeout = 3;
	$client->useragent .= ' -- SteamPress/'.$sp_version;

	// when set to true, this outputs debug messages by itself
	$client->debug = false;
	$client->query('weblogUpdates.ping', get_settings('blogname'), get_settings('home'));

}

function generic_ping($post_id = 0) {
	$services = get_settings('ping_sites');
	$services = preg_replace("|(\s)+|", '$1', $services); // Kill dupe lines
	$services = trim($services);
	if ('' != $services) {
		$services = explode("\n", $services);
		foreach ($services as $service) {
			weblog_ping($service);
		}
	}

	return $post_id;
}

add_action('publish_post', 'generic_ping');

// Send a Trackback
function trackback($trackback_url, $title, $excerpt, $ID) {
	global $spdb;
	$title = urlencode($title);
	$excerpt = urlencode($excerpt);
	$blog_name = urlencode(get_settings('blogname'));
	$tb_url = $trackback_url;
	$url = urlencode(get_permalink($ID));
	$query_string = "title=$title&url=$url&blog_name=$blog_name&excerpt=$excerpt";
	$trackback_url = parse_url($trackback_url);
	$http_request  = 'POST ' . $trackback_url['path'] . ($trackback_url['query'] ? '?'.$trackback_url['query'] : '') . " HTTP/1.0\r\n";
	$http_request .= 'Host: '.$trackback_url['host']."\r\n";
	$http_request .= 'Content-Type: application/x-www-form-urlencoded; charset='.get_settings('blog_charset')."\r\n";
	$http_request .= 'Content-Length: '.strlen($query_string)."\r\n";
	$http_request .= "\r\n";
	$http_request .= $query_string;
	if ( '' == $trackback_url['port'] )
		$trackback_url['port'] = 80;
	$fs = @fsockopen($trackback_url['host'], $trackback_url['port'], $errno, $errstr, 4);
	@fputs($fs, $http_request);
/*
	$debug_file = 'trackback.log';
	$fp = fopen($debug_file, 'a');
	fwrite($fp, "\n*****\nRequest:\n\n$http_request\n\nResponse:\n\n");
	while(!@feof($fs)) {
		fwrite($fp, @fgets($fs, 4096));
	}
	fwrite($fp, "\n\n");
	fclose($fp);
*/
	@fclose($fs);

	$spdb->query("UPDATE $spdb->posts SET pinged = CONCAT(pinged, '\n', '$tb_url') WHERE ID = '$ID'");
	$spdb->query("UPDATE $spdb->posts SET to_ping = REPLACE(to_ping, '$tb_url', '') WHERE ID = '$ID'");
	return $result;
}

function make_url_footnote($content) {
	preg_match_all('/<a(.+?)href=\"(.+?)\"(.*?)>(.+?)<\/a>/', $content, $matches);
	$j = 0;
	for ($i=0; $i<count($matches[0]); $i++) {
		$links_summary = (!$j) ? "\n" : $links_summary;
		$j++;
		$link_match = $matches[0][$i];
		$link_number = '['.($i+1).']';
		$link_url = $matches[2][$i];
		$link_text = $matches[4][$i];
		$content = str_replace($link_match, $link_text.' '.$link_number, $content);
		$link_url = (strtolower(substr($link_url,0,7)) != 'http://') ? get_settings('home') . $link_url : $link_url;
		$links_summary .= "\n".$link_number.' '.$link_url;
	}
	$content = strip_tags($content);
	$content .= $links_summary;
	return $content;
}


function xmlrpc_getposttitle($content) {
	global $post_default_title;
	if (preg_match('/<title>(.+?)<\/title>/is', $content, $matchtitle)) {
		$post_title = $matchtitle[0];
		$post_title = preg_replace('/<title>/si', '', $post_title);
		$post_title = preg_replace('/<\/title>/si', '', $post_title);
	} else {
		$post_title = $post_default_title;
	}
	return $post_title;
}
	
function xmlrpc_getpostcategory($content) {
	global $post_default_category;
	if (preg_match('/<category>(.+?)<\/category>/is', $content, $matchcat)) {
		$post_category = trim($matchcat[1], ',');
		$post_category = explode(',', $post_category);
	} else {
		$post_category = $post_default_category;
	}
	return $post_category;
}

function xmlrpc_removepostdata($content) {
	$content = preg_replace('/<title>(.+?)<\/title>/si', '', $content);
	$content = preg_replace('/<category>(.+?)<\/category>/si', '', $content);
	$content = trim($content);
	return $content;
}

function debug_fopen($filename, $mode) {
	global $debug;
	if ($debug == 1) {
		$fp = fopen($filename, $mode);
		return $fp;
	} else {
		return false;
	}
}

function debug_fwrite($fp, $string) {
	global $debug;
	if ($debug == 1) {
		fwrite($fp, $string);
	}
}

function debug_fclose($fp) {
	global $debug;
	if ($debug == 1) {
		fclose($fp);
	}
}

function do_enclose( $content, $post_ID ) {
	global $sp_version, $spdb;
	include_once (ABSPATH . SPINC . '/class-IXR.php');

	// original code by Mort (http://mort.mine.nu:8080)
	$log = debug_fopen(ABSPATH . '/pingback.log', 'a');
	$post_links = array();
	debug_fwrite($log, 'BEGIN '.date('YmdHis', time())."\n");

	$pung = get_pung($post_ID);

	// Variables
	$ltrs = '\w';
	$gunk = '/#~:.?+=&%@!\-';
	$punc = '.:?\-';
	$any = $ltrs . $gunk . $punc;

	// Step 1
	// Parsing the post, external links (if any) are stored in the $post_links array
	// This regexp comes straight from phpfreaks.com
	// http://www.phpfreaks.com/quickcode/Extract_All_URLs_on_a_Page/15.php
	preg_match_all("{\b http : [$any] +? (?= [$punc] * [^$any] | $)}x", $content, $post_links_temp);

	// Debug
	debug_fwrite($log, 'Post contents:');
	debug_fwrite($log, $content."\n");
	
	// Step 2.
	// Walking thru the links array
	// first we get rid of links pointing to sites, not to specific files
	// Example:
	// http://dummy-weblog.org
	// http://dummy-weblog.org/
	// http://dummy-weblog.org/post.php
	// We don't wanna ping first and second types, even if they have a valid <link/>

	foreach($post_links_temp[0] as $link_test) :
		if ( !in_array($link_test, $pung) ) : // If we haven't pung it already
			$test = parse_url($link_test);
			if (isset($test['query']))
				$post_links[] = $link_test;
			elseif(($test['path'] != '/') && ($test['path'] != ''))
				$post_links[] = $link_test;
		endif;
	endforeach;

	foreach ($post_links as $url){
                if( $url != '' && in_array($url, $pung) == false ) {
                    set_time_limit( 60 ); 
                    $file = str_replace( "http://", "", $url );
                    $host = substr( $file, 0, strpos( $file, "/" ) );
                    $file = substr( $file, strpos( $file, "/" ) );
                    $headers = "HEAD $file HTTP/1.1\r\nHOST: $host\r\n\r\n";
                    $port    = 80;
                    $timeout = 3;
                    $fp = @fsockopen($host, $port, $err_num, $err_msg, $timeout);
                    if( $fp ) {
                        fputs($fp, $headers );
                        $response = '';
                        while ( !feof($fp) && strpos( $response, "\r\n\r\n" ) == false )
                            $response .= fgets($fp, 2048);
                        fclose( $fp );
                    } else {
                        $response = '';
                    }
                    if( $response != '' ) {
                        $len = substr( $response, strpos( $response, "Content-Length:" ) + 16 );
                        $len = substr( $len, 0, strpos( $len, "\n" ) );
                        $type = substr( $response, strpos( $response, "Content-Type:" ) + 14 );
                        $type = substr( $type, 0, strpos( $type, "\n" ) + 1 );
                        $allowed_types = array( "video", "audio", "image" );
                        if( in_array( substr( $type, 0, strpos( $type, "/" ) ), $allowed_types ) ) {
                            $meta_value = "$url\n$len\n$type\n";
                            $query = "INSERT INTO `".$spdb->postmeta."` ( `meta_id` , `post_id` , `meta_key` , `meta_value` )
                                VALUES ( NULL, '$post_ID', 'enclosure' , '".$meta_value."')";
                            $spdb->query( $query );
                            add_ping( $post_ID, $url );
                        }
                    }
                }
        }
}

function start_wp($use_sp_query = false) {
  global $post, $id, $postdata, $authordata, $day, $preview, $page, $pages, $multipage, $more, $numpages, $sp_query;
	global $pagenow;

	if ($use_sp_query) {
	  $post = $sp_query->next_post();
	} else {
	  $sp_query->next_post();
	}

	if (!$preview) {
		$id = $post->ID;
	} else {
		$id = 0;
		$postdata = array (
			'ID' => 0,
			'Author_ID' => $_GET['preview_userid'],
			'Date' => $_GET['preview_date'],
			'Content' => $_GET['preview_content'],
			'Excerpt' => $_GET['preview_excerpt'],
			'Title' => $_GET['preview_title'],
			'Category' => $_GET['preview_category'],
			'Notify' => 1
			);
	}
	$authordata = get_userdata($post->post_author);

	$day = mysql2date('d.m.y', $post->post_date);
	$currentmonth = mysql2date('m', $post->post_date);
	$numpages = 1;
	if (!$page)
		$page = 1;
	if (isset($p))
		$more = 1;
	$content = $post->post_content;
	if (preg_match('/<!--nextpage-->/', $post->post_content)) {
		if ($page > 1)
			$more = 1;
		$multipage = 1;
		$content = $post->post_content;
		$content = str_replace("\n<!--nextpage-->\n", '<!--nextpage-->', $content);
		$content = str_replace("\n<!--nextpage-->", '<!--nextpage-->', $content);
		$content = str_replace("<!--nextpage-->\n", '<!--nextpage-->', $content);
		$pages = explode('<!--nextpage-->', $content);
		$numpages = count($pages);
	} else {
		$pages[0] = $post->post_content;
		$multipage = 0;
	}
	return true;
}

function is_new_day() {
	global $day, $previousday;
	if ($day != $previousday) {
		return(1);
	} else {
		return(0);
	}
}

// Filters: these are the core of SP's plugin architecture

function apply_filters($tag, $string, $filter = true) {
	global $sp_filter;
	if (isset($sp_filter['all'])) {
		foreach ($sp_filter['all'] as $priority => $functions) {
			if (isset($sp_filter[$tag][$priority]))
				$sp_filter[$tag][$priority] = array_merge($sp_filter['all'][$priority], $sp_filter[$tag][$priority]);
			else
				$sp_filter[$tag][$priority] = array_merge($sp_filter['all'][$priority], array());
			$sp_filter[$tag][$priority] = array_unique($sp_filter[$tag][$priority]);
		}

	}

	if (isset($sp_filter[$tag])) {
		ksort($sp_filter[$tag]);
		foreach ($sp_filter[$tag] as $priority => $functions) {
			if (!is_null($functions)) {
				foreach($functions as $function) {
					if ($filter)
						$string = call_user_func($function, $string);
					else
						call_user_func($function, $string);
				}
			}
		}
	}
	return $string;
}

function add_filter($tag, $function_to_add, $priority = 10) {
	global $sp_filter;
	// So the format is sp_filter['tag']['array of priorities']['array of functions']
	if (!@in_array($function_to_add, $sp_filter[$tag]["$priority"])) {
		$sp_filter[$tag]["$priority"][] = $function_to_add;
	}
	return true;
}

function remove_filter($tag, $function_to_remove, $priority = 10) {
	global $sp_filter;
	if (@in_array($function_to_remove, $sp_filter[$tag]["$priority"])) {
		foreach ($sp_filter[$tag]["$priority"] as $function) {
			if ($function_to_remove != $function) {
				$new_function_list[] = $function;
			}
		}
		$sp_filter[$tag]["$priority"] = $new_function_list;
	}
	//die(var_dump($sp_filter));
	return true;
}

// The *_action functions are just aliases for the *_filter functions, they take special strings instead of generic content

function do_action($tag, $string) {
	apply_filters($tag, $string, false);
	return $string;
}

function add_action($tag, $function_to_add, $priority = 10) {
	add_filter($tag, $function_to_add, $priority);
}

function remove_action($tag, $function_to_remove, $priority = 10) {
	remove_filter($tag, $function_to_remove, $priority);
}

function get_page_uri($page) {
	global $spdb;
	$page = $spdb->get_row("SELECT ID, post_name, post_parent FROM $spdb->posts WHERE ID = '$page'");

	$uri = urldecode($page->post_name);

	// A page cannot be it's own parent.
	if ($page->post_parent == $page->ID) {
		return $uri;
	}

	while ($page->post_parent != 0) {
		$page = $spdb->get_row("SELECT post_name, post_parent FROM $spdb->posts WHERE ID = '$page->post_parent'");
		$uri = urldecode($page->post_name) . "/" . $uri;
	}

	return $uri;
}

function get_posts($args) {
	global $spdb;
	parse_str($args, $r);
	if (!isset($r['numberposts'])) $r['numberposts'] = 5;
	if (!isset($r['offset'])) $r['offset'] = 0;
	// The following not implemented yet
	if (!isset($r['category'])) $r['category'] = '';
	if (!isset($r['orderby'])) $r['orderby'] = '';
	if (!isset($r['order'])) $r['order'] = '';

	$now = current_time('mysql');

	$posts = $spdb->get_results("SELECT DISTINCT * FROM $spdb->posts WHERE post_date <= '$now' AND (post_status = 'publish') GROUP BY $spdb->posts.ID ORDER BY post_date DESC LIMIT " . $r['offset'] . ',' . $r['numberposts']);

    update_post_caches($posts);
	
	return $posts;
}

function query_posts($query) {
    global $sp_query;

    return $sp_query->query($query);
}

function update_post_caches($posts) {
    global $category_cache, $comment_count_cache, $post_meta_cache;
    global $spdb;

    // No point in doing all this work if we didn't match any posts.
    if (! $posts) {
        return;
    }

    // Get the categories for all the posts
    foreach ($posts as $post) {
        $post_id_list[] = $post->ID;
    }
    $post_id_list = implode(',', $post_id_list);

    $dogs = $spdb->get_results("SELECT DISTINCT
        ID, category_id, cat_name, category_nicename, category_description, category_parent
        FROM $spdb->categories, $spdb->post2cat, $spdb->posts
        WHERE category_id = cat_ID AND post_id = ID AND post_id IN ($post_id_list)");
        
    if (!empty($dogs)) {
        foreach ($dogs as $catt) {
					$category_cache[$catt->ID][$catt->category_id] = $catt;
        }
    }

    // Do the same for comment numbers
    $comment_counts = $spdb->get_results("SELECT ID, COUNT( comment_ID ) AS ccount
        FROM $spdb->posts
        LEFT JOIN $spdb->comments ON ( comment_post_ID = ID  AND comment_approved =  '1')
        WHERE post_status =  'publish' AND ID IN ($post_id_list)
        GROUP BY ID");
    
    if ($comment_counts) {
        foreach ($comment_counts as $comment_count) {
            $comment_count_cache["$comment_count->ID"] = $comment_count->ccount;
        }
    }

    // Get post-meta info
    if ( $meta_list = $spdb->get_results("SELECT post_id, meta_key, meta_value FROM $spdb->postmeta  WHERE post_id IN($post_id_list) ORDER BY post_id, meta_key", ARRAY_A) ) {
		
        // Change from flat structure to hierarchical:
        $post_meta_cache = array();
        foreach ($meta_list as $metarow) {
            $mpid = $metarow['post_id'];
            $mkey = $metarow['meta_key'];
            $mval = $metarow['meta_value'];
			
            // Force subkeys to be array type:
            if (!isset($post_meta_cache[$mpid]) || !is_array($post_meta_cache[$mpid]))
                $post_meta_cache[$mpid] = array();
            if (!isset($post_meta_cache[$mpid]["$mkey"]) || !is_array($post_meta_cache[$mpid]["$mkey"]))
                $post_meta_cache[$mpid]["$mkey"] = array();
			
            // Add a value to the current pid/key:
            $post_meta_cache[$mpid][$mkey][] = $mval;
        }
    }
}

function update_category_cache() {
    global $cache_categories, $spdb;
    $dogs = $spdb->get_results("SELECT * FROM $spdb->categories");
    foreach ($dogs as $catt) {
        $cache_categories[$catt->cat_ID] = $catt;
    }
}

function update_user_cache() {
    global $cache_userdata, $spdb;

    if ( $users = $spdb->get_results("SELECT * FROM $spdb->users WHERE user_level > 0") ) :
		foreach ($users as $user) :
			$cache_userdata[$user->ID] = $user;
		endforeach;
		return true;
	else: 
		return false;
	endif;
}

function sp_head() {
	do_action('sp_head', '');
}

function is_single ($post = '') {
	global $sp_query;

	if (! $sp_query->is_single) {
		return false;
	}

	if (empty($post)) {
		return true;
	}

	$post_obj = $sp_query->get_queried_object();

	if ($post == $post_obj->ID) {
		return true;
	} else if ($post == $post_obj->post_title) {
		return true;
	} else if ($post == $post_obj->post_name) {
		return true;
	}

	return false;
}

function is_page ($page = '') {
	global $sp_query;

	if (! $sp_query->is_page) {
		return false;
	}

	if (empty($page)) {
		return true;
	}

	$page_obj = $sp_query->get_queried_object();
		
	if ($page == $page_obj->ID) {
		return true;
	} else if ($page == $page_obj->post_title) {
		return true;
	} else if ($page == $page_obj->post_name) {
		return true;
	}

	return false;
}

function is_archive () {
    global $sp_query;

    return $sp_query->is_archive;
}

function is_date () {
    global $sp_query;

    return $sp_query->is_date;
}

function is_year () {
    global $sp_query;

    return $sp_query->is_year;
}

function is_month () {
    global $sp_query;

    return $sp_query->is_month;
}

function is_day () {
    global $sp_query;

    return $sp_query->is_day;
}

function is_time () {
    global $sp_query;

    return $sp_query->is_time;
}

function is_author ($author = '') {
	global $sp_query;

	if (! $sp_query->is_author) {
		return false;
	}

	if (empty($author)) {
		return true;
	}

	$author_obj = $sp_query->get_queried_object();
		
	if ($author == $author_obj->ID) {
		return true;
	} else if ($author == $author_obj->user_nickname) {
		return true;
	} else if ($author == $author_obj->user_nicename) {
		return true;
	}

	return false;
}

function is_category ($category = '') {
	global $sp_query;

	if (! $sp_query->is_category) {
		return false;
	}

	if (empty($category)) {
		return true;
	}

	$cat_obj = $sp_query->get_queried_object();
		
	if ($category == $cat_obj->cat_ID) {
		return true;
	} else if ($category == $cat_obj->cat_name) {
		return true;
	} else if ($category == $cat_obj->category_nicename) {
		return true;
	}

	return false;
}

function is_search () {
    global $sp_query;

    return $sp_query->is_search;
}

function is_feed () {
    global $sp_query;

    return $sp_query->is_feed;
}

function is_trackback () {
    global $sp_query;

    return $sp_query->is_trackback;
}

function is_home () {
    global $sp_query;

    return $sp_query->is_home;
}

function is_404 () {
    global $sp_query;

    return $sp_query->is_404;
}

function is_paged () {
    global $sp_query;

    return $sp_query->is_paged;
}

function get_query_var($var) {
  global $sp_query;

  return $sp_query->get($var);
}

function have_posts() {
    global $sp_query;

    return $sp_query->have_posts();
}

function the_post() {
    start_wp(true);
}

function get_stylesheet() {
	return apply_filters('stylesheet', get_settings('stylesheet'));
}

function get_template() {
	return apply_filters('template', get_settings('template'));
}

function get_template_directory() {
	$template = get_template();

	if (empty($template) || $template == 'default') {
		$template = ABSPATH . "sp-content";
	} else {
		$template = ABSPATH . "sp-content/themes/$template";
	}

	return $template;
}

function get_theme_data($theme_file) {
	$theme_data = implode('', file($theme_file));
	preg_match("|Theme Name:(.*)|i", $theme_data, $theme_name);
	preg_match("|Theme URI:(.*)|i", $theme_data, $theme_uri);
	preg_match("|Description:(.*)|i", $theme_data, $description);
	preg_match("|Author:(.*)|i", $theme_data, $author_name);
	preg_match("|Author URI:(.*)|i", $theme_data, $author_uri);
	preg_match("|Template:(.*)|i", $theme_data, $template);
	if ( preg_match("|Version:(.*)|i", $theme_data, $version) )
		$version = $version[1];
	else
		$version ='';

	$description = sptexturize($description[1]);

	$name = $theme_name[1];
	$name = trim($name);
	$theme = $name;
	if ('' != $theme_uri[1] && '' != $name) {
		$theme = __("<a href='{$theme_uri[1]}' title='Visit theme homepage'>{$theme}</a>");
	}

	if ('' == $author_uri[1]) {
		$author = $author_name[1];
	} else {
		$author = __("<a href='{$author_uri[1]}' title='Visit author homepage'>{$author_name[1]}</a>");
	}

	return array('Name' => $name, 'Title' => $theme, 'Description' => $description, 'Author' => $author, 'Version' => $version, 'Template' => $template[1]);
}

function get_themes() {
	global $sp_themes;
	global $sp_broken_themes;

	if (isset($sp_themes)) {
		return $sp_themes;
	}

	$themes = array();
	$sp_broken_themes = array();
	$theme_loc = 'sp-content/themes';
	$theme_root = ABSPATH . $theme_loc;

	// Files in sp-content/themes directory
	$themes_dir = @ dir($theme_root);
	if ($themes_dir) {
		while(($theme_dir = $themes_dir->read()) !== false) {
			if (is_dir($theme_root . '/' . $theme_dir)) {
				if ($theme_dir == '.' || $theme_dir == '..') {
					continue;
				}
				$stylish_dir = @ dir($theme_root . '/' . $theme_dir);
				$found_stylesheet = false;
				while(($theme_file = $stylish_dir->read()) !== false) {
					if ( $theme_file == 'style.css' ) {
						$theme_files[] = $theme_dir . '/' . $theme_file;
						$found_stylesheet = true;
						break;
					}
				}
				if (!$found_stylesheet) {
					$sp_broken_themes[$theme_dir] = array('Name' => $theme_dir, 'Title' => $theme_dir, 'Description' => __('Stylesheet is missing.'));
				}
			}
		}
	}

	$default_files = array('sp-comments.php', 'sp-comments-popup.php', 'sp-comments-post.php', 'sp-footer.php', 'sp-header.php', 'sp-sidebar.php', 'footer.php', 'header.php', 'sidebar.php');

	// Get the files for the default template.
	$default_template_files = array();
	{
		// Find the index.
		if (file_exists(ABSPATH  .'sp-content/index.php')) {
			$default_template_files[] = 'sp-content/index.php';
		} else {
			$default_template_files[] = 'index.php';
		}
		
		$dirs = array('', 'sp-content');
		foreach ($dirs as $dir) {
			$template_dir = @ dir(ABSPATH . $dir);
			while(($file = $template_dir->read()) !== false) {
				if ( !preg_match('|^\.+$|', $file) && in_array($file, $default_files)) 
					$default_template_files[] = trim("$dir/$file", '/');
			}
		}
	}

	// Get the files for the default stylesheet.
	$default_stylesheet_files = array();
	{
		$stylesheet_dir = @ dir(ABSPATH);
		while(($file = $stylesheet_dir->read()) !== false) {
			if ( !preg_match('|^\.+$|', $file) && preg_match('|\.css$|', $file)) 
				$default_stylesheet_files[] = "$file";
		}
	}
	
	// The default theme always exists.
	$themes['Default'] = array('Name' => 'Default', 'Title' => 'SteamPress Default', 'Description' => 'The default theme included with SteamPress.', 'Author' => 'Samir M. Nassar', 'Version' => '1.0', 'Template' => 'default', 'Stylesheet' => 'default', 'Template Files' => $default_template_files, 'Stylesheet Files' => $default_stylesheet_files, 'Template Dir' => '/', 'Stylesheet Dir' => '/', 'Parent Theme' => '');

	if (!$themes_dir || !$theme_files) {
		return $themes;
	}

	sort($theme_files);

	foreach($theme_files as $theme_file) {
		$theme_data = get_theme_data("$theme_root/$theme_file");
	  
		$name = $theme_data['Name']; 
		$title = $theme_data['Title'];
		$description = sptexturize($theme_data['Description']);
		$version = $theme_data['Version'];
		$author = $theme_data['Author'];
		$template = $theme_data['Template'];
		$stylesheet = dirname($theme_file);

		if (empty($name)) {
			$name = dirname($theme_file);
			$title = $name;
		}

		if (empty($template)) {
			if (file_exists(dirname("$theme_root/$theme_file/index.php"))) {
				$template = dirname($theme_file);
			} else {
				continue;
			}
		}

		$template = trim($template);

		if (($template != 'default') && (! file_exists("$theme_root/$template/index.php"))) {
			$sp_broken_themes[$name] = array('Name' => $name, 'Title' => $title, 'Description' => __('Template is missing.'));
			continue;
		}
		
		$stylesheet_files = array();
		if ($stylesheet != 'default') {
			$stylesheet_dir = @ dir("$theme_root/$stylesheet");
			if ($stylesheet_dir) {
				while(($file = $stylesheet_dir->read()) !== false) {
					if ( !preg_match('|^\.+$|', $file) && preg_match('|\.css$|', $file) ) 
						$stylesheet_files[] = "$theme_loc/$stylesheet/$file";
				}
			}
		} else {
			$stylesheet_files = $default_stylesheet_files;
		}

		$template_files = array();		
		if ($template != 'default') {
			$template_dir = @ dir("$theme_root/$template");
			if ($template_dir) {
				while(($file = $template_dir->read()) !== false) {
					if ( !preg_match('|^\.+$|', $file) && preg_match('|\.php$|', $file) ) 
						$template_files[] = "$theme_loc/$template/$file";
				}
			}
		} else {
			$template_files = $default_template_files;
		}

		$template_dir = dirname($template_files[0]);
		$stylesheet_dir = dirname($stylesheet_files[0]);

		if (empty($template_dir)) $template_dir = '/';
		if (empty($stylesheet_dir)) $stylesheet_dir = '/';
		
		$themes[$name] = array('Name' => $name, 'Title' => $title, 'Description' => $description, 'Author' => $author, 'Version' => $version, 'Template' => $template, 'Stylesheet' => $stylesheet, 'Template Files' => $template_files, 'Stylesheet Files' => $stylesheet_files, 'Template Dir' => $template_dir, 'Stylesheet Dir' => $stylesheet_dir);
	}

	// Resolve theme dependencies.
	$theme_names = array_keys($themes);

	foreach ($theme_names as $theme_name) {
		$themes[$theme_name]['Parent Theme'] = '';
		if ($themes[$theme_name]['Stylesheet'] != $themes[$theme_name]['Template']) {
			foreach ($theme_names as $parent_theme_name) {
				if (($themes[$parent_theme_name]['Stylesheet'] == $themes[$parent_theme_name]['Template']) && ($themes[$parent_theme_name]['Template'] == $themes[$theme_name]['Template'])) {
					$themes[$theme_name]['Parent Theme'] = $themes[$parent_theme_name]['Name'];
					break;
				}
			}
		}
	}

	$sp_themes = $themes;

	return $themes;
}

function get_theme($theme) {
	$themes = get_themes();

	if (array_key_exists($theme, $themes)) {
		return $themes[$theme];
	}

	return NULL;
}

function get_current_theme() {
	$themes = get_themes();
	$theme_names = array_keys($themes);
	$current_template = get_settings('template');
	$current_stylesheet = get_settings('stylesheet');
	$current_theme = 'Default';

	if ($themes) {
		foreach ($theme_names as $theme_name) {
			if ($themes[$theme_name]['Stylesheet'] == $current_stylesheet &&
					$themes[$theme_name]['Template'] == $current_template) {
				$current_theme = $themes[$theme_name]['Name'];
			}
		}
	}

	return $current_theme;
}

function get_page_template() {
	global $sp_query;

	$id = $sp_query->post->ID;	
	$template_dir = get_template_directory();
	$default = "$template_dir/page.php";

	$template = get_post_meta($id, '_sp_page_template', true);

	if (empty($template) || ($template == 'default')) {
		return $default;
	}

	if (file_exists("$template_dir/$template")) {
		return "$template_dir/$template";
	}

	return $default;
}

// Borrowed from the PHP Manual user notes. Convert entities, while
// preserving already-encoded entities:
function htmlentities2($myHTML) {
	$translation_table=get_html_translation_table (HTML_ENTITIES,ENT_QUOTES);
	$translation_table[chr(38)] = '&';
	return preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/","&amp;" , strtr($myHTML, $translation_table));
}


function sp_mail($to, $subject, $message, $headers = '', $more = '') {
        if( $headers == '' ) {
                $headers = "MIME-Version: 1.0\n" .
                           "From: " . $to . " <" . $to . ">\n" .
                           "Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\n";
        }
	if ( function_exists('mb_send_mail') )
                return mb_send_mail($to, $subject, $message, $headers, $more);
	else
		return mail($to, $subject, $message, $headers, $more);
}

if ( !function_exists('sp_login') ) :
function sp_login($username, $password, $already_md5 = false) {
	global $spdb, $error;

	if ( !$username )
		return false;

	if ( !$password ) {
		$error = __('<strong>Error</strong>: The password field is empty.');
		return false;
	}

	$login = $spdb->get_row("SELECT ID, user_login, user_pass FROM $spdb->users WHERE user_login = '$username'");

	if (!$login) {
		$error = __('<strong>Error</strong>: Wrong login.');
		return false;
	} else {
		// If the password is already_md5, it has been double hashed.
		// Otherwise, it is plain text.
		if ( ($already_md5 && $login->user_login == $username && md5($login->user_pass) == $password) || ($login->user_login == $username && $login->user_pass == md5($password)) ) {
			return true;
		} else {
			$error = __('<strong>Error</strong>: Incorrect password.');
			$pwd = '';
			return false;
		}
	}
}
endif;

if ( !function_exists('auth_redirect') ) :
function auth_redirect() {
	// Checks if a user is logged in, if not redirects them to the login page
	if ( (!empty($_COOKIE['steampressuser_' . COOKIEHASH]) && 
	!sp_login($_COOKIE['steampressuser_' . COOKIEHASH], $_COOKIE['steampresspass_' . COOKIEHASH], true)) ||
	 (empty($_COOKIE['steampressuser_' . COOKIEHASH])) ) {
		header('Expires: Wed, 5 Jun 1979 23:41:00 GMT'); // Michel's birthday
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
	
		header('Location: ' . get_settings('siteurl') . '/sp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
		exit();
	}
}
endif;

function is_plugin_page() {
	global $plugin_page;

	if (isset($plugin_page)) {
		return true;
	}

	return false;
}

/*
add_query_arg: Returns a modified querystring by adding
a single key & value or an associative array.
Setting a key value to emptystring removes the key.
Omitting oldquery_or_uri uses the $_SERVER value.

Parameters:
add_query_arg(newkey, newvalue, oldquery_or_uri) or
add_query_arg(associative_array, oldquery_or_uri)
*/
function add_query_arg() {
	$ret = '';
	if(is_array(func_get_arg(0))) {
		$uri = @func_get_arg(1);
	}
	else {
		if (@func_num_args() < 3) {
			$uri = $_SERVER['REQUEST_URI'];
		} else {
			$uri = @func_get_arg(2);
		}
	}

	if (strstr($uri, '?')) {
		$parts = explode('?', $uri, 2);
		if (1 == count($parts)) {
			$base = '?';
			$query = $parts[0];
		}
		else {
			$base = $parts[0] . '?';
			$query = $parts[1];
		}
	}
	else {
		$base = $uri . '?';
		$query = '';
	}
	parse_str($query, $qs);
	if (is_array(func_get_arg(0))) {
		$kayvees = func_get_arg(0);
		$qs = array_merge($qs, $kayvees);
	}
	else
    {
			$qs[func_get_arg(0)] = func_get_arg(1);
    }

	foreach($qs as $k => $v)
    {
			if($v != '')
        {
					if($ret != '') $ret .= '&';
					$ret .= "$k=$v";
        }
    }
	$ret = $base . $ret;   
	return trim($ret, '?');
}

function remove_query_arg($key, $query) {
	add_query_arg($key, '', $query);
}

function load_template($file) {
	global $posts, $post, $sp_did_header, $sp_did_template_redirect, $sp_query,
		$sp_rewrite, $spdb;

	extract($sp_query->query_vars);

	include($file);
}

function add_magic_quotes($array) {
	foreach ($array as $k => $v) {
		if (is_array($v)) {
			$array[$k] = add_magic_quotes($v);
		} else {
			$array[$k] = addslashes($v);
		}
	}
	return $array;
}

?>
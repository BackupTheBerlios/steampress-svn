<?php

if (! isset($sp_did_header)):
if ( !file_exists( dirname(__FILE__) . '/sp-config.php') )
    die("There doesn't seem to be a <code>sp-config.php</code> file. I need this before we can get started. You can <a href='sp-admin/setup-config.php'>create a <code>sp-config.php</code> file through a web interface</a>, but this doesn't work for all server setups. The safest way is to manually create the file.");

require_once( dirname(__FILE__) . '/sp-config.php');

require_once( dirname(__FILE__) . '/sp-includes/sp-l10n.php');

$query_vars = array();

// Process PATH_INFO and 404.
if ((isset($_GET['error']) && $_GET['error'] == '404') ||
		((! empty($_SERVER['PATH_INFO'])) &&
		('/' != $_SERVER['PATH_INFO']) &&
		 (false === strpos($_SERVER['PATH_INFO'], 'index.php'))
		)) {

	// If we match a rewrite rule, this will be cleared.
	$error = '404';

	// Fetch the rewrite rules.
	$rewrite = $sp_rewrite->sp_rewrite_rules();

	if (! empty($rewrite)) {
		$pathinfo = $_SERVER['PATH_INFO'];
		$req_uri = $_SERVER['REQUEST_URI'];      
		$home_path = parse_url(get_settings('home'));
		$home_path = $home_path['path'];

		// Trim path info from the end and the leading home path from the
		// front.  For path info requests, this leaves us with the requesting
		// filename, if any.  For 404 requests, this leaves us with the
		// requested permalink.	
		$req_uri = str_replace($pathinfo, '', $req_uri);
		$req_uri = str_replace($home_path, '', $req_uri);
		$req_uri = trim($req_uri, '/');
		$pathinfo = trim($pathinfo, '/');

		// The requested permalink is in $pathinfo for path info requests and
		//  $req_uri for other requests.
		if (! empty($pathinfo)) {
			$request = $pathinfo;
		} else {
			$request = $req_uri;
		}

		// Look for matches.
		$request_match = $request;
		foreach ($rewrite as $match => $query) {
			// If the requesting file is the anchor of the match, prepend it
			// to the path info.
	    if ((! empty($req_uri)) && (strpos($match, $req_uri) === 0)) {
	      $request_match = $req_uri . '/' . $request;
	    }

			if (preg_match("!^$match!", $request_match, $matches)) {
				// Got a match.
				// Trim the query of everything up to the '?'.
				$query = preg_replace("!^.+\?!", '', $query);

				// Substitute the substring matches into the query.
				eval("\$query = \"$query\";");

				// Parse the query.
				parse_str($query, $query_vars);

				// If we're processing a 404 request, clear the error var
				// since we found something.
				if (isset($_GET['error'])) {
					unset($_GET['error']);
				}

				if (isset($error)) {
					unset($error);
				}

				break;
			}
		}
	}
 }

$spvarstoreset = array('m','p','posts','w', 'cat','withcomments','s','search','exact', 'sentence','poststart','postend','preview','debug', 'calendar','page','paged','more','tb', 'pb','author','order','orderby', 'year', 'monthnum', 'day', 'hour', 'minute', 'second', 'name', 'category_name', 'feed', 'author_name', 'static', 'pagename', 'page_id', 'error');

$spvarstoreset = apply_filters('query_vars', $spvarstoreset);

for ($i=0; $i<count($spvarstoreset); $i += 1) {
	$spvar = $spvarstoreset[$i];
	if (!isset($$spvar)) {
		if (empty($_POST[$spvar])) {
			if (empty($_GET[$spvar]) && empty($query_vars[$spvar])) {
				$$spvar = '';
			} elseif (!empty($_GET[$spvar])) {
				$$spvar = $_GET[$spvar];
			} else {
				$$spvar = $query_vars[$spvar];
			}
		} else {
			$$spvar = $_POST[$spvar];
		}
	}
}

if ('' != $feed) {
    $doing_rss = true;
}

if (1 == $tb) {
    $doing_trackback = true;
}

// Sending HTTP headers

if (is_404()) {
	header("HTTP/1.x 404 Not Found");
} else if ( !isset($doing_rss) || !$doing_rss ) {
	@header ('X-Pingback: '. get_bloginfo('pingback_url'));
} else {
	// We're showing a feed, so SP is indeed the only thing that last changed
	if ( $withcomments )
		$sp_last_modified = mysql2date('D, d M Y H:i:s', get_lastcommentmodified('GMT'), 0).' GMT';
	else 
		$sp_last_modified = mysql2date('D, d M Y H:i:s', get_lastpostmodified('GMT'), 0).' GMT';
	$sp_etag = '"' . md5($sp_last_modified) . '"';
	@header("Last-Modified: $sp_last_modified");
	@header("ETag: $sp_etag");
	@header ('X-Pingback: ' . get_bloginfo('pingback_url'));

	// Support for Conditional GET
	if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) $client_last_modified = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
	else $client_last_modified = false;
	if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) $client_etag = stripslashes($_SERVER['HTTP_IF_NONE_MATCH']);
	else $client_etag = false;

	if ( ($client_last_modified && $client_etag) ?
	    (($client_last_modified == $sp_last_modified) && ($client_etag == $sp_etag)) :
	    (($client_last_modified == $sp_last_modified) || ($client_etag == $sp_etag)) ) {
		if ( preg_match('/cgi/',php_sapi_name()) ) {
		    header('HTTP/1.1 304 Not Modified');
		    echo "\r\n\r\n";
		    exit;
		} else {
		    if (version_compare(phpversion(),'4.3.0','>=')) {
		        header('Not Modified', TRUE, 304);
		    } else {
		        header('HTTP/1.x 304 Not Modified');
		    }
			exit;
		}
	}
}

// Getting settings from DB
if ( isset($doing_rss) && $doing_rss == 1 )
    $posts_per_page = get_settings('posts_per_rss');

$use_gzipcompression = get_settings('gzipcompression');

$more_wpvars = array('posts_per_page', 'posts_per_archive_page', 'what_to_show', 'showposts', 'nopaging');

// Construct the query string.
$query_string = '';
foreach (array_merge($spvarstoreset, $more_wpvars) as $spvar) {
	if ($$spvar != '') {
		$query_string .= (strlen($query_string) < 1) ? '' : '&';
		$query_string .= $spvar . '=' . rawurlencode($$spvar);
	}
}

$query_string = apply_filters('query_string', $query_string);

update_category_cache();

// Call query posts to do the work.
$posts = query_posts($query_string);

// Extract updated query vars back into global namespace.
extract($sp_query->query_vars);

if (1 == count($posts)) {
	if (is_single()) {
		$more = 1;
		$single = 1;
	}
	if ( $s && empty($paged) && !strstr($_SERVER['PHP_SELF'], 'sp-admin/')) { // If they were doing a search and got one result
		header('Location: ' . get_permalink($posts[0]->ID));
	}
}

// Issue a 404 if a permalink request doesn't match any posts.  Don't issue a
// 404 if one was already issued, if the request was a search, or if the
// request was a regular query string request rather than a permalink request.
if ( (0 == count($posts)) && !is_404() && !is_search()
		 && !empty($_SERVER['QUERY_STRING']) &&
		 (false === strpos($_SERVER['REQUEST_URI'], '?')) ) {
	$sp_query->is_404 = true;
	header("HTTP/1.x 404 Not Found");
}

$sp_did_header = true;
endif;

$sp_template_dir = TEMPLATEPATH;

// Template redirection
if ($pagenow == 'index.php') {
	if (! isset($sp_did_template_redirect)) {
		$sp_did_template_redirect = true;
		do_action('template_redirect', '');
		if (is_feed()) {
			$sp_did_template_redirect = true;
			include(dirname(__FILE__) . '/sp-feed.php');
			exit;
		} else if ($tb == 1) {
			$sp_did_template_redirect = true;
			include(dirname(__FILE__) . '/sp-trackback.php');
			exit;
		} else if (is_404() &&
							 file_exists("$sp_template_dir/404.php")) {
			$sp_did_template_redirect = true;
			include("$sp_template_dir/404.php");
			exit;
		} else if (is_home() && 
				file_exists("$sp_template_dir/index.php")) {
			$sp_did_template_redirect = true;
			include("$sp_template_dir/index.php");
			exit;
		} else if (is_single() &&
							 file_exists("$sp_template_dir/single.php")) {
			$sp_did_template_redirect = true;
			include("$sp_template_dir/single.php");
			exit;
		} else if (is_page() && file_exists(get_page_template())) {
			$sp_did_template_redirect = true;
			include(get_page_template());
			exit;
		} else if (is_category() &&
							 file_exists("$sp_template_dir/category.php")) {
			$sp_did_template_redirect = true;
			include("$sp_template_dir/category.php");
			exit;
		} else if (is_author() &&
							 file_exists("$sp_template_dir/author.php")) {
			$sp_did_template_redirect = true;
			include("$sp_template_dir/author.php");
			exit;
		} else if (is_date() &&
							 file_exists("$sp_template_dir/date.php")) {
			$sp_did_date = true;
			$sp_did_template_redirect = true;
			include("$sp_template_dir/date.php");
			exit;
		} else if (is_archive() &&
							 file_exists("$sp_template_dir/archive.php")) {
			$sp_did_template_redirect = true;
			include("$sp_template_dir/archive.php");
			exit;
		} else if (is_search() &&
							 file_exists("$sp_template_dir/search.php")) {
			$sp_did_template_redirect = true;
			include("$sp_template_dir/search.php");
			exit;
		} else if (is_paged() &&
							 file_exists("$sp_template_dir/paged.php")) {
			$sp_did_template_redirect = true;
			include("$sp_template_dir/paged.php");
			exit;
		} else if (file_exists("$sp_template_dir/index.php"))
			{
				$sp_did_template_redirect = true;
				include("$sp_template_dir/index.php");
				exit;
			}
	}
}

if ($pagenow != 'post.php' && $pagenow != 'edit.php') {
	if ( get_settings('gzipcompression') ) 
		gzip_compression();
}

?>
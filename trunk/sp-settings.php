<?php
$HTTP_HOST = getenv('HTTP_HOST');  /* domain name */
$REMOTE_ADDR = getenv('REMOTE_ADDR'); /* visitor's IP */
$HTTP_USER_AGENT = getenv('HTTP_USER_AGENT'); /* visitor's browser */

// Fix for IIS, which doesn't set REQUEST_URI
if (! isset($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
	
	// Append the query string if it exists and isn't null
	if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
		$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
	}
}

if ( !(phpversion() >= '4.1') )
	die( 'Your server is running PHP version ' . phpversion() . ' but SteamPress requires at least 4.1' );

function timer_start() {
	global $timestart;
	$mtime = explode(' ', microtime() );
	$mtime = $mtime[1] + $mtime[0];
	$timestart = $mtime;
	return true;
}
timer_start();

// Change to E_ALL for development/debugging
error_reporting(E_ALL ^ E_NOTICE);

define('WPINC', 'sp-includes');
require_once (ABSPATH . WPINC . '/sp-db.php');

// Table names
$spdb->posts               = $table_prefix . 'posts';
$spdb->users               = $table_prefix . 'users';
$spdb->categories          = $table_prefix . 'categories';
$spdb->post2cat            = $table_prefix . 'post2cat';
$spdb->comments            = $table_prefix . 'comments';
$spdb->links               = $table_prefix . 'links';
$spdb->linkcategories      = $table_prefix . 'linkcategories';
$spdb->options             = $table_prefix . 'options';
$spdb->postmeta            = $table_prefix . 'postmeta';

// We're going to need to keep this around for a few months even though we're not using it internally

$tableposts = $spdb->posts;
$tableusers = $spdb->users;
$tablecategories = $spdb->categories;
$tablepost2cat = $spdb->post2cat;
$tablecomments = $spdb->comments;
$tablelinks = $spdb->links;
$tablelinkcategories = $spdb->linkcategories;
$tableoptions = $spdb->options;
$tablepostmeta = $spdb->postmeta;

require (ABSPATH . WPINC . '/functions.php');
require (ABSPATH . WPINC . '/functions-formatting.php');
require (ABSPATH . WPINC . '/functions-post.php');
require (ABSPATH . WPINC . '/classes.php');
require (ABSPATH . WPINC . '/template-functions.php');
require (ABSPATH . WPINC . '/links.php');
require (ABSPATH . WPINC . '/kses.php');

require_once (ABSPATH . WPINC . '/sp-l10n.php');

$spdb->hide_errors();
if ( !update_user_cache() && !strstr($_SERVER['PHP_SELF'], 'install.php') )
	die("It doesn't look like you've installed WP yet. Try running <a href='sp-admin/install.php'>install.php</a>.");
$spdb->show_errors();

if (!strstr($_SERVER['PHP_SELF'], 'install.php') && !strstr($_SERVER['PHP_SELF'], 'sp-admin/import')) :
    $querystring_start = '?';
    $querystring_equal = '=';
    $querystring_separator = '&amp;';

    // Used to guarantee unique hash cookies
    $cookiehash = md5(get_settings('siteurl')); // Remove in 1.4
	define('COOKIEHASH', $cookiehash); 
endif;

require (ABSPATH . WPINC . '/vars.php');


// Check for hacks file if the option is enabled
if (get_settings('hack_file')) {
	if (file_exists(ABSPATH . '/my-hacks.php'))
		require(ABSPATH . '/my-hacks.php');
}

if ( get_settings('active_plugins') ) {
	$current_plugins = get_settings('active_plugins');
	foreach ($current_plugins as $plugin) {
		if ('' != $plugin && file_exists(ABSPATH . 'sp-content/plugins/' . $plugin))
			include_once(ABSPATH . 'sp-content/plugins/' . $plugin);
	}
}

define('TEMPLATEPATH', get_template_directory());

function shutdown_action_hook() {
	do_action('shutdown', '');
}
register_shutdown_function('shutdown_action_hook');

?>
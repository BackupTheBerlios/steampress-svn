<?php
require_once('../steampress-config.php');
require_once(ABSPATH . 'steampress-includes/steampress-l10n.php');

require_once(ABSPATH . 'steampress-admin/auth.php');
require(ABSPATH . 'steampress-admin/admin-functions.php');

$dogs = $steampressdb->get_results("SELECT * FROM $steampressdb->categories");
foreach ($dogs as $catt)
{
	$cache_categories[$catt->cat_ID] = $catt;
}

get_currentuserinfo();

$posts_per_page = get_settings('posts_per_page');
$what_to_show = get_settings('what_to_show');
$date_format = get_settings('date_format');
$time_format = get_settings('time_format');

function add_magic_quotes($array)
{
	foreach ($array as $k => $v)
	{
		if (is_array($v))
		{
			$array[$k] = add_magic_quotes($v);
		}
		else
		{
			$array[$k] = addslashes($v);
		}
	}
	return $array;
}

if (!get_magic_quotes_gpc())
{
	$_GET = add_magic_quotes($_GET);
	$_POST = add_magic_quotes($_POST);
	$_COOKIE = add_magic_quotes($_COOKIE);
}

$steampressvarstoreset = array('profile','redirect','redirect_url','a','popuptitle','popupurl','text', 'trackback', 'pingback');
for ($i=0; $i<count($steampressvarstoreset); $i += 1)
{
	$steampressvar = $steampressvarstoreset[$i];
	if (!isset($$steampressvar))
	{
		if (empty($_POST["$steampressvar"]))
		{
			if (empty($_GET["$steampressvar"]))
			{
				$$steampressvar = '';
			}
			else
			{
				$$steampressvar = $_GET["$steampressvar"];
			}
		}
		else
		{
			$$steampressvar = $_POST["$steampressvar"];
		}
	}
}

require(ABSPATH . '/steampress-admin/menu.php');

// Handle plugin admin pages.
if (isset($_GET['page']))
{
	$plugin_page = plugin_basename($_GET['page']);
	if (! file_exists(ABSPATH . "steampress-content/plugins/$plugin_page"))
	{
		die(sprintf(__('Cannot load %s.'), $plugin_page));
	}
	if (! isset($_GET['noheader']))
	{
		require_once(ABSPATH . '/steampress-admin/admin-header.php');
	}

	include(ABSPATH . "steampress-content/plugins/$plugin_page");

	include(ABSPATH . 'steampress-admin/admin-footer.php');	
}

?>
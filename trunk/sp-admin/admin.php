<?php
require_once('../sp-config.php');
require_once(ABSPATH . 'sp-includes/sp-l10n.php');
require_once(ABSPATH . 'sp-admin/admin-functions.php');
logged_in();

$dogs = $spdb->get_results("SELECT * FROM $spdb->categories");
foreach ($dogs as $catt) {
	$cache_categories[$catt->cat_ID] = $catt;
}

get_currentuserinfo();

$posts_per_page = get_settings('posts_per_page');
$what_to_show = get_settings('what_to_show');
$date_format = get_settings('date_format');
$time_format = get_settings('time_format');

$spvarstoreset = array('profile','redirect','redirect_url','a','popuptitle','popupurl','text', 'trackback', 'pingback');
for ($i=0; $i<count($spvarstoreset); $i += 1) {
    $spvar = $spvarstoreset[$i];
    if (!isset($$spvar)) {
        if (empty($_POST["$spvar"])) {
            if (empty($_GET["$spvar"])) {
                $$spvar = '';
            } else {
                $$spvar = $_GET["$spvar"];
            }
        } else {
            $$spvar = $_POST["$spvar"];
        }
    }
}

require(ABSPATH . '/sp-admin/menu.php');

// Handle plugin admin pages.
if (isset($_GET['page'])) {
	$plugin_page = plugin_basename($_GET['page']);
	if (! file_exists(ABSPATH . "sp-content/plugins/$plugin_page")) {
		die(sprintf(__('Cannot load %s.'), $plugin_page));
	}

	if (! isset($_GET['noheader'])) {
		require_once(ABSPATH . '/sp-admin/admin-header.php');
	}

	include(ABSPATH . "sp-content/plugins/$plugin_page");

	include(ABSPATH . 'sp-admin/admin-footer.php');	
}

?>
<?php
// This array constructs the admin menu bar.
//
// Menu item name
// The minimum level the user needs to access the item: between 0 and 10
// The URL of the item's file
$menu[0] = array(__('Dashboard'), 0, 'index.php');
$menu[5] = array(__('Write'), 1, 'post.php');
$menu[10] = array(__('Manage'), 1, 'edit.php');
$menu[20] = array(__('Links'), 5, 'link-manager.php');
$menu[25] = array(__('Users'), 0, 'profile.php');
$menu[30] = array(__('Options'), 6, 'options-general.php');

$submenu['post.php'][5] = array(__('Write Post'), 1, 'post.php');
$submenu['post.php'][10] = array(__('Write Page'), 5, 'page-new.php');

$submenu['edit.php'][5] = array(__('Posts'), 1, 'edit.php');
$submenu['edit.php'][10] = array(__('Pages'), 5, 'edit-pages.php');
$submenu['edit.php'][15] = array(__('Categories'), 1, 'categories.php');
$submenu['edit.php'][20] = array(__('Comments'), 1, 'edit-comments.php');
$awaiting_mod = $spdb->get_var("SELECT COUNT(*) FROM $spdb->comments WHERE comment_approved = '0'");
$submenu['edit.php'][25] = array(sprintf(__("Awaiting Moderation (%s)"), $awaiting_mod), 1, 'moderation.php');

$submenu['link-manager.php'][5] = array(__('Manage Links'), 5, 'link-manager.php');
$submenu['link-manager.php'][10] = array(__('Add Link'), 5, 'link-add.php');
$submenu['link-manager.php'][15] = array(__('Link Categories'), 5, 'link-categories.php');
$submenu['link-manager.php'][20] = array(__('Import Links'), 5, 'link-import.php');

$submenu['profile.php'][5] = array(__('Your Profile'), 0, 'profile.php');
$submenu['profile.php'][10] = array(__('Authors &amp; Users'), 5, 'users.php');

$submenu['options-general.php'][5] = array(__('General'), 6, 'options-general.php');
$submenu['options-general.php'][10] = array(__('Writing'), 6, 'options-writing.php');
$submenu['options-general.php'][15] = array(__('Reading'), 6, 'options-reading.php');
$submenu['options-general.php'][20] = array(__('Discussion'), 6, 'options-discussion.php');
$submenu['options-general.php'][25] = array(__('Permalinks'), 6, 'options-permalink.php');
$submenu['options-general.php'][30] = array(__('Themes'), 6, 'options-themes.php');
$submenu['options-general.php'][35] = array(__('Plugins'), 6, 'options-plugins.php');

$submenu['users.php'][5] = array(__('Your Profile'), 0, 'profile.php');
$submenu['users.php'][10] = array(__('Authors &amp; Users'), 5, 'users.php');

do_action('admin_menu', '');
ksort($menu); // make it all pretty

if (! user_can_access_admin_page()) {
	die( __('You have do not have sufficient permissions to access this page.') );
}

?>

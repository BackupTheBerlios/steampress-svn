<?php
require_once('admin.php');

if ( isset($_GET['action']) ) {
	check_admin_referer();

	if ('activate' == $_GET['action']) {
		$current = get_settings('active_plugins');
		if (!in_array($_GET['plugin'], $current)) {
			$current[] = trim( $_GET['plugin'] );
		}
		sort($current);
		update_option('active_plugins', $current);
		header('Location: options-plugins.php?activate=true');
	}
	
	if ('deactivate' == $_GET['action']) {
		$current = get_settings('active_plugins');
		array_splice($current, array_search( $_GET['plugin'], $current), 1 ); // Array-fu!
		update_option('active_plugins', $current);
		header('Location: options-plugins.php?deactivate=true');
	}
}

$title = __('Manage Plugins');
$parent_file = 'options-general.php';
require_once('admin-header.php');

// Clean up options
// If any plugins don't exist, axe 'em

$check_plugins = get_settings('active_plugins');
foreach ($check_plugins as $check_plugin) {
	if (!file_exists(ABSPATH . 'sp-content/plugins/' . $check_plugin)) {
			$current = get_settings('active_plugins');
			unset($current[$_GET['plugin']]);
			update_option('active_plugins', $current);
	}
}
?>

<?php if (isset($_GET['activate'])) : ?>
<div class="updated"><p><?php _e('Plugin <strong>activated</strong>.') ?></p>
</div>
<?php endif; ?>
<?php if (isset($_GET['deactivate'])) : ?>
<div class="updated"><p><?php _e('Plugin <strong>deactivated</strong>.') ?></p>
</div>
<?php endif; ?>

<div class="wrap">
<h2><?php _e('Plugin Management'); ?></h2>
<p><?php _e('Plugins are files you usually download separately from SteamPress that add functionality. To install a plugin you generally just need to put the plugin file into your <code>sp-content/plugins</code> directory. Once a plugin is installed, you may activate it or deactivate it here. If something goes wrong with a plugin and you can&#8217;t use SteamPress, delete that plugin from the <code>sp-content/plugins</code> directory and it will be automatically deactivated.'); ?></p>
<?php

if ( get_settings('active_plugins') )
	$current_plugins = get_settings('active_plugins');

$plugins = get_plugins();

if (empty($plugins)) {
	_e("<p>Couldn't open plugins directory or there are no plugins available.</p>"); // TODO: make more helpful
} else {
?>
<table width="100%" cellpadding="3" cellspacing="3">
	<tr>
		<th><?php _e('Plugin'); ?></th>
		<th><?php _e('Version'); ?></th>
		<th><?php _e('Author'); ?></th>
		<th><?php _e('Description'); ?></th>
		<th><?php _e('Action'); ?></th>
	</tr>
<?php
	$style = '';
	foreach($plugins as $plugin_file => $plugin_data) {
		$style = ('class="alternate"' == $style) ? '' : 'class="alternate"';

		if (!empty($current_plugins) && in_array($plugin_file, $current_plugins)) {
			$action = "<a href='options-plugins.php?action=deactivate&amp;plugin=$plugin_file' title='".__('Deactivate this plugin')."' class='delete'>".__('Deactivate')."</a>";
			$plugin = "<strong>$plugin</strong>";
		} else {
			$action = "<a href='options-plugins.php?action=activate&amp;plugin=$plugin_file' title='".__('Activate this plugin')."' class='edit'>".__('Activate')."</a>";
		}
		echo "
	<tr $style>
		<td>{$plugin_data['Title']}</td>
		<td>{$plugin_data['Version']}</td>
		<td>{$plugin_data['Author']}</td>
		<td>{$plugin_data['Description']}</td>
		<td>$action</td>
	</tr>";
	}
?>

</table>
<?php
}
?>
</div>

<?php
include('admin-footer.php');
?>

<div id="adminmenu">
<?php
$self = preg_replace('|^.*/sp-admin/|i', '', $_SERVER['PHP_SELF']);
$self = preg_replace('|^.*/plugins/|i', '', $self);

get_admin_page_parent();

foreach ($menu as $item)
{
	$class = '';

	// 0 = name, 1 = user_level, 2 = file
	if ((substr($self, -10) == substr($item[2], -10) && empty($parent_file)) || ($parent_file && ($item[2] == $parent_file))) $class = ' class="current"';
    
	if ($user_level >= $item[1])
	{
		if ( file_exists(ABSPATH . "sp-content/plugins/{$item[2]}") )
		{
			echo "\n\t<h2><a href='" . get_settings('siteurl') . "/sp-admin/admin.php?page={$item[2]}'$class>{$item[0]}</a></h2>";
			if ( isset($submenu["$parent_file"]) )
			{
?>
<ul id="submenu">
<?php 
				foreach ($submenu["$parent_file"] as $item)
				{
					if ($user_level < $item[1])
					{
					continue;
					}
					if ( (substr($self, -10) == substr($item[2], -10)) || (isset($plugin_page) && $plugin_page == $item[2]) )
					{
						$class = ' class="current"';
					}
					elseif (isset($submenu_file) && $submenu_file == substr($item[2], -10))
					{
						$class = ' class="current"';
					}
					else
					{
						$class = '';
					}
	
					if (file_exists(ABSPATH . "sp-content/plugins/{$item[2]}"))
					{
						echo "\n\t<li><a href='" . get_settings('siteurl') . "/sp-admin/admin.php?page={$item[2]}'$class>{$item[0]}</a></li>";
					}
					else
					{
						echo "\n\t<li><a href='" . get_settings('siteurl') . "/sp-admin/{$item[2]}'$class>{$item[0]}</a></li>";
					}
				}
			}
?>
</ul>
</div>
<?php
		}
		else
		{
			echo "\n\t<h2><a href='" . get_settings('siteurl') . "/sp-admin/{$item[2]}'$class>{$item[0]}</a></h2>";
			if ( isset($submenu["$parent_file"]) )
			{
?>
<ul id="submenu">
<?php 
				foreach ($submenu["$parent_file"] as $item)
				{
					if ($user_level < $item[1])
					{
						continue;
					}
					if ( (substr($self, -10) == substr($item[2], -10)) || (isset($plugin_page) && $plugin_page == $item[2]) )
					{
						$class = ' class="current"';
					}
					elseif (isset($submenu_file) && $submenu_file == substr($item[2], -10))
					{
						$class = ' class="current"';
					}
					else
					{
						$class = '';
					}
	
					if (file_exists(ABSPATH . "sp-content/plugins/{$item[2]}"))
					{
						echo "\n\t<li><a href='" . get_settings('siteurl') . "/sp-admin/admin.php?page={$item[2]}'$class>{$item[0]}</a></li>";
					}
					else
					{
						echo "\n\t<li><a href='" . get_settings('siteurl') . "/sp-admin/{$item[2]}'$class>{$item[0]}</a></li>";
					}
				}
?>

</ul>
<?php
			}
		}
	}
}
?>
	<h2 class="last">
		<a href="<?php echo get_settings('siteurl') ?>/sp-logout.php" title="<?php _e('Log out of this account') ?>">
			<?php printf(__('Logout (%s)'), $user_nickname) ?>
		</a>
	</h2>
</div>
<?php

function url_shorten ($url) {
	$short_url = str_replace('http://', '', stripslashes($url));
	$short_url = str_replace('www.', '', $short_url);
	if ('/' == substr($short_url, -1))
		$short_url = substr($short_url, 0, -1);
	if (strlen($short_url) > 35)
		$short_url =  substr($short_url, 0, 32).'...';
	return $short_url;
}

function selected($selected, $current) {
	if ($selected == $current) echo ' selected="selected"';
}

function checked($checked, $current) {
	if ($checked == $current) echo ' checked="checked"';
}

function return_categories_list( $parent = 0, $sortbyname = FALSE )
{
        /*
         * This function returns an list of all categories
         * that have $parent as their parent
         * if no parent is specified we will assume top level caegories
         * are required.
         */
        global $spdb;

        // select sort order
        $sort = "cat_id";
        if( TRUE == $sortbyname )
        {
                $sort = "cat_name";
        }

        // First query the database
        $cats_tmp = $spdb->get_results("SELECT cat_id FROM $spdb->categories WHERE category_parent = $parent ORDER BY $sort");

        // Now strip this down to a simple array of IDs
        $cats = array();
        if( count($cats_tmp) > 0 )
        {
                foreach( $cats_tmp as $cat )
                {
                        $cats[] = $cat->cat_id;
                }
        }

        // Return the list of categories
        return $cats;
}

function get_nested_categories($default = 0, $parent = 0) {
 global $post_ID, $mode, $spdb;

 if ($post_ID) {
   $checked_categories = $spdb->get_col("
     SELECT category_id
     FROM $spdb->categories, $spdb->post2cat
     WHERE $spdb->post2cat.category_id = cat_ID AND $spdb->post2cat.post_id = '$post_ID'
     ");

   if(count($checked_categories) == 0)
   {
     // No selected categories, strange
     $checked_categories[] = $default;
   }

 } else {
   $checked_categories[] = $default;
 }

 $cats = return_categories_list($parent, TRUE);
 $result = array();

 foreach($cats as $cat)
 {
   $result[$cat]['children'] = get_nested_categories($default, $cat);
   $result[$cat]['cat_ID'] = $cat;
   $result[$cat]['checked'] = in_array($cat, $checked_categories);
   $result[$cat]['cat_name'] = get_the_category_by_ID($cat);
 }

 return $result;
}

function write_nested_categories($categories) {
 foreach($categories as $category) {
   echo '<label for="category-', $category['cat_ID'], '" class="selectit"><input value="', $category['cat_ID'],
     '" type="checkbox" name="post_category[]" id="category-', $category['cat_ID'], '"',
     ($category['checked'] ? ' checked="checked"' : ""), '/> ', sp_specialchars($category['cat_name']), "</label>\n";

   if(isset($category['children'])) {
     echo "\n<span class='cat-nest'>\n";
     write_nested_categories($category['children']);
     echo "</span>\n";
   }
 }
}

function dropdown_categories($default = 0) {
 write_nested_categories(get_nested_categories($default));
} 

// Dandy new recursive multiple category stuff.
function cat_rows($parent = 0, $level = 0, $categories = 0) {
	global $spdb, $class, $user_level;
	if (!$categories)
		$categories = $spdb->get_results("SELECT * FROM $spdb->categories ORDER BY cat_name");

	if ($categories) {
		foreach ($categories as $category) {
			if ($category->category_parent == $parent) {
				$category->cat_name = sp_specialchars($category->cat_name);
				$count = $spdb->get_var("SELECT COUNT(post_id) FROM $spdb->post2cat WHERE category_id = $category->cat_ID");
				$pad = str_repeat('&#8212; ', $level);
				if ( $user_level > 3 )
					$edit = "<a href='categories.php?action=edit&amp;cat_ID=$category->cat_ID' class='edit'>" . __('Edit') . "</a></td><td><a href='categories.php?action=delete&amp;cat_ID=$category->cat_ID' onclick=\"return confirm('".  sprintf(__("You are about to delete the category \'%s\'.  All of its posts will go to the default category.\\n  \'OK\' to delete, \'Cancel\' to stop."), addslashes($category->cat_name)) . "')\" class='delete'>" .  __('Delete') . "</a>";
				else
					$edit = '';
				
				$class = ('alternate' == $class) ? '' : 'alternate';
				echo "<tr class='$class'><th scope='row'>$category->cat_ID</th><td>$pad $category->cat_name</td>
				<td>$category->category_description</td>
				<td>$count</td>
				<td>$edit</td>
				</tr>";
				cat_rows($category->cat_ID, $level + 1);
			}
		}
	} else {
		return false;
	}
}

function sp_dropdown_cats($currentcat = 0, $currentparent = 0, $parent = 0, $level = 0, $categories = 0) {
	global $spdb, $bgcolor;
	if (!$categories) {
		$categories = $spdb->get_results("SELECT * FROM $spdb->categories ORDER BY cat_name");
	}
	if ($categories) {
		foreach ($categories as $category) { if ($currentcat != $category->cat_ID && $parent == $category->category_parent) {
			$count = $spdb->get_var("SELECT COUNT(post_id) FROM $spdb->post2cat WHERE category_id = $category->cat_ID");
			$pad = str_repeat('&#8211; ', $level);
			$category->cat_name = sp_specialchars($category->cat_name);
			echo "\n\t<option value='$category->cat_ID'";
			if ($currentparent == $category->cat_ID)
				echo " selected='selected'";
			echo ">$pad$category->cat_name</option>";
			sp_dropdown_cats($currentcat, $currentparent, $category->cat_ID, $level + 1, $categories);
		} }
	} else {
		return false;
	}
}

function sp_create_thumbnail($file, $max_side, $effect = '') {

    // 1 = GIF, 2 = JPEG, 3 = PNG

    if(file_exists($file)) {
        $type = getimagesize($file);
        
        // if the associated function doesn't exist - then it's not
        // handle. duh. i hope.
        
        if(!function_exists('imagegif') && $type[2] == 1) {
            $error = __('Filetype not supported. Thumbnail not created.');
        }elseif(!function_exists('imagejpeg') && $type[2] == 2) {
            $error = __('Filetype not supported. Thumbnail not created.');
        }elseif(!function_exists('imagepng') && $type[2] == 3) {
            $error = __('Filetype not supported. Thumbnail not created.');
        } else {
        
            // create the initial copy from the original file
            if($type[2] == 1) {
                $image = imagecreatefromgif($file);
            } elseif($type[2] == 2) {
                $image = imagecreatefromjpeg($file);
            } elseif($type[2] == 3) {
                $image = imagecreatefrompng($file);
            }
            
			if (function_exists('imageantialias'))
	            imageantialias($image, TRUE);
            
            $image_attr = getimagesize($file);
            
            // figure out the longest side
            
            if($image_attr[0] > $image_attr[1]) {
                $image_width = $image_attr[0];
                $image_height = $image_attr[1];
                $image_new_width = $max_side;
                
                $image_ratio = $image_width/$image_new_width;
                $image_new_height = $image_height/$image_ratio;
                //width is > height
            } else {
                $image_width = $image_attr[0];
                $image_height = $image_attr[1];
                $image_new_height = $max_side;
                
                $image_ratio = $image_height/$image_new_height;
                $image_new_width = $image_width/$image_ratio;
                //height > width
            }
            
            $thumbnail = imagecreatetruecolor($image_new_width, $image_new_height);
            @imagecopyresized($thumbnail, $image, 0, 0, 0, 0, $image_new_width, $image_new_height, $image_attr[0], $image_attr[1]);
            
            // move the thumbnail to it's final destination
            
            $path = explode('/', $file);
            $thumbpath = substr($file, 0, strrpos($file, '/')) . '/thumb-' . $path[count($path)-1];
            
            if($type[2] == 1) {
                if(!imagegif($thumbnail, $thumbpath)) {
                    $error = __("Thumbnail path invalid");
                }
            } elseif($type[2] == 2) {
                if(!imagejpeg($thumbnail, $thumbpath)) {
                    $error = __("Thumbnail path invalid");
                }
            } elseif($type[2] == 3) {
                if(!imagepng($thumbnail, $thumbpath)) {
                    $error = __("Thumbnail path invalid");
                }
            }
            
        }
    }
    
    if(!empty($error))
    {
        return $error;
    }
    else
    {
        return 1;
    }
}

// Some postmeta stuff
function has_meta($postid) {
	global $spdb;

	return $spdb->get_results("
		SELECT meta_key, meta_value, meta_id, post_id
		FROM $spdb->postmeta
		WHERE post_id = '$postid'
		ORDER BY meta_key,meta_id",ARRAY_A);

}

function list_meta($meta) {
	global $post_ID;	
	// Exit if no meta
	if (!$meta) return;
	$count = 0;
?>
<table id='meta-list' cellpadding="3">
	<tr>
		<th><?php _e('Key') ?></th>
		<th><?php _e('Value') ?></th>
		<th colspan='2'><?php _e('Action') ?></th>
	</tr>
<?php
		
	foreach ($meta as $entry) {
		++$count;
		if ( $count % 2 ) $style = 'alternate';
		else $style = '';
		if ( '_' == $entry['meta_key']{0} ) $style .= ' hidden';
		echo "
	<tr class='$style'>
		<td valign='top'><input name='meta[{$entry['meta_id']}][key]' tabindex='6' type='text' size='20' value='{$entry['meta_key']}' /></td>
		<td><textarea name='meta[{$entry['meta_id']}][value]' tabindex='6' rows='2' cols='30'>{$entry['meta_value']}</textarea></td>
		<td align='center' width='10%'><input name='updatemeta' type='submit' class='updatemeta' tabindex='6' value='" . __('Update') ."' /></td>
		<td align='center' width='10%'><input name='deletemeta[{$entry['meta_id']}]' type='submit' class='deletemeta' tabindex='6' value='" . __('Delete') ."' /></td>
	</tr>
";
	}
	echo "
	</table>
";
}

// Get a list of previously defined keys
function get_meta_keys() {
	global $spdb;
	
	$keys = $spdb->get_col("
		SELECT meta_key
		FROM $spdb->postmeta
		GROUP BY meta_key
		ORDER BY meta_key");
	
	return $keys;
}

function meta_form() {
	global $spdb;
	$keys = $spdb->get_col("
		SELECT meta_key
		FROM $spdb->postmeta
		GROUP BY meta_key
		ORDER BY meta_id DESC
		LIMIT 10");
?>
<h3><?php _e('Add a new custom field to this post:') ?></h3>
<table cellspacing="3" cellpadding="3">
	<tr>
<th colspan="2"><?php _e('Key') ?></th>
<th><?php _e('Value') ?></th>
</tr>
	<tr valign="top">
		<td align="right" width="18%">
<?php if ($keys) : ?>
<select id="metakeyselect" name="metakeyselect" tabindex="7">
<option value="#NONE#"><?php _e('- Select -'); ?></option>
<?php
	foreach($keys as $key) {
		echo "\n\t<option value='$key'>$key</option>";
	}
?>
</select> <?php _e('or'); ?>
<?php endif; ?>
</td>
<td><input type="text" id="metakeyinput" name="metakeyinput" tabindex="7" /></td>
		<td><textarea id="metavalue" name="metavalue" rows="3" cols="25" tabindex="8"></textarea></td>
	</tr>

</table>
<p class="submit"><input type="submit" name="updatemeta" tabindex="9" value="<?php _e('Add Custom Field &raquo;') ?>" /></p>
<?php
}

function add_meta($post_ID) {
	global $spdb;
	
	$metakeyselect = $spdb->escape( stripslashes( trim($_POST['metakeyselect']) ) );
	$metakeyinput  = $spdb->escape( stripslashes( trim($_POST['metakeyinput']) ) );
	$metavalue     = $spdb->escape( stripslashes( trim($_POST['metavalue']) ) );

	if (!empty($metavalue) && ((('#NONE#' != $metakeyselect) && !empty($metakeyselect)) || !empty($metakeyinput))) {
		// We have a key/value pair. If both the select and the 
		// input for the key have data, the input takes precedence:

		if ('#NONE#' != $metakeyselect)
			$metakey = $metakeyselect;
				
		if ($metakeyinput)
			$metakey = $metakeyinput; // default

		$result = $spdb->query("
				INSERT INTO $spdb->postmeta 
				(post_id,meta_key,meta_value) 
				VALUES ('$post_ID','$metakey','$metavalue')
			");
	}
} // add_meta

function delete_meta($mid) {
	global $spdb;

	$result = $spdb->query("DELETE FROM $spdb->postmeta WHERE meta_id = '$mid'");
}

function update_meta($mid, $mkey, $mvalue) {
	global $spdb;

	return $spdb->query("UPDATE $spdb->postmeta SET meta_key = '$mkey', meta_value = '$mvalue' WHERE meta_id = '$mid'");
}

function touch_time($edit = 1) {
	global $month, $postdata;
	// echo $postdata['Date'];
	if ('draft' == $postdata->post_status) {
		$checked = 'checked="checked" ';
		$edit = false;
	} else {
		$checked = ' ';
	}

	echo '<fieldset><legend><input type="checkbox" class="checkbox" name="edit_date" value="1" id="timestamp" '.$checked.'/> <label for="timestamp">' . __('Edit timestamp') . '</label></legend>';
	
	$time_adj = time() + (get_settings('gmt_offset') * 3600);
	$post_date = $postdata->post_date;
	$jj = ($edit) ? mysql2date('d', $post_date) : gmdate('d', $time_adj);
	$mm = ($edit) ? mysql2date('m', $post_date) : gmdate('m', $time_adj);
	$aa = ($edit) ? mysql2date('Y', $post_date) : gmdate('Y', $time_adj);
	$hh = ($edit) ? mysql2date('H', $post_date) : gmdate('H', $time_adj);
	$mn = ($edit) ? mysql2date('i', $post_date) : gmdate('i', $time_adj);
	$ss = ($edit) ? mysql2date('s', $post_date) : gmdate('s', $time_adj);

	echo "<select name=\"mm\">\n";
	for ($i=1; $i < 13; $i=$i+1) {
		echo "\t\t\t<option value=\"$i\"";
		if ($i == $mm)
		echo " selected='selected'";
		if ($i < 10) {
			$ii = "0".$i;
		} else {
			$ii = "$i";
		}
		echo ">".$month["$ii"]."</option>\n";
	} 

?>
</select>
<input type="text" name="jj" value="<?php echo $jj; ?>" size="2" maxlength="2" />
<input type="text" name="aa" value="<?php echo $aa ?>" size="4" maxlength="5" /> @ 
<input type="text" name="hh" value="<?php echo $hh ?>" size="2" maxlength="2" /> : 
<input type="text" name="mn" value="<?php echo $mn ?>" size="2" maxlength="2" /> 
<input type="hidden" name="ss" value="<?php echo $ss ?>" size="2" maxlength="2" /> <?php _e('Existing timestamp'); ?>: <?php echo "{$month[$mm]} $jj, $aa @ $hh:$mn"; ?></fieldset>
	<?php
}

function check_admin_referer() {
  $adminurl = strtolower(get_settings('siteurl')).'/sp-admin';
  $referer = strtolower($_SERVER['HTTP_REFERER']);
  if ( !strstr($referer, $adminurl) ) {
    die('Sorry, you need to enable sending referrers, for this feature to work.');
  }
}

// insert_with_markers: Owen Winkler
// Inserts an array of strings into a file (.htaccess), placing it between
// BEGIN and END markers.  Replaces existing marked info.  Retains surrounding
// data.  Creates file if none exists.
// Returns true on write success, false on failure.
function insert_with_markers($filename, $marker, $insertion) {
	if (!file_exists($filename) || is_writeable($filename)) {
		if (!file_exists($filename)) {
			$markerdata = '';
		} else {
			$markerdata = explode("\n", implode('', file($filename)));
		}

		$f = fopen($filename, 'w');
		$foundit = false;
		if ($markerdata) {
			$state = true;
			$newline = '';
			foreach($markerdata as $markerline) {
				if (strstr($markerline, "# BEGIN {$marker}")) $state = false;
				if ($state) fwrite($f, "{$newline}{$markerline}");
				if (strstr($markerline, "# END {$marker}")) {
					fwrite($f, "{$newline}# BEGIN {$marker}");
					if(is_array($insertion)) foreach($insertion as $insertline) fwrite($f, "{$newline}{$insertline}");
					fwrite($f, "{$newline}# END {$marker}");
					$state = true;
					$foundit = true;
				}
				$newline = "\n";
			}
		}
		if (!$foundit) {
			fwrite($f, "# BEGIN {$marker}\n");
			foreach($insertion as $insertline) fwrite($f, "{$insertline}\n");
			fwrite($f, "# END {$marker}");				
		}
		fclose($f);
		return true;
	} else {
		return false;
	}
}

// insert_with_markers: Owen Winkler
// Returns an array of strings from a file (.htaccess) from between BEGIN
// and END markers.
function extract_from_markers($filename, $marker) {
	$result = array();

	if (!file_exists($filename)) {
		return $result;
	}

	if($markerdata = explode("\n", implode('', file($filename))));
	{
		$state = false;
		foreach($markerdata as $markerline) {
			if(strstr($markerline, "# END {$marker}"))	$state = false;
			if($state) $result[] = $markerline;
			if(strstr($markerline, "# BEGIN {$marker}")) $state = true;
		}
	}

	return $result;
}

function save_mod_rewrite_rules() {
	global $is_apache, $sp_rewrite;
	$home = get_settings('home');
	if ( $home != '' && $home != get_settings('siteurl') ) {
		$home_path = parse_url($home);
		$home_path = $home_root['path'];
		$root = str_replace($_SERVER["PHP_SELF"], '', $_SERVER["PATH_TRANSLATED"]);
		$home_path = $root . $home_path . "/";
	} else {
		$home_path = ABSPATH;
	}

	if ( (!file_exists($home_path.'.htaccess') && is_writable($home_path)) || is_writable($home_path.'.htaccess') )
		$writable = true;
	else
		$writable = false;

	if ($sp_rewrite->using_index_permalinks())
		$usingpi = true;
	else
		$usingpi = false;

	if ( $writable && !$usingpi && $is_apache ) {
		$rules = explode("\n", $sp_rewrite->mod_rewrite_rules());
		insert_with_markers($home_path.'.htaccess', 'SteamPress', $rules);
	}
}

function generate_page_rewrite_rules() {
	global $spdb;
	$posts = $spdb->get_results("SELECT ID, post_name FROM $spdb->posts WHERE post_status = 'static'");

	$page_rewrite_rules = array();
	
	if ($posts) {
		foreach ($posts as $post) {
			// URI => page name
			$uri = get_page_uri($post->ID);
			
			$page_rewrite_rules[$uri] = $post->post_name;
		}
		
		update_option('page_uris', $page_rewrite_rules);
		
		save_mod_rewrite_rules();
	}
}

function validate_current_theme() {
	$theme_loc = 'sp-content/themes';
	$theme_root = ABSPATH . $theme_loc;

	$template = get_settings('template');
	$stylesheet = get_settings('stylesheet');

	if (($template != 'default') && (! file_exists("$theme_root/$template/index.php"))) {
		update_option('template', 'default');
		update_option('stylesheet', 'default');
		do_action('switch_theme', 'Default');
		return false;
	}

	if (($stylesheet != 'default') && (! file_exists("$theme_root/$stylesheet/style.css"))) {
		update_option('template', 'default');
		update_option('stylesheet', 'default');
		do_action('switch_theme', 'Default');
		return false;
	}

	return true;
}

function get_broken_themes() {
	global $sp_broken_themes;

	get_themes();
	return $sp_broken_themes;
}

function get_page_templates() {
	$themes = get_themes();
	$theme = get_current_theme();
	$templates = $themes[$theme]['Template Files'];
	$page_templates = array();

	foreach ($templates as $template) {
		$template_data = implode('', file(ABSPATH . $template));
		preg_match("|Template Name:(.*)|i", $template_data, $name);
		preg_match("|Description:(.*)|i", $template_data, $description);

		$name = $name[1];
		$description = $description[1];

		if (! empty($name)) {
			$page_templates[trim($name)] = basename($template);
		}
	}

	return $page_templates;
}

function page_template_dropdown($default = '') {
	$templates = get_page_templates();
	foreach (array_keys($templates) as $template) :
		if ($default == $templates[$template]) $selected = " selected='selected'";
		else $selected = '';
		echo "\n\t<option value='" . $templates[$template] . "' $selected>$template</option>";
		endforeach;
}

function parent_dropdown($default = 0, $parent = 0, $level = 0) {
	global $spdb, $post_ID;
	$items = $spdb->get_results("SELECT ID, post_parent, post_title FROM $spdb->posts WHERE post_parent = $parent AND post_status = 'static' ORDER BY menu_order");

	if ($items) {
		foreach ($items as $item) {
			// A page cannot be it's own parent.
			if (!empty($post_ID)) {
				if ($item->ID == $post_ID) {
					continue;
				}
			}
			$pad = str_repeat('&nbsp;', $level * 3);
			if ($item->ID == $default)
				$current = ' selected="selected"';
			else
				$current = '';

			echo "\n\t<option value='$item->ID'$current>$pad $item->post_title</option>";
			parent_dropdown($default, $item->ID, $level + 1);
		}
	} else {
		return false;
	}
}

function user_can_access_admin_page() {
	global $pagenow;
	global $menu;
	global $submenu;
	global $user_level;

	$parent = get_admin_page_parent();

	foreach ($menu as $menu_array) {
		//echo "parent array: " . $menu_array[2];
		if ($menu_array[2] == $parent) {
			if ($user_level < $menu_array[1]) {
				return false;
			} else {
				break;
			}
		}
	}

	if (isset($submenu[$parent])) {
		foreach ($submenu[$parent] as $submenu_array) {
			if ($submenu_array[2] == $pagenow) {
				if ($user_level < $submenu_array[1]) {
					return false;
				} else {
					return true;
				}
			}
		}
	}
	
	return true;
}

function get_admin_page_title() {
	global $title;
	global $submenu;
	global $pagenow;
	global $plugin_page;

	if (isset($title) && ! empty($title)) {
		return $title;
	}

	foreach (array_keys($submenu) as $parent) {
		foreach ($submenu[$parent] as $submenu_array) {
			if (isset($submenu_array[3])) {
				if ($submenu_array[2] == $pagenow) {
					$title = $submenu_array[3];
					return $submenu_array[3];
				} else if (isset($plugin_page) && ($plugin_page == $submenu_array[2])) {
					$title = $submenu_array[3];
					return $submenu_array[3];
				}
			}
		}
	}

	return '';
}

function get_admin_page_parent() {
	global $parent_file;
	global $submenu;
	global $pagenow;
	global $plugin_page;

	if (isset($parent_file) && ! empty($parent_file)) {
		return $parent_file;
	}

	foreach (array_keys($submenu) as $parent) {
		foreach ($submenu[$parent] as $submenu_array) {
			if ($submenu_array[2] == $pagenow) {
				$parent_file = $parent;
				return $parent;
			} else if (isset($plugin_page) && ($plugin_page == $submenu_array[2])) {
				$parent_file = $parent;
				return $parent;
			}
		}
	}

	$parent_file = '';
	return '';
}

function plugin_basename($file) {
	return preg_replace('#^.*sp-content/plugins/#', '', $file);
}

function add_menu_page($page_title, $menu_title, $access_level, $file) {
	global $menu;

	$file = plugin_basename($file);

	$menu[] = array($menu_title, $access_level, $file, $page_title);
}

function add_submenu_page($parent, $page_title, $menu_title, $access_level, $file) {
	global $submenu;
	global $menu;

	$parent = plugin_basename($parent);
	$file = plugin_basename($file);

	// If the parent doesn't already have a submenu, add a link to the parent
	// as the first item in the submenu.  If the submenu file is the same as the
	// parent file someone is trying to link back to the parent manually.  In
	// this case, don't automatically add a link back to avoid duplication.
	if (! isset($submenu[$parent]) && $file != $parent) {
		foreach ($menu as $parent_menu) {
			if ($parent_menu[2] == $parent) {
				$submenu[$parent][] = $parent_menu;
			}
		}
	}
	
	$submenu[$parent][] = array($menu_title, $access_level, $file, $page_title);
}

function add_options_page($page_title, $menu_title, $access_level, $file) {
	add_submenu_page('options-general.php', $page_title, $menu_title, $access_level, $file);
}

function add_management_page($page_title, $menu_title, $access_level, $file) {
	add_submenu_page('edit.php', $page_title, $menu_title, $access_level, $file);
}

function validate_file_to_edit($file, $allowed_files = '') {
	if ('..' == substr($file,0,2))
		die (__('Sorry, can&#8217;t edit files with ".." in the name. If you are trying to edit a file in your SteamPress home directory, you can just type the name of the file in.'));
	
	if (':' == substr($file,1,1))
		die (__('Sorry, can&#8217;t call files with their real path.'));

	if ( !empty($allowed_files) && (! in_array($file, $allowed_files)) ) {
		die (__('Sorry, that file cannot be edited.'));
	}
	
	$file = stripslashes($file);

	return $file;
}

function get_real_file_to_edit($file) {
	$home = get_settings('home');
	if (($home != '')
			&& ($home != get_settings('siteurl')) &&
			('index.php' == $file || get_settings('blogfilename') == $file ||
			 '.htaccess' == $file)) {
		$home_root = parse_url($home);
		$home_root = $home_root['path'];
		$root = str_replace($_SERVER["PHP_SELF"], '', $_SERVER["PATH_TRANSLATED"]);
		$home_root = $root . $home_root;
		$real_file = $home_root . '/' . $file;
	} else {
		$real_file = ABSPATH . $file;
	}

	return $real_file;
}

$sp_file_descriptions = 
	array(
	'index.php' => __('Main Template'),
	'sp-layout.css' => __('Stylesheet'),
	'style.css' => __('Stylesheet'),
	'sp-comments.php' => __('Comments Template'),
	'comments.php' => __('Comments Template'),
	'sp-comments-popup.php' => __('Popup Comments Template'),
	'comments-popup.php' => __('Popup Comments Template'),
	'sp-footer.php' => __('Footer Template'),
	'footer.php' => __('Footer Template'),
	'sp-header.php' => __('Header Template'),
	'header.php' => __('Header Template'),
	'sp-sidebar.php' => __('Sidebar Template'),
	'sidebar.php' => __('Sidebar Template'),
	'archive.php' => __('Archive Template'),
	'category.php' => __('Category Template'),
	'page.php' => __('Page Template'),
	'search.php' => __('Search Template'),
	'single.php' => __('Post Template'),
	'404.php' => __('404 Template'),
	'my-hacks.php' => __('my-hacks.php (legacy hacks support)'),
	'.htaccess' => __('.htaccess (for rewrite rules)')
	);

function get_file_description($file) {
	global $sp_file_descriptions;

	if (isset($sp_file_descriptions[$file])) {
		return $sp_file_descriptions[$file];
	}

	return $file;
}

function update_recently_edited($file) {
	$oldfiles = (array) get_option('recently_edited');
	if ($oldfiles) {
		$oldfiles = array_reverse($oldfiles);
		$oldfiles[] = $file;
		$oldfiles = array_reverse($oldfiles);
		$oldfiles = array_unique($oldfiles);
		if ( 5 < count($oldfiles) )
			array_pop($oldfiles);
	} else {
		$oldfiles[] = $file;
	}
	update_option('recently_edited', $oldfiles);
}

function get_plugin_data($plugin_file) {
	$plugin_data = implode('', file($plugin_file));
	preg_match("|Plugin Name:(.*)|i", $plugin_data, $plugin_name);
	preg_match("|Plugin URI:(.*)|i", $plugin_data, $plugin_uri);
	preg_match("|Description:(.*)|i", $plugin_data, $description);
	preg_match("|Author:(.*)|i", $plugin_data, $author_name);
	preg_match("|Author URI:(.*)|i", $plugin_data, $author_uri);
	if ( preg_match("|Version:(.*)|i", $plugin_data, $version) )
		$version = $version[1];
	else
		$version ='';

	$description = sptexturize($description[1]);

	$name = $plugin_name[1];
	$name = trim($name);
	$plugin = $name;
	if ('' != $plugin_uri[1] && '' != $name) {
		$plugin = __("<a href='{$plugin_uri[1]}' title='Visit plugin homepage'>{$plugin}</a>");
	}

	if ('' == $author_uri[1]) {
		$author = $author_name[1];
	} else {
		$author = __("<a href='{$author_uri[1]}' title='Visit author homepage'>{$author_name[1]}</a>");
	}

	return array('Name' => $name, 'Title' => $plugin, 'Description' => $description, 'Author' => $author, 'Version' => $version, 'Template' => $template[1]);
}

function get_plugins() {
	global $sp_plugins;

	if (isset($sp_plugins)) {
		return $sp_plugins;
	}

	$sp_plugins = array();
	$plugin_loc = 'sp-content/plugins';
	$plugin_root = ABSPATH . $plugin_loc;

	// Files in sp-content/plugins directory
	$plugins_dir = @ dir($plugin_root);
	if ($plugins_dir) {
		while(($file = $plugins_dir->read()) !== false) {
			if ( preg_match('|^\.+$|', $file) )
				continue;
			if (is_dir($plugin_root . '/' . $file)) {
				$plugins_subdir = @ dir($plugin_root . '/' . $file);
				if ($plugins_subdir) {
					while(($subfile = $plugins_subdir->read()) !== false) {
						if ( preg_match('|^\.+$|', $subfile) )
							continue;
						if ( preg_match('|\.php$|', $subfile) )
							$plugin_files[] = "$file/$subfile";
					}
				}
			} else {
				if ( preg_match('|\.php$|', $file) ) 
					$plugin_files[] = $file;
			}
		}
	}

	if (!$plugins_dir || !$plugin_files) {
		return $sp_plugins;
	}

	sort($plugin_files);

	foreach($plugin_files as $plugin_file) {
		$plugin_data = get_plugin_data("$plugin_root/$plugin_file");
	  
		if (empty($plugin_data['Name'])) {
			continue;
		}

		$sp_plugins[plugin_basename($plugin_file)] = $plugin_data;
	}

	return $sp_plugins;
}

?>
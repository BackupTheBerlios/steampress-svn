<?php

require_once(dirname(__FILE__) . '/upgrade-schema.php');
// Functions to be called in install and upgrade scripts
function upgrade_all() {
	populate_options();
	upgrade_100();
	upgrade_101();
	upgrade_110();
	upgrade_130();
}

function upgrade_100() {
	global $spdb;

	// Get the title and ID of every post, post_name to check if it already has a value
	$posts = $spdb->get_results("SELECT ID, post_title, post_name FROM $spdb->posts WHERE post_name = ''");
	if ($posts) {
		foreach($posts as $post) {
			if ('' == $post->post_name) { 
				$newtitle = sanitize_title($post->post_title);
				$spdb->query("UPDATE $spdb->posts SET post_name = '$newtitle' WHERE ID = '$post->ID'");
			}
		}
	}
	
	$categories = $spdb->get_results("SELECT cat_ID, cat_name, category_nicename FROM $spdb->categories");
	foreach ($categories as $category) {
		if ('' == $category->category_nicename) { 
			$newtitle = sanitize_title($category->cat_name);
			$spdb->query("UPDATE $spdb->categories SET category_nicename = '$newtitle' WHERE cat_ID = '$category->cat_ID'");
		}
	}


	$spdb->query("UPDATE $spdb->options SET option_value = REPLACE(option_value, 'sp-links/links-images/', 'sp-images/links/')
	WHERE option_name LIKE 'links_rating_image%'
	AND option_value LIKE 'sp-links/links-images/%'");

	$done_ids = $spdb->get_results("SELECT DISTINCT post_id FROM $spdb->post2cat");
	if ($done_ids) :
		foreach ($done_ids as $done_id) :
			$done_posts[] = $done_id->post_id;
		endforeach;
		$catwhere = ' AND ID NOT IN (' . implode(',', $done_posts) . ')';
	else:
		$catwhere = '';
	endif;
	
	$allposts = $spdb->get_results("SELECT ID, post_category FROM $spdb->posts WHERE post_category != '0' $catwhere");
	if ($allposts) :
		foreach ($allposts as $post) {
			// Check to see if it's already been imported
			$cat = $spdb->get_row("SELECT * FROM $spdb->post2cat WHERE post_id = $post->ID AND category_id = $post->post_category");
			if (!$cat && 0 != $post->post_category) { // If there's no result
				$spdb->query("
					INSERT INTO $spdb->post2cat
					(post_id, category_id)
					VALUES
					('$post->ID', '$post->post_category')
					");
			}
		}
	endif;
}

function upgrade_101() {
	global $spdb;

	// Clean up indices, add a few
	add_clean_index($spdb->posts, 'post_name');
	add_clean_index($spdb->posts, 'post_status');
	add_clean_index($spdb->categories, 'category_nicename');
	add_clean_index($spdb->comments, 'comment_approved');
	add_clean_index($spdb->comments, 'comment_post_ID');
	add_clean_index($spdb->links , 'link_category');
	add_clean_index($spdb->links , 'link_visible');
}


function upgrade_110() {
  global $spdb;
	
    // Set user_nicename.
	$users = $spdb->get_results("SELECT ID, user_nickname, user_nicename FROM $spdb->users");
	foreach ($users as $user) {
		if ('' == $user->user_nicename) { 
			$newname = sanitize_title($user->user_nickname);
			$spdb->query("UPDATE $spdb->users SET user_nicename = '$newname' WHERE ID = '$user->ID'");
		}
	}

	$users = $spdb->get_results("SELECT ID, user_pass from $spdb->users");
	foreach ($users as $row) {
		if (!preg_match('/^[A-Fa-f0-9]{32}$/', $row->user_pass)) {
			   $spdb->query('UPDATE '.$spdb->users.' SET user_pass = MD5(\''.$row->user_pass.'\') WHERE ID = \''.$row->ID.'\'');
		}
	}


	// Get the GMT offset, we'll use that later on
	$all_options = get_alloptions_110();

	$time_difference = $all_options->time_difference;

	$server_time = time()+date('Z');
	$weblogger_time = $server_time + $time_difference*3600;
	$gmt_time = time();

	$diff_gmt_server = ($gmt_time - $server_time) / 3600;
	$diff_weblogger_server = ($weblogger_time - $server_time) / 3600;
	$diff_gmt_weblogger = $diff_gmt_server - $diff_weblogger_server;
	$gmt_offset = -$diff_gmt_weblogger;

	// Add a gmt_offset option, with value $gmt_offset
	add_option('gmt_offset', $gmt_offset);

	// Check if we already set the GMT fields (if we did, then
	// MAX(post_date_gmt) can't be '0000-00-00 00:00:00'
	// <michel_v> I just slapped myself silly for not thinking about it earlier
	$got_gmt_fields = ($spdb->get_var("SELECT MAX(post_date_gmt) FROM $spdb->posts") == '0000-00-00 00:00:00') ? false : true;

	if (!$got_gmt_fields) {

		// Add or substract time to all dates, to get GMT dates
		$add_hours = intval($diff_gmt_weblogger);
		$add_minutes = intval(60 * ($diff_gmt_weblogger - $add_hours));
		$spdb->query("UPDATE $spdb->posts SET post_date_gmt = DATE_ADD(post_date, INTERVAL '$add_hours:$add_minutes' HOUR_MINUTE)");
		$spdb->query("UPDATE $spdb->posts SET post_modified = post_date");
		$spdb->query("UPDATE $spdb->posts SET post_modified_gmt = DATE_ADD(post_modified, INTERVAL '$add_hours:$add_minutes' HOUR_MINUTE) WHERE post_modified != '0000-00-00 00:00:00'");
		$spdb->query("UPDATE $spdb->comments SET comment_date_gmt = DATE_ADD(comment_date, INTERVAL '$add_hours:$add_minutes' HOUR_MINUTE)");
		$spdb->query("UPDATE $spdb->users SET user_registered = DATE_ADD(dateYMDhour, INTERVAL '$add_hours:$add_minutes' HOUR_MINUTE)");
	}

	// First we need to enlarge option_value so it can hold larger values:
	$spdb->query("ALTER TABLE `$spdb->options` CHANGE `option_value` `option_value` TEXT NOT NULL");

	$spdb->query("ALTER TABLE `$spdb->comments` CHANGE `comment_author_url` `comment_author_url` VARCHAR( 200 ) NOT NULL");
}

function upgrade_130() {
    global $spdb, $table_prefix;

    // Remove extraneous backslashes.
	$posts = $spdb->get_results("SELECT ID, post_title, post_content, post_excerpt, guid, post_date, post_name, post_status, post_author FROM $spdb->posts");
	if ($posts) {
		foreach($posts as $post) {
            $post_content = addslashes(deslash($post->post_content));
            $post_title = addslashes(deslash($post->post_title));
            $post_excerpt = addslashes(deslash($post->post_excerpt));
			if ( empty($post->guid) )
				$guid = get_permalink($post->ID);
			else
				$guid = $post->guid;

            $spdb->query("UPDATE $spdb->posts SET post_title = '$post_title', post_content = '$post_content', post_excerpt = '$post_excerpt', guid = '$guid' WHERE ID = '$post->ID'");
		}
	}

    // Remove extraneous backslashes.
	$comments = $spdb->get_results("SELECT comment_ID, comment_author, comment_content FROM $spdb->comments");
	if ($comments) {
		foreach($comments as $comment) {
            $comment_content = addslashes(deslash($comment->comment_content));
            $comment_author = addslashes(deslash($comment->comment_author));
            $spdb->query("UPDATE $spdb->comments SET comment_content = '$comment_content', comment_author = '$comment_author' WHERE comment_ID = '$comment->comment_ID'");
		}
	}

    // Remove extraneous backslashes.
	$links = $spdb->get_results("SELECT link_id, link_name, link_description FROM $spdb->links");
	if ($links) {
		foreach($links as $link) {
            $link_name = addslashes(deslash($link->link_name));
            $link_description = addslashes(deslash($link->link_description));
            $spdb->query("UPDATE $spdb->links SET link_name = '$link_name', link_description = '$link_description' WHERE link_id = '$link->link_id'");
		}
	}

    // The "paged" option for what_to_show is no more.
    if ($spdb->get_var("SELECT option_value FROM $spdb->options WHERE option_name = 'what_to_show'") == 'paged') {
        $spdb->query("UPDATE $spdb->options SET option_value = 'posts' WHERE option_name = 'what_to_show'");
    }

	if ( !is_array( get_settings('active_plugins') ) ) {
		$plugins = explode("\n", trim(get_settings('active_plugins')) );
		update_option('active_plugins', $plugins);
	}

	// Obsolete tables
	$spdb->query('DROP TABLE IF EXISTS ' . $table_prefix . 'optionvalues');
	$spdb->query('DROP TABLE IF EXISTS ' . $table_prefix . 'optiontypes');
	$spdb->query('DROP TABLE IF EXISTS ' . $table_prefix . 'optiongroups');
	$spdb->query('DROP TABLE IF EXISTS ' . $table_prefix . 'optiongroup_options');

	// Update comments table to use comment_type
	$spdb->query("UPDATE $spdb->comments SET comment_type='trackback', comment_content = REPLACE(comment_content, '<trackback />', '') WHERE comment_content LIKE '<trackback />%'");
	$spdb->query("UPDATE $spdb->comments SET comment_type='pingback', comment_content = REPLACE(comment_content, '<pingback />', '') WHERE comment_content LIKE '<pingback />%'");

	// Some versions have multiple duplicate option_name rows with the same values
	$options = $spdb->get_results("SELECT option_name, COUNT(option_name) AS dupes FROM `$spdb->options` GROUP BY option_name");
	foreach ( $options as $option ) {
		if ( 1 != $option->dupes ) { // Could this be done in the query?
			$limit = $option->dupes - 1;
			$dupe_ids = $spdb->get_col("SELECT option_id FROM $spdb->options WHERE option_name = '$option->option_name' LIMIT $limit");
			$dupe_ids = join($dupe_ids, ',');
			$spdb->query("DELETE FROM $spdb->options WHERE option_id IN ($dupe_ids)");
		}
	}
}

// The functions we use to actually do stuff

// General
function maybe_create_table($table_name, $create_ddl) {
    global $spdb;
    foreach ($spdb->get_col("SHOW TABLES",0) as $table ) {
        if ($table == $table_name) {
            return true;
        }
    }
    //didn't find it try to create it.
    $q = $spdb->query($create_ddl);
    // we cannot directly tell that whether this succeeded!
    foreach ($spdb->get_col("SHOW TABLES",0) as $table ) {
        if ($table == $table_name) {
            return true;
        }
    }
    return false;
}

function drop_index($table, $index) {
	global $spdb;
	$spdb->hide_errors();
	$spdb->query("ALTER TABLE `$table` DROP INDEX `$index`");
	// Now we need to take out all the extra ones we may have created
	for ($i = 0; $i < 25; $i++) {
		$spdb->query("ALTER TABLE `$table` DROP INDEX `{$index}_$i`");
	}
	$spdb->show_errors();
	return true;
}

function add_clean_index($table, $index) {
	global $spdb;
	drop_index($table, $index);
	$spdb->query("ALTER TABLE `$table` ADD INDEX ( `$index` )");
	return true;
}

/**
 ** maybe_add_column()
 ** Add column to db table if it doesn't exist.
 ** Returns:  true if already exists or on successful completion
 **           false on error
 */
function maybe_add_column($table_name, $column_name, $create_ddl) {
    global $spdb, $debug;
    foreach ($spdb->get_col("DESC $table_name", 0) as $column ) {
        if ($debug) echo("checking $column == $column_name<br />");
        if ($column == $column_name) {
            return true;
        }
    }
    //didn't find it try to create it.
    $q = $spdb->query($create_ddl);
    // we cannot directly tell that whether this succeeded!
    foreach ($spdb->get_col("DESC $table_name", 0) as $column ) {
        if ($column == $column_name) {
            return true;
        }
    }
    return false;
}


// get_alloptions as it was for 1.2.
function get_alloptions_110() {
	global $spdb;
	if ($options = $spdb->get_results("SELECT option_name, option_value FROM $spdb->options")) {
		foreach ($options as $option) {
			// "When trying to design a foolproof system, 
			//  never underestimate the ingenuity of the fools :)" -- Dougal
			if ('siteurl' == $option->option_name) $option->option_value = preg_replace('|/+$|', '', $option->option_value);
			if ('home' == $option->option_name) $option->option_value = preg_replace('|/+$|', '', $option->option_value);
			if ('category_base' == $option->option_name) $option->option_value = preg_replace('|/+$|', '', $option->option_value);
			$all_options->{$option->option_name} = stripslashes($option->option_value);
		}
	}
	return $all_options;
}

function deslash($content) {
    // Note: \\\ inside a regex denotes a single backslash.

    // Replace one or more backslashes followed by a single quote with
    // a single quote.
    $content = preg_replace("/\\\+'/", "'", $content);

    // Replace one or more backslashes followed by a double quote with
    // a double quote.
    $content = preg_replace('/\\\+"/', '"', $content);

    // Replace one or more backslashes with one backslash.
    $content = preg_replace("/\\\+/", "\\", $content);

    return $content;
}

function dbDelta($queries, $execute = true) {
	global $spdb;
	
	// Seperate individual queries into an array
	if( !is_array($queries) ) {
		$queries = explode( ';', $queries );
		if('' == $queries[count($queries) - 1]) array_pop($queries);
	}
	
	$cqueries = array(); // Creation Queries
	$iqueries = array(); // Insertion Queries
	$for_update = array();
	
	// Create a tablename index for an array ($cqueries) of queries
	foreach($queries as $qry) {
		if(preg_match("|CREATE TABLE ([^ ]*)|", $qry, $matches)) {
			$cqueries[strtolower($matches[1])] = $qry;
			$for_update[$matches[1]] = 'Created table '.$matches[1];
		}
		else if(preg_match("|CREATE DATABASE ([^ ]*)|", $qry, $matches)) {
			array_unshift($cqueries, $qry);
		}
		else if(preg_match("|INSERT INTO ([^ ]*)|", $qry, $matches)) {
			$iqueries[] = $qry;
		}
		else if(preg_match("|UPDATE ([^ ]*)|", $qry, $matches)) {
			$iqueries[] = $qry;
		}
		else {
			// Unrecognized query type
		}
	}	

	// Check to see which tables and fields exist
	if($tables = $spdb->get_col('SHOW TABLES;')) {
		// For every table in the database
		foreach($tables as $table) {
			// If a table query exists for the database table...
			if( array_key_exists(strtolower($table), $cqueries) ) {
				// Clear the field and index arrays
				unset($cfields);
				unset($indices);
				// Get all of the field names in the query from between the parens
				preg_match("|\((.*)\)|ms", $cqueries[strtolower($table)], $match2);
				$qryline = trim($match2[1]);

				// Separate field lines into an array
				$flds = explode("\n", $qryline);

				//echo "<hr/><pre>\n".print_r(strtolower($table), true).":\n".print_r($cqueries, true)."</pre><hr/>";
				
				// For every field line specified in the query
				foreach($flds as $fld) {
					// Extract the field name
					preg_match("|^([^ ]*)|", trim($fld), $fvals);
					$fieldname = $fvals[1];
					
					// Verify the found field name
					$validfield = true;
					switch(strtolower($fieldname))
					{
					case '':
					case 'primary':
					case 'index':
					case 'fulltext':
					case 'unique':
					case 'key':
						$validfield = false;
						$indices[] = trim(trim($fld), ", \n");
						break;
					}
					$fld = trim($fld);
					
					// If it's a valid field, add it to the field array
					if($validfield) {
						$cfields[strtolower($fieldname)] = trim($fld, ", \n");
					}
				}
				
				// Fetch the table column structure from the database
				$tablefields = $spdb->get_results("DESCRIBE {$table};");
								
				// For every field in the table
				foreach($tablefields as $tablefield) {				
					// If the table field exists in the field array...
					if(array_key_exists(strtolower($tablefield->Field), $cfields)) {
						// Get the field type from the query
						preg_match("|".$tablefield->Field." ([^ ]*( unsigned)?)|i", $cfields[strtolower($tablefield->Field)], $matches);
						$fieldtype = $matches[1];

						// Is actual field type different from the field type in query?
						if($tablefield->Type != $fieldtype) {
							// Add a query to change the column type
							$cqueries[] = "ALTER TABLE {$table} CHANGE COLUMN {$tablefield->Field} " . $cfields[strtolower($tablefield->Field)];
							$for_update[$table.'.'.$tablefield->Field] = "Changed type of {$table}.{$tablefield->Field} from {$tablefield->Type} to {$fieldtype}";
						}
						
						// Get the default value from the array
							//echo "{$cfields[strtolower($tablefield->Field)]}<br>";
						if(preg_match("| DEFAULT '(.*)'|i", $cfields[strtolower($tablefield->Field)], $matches)) {
							$default_value = $matches[1];
							if($tablefield->Default != $default_value)
							{
								// Add a query to change the column's default value
								$cqueries[] = "ALTER TABLE {$table} ALTER COLUMN {$tablefield->Field} SET DEFAULT '{$default_value}'";
								$for_update[$table.'.'.$tablefield->Field] = "Changed default value of {$table}.{$tablefield->Field} from {$tablefield->Default} to {$default_value}";
							}
						}

						// Remove the field from the array (so it's not added)
						unset($cfields[strtolower($tablefield->Field)]);
					}
					else {
						// This field exists in the table, but not in the creation queries?
					}
				}

				// For every remaining field specified for the table
				foreach($cfields as $fieldname => $fielddef) {
					// Push a query line into $cqueries that adds the field to that table
					$cqueries[] = "ALTER TABLE {$table} ADD COLUMN $fielddef";
					$for_update[$table.'.'.$fieldname] = 'Added column '.$table.'.'.$fieldname;
				}
				
				// Index stuff goes here
				// Fetch the table index structure from the database
				$tableindices = $spdb->get_results("SHOW INDEX FROM {$table};");
				
				if($tableindices) {
					// Clear the index array
					unset($index_ary);

					// For every index in the table
					foreach($tableindices as $tableindex) {
						// Add the index to the index data array
						$keyname = $tableindex->Key_name;
						$index_ary[$keyname]['columns'][] = array('fieldname' => $tableindex->Column_name, 'subpart' => $tableindex->Sub_part);
						$index_ary[$keyname]['unique'] = ($tableindex->Non_unique == 0)?true:false;
					}

					// For each actual index in the index array
					foreach($index_ary as $index_name => $index_data) {
						// Build a create string to compare to the query
						$index_string = '';
						if($index_name == 'PRIMARY') {
							$index_string .= 'PRIMARY ';
						}
						else if($index_data['unique']) {
							$index_string .= 'UNIQUE ';
						}
						$index_string .= 'KEY ';
						if($index_name != 'PRIMARY') {
							$index_string .= $index_name;
						}
						$index_columns = '';
						// For each column in the index
						foreach($index_data['columns'] as $column_data) {					
							if($index_columns != '') $index_columns .= ',';
							// Add the field to the column list string
							$index_columns .= $column_data['fieldname'];
							if($column_data['subpart'] != '') {
								$index_columns .= '('.$column_data['subpart'].')';
							}
						}
						// Add the column list to the index create string 
						$index_string .= ' ('.$index_columns.')';

						if(!(($aindex = array_search($index_string, $indices)) === false)) {
							unset($indices[$aindex]);
							//echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">{$table}:<br/>Found index:".$index_string."</pre>\n";
						}
						//else echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">{$table}:<br/><b>Did not find index:</b>".$index_string."<br/>".print_r($indices, true)."</pre>\n";
					}
				}

				// For every remaining index specified for the table
				foreach($indices as $index) {
					// Push a query line into $cqueries that adds the index to that table
					$cqueries[] = "ALTER TABLE {$table} ADD $index";
					$for_update[$table.'.'.$fieldname] = 'Added index '.$table.' '.$index;
				}

				// Remove the original table creation query from processing
				unset($cqueries[strtolower($table)]);
				unset($for_update[strtolower($table)]);
			} else {
				// This table exists in the database, but not in the creation queries?
			}
		}
	}

	$allqueries = array_merge($cqueries, $iqueries);
	if($execute) {
		foreach($allqueries as $query) {
			//echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">".print_r($query, true)."</pre>\n";
			$spdb->query($query);
		}
	}

	return $for_update;
}

function make_db_current() {
	global $sp_queries;

	$alterations = dbDelta($sp_queries);
	echo "<ol>\n";
	foreach($alterations as $alteration) echo "<li>$alteration</li>\n";
	echo "</ol>\n";
}

function make_db_current_silent() {
	global $sp_queries;

	$alterations = dbDelta($sp_queries);
}

?>
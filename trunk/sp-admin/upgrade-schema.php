<?php
// Here we keep the DB structure and option values

$sp_queries="CREATE TABLE $spdb->categories (
  cat_ID int(4) NOT NULL auto_increment,
  cat_name varchar(55) NOT NULL default '',
  category_nicename varchar(200) NOT NULL default '',
  category_description text NOT NULL,
  category_parent int(4) NOT NULL default '0',
  PRIMARY KEY  (cat_ID),
  UNIQUE KEY cat_name (cat_name),
  KEY category_nicename (category_nicename)
);
CREATE TABLE $spdb->comments (
  comment_ID int(11) unsigned NOT NULL auto_increment,
  comment_post_ID int(11) NOT NULL default '0',
  comment_author tinytext NOT NULL,
  comment_author_email varchar(100) NOT NULL default '',
  comment_author_url varchar(200) NOT NULL default '',
  comment_author_IP varchar(100) NOT NULL default '',
  comment_date datetime NOT NULL default '0000-00-00 00:00:00',
  comment_date_gmt datetime NOT NULL default '0000-00-00 00:00:00',
  comment_content text NOT NULL,
  comment_karma int(11) NOT NULL default '0',
  comment_approved enum('0','1') NOT NULL default '1',
  comment_agent varchar(255) NOT NULL default '',
  comment_type varchar(20) NOT NULL default '',
  comment_parent int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  PRIMARY KEY  (comment_ID),
  KEY comment_approved (comment_approved),
  KEY comment_post_ID (comment_post_ID)
);
CREATE TABLE $spdb->linkcategories (
  cat_id int(11) NOT NULL auto_increment,
  cat_name tinytext NOT NULL,
  auto_toggle enum('Y','N') NOT NULL default 'N',
  show_images enum('Y','N') NOT NULL default 'Y',
  show_description enum('Y','N') NOT NULL default 'N',
  show_rating enum('Y','N') NOT NULL default 'Y',
  show_updated enum('Y','N') NOT NULL default 'Y',
  sort_order varchar(64) NOT NULL default 'rand',
  sort_desc enum('Y','N') NOT NULL default 'N',
  text_before_link varchar(128) NOT NULL default '<li>',
  text_after_link varchar(128) NOT NULL default '<br />',
  text_after_all varchar(128) NOT NULL default '</li>',
  list_limit int(11) NOT NULL default '-1',
  PRIMARY KEY  (cat_id)
);
CREATE TABLE $spdb->links (
  link_id int(11) NOT NULL auto_increment,
  link_url varchar(255) NOT NULL default '',
  link_name varchar(255) NOT NULL default '',
  link_image varchar(255) NOT NULL default '',
  link_target varchar(25) NOT NULL default '',
  link_category int(11) NOT NULL default '0',
  link_description varchar(255) NOT NULL default '',
  link_visible enum('Y','N') NOT NULL default 'Y',
  link_owner int(11) NOT NULL default '1',
  link_rating int(11) NOT NULL default '0',
  link_updated datetime NOT NULL default '0000-00-00 00:00:00',
  link_rel varchar(255) NOT NULL default '',
  link_notes mediumtext NOT NULL,
  link_rss varchar(255) NOT NULL default '',
  PRIMARY KEY  (link_id),
  KEY link_category (link_category),
  KEY link_visible (link_visible)
);
CREATE TABLE $spdb->options (
  option_id int(11) NOT NULL auto_increment,
  blog_id int(11) NOT NULL default '0',
  option_name varchar(64) NOT NULL default '',
  option_can_override enum('Y','N') NOT NULL default 'Y',
  option_type int(11) NOT NULL default '1',
  option_value text NOT NULL,
  option_width int(11) NOT NULL default '20',
  option_height int(11) NOT NULL default '8',
  option_description tinytext NOT NULL,
  option_admin_level int(11) NOT NULL default '1',
  autoload enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (option_id,blog_id,option_name),
  KEY option_name (option_name)
);
CREATE TABLE $spdb->post2cat (
  rel_id int(11) NOT NULL auto_increment,
  post_id int(11) NOT NULL default '0',
  category_id int(11) NOT NULL default '0',
  PRIMARY KEY  (rel_id),
  KEY post_id (post_id,category_id)
);
CREATE TABLE $spdb->postmeta (
  meta_id int(11) NOT NULL auto_increment,
  post_id int(11) NOT NULL default '0',
  meta_key varchar(255) default NULL,
  meta_value text,
  PRIMARY KEY  (meta_id),
  KEY post_id (post_id),
  KEY meta_key (meta_key)
);
CREATE TABLE $spdb->posts (
  ID int(11) unsigned NOT NULL auto_increment,
  post_author int(4) NOT NULL default '0',
  post_date datetime NOT NULL default '0000-00-00 00:00:00',
  post_date_gmt datetime NOT NULL default '0000-00-00 00:00:00',
  post_content text NOT NULL,
  post_title text NOT NULL,
  post_category int(4) NOT NULL default '0',
  post_excerpt text NOT NULL,
  post_status enum('publish','draft','private','static') NOT NULL default 'publish',
  comment_status enum('open','closed','registered_only') NOT NULL default 'open',
  ping_status enum('open','closed') NOT NULL default 'open',
  post_password varchar(20) NOT NULL default '',
  post_name varchar(200) NOT NULL default '',
  to_ping text NOT NULL,
  pinged text NOT NULL,
  post_modified datetime NOT NULL default '0000-00-00 00:00:00',
  post_modified_gmt datetime NOT NULL default '0000-00-00 00:00:00',
  post_content_filtered text NOT NULL,
  post_parent int(11) NOT NULL default '0',
  guid varchar(255) NOT NULL default '',
  menu_order int(11) NOT NULL default '0',
  PRIMARY KEY  (ID),
  KEY post_name (post_name)
);
CREATE TABLE $spdb->users (
  ID int(10) unsigned NOT NULL auto_increment,
  user_login varchar(20) NOT NULL default '',
  user_pass varchar(64) NOT NULL default '',
  user_firstname varchar(50) NOT NULL default '',
  user_lastname varchar(50) NOT NULL default '',
  user_nickname varchar(50) NOT NULL default '',
  user_nicename varchar(50) NOT NULL default '',
  user_icq int(10) unsigned NOT NULL default '0',
  user_email varchar(100) NOT NULL default '',
  user_url varchar(100) NOT NULL default '',
  user_ip varchar(15) NOT NULL default '',
  user_domain varchar(200) NOT NULL default '',
  user_browser varchar(200) NOT NULL default '',
  user_registered datetime NOT NULL default '0000-00-00 00:00:00',
  user_level int(2) unsigned NOT NULL default '0',
  user_aim varchar(50) NOT NULL default '',
  user_msn varchar(100) NOT NULL default '',
  user_yim varchar(50) NOT NULL default '',
  user_idmode varchar(20) NOT NULL default '',
  user_activation_key varchar(60) NOT NULL default '',
  user_status int(11) NOT NULL default '0',
  user_description TEXT NOT NULL default '',
  PRIMARY KEY  (ID),
  UNIQUE KEY user_login (user_login)
);";

function populate_options() {
	global $spdb;

	$guessurl = preg_replace('|/sp-admin/.*|i', '', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	add_option('siteurl', $guessurl, 'SteamPress web address');
	add_option('blogname', 'My Weblog', 'Blog title');
	add_option('blogdescription', 'Just another SteamPress weblog', 'Short tagline');
	add_option('new_users_can_blog', 0);
	add_option('users_can_register', 1);
	add_option('admin_email', 'you@example.com');
	add_option('start_of_week', 1);
	add_option('use_balanceTags', 1);
	add_option('use_smilies', 1);
	add_option('require_name_email', 1);
	add_option('comments_notify', 1);
	add_option('posts_per_rss', 10);
	add_option('rss_excerpt_length', 50);
	add_option('rss_use_excerpt', 0);
	add_option('use_fileupload', 0);
	add_option('fileupload_realpath', ABSPATH . 'sp-content');
	add_option('fileupload_url', get_option('siteurl') . '/sp-content');
	add_option('fileupload_allowedtypes', 'jpg jpeg gif png');
	add_option('fileupload_maxk', 300);
	add_option('fileupload_minlevel', 6);
	add_option('mailserver_url', 'mail.example.com');
	add_option('mailserver_login', 'login@example.com');
	add_option('mailserver_pass', 'password');
	add_option('mailserver_port', 110);
	add_option('default_category', 1);
	add_option('default_comment_status', 'open');
	add_option('default_ping_status', 'open');
	add_option('default_pingback_flag', 1);
	add_option('default_post_edit_rows', 9);
	add_option('posts_per_page', 10);
	add_option('what_to_show', 'posts');
	add_option('date_format', 'F j, Y');
	add_option('time_format', 'g:i a');
	add_option('weblogs_xml_url', 'http://static.steampress.berlios.de/changes.xml');
	add_option('links_updated_date_format', 'F j, Y g:i a');
	add_option('links_recently_updated_prepend', '<em>');
	add_option('links_recently_updated_append', '</em>');
	add_option('links_recently_updated_time', 120);
	add_option('comment_moderation', 0);
	add_option('moderation_notify', 1);
	add_option('permalink_structure');
	add_option('gzipcompression', 0);
	add_option('hack_file', 0);
	add_option('blog_charset', 'utf-8');
	add_option('moderation_keys');
	add_option('active_plugins');
	add_option('home');
	add_option('category_base');
	add_option('ping_sites', 'http://rpc.pingomatic.com/');
	add_option('advanced_edit', 0);
	add_option('comment_max_links', 2);
	// 1.3
	add_option('default_email_category', 1, 'Posts by email go to this category');
	add_option('recently_edited');
	add_option('use_linksupdate', 0);
	add_option('template', 'default');
	add_option('stylesheet', 'default');
	add_option('comment_whitelist', 0);
	add_option('page_uris');

	// Delete unused options
	$unusedoptions = array ('blodotgsping_url', 'bodyterminator', 'emailtestonly', 'phoneemail_separator', 'smilies_directory', 'subjectprefix', 'use_bbcode', 'use_blodotgsping', 'use_phoneemail', 'use_quicktags', 'use_weblogsping', 'weblogs_cache_file', 'use_preview', 'use_htmltrans', 'smilies_directory', 'rss_language', 'fileupload_allowedusers', 'use_phoneemail', 'default_post_status', 'default_post_category', 'archive_mode', 'time_difference', 'links_minadminlevel', 'links_use_adminlevels', 'links_rating_type', 'links_rating_char', 'links_rating_ignore_zero', 'links_rating_single_image', 'links_rating_image0', 'links_rating_image1', 'links_rating_image2', 'links_rating_image3', 'links_rating_image4', 'links_rating_image5', 'links_rating_image6', 'links_rating_image7', 'links_rating_image8', 'links_rating_image9', 'weblogs_cacheminutes', 'comment_allowed_tags', 'search_engine_friendly_urls', 'default_geourl_lat', 'default_geourl_lon', 'use_default_geourl');
	foreach ($unusedoptions as $option) :
		delete_option($option);
	endforeach;

	// Set up a few options not to load by default
	$fatoptions = array( 'moderation_keys', 'recently_edited' );
	foreach ($fatoptions as $fatoption) :
		$spdb->query("UPDATE $spdb->options SET `autoload` = 'no' WHERE option_name = '$fatoption'");
	endforeach;
}
?>
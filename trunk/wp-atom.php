<?php
// Atom 1.0 support for SteamPress.
// Written by Michael Hampton (error@ioerror.us),
// based on WordPress 1.5.2 wp-atom.php
//
// License: GNU Public License Version 2

if (empty($feed))
{
	$blog = 1;
	$feed = 'atom';
	$doing_rss = 1;
	require('wp-blog-header.php');
}

header('Content-type: application/atom+xml; charset=' . get_settings('blog_charset'), true);
$more = 1;

?>
<?php echo '<?xml version="1.0" encoding="'.get_settings('blog_charset').'" ?'.'>'; // The blank line below is supposed to be there. ?>

<feed xmlns="http://www.w3.org/2005/Atom">
	<id><?php bloginfo_rss('atom_url') ?></id>
	<title type="text"><?php bloginfo_rss('name') ?></title>
	<subtitle type="text"><?php bloginfo_rss('description') ?></subtitle>
	<link rel="self" type="application/atom+xml" href="<?php bloginfo_rss('atom_url') ?>" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo_rss('rss2_url') ?>" />
	<link rel="alternate" type="text/html"<?php if (defined('WPLANG') && strcmp(constant('WPLANG'), '')) echo ' hreflang="' . WPLANG . '"';?> href="<?php bloginfo_rss('home') ?>" />
	<updated><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_lastpostmodified('GMT'), false); ?></updated>
	<rights type="text">Copyright <?php echo mysql2date('Y', get_lastpostdate('blog'), 0); ?></rights>
	<generator uri="http://steampress.org/" version="<?php bloginfo_rss('version'); ?>">SteamPress <?php bloginfo_rss('version'); ?></generator>

<?php
$items_count = 0;
if ($posts)
{
	foreach ($posts as $post)
	{
		start_wp();
?>
	<entry>
		<author>
			<name><?php the_author() ?></name>
		</author>
		<title type="html"><?php the_title_rss() ?></title>
		<link rel="alternate" type="text/html" href="<?php permalink_single_rss() ?>" />
		<id><?php bloginfo_rss('atom_url') ?></id>
		<updated><?php echo get_post_time('Y-m-d\TH:i:s\Z', true); ?></updated>
		<published><?php echo get_post_time('Y-m-d\TH:i:s\Z', true); ?></published>
<?php // Atom categories
		$categories = get_the_category();
		$the_list = '';
		foreach ($categories as $category)
		{
			$category->cat_name = convert_chars($category->cat_name);
			$the_list .= "\t<category term=\"$category->cat_name\" />\n";
		}
		// NB: some plugins don't read the second argument and will generate
		// complete garbage here... stupid tag warrior....
		// You could also just echo $the_list; and screw the filters
		echo apply_filters('the_category_rss', $the_list, 'atom'); // Future-proof it
?>
<?php // Atom enclosures
		if (empty($post->post_password) || ($_COOKIE['wp-postpass_'.COOKIEHASH] == $post->post_password))
		{
			$custom_fields = get_post_custom();
			if ( is_array( $custom_fields ) )
			{
				while ( list( $key, $val ) = each( $custom_fields ) )
				{
					if ( $key == 'enclosure' && is_array($val) )
					{
						foreach ($val as $enc)
						{
							$enclosure = split( "\n", $enc );
							print "<link rel=\"enclosure\" length=\"".trim( $enclosure[ 1 ] )."\" type=\"".trim( $enclosure[ 2 ] )."\" href=\"".trim( htmlspecialchars($enclosure[ 0 ]) )."\"/>\n";
						}
					}
				}
			}
		}
?>
		<summary type="text"><?php the_excerpt_rss(); ?></summary>
<?php
// Always give full content to Technoratibot; this gets you better positioned in Technorati's various indices
		if (!get_settings('rss_use_excerpt') || strstr($_SERVER['HTTP_USER_AGENT'], "Technoratibot") !== FALSE)
		{
?>
<?php // Ignore bloginfo('html_type') since it's deliberately set incorrectly; SteamPress currently generates ONLY XHTML ?>
		<content type="xhtml"><div xmlns="http://www.w3.org/1999/xhtml"><?php /* This little hack forces full content */ $x = $more; $more = true; the_content('', 0, ''); $more = $x; ?></div></content>
<?php
		}
?>
	</entry>
<?php
		$items_count++;
		if (($items_count == get_settings('posts_per_rss')) && empty($m))
		{
			break;
		}
	}
}
?>
</feed>

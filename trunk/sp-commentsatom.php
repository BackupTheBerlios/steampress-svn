<?php 
if ( empty($feed) ) {
	$withcomments = 1;
	require('sp-blog-header.php');
}

header('Content-type: text/xml;charset=' . get_settings('blog_charset'), true);

echo '<?xml version="1.0" encoding="'.get_settings('blog_charset').'"?'.'>'; 
?>
<!-- generator="steampress/<?php echo $sp_version ?>" -->
<feed version="0.3"
  xmlns="http://purl.org/atom/ns#"
  xmlns:dc="http://purl.org/dc/elements/1.1/">
  
<?php if ( $posts )
{ 
	$i = 0;
	foreach ($posts as $post) { start_wp();
		if ($i < 1) {
			$i++;
?>

	<title><?php if (is_single()) { echo "Comments on: "; the_title_rss(); } else { bloginfo_rss("name"); echo " Comments"; } ?></title>
	<link rel="alternate" type="text/html" href="<?php bloginfo_rss('url') ?>" />
	<tagline><?php bloginfo_rss("description") ?></tagline>
	<modified><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_lastpostmodified('GMT')); ?></modified>
	<copyright>Copyright <?php echo mysql2date('Y', get_lastpostdate('blog')); ?></copyright>
	<generator url="http://steampress.berlios.de/" version="<?php echo $sp_version ?>">SteamPress</generator>

<?php 
		if (is_single()) {
				$comments = $spdb->get_results("SELECT comment_ID, comment_author, comment_author_email, 
				comment_author_url, comment_date, comment_content, comment_post_ID, 
				$spdb->posts.ID, $spdb->posts.post_password FROM $spdb->comments 
				LEFT JOIN $spdb->posts ON comment_post_id = id WHERE comment_post_ID = '$id' 
				AND $spdb->comments.comment_approved = '1' AND $spdb->posts.post_status = 'publish' 
				AND post_date < '".date("Y-m-d H:i:59")."' 
				ORDER BY comment_date LIMIT " . get_settings('posts_per_rss') );
			} else { // if no post id passed in, we'll just ue the last 10 comments.
				$comments = $spdb->get_results("SELECT comment_ID, comment_author, comment_author_email, 
				comment_author_url, comment_date, comment_content, comment_post_ID, 
				$spdb->posts.ID, $spdb->posts.post_password FROM $spdb->comments 
				LEFT JOIN $spdb->posts ON comment_post_id = id WHERE $spdb->posts.post_status = 'publish' 
				AND $spdb->comments.comment_approved = '1' AND post_date < '".date("Y-m-d H:i:s")."'  
				ORDER BY comment_date DESC LIMIT " . get_settings('posts_per_rss') );
			}
		// this line is SteamPress' motor, do not delete it.
			if ($comments) {
				foreach ($comments as $comment) {
?>

	<?php $items_count = 0; if ($posts) { foreach ($posts as $post) { start_wp(); ?>
	<entry>
	  	<author>
			<name><?php the_author() ?></name>
		</author>
		<title><?php the_title_rss() ?></title>
		<link rel="alternate" type="text/html" href="<?php permalink_single_rss() ?>" />
		<id><?php bloginfo_rss("url") ?>?p=<?php echo $id; ?></id>
		<modified><?php echo mysql2date('Y-m-d\TH:i:s\Z', $post->post_modified_gmt); ?></modified>
		<issued><?php echo mysql2date('Y-m-d\TH:i:s\Z', $post->post_date_gmt); ?></issued>
		<?php the_category_rss('rdf') ?>
		<summary type="text/html" mode="escaped"><?php the_excerpt_rss(get_settings('rss_excerpt_length'), 2) ?></summary>
<?php if (!get_settings('rss_use_excerpt')) { ?>
		<content type="text/html" mode="escaped" xml:base="<?php permalink_single_rss() ?>"><![CDATA[<?php the_content('', 0, '') ?>]]></content>
<?php }
						}
					}
				}
			}
		}
	}
}
?>

	</entry>
</feed>
<?php
// If a sidebar.php file exists in the WP root directory we
// use that, otherwise use this default sp-sidebar.php file.
if ( file_exists(TEMPLATEPATH . '/sidebar.php') ) :
	include_once(TEMPLATEPATH . '/sidebar.php');
else :
?>

<div id="menu">

<dl>
	<?php get_links_list(); ?>
 <dt id="categories"><?php _e('Categories:'); ?> </dt>
 <dd>
	<ul>
	<?php sp_list_cats(); ?>
	</ul>
 </dd>
 <dt id="search"><label for="s"><?php _e('Search:'); ?></label></dt>
 <dd>
   <form id="searchform" method="get" action="<?php echo $PHP_SELF; ?>">
	<div>
		<input type="text" name="s" id="s" size="15" /><br />
		<input type="submit" name="submit" value="<?php _e('Search'); ?>" />
	</div>
	</form>
</dd>
 
 <dt id="archives"><?php _e('Archives:'); ?></dt>
	 <?php sp_get_archives('type=monthly'); ?>

<dt id="meta"><?php _e('Meta:'); ?></dt>
		<dd><?php sp_register(); ?></dd>
		<dd><?php sp_loginout(); ?></dd>
		<dd><a href="<?php bloginfo('atom_url'); ?>" title="<?php _e('Syndicate this site using Atom'); ?>"><?php _e('Atom Feed'); ?></a></dd>
		<dd><a href="<?php bloginfo('comments_atom_url'); ?>" title="<?php _e('The latest comments to all posts in Atom'); ?>"><?php _e('Comments Atom Feed'); ?></a></dd>
		<dd><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Syndicate this site using RSS'); ?>"><?php _e('<abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></dd>
		<dd><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('The latest comments to all posts in RSS'); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></dd>
		<dd><a href="http://validator.w3.org/check/referer" title="<?php _e('This page validates as XHTML 1.0 Transitional'); ?>"><?php _e('Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr>'); ?></a></dd>
		<dd><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></dd>
		<?php sp_meta(); ?>
</dl>
<?php get_calendar(); ?>
</div>

<?php endif; ?>
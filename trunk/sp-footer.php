<?php
// If a footer.php file exists in the SP root directory we
// use that, otherwise use this default sp-footer.php file.
if ( file_exists(TEMPLATEPATH . '/footer.php') ) :
	include_once(TEMPLATEPATH . '/footer.php');
else :
?>

<p class="credit"><!--<?php echo $spdb->num_queries; ?> queries. <?php timer_stop(1); ?> seconds. --> <?php echo sprintf(__("Powered by <a href='http://steampress.berlios.de' title='%s'>SteamPress</a>"), __("Powered by SteamPress, state-of-the-art semantic personal publishing platform.")); ?> <?php bloginfo('version'); ?></p>

<?php do_action('sp_footer', ''); ?>
</body>
</html>
<?php endif; ?>
<?php
// If a footer.php file exists in the WP root directory we
// use that, otherwise use this default steampress-footer.php file.
if ( file_exists(ABSPATH . '/footer.php') ) :
	include_once(ABSPATH . '/footer.php');
else :
?>
</div>



<?php
// This code pulls in the sidebar:
include(ABSPATH . '/steampress-sidebar.php');
?>

</div>

<p class="credit"><!--<?php echo $steampressdb->num_queries; ?> queries. <?php timer_stop(1); ?> seconds. --> <cite><?php echo sprintf(__("Powered by <a href='http://wordpress.org' title='%s'><strong>SteamPress</strong></a>"), __("Powered by SteamPress, state-of-the-art semantic personal publishing platform.")); ?></cite></p>

<?php do_action('steampress_footer', ''); ?>
</body>
</html>
<?php endif; ?>
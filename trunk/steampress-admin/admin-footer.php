<div id="footer">
	<p>
		<a href="http://steamedpenguin.com/steampress/">SteamPress</a> <?php bloginfo('version'); ?><br />
<?php printf(__('%s seconds'), number_format(timer_stop(), 2)); ?>
	</p>
</div>

<?php do_action('admin_footer', ''); ?>

	</body>
</html>
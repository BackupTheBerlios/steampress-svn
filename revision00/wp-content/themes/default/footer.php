
<hr />
<div id="footer">
	<p>
		<?php bloginfo('name'); ?> is proudly powered by
		<a href="http://steamedpenguin.com/projects/steampress/">SteamPress</a>
		<br /><a href="feed:<?php bloginfo('rss2_url'); ?>">Entries (RSS)</a>
		and <a href="feed:<?php bloginfo('comments_rss2_url'); ?>">Comments (RSS)</a>.
		<!-- <?php echo $wpdb->num_queries; ?> queries. <?php timer_stop(1); ?> seconds. -->
	</p>
</div>
</div>

<?php /* "Just what do you think you're doing Dave?" */ ?>

		<?php wp_footer(); ?>

</body>
</html>
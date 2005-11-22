<?php

/*************************************************

SteamPress - Blogging without the Dirt
Author: SteamPress Development Team (developers@steampress.org)
Copyright (c): 2005 ispi, all rights reserved

    This file is part of SteamPress.

    SteamPress is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    SteamPress is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with SteamPress; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

You may contact the authors of Snoopy by e-mail at:
developers@steampress.org

Or, write to:

SteamPress Development Team
c/o Samir M. Nassar
2015 Central Ave. NE, #226
Minneapolis, MN 55418
USA

The latest version of SteamPress can be obtained from:
http://steampress.org/

*************************************************/
?>


<div class="wrap">
<h2><?php _e('Write Post'); ?></h2>
<form name="post" action="post.php" method="post" id="simple">

<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="action" value='post' />

<script type="text/javascript">
<!--
function focusit() {
	// focus on first input field
	document.getElementById('title').focus();
}
window.onload = focusit;
//-->
</script>

<div id="poststuff">
	<fieldset id="titlediv">
	<legend><a href="http://wordpress.org/docs/reference/post/#title" title="<?php _e('Help on titles') ?>"><?php _e('Title') ?></a></legend>
	<div><input type="text" name="post_title" size="30" tabindex="1" value="<?php echo $edited_post_title; ?>" id="title" /></div>
	</fieldset>

	<fieldset id="categorydiv">
	<legend><a href="http://wordpress.org/docs/reference/post/#category" title="<?php _e('Help on categories') ?>"><?php _e('Categories') ?></a></legend>
	<div><?php dropdown_categories($default_post_cat); ?></div>
	</fieldset>

<br />
<fieldset id="postdiv">
	<legend><a href="http://wordpress.org/docs/reference/post/#post" title="<?php _e('Help with post field') ?>"><?php _e('Post') ?></a></legend>

<?php
$rows = get_settings('default_post_edit_rows');
if (($rows < 3) || ($rows > 100)) {
	$rows = 10;
}
?>
<div><textarea name="content" tabindex="4" id="content"><?php echo $content ?></textarea></div>
</fieldset>


<script type="text/javascript">
<!--
edCanvas = document.getElementById('content');
//-->
</script>

<input type="hidden" name="post_pingback" value="<?php echo get_option('default_pingback_flag') ?>" id="post_pingback" />

<p><label for="trackback"> <?php printf(__('<a href="%s" title="Help on trackbacks"><strong>TrackBack</strong> a <abbr title="Universal Resource Identifier">URI</abbr></a>:</label> (Separate multiple <abbr title="Universal Resource Identifier">URI</abbr>s with spaces.)<br />'), 'http://wordpress.org/docs/reference/post/#trackback') ?>
	<input type="text" name="trackback_url" style="width: 360px" id="trackback" tabindex="7" /></p>

<p class="submit"><input name="saveasdraft" type="submit" id="saveasdraft" tabindex="9" value="<?php _e('Save as Draft') ?>" />
<input name="saveasprivate" type="submit" id="saveasprivate" tabindex="10" value="<?php _e('Save as Private') ?>" />

	<?php if ( user_can_create_post($user_ID) ) : ?>
<input name="publish" type="submit" id="publish" tabindex="6" style="font-weight: bold;" value="<?php _e('Publish') ?>" />
<?php endif;   if ('bookmarklet' != $mode) {
	echo '<input name="advanced" type="submit" id="advancededit" tabindex="7" value="' .  __('Advanced Editing &raquo;') . '" />';
} ?>
<input name="referredby" type="hidden" id="referredby" value="<?php if (isset($_SERVER['HTTP_REFERER'])) echo urlencode($_SERVER['HTTP_REFERER']); ?>" />
</p>

<?php do_action('simple_edit_form', ''); ?>

</div>
</form>

</div>

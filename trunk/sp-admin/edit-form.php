
<div class="wrap">

<form name="post" action="post.php" method="post" id="simple">

<?php if (isset($mode) && 'bookmarklet' == $mode) : ?>
<input type="hidden" name="mode" value="bookmarklet" />
<?php endif; ?>
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
      <legend><?php _e('Title') ?></legend> 
	  <div><input type="text" name="post_title" size="72" tabindex="1" value="<?php echo $edited_post_title; ?>" id="title" /></div>
    </fieldset>

    <fieldset id="categorydiv">
      <legend><?php _e('Categories') ?></legend> 
	  <div><?php dropdown_categories($default_post_cat); ?></div>
    </fieldset>

<br />
<fieldset id="postdiv">
    <legend><?php _e('Post') ?></legend>
<?php
 $rows = get_settings('default_post_edit_rows');
 if (($rows < 3) || ($rows > 100)) {
     $rows = 10;
 }
?>
<div><textarea rows="<?php echo $rows; ?>" cols="40" name="content" tabindex="4" id="content"><?php echo $content ?></textarea></div>
</fieldset>

<input type="hidden" name="post_pingback" value="1" id="post_pingback" />

<p class="trackback"><label for="trackback"><strong>TrackBack</strong> a <abbr title="Universal Resource Identifier">URI</abbr>:</label> (Separate multiple <abbr title="Universal Resource Identifier">URI</abbr>s with spaces.)<br /><input type="text" name="trackback_url"  tabindex="7" /></p>

<p class="submit"><input name="saveasdraft" type="submit" id="saveasdraft" tabindex="9" value="<?php _e('Save as Draft') ?>" /> 
  <input name="saveasprivate" type="submit" id="saveasprivate" tabindex="10" value="<?php _e('Save as Private') ?>" />

<?php if ( 1 < $user_level || (1 == $user_level && 2 == get_option('new_users_can_blog')) ) : ?>
  <input name="publish" type="submit" id="publish" tabindex="6" value="<?php _e('Publish') ?>" /> 
<?php endif; ?>

<?php if ('bookmarklet' != $mode) {
      echo '<input name="advanced" type="submit" id="advancededit" tabindex="7" value="' .  __('Advanced Editing &raquo;') . '" />';
  } ?>
  <input name="referredby" type="hidden" id="referredby" value="<?php if (isset($_SERVER['HTTP_REFERER'])) echo urlencode($_SERVER['HTTP_REFERER']); ?>" />
</p>

<?php do_action('simple_edit_form', ''); ?>

</div>
</form>

</div>
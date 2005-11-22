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
 
$submitbutton_text = __('Edit Comment &raquo;');
$toprow_title = sprintf(__('Editing Comment # %s'), $commentdata['comment_ID']);
$form_action = 'editedcomment';
$form_extra = "' />\n<input type='hidden' name='comment_ID' value='$comment' />\n<input type='hidden' name='comment_post_ID' value='".$commentdata["comment_post_ID"];
?>

<form name="post" action="post.php" method="post" id="post">
<div class="wrap">
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action . $form_extra ?>' />

<script type="text/javascript">
function focusit() {
	// focus on first input field
	document.post.name.focus();
}
window.onload = focusit;
</script>
<fieldset id="namediv">
	<legend><?php _e('Name:') ?></legend>
	<div>
	<input type="text" name="newcomment_author" size="22" value="<?php echo format_to_edit($commentdata['comment_author']) ?>" tabindex="1" id="name" />
	</div>
</fieldset>
<fieldset id="emaildiv">
		<legend><?php _e('E-mail:') ?></legend>
		<div>
		<input type="text" name="newcomment_author_email" size="30" value="<?php echo format_to_edit($commentdata['comment_author_email']) ?>" tabindex="2" id="email" />
	</div>
</fieldset>
<fieldset id="uridiv">
		<legend><?php _e('URI:') ?></legend>
		<div>
		<input type="text" name="newcomment_author_url" size="35" value="<?php echo format_to_edit($commentdata['comment_author_url']) ?>" tabindex="3" id="URL" />
	</div>
</fieldset>

<fieldset style="clear: both;">
		<legend><?php _e('Comment') ?></legend>

<?php
$rows = get_settings('default_post_edit_rows');
if (($rows < 3) || ($rows > 100)) {
	$rows = 10;
}
?>
<div><textarea name="content" tabindex="4" id="content" style="width: 99%"><?php echo $content ?></textarea></div>
</fieldset>

<script type="text/javascript">
<!--
edCanvas = document.getElementById('content');
//-->
</script>

<p class="submit"><input type="submit" name="editcomment" id="editcomment" value="<?php echo $submitbutton_text ?>" style="font-weight: bold;" tabindex="6" />
<input name="referredby" type="hidden" id="referredby" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
</p>

</div>

<div class="wrap">
<h2><?php _e('Advanced'); ?></h2>

<table width="100%" cellspacing="2" cellpadding="5" class="editform">
	<tr>
		<th scope="row" valign="top"><?php _e('Comment Status') ?>:</th>
		<td><label for="comment_status_approved" class="selectit"><input id="comment_status_approved" name="comment_status" type="radio" value="1" <?php checked($comment_status, '1'); ?> /> <?php _e('Approved') ?></label><br />
	<label for="comment_status_moderated" class="selectit"><input id="comment_status_moderated" name="comment_status" type="radio" value="0" <?php checked($comment_status, '0'); ?> /> <?php _e('Moderated') ?></label><br />
	<label for="comment_status_spam" class="selectit"><input id="comment_status_spam" name="comment_status" type="radio" value="spam" <?php checked($comment_status, 'spam'); ?> /> <?php _e('Spam') ?></label></td>
	</tr>

<?php if ($user_level > 4) : ?>
	<tr>
		<th scope="row"><?php _e('Edit time'); ?>:</th>
		<td><?php touch_time(('editcomment' == $action), 0); ?></td>
	</tr>
<?php endif; ?>

	<tr>
		<th scope="row"><?php _e('Delete'); ?>:</th>
		<td><p><a class="delete" href="post.php?action=confirmdeletecomment&amp;noredir=true&amp;comment=<?php echo $commentdata['comment_ID']; ?>&amp;p=<?php echo $commentdata['comment_post_ID']; ?>"><?php _e('Delete comment') ?></a></p></td>
	</tr>
</table>

</div>

</form>

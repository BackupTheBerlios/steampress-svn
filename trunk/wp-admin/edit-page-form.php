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
<h2><?php _e('Write Page'); ?></h2>
<?php
if (0 == $post_ID) {
	$form_action = 'post';
	$form_extra = '';
} else {
	$form_action = 'editpost';
	$form_extra = "<input type='hidden' name='post_ID' value='$post_ID' />";
}

$sendto = $_SERVER['HTTP_REFERER'];

if ( 0 != $post_ID && $sendto == get_permalink($post_ID) )
	$sendto = 'redo';
$sendto = wp_specialchars( $sendto );

?>

<form name="post" action="post.php" method="post" id="post">

<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action ?>' />
<?php echo $form_extra ?>
<input type="hidden" name="post_status" value="static" />

<script type="text/javascript">
<!--
function focusit() {
	// focus on first input field
	document.post.title.focus();
}
window.onload = focusit;
//-->
</script>
	<fieldset id="titlediv">
	<legend><?php _e('Page Title') ?></legend>
	<div><input type="text" name="post_title" size="30" tabindex="1" value="<?php echo $edited_post_title; ?>" id="title" /></div>
	</fieldset>
<fieldset id="commentstatusdiv">
	<legend><?php _e('Discussion') ?></legend>
	<div>
	<input name="advanced_view" type="hidden" value="1" />
	<label for="comment_status" class="selectit">
		<input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked($comment_status, 'open'); ?> />
		<?php _e('Allow Comments') ?></label>
		<label for="ping_status" class="selectit"><input name="ping_status" type="checkbox" id="ping_status" value="open" <?php checked($ping_status, 'open'); ?> /> <?php _e('Allow Pings') ?></label>
	</div>
</fieldset>
	<fieldset id="postpassworddiv">
	<legend><?php _e('Page Password') ?></legend>
	<div><input name="post_password" type="text" size="13" id="post_password" value="<?php echo $post_password ?>" /></div>
	</fieldset>
	<fieldset id="pageparent">
	<legend><?php _e('Page Parent') ?></legend>
	<div><select name="parent_id">
	<option value='0'><?php _e('Main Page (no parent)'); ?></option>
			<?php parent_dropdown($post_parent); ?>
		</select>
	</div>
	</fieldset>
<fieldset id="postdiv">
	<legend><?php _e('Page Content') ?></legend>

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

<p class="submit">
<?php if ( $post_ID ) : ?>
<input name="save" type="submit" id="save" tabindex="5" value=" <?php _e('Save and Continue Editing'); ?> "/>
<input name="savepage" type="submit" id="savepage" tabindex="6" value="<?php $post_ID ? _e('Edit Page') : _e('Create New Page') ?> &raquo;" />
<?php else : ?>
<input name="savepage" type="submit" id="savepage" tabindex="6" value="<?php _e('Create New Page') ?> &raquo;" />
<?php endif; ?>
<input name="referredby" type="hidden" id="referredby" value="<?php echo $sendto; ?>" />
</p>

<fieldset id="pageoptions">
	<legend><?php _e('Page Options') ?></legend>
<table width="100%" cellspacing="2" cellpadding="5" class="editform">
<?php if ( 0 != count( get_page_templates() ) ) { ?>
	<tr valign="top">
		<th scope="row" width="30%"><?php _e('Page Template:') ?></th>
		<td><div><select name="page_template">
		<option value='default'><?php _e('Default Template'); ?></option>
		<?php page_template_dropdown($page_template); ?>
		</select>

		</div>
		</td>
	</tr>
<?php } ?>
	<tr valign="top">
		<th scope="row" width="30%"><?php _e('Page slug') ?>:</th>
		<td><input name="post_name" type="text" size="25" id="post_name" value="<?php echo $post_name ?>" /></td>
	</tr>
<?php if ($user_level > 7 && $users = $wpdb->get_results("SELECT ID, user_login, user_firstname, user_lastname FROM $wpdb->users WHERE user_level <= $user_level AND user_level > 0") ) : ?>
	<tr>
		<th scope="row" width="30%"><?php _e('Page owner'); ?>:</th>
		<td>
		<select name="post_author" id="post_author">
		<?php
		foreach ($users as $o) :
			if ( $post_author == $o->ID ) $selected = 'selected="selected"';
			else $selected = '';
			echo "<option value='$o->ID' $selected>$o->user_login ($o->user_firstname $o->user_lastname)</option>";
		endforeach;
		?>
		</select>
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th scope="row" width="25%"><?php _e('Page Order') ?>:</th>
		<td><input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo $menu_order ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('Delete'); ?>:</th>
		<td><?php if ('edit' == $action) : ?>
		<input name="deletepost" class="delete" type="submit" id="deletepost" tabindex="10" value="<?php _e('Delete this page') ?>" <?php echo "onclick=\"return confirm('" . sprintf(__("You are about to delete this page \'%s\'\\n  \'Cancel\' to stop, \'OK\' to delete."), addslashes($edited_post_title) ) . "')\""; ?> />
<?php endif; ?></td>
	</tr>
</table>
</fieldset>

<fieldset id="postcustom">
<legend><?php _e('Custom Fields') ?> <script type="text/javascript">customToggleLink();</script></legend>
<div id="postcustomstuff">
<?php
if($metadata = has_meta($post_ID)) {
?>
<?php
	list_meta($metadata);
?>
<?php
}
	meta_form();
?>
</div>
</fieldset>

<?php do_action('edit_page_form', ''); ?>
</form>

</div>

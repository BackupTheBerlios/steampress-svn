<?php
require_once('admin.php');

$title = __('Miscellaneous Options');
$parent_file = 'options-general.php';

include('admin-header.php');

?>
 
<div class="wrap"> 
<h2><?php _e('Miscellaneous Options') ?></h2> 
<form name="miscoptions" method="post" action="options.php"> 
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="'use_geo_positions','use_linksupdate','weblogs_xml_url','links_updated_date_format','links_recently_updated_prepend','links_recently_updated_append'" /> 
	<fieldset class="options">
	<legend>
	<input name="use_linksupdate" type="checkbox" id="use_linksupdate" value="1" <?php checked('1', get_settings('use_linksupdate')); ?> />
	<label for="use_linksupdate"><?php _e('Track Link&#8217;s Update Times') ?></label></legend>
	<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
	<tr> 
	<th width="33%" valign="top" scope="row"><?php _e('Update file:') ?> </th> 
	<td>
	<input name="weblogs_xml_url" type="text" id="weblogs_xml_url" value="<?php form_option('weblogs_xml_url'); ?>" size="50" /><br />
	<?php __('Recommended: <code>http://static.steampress.org/changes.xml</code>') ?>
	
	</td> 
	</tr> 
	<tr>
	<th valign="top" scope="row"><?php _e('Updated link time format:') ?> </th>
	<td>          
	<input name="links_updated_date_format" type="text" id="links_updated_date_format" value="<?php form_option('links_updated_date_format'); ?>" size="50" />
	</td>
	</tr>
	<tr>
	<th scope="row"><?php _e('Prepend updated with:') ?> </th>
	<td><input name="links_recently_updated_prepend" type="text" id="links_recently_updated_prepend" value="<?php form_option('links_recently_updated_prepend'); ?>" size="50" /></td>
	</tr>
	<tr>
	<th valign="top" scope="row"><?php _e('Append updated with:') ?></th>
	<td><input name="links_recently_updated_append" type="text" id="links_recently_updated_append" value="<?php form_option('links_recently_updated_append'); ?>" size="50" /></td>
	</tr>
	</table>
	<p><?php printf(__('A link is "recent" if it has been updated in the past %s minutes.'), '<input name="links_recently_updated_time" type="text" id="links_recently_updated_time" size="3" value="' . get_settings('links_recently_updated_time'). '" />' ) ?></p>
	</fieldset>
	<p class="submit">
		<input type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" />
	</p>
</form> 
</div>

<?php include('./admin-footer.php'); ?>
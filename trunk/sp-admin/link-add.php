<?php
require_once('admin.php');

$title = __('Add Link');
$this_file = 'link-manager.php';
$parent_file = 'link-manager.php';

function category_dropdown($fieldname, $selected = 0) {
	global $spdb;
	
	$results = $spdb->get_results("SELECT cat_id, cat_name, auto_toggle FROM $spdb->linkcategories ORDER BY cat_id");
	echo "\n<select name='$fieldname' size='1'>\n";
	foreach ($results as $row) {
		echo "\n\t<option value='$row->cat_id'";
		if ($row->cat_id == $selected)
			echo " selected='selected'";
		echo ">$row->cat_id : " . sp_specialchars($row->cat_name);
		if ($row->auto_toggle == 'Y')
			echo ' (auto toggle)';
		echo "</option>";
	}
	echo "\n</select>\n";
}

$spvarstoreset = array('action', 'cat_id', 'linkurl', 'name', 'image',
                       'description', 'visible', 'category', 'link_id',
                       'submit', 'order_by', 'links_show_cat_id', 'rating', 'rel',
                       'notes', 'linkcheck[]');
for ($i=0; $i<count($spvarstoreset); $i += 1) {
    $spvar = $spvarstoreset[$i];
    if (!isset($$spvar)) {
        if (empty($_POST["$spvar"])) {
            if (empty($_GET["$spvar"])) {
                $$spvar = '';
            } else {
                $$spvar = $_GET["$spvar"];
            }
        } else {
            $$spvar = $_POST["$spvar"];
        }
    }
}
$link_url = stripslashes($_GET['linkurl']);
$link_name = htmlentities(stripslashes(urldecode($_GET['name'])));


$xfn = true;
require('admin-header.php');
?>

<?php if ($_GET['added']) : ?>
<div class="updated"><p><?php _e('Link added.'); ?></p></div>
<?php endif; ?>
<div class="wrap">
<h2><?php _e('<strong>Add</strong> a link:') ?></h2>
     <form name="addlink" method="post" action="link-manager.php">
<fieldset class="options">
	<legend><?php _e('Basics') ?></legend>
        <table class="editform" width="100%" cellspacing="2" cellpadding="5">
         <tr>
           <th width="33%" scope="row"><?php _e('URI:') ?></th>
           <td width="67%"><input type="text" name="linkurl" value="<?php echo sp_specialchars($_GET['linkurl'], 1); ?>" style="width: 95%;" /></td>
         </tr>
         <tr>
           <th scope="row"><?php _e('Link Name:') ?></th>
           <td><input type="text" name="name" value="<?php echo sp_specialchars( urldecode($_GET['name']), 1 ); ?>" style="width: 95%" /></td>
         </tr>
         <tr>
         	<th scope="row"><?php _e('Short description:') ?></th>
         	<td><input type="text" name="description" value="" style="width: 95%" /></td>
         	</tr>
        <tr>
           <th scope="row"><?php _e('Category:') ?></th>
           <td><?php category_dropdown('category'); ?></td>
         </tr>
</table>
</fieldset>
       <p class="submit">
         <input type="submit" name="submit" value="<?php _e('Add Link &raquo;') ?>" /> 
       </p>
	<fieldset class="options">
	<legend><?php _e('Link Relationship (XFN)') ?></legend>
        <table class="editform" width="100%" cellspacing="2" cellpadding="5">
            <tr>
            	<th width="33%" scope="row"><?php _e('rel:') ?></th>
            	<td width="67%"><input type="text" name="rel" id="rel" size="50" value="" /></td>
           	</tr>
            <tr>
            	<th scope="row"><?php _e('<a href="http://gmpg.org/xfn/">XFN</a> Creator:') ?></th>
            	<td><table cellpadding="3" cellspacing="5">
            			<tr>
            				<th scope="row"> <?php _e('friendship') ?> </th>
            				<td>
            				<label for="label1">
							<input class="valinp" type="radio" name="friendship" value="acquaintance" id="label1" />
							<?php _e('acquaintance') ?></label>
							<label for="label2">
							<input class="valinp" type="radio" name="friendship" value="contact" id="label2" />
							<?php _e('contact') ?></label>
							<label for="label3">
							<input class="valinp" type="radio" name="friendship" value="friend" id="label3" />
							<?php _e('friend') ?></label>
							<label for="label4">
                					<input class="valinp" type="radio" name="friendship" value="" id="label4" />
					<?php _e('none') ?></label>
            					</td>
           				</tr>
            			<tr>
            				<th scope="row"> <?php _e('physical') ?> </th>
            				<td><label for="label10">
            					<input class="valinp" type="checkbox" name="physical" value="met" id="label10" />
					<?php _e('met') ?></label>
            					</td>
           				</tr>
            			<tr>
            				<th scope="row"> <?php _e('professional') ?> </th>
            				<td><label for="label20">
            					<input class="valinp" type="checkbox" name="professional" value="co-worker" id="label20" />
					<?php _e('co-worker') ?></label>
                					<label for="label21">
                					<input class="valinp" type="checkbox" name="professional" value="colleague" id="label21" />
					<?php _e('colleague') ?></label>
            					</td>
           				</tr>
            			<tr>
            				<th scope="row"> <?php _e('geographical') ?> </th>
            				<td><label for="label30">
            					<input class="valinp" type="radio" name="geographical" value="co-resident" id="label30" />
					<?php _e('co-resident') ?></label>
                					<label for="label31">
                					<input class="valinp" type="radio" name="geographical" value="neighbor" id="label31" />
					<?php _e('neighbor') ?></label>
                					<label for="label32">
                					<input class="valinp" type="radio" name="geographical" value="" id="label32" />
					<?php _e('none') ?></label>
            					</td>
           				</tr>
            			<tr>
            				<th scope="row"> <?php _e('family') ?> </th>
            				<td>
            						<label for="label40">
            					<input class="valinp" type="radio" name="family" value="child" id="label40" />
					<?php _e('child') ?></label>
            						<label for="label41">
            					<input class="valinp" type="radio" name="family" value="kin" id="label41" />
					<?php _e('kin') ?></label>
                					<label for="label42">
                					<input class="valinp" type="radio" name="family" value="parent" id="label42" />
					<?php _e('parent') ?></label>
                					<label for="label43">
                					<input class="valinp" type="radio" name="family" value="sibling" id="label43" />
					<?php _e('sibling') ?></label>
                					<label for="label44">
                					<input class="valinp" type="radio" name="family" value="spouse" id="label44" />
					<?php _e('spouse') ?></label>
                					<label for="label45">
                					<input class="valinp" type="radio" name="family" value="" id="label45" />
					<?php _e('none') ?></label>
            					</td>
           				</tr>
            			<tr>
            				<th scope="row"> <?php _e('romantic') ?> </th>
            				<td><label for="label50">
            					<input class="valinp" type="checkbox" name="romantic" value="muse" id="label50" />
					<?php _e('muse') ?></label>
                					<label for="label51">
                					<input class="valinp" type="checkbox" name="romantic" value="crush" id="label51" />
					<?php _e('crush') ?></label>
                					<label for="label52">
                					<input class="valinp" type="checkbox" name="romantic" value="date" id="label42" />
					<?php _e('date') ?></label>
                					<label for="label53">
                					<input class="valinp" type="checkbox" name="romantic" value="sweetheart" id="label53" />
					<?php _e('sweetheart') ?></label>
            					</td>
           				</tr>
            			<tr>
            				<th scope="row"> <?php _e('identity') ?> </th>
            				<td><label for="label60">
            					<input class="valinp" type="checkbox" name="identity" value="me" id="label60" />
					<?php _e('me') ?></label>
            					</td>
           				</tr>
            			</table></td>
           	</tr>
</table>
</fieldset>
       <p class="submit">
         <input type="submit" name="submit" value="<?php _e('Add Link &raquo;') ?>" /> 
       </p>
<fieldset class="options">
	<legend><?php _e('Advanced') ?></legend>
        <table class="editform" width="100%" cellspacing="2" cellpadding="5">
         <tr>
           <th width="33%" scope="row"><?php _e('Image URI:') ?></th>
           <td width="67%"><input type="text" name="image" size="50" value="" style="width: 95%" /></td>
         </tr>
<tr>
           <th scope="row"><?php _e('RSS URI:') ?> </th>
           <td><input name="rss_uri" type="text" id="rss_uri" value="" size="50" style="width: 95%" /></td>
         </tr>
         <tr>
           <th scope="row"><?php _e('Notes:') ?></th>
           <td><textarea name="notes" cols="50" rows="10" style="width: 95%"></textarea></td>
         </tr>
         <tr>
           <th scope="row"><?php _e('Rating:') ?></th>
           <td><select name="rating" size="1">
             <?php
    for ($r = 0; $r < 10; $r++) {
      echo('            <option value="'.$r.'">'.$r.'</option>');
    }
?>
           </select>
           &nbsp;<?php _e('(Leave at 0 for no rating.)') ?> </td>
         </tr>
         <tr>
           <th scope="row"><?php _e('Visible:') ?></th>
           <td><label>
             <input type="radio" name="visible" checked="checked" value="Y" />
<?php _e('Yes') ?></label><br />
<label><input type="radio" name="visible" value="N" /> <input type="hidden" name="action" value="Add" /> 
<?php _e('No') ?></label></td>
         </tr>
</table>
</fieldset>

       <p class="submit">
         <input type="submit" name="submit" value="<?php _e('Add Link &raquo;') ?>" /> 
       </p>
  </form>
</div>

<?php
require('admin-footer.php');
?>
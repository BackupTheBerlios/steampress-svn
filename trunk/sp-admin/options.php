<?php
require_once('admin.php');

$title = __('Options');
$this_file = 'options.php';
$parent_file = 'options-general.php';

$spvarstoreset = array('action');
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

if ($user_level < 6)
	die ( __('Cheatin&#8217; uh?') );

switch($action) {

case 'update':
    $any_changed = 0;
    
	if (!$_POST['page_options']) {
		foreach ($_POST as $key => $value) {
			$option_names[] = "'$key'";
		}
		$option_names = implode(',', $option_names);
	} else {
		$option_names = stripslashes($_POST['page_options']);
	}

    $options = $spdb->get_results("SELECT $spdb->options.option_id, option_name, option_type, option_value, option_admin_level FROM $spdb->options WHERE option_name IN ($option_names)");

// HACK
// Options that if not there have 0 value but need to be something like "closed"
    $nonbools = array('default_ping_status', 'default_comment_status');
    if ($options) {
        foreach ($options as $option) {
            // should we even bother checking?
            if ($user_level >= $option->option_admin_level) {
                $old_val = $option->option_value;
                $new_val = sp_specialchars($_POST[$option->option_name]);
                if (!$new_val) {
                    if (3 == $option->option_type)
                        $new_val = '';
                    else
                        $new_val = 0;
                }
                if( in_array($option->option_name, $nonbools) && $new_val == '0' ) $new_val = 'closed';
                if ($new_val !== $old_val)
                    $result = $spdb->query("UPDATE $spdb->options SET option_value = '$new_val' WHERE option_name = '$option->option_name'");
            }
        }
        unset($cache_settings); // so they will be re-read
        get_settings('siteurl'); // make it happen now
    } // end if options
    
    if ($any_changed) {
        $message = sprintf(__('%d setting(s) saved... '), $any_changed);
    }
    
		//$referred = str_replace('?updated=true' , '', $_SERVER['HTTP_REFERER']);
		$referred = remove_query_arg('updated' , $_SERVER['HTTP_REFERER']);
		//$goback = str_replace('?updated=true', '', $_SERVER['HTTP_REFERER']) . '?updated=true';
		$goback = add_query_arg('updated', 'true', $_SERVER['HTTP_REFERER']);
	$goback = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $goback);
    header('Location: ' . $goback);
    break;

default:
	include('admin-header.php'); ?>

<div class="wrap">
  <h2>All options</h2>
  <form name="form" action="options.php" method="post">
  <input type="hidden" name="action" value="update" />
  <table width="98%">
<?php
$options = $spdb->get_results("SELECT * FROM $spdb->options ORDER BY option_name");

foreach ($options as $option) :
	$value = sp_specialchars($option->option_value);
	echo "
<tr>
	<th scope='row'><label for='$option->option_name'>$option->option_name</label></th>
	<td><input type='text' name='$option->option_name' id='$option->option_name' size='30' value='$value' /></td>
	<td>$option->option_description</td>
</tr>";
endforeach;
?>
  </table>
<p class="submit"><input type="submit" name="Update" value="<?php _e('Update Settings &raquo;') ?>" /></p>
  </form>
</div>


<?php
break;
} // end switch

include('admin-footer.php');
?>

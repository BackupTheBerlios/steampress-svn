<?php

$spvarstoreset = array('action','standalone', 'option_group_id');
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
?>

<br clear="all" />

<?php if (isset($_GET['updated'])) : ?>
<div class="updated"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php endif; ?>
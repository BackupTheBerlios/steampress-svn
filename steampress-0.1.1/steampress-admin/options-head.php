<?php

$steampressvarstoreset = array('action','standalone', 'option_group_id');
for ($i=0; $i<count($steampressvarstoreset); $i += 1)
{
	$steampressvar = $steampressvarstoreset[$i];
	if (!isset($$steampressvar))
	{
		if (empty($_POST["$steampressvar"]))
		{
			if (empty($_GET["$steampressvar"]))
			{
				$$steampressvar = '';
			}
			else
			{
				$$steampressvar = $_GET["$steampressvar"];
			}
		}
		else
		{
			$$steampressvar = $_POST["$steampressvar"];
		}
	}
}
?>

<br clear="all" />

<?php
if (isset($_GET['updated']))
{
?>
<div class="updated"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php
}
?>
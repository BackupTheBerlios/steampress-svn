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
 
define('WP_INSTALLING', true);
if (!file_exists('../wp-config.php')) die("There doesn't seem to be a wp-config.php file. Double check that you updated wp-config-sample.php with the proper database connection information and renamed it to wp-config.php.");
require('../wp-config.php');
timer_start();
require_once(ABSPATH . '/wp-admin/upgrade-functions.php');

$step = $_GET['step'];
if (!$step) $step = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>WordPress &rsaquo; Upgrade</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style media="screen" type="text/css">
	<!--
	html {
		background: #eee;
	}
	body {
		background: #fff;
		color: #000;
		font-family: Georgia, "Times New Roman", Times, serif;
		margin-left: 20%;
		margin-right: 20%;
		padding: .2em 2em;
	}

	h1 {
		color: #006;
		font-size: 18px;
		font-weight: lighter;
	}

	h2 {
		font-size: 16px;
	}

	p, li, dt {
		line-height: 140%;
		padding-bottom: 2px;
	}

	ul, ol {
		padding: 5px 5px 5px 20px;
	}
	#logo {
		margin-bottom: 2em;
	}
.step a, .step input {
	font-size: 2em;
}
.step, th {
	text-align: right;
}
#footer {
text-align: center; border-top: 1px solid #ccc; padding-top: 1em; font-style: italic;
}
	-->
	</style>
</head>
<body>
<h1 id="logo"><img alt="WordPress" src="http://static.wordpress.org/logo.png" /></h1>
<?php
switch($step) {

	case 0:
?>
<p><?php _e('This file upgrades you from any previous version of WordPress to the latest. It may take a while though, so be patient.'); ?></p>
	<h2 class="step"><a href="upgrade.php?step=1"><?php _e('Upgrade WordPress &raquo;'); ?></a></h2>
<?php
	break;

	case 1:
	make_db_current_silent();
	upgrade_all();
?>
<h2><?php _e('Step 1'); ?></h2>
	<p><?php printf(__("There's actually only one step. So if you see this, you're done. <a href='%s'>Have fun</a>!"), '../'); ?></p>

<!--
<pre>
<?php printf(__('%s queries'), $wpdb->num_queries);   printf(__('%s seconds'), timer_stop(0)); ?>
</pre>
-->

<?php
	break;
}
?>
</body>
</html>

<?php
// ** MySQL settings ** //
define('DB_NAME', 'steampress');     // The name of the database
define('DB_USER', 'username');     // Your MySQL username
define('DB_PASSWORD', 'password'); // ...and password
define('DB_HOST', 'localhost');     // 99% chance you won't need to change this value

// Change the prefix if you want to have multiple blogs in a single database.
$table_prefix  = 'sp_';   // example: 'sp_' or 'b2' or 'mylogin_'

// Change this to localize SteamPress.  A corresponding MO file for the
// chosen language must be installed to sp-includes/languages.
// For example, install de.mo to sp-includes/languages and set SPLANG to 'de'
// to enable German language support.
define ('SPLANG', '');

/* Stop editing */

define('ABSPATH', dirname(__FILE__).'/');
require_once(ABSPATH.'sp-settings.php');
?>
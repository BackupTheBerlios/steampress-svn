<?php
// ** MySQL settings ** //
define('DB_NAME', 'yourdbname');     // The name of the database
define('DB_USER', 'yourusername');     // Your MySQL username
define('DB_PASSWORD', 'yourpassword'); // ...and password
define('DB_HOST', 'localhost');     // 99% chance you won't need to change this value

// Change the prefix if you want to have multiple blogs in a single database.
$table_prefix  = 'steampress_';   // example: 'steampress_' or 'b2' or 'mylogin_'

// Change this to localize SteamPress.  A corresponding MO file for the
// chosen language must be installed to steampress-includes/languages.
// For example, install de.mo to steampress-includes/languages and set WPLANG to 'de'
// to enable German language support.
define ('WPLANG', '');

/* Stop editing */

define('ABSPATH', dirname(__FILE__).'/');
require_once(ABSPATH.'steampress-settings.php');
?>

<?php
$_steampress_installing = 1;

if (file_exists('../steampress-config.php'))
{
	die("The file 'steampress-config.php' already exists. If you need to reset any of the configuration items in this file, please delete it first.");
}

if (!file_exists('../steampress-config-sample.php'))
{
	die('Sorry, I need a steampress-config-sample.php file to work from. Please re-upload this file from your SteamPress installation.');
$configFile = file('../steampress-config-sample.php');
}

if (!is_writable('../'))
{
	die("Sorry, I can't write to the directory. You'll have to either change the permissions on your SteamPress directory or create your steampress-config.php manually.");
}

$step = $_GET['step'];
if (!$step)
{
	$step = 0;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SteamPress &rsaquo; Setup Configuration File</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style media="screen" type="text/css">
	<!--
	body {
		font-family: Georgia, "Times New Roman", Times, serif;
		margin-left: 15%;
		margin-right: 15%;
	}
	#logo {
		margin: 0;
		padding: 0;
		background-image: url(http://wordpress.org/images/logo.png);
		background-repeat: no-repeat;
		height: 60px;
		border-bottom: 4px solid #333;
	}
	#logo a {
		display: block;
		height: 60px;
	}
	#logo a span {
		display: none;
	}
	p, li {
		line-height: 140%;
	}
	-->
	</style>
</head>
<body> 
<h1 id="logo"><a href="http://wordpress.org"><span>SteamPress</span></a></h1> 
<?php

switch($step)
{
	case 0:
?> 
<p>Welcome to SteamPress. Before getting started, we need some information on the database. You will need to know the following items before proceeding.</p> 
<ol> 
  <li>Database name</li> 
  <li>Database username</li> 
  <li>Database password</li> 
  <li>Database host</li> 
  <li>Table prefix (if you want to run more than one SteamPress in a single database) </li>
</ol> 
<p><strong>If for any reason this automatic file creation doesn't work, don't worry. All this does is fill in the database information to a configuration file. You may also simply open <code>steampress-config-sample.php</code> in a text editor, fill in your information, and save it as <code>steampress-config.php</code>. </strong></p>
<p>In all likelihood, these items were supplied to you by your ISP. If you do not have this information, then you will need to contact them before you can continue. If you&#8217;re all ready, <a href="setup-config.php?step=1">let&#8217;s go</a>! </p>
<?php
	break;

	case 1:
	?> 
</p> 
<form method="post" action="setup-config.php?step=2"> 
  <p>Below you should enter your database connection details. If you're not sure about these, contact your host. </p>
  <table> 
	<tr> 
	  <th scope="row">Database Name</th> 
	  <td><input name="dbname" type="text" size="45" value="wordpress" /></td> 
	  <td>The name of the database you want to run WP in. </td> 
	</tr> 
	<tr> 
	  <th scope="row">User Name</th> 
	  <td><input name="uname" type="text" size="45" value="username" /></td> 
	  <td>Your MySQL username</td> 
	</tr> 
	<tr> 
	  <th scope="row">Password</th> 
	  <td><input name="pwd" type="text" size="45" value="password" /></td> 
	  <td>...and MySQL password.</td> 
	</tr> 
	<tr> 
	  <th scope="row">Database Host</th> 
	  <td><input name="dbhost" type="text" size="45" value="localhost" /></td> 
	  <td>99% chance you won't need to change this value.</td> 
	</tr>
	<tr>
	  <th scope="row">Table Prefix</th>
	  <td><input name="prefix" type="text" id="prefix" value="steampress_" size="45" /></td>
	  <td>If you want to run multiple SteamPress installations in a single database, change this.</td>
	</tr> 
  </table> 
  <input name="submit" type="submit" value="Submit" /> 
</form> 
<?php
	break;
	
	case 2:
	$dbname = $_POST['dbname'];
	$uname = $_POST['uname'];
	$passwrd = $_POST['pwd'];
	$dbhost = $_POST['dbhost'];
	$prefix = $_POST['prefix'];
	if (empty($prefix))
	{
		$prefix = 'steampress_';
	}

	// Test the db connection.
	define('DB_NAME', $dbname);
	define('DB_USER', $uname);
	define('DB_PASSWORD', $passwrd);
	define('DB_HOST', $dbhost);

	// We'll fail here if the values are no good.
	require_once('../steampress-includes/steampress-db.php');
	$handle = fopen('../steampress-config.php', 'w');

	foreach ($configFile as $line_num => $line)
	{
		switch (substr($line,0,16))
		{
			case "define('DB_NAME'":
				fwrite($handle, str_replace("wordpress", $dbname, $line));
				break;
			case "define('DB_USER'":
				fwrite($handle, str_replace("'username'", "'$uname'", $line));
				break;
			case "define('DB_PASSW":
				fwrite($handle, str_replace("'password'", "'$passwrd'", $line));
				break;
			case "define('DB_HOST'":
				fwrite($handle, str_replace("localhost", $dbhost, $line));
				break;
			case '$table_prefix  =':
				fwrite($handle, str_replace('steampress_', $prefix, $line));
				break;
			default:
				fwrite($handle, $line);
		}
	}
	fclose($handle);
	chmod('../steampress-config.php', 0666);
?> 
<p>All right sparky! You've made it through this part of the installation. SteamPress can now communicate with your database. If you are ready, time now to <a href="install.php">run the install!</a></p> 
<?php
	break;

}
?> 
</body>
</html>
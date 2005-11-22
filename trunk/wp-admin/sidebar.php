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
 
$mode = 'sidebar';

require_once('admin.php');

get_currentuserinfo();

if ($user_level == 0)
	die ("Cheatin' uh ?");

if ('b' == $_GET['a']) {

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SteamPress &#8250; Posted</title>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=UTF-8" />
<link rel="stylesheet" href="wp-admin.css" type="text/css" />
</head>
<body>
	<p>Posted !</p>
	<p><a href="sidebar.php">Click here</a> to post again.</p>
</body>
</html><?php

} else {

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SteamPress &#8250; Sidebar</title>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('blog_charset'); ?>" />
<link rel="stylesheet" href="wp-admin.css" type="text/css" />
<style type="text/css" media="screen">
form {
	padding: 3px;
}
.sidebar-categories {
	display: block;
	height: 6.6em;
	overflow: auto;
	background-color: #f4f4f4;
}
.sidebar-categories label {
	font-size: 10px;
	display: block;
	width: 90%;
}
</style>
</head>
<body id="sidebar">
<h1 id="wphead"><a href="http://steamedpenguin.com/projects/steampress/" rel="external">SteamPress</a></h1>
<form name="post" action="post.php" method="POST">
<div><input type="hidden" name="action" value="post" />
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="mode" value="sidebar" />
<p>Title:
<input type="text" name="post_title" size="20" tabindex="1" style="width: 100%;" />
</p>
<p>Categories:
<span class="sidebar-categories">
<?php dropdown_categories(); ?>
</span>
</p>
<p>
Post:
<textarea rows="8" cols="12" style="width: 100%" name="content" tabindex="2"></textarea>
</p>
<p>
	<input name="saveasdraft" type="submit" id="saveasdraft" tabindex="9" value="Save as Draft" />
	<input name="publish" type="submit" id="publish" tabindex="6" style="font-weight: bold;" value="Publish" />

</p>
</div>
</form>

</body>
</html>
<?php
}
?>
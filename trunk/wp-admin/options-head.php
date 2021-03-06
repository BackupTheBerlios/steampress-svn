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
 

$wpvarstoreset = array('action','standalone', 'option_group_id');
for ($i=0; $i<count($wpvarstoreset); $i += 1)
{
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar))
	{
		if (empty($_POST["$wpvar"]))
		{
			if (empty($_GET["$wpvar"]))
			{
				$$wpvar = '';
			}
			else
			{
				$$wpvar = $_GET["$wpvar"];
			}
		}
		else
		{
			$$wpvar = $_POST["$wpvar"];
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
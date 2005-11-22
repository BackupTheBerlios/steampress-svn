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
 

/* Functions missing from older PHP versions */


/* Added in PHP 4.2.0 */

if (!function_exists('floatval'))
{
	function floatval($string)
	{
		return ((float) $string);
	}
}

if (!function_exists('is_a'))
{
	function is_a($object, $class)
	{
		// by Aidan Lister <aidan@php.net>
		if (get_class($object) == strtolower($class))
		{
			return true;
		}
		else
		{
			return is_subclass_of($object, $class);
		}
	}
}

if (!function_exists('ob_clean'))
{
	function ob_clean()
	{
		// by Aidan Lister <aidan@php.net>
		if (@ob_end_clean())
		{
			return ob_start();
		}
		return false;
	}
}


/* Added in PHP 4.3.0 */

function printr($var, $do_not_echo = false)
{
	// from php.net/print_r user contributed notes
	ob_start();
	print_r($var);
	$code =  htmlentities(ob_get_contents());
	ob_clean();
	if (!$do_not_echo)
	{
		echo "<pre>$code</pre>";
	}
	return $code;
}

if (!defined('CASE_LOWER'))
{
	define('CASE_LOWER', 0);
}

if (!defined('CASE_UPPER'))
{
	define('CASE_UPPER', 1);
}


/*
* Replace array_change_key_case()
*
* @category    PHP
* @package     PHP_Compat
* @link        http://php.net/function.array_change_key_case
* @author      Stephan Schmidt <schst@php.net>
* @author      Aidan Lister <aidan@php.net>
* @version     $Revision: 2247 $
* @since       PHP 4.2.0
* @require     PHP 4.0.0 (user_error)
*/

if (!function_exists('array_change_key_case'))
{
	function array_change_key_case($input, $case = CASE_LOWER)
	{
		if (!is_array($input))
		{
			user_error('array_change_key_case(): The argument should be an array',
				E_USER_WARNING);
			return false;
		}

		$output   = array ();
		$keys     = array_keys($input);
		$casefunc = ($case == CASE_LOWER) ? 'strtolower' : 'strtoupper';

		foreach ($keys as $key)
		{
			$output[$casefunc($key)] = $input[$key];
		}

		return $output;
	}
}

?>

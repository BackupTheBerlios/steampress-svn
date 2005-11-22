<?php

/*************************************************

SteamPress - Blogging without the Dirt
Author: SteamPress Development Team (developers@steampress.org)
Copyright (c): 2005 SteamPress, all rights reserved

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
 

if ( defined('WPLANG') && '' != constant('WPLANG') )
{
	include_once(ABSPATH . 'wp-includes/streams.php');
	include_once(ABSPATH . 'wp-includes/gettext.php');
}

function get_locale()
{
	global $locale;

	if (isset($locale))
	{
		return $locale;
	}

	// WPLANG is defined in wp-config.
	if (defined('WPLANG'))
	{
		$locale = WPLANG;
	}

	if (empty($locale))
	{
		$locale = 'en_US';
	}

	$locale = apply_filters('locale', $locale);

	return $locale;
}

// Return a translated string.
function __($text, $domain = 'default')
{
	global $l10n;

	if (isset($l10n[$domain]))
	{
		return $l10n[$domain]->translate($text);
	}
	else
	{
		return $text;
	}
}

// Echo a translated string.
function _e($text, $domain = 'default')
{
	global $l10n;

	if (isset($l10n[$domain]))
	{
		echo $l10n[$domain]->translate($text);
	}
	else
	{
		echo $text;
	}
}

// Return the plural form.
function __ngettext($single, $plural, $number, $domain = 'default')
{
	global $l10n;

	if (isset($l10n[$domain]))
	{
		return $l10n[$domain]->ngettext($single, $plural, $number);
	}
	else
	{
		if ($number != 1)
		{
			return $plural;
		}
		else
		{
			return $single;
		}
	}
}

function load_textdomain($domain, $mofile)
{
	global $l10n;

	if (isset($l10n[$domain]))
	{
		return;
	}

	if ( is_readable($mofile))
	{
	$input = new CachedFileReader($mofile);
	}
	else
	{
		return;
	}

	$l10n[$domain] = new gettext_reader($input);
}

function load_default_textdomain()
{
	global $l10n;

	$locale = get_locale();
	$mofile = ABSPATH . "wp-includes/languages/$locale.mo";

	load_textdomain('default', $mofile);
}

function load_plugin_textdomain($domain)
{
	$locale = get_locale();

	$mofile = ABSPATH . "wp-content/plugins/$domain-$locale.mo";
	load_textdomain($domain, $mofile);
}

function load_theme_textdomain($domain)
{
	$locale = get_locale();

	$mofile = get_template_directory() . "/$locale.mo";
	load_textdomain($domain, $mofile);
}

?>

<?php
$parentpath = dirname(dirname(__FILE__));
 
require_once($parentpath.'/steampress-config.php');

$locale = '';

// WPLANG is defined in steampress-config.
if (defined('WPLANG'))
{
	$locale = WPLANG;
}

if (empty($locale))
{
	$locale = 'en_US';
}

require_once(ABSPATH . 'steampress-includes/streams.php');
require_once(ABSPATH . 'steampress-includes/gettext.php');

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
		return $text;
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
	$input = new FileReader($mofile);
	}
	else
	{
		return;
	}

	$l10n[$domain] = new gettext_reader($input);
}

function load_default_textdomain()
{
	global $l10n, $locale;

	$mofile = ABSPATH . "steampress-includes/languages/$locale.mo";
	
	load_textdomain('default', $mofile);
}

function load_plugin_textdomain($domain)
{
	global $locale;
	
	$mofile = ABSPATH . "steampress-content/plugins/$domain-$locale.mo";
	load_textdomain($domain, $mofile);
}

function load_theme_textdomain($domain)
{
	global $locale;
	
	$mofile = get_template_directory() . "/$locale.mo";
	load_textdomain($domain, $mofile);
}

// Load the default domain.
load_default_textdomain();

require($curpath . 'locale.php');
?>
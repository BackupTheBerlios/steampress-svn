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
 
require_once( dirname( dirname(__FILE__) ) . '/wp-config.php');
require_once( ABSPATH . 'wp-includes/class-snoopy.php');

if ( !get_option('use_linksupdate') )
	die('Feature disabled.');

$link_uris = $wpdb->get_col("SELECT link_url FROM $wpdb->links");

if ( !$link_uris )
	die('No links');

$link_uris = urlencode( join( $link_uris, "\n" ) );

$query_string = "uris=$link_uris";

$http_request  = "POST /updated-batch/ HTTP/1.0\r\n";
$http_request .= "Host: api.pingomatic.com\r\n";
$http_request .= 'Content-Type: application/x-www-form-urlencoded; charset='.get_settings('blog_charset')."\r\n";
$http_request .= 'Content-Length: ' . strlen($query_string) . "\r\n";
$http_request .= 'User-Agent: WordPress/' . $wp_version . "\r\n";
$http_request .= "\r\n";
$http_request .= $query_string;

$response = '';
if( false !== ( $fs = fsockopen('api.pingomatic.com', 80, $errno, $errstr, 5) ) ) {
	fwrite($fs, $http_request);
	while ( !feof($fs) )
		$response .= fgets($fs, 1160); // One TCP-IP packet
	fclose($fs);

	$response = explode("\r\n\r\n", $response, 2);
	$body = trim( $response[1] );
	$body = str_replace(array("\r\n", "\r"), "\n", $body);

	$returns = explode("\n", $body);

	foreach ($returns as $return) :
		$time = addslashes( substr($return, 0, 19) );
		$uri = addslashes( preg_replace('/(.*?) | (.*?)/', '$2', $return) );
		$wpdb->query("UPDATE $wpdb->links SET link_updated = '$time' WHERE link_url = '$uri'");
	endforeach;
}
?>
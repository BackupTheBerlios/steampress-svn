<?php

/* This file sets various arrays and variables for use in SteamPress */
require(ABSPATH . 'sp-includes/version.php');

// On which page are we ?
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match('#([^/]+.php)#', $PHP_SELF, $self_matches)) {
	$pagenow = $self_matches[1];
} else if (strstr($PHP_SELF, '?')) {
	$pagenow = explode('/', $PHP_SELF);
	$pagenow = trim($pagenow[(sizeof($pagenow)-1)]);
	$pagenow = explode('?', $pagenow);
	$pagenow = $pagenow[0];
	if (($querystring_start == '/') && ($pagenow != 'post.php')) {
		$pagenow = get_settings('siteurl') . '/' . get_settings('blogfilename');
	}
} else {
	$pagenow = 'index.php';
}

// Simple browser detection
$is_lynx = 0; $is_gecko = 0; $is_winIE = 0; $is_macIE = 0; $is_opera = 0; $is_NS4 = 0;
if (!isset($HTTP_USER_AGENT)) {
	$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
}
if (preg_match('/Lynx/', $HTTP_USER_AGENT)) {
	$is_lynx = 1;
} elseif (preg_match('/Gecko/', $HTTP_USER_AGENT)) {
	$is_gecko = 1;
} elseif ((preg_match('/MSIE/', $HTTP_USER_AGENT)) && (preg_match('/Win/', $HTTP_USER_AGENT))) {
	$is_winIE = 1;
} elseif ((preg_match('/MSIE/', $HTTP_USER_AGENT)) && (preg_match('/Mac/', $HTTP_USER_AGENT))) {
	$is_macIE = 1;
} elseif (preg_match('/Opera/', $HTTP_USER_AGENT)) {
	$is_opera = 1;
} elseif ((preg_match('/Nav/', $HTTP_USER_AGENT) ) || (preg_match('/Mozilla\/4\./', $HTTP_USER_AGENT))) {
	$is_NS4 = 1;
}
$is_IE    = (($is_macIE) || ($is_winIE));

// Server detection
$is_apache = strstr($_SERVER['SERVER_SOFTWARE'], 'Apache') ? 1 : 0;

// if the config file does not provide the smilies array, let's define it here
if (!isset($spsmiliestrans)) {
    $spsmiliestrans = array(
        ' :)'        => 'icon_smile.gif',
        ' :D'        => 'icon_biggrin.gif',
        ' :-D'       => 'icon_biggrin.gif',
        ':grin:'    => 'icon_biggrin.gif',
        ' :)'        => 'icon_smile.gif',
        ' :-)'       => 'icon_smile.gif',
        ':smile:'   => 'icon_smile.gif',
        ' :('        => 'icon_sad.gif',
        ' :-('       => 'icon_sad.gif',
        ':sad:'     => 'icon_sad.gif',
        ' :o'        => 'icon_surprised.gif',
        ' :-o'       => 'icon_surprised.gif',
        ':eek:'     => 'icon_surprised.gif',
        ' 8O'        => 'icon_eek.gif',
        ' 8-O'       => 'icon_eek.gif',
        ':shock:'   => 'icon_eek.gif',
        ' :?'        => 'icon_confused.gif',
        ' :-?'       => 'icon_confused.gif',
        ' :???:'     => 'icon_confused.gif',
        ' 8)'        => 'icon_cool.gif',
        ' 8-)'       => 'icon_cool.gif',
        ':cool:'    => 'icon_cool.gif',
        ':lol:'     => 'icon_lol.gif',
        ' :x'        => 'icon_mad.gif',
        ' :-x'       => 'icon_mad.gif',
        ':mad:'     => 'icon_mad.gif',
        ' :P'        => 'icon_razz.gif',
        ' :-P'       => 'icon_razz.gif',
        ':razz:'    => 'icon_razz.gif',
        ':oops:'    => 'icon_redface.gif',
        ':cry:'     => 'icon_cry.gif',
        ':evil:'    => 'icon_evil.gif',
        ':twisted:' => 'icon_twisted.gif',
        ':roll:'    => 'icon_rolleyes.gif',
        ':wink:'    => 'icon_wink.gif',
        ' ;)'        => 'icon_wink.gif',
        ' ;-)'       => 'icon_wink.gif',
        ':!:'       => 'icon_exclaim.gif',
        ':?:'       => 'icon_question.gif',
        ':idea:'    => 'icon_idea.gif',
        ':arrow:'   => 'icon_arrow.gif',
        ' :|'        => 'icon_neutral.gif',
        ' :-|'       => 'icon_neutral.gif',
        ':neutral:' => 'icon_neutral.gif',
        ':mrgreen:' => 'icon_mrgreen.gif',
    );
}

// sorts the smilies' array
if (!function_exists('smiliescmp')) {
	function smiliescmp ($a, $b) {
	   if (strlen($a) == strlen($b)) {
		  return strcmp($a, $b);
	   }
	   return (strlen($a) > strlen($b)) ? -1 : 1;
	}
}
uksort($spsmiliestrans, 'smiliescmp');

// generates smilies' search & replace arrays
foreach($spsmiliestrans as $smiley => $img) {
	$sp_smiliessearch[] = $smiley;
	$smiley_masked = htmlspecialchars( trim($smiley) , ENT_QUOTES);
	$sp_smiliesreplace[] = " <img src='" . get_settings('siteurl') . "/sp-images/smilies/$img' alt='$smiley_masked' class='sp-smiley' /> ";
}

// Path for cookies
define('COOKIEPATH', preg_replace('|https?://[^/]+|i', '', get_settings('home') . '/' ) );

// Some default filters
add_filter('bloginfo','sp_specialchars');
add_filter('category_description', 'sptexturize');
add_filter('list_cats', 'sptexturize');
add_filter('comment_author', 'sptexturize');
add_filter('comment_text', 'sptexturize');
add_filter('single_post_title', 'sptexturize');
add_filter('the_title', 'sptexturize');
add_filter('the_content', 'sptexturize');
add_filter('the_excerpt', 'sptexturize');
add_filter('bloginfo', 'sptexturize');

// Comments, trackbacks, pingbacks
add_filter('pre_comment_author_name', 'strip_tags');
add_filter('pre_comment_author_name', 'trim');
add_filter('pre_comment_author_name', 'sp_specialchars', 30);

add_filter('pre_comment_author_email', 'trim');
add_filter('pre_comment_author_email', 'sanitize_email');

add_filter('pre_comment_author_url', 'strip_tags');
add_filter('pre_comment_author_url', 'trim');
add_filter('pre_comment_author_url', 'clean_url');

add_filter('pre_comment_content', 'sp_filter_kses');
add_filter('pre_comment_content', 'format_to_post');
add_filter('pre_comment_content', 'balanceTags', 30);

// Default filters for these functions
add_filter('comment_author', 'sptexturize');
add_filter('comment_author', 'convert_chars');
add_filter('comment_author', 'sp_specialchars');

add_filter('comment_email', 'antispambot');

add_filter('comment_url', 'clean_url');

add_filter('comment_text', 'convert_chars');
add_filter('comment_text', 'make_clickable');
add_filter('comment_text', 'wpautop', 30);
add_filter('comment_text', 'convert_smilies', 20);

add_filter('comment_text_rss', 'htmlspecialchars');

add_filter('comment_excerpt', 'convert_chars');

// Places to balance tags on input
add_filter('content_save_pre', 'balanceTags', 50);
add_filter('excerpt_save_pre', 'balanceTags', 50);
add_filter('comment_save_pre', 'balanceTags', 50);

?>
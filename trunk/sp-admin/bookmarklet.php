<?php
$mode = 'bookmarklet';
require_once('admin.php');

if ($user_level == 0)
	die ("Cheatin' uh?");

if ('b' == $a) {

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript">
<!--
window.close()
-->
</script>
</head>
<body></body>
</html>
<?php
} else {
    $popuptitle = sp_specialchars(stripslashes($popuptitle));
    $text = sp_specialchars(stripslashes(urldecode($text)));
    
    /* big funky fixes for browsers' javascript bugs */
    
    if (($is_macIE) && (!isset($IEMac_bookmarklet_fix))) {
        $popuptitle = preg_replace($sp_macIE_correction["in"],$sp_macIE_correction["out"],$popuptitle);
        $text = preg_replace($sp_macIE_correction["in"],$sp_macIE_correction["out"],$text);
    }
    
    if (($is_winIE) && (!isset($IEWin_bookmarklet_fix))) {
        $popuptitle =  preg_replace("/\%u([0-9A-F]{4,4})/e",  "'&#'.base_convert('\\1',16,10).';'", $popuptitle);
        $text =  preg_replace("/\%u([0-9A-F]{4,4})/e",  "'&#'.base_convert('\\1',16,10).';'", $text);
    }
    
    if (($is_gecko) && (!isset($Gecko_bookmarklet_fix))) {
        $popuptitle = preg_replace($sp_gecko_correction["in"],$sp_gecko_correction["out"],$popuptitle);
        $text = preg_replace($sp_gecko_correction["in"],$sp_gecko_correction["out"],$text);
    }
    
    $post_title = sp_specialchars($_REQUEST['post_title']);
    if (!empty($post_title)) {
        $post_title =  stripslashes($post_title);
    } else {
        $post_title = $popuptitle;
    }

    $edited_post_title = sp_specialchars($post_title);

// $post_pingback needs to be set in any file that includes edit-form.php
    $post_pingback = get_settings('default_pingback_flag');
    
    $content  = sp_specialchars($_REQUEST['content']);
	$popupurl = sp_specialchars($_REQUEST['popupurl']);
    if ( !empty($content) ) {
        $content = sp_specialchars( stripslashes($_REQUEST['content']) );
    } else {
        $content = '<a href="'.$popupurl.'">'.$popuptitle.'</a>'."\n$text";
    }
    
    /* /big funky fixes */

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php bloginfo('name') ?> &rsaquo; Bookmarklet &#8212; SteamPress</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_settings('blog_charset'); ?>" />
<link rel="stylesheet" href="sp-admin.css" type="text/css" />
<link rel="shortcut icon" href="../sp-images/sp-favicon.png" />

<style type="text/css">
<!--

#wpbookmarklet textarea,input,select {
	border-width: 1px;
	border-color: #cccccc;
	border-style: solid;
	padding: 2px;
	margin: 1px;
}

#wpbookmarklet .checkbox {
	background-color: #ffffff;
	border-width: 0px;
	padding: 0px;
	margin: 0px;
}

#wpbookmarklet textarea {
	font-family: Verdana, Geneva, Arial, Helvetica;
	font-size: 0.9em;
}

#wpbookmarklet .wrap {
    border: 0px;
}

#wpbookmarklet #postdiv {
    margin-bottom: 0.5em;
}

#wpbookmarklet #titlediv {
    margin-bottom: 1em;
}

-->
</style>
</head>
<body id="wpbookmarklet">
<div id="wphead">
<h1><?php bloginfo('name') ?></h1>
</div>

<?php require('edit-form.php'); ?>

</body>
</html><?php
}
?>
<?php

if (empty($doing_rss)) {
    $doing_rss = 1;
    require('steampress-blog-header.php');
}

// Remove the pad, if present.
$feed = preg_replace('/^_+/', '', $feed);

if ($feed == '' || $feed == 'feed') {
    // TODO:  Get default feed from options DB.
    $feed = 'rss2';
}

if ( is_single() || ($withcomments == 1) ) {
    require('steampress-commentsrss2.php');
} else {
    switch ($feed) {
    case 'atom':
        require('steampress-atom.php');
        break;
    case 'rdf':
        require('steampress-rdf.php');
        break;
    case 'rss':
        require('steampress-rss.php');
        break;
    case 'rss2':
        require('steampress-rss2.php');
        break;
    }
}

?>

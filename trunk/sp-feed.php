<?php

if (empty($doing_rss)) {
    $doing_rss = 1;
    require('sp-blog-header.php');
}

// Remove the pad, if present.
$feed = preg_replace('/^_+/', '', $feed);

if ($feed == '' || $feed == 'feed') {
    // TODO:  Get default feed from options DB.
    $feed = 'rss2';
}

if ( is_single() || ($withcomments == 1) ) {
    require('sp-commentsrss2.php');
} else {
    switch ($feed) {
    case 'atom':
        require('sp-atom.php');
        break;
    case 'rdf':
        require('sp-rdf.php');
        break;
    case 'rss':
        require('sp-rss.php');
        break;
    case 'rss2':
        require('sp-rss2.php');
        break;
    }
}

?>

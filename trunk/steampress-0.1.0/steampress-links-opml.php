<?php	
$doing_rss	=	1;

require('steampress-blog-header.php');
header('Content-type:	text/xml;	charset='	.	get_settings('blog_charset'),	true);
$link_cat	=	$_GET['link_cat'];
if	((empty($link_cat))	||	($link_cat	==	'all')	||	($link_cat	==	'0'))
{
	$sql_cat	=	'';
}
else
{	//	be	safe
	$link_cat = ''.urldecode($link_cat).'';
	$link_cat = addslashes_gpc($link_cat);
	$link_cat = intval($link_cat);
	if ($link_cat != 0)
	{
		$sql_cat = "AND $steampressdb->links.link_category = $link_cat";
		$cat_name = $steampressdb->get_var("SELECT $steampressdb->linkcategories.cat_name FROM $steampressdb->linkcategories WHERE $steampressdb->linkcategories.cat_id = $link_cat");
		if (!empty($cat_name))
		{
			$cat_name = ": category $cat_name";
		}
	}
}
?>
<?php echo "<?xml	version=\"1.0\"?".">\n"; ?>
<!-- generator="steampress/<?php	echo $steampress_version ?>"	-->
<opml version="1.0">
	<head>
		<title>Links for <?php echo get_bloginfo('name').$cat_name ?></title>
		<dateCreated><?php echo gmdate("D, d M Y H:i:s"); ?> GMT</dateCreated>
	</head>
	<body>
<?php $sql = "SELECT $steampressdb->links.link_url, link_rss, $steampressdb->links.link_name, $steampressdb->links.link_category, $steampressdb->linkcategories.cat_name 
FROM $steampressdb->links 
	LEFT JOIN $steampressdb->linkcategories on $steampressdb->links.link_category = $steampressdb->linkcategories.cat_id
	$sql_cat
	ORDER BY $steampressdb->linkcategories.cat_name, $steampressdb->links.link_name	\n";
	//echo("<!-- $sql -->");
$prev_cat_id = 0;
$results = $steampressdb->get_results($sql);
if ($results)
{
	foreach ($results as $result)
	{
		if ($result->link_category != $prev_cat_id)
		{
			// new category
			if ($prev_cat_id != 0)
			{
			// not first time
?>
		</outline>
<?php
			} // end if not first time
?>
		<outline type="category" title="<?php echo(htmlspecialchars(stripslashes($result->cat_name))) ?>">
<?php
			$prev_cat_id = $result->link_category;
		} // end I if new category
?>
			<outline title="<?php echo(htmlspecialchars(stripslashes($result->link_name))) ?>" type="link" xmlUrl="<?php echo $result->link_rss; ?>" htmlUrl="<?php echo($result->link_url) ?>"/>
<?php
	} // end foreach
} // end if
?>
		</outline>
	</body>
</opml>
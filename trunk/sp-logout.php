<?php
require(dirname(__FILE__) . '/sp-blog-header.php');
$login_method = get_settings('login_method');

//expire the cookies
setcookie('steampressuser_' . COOKIEHASH, ' ', time() - 31536000, COOKIEPATH);
setcookie('steampresspass_' . COOKIEHASH, ' ', time() - 31536000, COOKIEPATH);
header('Expires: Mon, 11 Jan 1984 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
if($login_method == 'new')
{
	header('Location: ' . get_settings('siteurl') . '/sp-admin/');
}
exit();
?>

<?php 
require_once('admin.php');

$title = 'Profile';
$parent_file = 'profile.php';

$spvarstoreset = array('action', 'profile', 'user');
for ($i=0; $i<count($spvarstoreset); $i += 1) {
	$spvar = $spvarstoreset[$i];
	if (!isset($$spvar)) {
		if (empty($_POST["$spvar"])) {
			if (empty($_GET["$spvar"])) {
				$$spvar = '';
			} else {
				$$spvar = $_GET["$spvar"];
			}
		} else {
			$$spvar = $_POST["$spvar"];
		}
	}
}

require_once('../sp-config.php');
auth_redirect();
switch($action) {

case 'update':

	get_currentuserinfo();

	/* checking the nickname has been typed */
	if (empty($_POST["newuser_nickname"])) {
		die (__("<strong>ERROR</strong>: please enter your nickname (can be the same as your login)"));
		return false;
	}

	/* if the ICQ UIN has been entered, check to see if it has only numbers */
	if (!empty($_POST["newuser_icq"])) {
		if ((ereg("^[0-9]+$",$_POST["newuser_icq"]))==false) {
			die (__("<strong>ERROR</strong>: your ICQ UIN can only be a number, no letters allowed"));
			return false;
		}
	}

	/* checking e-mail address */
	if (empty($_POST["newuser_email"])) {
		die (__("<strong>ERROR</strong>: please type your e-mail address"));
		return false;
	} else if (!is_email($_POST["newuser_email"])) {
		die (__("<strong>ERROR</strong>: the e-mail address isn't correct"));
		return false;
	}

	if ($_POST["pass1"] == "") {
		if ($_POST["pass2"] != "")
			die (__("<strong>ERROR</strong>: you typed your new password only once. Go back to type it twice."));
		$updatepassword = "";
	} else {
		if ($_POST["pass2"] == "")
			die (__("<strong>ERROR</strong>: you typed your new password only once. Go back to type it twice."));
		if ($_POST["pass1"] != $_POST["pass2"])
			die (__("<strong>ERROR</strong>: you typed two different passwords. Go back to correct that."));
		$newuser_pass = $_POST["pass1"];
		$updatepassword = "user_pass=MD5('$newuser_pass'), ";
		setcookie('steampresspass_' . COOKIEHASH, " ", time() - 31536000, COOKIEPATH);
		setcookie('steampresspass_' . COOKIEHASH, md5(md5($newuser_pass)), time() + 31536000, COOKIEPATH);
	}

	$newuser_firstname = sp_specialchars($_POST['newuser_firstname']);
	$newuser_lastname = sp_specialchars($_POST['newuser_lastname']);
	$newuser_nickname = $_POST['newuser_nickname'];
    $newuser_nicename = sanitize_title($newuser_nickname);
	$newuser_icq = sp_specialchars($_POST['newuser_icq']);
	$newuser_aim = sp_specialchars($_POST['newuser_aim']);
	$newuser_msn = sp_specialchars($_POST['newuser_msn']);
	$newuser_yim = sp_specialchars($_POST['newuser_yim']);
	$newuser_email = sp_specialchars($_POST['newuser_email']);
	$newuser_url = sp_specialchars($_POST['newuser_url']);
	$newuser_url = preg_match('/^(https?|ftps?|mailto|news|gopher):/is', $newuser_url) ? $newuser_url : 'http://' . $newuser_url; 
	$newuser_idmode = sp_specialchars($_POST['newuser_idmode']);
	$user_description = $_POST['user_description'];

	$result = $spdb->query("UPDATE $spdb->users SET user_firstname='$newuser_firstname', $updatepassword user_lastname='$newuser_lastname', user_nickname='$newuser_nickname', user_icq='$newuser_icq', user_email='$newuser_email', user_url='$newuser_url', user_aim='$newuser_aim', user_msn='$newuser_msn', user_yim='$newuser_yim', user_idmode='$newuser_idmode', user_description = '$user_description', user_nicename = '$newuser_nicename' WHERE ID = $user_ID");

	header('Location: profile.php?updated=true');
break;

case 'IErightclick':

	$bookmarklet_height= 550;

	?>

	<div class="menutop">&nbsp;IE one-click bookmarklet</div>

	<table width="100%" cellpadding="20">
	<tr><td>

	<p>To have a one-click bookmarklet, just copy and paste this<br />into a new text file:</p>
	<?php
	$regedit = "REGEDIT4\r\n[HKEY_CURRENT_USER\Software\Microsoft\Internet Explorer\MenuExt\Post To &SP : ". get_settings('blogname') ."]\r\n@=\"javascript:doc=external.menuArguments.document;Q=doc.selection.createRange().text;void(btw=window.open('". get_settings('siteurl') ."/sp-admin/bookmarklet.php?text='+escape(Q)+'".$bookmarklet_tbpb."&popupurl='+escape(doc.location.href)+'&popuptitle='+escape(doc.title),'bookmarklet','scrollbars=no,width=480,height=".$bookmarklet_height.",left=100,top=150,status=yes'));btw.focus();\"\r\n\"contexts\"=hex:31\"";
	?>
	<pre style="margin: 20px; background-color: #cccccc; border: 1px dashed #333333; padding: 5px; font-size: 12px;"><?php echo $regedit; ?></pre>
	<p>Save it as steampress.reg, and double-click on this file in an Explorer<br />
	window. Answer Yes to the question, and restart Internet Explorer.<br /><br />
	That's it, you can now right-click in an IE window and select <br />
	'Post to SP' to make the bookmarklet appear. :)</p>

	<p align="center">
	  <form>
		<input class="search" type="button" value="1" name="Close this window" />
	  </form>
	</p>
	</td></tr>
	</table>
	<?php

break;


default:
	$parent_file = 'users.php';
	include_once('admin-header.php');
	$profiledata=get_userdata($user_ID);

	$bookmarklet_height= 440;

if (isset($updated)) { ?>
<div class="updated">
<p><strong><?php _e('Profile updated.') ?></strong></p>
</div>
<?php } ?>
<div class="wrap">
<h2><?php _e('Profile'); ?></h2>
<form name="profile" id="profile" action="profile.php" method="post">
	<p>
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="checkuser_id" value="<?php echo $user_ID ?>" />
  </p>

  <table width="99%"  border="0" cellspacing="2" cellpadding="3">
    <tr>
      <th width="33%" scope="row"><?php _e('Login:') ?></th>
      <td width="67%"><?php echo $profiledata->user_login; ?></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Level:') ?></th>
      <td><?php echo $profiledata->user_level; ?></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Posts:') ?></th>
      <td>    <?php
	$posts = get_usernumposts($user_ID);
	echo $posts;
	?></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('First name:') ?></th>
      <td><input type="text" name="newuser_firstname" id="newuser_firstname" value="<?php echo $profiledata->user_firstname ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Last name:') ?></th>
      <td><input type="text" name="newuser_lastname" id="newuser_lastname2" value="<?php echo $profiledata->user_lastname ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Profile:') ?></th>
      <td><textarea name="user_description" rows="5" id="textarea2" style="width: 99%; "><?php echo $profiledata->user_description ?></textarea></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Nickname:') ?></th>
      <td><input type="text" name="newuser_nickname" id="newuser_nickname2" value="<?php echo $profiledata->user_nickname ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('E-mail:') ?></th>
      <td><input type="text" name="newuser_email" id="newuser_email2" value="<?php echo $profiledata->user_email ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Website:') ?></th>
      <td><input type="text" name="newuser_url" id="newuser_url2" value="<?php echo $profiledata->user_url ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('ICQ:') ?></th>
      <td><input type="text" name="newuser_icq" id="newuser_icq2" value="<?php if ($profiledata->user_icq > 0) { echo $profiledata->user_icq; } ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('AIM:') ?></th>
      <td><input type="text" name="newuser_aim" id="newuser_aim2" value="<?php echo $profiledata->user_aim ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('MSN IM:') ?> </th>
      <td><input type="text" name="newuser_msn" id="newuser_msn2" value="<?php echo $profiledata->user_msn ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Yahoo IM:') ?> </th>
      <td>        <input type="text" name="newuser_yim" id="newuser_yim2" value="<?php echo $profiledata->user_yim ?>" />      </td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Identity on blog:') ?> </th>
      <td><select name="newuser_idmode">
        <option value="nickname"<?php
	if ($profiledata->user_idmode == 'nickname')
	echo ' selected="selected"'; ?>><?php echo $profiledata->user_nickname ?></option>
        <option value="login"<?php
	if ($profiledata->user_idmode=="login")
	echo ' selected="selected"'; ?>><?php echo $profiledata->user_login ?></option>
        <option value="firstname"<?php
	if ($profiledata->user_idmode=="firstname")
	echo ' selected="selected"'; ?>><?php echo $profiledata->user_firstname ?></option>
        <option value="lastname"<?php
	if ($profiledata->user_idmode=="lastname")
	echo ' selected="selected"'; ?>><?php echo $profiledata->user_lastname ?></option>
        <option value="namefl"<?php
	if ($profiledata->user_idmode=="namefl")
	echo ' selected="selected"'; ?>><?php echo $profiledata->user_firstname." ".$profiledata->user_lastname ?></option>
        <option value="namelf"<?php
	if ($profiledata->user_idmode=="namelf")
	echo ' selected="selected"'; ?>><?php echo $profiledata->user_lastname." ".$profiledata->user_firstname ?></option>
      </select>        </td>
    </tr>
    <tr>
      <th scope="row"><?php _e('New <strong>Password</strong><br/>(Leave blank to stay the same.)') ?></th>
      <td><input type="password" name="pass1" size="16" value="" />
      	<br />
        <input type="password" name="pass2" size="16" value="" /></td>
    </tr>
  </table>
  <p class="submit">
    <input type="submit" value="<?php _e('Update Profile &raquo;') ?>" name="submit" />
  </p>
</form>
</div>

</div>
	<?php

break;
}

/* </Profile | My Profile> */
include('admin-footer.php');
 ?>

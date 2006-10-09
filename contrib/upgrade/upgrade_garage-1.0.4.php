<?php
/***************************************************************************
 *                               upgrade_garage.php
 *                            -------------------
 *
 *   copyright            : ©2003 Freakin' Booty ;-P & Antony Bailey
 *   project              : http://sourceforge.net/projects/dbgenerator
 *   Website              : http://freakingbooty.no-ip.com/ & http://www.rapiddr3am.net
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

define('IN_PHPBB', true);
$phpbb_root_path = './';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);

//
// Start session management
//
$userdata = session_pagestart($user_ip, PAGE_INDEX);
init_userprefs($userdata);
//
// End session management
//

if( !$userdata['session_logged_in'] )
{
	$header_location = ( @preg_match('/Microsoft|WebSTAR|Xitami/', getenv('SERVER_SOFTWARE')) ) ? 'Refresh: 0; URL=' : 'Location: ';
	header($header_location . append_sid("login.$phpEx?redirect=upgrade_garage.$phpEx", true));
	exit;
}

if( $userdata['user_level'] != ADMIN )
{
	message_die(GENERAL_MESSAGE, 'You are not authorised to access this page');
}

$page_title = 'Upgrading To phpBB Garage Version 1.2.0';
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

echo '<table width="100%" cellspacing="1" cellpadding="2" border="0" class="forumline">';
echo '<tr><th>Upgrading To phpBB Garage Version 1.2.0</th></tr><tr><td class="row1" ><span class="genmed"><ul type="circle">';

$sql = array();

//Update Existing Fields
$sql[] = "UPDATE " . $table_prefix . "garage_config SET config_value = '1.2.0' WHERE config_name = 'version'";

//Alter Exsiting Fields
$sql[] = "ALTER TABLE " . $table_prefix . "garage_categories ADD `field_order` TINYINT( 4 ) UNSIGNED NOT NULL DEFAULT '0'";
$sql[] = "ALTER TABLE " . $table_prefix . "garage_images` ADD `attach_thumb_filesize` INT( 10 ) NOT NULL DEFAULT '0'";
$sql[] = "ALTER TABLE " . $table_prefix . "garage_images ADD `garage_id` int(10) unsigned NOT NULL default '0'";
$sql[] = "ALTER TABLE " . $table_prefix . "garage_mods` ADD `purchase_rating` TINYINT( 2 ) NULL AFTER `install_comments`";

//Create New Entries
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('max_upload_images', '5')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('max_remote_images', '5')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('private_upload_quota', '')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('private_remote_quota', '')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('topdynorun_on', '1')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('topdynorun_limit', '5')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('quartermile_image_required', '1')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('quartermile_image_required_limit', '13')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('dynorun_image_required', '1')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('dynorun_image_required_limit', '300')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('items_pending', '0')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('private_deny_perms', '')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('garage_images', '1')";

//We Need To Setup Field Order Since It Will Be Blank
$sql2 = "SELECT * FROM " . $table_prefix ."garage_categories";
if ( !($result2 = $db->sql_query($sql2)) )
{
	message_die(GENERAL_ERROR, 'Could Select Business Data', '', __LINE__, __FILE__, $sql);
}

$i = 1;
while( $row = $db->sql_fetchrow($result2) )
{
	$sql[] = "UPDATE " . $table_prefix . "garage_categories SET field_order = '$i' WHERE id = ".$row['id'];
	$i++;
}

for( $i = 0; $i < count($sql); $i++ )
{
	if( !$result = $db->sql_query ($sql[$i]) )
	{
		$error = $db->sql_error();

		echo '<li>' . $sql[$i] . '<br /> +++ <font color="#FF0000"><b>Error:</b></font> ' . $error['message'] . '</li><br />';
	}
	else
	{
		echo '<li>' . $sql[$i] . '<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
	}
}




echo '</ul></span></td></tr><tr><td class="catBottom" height="28">&nbsp;</td></tr>';

echo '<tr><th>End</th></tr><tr><td><span class="genmed">Upgrade is now finished. Please be sure to delete this file now.<br />If you have run into any errors, please visit the <a href="http://www.phpbbgarage.com" target="_phpbbsupport">phpBB Garage Site</a> and ask someone for help.</span></td></tr>';
echo '<tr><td class="catBottom" height="28" align="center"><span class="genmed"><a href="' . append_sid("index.$phpEx") . '">Have a nice day</a></span></td></table>';

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>

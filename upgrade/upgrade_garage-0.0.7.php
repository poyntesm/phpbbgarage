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

$page_title = 'Upgrading To phpBB Garage Version 1.0.0';
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

echo '<table width="100%" cellspacing="1" cellpadding="2" border="0" class="forumline">';
echo '<tr><th>Upgrading To phpBB Garage Version 1.0.0</th></tr><tr><td class="row1" ><span class="genmed"><ul type="circle">';

$sql = array();

//add pending to makes
$sql[] = "ALTER TABLE " . $table_prefix . "garage_makes ADD pending TINYINT( 1 ) DEFAULT '1' NOT NULL";
$sql[] = "UPDATE " . $table_prefix . "garage_makes SET pending = 0 WHERE pending = 1";

//add pending to models
$sql[] = "ALTER TABLE " . $table_prefix . "garage_models ADD pending TINYINT( 1 ) DEFAULT '1' NOT NULL";
$sql[] = "UPDATE " . $table_prefix . "garage_models SET pending = 0 WHERE pending = 1";

//add install comments to mods
$sql[] = "ALTER TABLE " . $table_prefix . "garage_mods ADD install_comments TEXT DEFAULT '' NULL";

//add pending to rollingroad
$sql[] = "ALTER TABLE " . $table_prefix . "garage_rollingroad ADD pending int(11) DEFAULT '1' NOT NULL";

//Update images
$sql[] ="UPDATE `phpbb_garage_images` SET `attach_ext` = '.jpg' WHERE `attach_ext` = 'jpg'";
$sql[] ="UPDATE `phpbb_garage_images` SET `attach_ext` = '.gif' WHERE `attach_ext` = 'gif'";
$sql[] ="UPDATE `phpbb_garage_images` SET `attach_ext` = '.png' WHERE `attach_ext` = 'png'";

//add rating table
$sql[] = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "garage_rating (
		`id` int(10) NOT NULL auto_increment,
		`garage_id` int(10) NOT NULL default '0',
		`rating` int(10) NOT NULL default '0',
		`user_id` int(10) NOT NULL default '0',
		`rate_date` int(10) default NULL,
		PRIMARY KEY  (`id`)
	)";

//add missing config options
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('browse_perms', '*')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('interact_perms', '*')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('add_perms', '*')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('upload_perms', '*')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('private_browse_perms', '')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('private_interact_perms', '')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('private_add_perms', '')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('private_upload_perms', '')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('menu_selection', 'MAIN,BROWSE,SEARCH,INSURANCEREVIEW,GARAGEREVIEW,SHOPREVIEW,QUARTERMILE,ROLLINGROAD')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('featured_vehicle_from_block', '')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('toprated_on', '1')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('toprated_limit', '5')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('topquartermile_on', '1')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('topquartermile_limit', '5')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('enable_quartermile', '1')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('enable_quartermile_approval', '1')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('enable_business_approval', '1')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('rating_permanent', '0')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('rating_always_updateable', '1')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('enable_rollingroad', '1')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('enable_rollingroad_approval', '1')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('enable_insurance', '1')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('profile_thumbs', '1')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('enable_user_submit_make', '1')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('enable_user_submit_model', '1')";
$sql[] = "INSERT INTO " . $table_prefix . "garage_config VALUES ('version', '1.0.0')";

//Added For RC5
$sql[] = "ALTER TABLE " . $table_prefix . "garage CHANGE `year` `made_year` VARCHAR(4) NOT NULL DEFAULT '2003'";
$sql[] = "ALTER TABLE " . $table_prefix . "garage_business CHANGE `name` `title` VARCHAR(255) NULL DEFAULT NULL";

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

if (file_exists($phpbb_root_path.'admin/admin_garage_moderation.php'))
{
	@unlink($phpbb_root_path.'admin/admin_garage_moderation.php');
	echo '<li>Delete File : '.$phpbb_root_path.'admin/admin_garage_moderation.php<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'includes/functions_garage_errors.php'))
{
	@unlink($phpbb_root_path.'includes/functions_garage_errors.php');
	echo '<li>Delete File : '.$phpbb_root_path.'includes/functions_garage_errors.php<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_add_insurance.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_add_insurance.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_add_insurance.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_add_modification.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_add_modification.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_add_modification.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_add_quartermile.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_add_quartermile.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_add_quartermile.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_add_rollingroad.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_add_rollingroad.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_add_rollingroad.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_business_pending.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_business_pending.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_business_pending.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_business_pending_body.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_business_pending_body.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_business_pending_body.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_create_vehicle.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_create_vehicle.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_create_vehicle.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_edit_insurance.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_edit_insurance.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_edit_insurance.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_edit_modification.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_edit_modification.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_edit_modification.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_edit_quartermile.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_edit_quartermile.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_edit_quartermile.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_edit_rollingroad.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_edit_rollingroad.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_edit_rollingroad.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_edit_vehicle.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_edit_vehicle.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_edit_vehicle.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_quartermile_body.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_quartermile_body.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_quartermile_body.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_quartermile_pending.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_quartermile_pending.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_quartermile_pending.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_quartermile_pending_body.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_quartermile_pending_body.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_quartermile_pending_body.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_rollingroad_pending.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_rollingroad_pending.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_rollingroad_pending.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_search_insurance.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_search_insurance.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_search_insurance.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_tank.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_tank.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_tank.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/garage_view_own_vehicle.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/garage_view_own_vehicle.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/garage_view_own_vehicle.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_business_body.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_business_body.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_business_body.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_business_edit_body.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_business_edit_body.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_business_edit_body.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_cat_body.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_cat_body.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_cat_body.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_cat_delete_body.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_cat_delete_body.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_cat_delete_body.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_cat_new_body.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_cat_new_body.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_cat_new_body.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_cat_select_body.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_cat_select_body.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_cat_select_body.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_config_body.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_config_body.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_config_body.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_edit_insurance.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_edit_insurance.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_edit_insurance.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_edit_quartermile.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_edit_quartermile.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_edit_quartermile.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_edit_rollingroad.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_edit_rollingroad.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_edit_rollingroad.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_makes_body.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_makes_body.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_makes_body.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_model_delete_body.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_model_delete_body.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_model_delete_body.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_model_modify_body.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_model_modify_body.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_model_modify_body.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_moderation_body.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_moderation_body.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_moderation_body.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_moderation_car_body.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_moderation_car_body.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_moderation_car_body.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_moderation_list_mods_body.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_moderation_list_mods_body.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_moderation_list_mods_body.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_moderation_list_quartermile.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_moderation_list_quartermile.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_moderation_list_quartermile.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_moderation_list_rollingroad.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_moderation_list_rollingroad.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_moderation_list_rollingroad.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_moderation_mod_body.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_moderation_mod_body.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_moderation_mod_body.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}
if (file_exists($phpbb_root_path.'templates/subSilver/admin/garage_tools_body.tpl'))
{
	@unlink($phpbb_root_path.'templates/subSilver/admin/garage_tools_body.tpl');
	echo '<li>Delete File : '.$phpbb_root_path.'templates/subSilver/admin/garage_tools_body.tpl<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
}

echo '</ul></span></td></tr><tr><td class="catBottom" height="28">&nbsp;</td></tr>';

echo '<tr><th>End</th></tr><tr><td><span class="genmed">Installation is now finished. Please be sure to delete this file now.<br />If you have run into any errors, please visit the <a href="http://www.phpbb.com/phpBB/viewtopic.php?t=290641" target="_phpbbsupport">Garage Support Thread</a> and ask someone for help.</span></td></tr>';
echo '<tr><td class="catBottom" height="28" align="center"><span class="genmed"><a href="' . append_sid("index.$phpEx") . '">Have a nice day</a></span></td></table>';

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>

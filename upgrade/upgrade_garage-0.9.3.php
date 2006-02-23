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

//add missing config options
$sql[] = "ALTER TABLE " . $table_prefix . "garage CHANGE `year` `made_year` VARCHAR(4) NOT NULL DEFAULT '2003'";
$sql[] = "ALTER TABLE " . $table_prefix . "garage_business CHANGE `name` `title` VARCHAR(255) NULL DEFAULT NULL";
$sql[] = "UPDATE " . $table_prefix . "garage_config SET config_value = '1.0.0' WHERE config_name = 'version'";

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

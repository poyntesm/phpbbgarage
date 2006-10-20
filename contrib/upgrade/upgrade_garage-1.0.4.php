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
$sql[] = "ALTER TABLE " . $table_prefix . "garage_images ADD `attach_thumb_filesize` INT( 10 ) NOT NULL DEFAULT '0'";
$sql[] = "ALTER TABLE " . $table_prefix . "garage_images ADD `garage_id` int(10) unsigned NOT NULL default '0'";
$sql[] = "ALTER TABLE " . $table_prefix . "garage_mods ADD `purchase_rating` TINYINT( 2 ) NULL AFTER `install_comments`";
$sql[] = "ALTER TABLE " . $table_prefix . "garage ADD `weighted_rating` DOUBLE NOT NULL default '0' AFTER `main_vehicle`";
$sql[] = "ALTER TABLE " . $table_prefix . "garage ADD `engine_type` varchar(128) default NULL AFTER `made_year`";

//Make Sure All Config Entries Exist
$params = array('max_user_cars' => '1', 'year_start' => '1980', 'year_end' => '1', 'cars_per_page' => '30', 'allow_image_upload' => '1', 'max_image_kbytes' => '1024', 'max_image_resolution' => '1024', 'max_upload_images' => '5', 'max_remote_images' => '5', 'thumbnail_resolution' => '150', 'enable_guestbooks' => '1', 'enable_featured_vehicle' => '1', 'featured_vehicle_id' => '1', 'featured_vehicle_description' => '', 'featured_vehicle_random' => '0', 'allow_image_url' => '1', 'allow_mod_image' => '1', 'show_mod_gallery' => '1', 'limit_mod_gallery' => '12', 'newestvehicles_on' => '1', 'newestvehicles_limit' => '5', 'newestmods_on' => '1', 'newestmods_limit' => '5', 'mostmodded_on' => '1', 'mostmodded_limit' => '5', 'mostmoneyspent_on' => '1', 'mostmoneyspent_limit' => '5', 'mostviewed_on' => '1', 'mostviewed_limit' => '5', 'lastupdatedvehicles_on' => '1', 'lastupdatedvehicles_limit' => '5', 'lastupdatedvehiclesmain_on' => '1', 'lastupdatedvehiclesmain_limit' => '5', 'lastupdatedmods_on' => '1', 'lastupdatedmods_limit' => '5', 'lastcommented_on' => '1', 'lastcommented_limit' => '5', 'remote_timeout' => '60', 'browse_perms' => '*', 'interact_perms' => '*', 'add_perms' => '*', 'upload_perms' => '*', 'private_browse_perms' => '', 'private_interact_perms' => '', 'private_add_perms' => '', 'private_upload_perms' => '', 'private_add_quota' => '', 'private_upload_quota' => '', 'private_remote_quota' => '', 'menu_selection' => 'MAIN,BROWSE,SEARCH,INSURANCEREVIEW,GARAGEREVIEW,SHOPREVIEW,QUARTERMILE,ROLLINGROAD', 'featured_vehicle_from_block' => '', 'toprated_on' => '1', 'toprated_limit' => '5', 'topquartermile_on' => '1', 'topquartermile_limit' => '5', 'topdynorun_on' => '1', 'topdynorun_limit' => '5', 'enable_quartermile' => '1', 'enable_quartermile_approval' => '1', 'enable_business_approval' => '1', 'rating_permanent' => '0', 'rating_always_updateable' => '1', 'enable_rollingroad' => '1', 'enable_rollingroad_approval' => '1', 'enable_insurance' => '1', 'profile_thumbs' => '1', 'enable_user_submit_make' => '1', 'enable_user_submit_model' => '1', 'version' => '1.2.0', 'garage_images' => '1', 'quartermile_image_required' => '1', 'quartermile_image_required_limit' => '13', 'dynorun_image_required' => '1', 'dynorun_image_required_limit' => '300', 'items_pending' => '0', 'private_deny_perms' => '', 'minimum_ratings_required' => '5');
while( list($config_name, $config_value) = @each($params) )
{
	$config_sql = "SELECT count(config_name) as total
			FROM " . $table_prefix . "garage_config
			WHERE config_name ='".$config_name."'";

	if ( !($result = $db->sql_query($config_sql)) )
	{
		message_die(GENERAL_ERROR, 'Error Counting Config Values', '', __LINE__, __FILE__, $config_sql);
	}

	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	if ( $row['total'] != 1 )
	{
		$sql[] = "INSERT INTO " . $table_prefix . "garage_config (config_name, config_value) VALUES ('" . $config_name . "', '" . $config_value . "')";
	}
}

//We Need To Setup Field Order Since It Will Be Blank On Upgrade....
$sql2 = "SELECT * FROM " . $table_prefix . "garage_categories";
if ( !($result2 = $db->sql_query($sql2)) )
{
	message_die(GENERAL_ERROR, 'Could Select Business Data', '', __LINE__, __FILE__, $sql2);
}

$i = 1;
while( $row2 = $db->sql_fetchrow($result2) )
{
	$sql[] = "UPDATE " . $table_prefix . "garage_categories SET field_order = '$i' WHERE id = ".$row2['id'];
	$i++;
}

//We Need To Fill In 'attach_thumb_filesize', 'attach_thumb_width', 'attach_thumb_width', 'garage_id' for GARAGE_IMAGES_TABLE As Again It Will Be Blank On Upgrade And Needs Data...
//This Might Take A While As We Have To Process Each Image & Work Out Lots Of Info
$sql3 = "SELECT attach_id, attach_thumb_location FROM " . $table_prefix . "garage_images";
if ( !($result3 = $db->sql_query($sql3)) )
{
	message_die(GENERAL_ERROR, 'Could Select Image Data', '', __LINE__, __FILE__, $sql3);
}

while( $row3 = $db->sql_fetchrow($result3) )
{
	//Workout Thumb Image Details & Update DB
	if (file_exists($phpbb_root_path . GARAGE_UPLOAD_PATH . $row3['attach_thumb_location']))
	{
		$attach_thumb_filesize = filesize($phpbb_root_path . GARAGE_UPLOAD_PATH . $row3['attach_thumb_location']);
		$attach_thumb_imagesize = getimagesize($phpbb_root_path . GARAGE_UPLOAD_PATH . $row3['attach_thumb_location']);
		$sql[] = "UPDATE " . $table_prefix . "garage_images SET attach_thumb_width = '" . $attach_thumb_imagesize[0] . "' WHERE attach_id = " . $row3['attach_id'];
		$sql[] = "UPDATE " . $table_prefix . "garage_images SET attach_thumb_height = '" . $attach_thumb_imagesize[1] . "' WHERE attach_id = " . $row3['attach_id'];
		$sql[] = "UPDATE " . $table_prefix . "garage_images SET attach_thumb_filesize = '$attach_thumb_filesize' WHERE attach_id = " . $row3['attach_id'];
	}

	//Workout If Image Is Attached To Vehicle Gallery
	$sql4 = "SELECT gallery.garage_id
		FROM " . GARAGE_GALLERY_TABLE . " gallery
        		LEFT JOIN " . GARAGE_IMAGES_TABLE . " images ON images.attach_id = gallery.image_id 
		WHERE images.attach_id = " . $row3['attach_id'];
	if ( !($result4 = $db->sql_query($sql4)) )
	{
		message_die(GENERAL_ERROR, 'Could Select Image Data', '', __LINE__, __FILE__, $sql4);
	}
	$row4 = $db->sql_fetchrow($result4);
	if (!empty($row4['garage_id']))
	{
		$sql[] = "UPDATE " . $table_prefix . "garage_images SET garage_id = '".$row4['garage_id']."' WHERE attach_id = " . $row3['attach_id'];
		//Since We Found The Garage Entry We Need To Skip To Next Image
		continue;
	}

	//Workout If Image Is Attached To Vehicle Modification
	$sql5 = "SELECT mods.garage_id
		FROM " . GARAGE_MODS_TABLE . " mods
        		LEFT JOIN " . GARAGE_IMAGES_TABLE . " images ON images.attach_id = mods.image_id 
        	WHERE images.attach_id = " . $row3['attach_id'];
	if ( !($result5 = $db->sql_query($sql5)) )
	{
		message_die(GENERAL_ERROR, 'Could Select Image Data', '', __LINE__, __FILE__, $sql5);
	}
	$row5 = $db->sql_fetchrow($result5);
	if (!empty($row5['garage_id']))
	{
		$sql[] = "UPDATE " . $table_prefix . "garage_images SET garage_id = '".$row5['garage_id']."' WHERE attach_id = " . $row3['attach_id'];
		//Since We Found The Garage Entry We Need To Skip To Next Image
		continue;
	}

	//Workout If Image Is Attached To Vehicle Quartermile
	$sql6 = "SELECT qm.garage_id
		FROM " . GARAGE_QUARTERMILE_TABLE . " qm
        		LEFT JOIN " . GARAGE_IMAGES_TABLE . " images ON images.attach_id = qm.image_id 
		WHERE images.attach_id = " . $row3['attach_id'];
	if ( !($result6 = $db->sql_query($sql6)) )
	{
		message_die(GENERAL_ERROR, 'Could Select Image Data', '', __LINE__, __FILE__, $sql6);
	}
	$row6 = $db->sql_fetchrow($result6);
	if (!empty($row6['garage_id']))
	{
		$sql[] = "UPDATE " . $table_prefix . "garage_images SET garage_id = '".$row6['garage_id']."' WHERE attach_id = " . $row3['attach_id'];
		//Since We Found The Garage Entry We Need To Skip To Next Image
		continue;
	}

	//Workout If Image Is Attached To Vehicle Dynorun
	$sql7 = "SELECT rr.garage_id
		FROM " . GARAGE_ROLLINGROAD_TABLE . " rr
        		LEFT JOIN " . GARAGE_IMAGES_TABLE . " images ON images.attach_id = rr.image_id 
        	WHERE images.attach_id = " . $row3['attach_id'];
	if ( !($result7 = $db->sql_query($sql7)) )
	{
		message_die(GENERAL_ERROR, 'Could Select Image Data', '', __LINE__, __FILE__, $sql7);
	}
	$row7 = $db->sql_fetchrow($result7);
	if (!empty($row7['garage_id']))
	{
		$sql[] = "UPDATE " . $table_prefix . "garage_images SET garage_id = '".$row7['garage_id']."' WHERE attach_id = " . $row3['attach_id'];
		//Since We Found The Garage Entry We Need To Skip To Next Image
		continue;
	}

}

//Next Up We Need To Work Out The Weighted Rating Value Per Vehicle...Man This Upgrade Is Doing Lots Of Work!!!
$sql8 = "SELECT * FROM " . $table_prefix . "garage";
if ( !($result8 = $db->sql_query($sql8)) )
{
	message_die(GENERAL_ERROR, 'Could Select Business Data', '', __LINE__, __FILE__, $sql2);
}

$i = 1;
while( $row8 = $db->sql_fetchrow($result8) )
{
	//Count Votes This Vehicle Has Recived & Average Rating So Far
	$sql9 = "SELECT count(id) AS votes_recieved, AVG(rating) as average_rating
		FROM " . $table_prefix . "garage_rating
		WHERE garage_id = '".$row8['id']."'";

	if(!$result9 = $db->sql_query($sql9))
	{
		message_die(GENERAL_ERROR, 'Error Counting Vehicles', '', __LINE__, __FILE__, $sql);
	}

        $row9 = $db->sql_fetchrow($result9);
	$db->sql_freeresult($result9);

	//Count Votes This Vehicle Has Recived & Average Rating So Far
	$sql10 = "SELECT AVG(rating) as site_average
		FROM " . $table_prefix . "garage_rating";

	if(!$result10 = $db->sql_query($sql10))
	{
		message_die(GENERAL_ERROR, 'Error Counting Vehicles', '', __LINE__, __FILE__, $sql);
	}

        $row10 = $db->sql_fetchrow($result10);
	$db->sql_freeresult($result10);

	//Weighted Rating Formula We Use 'WR=(V/(V+M)) * R + (M/(V+M)) * C'
	$garage_config['minimum_rating'] = '5';
	$weighted_rating= ( $row9['votes_recieved'] / ( $row9['votes_recieved'] + $garage_config['minimum_rating'] ) ) * $row9['average_rating'] + ( $garage_config['minimum_rating'] / ( $row9['votes_recieved'] + $garage_config['minimum_rating'] ) ) * $row10['site_average'];

	$sql[] = "UPDATE ". $table_prefix ."garage SET weighted_rating = '$weighted_rating' WHERE id = '".$row8['id']."';";
}


//Now Process All SQL Statements We Have Built
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

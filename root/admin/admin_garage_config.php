<?php
/***************************************************************************
 *                              admin_garage_config.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id$
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

if( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['Garage']['Configuration'] = $filename;
	return;
}

// Let's set the root dir for phpBB
$phpbb_root_path = '../';
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);
require($phpbb_root_path . 'includes/functions_garage.' . $phpEx);
require($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_garage.' . $phpEx);

// Pull all config data
$sql = "SELECT * FROM " . GARAGE_CONFIG_TABLE;
if(!$result = $db->sql_query($sql))
{
	message_die(CRITICAL_ERROR, "Could not query garage config information", "", __LINE__, __FILE__, $sql);
}
else
{
	while( $row = $db->sql_fetchrow($result) )
	{
		$config_name = $row['config_name'];
		$config_value = $row['config_value'];
		$default_config[$config_name] = isset($HTTP_POST_VARS['submit']) ? str_replace("'", "\'", $config_value) : $config_value;
		$new[$config_name] = ( isset($HTTP_POST_VARS[$config_name]) ) ? $HTTP_POST_VARS[$config_name] : $default_config[$config_name];

		if( isset($HTTP_POST_VARS['submit']) )
		{

			$sql = "UPDATE " . GARAGE_CONFIG_TABLE . " SET
				config_value = '" . str_replace("\'", "''", $new[$config_name]) . "'
				WHERE config_name = '$config_name'";
			if( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, "Failed to update garage configuration for $config_name", "", __LINE__, __FILE__, $sql);
			}
		}
	}

//START MAKE RANDOM WORK
	$featured_vehicle_random = str_replace("\'", "''", trim($HTTP_POST_VARS['featured_vehicle_random']));
	if ($featured_vehicle_random == 'on')
	{
		$config_value = 'on';
		$config_name = 'featured_vehicle_random';
	}
	else
	{
		$config_value = 'off';
		$config_name = 'featured_vehicle_random';
	}

	if( isset($HTTP_POST_VARS['submit']) )
	{
		$sql = "UPDATE " . GARAGE_CONFIG_TABLE . " SET
			config_value = '$config_value'
			WHERE config_name = '$config_name'";
		if( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, "Failed to update garage configuration for $config_name", "", __LINE__, __FILE__, $sql);
		}
	}
//END OF MAKE RANDOM WORK

	if (isset($HTTP_POST_VARS['menu_selection'])) 
	{
		$menu = @implode(',', $HTTP_POST_VARS['menu_selection']);
		$selection = str_replace("\'", "''", $menu);
		$sql = "UPDATE ". GARAGE_CONFIG_TABLE ."
			SET config_value = '$selection'
			WHERE config_name = 'menu_selection'";
		if ( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not update menu', '', __LINE__, __FILE__, $sql);
		}
	}

	if ( (isset($HTTP_POST_VARS['featured_vehicle_from_block'])) AND (!empty($HTTP_POST_VARS['featured_vehicle_from_block'])) ) 
	{
		$sql = "UPDATE ". GARAGE_CONFIG_TABLE ."
			SET config_value = '".$HTTP_POST_VARS['featured_vehicle_from_block']."'
			WHERE config_name = 'featured_vehicle_from_block'";
		if ( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not update menu', '', __LINE__, __FILE__, $sql);
		}
	}


	if( isset($HTTP_POST_VARS['submit']) )
	{
		$message = $lang['Garage_Config_Updated'] . "<br /><br />" . sprintf($lang['Click_Return_Garage_Config'], "<a href=\"" . append_sid("admin_garage_config.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
	}
}


$template->set_filenames(array(
	"body" => "admin/garage_config.tpl")
);


$select_featured_by_block = "<select name='featured_vehicle_from_block' class='forminput'>";

if (!empty($garage_config['featured_vehicle_from_block']))
{
	$select_featured_by_block .= "<option value='".$garage_config['featured_vehicle_from_block']."' selected='selected'>".$garage_config['featured_vehicle_from_block']."</option>";
}
	
// You may add/removed/edit the currency listings here.
$select_featured_by_block .= "<option value=''>--------------</option>";
$select_featured_by_block .= "<option value='".$lang['Newest_Vehicles']."'>".$lang['Newest_Vehicles']."</option>";
$select_featured_by_block .= "<option value='".$lang['Last_Updated_Vehicles']."'>".$lang['Last_Updated_Vehicles']."</option>";
$select_featured_by_block .= "<option value='".$lang['Newest_Modifications']."'>".$lang['Newest_Modifications']."</option>";
$select_featured_by_block .= "<option value='".$lang['Last_Updated_Modifications']."'>".$lang['Last_Updated_Modifications']."</option>";
$select_featured_by_block .= "<option value='".$lang['Most_Modified_Vehicle']."'>".$lang['Most_Modified_Vehicle']."</option>";
$select_featured_by_block .= "<option value='".$lang['Most_Money_Spent']."'>".$lang['Most_Money_Spent']."</option>";
$select_featured_by_block .= "<option value='".$lang['Most_Viewed_Vehicle']."'>".$lang['Most_Viewed_Vehicle']."</option>";
$select_featured_by_block .= "<option value='".$lang['Latest_Vehicle_Comments']."'>".$lang['Latest_Vehicle_Comments']."</option>";
$select_featured_by_block .= "<option value='".$lang['Top_Quartermile_Runs']."'>".$lang['Top_Quartermile_Runs']."</option>";
$select_featured_by_block .= "<option value='".$lang['Top_Dyno_Runs']."'>".$lang['Top_Dyno_Runs']."</option>";
$select_featured_by_block .= "<option value='".$lang['Top_Rated_Vehicles']."'>".$lang['Top_Rated_Vehicles']."</option>";
$select_featured_by_block .= "</select>";

$template->assign_vars(array(
	'SELECT_BY_BLOCK' => $select_featured_by_block)
);

$template->assign_vars(array(

	// GENERAL VARS
	'S_GARAGE_CONFIG_ACTION' => append_sid('admin_garage_config.'.$phpEx),

	// GENERAL GARAGE CONFIG VARS
	'MAIN_CHECKED' => (preg_match("/MAIN/",$garage_config['menu_selection'])) ? 'selected="selected"' : '',
	'BROWSE_CHECKED' => (preg_match("/BROWSE/",$garage_config['menu_selection'])) ? 'selected="selected"' : '',
	'SEARCH_CHECKED' => (preg_match("/SEARCH/",$garage_config['menu_selection'])) ? 'selected="selected"' : '',
	'INSURANCE_CHECKED' => (preg_match("/INSURANCEREVIEW/",$garage_config['menu_selection'])) ? 'selected="selected"' : '',
	'GARAGE_CHECKED' => (preg_match("/GARAGEREVIEW/",$garage_config['menu_selection'])) ? 'selected="selected"' : '',
	'SHOP_CHECKED' => (preg_match("/SHOPREVIEW/",$garage_config['menu_selection'])) ? 'selected="selected"' : '',
	'QUARTERMILE_CHECKED' => (preg_match("/QUARTERMILE/",$garage_config['menu_selection'])) ? 'selected="selected"' : '',
	'ROLLINGROAD_CHECKED' => (preg_match("/ROLLINGROAD/",$garage_config['menu_selection'])) ? 'selected="selected"' : '',
	'CARS_PER_PAGE' => $new['cars_per_page'],
	'YEAR_START' => $new['year_start'],
	'YEAR_END' => $new['year_end'],
	'MAX_USER_CARS' => $new['max_user_cars'],
	'LATEST_UPDATED_VEHCILE_ALL_PAGES_ENABLED' => ($new['lastupdatedvehiclesmain_on'] == 1) ? 'checked="checked"' : '',
	'LATEST_UPDATED_VEHCILE_ALL_PAGES_DISABLED' => ($new['lastupdatedvehiclesmain_on'] == 0) ? 'checked="checked"' : '',
	'LASTUPDATEDVEHICLESMAIN_LIMIT' => $new['lastupdatedvehiclesmain_limit'],
	'ALLOW_CAR_HTML_ENABLED' => ($new['allow_html_car'] == 1) ? 'checked="checked"' : '',
	'ALLOW_CAR_HTML_DISABLED' => ($new['allow_html_car'] == 0) ? 'checked="checked"' : '',
	'ALLOW_MOD_HTML_ENABLED' => ($new['allow_html_mod'] == 1) ? 'checked="checked"' : '',
	'ALLOW_MOD_HTML_DISABLED' => ($new['allow_html_mod'] == 0) ? 'checked="checked"' : '',
	'GUESTBOOK_ENABLED' => ($new['enable_guestbooks'] == 1) ? 'checked="checked"' : '',
	'GUESTBOOK_DISABLED' => ($new['enable_guestbooks'] == 0) ? 'checked="checked"' : '',
	'USER_SUBMIT_MAKE_ENABLED' => ($new['enable_user_submit_make'] == 1) ? 'checked="checked"' : '',
	'USER_SUBMIT_MAKE_DISABLED' => ($new['enable_user_submit_make'] == 0) ? 'checked="checked"' : '',
	'USER_SUBMIT_MODEL_ENABLED' => ($new['enable_user_submit_model'] == 1) ? 'checked="checked"' : '',
	'USER_SUBMIT_MODEL_DISABLED' => ($new['enable_user_submit_model'] == 0) ? 'checked="checked"' : '',

	// GARAGE MAIN MENU FEATURES VARS
	'FEATURED_VEHICLE_ENABLED' => ($new['enable_featured_vehicle'] == 1) ? 'checked="checked"' : '',
	'FEATURED_VEHICLE_DISABLED' => ($new['enable_featured_vehicle'] == 0) ? 'checked="checked"' : '',
	'FEATURED_VEHICLE_RANDOM' => ($new['featured_vehicle_random'] == 'on' ) ? 'checked="checked"' : '',
	'FEATURED_VEHICLE_ID' => $new['featured_vehicle_id'],
	'FEATURED_VEHICLE_DESCRIPTION' => $new['featured_vehicle_description'],
	'DATE_FORMAT' => $new['date_format'],
	'NEWEST_VEHICLE_ON' => ($new['newestvehicles_on'] == 1) ? 'checked="checked"' : '',
	'NEWEST_VEHICLE_OFF' => ($new['newestvehicles_on'] == 0) ? 'checked="checked"' : '',
	'NEWESTVEHICLES_LIMIT' => $new['newestvehicles_limit'],
	'UPDATED_VEHICLE_ON' => ($new['lastupdatedvehicles_on'] == 1) ? 'checked="checked"' : '',
	'UPDATED_VEHICLE_OFF' => ($new['lastupdatedvehicles_on'] == 0) ? 'checked="checked"' : '',
	'LASTUPDATEDVEHICLES_LIMIT' => $new['lastupdatedvehicles_limit'],
	'NEWEST_MOD_ON' => ($new['newestmods_on'] == 1) ? 'checked="checked"' : '',
	'NEWEST_MOD_OFF' => ($new['newestmods_on'] == 0) ? 'checked="checked"' : '',
	'NEWESTMODS_LIMIT' => $new['newestmods_limit'],
	'UPDATED_MOD_ON' => ($new['lastupdatedmods_on'] == 1) ? 'checked="checked"' : '',
	'UPDATED_MOD_OFF' => ($new['lastupdatedmods_on'] == 0) ? 'checked="checked"' : '',
	'LASTUPDATEDMODS_LIMIT' => $new['lastupdatedmods_limit'],
	'MOST_MODDED_ON' => ($new['mostmodded_on'] == 1) ? 'checked="checked"' : '',
	'MOST_MODDED_OFF' => ($new['mostmodded_on'] == 0) ? 'checked="checked"' : '',
	'MOSTMODDED_LIMIT' => $new['mostmodded_limit'],
	'MOST_SPENT_ON' => ($new['mostmoneyspent_on'] == 1) ? 'checked="checked"' : '',
	'MOST_SPENT_OFF' => ($new['mostmoneyspent_on'] == 0) ? 'checked="checked"' : '',
	'MOSTMONEYSPENT_LIMIT' => $new['mostmoneyspent_limit'],
	'MOST_VIEWED_ON' => ($new['mostviewed_on'] == 1) ? 'checked="checked"' : '',
	'MOST_VIEWED_OFF' => ($new['mostviewed_on'] == 0) ? 'checked="checked"' : '',
	'MOSTVIEWED_LIMIT' => $new['mostviewed_limit'],
	'MOST_COMMENTED_ON' => ($new['lastcommented_on'] == 1) ? 'checked="checked"' : '',
	'MOST_COMMENTED_OFF' => ($new['lastcommented_on'] == 0) ? 'checked="checked"' : '',
	'LASTCOMMENTED_LIMIT' => $new['lastcommented_limit'],
	'TOPQUARTERMILE_LIMIT' => $new['topquartermile_limit'],
	'TOP_QUARTERMILE_ON' => ($new['topquartermile_on'] == 1) ? 'checked="checked"' : '',
	'TOP_QUARTERMILE_OFF' => ($new['topquartermile_on'] == 0) ? 'checked="checked"' : '',
	'TOPDYNORUN_LIMIT' => $new['topdynorun_limit'],
	'TOP_DYNORUN_ON' => ($new['topdynorun_on'] == 1) ? 'checked="checked"' : '',
	'TOP_DYNORUN_OFF' => ($new['topdynorun_on'] == 0) ? 'checked="checked"' : '',
	'TOPRATED_LIMIT' => $new['toprated_limit'],
	'TOP_RATED_ON' => ($new['toprated_on'] == 1) ? 'checked="checked"' : '',
	'TOP_RATED_OFF' => ($new['toprated_on'] == 0) ? 'checked="checked"' : '',

	// GARAGE IMAGE CONFIGURATION VAR
	'GARAGE_IMAGES_ENABLED' => ($new['garage_images'] == 1) ? 'checked="checked"' : '',
	'GARAGE_IMAGES_DISABLED' => ($new['garage_images'] == 0) ? 'checked="checked"' : '',
	'ALLOW_IMAGE_UPLOAD_ENABLED' => ($new['allow_image_upload'] == 1) ? 'checked="checked"' : '',
	'ALLOW_IMAGE_UPLOAD_DISABLED' => ($new['allow_image_upload'] == 0) ? 'checked="checked"' : '',
	'ALLOW_MOD_IMAGES_ENABLED' => ($new['allow_mod_image'] == 1) ? 'checked="checked"' : '',
	'ALLOW_MOD_IMAGES_DISABLED' => ($new['allow_mod_image'] == 0) ? 'checked="checked"' : '',
	'SHOW_MOD_IMAGES_IN_GALLERY_ENABLED' => ($new['show_mod_gallery'] == 1) ? 'checked="checked"' : '',
	'SHOW_MOD_IMAGES_IN_GALLERY_DISABLED' => ($new['show_mod_gallery'] == 0) ? 'checked="checked"' : '',
	'LIMIT_MOD_GALLERY' => $new['limit_mod_gallery'],
	'ALLOW_REMOTE_IMAGES_ENABLED' => ($new['allow_image_url'] == 1) ? 'checked="checked"' : '',
	'ALLOW_REMOTE_IMAGES_DISABLED' => ($new['allow_image_url'] == 0) ? 'checked="checked"' : '',
	'REMOTE_TIMEOUT' => $new['remote_timeout'],
	'MAX_CAR_IMAGES' => $new['max_car_images'],
	'MAX_IMAGE_KBYTES' => $new['max_image_kbytes'],
	'MAX_IMAGE_RESOLUTION' => $new['max_image_resolution'],
	'CONVERT_PATH' => $new['convert_path'],
	'CONVERT_OPTIONS' => $new['convert_options'],
	'THUMBNAIL_RESOLUTION' => $new['thumbnail_resolution'],
	'THUMBNAIL_IMAGEMAGICK' => ($new['thumbnail_type'] == 'imagemagick') ? 'checked="checked"' : '',
	'THUMBNAIL_GD' => ($new['thumbnail_type'] == 'gd') ? 'checked="checked"' : '',
	'SHOW_THUMBS_IN_PROFILE_ENABLED' => ($new['profile_thumbs'] == 1) ? 'checked="checked"' : '',
	'SHOW_THUMBS_IN_PROFILE_DISABLED' => ($new['profile_thumbs'] == 0) ? 'checked="checked"' : '',

	// GARAGE QUARTERMILE CONFIGURATION VAR
	'QUARTERMILE_ENABLED' => ($new['enable_quartermile'] == 1) ? 'checked="checked"' : '',
	'QUARTERMILE_DISABLED' => ($new['enable_quartermile'] == 0) ? 'checked="checked"' : '',
	'QUARTERMILE_APPROVAL_ENABLED' => ($new['enable_quartermile_approval'] == 1) ? 'checked="checked"' : '',
	'QUARTERMILE_APPROVAL_DISABLED' => ($new['enable_quartermile_approval'] == 0) ? 'checked="checked"' : '',

	// GARAGE ROLLINGROAD CONFIGURATION VAR
	'ROLLINGROAD_ENABLED' => ($new['enable_rollingroad'] == 1) ? 'checked="checked"' : '',
	'ROLLINGROAD_DISABLED' => ($new['enable_rollingroad'] == 0) ? 'checked="checked"' : '',
	'ROLLINGROAD_APPROVAL_ENABLED' => ($new['enable_rollingroad_approval'] == 1) ? 'checked="checked"' : '',
	'ROLLINGROAD_APPROVAL_DISABLED' => ($new['enable_rollingroad_approval'] == 0) ? 'checked="checked"' : '',

	// GARAGE INSURANCE CONFIGURATION VAR
	'INSURANCE_ENABLED' => ($new['enable_insurance'] == 1) ? 'checked="checked"' : '',
	'INSURANCE_DISABLED' => ($new['enable_insurance'] == 0) ? 'checked="checked"' : '',

	// GARAGE RATING CONFIGURATION VAR
	'RATING_PERMANENT_ENABLED' => ($new['rating_permanent'] == 1) ? 'checked="checked"' : '',
	'RATING_PERMANENT_DISABLED' => ($new['rating_permanent'] == 0) ? 'checked="checked"' : '',
	'RATING_ALWAYS_UPDATEABLE_ENABLED' => ($new['rating_always_updateable'] == 1) ? 'checked="checked"' : '',
	'RATING_ALWAYS_UPDATEABLE_DISABLED' => ($new['rating_always_updateable'] == 0) ? 'checked="checked"' : '',

	// GARAGE BUSINESS CONFIGURATION VAR
	'BUSINESS_APPROVAL_ENABLED' => ($new['enable_business_approval'] == 1) ? 'checked="checked"' : '',
	'BUSINESS_APPROVAL_DISABLED' => ($new['enable_business_approval'] == 0) ? 'checked="checked"' : '',

	// GARAGE INSURANCE CONFIGURATION VAR
	'MILEAGE_ENABLED' => ($new['enable_mileage'] == 1) ? 'checked="checked"' : '',
	'MILEAGE_DISABLED' => ($new['enable_mileage'] == 0) ? 'checked="checked"' : '',

	// ALL LANGUAGE VARS
	'L_MAX_MOD_IMAGES_VIEWED' => $lang['Max_Mod_Images_Viewed'],
	'L_GARAGE_CONFIG_TITLE' => $lang['Garage_Config_Title'],
	'L_GARAGE_CONFIG_EXPLAIN' => $lang['Garage_Config_Explain'],
	'L_CARS_PER_PAGE' => $lang['Cars_Per_Page'],
	'L_MAX_USER_CARS' => $lang['Max_User_Cars'],
	'L_ENABLE_HTML_CAR' => $lang['Enable_Html_Car'],
	'L_ENABLE_HTML_MOD' => $lang['Enable_Html_Mod'],
	'L_YEAR_START' => $lang['Year_Start'],
	'L_YEAR_END' => $lang['Year_End'],
	'L_ENABLE_GUESTBOOK' => $lang['Allow_Guestbooks'],
	'L_ENABLE_USER_SUBMIT_MAKE' => $lang['Enable_User_Submit_Make'],
	'L_ENABLE_USER_SUBMIT_MODEL' => $lang['Enable_User_Submit_Model'],
	'L_LATEST_UPDATED_VEHCILE_ALL_PAGES' => $lang['Latest_Updated_Vehicle_All_Pages'],
	'L_FEATURED_VEHICLE' => $lang['Enable_Featured_Vehicle'],
	'L_FEATURED_VEHICLE_ID' => $lang['Featured_Vehcile_ID'],
	'L_FEATURED_VEHICLE_DESCRIPTION' => $lang['Featured_Vehcile_Description'],
	'L_OR_RANDOM' => $lang['Or_Random'],
	'L_OR_TOP_VEHICLE_IN' => $lang['Or_Top_Vehicle_In'],
	'L_DATE_FORMAT' => $lang['Date_Format'],
	'L_ENABLE_NEWEST_VEHICLE' => $lang['Enable_Newest_Vehicle'],
	'L_ENABLE_UPDATED_VEHICLE' => $lang['Enable_Updated_Vehicle'],
	'L_ENABLE_NEWEST_MODIFICATIONS' => $lang['Enable_Newest_Modifications'],
	'L_ENABLE_UPDATED_MODIFICAIONS' => $lang['Enable_Updated_Modifications'],
	'L_ENABLE_MOST_MODDED' => $lang['Enable_Most_Modded'],
	'L_ENABLE_MOST_SPENT' => $lang['Enable_Most_Spent'],
	'L_ENABLE_MOST_VIEWED' => $lang['Enable_Most_Viewed'],
	'L_ENABLE_MOST_COMMENTED' => $lang['Enable_Latest_Commented'],
	'L_ENABLE_TOP_QUARTERMILE' => $lang['Enable_Top_Quartermile'],
	'L_ENABLE_TOP_DYNORUN' => $lang['Enable_Top_Dynorun'],
	'L_ENABLE_TOP_RATED' => $lang['Enable_Top_Rated'],
	'L_MAX_MOST_VIEWED' => $lang['Max_Most_Viewed'],
	'L_MAX_MOD_VIEWED' => $lang['Max_Mod_Viewed'],
	'L_MAX_COMMENT_VIEWED' => $lang['Max_Comment_Viewed'],
	'L_MAX_TOP_QUARTERMILE' => $lang['Max_Top_Quartermile'],
	'L_MAX_TOP_DYNORUN' => $lang['Max_Top_Dynorun'],
	'L_GARAGE_FEATURES' => $lang['Garage_Features'],
	'L_GARAGE_CONFIG' => $lang['Garage_Config'],
	'L_DISABLED' => $lang['Disabled'],
	'L_ENABLED' => $lang['Enabled'],
	'L_YES' => $lang['Yes'],
	'L_NO' => $lang['No'],
	'L_SUBMIT' => $lang['Submit'],
	'L_GARAGE_IMAGE_FEATURES' => $lang['Garage_Image_Features'],
	'L_ALLOW_IMAGE_UPLOAD' => $lang['Allow_Image_Upload'],
	'L_ALLOW_MOD_IMAGES' => $lang['Allow_Mod_Images'],
	'L_SHOW_MOD_IMAGES_IN_GALLERY' => $lang['Show_Mod_Images_In_Gallery'],
	'L_ALLOW_REMOTE_IMAGES' => $lang['Allow_Remote_Images'],
	'L_REMOTE_TIMEOUT' => $lang['Remote_Timeout'],
	'L_MAX_IMAGES_PER_GALLERY' => $lang['Max_Images_Per_Gallery'],
	'L_MAX_IMAGE_SIZE' => $lang['Max_Image_Size'],
	'L_MAX_IMAGE_RESOLUTION' => $lang['Max_Image_Resolution'],
	'L_CREATE_THUMBS_WITH' => $lang['Create_Thumbs_With'],
	'L_GD' => $lang['GD'],
	'L_IM' => $lang['IM'],
	'L_GARAGE_ENABLE_IMAGES' => $lang['Garage_Enable_Images'],
	'L_PATH_TO_CONVERT' => $lang['Path_To_Convert'],
	'L_CONVERT_OPTIONS' => $lang['Convert_Options'],
	'L_THUMBNAIL_RESOLUTION' => $lang['Thumbnail_Resolution'],
	'L_MENU_SELECTION' => $lang['Menu_Selection'],
	'L_MAIN_MENU' => $lang['Main_Menu'],
	'L_PROFILE_INTEGRATION' => $lang['Profile_Integration'],
	'L_BROWSE_GARAGE' => $lang['Browse_Garage'],
	'L_SEARCH_GARAGE' => $lang['Search_Garage'],
	'L_INSURANCE_REVIEW' => $lang['Insurance_Summary'],
	'L_GARAGE_REVIEW' => $lang['Garage_Review'],
	'L_SHOP_REVIEW' => $lang['Shop_Review'],
	'L_QUARTERMILE_TABLE' => $lang['Quartermile_Table'],
	'L_ROLLINGROAD_TABLE' => $lang['Rollingroad_Table'],
	'L_GARAGE_QUARTERMILE_FEATURES' => $lang['Garage_Quartermile_Features'],
	'L_GARAGE_BUSINESS_FEATURES' => $lang['Garage_Business_Features'],
	'L_GARAGE_ROLLINGROAD_FEATURES' => $lang['Garage_Rollingroad_Features'],
	'L_GARAGE_INSURANCE_FEATURES' => $lang['Garage_Insurance_Features'],
	'L_GARAGE_MILEAGE_FEATURES' => $lang['Garage_Mileage_Features'],
	'L_ENABLE_INSURANCE' => $lang['Enable_Insurance'],
	'L_ENABLE_MILEAGE' => $lang['Enable_Mileage'],
	'L_ENABLE_QUARTERMILE' => $lang['Enable_Quartermile'],
	'L_ENABLE_ROLLINGROAD' => $lang['Enable_Rollingroad'],
	'L_REQUIRE_QUARTERMILE_APPROVAL' => $lang['Require_Quartermile_Approval'],
	'L_REQUIRE_ROLLINGROAD_APPROVAL' => $lang['Require_Rollingroad_Approval'],
	'L_REQUIRE_BUSINESS_APPROVAL' => $lang['Require_Business_Approval'],
	'L_GARAGE_RATING_FEATURES' => $lang['Garage_Rating_Features'],
	'L_RATING_PERMANENT' => $lang['Rating_Permanent'],
	'L_RATING_ALWAYS_UPDATEABLE' => $lang['Rating_Always_Updateable'],
	'L_RESET' => $lang['Reset'])
);

$template->pparse("body");

include('./page_footer_admin.'.$phpEx);

?>

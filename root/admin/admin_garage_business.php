<?php
/***************************************************************************
 *                              admin_garage_business.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: admin_garage_business.php,v 0.1.1 20/07/2005 20:47:20 poynesmo Exp $
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
	$module['Garage']['Business'] = $filename;
	return;
}

// Let's set the root dir for phpBB
$phpbb_root_path = '../';
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);
require($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_garage.' . $phpEx);

//Build All Garage Functions For $garage_lib->
require($phpbb_root_path . 'includes/functions_garage.' . $phpEx);

//Get All String Parameters And Make Safe
$params = array('mode' => 'mode');
while( list($var, $param) = @each($params) )
{
	if ( !empty($HTTP_POST_VARS[$param]) || !empty($HTTP_GET_VARS[$param]) )
	{
		$$var = ( !empty($HTTP_POST_VARS[$param]) ) ? str_replace("\'", "''", trim(htmlspecialchars($HTTP_POST_VARS[$param]))) : str_replace("\'", "''", trim(htmlspecialchars($HTTP_GET_VARS[$param])));
	}
	else
	{
		$$var = '';
	}
}

switch($mode)
{
	case 'insert_business':

		//Get All Data Posted And Make It Safe To Use
		$params = array('name', 'address', 'telephone', 'fax', 'website', 'email', 'opening_hous', 'insurance', 'garage', 'retail_shop', 'web_shop');
		$data = $garage_lib->process_post_vars($params);
		$data['pending'] = ($garage_config['enable_business_approval'] == '1') ? 1 : 0 ;
		$data['insurance'] = ($data['insurance'] == 'on') ? 1 : 0 ;
		$data['garage'] = ($data['garage'] == 'on') ? 1 : 0 ;
		$data['retail_shop'] = ($data['retail_shop'] == 'on') ? 1 : 0 ;
		$data['web_shop'] = ($data['web_shop'] == 'on') ? 1 : 0 ;
		//Check They Entered http:// In The Front Of The Link
		if ( (!preg_match( "/^http:\/\//i", $data['website'])) AND (!empty($data['website'])) )
		{
			$data['website'] = "http://".$data['website'];
		}

		//Checks All Required Data Is Present
		$params = array('name');
		$garage_lib->check_required_vars($params);

		//Update The DB With Data Acquired
		$garage_lib->insert_business($data);

		$message = $lang['New_Business_Created'] . "<br /><br />" . sprintf($lang['Click_Return_Garage_Business'], "<a href=\"" . append_sid("admin_garage_business.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
				
		break;

	case 'update_business':

		//Get All Data Posted And Make It Safe To Use
		$params = array('BUS_ID', 'name', 'address', 'telephone', 'fax', 'website', 'email', 'opening_hours', 'insurance', 'garage', 'retail_shop', 'web_shop');
		$data = $garage_lib->process_post_vars($params);
		$data['pending'] = ($garage_config['enable_business_approval'] == '1') ? 1 : 0 ;
		$data['insurance'] = ($data['insurance'] == 'on') ? 1 : 0 ;
		$data['garage'] = ($data['garage'] == 'on') ? 1 : 0 ;
		$data['retail_shop'] = ($data['retail_shop'] == 'on') ? 1 : 0 ;
		$data['web_shop'] = ($data['web_shop'] == 'on') ? 1 : 0 ;
		//Check They Entered http:// In The Front Of The Link
		if ( (!preg_match( "/^http:\/\//i", $data['website'])) AND (!empty($data['website'])) )
		{
			$data['website'] = "http://".$data['website'];
		}

		//Checks All Required Data Is Present
		$params = array('name');
		$garage_lib->check_required_vars($params);

		//Update The DB With Data Acquired
		$garage_lib->update_business($data);

		$message = $lang['Business_Updated'] . "<br /><br />" . sprintf($lang['Click_Return_Garage_Business'], "<a href=\"" . append_sid("admin_garage_business.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
		
		break;

	case 'delete_business':

		//Get All Data Posted And Make It Safe To Use
		$params = array('id');
		$data = $garage_lib->process_post_vars($params);

		$sql = "DELETE FROM ". GARAGE_BUSINESS_TABLE ."
			WHERE id = ".$data['id'];

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Delete Business', '', __LINE__, __FILE__, $sql);
		}

		$sql = "UPDATE ". GARAGE_MODS_TABLE ." SET `business_id` = NULL WHERE business_id = ".$data['id'];

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not create new make', '', __LINE__, __FILE__, $sql);
		}

		$sql = "UPDATE ". GARAGE_MODS_TABLE ." SET `install_business_id` = NULL WHERE install_business_id = ".$data['id'];

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not create new make', '', __LINE__, __FILE__, $sql);
		}

		// Return a message...
		$message = $lang['Business_Deleted'] . "<br /><br />" . sprintf($lang['Click_Return_Garage_Business'], "<a href=\"" . append_sid("admin_garage_business.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
				
		break;	

	case 'edit_business':

		//Get All Data Posted And Make It Safe To Use
		$params = array('id');
		$data = $garage_lib->process_post_vars($params);

		$sql = "SELECT * 
			FROM ".GARAGE_BUSINESS_TABLE."
			WHERE id = ".$data['id'];
	
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not select model data', '', __LINE__, __FILE__, $sql);
		}

		$business = $db->sql_fetchrow($result);
		$name = $business['title'];
		$address = $business['address'];
		$telephone = $business['telephone'];
		$fax = $business['fax'];
		$website = $business['website'];
		$email = $business['email'];
		$opening_hours = $business['opening_hours'];
		$insurance = $business['insurance'];
		$garage = $business['garage'];
		$retail_shop = $business['retail_shop'];
		$web_shop = $business['web_shop'];
		$insurance_checked = ( $insurance == TRUE ) ? 'checked="checked"' : '' ;
		$garage_checked = ( $garage == TRUE ) ? 'checked="checked"' : '' ;
		$retail_checked = ( $retail_shop == TRUE ) ? 'checked="checked"' : '' ;
		$web_checked = ( $web_shop == TRUE ) ? 'checked="checked"' : '' ;
	
		$template->set_filenames(array(
			"body" => "admin/garage_business_edit.tpl")
		);

		$template->assign_vars(array(
			'L_BUSINESS_NAME' => $lang['Business_Name'],
			'L_EDIT_BUSINESS' => $lang['Edit_Business'],
			'L_INSURANCE' => $lang['Insurance'],
			'L_GARAGE' => $lang['Garage'],
			'L_RETAIL_SHOP' => $lang['Retail_Shop'],
			'L_WEB_SHOP' => $lang['Web_Shop'],
			'L_BUSINESS_TYPE' => $lang['Business_Type'],
			'L_BUSINESS_OPENING_HOURS' => $lang['Business_Opening_Hours'],
			'L_BUSINESS_EMAIL' => $lang['Business_Email'],
			'L_BUSINESS_FAX_NO' => $lang['Business_Fax_No'],
			'L_BUSINESS_TELEPHONE_NO' => $lang['Business_Telephone_No'],
			'L_BUSINESS_ADDRESS' => $lang['Business_Address'],
			'L_BUSINESS_WEBSITE' => $lang['Business_Website'],
			'S_GARAGE_MODELS_ACTION' => append_sid('admin_garage_business.'.$phpEx),
			'NAME' => $name,
			'ADDRESS' => $address,
			'TELEPHONE' => $telephone,
			'FAX' => $fax,
			'WEBSITE' => $website,
			'EMAIL' => $email,
			'OPENING_HOURS' => $opening_hours,
			'INSURANCE_CHECKED' => $insurance_checked,
			'GARAGE_CHECKED' => $garage_checked,
			'RETAIL_CHECKED' => $retail_checked,
			'WEB_CHECKED' => $web_checked,
			'ID' => $data['id'],)
		);

			$template->pparse("body");

		break;

	default:

		$garage_lib->build_business_list_html('','');

		$sql = "SELECT id,make 
			FROM " . GARAGE_MAKES_TABLE . "
			ORDER BY make ASC";
	
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not select makes data', '', __LINE__, __FILE__, $sql);
		}
	
		$make_list = '';
	
		if($db->sql_numrows($result) != 0)
		{
			$template->assign_block_vars('makes_exist', array());
	
			while($row = $db->sql_fetchrow($result))
			{
				$make_list .= '<option value="'.$row['id'].'">'.strip_tags(htmlspecialchars($row['make'])).'</option>';
			}
		}

		$template->set_filenames(array(
			"body" => "admin/garage_business.tpl")
		);

		$template->assign_vars(array(
			'L_BUSINESS_NAME' => $lang['Business_Name'],
			'L_DELETE_BUSINESS' => $lang['Delete_Business'],
			'L_EDIT_EXISTING_BUSINESS' => $lang['Edit_Existing_Business'],
			'L_ADD_NEW_BUSINESS' => $lang['Add_New_Business'],
			'L_INSURANCE' => $lang['Insurance'],
			'L_GARAGE' => $lang['Garage'],
			'L_RETAIL_SHOP' => $lang['Retail_Shop'],
			'L_WEB_SHOP' => $lang['Web_Shop'],
			'L_BUSINESS_TYPE' => $lang['Business_Type'],
			'L_BUSINESS_OPENING_HOURS' => $lang['Business_Opening_Hours'],
			'L_BUSINESS_EMAIL' => $lang['Business_Email'],
			'L_BUSINESS_FAX_NO' => $lang['Business_Fax_No'],
			'L_BUSINESS_TELEPHONE_NO' => $lang['Business_Telephone_No'],
			'L_BUSINESS_ADDRESS' => $lang['Business_Address'],
			'L_BUSINESS_WEBSITE' => $lang['Business_Website'],
			'L_GARAGE_BUSINESS_TITLE' => $lang['Garage_Business_Title'],
			'L_GARAGE_BUSINESS_EXPLAIN' => $lang['Garage_Business_Explain'],
			'L_ADD_MAKE' => $lang['Add_Make'],
			'L_ADD_MAKE_BUTTON' => $lang['Add_Make_Button'],
			'L_MODIFY_MAKE' => $lang['Modify_Make'],
			'L_MODIFY_MAKE_BUTTON' => $lang['Modify_Make_Button'],
			'L_DELETE_MAKE' => $lang['Delete_Make'],
			'L_DELETE_MAKE_BUTTON' => $lang['Delete_Make_Button'],
			'L_ADD_MODEL' => $lang['Add_Model'],
			'L_ADD_MODEL_BUTTON' => $lang['Add_Model_Button'],
			'L_MODIFY_MODEL' => $lang['Modify_Model'],
			'L_CHOOSE_MODIFY_MODEL_BUTTON' => $lang['Choose_Modify_Model_Button'],
			'L_DELETE_MODEL' => $lang['Delete_Model'],
			'L_CHOOSE_DELETE_MODEL_BUTTON' => $lang['Choose_Delete_Model_Button'],
			'L_VEHICLE_MAKE' => $lang['Vehicle_Make'],
			'L_VEHICLE_MODEL' => $lang['Vehicle_Model'],
			'L_CHANGE_TO' => $lang['Change_To'],
			'S_GARAGE_MODELS_ACTION' => append_sid('admin_garage_models.'.$phpEx),
			'MAKE_LIST' => $make_list,)
		);

		$template->pparse("body");

}

include('./page_footer_admin.'.$phpEx);

?>

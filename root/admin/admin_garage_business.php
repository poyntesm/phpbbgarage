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
include($phpbb_root_path . 'includes/functions_garage.' . $phpEx);

if( isset( $HTTP_POST_VARS['mode'] ) || isset( $HTTP_GET_VARS['mode'] ) )
{
	$mode = ( isset($HTTP_POST_VARS['mode']) ) ? $HTTP_POST_VARS['mode'] : $HTTP_GET_VARS['mode'];
}
else
{
	$mode = '';
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

		$id = ( isset($HTTP_GET_VARS['id']) ) ? intval($HTTP_GET_VARS['id']) : $HTTP_POST_VARS['id'];
		$name = str_replace("\'", "''", trim($HTTP_POST_VARS['name']));
		$address = str_replace("\'", "''", trim($HTTP_POST_VARS['address']));
		$telephone = str_replace("\'", "''", trim($HTTP_POST_VARS['telephone']));
		$fax = str_replace("\'", "''", trim($HTTP_POST_VARS['fax']));
		$website = str_replace("\'", "''", trim($HTTP_POST_VARS['website']));
		$email = str_replace("\'", "''", trim($HTTP_POST_VARS['email']));
		$opening_hours = str_replace("\'", "''", trim($HTTP_POST_VARS['opening_hours']));
		$insurance = str_replace("\'", "''", trim($HTTP_POST_VARS['insurance']));
		$insurance = ($insurance == 'on') ? 1 : 0 ;
		$garage = str_replace("\'", "''", trim($HTTP_POST_VARS['garage']));
		$garage = ($garage == 'on') ? 1 : 0 ;
		$retail_shop = str_replace("\'", "''", trim($HTTP_POST_VARS['retail_shop']));
		$retail_shop = ($retail_shop == 'on') ? 1 : 0 ;
		$web_shop = str_replace("\'", "''", trim($HTTP_POST_VARS['web_shop']));
		$web_shop = ($web_shop == 'on') ? 1 : 0 ;
		
		$sql = "UPDATE ". GARAGE_BUSINESS_TABLE ." 
			SET name = '$name', address = '$address', telephone = '$telephone', fax = '$fax', website = '$website', email = '$email', opening_hours = '$opening_hours', insurance = '$insurance', garage = '$garage', retail_shop = '$retail_shop', web_shop = '$web_shop'
			WHERE id = $id";
	
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not create new make', '', __LINE__, __FILE__, $sql);
		}

		$message = $lang['Business_Updated'] . "<br /><br />" . sprintf($lang['Click_Return_Garage_Business'], "<a href=\"" . append_sid("admin_garage_business.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
		
		break;

	case 'confirm_delete':

		$id = intval($HTTP_GET_VARS['id']);

		$sql = "SELECT id, name
			FROM ". GARAGE_BUSINESS_TABLE ."
			ORDER BY id ASC";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not query Garage Business Data', '', __LINE__, __FILE__, $sql);
		}

		$cat_found = FALSE;
		while( $row = $db->sql_fetchrow($result) )
		{
			if( $row['id'] == $id )
			{
				$thiscat = $row;
				$cat_found = TRUE;
			}
			else
			{
				$catrow[] = $row;
			}
		}
		if( $cat_found == FALSE )
		{
			message_die(GENERAL_ERROR, 'The requested business does not existed');
		}

		$select_to = '<select name="target">';
		$select_to .= '<option value="">---------</option>';
		for ($i = 0; $i < count($catrow); $i++)
		{
			$select_to .= '<option value="'. $catrow[$i]['id'] .'">'. $catrow[$i]['name'] .'</option>';
		}
		$select_to .= '</select>';

		$template->set_filenames(array(
			'body' => 'admin/garage_confirm_delete.tpl')
		);

		$template->assign_vars(array(
			'S_GARAGE_ACTION' => append_sid("admin_garage_business.$phpEx?id=$id"),
			'L_DELETE' => $lang['Delete_Business'],
			'L_DELETE_EXPLAIN' => $lang['Delete_Business_Explain'],
			'L_TITLE' => $lang['Business'],
			'S_TITLE' => $thiscat['name'],
			'L_MOVE_CONTENTS' => $lang['Move_contents'],
			'L_MOVE_DELETE' => $lang['Move_and_Delete'],
			'L_REQUIRED' => $lang['Required'],
			'L_REMOVE' => $lang['Remove_Business'],
			'L_MOVE_DELETE' => $lang['Move_Delete_Business'],
			'L_MOVE_DELETE_BUTTON' => $lang['Move_Delete_Business_Button'],
			'MOVE_TO_LIST' => $select_to)
		);

		$template->pparse('body');

		include('./page_footer_admin.'.$phpEx);

		break;

	case 'delete':

		//Get All Data Posted And Make It Safe To Use
		$params = array('id', 'target');
		$data = $garage_lib->process_post_vars($params);

		//Set Message For Missing
		$message = '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_business.$phpEx?mode=confirm_delete&id=".$data['id']."") . '">'. $lang['Missing_Required_Data']. "<br /><br />" . sprintf($lang['Click_Return_Garage_Business'], "<a href=\"" . append_sid("admin_garage_business.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		//Checks All Required Data Is Present
		$params = array('id', 'target');
		$garage_lib->check_acp_required_vars($params, $message);

		echo "In Delete";
		exit;

		$id = ( isset($HTTP_POST_VARS['id'] ) ) ? $HTTP_POST_VARS['id'] : rawurldecode($HTTP_GET_VARS['id']);

		$sql = "DELETE FROM ". GARAGE_BUSINESS_TABLE ."
			WHERE id = '$id'";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not create new make', '', __LINE__, __FILE__, $sql);
		}

		$sql = "UPDATE ". GARAGE_MODS_TABLE ." SET `business_id` = NULL WHERE business_id = '$id'";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not create new make', '', __LINE__, __FILE__, $sql);
		}

		$sql = "UPDATE ". GARAGE_MODS_TABLE ." SET `install_business_id` = NULL WHERE install_business_id = '$id'";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not create new make', '', __LINE__, __FILE__, $sql);
		}

		// Return a message...
		$message = $lang['Business_Deleted'] . "<br /><br />" . sprintf($lang['Click_Return_Garage_Business'], "<a href=\"" . append_sid("admin_garage_business.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
				
		break;	

	case 'set_pending':

		//Get All Data Posted And Make It Safe To Use
		$params = array('id');
		$data = $garage_lib->process_post_vars($params);

		//Checks All Required Data Is Present
		$params = array('id');
		$garage_lib->check_required_vars($params);

		$garage_lib->update_single_field(GARAGE_BUSINESS_TABLE,'pending',1,'id',$data['id']);

		$message = '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_business.$phpEx") . '">'. $lang['Business_Updated'] . "<br /><br />" . sprintf($lang['Click_Return_Garage_Business'], "<a href=\"" . append_sid("admin_garage_business.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);

		break;

	case 'set_approved':

		//Get All Data Posted And Make It Safe To Use
		$params = array('id');
		$data = $garage_lib->process_post_vars($params);

		//Checks All Required Data Is Present
		$params = array('id');
		$garage_lib->check_required_vars($params);

		$garage_lib->update_single_field(GARAGE_BUSINESS_TABLE,'pending',0,'id',$data['id']);

		$message = '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_business.$phpEx") . '">'. $lang['Business_Updated'] . "<br /><br />" . sprintf($lang['Click_Return_Garage_Business'], "<a href=\"" . append_sid("admin_garage_business.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);

		break;

	default:

		$template->set_filenames(array(
			'body' => 'admin/garage_business.tpl')
		);

		$template->assign_vars(array(
			'L_GARAGE_BUSINESS_TITLE' => $lang['Garage_Business_Title'],
			'L_GARAGE_BUSINESS_EXPLAIN' => $lang['Garage_Business_Explain'],
			'L_ADD_NEW_BUSINESS' => $lang['Add_New_Business'],
			'L_EDIT_BUSINESS' => $lang['Edit_Business'],
			'L_BUSINESS_NAME' => $lang['Business_Name'],
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
			'L_NAME' => $lang['Name'],
			'L_EDIT' => $lang['Edit'],
			'L_STATUS' => $lang['Status'],
			'L_DELETE' => $lang['Delete'],
			'L_CREATE_CATEGORY' => $lang['Create_category'],
			'L_CAT_TITLE' => $lang['New_Category_Title'],
			'L_PANEL_TITLE' => $lang['Create_category'],
			'L_EMPTY_TITLE' => $lang['Empty_Title'],
			'SHOW' => '<img src="../' . $images['garage_show_details'] . '" alt="'.$lang['Show_Details'].'" title="'.$lang['Show_Details'].'" border="0" />',
			'HIDE' => '<img src="../' . $images['garage_hide_details'] . '" alt="'.$lang['Hide_Details'].'" title="'.$lang['Hide_Details'].'" border="0" />',
			'S_GARAGE_MODE_UPDATE' => append_sid("admin_garage_business.$phpEx?mode=update"),
			'S_GARAGE_MODE_NEW' => append_sid("admin_garage_business.$phpEx?mode=new"))
		);

		$data = $garage_lib->select_business_data('');

		for( $i = 0; $i < count($data); $i++ )
		{
			$status_mode =  ( $data[$i]['pending'] == TRUE ) ? 'set_approved' : 'set_pending' ;

			$delete_url = append_sid("admin_garage_business.$phpEx?mode=confirm_delete&amp;id=" . $data[$i]['id']);
			$status_url = append_sid("admin_garage_business.$phpEx?mode=$status_mode&amp;id=" . $data[$i]['id']);

			$delete = ( $garage_config['enable_images'] ) ? '<img src="../' . $images['garage_delete'] . '" alt="'.$lang['Delete'].'" title="'.$lang['Delete'].'" border="0" />' : $lang['Delete'] ;
			$status = ( $garage_config['enable_images'] ) ? '<img src="../' . $images['garage_'.$status_mode] . '" alt="'.$lang[$status_mode].'" title="'.$lang[$status_mode].'" border="0" />' : $lang[$status_mode];

			$template->assign_block_vars('business', array(
				'COLOR' => ($i % 2) ? 'row1' : 'row2',
				'ID' => $data[$i]['id'],
				'NAME' => $data[$i]['name'],
				'ADDRESS' => $data[$i]['address'],
				'TELEPHONE' => $data[$i]['telephone'],
				'FAX' => $data[$i]['fax'],
				'WEBSITE' => $data[$i]['website'],
				'EMAIL' => $data[$i]['email'],
				'OPENING_HOURS' => $data[$i]['opening_hours'],
				'INSURANCE_CHECKED' => ( $data[$i]['insurance'] == TRUE ) ? 'CHECKED' : '' ,
				'GARAGE_CHECKED' => ( $data[$i]['garage'] == TRUE ) ? 'CHECKED' : '' ,
				'RETAIL_CHECKED' => ( $data[$i]['retail_shop'] == TRUE ) ? 'CHECKED' : '' ,
				'WEB_CHECKED' => ( $data[$i]['web_shop'] == TRUE ) ? 'CHECKED' : '' ,
				'DELETE' => $delete,
				'STATUS' => $status,
				'U_DELETE' => $delete_url,
				'U_STATUS' => $status_url)
			);
			$template->assign_block_vars('business.detail', array());
		}

		$template->pparse('body');

		include('./page_footer_admin.'.$phpEx);

		break;

}

include('./page_footer_admin.'.$phpEx);

?>

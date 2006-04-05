<?php
/***************************************************************************
 *                              admin_garage_business.php
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
		$params = array('title', 'address', 'telephone', 'fax', 'website', 'email', 'opening_hours', 'insurance', 'garage', 'retail_shop', 'web_shop');
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

		//Set Message For Missing Data
		$message = '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_business.$phpEx") . '">'. $lang['Missing_Required_Data']. "<br /><br />" . sprintf($lang['Click_return_garage_business'], "<a href=\"" . append_sid("admin_garage_business.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		//Checks All Required Data Is Present
		$params = array('title');
		$garage_lib->check_acp_required_vars($params , $message);

		//Update The DB With Data Acquired
		$garage_lib->insert_business($data);

		$message = '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_business.$phpEx") . '">' . $lang['New_Business_Created'] . "<br /><br />" . sprintf($lang['Click_Return_Garage_Business'], "<a href=\"" . append_sid("admin_garage_business.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
				
		break;

	case 'update_business':

		//Get All Data Posted And Make It Safe To Use
		$params = array('id', 'title', 'address', 'telephone', 'fax', 'website', 'email', 'opening_hours', 'insurance', 'garage', 'retail_shop', 'web_shop');
		$data = $garage_lib->process_post_vars($params);
		$data['pending'] = ($garage_config['enable_business_approval'] == '1') ? 1 : 0 ;
		$data['insurance'] = ($data['insurance'] == 'true') ? 1 : 0 ;
		$data['garage'] = ($data['garage'] == 'true') ? 1 : 0 ;
		$data['retail_shop'] = ($data['retail_shop'] == 'true') ? 1 : 0 ;
		$data['web_shop'] = ($data['web_shop'] == 'true') ? 1 : 0 ;

		//Check They Entered http:// In The Front Of The Link
		if ( (!preg_match( "/^http:\/\//i", $data['website'])) AND (!empty($data['website'])) )
		{
			$data['website'] = "http://".$data['website'];
		}

		$garage_lib->update_business($data);
		
		$message = '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_business.$phpEx") . '">' . $lang['Business_Updated'] . "<br /><br />" . sprintf($lang['Click_Return_Garage_Business'], "<a href=\"" . append_sid("admin_garage_business.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
		
		break;

	case 'confirm_delete':

		$template->set_filenames(array(
			'body' => 'admin/garage_confirm_delete.tpl')
		);

		//Store Data Of Business We Are Deleting For Use In Action Variable
		$params = array('id');
		$data = $garage_lib->process_post_vars($params);
		$data = $garage_lib->select_business_data($data['id']);

		//Get All Business Data To Build Dropdown Of Where To Move Linked Items To
		$all_data = $garage_lib->select_business_data('');

		//Build Dropdown Options For Where To Love Linked Items To
		for ($i = 0; $i < count($all_data); $i++)
		{
			//Do Not List Business We Are Deleting..
			if ( $data[0]['id'] == $all_data[$i]['id'] )
			{
				continue;
			}
			$select_to .= '<option value="'. $all_data[$i]['id'] .'">'. $all_data[$i]['title'] .'</option>';
		}

		//Send Needed Info To Template
		$template->assign_vars(array(
			'S_GARAGE_ACTION' => append_sid("admin_garage_business.$phpEx?id=".$data[0]['id']),
			'S_TITLE' => $data[0]['title'],
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

		//Move Any Existing Items To New Target Then Delete Business
		$garage_lib->update_single_field(GARAGE_MODS_TABLE,'business_id',$data['target'],'business_id',$data['id']);
		$garage_lib->update_single_field(GARAGE_MODS_TABLE,'install_business_id',$data['target'],'install_business_id',$data['id']);
		$garage_lib->delete_rows(GARAGE_BUSINESS_TABLE,'id',$data['id']);

		// Return a message...
		$message = '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_business.$phpEx") . '">' . $lang['Business_Deleted'] . "<br /><br />" . sprintf($lang['Click_Return_Garage_Business'], "<a href=\"" . append_sid("admin_garage_business.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
				
		break;	

	case 'set_pending':

		//Get All Data Posted And Make It Safe To Use
		$params = array('id');
		$data = $garage_lib->process_int_vars($params);

		$garage_lib->update_single_field(GARAGE_BUSINESS_TABLE,'pending',1,'id',$data['id']);

		$message = '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_business.$phpEx") . '">'. $lang['Business_Updated'] . "<br /><br />" . sprintf($lang['Click_Return_Garage_Business'], "<a href=\"" . append_sid("admin_garage_business.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);

		break;

	case 'set_approved':

		//Get All Data Posted And Make It Safe To Use
		$params = array('id');
		$data = $garage_lib->process_int_vars($params);

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
			'L_TYPE' => $lang['Type'],
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
			'L_CLICK_TO_SHOW' => $lang['Show_Details'],
			'L_CLICK_TO_HIDE' => $lang['Hide_Details'],
			'SHOW' => '<img src="../' . $images['garage_show_details'] . '" alt="'.$lang['Show_Details'].'" title="'.$lang['Show_Details'].'" border="0" />',
			'HIDE' => '<img src="../' . $images['garage_hide_details'] . '" alt="'.$lang['Hide_Details'].'" title="'.$lang['Hide_Details'].'" border="0" />',
			'S_GARAGE_MODE_UPDATE' => append_sid("admin_garage_business.$phpEx?mode=update_business"),
			'S_GARAGE_MODE_NEW' => append_sid("admin_garage_business.$phpEx?mode=insert_business"))
		);

		//Get All The Business Data
		$data = $garage_lib->select_business_data('');

		for( $i = 0; $i < count($data); $i++ )
		{
			$status_mode =  ( $data[$i]['pending'] == TRUE ) ? 'set_approved' : 'set_pending' ;

			$delete_url = append_sid("admin_garage_business.$phpEx?mode=confirm_delete&amp;id=".$data[$i]['id']);
			$status_url = append_sid("admin_garage_business.$phpEx?mode=$status_mode&amp;id=".$data[$i]['id']);

			$update_url = '<a href="javascript:update('.$data[$i]['id'].')"><img src="../' . $images['garage_edit'] . '" alt="'.$lang['Rename'].'" title="'.$lang['Rename'].'" border="0" /></a>';

			$delete = ( $garage_config['garage_images'] ) ? '<img src="../' . $images['garage_delete'] . '" alt="'.$lang['Delete'].'" title="'.$lang['Delete'].'" border="0" />' : $lang['Delete'] ;
			$status = ( $garage_config['garage_images'] ) ? '<img src="../' . $images['garage_'.$status_mode] . '" alt="'.$lang[$status_mode].'" title="'.$lang[$status_mode].'" border="0" />' : $lang[$status_mode];

			//Work Out Type Of Business
			$type ='';
			if ( $data[$i]['insurance'] == '1' )
			{
		       	 	$type = $lang['Insurance'] ;
			}
			if ( ($data[$i]['web_shop'] == '1') OR ($data[$i]['retail_shop'] == '1') )
			{
				if (empty($type))
				{
					$type = $lang['Shop'] ;
				}
				else
				{
					$type .= ", " . $lang['Shop'] ;
				}
			}
			if ( $data[$i]['garage'] == '1' )
			{
				if (empty($type))
				{
					$type = $lang['Garage'] ;
				}
				else
				{
					$type .= ", " . $lang['Garage'] ;
				}
			}

			$template->assign_block_vars('business', array(
				'COLOR' => ($i % 2) ? 'row1' : 'row2',
				'ID' => $data[$i]['id'],
				'TITLE' => $data[$i]['title'],
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
				'TYPE' => $type,
				'U_UPDATE' => $update_url,
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

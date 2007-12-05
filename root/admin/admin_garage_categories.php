<?php
/***************************************************************************
 *                              admin_garage_categories.php
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
	$module['Garage']['Categories'] = $filename;
	return;
}

// Let's set the root dir for phpBB
$phpbb_root_path = '../';
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);
require($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_garage.' . $phpEx);

//Build All Garage Classes e.g $garage_images->
require($phpbb_root_path . 'includes/class_garage.' . $phpEx);
require($phpbb_root_path . 'includes/class_garage_admin.' . $phpEx);
require($phpbb_root_path . 'includes/class_garage_modification.' . $phpEx);

$params = array('mode' => 'mode');
while( list($var, $param) = @each($params) )
{
	$$var = '';
	if ( !empty($HTTP_POST_VARS[$param]) || !empty($HTTP_GET_VARS[$param]) )
	{
		$$var = ( !empty($HTTP_POST_VARS[$param]) ) ? str_replace("\'", "''", trim(htmlspecialchars($HTTP_POST_VARS[$param]))) : str_replace("\'", "''", trim(htmlspecialchars($HTTP_GET_VARS[$param])));
	}
}

//Lets Setup Messages We Might Need...Just Easier On The Eye Doing This Seperatly
$missing_data_message = '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_categories.$phpEx?mode=confirm_delete&id=".$data['id']."") . '">'. $lang['Missing_Required_Data']. "<br /><br />" . sprintf($lang['Click_return_garage_category'], "<a href=\"" . append_sid("admin_garage_category.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");
$category_created_message = '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_categories.$phpEx") . '">'. $lang['New_category_created'] . "<br /><br />" . sprintf($lang['Click_return_garage_category'], "<a href=\"" . append_sid("admin_garage_categories.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");
$category_updated_message = '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_categories.$phpEx") . '">'. $lang['Category_Updated'] . "<br /><br />" . sprintf($lang['Click_return_garage_category'], "<a href=\"" . append_sid("admin_garage_categories.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");
$category_deleted_message = '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_categories.$phpEx") . '">'. $lang['Category_Deleted'] . "<br /><br />" . sprintf($lang['Click_return_garage_category'], "<a href=\"" . append_sid("admin_garage_categories.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");
$category_order_message = '<meta http-equiv="refresh" content="2;url=' . append_sid("admin_garage_categories.$phpEx") . '">'. $lang['Category_Order_Updated'] . "<br /><br />" . sprintf($lang['Click_return_garage_category'], "<a href=\"" . append_sid("admin_garage_categories.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

switch ( $mode )
{
	case 'insert_category':

		//Count Current Categories..So We Can Work Out Order
		$count = count($garage->select_all_category_data());

		//Get posting variables
		$str_params = array('title');
		$data = $garage->process_str_vars($str_params);
		$data['field_order'] = $count + 1;

		//Insert New Category Into DB
		$garage_admin->insert_category($data);

		//Return a message...
		message_die(GENERAL_MESSAGE, $category_created_message);

		break;

	case 'update_category':

		// Get posting variables
		$int_params = array('id');
		$int_data = $garage->process_int_vars($int_params);
		$str_params = array('title');
		$str_data = $garage->process_str_vars($str_params);
		$data = $garage->merge_int_str_data($int_data, $str_data);

		// Now we update this row
		$garage->update_single_field(GARAGE_CATEGORIES_TABLE, 'title', $data['title'], 'id', $data['id']);

		// Return a message...
		message_die(GENERAL_MESSAGE, $category_updated_message);

		break;

	case 'confirm_delete':

		//Store ID Of Category We Are Deleting For Use In Action Variable
		$int_params = array('id');
		$data = $garage->process_int_vars($int_params);
		$data = $garage->select_category_data($data['id']);
		$all_data = $garage->select_all_category_data();

		//Build Dropdown Options For Where To Love Linked Items To
		for ($i = 0; $i < count($all_data); $i++)
		{
			//Do Not List Category We Are Deleting..
			if ( $data['id'] == $all_data[$i]['id'] )
			{
				continue;
			}
			$select_to .= '<option value="'. $all_data[$i]['id'] .'">'. $all_data[$i]['title'] .'</option>';
		}

		$template->set_filenames(array(
			'body' => 'admin/garage_confirm_delete.tpl')
		);

		$template->assign_vars(array(
			'S_GARAGE_ACTION' => append_sid("admin_garage_categories.$phpEx?mode=delete_category&amp;id=".$data['id']),
			'L_DELETE' => $lang['Delete_Category'],
			'L_DELETE_EXPLAIN' => $lang['Delete_Category_Explain'],
			'L_TITLE' => $lang['category'],
			'S_TITLE' => $data['title'],
			'L_MOVE_CONTENTS' => $lang['Move_contents'],
			'L_MOVE_DELETE' => $lang['Move_and_Delete'],
			'L_REQUIRED' => $lang['Required'],
			'L_REMOVE' => $lang['Remove_Category'],
			'L_MOVE_DELETE' => $lang['Move_Delete_Category'],
			'L_MOVE_DELETE_BUTTON' => $lang['Delete_Category'],
			'L_OR' => $lang['Or'],
			'L_DELETE_PERMENANTLY' => $lang['Delete_Permenantly'],
			'MOVE_TO_LIST' => $select_to)
		);

		$template->pparse('body');

		include('./page_footer_admin.'.$phpEx);

		break;

	case 'delete_category':

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('id', 'target', 'permenant');
		$data = $garage->process_int_vars($int_params);

		//If Set Delete Permentantly
		if ($data['permenant'] == '1')
		{
			//Delete It Without Looking For Child Objects!!
			$garage->delete_rows(GARAGE_CATEGORIES_TABLE, 'id', $data['id']);

			// Return a message...
			message_die(GENERAL_MESSAGE, $category_deleted_message);
		}

		//Checks All Required Data Is Present
		$params = array('id', 'target');
		$garage->check_acp_required_vars($params, $missing_data_message);

		//Move All Modifications To New Category
		$garage->update_single_field(GARAGE_MODS_TABLE, 'category_id', $data['target'], 'category_id', $data['id']);
		
		//This Category Is Now Emptied, We Can Delete It!
		$garage->delete_rows(GARAGE_CATEGORIES_TABLE, 'id', $data['id']);

		//Return a message...
		message_die(GENERAL_MESSAGE, $category_deleted_message);

		break;

	case 'move_up':

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('order');
		$data = $garage->process_int_vars($int_params);
		
		$field_order = $data['order'];
		$order_total = $field_order * 2 + (($mode == 'move_up') ? -1 : 1);

		$sql = 'UPDATE ' . GARAGE_CATEGORIES_TABLE . "
			SET field_order = $order_total - field_order
			WHERE field_order IN ($field_order, " . (($mode == 'move_up') ? $field_order - 1 : $field_order + 1) . ')';

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not create new Garage Category', '', __LINE__, __FILE__, $sql);
		}

		// Return a message...
		message_die(GENERAL_MESSAGE, $category_order_message);

		break;

	case 'move_down':

		$int_params = array('order');
		$data = $garage->process_int_vars($int_params);

		$field_order = $data['order'];
		$order_total = $field_order * 2 + (($mode == 'move_up') ? -1 : 1);

		$sql = 'UPDATE ' . GARAGE_CATEGORIES_TABLE . "
			SET field_order = $order_total - field_order
			WHERE field_order IN ($field_order, " . (($mode == 'move_up') ? $field_order - 1 : $field_order + 1) . ')';

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not create new Garage Category', '', __LINE__, __FILE__, $sql);
		}

		//Return a message...
		message_die(GENERAL_MESSAGE, $category_order_message);

		break;

	default:

		$template->set_filenames(array(
			'body' => 'admin/garage_category.tpl')
		);

		$template->assign_vars(array(
			'L_GARAGE_CAT_TITLE' => $lang['Garage_Categories_Title'],
			'L_GARAGE_CAT_EXPLAIN' => $lang['Garage_Categories_Explain'],
			'L_NAME' => $lang['Name'],
			'L_RENAME' => $lang['Rename'],
			'L_DELETE' => $lang['Delete'],
			'L_REORDER' => $lang['Reorder'],
			'L_CREATE_CATEGORY' => $lang['Create_category'],
			'L_CAT_TITLE' => $lang['New_Category_Title'],
			'L_PANEL_TITLE' => $lang['Create_category'],
			'L_EMPTY_TITLE' => $lang['Empty_Title'],
			'S_GARAGE_MODE_RENAME' => append_sid("admin_garage_categories.$phpEx?mode=update_category"),
			'S_GARAGE_MODE_NEW' => append_sid("admin_garage_categories.$phpEx?mode=insert_category"))
		);

		//Get All Category Data...
		$data = $garage->select_all_category_data();

		//Process Each Category
		for( $i = 0; $i < count($data); $i++ )
		{
			$order = $i + 1;
			//Build The Actual URL's
			$rename_url = $data[$i]['id'];
			$delete_url = append_sid("admin_garage_categories.$phpEx?mode=confirm_delete&amp;id=" . $data[$i]['id']);
			$move_up_url = append_sid("admin_garage_categories.$phpEx?mode=move_up&amp;id=" . $data[$i]['id']. "&amp;order=$order");
			$move_down_url = append_sid("admin_garage_categories.$phpEx?mode=move_down&amp;id=" . $data[$i]['id']. "&amp;order=$order");

			//Build How The URL's Will Look..Users Might Have Images Turned Off
			$rename_url_dsp = '<img src="../'.$images['garage_edit'].'" alt="'.$lang['Rename'].'" title="'.$lang['Rename'].'" border="0" />';
			$delete_url_dsp = '<img src="../'.$images['garage_delete'].'" alt="'.$lang['Delete'].'" title="'.$lang['Delete'].'" border="0" />';
			$move_up_url_dsp = '<img src="../'.$images['garage_move_up'].'" alt="'.$lang['Move_Up'].'" title="'.$lang['Move_Up'].'" border="0" />';
			$move_down_url_dsp = '<img src="../'.$images['garage_move_down'].'" alt="'.$lang['Move_Down'].'" title="'.$lang['Move_Down'].'" border="0" />';

			$template->assign_block_vars('catrow', array(
				'COLOR' => ($i % 2) ? 'row1' : 'row2',
				'ID' => $data[$i]['id'],
				'TITLE' => $data[$i]['title'],
				'RENAME' => $rename_url_dsp,
				'DELETE' => $delete_url_dsp,
				'MOVE_UP' => $move_up_url_dsp,
				'MOVE_DOWN' => $move_down_url_dsp,
				'U_RENAME' => $rename_url,
				'U_DELETE' => $delete_url,
				'U_MOVE_UP' => $move_up_url,
				'U_MOVE_DOWN' => $move_down_url)
			);
		}

		$template->pparse('body');

		include('./page_footer_admin.'.$phpEx);

		break;
}

?>

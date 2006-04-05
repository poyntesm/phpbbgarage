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

//Build All Garage Functions For $garage_lib->
require($phpbb_root_path . 'includes/functions_garage.' . $phpEx);

if( isset( $HTTP_POST_VARS['mode'] ) || isset( $HTTP_GET_VARS['mode'] ) )
{
	$mode = ( isset($HTTP_POST_VARS['mode']) ) ? $HTTP_POST_VARS['mode'] : $HTTP_GET_VARS['mode'];
}
else
{
	$mode = '';
}

switch ( $mode )
{
	case 'confirm_delete':

		//Get All The Categories Data
		$data = $garage_lib->select_category_data();

		//Build Drop Down Options Where To Move Linked Items
		for ($i = 0; $i < count($data); $i++)
		{
			$select_to .= '<option value="'. $data[$i]['id'] .'">'. $data[$i]['title'] .'</option>';
		}

		//Store ID Of Category We Are Deleting For Use In Action Variable
		$params = array('id');
		$data = $garage_lib->process_post_vars($params);
		$data = $garage_lib->select_category_data($data['id']);

		$template->set_filenames(array(
			'body' => 'admin/garage_confirm_delete.tpl')
		);

		$template->assign_vars(array(
			'S_GARAGE_ACTION' => append_sid("admin_garage_categories.$phpEx?id=".$data['id']),
			'L_DELETE' => $lang['Delete_Category'],
			'L_DELETE_EXPLAIN' => $lang['Delete_Category_Explain'],
			'L_TITLE' => $lang['category'],
			'S_TITLE' => $data['title'],
			'L_MOVE_CONTENTS' => $lang['Move_contents'],
			'L_MOVE_DELETE' => $lang['Move_and_Delete'],
			'L_REQUIRED' => $lang['Required'],
			'L_REMOVE' => $lang['Remove_Category'],
			'L_MOVE_DELETE' => $lang['Move_Delete_Category'],
			'L_MOVE_DELETE_BUTTON' => $lang['Move_Delete_Category_Button'],
			'MOVE_TO_LIST' => $select_to)
		);

		$template->pparse('body');

		include('./page_footer_admin.'.$phpEx);

		break;

	case 'new':

		$count = $garage_lib->count_mod_catgories();
		
		if( isset($HTTP_POST_VARS['title']) )
		{
			// Get posting variables
			$title = str_replace("\'", "''", htmlspecialchars(trim($HTTP_POST_VARS['title'])));

		// Here we insert a new row into the db
		$sql = "INSERT INTO ". GARAGE_CATEGORIES_TABLE ." (title,field_order)
			VALUES ('$title', $field_order)";
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not create new Garage Category', '', __LINE__, __FILE__, $sql);
		}

		// Return a message...
		$message = '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_categories.$phpEx") . '">'. $lang['New_category_created'] . "<br /><br />" . sprintf($lang['Click_return_garage_category'], "<a href=\"" . append_sid("admin_garage_categories.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
	}

		break;

	case 'rename':

		// Get posting variables
		$id = (intval($HTTP_POST_VARS['category_id']));
		$title = str_replace("\'", "''", htmlspecialchars(trim($HTTP_POST_VARS['title_'.$id])));

		// Now we update this row
		$sql = "UPDATE ". GARAGE_CATEGORIES_TABLE ."
				SET title = '$title'
				WHERE id = $id";
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not update this Garage Category', '', __LINE__, __FILE__, $sql);
		}

		// Return a message...
		$message = '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_categories.$phpEx") . '">'. $lang['Category_Updated'] . "<br /><br />" . sprintf($lang['Click_return_garage_category'], "<a href=\"" . append_sid("admin_garage_categories.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");
	
		message_die(GENERAL_MESSAGE, $message);

		break;

	case 'delete':

		//Get All Data Posted And Make It Safe To Use
		$params = array('id', 'target');
		$data = $garage_lib->process_post_vars($params);

		//Set Message For Missing Data
		$message = '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_categories.$phpEx?mode=confirm_delete&id=".$data['id']."") . '">'. $lang['Missing_Required_Data']. "<br /><br />" . sprintf($lang['Click_return_garage_category'], "<a href=\"" . append_sid("admin_garage_category.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		//Checks All Required Data Is Present
		$params = array('id', 'target');
		$garage_lib->check_acp_required_vars($params, $message);
		
		$sql = "UPDATE ". GARAGE_MODS_TABLE ."
			SET category_id = ".$data['target']."
			WHERE category_id = ".$data['id'];
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not update this Category content', '', __LINE__, __FILE__, $sql);
		}

		// This category is now emptied, we can remove it!
		$sql = "DELETE FROM ". GARAGE_CATEGORIES_TABLE ."
			WHERE id = ".$data['id'];
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not delete this Category', '', __LINE__, __FILE__, $sql);
		}

		// Return a message...
		$message = '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_categories.$phpEx") . '">'. $lang['Category_Deleted'] . "<br /><br />" . sprintf($lang['Click_return_garage_category'], "<a href=\"" . append_sid("admin_garage_categories.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);

		break;

	case 'move_up':

		//Get All Data Posted And Make It Safe To Use
		$params = array('order');
		$data = $garage_lib->process_post_vars($params);
		
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
		$message = '<meta http-equiv="refresh" content="2;url=' . append_sid("admin_garage_categories.$phpEx") . '">'. $lang['Category_Order_Updated'] . "<br /><br />" . sprintf($lang['Click_return_garage_category'], "<a href=\"" . append_sid("admin_garage_categories.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);

		break;

	case 'move_down':

		$params = array('order');
		$data = $garage_lib->process_post_vars($params);

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
		$message = '<meta http-equiv="refresh" content="2;url=' . append_sid("admin_garage_categories.$phpEx") . '">'. $lang['Category_Order_Updated'] . "<br /><br />" . sprintf($lang['Click_return_garage_category'], "<a href=\"" . append_sid("admin_garage_categories.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);

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
			'S_GARAGE_MODE_RENAME' => append_sid("admin_garage_categories.$phpEx?mode=rename"),
			'S_GARAGE_MODE_NEW' => append_sid("admin_garage_categories.$phpEx?mode=new"))
		);

		$data = $garage_lib->select_category_data();

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

<?php
/***************************************************************************
 *                              admin_garage_categories.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: admin_garage_categories.php,v 0.1.1 20/07/2005 20:47:20 poynesmo Exp $
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

if( !isset($HTTP_POST_VARS['mode']) )
{
	if( !isset($HTTP_GET_VARS['action']) )
	{
		$template->set_filenames(array(
			'body' => 'admin/garage_category.tpl')
		);

		$template->assign_vars(array(
			'L_GARAGE_CAT_TITLE' => $lang['Garage_Categories_Title'],
			'L_GARAGE_CAT_EXPLAIN' => $lang['Garage_Categories_Explain'],
			'S_GARAGE_ACTION' => append_sid("admin_garage_categories.$phpEx"),
			'L_EDIT' => $lang['Edit'],
			'L_DELETE' => $lang['Delete'],
			'S_MODE' => 'new',
			'L_CREATE_CATEGORY' => $lang['Create_category'],
			'L_CAT_TITLE' => $lang['New_Category_Title'],
			'L_PANEL_TITLE' => $lang['Create_category'])
		);

		$sql = "SELECT *
			FROM " . GARAGE_CATEGORIES_TABLE . "
			ORDER BY id ASC";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not query Garage Categories information', '', __LINE__, __FILE__, $sql);
		}
		while ($row = $db->sql_fetchrow($result))
		{
			$catrow[] = $row;
		}

		for( $i = 0; $i < count($catrow); $i++ )
		{
			$template->assign_block_vars('catrow', array(
				'COLOR' => ($i % 2) ? 'row1' : 'row2',
				'TITLE' => $catrow[$i]['title'],
				'S_EDIT_ACTION' => append_sid("admin_garage_categories.$phpEx?action=edit&amp;id=" . $catrow[$i]['id']),
				'S_DELETE_ACTION' => append_sid("admin_garage_categories.$phpEx?action=delete&amp;id=" . $catrow[$i]['id'])
				)
			);
		}

		$template->pparse('body');

		include('./page_footer_admin.'.$phpEx);
	}
	else
	{
		if( $HTTP_GET_VARS['action'] == 'edit' )
		{
			$id = intval($HTTP_GET_VARS['id']);

			$sql = "SELECT *
					FROM ". GARAGE_CATEGORIES_TABLE ."
					WHERE id = '$id'";
			if(!$result = $db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, 'Could not query Garage Categories information', '', __LINE__, __FILE__, $sql);
			}
			if( $db->sql_numrows($result) == 0 )
			{
				message_die(GENERAL_ERROR, 'The requested category did not existed');
			}
			$catrow = $db->sql_fetchrow($result);

			$template->set_filenames(array(
				'body' => 'admin/garage_category_edit.tpl')
			);

			$template->assign_vars(array(
				'L_GARAGE_CAT_TITLE' => $lang['Garage_Categories_Title'],
				'L_GARAGE_CAT_EXPLAIN' => $lang['Garage_Categories_Explain'],
				'L_GARAGE_TITLE' => $lang['Category_Title'],
				'L_CAT_TITLE' => $lang['New_Category_Title'],
				'L_DISABLED' => $lang['Disabled'],
				'L_PANEL_TITLE' => $lang['Edit_Category'],

				'S_CAT_TITLE' => $catrow['title'],
				'S_MODE' => 'edit',
				'S_GARAGE_ACTION' => append_sid("admin_garage_categories.$phpEx?id=$id"))
			);

			$template->pparse('body');

			include('./page_footer_admin.'.$phpEx);
		}
		else if( $HTTP_GET_VARS['action'] == 'delete' )
		{
			$id = intval($HTTP_GET_VARS['id']);

			$sql = "SELECT id, title, image_id
					FROM ". GARAGE_CATEGORIES_TABLE ."
					ORDER BY id ASC";
			if(!$result = $db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, 'Could not query Garage Categories information', '', __LINE__, __FILE__, $sql);
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
				message_die(GENERAL_ERROR, 'The requested category is not existed');
			}

			$select_to = '<select name="target">';
			for ($i = 0; $i < count($catrow); $i++)
			{
				$select_to .= '<option value="'. $catrow[$i]['id'] .'">'. $catrow[$i]['title'] .'</option>';
			}
			$select_to .= '</select>';

			$template->set_filenames(array(
				'body' => 'admin/garage_category_delete.tpl')
			);

			$template->assign_vars(array(
				'S_GARAGE_ACTION' => append_sid("admin_garage_categories.$phpEx?id=$id"),
				'L_CAT_DELETE' => $lang['Delete_Category'],
				'L_CAT_DELETE_EXPLAIN' => $lang['Delete_Category_Explain'],
				'L_CAT_TITLE' => $lang['category'],
				'S_CAT_TITLE' => $thiscat['title'],
				'L_MOVE_CONTENTS' => $lang['Move_contents'],
				'L_MOVE_DELETE' => $lang['Move_and_Delete'],
				'L_REQUIRED' => $lang['Required'],
				'L_REMOVE_CATEGORY' => $lang['Remove_Category'],
				'L_MOVE_DELETE_CATEGORY' => $lang['Move_Delete_Category'],
				'L_MOVE_DELETE_CATEGORY_BUTTON' => $lang['Move_Delete_Category_Button'],
				'S_SELECT_TO' => $select_to)
			);

			$template->pparse('body');

			include('./page_footer_admin.'.$phpEx);
		}
	}
}
else
{
	if( $HTTP_POST_VARS['mode'] == 'new' )
	{
		if( isset($HTTP_POST_VARS['title']) )
		{
			// Get posting variables
			$title = str_replace("\'", "''", htmlspecialchars(trim($HTTP_POST_VARS['title'])));

			// Here we insert a new row into the db
			$sql = "INSERT INTO ". GARAGE_CATEGORIES_TABLE ." (title)
					VALUES ('$title')";
			if(!$result = $db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, 'Could not create new Garage Category', '', __LINE__, __FILE__, $sql);
			}

			// Return a message...
			$message = $lang['New_category_created'] . "<br /><br />" . sprintf($lang['Click_return_garage_category'], "<a href=\"" . append_sid("admin_garage_categories.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

			message_die(GENERAL_MESSAGE, $message);
		}
	}
	else if( $HTTP_POST_VARS['mode'] == 'edit' )
	{
		// Get posting variables
		$id = intval($HTTP_GET_VARS['id']);
		$title = str_replace("\'", "''", htmlspecialchars(trim($HTTP_POST_VARS['title'])));

		// Now we update this row
		$sql = "UPDATE ". GARAGE_CATEGORIES_TABLE ."
				SET title = '$title'
				WHERE id = $id";
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not update this Garage Category', '', __LINE__, __FILE__, $sql);
		}

		// Return a message...
		$message = $lang['Category_updated'] . "<br /><br />" . sprintf($lang['Click_return_garage_category'], "<a href=\"" . append_sid("admin_garage_categories.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
	}
	else if( $HTTP_POST_VARS['mode'] == 'delete' )
	{
		$id = intval($HTTP_GET_VARS['id']);
		$target = intval($HTTP_POST_VARS['target']);

		$sql = "UPDATE ". GARAGE_MODS_TABLE ."
			SET category_id = $target
			WHERE category_id = $id";
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not update this Category content', '', __LINE__, __FILE__, $sql);
		}

		// This category is now emptied, we can remove it!
		$sql = "DELETE FROM ". GARAGE_CATEGORIES_TABLE ."
			WHERE id = $id";
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not delete this Category', '', __LINE__, __FILE__, $sql);
		}

		// Return a message...
		$message = $lang['Category_deleted'] . "<br /><br />" . sprintf($lang['Click_return_garage_category'], "<a href=\"" . append_sid("admin_garage_categories.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
	}
}

?>

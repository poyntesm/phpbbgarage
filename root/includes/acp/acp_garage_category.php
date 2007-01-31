<?php
/***************************************************************************
 *                              acp_garage_category.php
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

class acp_garage_category
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx, $garage;

		//Build All Garage Classes e.g $garage_images->
		require($phpbb_root_path . 'includes/mods/class_garage_admin.' . $phpEx);

		$user->add_lang('acp/garage');
		$this->tpl_name = 'acp_garage_category';
		$this->page_title = 'ACP_MANAGE_FORUMS';

		$action		= request_var('action', '');
		$update		= (isset($_POST['update'])) ? true : false;
		$category_id	= request_var('id', 0);

		$errors = array();

		// Major routines
		if ($update)
		{
			switch ($action)
			{
				case 'edit':

					$title	= request_var('title', '');

					if(!$title)
					{
						$errors[] = $user->lang['CATEGORY_NAME_EMPTY'];
						break;
					}

					$garage->update_single_field(GARAGE_CATEGORIES_TABLE, 'title', $title, 'id', $category_id);

					trigger_error($user->lang['CATEGORY_UPDATED'] . adm_back_link($this->u_action));

				break;

				case 'delete':
					$action_modifications	= request_var('action_modifications', '');
					$modifications_to_id	= request_var('modifications_to_id', 0);

					$errors = $this->delete_category($category_id, $action_modifications, $modifications_to_id);

					if (sizeof($errors))
					{
						break;
					}

					trigger_error($user->lang['CATEGORY_DELETED'] . adm_back_link($this->u_action));
				break;

			}
		}

		switch ($action)
		{
			case 'add':
		
				//Count Current Categories..So We Can Work Out Order
				$count = $garage_admin->count_categories();
		
				//Get posting variables
				$data['title'] = request_var('category', '');
				$data['field_order'] = $count + 1;

				if(!$data['title'])
				{
					$errors[] = $user->lang['CATEGORY_NAME_EMPTY'];
					break;
				}
		
				//Insert New Category Into DB
				$garage_admin->insert_category($data);

				add_log('admin', 'LOG_FORUM_ADD', $data['title']);
		
				break;
		
			case 'edit':

				if (!$category_id)
				{
					trigger_error($user->lang['NO_FORUM'] . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
				}

				$category_data = $garage->get_category($category_id);

				$template->assign_vars(array(
					'S_EDIT_CATEGORY'		=> true,
					'U_ACTION'			=> $this->u_action . "&amp;action=edit&amp;id=$category_id",
					'U_BACK'			=> $this->u_action,
					'CATEGORY_NAME'			=> $category_data['title'],
					'S_ERROR'			=> (sizeof($errors)) ? true : false,
					'ERROR_MSG'			=> (sizeof($errors)) ? implode('<br />', $errors) : '')
				);

				return;
		
			case 'delete':

				if (!$category_id)
				{
					trigger_error($user->lang['NO_FORUM'] . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
				}

				$category_data = $garage->get_category($category_id);
				$all_data = $garage->get_categories();

				//Build Dropdown Options For Where To Love Linked Items To
				$select_to = null;
				for ($i = 0; $i < count($all_data); $i++)
				{
					//Do Not List Category We Are Deleting..
					if ( $category_id == $all_data[$i]['id'] )
					{
						continue;
					}
					$select_to .= '<option value="'. $all_data[$i]['id'] .'">'. $all_data[$i]['title'] .'</option>';
				}

				$template->assign_vars(array(
					'S_DELETE_CATEGORY'		=> true,
					'S_MOVE_CATEGORY'		=> ($garage_admin->count_categories() > 1) ? true : false,
					'U_ACTION'			=> $this->u_action . "&amp;action=delete&amp;id=$category_id",
					'U_BACK'			=> $this->u_action,
					'CATEGORY_NAME'			=> $category_data['title'],
					'S_MOVE_CATEGORY_OPTIONS'	=> $select_to,
					'S_ERROR'			=> (sizeof($errors)) ? true : false,
					'ERROR_MSG'			=> (sizeof($errors)) ? implode('<br />', $errors) : '')
				);

				return;
			break;
		
			case 'move_up':
			case 'move_down':

				$field_order = request_var('order', '');
				$order_total = $field_order * 2 + (($action == 'move_up') ? -1 : 1);

				//Get Category Name
				$data = $garage->get_category($category_id);

				//Get Relative Position
				$moved_id = $field_order + (($action == 'move_up') ? -1 : 1);
				$moved = $garage->get_category($moved_id);

				$sql = 'UPDATE ' . GARAGE_CATEGORIES_TABLE . "
					SET field_order = $order_total - field_order
					WHERE field_order IN ($field_order, " . (($action == 'move_up') ? $field_order - 1 : $field_order + 1) . ')';

				$db->sql_query($sql);

				add_log('admin', 'LOG_FORUM_' . strtoupper($action), $data['title'], $moved['title']);

			break;
		}

		// Default management page
		$template->assign_vars(array(
			'S_ERROR'	=> (sizeof($errors)) ? true : false,
			'ERROR_MSG'	=> (sizeof($errors)) ? implode('<br />', $errors) : '',
			'U_ACTION'	=> $this->u_action,
		));

		//Get All Category Data...
		$data = $garage->get_categories();

		//Process Each Category
		for( $i = 0; $i < count($data); $i++ )
		{
			$order = $i + 1;
			$url = $this->u_action . "&amp;id={$data[$i]['id']}";

			$template->assign_block_vars('catrow', array(
				'ID' 		=> $data[$i]['id'],
				'TITLE' 	=> $data[$i]['title'],
				'U_MOVE_UP'	=> $url . '&amp;action=move_up&amp;order=' . $order,
				'U_MOVE_DOWN'	=> $url . '&amp;action=move_down&amp;order=' . $order,
				'U_EDIT'	=> $url . '&amp;action=edit',
				'U_DELETE'	=> $url . '&amp;action=delete',
			));
		}
		
	}

	/**
	* Remove complete category
	*/
	function delete_category($category_id, $action_modifications = 'delete', $modifications_to_id = 0)
	{

		global $db, $user, $cache, $garage;

		$category_data = $garage->get_category($category_id);

		$errors = array();
		$log_action_modifications = $modifications_to_name = '';

		if ($action_modifications == 'delete')
		{
			$log_action_modifications = 'MODIFICATIONS';
			$errors = array_merge($errors, $this->delete_category_content($category_id));
		}
		else if ($action_modifications == 'move')
		{
			if (!$modifications_to_id)
			{
				$errors[] = $user->lang['NO_DESTINATION_CATEGORY'];
			}
			else
			{
				$log_action_modifications = 'MOVE_MODIFICATIONS';

				$row = $garage->get_category($modifications_to_id);

				if (!$row)
				{
					$errors[] = $user->lang['NO_CATEGORY'];
				}
				else
				{
					$modifications_to_name = $row['title'];
					$errors = array_merge($errors, $this->move_category_content($category_id, $modifications_to_id));
					//Delete Contents Now Will Just Delete Category As Content Is Moved Already
					$errors = array_merge($errors, $this->delete_category_content($category_id));
				}
			}
		}

		if (sizeof($errors))
		{
			return $errors;
		}
	}

	/**
	* Delete category content
	*/
	function delete_category_content($category_id)
	{
		global $db, $config, $phpbb_root_path, $phpEx, $garage;

		include_once($phpbb_root_path . 'includes/mods/class_garage_modification.' . $phpEx);

		$modifications = $garage_modification->get_modifications_by_category_id($category_id);

		for ($i = 0, $count = sizeof($modifications);$i < $count; $i++)
		{
			$garage_modification->delete_modification($modifications[$i]['id']);
		}

		//This Category Is Now Emptied, We Can Delete It!
		$garage->delete_rows(GARAGE_CATEGORIES_TABLE, 'id', $category_id);

		return array();
	}

	/**
	* Move category content from one to another category
	*/
	function move_category_content($from_id, $to_id)
	{
		global $garage;

		//Move All Modifications To New Category
		$garage->update_single_field(GARAGE_MODIFICATIONS_TABLE, 'category_id', $to_id, 'category_id', $from_id);

		return array();
	}

}
?>

<?php
/** 
*
* @package acp
* @version $Id$
* @copyright (c) 2006 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @package acp
*/
class acp_garage_category
{
	var $u_action;

	function main($id, $mode)
	{
		/**
		* Setup global variables such as $db 
		*/
		global $db, $user, $auth, $template, $cache;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx, $garage;

		/**
		* Build All Garage Classes e.g $garage_images->
		*/
		require($phpbb_root_path . 'includes/mods/class_garage_admin.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);

		/**
		* Setup page variables such as title, template & available language strings
		*/
		$user->add_lang('acp/garage');
		$this->tpl_name = 'acp_garage_category';
		$this->page_title = 'ACP_MANAGE_FORUMS';

		/**
		* Setup variables required
		*/
		$action		= request_var('action', '');
		$update		= (isset($_POST['update'])) ? true : false;
		$category_id	= request_var('id', 0);
		$errors 	= array();

		/**
		* Perform a set action based on value for $action
		* An action is normally a DB action such as insert/update/delete
		* An action will only show a page to show success or failure
		*/
		if ($update)
		{
			switch ($action)
			{
				/**
				* Update an existing category
				*/
				case 'edit':
					$title	= request_var('title', '', true);

					if(!$title)
					{
						$errors[] = $user->lang['CATEGORY_NAME_EMPTY'];
						break;
					}

					$garage->update_single_field(GARAGE_CATEGORIES_TABLE, 'title', $title, 'id', $category_id);

					trigger_error($user->lang['CATEGORY_UPDATED'] . adm_back_link($this->u_action));
				break;

				/**
				* Delete an existing catgory
				*/
				case 'delete':
					$action_modifications	= request_var('action_modifications', '');
					$modifications_to_id	= request_var('modifications_to_id', 0);

					$errors = $garage_admin->delete_category($category_id, $action_modifications, $modifications_to_id);

					if (sizeof($errors))
					{
						break;
					}

					trigger_error($user->lang['CATEGORY_DELETED'] . adm_back_link($this->u_action));
				break;
			}
		}

		/**
		* Perform a set action based on value for $action
		*/
		switch ($action)
		{
			/**
			* Add a new category & log it
			*/
			case 'add':
				$count = $garage_admin->count_categories();
		
				$data['title'] = request_var('category', '', true);
				$data['field_order'] = $count + 1;

				if(!$data['title'])
				{
					$errors[] = $user->lang['CATEGORY_NAME_EMPTY'];
					break;
				}
		
				$garage_admin->insert_category($data);

				add_log('admin', 'LOG_FORUM_ADD', $data['title']);
			break;

			/**
			* Page to edit an existing category
			*/
			case 'edit':
				if (!$category_id)
				{
					trigger_error($user->lang['NO_CATEGORY'] . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
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
			break;
		
			/**
			* Page to delete an existing category
			* Administrators decides where modifications can be moved to
			*/
			case 'delete':
				if (!$category_id)
				{
					trigger_error($user->lang['NO_CATEGORY'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$category_data = $garage->get_category($category_id);
				$all_data = $garage->get_categories();
				$select_to = $garage_template->build_move_to($all_data, $category_id, 'id');

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
			break;

			/**
			* Move category up or down in order & log it
			*/
			case 'move_up':
			case 'move_down':
				$field_order = request_var('order', '');
				$order_total = $field_order * 2 + (($action == 'move_up') ? -1 : 1);

				$data = $garage->get_category($category_id);

				$moved_id = $field_order + (($action == 'move_up') ? -1 : 1);
				$moved = $garage->get_category($moved_id);

				$sql = 'UPDATE ' . GARAGE_CATEGORIES_TABLE . "
					SET field_order = $order_total - field_order
					WHERE field_order IN ($field_order, " . (($action == 'move_up') ? $field_order - 1 : $field_order + 1) . ')';

				$db->sql_query($sql);

				add_log('admin', 'LOG_FORUM_' . strtoupper($action), $data['title'], $moved['title']);

			break;
		}

		/**
		* Display default page to show list of categories
		*/
		$data = $garage->get_categories();
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

		$template->assign_vars(array(
			'S_ERROR'	=> (sizeof($errors)) ? true : false,
			'ERROR_MSG'	=> (sizeof($errors)) ? implode('<br />', $errors) : '',
			'U_ACTION'	=> $this->u_action,
		));
	}
}
?>

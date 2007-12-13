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
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package acp
*/
class acp_garage_model
{
	var $u_action;

	function main($id, $mode)
	{
		/**
		* Setup global variables such as $db 
		*/
		global $db, $user, $auth, $template, $cache, $garage, $garage_config, $garage_model, $garage_vehicle;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx;

		/**
		* Build All Garage Classes e.g $garage_images->
		*/
		require($phpbb_root_path . 'includes/mods/class_garage_model.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);

		/**
		* Setup page variables such as title, template & available language strings
		*/
		$user->add_lang('acp/garage');
		$this->tpl_name = 'acp_garage_model';
		$this->page_title = 'ACP_MANAGE_FORUMS';

		/**
		* Setup variables 
		*/
		$action		= request_var('action', '');
		$update		= (isset($_POST['update'])) ? true : false;
		$make_id	= request_var('make_id', '');
		$model_id	= request_var('model_id', '');
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
				* Delete a make & log it
				*/
				case 'make_delete':
					$action_make	= request_var('action_make', '');
					$make_to_id	= request_var('make_to_id', 0);

					$garage_model->delete_make($make_id, $action_make, $make_to_id);

					if (sizeof($errors))
					{
						break;
					}

					trigger_error($user->lang['MAKE_DELETED'] . adm_back_link($this->u_action));
				break;

				/**
				* Delete a model & log it
				*/
				case 'model_delete':
					$action_model	= request_var('action_model', '');
					$model_to_id	= request_var('model_to_id', 0);

					$garage_model->delete_model($model_id, $action_model, $model_to_id);

					if (sizeof($errors))
					{
						break;
					}

					trigger_error($user->lang['MODEL_DELETED'] . adm_back_link($this->u_action  . "&amp;action=models&amp;make_id=$make_id"));
				break;

				/**
				* Update an existing make & log it
				*/
				case 'make_edit':
					$params = array('make' => '');
					$data = $garage->process_mb_vars($params);
					$data['id'] = $make_id;
		
					$garage_model->update_make($data);
		
					trigger_error($user->lang['MAKE_UPDATED'] . adm_back_link($this->u_action));
				break;

				/**
				* Update an existing model & log it
				*/
				case 'model_edit':
					$params = array('model' => '');
					$data = $garage->process_mb_vars($params);
					$data['id'] = $model_id;
		
					$garage_model->update_model($data);
		
					trigger_error($user->lang['MODEL_UPDATED'] . adm_back_link($this->u_action  . "&amp;action=models&amp;make_id=$make_id"));
				break;
			}
		}

		/**
		* Perform a set action based on value for $action
		*/
		switch ($action)
		{
			/**
			* Add a new make & log it
			*/
			case 'make_add':
				$params = array('make' => '');
				$data = $garage->process_mb_vars($params);

				if(!$data['make'])
				{
					$errors[] = $user->lang['MAKE_NAME_EMPTY'];
					break;
				}

				$count = $garage_model->count_make($data['make']);
				if ( $count > 0)
				{
					$errors[] = $user->lang['MAKE_EXISTS'];
					break;
				}
		
				$garage_model->insert_make($data);

				add_log('admin', 'LOG_FORUM_ADD_MAKE', $data['make']);
			break;

			/**
			* Approve a make & log it
			*/
			case 'make_approve':
				$data = $garage_model->get_make($make_id);
				$garage->update_single_field(GARAGE_MAKES_TABLE, 'pending', 0, 'id', $make_id);
				add_log('admin', 'LOG_GARAGE_MAKE_APPROVED', $data['make']);
			break;

			/**
			* Disapprove a make & log it
			*/
			case 'make_disapprove':

				$data = $garage_model->get_make($make_id);
				$garage->update_single_field(GARAGE_MAKES_TABLE, 'pending', 1, 'id', $make_id);
				add_log('admin', 'LOG_GARAGE_MAKE_DISAPPROVED', $data['make']);
			break;

			/**
			* Page to edit an existing make
			*/
			case 'make_edit':
				if (!$make_id)
				{
					trigger_error($user->lang['NO_MAKE'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$make_data = $garage_model->get_make($make_id);

				$template->assign_vars(array(
					'S_EDIT_MAKE'		=> true,
					'U_ACTION'		=> $this->u_action . "&amp;action=make_edit&amp;make_id=$make_id",
					'U_BACK'		=> $this->u_action,
					'MAKE'			=> $make_data['make'],
					'S_ERROR'		=> (sizeof($errors)) ? true : false,
					'ERROR_MSG'		=> (sizeof($errors)) ? implode('<br />', $errors) : '')
				);

				return;
			break;

			/**
			* Page to edit an existing model
			*/
			case 'model_edit':
				if (!$model_id)
				{
					trigger_error($user->lang['NO_MODEL'] . adm_back_link($this->u_action . "&amp;action=models&amp;make_id=$make_id"), E_USER_WARNING);
				}

				$model_data = $garage_model->get_model($model_id);

				$template->assign_vars(array(
					'S_EDIT_MODEL'		=> true,
					'U_ACTION'		=> $this->u_action . "&amp;action=model_edit&amp;model_id=$model_id",
					'U_BACK'		=> $this->u_action . "&amp;action=models&amp;make_id=$make_id",
					'MODEL'			=> $model_data['model'],
					'S_ERROR'		=> (sizeof($errors)) ? true : false,
					'ERROR_MSG'		=> (sizeof($errors)) ? implode('<br />', $errors) : '')
				);

				return;
			break;

			/**
			* Page to delete an existing make
			* Administrators decides where vehicles can be moved to
			*/
			case 'make_delete':
				if (!$make_id)
				{
					trigger_error($user->lang['NO_MAKE'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$make_data = $garage_model->get_make($make_id);
				$makes_data = $garage_model->get_all_makes();
				$select_to = $garage_template->build_move_to($makes_data, $make_id, 'make');

				$template->assign_vars(array(
					'S_DELETE_MAKE'			=> true,
					'U_ACTION'			=> $this->u_action . "&amp;action=make_delete&amp;make_id=$make_id",
					'U_BACK'			=> $this->u_action,
					'S_MOVE'			=> (!empty($select_to)) ? true : false ,
					'S_MOVE_OPTIONS'		=> $select_to,
					'MAKE'				=> $make_data['make'],
					'S_ERROR'			=> (sizeof($errors)) ? true : false,
					'ERROR_MSG'			=> (sizeof($errors)) ? implode('<br />', $errors) : '')
				);
			break;

			/**
			* Page to delete an existing model
			* Administrators decides where vehicles can be moved to
			*/
			case 'model_delete':
				if (!$model_id)
				{
					trigger_error($user->lang['NO_MODEL'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$model_data = $garage_model->get_model($model_id);
				$models_data = $garage_model->get_all_models_from_make($make_id);
				$select_to = $garage_template->build_move_to($models_data, $model_id, 'model');

				$template->assign_vars(array(
					'S_DELETE_MODEL'		=> true,
					'U_ACTION'			=> $this->u_action . "&amp;action=model_delete&amp;model_id=$model_id&amp;make_id=$make_id",
					'U_BACK'			=> $this->u_action . "&amp;action=models&amp;make_id=$make_id",
					'S_MOVE'			=> (!empty($select_to)) ? true : false ,
					'S_MOVE_OPTIONS'		=> $select_to,
					'MODEL'				=> $model_data['model'],
					'S_ERROR'			=> (sizeof($errors)) ? true : false,
					'ERROR_MSG'			=> (sizeof($errors)) ? implode('<br />', $errors) : '')
				);
			break;

			/**
			* Page to display models for a specific make
			* Due to wanting approval & disapproval & addition to appear seamless we have them within this action also
			*/
			case 'model_add':
			case 'model_approve':
			case 'model_disapprove':
			case 'models':
				if ($action == 'model_approve')
				{
					$data = $garage_model->get_model($model_id);
					$garage->update_single_field(GARAGE_MODELS_TABLE, 'pending', 0, 'id', $model_id);
					add_log('admin', 'LOG_GARAGE_MODEL_APPROVED', $data['model']);
				}

				if ($action == 'model_disapprove')
				{
					$data = $garage_model->get_model($model_id);
					$garage->update_single_field(GARAGE_MODELS_TABLE, 'pending', 1, 'id', $model_id);
					add_log('admin', 'LOG_GARAGE_MODEL_DISAPPROVED', $data['model']);
				}

				if ($action == 'model_add')
				{
					$params = array('make_id' => '');
					$data = $garage->process_vars($params);
					$params = array('model' => '');
					$data += $garage->process_mb_vars($params);
	
					if(!$data['model'])
					{
						$errors[] = $user->lang['MODEL_NAME_EMPTY'];
					}
	
					$count = $garage_model->count_model_in_make($data['model'], $data['make_id']);
					if ( $count > 0)
					{
						$errors[] = $user->lang['MODEL_EXISTS'];
					}
						
					if (!sizeof($errors))
					{						
						$garage_model->insert_model($data);
						add_log('admin', 'LOG_FORUM_ADD_MODEL', $data['model']);
					}
				}

				$models = $garage_model->get_all_models_from_make($make_id);
				$make = $garage_model->get_make($make_id);
	
				for( $i = 0; $i < count($models); $i++ )
				{
					$url = $this->u_action . "&amp;make_id=$make_id&amp;model_id={$models[$i]['id']}";
					$template->assign_block_vars('model', array(
						'ID' 			=> $models[$i]['id'],
						'MODEL' 		=> $models[$i]['model'],
						'S_DISAPPROVED'		=> ($models[$i]['pending'] == 1) ? true : false,
						'S_APPROVED'		=> ($models[$i]['pending'] == 0) ? true : false,
						'U_APPROVE'		=> $url . '&amp;action=model_approve',
						'U_DISAPPROVE'		=> $url . '&amp;action=model_disapprove',
						'U_EDIT'		=> $url . '&amp;action=model_edit',
						'U_DELETE'		=> $url . '&amp;action=model_delete',
					));
				}
	
				$template->assign_vars(array(
					'S_ERROR'	=> (sizeof($errors)) ? true : false,
					'ERROR_MSG'	=> (sizeof($errors)) ? implode('<br />', $errors) : '',
					'MAKE'		=> $make['make'],
					'MAKE_ID'	=> $make_id,
					'U_LIST_MAKES'	=> $url = $this->u_action,
					'S_LIST_MODELS'	=> true,
					'S_MODE_ACTION' => append_sid('admin_garage_models.'.$phpEx),
				));
			break;
		}
		
		/**
		* Display default page to show list of makes
		*/
		$makes = $garage_model->get_all_makes();
		for( $i = 0; $i < count($makes); $i++ )
		{
			$url = $this->u_action . "&amp;make_id={$makes[$i]['id']}";
			$template->assign_block_vars('make', array(
				'ID' 			=> $makes[$i]['id'],
				'MAKE' 			=> $makes[$i]['make'],
				'S_DISAPPROVED'		=> ($makes[$i]['pending'] == 1) ? true : false,
				'S_APPROVED'		=> ($makes[$i]['pending'] == 0) ? true : false,
				'U_LIST_MODELS'		=> $url . '&amp;action=models',
				'U_APPROVE'		=> $url . '&amp;action=make_approve',
				'U_DISAPPROVE'		=> $url . '&amp;action=make_disapprove',
				'U_EDIT'		=> $url . '&amp;action=make_edit',
				'U_DELETE'		=> $url . '&amp;action=make_delete',
			));
		}
	
		$template->assign_vars(array(
			'S_ERROR'	=> (sizeof($errors)) ? true : false,
			'ERROR_MSG'	=> (sizeof($errors)) ? implode('<br />', $errors) : '',
		));
	}
}
?>

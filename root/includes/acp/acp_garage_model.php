<?php
/***************************************************************************
 *                              acp_garage_model.php
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

class acp_garage_model
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache, $garage, $garage_config;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx;

		//Build All Garage Classes e.g $garage_images->
		require($phpbb_root_path . 'includes/mods/class_garage_model.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);

		$user->add_lang('acp/garage');
		$this->tpl_name = 'acp_garage_model';
		$this->page_title = 'ACP_MANAGE_FORUMS';

		$action		= request_var('action', '');
		$update		= (isset($_POST['update'])) ? true : false;

		$make_id	= request_var('make_id', '');
		$model_id	= request_var('model_id', '');

		$errors = array();

		// Major routines
		if ($update)
		{
			switch ($action)
			{
				case 'add':


					break;

			case 'insert_make':
		
				//Get All Data Posted And Make It Safe To Use
				$params = array('make');
				$data = $garage->process_post_vars($params);
		
				//Checks All Required Data Is Present
				$params = array('make');
				$garage->check_acp_required_vars($params, $missing_data_message);
		
				//Check For Make With Same Name And Error If Exists
				$count = $garage_model->count_make($data['make']);
				if ( $count > 0)
				{
					message_die(GENERAL_MESSAGE, $make_exists_message);
				}
		
				//Insert New Make Into DB
				$garage_model->insert_make($data);
		
				//Return a message...
				message_die(GENERAL_MESSAGE, $make_created_message);
						
				break;
		
			case 'update_make':
		
				//Get All Data Posted And Make It Safe To Use
				$params = array('id', 'make');
				$data = $garage->process_post_vars($params);
		
				//Checks All Required Data Is Present
				$params = array('id', 'make');
				$garage->check_acp_required_vars($params , $missing_data_message);
		
				//Check For Make With Same Name And Error If Exists
				$count = $garage_model->count_make($data['make']);
				if ( $count > 0)
				{
					message_die(GENERAL_MESSAGE, $make_exists_message);
				}
		
				//Update Make In DB
				$garage_model->update_make($data);
		
				//Return a message...
				message_die(GENERAL_MESSAGE, $make_updated_message);
				
				break;
		
			case 'delete_make':
		
				//Get All Data Posted And Make It Safe To Use
				$params = array('id', 'target', 'permenant');
				$data = $garage->process_post_vars($params);
		
				//If Set Delete Permentantly..And Finish
				if ($data['permenant'] == '1')
				{
					//Delete The Model
					$garage->delete_rows(GARAGE_MAKES_TABLE, 'id', $data['id']);
			
					// Return a message...
					message_die(GENERAL_MESSAGE, $make_deleted_message);
				}
		
				//Checks All Required Data Is Present
				$params = array('id', 'target');
				$garage->check_acp_required_vars($params, $missing_data_message);
		
				//Move Any Existing Vehicles And Existing Models To New Target Make Then Delete Make
				$garage->update_single_field(GARAGE_TABLE,'make_id',$data['target'],'make_id',$data['id']);
				$garage->update_single_field(GARAGE_MODELS_TABLE,'make_id',$data['target'],'make_id',$data['id']);
				$garage->delete_rows(GARAGE_MAKES_TABLE, 'id', $data['id']);
		
				//Return a message...
				message_die(GENERAL_MESSAGE, $make_deleted_message);
		
				break;	
		
			case 'insert_model':
		
				//Get All Data Posted And Make It Safe To Use
				$params = array('make_id', 'model');
				$data = $garage->process_post_vars($params);
		
				//Checks All Required Data Is Present
				$params = array('make_id', 'model');
				$garage->check_acp_required_vars($params, $missing_data_message);
		
				//Insert Make Into DB
				$garage_model->insert_model($data);
		
				//Return a message...
				message_die(GENERAL_MESSAGE, $model_created_message);
				
				break;	
		
			case 'update_model':
		
				//Get All Data Posted And Make It Safe To Use
				$params = array('id', 'model');
				$data = $garage->process_post_vars($params);
		
				//Checks All Required Data Is Present
				$params = array('id', 'model');
				$garage->check_acp_required_vars($params , $message);
		
				//Update Model In DB
				$garage_model->update_model($data);
		
				//Return a message...
				message_die(GENERAL_MESSAGE, $model_updated_message);
				
				break;
		
			case 'delete_model':
		
				//Get All Data Posted And Make It Safe To Use
				$params = array('id', 'target', 'permenant');
				$data = $garage->process_post_vars($params);
		
				//If Set Delete Permentantly..And Finish
				if ($data['permenant'] == '1')
				{
					//Delete The Model
					$garage->delete_rows(GARAGE_MODELS_TABLE, 'id', $data['id']);
			
					// Return a message...
					message_die(GENERAL_MESSAGE, $model_deleted_message);
				}
		
				//Checks All Required Data Is Present
				$params = array('id', 'target');
				$garage->check_acp_required_vars($params, $missing_data_message);
		
				//Move Any Existing Vehicles To New Target Model Then Delete Model
				$garage->update_single_field(GARAGE_TABLE,'model_id',$data['target'],'model_id',$data['id']);
				$garage->delete_rows(GARAGE_MODELS_TABLE, 'id', $data['id']);
		
				//Return a message...
				message_die(GENERAL_MESSAGE, $model_deleted_message);

				break;
			}
		}

		switch ($action)
		{

			case 'make_add':
		
				//Count Current Categories..So We Can Work Out Order
				$count = $garage_admin->count_categories();
		
				//Get posting variables
				$data['title'] = request_var('category', '');
				$data['field_order'] = $count + 1;

				if(!$data['title'])
				{
					$errors[] = $user->lang['MAKE_NAME_EMPTY'];
					break;
				}
		
				//Insert New Category Into DB
				$garage_admin->insert_category($data);

				add_log('admin', 'LOG_FORUM_ADD', $data['title']);
		
				break;

			case 'model_add':
		
				//Count Current Categories..So We Can Work Out Order
				$count = $garage_admin->count_categories();
		
				//Get posting variables
				$data['title'] = request_var('category', '');
				$data['field_order'] = $count + 1;

				if(!$data['title'])
				{
					$errors[] = $user->lang['MODEL_NAME_EMPTY'];
					break;
				}
		
				//Insert New Category Into DB
				$garage_admin->insert_category($data);

				add_log('admin', 'LOG_FORUM_ADD', $data['title']);
		
				break;

			case 'make_approve':

				$data = $garage_model->get_make($make_id);
				$garage->update_single_field(GARAGE_MAKES_TABLE, 'pending', 0, 'id', $make_id);
				add_log('admin', 'LOG_GARAGE_MAKE_APPROVED', $data['make']);

			break;

			case 'make_disapprove':

				$data = $garage_model->get_make($make_id);
				$garage->update_single_field(GARAGE_MAKES_TABLE, 'pending', 1, 'id', $make_id);
				add_log('admin', 'LOG_GARAGE_MAKE_DISAPPROVED', $data['make']);

			break;

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

			case 'make_delete':

				if (!$make_id)
				{
					trigger_error($user->lang['NO_MAKE'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$make_data = $garage_model->get_make($make_id);
				$makes_data = $garage_model->get_all_makes();
				$select_to = $this->build_move_to($makes_data, $make_id);

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

			case 'model_delete':

				if (!$model_id)
				{
					trigger_error($user->lang['NO_MODEL'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$model_data = $garage_model->get_model($model_id);
				$models_data = $garage_model->get_all_models_from_make($make_id);
				$select_to = $this->build_move_model_to($models_data, $model_id);

				$template->assign_vars(array(
					'S_DELETE_MODEL'		=> true,
					'U_ACTION'			=> $this->u_action . "&amp;action=model_delete&amp;model_id=$model_id",
					'U_BACK'			=> $this->u_action . "&amp;action=models&amp;make_id=$make_id",
					'S_MOVE'			=> (!empty($select_to)) ? true : false ,
					'S_MOVE_OPTIONS'		=> $select_to,
					'MODEL'				=> $model_data['model'],
					'S_ERROR'			=> (sizeof($errors)) ? true : false,
					'ERROR_MSG'			=> (sizeof($errors)) ? implode('<br />', $errors) : '')
				);
		
			break;

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

				//Get Models
				$models = $garage_model->get_all_models_from_make($make_id);

				$make = $garage_model->get_make($make_id);
	
				//Process Array For Each Model
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
					'MAKE'		=> $make['make'],
					'U_LIST_MAKES'	=> $url = $this->u_action,
					'S_LIST_MODELS'	=> true,
					'S_MODE_ACTION' => append_sid('admin_garage_models.'.$phpEx),
				));
		
			break;

		}
		
		//Default Management screen..
		$makes = $garage_model->get_all_makes();
	
		//Process Array For Each Make
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
			'S_MODE_ACTION' => append_sid('admin_garage_models.'.$phpEx),
		));
		
	}

	function build_move_to($data, $exclude_id)
	{
		$select_to = null;
		for ($i = 0; $i < count($data); $i++)
		{
			if ($exclude_id == $data[$i]['id'])
			{
				continue;
			}
			$select_to .= '<option value="'. $data[$i]['id'] .'">'. $data[$i]['make'] .'</option>';
		}
		return $select_to;
	}

	function build_move_model_to($data, $exclude_id)
	{
		$select_to = null;
		for ($i = 0; $i < count($data); $i++)
		{
			if ($exclude_id == $data[$i]['id'])
			{
				continue;
			}
			$select_to .= '<option value="'. $data[$i]['id'] .'">'. $data[$i]['model'] .'</option>';
		}
		return $select_to;
	}

}

?>

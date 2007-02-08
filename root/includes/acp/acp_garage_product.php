<?php
/***************************************************************************
 *                              acp_garage_product.php
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

class acp_garage_product
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache, $garage, $garage_config, $garage_template, $garage_vehicle;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx, $garage_business, $garage_modification;

		//Build All Garage Classes e.g $garage_images->
		require($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_business.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_modification.' . $phpEx);


		$user->add_lang('acp/garage');
		$this->tpl_name = 'acp_garage_product';
		$this->page_title = 'ACP_MANAGE_FORUMS';

		$action		= request_var('action', '');
		$update		= (isset($_POST['update'])) ? true : false;

		$manufacturer_id	= request_var('manufacturer_id', '');
		$product_id		= request_var('product_id', '');

		$errors = array();

		// Major routines
		if ($update)
		{
			switch ($action)
			{

				case 'add_product':

					$params = array('title' => '', 'category_id' => '', 'manufacturer_id' => $manufacturer_id);
					$data = $garage->process_vars($params);

					$garage_modification->insert_product($data);
					add_log('admin', 'LOG_GARAGE_PRODUCT_CREATED', $data['title']);

					trigger_error($user->lang['PRODUCT_CREATED'] . adm_back_link($this->u_action . "&amp;action=products&amp;manufacturer_id=$manufacturer_id"));

				case 'edit_product':

					$params = array('title' => '', 'category_id' => '', 'manufacturer_id' => $manufacturer_id, 'product_id' => $product_id);
					$data = $garage->process_vars($params);

					$garage_modification->update_product($data);
					add_log('admin', 'LOG_GARAGE_PRODUCT_UPDATED', $data['title']);

					trigger_error($user->lang['PRODUCT_UPDATED'] . adm_back_link($this->u_action . "&amp;action=products&amp;manufacturer_id=$manufacturer_id"));

				case 'delete_product':

					$action_model		= request_var('action_modifications', '');
					$model_to_id		= request_var('modifications_to_id', 0);

					$garage_model->delete_product($product_id, $action_modifications, $model_to_id);

					if (sizeof($errors))
					{
						break;
					}

					trigger_error($user->lang['MODEL_DELETED'] . adm_back_link($this->u_action  . "&amp;action=models&amp;make_id=$make_id"));

				break;
			}
		}

		switch ($action)
		{
			case 'delete_product':

				if (!$model_id)
				{
					trigger_error($user->lang['NO_MODEL'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$model_data = $garage_model->get_model($model_id);
				$models_data = $garage_model->get_all_models_from_make($make_id);
				$select_to = $this->build_move_model_to($models_data, $model_id);

				$template->assign_vars(array(
					'S_DELETE_PRODUCT'		=> true,
					'U_ACTION'			=> $this->u_action . "&amp;action=model_delete&amp;model_id=$model_id&amp;make_id=$make_id",
					'U_BACK'			=> $this->u_action . "&amp;action=models&amp;make_id=$make_id",
					'S_MOVE'			=> (!empty($select_to)) ? true : false ,
					'S_MOVE_OPTIONS'		=> $select_to,
					'MODEL'				=> $model_data['model'],
					'S_ERROR'			=> (sizeof($errors)) ? true : false,
					'ERROR_MSG'			=> (sizeof($errors)) ? implode('<br />', $errors) : '')
				);
		
			break;

			case 'add_product':
			case 'edit_product':

				// Show form to create/modify a business
				if ($action == 'edit_product')
				{
					$this->page_title = 'EDIT_PRODUCT';
					$row = $garage_modification->get_product($product_id);

					if (!$update)
					{
						$product_data = $row;
					}
				}
				else
				{
					$this->page_title = 'CREATE_PRODUCT';

					// Fill business data with default values
					if (!$update)
					{
						$product_data = array(
							'title'		=> request_var('product', '', true),
							'category_id'	=> '',
						);
					}
				}

				$categories = $garage->get_categories();
				$garage_template->category_dropdown($categories, $product_data['category_id']);
				$template->assign_vars(array(
					'S_EDIT_PRODUCT'	=> true,
					'S_ERROR'		=> (sizeof($errors)) ? true : false,
					'U_BACK'		=> $this->u_action . "&amp;action=products&amp;manufacturer_id=$manufacturer_id",
					'U_ACTION'		=> $this->u_action . "&amp;action=$action&amp;manufacturer_id=$manufacturer_id&amp;product_id=$product_id",
					'PRODUCT'		=> $product_data['title'],
					'ERROR_MSG'		=> (sizeof($errors)) ? implode('<br />', $errors) : '',
					)
				);

				return;

			break;

			case 'approve_product':
			case 'disapprove_product':
			case 'products':

				if ($action == 'approve_product')
				{
					$data = $garage_model->get_model($model_id);
					$garage->update_single_field(GARAGE_MODELS_TABLE, 'pending', 0, 'id', $model_id);
					add_log('admin', 'LOG_GARAGE_MODEL_APPROVED', $data['model']);
				}

				if ($action == 'disapprove_product')
				{
					$data = $garage_model->get_model($model_id);
					$garage->update_single_field(GARAGE_MODELS_TABLE, 'pending', 1, 'id', $model_id);
					add_log('admin', 'LOG_GARAGE_MODEL_DISAPPROVED', $data['model']);
				}

				if ($action == 'add_product')
				{
					$params = array('model' => '', 'make_id' => '');
					$data = $garage->process_vars($params);
	
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

				//Get Products
				$products = $garage_modification->get_products_by_manufacturer($manufacturer_id);

				$manufacturer = $garage_business->get_business($manufacturer_id);
	
				//Process Array For Each Model
				for( $i = 0; $i < count($products); $i++ )
				{
					$url = $this->u_action . "&amp;manufacturer_id=$manufacturer_id&amp;product_id={$products[$i]['id']}";
					$template->assign_block_vars('product', array(
						'ID' 			=> $products[$i]['id'],
						'PRODUCT' 		=> $products[$i]['title'],
						'U_EDIT'		=> $url . '&amp;action=edit_product',
						'U_DELETE'		=> $url . '&amp;action=delete_product',
					));
				}
	
				$template->assign_vars(array(
					'S_ERROR'		=> (sizeof($errors)) ? true : false,
					'ERROR_MSG'		=> (sizeof($errors)) ? implode('<br />', $errors) : '',
					'MANUFACTURER'		=> $manufacturer['title'],
					'MANUFACTURER_ID'	=> $manufacturer_id,
					'U_LIST_MANUFACTURERS'	=> $url = $this->u_action,
					'S_LIST_PRODUCTS'	=> true,
				));
		
			break;

		}
		
		//Default Management screen..
		$manufacturers = $garage_business->get_business_by_type(BUSINESS_PRODUCT);
	
		//Process Array For Each Make
		for( $i = 0; $i < count($manufacturers); $i++ )
		{
			$url = $this->u_action . "&amp;manufacturer_id={$manufacturers[$i]['id']}";
			$template->assign_block_vars('manufacturer', array(
				'ID' 			=> $manufacturers[$i]['id'],
				'MANUFACTURER' 		=> $manufacturers[$i]['title'],
				'U_LIST_PRODUCTS'	=> $url . '&amp;action=products',
			));
		}
	
		$template->assign_vars(array(
			'S_ERROR'	=> (sizeof($errors)) ? true : false,
			'ERROR_MSG'	=> (sizeof($errors)) ? implode('<br />', $errors) : '',
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
			$select_to .= '<option value="'. $data[$i]['id'] .'">'. $data[$i]['title'] .'</option>';
		}
		return $select_to;
	}
}

?>

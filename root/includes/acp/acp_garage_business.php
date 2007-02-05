<?php
/***************************************************************************
 *                              acp_garage_business.php
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

/**
* @package acp
*/
class acp_garage_business
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache, $garage, $garage_config;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx;

		//Build All Garage Classes e.g $garage_images->
		require($phpbb_root_path . 'includes/mods/class_garage_business.' . $phpEx);

		$user->add_lang('acp/garage');
		$this->tpl_name = 'acp_garage_business';
		$this->page_title = 'ACP_MANAGE_FORUMS';

		$action		= request_var('action', '');
		$update		= (isset($_POST['update'])) ? true : false;
		$business_id	= request_var('id', 0);

		$errors = array();

		// Major routines
		if ($update)
		{
			switch ($action)
			{
				case 'add':

					//Get All Data Posted And Make It Safe To Use
					$params = array('title' => '', 'address' => '', 'telephone' => '', 'fax' => '', 'website' => '', 'email' => '', 'opening_hours' => '', 'insurance' => '', 'garage' => '', 'retail'  => '', 'product' => '', 'dynocentre'  => '', 'pending' => '0');
					$data = $garage->process_vars($params);

					//Check They Entered http:// In The Front Of The Link
					if ( (!preg_match( "/^http:\/\//i", $data['website'])) AND (!empty($data['website'])) )
					{
						$data['website'] = "http://".$data['website'];
					}

					//Insert New Business Into DB
					$garage_business->insert_business($data);
					add_log('admin', 'LOG_GARAGE_BUSINESS_CREATED', $data['title']);

					trigger_error($user->lang['BUSINESS_CREATED'] . adm_back_link($this->u_action));

				break;

				case 'edit':

					//Get All Data Posted And Make It Safe To Use
					$params = array('title' => '', 'address' => '', 'telephone' => '', 'fax' => '', 'website' => '', 'email' => '', 'opening_hours' => '', 'insurance' => '', 'garage' => '', 'retail'  => '', 'product' => '', 'dynocentre'  => '', 'pending' => '0');
					$data = $garage->process_vars($params);
					$data['id'] = $business_id;

					if(!$data['title'])
					{
						$errors[] = $user->lang['BUSINESS_NAME_EMPTY'];
						break;
					}

					//Check They Entered http:// In The Front Of The Link
					if ( (!preg_match( "/^http:\/\//i", $data['website'])) AND (!empty($data['website'])) )
					{
						$data['website'] = "http://".$data['website'];
					}
		
					$garage_business->update_business($data);
					add_log('admin', 'LOG_GARAGE_BUSINESS_UPDATED', $data['title']);

					trigger_error($user->lang['BUSINESS_UPDATED'] . adm_back_link($this->u_action));

				break;

				case 'delete':
					$action_garage		= request_var('action_garage', '');
					$action_insurance	= request_var('action_insurance', '');
					$action_dynocentre	= request_var('action_dynocentre', '');
					$action_retail		= request_var('action_retail', '');
					$action_product		= request_var('action_product', '');
					$garage_to_id		= request_var('garage_to_id', 0);
					$insurance_to_id	= request_var('insurance_to_id', 0);
					$dynocentre_to_id	= request_var('dynocentre_to_id', 0);
					$retail_to_id		= request_var('retail_to_id', 0);
					$product_to_id		= request_var('product_to_id', 0);

					$errors = $this->delete_business($business_id, $action_garage, $garage_to_id, $action_insurance, $insurance_to_id, $action_dynocentre, $dynocentre_to_id, $action_retail, $retail_to_id, $action_product, $product_to_id);

					if (sizeof($errors))
					{
						break;
					}

					trigger_error($user->lang['BUSINESS_DELETED'] . adm_back_link($this->u_action));
				break;
			}
		}
		
		switch ($action)
		{

			case 'approve':

				$data = $garage_business->get_business($business_id);
				$garage->update_single_field(GARAGE_BUSINESS_TABLE, 'pending', 0, 'id', $business_id);
				add_log('admin', 'LOG_GARAGE_BUSINESS_APPROVED', $data['title']);

			break;

			case 'disapprove':

				$data = $garage_business->get_business($business_id);
				$garage->update_single_field(GARAGE_BUSINESS_TABLE, 'pending', 1, 'id', $business_id);
				add_log('admin', 'LOG_GARAGE_BUSINESS_DISAPPROVED', $data['title']);

			break;

			case 'add':
			case 'edit':

				// Show form to create/modify a business
				if ($action == 'edit')
				{
					$this->page_title = 'EDIT_BUSINESS';
					$row = $garage_business->get_business($business_id);

					if (!$update)
					{
						$business_data = $row;
					}
				}
				else
				{
					$this->page_title = 'CREATE_BUSINESS';

					// Fill business data with default values
					if (!$update)
					{
						$business_data = array(
							'title'		=> request_var('title', '', true),
							'address'	=> '',
							'telephone'	=> '',
							'fax'		=> '',
							'website'	=> '',
							'email'		=> '',
							'opening_hours'	=> '',
							'garage'	=> '',
							'retail'	=> '',
							'insurance'	=> '',
							'product'	=> '',
							'dynocentre'	=> '',
						);
					}
				}

				$template->assign_vars(array(
					'S_EDIT_BUSINESS'	=> true,
					'S_BUSINESS_GARAGE'	=> ($business_data['garage']) ? true : false,
					'S_BUSINESS_RETAIL'	=> ($business_data['retail']) ? true : false,
					'S_BUSINESS_INSURANCE'	=> ($business_data['insurance']) ? true : false,
					'S_BUSINESS_PRODUCT'	=> ($business_data['product']) ? true : false,
					'S_BUSINESS_DYNOCENTRE'	=> ($business_data['dynocentre']) ? true : false,
					'S_ERROR'		=> (sizeof($errors)) ? true : false,
					'U_BACK'		=> $this->u_action,
					'U_EDIT_ACTION'		=> $this->u_action . "&amp;action=$action&amp;id=$business_id",
					'BUSINESS_NAME'		=> $business_data['title'],
					'ADDRESS'		=> $business_data['address'],
					'TELEPHONE'		=> $business_data['telephone'],
					'FAX'			=> $business_data['fax'],
					'WEBSITE'		=> $business_data['website'],
					'EMAIL'			=> $business_data['email'],
					'OPENING_HOURS'		=> $business_data['opening_hours'],
					'ERROR_MSG'		=> (sizeof($errors)) ? implode('<br />', $errors) : '',
					)
				);

				return;

			break;

			//LOTS & LOTS to take into consideration here...	
			case 'delete':

				if (!$business_id)
				{
					trigger_error($user->lang['NO_BUSINESS'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$business_data = $garage_business->get_business($business_id);

				if ($business_data['insurance'])
				{
					$insurance_data = $garage_business->get_business_by_type(BUSINESS_INSURANCE);
					$select_to = $this->build_move_to($insurance_data, $business_id);
					$template->assign_vars(array(
						'S_TYPE_INSURER'		=> true,
						'S_MOVE_INSURER'		=> (!empty($select_to)) ? true : false,
						'S_MOVE_INSURER_OPTIONS'	=> $select_to,
					));
				}
				if ($business_data['dynocentre'])
				{
					$dynocentre_data = $garage_business->get_business_by_type(BUSINESS_DYNOCENTRE);
					$select_to = $this->build_move_to($dynocentre_data, $business_id);
					$template->assign_vars(array(
						'S_TYPE_DYNOCENTRE'		=> true,
						'S_MOVE_DYNOCENTRE'		=> (!empty($select_to)) ? true : false ,
						'S_MOVE_DYNOCENTRE_OPTIONS'	=> $select_to,
					));
				}
				if ($business_data['retail'])
				{
					$retail_data = $garage_business->get_business_by_type(BUSINESS_RETAIL);
					$select_to = $this->build_move_to($retail_data, $business_id);
					$template->assign_vars(array(
						'S_TYPE_RETAIL'			=> true,
						'S_MOVE_RETAIL'			=> (!empty($select_to)) ? true : false ,
						'S_MOVE_RETAIL_OPTIONS'	=> $select_to,
					));
				}
				if ($business_data['garage'])
				{
					$garage_data = $garage_business->get_business_by_type(BUSINESS_GARAGE);
					$select_to = $this->build_move_to($garage_data, $business_id);
					$template->assign_vars(array(
						'S_TYPE_GARAGE'			=> true,
						'S_MOVE_GARAGE'			=> (!empty($select_to)) ? true : false ,
						'S_MOVE_GARAGE_OPTIONS'		=> $select_to,
					));
				}
				if ($business_data['product'])
				{
					$product_data = $garage_business->get_business_by_type(BUSINESS_PRODUCT);
					$select_to = $this->build_move_to($product_data, $business_id);
					$template->assign_vars(array(
						'S_TYPE_PRODUCT'		=> true,
						'S_MOVE_PRODUCT'		=> (!empty($select_to)) ? true : false ,
						'S_MOVE_PRODUCT_OPTIONS'	=> $select_to,
					));
				}

				$template->assign_vars(array(
					'S_DELETE_BUSINESS'		=> true,
					'U_ACTION'			=> $this->u_action . "&amp;action=delete&amp;id=$business_id",
					'U_BACK'			=> $this->u_action,
					'BUSINESS_NAME'			=> $business_data['title'],
					'S_ERROR'			=> (sizeof($errors)) ? true : false,
					'ERROR_MSG'			=> (sizeof($errors)) ? implode('<br />', $errors) : '')
				);
		
			break;	
		
		}
		
		//Default Management Page	
		$data = $garage_business->get_all_business();
		
		for($i = 0; $i < count($data); $i++)
		{
			//Work Out Type Of Business...
			$type ='';
			$type = ( $data[$i]['insurance'] == '1' ) ? $user->lang['Insurance']: '' ;
			if ($data[$i]['retail'] == '1')
			{
				$type .= (empty($type)) ? $user->lang['Shop'] : ", " . $user->lang['Shop'] ;
			}
			if ( $data[$i]['garage'] == '1' )
			{
				$type .= (empty($type)) ? $user->lang['Garage'] : ", " . $user->lang['Garage'] ;
			}

			$url = $this->u_action . "&amp;id={$data[$i]['id']}";
		
			$template->assign_block_vars('business', array(
				'ID' 			=> $data[$i]['id'],
				'TITLE' 		=> $data[$i]['title'],
				'ADDRESS' 		=> $data[$i]['address'],
				'TELEPHONE' 		=> $data[$i]['telephone'],
				'FAX' 			=> $data[$i]['fax'],
				'WEBSITE' 		=> $data[$i]['website'],
				'EMAIL' 		=> $data[$i]['email'],
				'OPENING_HOURS' 	=> $data[$i]['opening_hours'],
				'S_DISAPPROVED'		=> ($data[$i]['pending'] == 1) ? true : false,
				'S_APPROVED'		=> ($data[$i]['pending'] == 0) ? true : false,
				'TYPE' 			=> $type,
				'U_APPROVE'		=> $url . '&amp;action=approve',
				'U_DISAPPROVE'		=> $url . '&amp;action=disapprove',
				'U_EDIT'		=> $url . '&amp;action=edit',
				'U_DELETE'		=> $url . '&amp;action=delete',
			));
		}
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

	/**
	* Remove complete business
	*/
	function delete_business($business_id, $action_garage = 'delete', $garage_to_id = 0, $action_insurance = 'delete', $insurance_to_id = 0, $action_dynocentre = 'delete', $dynocentre_to_id = 0, $action_retail = 'delete', $retail_to_id = 0, $action_product = 'delete', $product_to_id = 0)
	{

		global $db, $user, $cache, $garage, $garage_business;

		$business_data = $garage_business->get_business($business_id);

		$errors = array();

		//Handle Items Linked To Garage Business
		if ($action_garage == 'delete')
		{
			$this->delete_garage_business_content($business_id);
			add_log('admin', 'LOG_GARAGE_DELETE_GARAGE', $business_data['title']);
		}
		else if ($action_garage == 'move')
		{
			if (!$garage_to_id)
			{
				$errors[] = $user->lang['NO_DESTINATION_GARAGE_BUSINESS'];
			}
			else
			{
				$row = $garage_business->get_business($garage_to_id);

				if (!$row)
				{
					$errors[] = $user->lang['NO_BUSINESS'];
				}
				else
				{
					$garage_to_name = $row['title'];
					$this->move_category_content($business_id, $garage_to_id);
					add_log('admin', 'LOG_GARAGE_MOVED_GARAGE', $from_name, $garage_to_name);
				}
			}
		}

		//Handle Items Linked To Insurance Business
		if ($action_insurance == 'delete')
		{
			$this->delete_insurance_business_content($business_id);
			add_log('admin', 'LOG_GARAGE_DELETE_GARAGE', $business_data['title']);
		}
		else if ($action_insurance == 'move')
		{
			if (!$insurance_to_id)
			{
				$errors[] = $user->lang['NO_DESTINATION_INSURANCE_BUSINESS'];
			}
			else
			{
				$row = $garage_business->get_business($insurance_to_id);

				if (!$row)
				{
					$errors[] = $user->lang['NO_BUSINESS'];
				}
				else
				{
					$insurance_to_name = $row['title'];
					$from_name = $business_data['title'];
					$this->move_category_content($business_id, $insurance_to_id);
					add_log('admin', 'LOG_GARAGE_MOVED_PREMIUMS', $from_name, $insurance_to_name);
				}
			}
		}

		//Handle Items Linked To Dynocentre Business
		if ($action_dynocentre == 'delete')
		{
			$this->delete_dynocentre_business_content($business_id);
			add_log('admin', 'LOG_GARAGE_DELETE_GARAGE', $business_data['title']);
		}
		else if ($action_dynocentre == 'move')
		{
			if (!$dynocentre_to_id)
			{
				$errors[] = $user->lang['NO_DESTINATION_DYNOCENTRE_BUSINESS'];
			}
			else
			{
				$row = $garage_business->get_business($garage_to_id);

				if (!$row)
				{
					$errors[] = $user->lang['NO_BUSINESS'];
				}
				else
				{
					$dynocentre_to_name = $row['title'];
					$from_name = $business_data['title'];
					$this->move_category_content($business_id, $dynocentre_to_id);
					add_log('admin', 'LOG_GARAGE_MOVED_DYNORUNS', $from_name, $dynocentre_to_name);
				}
			}
		}
		
		//Handle Items Linked To Retail Business
		if ($action_retail == 'delete')
		{
			$this->delete_retail_business_content($business_id);
			add_log('admin', 'LOG_GARAGE_DELETE_GARAGE', $business_data['title']);
		}
		else if ($action_retail == 'move')
		{
			if (!$retail_to_id)
			{
				$errors[] = $user->lang['NO_DESTINATION_RETAIL_BUSINESS'];
			}
			else
			{
				$row = $garage_business->get_business($retail_to_id);

				if (!$row)
				{
					$errors[] = $user->lang['NO_BUSINESS'];
				}
				else
				{
					$retail_to_name = $row['title'];
					$from_name = $business_data['title'];
					$this->move_category_content($business_id, $retail_to_id);
					add_log('admin', 'LOG_GARAGE_MOVED_RETAIL', $from_name, $retail_to_name);
				}
			}
		}

		//Handle Items Linked To Product Business
		if ($action_product == 'delete')
		{
			$this->delete_product_business_content($business_id);
			add_log('admin', 'LOG_GARAGE_DELETE_GARAGE', $business_data['title']);
		}
		else if ($action_product == 'move')
		{
			if (!$product_to_id)
			{
				$errors[] = $user->lang['NO_DESTINATION_PRODUCT_BUSINESS'];
			}
			else
			{
				$row = $garage_business->get_business($product_to_id);

				if (!$row)
				{
					$errors[] = $user->lang['NO_BUSINESS'];
				}
				else
				{
					$modifications_to_name = $row['title'];
					$from_name = $business_data['title'];
					$this->move_category_content($business_id, $product_to_id);
					add_log('admin', 'LOG_GARAGE_MOVED_PRODUCT', $from_name, $product_to_name);
				}
			}
		}

		$garage->delete_rows(GARAGE_BUSINESS_TABLE, 'id', $business_id);
		add_log('admin', 'LOG_GARAGE_BUSINESS_DELETED', $business_data['title']);

		if (sizeof($errors))
		{
			return $errors;
		}
	}

	function delete_garage_business_content($business_id)
	{
		global $db, $config, $phpbb_root_path, $phpEx, $garage;

		include_once($phpbb_root_path . 'includes/mods/class_garage_modification.' . $phpEx);
		$modifications = $garage_modification->get_modifications_by_garage_id($business_id);
		for ($i = 0, $count = sizeof($modifications);$i < $count; $i++)
		{
			$garage_modification->delete_modification($modifications[$i]['id']);
		}

		return;
	}

	function delete_insurance_business_content($business_id)
	{
		global $db, $config, $phpbb_root_path, $phpEx, $garage;

		include_once($phpbb_root_path . 'includes/mods/class_garage_insurance.' . $phpEx);
		$premiums = $garage_insurance->get_premiums_by_insurer_id($business_id);
		for ($i = 0, $count = sizeof($premiums);$i < $count; $i++)
		{
			$garage_insurance->delete_premium($premiums[$i]['id']);
		}

		return;
	}

	function delete_dynocentre_business_content($business_id)
	{
		global $db, $config, $phpbb_root_path, $phpEx, $garage;

		include_once($phpbb_root_path . 'includes/mods/class_garage_dynorun.' . $phpEx);
		$dynoruns = $garage_dynorun->get_dynoruns_by_dynocentre_id($business_id);
		for ($i = 0, $count = sizeof($dynoruns);$i < $count; $i++)
		{
			$garage_modification->delete_modification($dynoruns[$i]['id']);
		}

		return;
	}

	function delete_retail_business_content($business_id)
	{
		global $db, $config, $phpbb_root_path, $phpEx, $garage;

		include_once($phpbb_root_path . 'includes/mods/class_garage_modification.' . $phpEx);
		$modifications = $garage_modification->get_modifications_by_retail_id($business_id);
		for ($i = 0, $count = sizeof($modifications);$i < $count; $i++)
		{
			$garage_modification->delete_modification($modifications[$i]['id']);
		}

		return;
	}

	function delete_product_business_content($business_id)
	{
		global $db, $config, $phpbb_root_path, $phpEx, $garage;

		include_once($phpbb_root_path . 'includes/mods/class_garage_modification.' . $phpEx);
		$modifications = $garage_modification->get_modifications_by_manufacturer_id($business_id);
		for ($i = 0, $count = sizeof($modifications);$i < $count; $i++)
		{
			$garage_modification->delete_modification($modifications[$i]['id']);
		}

		return;
	}

	function move_garage_business_content($from_id, $to_id)
	{
		global $garage;

		$garage->update_single_field(GARAGE_MODIFICATIONS_TABLE, 'installer_id', $to_id, 'installer_id', $from_id);

		return;
	}

	function move_insurance_business_content($from_id, $to_id)
	{
		global $garage;

		$garage->update_single_field(GARAGE_PREMIUMS_TABLE, 'business_id', $to_id, 'business_id', $from_id);

		return;
	}

	function move_dynocentre_business_content($from_id, $to_id)
	{
		global $garage;

		$garage->update_single_field(GARAGE_DYNORUNS_TABLE, 'dynocentre_id', $to_id, 'dynocentre_id', $from_id);

		return;
	}

	function move_retail_business_content($from_id, $to_id)
	{
		global $garage;

		$garage->update_single_field(GARAGE_MODIFICATIONS_TABLE, 'shop_id', $to_id, 'shop_id', $from_id);

		return;
	}

	function move_product_business_content($from_id, $to_id)
	{
		global $garage;

		$garage->update_single_field(GARAGE_MODIFICATIONS_TABLE, 'manufacturer_id', $to_id, 'manufacturer_id', $from_id);

		return;
	}
}
?>

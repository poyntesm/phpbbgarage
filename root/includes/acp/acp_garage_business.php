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
class acp_garage_business
{
	var $u_action;

	function main($id, $mode)
	{
		/**
		* Setup global variables such as $db 
		*/
		global $db, $user, $auth, $template, $cache, $garage, $garage_config;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx, $garage_business, $garage_template;

		/**
		* Build All Garage Classes e.g $garage_images->
		*/
		require($phpbb_root_path . 'includes/mods/class_garage_business.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);

		/**
		* Setup page variables such as title, template & available language strings
		*/
		$user->add_lang('acp/garage');
		$this->tpl_name = 'acp_garage_business';
		$this->page_title = 'ACP_MANAGE_BUSINESS';

		/**
		* Setup variables 
		*/
		$action		= request_var('action', '');
		$update		= (isset($_POST['update'])) ? true : false;
		$business_id	= request_var('id', 0);
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
				* Add a new business & log it
				*/
				case 'add':
					$params = array('title' => '', 'address' => '', 'telephone' => '', 'fax' => '', 'website' => '', 'email' => '', 'opening_hours' => '', 'insurance' => '', 'garage' => '', 'retail'  => '', 'product' => '', 'dynocentre'  => '', 'pending' => '0');
					$data = $garage->process_vars($params);

					if ( (!preg_match( "/^http:\/\//i", $data['website'])) AND (!empty($data['website'])) )
					{
						$data['website'] = "http://".$data['website'];
					}

					$garage_business->insert_business($data);
					add_log('admin', 'LOG_GARAGE_BUSINESS_CREATED', $data['title']);

					trigger_error($user->lang['BUSINESS_CREATED'] . adm_back_link($this->u_action));
				break;

				/**
				* Update an existing business & log it
				*/
				case 'edit':
					$params = array('title' => '', 'address' => '', 'telephone' => '', 'fax' => '', 'website' => '', 'email' => '', 'opening_hours' => '', 'insurance' => '', 'garage' => '', 'retail'  => '', 'product' => '', 'dynocentre'  => '', 'pending' => '0');
					$data = $garage->process_vars($params);
					$data['id'] = $business_id;

					if(!$data['title'])
					{
						$errors[] = $user->lang['BUSINESS_NAME_EMPTY'];
						break;
					}

					if ( (!preg_match( "/^http:\/\//i", $data['website'])) AND (!empty($data['website'])) )
					{
						$data['website'] = "http://".$data['website'];
					}
		
					$garage_business->update_business($data);
					add_log('admin', 'LOG_GARAGE_BUSINESS_UPDATED', $data['title']);

					trigger_error($user->lang['BUSINESS_UPDATED'] . adm_back_link($this->u_action));
				break;

				/**
				* Delete an existing business & log it
				*/
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

					$errors = $garage_business->delete_business($business_id, $action_garage, $garage_to_id, $action_insurance, $insurance_to_id, $action_dynocentre, $dynocentre_to_id, $action_retail, $retail_to_id, $action_product, $product_to_id);

					if (sizeof($errors))
					{
						break;
					}

					trigger_error($user->lang['BUSINESS_DELETED'] . adm_back_link($this->u_action));
				break;
			}
		}

		/**
		* Perform a set action based on value for $action
		*/
		switch ($action)
		{
			/**
			* Approve a business & log it
			*/
			case 'approve':
				$data = $garage_business->get_business($business_id);
				$garage->update_single_field(GARAGE_BUSINESS_TABLE, 'pending', 0, 'id', $business_id);
				add_log('admin', 'LOG_GARAGE_BUSINESS_APPROVED', $data['title']);
			break;

			/**
			* Disapprove a business & log it
			*/
			case 'disapprove':
				$data = $garage_business->get_business($business_id);
				$garage->update_single_field(GARAGE_BUSINESS_TABLE, 'pending', 1, 'id', $business_id);
				add_log('admin', 'LOG_GARAGE_BUSINESS_DISAPPROVED', $data['title']);
			break;

			/**
			* Display page to add or edit a business
			*/
			case 'add':
			case 'edit':
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

					/**
					* Fill business data with default values
					*/
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
				));
				return;
			break;

			/**
			* Display page to handle deletion of a business
			* Administrator's will have a choice to move linked items 
			*/
			case 'delete':
				if (!$business_id)
				{
					trigger_error($user->lang['NO_BUSINESS'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$business_data = $garage_business->get_business($business_id);

				if ($business_data['insurance'])
				{
					$insurance_data = $garage_business->get_business_by_type(BUSINESS_INSURANCE);
					$select_to = $garage_template->build_move_to($insurance_data, $business_id, 'title');
					$template->assign_vars(array(
						'S_TYPE_INSURER'		=> true,
						'S_MOVE_INSURER'		=> (!empty($select_to)) ? true : false,
						'S_MOVE_INSURER_OPTIONS'	=> $select_to,
					));
				}
				if ($business_data['dynocentre'])
				{
					$dynocentre_data = $garage_business->get_business_by_type(BUSINESS_DYNOCENTRE);
					$select_to = $garage_template->build_move_to($dynocentre_data, $business_id, 'title');
					$template->assign_vars(array(
						'S_TYPE_DYNOCENTRE'		=> true,
						'S_MOVE_DYNOCENTRE'		=> (!empty($select_to)) ? true : false ,
						'S_MOVE_DYNOCENTRE_OPTIONS'	=> $select_to,
					));
				}
				if ($business_data['retail'])
				{
					$retail_data = $garage_business->get_business_by_type(BUSINESS_RETAIL);
					$select_to = $garage_template->build_move_to($retail_data, $business_id, 'title');
					$template->assign_vars(array(
						'S_TYPE_RETAIL'			=> true,
						'S_MOVE_RETAIL'			=> (!empty($select_to)) ? true : false ,
						'S_MOVE_RETAIL_OPTIONS'	=> $select_to,
					));
				}
				if ($business_data['garage'])
				{
					$garage_data = $garage_business->get_business_by_type(BUSINESS_GARAGE);
					$select_to = $garage_template->build_move_to($garage_data, $business_id, 'title');
					$template->assign_vars(array(
						'S_TYPE_GARAGE'			=> true,
						'S_MOVE_GARAGE'			=> (!empty($select_to)) ? true : false ,
						'S_MOVE_GARAGE_OPTIONS'		=> $select_to,
					));
				}
				if ($business_data['product'])
				{
					$product_data = $garage_business->get_business_by_type(BUSINESS_PRODUCT);
					$select_to = $garage_template->build_move_to($product_data, $business_id, 'title');
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
					'ERROR_MSG'			=> (sizeof($errors)) ? implode('<br />', $errors) : '',
				));
			break;	
		}

		/**
		* Display default page to show list of business's
		* Process by business type. This means a business might be listed twice or more
		*/
		$types = array($user->lang['GARAGE'], $user->lang['INSURANCE'], $user->lang['RETAIL'], $user->lang['DYNOCENTRE'], $user->lang['MANUFACTURER']);
		$type = array(BUSINESS_GARAGE, BUSINESS_INSURANCE, BUSINESS_RETAIL, BUSINESS_DYNOCENTRE, BUSINESS_PRODUCT);
		for($i = 0; $i < count($types); $i++)
		{
			$template->assign_block_vars('type', array(
				'TYPE' 	=> $types[$i],
			));

			$data = $garage_business->get_business_by_type($type[$i]);
			for($j = 0; $j < count($data); $j++)
			{
				$url = $this->u_action . "&amp;id={$data[$j]['id']}";
				$template->assign_block_vars('type.business', array(
					'ID' 			=> $data[$j]['id'],
					'TITLE' 		=> $data[$j]['title'],
					'ADDRESS' 		=> $data[$j]['address'],
					'TELEPHONE' 		=> $data[$j]['telephone'],
					'FAX' 			=> $data[$j]['fax'],
					'WEBSITE' 		=> $data[$j]['website'],
					'EMAIL' 		=> $data[$j]['email'],
					'OPENING_HOURS' 	=> $data[$j]['opening_hours'],
					'S_DISAPPROVED'		=> ($data[$j]['pending'] == 1) ? true : false,
					'S_APPROVED'		=> ($data[$j]['pending'] == 0) ? true : false,
					'U_APPROVE'		=> $url . '&amp;action=approve',
					'U_DISAPPROVE'		=> $url . '&amp;action=disapprove',
					'U_EDIT'		=> $url . '&amp;action=edit',
					'U_DELETE'		=> $url . '&amp;action=delete',
				));
			}
		}
	}
}
?>

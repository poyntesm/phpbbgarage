<?php
/** 
*
* @package mcp
* @version $Id$
* @copyright (c) 2007 phpBB Garage
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
* garage
* Handling the garage moderation queue
* Allows moderators to approve, edit or disapprove items. If items are linked moderator can decide what to do with them
* @package mcp
*/
class mcp_garage
{
	var $p_master;
	var $u_action;

	function mcp_main(&$p_master)
	{
		$this->p_master = &$p_master;
	}

	function main($id, $mode)
	{
		/**
		* Setup global variables such as $db 
		*/
		global $auth, $db, $user, $template;
		global $config, $phpbb_root_path, $phpEx, $action;
		global $garage_config, $garage_template, $garage_vehicle, $garage_business, $garage_guestbook, $garage_track;
		global $garage_template, $garage, $data;

		/**
		* Setup page variables such as title, template & available language strings
		*/
		$this->page_title = 'MCP_GARAGE';
		$this->tpl_name = 'mcp_garage';
		$user->add_lang(array('mods/garage', 'acp/garage'));

		/**
		* Build All Garage Classes e.g $garage_images->
		*/
		include_once($phpbb_root_path . 'includes/mods/class_garage.' . $phpEx);
		include_once($phpbb_root_path . 'includes/mods/class_garage_model.' . $phpEx);
		include_once($phpbb_root_path . 'includes/mods/class_garage_business.' . $phpEx);
		include_once($phpbb_root_path . 'includes/mods/class_garage_quartermile.' . $phpEx);
		include_once($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);
		include_once($phpbb_root_path . 'includes/mods/class_garage_dynorun.' . $phpEx);
		include_once($phpbb_root_path . 'includes/mods/class_garage_guestbook.' . $phpEx);
		include_once($phpbb_root_path . 'includes/mods/class_garage_modification.' . $phpEx);
		include_once($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);
		include_once($phpbb_root_path . 'includes/mods/class_garage_track.' . $phpEx);
		include_once($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);
		include_once($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);

		/**
		* Little cheat to swap certain actions to modes 
		*/
		if (in_array($action, array('disapprove_make_confirm', 'disapprove_model_confirm', 'disapprove_business_confirm', 'disapprove_track_confirm', 'disapprove_product_confirm')))
		{
			$mode = $action;
		}

		/**
		* Since we need this so often lists get it here and check we always have one item to work on
		*/
		$id_list = request_var('id_list', array(0));

		/**
		* Perform a set action based on value for $action
		* An action is normally a DB action such as insert/update/delete
		*/
		switch ($action)
		{
			/**
			* Check one and only one vehicle selected and redirect to correct page
			*/
			case 'edit_vehicle':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_edit'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have one and only one vehicle to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_VEHICLE_SELECTED');
				}
				else if (sizeof($id_list) > 1)
				{
					trigger_error('TOO_MANY_SELECTED');
				}

				redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=edit_vehicle&amp;VID={$id_list[0]}&amp;redirect=MCP"));

			break;

			/**
			* Check one and only one make selected and build page to edit
			*/
			case 'edit_make':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_edit'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have one and only one make to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_MAKE_SELECTED');
				}
				else if (sizeof($id_list) > 1)
				{
					trigger_error('TOO_MANY_SELECTED');
				}

				/**
				* Get make from DB
				*/
				$data = $garage_model->get_make($id_list[0]);

				/**
				* Handle template declarations & assignments
				*/
				$template->assign_vars(array(
					'S_EDIT_MAKE'		=> true,
					'S_MCP_ACTION'		=> $this->u_action . "&amp;action=update_make",
					'MAKE'			=> $data['make'],
					'MAKE_ID'		=> $data['id'],
				));
				$mode="";
			break;

			/**
			* Check one and only one model selected and build page to edit
			*/
			case 'edit_model':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_edit'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have one and only one model to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_MODEL_SELECTED');
				}
				else if (sizeof($id_list) > 1)
				{
					trigger_error('TOO_MANY_SELECTED');
				}

				/**
				* Get model from DB
				*/
				$data = $garage_model->get_model($id_list[0]);

				/**
				* Handle template declarations & assignments
				*/				
				$template->assign_vars(array(
					'S_EDIT_MODEL'		=> true,
					'S_MCP_ACTION'		=> $this->u_action . "&amp;action=update_model",
					'MAKE'			=> $data['make'],
					'MAKE_ID'		=> $data['make_id'],
					'MODEL'			=> $data['model'],
					'MODEL_ID'		=> $data['id'],
				));
				$mode="";

			break;

			/**
			* Check one and only one business selected and build page to edit
			*/
			case 'edit_business':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_edit'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have one and only one business to work on
				*/	
				if (!sizeof($id_list))
				{
					trigger_error('NO_BUSINESS_SELECTED');
				}
				else if (sizeof($id_list) > 1)
				{
					trigger_error('TOO_MANY_SELECTED');
				}

				/**
				* Get business from DB
				*/
				$data = $garage_business->get_business($id_list[0]);

				/**
				* Handle template declarations & assignments
				*/				
				$template->assign_vars(array(
					'S_DISPLAY_PENDING' 	=> $garage_config['enable_business_approval'],
					'S_BUSINESS_INSURANCE' 	=> ($data['insurance']) ? true : false,
					'S_BUSINESS_GARAGE' 	=> ($data['garage']) ? true : false,
					'S_BUSINESS_RETAIL' 	=> ($data['retail']) ? true : false,
					'S_BUSINESS_PRODUCT' 	=> ($data['product']) ? true : false,
					'S_BUSINESS_DYNOCENTRE'	=> ($data['dynocentre']) ? true : false,
					'S_MCP_ACTION'		=> $this->u_action . "&amp;action=update_business",
					'S_EDIT_BUSINESS'	=> true,
					'TITLE' 		=> $data['title'],
					'ADDRESS'		=> $data['address'],
					'TELEPHONE'		=> $data['telephone'],
					'FAX'			=> $data['fax'],
					'WEBSITE'		=> $data['website'],
					'EMAIL'			=> $data['email'],
					'OPENING_HOURS'		=> $data['opening_hours'],
					'BUSINESS_ID'		=> $data['id'],
				));
				$mode = "";

			break;

			/**
			* Check one and only one dynorun selected and redirect to correct page
			*/
			case 'edit_dynorun':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_edit'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have one and only one dynorun to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_DYNORUN_SELECTED');
				}
				else if (sizeof($id_list) > 1)
				{
					trigger_error('TOO_MANY_SELECTED');
				}

				/**
				* Get vehicle id for dynorun from DB
				*/
				$vid = $garage_dynorun->get_vehicle_id_for_dynorun($id_list[0]);

				redirect(append_sid("{$phpbb_root_path}garage_dynorun.$phpEx", "mode=edit_dynorun&amp;VID=$vid&amp;DID={$id_list[0]}&amp;redirect=MCP"));

			break;

			/**
			* Check one and only one quartermile selected and redirect to correct page
			*/
			case 'edit_quartermile':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_edit'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have one and only one quartermile to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_QUARTERMILE_SELECTED');
				}
				else if (sizeof($id_list) > 1)
				{
					trigger_error('TOO_MANY_SELECTED');
				}

				/**
				* Get vehicle id for quartermile from DB
				*/
				$vid = $garage_quartermile->get_vehicle_id_for_quartermile($id_list[0]);

				redirect(append_sid("{$phpbb_root_path}garage_quartermile.$phpEx", "mode=edit_quartermile&amp;VID=$vid&amp;QMID={$id_list[0]}&amp;redirect=MCP"));

			break;

			/**
			* Check one and only one lap selected and redirect to correct page
			*/
			case 'edit_lap':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_edit'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have one and only one lap to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_LAP_SELECTED');
				}
				else if (sizeof($id_list) > 1)
				{
					trigger_error('TOO_MANY_SELECTED');
				}

				/**
				* Get vehicle id for lap from DB
				*/
				$vid = $garage_track->get_vehicle_id_for_lap($id_list[0]);

				redirect(append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=edit_lap&amp;VID=$vid&amp;LID={$id_list[0]}&amp;redirect=MCP"));

				break;

			/**
			* Check one and only one track selected and build page to edit
			*/
			case 'edit_track':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_edit'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have one and only one track to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_TRACK_SELECTED');
				}
				else if (sizeof($id_list) > 1)
				{
					trigger_error('TOO_MANY_SELECTED');
				}

				/**
				* Get track from DB
				*/
				$data = $garage_track->get_track($id_list[0]);

				/**
				* Handle template declarations & assignments
				*/				
				$garage_template->mileage_dropdown($data['mileage_unit']);
				$template->assign_vars(array(
					'S_EDIT_TRACK'		=> true,
					'S_MCP_ACTION'		=> $this->u_action . "&amp;action=update_track",
					'TRACK_ID'		=> $$data['id'],
					'TITLE'			=> $data['title'],
					'LENGTH'		=> $data['length'],
				));
				$mode = ""; 

			break;

			/**
			* Check one and only one product selected and build page to edit
			*/
			case 'edit_product':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_edit'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have one and only one product to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_PRODUCT_SELECTED');
				}
				else if (sizeof($id_list) > 1)
				{
					trigger_error('TOO_MANY_SELECTED');
				}

				/**
				* Get product, categories and manyfacturers from DB
				*/
				$data 		= $garage_modification->get_product($id_list[0]);
				$categories 	= $garage->get_categories();
				$manufacturers 	= $garage_business->get_business_by_type(BUSINESS_PRODUCT);

				/**
				* Handle template declarations & assignments
				*/				
				$garage_template->category_dropdown($categories, $data['category_id']);
				$garage_template->manufacturer_dropdown($manufacturers, $data['business_id']);
				$template->assign_vars(array(
					'S_EDIT_PRODUCT'	=> true,
					'S_MCP_ACTION'		=> $this->u_action . "&amp;action=update_product",
					'PRODUCT_ID'		=> $id_list[0],
					'TITLE'			=> $data['title'],
				));
				$mode = ""; 

			break;

			/**
			* Update moderated make and redirect to unapproved list
			*/
			case 'update_make':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_edit'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Get all required/optional data 
				*/
				$params = array('id' => '');
				$data 	= $garage->process_vars($params);
				$params = array('make' => '');
				$data 	+= $garage->process_mb_vars($params);

				/**
				* Check required data is present
				*/
				$params = array('id', 'make');
				$garage->check_required_vars($params);

				/**
				* Perform required DB work to update make
				*/
				$garage_model->update_make($data);

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_makes"));

			break;

			/**
			* Update moderated model and redirect to unapproved list
			*/
			case 'update_model':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_edit'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Get all required/optional data
				*/
				$params = array('id' => '');
				$data 	= $garage->process_vars($params);
				$params = array('model' => '');
				$data 	+= $garage->process_mb_vars($params);

				/**
				* Check required data is present
				*/
				$params = array('id', 'model');
				$garage->check_required_vars($params);

				/**
				* Perform required DB work to update model
				*/
				$garage_model->update_model($data);

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_models"));

			break;

			/**
			* Update moderated business and redirect to unapproved list
			*/
			case 'update_business':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_edit'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Get all required/optional data
				*/
				$params = array('id' => '', 'telephone' => '', 'fax' => '', 'website' => '', 'email' => '', 'product' => 0, 'insurance' => 0, 'garage' => 0, 'retail' => 0, 'dynocentre' => 0);
				$data 	= $garage->process_vars($params);
				$params = array('title' => '', 'address' => '', 'opening_hours' => '');
				$data 	+= $garage->process_mb_vars($params);

				/**
				* Make sure website data is correctly formed
				*/
				if ( (!preg_match( "/^http:\/\//i", $data['website'])) AND (!empty($data['website'])) )
				{
					$data['website'] = "http://" . $data['website'];
				}

				/**
				* Check required data is present
				*/
				$params = array('title');
				$garage->check_required_vars($params);

				/**
				* Perform required DB work to update business
				*/
				$garage_business->update_business($data);

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_business"));

			break;

			/**
			* Update moderated track and redirect to unapproved list
			*/		
			case 'update_track':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_edit'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Get all required/optional data
				*/
				$params = array('id' => '', 'length' => '', 'mileage_unit' => '');
				$data 	= $garage->process_vars($params);
				$params = array('title' => '');
				$data 	+= $garage->process_mb_vars($params);

				/**
				* Check required data is present
				*/
				$params = array('id', 'title');
				$garage->check_required_vars($params);

				/**
				* Perform required DB work to update track
				*/
				$garage_track->update_track($data);

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_tracks"));

			break;

			/**
			* Update moderated product and redirect to unapproved list
			*/				
			case 'update_product':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_edit'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Get all required/optional data
				*/
				$params = array('id' => '', 'category_id' => '', 'manufacturer_id' => '');
				$data 	= $garage->process_vars($params);
				$params = array('title' => '');
				$data 	+= $garage->process_mb_vars($params);

				/**
				* Check required data is present
				*/
				$params = array('id', 'title', 'category_id', 'manufacturer_id');
				$garage->check_required_vars($params);

				/**
				* Perform required DB work to update product
				*/
				$garage_modification->update_product($data);

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_products"));
			break;

			/**
			* Approve all vehicles contained with an array recieved from page
			*/
			case 'approve_vehicle':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_approve_vehicle'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have at least one vehicle to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_VEHICLE_SELECTED');
				}
				
				/**
				* For each ID in array update the pending column in the DB
				*/
				for($i = 0; $i < count($id_list); $i++)
				{
					$garage->update_single_field(GARAGE_VEHICLES_TABLE, 'pending', 0, 'id', $id_list[$i]);
				}
	
				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_vehicles"));
			break;

			/**
			* Approve all makes contained with an array recieved from page
			*/
			case 'approve_make':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_approve_make'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have at least one make to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_MAKE_SELECTED');
				}

				/**
				* For each ID in array update the pending column in the DB
				*/
				for($i = 0; $i < count($id_list); $i++)
				{
					$garage->update_single_field(GARAGE_MAKES_TABLE, 'pending', 0, 'id', $id_list[$i]);
				}

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_makes"));
			break;
			
			/**
			* Approve all models contained with an array recieved from page
			*/
			case 'approve_model':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_approve_model'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have at least one model to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_MODEL_SELECTED');
				}

				/**
				* For each ID in array update the pending column in the DB
				*/
				for($i = 0; $i < count($id_list); $i++)
				{
					$garage->update_single_field(GARAGE_MODELS_TABLE, 'pending', 0, 'id', $id_list[$i]);
				}

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_models"));
			break;

			/**
			* Approve all business's contained with an array recieved from page
			*/
			case 'approve_business':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_approve_business'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have at least one business to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_BUSINESS_SELECTED');
				}

				/**
				* For each ID in array update the pending column in the DB
				*/				
				for($i = 0; $i < count($id_list); $i++)
				{
					$garage->update_single_field(GARAGE_BUSINESS_TABLE, 'pending', 0, 'id', $id_list[$i]);
				}

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_business"));

			break;
			
			/**
			* Approve all quartermile times contained with an array recieved from page
			*/
			case 'approve_quartermile':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_approve_quartermile'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have at least one quartermile to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_QUARTERMILE_SELECTED');
				}

				/**
				* For each ID in array update the pending column in the DB
				*/
				for($i = 0; $i < count($id_list); $i++)
				{
					$garage->update_single_field(GARAGE_QUARTERMILES_TABLE, 'pending', 0, 'id', $id_list[$i]);
				}

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_quartermiles"));
			break;
			
			/**
			* Approve all dynoruns contained with an array recieved from page
			*/
			case 'approve_dynorun':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_approve_dynorun'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have at least one dynorun to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_DYNORUN_SELECTED');
				}

				/**
				* For each ID in array update the pending column in the DB
				*/				
				for($i = 0; $i < count($id_list); $i++)
				{
					$garage->update_single_field(GARAGE_DYNORUNS_TABLE, 'pending', 0, 'id', $id_list[$i]);
				}

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_dynoruns"));
			break;
			
			/**
			* Approve all guestbook comments contained with an array recieved from page
			*/
			case 'approve_comment':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_approve_guestbook'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have at least one comment to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_COMMENT_SELECTED');
				}

				/**
				* For each ID in array update the pending column in the DB
				*/
				for($i = 0; $i < count($id_list); $i++)
				{
					$garage->update_single_field(GARAGE_GUESTBOOKS_TABLE, 'pending', 0, 'id', $id_list[$i]);
				}

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_comments"));
			break;
			
			/**
			* Approve all lap times contained with an array recieved from page
			*/
			case 'approve_lap':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_approve_lap'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have at least one lap to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_LAP_SELECTED');
				}

				/**
				* For each ID in array update the pending column in the DB
				*/
				for($i = 0; $i < count($id_list); $i++)
				{
					$garage->update_single_field(GARAGE_LAPS_TABLE, 'pending', 0, 'id', $id_list[$i]);
				}

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_laps"));
			break;
			
			/**
			* Approve all tracks contained with an array recieved from page
			*/
			case 'approve_track':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_approve_track'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have at least one track to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_TRACK_SELECTED');
				}

				/**
				* For each ID in array update the pending column in the DB
				*/
				for($i = 0; $i < count($id_list); $i++)
				{
					$garage->update_single_field(GARAGE_TRACKS_TABLE, 'pending', 0, 'id', $id_list[$i]);
				}

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_tracks"));
			break;

			/**
			* Approve all modification products contained with an array recieved from page
			*/
			case 'approve_product':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_approve_product'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have at least one track to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_PRODUCT_SELECTED');
				}

				/**
				* For each ID in array update the pending column in the DB
				*/
				for($i = 0; $i < count($id_list); $i++)
				{
					$garage->update_single_field(GARAGE_PRODUCTS_TABLE, 'pending', 0, 'id', $id_list[$i]);
				}

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_products"));
			break;
			
			/**
			* Disappove all vehicles contained with an array recieved from page
			* Dispproving a vehicle deletes it and all its items
			*/
			case 'disapprove_vehicle':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_delete'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have at least one vehicle to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_VEHICLE_SELECTED');
				}

				/**
				* For each ID in array call function to delete vehicle (deletes all linked items)
				*/
				for($i = 0; $i < count($id_list); $i++)
				{
					$garage_vehicle->delete_vehicle($id_list[$i]);
				}

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_vehicles"));;
			break;
			
			/**
			* Disappove a single make contained with an array recieved from page
			* Moderators will have chosen to delete or move linked items
			*/
			case 'disapprove_make':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_delete'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Get all required/optional data
				*/
				$params = array('make_id' => '', 'action_make' => '', 'make_to_id' => 0);
				$data = $garage->process_vars($params);

				/**
				* Call function to delete make, depending on options linked/child items will be re-assigned
				*/
				$garage_model->delete_make($data['make_id'], $data['action_make'], $data['make_to_id']);

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_makes"));
			break;
			
			/**
			* Disappove a single model contained with an array recieved from page
			* Moderators will have chosen to delete or move linked items
			*/
			case 'disapprove_model':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_delete'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Get all required/optional data
				*/
				$params = array('model_id' => '', 'action_model' => '', 'model_to_id' => 0);
				$data = $garage->process_vars($params);

				/**
				* Call function to delete model, depending on options linked/child items will be re-assigned
				*/
				$garage_model->delete_model($data['model_id'], $data['action_model'], $data['model_to_id']);

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_models"));
			break;
			
			/**
			* Disappove a single business contained with an array recieved from page
			* Moderators will have chosen to delete or move linked items
			*/
			case 'disapprove_business':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_delete'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Get all required/optional data
				*/
				$params = array('business_id' => '', 'action_garage' => '', 'action_insurance' => '', 'action_dynocentre' => '', 'action_retail' => '', 'action_product' => '', 'garage_to_id' => 0, 'insurance_to_id' => 0, 'dynocentre_to_id' => 0, 'retail_to_id' => 0, 'product_to_id' => 0);
				$data = $garage->process_vars($params);

				/**
				* Call function to delete business, depending on options linked/child items will be re-assigned
				*/
				$garage_business->delete_business($data['business_id'], $data['action_garage'], $data['garage_to_id'], $data['action_insurance'], $data['insurance_to_id'], $data['action_dynocentre'], $data['dynocentre_to_id'], $data['action_retail'], $data['retail_to_id'], $data['action_product'], $data['product_to_id']);

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_business"));
			break;
			
			/**
			* Disappove all quartermile times contained with an array recieved from page
			* Dispproving a quartermile time deletes it and all its images
			*/
			case 'disapprove_quartermile':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_delete'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have at least one quartermile to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_QUARTERMILE_SELECTED');
				}

				/**
				* For each ID in array call function to delete quartermile and linked images
				*/
				for($i = 0; $i < count($id_list); $i++)
				{
					$garage_quartermile->delete_quartermile($id_list[$i]);
				}

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_quartermiles"));

			break;
			
			/**
			* Disappove all dynoruns contained with an array recieved from page
			* Dispproving a dynorun deletes it and all its images
			*/
			case 'disapprove_dynorun':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_delete'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have at least one dynorun to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_DYNORUN_SELECTED');
				}

				/**
				* For each ID in array call function to delete dynorun and linked images
				*/
				for($i = 0; $i < count($id_list); $i++)
				{
					$garage_dynorun->delete_dynorun($id_list[$i]);
				}

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_dynoruns"));
			break;
			
			/**
			* Disappove all guestbook comments contained with an array recieved from page
			* Dispproving a comment deletes it
			*/
			case 'disapprove_comment':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_delete'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have at least one comment to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_COMMENT_SELECTED');
				}

				/**
				* For each ID in array call function to delete comment
				*/
				for($i = 0; $i < count($id_list); $i++)
				{
					$garage_guestbook->delete_comment($id_list[$i]);
				}

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_comments"));
			break;
			
			/**
			* Disappove all laps contained with an array recieved from page
			* Dispproving a lap deletes it and all its images
			*/
			case 'disapprove_lap':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_delete'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Check we have at least one lap to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_LAP_SELECTED');
				}

				/**
				* For each ID in array call function to delete lap and linked images
				*/
				for($i = 0; $i < count($id_list); $i++)
				{
					$garage_track->delete_lap($id_list[$i]);
				}

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_laps"));
			break;

			/**
			* Disappove a single track contained with an array recieved from page
			* Moderators will have chosen to delete or move linked items
			*/
			case 'disapprove_track':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_delete'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Get all required/optional data
				*/
				$params = array('track_id' => '', 'action_laps' => '', 'laps_to_id' => 0);
				$data = $garage->process_vars($params);

				/**
				* Call function to delete track, depending on options linked/child items will be re-assigned
				*/
				$garage_track->delete_track($data['track_id'], $data['action_laps'], $data['laps_to_id']);

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_tracks"));
			break;

			/**
			* Disappove a single modification product contained with an array recieved from page
			* Moderators will have chosen to delete or move linked items
			*/
			case 'disapprove_product':

				/**
				* Check authorisation to perform action, redirecting to error screen if not
				*/
				if (!$auth->acl_get('m_garage_delete'))
				{
					redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
				}

				/**
				* Get all required/optional data
				*/
				$params = array('product_id' => '', 'action_modifications' => '', 'modifications_to_id' => 0);
				$data = $garage->process_vars($params);

				/**
				* Call function to delete product, depending on options linked/child items will be re-assigned
				*/
				$garage_modification->delete_product($data['product_id'], $data['action_modifications'], $data['modifications_to_id']);

				redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_products"));
			break;
		}

		/**
		* Perform a set action based on value for $mode
		* Normally this invloves all work required to display a page
		*/
		switch ($mode)
		{
			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_vehicles':
				
				/**
				* Get pending vehicles from DB
				*/
				$data = $garage_vehicle->get_pending_vehicles();

				/**
				* Loop through all pending items and handle template assignments
				*/
				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					
					$template->assign_block_vars('vehicle_row', array(
						'U_PROFILE'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $data[$i]['user_id']),
						'USERNAME'		=> $data[$i]['username'],
						'USERNAME_COLOUR'	=> get_username_string('colour', $data[$i]['user_id'], $data[$i]['username'], $data[$i]['user_colour']),
						'ID'			=> $data[$i]['id'],
						'MAKE'			=> $data[$i]['make'],
						'MODEL'			=> $data[$i]['model'],
						'ENGINE_TYPE'		=> $garage_vehicle->get_engine_type($data[$i]['engine_type']),
						'COLOUR'		=> $data[$i]['colour'],
						'PRICE'			=> $data[$i]['price'],
						'CURRENCY'		=> $data[$i]['currency'],
						'MILEAGE'		=> $data[$i]['mileage'],
						'MILEAGE_UNIT'		=> $data[$i]['mileage_unit'],
					));
				}

				/**
				* Handle template declarations & assignments
				*/
				$template->assign_vars(array(
					'S_MCP_ACTION'		=> build_url(),
					'S_GARAGE_EDIT'		=> $auth->acl_get('m_garage_edit') ? true : false,
					'S_GARAGE_DELETE'	=> $auth->acl_get('m_garage_delete') ? true : false,
					'S_UNAPPROVED_VEHICLE' 	=> true,
				));
			break;

			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_makes':

				/**
				* Get pending makes from DB
				*/
				$data = $garage_model->get_pending_makes();

				/**
				* Loop through all pending items and handle template assignments
				*/
				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('makes_row', array(
						'ID'	=> $data[$i]['id'],
						'MAKE'	=> $data[$i]['make'])
					);
				}

				/**
				* Handle template declarations & assignments
				*/
				$template->assign_vars(array(
					'S_MCP_ACTION'		=> build_url(),
					'S_GARAGE_EDIT'		=> $auth->acl_get('m_garage_edit') ? true : false,
					'S_GARAGE_DELETE'	=> $auth->acl_get('m_garage_delete') ? true : false,
					'S_UNAPPROVED_MAKE' 	=> true,
				));
			break;

			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_models':

				/**
				* Get pending models from DB
				*/				
				$data = $garage_model->get_pending_models();

				/**
				* Loop through all pending items and handle template assignments
				*/
				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('models_row', array(
						'ID'	=> $data[$i]['id'],
						'MODEL'	=> $data[$i]['model'],
						'MAKE'	=> $data[$i]['make'])
					);
				}

				/**
				* Handle template declarations & assignments
				*/				
				$template->assign_vars(array(
					'S_MCP_ACTION'		=> build_url(),
					'S_GARAGE_EDIT'		=> $auth->acl_get('m_garage_edit') ? true : false,
					'S_GARAGE_DELETE'	=> $auth->acl_get('m_garage_delete') ? true : false,
					'S_UNAPPROVED_MODEL' 	=> true,
				));
			break;

			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_business':

				/**
				* Get pending business's from DB
				*/
				$data = $garage_business->get_pending_business();

				/**
				* Loop through all pending items and handle template assignments
				*/
				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$type = ($data[$i]['insurance']) ? $user->lang['INSURANCE'] . ', ' : '';
					$type .= ($data[$i]['garage']) ? $user->lang['GARAGE'] . ', ' : '';
					$type .= ($data[$i]['retail']) ? $user->lang['RETAIL'] . ', ' : '';
					$type .= ($data[$i]['product']) ? $user->lang['MANUFACTURER'] . ', ' : '';
					$type .= ($data[$i]['dynocentre']) ? $user->lang['DYNOCENTRE'] . ', ' : '';
					$type = rtrim($type, ', ');

					$template->assign_block_vars('business_row', array(
						'ID'		=> $data[$i]['id'],
						'TITLE'		=> $data[$i]['title'],
						'ADDRESS'	=> $data[$i]['address'],
						'TELEPHONE'	=> $data[$i]['telephone'],
						'FAX'		=> $data[$i]['fax'],
						'WEBSITE'	=> $data[$i]['website'],
						'EMAIL'		=> $data[$i]['email'],
						'OPENING_HOURS'	=> $data[$i]['opening_hours'],
						'TYPE'		=> $type,
					));
				}

				/**
				* Handle template declarations & assignments
				*/				
				$template->assign_vars(array(
					'S_MCP_ACTION'		=> build_url(),
					'S_GARAGE_EDIT'		=> $auth->acl_get('m_garage_edit') ? true : false,
					'S_GARAGE_DELETE'	=> $auth->acl_get('m_garage_delete') ? true : false,
					'S_UNAPPROVED_BUSINESS' => true,
				));
			break;

			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_quartermiles':

				/**
				* Get pending quartermiles from DB
				*/
				$data = $garage_quartermile->get_pending_quartermiles();

				/**
				* Loop through all pending items and handle template assignments
				*/
				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('quartermile_row', array(
						'U_PROFILE'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $data[$i]['user_id']),
						'U_VEHICLE'		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;VID=" . $data[$i]['vehicle_id']),
						'ID'			=> $data[$i]['qmid'],
						'USERNAME'		=> $data[$i]['username'],
						'USERNAME_COLOUR'	=> get_username_string('colour', $data[$i]['user_id'], $data[$i]['username'], $data[$i]['user_colour']),
						'VEHICLE'		=> $data[$i]['vehicle'],
						'RT'			=> $data[$i]['rt'],
						'SIXTY'			=> $data[$i]['sixty'],
						'THREE'			=> $data[$i]['three'],
						'EIGHTH'		=> $data[$i]['eighth'],
						'EIGHTHMPH'		=> $data[$i]['eighthmph'],
						'THOU'			=> $data[$i]['thou'],
						'QUART'			=> $data[$i]['quart'],
						'QUARTMPH'		=> $data[$i]['quartmph'],
					));
				}

				/**
				* Handle template declarations & assignments
				*/				
				$template->assign_vars(array(
					'S_MCP_ACTION'			=> build_url(),
					'S_GARAGE_EDIT'			=> $auth->acl_get('m_garage_edit') ? true : false,
					'S_GARAGE_DELETE'		=> $auth->acl_get('m_garage_delete') ? true : false,
					'S_UNAPPROVED_QUARTERMILE' 	=> true,
				));
			break;

			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_dynoruns':

				/**
				* Get pending dynoruns from DB
				*/
				$data = $garage_dynorun->get_pending_dynoruns();

				/**
				* Loop through all pending items and handle template assignments
				*/
				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('dynorun_row', array(
						'U_PROFILE'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $data[$i]['user_id']),
						'U_VEHICLE'		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;VID=" . $data[$i]['id']),
						'ID'			=> $data[$i]['did'],
						'USERNAME'		=> $data[$i]['username'],
						'USERNAME_COLOUR'	=> get_username_string('colour', $data[$i]['user_id'], $data[$i]['username'], $data[$i]['user_colour']),
						'VEHICLE'		=> $data[$i]['vehicle'],
						'DYNOCENTER'		=> $data[$i]['title'],
						'BHP'			=> $data[$i]['bhp'],
						'BHP_UNIT'		=> $data[$i]['bhp_unit'],
						'TORQUE'		=> $data[$i]['torque'],
						'TORQUE_UNIT'		=> $data[$i]['torque_unit'],
						'BOOST'			=> $data[$i]['boost'],
						'BOOST_UNIT'		=> $data[$i]['boost_unit'],
						'NITROUS'		=> $data[$i]['nitrous'],
						'PEAKPOINT'		=> $data[$i]['peakpoint'],
					));
				}

				/**
				* Handle template declarations & assignments
				*/
				$template->assign_vars(array(
					'S_MCP_ACTION'		=> build_url(),
					'S_GARAGE_EDIT'		=> $auth->acl_get('m_garage_edit') ? true : false,
					'S_GARAGE_DELETE'	=> $auth->acl_get('m_garage_delete') ? true : false,
					'S_UNAPPROVED_DYNORUN' 	=> true,
				));
			break;

			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_laps':

				/**
				* Get pending laps from DB
				*/
				$data = $garage_track->get_pending_laps();

				/**
				* Loop through all pending items and handle template assignments
				*/
				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('lap_row', array(
						'ID'			=> $data[$i]['lid'],
						'TRACK'			=> $data[$i]['title'],
						'CONDITION'		=> $garage_track->get_track_condition($data[$i]['condition_id']),
						'TYPE'			=> $garage_track->get_lap_type($data[$i]['type_id']),
						'MINUTE'		=> $data[$i]['minute'],
						'SECOND'		=> $data[$i]['second'],
						'MILLISECOND'		=> $data[$i]['millisecond'],
						'USERNAME'		=> $data[$i]['username'],
						'USERNAME_COLOUR'	=> get_username_string('colour', $data[$i]['user_id'], $data[$i]['username'], $data[$i]['user_colour']),
						'VEHICLE'		=> $data[$i]['vehicle'],
						'IMAGE'			=> $user->img('garage_img_attached', 'IMAGE_ATTACHED'),
						'U_PROFILE'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $data[$i]['user_id']),
						'U_VEHICLE'		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;VID=" . $data[$i]['id']),
						'U_IMAGE'		=> ($data[$i]['attach_id']) ? append_sid("garage.$phpEx", "mode=view_image&amp;image_id=". $data[$i]['attach_id']) : '',
						'U_TRACK'		=> append_sid("garage_track.$phpEx?mode=view_track&amp;TID=".$data[$i]['track_id']."&amp;VID=". $data[$i]['vehicle_id']),
						'U_LAP'			=> append_sid("garage_track.$phpEx?mode=view_lap&amp;LID=".$data[$i]['lid']."&amp;VID=". $data[$i]['vehicle_id']),
					));
				}

				/**
				* Handle template declarations & assignments
				*/
				$template->assign_vars(array(
					'S_MCP_ACTION'		=> build_url(),
					'S_GARAGE_EDIT'		=> $auth->acl_get('m_garage_edit') ? true : false,
					'S_GARAGE_DELETE'	=> $auth->acl_get('m_garage_delete') ? true : false,
					'S_UNAPPROVED_LAP' 	=> true,
				));
			break;

			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_tracks':

				/**
				* Get pending tracks from DB
				*/				
				$data = $garage_track->get_pending_tracks();

				/**
				* Loop through all pending items and handle template assignments
				*/
				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('track_row', array(
						'ID'		=> $data[$i]['id'],
						'TITLE'		=> $data[$i]['title'],
						'LENGTH'	=> $data[$i]['length'],
						'MILEAGE_UNIT'	=> $data[$i]['mileage_unit'],
					));
				}

				/**
				* Handle template declarations & assignments
				*/				
				$template->assign_vars(array(
					'S_MCP_ACTION'		=> build_url(),
					'S_GARAGE_EDIT'		=> $auth->acl_get('m_garage_edit') ? true : false,
					'S_GARAGE_DELETE'	=> $auth->acl_get('m_garage_delete') ? true : false,
					'S_UNAPPROVED_TRACK' 	=> true,
				));
			break;

			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_guestbook_comments':

				/**
				* Get pending comments from DB
				*/
				$data = $garage_guestbook->get_pending_comments();

				/**
				* Loop through all pending items and handle template assignments
				*/
				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$message = generate_text_for_display($data[$i]['post'], $data[$i]['bbcode_uid'], $data[$i]['bbcode_bitfield'], $data[$i]['bbcode_options']);
					$template->assign_block_vars('comment_row', array(
						'U_PROFILE'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $data[$i]['user_id']),
						'U_VEHICLE'		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;VID=" . $data[$i]['vehicle_id']),
						'AUTHOR'		=> $data[$i]['username'],
						'USERNAME_COLOUR'	=> get_username_string('colour', $data[$i]['user_id'], $data[$i]['username'], $data[$i]['user_colour']),
						'VEHICLE'		=> $data[$i]['vehicle'],
						'POST_TIME'		=> $user->format_date($data[$i]['post_date']),
						'POST'			=> $message,
						'ID'			=> $data[$i]['comment_id'],
					));
				}

				/**
				* Handle template declarations & assignments
				*/
				$template->assign_vars(array(
					'S_MCP_ACTION'		=> build_url(),
					'S_GARAGE_EDIT'		=> $auth->acl_get('m_garage_edit') ? true : false,
					'S_GARAGE_DELETE'	=> $auth->acl_get('m_garage_delete') ? true : false,
					'S_UNAPPROVED_COMMENT' 	=> true,
				));
			break;

			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_products':

				/**
				* Get pending products from DB
				*/
				$data = $garage_modification->get_pending_products();
				
				/**
				* Loop through all pending items and handle template assignments
				*/
				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('products_row', array(
						'PRODUCT'	=> $data[$i]['product'],
						'MANUFACTURER'	=> $data[$i]['manufacturer'],
						'CATEGORY'	=> $data[$i]['category'],
						'ID'		=> $data[$i]['id'],
					));
				}

				/**
				* Handle template declarations & assignments
				*/				
				$template->assign_vars(array(
					'S_MCP_ACTION'		=> build_url(),
					'S_GARAGE_EDIT'		=> $auth->acl_get('m_garage_edit') ? true : false,
					'S_GARAGE_DELETE'	=> $auth->acl_get('m_garage_delete') ? true : false,
					'S_UNAPPROVED_PRODUCT' 	=> true,
				));
			break;

			/**
			* Produce page to allow moderator decide on how linked items are handled
			* Linked items are models & vehicles. 
			* Models & vehicles can be moved to another make or deleted
			*/
			case 'disapprove_make_confirm':

				/**
				* Check we have one and only one make to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_MAKE_SELECTED');
				}
				else if (sizeof($id_list) != 1)
				{
					trigger_error('SELECT_ONE_ONLY');
				}

				/**
				* Get specific and all makes from DB
				*/
				$make_data = $garage_model->get_make($id_list[0]);
				$makes_data = $garage_model->get_all_makes();

				/**
				* Handle template declarations & assignments
				*/
				$template->assign_vars(array(
					'S_MCP_ACTION'		=> $this->u_action . "&amp;action=disapprove_make&amp;make_id=" . $id_list[0],
					'S_DELETE_MAKE'		=> true,
					'S_MOVE_OPTIONS'	=> $garage_template->build_move_to($makes_data, $id_list[0], 'make'),
					'MAKE'			=> $make_data['make'],
				));
			break;

			/**
			* Produce page to allow moderator decide on how linked items are handled
			* Linked are vehicles. 
			* Vehicles can be moved to another model of the same make or deleted
			*/
			case 'disapprove_model_confirm':

				/**
				* Check we have one and only one model to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_MODEL_SELECTED');
				}
				else if (sizeof($id_list) != 1)
				{
					trigger_error('SELECT_ONE_ONLY');
				}

				/**
				* Get specific and all models from DB
				*/
				$model_data = $garage_model->get_model($id_list[0]);
				$models_data = $garage_model->get_all_models_from_make($model_data['make_id']);

				/**
				* Handle template declarations & assignments
				*/				
				$template->assign_vars(array(
					'S_MCP_ACTION'		=> $this->u_action . "&amp;action=disapprove_model&amp;model_id=".$id_list[0]."&amp;make_id=" . $model_data['make_id'],
					'S_DELETE_MODEL'	=> true,
					'S_MOVE_OPTIONS'	=> $garage_template->build_move_to($models_data, $id_list[0], 'model'),
					'MODEL'			=> $model_data['model'],
				));
			break;

			/**
			* Produce page to allow moderator decide on how linked items are handled
			* Linked are modifications (installed, purchased & product), premiums, dynoruns. 
			* Modifications can be moved to another business (garage, shop & manufacturer) or deleted
			* Premiums can be moved to another insurer or deleted
			* Dynoruns can be moved to another dynocentre or deleted
			*/
			case 'disapprove_business_confirm':

				/**
				* Check we have one and only one business to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_BUSINESS_SELECTED');
				}
				else if (sizeof($id_list) != 1)
				{
					trigger_error('SELECT_ONE_ONLY');
				}

				/**
				* Get specific and all business types from DB
				*/
				$business_data = $garage_business->get_business($id_list[0]);
				$insurance_data = $garage_business->get_business_by_type(BUSINESS_INSURANCE);
				$dynocentre_data = $garage_business->get_business_by_type(BUSINESS_DYNOCENTRE);
				$retail_data = $garage_business->get_business_by_type(BUSINESS_RETAIL);
				$garage_data = $garage_business->get_business_by_type(BUSINESS_GARAGE);
				$product_data = $garage_business->get_business_by_type(BUSINESS_PRODUCT);

				if ($business_data['insurance'])
				{
					$template->assign_vars(array(
						'S_TYPE_INSURER'		=> true,
						'S_MOVE_INSURER_OPTIONS'	=> $garage_template->build_move_to($insurance_data, $id_list[0], 'title'),
					));
				}
				if ($business_data['dynocentre'])
				{
					$template->assign_vars(array(
						'S_TYPE_DYNOCENTRE'		=> true,
						'S_MOVE_DYNOCENTRE_OPTIONS'	=> $garage_template->build_move_to($dynocentre_data, $id_list[0], 'title'),
					));
				}
				if ($business_data['retail'])
				{
					$template->assign_vars(array(
						'S_TYPE_RETAIL'		=> true,
						'S_MOVE_RETAIL_OPTIONS'	=> $garage_template->build_move_to($retail_data, $id_list[0], 'title'),
					));
				}
				if ($business_data['garage'])
				{
					$template->assign_vars(array(
						'S_TYPE_GARAGE'		=> true,
						'S_MOVE_GARAGE_OPTIONS'	=> $garage_template->build_move_to($garage_data, $id_list[0], 'title'),
					));
				}
				if ($business_data['product'])
				{
					$template->assign_vars(array(
						'S_TYPE_PRODUCT'		=> true,
						'S_MOVE_PRODUCT_OPTIONS'	=> $garage_template->build_move_to($product_data, $id_list[0], 'title'),
					));
				}

				$template->assign_vars(array(
					'S_DELETE_BUSINESS'		=> true,
					'S_MCP_ACTION'			=> $this->u_action . "&amp;action=disapprove_business&amp;bid={$id_list[0]}",
					'BUSINESS_NAME'			=> $business_data['title'],
				));
			break;

			/**
			* Produce page to allow moderator decide on how linked items are handled
			* Linked are laps. 
			* Laps can be moved to another track or deleted
			*/
			case 'disapprove_track_confirm':

				/**
				* Check we have one and only one track to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_TRACK_SELECTED');
				}
				else if (sizeof($id_list) != 1)
				{
					trigger_error('SELECT_ONE_ONLY');
				}

				/**
				* Get specific and all tracks from DB
				*/
				$track_data = $garage_track->get_track($id_list[0]);
				$tracks_data = $garage_track->get_all_tracks();

				/**
				* Handle template declarations & assignments
				*/				
				$template->assign_vars(array(
					'S_MOVE_OPTIONS'	=> $garage_template->build_move_to($tracks_data, $id_list[0], 'title'),
					'S_DELETE_TRACK'	=> true,
					'S_MCP_ACTION'		=> $this->u_action . "&amp;action=disapprove_track&amp;id={$id_list[0]}",
					'TRACK_NAME'		=> $track_data['title'],
				));
			break;

			/**
			* Produce page to allow moderator decide on how linked items are handled
			* Linked are modifications. 
			* Modifications can be moved to another product in the same category by the same manufacturer or deleted
			*/
			case 'disapprove_product_confirm':

				/**
				* Check we have one and only one product to work on
				*/
				if (!sizeof($id_list))
				{
					trigger_error('NO_PRODUCT_SELECTED');
				}
				else if (sizeof($id_list) != 1)
				{
					trigger_error('SELECT_ONE_ONLY');
				}

				/**
				* Get specific and all products from DB
				*/
				$product_data 	= $garage_modification->get_product($id_list[0]);
				$products_data 	= $garage_modification->get_products_by_manufacturer($product_data['business_id'], $product_data['category_id']);

				/**
				* Handle template declarations & assignments
				*/
				$template->assign_vars(array(
					'S_DELETE_PRODUCT'	=> true,
					'S_MOVE_OPTIONS'	=> $garage_template->build_move_to($products_data, $id_list[0], 'title'),
					'S_MCP_ACTION'		=> $this->u_action . "&amp;action=disapprove_product&amp;product_id={$id_list[0]}",
					'PRODUCT'		=> $product_data['title'],
				));
			break;

		}
	}
}
?>

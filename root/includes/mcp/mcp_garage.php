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
* garage
* Handling the garage moderation queue
* Allows moderators to approve or disapprove items
* For certain items the option to reassign will be provided
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
		global $garage_template;

		/**
		* Setup page variables such as title, template & available language strings
		*/
		$this->page_title = 'MCP_GARAGE';
		$this->tpl_name = 'mcp_garage';
		$user->add_lang(array('mods/garage', 'acp/garage'));

		/**
		* Build All Garage Classes e.g $garage_images->
		*/
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
		* Perform a set action based on value for $action
		* An action is normally a DB action such as insert/update/delete
		* An action does not display a page
		*/
		switch ($action)
		{
			/**
			* Approve all vehicles contained with an array recieved from page
			*/
			case 'approve_vehicle':
				$id_list = request_var('vehicle_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_VEHICLE_SELECTED');
				}

				$garage_vehicle->approve_vehicle($id_list);
			break;
			
			/**
			* Approve all makes contained with an array recieved from page
			*/
			case 'approve_make':
				$id_list = request_var('make_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_MAKE_SELECTED');
				}

				$garage_model->approve_make($id_list);
			break;
			
			/**
			* Approve all models contained with an array recieved from page
			*/
			case 'approve_model':
				$id_list = request_var('model_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_MODEL_SELECTED');
				}

				$garage_model->approve_model($id_list);
			break;

			/**
			* Approve all business's contained with an array recieved from page
			*/
			case 'approve_business':
				$id_list = request_var('business_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_BUSINESS_SELECTED');
				}

				$garage_business->approve_business($id_list);
			break;
			
			/**
			* Approve all quartermile times contained with an array recieved from page
			*/
			case 'approve_quartermile':
				$id_list = request_var('quartermile_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_QUARTERMILE_SELECTED');
				}

				$garage_quartermile->approve_quartermile($id_list);
			break;
			
			/**
			* Approve all dynoruns contained with an array recieved from page
			*/
			case 'approve_dynorun':
				$id_list = request_var('dynorun_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_DYNORUN_SELECTED');
				}

				$garage_dynorun->approve_dynorun($id_list);
			break;
			
			/**
			* Approve all guestbook comments contained with an array recieved from page
			*/
			case 'approve_comment':
				$id_list = request_var('comment_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_COMMENT_SELECTED');
				}

				$garage_guestbook->approve_comment($id_list);
			break;
			
			/**
			* Approve all lap times contained with an array recieved from page
			*/
			case 'approve_lap':
				$id_list = request_var('lap_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_LAP_SELECTED');
				}

				$garage_track->approve_lap($id_list);
			break;
			
			/**
			* Approve all tracks contained with an array recieved from page
			*/
			case 'approve_track':
				$id_list = request_var('track_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_TRACK_SELECTED');
				}

				$garage_track->approve_track($id_list);
			break;

			/**
			* Approve all modification products contained with an array recieved from page
			*/
			case 'approve_product':
				$id_list = request_var('product_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_PRODUCT_SELECTED');
				}

				$garage_modification->approve_product($id_list);
			break;
			
			/**
			* Disappove all vehicles contained with an array recieved from page
			* Dispproving a vehicle deletes it and all its items
			*/
			case 'disapprove_vehicle':
				$id_list = request_var('vehicle_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_VEHICLE_SELECTED');
				}

				$garage_vehicle->disapprove_vehicle($id_list);
			break;
			
			/**
			* Disappove a single make contained with an array recieved from page
			* Moderators will have chosen to delete or move linked items
			*/
			case 'disapprove_make':
				$id_list 	= request_var('make_id_list', array(0));
				$action_make	= request_var('action_make', '');
				$make_to_id	= request_var('make_to_id', 0);

				if (!sizeof($id_list))
				{
					trigger_error('NO_MAKE_SELECTED');
				}

				if (sizeof($id_list) != 1)
				{
					trigger_error('SELECT_ONE_ONLY');
				}

				$garage_model->disapprove_make($id_list[0], $action_make, $make_to_id);
			break;
			
			/**
			* Disappove a single model contained with an array recieved from page
			* Moderators will have chosen to delete or move linked items
			*/
			case 'disapprove_model':
				$id_list 	= request_var('model_id_list', array(0));
				$action_model	= request_var('action_model', '');
				$model_to_id	= request_var('model_to_id', 0);

				if (!sizeof($id_list))
				{
					trigger_error('NO_MODEL_SELECTED');
				}

				if (sizeof($id_list) != 1)
				{
					trigger_error('SELECT_ONE_ONLY');
				}

				$garage_model->disapprove_model($id_list[0], $action_model, $model_to_id);
			break;
			
			/**
			* Disappove a single business contained with an array recieved from page
			* Moderators will have chosen to delete or move linked items
			*/
			case 'disapprove_business':
				$id_list 		= request_var('business_id_list', array(0));
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

				if (!sizeof($id_list))
				{
					trigger_error('NO_BUSINESS_SELECTED');
				}

				if (sizeof($id_list) != 1)
				{
					trigger_error('SELECT_ONE_ONLY');
				}

				$garage_business->disapprove_business($id_list[0], $action_garage, $garage_to_id, $action_insurance, $insurance_to_id, $action_dynocentre, $dynocentre_to_id, $action_retail, $retail_to_id, $action_product, $product_to_id);
			break;
			
			/**
			* Disappove all quartermile times contained with an array recieved from page
			* Dispproving a quartermile time deletes it and all its images
			*/
			case 'disapprove_quartermile':
				$id_list = request_var('quartermile_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_QUARTERMILE_SELECTED');
				}

				$garage_quartermile->disapprove_quartermile($id_list);
			break;
			
			/**
			* Disappove all dynoruns contained with an array recieved from page
			* Dispproving a dynorun deletes it and all its images
			*/
			case 'disapprove_dynorun':
				$id_list = request_var('dynorun_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_DYNORUN_SELECTED');
				}

				$garage_dynorun->disapprove_dynorun($id_list);
			break;
			
			/**
			* Disappove all guestbook comments contained with an array recieved from page
			* Dispproving a comment deletes it
			*/
			case 'disapprove_comment':
				$id_list = request_var('comment_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_COMMENT_SELECTED');
				}

				$garage_guestbook->disapprove_comment($id_list);
			break;
			
			/**
			* Disappove all laps contained with an array recieved from page
			* Dispproving a lap deletes it and all its images
			*/
			case 'disapprove_lap':
				$id_list = request_var('lap_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_LAP_SELECTED');
				}

				$garage_track->disapprove_lap($id_list);
			break;

			/**
			* Disappove a single track contained with an array recieved from page
			* Moderators will have chosen to delete or move linked items
			*/
			case 'disapprove_track':
				$id_list 	= request_var('track_id_list', array(0));
				$action_laps	= request_var('action_laps', '');
				$laps_to_id	= request_var('laps_to_id', 0);

				if (!sizeof($id_list))
				{
					trigger_error('NO_TRACK_SELECTED');
				}

				if (sizeof($id_list) != 1)
				{
					trigger_error('SELECT_ONE_ONLY');
				}

				$garage_track->disapprove_track($id_list[0], $action_laps, $laps_to_id);
			break;

			/**
			* Disappove a single modification product contained with an array recieved from page
			* Moderators will have chosen to delete or move linked items
			*/
			case 'disapprove_product':
				$id_list 		= request_var('product_id_list', array(0));
				$action_modifications	= request_var('action_modifications', '');
				$product_to_id		= request_var('modifications_to_id', 0);


				if (!sizeof($id_list))
				{
					trigger_error('NO_PRODUCT_SELECTED');
				}

				if (sizeof($id_list) != 1)
				{
					trigger_error('SELECT_ONE_ONLY');
				}

				$garage_modification->disapprove_product($id_list[0], $action_modifications, $product_to_id);
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
				$data = $garage_vehicle->get_pending_vehicles();

				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('vehicle_row', array(
						'U_PROFILE'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $data[$i]['user_id']),
						'U_EDIT'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=edit_vehicle&amp;VID=" . $data[$i]['id']. "&amp;redirect=MCP"),
						'USERNAME'	=> $data[$i]['username'],
						'ID'		=> $data[$i]['id'],
						'MAKE'		=> $data[$i]['make'],
						'MODEL'		=> $data[$i]['model'],
						'ENGINE_TYPE'	=> $data[$i]['engine_type'],
						'COLOUR'	=> $data[$i]['colour'],
						'PRICE'		=> $data[$i]['price'],
						'CURRENCY'	=> $data[$i]['currency'],
						'MILEAGE'	=> $data[$i]['mileage'],
						'MILEAGE_UNIT'	=> $data[$i]['mileage_unit'],
						'EDIT'		=> ($garage_config['enable_images']) ? $user->img('garage_edit', 'EDIT') : $user->lang['EDIT'])
					);
				}

				$template->assign_vars(array(
					'S_MCP_ACTION'		=> build_url(),
					'S_UNAPPROVED_VEHICLE' 	=> true,
				));
			break;

			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_makes':
				$data = $garage_model->get_pending_makes();

				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('makes_row', array(
						'ID'	=> $data[$i]['id'],
						'MAKE'	=> $data[$i]['make'])
					);
				}

				$template->assign_vars(array(
					'S_MCP_ACTION'		=> build_url(),
					'S_UNAPPROVED_MAKE' 	=> true,
				));
			break;

			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_models':
				$data = $garage_model->get_pending_models();

				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('models_row', array(
						'ID'	=> $data[$i]['id'],
						'MODEL'	=> $data[$i]['model'],
						'MAKE'	=> $data[$i]['make'])
					);
				}

				$template->assign_vars(array(
					'S_MCP_ACTION'		=> build_url(),
					'S_UNAPPROVED_MODEL' 	=> true,
				));
			break;

			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_business':
				$data = $garage_business->get_pending_business();

				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$type = ($data[$i]['insurance']) ? $user->lang['INSURANCE'] . ', ' : '';
					$type .= ($data[$i]['garage']) ? $user->lang['GARAGE'] . ', ' : '';
					$type .= ($data[$i]['retail']) ? $user->lang['SHOP'] . ', ' : '';
					$type .= ($data[$i]['product']) ? $user->lang['MANUFACTURER'] . ', ' : '';
					$type .= ($data[$i]['dynocentre']) ? $user->lang['DYNOCENTRE'] . ', ' : '';
					$type = rtrim($type, ', ');

					$template->assign_block_vars('business_row', array(
						'U_EDIT'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=edit_business&amp;BUS_ID=" . $data[$i]['id']. "&amp;redirect=MCP"),
						'ID'		=> $data[$i]['id'],
						'TITLE'		=> $data[$i]['title'],
						'ADDRESS'	=> $data[$i]['address'],
						'TELPHONE'	=> $data[$i]['telephone'],
						'FAX'		=> $data[$i]['fax'],
						'WEBSITE'	=> $data[$i]['website'],
						'EMAIL'		=> $data[$i]['email'],
						'OPENING_HOURS'	=> $data[$i]['opening_hours'],
						'TYPE'		=> $type,
						'EDIT'		=> ($garage_config['enable_images']) ? $user->img('garage_edit', 'EDIT') : $user->lang['EDIT'])
					);
				}

				$template->assign_vars(array(
					'S_MCP_ACTION'		=> build_url(),
					'S_UNAPPROVED_BUSINESS' => true,
				));
			break;

			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_quartermiles':
				$data = $garage_quartermile->get_pending_quartermiles();

				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('quartermile_row', array(
						'U_PROFILE'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $data[$i]['user_id']),
						'U_VEHICLE'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;VID=" . $data[$i]['vehicle_id']),
						'U_EDIT'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=edit_quartermile&amp;QMID=" . $data[$i]['qmid']. "&amp;VID=".$data[$i]['vehicle_id']."&amp;redirect=MCP"),
						'ID'		=> $data[$i]['qmid'],
						'USERNAME'	=> $data[$i]['username'],
						'VEHICLE'	=> $data[$i]['vehicle'],
						'RT'		=> $data[$i]['rt'],
						'SIXTY'		=> $data[$i]['sixty'],
						'THREE'		=> $data[$i]['three'],
						'EIGHTH'	=> $data[$i]['eighth'],
						'EIGHTHMPH'	=> $data[$i]['eighthmph'],
						'THOU'		=> $data[$i]['thou'],
						'QUART'		=> $data[$i]['quart'],
						'QUARTMPH'	=> $data[$i]['quartmph'],
						'EDIT'		=> ($garage_config['enable_images']) ? $user->img('garage_edit', 'EDIT') : $user->lang['EDIT'])
					);
				}

				$template->assign_vars(array(
					'S_MCP_ACTION'			=> build_url(),
					'S_UNAPPROVED_QUARTERMILE' 	=> true,
				));
			break;

			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_dynoruns':
				$data = $garage_dynorun->get_pending_dynoruns();

				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('dynorun_row', array(
						'U_PROFILE'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $data[$i]['user_id']),
						'U_VEHICLE'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;VID=" . $data[$i]['id']),
						'U_EDIT'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=edit_dynorun&amp;DID=" . $data[$i]['did']. "&amp;VID=" . $data[$i]['id']. "&amp;redirect=MCP"),
						'ID'		=> $data[$i]['did'],
						'USERNAME'	=> $data[$i]['username'],
						'VEHICLE'	=> $data[$i]['vehicle'],
						'DYNOCENTER'	=> $data[$i]['title'],
						'BHP'		=> $data[$i]['bhp'],
						'BHP_UNIT'	=> $data[$i]['bhp_unit'],
						'TORQUE'	=> $data[$i]['torque'],
						'TORQUE_UNIT'	=> $data[$i]['torque_unit'],
						'BOOST'		=> $data[$i]['boost'],
						'BOOST_UNIT'	=> $data[$i]['boost_unit'],
						'NITROUS'	=> $data[$i]['nitrous'],
						'PEAKPOINT'	=> $data[$i]['peakpoint'],
						'EDIT'		=> ($garage_config['enable_images']) ? $user->img('garage_edit', 'EDIT') : $user->lang['EDIT'])
					);
				}

				$template->assign_vars(array(
					'S_MCP_ACTION'		=> build_url(),
					'S_UNAPPROVED_DYNORUN' 	=> true,
				));
			break;

			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_laps':
				$data = $garage_track->get_pending_laps();

				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('lap_row', array(
						'ID'		=> $data[$i]['lid'],
						'TRACK'		=> $data[$i]['title'],
						'CONDITION'	=> $garage_track->get_track_condition($data[$i]['condition_id']),
						'TYPE'		=> $garage_track->get_lap_type($data[$i]['type_id']),
						'EDIT'		=> ($garage_config['enable_images']) ? $user->img('garage_edit', 'EDIT') : $user->lang['EDIT'],
						'MINUTE'	=> $data[$i]['minute'],
						'SECOND'	=> $data[$i]['second'],
						'MILLISECOND'	=> $data[$i]['millisecond'],
						'USERNAME'	=> $data[$i]['username'],
						'VEHICLE'	=> $data[$i]['vehicle'],
						'IMAGE'		=> $user->img('garage_img_attached', 'IMAGE_ATTACHED'),
						'U_PROFILE'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $data[$i]['user_id']),
						'U_VEHICLE'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;VID=" . $data[$i]['id']),
						'U_IMAGE'	=> ($data[$i]['attach_id']) ? append_sid("garage.$phpEx", "mode=view_image&amp;image_id=". $data[$i]['attach_id']) : '',
						'U_TRACK'	=> append_sid("garage_track.$phpEx?mode=view_track&amp;TID=".$data[$i]['track_id']."&amp;VID=". $data[$i]['vehicle_id']),
						'U_LAP'		=> append_sid("garage_track.$phpEx?mode=view_lap&amp;LID=".$data[$i]['lid']."&amp;VID=". $data[$i]['vehicle_id']),
						'U_EDIT'	=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=edit_lap&amp;LID=" . $data[$i]['lid'].  "&amp;redirect=MCP"),
					));
				}

				$template->assign_vars(array(
					'S_MCP_ACTION'		=> build_url(),
					'S_UNAPPROVED_LAP' 	=> true,
				));
			break;

			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_tracks':
				$data = $garage_track->get_pending_tracks();

				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('track_row', array(
						'U_EDIT'	=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=edit_track&amp;TID=" . $data[$i]['id'].  "&amp;redirect=MCP"),
						'ID'		=> $data[$i]['id'],
						'TITLE'		=> $data[$i]['title'],
						'EDIT'		=> ($garage_config['enable_images']) ? $user->img('garage_edit', 'EDIT') : $user->lang['EDIT'],
					));
				}

				$template->assign_vars(array(
					'S_MCP_ACTION'		=> build_url(),
					'S_UNAPPROVED_TRACK' 	=> true,
				));
			break;

			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_guestbook_comments':
				$data = $garage_guestbook->get_pending_comments();

				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$message = $data[$i]['post_text'];
					$message = str_replace("\n", '<br />', $message);
					if ($post_info['bbcode_bitfield'])
					{
						include_once($phpbb_root_path . 'includes/bbcode.' . $phpEx);
						$bbcode = new bbcode($data[$i]['bbcode_bitfield']);
						$bbcode->bbcode_second_pass($message, $data[$i]['bbcode_uid'], $data[$i]['bbcode_bitfield']);
					}
					$message = smiley_text($message);

					$template->assign_block_vars('comment_row', array(
						'U_PROFILE'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $data[$i]['user_id']),
						'U_VEHICLE'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;VID=" . $data[$i]['vehicle_id']),
						'U_EDIT'	=> append_sid("{$phpbb_root_path}garage_guestbook.$phpEx", "mode=edit_comment&amp;TID=" . $data[$i]['id'].  "&amp;redirect=MCP"),
						'USERNAME'	=> $data[$i]['username'],
						'VEHICLE'	=> $data[$i]['vehicle'],
						'POSTTIME'	=> $user->format_date($data[$i]['post_time']),
						'ID'		=> $data[$i]['id'],
						'EDIT'		=> ($garage_config['enable_images']) ? $user->img('garage_edit', 'EDIT') : $user->lang['EDIT'],
					));
				}

				$template->assign_vars(array(
					'S_MCP_ACTION'		=> build_url(),
					'S_UNAPPROVED_COMMENT' 	=> true,
				));
			break;

			/**
			* Produce page to show moderator all unapproved vehicles
			*/
			case 'unapproved_products':
				$data = $garage_modification->get_pending_products();

				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('products_row', array(
						'PRODUCT'	=> $data[$i]['product'],
						'MANUFACTURER'	=> $data[$i]['manufacturer'],
						'CATEGORY'	=> $data[$i]['category'],
						'ID'		=> $data[$i]['id'],
						'EDIT'		=> ($garage_config['enable_images']) ? $user->img('garage_edit', 'EDIT') : $user->lang['EDIT'],
					));
				}

				$template->assign_vars(array(
					'S_MCP_ACTION'		=> build_url(),
					'S_UNAPPROVED_PRODUCT' 	=> true,
				));
			break;

			/**
			* Produce page to allow moderator decide on how linked items are handled
			* Linked items are models & vehicles. 
			* Models & vehicles can be moved to another make or deleted
			*/
			case 'disapprove_make_confirm':

				$id_list = request_var('make_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_MAKE_SELECTED');
				}

				if (sizeof($id_list) != 1)
				{
					trigger_error('SELECT_ONE_ONLY');
				}

				$make_data = $garage_model->get_make($id_list[0]);
				$makes_data = $garage_model->get_all_makes();
				$select_to = $garage_template->build_move_to($makes_data, $id_list[0], 'make');

				$template->assign_vars(array(
					'U_ACTION'		=> $this->u_action . "&amp;action=make_delete&amp;make_id=" . $id_list[0],
					'S_DELETE_MAKE'		=> true,
					'S_MOVE'		=> (!empty($select_to)) ? true : false ,
					'S_MOVE_OPTIONS'	=> $select_to,
					'MAKE'			=> $make_data['make'],
				));
			break;

			/**
			* Produce page to allow moderator decide on how linked items are handled
			* Linked are vehicles. 
			* Vehicles can be moved to another model of the same make or deleted
			*/
			case 'disapprove_model_confirm':
				$id_list = request_var('model_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_MODEL_SELECTED');
				}

				if (sizeof($id_list) != 1)
				{
					trigger_error('SELECT_ONE_ONLY');
				}

				$model_data = $garage_model->get_model($id_list[0]);
				$models_data = $garage_model->get_all_models_from_make($model_data['make_id']);
				$select_to = $garage_template->build_move_to($models_data, $id_list[0], 'model');

				$template->assign_vars(array(
					'U_ACTION'		=> $this->u_action . "&amp;action=model_delete&amp;model_id=".$id_list[0]."&amp;make_id=" . $model_data['make_id'],
					'S_DELETE_MODEL'	=> true,
					'S_MOVE'		=> (!empty($select_to)) ? true : false ,
					'S_MOVE_OPTIONS'	=> $select_to,
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
				$id_list = request_var('business_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_MODEL_SELECTED');
				}

				if (sizeof($id_list) != 1)
				{
					trigger_error('SELECT_ONE_ONLY');
				}

				$business_id = $id_list[0];
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
					'BUSINESS_NAME'			=> $business_data['title'],
				));
			break;

			/**
			* Produce page to allow moderator decide on how linked items are handled
			* Linked are laps. 
			* Laps can be moved to another track or deleted
			*/
			case 'disapprove_track_confirm':
				$id_list = request_var('track_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_TRACK_SELECTED');
				}

				if (sizeof($id_list) != 1)
				{
					trigger_error('SELECT_ONE_ONLY');
				}

				$track_id = $id_list[0];
				$track_data = $garage_track->get_track($track_id);
				$tracks_data = $garage_track->get_all_tracks();

				$select_to = $garage_template->build_move_to($tracks_data, $track_id, 'title');
				$template->assign_vars(array(
					'S_MOVE'		=> (!empty($select_to)) ? true : false,
					'S_MOVE_OPTIONS'	=> $select_to,
					'S_DELETE_TRACK'	=> true,
					'U_ACTION'		=> $this->u_action . "&amp;action=delete&amp;id=$track_id",
					'TRACK_NAME'		=> $track_data['title'],
				));
			break;

			/**
			* Produce page to allow moderator decide on how linked items are handled
			* Linked are modifications. 
			* Modifications can be moved to another product in the same category by the same manufacturer or deleted
			*/
			case 'disapprove_product_confirm':
				$id_list = request_var('product_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_PRODUCT_SELECTED');
				}

				if (sizeof($id_list) != 1)
				{
					trigger_error('SELECT_ONE_ONLY');
				}

				$product_id 	= $id_list[0];
				$product_data 	= $garage_modification->get_product($product_id);
				$products_data 	= $garage_modification->get_products_by_manufacturer($product_data['business_id'], $product_data['category_id']);
				$select_to 	= $garage_template->build_move_to($products_data, $product_id, 'title');

				$template->assign_vars(array(
					'S_DELETE_PRODUCT'	=> true,
					'S_MOVE'		=> (!empty($select_to)) ? true : false ,
					'S_MOVE_OPTIONS'	=> $select_to,
					'U_ACTION'		=> $this->u_action . "&amp;action=product_delete&amp;product_id=$product_id",
					'PRODUCT'		=> $product_data['title'],
				));
			break;

		}
	}
}
?>

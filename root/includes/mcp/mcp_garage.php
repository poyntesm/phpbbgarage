<?php
/** 
*
* @package mcp
* @version $Id: mcp_queue.php,v 1.51 2006/08/12 13:12:18 acydburn Exp $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* mcp_queue
* Handling the moderation queue
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
		global $auth, $db, $user, $template;
		global $config, $phpbb_root_path, $phpEx, $action;
		global $garage_config, $garage_template, $garage_vehicle, $garage_business, $garage_guestbook, $garage_track;

		$start = request_var('start', 0);

		$this->page_title = 'MCP_GARAGE';

		$user->add_lang('mods/garage');

		include_once($phpbb_root_path . 'includes/mods/class_garage_model.' . $phpEx);
		include_once($phpbb_root_path . 'includes/mods/class_garage_business.' . $phpEx);
		include_once($phpbb_root_path . 'includes/mods/class_garage_quartermile.' . $phpEx);
		include_once($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);
		include_once($phpbb_root_path . 'includes/mods/class_garage_dynorun.' . $phpEx);
		include_once($phpbb_root_path . 'includes/mods/class_garage_guestbook.' . $phpEx);
		include_once($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);
		include_once($phpbb_root_path . 'includes/mods/class_garage_track.' . $phpEx);
		include_once($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);

		switch ($action)
		{
			case 'approve_vehicle':
				$id_list = request_var('vehicle_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_VEHICLE_SELECTED');
				}

				$garage_vehicle->approve_vehicle($id_list, $mode);
			break;
			case 'approve_make':
				$id_list = request_var('make_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_MAKE_SELECTED');
				}

				$garage_model->approve_make($id_list, $mode);
			break;
			case 'approve_model':
				$id_list = request_var('model_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_MODEL_SELECTED');
				}

				$garage_model->approve_model($id_list, $mode);
			break;
			case 'approve_business':
				$id_list = request_var('business_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_BUSINESS_SELECTED');
				}

				$garage_business->approve_business($id_list, $mode);
			break;
			case 'approve_quartermile':
				$id_list = request_var('quartermile_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_QUARTERMILE_SELECTED');
				}

				$garage_quartermile->approve_quartermile($id_list, $mode);
			break;
			case 'approve_dynorun':
				$id_list = request_var('dynorun_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_DYNORUN_SELECTED');
				}

				$garage_dynorun->approve_dynorun($id_list, $mode);
			break;
			case 'approve_guestbook_comment':
				$id_list = request_var('comment_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_COMMENT_SELECTED');
				}

				$garage_guestbook->approve_comment($id_list, $mode);
			break;
			case 'approve_lap':
				$id_list = request_var('lap_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_LAP_SELECTED');
				}

				$garage_track->approve_lap($id_list, $mode);
				break;
			case 'approve_track':
				$id_list = request_var('track_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_TRACK_SELECTED');
				}

				$garage_track->approve_track($id_list, $mode);
			break;
			case 'disapprove_vehicle':
				$id_list = request_var('vehicle_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_VEHICLE_SELECTED');
				}

				$garage_vehicle->disapprove_vehicle($id_list, $mode);
			break;
			case 'disapprove_make':
				$id_list = request_var('make_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_MAKE_SELECTED');
				}

				$garage_model->disapprove_make($id_list, $mode);
			break;
			case 'disapprove_model':
				$id_list = request_var('model_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_MODEL_SELECTED');
				}

				$garage_model->disapprove_model($id_list, $mode);
			break;
			case 'disapprove_business':
				$id_list = request_var('business_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_BUSINESS_SELECTED');
				}

				$garage_business->disapprove_business($id_list, $mode);
			break;
			case 'disapprove_quartermile':
				$id_list = request_var('quartermile_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_QUARTERMILE_SELECTED');
				}

				$garage_quartermile->disapprove_quartermile($id_list, $mode);
			break;
			case 'disapprove_dynorun':
				$id_list = request_var('dynorun_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_DYNORUN_SELECTED');
				}

				$garage_dynorun->disapprove_dynorun($id_list, $mode);
			break;
			case 'disapprove_guestbook':
				$id_list = request_var('comment_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_COMMENT_SELECTED');
				}

				$garage_guestbook->disapprove_comment($id_list, $mode);
			break;
			case 'disapprove_lap':
				$id_list = request_var('lap_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_LAP_SELECTED');
				}

				$garage_track->disapprove_lap($id_list, $mode);
			break;
			case 'disapprove_track':
				$id_list = request_var('track_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_TRACK_SELECTED');
				}

				$garage_track->disapprove_track($id_list, $mode);
			break;
			case 'reassign_business':
				$id_list = request_var('business_id_list', array(0));

				if (!sizeof($id_list))
				{
					trigger_error('NO_BUSINESS_SELECTED');
				}

				$garage_business->reassign_business($id_list, $mode);
			break;
		}

		switch ($mode)
		{
			case 'unapproved_vehicles':
				$data = $garage_vehicle->get_pending_vehicles();

				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('vehicle_row', array(
						'U_PROFILE'	=> append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=" . $data[$i]['user_id']),
						'U_EDIT'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=edit_vehicle&amp;CID=" . $data[$i]['id']. "&amp;redirect=MCP"),
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
					'S_MCP_ACTION'	=> build_url(array('t', 'f', 'sd', 'st', 'sk')))
				);

				$this->tpl_name = 'mcp_garage_approve_vehicles';
			break;

			case 'unapproved_makes':

				$data = $garage_model->get_pending_makes();

				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('vehicle_row', array(
						'ID'	=> $data[$i]['id'],
						'MAKE'	=> $data[$i]['make'])
					);
				}

				$template->assign_vars(array(
					'S_MCP_ACTION'	=> build_url(array('t', 'f', 'sd', 'st', 'sk')))
				);

				$this->tpl_name = 'mcp_garage_approve_makes';
			break;

			case 'unapproved_models':

				$data = $garage_model->get_pending_makes();

				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('model_row', array(
						'ID'	=> $data[$i]['id'],
						'MODEL'	=> $data[$i]['model'],
						'MAKE'	=> $data[$i]['make'])
					);
				}

				$template->assign_vars(array(
					'S_MCP_ACTION'	=> build_url(array('t', 'f', 'sd', 'st', 'sk')))
				);

				$this->tpl_name = 'mcp_garage_approve_models';
			break;

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
					'S_MCP_ACTION'	=> build_url(array('t', 'f', 'sd', 'st', 'sk')))
				);

				$this->tpl_name = 'mcp_garage_approve_business';
			break;

			case 'unapproved_quartermiles':

				$data = $garage_quartermile->get_pending_quartermiles();

				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('quartermile_row', array(
						'U_PROFILE'	=> append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=" . $data[$i]['user_id']),
						'U_VEHICLE'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;CID=" . $data[$i]['garage_id']),
						'U_EDIT'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=edit_quartermile&amp;QMID=" . $data[$i]['qmid']. "&amp;CID=".$data[$i]['garage_id']."&amp;redirect=MCP"),
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
					'S_MCP_ACTION'	=> build_url(array('t', 'f', 'sd', 'st', 'sk')))
				);

				$this->tpl_name = 'mcp_garage_approve_quartermiles';
			break;

			case 'unapproved_dynoruns':

				$data = $garage_dynorun->get_pending_dynoruns();

				for ($i = 0, $count = sizeof($data);$i < $count; $i++)
				{
					$template->assign_block_vars('dynorun_row', array(
						'U_PROFILE'	=> append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=" . $data[$i]['user_id']),
						'U_VEHICLE'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;CID=" . $data[$i]['id']),
						'U_EDIT'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=edit_dynorun&amp;RRID=" . $data[$i]['rr_id']. "&amp;CID=" . $data[$i]['id']. "&amp;redirect=MCP"),
						'ID'		=> $data[$i]['rr_id'],
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
					'S_MCP_ACTION'	=> build_url(array('t', 'f', 'sd', 'st', 'sk')))
				);

				$this->tpl_name = 'mcp_garage_approve_dynoruns';
				break;

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
						'IMAGE'		=> $user->img('garage_slip_img_attached', 'SLIP_IMAGE_ATTACHED'),
						'U_PROFILE'	=> append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=" . $data[$i]['user_id']),
						'U_VEHICLE'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;CID=" . $data[$i]['id']),
						'U_IMAGE'	=> ($data[$i]['attach_id']) ? append_sid("garage.$phpEx", "mode=view_image&amp;image_id=". $data[$i]['attach_id']) : '',
						'U_TRACK'	=> append_sid("garage_track.$phpEx?mode=view_track&amp;TID=".$data[$i]['track_id']."&amp;CID=". $data[$i]['garage_id']),
						'U_LAP'		=> append_sid("garage_track.$phpEx?mode=view_lap&amp;LID=".$data[$i]['lid']."&amp;CID=". $data[$i]['garage_id']),
						'U_EDIT'	=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=edit_lap&amp;LID=" . $data[$i]['lid'].  "&amp;redirect=MCP"),
					));
				}

				$this->tpl_name = 'mcp_garage_approve_laps';
				break;

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

				$this->tpl_name = 'mcp_garage_approve_tracks';
				break;

			case 'reassign_business':

				break;

		}
	}
}

?>

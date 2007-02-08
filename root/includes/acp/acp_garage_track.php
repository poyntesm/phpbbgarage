<?php
/***************************************************************************
 *                              acp_garage_track.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: acp_garage_track.php 366 2007-02-07 13:08:05Z poyntesm $
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
class acp_garage_track
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache, $garage, $garage_config;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx, $tid;

		//Build All Garage Classes e.g $garage_images->
		require($phpbb_root_path . 'includes/mods/class_garage_track.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);

		$user->add_lang('acp/garage');
		$user->add_lang('mods/garage');
		$this->tpl_name = 'acp_garage_track';
		$this->page_title = 'ACP_MANAGE_FORUMS';

		$action		= request_var('action', '');
		$update		= (isset($_POST['update'])) ? true : false;
		$track_id	= request_var('id', 0);

		$errors = array();

		// Major routines
		if ($update)
		{
			switch ($action)
			{
				case 'add':

					$params = array('title' => '', 'length' => '', 'mileage_unit' => '');
					$data = $garage->process_vars($params);

					$garage_track->insert_track($data);
					add_log('admin', 'LOG_GARAGE_TRACK_CREATED', $data['title']);

					trigger_error($user->lang['TRACK_CREATED'] . adm_back_link($this->u_action));

				break;

				case 'edit':

					$params = array('title' => '', 'length' => '', 'mileage_unit' => '');
					$data = $garage->process_vars($params);
					$tid = $track_id;

					if(!$data['title'])
					{
						$errors[] = $user->lang['TRACK_NAME_EMPTY'];
						break;
					}

					$garage_track->update_track($data);
					add_log('admin', 'LOG_GARAGE_TRACK_UPDATED', $data['title']);

					trigger_error($user->lang['TRACK_UPDATED'] . adm_back_link($this->u_action));

				break;

				case 'delete':
					$action_laps		= request_var('action_laps', '');
					$laps_to_id	= request_var('laps_to_id', 0);

					$errors = $this->delete_track($track_id, $action_laps, $laps_to_id);

					if (sizeof($errors))
					{
						break;
					}

					trigger_error($user->lang['TRACK_DELETED'] . adm_back_link($this->u_action));
				break;
			}
		}
		
		switch ($action)
		{

			case 'approve':

				$data = $garage_track->get_track($track_id);
				$garage->update_single_field(GARAGE_TRACK_TABLE, 'pending', 0, 'id', $track_id);
				add_log('admin', 'LOG_GARAGE_TRACK_APPROVED', $data['title']);

			break;

			case 'disapprove':

				$data = $garage_track->get_track($track_id);
				$garage->update_single_field(GARAGE_TRACK_TABLE, 'pending', 1, 'id', $track_id);
				add_log('admin', 'LOG_GARAGE_TRACK_DISAPPROVED', $data['title']);

			break;

			case 'add':
			case 'edit':

				// Show form to create/modify a track
				if ($action == 'edit')
				{
					$this->page_title = 'EDIT_TRACK';
					$row = $garage_track->get_track($track_id);

					if (!$update)
					{
						$track_data = $row;
					}
				}
				else
				{
					$this->page_title = 'CREATE_TRACK';

					// Fill track data with default values
					if (!$update)
					{
						$track_data = array(
							'title'		=> request_var('title', '', true),
							'length'	=> '',
							'mileage_unit'	=> '',
						);
					}
				}

				$garage_template->mileage_dropdown($track_data['mileage_unit']);
				$template->assign_vars(array(
					'S_EDIT_TRACK'		=> true,
					'S_ERROR'		=> (sizeof($errors)) ? true : false,
					'U_BACK'		=> $this->u_action,
					'U_EDIT_ACTION'		=> $this->u_action . "&amp;action=$action&amp;id=$track_id",
					'TRACK_NAME'		=> $track_data['title'],
					'LENGTH'		=> $track_data['length'],
					'MILEAGE_UNIT'		=> $track_data['mileage_unit'],
					'ERROR_MSG'		=> (sizeof($errors)) ? implode('<br />', $errors) : '',
					)
				);

				return;

			break;

			case 'delete':

				if (!$track_id)
				{
					trigger_error($user->lang['NO_TRACK'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$track_data = $garage_track->get_track($track_id);
				$tracks_data = $garage_track->get_all_tracks();

				$select_to = $this->build_move_to($tracks_data, $track_id);
				$template->assign_vars(array(
					'S_MOVE'		=> (!empty($select_to)) ? true : false,
					'S_MOVE_OPTIONS'	=> $select_to,
				));

				$template->assign_vars(array(
					'S_DELETE_TRACK'		=> true,
					'U_ACTION'			=> $this->u_action . "&amp;action=delete&amp;id=$track_id",
					'U_BACK'			=> $this->u_action,
					'TRACK_NAME'			=> $track_data['title'],
					'S_ERROR'			=> (sizeof($errors)) ? true : false,
					'ERROR_MSG'			=> (sizeof($errors)) ? implode('<br />', $errors) : '')
				);
		
			break;	
		
		}
		
		//Default Management Page	
		$data = $garage_track->get_all_tracks();
		
		for($i = 0; $i < count($data); $i++)
		{
			$url = $this->u_action . "&amp;id={$data[$i]['id']}";
		
			$template->assign_block_vars('track', array(
				'ID' 			=> $data[$i]['id'],
				'TITLE' 		=> $data[$i]['title'],
				'S_DISAPPROVED'		=> ($data[$i]['pending'] == 1) ? true : false,
				'S_APPROVED'		=> ($data[$i]['pending'] == 0) ? true : false,
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
	* Remove complete track
	*/
	function delete_track($track_id, $action_laps = 'delete', $laps_to_id = 0)
	{

		global $db, $user, $cache, $garage, $garage_track;

		$track_data = $garage_track->get_track($track_id);

		$errors = array();

		//Handle Items Linked To Garage Business
		if ($action_garage == 'delete')
		{
			$this->delete_garage_track_content($track_id);
			add_log('admin', 'LOG_GARAGE_DELETE_GARAGE', $track_data['title']);
		}
		else if ($action_garage == 'move')
		{
			if (!$laps_to_id)
			{
				$errors[] = $user->lang['NO_DESTINATION_GARAGE_TRACK'];
			}
			else
			{
				$row = $garage_track->get_track($laps_to_id);

				if (!$row)
				{
					$errors[] = $user->lang['NO_TRACK'];
				}
				else
				{
					$to_name = $row['title'];
					$from_name = $track_data['title'];
					$this->move_category_content($track_id, $laps_to_id);
					add_log('admin', 'LOG_GARAGE_MOVED_GARAGE', $from_name, $to_name);
				}
			}
		}

		$garage->delete_rows(GARAGE_TRACK_TABLE, 'id', $track_id);
		add_log('admin', 'LOG_GARAGE_TRACK_DELETED', $track_data['title']);

		if (sizeof($errors))
		{
			return $errors;
		}
	}

	function delete_garage_track_content($track_id)
	{
		global $db, $config, $phpbb_root_path, $phpEx, $garage;

		$laps = $garage_modification->get_laps_by_track($track_id);
		for ($i = 0, $count = sizeof($laps);$i < $count; $i++)
		{
			$garage_modification->delete_lap($laps[$i]['id']);
		}

		return;
	}

	function move_track_content($from_id, $to_id)
	{
		global $garage;

		$garage->update_single_field(GARAGE_LAPS_TABLE, 'track_id', $to_id, 'track_id', $from_id);

		return;
	}

}
?>

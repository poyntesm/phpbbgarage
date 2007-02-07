<?php
/***************************************************************************
 *                              acp_garage_quota.php
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

class acp_garage_quota
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache, $garage, $garage_config;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx;

		$user->add_lang('acp/garage');
		$this->tpl_name = 'acp_garage_quota';
		$this->page_title = 'ACP_MANAGE_FORUMS';

		//Build All Garage Classes e.g $garage_images->
		require($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_image.' . $phpEx);

		$action	= request_var('action', '');
		$update	= (isset($_POST['update'])) ? true : false;

		$errors = array();

		// Major routines
		if ($update)
		{
			switch ($action)
			{

				case 'update'	:

					$default_upload_quota = $default_remote_quota = $default_vehicle_quota = $upload_groups_value = $upload_groups_quotas_value = $remote_groups_value = $remote_groups_quotas_value = $add_groups_value = $add_groups_quotas_value = '' ;

					//Handle Default User Quotas
					$default_upload_quota = request_var('default_upload_quota', '');
					$default_remote_quota = request_var('default_remote_quota', '');
					$default_vehicle_quota = request_var('default_vehicle_quota', '');

					if (empty($default_upload_quota) || empty($default_remote_quota) || empty($default_vehicle_quota))
					{
						$errors[] = $user->lang['EMPTY_DEFAULT_QUOTA'];
					}

					//Let See If Any Group Vehicle Quotas Are Set
					$add_groups = request_var('add_groups', array(0));
					if ($add_groups)
					{
						//Now Lets Get Quota For Groups That Have Been Granted Permission
						$empty_group_vehicle_quota = false;
						for ($i = 0, $count = sizeof($add_groups);$i < $count; $i++)
						{
							$quota = request_var('add_quota_'.$add_groups[$i], '');
							$add_quota[] = $quota;
							if (empty($quota) && $empty_group_vehicle_quota == false )
							{
								$errors[] = $user->lang['EMPTY_GROUP_VEHICLE_QUOTA'];
								$empty_group_vehicle_quota = true;
								continue;
							}
							else if (empty($quota) && $empty_group_vehicle_quota == true )
							{
								continue;
							}
						}
						$add_groups_value = implode(",", $add_groups);
						$add_groups_quotas_value = implode(",", $add_quota);
					}
			
					//Let See If Any Private 'UPLOAD' Permisssions & Quotas Are Set
					$upload_groups = $remote_groups = request_var('upload_groups', array(0));
					if ($upload_groups) 
					{
						//Now Lets Get Image Quota For Groups That Have Been Granted Permission
						for ($i = 0, $count = sizeof($upload_groups);$i < $count; $i++)
						{
							$group_id = intval($upload_groups[$i]);

							$upload_quota[] = request_var('upload_quota_'.$group_id, '');
							$remote_quota[] = request_var('remote_quota_'.$group_id, '');
							if (empty($quota) && $empty_group_vehicle_quota == false )
							{
								$errors[] = $user->lang['EMPTY_GROUP_VEHICLE_QUOTA'];
								$empty_group_vehicle_quota = true;
								continue;
							}
							else if (empty($quota) && $empty_group_vehicle_quota == true )
							{
								continue;
							}
						}
						//Set Image Quotas
						$upload_groups_value = implode(",", $upload_groups);
						$upload_groups_quotas_value = implode(",", $upload_quota);
						$remote_groups_value = implode(",", $remote_groups);
						$remote_groups_quotas_value = implode(",", $remote_quota);
					}

					if (sizeof($errors))
					{
						break;
					}

					//Perform All DB Updates Now
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', $default_vehicle_quota, 'config_name', 'default_vehicle_quota');
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', $default_upload_quota, 'config_name', 'default_upload_quota');
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', $default_remote_quota, 'config_name', 'default_remote_quota');
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', $add_groups_value, 'config_name', 'add_groups');
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', $add_groups_quotas_value, 'config_name', 'add_groups_quotas');
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', $upload_groups_value, 'config_name', 'upload_groups');
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', $remote_groups_value, 'config_name', 'remote_groups');
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', $upload_groups_quotas_value, 'config_name', 'upload_groups_quotas');
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', $remote_groups_quotas_value, 'config_name', 'remote_groups_quotas');
			
					trigger_error($user->lang['QUOTAS_UPDATED'] . adm_back_link($this->u_action));
		
				break;
			}
		}
		
		//Default management page
		
		// Get the list of phpBB usergroups
		$groupdata = $garage->get_groups_allowed_quotas();

		//Get Add Permission Info For Usergruops...Bit Messy But Works!!
		$sql = "SELECT config_value as add_groups
			FROM ". GARAGE_CONFIG_TABLE ."
			WHERE config_name = 'add_groups'";
		$result = $db->sql_query($sql);
		$add_perms = $db->sql_fetchrow($result);
		$add_groups = @explode(',', $add_perms['add_groups']);

		//Get Upload Permission Info For Usergruops...Bit Messy But Works!!
		$sql = "SELECT config_value as upload_groups
			FROM ". GARAGE_CONFIG_TABLE ."
			WHERE config_name = 'upload_groups'";
		$result = $db->sql_query($sql);
		$upload_perms = $db->sql_fetchrow($result);
		$upload_groups = @explode(',', $upload_perms['upload_groups']);

		//Now Process Each & Every Group We Have
		for ($i = 0; $i < count($groupdata); $i++)
		{
			$groupname = (!empty($user->lang['G_' . $groupdata[$i]['group_name']]))? $user->lang['G_' . $groupdata[$i]['group_name']] : $groupdata[$i]['group_name'];
			$template->assign_block_vars('usergroup', array(
				'GROUP_ID' 		=> $groupdata[$i]['group_id'],
				'S_ADD_CHECKED' 	=> (in_array($groupdata[$i]['group_id'], $add_groups)) ? true : false,
				'S_UPLOAD_CHECKED' 	=> (in_array($groupdata[$i]['group_id'], $upload_groups)) ? true : false,
				'GROUP_NAME' 		=> $groupname,
				'VEHICLE_QUOTA' 	=> $garage_vehicle->get_group_vehicle_quota($groupdata[$i]['group_id']),
				'UPLOAD_QUOTA' 		=> $garage_image->get_group_upload_image_quota($groupdata[$i]['group_id']),
				'REMOTE_QUOTA' 		=> $garage_image->get_group_remote_image_quota($groupdata[$i]['group_id']),
			));
		}

		$template->assign_vars(array(
			'U_ACTION'		=> $this->u_action ."&amp;action=update",
			'U_BACK'		=> $this->u_action,
			'DEFAULT_VEHICLE_QUOTA' => $garage_config['max_user_cars'],
			'DEFAULT_UPLOAD_QUOTA' 	=> $garage_config['max_upload_images'],
			'DEFAULT_REMOTE_QUOTA' 	=> $garage_config['max_remote_images'],
			'S_ERROR'		=> (sizeof($errors)) ? true : false,
			'ERROR_MSG'		=> (sizeof($errors)) ? implode('<br />', $errors) : '',
		));
		
	}	
}


?>

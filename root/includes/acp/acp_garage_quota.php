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
class acp_garage_quota
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache, $garage, $garage_config;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx;

		$user->add_lang('acp/garage');
		$this->tpl_name = 'acp_garage_quota';
		$this->page_title = 'ACP_MANAGE_QUOTAS';

		//Build All Garage Classes e.g $garage_images->
		require($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_image.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_admin.' . $phpEx);

		$action	= request_var('action', '');
		$update	= (isset($_POST['update'])) ? true : false;

		$errors = $empty_add_groups = $empty_upload_groups = array();

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
					else
					{
						$garage_admin->set_config('default_vehicle_quota', $default_vehicle_quota, $garage_config);
						$garage_admin->set_config('default_upload_quota', $default_upload_quota, $garage_config);
						$garage_admin->set_config('default_remote_quota', $default_remote_quota, $garage_config);
					}

					//Let See If Any Group Vehicle Quotas Are Set
					$add_groups = request_var('add_groups', array(0));
					if ($add_groups)
					{
						//Now Lets Get Quota For Groups That Have Been Granted Permission
						$empty_add_vehicle_quota = false;
						for ($i = 0, $count = sizeof($add_groups);$i < $count; $i++)
						{
							$group_id = intval($add_groups[$i]);
							$quota = request_var('add_quota_'.$add_groups[$i], '');
							$add_quota[] = $quota;
							if (empty($quota) && $empty_add_vehicle_quota == false )
							{
								$errors[] = $user->lang['EMPTY_GROUP_VEHICLE_QUOTA'];
								$empty_add_vehicle_quota = true;
								array_push($empty_add_groups, $group_id);
								continue;
							}
							else if (empty($quota) && $empty_add_vehicle_quota == true )
							{
								continue;
							}
						}
						$add_groups_value = implode(",", $add_groups);
						$add_groups_quotas_value = implode(",", $add_quota);
					}
					if (empty($errors))
					{
						$garage_admin->set_config('add_groups', $add_groups_value, $garage_config);
						$garage_admin->set_config('add_groups_quotas', $add_groups_quotas_value, $garage_config);
						$garage_config = $garage_admin->sync_config();
					}

					//Let See If Any Private 'UPLOAD' Permisssions & Quotas Are Set
					$upload_groups = $remote_groups = request_var('upload_groups', array(0));
					if ($upload_groups) 
					{
						//Now Lets Get Image Quota For Groups That Have Been Granted Permission
						$empty_upload_vehicle_quota = false;
						for ($i = 0, $count = sizeof($upload_groups);$i < $count; $i++)
						{
							$group_id = intval($upload_groups[$i]);
							$u_quota = request_var('upload_quota_'.$group_id, '');
							$r_quota = request_var('remote_quota_'.$group_id, '');
							$upload_quota[] = $u_quota;
							$remote_quota[] = $r_quota;
							if ((empty($u_quota) || (empty($r_quota))) && $empty_upload_vehicle_quota == false )
							{
								$errors[] = $user->lang['EMPTY_GROUP_IMAGE_QUOTA'];
								$empty_upload_vehicle_quota = true;
								array_push($empty_upload_groups, $group_id);
								continue;
							}
							else if ((empty($u_quota) || (empty($r_quota))) && $empty_upload_vehicle_quota == true )
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
					$garage_admin->set_config('upload_groups', $upload_groups_value, $garage_config);
					$garage_admin->set_config('upload_groups_quotas', $upload_groups_quotas_value, $garage_config);
					$garage_admin->set_config('remote_groups', $remote_groups_value, $garage_config);
					$garage_admin->set_config('remote_groups_quotas', $remote_groups_quotas_value, $garage_config);
			
					trigger_error($user->lang['QUOTAS_UPDATED'] . adm_back_link($this->u_action));
		
				break;
			}
		}
		
		//Default management page
		
		//Get The List Of phpBB Usergroups Allowed Quotable Permissions
		$groupdata = $garage->get_groups_allowed_quotas();
		$add_groups = @explode(',', $garage_config['add_groups']);
		$upload_groups = @explode(',', $garage_config['upload_groups']);

		//Now Process Each & Every Group We Have
		for ($i = 0; $i < count($groupdata); $i++)
		{
			$groupname = (!empty($user->lang['G_' . $groupdata[$i]['group_name']]))? $user->lang['G_' . $groupdata[$i]['group_name']] : $groupdata[$i]['group_name'];
			$template->assign_block_vars('usergroup', array(
				'GROUP_ID' 		=> $groupdata[$i]['group_id'],
				'S_ADD_CHECKED' 	=> (in_array($groupdata[$i]['group_id'], $add_groups) || in_array($groupdata[$i]['group_id'], $empty_add_groups)) ? true : false,
				'S_UPLOAD_CHECKED' 	=> (in_array($groupdata[$i]['group_id'], $upload_groups)|| in_array($groupdata[$i]['group_id'], $empty_upload_groups)) ? true : false,
				'GROUP_NAME' 		=> $groupname,
				'VEHICLE_QUOTA' 	=> $garage_vehicle->get_group_vehicle_quota($groupdata[$i]['group_id']),
				'UPLOAD_QUOTA' 		=> $garage_image->get_group_upload_image_quota($groupdata[$i]['group_id']),
				'REMOTE_QUOTA' 		=> $garage_image->get_group_remote_image_quota($groupdata[$i]['group_id']),
			));
		}

		$template->assign_vars(array(
			'U_ACTION'		=> $this->u_action ."&amp;action=update",
			'U_BACK'		=> $this->u_action,
			'DEFAULT_VEHICLE_QUOTA' => (sizeof($errors)) ? $default_vehicle_quota : $garage_config['default_vehicle_quota'],
			'DEFAULT_UPLOAD_QUOTA' 	=> (sizeof($errors)) ? $default_upload_quota : $garage_config['default_upload_quota'],
			'DEFAULT_REMOTE_QUOTA' 	=> (sizeof($errors)) ? $default_remote_quota : $garage_config['default_remote_quota'],
			'S_ERROR'		=> (sizeof($errors)) ? true : false,
			'ERROR_MSG'		=> (sizeof($errors)) ? implode('<br />', $errors) : '',
		));
		
	}	
}


?>

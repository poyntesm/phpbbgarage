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
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package acp
*/
class acp_garage_quota
{
	var $u_action;

	function main($id, $mode)
	{
		/**
		* Setup global variables such as $db 
		*/
		global $db, $user, $auth, $template, $cache, $garage, $garage_config;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx;

		/**
		* Setup page variables such as title, template & available language strings
		*/
		$user->add_lang('acp/garage');
		$this->tpl_name = 'acp_garage_quota';
		$this->page_title = 'ACP_MANAGE_QUOTAS';

		/**
		* Build All Garage Classes e.g $garage_images->
		*/
		require($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_image.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_admin.' . $phpEx);

		/**
		* Setup variables required
		*/
		$action	= request_var('action', '');
		$update	= (isset($_POST['update'])) ? true : false;
		$errors = $empty_add_groups = $empty_upload_groups = $upload_quota = $remote_quota = array();
		$default_upload_quota = $default_remote_quota = $default_vehicle_quota = $upload_groups_value = $upload_groups_quotas_value = $remote_groups_value = $remote_groups_quotas_value = $add_groups_value = $add_groups_quotas_value = '' ;

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
				* Get quotas from page and update DB
				* Update once we have all data required to update a complete quota (default, upload, remote) 
				*/
				case 'update':
					/**
					* Handle default user quotas
					*/
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

					/**
					* Handle group add quota, this is for number of vehicles allowed
					*/
					$add_groups = request_var('add_groups', array(0));
					if ($add_groups)
					{
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

					/**
					* Update add quota if no errors
					* Sync config so if errors during upload quota page will reflect new value
					*/
					if (empty($errors))
					{
						$garage_admin->set_config('add_groups', $add_groups_value, $garage_config);
						$garage_admin->set_config('add_groups_quotas', $add_groups_quotas_value, $garage_config);
						$garage_config = $garage_admin->sync_config();
					}

					/**
					* Handle group upload quota, this is for number of images allowed
					* There are two values needed..upload & remote
					*/
					$upload_groups = $remote_groups = request_var('upload_groups', array(0));
					if ($upload_groups) 
					{
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
					}

					/**
					* Update upload quota
					* Display success page if we got this far with no errors :)
					*/
					if (empty($errors))
					{
						$garage_admin->set_config('upload_groups', implode(",", $upload_groups), $garage_config);
						$garage_admin->set_config('upload_groups_quotas', implode(",", $upload_quota), $garage_config);
						$garage_admin->set_config('remote_groups', implode(",", $remote_groups), $garage_config);
						$garage_admin->set_config('remote_groups_quotas', implode(",", $remote_quota), $garage_config);
						trigger_error($user->lang['QUOTAS_UPDATED'] . adm_back_link($this->u_action));
					}
				break;
			}
		}
		
		/**
		* Display default page to current quotas
		*/
		
		/**
		* Get an array of groups that have permission to a quotable permission
		* Also build two extra arrays to hold groups granted specific permission
		*/
		$groupdata = $garage->get_groups_allowed_quotas();
		$add_groups = @explode(',', $garage_config['add_groups']);
		$upload_groups = @explode(',', $garage_config['upload_groups']);

		/**
		* Process each group that has a quotable permission
		*/
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

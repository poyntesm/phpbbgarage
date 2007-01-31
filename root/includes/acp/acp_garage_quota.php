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


		$action		= request_var('action', '');
		$update		= (isset($_POST['update'])) ? true : false;

		switch ($mode)
		{
			case 'update_permissions':
		
				//Right First Lets Handle Private Usergroups With 'DENY' Setup..
				$deny_groups = str_replace("\'", "''", @implode(',', $HTTP_POST_VARS['deny']));
				$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', $deny_groups, 'config_name', 'private_deny_perms');
		
				//Now we loop through all other permissions types...
				$permission_mode = array('BROWSE', 'INTERACT', 'ADD', 'UPLOAD');
				$permission_mode_lwr = array('browse', 'interact', 'add', 'upload');
				for($i = 0; $i < count($permission_mode); $i++)
				{
					$db_string = '';
					//Let Work Out If All Permissions Are Set Or Global Is Set
					if ( (($HTTP_POST_VARS[$permission_mode[$i]."_ADMIN"] == 1) AND ($HTTP_POST_VARS[$permission_mode[$i]."_MOD"] == 1) AND ($HTTP_POST_VARS[$permission_mode[$i]."_USER"] == 1) AND ($HTTP_POST_VARS[$permission_mode[$i]."_GUEST"] == 1)) OR ($HTTP_POST_VARS[$permission_mode[$i]."_ALL"] == 1) )
					{
						$db_string = "*";
					}
					//Since All Permissions Or Global Not Set...Work Out Which Were....
					else
					{
						$admin = ($HTTP_POST_VARS[$permission_mode[$i]."_ADMIN"] == 1) ? 'ADMIN' : '' ;
						if (!empty($admin) AND empty($db_string))
						{
							$db_string = "$admin";
						}
		
						$mod = ($HTTP_POST_VARS[$permission_mode[$i]."_MOD"] == 1) ? 'MOD' : '' ;
						if (!empty($mod) AND !empty($db_string))
						{
							$db_string .= ",$mod";
						}
						else if  (!empty($mod) AND empty($db_string))
						{
							$db_string = "$mod";
						}
		
						$user = ($HTTP_POST_VARS[$permission_mode[$i]."_USER"] == 1) ? 'USER' : '' ;
						if (!empty($user) AND !empty($db_string))
						{
							$db_string .= ",$user";
						}
						else if  (!empty($user) AND empty($db_string))
						{
							$db_string = "$user";
						}
		
						$guest = ($HTTP_POST_VARS[$permission_mode[$i]."_GUEST"] == 1) ? 'GUEST' : '' ;
						if (!empty($guest) AND !empty($db_string))
						{
							$db_string .= ",$guest";
						}
						else if  (!empty($guest) AND empty($db_string))
						{
							$db_string = "$guest";
						}
		
						$private = ($HTTP_POST_VARS[$permission_mode[$i]."_PRIVATE"] == 1) ? 'PRIVATE' : '' ;
						if (!empty($private) AND !empty($db_string))
						{
							$db_string .= ",$private";
						}
						else if  (!empty($private) AND empty($db_string))
						{
							$db_string = "$private";
						}
					}
		
					// Now We Update The DB With The New Permissions
					$sql = "UPDATE ". GARAGE_CONFIG_TABLE ."
						SET config_value = '$db_string' WHERE config_name = '".$permission_mode_lwr[$i]."_perms'";
					if(!$result = $db->sql_query($sql))
					{
						message_die(GENERAL_ERROR, 'Could Not Update The Config', '', __LINE__, __FILE__, $sql);
					}
		
				}//End For Loop
				
				//Let See If Any Private 'BROWSE' Permisssions Are Set
				if ($HTTP_POST_VARS['BROWSE_PRIVATE'] == 1) 
				{
					$browse_groups = str_replace("\'", "''", @implode(',', $HTTP_POST_VARS['browse']));
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', $browse_groups, 'config_name', 'private_browse_perms');
				}
				else
				{
					//No Private Permisssion Set...So Blank DB Field
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', '', 'config_name', 'private_browse_perms');
				}
		
				//Let See If Any Private 'INTERACT' Permisssions Are Set
				if ($HTTP_POST_VARS['INTERACT_PRIVATE'] == 1) 
				{
					$interact_groups = str_replace("\'", "''", @implode(',', $HTTP_POST_VARS['interact']));
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', $interact_groups, 'config_name', 'private_interact_perms');
				}
				else
				{
					//No Private Permisssion Set...So Blank DB Field
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', '', 'config_name', 'private_interact_perms');
				}
		
				//Let See If Any Private 'ADD' Permisssions & Quotas Are Set
				if ($HTTP_POST_VARS['ADD_PRIVATE'] == 1) 
				{
					$add_groups = str_replace("\'", "''", @implode(',', $HTTP_POST_VARS['add']));
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', $add_groups, 'config_name', 'private_add_perms');
					//Now Lets Get Quota For Groups That Have Been Granted Permission
					foreach ( $HTTP_POST_VARS['upload'] as $id)
					{
						$group_id = intval($id);
						$add_quota .= intval($HTTP_POST_VARS['add_quota_'.$group_id]).',';
					}
					//Set Add Quotas
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', substr($add_quota, 0, -1), 'config_name', 'private_add_quota');
				}
				else
				{
					//No Private Permisssion Set...So Blank DB Fields
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', '', 'config_name', 'private_add_perms');
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', '', 'config_name', 'private_add_quota');
				}
		
				//Let See If Any Private 'UPLOAD' Permisssions & Quotas Are Set
				if ($HTTP_POST_VARS['UPLOAD_PRIVATE'] == 1) 
				{
					$upload_groups = str_replace("\'", "''", @implode(',', $HTTP_POST_VARS['upload']));
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', $upload_groups, 'config_name', 'private_upload_perms');
					//Now Lets Get Image Quota For Groups That Have Been Granted Permission
					foreach ( $HTTP_POST_VARS['upload'] as $id)
					{
						$group_id = intval($id);
						$upload_quota .= intval($HTTP_POST_VARS['upload_quota_'.$group_id]).',';
						$remote_quota .= intval($HTTP_POST_VARS['remote_quota_'.$group_id]).',';
					}
					//Set Image Quotas
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', substr($upload_quota, 0, -1), 'config_name', 'private_upload_quota');
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', substr($remote_quota, 0, -1), 'config_name', 'private_remote_quota');
				}
				else
				{
					//No Private Permisssion Set...So Blank DB Fields
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', '', 'config_name', 'private_upload_perms');
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', '', 'config_name', 'private_upload_quota');
					$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', '', 'config_name', 'private_remote_quota');
				}
		
				//Handle Default User Quotas
				$default_upload_quota = intval($HTTP_POST_VARS['max_upload_images']);
				$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', $default_upload_quota, 'config_name', 'max_upload_images');
				$default_remote_quota = intval($HTTP_POST_VARS['max_remote_images']);
				$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', $default_remote_quota, 'config_name', 'max_remote_images');
				$default_add_quota = intval($HTTP_POST_VARS['max_user_cars']);
				$garage->update_single_field(GARAGE_CONFIG_TABLE,'config_value', $default_add_quota, 'config_name', 'max_user_cars');
		
				//Assemble Message With Redirect That All Permissions Updated
				$message = '<meta http-equiv="refresh" content="2;url=' . append_sid("admin_garage_permissions.$phpEx") . '">' . $lang['Permissions_Updated'] . "<br /><br />" . sprintf($lang['Click_Return_Permissions'], "<a href=\"" . append_sid("admin_garage_permissions.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");
		
				message_die(GENERAL_MESSAGE, $message);
		
				break;
		
			default:
		
				// Get the list of phpBB usergroups
				$sql = "SELECT group_id, group_name
					FROM " . GROUPS_TABLE . "
					ORDER BY group_name ASC";
				if ( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, 'Could not get group list', '', __LINE__, __FILE__, $sql);
				}
		
				while( $row = $db->sql_fetchrow($result) )
				{
					$groupdata[] = $row;
				}
		
				//Get Deny Permission Info For Usergruops...Bit Messy But Works!!
				$sql = "SELECT config_value as private_deny_groups
					FROM ". GARAGE_CONFIG_TABLE ."
					WHERE config_name = 'private_deny_perms'";
				if( !$result = $db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, 'Could not get permissions', '', __LINE__, __FILE__, $sql);
				}
				$deny_perms = $db->sql_fetchrow($result);
				$deny_groups = @explode(',', $deny_perms['private_deny_groups']);
		
				//Get Browse Permission Info For Usergruops...Bit Messy But Works!!
				$sql = "SELECT config_value as private_browse_groups
					FROM ". GARAGE_CONFIG_TABLE ."
					WHERE config_name = 'private_browse_perms'";
				if( !$result = $db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, 'Could not get permissions', '', __LINE__, __FILE__, $sql);
				}
				$browse_perms = $db->sql_fetchrow($result);
				$browse_groups = @explode(',', $browse_perms['private_browse_groups']);
		
				//Get Interact Permission Info For Usergruops...Bit Messy But Works!!
				$sql = "SELECT config_value as private_interact_groups
					FROM ". GARAGE_CONFIG_TABLE ."
					WHERE config_name = 'private_interact_perms'";
				if( !$result = $db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, 'Could not get permissions', '', __LINE__, __FILE__, $sql);
				}
				$interact_perms = $db->sql_fetchrow($result);
				$interact_groups = @explode(',', $interact_perms['private_interact_groups']);
		
				//Get Interact Permission Info For Usergruops...Bit Messy But Works!!
				$sql = "SELECT config_value as private_add_groups
					FROM ". GARAGE_CONFIG_TABLE ."
					WHERE config_name = 'private_add_perms'";
				if( !$result = $db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, 'Could not get permissions', '', __LINE__, __FILE__, $sql);
				}
				$add_perms = $db->sql_fetchrow($result);
				$add_groups = @explode(',', $add_perms['private_add_groups']);
		
				//Get Interact Permission Info For Usergruops...Bit Messy But Works!!
				$sql = "SELECT config_value as private_upload_groups
					FROM ". GARAGE_CONFIG_TABLE ."
					WHERE config_name = 'private_upload_perms'";
				if( !$result = $db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, 'Could not get permissions', '', __LINE__, __FILE__, $sql);
				}
				$upload_perms = $db->sql_fetchrow($result);
				$upload_groups = @explode(',', $upload_perms['private_upload_groups']);
		
				//Now Process Each & Every Group We Have
				for ($i = 0; $i < count($groupdata); $i++)
				{
					$template->assign_block_vars('usergroup', array(
						'GROUP_ID' => $groupdata[$i]['group_id'],
						'GROUP_NAME' => $groupdata[$i]['group_name'],
						'DENY_CHECKED' => (in_array($groupdata[$i]['group_id'], $deny_groups)) ? 'checked="checked"' : '',
						'UPLOAD_QUOTA' => $garage_image->get_group_upload_image_quota($groupdata[$i]['group_id']),
						'REMOTE_QUOTA' => $garage_image->get_group_remote_image_quota($groupdata[$i]['group_id']),
						'BROWSE_CHECKED' => (in_array($groupdata[$i]['group_id'], $browse_groups)) ? 'checked="checked"' : '',
						'INTERACT_CHECKED' => (in_array($groupdata[$i]['group_id'], $interact_groups)) ? 'checked="checked"' : '',
						'ADD_CHECKED' => (in_array($groupdata[$i]['group_id'], $add_groups)) ? 'checked="checked"' : '',
						'ADD_DISPLAY' => (in_array($groupdata[$i]['group_id'], $add_groups)) ? '' : 'none',
						'UPLOAD_CHECKED' => (in_array($groupdata[$i]['group_id'], $upload_groups)) ? 'checked="checked"' : '',
						'UPLOAD_DISPLAY' => (in_array($groupdata[$i]['group_id'], $upload_groups)) ? '' : 'none')
					);
				}
		
				$template->set_filenames(array(
					"body" => "admin/garage_permissions.tpl")
				);
		
				$template->assign_vars(array(
					'MAX_USER_CARS' => $garage_config['max_user_cars'],
					'MAX_UPLOAD_IMAGES' => $garage_config['max_upload_images'],
					'MAX_REMOTE_IMAGES' => $garage_config['max_remote_images'],
					'S_GARAGE_ACTION' => append_sid("admin_garage_permissions.$phpEx?mode=update_permissions"))
				);
		
				break;
		}
	}	
}


?>

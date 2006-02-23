<?php
/***************************************************************************
 *                              admin_garage_permissions.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: admin_garage_categories.php,v 0.0.9 06/06/2005 20:47:20 poynesmo Exp $
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

define('IN_PHPBB', true);

if( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['Garage']['Permissions'] = $filename;
	return;
}

// Let's set the root dir for phpBB
$phpbb_root_path = '../';
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);
require($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_garage.' . $phpEx);
require($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_garage_error.' . $phpEx);
require($phpbb_root_path . 'includes/functions_garage.' . $phpEx);

if( isset( $HTTP_POST_VARS['mode'] ) || isset( $HTTP_GET_VARS['mode'] ) )
{
	$mode = ( isset($HTTP_POST_VARS['mode']) ) ? $HTTP_POST_VARS['mode'] : $HTTP_GET_VARS['mode'];
}
else
{
	$mode = '';
}


switch($mode)
{
	case 'update_permissions':

		//Now we loop through all permissions types...
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
		
		//Right Now Lets Handle Private Usergroup Permissions If Any Permission Set To Private
		if ($HTTP_POST_VARS['BROWSE_PRIVATE'] == 1) 
		{
			$browse_groups = @implode(',', str_replace("\'", "''", htmlspecialchars(trim($HTTP_POST_VARS['browse']))));
			$sql = "UPDATE ". GARAGE_CONFIG_TABLE ."
				SET config_value = '$browse_groups'
				WHERE config_name = 'private_browse_perms'";
			if ( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not update permissions', '', __LINE__, __FILE__, $sql);
			}
		}

		if ($HTTP_POST_VARS['INTERACT_PRIVATE'] == 1) 
		{
			$interact_groups = @implode(',', str_replace("\'", "''", htmlspecialchars(trim($HTTP_POST_VARS['interact']))));
			$sql = "UPDATE ". GARAGE_CONFIG_TABLE ."
				SET config_value = '$interact_groups'
				WHERE config_name = 'private_interact_perms'";
			if ( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not update permissions', '', __LINE__, __FILE__, $sql);
			}
		}

		if ($HTTP_POST_VARS['ADD_PRIVATE'] == 1) 
		{
			$add_groups = @implode(',', str_replace("\'", "''", htmlspecialchars(trim($HTTP_POST_VARS['add']))));
			$sql = "UPDATE ". GARAGE_CONFIG_TABLE ."
				SET config_value = '$add_groups'
				WHERE config_name = 'private_add_perms'";
			if ( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not update permissions', '', __LINE__, __FILE__, $sql);
			}
		}

		if ($HTTP_POST_VARS['UPLOAD_PRIVATE'] == 1) 
		{
			$upload_groups = @implode(',', str_replace("\'", "''", htmlspecialchars(trim($HTTP_POST_VARS['upload']))));
			$sql = "UPDATE ". GARAGE_CONFIG_TABLE ."
				SET config_value = '$upload_groups'
				WHERE config_name = 'private_upload_perms'";
			if ( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not update permissions', '', __LINE__, __FILE__, $sql);
			}
		}

		$template->set_filenames(array(
			'body' => 'admin/garage_message.tpl')
		);

		$text = "<br /><b>".$lang['Permissions_Updated']."</b><br />";

		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="1;url=' . append_sid("admin_garage_permissions.$phpEx") . '">',
			'TEXT' => "<br /><br /><p>$text</p><br /><br />")

		);

		$template->pparse('body');

		break;

	default:

		$permission_mode = array('browse', 'interact', 'add', 'upload');
		for($i = 0; $i < count($permission_mode); $i++)
		{
			$all_checked = '';
       			$admin_checked = '';
       			$mod_checked = '';
	       		$user_checked = '';
	       		$guest_checked = '';
       			$private_checked = '';

			if ($garage_config[$permission_mode[$i]."_perms"] == '*')
			{
		       		$all_checked = 'checked="checked"';
	       			$admin_checked = 'checked="checked"';
	       			$mod_checked = 'checked="checked"';
		       		$user_checked = 'checked="checked"';
		       		$guest_checked = 'checked="checked"';
			}
			else
			{
				if ( preg_match( "/ADMIN/i", $garage_config[$permission_mode[$i]."_perms"] ) )
				{
		       			$admin_checked = 'checked="checked"';
				}
				if ( preg_match( "/MOD/i", $garage_config[$permission_mode[$i]."_perms"] ) )
				{
	       				$mod_checked = 'checked="checked"';
				}
				if ( preg_match( "/USER/i", $garage_config[$permission_mode[$i]."_perms"] ) )
				{
	       				$user_checked = 'checked="checked"';
				}
				if ( preg_match( "/GUEST/i", $garage_config[$permission_mode[$i]."_perms"] ) )
				{
	       				$guest_checked = 'checked="checked"';
				}
				if ( preg_match( "/PRIVATE/i", $garage_config[$permission_mode[$i]."_perms"] ) )
				{
	       				$private_checked = 'checked="checked"';
	       				$private_found = 'TRUE';
				}
			}

			if ($permission_mode[$i] == 'browse')
			{
				$template->assign_vars(array(
					'BROWSE_ALL_CHECKED' => $all_checked,
					'BROWSE_ADMIN_CHECKED' => $admin_checked,
					'BROWSE_MOD_CHECKED' => $mod_checked,
					'BROWSE_USER_CHECKED' => $user_checked,
					'BROWSE_GUEST_CHECKED' => $guest_checked,
					'BROWSE_PRIVATE_CHECKED' => $private_checked)
				);
			}
			else if ($permission_mode[$i] == 'interact')
			{
				$template->assign_vars(array(
					'INTERACT_ALL_CHECKED' => $all_checked,
					'INTERACT_ADMIN_CHECKED' => $admin_checked,
					'INTERACT_MOD_CHECKED' => $mod_checked,
					'INTERACT_USER_CHECKED' => $user_checked,
					'INTERACT_GUEST_CHECKED' => $guest_checked,
					'INTERACT_PRIVATE_CHECKED' => $private_checked)
				);
			}
			else if ($permission_mode[$i] == 'add')
			{
				$template->assign_vars(array(
					'ADD_ALL_CHECKED' => $all_checked,
					'ADD_ADMIN_CHECKED' => $admin_checked,
					'ADD_MOD_CHECKED' => $mod_checked,
					'ADD_USER_CHECKED' => $user_checked,
					'ADD_GUEST_CHECKED' => $guest_checked,
					'ADD_PRIVATE_CHECKED' => $private_checked)
				);
			}
			else if ($permission_mode[$i] == 'upload')
			{
				$template->assign_vars(array(
					'UPLOAD_ALL_CHECKED' => $all_checked,
					'UPLOAD_ADMIN_CHECKED' => $admin_checked,
					'UPLOAD_MOD_CHECKED' => $mod_checked,
					'UPLOAD_USER_CHECKED' => $user_checked,
					'UPLOAD_GUEST_CHECKED' => $guest_checked,
					'UPLOAD_PRIVATE_CHECKED' => $private_checked)
				);
			}
		}

		if (!empty($private_found))
		{

			$template->assign_block_vars('private', array());

			// Get the list of phpBB usergroups
			$sql = "SELECT group_id, group_name
				FROM " . GROUPS_TABLE . "
				WHERE group_single_user <> " . TRUE ."
				ORDER BY group_name ASC";
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not get group list', '', __LINE__, __FILE__, $sql);
			}

			while( $row = $db->sql_fetchrow($result) )
			{
				$groupdata[] = $row;
			}

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

			for ($i = 0; $i < count($groupdata); $i++)
			{

				$template->assign_block_vars('usergroup', array(
					'GROUP_ID' => $groupdata[$i]['group_id'],
					'GROUP_NAME' => $groupdata[$i]['group_name'],
					'BROWSE_CHECKED' => (in_array($groupdata[$i]['group_id'], $browse_groups)) ? 'checked="checked"' : '',
					'INTERACT_CHECKED' => (in_array($groupdata[$i]['group_id'], $interact_groups)) ? 'checked="checked"' : '',
					'ADD_CHECKED' => (in_array($groupdata[$i]['group_id'], $add_groups)) ? 'checked="checked"' : '',
					'UPLOAD_CHECKED' => (in_array($groupdata[$i]['group_id'], $upload_groups)) ? 'checked="checked"' : '')
				);
			}
		}//End If Private Set

		$template->set_filenames(array(
			"body" => "admin/garage_permissions.tpl")
		);

		$template->assign_vars(array(
			'L_GARAGE_PERMISSIONS_TITLE' => $lang['Garage_Permissions_Title'],
			'L_GARAGE_PERMISSIONS_EXPLAIN' => $lang['Garage_Permissions_Explain'],
			'L_PERMISSION_ACCESS_LEVELS' => $lang['Permission_Access_Levels'],
			'L_NAME' => $lang['Name'],
			'L_BROWSE' => $lang['Browse'],
			'L_INTERACT' => $lang['Interact'],
			'L_ADD' => $lang['Add'],
			'L_UPLOAD' => $lang['Upload'],
			'L_SELECT' => $lang['Select'],
			'L_PRIVATE_PERMISSIONS' => $lang['Private_Permissions'],
			'L_GRANULAR_PERMISSIONS' => $lang['Granular_Permissions'],
			'L_GLOBAL_ALL_MASKS' => $lang['Global_All_Masks'],
			'L_ALL_MASKS' => $lang['All_Masks'],
			'L_ADMINISTRATORS' => $lang['Administrators'],
			'L_MODERATORS' => $lang['Moderators'],
			'L_REGISTERED_USERS' => $lang['Registered_Users'],
			'L_GUEST_USERS' => $lang['Guest_Users'],
			'L_PRIVATE' => $lang['Private'],
			'L_SAVE' => $lang['Save'],
			'S_GARAGE_ACTION' => append_sid("admin_garage_permissions.$phpEx?mode=update_permissions"))
		);

		$template->pparse("body");

}

include('./page_footer_admin.'.$phpEx);

?>

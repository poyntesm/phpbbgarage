<?php
/**
*
* @package install
* @version $Id: functions_install.php,v 1.15 2007/07/28 15:16:07 davidmj Exp $
* @copyright (c) 2006 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* Update role-specific ACL options. Function can grant or remove options. If option already granted it will NOT be updated.
* 
* @param grant|remove $mode defines whether roles are granted to removed
* @param strong $role_name role name to update
* @param mixed $options auth_options to grant (a auth_option has to be specified)
* @param ACL_YES|ACL_NO|ACL_NEVER $auth_setting defines the mode acl_options are getting set with
 *
*/
function update_user_permissions($mode = 'grant', $username, $options = array(), $auth_setting = ACL_YES)
{
	global $db, $auth, $cache;

	//First We Get User ID 
	$sql = "SELECT u.user_id
		FROM " . USERS_TABLE . " u
		WHERE username = '$username'";
	$result = $db->sql_query($sql);
	$user_id = (int) $db->sql_fetchfield('user_id');
	$db->sql_freeresult($result);

	//Now Lets Get All Current Options For User
	$user_options = array();
	$sql = "SELECT auth_option_id
		FROM " . ACL_USERS_TABLE . "
		WHERE user_id = " . (int) $user_id . "
		GROUP BY auth_option_id";
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		$user_options[] = $row;
	}
	$db->sql_freeresult($result);

	//Get Option ID Values For Options Granting Or Removing
	$acl_options_ids = array();
	$sql = "SELECT auth_option_id
		FROM " . ACL_OPTIONS_TABLE . "
		WHERE " . $db->sql_in_set('auth_option', $options) . "
		GROUP BY auth_option_id";
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		$acl_options_ids[] = $row;
	}
	$db->sql_freeresult($result);


	//If Granting Permissions
	if ($mode == 'grant')
	{
		//Make Sure We Have Option IDs
		if (empty($acl_options_ids))
		{
			return false;
		}
		
		//Build SQL Array For Query
		$sql_ary = array();
		for ($i = 0, $count = sizeof($acl_options_ids);$i < $count; $i++)
		{

			//If Option Already Granted To User Then Skip It
			if (in_array($acl_options_ids[$i]['auth_option_id'], $user_options))
			{
				continue;
			}
			$sql_ary[] = array(
				'user_id'		=> (int) $user_id,
				'auth_option_id'	=> (int) $acl_options_ids[$i]['auth_option_id'],
				'auth_setting'		=> $auth_setting, 
			);
		}

		$db->sql_multi_insert(ACL_USERS_TABLE, $sql_ary);
		$cache->destroy('acl_options');
		$auth->acl_clear_prefetch();
	}

	//If Removing Permissions
	if ($mode == 'remove')
	{
		//Make Sure We Have Option IDs
		if (empty($acl_options_ids))
		{
			return false;
		}
		
		//Process Each Option To Remove
		for ($i = 0, $count = sizeof($acl_options_ids);$i < $count; $i++)
		{
			$sql = "DELETE 
				FROM " . ACL_USERS_TABLE . "
				WHERE auth_option_id = " . $acl_options_ids[$i]['auth_option_id'];

			$db->sql_query($sql);
		}

		$cache->destroy('acl_options');
		$auth->acl_clear_prefetch();
	}

	return;
}

/**
* Update role-specific ACL options. Function can grant or remove options. If option already granted it will NOT be updated.
* 
* @param grant|remove $mode defines whether roles are granted to removed
* @param strong $role_name role name to update
* @param mixed $options auth_options to grant (a auth_option has to be specified)
* @param ACL_YES|ACL_NO|ACL_NEVER $auth_setting defines the mode acl_options are getting set with
 *
*/
function update_role_permissions($mode = 'grant', $role_name, $options = array(), $auth_setting = ACL_YES)
{
	global $db, $auth, $cache;

	//First We Get Role ID 
	$sql = "SELECT r.role_id
		FROM " . ACL_ROLES_TABLE . " r
		WHERE role_name = '$role_name'";
	$result = $db->sql_query($sql);
	$role_id = (int) $db->sql_fetchfield('role_id');
	$db->sql_freeresult($result);

	//Now Lets Get All Current Options For Role
	$role_options = array();
	$sql = "SELECT auth_option_id
		FROM " . ACL_ROLES_DATA_TABLE . "
		WHERE role_id = " . (int) $role_id . "
		GROUP BY auth_option_id";
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		$role_options[] = $row;
	}
	$db->sql_freeresult($result);

	//Get Option ID Values For Options Granting Or Removing
	$acl_options_ids = array();
	$sql = "SELECT auth_option_id
		FROM " . ACL_OPTIONS_TABLE . "
		WHERE " . $db->sql_in_set('auth_option', $options) . "
		GROUP BY auth_option_id";
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		$acl_options_ids[] = $row;
	}
	$db->sql_freeresult($result);


	//If Granting Permissions
	if ($mode == 'grant')
	{
		//Make Sure We Have Option IDs
		if (empty($acl_options_ids))
		{
			return false;
		}
		
		//Build SQL Array For Query
		$sql_ary = array();
		for ($i = 0, $count = sizeof($acl_options_ids);$i < $count; $i++)
		{

			//If Option Already Granted To Role Then Skip It
			if (in_array($acl_options_ids[$i]['auth_option_id'], $role_options))
			{
				continue;
			}
			$sql_ary[] = array(
				'role_id'		=> (int) $role_id,
				'auth_option_id'	=> (int) $acl_options_ids[$i]['auth_option_id'],
				'auth_setting'		=> $auth_setting, 
			);
		}

		$db->sql_multi_insert(ACL_ROLES_DATA_TABLE, $sql_ary);
		$cache->destroy('acl_options');
		$auth->acl_clear_prefetch();
	}

	//If Removing Permissions
	if ($mode == 'remove')
	{
		//Make Sure We Have Option IDs
		if (empty($acl_options_ids))
		{
			return false;
		}
		
		//Process Each Option To Remove
		for ($i = 0, $count = sizeof($acl_options_ids);$i < $count; $i++)
		{
			$sql = "DELETE 
				FROM " . ACL_ROLES_DATA_TABLE . "
				WHERE auth_option_id = " . $acl_options_ids[$i]['auth_option_id'];

			$db->sql_query($sql);
		}

		$cache->destroy('acl_options');
		$auth->acl_clear_prefetch();
	}

	return;
}

/**
* Update group-specific ACL options. Function can grant or remove options. If option already granted it will NOT be updated.
* 
* @param grant|remove $mode defines whether roles are granted to removed
* @param string $group_name group name to update
* @param mixed $options auth_options to grant (a auth_option has to be specified)
* @param ACL_YES|ACL_NO|ACL_NEVER $auth_setting defines the mode acl_options are getting set with
 *
*/
function update_group_permissions($mode = 'grant', $group_name, $options = array(), $auth_setting = ACL_YES)
{
	global $db, $auth, $cache;

	//First We Get Role ID 
	$sql = "SELECT g.group_id
		FROM " . GROUPS_TABLE . " g
		WHERE group_name = '$group_name'";
	$result = $db->sql_query($sql);
	$group_id = (int) $db->sql_fetchfield('group_id');
	$db->sql_freeresult($result);

	//Now Lets Get All Current Options For Role
	$group_options = array();
	$sql = "SELECT auth_option_id
		FROM " . ACL_GROUPS_TABLE . "
		WHERE group_id = " . (int) $group_id . "
		GROUP BY auth_option_id";
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		$group_options[] = $row;
	}
	$db->sql_freeresult($result);

	//Get Option ID Values For Options Granting Or Removing
	$sql = "SELECT auth_option_id
		FROM " . ACL_OPTIONS_TABLE . "
		WHERE " . $db->sql_in_set('auth_option', $options) . "
		GROUP BY auth_option_id";
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		$acl_options_ids[] = $row;
	}
	$db->sql_freeresult($result);


	//If Granting Permissions
	if ($mode == 'grant')
	{
		//Make Sure We Have Option IDs
		if (empty($acl_options_ids))
		{
			return false;
		}
		
		//Build SQL Array For Query
		$sql_ary = array();
		for ($i = 0, $count = sizeof($acl_options_ids);$i < $count; $i++)
		{

			//If Option Already Granted To Role Then Skip It
			if (in_array($acl_options_ids[$i]['auth_option_id'], $group_options))
			{
				continue;
			}
			$sql_ary[] = array(
				'group_id'		=> (int) $group_id,
				'auth_option_id'	=> (int) $acl_options_ids[$i]['auth_option_id'],
				'auth_setting'		=> $auth_setting, 
			);
		}

		$db->sql_multi_insert(ACL_GROUPS_TABLE, $sql_ary);
		$cache->destroy('acl_options');
		$auth->acl_clear_prefetch();
	}

	//If Removing Permissions
	if ($mode == 'remove')
	{
		//Make Sure We Have Option IDs
		if (empty($acl_options_ids))
		{
			return false;
		}
		
		//Process Each Option To Remove
		for ($i = 0, $count = sizeof($acl_options_ids);$i < $count; $i++)
		{
			$sql = "DELETE 
				FROM " . ACL_GROUPS_TABLE . "
				WHERE auth_option_id = " . $acl_options_ids[$i]['auth_option_id'];

			$db->sql_query($sql);
		}

		$cache->destroy('acl_options');
		$auth->acl_clear_prefetch();
	}

	return;
}

function create_modules($module_data)
{
	global $modules;

	for ($i = 0, $count = sizeof($module_data);$i < $count; $i++)
	{
		$errors = $modules->update_module_data($module_data[$i]);
		if (!sizeof($errors))
		{
			$modules->remove_cache_file();
		}
	}
}

function get_module_id($type, $module)
{
	global $db;

	$sql = "SELECT m.module_id
		FROM " . MODULES_TABLE . " m
		WHERE m.module_class = '".$type."'
			AND m.module_langname = '".$module."'";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);

	return $row['module_id'];
}

function get_role_by_name($name)
{
	global $db;

	$data = null;

	$sql = "SELECT *
		FROM " . ACL_ROLES_TABLE . "
		WHERE role_name = '$name'";
	$result = $db->sql_query($sql);
	$data = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	return $data;
}

?>

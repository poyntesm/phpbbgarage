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
* Set role-specific ACL options without deleting enter existing options. If option already set it will NOT be updated.
* 
* @param int $role_id role id to update (a role_id has to be specified)
* @param mixed $auth_options auth_options to grant (a auth_option has to be specified)
* @param ACL_YES|ACL_NO|ACL_NEVER $auth_setting defines the mode acl_options are getting set with
 *
*/
function acl_update_role($role_id, $auth_options, $auth_setting = ACL_YES)
{
	global $db, $cache, $auth;

	$acl_options_ids = get_acl_option_ids($auth_options);

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

	$sql_ary = array();
	for ($i = 0, $count = sizeof($acl_options_ids);$i < $count; $i++)
	{
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

/**
* Set user specific ACL options without deleting enter existing options. If option already set it will NOT be updated.
* 
* @param int $user_id user id to update (a user_id has to be specified)
* @param mixed $auth_options auth_options to grant (a auth_option has to be specified)
* @param ACL_YES|ACL_NO|ACL_NEVER $auth_setting defines the mode acl_options are getting set with
 *
*/
function acl_update_user($user_id, $auth_options, $auth_setting = ACL_YES)
{
	global $db, $cache, $auth;

	$acl_options_ids = get_acl_option_ids($auth_options);

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

	$sql_ary = array();
	for ($i = 0, $count = sizeof($acl_options_ids);$i < $count; $i++)
	{
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

/**
* Get ACL option ids
*
* @param mixed $auth_options auth_options to grant (a auth_option has to be specified)
*/
function get_acl_option_ids($auth_options)
{
	global $db;

	$data = array();
	$sql = "SELECT auth_option_id
		FROM " . ACL_OPTIONS_TABLE . "
		WHERE " . $db->sql_in_set('auth_option', $auth_options) . "
		GROUP BY auth_option_id";
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		$data[] = $row;
	}
	$db->sql_freeresult($result);

	return $data;
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

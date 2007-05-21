<?php
/** 
*
* @package install
* @version $Id$
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

/**
* Setup user session, authorisation & language 
*/
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/garage');

//Get Mode Required For Page
$mode	= request_var('mode', '');

$page_title = 'Manage phpBB Garage Modules';

// Output the page
page_header($page_title);
$template->set_filenames(array(
	'body' =>  'message_body.html')
);

require($phpbb_root_path . 'includes/acp/acp_modules.' . $phpEx);
$modules = new acp_modules();

//Decide What Mode The User Is Doing
switch( $mode )
{
	case 'add':

		//Lets Add Automatically The Modules
		$module_data = $errors = array();

		//Define ACP Garage Module Categories
		$module_data[] = array('module_basename' => '', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => '24', 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_SETTINGS', 'module_mode' => '', 'module_auth' => '' );
		$module_data[] = array('module_basename' => '', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => '24', 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_MANAGEMENT', 'module_mode' => '', 'module_auth' => '' );

		//Define MCP Garage Module Categories
		$module_data[] = array('module_basename' => '', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => '0', 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE', 'module_mode' => '', 'module_auth' => '' );

		//Define UCP Garage Module Categories
		$module_data[] = array('module_basename' => '', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => '0', 'module_class' => 'ucp', 'module_langname' => 'UCP_GARAGE', 'module_mode' => '', 'module_auth' => '' );

		//Create All Required Module Categories & Reset
		create_modules($module_data);
		$module_data = null;
		$module_data = array();

		$acp_settings_parent = get_module('acp', 'ACP_GARAGE_SETTINGS');
		$acp_management_parent = get_module('acp', 'ACP_GARAGE_MANAGEMENT');
		$mcp_parent = get_module('mcp', 'MCP_GARAGE');
		$ucp_parent = get_module('ucp', 'UCP_GARAGE');

		//Define ACP Settings Modules
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_GENERAL_SETTINGS', 'module_mode' => 'general', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_MENU_SETTINGS', 'module_mode' => 'menu', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_INDEX_SETTINGS', 'module_mode' => 'index', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_IMAGES_SETTINGS', 'module_mode' => 'images', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_QUARTERMILE_SETTINGS', 'module_mode' => 'quartermile', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_DYNORUN_SETTINGS', 'module_mode' => 'dynorun', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_TRACK_SETTINGS', 'module_mode' => 'track', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_INSURANCE_SETTINGS', 'module_mode' => 'insurance', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_BUSINESS_SETTINGS', 'module_mode' => 'business', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_RATING_SETTINGS', 'module_mode' => 'rating', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_GUESTBOOK_SETTINGS', 'module_mode' => 'guestbook', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_PRODUCT_SETTINGS', 'module_mode' => 'product', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_SERVICE_SETTINGS', 'module_mode' => 'service', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_BLOG_SETTINGS', 'module_mode' => 'blog', 'module_auth' => 'acl_a_garage_setting');

		//Define ACP Management Modules
		$module_data[] = array('module_basename' => 'garage_business', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_management_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_BUSINESS', 'module_mode' => 'business', 'module_auth' => 'acl_a_garage_business');
		$module_data[] = array('module_basename' => 'garage_category', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_management_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_CATEGORIES', 'module_mode' => 'categories', 'module_auth' => 'acl_a_garage_category');
		$module_data[] = array('module_basename' => 'garage_model', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_management_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_MODELS', 'module_mode' => 'makes', 'module_auth' => 'acl_a_garage_model');
		$module_data[] = array('module_basename' => 'garage_product', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_management_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_PRODUCTS', 'module_mode' => 'products', 'module_auth' => 'acl_a_garage_product');
		$module_data[] = array('module_basename' => 'garage_quota', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_management_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_QUOTAS', 'module_mode' => 'quotas', 'module_auth' => 'acl_a_garage_quota');
		$module_data[] = array('module_basename' => 'garage_tool', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_management_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_TOOLS', 'module_mode' => 'tools', 'module_auth' => 'acl_a_garage_tool');
		$module_data[] = array('module_basename' => 'garage_track', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_management_parent['module_id'], 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_TRACK', 'module_mode' => 'track', 'module_auth' => 'acl_a_garage_track');

		//Define MCP Modules
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent['module_id'], 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_VEHICLES', 'module_mode' => 'unapproved_vehicles', 'module_auth' => 'acl_m_garage_approve_vehicle');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent['module_id'], 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_MAKES', 'module_mode' => 'unapproved_makes', 'module_auth' => 'acl_m_garage_approve_make');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent['module_id'], 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_MODELS', 'module_mode' => 'unapproved_models', 'module_auth' => 'acl_m_garage_approve_model');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent['module_id'], 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_BUSINESS', 'module_mode' => 'unapproved_business', 'module_auth' => 'acl_m_garage_approve_business');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent['module_id'], 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_QUARTERMILES', 'module_mode' => 'unapproved_quartermiles', 'module_auth' => 'acl_m_garage_approve_quartermile');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent['module_id'], 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_DYNORUNS', 'module_mode' => 'unapproved_dynoruns', 'module_auth' => 'acl_m_garage_approve_dynorun');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent['module_id'], 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_GUESTBOOK_COMMENTS', 'module_mode' => 'unapproved_guestbook_comments', 'module_auth' => 'acl_m_garage_approve_gustbook');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent['module_id'], 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_LAPS', 'module_mode' => 'unapproved_laps', 'module_auth' => 'acl_m_garage_approve_lap');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent['module_id'], 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_TRACKS', 'module_mode' => 'unapproved_tracks', 'module_auth' => 'acl_m_garage_approve_track');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent['module_id'], 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_PRODUCTS', 'module_mode' => 'unapproved_products', 'module_auth' => 'acl_m_garage_approve_product');

		//Define UCP Modules
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $ucp_parent['module_id'], 'module_class' => 'ucp', 'module_langname' => 'UCP_GARAGE_OPTIONS', 'module_mode' => 'options', 'module_auth' => '');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $ucp_parent['module_id'], 'module_class' => 'ucp', 'module_langname' => 'UCP_GARAGE_NOTIFY', 'module_mode' => 'notify', 'module_auth' => '');

		create_modules($module_data);

		$template->assign_vars(array(
			'MESSAGE_TITLE'	=> 'MODULES ADDED',
			'MESSAGE_TEXT'	=> 'UCP, MCP & ACP Modules Added',
		));
	break;

	case 'delete':

		//Delete UCP Modules
		$ucp_modules = array('UCP_GARAGE_OPTIONS', 'UCP_GARAGE_NOTIFY', 'UCP_GARAGE');
		for ($i = 0, $count = sizeof($ucp_modules);$i < $count; $i++)
		{
			$module = get_module('ucp', $ucp_modules[$i]);
			if (!empty($module))
			{
				delete_module('ucp', $module);
			}
		}

		//Delete MCP Modules
		$mcp_modules = array('MCP_GARAGE_UNAPPROVED_VEHICLES', 'MCP_GARAGE_UNAPPROVED_MAKES', 'MCP_GARAGE_UNAPPROVED_MODELS', 'MCP_GARAGE_UNAPPROVED_BUSINESS', 'MCP_GARAGE_UNAPPROVED_QUARTERMILES', 'MCP_GARAGE_UNAPPROVED_DYNORUNS', 'MCP_GARAGE_UNAPPROVED_GUESTBOOK_COMMENTS', 'MCP_GARAGE_UNAPPROVED_LAPS', 'MCP_GARAGE_UNAPPROVED_TRACKS', 'MCP_GARAGE_UNAPPROVED_PRODUCTS', 'MCP_GARAGE');
		for ($i = 0, $count = sizeof($mcp_modules);$i < $count; $i++)
		{
			$module = get_module('mcp', $mcp_modules[$i]);
			if (!empty($module))
			{
				delete_module('mcp', $module);
			}
		}

		//Delete ACP Management Modules
		$acp_management_modules = array('ACP_GARAGE_BUSINESS', 'ACP_GARAGE_CATEGORIES', 'ACP_GARAGE_MODELS', 'ACP_GARAGE_PRODUCTS', 'ACP_GARAGE_QUOTAS', 'ACP_GARAGE_TOOLS', 'ACP_GARAGE_TRACK', 'ACP_GARAGE_MANAGEMENT');
		for ($i = 0, $count = sizeof($acp_management_modules);$i < $count; $i++)
		{
			$module = get_module('acp', $acp_management_modules[$i]);
			if (!empty($module))
			{
				delete_module('acp', $module);
			}
		}

		//Delete ACP Settings Modules
		$acp_settings_modules = array('ACP_GARAGE_GENERAL_SETTINGS', 'ACP_GARAGE_MENU_SETTINGS', 'ACP_GARAGE_INDEX_SETTINGS', 'ACP_GARAGE_IMAGES_SETTINGS', 'ACP_GARAGE_QUARTERMILE_SETTINGS', 'ACP_GARAGE_DYNORUN_SETTINGS', 'ACP_GARAGE_TRACK_SETTINGS', 'ACP_GARAGE_INSURANCE_SETTINGS', 'ACP_GARAGE_BUSINESS_SETTINGS', 'ACP_GARAGE_RATING_SETTINGS', 'ACP_GARAGE_GUESTBOOK_SETTINGS', 'ACP_GARAGE_PRODUCT_SETTINGS', 'ACP_GARAGE_SERVICE_SETTINGS', 'ACP_GARAGE_BLOG_SETTINGS','ACP_GARAGE_SETTINGS');
		for ($i = 0, $count = sizeof($acp_settings_modules);$i < $count; $i++)
		{
			$module = get_module('acp', $acp_settings_modules[$i]);
			if (!empty($module))
			{
				delete_module('acp', $module);
			}
		}

		$modules->remove_cache_file();

		$template->assign_vars(array(
			'MESSAGE_TITLE'	=> 'MODULES DELETED',
			'MESSAGE_TEXT'	=> 'UCP, MCP & ACP Modules Deleted',
		));
		
	break;
}

page_footer();

function create_modules($module_data)
{
	global $modules;

	for ($i = 0, $count = sizeof($module_data);$i < $count; $i++)
	{
		if ($module_data[$i]['module_class'] == 'acp')
		{
			$modules->module_class = 'acp';
		}
		if ($module_data[$i]['module_class'] == 'mcp')
		{
			$modules->module_class = 'mcp';
		}
		if ($module_data[$i]['module_class'] == 'ucp')
		{
			$modules->module_class = 'ucp';
		}
		$errors = $modules->update_module_data($module_data[$i]);
		if (!sizeof($errors))
		{
			$modules->remove_cache_file();
		}
	}

	return;
}

function get_module($type, $module_name)
{
	global $db;

	$sql = "SELECT *
		FROM " . MODULES_TABLE . " m
		WHERE m.module_class = '".$type."'
		AND m.module_langname = '".$module_name."'";

	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);

	return $row;
}

function get_module_branch($module_id, $module_class, $type = 'all', $order = 'descending', $include_module = true)
{
	global $db;

	switch ($type)
	{
		case 'parents':
			$condition = 'm1.left_id BETWEEN m2.left_id AND m2.right_id';
		break;

		case 'children':
			$condition = 'm2.left_id BETWEEN m1.left_id AND m1.right_id';
		break;

		default:
			$condition = 'm2.left_id BETWEEN m1.left_id AND m1.right_id OR m1.left_id BETWEEN m2.left_id AND m2.right_id';
		break;
	}

	$rows = array();

	$sql = 'SELECT m2.*
		FROM ' . MODULES_TABLE . ' m1
		LEFT JOIN ' . MODULES_TABLE . " m2 ON ($condition)
		WHERE m1.module_class = '" . $module_class . "'
			AND m2.module_class = '" . $module_class . "'
			AND m1.module_id = $module_id
		ORDER BY m2.left_id " . (($order == 'descending') ? 'ASC' : 'DESC');
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		if (!$include_module && $row['module_id'] == $module_id)
		{
			continue;
		}

		$rows[] = $row;
	}
	$db->sql_freeresult($result);

	return $rows;
}

function delete_module($type, $module)
{
	global $db, $user, $modules;

	$branch = get_module_branch($module['module_id'], $type, 'children', 'descending', false);

	if (sizeof($branch))
	{
		return array($user->lang['CANNOT_REMOVE_MODULE']);
	}

	// If not move
	$diff = 2;
	$sql = 'DELETE FROM ' . MODULES_TABLE . "
		WHERE module_class = '" . $type . "'
			AND module_id = " . $module['module_id'];
	$db->sql_query($sql);

	// Resync tree
	$sql = 'UPDATE ' . MODULES_TABLE . "
		SET right_id = right_id - $diff
		WHERE module_class = '" . $type . "'
			AND left_id < {$module['right_id']} AND right_id > {$module['right_id']}";
	$db->sql_query($sql);

	$sql = 'UPDATE ' . MODULES_TABLE . "
		SET left_id = left_id - $diff, right_id = right_id - $diff
		WHERE module_class = '" . $type . "'
			AND left_id > {$module['right_id']}";
	$db->sql_query($sql);

	return array();
}

?>

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

$page_title = 'Manage Garage Permissions';

// Output the page
page_header($page_title);
$template->set_filenames(array(
	'body' =>  'message_body.html')
);

//Setup $auth_admin class so we can add permission options
include($phpbb_root_path . '/includes/acp/auth.' . $phpEx);
$auth_admin = new auth_admin();

//Decide What Mode The User Is Doing
switch( $mode )
{
	case 'add':

		//Lets Add The Required New Permissions
		$phpbbgarage_permissions = array(
			'local'		=> array(),
			'global'	=> array(
				'u_garage_browse',
				'u_garage_search',
				'u_garage_add_vehicle',
				'u_garage_delete_vehicle',
				'u_garage_add_modification',
				'u_garage_delete_modification',
				'u_garage_add_quartermile',
				'u_garage_delete_quartermile',
				'u_garage_add_lap',
				'u_garage_delete_lap',
				'u_garage_add_track',
				'u_garage_delete_track',
				'u_garage_add_dynorun',
				'u_garage_delete_dynorun',
				'u_garage_add_insurance',
				'u_garage_delete_insurance',
				'u_garage_add_service',
				'u_garage_delete_service',
				'u_garage_add_blog',
				'u_garage_delete_blog',
				'u_garage_add_business',
				'u_garage_add_make_model',
				'u_garage_add_product',
				'u_garage_rate',
				'u_garage_comment',
				'u_garage_upload_image',
				'u_garage_remote_image',
				'u_garage_delete_image',
				'u_garage_deny',
				'm_garage_edit',
				'm_garage_delete',
				'm_garage_rating',
				'm_garage_approve_vehicle',
				'm_garage_approve_make',
				'm_garage_approve_model',
				'm_garage_approve_business',
				'm_garage_approve_quartermile',
				'm_garage_approve_dynorun',
				'm_garage_approve_guestbook',
				'm_garage_approve_lap',
				'm_garage_approve_track',
				'm_garage_approve_product',
			 	'a_garage_setting',
			 	'a_garage_business',
			 	'a_garage_category',
			 	'a_garage_field',
			 	'a_garage_model',
			 	'a_garage_product',
			 	'a_garage_quota',
			 	'a_garage_tool',
			 	'a_garage_track',
		));
		$auth_admin->acl_add_option($phpbbgarage_permissions);

		//Standard Admin Role
		$role = get_role_by_name('ROLE_ADMIN_STANDARD');
		if ($role)
		{
			acl_update_role($role['role_id'], array('a_garage_setting', 'a_garage_business', 'a_garage_category', 'a_garage_field', 'a_garage_model', 'a_garage_product', 'a_garage_quota', 'a_garage_tool', 'a_garage_track'));
		}

		//Full Admin Role
		$role = get_role_by_name('ROLE_ADMIN_FULL');
		if ($role)
		{
			acl_update_role($role['role_id'], array('a_garage_setting', 'a_garage_business', 'a_garage_category', 'a_garage_field', 'a_garage_model', 'a_garage_product', 'a_garage_quota', 'a_garage_tool', 'a_garage_track'));
		}

		//Queue Moderator Role
		$role = get_role_by_name('ROLE_MOD_QUEUE');
		if ($role)
		{
			acl_update_role($role['role_id'], array('m_garage_approve_vehicle', 'm_garage_approve_make', 'm_garage_approve_model', 'm_garage_approve_business', 'm_garage_approve_quartermile', 'm_garage_approve_dynorun', 'm_garage_approve_guestbook', 'm_garage_approve_lap', 'm_garage_approve_track', 'm_garage_approve_product'));
		}

		//Standard Moderator Role
		$role = get_role_by_name('ROLE_MOD_STANDARD');
		if ($role)
		{
			acl_update_role($role['role_id'], array('m_garage_edit', 'm_garage_delete', 'm_garage_rating', 'm_garage_approve_vehicle', 'm_garage_approve_make', 'm_garage_approve_model', 'm_garage_approve_business', 'm_garage_approve_quartermile', 'm_garage_approve_dynorun', 'm_garage_approve_guestbook', 'm_garage_approve_lap', 'm_garage_approve_track', 'm_garage_approve_product'));
		}
	
		//Full Moderator Role
		$role = get_role_by_name('ROLE_MOD_FULL');
		if ($role)
		{
			acl_update_role($role['role_id'], array('m_garage_edit', 'm_garage_delete', 'm_garage_rating', 'm_garage_approve_vehicle', 'm_garage_approve_make', 'm_garage_approve_model', 'm_garage_approve_business', 'm_garage_approve_quartermile', 'm_garage_approve_dynorun', 'm_garage_approve_guestbook', 'm_garage_approve_lap', 'm_garage_approve_track', 'm_garage_approve_product'));
		}

		//Standard Features User Role
		$role = get_role_by_name('ROLE_USER_STANDARD');
		if ($role)
		{
			acl_update_role($role['role_id'], array('u_garage_browse', 'u_garage_search', 'u_garage_add_vehicle', 'u_garage_delete_vehicle', 'u_garage_add_modification', 'u_garage_delete_modification', 'u_garage_add_quartermile', 'u_garage_delete_quartermile', 'u_garage_add_lap', 'u_garage_delete_lap', 'u_garage_add_track', 'u_garage_delete_track', 'u_garage_add_dynorun', 'u_garage_delete_dynorun', 'u_garage_add_insurance', 'u_garage_delete_insurance', 'u_garage_add_service', 'u_garage_delete_service', 'u_garage_add_blog', 'u_garage_delete_blog', 'u_garage_add_business', 'u_garage_add_make_model', 'u_garage_add_product', 'u_garage_rate', 'u_garage_comment', 'u_garage_upload_image', 'u_garage_remote_image', 'u_garage_delete_image', 'u_garage_deny'));
		}

		//All Features User Role
		$role = get_role_by_name('ROLE_USER_FULL');
		if ($role)
		{
			acl_update_role($role['role_id'], array('u_garage_browse', 'u_garage_search', 'u_garage_add_vehicle', 'u_garage_delete_vehicle', 'u_garage_add_modification', 'u_garage_delete_modification', 'u_garage_add_quartermile', 'u_garage_delete_quartermile', 'u_garage_add_lap', 'u_garage_delete_lap', 'u_garage_add_track', 'u_garage_delete_track', 'u_garage_add_dynorun', 'u_garage_delete_dynorun', 'u_garage_add_insurance', 'u_garage_delete_insurance', 'u_garage_add_service', 'u_garage_delete_service', 'u_garage_add_blog', 'u_garage_delete_blog', 'u_garage_add_business', 'u_garage_add_make_model', 'u_garage_add_product', 'u_garage_rate', 'u_garage_comment', 'u_garage_upload_image', 'u_garage_remote_image', 'u_garage_delete_image', 'u_garage_deny'));
		}

		$template->assign_vars(array(
			'MESSAGE_TITLE'	=> $user->lang['INSTALL_STEP1_SUCCESS'],
			'MESSAGE_TEXT'	=> sprintf($user->lang['INSTALL_STEP1_SUCCESS_EXPLAIN'], $u_step2, $u_step3, $u_step4)
		));
	break;

	case 'delete':

	break;


	case 'clear_cache':

		$cache->destroy('acl_options');
		$auth->acl_clear_prefetch();
	break;
}

page_footer();

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

?>

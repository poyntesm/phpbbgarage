<?php
/** 
*
* @package install
* @version $Id: install_convert.php,v 1.50 2007/07/16 01:06:34 davidmj Exp $
* @copyright (c) 2006 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
*/

if (!defined('IN_INSTALL'))
{
	// Someone has tried to access the file direct. This is not a good idea, so exit
	exit;
}

if (!empty($setmodules))
{
	// If phpBB Garage is not installed we do not include this module
	/*if (!defined('PHPBBGARAGE_INSTALLED'))
	{
		return;
	}*/

	$module[] = array(
		'module_type'		=> 'install',
		'module_title'		=> 'REMOVE',
		'module_filename'	=> substr(basename(__FILE__), 0, -strlen($phpEx)-1),
		'module_order'		=> 20,
		'module_subs'		=> '',
		'module_stages'		=> array('INTRO', 'DATA', 'FILES', 'FINAL'),
		'module_reqs'		=> ''
	);
}

/**
* Remove class for un-installs
* @package install
*/
class install_remove extends module
{
	function install_remove(&$p_master)
	{
		$this->p_master = &$p_master;
	}

	function main($mode, $sub)
	{
		global $lang, $template, $phpbb_root_path, $phpEx, $cache, $config, $language, $table_prefix;

		$this->tpl_name = 'garage_install_remove';
		$this->mode = $mode;

		switch ($sub)
		{
			case 'intro':
				if (!defined('PHPBB_INSTALLED'))
				{
					$template->assign_vars(array(
						'S_NOT_INSTALLED'	=> true,
						'TITLE'			=> $lang['BOARD_NOT_INSTALLED'],
						'BODY'			=> sprintf($lang['BOARD_NOT_INSTALLED_EXPLAIN'], append_sid($phpbb_root_path . 'install/index.' . $phpEx, 'mode=install&amp;language=' . $language)),
					));

					return;
				}

				$this->page_title = $lang['SUB_INTRO'];

				$template->assign_vars(array(
					'TITLE'		=> $lang['REMOVE_INTRO'],
					'BODY'		=> $lang['REMOVE_INTRO_BODY'],
					'L_SUBMIT'	=> $lang['NEXT_STEP'],
					'U_ACTION'	=> $this->p_master->module_url . "?mode=$mode&amp;sub=data&amp;language=$language",
				));

			break;

			case 'data':
				$this->delete_garage_data($mode, $sub);
			break;

			case 'files':
				$this->delete_garage_files($mode, $sub);
			break;

			case 'final':
				$this->page_title = $lang['REMOVE_COMPLETE'];

				$template->assign_vars(array(
					'TITLE'		=> $lang['REMOVE_COMPLETE'],
					'BODY'		=> $lang['REMOVE_COMPLETE_EXPLAIN'],
				));

			break;
		}
	}

	/**
	* Delete ALL phpBB Garage Data
	*/
	function delete_garage_data($mode, $sub)
	{
		global $cache, $db, $phpbb_root_path, $phpEx, $table_prefix, $lang, $template;

		$this->page_title = $lang['STAGE_OPTIONAL'];

		//Setup $auth_admin class so we can remove permission options
		include($phpbb_root_path . '/includes/acp/auth.' . $phpEx);
		include($phpbb_root_path . '/includes/acp/acp_modules.' . $phpEx);
		$auth_admin = new auth_admin();
		$module_admin = new acp_modules();

		// lets get rid of phpBB Garage internal data first
		foreach ($this->garage_tables as $table)
		{
			$db->sql_query('DROP TABLE ' . $table_prefix . $table);
		}

		// next for the chop is permisions we added
		foreach ($this->garage_permissions as $permission)
		{
			// get auth option id so we can remove it from any user, group or role
			$sql = $db->sql_build_query('SELECT', 
				array(
				'SELECT'	=> 'acl.auth_option_id',
				'FROM'		=> array(
					ACL_OPTIONS_TABLE	=> 'acl',
				),
				'WHERE'		=>  "acl.auth_option = '$permission'"
			));

			$result = $db->sql_query($sql);
			$auth_option_id = (int) $db->sql_fetchfield('auth_option_id');
			$db->sql_freeresult($result);

			// remove option from user
			$db->sql_query('DELETE FROM ' . ACL_USERS_TABLE . " WHERE auth_option_id = " . $auth_option_id);

			// remove option from group
			$db->sql_query('DELETE FROM ' . ACL_GROUPS_TABLE . " WHERE auth_option_id = " . $auth_option_id);
			
			// remove option from role
			$db->sql_query('DELETE FROM ' . ACL_ROLES_DATA_TABLE . " WHERE auth_option_id = " . $auth_option_id);

			// remove option itself now
			$db->sql_query('DELETE FROM ' . ACL_OPTIONS_TABLE . " WHERE auth_option_id = " . $auth_option_id);
		}

		// clear permissions cache now we have handled them all
		$cache->destroy('_acl_options');
		$auth_admin->acl_clear_prefetch();

		// next into the slaughter house is modules we added
		foreach ($this->garage_modules as $module_name => $module_class)
		{
			// get module id so we can remove it
			$sql = $db->sql_build_query('SELECT', 
				array(
				'SELECT'	=> 'm.*',
				'FROM'		=> array(
					MODULES_TABLE	=> 'm',
				),
				'WHERE'		=>  "m.module_langname = '$module_name'
							AND m.module_class = '$module_class'"
			));

			// module may have been installed in multiple places per class ... delete them all
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				$module_id = (int) $row['module_id'];
				$branch = $module_admin->get_module_branch($module_id, 'children', 'descending', false);

				if (sizeof($branch))
				{
					return array($user->lang['CANNOT_REMOVE_MODULE']);
				}

				// If not move
				$diff = 2;
				$sql = 'DELETE FROM ' . MODULES_TABLE . "
					WHERE module_class = '" . $module_class . "'
						AND module_id = $module_id";
				$db->sql_query($sql);
		
				$row['right_id'] = (int) $row['right_id'];
				$row['left_id'] = (int) $row['left_id'];

				// Resync tree
				$sql = 'UPDATE ' . MODULES_TABLE . "
					SET right_id = right_id - $diff
					WHERE module_class = '" . $module_class . "'
						AND left_id < {$row['right_id']} AND right_id > {$row['right_id']}";
				$db->sql_query($sql);

				$sql = 'UPDATE ' . MODULES_TABLE . "
					SET left_id = left_id - $diff, right_id = right_id - $diff
					WHERE module_class = '" . $module_class . "'
						AND left_id > {$row['right_id']}";
				$db->sql_query($sql);
			}
			$db->sql_freeresult($result);
		}

		// module categories ... be no more
		foreach ($this->garage_module_categories as $module_category => $module_class)
		{
			// get module id so we can remove it
			$sql = $db->sql_build_query('SELECT', 
				array(
				'SELECT'	=> 'm.*',
				'FROM'		=> array(
					MODULES_TABLE	=> 'm',
				),
				'WHERE'		=>  "m.module_langname = '$module_category'
							AND m.module_class = '$module_class'"
			));

			// we can only delete the categories we created...
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			$module_id = (int) $row['module_id'];

			$branch = $module_admin->get_module_branch($module_id, 'children', 'descending', false);

			if (sizeof($branch))
			{
				return array($user->lang['CANNOT_REMOVE_MODULE']);
			}

			// If not move
			$diff = 2;
			$sql = 'DELETE FROM ' . MODULES_TABLE . "
				WHERE module_class = '" . $module_class . "'
					AND module_id = $module_id";
			$db->sql_query($sql);
	
			$row['right_id'] = (int) $row['right_id'];
			$row['left_id'] = (int) $row['left_id'];

			// Resync tree
			$sql = 'UPDATE ' . MODULES_TABLE . "
				SET right_id = right_id - $diff
				WHERE module_class = '" . $module_class . "'
					AND left_id < {$row['right_id']} AND right_id > {$row['right_id']}";
			$db->sql_query($sql);

			$sql = 'UPDATE ' . MODULES_TABLE . "
				SET left_id = left_id - $diff, right_id = right_id - $diff
				WHERE module_class = '" . $module_class . "'
					AND left_id > {$row['right_id']}";
			$db->sql_query($sql);
		}
		
		// clear module cache now we have handled them all
		$module_admin->remove_cache_file();

		// and finally step forward data in $config
		foreach ($this->config_data as $config_data)
		{
			$db->sql_query('DELETE FROM ' . CONFIG_TABLE . " WHERE config_name = '{$config_data}'");
		}

		$submit = $lang['NEXT_STEP'];

		$url = $this->p_master->module_url . "?mode=$mode&amp;sub=files";

		$template->assign_vars(array(
			'BODY'		=> $lang['STAGE_DATA_EXPLAIN'],
			'L_SUBMIT'	=> $submit,
			'U_ACTION'	=> $url,
		));
	}

	/**
	* Delete ALL phpBB Garage Files
	*/
	function delete_garage_files($mode, $sub)
	{
		global $cache, $db, $phpbb_root_path, $phpEx, $lang, $template;

		$this->page_title = $lang['STAGE_FILES'];

		// lets get rid of phpBB Garage internal data first
		foreach ($this->garage_files as $file)
		{
			@unlink($phpbb_root_path . $file .$phpEx);
		}

		// lets get rid of phpBB Garage internal data first
		foreach ($this->garage_directories as $directory)
		{
			//@unlink
		}

		$submit = $lang['NEXT_STEP'];

		$url = $this->p_master->module_url . "?mode=$mode&amp;sub=final";

		$template->assign_vars(array(
			'BODY'		=> $lang['STAGE_FILES_EXPLAIN'],
			'L_SUBMIT'	=> $submit,
			'U_ACTION'	=> $url,
		));
	}

	/**
	* The information below will be used to remove $config entries
	*/
	var $config_data = array(
		'phpbbgarage_installed',
	);

	/**
	* The information below will be used to remove modules
	*/
	var $garage_modules = array(
		'UCP_GARAGE_OPTIONS'				=> 'ucp',
		'UCP_GARAGE_NOTIFY'				=> 'ucp',
		'MCP_GARAGE_UNAPPROVED_VEHICLES'		=> 'mcp',
		'MCP_GARAGE_UNAPPROVED_MAKES'			=> 'mcp',
		'MCP_GARAGE_UNAPPROVED_MODELS'			=> 'mcp',
		'MCP_GARAGE_UNAPPROVED_BUSINESS'		=> 'mcp',
		'MCP_GARAGE_UNAPPROVED_QUARTERMILES'		=> 'mcp',
		'MCP_GARAGE_UNAPPROVED_DYNORUNS'		=> 'mcp',
		'MCP_GARAGE_UNAPPROVED_GUESTBOOK_COMMENTS'	=> 'mcp',
		'MCP_GARAGE_UNAPPROVED_LAPS'			=> 'mcp',
		'MCP_GARAGE_UNAPPROVED_TRACKS'			=> 'mcp',
		'MCP_GARAGE_UNAPPROVED_PRODUCTS'		=> 'mcp',
		'ACP_GARAGE_GENERAL_SETTINGS'			=> 'acp',
		'ACP_GARAGE_MENU_SETTINGS'			=> 'acp',
		'ACP_GARAGE_INDEX_SETTINGS'			=> 'acp',
		'ACP_GARAGE_IMAGES_SETTINGS'			=> 'acp',
		'ACP_GARAGE_QUARTERMILE_SETTINGS'		=> 'acp',
		'ACP_GARAGE_DYNORUN_SETTINGS'			=> 'acp',
		'ACP_GARAGE_TRACK_SETTINGS'			=> 'acp',
		'ACP_GARAGE_INSURANCE_SETTINGS'			=> 'acp',
		'ACP_GARAGE_BUSINESS_SETTINGS'			=> 'acp',
		'ACP_GARAGE_RATING_SETTINGS'			=> 'acp',
		'ACP_GARAGE_GUESTBOOK_SETTINGS'			=> 'acp',
		'ACP_GARAGE_PRODUCT_SETTINGS'			=> 'acp',
		'ACP_GARAGE_SERVICE_SETTINGS'			=> 'acp',
		'ACP_GARAGE_BLOG_SETTINGS'			=> 'acp',
		'ACP_GARAGE_BUSINESS'				=> 'acp',
		'ACP_GARAGE_CATEGORIES'				=> 'acp',
		'ACP_GARAGE_MODELS'				=> 'acp',
		'ACP_GARAGE_PRODUCTS'				=> 'acp',
		'ACP_GARAGE_QUOTAS'				=> 'acp',
		'ACP_GARAGE_TOOLS'				=> 'acp',
		'ACP_GARAGE_TRACK'				=> 'acp',
	);

	/**
	* The information below will be used to remove module categories
	*/
	var $garage_module_categories = array(
		'UCP_GARAGE'		=> 'ucp',
		'MCP_GARAGE'		=> 'mcp',
		'ACP_GARAGE_SETTINGS'	=> 'acp',
		'ACP_GARAGE_MANAGEMENT'	=> 'acp',
	);

	/**
	* The information below will be used to remove files
	*/
	var $garage_files = array(
		'garage.php',
		'garage_blog.php',
		'garage_dynorun.php',
		'garage_guestbook.php',
		'garage_modification.php',
		'garage_premium.php',
		'garage_quartermile.php',
		'garage_service.php',
		'garage_track.php',
		'garage_vehicle.php',
		'adm/images/phpbbgarage_logo.gif',
		'adm/style/acp_garage_business.html',
		'adm/style/acp_garage_category.html',
		'adm/style/acp_garage_fields.html',
		'adm/style/acp_garage_message.html',
		'adm/style/acp_garage_model.html',
		'adm/style/acp_garage_orphans.html',
		'adm/style/acp_garage_product.html',
		'adm/style/acp_garage_quota.html',
		'adm/style/acp_garage_tool.html',
		'adm/style/acp_garage_track.html',
		'adm/style/garage.css',
		'adm/style/garage_install_convert.html',
		'adm/style/garage_install_error.html',
		'adm/style/garage_install_footer.html',
		'adm/style/garage_install_header.html',
		'adm/style/garage_install_install.html',
		'adm/style/garage_install_main.html',
		'adm/style/garage_install_remove.html',
		'adm/style/garage_install_updated.html',
		'adm/style/garage_install_update_diff.html',
		'includes/acp/',
		'includes/acp/',
		'includes/mcp/',
		'includes/mcp/',
		'includes/mods/',
		'includes/mods/',
		'includes/ucp/',
		'includes/ucp/',
		'language/',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
	);

	/**
	* The information below will be used to remove directories
	*/
	var $garage_directories = array(
		'garage',
	);

	/**
	* The information below will be used to drop all tables phpBB Garage created
	*/
	var $garage_tables = array(
		'garage_vehicles',
		'garage_business',
		'garage_categories',
		'garage_config',
		'garage_vehicles_gallery',
		'garage_modifications_gallery',
		'garage_quartermiles_gallery',
		'garage_dynoruns_gallery',
		'garage_laps_gallery',
		'garage_guestbooks',
		'garage_images',
		'garage_premiums',
		'garage_makes',
		'garage_models',
		'garage_modifications',
		'garage_products',
		'garage_quartermiles',
		'garage_dynoruns',
		'garage_ratings',
		'garage_tracks',
		'garage_laps',
		'garage_service_history',
		'garage_blog',
		'garage_custom_fields',
		'garage_custom_fields_data',
		'garage_custom_fields_lang',
		'garage_lang',
	);

	/**
	* The information below will be used to drop all phpBB Garage permissions
	*/
	var $garage_permissions = array(
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
	);
}

?>

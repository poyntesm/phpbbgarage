<?php
/** 
*
* @package install
* @version $Id: install_install.php 504 2008-01-23 15:09:35Z poyntesm $
* @copyright (c) 2005 phpBB Group 
* @copyright (c) 2007 Esmond Poynton
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
	// If phpBB Garage is already installed we do not include this module
	if (defined('PHPBBGARAGE_INSTALLED'))
	{
		return;
	}

	$module[] = array(
		'module_type'		=> 'install',
		'module_title'		=> 'INSTALLMIN',
		'module_filename'	=> substr(basename(__FILE__), 0, -strlen($phpEx)-1),
		'module_order'		=> 10,
		'module_subs'		=> '',
		'module_stages'		=> array('INTRO', 'REQUIREMENTS', 'OPTIONAL', 'CREATE_TABLE', 'CREATE_PERMISSIONS', 'INSTALL_MODULES', 'FINAL'),
		'module_reqs'		=> ''
	);
}

/**
* Installation
* @package install
*/
class install_installmin extends module
{
	function install_installmin(&$p_master)
	{
		$this->p_master = &$p_master;
	}

	function main($mode, $sub)
	{
		global $lang, $template, $language, $phpbb_root_path, $phpEx, $config, $db, $table_prefix, $db, $auth, $cache, $user;

		$this->tpl_name = 'garage_install_install';

		switch ($sub)
		{
			case 'intro':
				$this->page_title = $lang['SUB_INTRO'];

				$template->assign_vars(array(
					'TITLE'		=> $lang['INSTALL_INTRO'],
					'BODY'		=> $lang['INSTALL_INTRO_BODY'],
					'L_SUBMIT'	=> $lang['NEXT_STEP'],
					'U_ACTION'	=> $this->p_master->module_url . "?mode=$mode&amp;sub=requirements&amp;language=$language",
				));

			break;

			case 'requirements':
				$this->check_server_requirements($mode, $sub);

			break;

			case 'optional':
				$this->obtain_optional_settings($mode, $sub);
			break;

			case 'create_table':
				$this->load_schema($mode, $sub);
			break;

			case 'create_permissions':
				$this->add_permissions($mode, $sub);
				$this->update_user_permissions($mode, $sub);
				$this->update_role_permissions($mode, $sub);

				$submit = $lang['NEXT_STEP'];

				$url = $this->p_master->module_url . "?mode=$mode&amp;sub=install_modules";

				$template->assign_vars(array(
					'BODY'		=> $lang['STAGE_CREATE_PERMISSIONS_EXPLAIN'],
					'L_SUBMIT'	=> $submit,
					'U_ACTION'	=> $url,
				));

			break;

			case 'install_modules':
				$this->add_modules($mode, $sub);

				$submit = $lang['NEXT_STEP'];

				$url = $this->p_master->module_url . "?mode=$mode&amp;sub=fina;";

				$template->assign_vars(array(
					'BODY'		=> $lang['STAGE_INSTALL_MODULES_EXPLAIN'],
					'L_SUBMIT'	=> $submit,
					'U_ACTION'	=> $url,
				));
			break;

			case 'final':

				$this->tpl_name = 'garage_install_install';
				// Remove the lock file
				@unlink($phpbb_root_path . 'cache/install_lock');

				$sql = $db->sql_build_query('SELECT', 
					array(
					'SELECT'	=> 'c.config_name, c.config_value',
					'FROM'		=> array(
						GARAGE_CONFIG_TABLE	=> 'c',
					)
				));

				$result = $db->sql_query($sql);
				while( $row = $db->sql_fetchrow($result) )
				{
					$garage_config[$row['config_name']] = $row['config_value'];
				}
				$db->sql_freeresult($result);

				$template->assign_vars(array(
					'S_FILE_CHECK'	=> false,
					'TITLE'		=> $lang['INSTALL_CONGRATS'],
					'BODY'		=> sprintf($lang['INSTALL_CONGRATS_EXPLAIN'], $garage_config['version'], append_sid($phpbb_root_path . 'garage/install/index.' . $phpEx, 'mode=convert&amp;'), '../docs/README.html'),
				));

				$sql = 'INSERT INTO ' . GARAGE_CONFIG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
					'config_name'	=> 'installed',
				));
				$db->sql_query($sql);

				$cache->purge();

			break;

		}

	}

	/**
	* Checks that the server we are installing on meets the requirements for running phpBB Garage
	*/
	function check_server_requirements($mode, $sub)
	{
		global $lang, $template, $phpbb_root_path, $phpEx, $language;

		$this->page_title = $lang['STAGE_REQUIREMENTS'];

		$template->assign_vars(array(
			'TITLE'		=> $lang['REQUIREMENTS_TITLE'],
			'BODY'		=> $lang['REQUIREMENTS_EXPLAIN'],
		));

		$passed = array('php' => false, 'files' => false, 'imagesize' => false,);

		// Test for basic PHP settings
		$template->assign_block_vars('checks', array(
			'S_LEGEND'		=> true,
			'LEGEND'		=> $lang['PHP_SETTINGS'],
			'LEGEND_EXPLAIN'	=> $lang['PHP_SETTINGS_EXPLAIN'],
		));

		// Test the minimum PHP version
		$php_version = PHP_VERSION;

		if (version_compare($php_version, '4.3.3') < 0)
		{
			$result = '<strong style="color:red">' . $lang['NO'] . '</strong>';
		}
		else
		{
			$passed['php'] = true;

			// We also give feedback on whether we're running in safe mode
			$result = '<strong style="color:green">' . $lang['YES'];
			if (@ini_get('safe_mode') || strtolower(@ini_get('safe_mode')) == 'on')
			{
				$result .= ', ' . $lang['PHP_SAFE_MODE'];
			}
			$result .= '</strong>';
		}

		$template->assign_block_vars('checks', array(
			'TITLE'			=> $lang['PHP_VERSION_REQD'],
			'RESULT'		=> $result,

			'S_EXPLAIN'		=> false,
			'S_LEGEND'		=> false,
		));

		// Check for url_fopen 
		if (@ini_get('allow_url_fopen') == '1' || strtolower(@ini_get('allow_url_fopen')) == 'on')
		{
			$result = '<strong style="color:green">' . $lang['YES'] . '</strong>';
		}
		else
		{
			$result = '<strong style="color:red">' . $lang['NO'] . '</strong>';
		}

		$template->assign_block_vars('checks', array(
			'TITLE'			=> $lang['PHP_URL_FOPEN_SUPPORT'],
			'TITLE_EXPLAIN'		=> $lang['PHP_URL_FOPEN_SUPPORT_EXPLAIN'],
			'RESULT'		=> $result,

			'S_EXPLAIN'		=> true,
			'S_LEGEND'		=> false,
		));
		
		
		// Check for getimagesize 
		if (@function_exists('getimagesize'))
		{
			$passed['imagesize'] = true;
			$result = '<strong style="color:green">' . $lang['YES'] . '</strong>';
		}
		else
		{
			$result = '<strong style="color:red">' . $lang['NO'] . '</strong>';
		}

		$template->assign_block_vars('checks', array(
			'TITLE'			=> $lang['PHP_GETIMAGESIZE_SUPPORT'],
			'TITLE_EXPLAIN'	=> $lang['PHP_GETIMAGESIZE_SUPPORT_EXPLAIN'],
			'RESULT'		=> $result,

			'S_EXPLAIN'		=> true,
			'S_LEGEND'		=> false,
		));


		// Test for other modules
		$template->assign_block_vars('checks', array(
			'S_LEGEND'		=> true,
			'LEGEND'		=> $lang['PHP_REQUIRED_MODULE'],
			'LEGEND_EXPLAIN'	=> $lang['PHP_REQUIRED_MODULE_EXPLAIN'],
		));

		foreach ($this->php_dlls_other as $dll)
		{
			if (!@extension_loaded($dll))
			{
				if (!can_load_dll($dll))
				{
					$template->assign_block_vars('checks', array(
						'TITLE'		=> $lang['DLL_' . strtoupper($dll)],
						'RESULT'	=> '<strong style="color:red">' . $lang['UNAVAILABLE'] . '</strong>',

						'S_EXPLAIN'	=> false,
						'S_LEGEND'	=> false,
					));
					continue;
				}
			}

			$template->assign_block_vars('checks', array(
				'TITLE'		=> $lang['DLL_' . strtoupper($dll)],
				'RESULT'	=> '<strong style="color:green">' . $lang['AVAILABLE'] . '</strong>',

				'S_EXPLAIN'	=> false,
				'S_LEGEND'	=> false,
			));
		}

		// Check permissions on files/directories we need access to
		$template->assign_block_vars('checks', array(
			'S_LEGEND'		=> true,
			'LEGEND'		=> $lang['FILES_REQUIRED'],
			'LEGEND_EXPLAIN'	=> $lang['FILES_REQUIRED_EXPLAIN'],
		));

		$directories = array('garage/upload/');

		umask(0);

		$passed['files'] = true;
		foreach ($directories as $dir)
		{
			$exists = $write = false;

			// Try to create the directory if it does not exist
			if (!file_exists($phpbb_root_path . $dir))
			{
				@mkdir($phpbb_root_path . $dir, 0777);
				@chmod($phpbb_root_path . $dir, 0777);
			}

			// Now really check
			if (file_exists($phpbb_root_path . $dir) && is_dir($phpbb_root_path . $dir))
			{
				if (!@is_writable($phpbb_root_path . $dir))
				{
					@chmod($phpbb_root_path . $dir, 0777);
				}
				$exists = true;
			}

			// Now check if it is writable by storing a simple file
			$fp = @fopen($phpbb_root_path . $dir . 'test_lock', 'wb');
			if ($fp !== false)
			{
				$write = true;
			}
			@fclose($fp);

			@unlink($phpbb_root_path . $dir . 'test_lock');

			$passed['files'] = ($exists && $write && $passed['files']) ? true : false;

			$exists = ($exists) ? '<strong style="color:green">' . $lang['FOUND'] . '</strong>' : '<strong style="color:red">' . $lang['NOT_FOUND'] . '</strong>';
			$write = ($write) ? ', <strong style="color:green">' . $lang['WRITABLE'] . '</strong>' : (($exists) ? ', <strong style="color:red">' . $lang['UNWRITABLE'] . '</strong>' : '');

			$template->assign_block_vars('checks', array(
				'TITLE'		=> $dir,
				'RESULT'	=> $exists . $write,

				'S_EXPLAIN'	=> false,
				'S_LEGEND'	=> false,
			));
		}

		// And finally where do we want to go next (well today is taken isn't it :P)
		$s_hidden_fields = '';

		$url = (!in_array(false, $passed)) ? $this->p_master->module_url . "?mode=$mode&amp;sub=optional&amp;language=$language" : $this->p_master->module_url . "?mode=$mode&amp;sub=requirements&amp;language=$language	";
		$submit = (!in_array(false, $passed)) ? $lang['INSTALL_START'] : $lang['INSTALL_TEST'];


		$template->assign_vars(array(
			'L_SUBMIT'	=> $submit,
			'S_HIDDEN'	=> $s_hidden_fields,
			'U_ACTION'	=> $url,
		));
	}

	/**
	* Provide an opportunity to customise some optional settings during the install
	*/
	function obtain_optional_settings($mode, $sub)
	{
		global $lang, $template, $phpEx;

		$this->page_title = $lang['STAGE_OPTIONAL'];

		// Obtain any submitted data
		$data = $this->get_submitted_data();

		$s_hidden_fields = '<input type="hidden" name="language" value="' . $data['language'] . '" />';

		$data['insert_makes'] = ($data['insert_makes'] !== '') ? $data['insert_makes'] : true;
		$data['insert_categories'] = ($data['insert_categories'] !== '') ? $data['insert_categories'] : true;

		foreach ($this->optional_config_options as $config_key => $vars)
		{
			if (!is_array($vars) && strpos($config_key, 'legend') === false)
			{
				continue;
			}

			if (strpos($config_key, 'legend') !== false)
			{
				$template->assign_block_vars('options', array(
					'S_LEGEND'		=> true,
					'LEGEND'		=> $lang[$vars])
				);

				continue;
			}

			$options = isset($vars['options']) ? $vars['options'] : '';

			$template->assign_block_vars('options', array(
				'KEY'			=> $config_key,
				'TITLE'			=> $lang[$vars['lang']],
				'S_EXPLAIN'		=> $vars['explain'],
				'S_LEGEND'		=> false,
				'TITLE_EXPLAIN'	=> ($vars['explain']) ? $lang[$vars['lang'] . '_EXPLAIN'] : '',
				'CONTENT'		=> $this->p_master->input_field($config_key, $vars['type'], $data[$config_key], $options),
				)
			);
		}

		$submit = $lang['NEXT_STEP'];

		$url = $this->p_master->module_url . "?mode=$mode&amp;sub=create_table";

		$template->assign_vars(array(
			'BODY'		=> $lang['STAGE_OPTIONAL_EXPLAIN'],
			'L_SUBMIT'	=> $submit,
			'S_HIDDEN'	=> $s_hidden_fields,
			'U_ACTION'	=> $url,
		));
	}

	/**
	* Load the contents of the schema into the database and then alter it based on what has been input during the installation
	*/
	function load_schema($mode, $sub)
	{
		global $db, $lang, $template, $phpbb_root_path, $phpEx, $dbms, $table_prefix;

		$this->page_title = $lang['STAGE_CREATE_TABLE'];
		$s_hidden_fields = '';

		// Obtain any submitted data
		$data = $this->get_submitted_data();

		//We will just setup $db here rather than try work it in earlier - might need to rethink this though for languages
		require($phpbb_root_path . 'includes/functions_convert.' . $phpEx);

		$available_dbms = get_available_dbms($dbms);

		// If mysql is chosen, we need to adjust the schema filename slightly to reflect the correct version. ;)
		if ($dbms == 'mysql')
		{
			if (version_compare($db->mysql_version, '4.1.3', '>='))
			{
				$available_dbms[$dbms]['SCHEMA'] .= '_41';
			}
			else
			{
				$available_dbms[$dbms]['SCHEMA'] .= '_40';
			}
		}

		// Ok we have the db info go ahead and read in the relevant schema
		// and work on building the table
		$create_schema = 'schemas/' . $available_dbms[$dbms]['SCHEMA'] . '_schema.sql';

		// How should we treat this schema?
		$remove_remarks = $available_dbms[$dbms]['COMMENTS'];
		$delimiter = $available_dbms[$dbms]['DELIM'];

		$create_query = file_get_contents($create_schema);

		$create_query = preg_replace('#phpbb_#i', $table_prefix, $create_query);

		$remove_remarks($create_query);

		$create_query = split_sql_file($create_query, $delimiter);

		foreach ($create_query as $sql)
		{
			//$sql = trim(str_replace('|', ';', $sql));
			if (!$db->sql_query($sql))
			{
				$error = $db->sql_error();
				$this->p_master->db_error($error['message'], $sql, __LINE__, __FILE__);
			}
		}
		unset($create_query);

		// Ok tables have been built, let's fill in the basic information & prepare optional data too
		$sql_query = file_get_contents('schemas/schema_data.sql');
		$make_query = file_get_contents('schemas/schema_make_data.sql');
		$category_query = file_get_contents('schemas/schema_category_data.sql');

		// Deal with any special comments
		switch ($dbms)
		{
			case 'mssql':
			case 'mssql_odbc':
				$sql_query = preg_replace('#\# MSSQL IDENTITY (phpbb_[a-z_]+) (ON|OFF) \##s', 'SET IDENTITY_INSERT \1 \2;', $sql_query);
				$make_query = preg_replace('#\# MSSQL IDENTITY (phpbb_[a-z_]+) (ON|OFF) \##s', 'SET IDENTITY_INSERT \1 \2;', $make_query);
				$category_query = preg_replace('#\# MSSQL IDENTITY (phpbb_[a-z_]+) (ON|OFF) \##s', 'SET IDENTITY_INSERT \1 \2;', $category_query);
			break;

			case 'postgres':
				$sql_query = preg_replace('#\# POSTGRES (BEGIN|COMMIT) \##s', '\1; ', $sql_query);
				$make_query = preg_replace('#\# POSTGRES (BEGIN|COMMIT) \##s', '\1; ', $make_query);
				$category_query = preg_replace('#\# POSTGRES (BEGIN|COMMIT) \##s', '\1; ', $category_query);
			break;
		}

		// Change prefix
		$sql_query = preg_replace('#phpbb_#i', $table_prefix, $sql_query);
		$make_query = preg_replace('#phpbb_#i', $table_prefix, $make_query);
		$category_query = preg_replace('#phpbb_#i', $table_prefix, $category_query);

		// Since we know the comment style and are able to remove it directly with remove_remarks
		remove_remarks($sql_query);
		remove_remarks($make_query);
		remove_remarks($category_query);
		$sql_query = split_sql_file($sql_query, ';');
		$make_query = split_sql_file($make_query, ';');
		$category_query = split_sql_file($category_query, ';');

		foreach ($sql_query as $sql)
		{
			if (!$db->sql_query($sql))
			{
				$error = $db->sql_error();
				$this->p_master->db_error($error['message'], $sql, __LINE__, __FILE__);
			}
		}
		unset($sql_query);

		// Does the user want default makes
		if ($data['insert_makes'])
		{
			foreach ($make_query as $sql)
			{
				//$sql = trim(str_replace('|', ';', $sql));
				if (!$db->sql_query($sql))
				{
					$error = $db->sql_error();
					$this->p_master->db_error($error['message'], $sql, __LINE__, __FILE__);
				}
			}
			unset($make_query);
		}

		// Does the user want default categories
		if ($data['insert_categories'])
		{
			foreach ($category_query as $sql)
			{
				//$sql = trim(str_replace('|', ';', $sql));
				if (!$db->sql_query($sql))
				{
					$error = $db->sql_error();
					$this->p_master->db_error($error['message'], $sql, __LINE__, __FILE__);
				}
			}
			unset($category_query);
		}

		//Handle the installed & supported style imagesets
		$sql = 'SELECT *
			FROM ' . STYLES_IMAGESET_TABLE;
		$result = $db->sql_query($sql);
		while( $row = $db->sql_fetchrow($result) )
		{
			//Check For Imageset Data To Load
			if (file_exists($phpbb_root_path . "garage/install/install/styles/{$row['imageset_name']}/imageset/data." . $phpEx))
			{
				$imageset_info= array();
				include($phpbb_root_path . "garage/install/install/styles/{$row['imageset_name']}/imageset/data." . $phpEx);
				for ($i = 0, $count = sizeof($imageset_info);$i < $count; $i++)
				{
					$sql = 'INSERT INTO ' . STYLES_IMAGESET_DATA_TABLE . ' ' . $db->sql_build_array('INSERT', array(
						'image_name'	=> $imageset_info[$i]['image_name'],
						'image_filename'=> $imageset_info[$i]['image_filename'],
						'image_lang'	=> $imageset_info[$i]['image_lang'],
						'image_height'	=> $imageset_info[$i]['image_height'],
						'image_width'	=> $imageset_info[$i]['image_width'],
						'imageset_id'	=> $row['imageset_id'],
					));
					$db->sql_query($sql);
				}
			}

			//Check For All Installed Languages
			$sql = 'SELECT *
			FROM ' . LANG_TABLE;
			$lresult = $db->sql_query($sql);
			while( $lrow = $db->sql_fetchrow($lresult) )
			{
				//Check For Imageset Data To Load
				if (file_exists($phpbb_root_path . "garage/install/install/styles/{$row['imageset_name']}/imageset/{$lrow['lang_dir']}/data." . $phpEx))
				{
					$imageset_info= array();
					include($phpbb_root_path . "garage/install/install/styles/{$row['imageset_name']}/imageset/{$lrow['lang_dir']}/data." . $phpEx);
					for ($i = 0, $count = sizeof($imageset_info);$i < $count; $i++)
					{
						$sql = 'INSERT INTO ' . STYLES_IMAGESET_DATA_TABLE . ' ' . $db->sql_build_array('INSERT', array(
							'image_name'	=> $imageset_info[$i]['image_name'],
							'image_filename'=> $imageset_info[$i]['image_filename'],
							'image_lang'	=> $imageset_info[$i]['image_lang'],
							'image_height'	=> $imageset_info[$i]['image_height'],
							'image_width'	=> $imageset_info[$i]['image_width'],
							'imageset_id'	=> $row['imageset_id'],
						));
						$db->sql_query($sql);
					}
				}
			}
			$db->sql_freeresult($lresult);
		}
		$db->sql_freeresult($result);

		//Handle the installed & supported style themes
		$sql = 'SELECT *
			FROM ' . STYLES_THEME_TABLE;
		$result = $db->sql_query($sql);
		while( $row = $db->sql_fetchrow($result) )
		{
			//Check For Imageset Data To Load
			if (file_exists($phpbb_root_path . "garage/install/install/styles/{$row['theme_name']}/theme/data." . $phpEx))
			{
				$theme_info= array();
				include($phpbb_root_path . "garage/install/install/styles/{$row['theme_name']}/theme/data." . $phpEx);
				$theme_data =  $row['theme_data'] . $theme_info['theme_data'];

				$update_sql = array(
					'theme_data'	=> $theme_data,
				);

				$sql = 'UPDATE ' . STYLES_THEME_TABLE . '
					SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
					WHERE theme_id = {$row['theme_id']}";

				$db->sql_query($sql);
			}
		}
		$db->sql_freeresult($result);

		$schema_changes = array(
			'add_columns'	=> array(
				USERS_TABLE	=> array(
					'user_garage_index_columns' 		=> array('BOOL', 2),
					'user_garage_guestbook_email_notify' 	=> array('BOOL', 1),
					'user_garage_guestbook_pm_notify' 	=> array('BOOL', 1),
					'user_garage_mod_email_optout' 		=> array('BOOL', 0),
					'user_garage_mod_pm_optout' 		=> array('BOOL', 0),
				),
			),
		);

		require($phpbb_root_path . 'includes/db/db_tools.' . $phpEx);
		$mod_db = new phpbb_db_tools($db);

		$mod_db->perform_schema_changes($schema_changes);

		$submit = $lang['NEXT_STEP'];

		$url = $this->p_master->module_url . "?mode=$mode&amp;sub=create_permissions";

		$template->assign_vars(array(
			'BODY'		=> $lang['STAGE_CREATE_TABLE_EXPLAIN'],
			'L_SUBMIT'	=> $submit,
			'S_HIDDEN'	=> build_hidden_fields($data),
			'U_ACTION'	=> $url,
		));
	}

	/**
	* Populate the module tables
	*/
	function add_modules($mode, $sub)
	{
		global $db, $lang, $phpbb_root_path, $phpEx, $modules;

		//Lets Add Automatically The Modules
		require($phpbb_root_path . 'includes/acp/acp_modules.' . $phpEx);
		$modules = new acp_modules();
		$module_data = $errors = array();

		//Define ACP Garage Module Categories
		$module_data[] = array('module_basename' => '', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => '31', 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_SETTINGS', 'module_mode' => '', 'module_auth' => '' );
		$module_data[] = array('module_basename' => '', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => '31', 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_MANAGEMENT', 'module_mode' => '', 'module_auth' => '' );

		//Define MCP Garage Module Categories
		$module_data[] = array('module_basename' => '', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => '0', 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE', 'module_mode' => '', 'module_auth' => '' );

		//Define UCP Garage Module Categories
		$module_data[] = array('module_basename' => '', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => '0', 'module_class' => 'ucp', 'module_langname' => 'UCP_GARAGE', 'module_mode' => '', 'module_auth' => '' );

		//Create All Required Module Categories & Reset
		create_modules($module_data);
		$module_data = null;
		$module_data = array();

		$acp_settings_parent = get_module_id('acp', 'ACP_GARAGE_SETTINGS');
		$acp_management_parent = get_module_id('acp', 'ACP_GARAGE_MANAGEMENT');
		$mcp_parent = get_module_id('mcp', 'MCP_GARAGE');
		$ucp_parent = get_module_id('ucp', 'UCP_GARAGE');

		//Define ACP Settings Modules
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_GENERAL_SETTINGS', 'module_mode' => 'general', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_MENU_SETTINGS', 'module_mode' => 'menu', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_INDEX_SETTINGS', 'module_mode' => 'index', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_IMAGES_SETTINGS', 'module_mode' => 'images', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_QUARTERMILE_SETTINGS', 'module_mode' => 'quartermile', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_DYNORUN_SETTINGS', 'module_mode' => 'dynorun', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_TRACK_SETTINGS', 'module_mode' => 'track', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_INSURANCE_SETTINGS', 'module_mode' => 'insurance', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_BUSINESS_SETTINGS', 'module_mode' => 'business', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_RATING_SETTINGS', 'module_mode' => 'rating', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_GUESTBOOK_SETTINGS', 'module_mode' => 'guestbook', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_PRODUCT_SETTINGS', 'module_mode' => 'product', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_SERVICE_SETTINGS', 'module_mode' => 'service', 'module_auth' => 'acl_a_garage_setting');
		$module_data[] = array('module_basename' => 'garage_setting', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_settings_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_BLOG_SETTINGS', 'module_mode' => 'blog', 'module_auth' => 'acl_a_garage_setting');

		//Define ACP Management Modules
		$module_data[] = array('module_basename' => 'garage_update', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_management_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_VERSION_CHECK', 'module_mode' => 'version_check', 'module_auth' => 'acl_a_garage_update');
		$module_data[] = array('module_basename' => 'garage_business', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_management_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_BUSINESS', 'module_mode' => 'business', 'module_auth' => 'acl_a_garage_business');
		$module_data[] = array('module_basename' => 'garage_category', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_management_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_CATEGORIES', 'module_mode' => 'categories', 'module_auth' => 'acl_a_garage_category');
		$module_data[] = array('module_basename' => 'garage_model', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_management_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_MODELS', 'module_mode' => 'makes', 'module_auth' => 'acl_a_garage_model');
		$module_data[] = array('module_basename' => 'garage_product', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_management_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_PRODUCTS', 'module_mode' => 'products', 'module_auth' => 'acl_a_garage_product');
		$module_data[] = array('module_basename' => 'garage_quota', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_management_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_QUOTAS', 'module_mode' => 'quotas', 'module_auth' => 'acl_a_garage_quota');
		$module_data[] = array('module_basename' => 'garage_tool', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_management_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_TOOLS', 'module_mode' => 'tools', 'module_auth' => 'acl_a_garage_tool');
		$module_data[] = array('module_basename' => 'garage_track', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $acp_management_parent, 'module_class' => 'acp', 'module_langname' => 'ACP_GARAGE_TRACK', 'module_mode' => 'track', 'module_auth' => 'acl_a_garage_track');

		//Define MCP Modules
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent, 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_VEHICLES', 'module_mode' => 'unapproved_vehicles', 'module_auth' => 'acl_m_garage_approve_vehicle');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent, 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_MAKES', 'module_mode' => 'unapproved_makes', 'module_auth' => 'acl_m_garage_approve_make');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent, 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_MODELS', 'module_mode' => 'unapproved_models', 'module_auth' => 'acl_m_garage_approve_model');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent, 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_BUSINESS', 'module_mode' => 'unapproved_business', 'module_auth' => 'acl_m_garage_approve_business');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent, 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_QUARTERMILES', 'module_mode' => 'unapproved_quartermiles', 'module_auth' => 'acl_m_garage_approve_quartermile');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent, 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_DYNORUNS', 'module_mode' => 'unapproved_dynoruns', 'module_auth' => 'acl_m_garage_approve_dynorun');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent, 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_GUESTBOOK_COMMENTS', 'module_mode' => 'unapproved_guestbook_comments', 'module_auth' => 'acl_m_garage_approve_guestbook');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent, 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_LAPS', 'module_mode' => 'unapproved_laps', 'module_auth' => 'acl_m_garage_approve_lap');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent, 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_TRACKS', 'module_mode' => 'unapproved_tracks', 'module_auth' => 'acl_m_garage_approve_track');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent, 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_PRODUCTS', 'module_mode' => 'unapproved_products', 'module_auth' => 'acl_m_garage_approve_product');

		//Define UCP Modules
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $ucp_parent, 'module_class' => 'ucp', 'module_langname' => 'UCP_GARAGE_OPTIONS', 'module_mode' => 'options', 'module_auth' => '');
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $ucp_parent, 'module_class' => 'ucp', 'module_langname' => 'UCP_GARAGE_NOTIFY', 'module_mode' => 'notify', 'module_auth' => '');

		create_modules($module_data);
	}

	/**
	* Create required phpBB Garage
	*/
	function add_permissions($mode, $sub)
	{
		global $phpbb_root_path, $phpEx, $lang, $template, $data;

		//Setup $auth_admin class so we can add permission options
		include($phpbb_root_path . '/includes/acp/auth.' . $phpEx);
		$auth_admin = new auth_admin();

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
			 	'a_garage_update',
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
	}

	/**
	* Update user permissions
	*/
	function update_user_permissions($mode, $sub)
	{
		//Set Anonymous User Permissions
		acl_update_user(ANONYMOUS, array('u_garage_browse'));
	}

	/**
	* Update role permissions
	*/
	function update_role_permissions($mode, $sub)
	{
		//Standard Admin Role
		$role = get_role_by_name('ROLE_ADMIN_STANDARD');
		if ($role)
		{
			acl_update_role($role['role_id'], array('a_garage_update', 'a_garage_setting', 'a_garage_business', 'a_garage_category', 'a_garage_field', 'a_garage_model', 'a_garage_product', 'a_garage_quota', 'a_garage_tool', 'a_garage_track'));
		}

		//Full Admin Role
		$role = get_role_by_name('ROLE_ADMIN_FULL');
		if ($role)
		{
			acl_update_role($role['role_id'], array('a_garage_update', 'a_garage_setting', 'a_garage_business', 'a_garage_category', 'a_garage_field', 'a_garage_model', 'a_garage_product', 'a_garage_quota', 'a_garage_tool', 'a_garage_track'));
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
	}

	/**
	* Get submitted data
	*/
	function get_submitted_data()
	{
		return array(
			'language'		=> basename(request_var('language', '')),
			'default_lang'		=> basename(request_var('default_lang', '')),
			'insert_makes'		=> request_var('insert_makes', ''),
			'insert_categories'	=> request_var('insert_categories',''),
		);
	}

	/**
	* The information below will be used to build the input fields presented to the user
	*/
	var $optional_config_options = array(
		'legend1'		=> 'INSERT_OPTIONS',
		'insert_makes'		=> array('lang' => 'INSERT_MAKES',		'type' => 'radio:enabled_disabled', 'explain' => true),
		'insert_categories'	=> array('lang' => 'INSERT_CATEGORIES',		'type' => 'radio:enabled_disabled', 'explain' => true),
	);

	/**
	* Specific PHP modules we may require for certain optional or extended features
	*/
	var $php_dlls_other = array('gd');

	/**
	* Define the module structure so that we can populate the database without
	* needing to hard-code module_id values
	*/
	/*var $module_categories = array(
		'acp'	=> array(
			'ACP_CAT_GENERAL'		=> array(
				'ACP_QUICK_ACCESS',
				'ACP_BOARD_CONFIGURATION',
				'ACP_CLIENT_COMMUNICATION',
				'ACP_SERVER_CONFIGURATION',
			),
			'ACP_CAT_FORUMS'		=> array(
				'ACP_MANAGE_FORUMS',
				'ACP_FORUM_BASED_PERMISSIONS',
			),
			'ACP_CAT_POSTING'		=> array(
				'ACP_MESSAGES',
				'ACP_ATTACHMENTS',
			),
			'ACP_CAT_USERGROUP'		=> array(
				'ACP_CAT_USERS',
				'ACP_GROUPS',
				'ACP_USER_SECURITY',
			),
			'ACP_CAT_PERMISSIONS'	=> array(
				'ACP_GLOBAL_PERMISSIONS',
				'ACP_FORUM_BASED_PERMISSIONS',
				'ACP_PERMISSION_ROLES',
				'ACP_PERMISSION_MASKS',
			),
			'ACP_CAT_STYLES'		=> array(
				'ACP_STYLE_MANAGEMENT',
				'ACP_STYLE_COMPONENTS',
			),
			'ACP_CAT_MAINTENANCE'	=> array(
				'ACP_FORUM_LOGS',
				'ACP_CAT_DATABASE',
			),
			'ACP_CAT_SYSTEM'		=> array(
				'ACP_AUTOMATION',
				'ACP_GENERAL_TASKS',
				'ACP_MODULE_MANAGEMENT',
			),
			'ACP_CAT_DOT_MODS'		=> null,
		),
		'mcp'	=> array(
			'MCP_MAIN'		=> null,
			'MCP_QUEUE'		=> null,
			'MCP_REPORTS'		=> null,
			'MCP_NOTES'		=> null,
			'MCP_WARN'		=> null,
			'MCP_LOGS'		=> null,
			'MCP_BAN'		=> null,
		),
		'ucp'	=> array(
			'UCP_MAIN'		=> null,
			'UCP_PROFILE'		=> null,
			'UCP_PREFS'		=> null,
			'UCP_PM'		=> null,
			'UCP_USERGROUPS'	=> null,
			'UCP_ZEBRA'		=> null,
		),
	);

	var $module_extras = array(
		'acp'	=> array(
			'ACP_QUICK_ACCESS' => array(
				'ACP_MANAGE_USERS',
				'ACP_GROUPS_MANAGE',
				'ACP_MANAGE_FORUMS',
				'ACP_MOD_LOGS',
				'ACP_BOTS',
				'ACP_PHP_INFO',
			),
			'ACP_FORUM_BASED_PERMISSIONS' => array(
				'ACP_FORUM_PERMISSIONS',
				'ACP_FORUM_MODERATORS',
				'ACP_USERS_FORUM_PERMISSIONS',
				'ACP_GROUPS_FORUM_PERMISSIONS',
			),
		),
	);*/

}

?>

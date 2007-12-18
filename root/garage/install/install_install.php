<?php
/** 
*
* @package install
* @version $Id$
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
		'module_title'		=> 'INSTALL',
		'module_filename'	=> substr(basename(__FILE__), 0, -strlen($phpEx)-1),
		'module_order'		=> 10,
		'module_subs'		=> '',
		'module_stages'		=> array('INTRO', 'REQUIREMENTS', 'OPTIONAL', 'CREATE_TABLE', 'CREATE_PERMISSIONS', 'INSTALL_MODULES', 'FILE_CHECK', 'UPDATE_FILES', 'FINAL'),
		'module_reqs'		=> ''
	);
}

/**
* Installation
* @package install
*/
class install_install extends module
{
	function install_install(&$p_master)
	{
		$this->p_master = &$p_master;
	}

	function main($mode, $sub)
	{
		global $lang, $template, $language, $phpbb_root_path, $phpEx, $config, $db, $table_prefix, $db, $auth, $cache, $user;

		$this->old_location = $phpbb_root_path . 'garage/install/install/old/';
		$this->new_location = $phpbb_root_path . 'garage/install/install/new/';

		// Special options for conflicts/modified files
		define('MERGE_NO_MERGE_NEW', 1);
		define('MERGE_NO_MERGE_MOD', 2);
		define('MERGE_NEW_FILE', 3);
		define('MERGE_MOD_FILE', 4);

		$this->install_info = $this->get_file('install_info');

		// Include renderer and engine
		$this->include_file('includes/diff/diff.' . $phpEx);
		$this->include_file('includes/diff/engine.' . $phpEx);
		$this->include_file('includes/diff/renderer.' . $phpEx);

		// Make sure we stay at the file check if checking the files again
		if (!empty($_POST['check_again']))
		{
			$sub = $this->p_master->sub = 'file_check';
		}

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

				$url = $this->p_master->module_url . "?mode=$mode&amp;sub=file_check";

				$template->assign_vars(array(
					'BODY'		=> $lang['STAGE_INSTALL_MODULES_EXPLAIN'],
					'L_SUBMIT'	=> $submit,
					'U_ACTION'	=> $url,
				));
			break;

			// Last step is just a re-check of files...
			case 'final':
			case 'file_check':

				$this->tpl_name = 'garage_install_update';
				$this->page_title = 'STAGE_FILE_CHECK';

				// Now make sure our install list is correct if the admin refreshes
				$action = request_var('action', '');

				// We are directly within an update. To make sure our install list is correct we check its status.
				$install_list = (!empty($_POST['check_again'])) ? false : $cache->get('_install_list');
				$modified = ($install_list !== false) ? @filemtime($cache->cache_dir . 'data_install_list.' . $phpEx) : 0;

				// Make sure the list is up-to-date
				if ($install_list !== false)
				{
					$get_new_list = false;
					foreach ($this->install_info['files'] as $file)
					{
						if (file_exists($phpbb_root_path . $file) && filemtime($phpbb_root_path . $file) > $modified)
						{
							$get_new_list = true;
							break;
						}
					}
				}
				else
				{
					$get_new_list = true;
				}

				if ($get_new_list)
				{
					$install_list = $this->get_install_structure();
					$cache->put('_install_list', $install_list);
				}

				if ($action == 'diff')
				{
					$this->show_diff($install_list);
					return;
				}

				// Now assign the list to the template
				foreach ($install_list as $status => $filelist)
				{
					if ($status == 'no_update' || !sizeof($filelist))
					{
						continue;
					}

					$template->assign_block_vars('files', array(
						'S_STATUS'		=> true,
						'STATUS'		=> $status,
						'L_STATUS'		=> $user->lang['STATUS_' . strtoupper($status)],
						'TITLE'			=> $user->lang['FILES_' . strtoupper($status)],
						'EXPLAIN'		=> $user->lang['FILES_' . strtoupper($status) . '_EXPLAIN'],
						)
					);

					foreach ($filelist as $file_struct)
					{

						$filename = htmlspecialchars($file_struct['filename']);
						if (strrpos($filename, '/') !== false)
						{
							$dir_part = substr($filename, 0, strrpos($filename, '/') + 1);
							$file_part = substr($filename, strrpos($filename, '/') + 1);
						}
						else
						{
							$dir_part = '';
							$file_part = $filename;
						}

						$diff_url = append_sid($this->p_master->module_url, "mode=$mode&amp;sub=file_check&amp;action=diff&amp;status=$status&amp;file=" . urlencode($file_struct['filename']));

						$template->assign_block_vars('files', array(
							'STATUS'			=> $status,

							'FILENAME'		=> $filename,
							'DIR_PART'		=> $dir_part,
							'FILE_PART'		=> $file_part,
							'NUM_CONFLICTS'		=> (isset($file_struct['conflicts'])) ? $file_struct['conflicts'] : 0,

							'S_CUSTOM'		=> ($file_struct['custom']) ? true : false,
							'CUSTOM_ORIGINAL'	=> ($file_struct['custom']) ? $file_struct['original'] : '',

							'U_SHOW_DIFF'		=> $diff_url,
							'L_SHOW_DIFF'		=> ($status != 'up_to_date') ? $user->lang['SHOW_DIFF_' . strtoupper($status)] : '',

							'U_VIEW_MOD_FILE'	=> $diff_url . '&amp;op=' . MERGE_MOD_FILE,
							'U_VIEW_NEW_FILE'	=> $diff_url . '&amp;op=' . MERGE_NEW_FILE,
							'U_VIEW_NO_MERGE_MOD'	=> $diff_url . '&amp;op=' . MERGE_NO_MERGE_MOD,
							'U_VIEW_NO_MERGE_NEW'	=> $diff_url . '&amp;op=' . MERGE_NO_MERGE_NEW,
						));
					}
				}

				$all_up_to_date = true;
				foreach ($install_list as $status => $filelist)
				{
					if ($status != 'up_to_date' && $status != 'custom' && sizeof($filelist))
					{
						$all_up_to_date = false;
						break;
					}
				}

				$template->assign_vars(array(
					'S_FILE_CHECK'		=> true,
					'S_ALL_UP_TO_DATE'	=> $all_up_to_date,
					#'S_VERSION_UP_TO_DATE'	=> $up_to_date,
					'U_ACTION'		=> append_sid($this->p_master->module_url, "mode=$mode&amp;sub=file_check"),
					'U_UPDATE_ACTION'	=> append_sid($this->p_master->module_url, "mode=$mode&amp;sub=update_files"),
				));

				if (($all_up_to_date) AND ($sub == 'final')) 
				{
					// Remove the lock file
					@unlink($phpbb_root_path . 'cache/install_lock');
				}

				// Make sure we stay at the final if we checked_again and all is now up to date
				if ((!empty($_POST['check_again'])) && ($all_up_to_date))
				{
					$sub = $this->p_master->sub = 'final';
				}

				/*if ($all_up_to_date)
				{
					// Add database update to log
					//add_log('admin', 'LOG_UPDATE_PHPBB', $this->current_version, $this->latest_version);

					// Refresh prosilver css data - this may cause some unhappy users, but 
					$sql = 'SELECT *
						FROM ' . STYLES_THEME_TABLE . "
						WHERE theme_name = 'prosilver'";
					$result = $db->sql_query($sql);
					$theme = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					if ($theme)
					{
						$recache = (empty($theme['theme_data'])) ? true : false;
						$update_time = time();

						// We test for stylesheet.css because it is faster and most likely the only file changed on common themes
						if (!$recache && $theme['theme_mtime'] < @filemtime("{$phpbb_root_path}styles/" . $theme['theme_path'] . '/theme/stylesheet.css'))
						{
							$recache = true;
							$update_time = @filemtime("{$phpbb_root_path}styles/" . $theme['theme_path'] . '/theme/stylesheet.css');
						}
						else if (!$recache)
						{
							$last_change = $theme['theme_mtime'];
							$dir = @opendir("{$phpbb_root_path}styles/{$theme['theme_path']}/theme");

							if ($dir)
							{
								while (($entry = readdir($dir)) !== false)
								{
									if (substr(strrchr($entry, '.'), 1) == 'css' && $last_change < @filemtime("{$phpbb_root_path}styles/{$theme['theme_path']}/theme/{$entry}"))
									{
										$recache = true;
										break;
									}
								}
								closedir($dir);
							}
						}

						if ($recache)
						{
							include_once($phpbb_root_path . 'includes/acp/acp_styles.' . $phpEx);

							$theme['theme_data'] = acp_styles::db_theme_data($theme);
							$theme['theme_mtime'] = $update_time;

							// Save CSS contents
							$sql_ary = array(
								'theme_mtime'	=> $theme['theme_mtime'],
								'theme_data'	=> $theme['theme_data']
							);

							$sql = 'UPDATE ' . STYLES_THEME_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
								WHERE theme_id = ' . $theme['theme_id'];
							$db->sql_query($sql);

							$cache->destroy('sql', STYLES_THEME_TABLE);
						}
					}

					$db->sql_return_on_error(true);
					$db->sql_query('DELETE FROM ' . GARAGE_CONFIG_TABLE . " WHERE config_name = 'version_update_from'");
					$db->sql_return_on_error(false);

					$cache->purge();
				}*/

			break;

			case 'update_files':

				$this->tpl_name = 'garage_install_update';
				$this->page_title = 'STAGE_UPDATE_FILES';

				$s_hidden_fields = '';
				$conflicts = request_var('conflict', array('' => 0));
				$modified = request_var('modified', array('' => 0));

				foreach ($conflicts as $filename => $merge_option)
				{
					$s_hidden_fields .= '<input type="hidden" name="conflict[' . htmlspecialchars($filename) . ']" value="' . $merge_option . '" />';
				}

				foreach ($modified as $filename => $merge_option)
				{
					if (!$merge_option)
					{
						continue;
					}
					$s_hidden_fields .= '<input type="hidden" name="modified[' . htmlspecialchars($filename) . ']" value="' . $merge_option . '" />';
				}

				$no_update = request_var('no_update', array(0 => ''));

				foreach ($no_update as $index => $filename)
				{
					$s_hidden_fields .= '<input type="hidden" name="no_update[]" value="' . htmlspecialchars($filename) . '" />';
				}

				if (!empty($_POST['download']))
				{
					$this->include_file('includes/functions_compress.' . $phpEx);

					$use_method = request_var('use_method', '');
					$methods = array('.tar');

					$available_methods = array('.tar.gz' => 'zlib', '.tar.bz2' => 'bz2', '.zip' => 'zlib');
					foreach ($available_methods as $type => $module)
					{
						if (!@extension_loaded($module))
						{
							continue;
						}
		
						$methods[] = $type;
					}

					// Let the user decide in which format he wants to have the pack
					if (!$use_method)
					{
						$this->page_title = 'SELECT_DOWNLOAD_FORMAT';

						$radio_buttons = '';
						foreach ($methods as $method)
						{
							$radio_buttons .= '<label><input type="radio"' . ((!$radio_buttons) ? ' id="use_method"' : '') . ' class="radio" value="' . $method . '" name="use_method" /> ' . $method . '</label>';
						}

						$template->assign_vars(array(
							'S_DOWNLOAD_FILES'		=> true,
							'U_ACTION'				=> append_sid($this->p_master->module_url, "mode=$mode&amp;sub=update_files"),
							'RADIO_BUTTONS'			=> $radio_buttons,
							'S_HIDDEN_FIELDS'		=> $s_hidden_fields)
						);

						// To ease the update process create a file location map
						$install_list = $cache->get('_install_list');
						$script_path = ($config['force_server_vars']) ? (($config['script_path'] == '/') ? '/' : $config['script_path'] . '/') : $user->page['root_script_path'];

						foreach ($install_list as $status => $files)
						{
							if ($status == 'up_to_date' || $status == 'no_update')
							{
								continue;
							}

							foreach ($files as $file_struct)
							{
								if (in_array($file_struct['filename'], $no_update))
								{
									continue;
								}

								$template->assign_block_vars('location', array(
									'SOURCE'		=> htmlspecialchars($file_struct['filename']),
									'DESTINATION'	=> $script_path . htmlspecialchars($file_struct['filename']),
								));
							}
						}

						return;
					}

					if (!in_array($use_method, $methods))
					{
						$use_method = '.tar';
					}

					$update_mode = 'download';
				}
				else
				{
					$this->include_file('includes/functions_transfer.' . $phpEx);

					// Choose FTP, if not available use fsock...
					$method = request_var('method', '');
					$submit = (isset($_POST['submit'])) ? true : false;
					$test_ftp_connection = request_var('test_connection', '');

					if (!$method)
					{
						$method = 'ftp';
						$methods = transfer::methods();

						if (!in_array('ftp', $methods))
						{
							$method = $methods[0];
						}
					}

					$test_connection = false;
					if ($test_ftp_connection || $submit)
					{
						$transfer = new $method(request_var('host', ''), request_var('username', ''), request_var('password', ''), request_var('root_path', ''), request_var('port', ''), request_var('timeout', ''));
						$test_connection = $transfer->open_session();

						// Make sure that the directory is correct by checking for the existence of common.php
						if ($test_connection === true)
						{
							// Check for common.php file
							if (!$transfer->file_exists($phpbb_root_path, 'common.' . $phpEx))
							{
								$test_connection = 'ERR_WRONG_PATH_TO_PHPBB';
							}
						}

						$transfer->close_session();

						// Make sure the login details are correct before continuing
						if ($submit && $test_connection !== true)
						{
							$submit = false;
							$test_ftp_connection = true;
						}
					}

					if (!$submit)
					{
						$this->page_title = 'SELECT_FTP_SETTINGS';

						if (!class_exists($method))
						{
							trigger_error('Method does not exist.', E_USER_ERROR);
						}

						$requested_data = call_user_func(array($method, 'data'));
						foreach ($requested_data as $data => $default)
						{
							$template->assign_block_vars('data', array(
								'DATA'		=> $data,
								'NAME'		=> $user->lang[strtoupper($method . '_' . $data)],
								'EXPLAIN'	=> $user->lang[strtoupper($method . '_' . $data) . '_EXPLAIN'],
								'DEFAULT'	=> (!empty($_REQUEST[$data])) ? request_var($data, '') : $default
							));
						}

						$s_hidden_fields .= build_hidden_fields(array('method' => $method));

						$template->assign_vars(array(
							'S_CONNECTION_SUCCESS'		=> ($test_ftp_connection && $test_connection === true) ? true : false,
							'S_CONNECTION_FAILED'		=> ($test_ftp_connection && $test_connection !== true) ? true : false,
							'ERROR_MSG'					=> ($test_ftp_connection && $test_connection !== true) ? $user->lang[$test_connection] : '',

							'S_FTP_UPLOAD'		=> true,
							'UPLOAD_METHOD'		=> $method,
							'U_ACTION'			=> append_sid($this->p_master->module_url, "mode=$mode&amp;sub=update_files"),
							'S_HIDDEN_FIELDS'	=> $s_hidden_fields)
						);

						return;
					}

					$update_mode = 'upload';
				}

				// Now update the installation or download the archive...
				$download_filename = 'install_phpbbgarage_' . $this->install_info['version']['install'];
				$archive_filename = $download_filename . '_' . time() . '_' . unique_id();

				$install_list = $cache->get('_install_list');
				$conflicts = request_var('conflict', array('' => 0));
				$modified = request_var('modified', array('' => 0));

				if ($install_list === false)
				{
					trigger_error($user->lang['NO_UPDATE_INFO'], E_USER_ERROR);
				}

				// Check if the conflicts data is valid
				if (sizeof($conflicts))
				{
					$conflict_filenames = array();
					foreach ($install_list['conflict'] as $files)
					{
						$conflict_filenames[] = $files['filename'];
					}

					$new_conflicts = array();
					foreach ($conflicts as $filename => $diff_method)
					{
						if (in_array($filename, $conflict_filenames))
						{
							$new_conflicts[$filename] = $diff_method;
						}
					}

					$conflicts = $new_conflicts;
				}

				// Build list for modifications
				if (sizeof($modified))
				{
					$modified_filenames = array();
					foreach ($install_list['modified'] as $files)
					{
						$modified_filenames[] = $files['filename'];
					}

					$new_modified = array();
					foreach ($modified as $filename => $diff_method)
					{
						if (in_array($filename, $modified_filenames))
						{
							$new_modified[$filename] = $diff_method;
						}
					}

					$modified = $new_modified;
				}

				// Check number of conflicting files, they need to be equal. For modified files the number can differ
				if (sizeof($install_list['conflict']) != sizeof($conflicts))
				{
					trigger_error($user->lang['MERGE_SELECT_ERROR'], E_USER_ERROR);
				}

				// Now init the connection
				if ($update_mode == 'download')
				{
					if ($use_method == '.zip')
					{
						$compress = new compress_zip('w', $phpbb_root_path . 'store/' . $archive_filename . $use_method);
					}
					else
					{
						$compress = new compress_tar('w', $phpbb_root_path . 'store/' . $archive_filename . $use_method, $use_method);
					}
				}
				else
				{
					$transfer = new $method(request_var('host', ''), request_var('username', ''), request_var('password', ''), request_var('root_path', ''), request_var('port', ''), request_var('timeout', ''));
					$transfer->open_session();
				}

				// Ok, go through the update list and do the operations based on their status
				foreach ($install_list as $status => $files)
				{
					foreach ($files as $file_struct)
					{
						// Skip this file if the user selected to not update it
						if (in_array($file_struct['filename'], $no_update))
						{
							continue;
						}

						$original_filename = ($file_struct['custom']) ? $file_struct['original'] : $file_struct['filename'];

						switch ($status)
						{
							case 'new':
							case 'new_conflict':
							case 'not_modified':
								if ($update_mode == 'download')
								{
									$compress->add_custom_file($this->new_location . $original_filename, $file_struct['filename']);
								}
								else
								{
									if ($status != 'new')
									{
										$transfer->rename($file_struct['filename'], $file_struct['filename'] . '.bak');
									}
									$transfer->copy_file($this->new_location . $original_filename, $file_struct['filename']);
								}
							break;

							case 'modified':

								$option = (isset($modified[$file_struct['filename']])) ? $modified[$file_struct['filename']] : 0;

								switch ($option)
								{
									case MERGE_NO_MERGE_NEW:
										$contents = file_get_contents($this->new_location . $original_filename);
									break;

									case MERGE_NO_MERGE_MOD:
										$contents = file_get_contents($phpbb_root_path . $file_struct['filename']);
									break;

									default:
										$diff = $this->return_diff($this->old_location . $original_filename, $phpbb_root_path . $file_struct['filename'], $this->new_location . $original_filename);

										$contents = implode("\n", $diff->merged_output());
										unset($diff);
									break;
								}

								if ($update_mode == 'download')
								{
									$compress->add_data($contents, $file_struct['filename']);
								}
								else
								{
									// @todo add option to specify if a backup file should be created?
									$transfer->rename($file_struct['filename'], $file_struct['filename'] . '.bak');
									$transfer->write_file($file_struct['filename'], $contents);
								}
							break;

							case 'conflict':

								$option = $conflicts[$file_struct['filename']];
								$contents = '';

								switch ($option)
								{
									case MERGE_NO_MERGE_NEW:
										$contents = file_get_contents($this->new_location . $original_filename);
									break;

									case MERGE_NO_MERGE_MOD:
										$contents = file_get_contents($phpbb_root_path . $file_struct['filename']);
									break;

									default:

										$diff = $this->return_diff($this->old_location . $original_filename, $phpbb_root_path . $file_struct['filename'], $this->new_location . $original_filename);

										if ($option == MERGE_NEW_FILE)
										{
											$contents = implode("\n", $diff->merged_new_output());
										}
										else if ($option == MERGE_MOD_FILE)
										{
											$contents = implode("\n", $diff->merged_orig_output());
										}
										else
										{
											unset($diff);
											break 2;
										}

										unset($diff);
									break;
								}

								if ($update_mode == 'download')
								{
									$compress->add_data($contents, $file_struct['filename']);
								}
								else
								{
									$transfer->rename($file_struct['filename'], $file_struct['filename'] . '.bak');
									$transfer->write_file($file_struct['filename'], $contents);
								}
							break;
						}
					}
				}

				if ($update_mode == 'download')
				{
					$compress->close();

					$compress->download($archive_filename, $download_filename);
					@unlink($phpbb_root_path . 'store/' . $archive_filename . $use_method);

					exit;
				}
				else
				{
					$transfer->close_session();

					$template->assign_vars(array(
						'S_UPLOAD_SUCCESS'	=> true,
						'U_ACTION'		=> append_sid($this->p_master->module_url, "mode=$mode&amp;sub=final"))
					);
					return;
				}

			break;
		}

		switch ($sub)
		{
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
					'BODY'		=> sprintf($lang['INSTALL_CONGRATS_EXPLAIN'], $garage_config['version'], append_sid($phpbb_root_path . 'install/index.' . $phpEx, 'mode=convert&amp;'), '../docs/README.html'),
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
		$module_data[] = array('module_basename' => 'garage', 'module_enabled' => '1', 'module_display' => '1', 'parent_id' => $mcp_parent, 'module_class' => 'mcp', 'module_langname' => 'MCP_GARAGE_UNAPPROVED_GUESTBOOK_COMMENTS', 'module_mode' => 'unapproved_guestbook_comments', 'module_auth' => 'acl_m_garage_approve_gustbook');
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


	/**
	* Show file diff
	*/
	function show_diff(&$install_list)
	{
		global $phpbb_root_path, $template, $user;

		$this->tpl_name = 'install_update_diff';

		// Got the diff template itself updated? If so, we are able to directly use it
		if (in_array('adm/style/install_update_diff.html', $this->install_info['files']))
		{
			$this->tpl_name = '../../install/update/new/adm/style/install_update_diff';
		}

		$this->page_title = 'VIEWING_FILE_DIFF';

		$status = request_var('status', '');
		$file = request_var('file', '');
		$diff_mode = request_var('diff_mode', 'side_by_side');

		// First of all make sure the file is within our file update list with the correct status
		$found_entry = array();
		foreach ($install_list[$status] as $index => $file_struct)
		{
			if ($file_struct['filename'] === $file)
			{
				$found_entry = $install_list[$status][$index];
			}
		}

		if (empty($found_entry))
		{
			trigger_error($user->lang['FILE_DIFF_NOT_ALLOWED'], E_USER_ERROR);
		}

		// If the status is 'up_to_date' then we do not need to show a diff
		if ($status == 'up_to_date')
		{
			trigger_error($user->lang['FILE_ALREADY_UP_TO_DATE'], E_USER_ERROR);
		}

		$original_file = ($found_entry['custom']) ? $found_entry['original'] : $file;

		// Get the correct diff
		switch ($status)
		{
			case 'conflict':
				$option = request_var('op', 0);

				switch ($option)
				{
					case MERGE_NO_MERGE_NEW:
					case MERGE_NO_MERGE_MOD:

						$diff = $this->return_diff(array(), ($option == MERGE_NO_MERGE_NEW) ? $this->new_location . $original_file : $phpbb_root_path . $file);

						$template->assign_var('S_DIFF_NEW_FILE', true);
						$diff_mode = 'inline';
						$this->page_title = 'VIEWING_FILE_CONTENTS';

					break;

					case MERGE_NEW_FILE:
					case MERGE_MOD_FILE:

						$diff = $this->return_diff($this->old_location . $original_file, $phpbb_root_path . $file, $this->new_location . $original_file);

						$tmp = array(
							'file1'		=> array(),
							'file2'		=> ($option == MERGE_NEW_FILE) ? implode("\n", $diff->merged_new_output()) : implode("\n", $diff->merged_orig_output()),
						);

						$diff = &new diff($tmp['file1'], $tmp['file2']);

						unset($tmp);

						$template->assign_var('S_DIFF_NEW_FILE', true);
						$diff_mode = 'inline';
						$this->page_title = 'VIEWING_FILE_CONTENTS';

					break;

					default:

						$diff = $this->return_diff($this->old_location . $original_file, $phpbb_root_path . $file, $this->new_location . $original_file);

						$template->assign_vars(array(
							'S_DIFF_CONFLICT_FILE'	=> true,
							'NUM_CONFLICTS'			=> $diff->merged_output(false, false, false, true))
						);

						$diff = $this->return_diff($phpbb_root_path . $file, $diff->merged_output());
					break;
				}

			break;

			case 'modified':
				$option = request_var('op', 0);

				switch ($option)
				{
					case MERGE_NO_MERGE_NEW:
					case MERGE_NO_MERGE_MOD:

						$diff = $this->return_diff(array(), ($option == MERGE_NO_MERGE_NEW) ? $this->new_location . $original_file : $phpbb_root_path . $file);

						$template->assign_var('S_DIFF_NEW_FILE', true);
						$diff_mode = 'inline';
						$this->page_title = 'VIEWING_FILE_CONTENTS';

					break;

					default:
						$diff = $this->return_diff($this->old_location . $original_file, $phpbb_root_path . $original_file, $this->new_location . $file);
					break;
				}
			break;

			case 'not_modified':
			case 'new_conflict':
				$diff = $this->return_diff($phpbb_root_path . $file, $this->new_location . $original_file);
			break;

			case 'new':

				$diff = $this->return_diff(array(), $this->new_location . $original_file);

				$template->assign_var('S_DIFF_NEW_FILE', true);
				$diff_mode = 'inline';
				$this->page_title = 'VIEWING_FILE_CONTENTS';

			break;
		}

		$diff_mode_options = '';
		foreach (array('side_by_side', 'inline', 'unified', 'raw') as $option)
		{
			$diff_mode_options .= '<option value="' . $option . '"' . (($diff_mode == $option) ? ' selected="selected"' : '') . '>' . $user->lang['DIFF_' . strtoupper($option)] . '</option>';
		}

		// Now the correct renderer
		$render_class = 'diff_renderer_' . $diff_mode;

		if (!class_exists($render_class))
		{
			trigger_error('Chosen diff mode is not supported', E_USER_ERROR);
		}

		$renderer = &new $render_class();

		$template->assign_vars(array(
			'DIFF_CONTENT'			=> $renderer->get_diff_content($diff),
			'DIFF_MODE'			=> $diff_mode,
			'S_DIFF_MODE_OPTIONS'	=> $diff_mode_options,
			'S_SHOW_DIFF'			=> true,
		));

		unset($diff, $renderer);
	}

	/**
	* Collect all file status infos we need for the install by diffing all files
	*/
	function get_install_structure()
	{
		global $phpbb_root_path, $phpEx, $user;

		$install_list = array(
			'up_to_date'	=> array(),
			'new'		=> array(),
			'not_modified'	=> array(),
			'modified'	=> array(),
			'new_conflict'	=> array(),
			'conflict'	=> array(),
			'no_update'	=> array(),
		);

		// not modified?
		foreach ($this->install_info['files'] as $index => $file)
		{
			$this->make_install_diff($install_list, $file, $file);
		}

		return $install_list;
	}

	/**
	* Compare files for storage in install_list
	*/
	function make_install_diff(&$install_list, $original_file, $file, $custom = false)
	{
		global $phpbb_root_path, $user;

		$update_ary = array('filename' => $file, 'custom' => $custom);

		if ($custom)
		{
			$update_ary['original'] = $original_file;
		}

		// On a successfull update the new location file exists but the old one does not exist.
		// Check for this circumstance, the new file need to be up-to-date with the current file then...
		if (!file_exists($this->old_location . $original_file) && file_exists($this->new_location . $original_file) && file_exists($phpbb_root_path . $file))
		{
			$tmp = array(
				'file1'		=> file_get_contents($this->new_location . $original_file),
				'file2'		=> file_get_contents($phpbb_root_path . $file),
			);

			// We need to diff the contents here to make sure the file is really the one we expect
			$diff = &new diff($tmp['file1'], $tmp['file2'], false);
			$empty = $diff->is_empty();

			unset($tmp, $diff);

			// if there are no differences we have an up-to-date file...
			if ($empty)
			{
				$install_list['up_to_date'][] = $update_ary;
				return;
			}

			// If no other status matches we have another file in the way...
			$install_list['new_conflict'][] = $update_ary;
			return;
		}

		// Check for existance, else abort immediately
		if (!file_exists($this->old_location . $original_file) || !file_exists($this->new_location . $original_file))
		{
			trigger_error($user->lang['INCOMPLETE_UPDATE_FILES'], E_USER_ERROR);
		}

		$tmp = array(
			'file1'		=> file_get_contents($this->old_location . $original_file),
			'file2'		=> file_get_contents($phpbb_root_path . $file),
		);

		// We need to diff the contents here to make sure the file is really the one we expect
		$diff = &new diff($tmp['file1'], $tmp['file2'], false);
		$empty_1 = $diff->is_empty();

		unset($tmp, $diff);

		$tmp = array(
			'file1'		=> file_get_contents($this->new_location . $original_file),
			'file2'		=> file_get_contents($phpbb_root_path . $file),
		);

		// We need to diff the contents here to make sure the file is really the one we expect
		$diff = &new diff($tmp['file1'], $tmp['file2'], false);
		$empty_2 = $diff->is_empty();

		unset($tmp, $diff);

		// If the file is not modified we are finished here...
		if ($empty_1)
		{
			// Further check if it is already up to date - it could happen that non-modified files
			// slip through
			if ($empty_2)
			{
				$install_list['up_to_date'][] = $update_ary;
				return;
			}

			$install_list['not_modified'][] = $update_ary;
			return;
		}

		// If the file had been modified then we need to check if it is already up to date

		// if there are no differences we have an up-to-date file...
		if ($empty_2)
		{
			$install_list['up_to_date'][] = $update_ary;
			return;
		}

		// if the file is modified we try to make sure a merge succeed
		$tmp = array(
			'file1'		=> file_get_contents($this->old_location . $original_file),
			'file2'		=> file_get_contents($phpbb_root_path . $file),
			'file3'		=> file_get_contents($this->new_location . $original_file),
		);

		$diff = &new diff3($tmp['file1'], $tmp['file2'], $tmp['file3'], false);

		unset($tmp);

		if ($diff->merged_output(false, false, false, true))
		{
			$update_ary['conflicts'] = $diff->_conflicting_blocks;

			// There is one special case... users having merged with a conflicting file... we need to check this
			$tmp = array(
				'file1'		=> file_get_contents($phpbb_root_path . $file),
				'file2'		=> implode("\n", $diff->merged_orig_output()),
			);

			$diff = &new diff($tmp['file1'], $tmp['file2'], false);
			$empty = $diff->is_empty();

			if ($empty)
			{
				unset($update_ary['conflicts']);
				unset($diff);
				$install_list['up_to_date'][] = $update_ary;
				return;
			}

			$install_list['conflict'][] = $update_ary;
			unset($diff);

			return;
		}

		$tmp = array(
			'file1'		=> file_get_contents($phpbb_root_path . $file),
			'file2'		=> implode("\n", $diff->merged_output()),
		);

		// now compare the merged output with the original file to see if the modified file is up to date
		$diff = &new diff($tmp['file1'], $tmp['file2'], false);
		$empty = $diff->is_empty();

		if ($empty)
		{
			unset($diff);

			$install_list['up_to_date'][] = $update_ary;
			return;
		}

		// If no other status matches we have a modified file...
		$install_list['modified'][] = $update_ary;
	}

	/**
	* Get remote file
	*/
	function get_file($mode)
	{
		global $user, $db;

		$errstr = '';
		$errno = 0;

		switch ($mode)
		{
			case 'version_info':
				global $phpbb_root_path, $phpEx;
				$info = get_remote_file('www.phpbbgarage.com', '/updatecheck', '20x.txt', $errstr, $errno);

				if ($info !== false)
				{
					$info = explode("\n", $info);
					$info = trim($info[0]);
				}

				if ($this->test_update !== false)
				{
					$info = $this->test_update;
				}

				// If info is false the fsockopen function may not be working. Instead get the latest version from our update file (and pray it is up-to-date)
				if ($info === false)
				{
					$update_info = array();
					include($phpbb_root_path . 'garage/install/update/index.php');
					$info = (empty($update_info) || !is_array($update_info)) ? false : $update_info;

					if ($info !== false)
					{
						$info = (!empty($info['version']['to'])) ? trim($info['version']['to']) : false;
					}
				}
			break;

			case 'install_info':
				global $phpbb_root_path, $phpEx;

				$install_info = array();
				include($phpbb_root_path . 'garage/install/install/index.php');

				//Handle the installed & supported style themes
				$sql = 'SELECT *
					FROM ' . STYLES_THEME_TABLE;
				$result = $db->sql_query($sql);
				while( $row = $db->sql_fetchrow($result) )
				{
					//Check For Imageset Data To Load
					if (file_exists($phpbb_root_path . "garage/install/install/styles/{$row['theme_path']}/theme/index." . $phpEx))
					{
						$theme_info= array();
						include($phpbb_root_path . "garage/install/install/styles/{$row['theme_path']}/theme/index." . $phpEx);
						$install_info['files'] = array_merge($install_info['files'], $theme_info['files']);
					}
				}
				$db->sql_freeresult($result);

				//Handle the installed & supported style template
				$sql = 'SELECT *
					FROM ' . STYLES_TEMPLATE_TABLE;
				$result = $db->sql_query($sql);
				while( $row = $db->sql_fetchrow($result) )
				{
					//Check For Imageset Data To Load
					if (file_exists($phpbb_root_path . "garage/install/install/styles/{$row['template_path']}/template/index." . $phpEx))
					{
						$template_info= array();
						include($phpbb_root_path . "garage/install/install/styles/{$row['template_path']}/template/index." . $phpEx);
						$install_info['files'] = array_merge($install_info['files'], $template_info['files']);
					}
				}
				$db->sql_freeresult($result);

				//Handle the installed & supported style imagesets
				$sql = 'SELECT *
					FROM ' . STYLES_IMAGESET_TABLE;
				$result = $db->sql_query($sql);
				while( $row = $db->sql_fetchrow($result) )
				{
					//Check For Imageset Data To Load
					if (file_exists($phpbb_root_path . "garage/install/install/styles/{$row['imageset_name']}/imageset/index." . $phpEx))
					{
						$imageset_info= array();
						include($phpbb_root_path . "garage/install/install/styles/{$row['imageset_name']}/imageset/index." . $phpEx);
						$install_info['files'] = array_merge($install_info['files'], $imageset_info['files']);
					}

					//Check For All Installed Languages
					$sql = 'SELECT *
					FROM ' . LANG_TABLE;
					$lresult = $db->sql_query($sql);
					while( $lrow = $db->sql_fetchrow($lresult) )
					{
						//Check For Imageset Data To Load
						if (file_exists($phpbb_root_path . "garage/install/install/styles/{$row['imageset_name']}/imageset/{$lrow['lang_dir']}/index." . $phpEx))
						{
							$imageset_info= array();
							include($phpbb_root_path . "garage/install/install/styles/{$row['imageset_name']}/imageset/{$lrow['lang_dir']}/index." . $phpEx);
							$install_info['files'] = array_merge($install_info['files'], $imageset_info['files']);
						}
					}
					$db->sql_freeresult($lresult);
				}
				$db->sql_freeresult($result);

				$info = (empty($install_info) || !is_array($install_info)) ? false : $install_info;
				$errstr = ($info === false) ? $user->lang['WRONG_INFO_FILE_FORMAT'] : '';

				if ($info !== false)
				{
					// Adjust the update info file to hold some specific style-related information
					$info['custom'] = array();
/*
					// Get custom installed styles...
					$sql = 'SELECT template_name, template_path
						FROM ' . STYLES_TEMPLATE_TABLE . "
						WHERE LOWER(template_name) NOT IN ('subsilver2', 'prosilver')";
					$result = $db->sql_query($sql);

					$templates = array();
					while ($row = $db->sql_fetchrow($result))
					{
						$templates[] = $row;
					}
					$db->sql_freeresult($result);

					if (sizeof($templates))
					{
						foreach ($info['files'] as $filename)
						{
							// Template update?
							if (strpos(strtolower($filename), 'styles/prosilver/template/') === 0)
							{
								foreach ($templates as $row)
								{
									$info['custom'][$filename][] = str_replace('/prosilver/', '/' . $row['template_path'] . '/', $filename);
								}
							}
						}
					}
*/
				}
			break;

			default:
				trigger_error('Mode for getting remote file not specified', E_USER_ERROR);
			break;
		}

		if ($info === false)
		{
			trigger_error($errstr, E_USER_ERROR);
		}

		return $info;
	}

	/**
	* Function for including files...
	*/
	function include_file($filename)
	{
		global $phpbb_root_path, $phpEx;

		if (!empty($this->install_info['files']) && in_array($filename, $this->install_info['files']))
		{
			include_once($this->new_location . $filename);
		}
		else
		{
			include_once($phpbb_root_path . $filename);
		}
	}

	/**
	* Wrapper for returning a diff object
	*/
	function &return_diff()
	{
		$args = func_get_args();
		$three_way_diff = (func_num_args() > 2) ? true : false;

		$file1 = array_shift($args);
		$file2 = array_shift($args);

		$tmp['file1'] = (!empty($file1) && is_string($file1)) ? file_get_contents($file1) : $file1;
		$tmp['file2'] = (!empty($file2) && is_string($file2)) ? file_get_contents($file2) : $file2;

		if ($three_way_diff)
		{
			$file3 = array_shift($args);
			$tmp['file3'] = (!empty($file3) && is_string($file3)) ? file_get_contents($file3) : $file3;

			$diff = &new diff3($tmp['file1'], $tmp['file2'], $tmp['file3']);
		}
		else
		{
			$diff = &new diff($tmp['file1'], $tmp['file2']);
		}

		unset($tmp);

		return $diff;
	}
}

?>

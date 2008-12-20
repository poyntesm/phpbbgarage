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
	$module[] = array(
		'module_type'		=> 'install',
		'module_title'		=> 'REMOVE',
		'module_filename'	=> substr(basename(__FILE__), 0, -strlen($phpEx)-1),
		'module_order'		=> 20,
		'module_subs'		=> '',
		'module_stages'		=> array('INTRO', 'DATA', 'FILE_CHECK', 'UPDATE_FILES', 'FILES', 'FINAL'),
		'module_reqs'		=> ''
	);
}

/**
* Remove class for un-installs
* @package install
*/
class install_remove extends module
{
	var $remove_info;
	
	var $old_location;
	var $new_location;
	var $latest_version;
	var $current_version;
	var $unequal_version;

	function install_remove(&$p_master)
	{
		$this->p_master = &$p_master;
	}

	function main($mode, $sub)
	{
		global $lang, $template, $phpbb_root_path, $phpEx, $cache, $config, $language, $table_prefix, $db, $user;

		$this->tpl_name = 'garage_install_remove';
		$this->mode = $mode;
		$this->unequal_version = false;

		$this->old_location = $phpbb_root_path . 'garage/install/remove/old/';
		$this->new_location = $phpbb_root_path . 'garage/install/remove/new/';

		// Special options for conflicts/modified files
		define('MERGE_NO_MERGE_NEW', 1);
		define('MERGE_NO_MERGE_MOD', 2);
		define('MERGE_NEW_FILE', 3);
		define('MERGE_MOD_FILE', 4);

		$this->remove_info = $this->get_file('remove_info');

		// Check for a valid remove directory, else point the user to the phpbbgarage.com website
		if (!file_exists($phpbb_root_path . 'garage/install/remove') || !file_exists($phpbb_root_path . 'garage/install/remove/index.' . $phpEx) || !file_exists($this->old_location) || !file_exists($this->new_location))
		{
			$template->assign_vars(array(
				'S_ERROR'		=> true,
				'ERROR_MSG'		=> ($up_to_date) ? $user->lang['NO_UPDATE_FILES_UP_TO_DATE'] : sprintf($user->lang['NO_UPDATE_FILES_OUTDATED'], $garage_config['version'], $this->current_version, $this->latest_version))
			);

			return;
		}

		// Check if the update files stored are for the latest version...
		if ($this->current_version != $this->remove_info['version']['remove'])
		{
			$this->unequal_version = true;

			$template->assign_vars(array(
				'S_WARNING'	=> true,
				'WARNING_MSG'	=> sprintf($user->lang['INCOMPATIBLE_REMOVE_FILES'], $this->current_version, $this->remove_info['version']['remove']),
			));
		}

		// Include renderer and engine
		$this->include_file('includes/diff/diff.' . $phpEx);
		$this->include_file('includes/diff/engine.' . $phpEx);
		$this->include_file('includes/diff/renderer.' . $phpEx);

		// Make sure we stay at the file check if checking the files again
		if (!empty($_POST['check_again']))
		{
			$sub = $this->p_master->sub = 'file_check';
		}

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

			case 'file_check':
				
				$this->tpl_name = 'garage_install_update';

				// Make sure the previous file collection is no longer valid...
				$cache->destroy('_diff_files');

				$this->page_title = 'STAGE_FILE_CHECK';

				// Now make sure our update list is correct if the admin refreshes
				$action = request_var('action', '');

				// We are directly within an update. To make sure our update list is correct we check its status.
				$update_list = (!empty($_POST['check_again'])) ? false : $cache->get('_update_list');
				$modified = ($update_list !== false) ? @filemtime($cache->cache_dir . 'data_update_list.' . $phpEx) : 0;

				// Make sure the list is up-to-date
				if ($update_list !== false)
				{
					$get_new_list = false;
					foreach ($this->remove_info['files'] as $file)
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

				if (!$get_new_list && $update_list['status'] != -1)
				{
					$get_new_list = true;
				}

				if ($get_new_list)
				{
					$this->get_update_structure($update_list);
					$cache->put('_update_list', $update_list);

					// Refresh the page if we are still not finished...
					if ($update_list['status'] != -1)
					{
						$refresh_url = append_sid($this->p_master->module_url, "mode=$mode&amp;sub=file_check");
						meta_refresh(2, $refresh_url);

						$template->assign_vars(array(
							'S_IN_PROGRESS'		=> true,
							'S_COLLECTED'		=> (int) $update_list['status'],
							'S_TO_COLLECT'		=> sizeof($this->remove_info['files']),
							'L_IN_PROGRESS'		=> $user->lang['COLLECTING_FILE_DIFFS'],
							'L_IN_PROGRESS_EXPLAIN'	=> sprintf($user->lang['NUMBER_OF_FILES_COLLECTED'], (int) $update_list['status'], sizeof($this->remove_info['files'])),
						));

						return;
					}
				}

				if ($action == 'diff')
				{
					$this->show_diff($update_list);
					return;
				}

				if (sizeof($update_list['no_update']))
				{
					$template->assign_vars(array(
						'S_NO_UPDATE_FILES'		=> true,
						'NO_UPDATE_FILES'		=> implode(', ', array_map('htmlspecialchars', $update_list['no_update'])))
					);
				}

				// Now assign the list to the template
				foreach ($update_list as $status => $filelist)
				{
					if ($status == 'no_update' || !sizeof($filelist) || $status == 'status')
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

						#echo "$filename <br/>";
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
							'STATUS'		=> $status,

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
				foreach ($update_list as $status => $filelist)
				{
					if ($status != 'up_to_date' && $status != 'custom' && $status != 'status' && sizeof($filelist))
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
					'U_DB_UPDATE_ACTION'	=> append_sid($this->p_master->module_url, "mode=$mode&amp;sub=update_db"),
				));

				// Make sure we stay at the final if we checked_again and all is now up to date
				if ((!empty($_POST['check_again'])) && ($all_up_to_date))
				{
					$redirect_url = append_sid($this->p_master->module_url, "mode=$mode&amp;sub=files");
					meta_refresh(3, $redirect_url);
				}

				if ($all_up_to_date)
				{
					// Add database update to log
					add_log('admin', 'LOG_UPDATE_PHPBB', $this->current_version, $this->latest_version);

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
				}

			break;

			case 'update_files':

				$this->tpl_name = 'garage_install_update';

				$this->page_title = 'STAGE_UPDATE_FILES';

				$s_hidden_fields = '';
				$params = array();
				$conflicts = request_var('conflict', array('' => 0));
				$modified = request_var('modified', array('' => 0));

				foreach ($conflicts as $filename => $merge_option)
				{
					$s_hidden_fields .= '<input type="hidden" name="conflict[' . htmlspecialchars($filename) . ']" value="' . $merge_option . '" />';
					$params[] = 'conflict[' . urlencode($filename) . ']=' . urlencode($merge_option);
				}

				foreach ($modified as $filename => $merge_option)
				{
					if (!$merge_option)
					{
						continue;
					}
					$s_hidden_fields .= '<input type="hidden" name="modified[' . htmlspecialchars($filename) . ']" value="' . $merge_option . '" />';
					$params[] = 'modified[' . urlencode($filename) . ']=' . urlencode($merge_option);
				}

				$no_update = request_var('no_update', array(0 => ''));

				foreach ($no_update as $index => $filename)
				{
					$s_hidden_fields .= '<input type="hidden" name="no_update[]" value="' . htmlspecialchars($filename) . '" />';
					$params[] = 'no_update[]=' . urlencode($filename);
				}

				// Before the user is choosing his preferred method, let's create the content list...
				$update_list = $cache->get('_update_list');

				if ($update_list === false)
				{
					trigger_error($user->lang['NO_UPDATE_INFO'], E_USER_ERROR);
				}

				// Check if the conflicts data is valid
				if (sizeof($conflicts))
				{
					$conflict_filenames = array();
					foreach ($update_list['conflict'] as $files)
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
					foreach ($update_list['modified'] as $files)
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
				if (sizeof($update_list['conflict']) != sizeof($conflicts))
				{
					trigger_error($user->lang['MERGE_SELECT_ERROR'], E_USER_ERROR);
				}

				// Before we do anything, let us diff the files and store the raw file information "somewhere"
				$get_files = false;
				$file_list = $cache->get('_diff_files');

				if ($file_list === false || $file_list['status'] != -1)
				{
					$get_files = true;
				}

				if ($get_files)
				{
					if ($file_list === false)
					{
						$file_list = array(
							'status'	=> 0,
						);
					}

					$processed = 0;
					foreach ($update_list as $status => $files)
					{
						if (!is_array($files))
						{
							continue;
						}

						foreach ($files as $file_struct)
						{
							// Skip this file if the user selected to not update it
							if (in_array($file_struct['filename'], $no_update))
							{
								continue;
							}

							// Already handled... then skip of course...
							if (isset($file_list[$file_struct['filename']]))
							{
								continue;
							}

							// Refresh if we reach 5 diffs...
							if ($processed >= 5)
							{
								$cache->put('_diff_files', $file_list);

								if (!empty($_REQUEST['download']))
								{
									$params[] = 'download=1';
								}

								$redirect_url = append_sid($this->p_master->module_url, "mode=$mode&amp;sub=update_files&amp;" . implode('&amp;', $params));
								meta_refresh(3, $redirect_url);

								$template->assign_vars(array(
									'S_IN_PROGRESS'			=> true,
									'L_IN_PROGRESS'			=> $user->lang['MERGING_FILES'],
									'L_IN_PROGRESS_EXPLAIN'	=> $user->lang['MERGING_FILES_EXPLAIN'],
								));

								return;
							}

							$original_filename = ($file_struct['custom']) ? $file_struct['original'] : $file_struct['filename'];

							switch ($status)
							{
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

									$file_list[$file_struct['filename']] = '_file_' . md5($file_struct['filename']);
									$cache->put($file_list[$file_struct['filename']], base64_encode($contents));

									$file_list['status']++;
									$processed++;

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

									$file_list[$file_struct['filename']] = '_file_' . md5($file_struct['filename']);
									$cache->put($file_list[$file_struct['filename']], base64_encode($contents));

									$file_list['status']++;
									$processed++;

								break;
							}
						}
					}
				}

				$file_list['status'] = -1;
				$cache->put('_diff_files', $file_list);

				if (!empty($_REQUEST['download']))
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
						$update_list = $cache->get('_update_list');
						$script_path = ($config['force_server_vars']) ? (($config['script_path'] == '/') ? '/' : $config['script_path'] . '/') : $user->page['root_script_path'];

						foreach ($update_list as $status => $files)
						{
							if ($status == 'up_to_date' || $status == 'no_update' || $status == 'status')
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
					$method = basename(request_var('method', ''));
					$submit = (isset($_POST['submit'])) ? true : false;
					$test_ftp_connection = request_var('test_connection', '');

					if (!$method || !class_exists($method))
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

					$s_hidden_fields .= build_hidden_fields(array('method' => $method));

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
				$download_filename = 'update_' . $this->remove_info['version']['remove'];
				$archive_filename = $download_filename . '_' . time() . '_' . unique_id();

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
				foreach ($update_list as $status => $files)
				{
					if (!is_array($files))
					{
						continue;
					}

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

									// New directory too?
									$dirname = dirname($file_struct['filename']);

									if ($dirname && !file_exists($phpbb_root_path . $dirname))
									{
										$transfer->make_dir($dirname);
									}

									$transfer->copy_file($this->new_location . $original_filename, $file_struct['filename']);
								}
							break;

							case 'modified':

								$contents = base64_decode($cache->get($file_list[$file_struct['filename']]));

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

								$contents = base64_decode($cache->get($file_list[$file_struct['filename']]));

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
						'U_ACTION'			=> append_sid($this->p_master->module_url, "mode=$mode&amp;sub=file_check"))
					);
					return;
				}

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


		// lets get rid of phpBB Garage internal data first
		if ($db->sql_layer == 'postgres')
		{
			foreach ($this->garage_postgres_sequences as $sequence)
			{
				$db->sql_query('DROP SEQUENCE ' . $table_prefix . $sequence);
			}
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

		// next for the chop is imageset data we added
		foreach ($this->garage_imageset as $image_name)
		{
			$db->sql_query('DELETE FROM ' . STYLES_IMAGESET_DATA_TABLE . " WHERE image_name = '" . $image_name. "'");
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

		$url = $this->p_master->module_url . "?mode=$mode&amp;sub=file_check";

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
			@unlink($phpbb_root_path . $file);
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
	* Show file diff
	*/
	function show_diff(&$update_list)
	{
		global $phpbb_root_path, $template, $user;

		$this->tpl_name = 'install_update_diff';

		// Got the diff template itself updated? If so, we are able to directly use it
		if (in_array('adm/style/install_update_diff.html', $this->remove_info['files']))
		{
			$this->tpl_name = '../../install/update/new/adm/style/install_update_diff';
		}

		$this->page_title = 'VIEWING_FILE_DIFF';

		$status = request_var('status', '');
		$file = request_var('file', '');
		$diff_mode = request_var('diff_mode', 'side_by_side');

		// First of all make sure the file is within our file update list with the correct status
		$found_entry = array();
		foreach ($update_list[$status] as $index => $file_struct)
		{
			if ($file_struct['filename'] === $file)
			{
				$found_entry = $update_list[$status][$index];
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
			'S_DIFF_MODE_OPTIONS'		=> $diff_mode_options,
			'S_SHOW_DIFF'			=> true,
		));

		unset($diff, $renderer);
	}

	/**
	* Collect all file status infos we need for the update by diffing all files
	*/
	function get_update_structure(&$update_list)
	{
		global $phpbb_root_path, $phpEx, $user;

		if ($update_list === false)
		{
			$update_list = array(
				'up_to_date'	=> array(),
				'new'		=> array(),
				'not_modified'	=> array(),
				'modified'	=> array(),
				'new_conflict'	=> array(),
				'conflict'	=> array(),
				'no_update'	=> array(),
				'status'	=> 0,
			);
		}

		/* if (!empty($this->remove_info['custom']))
		{
			foreach ($this->remove_info['custom'] as $original_file => $file_ary)
			{
				foreach ($file_ary as $index => $file)
				{
					$this->make_update_diff($update_list, $original_file, $file, true);
				}
			}
		} */

		// Get a list of those files which are completely new by checking with file_exists...
		$num_bytes_processed = 0;


		foreach ($this->remove_info['files'] as $index => $file)
		{
			if (is_int($update_list['status']) && $index < $update_list['status'])
			{
				continue;
			}

			if ($num_bytes_processed >= 500 * 1024)
			{
				return;
			}

			if (!file_exists($phpbb_root_path . $file))
			{
				
				// Make sure the update files are consistent by checking if the file is in new_files...
				if (!file_exists($this->new_location . $file))
				{
					trigger_error($user->lang['INCOMPLETE_UPDATE_FILES'], E_USER_ERROR);
				}

				// If the file exists within the old directory the file got removed and we will write it back
				// not a biggie, but we might want to state this circumstance separately later.
				//	if (file_exists($this->old_location . $file))
				//	{
				//		$update_list['removed'][] = $file;
				//	}

				/* Only include a new file as new if the underlying path exist
				// The path normally do not exist if the original style or language has been removed
				if (file_exists($phpbb_root_path . dirname($file)))
				{
					$this->get_custom_info($update_list['new'], $file);
					$update_list['new'][] = array('filename' => $file, 'custom' => false);
				}
				else
				{
					// Do not include style-related or language-related content
					if (strpos($file, 'styles/') !== 0 && strpos($file, 'language/') !== 0)
					{
						$update_list['no_update'][] = $file;
					}
				}*/

				if (file_exists($phpbb_root_path . dirname($file)) || (strpos($file, 'styles/') !== 0 && strpos($file, 'language/') !== 0))
				{
					$this->get_custom_info($update_list['new'], $file);
					$update_list['new'][] = array('filename' => $file, 'custom' => false);
				}

				// unset($this->remove_info['files'][$index]);
			}
			else
			{
				// not modified?
				$this->make_update_diff($update_list, $file, $file);
			}

			$num_bytes_processed += (file_exists($this->new_location . $file)) ? filesize($this->new_location . $file) : 100 * 1024;
			$update_list['status']++;
		}

		$update_list['status'] = -1;
/*		if (!sizeof($this->remove_info['files']))
		{
			return $update_list;
		}

		// Now diff the remaining files to get information about their status (not modified/modified/up-to-date)

		// not modified?
		foreach ($this->remove_info['files'] as $index => $file)
		{
			$this->make_update_diff($update_list, $file, $file);
		}

		// Now to the styles...
		if (empty($this->remove_info['custom']))
		{
			return $update_list;
		}

		foreach ($this->remove_info['custom'] as $original_file => $file_ary)
		{
			foreach ($file_ary as $index => $file)
			{
				$this->make_update_diff($update_list, $original_file, $file, true);
			}
		}

		return $update_list;*/
	}

	/**
	* Compare files for storage in update_list
	*/
	function make_update_diff(&$update_list, $original_file, $file, $custom = false)
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
				$update_list['up_to_date'][] = $update_ary;
				return;
			}

			// If no other status matches we have another file in the way...
			$update_list['new_conflict'][] = $update_ary;
			return;
		}

		// Old file removed?
		if (file_exists($this->old_location . $original_file) && !file_exists($this->new_location . $original_file))
		{
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
				$update_list['up_to_date'][] = $update_ary;
				return;
			}

			$update_list['not_modified'][] = $update_ary;
			return;
		}

		// If the file had been modified then we need to check if it is already up to date

		// if there are no differences we have an up-to-date file...
		if ($empty_2)
		{
			$update_list['up_to_date'][] = $update_ary;
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
				$update_list['up_to_date'][] = $update_ary;
				return;
			}

			$update_list['conflict'][] = $update_ary;
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

			$update_list['up_to_date'][] = $update_ary;
			return;
		}

		// If no other status matches we have a modified file...
		$update_list['modified'][] = $update_ary;
	}

	/**
	* Update update_list with custom new files
	*/
	function get_custom_info(&$update_list, $file)
	{
		if (empty($this->remove_info['custom']))
		{
			return;
		}

		if (in_array($file, array_keys($this->remove_info['custom'])))
		{
			foreach ($this->remove_info['custom'][$file] as $_file)
			{
				$update_list[] = array('filename' => $_file, 'custom' => true, 'original' => $file);
			}
		}
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
					$remove_info = array();
					include($phpbb_root_path . 'garage/install/remove/index.php');
					$info = (empty($remove_info) || !is_array($remove_info)) ? false : $remove_info;

					if ($info !== false)
					{
						$info = (!empty($info['version']['to'])) ? trim($info['version']['to']) : false;
					}
				}
			break;

			case 'remove_info':
				global $phpbb_root_path, $phpEx;

				$remove_info = array();
				include($phpbb_root_path . 'garage/install/remove/index.php');

				$info = (empty($remove_info) || !is_array($remove_info)) ? false : $remove_info;
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

		if (!empty($this->remove_info['files']) && in_array($filename, $this->remove_info['files']))
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
		'ACP_GARAGE_VERSION_CHECK'			=> 'acp',
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
		#'adm/style/garage.css',
		#'adm/style/garage_install_convert.html',
		#'adm/style/garage_install_error.html',
		#'adm/style/garage_install_footer.html',
		#'adm/style/garage_install_header.html',
		#'adm/style/garage_install_install.html',
		#'adm/style/garage_install_main.html',
		#'adm/style/garage_install_remove.html',
		#'adm/style/garage_install_updated.html',
		#'adm/style/garage_install_update_diff.html',
		'includes/acp/',
		'includes/acp/',
		'includes/acp/info/',
		'includes/acp/info/',
		'includes/db/firebird_mod.php',
		'includes/db/mssql_mod.php',
		'includes/db/mssql_odbc_mod.php',
		'includes/db/mysql_mod.php',
		'includes/db/mysqli_mod.php',
		'includes/db/oracle_mod.php',
		'includes/db/postgres_mod.php',
		'includes/db/sqlite_mod.php',
		'includes/mcp/mcp_garage.php',
		'includes/mcp/info/mcp_garage.php',
		'includes/mods/class_garage.php',
		'includes/mods/class_garage_admin.php',
		'includes/mods/class_garage_blog.php',
		'includes/mods/class_garage_business.php',
		'includes/mods/class_garage_custom_fields.php',
		'includes/mods/class_garage_dynorun.php',
		'includes/mods/class_garage_guestbook.php',
		'includes/mods/class_garage_image.php',
		'includes/mods/class_garage_insurance.php',
		'includes/mods/class_garage_model.php',
		'includes/mods/class_garage_modification.php',
		'includes/mods/class_garage_quartermile.php',
		'includes/mods/class_garage_service.php',
		'includes/mods/class_garage_template.php',
		'includes/mods/class_garage_track.php',
		'includes/mods/class_garage_vehicle.php',
		'includes/mods/constants_garage.php',
		#'includes/mods/functions_garage_install.php',
		'includes/ucp/ucp_garage.php',
		'includes/ucp/info/ucp_garage.php',
		'',
	);

	var $garage_language_files = array(
		'acp/garage.php',
		'acp/permissions_garage.php',
		'email/garage_pending.txt',
		'email/garage_guestbook_comment.txt',
		'mods/garage.php',
		'mods/garage_common.php',
		'mods/garage_install.php',
		'mods/info_acp_garage.php',
		'mods/info_mcp_garage.php',
		'mods/info_ucp_garage.php',
		'mods/garage_style.php',
	);

	var $garage_style_files = array(
		'imageset/garage_camera.gif',
		'imageset/garage_delete.gif',
		'imageset/garage_edit.gif',
		'imageset/garage_toogle.gif',
		'template/',
		'theme/garage_content.css',
		'theme/images/icon_dynorun.gif',
		'theme/images/icon_garage.gif',
		'theme/images/icon_quartermile.gif',
	);

	var $garage_style_language_files = array(
		'imageset',
		'template',
		'theme/garage_content.css',
		'theme/images/icon_dynorun.gif',
		'theme/images/icon_garage.gif',
		'theme/images/icon_quartermile.gif',
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
	* The information below will be used to drop all tables phpBB Garage created
	*/
	var $garage_imageset = array(
		'garage_img_attached',
		'garage_toggle',
		'garage_edit',
		'garage_delete',
		'garage_icon_garage',
		'garage_create_vehicle',
		'garage_edit_vehicle',
		'garage_delete_vehicle',
		'garage_view_vehicle',
		'garage_add_modification',
		'garage_add_insurance',
		'garage_add_dynorun',
		'garage_add_quartermile',
		'garage_add_lap',
		'garage_add_service',
		'garage_main_vehicle',
		'garage_no_thumb',
		'garage_main_menu',
		'garage_browse',
		'garage_search',
		'garage_quartermile_table',
		'garage_lap_table',
		'garage_dynorun_table',
		'garage_garage_review',
		'garage_shop_review',
		'garage_insurance_review',
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
	);

	var $garage_postgres_sequences = array(
		'garage_blog_seq',
		'garage_business_seq',
		'garage_categories_seq',
		'garage_custom_fields_seq',
		'garage_dynoruns_gallery_seq',
		'garage_dynoruns_seq',
		'garage_guestbooks_seq',
		'garage_images_seq',
		'garage_laps_gallery_seq',
		'garage_laps_seq',
		'garage_makes_seq',
		'garage_models_seq',
		'garage_modifications_gallery_seq',
		'garage_modifications_seq',
		'garage_premiums_seq',
		'garage_products_seq',
		'garage_quartermiles_gallery_seq',
		'garage_quartermiles_seq',
		'garage_ratings_seq',
		'garage_service_history_seq',
		'garage_tracks_seq',
		'garage_vehicles_gallery_seq',
		'garage_vehicles_seq',
	);

}

?>

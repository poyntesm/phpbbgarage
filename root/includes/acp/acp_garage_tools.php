<?php
/***************************************************************************
 *                              admin_garage_tools.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: admin_garage_tools.php 124 2006-05-13 14:57:36Z poyntesm $
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
	$module['Garage']['Tools'] = $filename;
	return;
}

//
// Let's set the root dir for phpBB
//
$no_page_header = TRUE;
$phpbb_root_path = './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require('./pagestart.' . $phpEx);
include($phpbb_root_path . 'includes/sql_parse.'.$phpEx);
require($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_garage.' . $phpEx);

//Build All Garage Classes e.g $garage_images->
require($phpbb_root_path . 'includes/class_garage.' . $phpEx);
require($phpbb_root_path . 'includes/class_garage_admin.' . $phpEx);
require($phpbb_root_path . 'includes/class_garage_image.' . $phpEx);
require($phpbb_root_path . 'includes/class_garage_template.' . $phpEx);
require($phpbb_root_path . 'includes/class_garage_vehicle.' . $phpEx);

if( isset( $HTTP_POST_VARS['mode'] ) || isset( $HTTP_GET_VARS['mode'] ) )
{
	$mode = ( isset($HTTP_POST_VARS['mode']) ) ? $HTTP_POST_VARS['mode'] : $HTTP_GET_VARS['mode'];
}
else
{
	$mode = '';
}

// Set VERBOSE to 1  for debugging info..
define("VERBOSE", 0);

// Increase maximum execution time, but don't complain about it if it isn't allowed.
@set_time_limit(1200);

switch($mode)
{
	case 'rebuild_thumbs':
		
		$params = array('start', 'cycle', 'file', 'done');
		$data = $garage->process_post_vars($params);
		$data['start'] = (empty($data['start'])) ? '0' : $data['start'] ;
		$data['cycle'] = (empty($data['cycle'])) ? '20' : $data['cycle'] ;
		$data['done'] = (empty($data['done'])) ? '0' : $data['done'] ;

		$garage_image->rebuild_thumbs($data['start'], $data['cycle'], $data['done'], $data['file']);

		break;

	case 'orphan_search':

		include('./page_header_admin.'.$phpEx);

		$active_attach = array();
		$present_attach = array();
		$orphan_attach = array();

		//Get All Images Data From The DB
		$data = $garage_image->select_all_image_data();

		for( $i = 0; $i < count($data); $i++ )
		{
			//Since Remote Images Aren't On Our Local Drive...We Can Ingore Them ;)
			if ( !preg_match("/^http:\/\//i", $data[$i]['attach_location']) )
			{
				$active_attach[] = $data[$i]['attach_location'];
			}
          
			if ( !preg_match("/^http:\/\//i", $data[$i]['attach_thumb_location']) )
			{
				$active_attach[] = $data[$i]['attach_thumb_location'];
			}
		}

		//Grab List Of Currently Present Attachments On Local Drive
		$upload_dir = opendir($phpbb_root_path . GARAGE_UPLOAD_PATH);
		while ( false !== ( $file = readdir($upload_dir) ) )
		{
			//Remove Directory Pointers '.' & '..'
			if ( ($file != "." && $file != "..") )
			{
				$present_attach[] = $file;
			}
		}
		closedir($upload_dir);
        
		//Work Out The Differences...These Are The Orphans
		$orphan_attach = array_diff($present_attach, $active_attach);
		
		//If No Orphans Exists (Good News) Let Them Know...
		if ( count($orphan_attach) <= 0 )
		{
			$message = '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_tools.$phpEx") . '">' . $lang['No_Orphaned_Files'] . "<br /></br>" . sprintf($lang['Click_Return_Tools'], "<a href=\"" . append_sid("admin_garage_tools.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

			message_die(GENERAL_MESSAGE, $message);
		}
		else
		{
			$template->set_filenames(array(
				'body' => 'admin/garage_orphans.tpl')
			);

			$template->assign_vars(array(
				'L_GARAGE_ORPHANS_TITLE' => $lang['Garage_Orphans_Title'],
				'L_GARAGE_ORPHANS_EXPLAIN' => $lang['Garage_Orphans_Explain'],
				'L_GARAGE_ORPHANS_TABLE_TITLE' => $lang['Garage_Orphans_Table_Title'],
				'L_REMOVE_SELECTED_ORPHANS' => $lang['Remove_Selected_Orphans'],
				'S_ACTION' => append_sid('admin_garage_tools.'.$phpEx))
			);

			//Otherwise Print Them All Out Baby!
			foreach ($orphan_attach as $orphan_file)
			{
					$template->assign_block_vars('file', array(
						'ORPHAN_LINK' => $phpbb_root_path . GARAGE_UPLOAD_PATH . $orphan_file,
						'ORPHAN' => $orphan_file)
					);
			}

			$template->pparse('body');

		}

		include('./page_footer_admin.'.$phpEx);

		break;

	case 'orphan_remove':

		//Setup Needed Arrays
		$output = array();
		$files = array();

		//Build Array For Orphaned Files
		if( isset( $HTTP_POST_VARS['orphan_attach'] ) )
		{
			$files = $HTTP_POST_VARS['orphan_attach'];
		}

        	// If they didn't select anything we won't get an array here ;)
	        if ( !empty($files) )
        	{
			for( $i = 0; $i < count($files); $i++ )
			{
				// Just to make sure, if the file exists...
		                if ( @file_exists( $phpbb_root_path . GARAGE_UPLOAD_PATH . $files[$i] ) )
        		        {
                			// Remove it
		        	        @unlink( $phpbb_root_path . GARAGE_UPLOAD_PATH . $files[$i] );
	                 		// And report what we just did
        	            		$output[] = $files[$i];
                		}
			}

			//Let Them Know What Files Have Been Deleted
			$message = '<meta http-equiv="refresh" content="4;url=' . append_sid("admin_garage_tools.$phpEx") . '">' . $lang['Orphaned_Files_Removed'] . "<br /></br>".implode( "<br />", $output )."<br /><br />" . sprintf($lang['Click_Return_Tools'], "<a href=\"" . append_sid("admin_garage_tools.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

			message_die(GENERAL_MESSAGE, $message);
		}

		//No File Selected For Deletion..So Let Them Know
		$message = '<meta http-equiv="refresh" content="4;url=' . append_sid("admin_garage_tools.$phpEx") . '">' . $lang['No_Orphaned_Files_Selected'] . "<br /></br>" . sprintf($lang['Click_Return_Tools'], "<a href=\"" . append_sid("admin_garage_tools.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);

	case 'backup':

		$error = false;
		switch(SQL_LAYER)
		{
			case 'oracle':
				$error = true;
				break;
			case 'db2':
				$error = true;
				break;
			case 'msaccess':
				$error = true;
				break;
			case 'mssql':
			case 'mssql-odbc':
				$error = true;
				break;
		}

		if ($error)
		{
			include('./page_header_admin.'.$phpEx);

			$template->set_filenames(array(
				"body" => "admin/admin_message_body.tpl")
			);

			$template->assign_vars(array(
				"MESSAGE_TITLE" => $lang['Information'],
				"MESSAGE_TEXT" => $lang['Backups_not_supported'])
			);

			$template->pparse("body");

			include('./page_footer_admin.'.$phpEx);
		}

		$tables = array('garage', 'garage_business', 'garage_categories', 'garage_config', 'garage_gallery', 'garage_guestbooks', 'garage_images', 'garage_insurance', 'garage_makes', 'garage_models', 'garage_mods', 'garage_quartermile', 'garage_rating', 'garage_rollingroad');

		$backup_type = (isset($HTTP_POST_VARS['backup_type'])) ? $HTTP_POST_VARS['backup_type'] : ( (isset($HTTP_GET_VARS['backup_type'])) ? $HTTP_GET_VARS['backup_type'] : "" );

		$gzipcompress = (!empty($HTTP_POST_VARS['gzipcompress'])) ? $HTTP_POST_VARS['gzipcompress'] : ( (!empty($HTTP_GET_VARS['gzipcompress'])) ? $HTTP_GET_VARS['gzipcompress'] : 0 );

		$drop = (!empty($HTTP_POST_VARS['drop'])) ? intval($HTTP_POST_VARS['drop']) : ( (!empty($HTTP_GET_VARS['drop'])) ? intval($HTTP_GET_VARS['drop']) : 0 );

		if( !isset($HTTP_POST_VARS['startdownload']) && !isset($HTTP_GET_VARS['startdownload']) )
		{
			$template->set_filenames(array(
				"body" => "admin/admin_message_body.tpl")
			);

			$template->assign_vars(array(
				"META" => '<meta http-equiv="refresh" content="2;url=' . append_sid("admin_garage_tools.$phpEx?mode=backup&backup_type=$backup_type&drop=1&amp;backupstart=1&gzipcompress=$gzipcompress&startdownload=1") . '">',

				"MESSAGE_TITLE" => $lang['Database_Utilities'] . " : " . $lang['Backup'],
				"MESSAGE_TEXT" => $lang['Backup_download'])
			);

			include('./page_header_admin.'.$phpEx);

			$template->pparse("body");

			include('./page_footer_admin.'.$phpEx);

		}

		header("Pragma: no-cache");
		$do_gzip_compress = FALSE;
		if( $gzipcompress )
		{
			$phpver = phpversion();

			if($phpver >= "4.0")
			{
				if(extension_loaded("zlib"))
				{
					$do_gzip_compress = TRUE;
				}
			}
		}
		if($do_gzip_compress)
		{
			@ob_start();
			@ob_implicit_flush(0);
			header("Content-Type: application/x-gzip; name=\"phpbb_db_garage_backup.sql.gz\"");
			header("Content-disposition: attachment; filename=phpbb_db_garage_backup.sql.gz");
		}
		else
		{
			header("Content-Type: text/x-delimtext; name=\"phpbb_db_garage_backup.sql\"");
			header("Content-disposition: attachment; filename=phpbb_db_garage_backup.sql");
		}

		//
		// Build the sql script file...
		//
		echo "#\n";
		echo "# phpBB Garage Backup Script\n";
		echo "# Dump of tables for $dbname\n";
		echo "#\n# DATE : " .  gmdate("d-m-Y H:i:s", time()) . " GMT\n";
		echo "#\n";

		if(SQL_LAYER == 'postgresql')
		{
			 echo "\n" . $garage_admin->pg_get_sequences("\n", $backup_type);
		}

		for($i = 0; $i < count($tables); $i++)
		{
			$table_name = $tables[$i];

			switch (SQL_LAYER)
			{
				case 'postgresql':
					$table_def_function = 'get_table_def_postgresql';
					$table_content_function = 'get_table_content_postgresql';
					break;

				case 'mysql':
				case 'mysql4':
					$table_def_function = 'get_table_def_mysql';
					$table_content_function = 'get_table_content_mysql';
					break;
			}

			if($backup_type != 'data')
			{
				echo "#\n# TABLE: " . $table_prefix . $table_name . "\n#\n";
				echo $garage_admin->$table_def_function($table_prefix . $table_name, "\n") . "\n";
			}

			if($backup_type != 'structure')
			{
				$garage_admin->$table_content_function($table_prefix . $table_name, "output_table_content");
			}
		}
		
		break;

	case 'restore':

		// Handle the file upload ....
		// If no file was uploaded report an error...
		$backup_file_name = (!empty($HTTP_POST_FILES['backup_file']['name'])) ? $HTTP_POST_FILES['backup_file']['name'] : "";
		$backup_file_tmpname = ($HTTP_POST_FILES['backup_file']['tmp_name'] != "none") ? $HTTP_POST_FILES['backup_file']['tmp_name'] : "";
		$backup_file_type = (!empty($HTTP_POST_FILES['backup_file']['type'])) ? $HTTP_POST_FILES['backup_file']['type'] : "";

		if($backup_file_tmpname == "" || $backup_file_name == "")
		{
			message_die(GENERAL_MESSAGE, $lang['Restore_Error_no_file']);
		}
		//
		// If I file was actually uploaded, check to make sure that we
		// are actually passed the name of an uploaded file, and not
		// a hackers attempt at getting us to process a local system
		// file.
		//
		if( file_exists(phpbb_realpath($backup_file_tmpname)) )
		{
			if( preg_match("/^(text\/[a-zA-Z]+)|(application\/(x\-)?gzip(\-compressed)?)|(application\/octet-stream)$/is", $backup_file_type) )
			{
				if( preg_match("/\.gz$/is",$backup_file_name) )
				{
					$do_gzip_compress = FALSE;
					$phpver = phpversion();
					if($phpver >= "4.0")
					{
						if(extension_loaded("zlib"))
						{
							$do_gzip_compress = TRUE;
						}
					}

					if($do_gzip_compress)
					{
						$gz_ptr = gzopen($backup_file_tmpname, 'rb');
						$sql_query = "";
						while( !gzeof($gz_ptr) )
						{
							$sql_query .= gzgets($gz_ptr, 100000);
						}
					}
					else
					{
						message_die(GENERAL_ERROR, $lang['Restore_Error_decompress']);
					}
				}
				else
				{
					$sql_query = fread(fopen($backup_file_tmpname, 'r'), filesize($backup_file_tmpname));
				}
				//
				// Comment this line out to see if this fixes the stuff...
				//
				//$sql_query = stripslashes($sql_query);
			}
			else
			{
				message_die(GENERAL_ERROR, $lang['Restore_Error_filename'] ." $backup_file_type $backup_file_name");
			}
		}
		else
		{
			message_die(GENERAL_ERROR, $lang['Restore_Error_uploading']);
		}

		if($sql_query != "")
		{
			// Strip out sql comments...
			$sql_query = remove_remarks($sql_query);
			$pieces = split_sql_file($sql_query, ";");

			$sql_count = count($pieces);
			for($i = 0; $i < $sql_count; $i++)
			{
				$sql = trim($pieces[$i]);

				if(!empty($sql) and $sql[0] != "#")
				{
					if(VERBOSE == 1)
					{
						echo "Executing: $sql\n<br>";
						flush();
					}

					$result = $db->sql_query($sql);

					if(!$result && ( !(SQL_LAYER == 'postgresql' && eregi("drop table", $sql) ) ) )
					{
						message_die(GENERAL_ERROR, "Error importing backup file", "", __LINE__, __FILE__, $sql);
					}
				}
			}
		}

		include('./page_header_admin.'.$phpEx);

		$template->set_filenames(array(
			"body" => "admin/admin_message_body.tpl")
		);

		$message = $lang['Restore_success'];

		$template->assign_vars(array(
			"MESSAGE_TITLE" => $lang['Database_Utilities'] . " : " . $lang['Restore'],
			"MESSAGE_TEXT" => $message)
		);

		$template->pparse("body");

		include('./page_footer_admin.'.$phpEx);

		break;

	default:

		include('./page_header_admin.'.$phpEx);

		$template->set_filenames(array(
			"body" => "admin/garage_tools.tpl")
		);

		$template->assign_vars(array(
			'L_GARAGE_TOOLS_TITLE' => $lang['Garage_Tools_Title'],
			'L_GARAGE_TOOLS_EXPLAIN' => $lang['Garage_Tools_Explain'],
			'L_GARAGE_TOOLS_REBUILD' => $lang['Garage_Tools_Rebuild'],
			'L_GARAGE_TOOLS_REBUILD_ALL' => $lang['Garage_Tools_Rebuild_All'],
			'L_GARAGE_TOOLS_CREATE_LOG' => $lang['Garage_Tools_Create_Log'],
			'L_GARAGE_TOOLS_ORPHANED_TITLE' => $lang['Garage_Tools_Orphaned_Title'],
			'L_GARAGE_TOOLS_ORPHANED' => $lang['Garage_Tools_Orphaned'],
			'L_GARAGE_TOOLS_ORPHANED_BUTTON' => $lang['Garage_Tools_Orphaned_Button'],
			'L_GARAGE_TOOLS_ORPHANED_BUTTON' => $lang['Garage_Tools_Orphaned_Button'],
			'L_GARAGE_DB_BACKUP' => $lang['Garage_DB_Backup'],
			'L_GARAGE_DB_RESTORE' => $lang['Garage_DB_Restore'],
			'L_START_BACKUP' => $lang['Start_Backup'],
			'L_BASE_DIRECTORY' => $phpbb_root_path . GARAGE_UPLOAD_PATH,
			'L_PER_CYCLE' => $lang['Per_Cycle'],
			'L_YES' => $lang['Yes'],
			'L_NO' => $lang['No'],
			'L_SUBMIT' => $lang['Submit'],
			'L_RESET' => $lang['Reset'],
			'L_DATABASE_BACKUP' => $lang['Database_Utilities'] . " : " . $lang['Backup'],
			'L_BACKUP_EXPLAIN' => $lang['Backup_explain'],
			'L_FULL_BACKUP' => $lang['Full_backup'],
			'L_STRUCTURE_BACKUP' => $lang['Structure_backup'],
			'L_DATA_BACKUP' => $lang['Data_backup'],
			'L_ADDITIONAL_TABLES' => $lang['Additional_tables'],
			'L_START_BACKUP' => $lang['Start_backup'],
			'L_BACKUP_OPTIONS' => $lang['Backup_options'],
			'L_GZIP_COMPRESS' => $lang['Gzip_compress'],
			'L_DATABASE_RESTORE' => $lang['Database_Utilities'] . " : " . $lang['Restore'],
			'L_RESTORE_EXPLAIN' => $lang['Restore_explain'],
			'L_SELECT_FILE' => $lang['Select_file'],
			'L_START_RESTORE' => $lang['Start_Restore'],
			'L_NO' => $lang['No'],
			'L_YES' => $lang['Yes'],
			'S_GARAGE_ACTION' => append_sid('admin_garage_tools.'.$phpEx),
			'CYCLE' => '20')
		);

		$template->pparse("body");

		include('./page_footer_admin.'.$phpEx);

		break;
}

?>

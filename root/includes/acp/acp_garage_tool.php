<?php
/***************************************************************************
 *                              admin_garage_tools.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id$
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
class acp_garage_tool
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx;

		$user->add_lang('acp/garage');
		$this->tpl_name = 'acp_garage_business';
		$this->page_title = 'ACP_MANAGE_FORUMS';

		$action		= request_var('action', '');
		$update		= (isset($_POST['update'])) ? true : false;

// Increase maximum execution time, but don't complain about it if it isn't allowed.
@set_time_limit(1200);
		switch ($mode)
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

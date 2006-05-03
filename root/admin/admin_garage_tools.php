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
$phpbb_root_path = './../';
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);
require($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_garage.' . $phpEx);

//Build All Garage Classes e.g $garage_images->
require($phpbb_root_path . 'includes/class_garage.' . $phpEx);
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

switch($mode)
{
	case 'rebuild_thumbs':
		
		$params = array('start', 'cycle', 'file', 'done');
		$data = $garage->process_post_vars($params);
		$data['start'] = (empty($data['start'])) ? '0' : $data['start'] ;
		$data['cycle'] = (empty($data['cycle'])) ? '20' : $data['cycle'] ;
		$data['done'] = (empty($data['done'])) ? '0' : $data['done'] ;

		rebuild_thumbs($data['start'], $data['cycle'], $data['done'], $data['file']);

		break;

	case 'orphan_search':

		$active_attach = array();
		$present_attach = array();
		$orphan_attach = array();

		// First let's compile a list of all the attachments that are currently known in DB
	        $sql = "SELECT *
			FROM  " . GARAGE_IMAGES_TABLE . "
			ORDER BY attach_id ASC";

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Getting Image Data', '', __LINE__, __FILE__, $sql);
		}

		while ( $row = $db->sql_fetchrow($result) )
		{
			// Since remote images aren't on our local drive we won't track them ;)
			if ( !preg_match("/^http:\/\//i", $row['attach_location']) )
			{
				$active_attach[] = $row['attach_location'];
			}
          
			if ( !preg_match("/^http:\/\//i", $row['attach_thumb_location']) )
			{
				$active_attach[] = $row['attach_thumb_location'];
			}
		}

		// Now let's grab the list of currently present attachments on the local drive
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
        
		// Calculate which ones don't belong
		$orphan_attach = array_diff($present_attach, $active_attach);
		
		// If they don't have any, let them know
		if ( count($orphan_attach) <= 0 )
		{
			$template->set_filenames(array(
				'body' => 'admin/garage_message.tpl')
			);

			$message = '<meta http-equiv="refresh" content="4;url=' . append_sid("admin_garage_tools.$phpEx") . '">' . $lang['No_Orphaned_Files'] . "<br /></br>" . sprintf($lang['Click_Return_Tools'], "<a href=\"" . append_sid("admin_garage_tools.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

			message_die(GENERAL_MESSAGE, $message);
		}
		else
		{
			$template->set_filenames(array(
				'body' => 'admin/garage_orphans.tpl')
			);

			//-------------------------------
			// Construct menu HTML
			//-------------------------------
			$template->assign_vars(array(
				'L_GARAGE_ORPHANS_TITLE' => $lang['Garage_Orphans_Title'],
				'L_GARAGE_ORPHANS_EXPLAIN' => $lang['Garage_Orphans_Explain'],
				'L_GARAGE_ORPHANS_TABLE_TITLE' => $lang['Garage_Orphans_Table_Title'],
				'L_REMOVE_SELECTED_ORPHANS' => $lang['Remove_Selected_Orphans'],
				'S_ACTION' => append_sid('admin_garage_tools.'.$phpEx))
			);

			// Otherwise print them all out baby!
			foreach ($orphan_attach as $orphan_file)
			{
					$template->assign_block_vars('file', array(
						'ORPHAN_LINK' => $phpbb_root_path . GARAGE_UPLOAD_PATH . $orphan_file,
						'ORPHAN' => $orphan_file)
					);
			}

			$template->pparse('body');

		}

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
			'L_BASE_DIRECTORY' => $phpbb_root_path . GARAGE_UPLOAD_PATH,
			'L_PER_CYCLE' => $lang['Per_Cycle'],
			'L_YES' => $lang['Yes'],
			'L_NO' => $lang['No'],
			'L_SUBMIT' => $lang['Submit'],
			'L_RESET' => $lang['Reset'],
			'S_GARAGE_CONFIG_ACTION' => append_sid('admin_garage_tools.'.$phpEx),
			'CYCLE' => '20')
		);

		$template->pparse("body");
		break;
}


//---------------------------------------------
// Rebuild All Thumbnails
//--------------------------------------------
function rebuild_thumbs($start, $cycle, $done, $file) 
{

	global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config, $garage_image, $garage;

	$output = array();
	$end = $start + $cycle;
	if (!empty($file))
	{
        	$log_file   = $phpbb_root_path . GARAGE_UPLOAD_PATH . $file;
	}

        // Are we logging?
        if ( empty($log_file) == FALSE )
        {
        	//If we are just starting make sure we start with a clean file	esle Append
		$log_type = ( $start == 0 ) ? 'wb' : 'ab';
        }

	//Count Total Images So We Know How Many Need Processing
	$total = $garage_image->count_total_images();

       	// Loop through the images avoiding limit
        $sql = "SELECT *
		FROM  " . GARAGE_IMAGES_TABLE . "
		WHERE attach_is_image = 1 
		ORDER BY attach_id ASC LIMIT $start, $cycle";

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Error Getting Image Data', '', __LINE__, __FILE__, $sql);
	}

	//We Must Be Complete As We Have No More Images To Process
	if ( $db->sql_numrows($result) < 1 )
	{
		$message = '<meta http-equiv="refresh" content="4;url=' . append_sid("admin_garage_tools.$phpEx") . '">' . $lang['Rebuild_Thumbnails_Complete'] . "<br /></br>" . sprintf($lang['Click_Return_Tools'], "<a href=\"" . append_sid("admin_garage_tools.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
	}
            
        while ( $image_row = $db->sql_fetchrow($result) )
      	{
       		// Logging
		_log($log_handle,$lang['Processing_Attach_ID'] . $image_row['attach_id']);

       	        // The process is a little different for local vs. remote files
               	if ( preg_match("/^http:\/\//i", $image_row['attach_location']) )
                {
			// This is a remote image!
			$location = $image_row['attach_location'];
			$file_name = preg_replace( "/^(.+?)\..+?$/", "\\1", $image_row['attach_file'] );

                    	// Generate our temp file name
       	            	$tmp_file_name = $file_name . '-' . time() . $image_row['attach_ext'];

               	    	// Generate our thumb file name
                    	if ( (empty($image_row['attach_thumb_location'])) OR ($image_row['attach_thumb_location'] == "remote") )
       	            	{
       		    		// We are going to use the attach_file name to create our _thumb
				//   file name since this image did not have a thumb before.
                       		$thumb_file_name = $file_name . time() . '_thumb' . $image_row['attach_ext'];
                  
			} 
			else
		       	{
                       		// We already know the thumbnail filename :)
	                        $thumb_file_name = $image_row['attach_thumb_location'];

               		}

	                _log($log_handle,$lang['Remote_Image'] . $image_row['attach_location'], 1);
    	                _log($log_handle,$lang['File_Name'] . $file_name, 2);
               		_log($log_handle,$lang['Temp_File_Name'] . $tmp_file_name, 2);

                    	// Make sure it exists, or we'll get nasty errors!
               		if ( $garage_image->remote_file_exist($image_row['attach_location']) )
			{
				// Download the remote image to our temporary file
				$garage_image->download_remote_image($image_row['attach_location'], $tmp_file_name);

				//Create The New Thumbnail
				$garage_image->create_thumbnail($tmp_file_name, $thumb_file_name, $image_row['attach_ext']);

				//Get Thumbnail Width & Height
				$image_width = $garage_image->get_image_width($thumb_file_name);
				$image_height = $garage_image->get_image_height($thumb_file_name);
	
				//Update the DB With New Thumbnail Details
				$garage->update_single_field(GARAGE_IMAGES_TABLE, 'attach_thumb_location', $thumb_file_name, 'attach_id', $image_row['attach_id']);
				$garage->update_single_field(GARAGE_IMAGES_TABLE, 'attach_thumb_width', $image_width, 'attach_id', $image_row['attach_id']);
				$garage->update_single_field(GARAGE_IMAGES_TABLE, 'attach_thumb_height', $image_height, 'attach_id', $image_row['attach_id']);

		                // Remove our temporary file!
				@unlink($phpbb_root_path . GARAGE_UPLOAD_PATH . $tmp_file_name);

                        	// Add the status message
				$output[] = $lang['Rebuilt'] . $image_row['attach_location'] . ' -> '.$thumb_file_name;

                        	_log($log_handle,$lang['Thumb_File'] . $thumb_file_name,1);

                    	}
			else
			{
                        	// Tell them that the remote file doesn't exists
                        	$output[] = "<b><font color='red'>ERROR</font></b>".$lang['File_Does_Not_Exist']."(".$image_row['attach_file'].")";
                        	_log($log_handle,$lang['File_Does_Not_Exist'],1);
                    	}
                }
		else
		{
			$source_file = $phpbb_root_path . GARAGE_UPLOAD_PATH . $image_row['attach_location'];

                    	// Generate our thumb file name
                    	if ( empty($image_row['attach_thumb_location']) )
                    	{
                       		// We are going to use the attach_id to create our _thumb
		                //   file name since this image did not have a thumb before.
                	        $thumb_file_name = preg_replace( "/^(.+?)\..+?$/", "\\1", $image_row['attach_location'] );
                       		$thumb_file_name .= '_thumb' . $image_row['attach_ext'];
                    	}
			else
			{
                        	// We already know the thumbnail filename :)
                        	$thumb_file_name = $image_row['attach_thumb_location'];
                    	}

			//Make Sure The File Actually Exists Before Processing It
			if (file_exists($phpbb_root_path . GARAGE_UPLOAD_PATH . $image_row['attach_location']))
			{
				//Create The New Thumbnail
				$garage_image->create_thumbnail($image_row['attach_location'], $thumb_file_name, $image_row['attach_ext']);

				//Get Thumbnail Width & Height
				$image_width = $garage_image->get_image_width($thumb_file_name);
				$image_height = $garage_image->get_image_height($thumb_file_name);
	
				//Update the DB With New Thumbnail Details
				$garage->update_single_field(GARAGE_IMAGES_TABLE, 'attach_thumb_location', $thumb_file_name, 'attach_id', $image_row['attach_id']);
				$garage->update_single_field(GARAGE_IMAGES_TABLE, 'attach_thumb_width', $image_width, 'attach_id', $image_row['attach_id']);
				$garage->update_single_field(GARAGE_IMAGES_TABLE, 'attach_thumb_height', $image_height, 'attach_id', $image_row['attach_id']);

	                    	// Add the status message
        	            	$output[] = $lang['Rebuilt'] . $image_row['attach_location'].' -> '.$thumb_file_name;

                	    	_log($log_handle,$lang['Thumb_File'] . $thumb_file_name, 1);
			}
			//Original Source File Is Missing
			else
			{
        	            	$output[] = $lang['Source_Unavailable'] . $image_row['attach_location'];
                	    	_log($log_handle,$lang['No_Source_File'], 1);
			}
		} // End if remote/local 

              	$done++;

	} // End while loop

	$message = '<meta http-equiv="refresh" content="5;url=' . append_sid("admin_garage_tools.$phpEx?mode=rebuild_thumbs&amp;start=$end&amp;cycle=$cycle&amp;file=$file&amp;done=$done") . '">'."<div align=\"left\"><b>".$lang['Started_At']."$start <br />".$lang['Ended_At']."$end <br />".$lang['Have_Done']."$done<br />".$lang['Need_To_Process']."$total <br />".$lang['Log_To']."$log_file <br /></b></b><br /><br />".implode( "<br />", $output )."<br /></br>";

	message_die(GENERAL_MESSAGE, $message);
}


include('./page_footer_admin.'.$phpEx);
?>

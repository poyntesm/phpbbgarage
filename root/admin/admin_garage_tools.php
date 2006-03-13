<?php
/***************************************************************************
 *                              admin_garage_tools.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: admin_garage_tools.php,v 0.0.9 06/06/2005 20:47:20 poynesmo Exp $
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
require($phpbb_root_path . 'includes/functions_garage.' . $phpEx);

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

		if( isset( $HTTP_POST_VARS['start'] ) || isset( $HTTP_GET_VARS['start'] ) )
		{
			$start = ( isset($HTTP_POST_VARS['start']) ) ? intval($HTTP_POST_VARS['start']) : intval($HTTP_GET_VARS['start']);
		}
		else
		{
			$start = '0';
		}

		if( isset( $HTTP_POST_VARS['cycle'] ) || isset( $HTTP_GET_VARS['cycle'] ) )
		{
			$cycle = ( isset($HTTP_POST_VARS['cycle']) ) ? intval($HTTP_POST_VARS['cycle']) : intval($HTTP_GET_VARS['cycle']);
		}
		else
		{
			$cycle = '20';
		}

		if( isset( $HTTP_POST_VARS['file'] ) || isset( $HTTP_GET_VARS['file'] ) )
		{
			$file = ( isset($HTTP_POST_VARS['file']) ) ? $HTTP_POST_VARS['file'] : $HTTP_GET_VARS['file'];
		}
		else
		{
			$file = '';
		}

		if( isset( $HTTP_POST_VARS['done'] ) || isset( $HTTP_GET_VARS['done'] ) )
		{
			$done = ( isset($HTTP_POST_VARS['done']) ) ? intval($HTTP_POST_VARS['done']) : intval($HTTP_GET_VARS['done']);
		}
		else
		{
			$done = '0';
		}

		rebuild_thumbs($start, $cycle, $done, $file);

		break;

	case 'orphan_search':

		orphan_search();

		break;

	case 'orphan_remove':

		$files = array();

		// users id
		// because it is an array we will intval() it when we use it
		if ( isset($HTTP_POST_VARS['orphan_attach']) || isset($HTTP_GET_VARS['orphan_attach']) )
		{
			$files = ( isset($HTTP_POST_VARS['orphan_attach']) ) ? $HTTP_POST_VARS['orphan_attach'] : $HTTP_GET_VARS['orphan_attach'];
		}

		orphan_remove($files);

		break;

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
			'S_GARAGE_CONFIG_ACTION' => append_sid('admin_garage_tools.'.$phpEx),

			'CYCLE' => '20',

			'L_YES' => $lang['Yes'],
			'L_NO' => $lang['No'],
			'L_SUBMIT' => $lang['Submit'],
			'L_RESET' => $lang['Reset'])
		);

		$template->pparse("body");
		break;
}


//---------------------------------------------
// Find those Orphans!
//--------------------------------------------
function orphan_search() 
{
	global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;

	$active_attach = array();
	$present_attach = array();
	$orphan_attach = array();

	// First let's compile a list of all the attachments that are currently
	//   tracked in the database (currently in use)
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

	// Now let's grab the list of currently present attachments on
	//   the local drive
	$upload_dir = opendir($phpbb_root_path . GARAGE_UPLOAD_PATH);
	while ( false !== ( $file = readdir($upload_dir) ) )
	{
		if ( preg_match("/^garage/", $file) OR in_array($file,$active_attach) )
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

		$text = "<br /><b>".$lang['No_Orphaned_Files']."</b><br />";

		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_tools.$phpEx") . '">',
			'TEXT' => "<b><br /><br />$text<br /><br /></b>")

		);

		$template->pparse('body');
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
}

function orphan_remove($files)
{

	global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;

        $output = array();

        // If they didn't select anything we won't get an array here ;)
        if ( !empty($files) )
        {
		$i = 0;
		$count = count($files);
		while( $i < count($files) )
		{
			$orphan_file = $files[$i];

			// Just to make sure, if the file exists...
	                if ( @file_exists( $phpbb_root_path . GARAGE_UPLOAD_PATH . $orphan_file ) )
        	        {
                		// Remove it
		                @unlink( $phpbb_root_path . GARAGE_UPLOAD_PATH . $orphan_file );

                 		// And report what we just did
                    		$output[] = $lang['Image_Deleted'] . ' : '. $orphan_file;
                	}
			$i++;
		}

		// Report our mischief ways :)
		$template->set_filenames(array(
			'body' => 'admin/garage_message.tpl')
		);
		$text = "<b>".$lang['Orphaned_Files_Removed']."</b><br /><br />".implode( "<br />", $output );
		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="5;url=' . append_sid("admin_garage_tools.$phpEx") . '">',
			'TEXT' => $text)

		);
		$template->pparse('body');
		exit;
        }
        else
        {
		//So Display A Message But Forgetting To Select Any And Get Out Of Here..
		$template->set_filenames(array(
			'body' => 'admin/garage_message.tpl')
		);
		$text = "<b>".$lang['No_Orphaned_Files_Selected']."</b>";
		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_tools.$phpEx") . '">',
			'TEXT' => $text)

		);
		$template->pparse('body');
		exit;
        }
}


//---------------------------------------------
// Rebuild All Thumbnails
//--------------------------------------------
function rebuild_thumbs($start, $cycle, $done, $file) 
{

	global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;

	//-------------------------------
	// Set up
	//-------------------------------
	$output     = array();
	$end = $start + $cycle;
	if (!empty($file))
	{
        	$log_file   = $phpbb_root_path . GARAGE_UPLOAD_PATH . $file;
	}

        // Are we logging?
        if ( empty($log_file) == FALSE )
        {
        	// If we are just starting make sure we start with a clean file	
		if ( $start == 0 )
            	{
                	$log_type = 'wb';
            	}
            	// If not then append to existing log
            	else
            	{
                	$log_type = 'ab';
            	}

            	// Open that log up!
            	$log_handle = @fopen( $log_file, $log_type );
        }

        //-------------------------------
	// Got any more?
	//-------------------------------

      	// Loop through the images avoiding limit
        $sql = "SELECT count(*) as total
			FROM  " . GARAGE_IMAGES_TABLE . "
			WHERE attach_is_image = 1 ";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Error Getting Image Data', '', __LINE__, __FILE__, $sql);
	}
	$row = $db->sql_fetchrow($result);
	$total = $row['total'];

       	// Loop through the images avoiding limit
        $sql = "SELECT *
			FROM  " . GARAGE_IMAGES_TABLE . "
			WHERE attach_is_image = 1 
			ORDER BY attach_id ASC LIMIT $start, $cycle";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Error Getting Image Data', '', __LINE__, __FILE__, $sql);
	}

	// Done...no rows so our job here is done
	if ( $db->sql_numrows($result) < 1 )
	{
		//So Display A Message And Get Out Of Here..
		$template->set_filenames(array(
			'body' => 'admin/garage_message.tpl')
		);

		$text = "<br /><b>".$lang['Rebuild_Thumbnails_Complete']."</b><br />";

		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_garage_tools.$phpEx") . '">',
			'TEXT' => "<b>$text<br /><br /></b>".implode( "<br />", $output ))

		);

		$template->pparse('body');
		exit;
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
               		if ( remote_file_exist($image_row['attach_location']) )
               		{

               			// Download the remote image to our temporary file
                       		$infile = @fopen ($image_row['attach_location'], "rb");
                       		$outfile = @fopen ( $phpbb_root_path . GARAGE_UPLOAD_PATH . $tmp_file_name, "wb");

                       		// Set our custom timeout
                       		socket_set_timeout($infile, $garage_config['remote_timeout']);

               			while (!@feof ($infile)) 
				{
	                         	@fwrite($outfile, @fread ($infile, 4096));
				}
                       		@fclose($outfile);
	                        @fclose($infile);

				@chmod($phpbb_root_path . GARAGE_UPLOAD_PATH . $tmp_file_name, 0777);

				$url_image_imagesize = getimagesize($phpbb_root_path . GARAGE_UPLOAD_PATH . $tmp_file_name);

				$url_image_width = $url_image_imagesize[0];
				$url_image_height = $url_image_imagesize[1];

				$gd_errored = FALSE;

				switch ($image_row['attach_ext'])
				{
					case '.jpg':
						$read_function = 'imagecreatefromjpeg';
						break;
					case '.png':
						$read_function = 'imagecreatefrompng';
						break;
					case '.gif':
						$read_function = 'imagecreatefromgif';
						break;
				}

				$src = @$read_function($phpbb_root_path . GARAGE_UPLOAD_PATH  . $tmp_file_name);

				if (!$src)
				{
					$gd_errored = TRUE;
					$thumb_file_name = '';
				}
				else if( ($url_image_width > $garage_config['thumbnail_resolution']) or ($url_image_height > $garage_config['thumbnail_resolution']) )
				{
					// Resize it
					if ($url_image_width > $url_image_height)
					{
						$thumb_width = $garage_config['thumbnail_resolution'];
						$thumb_height = $garage_config['thumbnail_resolution'] * ($url_image_height/$url_image_width);
					}
					else
					{
						$thumb_height = $garage_config['thumbnail_resolution'];
						$thumb_width = $garage_config['thumbnail_resolution'] * ($url_image_width/$url_image_height);
					}

					$thumb = ($garage_config['gd_version'] == 1) ? @imagecreate($thumb_width, $thumb_height) : @imagecreatetruecolor($thumb_width, $thumb_height);

					$resize_function = ($garage_config['gd_version'] == 1) ? 'imagecopyresized' : 'imagecopyresampled';

					@$resize_function($thumb, $src, 0, 0, 0, 0, $thumb_width, $thumb_height, $url_image_width, $url_image_height);
				}
				else
				{
					$thumb = $src;
				}

				if (!$gd_errored)
				{
					// Write to disk
					switch ($image_row['attach_ext'])
					{
						case '.jpg':
							@imagejpeg($thumb,$phpbb_root_path . GARAGE_UPLOAD_PATH . $thumb_file_name, 80);
							break;
						case '.png':
							@imagepng($thumb,$phpbb_root_path . GARAGE_UPLOAD_PATH . $thumb_file_name);
							break;
						case '.gif':
							@imagegif($thumb,$phpbb_root_path . GARAGE_UPLOAD_PATH . $thumb_file_name);
							break;
					}

					@chmod($phpbb_root_path . GARAGE_UPLOAD_PATH . $thumb_file_name, 0777);

				} // End IF $gd_errored

		                // Remove our temporary file!
				@unlink($phpbb_root_path . GARAGE_UPLOAD_PATH . $tmp_file_name);

		             	// Update the DB
 				$sql = "UPDATE ". GARAGE_IMAGES_TABLE ." 
						SET attach_thumb_location = '$thumb_file_name',
						    attach_thumb_width = '$thumb_width',
						    attach_thumb_height = '$thumb_height'
				       		WHERE attach_id = '". $image_row['attach_id'] ."'";
				if( !$update_result = $db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, 'Could Not Update Vehicle Hilite Image', '', __LINE__, __FILE__, $sql);
				}

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
				$image_imagesize = getimagesize($phpbb_root_path . GARAGE_UPLOAD_PATH . $image_row['attach_location']);
				$image_width = $image_imagesize[0];
				$image_height = $image_imagesize[1];
	
				$gd_errored = FALSE;

				switch ($image_row['attach_ext'])
				{
					case '.jpg':
						$read_function = 'imagecreatefromjpeg';
						break;
					case '.png':
						$read_function = 'imagecreatefrompng';
						break;
					case '.gif':
						$read_function = 'imagecreatefromgif';
						break;
				}

	                	// This is a local image
				$src = @$read_function($phpbb_root_path . GARAGE_UPLOAD_PATH  . $image_row['attach_location']);

                	    	_log($log_handle,$lang['Source_File'] . $image_row['attach_location'], 1);
			
				if (!$src)
				{
					$gd_errored = TRUE;
					$thumb_file_name = '';
				}
				else if( ($image_width > $garage_config['thumbnail_resolution']) or ($image_height > $garage_config['thumbnail_resolution']) )
				{
					// Resize it
					if ($image_width > $image_height)
					{
						$thumb_width = $garage_config['thumbnail_resolution'];
						$thumb_height = $garage_config['thumbnail_resolution'] * ($image_height/$image_width);
					}
					else
					{
						$thumb_height = $garage_config['thumbnail_resolution'];
						$thumb_width = $garage_config['thumbnail_resolution'] * ($image_width/$image_height);
					}

					$thumb = ($garage_config['gd_version'] == 1) ? @imagecreate($thumb_width, $thumb_height) : @imagecreatetruecolor($thumb_width, $thumb_height);

					$resize_function = ($garage_config['gd_version'] == 1) ? 'imagecopyresized' : 'imagecopyresampled';

					@$resize_function($thumb, $src, 0, 0, 0, 0, $thumb_width, $thumb_height, $image_width, $image_height);
				}
				else
				{
					$thumb = $src;
				}

				if (!$gd_errored)
				{
					// Write to disk
					switch ($image_row['attach_ext'])
					{
						case '.jpg':
							@imagejpeg($thumb,$phpbb_root_path . GARAGE_UPLOAD_PATH . $thumb_file_name, 80);
							break;
						case '.png':
							@imagepng($thumb,$phpbb_root_path . GARAGE_UPLOAD_PATH . $thumb_file_name);
							break;
						case '.gif':
							@imagegif($thumb,$phpbb_root_path . GARAGE_UPLOAD_PATH . $thumb_file_name);
						break;
					}

					@chmod($phpbb_root_path . GARAGE_UPLOAD_PATH . $thumb_file_name, 0777);

				} // End IF $gd_errored

	             		// Update the DB
	 			$sql = "UPDATE ". GARAGE_IMAGES_TABLE ." 
					SET attach_thumb_location = '$thumb_file_name',
					    attach_thumb_width = '$thumb_width',
					    attach_thumb_height = '$thumb_height'
		       			WHERE attach_id = '". $image_row['attach_id'] ."'";
				if( !$update_result = $db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, 'Could Not Update Vehicle Hilite Image', '', __LINE__, __FILE__, $sql);
				}

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

	// More to process so redirect with new start point..
	$template->set_filenames(array(
		  'body' => 'admin/garage_message.tpl')
		);

	$text = "<b>".$lang['Started_At']."$start <br />".$lang['Ended_At']."$end <br />".$lang['Have_Done']."$done<br />".$lang['Need_To_Process']."$total <br />".$lang['Log_To']."$log_file <br /></b>";
					
	//Setup refresh with new start equal to where we finished....
	$template->assign_vars(array(
		'META' => '<meta http-equiv="refresh" content="5;url=' . append_sid("admin_garage_tools.$phpEx?mode=rebuild_thumbs&amp;start=$end&amp;cycle=$cycle&amp;file=$file&amp;done=$done") . '">',
		'TEXT' => "<b>$text</b><br /><br />".implode( "<br />", $output ))

	);

	$template->pparse('body');

}

function _log ($log_handle,$message,$level=0)
{

	if ( empty($log_handle) == FALSE )
	{
		// Make sure we end with a new line
		if ( !preg_match('/^.+?\n$/', $message) )
		{
			$message .= "\n";
		}

		// Prepend number of tabs equal to level
		while ( $level > 0 )
		{
			$message = "\t".$message;
			$level--;
		}

		// Write the message to the log
		@fwrite( $log_handle, $message );
	}

	return TRUE;
}

function remote_file_exist($url)
{

        // Make sure php will allow us to do this...
        if ( ini_get('allow_url_fopen') )
        {

            $head = '';
            $url_p = parse_url ($url);

            if (isset ($url_p['host']))
            { $host = $url_p['host']; }
            else
            {
               return false;
            }

            if (isset ($url_p['path']))
            { $path = $url_p['path']; }
            else
            { $path = ''; }

            $fp = @fsockopen ($host, 80, $errno, $errstr, 20);
            if (!$fp)
            {
               return false;
            }
            else
            {
               $parse = parse_url($url);
               $host = $parse['host'];

               @fputs($fp, 'HEAD '.$url." HTTP/1.1\r\n");
               @fputs($fp, 'HOST: '.$host."\r\n");
               @fputs($fp, "Connection: close\r\n\r\n");
               $headers = '';
               while (!@feof ($fp))
               { $headers .= @fgets ($fp, 128); }
            }
            @fclose ($fp);

            $arr_headers = explode("\n", $headers);
            if (isset ($arr_headers[0]))    {
               if(strpos ($arr_headers[0], '200') !== false)
               { return true; }
               if( (strpos ($arr_headers[0], '404') !== false) ||
                   (strpos ($arr_headers[0], '509') !== false) ||
                   (strpos ($arr_headers[0], '410') !== false))
               { return false; }
               if( (strpos ($arr_headers[0], '301') !== false) ||
                   (strpos ($arr_headers[0], '302') !== false))
               {
                   preg_match("/Location:\s*(.+)\r/i", $headers, $matches);
                   if(!isset($matches[1]))
                       return false;
                   $nextloc = $matches[1];
                   return remote_file_exists($nextloc);
               }
            }

            // If we are still here then we got an unexpected header
            return false;
        }
        else
        {
            // Since we aren't allowed to use URL's bomb out
            return false;
        }
}

include('./page_footer_admin.'.$phpEx);


?>

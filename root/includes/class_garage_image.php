<?php
/***************************************************************************
 *                              functions_garage.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: functions_garage.php 91 2006-04-07 14:51:14Z poyntesm $
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
if (!defined('IN_PHPBB'))
{
	die('Hacking attempt');
}

class garage_image
{
	var $classname = "garage_image";

	/*========================================================================*/
	// Gets User Image Upload Quota
	// Usage: get_user_upload_quota();
	/*========================================================================*/
	function get_user_upload_quota()
	{
		global $userdata, $garage_config, $garage;
	
		if (empty($garage_config['private_upload_quota']))
		{
			//Since No Specific Group Value Exists Use Default Value
			return $garage_config['max_car_images'];
		}
		//It Appears Some Groups Have Private Permissions & Quotas We Will Need To Check Them
		else
		{
			//Get All Group Memberships
			$groupdata = $garage->get_group_membership($userdata['user_id']);
			
			//Lets Get The Private Upload Groups & Quotas
			$private_upload_groups = @explode(',', $garage_config['private_upload_perms']);
			$private_upload_quotas = @explode(',', $garage_config['private_upload_quota']);

			//Process All Groups You Are Member Of To See If Any Are Granted Permission & Quota
			for ($i = 0; $i < count($groupdata); $i++)
			{
				if (in_array($groupdata[$i]['group_id'], $private_upload_groups))
				{
					//Your A Member Of A Group Granted Permission - Find Array Key
					$index = array_search($groupdata[$i]['group_id'], $private_upload_groups);
					//So Your Quota For This Group Is...
					$quota[$i] = $private_upload_quotas[$index];
				}
			}

			//Your Were Not Granted Any Private Permissions..Return Default Value
			if  (empty($quota))
			{
				return $garage_config['max_car_images'];
			}

			//Return The Highest Quota You Were Granted
			return max($quota);
		}
	}

	/*========================================================================*/
	// Gets Group Image Upload Quota - Used Only In ACP Page
	// Usage: get_user_upload_quota();
	/*========================================================================*/
	function get_group_upload_quota($gid)
	{
		global $garage_config;

		if (empty($garage_config['private_upload_quota']))
		{
			//Since No Specific Group Value Exists Use Default Value
			return $garage_config['max_car_images'];
		}
		//It Appears Some Groups Have Private Permissions & Quotas We Will Need To Check Them
		else
		{
			//Lets Get The Private Upload Groups & Quotas
			$private_upload_groups = @explode(',', $garage_config['private_upload_perms']);
			$private_upload_quota = @explode(',', $garage_config['private_upload_quota']);

			//Find The Matching Index In Second Array For The Group ID
			if (($index = array_search($gid, $private_upload_groups)) === FALSE)
			{
				//Hmmm..Group Has Currently No Private Upload Permissions...So Give It The Default Incase They Turn It On
				return $garage_config['max_car_images'];
			} 

			//Return The Groups Quota
			return $private_upload_quota[$index];
		}
	}

	/*========================================================================*/
	// Inserts Image Into Vehicle Gallery
	// Usage: insert_gallery_image(array());
	/*========================================================================*/
	function insert_gallery_image($image_id)
	{
		global $db, $cid;

		$sql = "INSERT INTO ". GARAGE_GALLERY_TABLE ." 
			SET garage_id = '".$cid."', image_id = '".$image_id."'";

		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could Not Insert Image Data', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Check GD version
	// Usage: gd_version_check();
	/*========================================================================*/
	function gd_version_check($user_ver = 0)
	{
		if (! extension_loaded('gd'))
		{
			return;
		}
	
		static $gd_ver = 0;
		// Just accept the specified setting if it's 1.
		if ($user_ver == 1) 
		{
			$gd_ver = 1;
		       	return 1; 
		}
		// Use the static variable if function was called previously.
		if ($user_ver !=2 && $gd_ver > 0 ) 
		{ 
			return $gd_ver;
		}
		// Use the gd_info() function if possible.
		if (function_exists('gd_info')) 
		{
			$ver_info = gd_info();
			preg_match('/\d/', $ver_info['GD Version'], $match);
			$gd_ver = $match[0];
			return $match[0];
		}
		// If phpinfo() is disabled use a specified / fail-safe choice...
		if (preg_match('/phpinfo/', ini_get('disable_functions'))) 
		{
			if ($user_ver == 2) 
			{
				$gd_ver = 2;
				return 2;
			}
			else 
			{
				$gd_ver = 1;
				return 1;
			}
		}
		// ...otherwise use phpinfo().
		ob_start();
		phpinfo(8);
		$info = ob_get_contents();
		ob_end_clean();
		$info = stristr($info, 'gd version');
		preg_match('/\d/', $info, $match);
		$gd_ver = $match[0];
		return $match[0];
	}

	/*========================================================================*/
	// Return True/False Depending On If Any Images Need Handling
	// Usage:  image_attached();
	/*========================================================================*/
	function image_attached()
	{
		global $HTTP_POST_FILES, $HTTP_POST_VARS;

		if ( ((isset($HTTP_POST_FILES['FILE_UPLOAD'])) AND ($HTTP_POST_FILES['FILE_UPLOAD']['name'])) OR ((!preg_match("/^http:\/\/$/i", $HTTP_POST_VARS['url_image'])) AND (!empty($HTTP_POST_VARS['url_image']))) )
		{
			//We Have A Image To Handle So Return True
			return true;
		}
		
		return false;
	}
	
	/*========================================================================*/
	// Handle Image Upload And Thumbnail Creation For Remote/Local Images
	// Usage:  process_image_attached('type', 'id');
	/*========================================================================*/
	function process_image_attached($type, $id)
	{
		global $userdata, $template, $db, $SID, $lang, $images, $phpEx, $phpbb_root_path, $garage_config, $board_config, $HTTP_POST_FILES, $HTTP_POST_VARS, $images;
	
		if (!$this->check_permissions('UPLOAD',''))
		{
			return ;
		}

		if ( (empty($type)) OR (empty($id)) )
		{
			message_die(GENERAL_ERROR, 'Missing Type Or ID Data For Image Upload');
		}
	
		if ($gd_version = $this->gd_version_check())
	       	{
	   		if ($gd_version == 2) 
			{
				$garage_config['gd_version'] = 2;
	   		}
			else if ( $gd_version == 1 )
			{
				$garage_config['gd_version'] = 1;
			}
	   		else
			{
				$garage_config['gd_version'] = 0;
			}
		}
	       	else
	       	{
			redirect(append_sid("garage.$phpEx?mode=error&EID=19", true));
		}

		//Lets make sure it is not just a default http://
		$url_image = str_replace("\'", "''", trim($HTTP_POST_VARS['url_image']));
		if ( preg_match( "/^http:\/\/$/i", $url_image ) )
		{
			$url_image = "";
		}

		//Lets Check Directory Exists
		if (!file_exists($phpbb_root_path. GARAGE_UPLOAD_PATH))
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=24", true));
		}
		//Lets Check Its Writeable...
		//16895 is Octal for drwxrwxrwx - and thats what we need..
		if ( !fileperms($phpbb_root_path. GARAGE_UPLOAD_PATH) == '16895')
		{

			redirect(append_sid("garage.$phpEx?mode=error&EID=25", true));
		}

		//Check For Both A Remote Image & Image Upload
		if ( (!empty($url_image)) AND (!empty($HTTP_POST_FILES['FILE_UPLOAD']['name'])) )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=11", true));
		}
		//Handle Remote Images
		else if ( (!empty($url_image)) AND ( $HTTP_POST_FILES['FILE_UPLOAD']['name'] == "" OR !$HTTP_POST_FILES['FILE_UPLOAD']['name'] OR  ($HTTP_POST_FILES['FILE_UPLOAD']['name'] == "none") ) )
		{
			//Stop dynamic images and display correct error message
			if ( preg_match( "/[?&;]/", $url_image ) )
			{
				redirect(append_sid("garage.$phpEx?mode=error&EID=9", true));
			}
	
			$url_image_date = time();
			$url_image_ext = strtolower( preg_replace( "/^.*\.(\S+)$/", "\\1", $url_image ) );
			$url_image_name =  preg_replace( "/^.*\/(.*\.\S+)$/", "\\1", $url_image );
			
			switch ($url_image_ext)
			{
				case 'jpeg':
					$url_image_ext = '.jpg';
					$attach_is_image = '1';
					break;
				case 'jpg':
					$url_image_ext = '.jpg';
					$attach_is_image = '1';
					break;
				case 'png':
					$url_image_ext = '.png';
					$attach_is_image = '1';
					break;
				case 'gif':
					$url_image_ext = '.gif';
					$attach_is_image = '1';
					break;
				default:
					redirect(append_sid("garage.$phpEx?mode=error&EID=12", true));
			}
	
			// Does it exist?
			if ( !$this->remote_file_exists($url_image) ) 
			{
				redirect(append_sid("garage.$phpEx?mode=error&EID=10", true));
			}
	
			if ( $type == 'vehicle')
			{
				$tmp_file_name = 'garage_gallery-' . $id . '-' . $url_image_date;
			}
			if ( $type == 'modification')
			{
				$tmp_file_name = 'garage_mod-' . $id . '-' . $url_image_date;
			}
			if ( $type == 'quartermile')
			{
				$tmp_file_name = 'garage_quartermile-' . $id . '-' . $url_image_date;
			}
			if ( $type == 'rollingroad')
			{
				$tmp_file_name = 'garage_rollingroad-' . $id . '-' . $url_image_date;
			}
	
			$thumb_file_name = $tmp_file_name . '_thumb';

			// Append our file extension to both
			$tmp_file_name .= $url_image_ext;
			$thumb_file_name .= $url_image_ext;
	
			// Download the remote image to our temporary file
			$this->download_remote_image($url_image, $tmp_file_name);

			//Create The Thumbnail
			if ( $garage_config['gd_version'] > 0 )
			{
				$this->create_thumbnail($tmp_file_name, $thumb_file_name, $url_image_ext);
			}
			else
			{
				$thumb_file_name = $phpbb_root_path . $images['garage_no_thumb'];
				$thumb_width = '145';
				$thumb_height = '35';
			}
	
			@unlink($phpbb_root_path . GARAGE_UPLOAD_PATH . $tmp_file_name);
	
			// Handle All The DB Stuff Now
			$sql = "INSERT INTO ". GARAGE_IMAGES_TABLE ." (attach_location, attach_hits, attach_ext, attach_file, attach_thumb_location, attach_thumb_width, attach_thumb_height, attach_is_image, attach_date, attach_filesize)
				VALUES ('$url_image', '0', '$url_image_ext', '$url_image_name', '$thumb_file_name', '$thumb_width', '$thumb_height', '$attach_is_image', '$url_image_date', '0')";
			if( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not insert new entry', '', __LINE__, __FILE__, $sql);
			}
	
			$image_id = $db->sql_nextid();
	
			return $image_id;
		}
		// Uploaded Image Not Remote Image
		else if ( (isset($HTTP_POST_FILES['FILE_UPLOAD'])) AND (!empty($HTTP_POST_FILES['FILE_UPLOAD']['name'])) )
		{
			$attach_filetype = $HTTP_POST_FILES['FILE_UPLOAD']['type'];
			$attach_filesize = $HTTP_POST_FILES['FILE_UPLOAD']['size'];
			$attach_tmp = $HTTP_POST_FILES['FILE_UPLOAD']['tmp_name'];
			$attach_file = $HTTP_POST_FILES['FILE_UPLOAD']['name'];
			$attach_date = time();
	
			if ($attach_filesize == 0) 
			{
				redirect(append_sid("garage.$phpEx?mode=error&EID=6", true));
			}
	
			if ($attach_filesize / 1024 > $garage_config['max_image_kbytes'])
			{
				redirect(append_sid("garage.$phpEx?mode=error&EID=7", true));
			}
	
			// Check File Type 
			switch ($attach_filetype)
			{
				case 'image/jpeg':
				case 'image/jpg':
				case 'image/pjpeg':
					$attach_ext = '.jpg';
					$attach_is_image = '1';
					break;
				case 'image/png':
				case 'image/x-png':
					$attach_ext = '.png';
					$attach_is_image = '1';
					break;
				case 'image/gif':
					$attach_ext = '.gif';
					$attach_is_image = '1';
					break;
				default:
					message_die(GENERAL_ERROR, $lang['Not_Allowed_File_Type_Vehicle_Created_No_Image'] . "<br />Your File Type Was $attach_filetype");
			}
	
			// Generate filename
			if ( $type == 'vehicle')
			{
				$prefix = 'garage_gallery-' . $id . '-' . $attach_date;
			}
			else if ( $type == 'modification')
			{
				$prefix = 'garage_mod-' . $id . '-' . $attach_date;
			}
			else if ( $type == 'quartermile')
			{
				$prefix = 'garage_quartermile-' . $id . '-' . $attach_date;
			}
			else if ( $type == 'rollingroad')
			{
				$prefix = 'garage_rollingroad-' . $id . '-' . $attach_date;
			}
	
			do
			{
				$attach_location = $prefix . $attach_ext;
			}
			while( file_exists($phpbb_root_path . GARAGE_UPLOAD_PATH . $attach_location) );
	
			$attach_thumb_location = $prefix . '_thumb' . $attach_ext;
	
			// Move this file to upload directory
			$ini_val = ( @phpversion() >= '4.0.0' ) ? 'ini_get' : 'get_cfg_var';
	
			if ( @$ini_val('open_basedir') != '' )
			{
				if ( @phpversion() < '4.0.3' )
				{
					message_die(GENERAL_ERROR, 'open_basedir is set and your PHP version does not allow move_uploaded_file<br /><br />Please contact your server admin', '', __LINE__, __FILE__);
				}
	
				$move_file = 'move_uploaded_file';
			}
			else
			{
				$move_file = 'copy';
			}
	
			$move_file($attach_tmp, $phpbb_root_path . GARAGE_UPLOAD_PATH . $attach_location);
			@chmod($phpbb_root_path . GARAGE_UPLOAD_PATH . $attach_location, 0777);
	
			// Well, it's an image. Check its image size
			$attach_imagesize = getimagesize($phpbb_root_path . GARAGE_UPLOAD_PATH . $attach_location);
			$attach_width = $attach_imagesize[0];
			$attach_height = $attach_imagesize[1];
	
			if ( ($attach_width > $garage_config['max_image_resolution']) or ($attach_height > $garage_config['max_image_resolution']) )
			{
				@unlink($phpbb_root_path . GARAGE_UPLOAD_PATH . $attach_location);
				redirect(append_sid("garage.$phpEx?mode=error&EID=8", true));
			}

			//Create The Thumbnail For This Image
			if ( $garage_config['gd_version'] > 0 )
			{
				$this->create_thumbnail($attach_location, $attach_thumb_location, $attach_ext);
			}
			else
			{
				$attach_thumb_location = $phpbb_root_path . $images['garage_no_thumb'];
				$thumb_width = '145';
				$thumb_height = '35';
			}
	
			// Handle All The DB Stuff Now
			$sql = "INSERT INTO ". GARAGE_IMAGES_TABLE ." (attach_location, attach_hits, attach_ext, attach_file, attach_thumb_location, attach_thumb_width, attach_thumb_height, attach_is_image, attach_date, attach_filesize)
				VALUES ('$attach_location', '0', '$attach_ext', '$attach_file', '$attach_thumb_location', '$thumb_width', '$thumb_height', '$attach_is_image', '$attach_date', '$attach_filesize')";
			if( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not insert new entry', '', __LINE__, __FILE__, $sql);
			}
	
			$image_id = $db->sql_nextid();
	
			return $image_id;
		}
		//We really should not end up here...but lets return as we check for a empty $image_id
		else
		{
			return;
		}

		return;
	}
	
	/*========================================================================*/
	// Create Thumbnail From Sourcefile 
	// Usage: create_thumbnail('source file', 'destination file', 'file type');
	/*========================================================================*/
	function create_thumbnail($source_file_name, $thumb_file_name, $file_ext)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		$gd_errored = FALSE;

		switch ($file_ext)
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

		$imagesize = getimagesize($phpbb_root_path . GARAGE_UPLOAD_PATH  . $source_file_name );
		$width = $imagesize[0];
		$height = $imagesize[1];
	
		$src = @$read_function( $phpbb_root_path . GARAGE_UPLOAD_PATH  . $source_file_name );
	
		if (!$src)
		{
			$gd_errored = TRUE;
			$thumb_file_name = '';
		}
		else if( ($width > $garage_config['thumbnail_resolution']) or ($height > $garage_config['thumbnail_resolution']) )
		{
			// Resize it
			if ($width > $height)
			{
				$thumb_width = $garage_config['thumbnail_resolution'];
				$thumb_height = $garage_config['thumbnail_resolution'] * ($height/$width);
			}
			else
			{
				$thumb_height = $garage_config['thumbnail_resolution'];
				$thumb_width = $garage_config['thumbnail_resolution'] * ($width/$height);
			}

			$thumb = ($garage_config['gd_version'] == 1) ? @imagecreate($thumb_width, $thumb_height) : @imagecreatetruecolor($thumb_width, $thumb_height);

			$resize_function = ($garage_config['gd_version'] == 1) ? 'imagecopyresized' : 'imagecopyresampled';

			@$resize_function($thumb, $src, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);

		}
		else
		{
			$thumb = $src;
		}
	
		if (!$gd_errored)
		{
			// Write to disk
			switch ($file_ext)
			{
				case '.jpg':
					@imagejpeg($thumb, $phpbb_root_path . GARAGE_UPLOAD_PATH . $thumb_file_name, 80);
					break;
				case '.png':
					@imagepng($thumb, $phpbb_root_path . GARAGE_UPLOAD_PATH . $thumb_file_name);
					break;
				case '.gif':
					@imagegif($thumb, $phpbb_root_path . GARAGE_UPLOAD_PATH . $thumb_file_name);
					break;
			}
			@chmod($phpbb_root_path . GARAGE_UPLOAD_PATH . $thumb_file_name, 0777);
		} 

		//We should ALWAYS clear the RAM used by this.
		imagedestroy($thumb);
		imagedestroy($src);

		return;
	}

	/*========================================================================*/
	// Return The Width Of An Image
	// Usage: get_image_width('source file');
	/*========================================================================*/
	function get_image_width($source_file_name)
	{
		global $phpbb_root_path;

		$imagesize = getimagesize($phpbb_root_path . GARAGE_UPLOAD_PATH . $source_file_name);

		return $imagesize[0];
	}

	/*========================================================================*/
	// Return The Height Of An Image
	// Usage: get_image_height('source file');
	/*========================================================================*/
	function get_image_height($source_file_name)
	{
		global $phpbb_root_path;

		$imagesize = getimagesize($phpbb_root_path . GARAGE_UPLOAD_PATH . $source_file_name);

		return $imagesize[1];
	}

	
	/*========================================================================*/
	// Delete Image Including Actual File & Thumbnail
	// Usage:  delete_image('image id');
	/*========================================================================*/
	function delete_image($image_id)
	{
		global $phpbb_root_path;
	
		//Right They Want To Delete A Image
		if (empty($image_id))
		{
	 		message_die(GENERAL_ERROR, 'Image ID Not Entered', '', __LINE__, __FILE__);
		}
		
		//Right User Want To Delete An Image Lets Get All Info
		$data = $this->select_image_data($image_id);
	
		if ( (!empty($data['attach_location'])) OR (!empty($data['attach_thumb_location'])) )
		{
			//Right Image Exists So Lets Delete From DB First
			$this->delete_rows(GARAGE_IMAGES_TABLE, 'attach_id', $image_id);
	
			//Make sure it is not a remote image and then delete both files
			if ( !preg_match( "/^http:\/\//i", $data['attach_location']) )
			{
				@unlink($phpbb_root_path . GARAGE_UPLOAD_PATH . $data['attach_location']);
				@unlink($phpbb_root_path . GARAGE_UPLOAD_PATH . $data['attach_thumb_location']);
			}
			//Remote Image So Delete Just The Thumbnail
			else
			{
				@unlink($phpbb_root_path . GARAGE_UPLOAD_PATH . $data['attach_thumb_location']);
			}
		}
	
		return;
	}
	
	/*========================================================================*/
	// Delete A Gallery Image
	// Usage:  delete_gallery_image('image id');
	/*========================================================================*/
	function delete_gallery_image($image_id)
	{
		global $db, $cid;

		$this->delete_image($image_id);
	
		$data = $this->select_vehicle_data($cid);
	
		if ( $data['image_id']  == $image_id)
		{
			$this->update_single_field(GARAGE_TABLE,'image_id','NULL','image_id',$image_id);
		}

		// Remove From Gallery DB Table
		$this->delete_rows(GARAGE_GALLERY_TABLE, 'image_id', $image_id);

		return;
	}
	
	/*========================================================================*/
	// Check The Remote Image Exists
	// Usage:  remote_file_exists('url location');
	/*========================================================================*/
	function remote_file_exists($url)
	{
	        // Make sure php will allow us to do this...
	        if ( ini_get('allow_url_fopen') )
	        {
	        	$head = '';
	        	$url_p = parse_url ($url);
	
	        	if (isset ($url_p['host']))
	            	{
				$host = $url_p['host']; 
			}
	            	else
	            	{
	                	return false;
	            	}
	
	            	if (isset ($url_p['path']))
	            	{ 
				$path = $url_p['path']; 
			}
	            	else
	            	{
			       	$path = ''; 
			}
	
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
	               		{ 
					$headers .= @fgets ($fp, 128); 
				}
	            	}
	            	@fclose ($fp);
	
	            	$arr_headers = explode("\n", $headers);
	            	if (isset ($arr_headers[0]))    
			{
	               		if(strpos ($arr_headers[0], '200') !== false)
	               		{ 
					return true; 
				}
	               		if( (strpos ($arr_headers[0], '404') !== false) || (strpos ($arr_headers[0], '509') !== false) || (strpos ($arr_headers[0], '410') !== false))
	               		{ 
					return false; 
				}
	               		if( (strpos ($arr_headers[0], '301') !== false) || (strpos ($arr_headers[0], '302') !== false))
				{
	                   		preg_match("/Location:\s*(.+)\r/i", $headers, $matches);
	                   		if(!isset($matches[1]))
					{
	                       			return false;
					}
	                   		$nextloc = $matches[1];
					return $this->remote_file_exists($nextloc);
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
	
	/*========================================================================*/
	// Select All Image Data From DB
	// Usage: select_image_data('image id');
	/*========================================================================*/
	function select_image_data($image_id)
	{
		global $db;

		$sql = "SELECT  * FROM " . GARAGE_IMAGES_TABLE . " WHERE attach_id ='$image_id'";

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Image Data', '', __LINE__, __FILE__, $sql);
		}
		
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
	
		return $row;
	}

	/*========================================================================*/
	// Select All Gallery Data From DB
	// Usage: select_gallery_data('vehicle id');
	/*========================================================================*/
	function select_gallery_data($cid)
	{
		global $db;

		//Process Each Gallery Image For This Vehicle
		$sql = "SELECT gallery.*, images.*
     			FROM " . GARAGE_GALLERY_TABLE . " AS gallery
        			LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = gallery.image_id 
        		WHERE gallery.garage_id = $cid
			GROUP BY gallery.id";

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select Image Data', '', __LINE__, __FILE__, $sql);
      		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}
		$db->sql_freeresult($result);
	
		return $rows;
	}

	/*========================================================================*/
	// Count All Images In The Garage
	// Usage: count_total_images();
	/*========================================================================*/
	function count_total_images()
	{
		global $db;

		$sql = "SELECT count(*) as total
		       	FROM " . GARAGE_IMAGES_TABLE ;

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Image Data', '', __LINE__, __FILE__, $sql);
		}
		
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
	
		return $row['total'];
	}

	/*========================================================================*/
	// Download Remote Image
	// Usage: download_remote_image('Image URL', 'Destination File Name');
	/*========================================================================*/
	function download_remote_image($remote_url, $destination_file)
	{
		global $garage_config;

		// Download the remote image to our temporary file
                $infile = @fopen ($remote_url, "rb");
                $outfile = @fopen ( $phpbb_root_path . GARAGE_UPLOAD_PATH . $destination_file, "wb");

                // Set our custom timeout
                socket_set_timeout($infile, $garage_config['remote_timeout']);

               	while (!@feof ($infile)) 
		{
	               	@fwrite($outfile, @fread ($infile, 4096));
		}
                @fclose($outfile);
	        @fclose($infile);

		@chmod($phpbb_root_path . GARAGE_UPLOAD_PATH . $destination_file, 0777);
	}
}

$garage_image = new garage_image();

?>

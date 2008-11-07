<?php
/** 
*
* @package garage
* @version $Id$
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
*/
if (!defined('IN_PHPBB'))
{
	die('Hacking attempt');
}

/**
* phpBB Garage Images Class
* @package garage
*/
class garage_image
{
	var $classname = "garage_image";

	/**
	* Return users upload image quota
	*
	* @param array $groups multi-dimensional array holding the users group membership
	*
	*/
	function get_user_upload_image_quota($groups)
	{
		global $user, $garage_config, $garage;
	
		//If No Specific Group Value Exists Use Default Value
		if (empty($garage_config['upload_groups']))
		{
			return $garage_config['default_upload_quota'];
		}
		//It Appears Some Groups Have Private Permissions & Quotas We Will Need To Check Them
		else
		{
			//Lets Get The Private Upload Groups & Quotas
			$private_upload_groups = @explode(',', $garage_config['upload_groups']);
			$private_upload_quotas = @explode(',', $garage_config['upload_groups_quotas']);

			//Process All Groups You Are Member Of To See If Any Are Granted Permission & Quota
			for ($i = 0, $count = sizeof($groups);$i < $count; $i++)
			{
				if (in_array($groups[$i]['group_id'], $private_upload_groups))
				{
					//Your A Member Of A Group Granted Permission - Find Array Key & Get Quota
					$index = array_search($groups[$i]['group_id'], $private_upload_groups);
					$quota[$i] = $private_upload_quotas[$index];
				}
			}

			//Your Were Not Granted Any Private Permissions..Return Default Value
			if  (empty($quota))
			{
				return $garage_config['default_upload_quota'];
			}

			//Return The Highest Quota You Were Granted
			return max($quota);
		}
	}

	/**
	* Return users remote image quota
	*
	* @param array $groups multi-dimensional array holding the users group membership
	*
	*/
	function get_user_remote_image_quota($groups)
	{
		global $user, $garage_config, $garage;
	
		//If No Specific Group Value Exists Use Default Value
		if (empty($garage_config['remote_groups']))
		{
			return $garage_config['default_remote_quota'];
		}
		//It Appears Some Groups Have Private Permissions & Quotas We Will Need To Check Them
		else
		{
			//Lets Get The Private Upload Groups & Remote Quotas
			$private_upload_groups = @explode(',', $garage_config['remote_groups']);
			$private_remote_quotas = @explode(',', $garage_config['remote_groups_quotas']);

			//Process All Groups You Are Member Of To See If Any Are Granted Permission & Quota
			for ($i = 0, $count = sizeof($groups);$i < $count; $i++)
			{
				if (in_array($groups[$i]['group_id'], $private_upload_groups))
				{
					//Your A Member Of A Group Granted Permission - Find Array Key & Get Quota
					$index = array_search($groups[$i]['group_id'], $private_upload_groups);
					$quota[$i] = $private_remote_quotas[$index];
				}
			}

			//Your Were Not Granted Any Private Permissions..Return Default Value
			if  (empty($quota))
			{
				return $garage_config['default_remote_quota'];
			}

			//Return The Highest Quota You Were Granted
			return max($quota);
		}
	}

	/**
	* Return groups upload image quota
	*
	* @param int $gid group id to filter on
	*
	*/
	function get_group_upload_image_quota($gid)
	{
		global $garage_config;

		//If No Specific Group Value Exists Use Default Value
		if (empty($garage_config['upload_groups']))
		{
			return;
		}
		//It Appears Some Groups Have Private Permissions & Quotas We Will Need To Check Them
		else
		{
			//Lets Get The Private Upload Groups & Quotas
			$private_upload_groups	= @explode(',', $garage_config['upload_groups']);
			$private_upload_quota 	= @explode(',', $garage_config['upload_groups_quotas']);

			//Find The Matching Index In Second Array For The Group ID
			if (($index = array_search($gid, $private_upload_groups)) === false)
			{
				return;
			} 

			//Return The Groups Quota
			return $private_upload_quota[$index];
		}
	}

	/**
	* Return groups remote image quota
	*
	* @param int $gid group id to filter on
	*
	*/
	function get_group_remote_image_quota($gid)
	{
		global $garage_config;

		//If No Specific Group Value Exists Use Default Value
		if (empty($garage_config['remote_groups']))
		{
			return;
		}
		//It Appears Some Groups Have Private Permissions & Quotas We Will Need To Check Them
		else
		{
			//Lets Get The Private Upload Groups & Quotas
			$private_upload_groups	= @explode(',', $garage_config['remote_groups']);
			$private_remote_quota 	= @explode(',', $garage_config['remote_groups_quotas']);

			//Find The Matching Index In Second Array For The Group ID
			if (($index = array_search($gid, $private_upload_groups)) === false)
			{
				return;
			} 

			//Return The Groups Quota
			return $private_remote_quota[$index];
		}
	}

	/**
	* TODO: change global vid to parameter 
	* Insert image into vehicle gallery
	*
	* @param int $image_id image id to add to vehicle gallery
	* @param boolean $hilite image should be highlight image
	*
	*/
	function insert_vehicle_gallery_image($image_id, $hilite)
	{
		global $db, $vid;

		$sql = 'INSERT INTO ' . GARAGE_VEHICLE_GALLERY_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'vehicle_id'	=> $vid,
			'image_id'	=> $image_id,
			'hilite'	=> $hilite)
		);

		$db->sql_query($sql);

		return;
	}

	/**
	* TODO: change global mid and vid to parameter 
	* Insert image into modification gallery
	*
	* @param int $image_id image id to add to vehicle gallery
	* @param boolean $hilite image should be highlight image
	*
	*/
	function insert_modification_gallery_image($image_id, $hilite)
	{
		global $db, $vid, $mid;

		$sql = 'INSERT INTO ' . GARAGE_MODIFICATION_GALLERY_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'vehicle_id'		=> $vid,
			'modification_id'	=> $mid,
			'image_id'		=> $image_id,
			'hilite'		=> $hilite)
		);

		$db->sql_query($sql);

		return;
	}

	/**
	* TODO: change global qmid and vid to parameter 
	* Insert image into quartermile gallery
	*
	* @param int $image_id image id to add to vehicle gallery
	* @param boolean $hilite image should be highlight image
	*
	*/
	function insert_quartermile_gallery_image($image_id, $hilite)
	{
		global $db, $vid, $qmid;

		$sql = 'INSERT INTO ' . GARAGE_QUARTERMILE_GALLERY_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'vehicle_id'	=> $vid,
			'quartermile_id'=> $qmid,
			'image_id'	=> $image_id,
			'hilite'	=> $hilite)
		);

		$db->sql_query($sql);

		return;
	}

	/**
	* TODO: change global did and vid to parameter 
	* Insert image into dynorun gallery
	*
	* @param int $image_id image id to add to vehicle gallery
	* @param boolean $hilite image should be highlight image
	*
	*/
	function insert_dynorun_gallery_image($image_id, $hilite)
	{
		global $db, $vid, $did;

		$sql = 'INSERT INTO ' . GARAGE_DYNORUN_GALLERY_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'vehicle_id'	=> $vid,
			'dynorun_id'	=> $did,
			'image_id'	=> $image_id,
			'hilite'	=> $hilite)
		);

		$db->sql_query($sql);

		return;
	}

	/**
	* TODO: change global lid and vid to parameter 
	* Insert image into lap gallery
	*
	* @param int $image_id image id to add to vehicle gallery
	* @param boolean $hilite image should be highlight image
	*
	*/
	function insert_lap_gallery_image($image_id, $hilite)
	{
		global $db, $vid, $lid;

		$sql = 'INSERT INTO ' . GARAGE_LAP_GALLERY_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'vehicle_id'	=> $vid,
			'lap_id'	=> $lid,
			'image_id'	=> $image_id,
			'hilite'	=> $hilite)
		);

		$db->sql_query($sql);

		return;
	}

	/**
	* Determine GD version if available else set to 0
	*
	* @param int $user_ver version to default
	*
	*/
	function gd_version_check($user_ver = 0)
	{
		if (!extension_loaded('gd'))
		{
			return;
		}
	
		static $gd_ver = 0;
		//Just Accept The Specified Setting If It's 1
		if ($user_ver == 1) 
		{
			$gd_ver = 1;
		       	return 1; 
		}
		//Use The Static Variable If function Was Called Previously
		if ($user_ver !=2 && $gd_ver > 0 ) 
		{ 
			return $gd_ver;
		}
		//Use The gd_info() Function If Possible
		if (function_exists('gd_info')) 
		{
			$ver_info = gd_info();
			preg_match('/\d/', $ver_info['GD Version'], $match);
			$gd_ver = $match[0];
			return $match[0];
		}
		//If phpinfo() is disabled use a specified / fail-safe choice...
		if (preg_match('/phpinfo/', ini_get('disable_functions'))) 
		{
			$gd_ver = ($user_ver == 2) ? 2 : 1;

			return $gd_ver;
		}
		//Otherwise Use phpinfo()
		ob_start();
		phpinfo(8);
		$info = ob_get_contents();
		ob_end_clean();
		$info = stristr($info, 'gd version');
		preg_match('/\d/', $info, $match);
		$gd_ver = $match[0];
		return $match[0];
	}

	/**
	* Check if any image either uploaded or remote needs processing
	*
	* @return boolean
	*
	*/
	function image_attached()
	{
		global $_FILES, $_POST;

		//Look For Image To Handle From Either Upload Or Remotely Linked
		if ( ((isset($_FILES['FILE_UPLOAD'])) AND ($_FILES['FILE_UPLOAD']['name'])) OR ((!preg_match("/^http:\/\/$/i", $_POST['url_image'])) AND (!empty($_POST['url_image']))) )
		{
			return true;
		}

		//No Image To Handle So Return False	
		return false;
	}

	/**
	* Determine if image is remote
	*
	* @return boolean
	*
	*/
	function image_is_remote()
	{
		global $_POST;

		//Lets Make Sure It's Not Just A Default http:// 
		$url_image = str_replace("\'", "''", trim($_POST['url_image']));
		if ( preg_match( "/^http:\/\/$/i", $url_image ) )
		{
			$url_image = "";
		}

		//Is Image Remote
		if ( !empty($url_image) )
		{
			return true;
		}

		//Image Is Not Remote So Return False	
		return false;
	}

	/**
	* Determine if image is uploaded
	*
	* @return boolean
	*
	*/
	function image_is_local()
	{
		global $_FILES;

		//Is Image Local
		if ( (isset($_FILES['FILE_UPLOAD'])) AND (!empty($_FILES['FILE_UPLOAD']['name'])) )
		{
			return true;
		}

		//Image Is Not Local So Return False	
		return false;
	}

	/**
	* Handle image upload including thumbnail creation
	*
	* @param string $type type of parent item (vehicle, modification quartermile, dynorun, lap)
	* @param int $id id of parent item 
	*
	*/
	function process_image_attached($type, $id)
	{
		global $user, $images, $phpEx, $phpbb_root_path, $garage_config, $_FILES, $_POST, $garage, $vid, $auth;

		if ( (!$auth->acl_get('u_garage_upload_image')) OR (!$auth->acl_get('u_garage_remote_image')) )
		{
			return;
		}

		//Setup $garage_config['gd_version']
		if ($gd_version = $this->gd_version_check())
	       	{
			$garage_config['gd_version'] = 0;
	   		if ($gd_version == 2) 
			{
				$garage_config['gd_version'] = 2;
	   		}
			else if ( $gd_version == 1 )
			{
				$garage_config['gd_version'] = 1;
			}
		}

		//Check Directory Exists...And If Not Let User Know To Contact Administrator With Helpful Pointer
		if (!file_exists($phpbb_root_path. GARAGE_UPLOAD_PATH))
		{
			redirect(append_sid("garage.$phpEx", "mode=error&amp;EID=24"));
		}
		//Check Its Writeable '16895' Is Octal For drwxrwxrwx.... Let User Know To Contact Admin With Helpful Pointer
		if (!fileperms($phpbb_root_path. GARAGE_UPLOAD_PATH) == '16895')
		{
			redirect(append_sid("garage.$phpEx", "mode=error&amp;EID=25"));
		}

		//Check For Both A Remote Image & Image Upload..Not Allowed
		if ( ($this->image_is_remote()) AND ($this->image_is_local()) )
		{
			redirect(append_sid("garage.$phpEx", "mode=error&amp;EID=11"));
		}
		//Process The Remote Image
		else if ( $this->image_is_remote() )
		{
			$data['location'] = str_replace("\'", "''", trim($_POST['url_image']));

			//Stop dynamic images and display correct error message
			if ( preg_match( "/[?&;]/", $data['location'] ) )
			{
				redirect(append_sid("garage.$phpEx", "mode=error&amp;EID=9"));
			}
			//Does Remote File Exist?
			if ( !$this->remote_file_exists($data['location']) ) 
			{
				redirect(append_sid("garage.$phpEx", "mode=error&amp;EID=10"));
			}
	
			$data['date']	= time();
			$data['ext'] 	= strtolower( preg_replace( "/^.*\.(\S+)$/", "\\1", $data['location'] ) );
			$data['file'] 	= preg_replace( "/^.*\/(.*\.\S+)$/", "\\1", $data['location'] );

			switch ($data['ext'])
			{
				case 'jpeg':
				case 'jpg':
					$data['ext'] = '.jpg';
					$data['is_image'] = '1';
					break;
				case 'png':
					$data['ext'] = '.png';
					$data['image'] = '1';
					break;
				case 'gif':
					$data['ext'] = '.gif';
					$data['is_image'] = '1';
					break;
				default:
					redirect(append_sid("garage.$phpEx", "mode=error&amp;EID=12"));
			}
	
			//Build File Names	
			$data['tmp_name'] 	= 'garage_' . $type . '-' . $id . '-' . $data['date'] . $data['ext'];
			$data['thumb_location'] = 'garage_' . $type . '-' . $id . '-' . $data['date'] . '_thumb' . $data['ext'];
			$data['vehicle_id'] 	= ($type == 'vehicle') ? $id : $vid;
	
			//Download Remote Image To Our Temporary File
			$this->download_remote_image($data['location'], $data['tmp_name']);

			//Create The Thumbnail If We Have GD On The Server
			if ( $garage_config['gd_version'] > 0 )
			{
				//Create The Thumbnail
				$this->create_thumbnail($data['tmp_name'], $data['thumb_location'], $data['ext']);

				//Get Thumbnail Width & Height
				$data['thumb_width']	= $this->get_image_width($data['thumb_location']);
				$data['thumb_height'] 	= $this->get_image_height($data['thumb_location']);
				$data['thumb_filesize'] = $this->get_image_filesize($data['thumb_location']);
			}
			//No GD So Use Default Image
			else
			{
				$data['thumb_location']	= $phpbb_root_path . $images['garage_no_thumb'];
				$data['thumb_width'] 	= '145';
				$data['thumb_height'] 	= '35';
			}

			//Filesize is 0 as we have not used local storage for the many image.. only thumbnai
			$data['filesize'] = 0;

			//Remove Our Temporary File As We No Longer Need It..
			@unlink($phpbb_root_path . GARAGE_UPLOAD_PATH . $data['tmp_name']);
	
			//Insert The Image Into The DB Now We Are Finished
			$image_id = $this->insert_image($data);
	
			return $image_id;
		}
		//Uploaded Image Not Remote Image
		else if ( $this->image_is_local() )
		{
			$data['filesize']	= $_FILES['FILE_UPLOAD']['size'];
			$data['tmp_name'] 	= $_FILES['FILE_UPLOAD']['tmp_name'];
			$data['file']		= trim(str_replace("\'", "''", trim(htmlspecialchars($_FILES['FILE_UPLOAD']['name']))));
			$data['date'] 		= time();
			$imagesize 		= getimagesize($_FILES['FILE_UPLOAD']['tmp_name']);
			$data['filetype'] 	= $imagesize[2];
	
			if ($data['filesize'] == 0) 
			{
				redirect(append_sid("garage.$phpEx?mode=error&EID=6", true));
			}
	
			//Check File Type 
			switch ($data['filetype'])
			{
				case '1':
					$data['ext'] = '.gif';
					$data['is_image'] = '1';
					break;
				case '2':
					$data['ext'] = '.jpg';
					$data['is_image'] = '1';
					break;
				case '3':
					$data['ext'] = '.png';
					$data['is_image'] = '1';
					break;
				default:
					trigger_error($lang['Not_Allowed_File_Type_Vehicle_Created_No_Image'] . "<br />Your File Type Was " .$data['filetype'] . adm_back_link(append_sid("index.$phpEx", "i=garage_tool")));
			}
	
			//Generate Required Filename & Thumbname
			$data['vehicle_id'] 	= ($type == 'vehicle') ? $id : $vid;
			$data['location'] 	= 'garage_' . $type . '-' . $id . '-' . $data['date'] . $data['ext'];
			$data['thumb_location'] = 'garage_' . $type . '-' . $id . '-' . $data['date'] . '_thumb' . $data['ext'];
	
			//Move File To Upload Directory...We Know Directory Exists From Earlier Checks...
			$move_file = 'copy';
			$ini_val = ( @phpversion() >= '4.0.0' ) ? 'ini_get' : 'get_cfg_var';
			if ( @$ini_val('open_basedir') != '' )
			{
				if ( @phpversion() < '4.0.3' )
				{
					trigger_error('open_basedir is set and your PHP version does not allow move_uploaded_file<br /><br />Please contact your server admin');
				}
				$move_file = 'move_uploaded_file';
			}
	
			$move_file($data['tmp_name'], $phpbb_root_path . GARAGE_UPLOAD_PATH . $data['location']);
			@chmod($phpbb_root_path . GARAGE_UPLOAD_PATH . $data['location'], 0777);
	
			//Lets Get Image Width & Height
			$data['width'] 	= $this->get_image_width($data['location']);
			$data['height'] = $this->get_image_height($data['location']);

			//Check If Image Breaches Site Rules...If So Just Resize It To Required Size.
			if ( ($data['width'] > $garage_config['max_image_resolution']) or ($data['height'] > $garage_config['max_image_resolution']) )
			{
				//Create Temp Filename To Make Compliant Image
				$data['tmp_location'] = "temp_" . $data['location'];
				//Work Out Image Resize Deminisions To Keep Ratio
				if ($data['width'] > $data['height'])
				{
					$resize_width = $garage_config['max_image_resolution'];
					$resize_height = ($garage_config['max_image_resolution'] / $data['width']) * $data['height'];
				}
				else
				{
					$resize_width =  ($garage_config['max_image_resolution'] / $data['height']) * $data['width'];
					$resize_height = $garage_config['max_image_resolution'];
				}

				//Resize Images Thats Too Big To A Compliant Size & Set Its Permission
				$this->resize_image($data['location'], $data['tmp_location'], $data['ext'], $data['width'], $data['height'], $resize_width, $resize_height);

				//Delete Original Too Large Image
				@unlink($phpbb_root_path . GARAGE_UPLOAD_PATH . $data['location']);

				//Move Compliant Image Back To Original Name & Setup Permissions
				rename($phpbb_root_path . GARAGE_UPLOAD_PATH . $data['tmp_location'], $phpbb_root_path . GARAGE_UPLOAD_PATH . $data['location']);

				//Reset Width & Height Values
				$data['width'] = $resize_width;
				$data['height'] = $resize_height;
			}

			//If After Resize We Are Still Too Big Guess We Just Need To Error
			$data['filesize'] = filesize($phpbb_root_path . GARAGE_UPLOAD_PATH . $data['location']);
			if ($data['filesize'] / 1024 > $garage_config['max_image_kbytes'])
			{
				redirect(append_sid("garage.$phpEx?mode=error&EID=7", true));
			}


			//Create The Thumbnail For This Image
			if ( $garage_config['gd_version'] > 0 )
			{
				$this->create_thumbnail($data['location'], $data['thumb_location'], $data['ext']);

				//Get Thumbnail Width & Height
				$data['thumb_width'] 	= $this->get_image_width($data['thumb_location']);
				$data['thumb_height'] 	= $this->get_image_height($data['thumb_location']);
				$data['thumb_filesize'] = $this->get_image_filesize($data['thumb_location']);
			}
			else
			{
				$data['thumb_location'] = $phpbb_root_path . $images['garage_no_thumb'];
				$data['thumb_width'] = '145';
				$data['thumb_height'] = '35';
			}

			//Filesize Is Zero Since Its Remote
			$data['filesize'] = '0';
	
			//Insert The Image Into The DB Now We Are Finished
			$image_id = $this->insert_image($data);
	
			return $image_id;
		}
	}

	/**
	* Insert image details into DB
	*
	* @param array $data single-dimension array with data for image
	*
	*/
	function insert_image($data)
	{
		global $db;

		$sql = 'INSERT INTO ' . GARAGE_IMAGES_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'vehicle_id'		=> $data['vehicle_id'],
			'attach_location'	=> $data['location'],
			'attach_hits'		=> '0',
			'attach_ext'		=> $data['ext'],
			'attach_file'		=> $data['file'],
			'attach_thumb_location'	=> $data['thumb_location'],
			'attach_thumb_width'	=> $data['thumb_width'],
			'attach_thumb_height'	=> $data['thumb_height'],
			'attach_is_image'	=> $data['is_image'],
			'attach_date'		=> time(),
			'attach_filesize'	=> $data['filesize'],
			'attach_thumb_filesize'	=> $data['thumb_filesize'])
		);

		$db->sql_query($sql);
		
		return $db->sql_nextid();
	}
	

	/**
	* Thumbnail creation
	*
	* @param string $source_file_name file name to use source file
	* @param string $thumb_file_name destination name for thumbnail
	* @param string $file_ext file type of source file
	*
	*/
	function create_thumbnail($source_file_name, $thumb_file_name, $file_ext)
	{
		global $phpbb_root_path, $garage_config;
	
		$gd_errored = false;

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

		$width 	= $this->get_image_width($source_file_name);
		$height = $this->get_image_height($source_file_name);
	
		$src = @$read_function( $phpbb_root_path . GARAGE_UPLOAD_PATH  . $source_file_name );
	
		if (!$src)
		{
			$gd_errored = true;
			$thumb_file_name = '';
		}
		else if( ($width > $garage_config['thumbnail_resolution']) or ($height > $garage_config['thumbnail_resolution']) )
		{
			//Resize it
			if ($width > $height)
			{
				$thumb_width	= $garage_config['thumbnail_resolution'];
				$thumb_height 	= $garage_config['thumbnail_resolution'] * ($height/$width);
			}
			else
			{
				$thumb_height 	= $garage_config['thumbnail_resolution'];
				$thumb_width 	= $garage_config['thumbnail_resolution'] * ($width/$height);
			}

			$thumb = ($garage_config['gd_version'] == 1) ? @imagecreate($thumb_width, $thumb_height) : @imagecreatetruecolor($thumb_width, $thumb_height);
			$resize_function = ($garage_config['gd_version'] == 1) ? 'imagecopyresized' : 'imagecopyresampled';
			@$resize_function($thumb, $src, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);

		}
		//Source Is Bigger Than Thumb...So Just Use Source..
		else
		{
			$thumb = $src;
		}

		//No Problems So Far So Lets Create The Actual Thumbnail File Next...
		if (!$gd_errored)
		{
			//Different Call Based On Image Type...
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

	/**
	* Resize Image
	*
	* @param string $source source file
	* @param string $destination destination file
	* @param string $ext file tyep of source file
	* @param int $src_width width of source 
	* @param int $src_hieght height of source
	* @param int $resize_width required resized width
	* @param int $resize_height required resized height
	*
	*/
	function resize_image($source, $destination, $ext, $src_width, $src_height, $resize_width, $resize_height)
	{
		global $phpbb_root_path, $garage_config;
	
		$gd_errored = false;

		switch ($ext)
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

		$source_file_name = $phpbb_root_path . GARAGE_UPLOAD_PATH  . $source;
		$destination_file_name = $phpbb_root_path . GARAGE_UPLOAD_PATH  . $destination;

		$src = @$read_function($source_file_name);
	
		if (!$src)
		{
			$gd_errored = true;
			$destination_file_name = '';
		}
		else
		{
			$dest = ($garage_config['gd_version'] == 1) ? @imagecreate($resize_width, $resize_height) : @imagecreatetruecolor($resize_width, $resize_height);
			$resize_function = ($garage_config['gd_version'] == 1) ? 'imagecopyresized' : 'imagecopyresampled';
			@$resize_function($dest, $src, 0, 0, 0, 0, $resize_width, $resize_height, $src_width, $src_height);

		}

		//No Problems So Far So Lets Create The Actual Thumbnail File Next...
		if (!$gd_errored)
		{
			//Different Call Based On Image Type...
			switch ($ext)
			{
				case '.jpg':
					@imagejpeg($dest, $destination_file_name, 100);
					break;
				case '.png':
					@imagepng($dest, $destination_file_name);
					break;
				case '.gif':
					@imagegif($dest, $destination_file_name);
					break;
			}
			@chmod($destination_file_name, 0777);
		} 

		//We should ALWAYS clear the RAM used by this.
		imagedestroy($dest);
		imagedestroy($src);

		return;
	}

	/**
	* Return image width
	*
	* @param string $source_file_name source file you require width of
	*
	*/
	function get_image_width($source_file_name)
	{
		global $phpbb_root_path;

		$imagesize = getimagesize($phpbb_root_path . GARAGE_UPLOAD_PATH . $source_file_name);

		return $imagesize[0];
	}

	/**
	* Return image height
	*
	* @param string $source_file_name source file you require height of
	*
	*/	
	function get_image_height($source_file_name)
	{
		global $phpbb_root_path;

		$imagesize = getimagesize($phpbb_root_path . GARAGE_UPLOAD_PATH . $source_file_name);

		return $imagesize[1];
	}

	/**
	* Return image size in btyes
	*
	* @param string $source_file_name sourec file you require size of
	*
	*/	
	function get_image_filesize($source_file_name)
	{
		global $phpbb_root_path;

		return filesize($phpbb_root_path . GARAGE_UPLOAD_PATH . $source_file_name);
	}

	/**
	* Return size of all images in btyes stored for user
	*
	* @param int $user_id user id to get space usage for
	*
	*/
	function get_user_space_used($user_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'SUM(i.attach_filesize + i.attach_thumb_filessize) as space_used',
			'FROM'		=> array(
				GARAGE_IMAGES_TABLE	=> 'i',
				GARAGE_VEHICLES_TABLE	=> 'v',
			),
			'WHERE'		=>  "i.attach_is_image = 1
						AND i.vehicle_id = v.id
						AND v.user_id = $user_id",
			'ORDER_BY'	=>  "i.attach_id ASC"
		));

		$result = $db->sql_query_limit($sql, $limit, $start);
		$data = $db->sql_fetchrow($result);
	
		return $data['space_used'];
	}
	
	/**
	* Delete an image physically from server & entry in garage_images_table
	*
	* @param int $image_id id of image to delete
	*
	*/	
	function delete_image($image_id)
	{
		global $phpbb_root_path, $garage;
	
		//Right User Wants To Delete An Image Lets Get All Info
		$data = $this->get_image($image_id);
	
		if ( (!empty($data['attach_location'])) OR (!empty($data['attach_thumb_location'])) )
		{
			//Right Image Exists So Lets Delete From DB First
			$garage->delete_rows(GARAGE_IMAGES_TABLE, 'attach_id', $image_id);

			//Delete Thumbnail	
			if (file_exists($phpbb_root_path . GARAGE_UPLOAD_PATH . $data['attach_thumb_location']))
			{
				@unlink($phpbb_root_path . GARAGE_UPLOAD_PATH . $data['attach_thumb_location']);
			}

			//If Its A Local Image Delete The Source File As Well
			if ( !preg_match( "/^http:\/\//i", $data['attach_location']) )
			{
				if (file_exists($phpbb_root_path . GARAGE_UPLOAD_PATH . $data['attach_location']))
				{
					@unlink($phpbb_root_path . GARAGE_UPLOAD_PATH . $data['attach_location']);
				}
			}
		}
	
		return;
	}
	
	/**
	* Delete image from vehicle gallery
	*
	* @param int $image_id image id to delete
	*
	*/	
	function delete_vehicle_image($image_id)
	{
		global $garage;

		$this->delete_image($image_id);

		$garage->delete_rows(GARAGE_VEHICLE_GALLERY_TABLE, 'image_id', $image_id);

		return;
	}

	/**
	* Delete image from modification gallery
	*
	* @param int $image_id image id to delete
	*
	*/
	function delete_modification_image($image_id)
	{
		global $garage;

		$this->delete_image($image_id);

		$garage->delete_rows(GARAGE_MODIFICATION_GALLERY_TABLE, 'image_id', $image_id);

		return;
	}

	/**
	* Delete image from quartermile gallery
	*
	* @param int $image_id image id to delete
	*
	*/
	function delete_quartermile_image($image_id)
	{
		global $garage;

		$this->delete_image($image_id);

		$garage->delete_rows(GARAGE_QUARTERMILE_GALLERY_TABLE, 'image_id', $image_id);

		return;
	}

	/**
	* Delete image from dynorun gallery
	*
	* @param int $image_id image id to delete
	*
	*/
	function delete_dynorun_image($image_id)
	{
		global $garage;

		$this->delete_image($image_id);

		$garage->delete_rows(GARAGE_DYNORUN_GALLERY_TABLE, 'image_id', $image_id);

		return;
	}

	/**
	* Delete image from lap gallery
	*
	* @param int $image_id image id to delete
	*
	*/
	function delete_lap_image($image_id)
	{
		global $garage;

		$this->delete_image($image_id);

		$garage->delete_rows(GARAGE_LAP_GALLERY_TABLE, 'image_id', $image_id);

		return;
	}
	
	/**
	* Check a remote file exists
	*
	* @param string $url url to check for exists
	*
	*/
	function remote_file_exists($url)
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
	
		$path = (isset ($url_p['path'])) ? $url_p['path'] : '';

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

	        //If we are still here then we got an unexpected header
	        return false;
	}
	
	/**
	* Return data for specific image
	*
	* @param int $image_id image id to get data for
	*
	*/
	function get_image($image_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'i.*',
			'FROM'		=> array(
				GARAGE_IMAGES_TABLE	=> 'i',
			),
			'WHERE'		=>  "i.attach_id = $image_id"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return a limited array of random selection of images
	*
	* @param int $required number of images to return
	*
	*/
	function get_random_image($required = 5)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'i.*',
			'FROM'		=> array(
				GARAGE_IMAGES_TABLE	=> 'i',
			),
			'ORDER_BY'	=>  "rand()"
		));

		$result = $db->sql_query_limit($sql, $required);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);
	
		return $data;
	}

	/**
	* Return a limited array of images
	*
	* @param int $start start point for images
	* @param int $limit number of images to return
	*
	*/
	function get_images($start = 0 , $limit = 20)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'i.*',
			'FROM'		=> array(
				GARAGE_IMAGES_TABLE	=> 'i',
			),
			'WHERE'		=>  "i.attach_is_image = 1",
			'ORDER_BY'	=>  "i.attach_id ASC"
		));

		$result = $db->sql_query_limit($sql, $limit, $start);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);
	
		return $data;
	}

	/**
	* Return array of all images
	*/
	function get_all_images()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> "i.*, u.*, v.made_year, mk.make, md.model",
			'FROM'		=> array(
				GARAGE_IMAGES_TABLE	=> 'i',
				GARAGE_VEHICLES_TABLE	=> 'v',
				GARAGE_MAKES_TABLE	=> 'mk',
				GARAGE_MODELS_TABLE	=> 'md',
				USERS_TABLE		=> 'u',
			),
			'WHERE'		=> "i.vehicle_id = v.id
						AND v.user_id = u.user_id
						AND v.make_id = mk.id
						AND v.model_id = md.id",
			'ORDER_BY'	=> "i.attach_id ASC"
		));

		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			if (!empty($row))
			{
				$row['vehicle'] = "{$row['made_year']} {$row['make']} {$row['model']}";
			}
			$data[] = $row;
		}
		$db->sql_freeresult($result);
	
		return $data;
	}

	/**
	* Return array of specific vehicle gallery images
	*
	* @param int $vid vehicle id to get images for
	*
	*/	
	function get_vehicle_gallery($vid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'vg.*, i.*',
			'FROM'		=> array(
				GARAGE_VEHICLE_GALLERY_TABLE	=> 'vg',
				GARAGE_IMAGES_TABLE		=> 'i',
			),
			'WHERE'		=>  "vg.vehicle_id = $vid
						AND i.attach_id = vg.image_id",
			'GROUP_BY'	=>  "vg.id"
		));

      		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array of specific modification gallery images
	*
	* @param int $mid modification id to get images for
	*
	*/
	function get_modification_gallery($mid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'mg.*, i.*',
			'FROM'		=> array(
				GARAGE_MODIFICATION_GALLERY_TABLE	=> 'mg',
				GARAGE_IMAGES_TABLE			=> 'i',
			),
			'WHERE'		=>  "mg.modification_id = $mid
						AND i.attach_id = mg.image_id",
			'GROUP_BY'	=>  "mg.id"
		));

      		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array of specific quartermile gallery images
	*
	* @param int $qmid quartermile id to get images for
	*
	*/
	function get_quartermile_gallery($qmid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'qg.*, i.*',
			'FROM'		=> array(
				GARAGE_QUARTERMILE_GALLERY_TABLE	=> 'qg',
				GARAGE_IMAGES_TABLE			=> 'i',
			),
			'WHERE'		=>  "qg.quartermile_id = $qmid
						AND i.attach_id = qg.image_id",
			'GROUP_BY'	=>  "qg.id"
		));

      		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array of specific dynorun gallery images
	*
	* @param int $did dynorun id to get images for
	*
	*/
	function get_dynorun_gallery($did)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'dg.*, i.*',
			'FROM'		=> array(
				GARAGE_DYNORUN_GALLERY_TABLE	=> 'dg',
				GARAGE_IMAGES_TABLE		=> 'i',
			),
			'WHERE'		=>  "dg.dynorun_id = $did
						AND i.attach_id = dg.image_id",
			'GROUP_BY'	=>  "dg.id"
		));

      		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array of specific lap gallery images
	*
	* @param int $lid lap id to get images for
	*
	*/
	function get_lap_gallery($lid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'lg.*, i.*',
			'FROM'		=> array(
				GARAGE_LAP_GALLERY_TABLE	=> 'lg',
				GARAGE_IMAGES_TABLE		=> 'i',
			),
			'WHERE'		=>  "lg.lap_id = $lid
						AND i.attach_id = lg.image_id",
			'GROUP_BY'	=>  "lg.id"
		));

      		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array of uploaded images for specific user
	*
	* @param int $user_id user id to get uploaded images for
	*
	*/
	function get_user_upload_images($user_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'i.*',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'v',
				GARAGE_IMAGES_TABLE	=> 'i',
			),
			'WHERE'		=>  "v.user_id = $user_id
		       				AND v.id = i.vehicle_id	
						AND i.attach_location NOT LIKE 'http://%'"
		));

      		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);
	
		return $data;
	}

	/**
	* Return array of remote images for specific user
	*
	* @param int $user_id user id to get remote images for
	*
	*/
	function get_user_remote_images($user_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'i.*',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'v',
				GARAGE_IMAGES_TABLE	=> 'i',
			),
			'WHERE'		=>  "v.user_id = $user_id 
		       				AND v.id = i.vehicle_id	
						AND i.attach_location LIKE 'http://%'"
		));

      		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);
	
		return $data;
	}

	/**
	* Return boolean if user is below remote or uploaded quota depending on image 
	* 
	* @return boolean
	*
	*/
	function below_image_quotas()
	{
		global $phpbb_root_path, $phpEx, $user;

		include_once($phpbb_root_path . 'includes/functions_user.' . $phpEx);

		//Get All Users Images So We Can Workout Current Quota Usage
		$user_upload_image_data = $this->get_user_upload_images($user->data['user_id']);
		$user_remote_image_data = $this->get_user_remote_images($user->data['user_id']);

		//Get Users Group Memberships Now As We Should Do This Only Once
		$group_memberships = group_memberships(false, array($user->data['user_id']), false);

		//Check For Remote & Local Image Quotas
		if ( (($this->image_is_remote() ) AND (sizeof($user_remote_image_data) < $this->get_user_remote_image_quota($group_memberships))) OR (($this->image_is_local() ) AND (sizeof($user_upload_image_data) < $this->get_user_upload_image_quota($group_memberships))) )
		{
			return true;
		}
	}

	/**
	* Return boolean if user is above remote or uploaded quota depending on image
	* 
	* @return boolean
	*
	*/
	function above_image_quotas()
	{
		global $phpbb_root_path, $phpEx, $user;

		include_once($phpbb_root_path . 'includes/functions_user.' . $phpEx);

		//Get All Users Images So We Can Workout Current Quota Usage
		$user_upload_image_data = $this->get_user_upload_images($user->data['user_id']);
		$user_remote_image_data = $this->get_user_remote_images($user->data['user_id']);

		//Get Users Group Memberships Now As We Should Do This Only Once
		$group_memberships = group_memberships(false, array($user->data['user_id']), false);

		//You Have Reached Your Image Quota
		if ( (($this->image_is_remote() ) AND (sizeof($user_remote_image_data) >= $this->get_user_remote_image_quota($group_memberships))) OR (($this->image_is_local() ) AND (sizeof($user_upload_image_data) >= $this->get_user_upload_image_quota($group_memberships))) )
		{
			return true;
		}
	}

	/**
	* Return count of total images
	*/	
	function count_total_images()
	{
		return sizeof($this->get_all_images());
	}

	/**
	* Download a remote image to local for thumbnail creation
	*
	* @param string $remote_url url to download
	* @param string $destination_file destination file
	*
	*/
	function download_remote_image($remote_url, $destination_file)
	{
		global $garage_config, $phpbb_root_path;

		//If Allowed By Host Use fopen....
	        if ( ini_get('allow_url_fopen') )
		{
			$infile	= @fopen ($remote_url, "rb");
                	$outfile= @fopen ( $phpbb_root_path . GARAGE_UPLOAD_PATH . $destination_file, "wb");

       		        socket_set_timeout($infile, $garage_config['remote_timeout']);

               		while (!@feof ($infile)) 
			{
	               		@fwrite($outfile, @fread ($infile, 4096));
			}
	                @fclose($outfile);
		        @fclose($infile);
		}
		//Not Allowed Use fopen So Use fsockopen...So Everyone Is Happy
		else
		{
			$infile = $this->fsockopen_url($remote_url);
			$outfile= @fopen($phpbb_root_path . GARAGE_UPLOAD_PATH . $destination_file,"w+");
			@fwrite($outfile,$infile);
			@fclose($outfile);
			@fclose($infile);
		}
                @fclose($outfile);
	        @fclose($infile);

		@chmod($phpbb_root_path . GARAGE_UPLOAD_PATH . $destination_file, 0777);

		return;
	}


	/**
	* Download a remote image to local using fsockopen
	*
	* @param string $remote_url url to download
	*
	*/
	function fsockopen_url($url) 
	{
		$url_parsed = parse_url($url);
		$host = $url_parsed["host"];
		$port = $url_parsed["port"];
		if ($port==0)
		{
			$port = 80;
		}

		$path = $url_parsed["path"];
		if (empty($path))
		{
			$path="/";
		}
	
		if ($url_parsed["query"] != "")
		{
			$path .= "?".$url_parsed["query"];
		}

	  	$out = "GET $path HTTP/1.0\r\nHost: $host\r\n\r\n";
	  	$fp = fsockopen($host, $port, $errno, $errstr, 5);
	  	if (!$fp) 
		{    
			return false;
	  	} 
		else 
		{
			fwrite($fp, $out);
			$body = false;
			while (!feof($fp)) 
			{
		  		$s = fgets($fp, 1024);
		  		if ( $body )
				{
					$in .= $s;
				}
		  		if ( $s == "\r\n" )
				{
					$body = true;
				}
			} 
		}
	  	fclose($fp);
	  	return $in;
	}

	/**
	* Rebuild thumbnails. Useful when deminisions changed in ACP
	*
	* @param int $start starting point for loop
	* @param int $limit images to process in loop
	* @param int $done number of images completed already
	* @param string $file file name to for log creation
	*
	*/
	function rebuild_thumbs($start, $limit, $done, $file) 
	{
	
		global $user, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $garage, $u_action;
	
		$output = array();
		$end = $start + $limit;
		$log_type = 'wb';
		$log_file = null;

		//Setup Log File Location
		if (!empty($file))
		{
	        	$log_file   = $phpbb_root_path . GARAGE_UPLOAD_PATH . $file;
		}
	
		//Count Total Images So We Know How Many Need Processing
		$total = $this->count_total_images();

		//Get Images Required To Process
		$images = $this->get_images($start, $limit);
	
		if (!$images && $start > 0 )
		{

			trigger_error($user->lang['REBUILD_THUMBNAILS_COMPLETE'] . adm_back_link(append_sid("index.$phpEx", "i=garage_tool")));
		}
		else if (!$images && $start == 0 )
		{
			trigger_error($user->lang['NO_THUMBNAILS_TO_REBUILD'] . adm_back_link(append_sid("index.$phpEx", "i=garage_tool")), E_USER_WARNING);
		}
	
		//Work Out If Logging Is Appending Or Creating A File
	        if ( (empty($log_file) == false) AND ( $done == 0 ) )
		{
			//Just Starting So Write From Start..Produce A Message..Then Set To Appebd
			$log_type = 'wb';
			$garage->write_logfile($log_file, $log_type, '', 0);
			$log_type = 'ab';
		}
		else if ( (empty($log_file) == false) AND ( $done > 0 ) )
		{
			//We Will Append Since This Is Not The Start
			$log_type = 'ab';
		}

		//Setup $garage_config['gd_version']
		if ($gd_version = $this->gd_version_check())
	       	{
			$garage_config['gd_version'] = 0;
	   		if ($gd_version == 2) 
			{
				$garage_config['gd_version'] = 2;
	   		}
			else if ( $gd_version == 1 )
			{
				$garage_config['gd_version'] = 1;
			}
		}

		for ( $i = 0; $i < count($images); $i++ )
	      	{
			//Write Log Message
			$garage->write_logfile($log_file, $log_type, $lang['Processing_Attach_ID'] . $images[$i]['attach_id'], 0);
	
	       	        //The Process Is Different For Local v Remote Files
	               	if ( preg_match("/^http:\/\//i", $images[$i]['attach_location']) )
	                {
				//This is a remote image!
				$location = $images[$i]['attach_location'];
				$file_name = preg_replace( "/^(.+?)\..+?$/", "\\1", $images[$i]['attach_file'] );
	
	                    	//Generate Temp File Name
	       	            	$tmp_file_name = $file_name . '-' . time() . $images[$i]['attach_ext'];
	
	               	    	//Generate Thumbnail Filename
	                    	if ( (empty($images[$i]['attach_thumb_location'])) OR ($images[$i]['attach_thumb_location'] == "remote") )
	       	            	{
	       		    		//Use The 'attach_file' field To Create Thumbnail Filename 
	                       		$thumb_file_name = $file_name . time() . '_thumb' . $images[$i]['attach_ext'];
				} 
				else
			       	{
	                       		//We already Know The Thumbnail Filename :)
		                        $thumb_file_name = $images[$i]['attach_thumb_location'];
	               		}
	
		                $garage->write_logfile($log_file, $log_type, $lang['Remote_Image'] . $images[$i]['attach_location'], 1);
	    	                $garage->write_logfile($log_file, $log_type, $lang['File_Name'] . $file_name, 2);
	               		$garage->write_logfile($log_file, $log_type, $lang['Temp_File_Name'] . $tmp_file_name, 2);
	
	                    	// Make sure it exists, or we'll get nasty errors!
	               		if ( $this->remote_file_exists($images[$i]['attach_location']) )
				{
					// Download the remote image to our temporary file
					$this->download_remote_image($images[$i]['attach_location'], $tmp_file_name);
	
					//Create The New Thumbnail
					$this->create_thumbnail($tmp_file_name, $thumb_file_name, $images[$i]['attach_ext']);
	
					//Get Thumbnail Width & Height
					$image_width = $this->get_image_width($thumb_file_name);
					$image_height = $this->get_image_height($thumb_file_name);
					$image_filesize = $this->get_image_filesize($thumb_file_name);
		
					//Update the DB With New Thumbnail Details
					$garage->update_single_field(GARAGE_IMAGES_TABLE, 'attach_thumb_location', $thumb_file_name, 'attach_id', $images[$i]['attach_id']);
					$garage->update_single_field(GARAGE_IMAGES_TABLE, 'attach_thumb_width', $image_width, 'attach_id', $images[$i]['attach_id']);
					$garage->update_single_field(GARAGE_IMAGES_TABLE, 'attach_thumb_height', $image_height, 'attach_id', $images[$i]['attach_id']);
					$garage->update_single_field(GARAGE_IMAGES_TABLE, 'attach_thumb_filesize', $image_filesize, 'attach_id', $images[$i]['attach_id']);
	
			                // Remove our temporary file!
					@unlink($phpbb_root_path . GARAGE_UPLOAD_PATH . $tmp_file_name);
	
	                        	// Add the status message
					$output[] = $lang['Rebuilt'] . $images[$i]['attach_location'] . ' -> '.$thumb_file_name;
	
	                        	$garage->write_logfile($log_file, $log_type, $lang['Thumb_File'] . $thumb_file_name, 1);
	                    	}
				else
				{
	                        	// Tell them that the remote file doesn't exists
	                        	$output[] = '<b><span class="gensmall" style="color:#FF0000">ERROR</span></b>'.$lang['File_Does_Not_Exist']."(".$images[$i]['attach_file'].")";
	                        	$garage->write_logfile($log_file, $log_type, $lang['File_Does_Not_Exist'], 1);
	                    	}
	                }
			else
			{
				$source_file = $phpbb_root_path . GARAGE_UPLOAD_PATH . $images[$i]['attach_location'];
	
	               	    	//Generate Thumbnail Filename
	                    	if ( empty($images[$i]['attach_thumb_location']) )
	                    	{
	                       		// We are going to use the attach_id to create our _thumb
			                //   file name since this image did not have a thumb before.
	                	        $thumb_file_name = preg_replace( "/^(.+?)\..+?$/", "\\1", $images[$i]['attach_location'] );
	                       		$thumb_file_name .= '_thumb' . $images[$i]['attach_ext'];
	                    	}
				else
				{
	                        	//We Already Know The Thumbnail Filename :)
	                        	$thumb_file_name = $images[$i]['attach_thumb_location'];
	                    	}
	
				//Make Sure The File Actually Exists Before Processing It
				if (file_exists($phpbb_root_path . GARAGE_UPLOAD_PATH . $images[$i]['attach_location']))
				{
					//Create The New Thumbnail
					$this->create_thumbnail($images[$i]['attach_location'], $thumb_file_name, $images[$i]['attach_ext']);
	
					//Get Thumbnail Width & Height
					$image_width = $this->get_image_width($thumb_file_name);
					$image_height = $this->get_image_height($thumb_file_name);
					$image_filesize = $this->get_image_filesize($thumb_file_name);
		
					//Update the DB With New Thumbnail Details
					$garage->update_single_field(GARAGE_IMAGES_TABLE, 'attach_thumb_location', $thumb_file_name, 'attach_id', $images[$i]['attach_id']);
					$garage->update_single_field(GARAGE_IMAGES_TABLE, 'attach_thumb_width', $image_width, 'attach_id', $images[$i]['attach_id']);
					$garage->update_single_field(GARAGE_IMAGES_TABLE, 'attach_thumb_height', $image_height, 'attach_id', $images[$i]['attach_id']);
					$garage->update_single_field(GARAGE_IMAGES_TABLE, 'attach_thumb_filesize', $image_filesize, 'attach_id', $images[$i]['attach_id']);
	
		                    	//Add The Status Message
	        	            	$output[] = $lang['Rebuilt'] . $images[$i]['attach_location'].' -> '.$thumb_file_name;
	
	                	    	$garage->write_logfile($log_file, $log_type, $lang['Thumb_File'] . $thumb_file_name, 1);
				}
				//Original Source File Is Missing
				else
				{
	        	            	$output[] = $lang['Source_Unavailable'] . $images[$i]['attach_location'];
	                	    	$garage->write_logfile($log_file, $log_type, $lang['No_Source_File'], 1);
				}
			} // End if remote/local 
	              	$done++;
		}

		trigger_error('<meta http-equiv="refresh" content="5;url=' . append_sid("index.$phpEx", "i=garage_tool&amp;mode=tools&amp;action=rebuild_thumbs&amp;start=$end&amp;limit=$limit&amp;file=$file&amp;done=$done") . '">'."<div align=\"left\"><b>".$user->lang['STARTED_AT']."$start <br />".$user->lang['ENDED_AT']."$end <br />".$user->lang['HAVE_DONE']."$done<br />".$user->lang['NEED_TO_PROCESS']."$total <br />".$lang['Log_To']."$log_file <br /></b>".implode( "<br />", $output) . adm_back_link(append_sid("index.$phpEx", "i=garage_tool")));
	}
}

$garage_image = new garage_image();

?>

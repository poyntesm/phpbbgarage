<?php
/** 
*
* @package acp
* @version $Id$
* @copyright (c) 2006 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @package acp
*/
class acp_garage_tool
{
	var $u_action;

	function main($id, $mode)
	{
		/**
		* Setup global variables such as $db 
		*/
		global $db, $user, $auth, $template, $cache, $garage_config, $garage;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx;

		/**
		* Setup page variables such as title, template & available language strings
		*/
		$user->add_lang(array('acp/garage', 'mods/garage'));
		$this->tpl_name = 'acp_garage_tool';
		$this->page_title = 'ACP_MANAGE_FORUMS';

		/**
		* Build All Garage Classes e.g $garage_images->
		*/
		require($phpbb_root_path . 'includes/mods/class_garage_image.' . $phpEx);

		/**
		* Setup variables required
		*/
		$action		= request_var('action', '');
		$update		= (isset($_POST['update'])) ? true : false;
		@set_time_limit(1200);

		switch ($action)
		{
			/**
			* Rebuild a set number of thumbnails to new deminision
			*/
			case 'rebuild_thumbs':
				$start	= request_var('start', '0');
				$cycle	= request_var('cycle', '20');
				$file	= request_var('file', '');
				$done	= request_var('done', '0');
				$garage_image->rebuild_thumbs($start, $cycle, $done, $file);
			break;

			/**
			* Find all orphaned image files
			*/
			case 'orphan_search':
				$active_attach = $present_attach = $orphan_attch = array();
		
				$data = $garage_image->get_all_images();

				/**
				* Build array of all non remote images
				*/
				for( $i = 0; $i < count($data); $i++ )
				{
					if ( !preg_match("/^http:\/\//i", $data[$i]['attach_location']) )
					{
						$active_attach[] = $data[$i]['attach_location'];
					}

					if ( !preg_match("/^http:\/\//i", $data[$i]['attach_thumb_location']) )
					{
						$active_attach[] = $data[$i]['attach_thumb_location'];
					}
				}
		
				/**
				* Build array of all files stored in upload directory
				*/
				$upload_dir = opendir($phpbb_root_path . GARAGE_UPLOAD_PATH);
				while ( false !== ( $file = readdir($upload_dir) ) )
				{
					if ( ($file != "." && $file != "..") )
					{
						$present_attach[] = $file;
					}
				}
				closedir($upload_dir);

				/**
				* Difference two arrays to get orpahns
				*/
				$orphan_attach = array_diff($present_attach, $active_attach);
				
				if (sizeof($orphan_attach) <= 0)
				{
					trigger_error($user->lang['NO_ORPHANED_FILES'] . adm_back_link($this->u_action));
				}

				$this->tpl_name = 'acp_garage_orphans';
		
				foreach ($orphan_attach as $orphan_file)
				{
						$template->assign_block_vars('file', array(
							'ORPHAN_LINK' => $phpbb_root_path . GARAGE_UPLOAD_PATH . $orphan_file,
							'ORPHAN' => $orphan_file,
						));
				}

				$template->assign_vars(array(
					'S_ACTION' => $this->u_action . "&amp;action=orphan_remove",
				));
			break;

			/**
			* Remove orphan files from disk
			*/
			case 'orphan_remove':
				$output = $files = array();

				$files = request_var('orphan_attach');
		
			        if (!empty($files))
		        	{
					for( $i = 0; $i < count($files); $i++ )
					{
				                if ( @file_exists( $phpbb_root_path . GARAGE_UPLOAD_PATH . $files[$i] ) )
		        		        {
				        	        @unlink( $phpbb_root_path . GARAGE_UPLOAD_PATH . $files[$i] );
		                		}
					}
		
					trigger_error($user->lang['ORPHANED_FILES_REMOVED'] . adm_back_link($this->u_action));
				}
		
				trigger_error($user->lang['NO_ORPHANED_FILES_SELECTED'] . adm_back_link($this->u_action));
			break;
		}

		$template->assign_vars(array(
			'L_BASE_DIRECTORY' 		=> $phpbb_root_path . GARAGE_UPLOAD_PATH,
			'S_GARAGE_REBUILD_ACTION'	=> $this->u_action . "&amp;action=rebuild_thumbs",
			'S_GARAGE_ORPHAN_ACTION'	=> $this->u_action . "&amp;action=orphan_search",
			'CYCLE'				=> '20',
		));

	}
}
?>

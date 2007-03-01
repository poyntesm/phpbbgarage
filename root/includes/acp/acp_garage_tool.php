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
		global $db, $user, $auth, $template, $cache, $garage_config, $garage;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx;

		$user->add_lang('acp/garage');
		$this->tpl_name = 'acp_garage_tool';
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
					'L_BASE_DIRECTORY' => $phpbb_root_path . GARAGE_UPLOAD_PATH,
					'S_GARAGE_ACTION' => append_sid('admin_garage_tools.'.$phpEx),
					'CYCLE' => '20')
				);
		
				break;
		}
	}
}
?>

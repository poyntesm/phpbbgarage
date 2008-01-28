<?php
/** 
*
* @package acp
* @version $Id: $
* @copyright (c) 2006 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package acp
*/
class acp_garage_track
{
	var $u_action;

	function main($id, $mode)
	{
		/**
		* Setup global variables such as $db 
		*/
		global $db, $user, $auth, $template, $cache, $garage, $garage_config;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx, $tid;

		/**
		* Build All Garage Classes e.g $garage_images->
		*/
		require($phpbb_root_path . 'includes/mods/class_garage_track.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);

		/**
		* Setup page variables such as title, template & available language strings
		*/
		$user->add_lang(array('acp/garage', 'mods/garage'));
		$this->tpl_name = 'acp_garage_track';
		$this->page_title = 'ACP_MANAGE_TRACKS';

		/**
		* Setup variables required
		*/
		$action		= request_var('action', '');
		$update		= (isset($_POST['update'])) ? true : false;
		$track_id	= request_var('id', 0);
		$errors 	= array();

		/**
		* Perform a set action based on value for $action
		* An action is normally a DB action such as insert/update/delete
		* An action will only show a page to show success or failure
		*/
		if ($update)
		{
			switch ($action)
			{
				/**
				* Add a new track
				*/
				case 'add':
					$params = array('length' => '', 'mileage_unit' => '');
					$data = $garage->process_vars($params);
					$params = array('title' => '');
					$data += $garage->process_mb_vars($params);

					$garage_track->insert_track($data);
					add_log('admin', 'LOG_GARAGE_TRACK_CREATED', $data['title']);

					trigger_error($user->lang['TRACK_CREATED'] . adm_back_link($this->u_action));
				break;

				/**
				* Update an existing category
				*/
				case 'edit':
					$params = array('length' => '', 'mileage_unit' => '');
					$data = $garage->process_vars($params);
					$params = array('title' => '');
					$data += $garage->process_mb_vars($params);
					$tid = $track_id;

					if(!$data['title'])
					{
						$errors[] = $user->lang['TRACK_NAME_EMPTY'];
						break;
					}

					$garage_track->update_track($data);
					add_log('admin', 'LOG_GARAGE_TRACK_UPDATED', $data['title']);

					trigger_error($user->lang['TRACK_UPDATED'] . adm_back_link($this->u_action));
				break;

				/**
				* Delete an existing catgory
				*/
				case 'delete':
					$action_laps	= request_var('action_laps', '');
					$laps_to_id	= request_var('laps_to_id', 0);

					$errors = $garage_track->delete_track($track_id, $action_laps, $laps_to_id);

					if (sizeof($errors))
					{
						break;
					}

					trigger_error($user->lang['TRACK_DELETED'] . adm_back_link($this->u_action));
				break;
			}
		}

		/**
		* Perform a set action based on value for $action
		*/
		switch ($action)
		{
			/**
			* Approve a track & log it
			*/
			case 'approve':
				$data = $garage_track->get_track($track_id);
				$garage->update_single_field(GARAGE_TRACKS_TABLE, 'pending', 0, 'id', $track_id);
				add_log('admin', 'LOG_GARAGE_TRACK_APPROVED', $data['title']);
			break;

			/**
			* Disapprove a business & log it
			*/
			case 'disapprove':
				$data = $garage_track->get_track($track_id);
				$garage->update_single_field(GARAGE_TRACKS_TABLE, 'pending', 1, 'id', $track_id);
				add_log('admin', 'LOG_GARAGE_TRACK_DISAPPROVED', $data['title']);
			break;

			/**
			* Display page to add or edit a track
			*/
			case 'add':
			case 'edit':
				if ($action == 'edit')
				{
					$this->page_title = 'EDIT_TRACK';
					$row = $garage_track->get_track($track_id);

					if (!$update)
					{
						$track_data = $row;
					}
				}
				else
				{
					$this->page_title = 'CREATE_TRACK';

					/**
					* Fill track data with default values
					*/
					if (!$update)
					{
						$track_data = array(
							'title'		=> request_var('title', '', true),
							'length'	=> '',
							'mileage_unit'	=> '',
						);
					}
				}

				$garage_template->mileage_dropdown($track_data['mileage_unit']);
				$template->assign_vars(array(
					'S_EDIT_TRACK'		=> true,
					'S_ERROR'		=> (sizeof($errors)) ? true : false,
					'U_BACK'		=> $this->u_action,
					'U_EDIT_ACTION'		=> $this->u_action . "&amp;action=$action&amp;id=$track_id",
					'TRACK_NAME'		=> $track_data['title'],
					'LENGTH'		=> $track_data['length'],
					'MILEAGE_UNIT'		=> $track_data['mileage_unit'],
					'ERROR_MSG'		=> (sizeof($errors)) ? implode('<br />', $errors) : '',
					)
				);
				return;
			break;

			/**
			* Display page to handle deletion of a track
			* Administrator's will have a choice to move linked items 
			*/
			case 'delete':
				if (!$track_id)
				{
					trigger_error($user->lang['NO_TRACK'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$track_data = $garage_track->get_track($track_id);
				$tracks_data = $garage_track->get_all_tracks();

				$select_to = $garage_template->build_move_to($tracks_data, $track_id, 'title');
				$template->assign_vars(array(
					'S_MOVE'		=> (!empty($select_to)) ? true : false,
					'S_MOVE_OPTIONS'	=> $select_to,
					'S_DELETE_TRACK'		=> true,
					'U_ACTION'			=> $this->u_action . "&amp;action=delete&amp;id=$track_id",
					'U_BACK'			=> $this->u_action,
					'TRACK_NAME'			=> $track_data['title'],
					'S_ERROR'			=> (sizeof($errors)) ? true : false,
					'ERROR_MSG'			=> (sizeof($errors)) ? implode('<br />', $errors) : '',
				));
			break;	
		
		}
		
		/**
		* Display default page to show list of tracks
		*/	
		$data = $garage_track->get_all_tracks();
		for($i = 0; $i < count($data); $i++)
		{
			$url = $this->u_action . "&amp;id={$data[$i]['id']}";
			$template->assign_block_vars('track', array(
				'ID' 			=> $data[$i]['id'],
				'TITLE' 		=> $data[$i]['title'],
				'S_DISAPPROVED'		=> ($data[$i]['pending'] == 1) ? true : false,
				'S_APPROVED'		=> ($data[$i]['pending'] == 0) ? true : false,
				'U_APPROVE'		=> $url . '&amp;action=approve',
				'U_DISAPPROVE'		=> $url . '&amp;action=disapprove',
				'U_EDIT'		=> $url . '&amp;action=edit',
				'U_DELETE'		=> $url . '&amp;action=delete',
			));
		}
	}
}
?>

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
* @ignore
*/
define('IN_PHPBB', true);

/**
* Set root path & include standard phpBB files required
*/
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);
require($phpbb_root_path . 'includes/functions_display.' . $phpEx);

/**
* Setup user session, authorisation & language 
*/
$user->session_begin();
$auth->acl($user->data);
$user->setup(array('mods/garage'));

/**
* Check For Garage Install Files
*/
$garage->check_installation_files();

/**
* Build All Garage Classes e.g $garage_images->
*/
require($phpbb_root_path . 'includes/mods/class_garage_image.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_track.' . $phpEx);

/**
* Setup variables 
*/
$mode = request_var('mode', '');
$vid = request_var('VID', '');
$lid = request_var('LID', '');
$tid = request_var('TID', '');
$image_id = request_var('image_id', '');

/**
* Build inital navlink..we use the standard phpBB3 breadcrumb process
*/
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['GARAGE'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx"))
);

/**
* Display the moderator control panel link if authorised
*/
if ($garage->mcp_access())
{
	$template->assign_vars(array(
		'U_MCP'	=> append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=garage', true, $user->session_id),
	));
}

/**
* Perform a set action based on value for $mode
*/
switch( $mode )
{
	/**
	* Display page to add new lap
	*/
	case 'add_lap':
		/**
		* Check user logged in, else redirecting to login with return address to get them back
		*/
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_track.$phpEx?mode=add_lap&amp;VID=$vid");
		}

		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$garage_config['enable_tracktime'] || !$auth->acl_get('u_garage_add_lap'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Get vehicle & tracks data from DB
		*/
		$vehicle=$garage_vehicle->get_vehicle($vid);
		$tracks = $garage_track->get_all_tracks();

		/**
		* Get all required/optional data and check required data is present
		*/
		$params = array('VID' => '', 'track_id' => '', 'length' => '', 'mileage_unit' => '', 'condition_id' => '', 'type_id' => '', 'minute' => '', 'second' => '', 'millisecond' => '', 'redirect' => '', 'url_image' => '');
		$data 	= $garage->process_vars($params);
		$params = array('title' => '');
		$data 	+= $garage->process_mb_vars($params);

		/**
		* Handle template declarations & assignments
		*/
		page_header($user->lang['GARAGE']);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_lap.html')
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $vehicle['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"))
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['ADD_LAP'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=add_lap&amp;VID=$vid"))
		);
		$garage_template->attach_image('lap');
		$garage_template->track_dropdown($tracks, $data['track_id']);
		$garage_template->track_condition_dropdown($data['condition_id']);
		$garage_template->lap_type_dropdown($data['type_id']);
		$template->assign_vars(array(
			'L_TITLE'  		=> $user->lang['ADD_LAP'],
			'L_BUTTON'  		=> $user->lang['ADD_LAP'],
			'U_ADD_TRACK'		=> 'javascript:add_track()',
			'VID' 			=> $vid,
			'MINUTE' 		=> $data['minute'],
			'SECOND' 		=> $data['second'],
			'MILLISECOND' 		=> $data['millisecond'],
			'URL_IMAGE'		=> $data['url_image'],
			'S_DISPLAY_ADD_TRACK'	=> $garage_config['enable_user_add_track'],
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=insert_lap"),
			'S_MODE_USER_SUBMIT' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_data"),
         	));
		$garage_template->sidemenu();
	break;

	/**
	* Insert new lap
	*/
	case 'insert_lap':
		/**
		* Check user logged in, else redirecting to login with return address to get them back
		*/
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_track.$phpEx?mode=add_lap&amp;VID=$vid");
		}

		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$garage_config['enable_tracktime'] || !$auth->acl_get('u_garage_add_lap'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Get all required/optional data and check required data is present
		*/
		$params = array('track_id' => '', 'condition_id' => '', 'type_id' => '', 'minute' => '', 'second' => '', 'millisecond' => '');
		$data 	= $garage->process_vars($params);
		$params = array('track_id', 'condition_id', 'type_id', 'minute', 'second', 'millisecond');
		$garage->check_required_vars($params);

		/**
		* Perform required DB work to create lap
		*/
		$lid = $garage_track->insert_lap($data);

		/**
		* Updates timestamp on vehicle, indicating it has been updated.
		* Updated vehicles are displayed on statistics page
		*/
		$garage_vehicle->update_vehicle_time($vid);

		/**
		* Handle any images
		*/
		if ($garage_image->image_attached())
		{
			if ($garage_image->below_image_quotas())
			{
				$image_id = $garage_image->process_image_attached('lap', $lid);
				$hilite = $garage_track->hilite_exists($lid);
				$garage_image->insert_lap_gallery_image($image_id, $hilite);
			}
			else if ($garage_image->above_image_quotas())
			{
				redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=4"));
			}
		}

		/**
		* Perform notification if required
		*/
		if ($garage_config['enable_lap_approval'])
		{
			$garage->pending_notification('unapproved_laps');
		}

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"));
	break;

	/**
	* Insert new track
	*/
	case 'insert_track':
		/**
		* Check user logged in, else redirecting to login with return address to get them back
		*/
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_track.$phpEx?mode=add_lap&amp;VID=$vid");
		}

		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$garage_config['enable_tracktime'] || !$auth->acl_get('u_garage_add_track'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Get all required/optional data and check required data is present
		*/
		$params = array('VID' => '', 'LID' => '', 'length' => '', 'mileage_unit' => '', 'condition_id' => '', 'type_id' => '', 'minute' => '', 'second' => '', 'millisecond' => '', 'redirect' => '', 'primary' => '', 'secondary' => '', 'tertiary' => '');
		$data 	= $garage->process_vars($params);
		$params = array('title' => '');
		$data 	+= $garage->process_mb_vars($params);
		$params = array('title');
		$garage->check_required_vars($params);

		/**
		* Perform required DB work to create track
		*/
		$tid = $garage_track->insert_track($data);
		$data['track_id'] = $tid;

		/**
		* Perform notification if required
		*/
		if ($garage_config['enable_track_approval'])
		{
			$garage->pending_notification('unapproved_tracks');
		}

		//Now rather than redirect.. we build a page with all data
		page_header($user->lang['GARAGE']);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' 	=> 'garage_header.html',
			'body'   	=> 'garage_user_submit_data.html')
		);

		$user_submit_action = append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=add_lap");
		if ($data['tertiary'] == "edit")
		{
			$user_submit_action = append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=edit_lap&amp;VID={$data['VID']}&amp;LID={$data['LID']}");
		}

		$template->assign_vars(array(
			'L_BUTTON_LABEL'		=> $user->lang['RETURN_TO_ITEM'],
			'S_USER_SUBMIT_SUCCESS'		=> true,
			'S_USER_SUBMIT_ACTION'		=> $user_submit_action,
		));

		foreach ($data as $key => $value)
		{
			if (empty($value))
			{
				continue;
			}
			$template->assign_block_vars('hidden_data', array(
				'VALUE'	=> $value,
				'NAME'	=> $key,
			));
		}

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

	break;

	/**
	* Display page to edit an existing lap
	*/
	case 'edit_lap':
		/**
		* Check user logged in, else redirecting to login with return address to get them back
		*/
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_track.$phpEx?mode=edit_lap&amp;LID=$lid&amp;VID=$vid");
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Get any changed data incase we are arriving from creating a track
		*/
		$params = array('track_id' => '', 'condition_id' => '', 'type_id' => '', 'minute' => '', 'second' => '', 'millisecond' => '', 'redirect' => '');
		$store 	= $garage->process_vars($params);

		/**
		* Get vehicle, tracks & lap data from DB
		*/
		$vehicle_data 	= $garage_vehicle->get_vehicle($vid);
		$data 		= $garage_track->get_lap($lid);
		$tracks 	= $garage_track->get_all_tracks();
		$gallery_data 	= $garage_image->get_lap_gallery($lid);

		/**
		* Handle template declarations & assignments
		*/
		page_header($user->lang['GARAGE']);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_lap.html')
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $vehicle_data['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"))
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['EDIT_LAP'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=edit_lap&amp;VID=$vid&amp;LID=$lid"))
		);
		$garage_template->track_dropdown($tracks, (!empty($store['track_id'])) ? $store['track_id'] : $data['track_id']);
		$garage_template->track_condition_dropdown((!empty($store['condition_id'])) ? $store['condition_id'] : $data['condition_id']);
		$garage_template->lap_type_dropdown((!empty($store['type_id'])) ? $store['type_id'] : $data['type_id']);
		$template->assign_vars(array(
			'L_TITLE'  		=> $user->lang['EDIT_LAP'],
			'L_BUTTON'  		=> $user->lang['EDIT_LAP'],
			'U_EDIT_DATA' 		=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=edit_lap&amp;VID=$vid&amp;LID=$lid"),
			'U_MANAGE_GALLERY' 	=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=edit_lap&amp;VID=$vid&amp;LID=$lid#images"),
			'U_ADD_TRACK'		=> "javascript:add_track('edit')",
			'MINUTE' 		=> (!empty($store['minute'])) ? $store['minute'] : $data['minute'],
			'SECOND' 		=> (!empty($store['second'])) ? $store['second'] : $data['second'],
			'MILLISECOND' 		=> (!empty($store['millisecond'])) ? $store['millisecond'] : $data['millisecond'],
			'VID' 			=> $vid,
			'LID' 			=> $lid,
			'S_DISPLAY_ADD_TRACK'	=> $garage_config['enable_user_add_track'],
			'S_MODE_USER_SUBMIT' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_data"),
			'REDIRECT' 		=> $store['redirect'],
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=update_lap"),
			'S_IMAGE_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=insert_lap_image"),
		));
		$garage_template->attach_image('lap');
		for ($i = 0, $count = sizeof($gallery_data);$i < $count; $i++)
		{
			$template->assign_block_vars('pic_row', array(
				'U_IMAGE'	=> (($gallery_data[$i]['attach_id']) AND ($gallery_data[$i]['attach_is_image']) AND (!empty($gallery_data[$i]['attach_thumb_location'])) AND (!empty($gallery_data[$i]['attach_location']))) ? append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_image&amp;image_id=" . $gallery_data[$i]['attach_id']) : '',
				'U_REMOVE_IMAGE'=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=remove_lap_image&amp;&amp;VID=$vid&amp;LID=$lid&amp;image_id=" . $gallery_data[$i]['attach_id']),
				'U_SET_HILITE'	=> ($gallery_data[$i]['hilite'] == 0) ? append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=set_lap_hilite&amp;image_id=" . $gallery_data[$i]['attach_id'] . "&amp;VID=$vid&amp;LID=$lid") : '',
				'IMAGE' 	=> $phpbb_root_path . GARAGE_UPLOAD_PATH . $gallery_data[$i]['attach_thumb_location'],
				'IMAGE_TITLE' 	=> $gallery_data[$i]['attach_file'])
			);
		}
		$garage_template->sidemenu();
	break;

	/**
	* Update existing lap
	*/
	case 'update_lap':
		/**
		* Check user logged in, else redirecting to login with return address to get them back
		*/
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_track.$phpEx?mode=edit_lap&amp;LID=$lid&amp;VID=$vid");
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Get all required/optional data and check required data is present
		*/
		$params = array('track_id' => '', 'condition_id' => '', 'type_id' => '', 'minute' => '', 'second' => '', 'millisecond' => '', 'redirect' => '');
		$data 	= $garage->process_vars($params);
		$params = array('track_id', 'condition_id', 'type_id', 'minute', 'second', 'millisecond');
		$garage->check_required_vars($params);

		/**
		* Perform required DB work to update lap
		*/
		$garage_track->update_lap($data);

		/**
		* Updates timestamp on vehicle, indicating it has been updated.
		* Updated vehicles are displayed on statistics page
		*/
		$garage_vehicle->update_vehicle_time($vid);

		/**
		* Perform notification if required
		*/
		if ($garage_config['enable_lap_approval'])
		{
			$garage->pending_notification('unapproved_laps');
		}

		/**
		* If editted by MCP redirect back to MCP
		*/
		if ($data['redirect'] == 'MCP')
		{
			redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_laps"));
		}

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"));
	break;

	/**
	* Delete existing lap
	*/
	case 'delete_lap':
		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$auth->acl_get('u_garage_delete_lap'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Perform required DB work to delete lap
		*/
		$garage_track->delete_lap($lid);

		/**
		* Updates timestamp on vehicle, indicating it has been updated.
		* Updated vehicles are displayed on statistics page
		*/
		$garage_vehicle->update_vehicle_time($vid);

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"));
	break;

	/**
	* Insert image into lap
	*/
	case 'insert_lap_image':
		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if ((!$auth->acl_get('u_garage_upload_image')) OR (!$auth->acl_get('u_garage_remote_image')))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=16"));
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Handle any images
		*/
		if ($garage_image->image_attached())
		{
			if ($garage_image->below_image_quotas())
			{
				$image_id = $garage_image->process_image_attached('lap', $lid);
				$hilite = $garage_track->hilite_exists($lid);
				$garage_image->insert_lap_gallery_image($image_id, $hilite);
			}
			else if ($garage_image->above_image_quotas())
			{
				redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=4"));
			}
		}

		/**
		* Updates timestamp on vehicle, indicating it has been updated.
		* Updated vehicles are displayed on statistics page
		*/
		$garage_vehicle->update_vehicle_time($vid);

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=edit_lap&amp;VID=$vid&amp;LID=$lid#images"));
	break;

	/**
	* Set highlight image for lap
	*/
	case 'set_lap_hilite':
		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Perform required DB work to set hightlight image
		*/
		$garage->update_single_field(GARAGE_LAP_GALLERY_TABLE, 'hilite', 0, 'lap_id', $lid);
		$garage->update_single_field(GARAGE_LAP_GALLERY_TABLE, 'hilite', 1, 'image_id', $image_id);

		/**
		* Updates timestamp on vehicle, indicating it has been updated.
		* Updated vehicles are displayed on statistics page
		*/
		$garage_vehicle->update_vehicle_time($vid);

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=edit_lap&amp;VID=$vid&amp;LID=$lid#images"));
	break;

	/**
	* Delete lap image
	*/
	case 'remove_lap_image':
		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Perform required DB work to delete image
		*/
		$garage_image->delete_lap_image($image_id);

		/**
		* Updates timestamp on vehicle, indicating it has been updated.
		* Updated vehicles are displayed on statistics page
		*/
		$garage_vehicle->update_vehicle_time($vid);

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=edit_lap&amp;VID=$vid&amp;LID=$lid#images"));
	break;

	/**
	* Display page to view lap
	*/
	case 'view_lap':
		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		/**
		* Get lap & gallery data from DB
		*/
		$data = $garage_track->get_lap($lid);
		$gallery_data = $garage_image->get_lap_gallery($lid);

		/**
		* Handle template declarations & assignments
		*/
		page_header($user->lang['GARAGE']);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_view_lap.html')
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $data['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;VID=$vid"))
		);
       		for ( $i = 0; $i < count($gallery_data); $i++ )
        	{
			if ( (empty($gallery_data[$i]['attach_thumb_location']) == false) AND ($gallery_data[$i]['attach_thumb_location'] != $gallery_data[$i]['attach_location']) )
			{
				$template->assign_vars(array(
					'S_DISPLAY_GALLERIES' 	=> true,
				));

				$template->assign_block_vars('lap_image', array(
					'U_IMAGE' 	=> append_sid('garage.'.$phpEx.'?mode=view_image&amp;image_id='. $gallery_data[$i]['attach_id']),
					'IMAGE_NAME'	=> $gallery_data[$i]['attach_file'],
					'IMAGE_SOURCE'	=> $phpbb_root_path . GARAGE_UPLOAD_PATH . $gallery_data[$i]['attach_thumb_location'])
				);
               		} 
		}

		$template->assign_vars(array(
			'U_VIEW_PROFILE' 	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $data['user_id']),
			'U_VIEW_TRACK' 		=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=view_track&amp;TID=" . $data['track_id']),
			'TRACK'			=> $data['title'],
			'CONDITION'		=> $garage_track->get_track_condition($data['condition_id']),
			'TYPE'			=> $garage_track->get_lap_type($data['type_id']),
			'MINUTE'		=> $data['minute'],
			'SECOND'		=> $data['second'],
			'MILLISECOND'		=> $data['millisecond'],
			'YEAR' 			=> $data['made_year'],
			'MAKE' 			=> $data['make'],
			'MODEL' 		=> $data['model'],
			'USERNAME' 		=> $data['username'],
			'USERNAME_COLOUR'	=> get_username_string('colour', $data['user_id'], $data['username'], $data['user_colour']),
            		'AVATAR_IMG' 		=> ($user->optionget('viewavatars')) ? get_user_avatar($data['user_avatar'], $data['user_avatar_type'], $data['user_avatar_width'], $data['user_avatar_height']) : '',
         	));
		$garage_template->sidemenu();
	break;

	/**
	* Display page to view track
	*/
	case 'view_track':
		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		/**
		* Get track & laps data from DB
		*/
		$data = $garage_track->get_track($tid);
		$lap_data = $garage_track->get_laps_by_track($tid);

		/**
		* Handle template declarations & assignments
		*/
		page_header($user->lang['GARAGE']);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_view_track.html')
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $data['title'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=view_track&amp;TID=$tid"))
		);
       		for ( $i = 0; $i < count($lap_data); $i++ )
		{

			$template->assign_vars(array(
				'S_DISPLAY_LAPS' 	=> true,
			));

			$template->assign_block_vars('lap', array(
				'CONDITION'	=> $garage_track->get_track_condition($lap_data[$i]['condition_id']),
				'TYPE'		=> $garage_track->get_lap_type($lap_data[$i]['type_id']),
				'MINUTE'	=> $lap_data[$i]['minute'],
				'SECOND'	=> $lap_data[$i]['second'],
				'MILLISECOND'	=> $lap_data[$i]['millisecond'],
				'USERNAME'	=> $lap_data[$i]['username'],
				'USERNAME_COLOUR'	=> get_username_string('colour', $lap_data[$i]['user_id'], $lap_data[$i]['username'], $lap_data[$i]['user_colour']),
				'VEHICLE'	=> $lap_data[$i]['vehicle'],
				'IMAGE'		=> $user->img('garage_img_attached', 'IMAGE_ATTACHED'),
				'U_IMAGE'	=> ($lap_data[$i]['attach_id']) ? append_sid("garage.$phpEx", "mode=view_image&amp;image_id=". $lap_data[$i]['attach_id']) : '',
				'U_VIEWPROFILE'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $lap_data[$i]['user_id']),
				'U_VIEWVEHICLE'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;VID=" . $lap_data[$i]['vehicle_id']),
				'U_LAP'		=> append_sid("garage_track.$phpEx?mode=view_lap&amp;LID=".$lap_data[$i]['lid']."&amp;VID=". $lap_data[$i]['vehicle_id']),
			));

			if ( (empty($lap_data[$i]['attach_thumb_location']) == false) AND ($lap_data[$i]['attach_thumb_location'] != $lap_data[$i]['attach_location']) )
			{
				$template->assign_vars(array(
					'S_DISPLAY_GALLERIES' 	=> true,
				));

				$template->assign_block_vars('track_image', array(
					'U_IMAGE' 	=> append_sid('garage.'.$phpEx.'?mode=view_image&amp;image_id='. $lap_data[$i]['attach_id']),
					'IMAGE_NAME'	=> $lap_data[$i]['attach_file'],
					'IMAGE_SOURCE'	=> $phpbb_root_path . GARAGE_UPLOAD_PATH . $lap_data[$i]['attach_thumb_location'])
				);
               		} 
	       	}
		$template->assign_vars(array(
			'TRACK'			=> $data['title'],
			'LENGTH'		=> $data['length'],
			'MILEAGE_UNIT'		=> $data['mileage_unit'],
         	));
		$garage_template->sidemenu();
	break;
}
$garage_template->version_notice();

$template->set_filenames(array(
	'garage_footer' => 'garage_footer.html')
);

page_footer();
?>

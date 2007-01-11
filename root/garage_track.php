<?php
/***************************************************************************
 *                              garage_track.php
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

//Let's Set The Root Dir For phpBB And Load Normal phpBB Required Files
$phpbb_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);

//Start Session Management
$user->session_begin();
$auth->acl($user->data);

//Setup Lang Files
$user->setup(array('mods/garage'));

//Build All Garage Classes e.g $garage_images->
require($phpbb_root_path . 'includes/mods/class_garage_image.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_track.' . $phpEx);

//Set The Page Title
$page_title = $user->lang['GARAGE'];

//Get All String Parameters And Make Safe
$params = array('mode' => 'mode', 'sort' => 'sort', 'start' => 'start', 'order' => 'order');
while(list($var, $param) = @each($params))
{
	$$var = request_var($param, '');
}

//Get All Non-String Parameters
$params = array('cid' => 'CID', 'eid' => 'EID', 'image_id' => 'image_id', 'tid' => 'TID', 'lid' => 'LID');
while(list($var, $param) = @each($params))
{
	$$var = request_var($param, '');
}

//Build Inital Navlink...Yes Forum Name!! We Use phpBB3 Standard Navlink Process!!
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['GARAGE'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx"))
);

//Display MCP Link If Authorised
$template->assign_vars(array(
	'U_MCP'	=> ($auth->acl_get('m_garage')) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=garage', true, $user->session_id) : '')
);

//Decide What Mode The User Is Doing
switch( $mode )
{
	case 'add_lap':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_track.$phpEx?mode=add_lap&amp;CID=$cid");
		}

		//Let Check That Laps Are Allowed...If Not Redirect
		if (!$garage_config['enable_tracktime'] || !$auth->acl_get('u_garage_add_lap'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);
		
		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_lap.html')
		);

		//Get Vehicle Data For Navlinks
		$vehicle=$garage_vehicle->get_vehicle($cid);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $vehicle['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"))
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['ADD_LAP'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=add_lap&amp;CID=$cid"))
		);

		//Get Required Data
		$tracks = $garage_track->get_all_tracks();

		//Build Required HTML Components Like Drop Down Boxes.....
		$garage_template->attach_image('lap');
		$garage_template->track_dropdown($tracks);
		$garage_template->track_condition_dropdown();
		$garage_template->lap_type_dropdown();
		$template->assign_vars(array(
			'L_TITLE'  		=> $user->lang['ADD_LAP'],
			'L_BUTTON'  		=> $user->lang['ADD_LAP'],
			'U_ADD_TRACK'		=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=add_track&amp;CID=$cid&amp;redirect=add_lap"),
			'CID' 			=> $cid,
			'S_DISPLAY_ADD_TRACK'	=> $garage_config['enable_user_add_track'],
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=insert_lap"))
         	);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'add_track':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_track.$phpEx?mode=add_track&amp;CID=$cid");
		}

		//Let Check That User Adding Tracks Are Allowed...If Not Redirect
		if (!$garage_config['enable_tracktime'] || !$garage_config['enable_user_add_track'] || !$auth->acl_get('u_garage_add_track'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);
		
		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_track.html')
		);

		//Get Vehicle Data For Navlinks
		$vehicle=$garage_vehicle->get_vehicle($cid);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $vehicle['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"))
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['ADD_TRACK'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=add_track&amp;CID=$cid"))
		);

		//Build Required HTML Components Like Drop Down Boxes.....
		$garage_template->mileage_dropdown();
		$template->assign_vars(array(
			'L_TITLE'  		=> $user->lang['ADD_TRACK'],
			'L_BUTTON'  		=> $user->lang['ADD_TRACK'],
			'CID' 			=> $cid,
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=insert_track"))
         	);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'insert_lap':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_track.$phpEx?mode=add_lap&amp;CID=$cid");
		}

		//Let Check That Laps Are Allowed...If Not Redirect
		if (!$garage_config['enable_tracktime'] || !$auth->acl_get('u_garage_add_lap'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('track_id' => '', 'condition_id' => '', 'type_id' => '', 'minute' => '', 'second' => '', 'millisecond' => '');
		$data 	= $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('track_id', 'condition_id', 'type_id', 'minute', 'second', 'millisecond');
		$garage->check_required_vars($params);

		//Update The Dynorun With Data Acquired
		$lid = $garage_track->insert_lap($data);

		//Update The Time Now...In Case We Get Redirected During Image Processing
		$garage_vehicle->update_vehicle_time($cid);

		//If Any Image Variables Set Enter The Image Handling
		if ($garage_image->image_attached())
		{
			//Check For Remote & Local Image Quotas
			if ($garage_image->below_image_quotas())
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('lap', $lid);
				//Insert Image Into Lap Gallery
				$hilite = $garage_track->hilite_exists($cid, $lid);
				$garage_image->insert_lap_gallery_image($image_id, $hilite);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ($garage_image->above_image_quotas())
			{
				redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=4"));
			}
		}

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ($garage_config['enable_lap_approval'])
		{
			$garage->pending_notification('unapproved_laps');
		}

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

	case 'insert_track':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_track.$phpEx?mode=add_lap&amp;CID=$cid");
		}

		//Let Check That Laps Are Allowed...If Not Redirect
		if (!$garage_config['enable_tracktime'] || !$auth->acl_get('u_garage_add_track'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('title' => '', 'length' => '', 'mileage_unit' => '');
		$data 	= $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('title');
		$garage->check_required_vars($params);

		//Update The Dynorun With Data Acquired
		$tid = $garage_track->insert_track($data);

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ($garage_config['enable_track_approval'])
		{
			$garage->pending_notification('unapproved_tracks');
		}

		redirect(append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=add_lap&amp;CID=$cid"));

		break;

	case 'edit_lap':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_track.$phpEx?mode=edit_lap&amp;LID=$lid&amp;CID=$cid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_lap.html')
		);

		//Pull Required Lap Data From DB
		$data = $garage_track->get_lap($lid);

		//See If We Got Sent Here By Pending Page...If So We Need To Tell Update To Redirect Correctly
		$params = array('PENDING' => '');
		$redirect = $garage->process_vars($params);

		//Build All Required HTML
		$tracks = $garage_track->get_all_tracks();

		//Build Required HTML Components Like Drop Down Boxes.....
		$garage_template->track_dropdown($tracks, $data['track_id']);
		$garage_template->track_condition_dropdown($data['condition_id']);
		$garage_template->lap_type_dropdown($data['type_id']);
		$template->assign_vars(array(
			'L_TITLE'  		=> $user->lang['EDIT_LAP'],
			'L_BUTTON'  		=> $user->lang['EDIT_LAP'],
			'U_EDIT_DATA' 		=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=edit_lap&amp;CID=$cid&amp;LID=$lid"),
			'U_MANAGE_GALLERY' 	=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=manage_lap_gallery&amp;CID=$cid&amp;LID=$lid"),
			'MINUTE' 		=> $data['minute'],
			'SECOND' 		=> $data['second'],
			'MILLISECOND' 		=> $data['millisecond'],
			'CID' 			=> $cid,
			'LID' 			=> $lid,
			'PENDING_REDIRECT'	=> $redirect['PENDING'],
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=update_lap"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'update_lap':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_track.$phpEx?mode=edit_lap&amp;LID=$lid&amp;CID=$cid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('track_id' => '', 'condition_id' => '', 'type_id' => '', 'minute' => '', 'second' => '', 'millisecond' => '', 'pending_redirect' => '');
		$data 	= $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('track_id', 'condition_id', 'type_id', 'minute', 'second', 'millisecond');
		$garage->check_required_vars($params);

		//Update The Dynorun With Data Acquired
		$garage_track->update_lap($data);

		//Update The Time Now...In Case We Get Redirected During Image Processing
		$garage_vehicle->update_vehicle_time($cid);

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ($garage_config['enable_lap_approval'])
		{
			$garage->pending_notification('unapproved_laps');
		}

		//If Editting From Pending Page Redirect Back To There Instead
		if ($data['pending_redirect'] == 'MCP')
		{
			redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_laps"));
		}

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

	case 'delete_lap':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_delete_lap'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Delete The Dynorun
		$garage_track->delete_lap($lid);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

	case 'insert_lap_image':

		//Let Check The User Is Allowed Perform This Action
		if ((!$auth->acl_get('u_garage_upload_image')) OR (!$auth->acl_get('u_garage_remote_image')))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=16"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//If Any Image Variables Set Enter The Image Handling
		if ($garage_image->image_attached())
		{
			//Check For Remote & Local Image Quotas
			if ($garage_image->below_image_quotas())
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('lap', $lid);
				//Insert Image Into Lap Gallery
				$hilite = $garage_track->hilite_exists($lid);
				$garage_image->insert_lap_gallery_image($image_id, $hilite);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ($garage_image->above_image_quotas())
			{
				redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=4"));
			}
		}

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=manage_lap_gallery&amp;CID=$cid&amp;LID=$lid"));

		break;

	case 'manage_lap_gallery':

		//Let Check The User Is Allowed Perform This Action
		if ((!$auth->acl_get('u_garage_upload_image')) OR (!$auth->acl_get('u_garage_remote_image')))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=16"));
		}

		//Check Vehicle Ownership		
		$garage_vehicle->check_ownership($cid);

		//Build Page Header ;)
		page_header($page_title);
		
		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_manage_gallery.html')
		);

		//Pre Build All Side Menus
		$garage_template->attach_image('lap');

		//Pull Lap Gallery Data From DB
		$data = $garage_image->get_lap_gallery($cid, $lid);

		//Process Each Image From Lap Gallery
		for ($i = 0, $count = sizeof($data);$i < $count; $i++)
		{
			$template->assign_block_vars('pic_row', array(
				'U_IMAGE'	=> (($data[$i]['attach_id']) AND ($data[$i]['attach_is_image']) AND (!empty($data[$i]['attach_thumb_location'])) AND (!empty($data[$i]['attach_location']))) ? append_sid("{$phpbb_root_path}garage. $phpEx", "?mode=view_image&amp;image_id=" . $data[$i]['attach_id']) : '',
				'U_REMOVE_IMAGE'=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=remove_lap_image&amp;&amp;CID=$cid&amp;LID=$lid&amp;image_id=" . $data[$i]['attach_id']),
				'U_SET_HILITE'	=> ($data[$i]['hilite'] == 0) ? append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=set_lap_hilite&amp;image_id=" . $data[$i]['attach_id'] . "&amp;CID=$cid&amp;LID=$lid") : '',
				'IMAGE' 	=> $phpbb_root_path . GARAGE_UPLOAD_PATH . $data[$i]['attach_thumb_location'],
				'IMAGE_TITLE' 	=> $data[$i]['attach_file'])
			);
		}

		$template->assign_vars(array(
			'U_EDIT_DATA' 		=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=edit_lap&amp;CID=$cid&amp;LID=$lid"),
			'U_MANAGE_GALLERY' 	=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=manage_lap_gallery&amp;CID=$cid&amp;LID=$lid"),
			'LID' => $lid,
			'CID' => $cid,
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=insert_lap_image"))
         	);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();		

		break;

	case 'set_lap_hilite':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Set All Images To Non Hilite So We Do Not End Up With Two Hilites & Then Set Hilite
		$garage->update_single_field(GARAGE_LAP_GALLERY_TABLE, 'hilite', 0, 'lap_id', $lid);
		$garage->update_single_field(GARAGE_LAP_GALLERY_TABLE, 'hilite', 1, 'image_id', $image_id);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=manage_lap_gallery&amp;CID=$cid&amp;LID=$lid"));

		break;

	case 'remove_lap_image':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Remove Image From Lap Gallery & Deletes Image
		$garage_image->delete_lap_image($image_id);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=manage_lap_gallery&amp;CID=$cid&amp;LID=$lid"));

		break;

	case 'view_lap':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_view_lap.html')
		);

		//Pull Required Modification Data From DB
		$data = $garage_track->get_lap($lid);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $data['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;CID=$cid"))
		);

		//Get All Gallery Data Required
		$gallery_data = $garage_image->get_lap_gallery($cid, $lid);
			
		//Process Each Image From Lap Gallery	
       		for ( $i = 0; $i < count($gallery_data); $i++ )
        	{
               		// Do we have a thumbnail?  If so, our job is simple here :)
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

		//Build The Owners Avatar Image If Any...
		$data['avatar'] = '';
		if ($data['user_avatar'] AND $user->optionget('viewavatars'))
		{
			$avatar_img = '';
			switch( $data['user_avatar_type'] )
			{
				case AVATAR_UPLOAD:
					$avatar_img = $config['avatar_path'] . '/' . $data['user_avatar'];
				break;

				case AVATAR_GALLERY:
					$avatar_img = $config['avatar_gallery_path'] . '/' . $data['user_avatar'];
				break;
			}
			$data['avatar'] = '<img src="' . $avatar_img . '" width="' . $data['user_avatar_width'] . '" height="' . $data['user_avatar_height'] . '" alt="" />';
		}

		$template->assign_vars(array(
			'U_VIEW_PROFILE' 	=> append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=" . $data['user_id']),
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
            		'AVATAR_IMG' 		=> $data['avatar'],
         	));

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'view_track':

		break;
}

$garage_template->version_notice();

//Set Template Files In Used For Footer
$template->set_filenames(array(
	'garage_footer' => 'garage_footer.html')
);

//Generate Page Footer
page_footer();

?>

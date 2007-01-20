<?php
/***************************************************************************
 *                              garage_quartermile.php
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
require($phpbb_root_path . 'includes/mods/class_garage_business.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_dynorun.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_image.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_insurance.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_modification.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_quartermile.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_guestbook.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_model.' . $phpEx);

//Set The Page Title
$page_title = $user->lang['GARAGE'];

//Get All String Parameters And Make Safe
$params = array('mode' => 'mode', 'sort' => 'sort', 'start' => 'start', 'order' => 'order');
while(list($var, $param) = @each($params))
{
	$$var = request_var($param, '');
}

//Get All Non-String Parameters
$params = array('cid' => 'CID', 'mid' => 'MID', 'did' => 'DID', 'qmid' => 'QMID', 'ins_id' => 'INS_ID', 'eid' => 'EID', 'image_id' => 'image_id', 'comment_id' => 'CMT_ID', 'bus_id' => 'BUS_ID');
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
	case 'add_quartermile':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_quartermile') || $garage_config['enable_quartermile'] == '0')
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_quartermile.html')
		);

		//Get Vehicle Data For Navlinks
		$vehicle=$garage_vehicle->get_vehicle($cid);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $vehicle['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"))
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['ADD_QUARTERMILE'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=add_quartermile&amp;CID=$cid"))
		);

		//If Dynoruns Exist, Allow User To Link Quartermile Times To Know Vehicle Spec..
		if ( $garage_dynorun->count_runs($cid) > 0 )
		{
			$template->assign_vars(array(
				'S_DISPLAY_DYNORUNS' => true)
			);
			$dynoruns = $garage_dynorun->get_dynoruns_by_vehicle($cid);
			$garage_template->dynorun_dropdown($dynoruns);
		}

		$garage_template->attach_image('quartermile');
		$template->assign_vars(array(
			'L_TITLE'  			=> $user->lang['ADD_NEW_TIME'],
			'L_BUTTON'  			=> $user->lang['ADD_NEW_TIME'],
			'CID' 				=> $cid,
			'S_MODE_ACTION' 		=> append_sid("{$phpbb_root_path}garage_quartermile.$phpEx", "mode=insert_quartermile"))
         	);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'insert_quartermile':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_quartermile') || !$garage_config['enable_quartermile'])
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params	= array('rt' => '', 'sixty' => '', 'three' => '', 'eighth' => '', 'eighthmph' => '', 'thou' => '', 'quart' => '', 'quartmph' => '', 'dynorun_id' => '', 'install_comments' => '');
		$data 	= $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('quart');
		$garage->check_required_vars($params);

		//Update Quartermile With Data Acquired
		$qmid = $garage_quartermile->insert_quartermile($data);

		//Update The Time Now...In Case We Get Redirected During Image Processing
		$garage_vehicle->update_vehicle_time($cid);

		//If Any Image Variables Set Enter The Image Handling
		if ($garage_image->image_attached() )
		{
			//Check For Remote & Local Image Quotas
			if ( $garage_image->below_image_quotas() )
			{
				//Create Thumbnail & DB Entry For Image + Link To Item
				$image_id = $garage_image->process_image_attached('quartermile', $qmid);
				//Insert Image Into Quartermiles Gallery
				$hilite = $garage_quartermile->hilite_exists($cid, $qmid);
				$garage_image->insert_quartermile_gallery_image($image_id, $hilite);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ( $garage_image->above_image_quotas() )
			{
				redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=4"));
			}
		}
		//No Image Attached..We Need To Check If This Breaks The Site Rule
		else if ( ($garage_config['enable_quartermile_image_required'] == '1') AND ($data['quart'] <= $garage_config['quartermile_image_required_limit']))
		{
			//That Time Requires An Image...Delete Entered Time And Notify User
			$garage_quartermile->delete_quartermile($qmid);
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=26"));
		}

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ( $garage_config['enable_quartermile_approval'] )
		{
			$garage->pending_notification('unapproved_quartermiles');
		}

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

	case 'edit_quartermile':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_quartermile.$phpEx?mode=edit_quartermile&amp;QMID=$qmid&amp;CID=$cid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_quartermile.html')
		);

		//Count Dynoruns For Vehicle
		$count = $garage_dynorun->count_runs($cid);	

		//See If We Got Sent Here By Pending Page...If So We Need To Tell Update To Redirect Correctly
		$params = array('PENDING' => '');
		$redirect = $garage->process_vars($params);

		//Pull Required Quartermile Data From DB
		$data = $garage_quartermile->get_quartermile($qmid);

		//If Dynorun Is Already Linked Display Dropdown Correctly
		if ((!empty($data['dynorun_id'])) AND ($count > 0))
		{
			$bhp_statement = $data['bhp'] . ' BHP @ ' . $data['bhp_unit'];
			$template->assign_block_vars('link_rr', array());
			$garage_template->dynorun_dropdown($data['dynorun_id'], $bhp_statement, $cid);
		}
		//Allow User To Link To Dynorun
		else if ((empty($data['dynorun_id'])) AND ($count > 0))
		{
			$template->assign_block_vars('link_rr', array());
			$garage_template->dynorun_dropdown(NULL, NULL, $cid);
		}

		//Build All HTML Parts
		$template->assign_vars(array(
			'L_TITLE'		=> $user->lang['EDIT_TIME'],
			'L_BUTTON'		=> $user->lang['EDIT_TIME'],
			'U_EDIT_DATA' 		=> append_sid("{$phpbb_root_path}garage_quartermile.$phpEx", "mode=edit_quartermile&amp;CID=$cid&amp;QMID=$qmid"),
			'U_MANAGE_GALLERY' 	=> append_sid("{$phpbb_root_path}garage_quartermile.$phpEx", "mode=manage_quartermile_gallery&amp;CID=$cid&amp;QMID=$qmid"),
			'CID'			=> $cid,
			'QMID'			=> $qmid,
			'RT'			=> $data['rt'],
			'SIXTY'			=> $data['sixty'],
			'THREE' 		=> $data['three'],
			'EIGHTH' 		=> $data['eighth'],
			'EIGHTHMPH' 		=> $data['eighthmph'],
			'THOU' 			=> $data['thou'],
			'QUART' 		=> $data['quart'],
			'QUARTMPH' 		=> $data['quartmph'],
			'PENDING_REDIRECT'	=> $redirect['PENDING'],
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_quartermile.$phpEx", "mode=update_quartermile"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'update_quartermile':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_quartermile.$phpEx?mode=edit_quartermile&amp;QMID=$qmid&amp;CID=$cid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('rt' => '', 'sixty' => '', 'three' => '', 'eighth' => '', 'eighthmph' => '', 'thou' => '', 'quart' => '', 'quartmph' => '', 'dynorun_id' => '', 'install_comments' => '', 'editupload' => '', 'image_id' => '', 'pending_redirect' => '');
		$data = $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('quart');
		$garage->check_required_vars($params);

		//Update The Quartermile With Data Acquired
		$garage_quartermile->update_quartermile($data);

		//Update The Vehicle Timestamp Now...In Case We Get Redirected During Image Processing
		$garage_vehicle->update_vehicle_time($cid);

		//Removed The Old Image If Required By A Delete Or A New Image Existing
		if (($data['editupload'] == 'delete') OR ($data['editupload'] == 'new'))
		{
			$garage_image->delete_quartermile_image($data['image_id']);
		}

		//If Any Image Variables Set Enter The Image Handling
		if ($garage_image->image_attached())
		{
			//Check For Remote & Local Image Quotas
			if ($garage_image->below_image_quotas())
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('quartermile', $qmid);
				$garage->update_single_field(GARAGE_QUARTERMILES_TABLE, 'image_id', $image_id, 'id', $qmid);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ($garage_image->above_image_quotas())
			{
				redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=4"));
			}
		}
		//No Image Attached..We Need To Check If This Breaks The Site Rule
		else if (($garage_config['enable_quartermile_image_required'] == '1') AND ($data['quart'] <= $garage_config['quartermile_image_required_limit']))
		{
			//That Time Requires An Image...Delete Entered Time And Notify User
			$garage_quartermile->delete_quartermile_time($qmid);
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=26"));
		}

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ($garage_config['enable_quartermile_approval'])
		{
			$garage->pending_notification('unapproved_quartermiles');
		}

		//If Editting From Pending Page Redirect Back To There Instead
		if ($data['pending_redirect'] == 'MCP')
		{
			redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_quartermiles"));
		}

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

	case 'delete_quartermile':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_delete_quartermile'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Delete The Quartermie Time
		$garage_quartermile->delete_quartermile($qmid);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;
	
	case 'insert_quartermile_image':

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
				$image_id = $garage_image->process_image_attached('quartermile', $qmid);
				//Insert Image Into Quartermile Gallery
				$hilite = $garage_quartermile->hilite_exists($qmid);
				$garage_image->insert_quartermile_gallery_image($image_id, $hilite);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ($garage_image->above_image_quotas())
			{
				redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=4"));
			}
		}

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage_quartermile.$phpEx", "mode=manage_quartermile_gallery&amp;CID=$cid&amp;QMID=$qmid"));

		break;

	case 'manage_quartermile_gallery':

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
		$garage_template->attach_image('quartermile');

		//Pull Quartermile Gallery Data From DB
		$data = $garage_image->get_quartermile_gallery($cid, $qmid);

		//Process Each Image From Quartermile Gallery
		for ($i = 0, $count = sizeof($data);$i < $count; $i++)
		{
			$template->assign_block_vars('pic_row', array(
				'U_IMAGE'	=> (($data[$i]['attach_id']) AND ($data[$i]['attach_is_image']) AND (!empty($data[$i]['attach_thumb_location'])) AND (!empty($data[$i]['attach_location']))) ? append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_image&amp;image_id=" . $data[$i]['attach_id']) : '',
				'U_REMOVE_IMAGE'=> append_sid("{$phpbb_root_path}garage_quartermile.$phpEx", "mode=remove_quartermile_image&amp;CID=$cid&amp;QMID=$qmid&amp;image_id=" . $data[$i]['attach_id']),
				'U_SET_HILITE'	=> ($data[$i]['hilite'] == 0) ? append_sid("{$phpbb_root_path}garage_quartermile.$phpEx", "mode=set_quartermile_hilite&amp;image_id=" . $data[$i]['attach_id'] . "&amp;CID=$cid&amp;QMID=$qmid") : '',
				'IMAGE' 	=> $phpbb_root_path . GARAGE_UPLOAD_PATH . $data[$i]['attach_thumb_location'],
				'IMAGE_TITLE' 	=> $data[$i]['attach_file'])
			);
		}

		$template->assign_vars(array(
			'U_EDIT_DATA' 		=> append_sid("{$phpbb_root_path}garage_quartermile.$phpEx", "mode=edit_quartermile&amp;CID=$cid&amp;QMID=$qmid"),
			'U_MANAGE_GALLERY' 	=> append_sid("{$phpbb_root_path}garage_quartermile.$phpEx", "mode=manage_quartermile_gallery&amp;CID=$cid&amp;QMID=$qmid"),
			'QMID' => $qmid,
			'CID' => $cid,
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_quartermile.$phpEx", "mode=insert_quartermile_image"))
         	);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();		

		break;

	case 'set_quartermile_hilite':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Set All Images To Non Hilite So We Do Not End Up With Two Hilites & Then Set Hilite
		$garage->update_single_field(GARAGE_QUARTERMILE_GALLERY_TABLE, 'hilite', 0, 'quartermile_id', $qmid);
		$garage->update_single_field(GARAGE_QUARTERMILE_GALLERY_TABLE, 'hilite', 1, 'image_id', $image_id);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage_quartermile.$phpEx", "mode=manage_quartermile_gallery&amp;CID=$cid&amp;QMID=$qmid"));

		break;

	case 'remove_quartermile_image':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Remove Image From Quartermile Gallery & Deletes Image
		$garage_image->delete_quartermile_image($image_id);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage_quartermile.$phpEx", "mode=manage_quartermile_gallery&amp;CID=$cid&amp;QMID=$qmid"));

		break;


	case 'view_quartermile':

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
			'body'   => 'garage_view_quartermile.html')
		);

		//Pull Required Modification Data From DB
		$data = $garage_quartermile->get_quartermile($qmid);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $data['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;CID=$cid"))
		);

		//Get All Gallery Data Required
		$gallery_data = $garage_image->get_quartermile_gallery($cid, $qmid);
			
		//Process Each Image From Quartermile Gallery	
       		for ( $i = 0; $i < count($gallery_data); $i++ )
        	{
               		// Do we have a thumbnail?  If so, our job is simple here :)
			if ( (empty($gallery_data[$i]['attach_thumb_location']) == false) AND ($gallery_data[$i]['attach_thumb_location'] != $gallery_data[$i]['attach_location']) )
			{
				$template->assign_vars(array(
					'S_DISPLAY_GALLERIES' 	=> true,
				));

				$template->assign_block_vars('quartermile_image', array(
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
			'USERNAME' 		=> $data['username'],
			'YEAR' 			=> $data['made_year'],
			'MAKE' 			=> $data['make'],
			'MODEL' 		=> $data['model'],
            		'AVATAR_IMG' 		=> $data['avatar'],
            		'DATE_UPDATED' 		=> $user->format_date($data['date_updated']),
            		'RT' 			=> $data['rt'],
            		'SIXTY' 		=> $data['sixty'],
            		'THREE'	 		=> $data['three'],
            		'EIGHTH' 		=> $data['eighth'],
            		'EIGHTHMPH' 		=> $data['eighthmph'],
            		'THOU'	 		=> $data['thou'],
			'QUART' 		=> $data['quart'],
			'QUARTMPH' 		=> $data['quartmph'],
		));

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

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

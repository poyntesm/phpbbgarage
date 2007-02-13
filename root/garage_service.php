<?php
/***************************************************************************
 *                              garage_service.php
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
require($phpbb_root_path . 'includes/mods/class_garage_service.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_business.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);

//Set The Page Title
$page_title = $user->lang['GARAGE'];

//Get All String Parameters And Make Safe
$params = array('mode' => 'mode', 'sort' => 'sort', 'start' => 'start', 'order' => 'order');
while(list($var, $param) = @each($params))
{
	$$var = request_var($param, '');
}

//Get All Non-String Parameters
$params = array('cid' => 'CID', 'svid' => 'SVID', 'eid' => 'EID');
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
	case 'add_service':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_service') || $garage_config['enable_service'] == '0')
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
			'body'   => 'garage_service.html')
		);

		//Get Vehicle Data For Navlinks
		$vehicle = $garage_vehicle->get_vehicle($cid);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $vehicle['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"))
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['ADD_SERVICE'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=add_service&amp;CID=$cid"))
		);

		$garages 	= $garage_business->get_business_by_type(BUSINESS_GARAGE);

		$garage_template->garage_dropdown($garages);
		$garage_template->rating_dropdown('rating');
		$garage_template->service_type_dropdown();
		$template->assign_vars(array(
			'L_TITLE'  			=> $user->lang['ADD_SERVICE'],
			'L_BUTTON'  			=> $user->lang['ADD_SERVICE'],
			'CID' 				=> $cid,
			'S_MODE_ACTION' 		=> append_sid("{$phpbb_root_path}garage_service.$phpEx", "mode=insert_service"))
         	);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'insert_service':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_service') || !$garage_config['enable_service'])
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params	= array('garage_id' => '', 'type_id' => '', 'price' => '', 'rating' => '', 'mileage' => '');
		$data 	= $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('garage_id', 'type_id', 'mileage');
		$garage->check_required_vars($params);

		//Update Service With Data Acquired
		$svid = $garage_service->insert_service($data);

		//Update The Time Now...In Case We Get Redirected During Image Processing
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

	case 'edit_service':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_service.$phpEx?mode=edit_service&amp;SVID=$svid&amp;CID=$cid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_service.html')
		);

		//Build Navlinks
		$vehicle_data 	= $garage_vehicle->get_vehicle($cid);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $vehicle_data['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"))
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['EDIT_SERVICE'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=edit_vehicle&amp;CID=$cid&amp;SVID=$svid"))
		);

		//Pull Required Service Data From DB
		$data = $garage_service->get_service($svid);
		$garages = $garage_business->get_business_by_type(BUSINESS_GARAGE);

		//Build All HTML Parts
		$garage_template->garage_dropdown($garages, $data['garage_id']);
		$garage_template->rating_dropdown('rating', $data['rating']);
		$garage_template->service_type_dropdown($data['type_id']);
		$template->assign_vars(array(
			'L_TITLE'		=> $user->lang['EDIT_SERVICE'],
			'L_BUTTON'		=> $user->lang['EDIT_SERVICE'],
			'PRICE'			=> $data['price'],
			'MILEAGE'		=> $data['mileage'],
			'CID'			=> $cid,
			'SVID'			=> $svid,
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_service.$phpEx", "mode=update_service"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'update_service':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_service.$phpEx?mode=edit_service&amp;SVID=$svid&amp;CID=$cid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params	= array('garage_id' => '', 'type_id' => '', 'price' => '', 'rating' => '', 'mileage' => '');
		$data = $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('garage_id', 'type_id', 'mileage');
		$garage->check_required_vars($params);

		//Update The Service With Data Acquired
		$garage_service->update_service($data);

		//Update The Vehicle Timestamp Now...In Case We Get Redirected During Image Processing
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

	case 'delete_service':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_delete_service'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Delete The Quartermie Time
		$garage_service->delete_service($svid);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

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

<?php
/** 
*
* @package garage
* @version $Id$
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

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
$params = array('vid' => 'VID', 'mid' => 'MID', 'did' => 'DID', 'qmid' => 'QMID', 'ins_id' => 'INS_ID', 'eid' => 'EID', 'image_id' => 'image_id', 'comment_id' => 'CMT_ID', 'bus_id' => 'BUS_ID');
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
	case 'add_premium':

		//Let Check That Insurance Premiums Are Allowed...If Not Redirect
		if (!$garage_config['enable_insurance'] || !$auth->acl_get('u_garage_add_insurance'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($vid);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_insurance.html')
		);

		//Get Data
		$insurance_business 	= $garage_business->get_business_by_type(BUSINESS_INSURANCE);

		//Get Vehicle Data For Navlinks
		$vehicle=$garage_vehicle->get_vehicle($vid);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $vehicle['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"))
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['ADD_PREMIUM'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=add_premium&amp;VID=$vid"))
		);

		//Build All Required HTML Components
		$garage_template->insurance_dropdown($insurance_business);
		$garage_template->cover_dropdown();
		$template->assign_vars(array(
			'L_TITLE' 		=> $user->lang['ADD_PREMIUM'],
			'L_BUTTON' 		=> $user->lang['ADD_PREMIUM'],
			'U_SUBMIT_BUSINESS' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_business&amp;VID=$vid&amp;redirect=add_premium&amp;BUSINESS=" . BUSINESS_INSURANCE),
			'VID' 			=> $vid,
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_premium.$phpEx", "mode=insert_premium"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'insert_premium':

		//Let Check That Insurance Premiums Are Allowed...If Not Redirect
		if (!$garage_config['enable_insurance'] || !$auth->acl_get('u_garage_add_insurance'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($vid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('business_id' => '', 'premium' => '', 'cover_type' => '', 'comments' => '');
		$data 	= $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('business_id', 'premium', 'cover_type');
		$garage->check_required_vars($params);

		//Insert The Insurnace Premium
		$garage_insurance->insert_premium($data);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($vid);

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"));

	case 'edit_premium':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_premium.$phpEx?mode=edit_premium&amp;INS_ID=$ins_id&amp;VID=$vid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($vid);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_insurance.html')
		);

		//Build Navlinks
		$vehicle_data 	= $garage_vehicle->get_vehicle($vid);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $vehicle_data['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"))
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['EDIT_PREMIUM'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=edit_vehicle&amp;VID=$vid&amp;INS_ID=$ins_id"))
		);

		//Pull Required Insurance Premium Data From DB
		$data = $garage_insurance->get_premium($ins_id);
		$insurance_business = $garage_business->get_business_by_type(BUSINESS_INSURANCE);

		//Build Required HTML Components
		$garage_template->insurance_dropdown($insurance_business, $data['business_id']);
		$garage_template->cover_dropdown($data['cover_type']);
		$template->assign_vars(array(
			'L_TITLE' 		=> $user->lang['EDIT_PREMIUM'],
			'L_BUTTON' 		=> $user->lang['EDIT_PREMIUM'],
			'INS_ID' 		=> $ins_id,
			'VID' 			=> $vid,
			'PREMIUM' 		=> $data['premium'],
			'COMMENTS' 		=> $data['comments'],
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_premium.$phpEx", "mode=update_premium"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'update_premium':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($vid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('business_id' => '', 'premium' => '', 'cover_type' => '', 'comments' => '');
		$data = $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('business_id', 'premium', 'cover_type');
		$garage->check_required_vars($params);

		//Update The Insurance Premium With Data Acquired
		$garage_insurnace->update_premium($data);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($vid);

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"));

		break;
	
	case 'delete_premium':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_delete_insurance'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($vid);

		//Delete Insurance Premium
		$garage_insurance->delete_premium($ins_id);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($vid);

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"));

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

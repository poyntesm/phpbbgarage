<?php
/***************************************************************************
 *                              garage.php
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

//Setup Garage Language Files...
if(empty($board_config['default_lang']))
{
	$board_config['default_lang'] = 'english';
}

//Start Session Management
$user->session_begin();
$auth->acl($user->data);
$user->setup(array('mods/garage'));

//Build All Garage Classes e.g $garage_images->
require($phpbb_root_path . 'language/' . $user->data['user_lang'] . '/mods/garage.' . $phpEx);
require($phpbb_root_path . 'language/' . $user->data['user_lang'] . '/mods/garage_error.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage.' . $phpEx);
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
$page_title = $user->lang['Garage'];

//Get All String Parameters And Make Safe
$params = array('mode' => 'mode', 'sort' => 'sort', 'start' => 'start', 'order' => 'order');
while( list($var, $param) = @each($params) )
{
	$$var = request_var($param, '');
}

//Get All Non-String Parameters
$params = array('cid' => 'CID', 'mid' => 'MID', 'rrid' => 'RRID', 'qmid' => 'QMID', 'ins_id' => 'INS_ID', 'eid' => 'EID', 'image_id' => 'image_id', 'comment_id' => 'comment_id', 'bus_id' => 'BUS_ID');
while( list($var, $param) = @each($params) )
{
	$$var = request_var($param, '');
}

//Decide What Mode The User Is Doing
switch( $mode )
{
	//Mode To Display Create Vehicle Sceen
	case 'create_vehicle':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=create_vehicle", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD', "garage.$phpEx?mode=error&EID=14");

		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'javascript' => 'garage_vehicle_select_javascript.html',
			'body'   => 'garage_vehicle.html')
		);

		//Count Vehicles User Already Has
		$count = $garage_vehicle->count_user_vehicles();

		//Check To See If User Has Too Many Vehicles Already...If So Display Notice
		if ( $count >= $garage_vehicle->get_user_add_quota() ) 
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=5", true));
		}

		//Set Default Make 
		$params = array('MAKE', 'MODEL');
		$data = $garage->process_post_vars($params);
		$data['MAKE'] = (empty($data['MAKE'])) ? '' : $data['MAKE'];

		//Build All Required Javascript And Arrays
		$template->assign_vars(array(
			'VEHICLE_ARRAY' => $garage_template->vehicle_array())
		);
		$template->assign_var_from_handle('JAVASCRIPT', 'javascript');

		//Check If User Should Be Able To Submit New Makes & Models
		if ($garage_config['enable_user_submit_make'])
		{
			$template->assign_block_vars('enable_user_submit_make', array());
		}
		if ($garage_config['enable_user_submit_model'])
		{
			$template->assign_block_vars('enable_user_submit_model', array());
		}

		//Build All Required HTML 
		$garage_template->year_dropdown();
		$garage_template->attach_image('vehicle');
		$template->assign_vars(array(
			'L_TITLE' => $lang['Create_New_Vehicle'],
			'L_BUTTON' => $lang['Create_New_Vehicle'],
			'L_REQUIRED' => $lang['Required'],
			'L_CHECK_FOR_PM' => $lang['Check_For_PM'],
			'L_VEHICLE_INFO' => $lang['Vehicle_Info'],
			'L_YEAR' => $lang['Year'],
			'L_MAKE' => $lang['Make'],
       			'L_MODEL' => $lang['Model'],
			'L_COLOUR' => $lang['Colour'],
			'L_MILEAGE' => $lang['Mileage'],
			'L_PURCHASED_PRICE' => $lang['Purchased_Price'],
			'L_CURRENCY' => $lang['Currency'],
			'L_PM_GUESTBOOK_NOTIFICATIONS' => $lang['PM_Guestbook_Notifications'],
			'L_DESCRIPTION' => $lang['Description'],
			'L_NOT_LISTED_YET' => $lang['Not_Listed_Yet'],
			'L_HERE' => $lang['Here'],
			'S_MODE_ACTION' => append_sid("garage.$phpEx?mode=insert_vehicle"),
			'U_USER_SUBMIT_MAKE' => append_sid("garage.$phpEx?mode=user_submit_make"),
			'MAKE' 	=> $data['MAKE'],
			'MODEL'	=> $data['MODEL'],
			'ADDING_MODEL' => 'NO',
			'CURRENCY_LIST'	=> $garage_template->dropdown('currency', $currency_types, $currency_types),
			'MILEAGE_UNIT_LIST' => $garage_template->dropdown('mileage_units', $mileage_unit_types, $mileage_unit_types))
		);

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);
		
		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();		
		$template->pparse('body');

		break;

	//Mode To Actaully Insert Into DB A New Vehicle
	case 'insert_vehicle':

		//User Is Annoymous...So Not Allowed To Create A Vehicle
		if ( $user->data['user_id'] == ANONYMOUS )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=2", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD', "garage.$phpEx?mode=error&EID=14");

		//Count Vehicles User Already Has
		$count = $garage_vehicle->count_user_vehicles();

		//Check To See If User Has Too Many Vehicles Already...If So Display Notice
		if ( $count >= $garage_vehicle->get_user_add_quota() ) 
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=5", true));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('year', 'make_id', 'model_id', 'colour', 'mileage', 'mileage_units', 'price', 'currency', 'comments', 'guestbook_pm_notify', 'adding_model');
		$data = $garage->process_post_vars($params);
		$data['guestbook_pm_notify'] = ($data['guestbook_pm_notify'] == 'on') ? 1 : 0;
		$data['time'] = time();

		//We Need To Check If We Have Been Sent Here To Add A Model...
		if ( $data['adding_model'] == 'YES' )
		{
			//We Are Adding A Model But No Make Given..Error Nicely
			if ( empty($data['make_id']) )
			{
				redirect(append_sid("garage.$phpEx?mode=error&EID=23", true));
			}
			redirect(append_sid("garage.$phpEx?mode=user_submit_model&MAKE_ID=".$data['make_id'], true));
		}

		//Set As Main User Vehicle If No Other Vehicle Exists For User
		$data['main_vehicle'] = ( $count == 0 ) ? 1 : 0;

		//Checks All Required Data Is Present
		$params = array('year', 'make_id', 'model_id');
		$garage->check_required_vars($params);

		//Insert The Vehicle Into The DB And Get The CID
		$cid = $garage_vehicle->insert_vehicle($data);

		//If Any Image Variables Set Enter The Image Handling
		if( $garage_image->image_attached() )
		{
			//Get All Users Images So We Can Workout Current Quota Usage
			$user_upload_image_data = $garage_image->select_user_upload_images($user->data['user_id']);
			$user_remote_image_data = $garage_image->select_user_remote_images($user->data['user_id']);

			//Check For Remote & Local Image Quotas
			if ( (($garage_image->image_is_remote() ) AND (count($user_remote_image_data) < $garage_image->get_user_remote_image_quota($user->data['user_id']))) OR (($garage_image->image_is_local() ) AND (count($user_image_data) < $garage_image->get_user_upload_image_quota($user->data['user_id']))) )
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('vehicle', $cid);
				//Insert Image Into Vehicles Gallery
				$garage_image->insert_gallery_image($image_id);
				//Set Image As Hilite Image For Vehicle
				$garage->update_single_field(GARAGE_TABLE, 'image_id', $image_id, 'id', $cid);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ( (($garage_image->image_is_remote() ) AND (count($user_remote_image_data) >= $garage_image->get_user_remote_image_quota($user->data['user_id']))) OR (($garage_image->image_is_local() ) AND (count($user_image_data) >= $garage_image->get_user_upload_image_quota($user->data['user_id']))) )
			{
				redirect(append_sid("garage.$phpEx?mode=error&EID=4", true));
			}
		}

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	//Mode To Display Editting Page Of An Existing Vehicle
	case 'edit_vehicle':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$user->data['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=edit_vehicle&CID=$cid", true));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'javascript' => 'garage_vehicle_select_javascript.html',
			'body'   => 'garage_vehicle.html')
		);

		//Pull Required Vehicle Data From DB
		$data = $garage_vehicle->select_vehicle_data($cid);

		//Build All Required Javascript And Arrays
		$garage_template->year_dropdown($data['made_year']);
		$template->assign_vars(array(
			'VEHICLE_ARRAY' => $garage_template->vehicle_array())
		);
		$template->assign_var_from_handle('JAVASCRIPT', 'javascript');

		$template->assign_vars(array(
			'S_MODE_ACTION' => append_sid("garage.$phpEx?mode=update_vehicle"),
			'L_REQUIRED' => $lang['Required'],
			'L_CHECK_FOR_PM' => $lang['Check_For_PM'],
			'L_VEHICLE_INFO' => $lang['Vehicle_Info'],
			'L_YEAR' => $lang['Year'],
			'L_MAKE' => $lang['Make'],
       			'L_MODEL' => $lang['Model'],
			'L_COLOUR' => $lang['Colour'],
			'L_MILEAGE' => $lang['Mileage'],
			'L_PURCHASED_PRICE' => $lang['Purchased_Price'],
			'L_CURRENCY' => $lang['Currency'],
			'L_PM_GUESTBOOK_NOTIFICATIONS' => $lang['PM_Guestbook_Notifications'],
			'L_DESCRIPTION' => $lang['Description'],
       			'L_TITLE' => $lang['Edit_Vehicle'],
       			'L_BUTTON' => $lang['Edit_Vehicle'],
			'CID' => $cid,
			'MAKE' => $data['make'],
			'MODEL' => $data['model'],
			'YEAR' => $data['made_year'],
			'CHECKED' => ($data['guestbook_pm_notify'] == TRUE) ? 'checked="checked"': '',
			'COLOUR' => $data['color'],
			'MILEAGE' => $data['mileage'],
			'MILEAGE_UNITS' => $data['mileage_units'],
			'PRICE' => $data['price'],
			'CURRENCY_LIST' => $garage_template->dropdown('currency', $currency_types, $currency_types, $data['currency']),
			'MILEAGE_UNIT_LIST' => $garage_template->dropdown('mileage_units', $mileage_unit_types, $mileage_unit_types, $data['mileage_units']),
			'COMMENTS' => $data['comments'])
		);

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	//Mode To Actaully Update The DB Of An Existing Vehicle
	case 'update_vehicle':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$user->data['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=edit_vehicle&CID=$cid", true));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('year', 'make_id', 'model_id', 'colour', 'mileage', 'mileage_units', 'price', 'currency', 'comments', 'guestbook_pm_notify');
		$data = $garage->process_post_vars($params);
		$data['guestbook_pm_notify'] = ($data['guestbook_pm_notify'] == 'on') ? 1 : 0;

		//Checks All Required Data Is Present
		$params = array('year', 'make_id', 'model_id');
		$garage->check_required_vars($params);

		//Update The Vehicle With Data Acquired
		$garage_vehicle->update_vehicle($data);
	
		//Update Timestamp For Vehicle	
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	//Mode To Delete A Vehicle From The DB
	case 'delete_vehicle':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Actually Delete The Vehicle..This Will Delete All Related Items!!
		$garage_vehicle->delete_vehicle($cid);

		redirect(append_sid("garage.$phpEx?mode=main_menu", true));

		break;

	//Mode To Display Add Modification Page
	case 'add_modification':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD', "garage.$phpEx?mode=error&EID=14");

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$user->data['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=add_modification&CID=$cid", true));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_modification.html')
		);

		//Build HTML Components
		$garage_template->category_dropdown();
		$garage_template->attach_image('modification');
		$garage_template->garage_install_dropdown();
		$garage_template->shop_dropdown();

		$template->assign_vars(array(
			'S_MODE_ACTION'		=> append_sid("garage.$phpEx?mode=insert_modification&CID=$cid"),
			'L_CATEGORY'		=> $lang['Category'],
			'L_NOT_LISTED_YET' 	=> $lang['Not_Listed_Yet'],
			'L_HERE' 		=> $lang['Here'],
			'L_REQUIRED' 		=> $lang['Required'],
			'L_MODIFICATION' 	=> $lang['Modification'],
			'L_PURCHASE_RATING' 	=> $lang['Purchase_Rating'],
			'L_PURCHASED_PRICE' 	=> $lang['Purchased_Price'],
			'L_PURCHASED_FROM' 	=> $lang['Purchased_From'],
			'L_INSTALLED_BY' 	=> $lang['Installed_By'],
			'L_INSTALLATION_PRICE' 	=> $lang['Installation_Price'],
			'L_PRODUCT_RATING' 	=> $lang['Product_Rating'],
			'L_INSTALLATION_RATING' => $lang['Installation_Rating'],
			'L_DESCRIPTION' 	=> $lang['Description'],
			'L_INSTALL_COMMENTS' 	=> $lang['Install_Comments'],
			'L_ONLY_SHOW_IN_REVIEW' => $lang['Only_Show_In_Review'],
			'L_CREATE_NEW_MOD' 	=> $lang['Create_New_Mod'],
			'L_BUTTON' 		=> $lang['Add_Modification'],
			'L_TITLE' 		=> $lang['Add_Modification'],
			'U_SUBMIT_SHOP'		=> append_sid("garage.$phpEx?mode=user_submit_business&CID=$cid&mode_redirect=add_modification&BUSINESS=shop"),
			'U_SUBMIT_GARAGE'	=> append_sid("garage.$phpEx?mode=user_submit_business&CID=$cid&mode_redirect=add_modification&BUSINESS=garage"),
			'PRODUCT_RATING_LIST' 	=> $garage_template->dropdown('product_rating', $rating_text, $rating_types),
			'PURCHASE_RATING_LIST' 	=> $garage_template->dropdown('purchase_rating', $rating_text, $rating_types),
			'INSTALL_RATING_LIST' 	=> $garage_template->dropdown('install_rating', $rating_text, $rating_types),
			'CID' => $cid)
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'insert_modification':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD', "garage.$phpEx?mode=error&EID=14");

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$user->data['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=add_modification&CID=$cid", true));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('category_id', 'title', 'price', 'business_id', 'install_business_id', 'install_price', 'install_rating', 'product_rating', 'comments', 'install_comments', 'purchase_rating');
		$data = $garage->process_post_vars($params);
		$data['time'] = time();
		$vehicle = $garage_vehicle->select_vehicle_data($cid);
		$data['member_id'] = $vehicle['member_id'];

		//Checks All Required Data Is Present
		$params = array('category_id', 'title');
		$garage->check_required_vars($params);

		//Insert The Modification Into The DB With Data Acquired
		$mid = $garage_modification->insert_modification($data);

		//Update The Time Now...In Case We Get Redirected During Image Processing
		$garage_vehicle->update_vehicle_time($cid);

		//If Any Image Variables Set Enter The Image Handling
		if ( $garage_image->image_attached() )
		{
			//Get All Users Images So We Can Workout Current Quota Usage
			$user_upload_image_data = $garage_image->select_user_upload_images($user->data['user_id']);
			$user_remote_image_data = $garage_image->select_user_remote_images($user->data['user_id']);

			//Check For Remote & Local Image Quotas
			if ( (($garage_image->image_is_remote() ) AND (count($user_remote_image_data) < $garage_image->get_user_remote_image_quota($userdata['user_id']))) OR (($garage_image->image_is_local() ) AND (count($user_image_data) < $garage_image->get_user_upload_image_quota($userdata['user_id']))) )
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('modification', $mid);
				//Set Image To This Modification
				$garage->update_single_field(GARAGE_MODS_TABLE, 'image_id', $image_id, 'id', $mid);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ( (($garage_image->image_is_remote() ) AND (count($user_remote_image_data) >= $garage_image->get_user_remote_image_quota($userdata['user_id']))) OR (($garage_image->image_is_local() ) AND (count($user_image_data) >= $garage_image->get_user_upload_image_quota($userdata['user_id']))) )
			{
				redirect(append_sid("garage.$phpEx?mode=error&EID=4", true));
			}
		}

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	case 'edit_modification':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=edit_modification&MID=$mid&CID=$cid", true));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_modification.html')
		);
		
		//Pull Required Modification Data From DB
		$data = $garage_modification->select_modification_data($mid);

		//Build All Required HTML parts
		$garage_template->category_dropdown($data['category_id']);
		$garage_template->garage_install_dropdown($data['install_business_id'], $data['install_business_name']);
		$garage_template->shop_dropdown($data['business_id'], $data['business_name']);
		$garage_template->edit_image($data['image_id'], $data['attach_file']);

		$template->assign_block_vars('level2', array());
		$template->assign_vars(array(
			'L_LEVEL2' => $data['vehicle'],
			'L_VEHICLE_INFO' => $lang['Vehicle_Info'],
			'L_REQUIRED' => $lang['Required'],
			'L_CATEGORY' => $lang['Category'],
			'L_TITLE' => $lang['Title'],
			'L_MODIFICATION' => $lang['Modification'],
       			'L_PURCHASE_RATING' => $lang['Purchase_Rating'],
       			'L_PURCHASED_PRICE' => $lang['Purchased_Price'],
			'L_PURCHASED_FROM' => $lang['Purchased_From'],
			'L_INSTALLED_BY' => $lang['Installed_By'],
			'L_INSTALLATION_PRICE' => $lang['Installation_Price'],
			'L_PRODUCT_RATING' => $lang['Product_Rating'],
			'L_INSTALLATION_RATING' => $lang['Installation_Rating'],
			'L_DESCRIPTION' => $lang['Description'],
			'L_INSTALL_COMMENTS' => $lang['Install_Comments'],
			'L_ONLY_SHOW_IN_REVIEW' => $lang['Only_Show_In_Review'],
       			'L_TITLE' => $lang['Modify_Mod'],
       			'L_BUTTON' => $lang['Modify_Mod'],
			'U_LEVEL2' => append_sid("garage.$phpEx?mode=view_own_vehicle&amp;CID=".$cid),
			'S_MODE_ACTION' => append_sid("garage.$phpEx?mode=update_modification"),
			'MID' => $mid,
			'CID' => $cid,
			'JAVASCRIPT' => $data['vehicle_javascript'],
			'TITLE' => $data['title'],
			'MAKE' => $data['make'],
			'MODEL' => $data['model'],
			'PRICE' => $data['price'],
			'INSTALL_PRICE' => $data['install_price'],
			'PRODUCT_RATING_LIST' => $garage_template->dropdown('product_rating', $rating_text, $rating_types, $data['product_rating']),
			'PURCHASE_RATING_LIST' => $garage_template->dropdown('purchase_rating', $rating_text, $rating_types, $data['purchase_rating']),
			'INSTALL_RATING_LIST' => $garage_template->dropdown('install_rating', $rating_text, $rating_types, $data['install_rating']),
			'COMMENTS' => $data['comments'],
			'INSTALL_COMMENTS' => $data['install_comments'])
		);

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();		
		$template->pparse('body');

		break;

	case 'update_modification':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('category_id', 'title', 'price', 'business_id', 'install_business_id', 'install_price', 'install_rating', 'product_rating', 'comments', 'install_comments', 'editupload', 'image_id', 'purchase_rating');
		$data = $garage->process_post_vars($params);
		$data['time'] = time();

		//Checks All Required Data Is Present
		$params = array('category_id', 'title');
		$garage->check_required_vars($params);

		//Update The Modification With Data Acquired
		$garage_modification->update_modification($data);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		//User Has Chosen To Delete Existing Image
		if ( ($data['editupload'] == 'delete') OR ( $data['editupload'] == 'new') )
		{
			$garage_image->delete_image($data['image_id']);
			$garage->update_single_field(GARAGE_MODS_TABLE, 'image_id', 'NULL', 'id', $mid);
		}

		//If Any Image Variables Set Enter The Image Handling
		if( $garage_image->image_attached() )
		{
			//Get All Users Images So We Can Workout Current Quota Usage
			$user_upload_image_data = $garage_image->select_user_upload_images($userdata['user_id']);
			$user_remote_image_data = $garage_image->select_user_remote_images($userdata['user_id']);

			//Check For Remote & Local Image Quotas
			if ( (($garage_image->image_is_remote() ) AND (count($user_remote_image_data) < $garage_image->get_user_remote_image_quota($userdata['user_id']))) OR (($garage_image->image_is_local() ) AND (count($user_image_data) < $garage_image->get_user_upload_image_quota($userdata['user_id']))) )
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('modification', $mid);
				//Set Image To This Modification
				$garage->update_single_field(GARAGE_MODS_TABLE, 'image_id', $image_id, 'id', $mid);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ( (($garage_image->image_is_remote() ) AND (count($user_remote_image_data) >= $garage_image->get_user_remote_image_quota($userdata['user_id']))) OR (($garage_image->image_is_local() ) AND (count($user_image_data) >= $garage_image->get_user_upload_image_quota($userdata['user_id']))) )
			{
				redirect(append_sid("garage.$phpEx?mode=error&EID=4", true));
			}
		}

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	case 'delete_modification':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Delete The Modification
		$garage_modification->delete_modification($mid);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	case 'add_quartermile':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD', "garage.$phpEx?mode=error&EID=14");

		//Let Check That Quartermile Times Are Allowed...If Not Redirect
		if ($garage_config['enable_quartermile'] == '0')
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=18", true));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//If Dynoruns Exist, Allow User To Link Quartermile Times To Know Vehicle Spec..
		if ( $garage_dynorun->count_runs($cid) > 0 )
		{
			$template->assign_block_vars('link_rr', array());
			$garage_template->dynorun_dropdown(NULL, NULL, $cid);
		}
		
		include($phpbb_root_path . 'includes/page_header.' . $phpEx);

		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_quartermile.html')
		);

		//Pre Build All Side Menus
		$garage_template->attach_image('vehicle');

		$template->assign_vars(array(
			'L_NOT_LISTED_YET' => $lang['Not_Listed_Yet'],
			'L_HERE' => $lang['Here'],
			'L_REQUIRED' => $lang['Required'],
			'L_GARAGE_QUARTERMILE_TIMES' => $lang['Garage_Quartermile_Times'],
			'L_TITLE'  => $lang['Add_New_Time'],
			'L_BUTTON'  => $lang['Add_New_Time'],
			'L_LINK_TO_RR'  => $lang['Link_To_RR'],
			'L_RT' => $lang['Rt_Explain'],
			'L_SIXTY' => $lang['Sixty_Explain'],
			'L_THREE' => $lang['Three_Explain'],
			'L_EIGHT' => $lang['Eight_Explain'],
			'L_EIGHTMPH' => $lang['Eightmph_Explain'],
			'L_THOU' => $lang['Thou_Explain'],
			'L_QUART' => $lang['Quart_Explain'],
			'L_QUARTMPH' => $lang['Quartmph_Explain'],
			'CID' => $cid,
			'S_MODE_ACTION' => append_sid("garage.$phpEx?mode=insert_quartermile"))
         	);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'insert_quartermile':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD', "garage.$phpEx?mode=error&EID=14");

		//Let Check That Quartermile Times Are Allowed...If Not Redirect
		if ($garage_config['enable_quartermile'] == '0')
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=18", true));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('rt', 'sixty', 'three', 'eight', 'eightmph', 'thou', 'quart', 'quartmph', 'rr_id', 'install_comments');
		$data = $garage->process_post_vars($params);
		$data['pending'] = ($garage_config['enable_quartermile_approval'] == '1') ? 1 : 0 ;
		$data['time'] = time();

		//Checks All Required Data Is Present
		$params = array('quart');
		$garage->check_required_vars($params);

		//Update Quartermile With Data Acquired
		$qmid = $garage_quartermile->insert_quartermile($data);

		//Update The Time Now...In Case We Get Redirected During Image Processing
		$garage_vehicle->update_vehicle_time($cid);

		//If Any Image Variables Set Enter The Image Handling
		if( $garage_image->image_attached() )
		{
			//Get All Users Images So We Can Workout Current Quota Usage
			$user_upload_image_data = $garage_image->select_user_upload_images($userdata['user_id']);
			$user_remote_image_data = $garage_image->select_user_remote_images($userdata['user_id']);

			//Check For Remote & Local Image Quotas
			if ( (($garage_image->image_is_remote() ) AND (count($user_remote_image_data) < $garage_image->get_user_remote_image_quota($userdata['user_id']))) OR (($garage_image->image_is_local() ) AND (count($user_image_data) < $garage_image->get_user_upload_image_quota($userdata['user_id']))) )
			{
				//Create Thumbnail & DB Entry For Image + Link To Item
				$image_id = $garage_image->process_image_attached('quartermile', $qmid);
				$garage->update_single_field(GARAGE_QUARTERMILE_TABLE, 'image_id', $image_id, 'id', $qmid);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ( (($garage_image->image_is_remote() ) AND (count($user_remote_image_data) >= $garage_image->get_user_remote_image_quota($userdata['user_id']))) OR (($garage_image->image_is_local() ) AND (count($user_image_data) >= $garage_image->get_user_upload_image_quota($userdata['user_id']))) )
			{
				redirect(append_sid("garage.$phpEx?mode=error&EID=4", true));
			}
		}
		//No Image Attached..We Need To Check If This Breaks The Site Rule
		else if ( ($garage_config['quartermile_image_required'] == '1') AND ($data['quart'] <= $garage_config['quartermile_image_required_limit']))
		{
			//That Time Requires An Image...Delete Entered Time And Notify User
			$garage_quartermile->delete_quartermile($qmid);
			redirect(append_sid("garage.$phpEx?mode=error&EID=26", true));
		}

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ( $data['pending'] == 1 )
		{
			$garage->pending_notification();
			$garage->update_single_field(GARAGE_CONFIG_TABLE, 'config_value', $data['pending'], 'config_name', 'items_pending');
		}

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	case 'edit_quartermile':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=edit_quartermile&QMID=$qmid&CID=$cid", true));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_quartermile.html')
		);

		//Count Dynoruns For Vehicle
		$count = $garage_dynorun->count_runs($cid);	

		//See If We Got Sent Here By Pending Page...If So We Need To Tell Update To Redirect Correctly
		$params = array('PENDING');
		$redirect = $garage->process_post_vars($params);

		//Pull Required Quartermile Data From DB
		$data = $garage_quartermile->select_quartermile_data($qmid);

		//If Dynorun Is Already Linked Display Dropdown Correctly
		if ( (!empty($data['rr_id'])) AND ($count > 0) )
		{
			$bhp_statement = $data['bhp'] . ' BHP @ ' . $data['bhp_unit'];
			$template->assign_block_vars('link_rr', array());
			$garage_template->dynorun_dropdown($data['rr_id'], $bhp_statement, $cid);
		}
		//Allow User To Link To Dynorun
		else if ( (empty($data['rr_id'])) AND ($count > 0) )
		{
			$template->assign_block_vars('link_rr', array());
			$garage_template->dynorun_dropdown(NULL, NULL, $cid);
		}

		//Build All HTML Parts
		$garage_template->edit_image($data['image_id'], $data['attach_file']);

		$template->assign_block_vars('level2', array());
		$template->assign_vars(array(
			'L_GARAGE_QUARTERMILE_TIMES' => $lang['Garage_Quartermile_Times'],
			'U_LEVEL2' => append_sid("garage.$phpEx?mode=view_own_vehicle&amp;CID=" . $cid),
			'L_LEVEL2' => $data['vehicle'],
			'L_TITLE'  => $lang['Edit_Time'],
			'L_BUTTON'  => $lang['Edit_Time'],
			'L_LINK_TO_RR'  => $lang['Link_To_RR'],
			'L_RT' => $lang['Rt_Explain'],
			'L_SIXTY' => $lang['Sixty_Explain'],
			'L_THREE' => $lang['Three_Explain'],
			'L_EIGHT' => $lang['Eight_Explain'],
			'L_EIGHTMPH' => $lang['Eightmph_Explain'],
			'L_THOU' => $lang['Thou_Explain'],
			'L_QUART' => $lang['Quart_Explain'],
			'L_QUARTMPH' => $lang['Quartmph_Explain'],
			'L_REQUIRED' => $lang['Required'],
			'CID' => $cid,
			'QMID' => $qmid,
			'RT' => $data['rt'],
			'SIXTY' => $data['sixty'],
			'THREE' => $data['three'],
			'EIGHT' => $data['eight'],
			'EIGHTMPH' => $data['eightmph'],
			'THOU' => $data['thou'],
			'QUART' => $data['quart'],
			'QUARTMPH' => $data['quartmph'],
			'PENDING_REDIRECT' => $redirect['PENDING'],
			'S_MODE_ACTION' => append_sid("garage.$phpEx?mode=update_quartermile"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'update_quartermile':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('rt', 'sixty', 'three', 'eight', 'eightmph', 'thou', 'quart', 'quartmph', 'rr_id', 'install_comments', 'editupload', 'image_id', 'pending_redirect');
		$data = $garage->process_post_vars($params);
		$data['pending'] = ($garage_config['enable_quartermile_approval'] == '1') ? 1 : 0 ;
		$data['time'] = time();

		//Checks All Required Data Is Present
		$params = array('quart');
		$garage->check_required_vars($params);

		//Update The Quartermile With Data Acquired
		$garage_quartermile->update_quartermile($data);

		//Update The Vehicle Timestamp Now...In Case We Get Redirected During Image Processing
		$garage_vehicle->update_vehicle_time($cid);

		//Removed The Old Image If Required By A Delete Or A New Image Existing
		if ( ($data['editupload'] == 'delete') OR ($data['editupload'] == 'new') )
		{
			$garage_image->delete_image($data['image_id']);
			$garage->update_single_field(GARAGE_QUARTERMILE_TABLE, 'image_id', 'NULL', 'id', $qmid);
		}

		//If Any Image Variables Set Enter The Image Handling
		if( $garage_image->image_attached() )
		{
			//Get All Users Images So We Can Workout Current Quota Usage
			$user_upload_image_data = $garage_image->select_user_upload_images($userdata['user_id']);
			$user_remote_image_data = $garage_image->select_user_remote_images($userdata['user_id']);

			//Check For Remote & Local Image Quotas
			if ( (($garage_image->image_is_remote() ) AND (count($user_remote_image_data) < $garage_image->get_user_remote_image_quota($userdata['user_id']))) OR (($garage_image->image_is_local() ) AND (count($user_image_data) < $garage_image->get_user_upload_image_quota($userdata['user_id']))) )
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('quartermile', $qmid);
				$garage->update_single_field(GARAGE_QUARTERMILE_TABLE, 'image_id', $image_id, 'id', $qmid);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ( (($garage_image->image_is_remote() ) AND (count($user_remote_image_data) >= $garage_image->get_user_remote_image_quota($userdata['user_id']))) OR (($garage_image->image_is_local() ) AND (count($user_image_data) >= $garage_image->get_user_upload_image_quota($userdata['user_id']))) )
			{
				redirect(append_sid("garage.$phpEx?mode=error&EID=4", true));
			}
		}
		//No Image Attached..We Need To Check If This Breaks The Site Rule
		else if ( ($garage_config['quartermile_image_required'] == '1') AND ($data['quart'] <= $garage_config['quartermile_image_required_limit']))
		{
			//That Time Requires An Image...Delete Entered Time And Notify User
			$garage_quartermile->delete_quartermile_time($qmid);
			redirect(append_sid("garage.$phpEx?mode=error&EID=26", true));
		}

		//If Editting From Pending Page Redirect Back To There Instead
		if ( $data['pending_redirect'] == 'YES' )
		{
			redirect(append_sid("garage.$phpEx?mode=garage_pending", true));
		}

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	case 'delete_quartermile':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Delete The Quartermie Time
		$garage_quartermile->delete_quartermile_time($qmid);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;
	
	case 'add_rollingroad':

		//Let Check That Rollingroad Runs Are Allowed...If Not Redirect
		if ($garage_config['enable_rollingroad'] == '0')
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=18", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD', "garage.$phpEx?mode=error&EID=14");

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);
		
		include($phpbb_root_path . 'includes/page_header.' . $phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_rollingroad.html')
		);

		//Build Required HTML Components Like Drop Down Boxes.....
		$garage_template->attach_image('vehicle');

		$template->assign_vars(array(
			'L_NOT_LISTED_YET' => $lang['Not_Listed_Yet'],
			'L_HERE' => $lang['Here'],
			'L_REQUIRED' => $lang['Required'],
			'L_GARAGE_ROLLINGROAD_RUNS' => $lang['Garage_Rollingroad_Runs'],
			'L_TITLE'  => $lang['Add_New_Run'],
			'L_BUTTON'  => $lang['Add_New_Run'],
			'L_BOOST' => $lang['Boost_Explain'],
			'L_DYNO_CENTER' => $lang['Dyno_Center'],
			'L_PEAKPOINT' => $lang['Peakpoint_Explain'],
			'L_BHP' => $lang['Bhp_Explain'],
			'L_TORQUE' => $lang['Torque_Explain'],
			'L_NITROUS' => $lang['Nitrous_Explain'],
			'NITROUS_UNIT_LIST' => $garage_template->dropdown('nitrous', $nitrous_types_text, $nitrous_types),
			'TORQUE_UNIT_LIST' => $garage_template->dropdown('torque_unit', $power_types, $power_types),
			'BHP_UNIT_LIST' => $garage_template->dropdown('bhp_unit', $power_types, $power_types),
			'BOOST_UNIT_LIST' => $garage_template->dropdown('boost_unit', $boost_types, $boost_types),
			'CID' => $cid,
			'S_MODE_ACTION' => append_sid("garage.$phpEx?mode=insert_rollingroad"))
         	);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'insert_rollingroad':

		//Let Check That Rollingroad Runs Are Allowed...If Not Redirect
		if ($garage_config['enable_rollingroad'] == '0')
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=18", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD', "garage.$phpEx?mode=error&EID=14");

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('dynocenter', 'bhp', 'bhp_unit', 'torque', 'torque_unit', 'boost', 'boost_unit', 'nitrous', 'peakpoint');
		$data = $garage->process_post_vars($params);
		$data['pending'] = ($garage_config['enable_rollingroad_approval'] == '1') ? 1 : 0 ;
		$data['time'] = time();

		//Checks All Required Data Is Present
		$params = array('bhp', 'bhp_unit');
		$garage->check_required_vars($params);

		//Update The Dynorun With Data Acquired
		$rrid = $garage_dynorun->insert_dynorun($data);

		//Update The Time Now...In Case We Get Redirected During Image Processing
		$garage_vehicle->update_vehicle_time($cid);

		//If Any Image Variables Set Enter The Image Handling
		if( $garage_image->image_attached() )
		{
			//Get All Users Images So We Can Workout Current Quota Usage
			$user_upload_image_data = $garage_image->select_user_upload_images($userdata['user_id']);
			$user_remote_image_data = $garage_image->select_user_remote_images($userdata['user_id']);

			//Check For Remote & Local Image Quotas
			if ( (($garage_image->image_is_remote() ) AND (count($user_remote_image_data) < $garage_image->get_user_remote_image_quota($userdata['user_id']))) OR (($garage_image->image_is_local() ) AND (count($user_image_data) < $garage_image->get_user_upload_image_quota($userdata['user_id']))) )
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('rollingroad', $rrid);
				$garage->update_single_field(GARAGE_ROLLINGROAD_TABLE,'image_id', $image_id, 'id', $rrid);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ( (($garage_image->image_is_remote() ) AND (count($user_remote_image_data) >= $garage_image->get_user_remote_image_quota($userdata['user_id']))) OR (($garage_image->image_is_local() ) AND (count($user_image_data) >= $garage_image->get_user_upload_image_quota($userdata['user_id']))) )
			{
				redirect(append_sid("garage.$phpEx?mode=error&EID=4", true));
			}
		}
		//No Image Attached..We Need To Check If This Breaks The Site Rule
		else if ( ($garage_config['dynorun_image_required'] == '1') AND ($data['bhp'] >= $garage_config['dynorun_image_required_limit']))
		{
			//That Time Requires An Image...Delete Entered Time And Notify User
			$garage_dynorun->delete_dynorun($rrid);
			redirect(append_sid("garage.$phpEx?mode=error&EID=26", true));
		}

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ( $data['pending'] == 1 )
		{
			$garage->pending_notification();
			$garage->update_single_field(GARAGE_CONFIG_TABLE, 'config_value', $data['pending'], 'config_name', 'items_pending');
		}

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	case 'edit_rollingroad':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=edit_rollingroad&RRID=$rrid&CID=$cid", true));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_rollingroad.html')
		);

		//Pull Required Dynorun Data From DB
		$data = $garage_dynorun->select_dynorun_data($rrid);

		//See If We Got Sent Here By Pending Page...If So We Need To Tell Update To Redirect Correctly
		$params = array('PENDING');
		$redirect = $garage->process_post_vars($params);

		//Build All Required HTML
		$garage_template->edit_image($data['image_id'], $data['attach_file']);

		$template->assign_block_vars('level2', array());
		$template->assign_vars(array(
			'U_LEVEL2' => append_sid("garage.$phpEx?mode=view_own_vehicle&amp;CID=" . $cid),
			'L_LEVEL2' => $data['vehicle'],
			'L_GARAGE_ROLLINGROAD_RUNS' => $lang['Garage_Rollingroad_Runs'],
			'L_TITLE'  => $lang['Edit_Run'],
			'L_BUTTON'  => $lang['Edit_Run'],
			'L_BOOST' => $lang['Boost_Explain'],
			'L_DYNO_CENTER' => $lang['Dyno_Center'],
			'L_PEAKPOINT' => $lang['Peakpoint_Explain'],
			'L_BHP' => $lang['Bhp_Explain'],
			'L_TORQUE' => $lang['Torque_Explain'],
			'L_NITROUS' => $lang['Nitrous_Explain'],
			'L_REQUIRED' => $lang['Required'],
			'CID' => $cid,
			'RRID' => $rrid,
			'DYNOCENTER' => $data['dynocenter'],
			'BHP' => $data['bhp'],
			'TORQUE' => $data['torque'],
			'BOOST' => $data['boost'],
			'NITROUS' => $data['nitrous'],
			'PEAKPOINT' => $data['peakpoint'],
			'PENDING_REDIRECT' => $redirect['PENDING'],
			'NITROUS_UNIT_LIST' => $garage_template->dropdown('nitrous', $nitrous_types_text, $nitrous_types, $data['nitrous']),
			'BOOST_UNIT_LIST' => $garage_template->dropdown('boost_unit', $boost_types, $boost_types, $data['boost_unit']),
			'TORQUE_UNIT_LIST' => $garage_template->dropdown('torque_unit', $power_types, $power_types, $data['torque_unit']),
			'BHP_UNIT_LIST' => $garage_template->dropdown('bhp_unit', $power_types, $power_types, $data['bhp_unit']),
			'S_MODE_ACTION' => append_sid("garage.$phpEx?mode=update_rollingroad"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'update_rollingroad':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('dynocenter', 'bhp', 'bhp_unit', 'torque', 'torque_unit', 'boost', 'boost_unit', 'nitrous', 'peakpoint', 'editupload', 'image_id', 'pending_redirect');
		$data = $garage->process_post_vars($params);
		$data['pending'] = ($garage_config['enable_rollingroad_approval'] == '1') ? 1 : 0 ;
		$data['time'] = time();

		//Checks All Required Data Is Present
		$params = array('bhp', 'bhp_unit');
		$garage->check_required_vars($params);

		//Update The Dynorun With Data Acquired
		$garage_dynorun->update_dynorun($data);

		//Update The Time Now...In Case We Get Redirected During Image Processing
		$garage_vehicle->update_vehicle_time($cid);

		//Removed The Old Image If Required By A Delete Or A New Image Existing
		if ( ($data['editupload'] == 'delete') OR ($data['editupload'] == 'new') )
		{
			$garage_image->delete_image($data['image_id']);
			$garage->update_single_field(GARAGE_ROLLINGROAD_TABLE, 'image_id', 'NULL', 'id', $rrid);
		}

		//If Any Image Variables Set Enter The Image Handling
		if( $garage_image->image_attached() )
		{
			//Get All Users Images So We Can Workout Current Quota Usage
			$user_upload_image_data = $garage_image->select_user_upload_images($userdata['user_id']);
			$user_remote_image_data = $garage_image->select_user_remote_images($userdata['user_id']);

			//Check For Remote & Local Image Quotas
			if ( (($garage_image->image_is_remote() ) AND (count($user_remote_image_data) < $garage_image->get_user_remote_image_quota($userdata['user_id']))) OR (($garage_image->image_is_local() ) AND (count($user_image_data) < $garage_image->get_user_upload_image_quota($userdata['user_id']))) )
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('rollingroad', $rrid);
				$garage->update_single_field(GARAGE_ROLLINGROAD_TABLE, 'image_id', $image_id, 'id', $rrid);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ( (($garage_image->image_is_remote() ) AND (count($user_remote_image_data) >= $garage_image->get_user_remote_image_quota($userdata['user_id']))) OR (($garage_image->image_is_local() ) AND (count($user_image_data) >= $garage_image->get_user_upload_image_quota($userdata['user_id']))) )
			{
				redirect(append_sid("garage.$phpEx?mode=error&EID=4", true));
			}
		}
		//No Image Attached..We Need To Check If This Breaks The Site Rule
		else if ( ($garage_config['dynorun_image_required'] == '1') AND ($data['bhp'] >= $garage_config['dynorun_image_required_limit']))
		{
			//That Time Requires An Image...Delete Entered Time And Notify User
			$garage_dynorun->delete_dynorun($rrid);
			redirect(append_sid("garage.$phpEx?mode=error&EID=26", true));
		}

		//If Editting From Pending Page Redirect Back To There Instead
		if ( $data['pending_redirect'] == 'YES' )
		{
			redirect(append_sid("garage.$phpEx?mode=garage_pending", true));
		}

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	case 'delete_rollingroad':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Delete The Dynorun
		$garage_dynorun->delete_dynorun($rrid);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	case 'add_insurance':

		//Let Check That Insurance Premiums Are Allowed...If Not Redirect
		if ($garage_config['enable_insurance'] == '0')
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=18", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD', "garage.$phpEx?mode=error&EID=14");

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_insurance.html')
		);

		//Build All Required HTML Components
		$garage_template->attach_image('modification');
		$garage_template->insurance_dropdown();

		$template->assign_vars(array(
			'L_TITLE' => $lang['Add_Premium'],
			'L_BUTTON' => $lang['Add_Premium'],
			'L_NOT_LISTED_YET' => $lang['Not_Listed_Yet'],
			'L_HERE' => $lang['Here'],
			'L_REQUIRED' => $lang['Required'],
			'L_ADD_PREMIUM' => $lang['Add_Premium'],
			'L_PREMIUM_PRICE' => $lang['Premium_Price'],
			'L_INSURANCE_COMPANY' => $lang['Insurance_Company'],
			'L_COVER_TYPE' => $lang['Cover_Type'],
			'L_COMMENTS' => $lang['Comments'],
			'S_MODE_ACTION' => append_sid("garage.$phpEx?mode=insert_insurance"),
			'U_SUBMIT_BUSINESS' => append_sid("garage.$phpEx?mode=user_submit_business&CID=$cid&mode_redirect=add_insurance&BUSINESS=insurance"),
			'CID' => $cid,
			'COVER_TYPE_LIST' => $garage_template->dropdown('cover_type', $cover_types, $cover_types))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'insert_insurance':

		//Let Check That Insurance Premiums Are Allowed...If Not Redirect
		if ($garage_config['enable_insurance'] == '0')
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=18", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD', "garage.$phpEx?mode=error&EID=14");

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('business_id', 'premium', 'cover_type', 'comments');
		$data = $garage->process_post_vars($params);
		$data['time'] = time();

		//Checks All Required Data Is Present
		$params = array('business_id', 'premium', 'cover_type');
		$garage->check_required_vars($params);

		//Insert The Insurnace Premium
		$garage_insurance->insert_premium($data);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

	case 'edit_insurance':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=edit_insurance&IND_ID=$ins_id&CID=$cid", true));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_insurance.html')
		);

		//Pull Required Insurance Premium Data From DB
		$data = $garage_insurance->select_insurance_data($ins_id);

		//Build Required HTML Components
		$garage_template->insurance_dropdown($data['business_id'], $data['title']);

		$template->assign_block_vars('level2', array());
		$template->assign_vars(array(
			'U_LEVEL2' 		=> append_sid("garage.$phpEx?mode=view_own_vehicle&amp;CID=" . $cid),
			'L_LEVEL2' 		=> $data['vehicle'],
			'L_TITLE' 		=> $lang['Edit_Premium'],
			'L_BUTTON' 		=> $lang['Edit_Premium'],
			'L_INSURANCE_COMPANY' 	=> $lang['Insurance_Company'],
			'L_PREMIUM_PRICE' 	=> $lang['Premium_Price'],
			'L_COVER_TYPE' 		=> $lang['Cover_Type'],
			'L_COMMENTS' 		=> $lang['Comments'],
			'L_REQUIRED' 		=> $lang['Required'],
			'S_MODE_ACTION' 	=> append_sid("garage.$phpEx?mode=update_insurance"),
			'INS_ID' 		=> $ins_id,
			'CID' 			=> $cid,
			'PREMIUM' 		=> $data['premium'],
			'COMMENTS' 		=> $data['comments'],
			'COVER_TYPE_LIST' 	=> $garage_template->dropdown('cover_type', $cover_types, $cover_types, $data['cover_type']))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'update_insurance':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('business_id', 'premium', 'cover_type', 'comments');
		$data = $garage->process_post_vars($params);

		//Checks All Required Data Is Present
		$params = array('business_id', 'premium', 'cover_type');
		$garage->check_required_vars($params);

		//Update The Insurance Premium With Data Acquired
		$garage_insurnace->update_premium($data);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;
	
	case 'delete_insurance':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Delete Insurance Premium
		$garage_insurance->delete_premium($ins_id);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	//Mode To Display A List Of Vehicles..Also Used To Display Search Results For Search By Make/Model/User
	case 'browse':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('BROWSE', "garage.$phpEx?mode=error&EID=15");

		//Set Required Values To Defaults If They Are Empty
		$start = (empty($start)) ? '0' : $start;
		$order_by = (empty($sort)) ? 'date_updated' : $sort;
		$sort_order = (empty($order)) ? 'DESC' : $order;

		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_browse.html')
		);

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);

		//Check If This Is A Search....If So We Have A Bit More Work To Do.....
		if ((isset($HTTP_GET_VARS['search'])) OR (isset($HTTP_POST_VARS['search'])))
		{
			$search = (isset($HTTP_POST_VARS['search'])) ? htmlspecialchars($HTTP_POST_VARS['search']) : htmlspecialchars($HTTP_GET_VARS['search']);

			$search_data = $garage_model->build_search_for_user_make_model();
			$search_data['pagination'] = ';search=yes&amp';

			$template->assign_block_vars('level3_nolink', array());
	      		$template->assign_block_vars('switch_search', array());
			$template->assign_vars(array(
				'SEARCH_MESSAGE' => $search_data['search_message'])
			);
		}

		//Setup Arrays For Producing Sort Options Drop Down Selection Box
		$sort_types_text = array($lang['Last_Created'], $lang['Last_Updated'], $lang['Owner'], $lang['Year'], $lang['Make'], $lang['Model'],  $lang['Colour'], $lang['Total_Views'], $lang['Total_Mods']);
		$sort_types = array('date_created', 'date_updated', 'username', 'made_year', 'make', 'model', 'color', 'views', 'total_mods');

		//Build All Required HTML
		$garage_template->sort_order($sort_order);

		//Get All Vehicle Data....
		$data = $garage_vehicle->select_all_vehicle_data($search_data['where'], $order_by, $sort_order, $start, $garage_config['cars_per_page']);
		for ($i = 0; $i < count($data); $i++)
      		{
			$image_attached = '';
            		if ($data[$i]['image_id'])
			{
				$image_attached = '<img hspace="1" vspace="1" src="' . $images['vehicle_image_attached'] . '" alt="' . $lang['Vehicle_Image_Attahced'] . '" title="' . $lang['Vehicle_Image_Attached'] . '" border="0" />';
			}

			$row_color = ( !($i % 2) ) ? $theme['td_color1'] : $theme['td_color2'];
			$row_class = ( !($i % 2) ) ? $theme['td_class1'] : $theme['td_class2'];
	
			$template->assign_block_vars('vehiclerow', array(
				'ROW_NUMBER' => $i + ( $start + 1 ),
				'ROW_COLOR' => '#' . $row_color,
				'ROW_CLASS' => $row_class,
				'IMAGE_ATTACHED' => $image_attached,
				'YEAR' => $data[$i]['made_year'],
				'MAKE' => $data[$i]['make'],
				'COLOUR' => $data[$i]['color'],
				'UPDATED' => create_date($board_config['default_dateformat'], $data[$i]['date_updated'], $board_config['board_timezone']),
				'VIEWS' => $data[$i]['views'],
				'MODS' => $data[$i]['total_mods'],
				'MODEL' => $data[$i]['model'],
				'OWNER' => $data[$i]['username'],
				'U_VIEW_VEHICLE' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=" . $data['id']),
				'U_VIEW_PROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $data['member_id']))
			);
		}

		//Count Total Returned For Pagination...Notice No $start or $end to get complete count
		$count = $garage_vehicle->select_all_vehicle_data($search_data['where'], $order_by, $sort_order);

		$pagination = generate_pagination("garage.$phpEx?mode=browse&amp" . $search_data['make_pagination'] . $search_data['model_pagination'] . $search_data['pagination'] . ";sort=$sort&amp;order=$sort_order", $count[0]['total'], $garage_config['cars_per_page'], $start);

		$template->assign_block_vars('level2', array());
		$template->assign_vars(array(
			'L_GOTO_PAGE' => $lang['Goto_page'],
			'L_LEVEL2' => $lang['Browse'],
			'L_SORTED_BY' => $lang['Sorted_By'],
			'L_IN' => $lang['In'],
			'L_GO' => $lang['Go'],
			'L_YEAR' => $lang['Year'],
	  		'L_MAKE' => $lang['Make'],
			'L_MODEL' => $lang['Model'],
			'L_COLOUR' => $lang['Colour'],
			'L_OWNER' => $lang['Owner'],
			'L_VIEWS' => $lang['Views'],
			'L_MODS' => $lang['Mods'],
			'L_UPDATED' => $lang['Updated'],
			'U_LEVEL2' => append_sid("garage.$phpEx?mode=browse"),
			'MAKE_ID' => $search_data['make_id'],
			'MODEL_ID' => $search_data['model_id'],
			'SEARCH' => $search,
			'PAGINATION' => $pagination,
			'PAGE_NUMBER' => sprintf($lang['Page_of'], ( floor( $start / $garage_config['cars_per_page'] ) + 1 ), ceil( $count[0]['total'] / $garage_config['cars_per_page'] )), 
			'S_SORT_SELECT' => $garage_template->dropdown('sort', $sort_types_text, $sort_types, $sort),
			'S_MODE_ACTION' => append_sid("garage.$phpEx?mode=browse"))
		);
	
		//Modify Nav Links If Search Was Performed!!
		if (!empty($search_data['search_message']))
		{
			$template->assign_vars(array(
				'L_LEVEL2' => $lang['Search'],
				'U_LEVEL2' => append_sid("garage.$phpEx?mode=search"))
			);
		}

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	//Mode To Display Searches Of Insurance
	case 'search_insurance':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('BROWSE',"garage.$phpEx?mode=error&EID=15");

		//Set Required Values To Defaults If They Are Empty
		$start = (empty($start)) ? '0' : $start;
		$order_by = (empty($sort)) ? 'premium' : $sort;
		$sort_order = (empty($order)) ? 'ASC' : $order;

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);

		$search_data = $garage_model->build_search_for_user_make_model();
		
		if (!empty($search_data['search_message']))
		{
			$template->assign_block_vars('level2', array());
			$template->assign_block_vars('level3_nolink', array());
			$template->assign_vars(array(
				'L_LEVEL2' => $lang['Search'],
				'L_LEVEL3' => $lang['Insurance_Results'],
				'U_LEVEL2' => append_sid("garage.$phpEx?mode=search"))
			);
		}

		$sort_types_text = array($lang['Price'], $lang['Mod_Price'], $lang['Owner'], $lang['Premium'], $lang['Cover_Type'],  $lang['Business_Name']);
		$sort_types = array('g.price', 'total_spent', 'username', 'premium', 'cover_type', 'name');

	      	$template->assign_block_vars('switch_search', array());
		$template->assign_vars(array(
			'SEARCH_MESSAGE' => $search_data['search_message'])
		);

		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body' => 'garage_browse_insurance.html')
		);

		//Pre Build All Side Menus
		$garage_template->sort_order($sort_order);

		//Get All Insurance Data....
		$data = $garage_insurance->select_all_insurance_data($search_data['where'], $order_by, $sort_order, $start, $garage_config['cars_per_page']);
		for ($i = 0; $i < count($data); $i++)
      		{
			$row_color = ( !($i % 2) ) ? $theme['td_color1'] : $theme['td_color2'];
			$row_class = ( !($i % 2) ) ? $theme['td_class1'] : $theme['td_class2'];

			$template->assign_block_vars('vehiclerow', array(
				'ROW_COLOR' => '#' . $row_color,
				'ROW_CLASS' => $row_class,
				'U_VIEW_VEHICLE' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=" . $data[$i]['id']),
				'U_VIEW_PROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $data[$i]['user_id']),
				'U_VIEW_BUSINESS' => append_sid("garage.$phpEx?mode=view_insurance_business&amp;business_id=" . $data[$i]['business_id']),
				'VEHICLE' => $data[$i]['vehicle'],
				'USERNAME' => $data[$i]['username'],
				'BUSINESS' => $data[$i]['title'],
				'PRICE' => $data[$i]['price'],
				'MOD_PRICE' => $data[$i]['total_spent'],
				'PREMIUM' => $data[$i]['premium'],
				'COVER_TYPE' => $data[$i]['cover_type'])
			);
		}

		//Count Total Returned For Pagination...Notice No $start or $end to get complete count
		$count = count($garage_insurance->select_all_insurance_data($search_data['where'], $order_by, $sort_order, '', ''));
		$pagination = generate_pagination("garage.$phpEx?mode=search_insurance&amp;make_id=" . $search_data['make_id'] . "&amp;model_id=" . $search_data['model_id'] . "&amp;sort=$sort&amp;order=$sort_order", $count, $garage_config['cars_per_page'], $start);

		$template->assign_vars(array(
			'PAGINATION' 	=> $pagination,
			'PAGE_NUMBER' 	=> sprintf($lang['Page_of'], ( floor( $start / $garage_config['cars_per_page'] ) + 1 ), ceil( $count / $garage_config['cars_per_page'] )), 
			'L_GOTO_PAGE' 	=> $lang['Goto_page'],
			'L_SORTED_BY' 	=> $lang['Insurance_Sorted_By'],
			'L_IN' 		=> $lang['In'],
			'L_GO' 		=> $lang['Go'],
			'L_VEHICLE' 	=> $lang['Vehicle'],
			'L_PRICE' 	=> $lang['Price'],
			'L_MOD_PRICE' 	=> $lang['Mod_Price'],
			'L_OWNER' 	=> $lang['Owner'],
			'L_PREMIUM' 	=> $lang['Premium'],
			'L_COVER_TYPE' 	=> $lang['Cover_Type'],
			'L_BUSINESS'	=> $lang['Business_Name'],
			'S_SORT_SELECT' => $garage_template->dropdown('sort', $sort_types_text, $sort_types, $sort),
			'S_MODE_ACTION' => append_sid("garage.$phpEx?mode=search_insurance"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	//Display Search Options Page...
	case 'search':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('BROWSE', "garage.$phpEx?mode=error&EID=15");

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'javascript' => 'garage_vehicle_select_javascript.html',
			'body'   => 'garage_search.html')
		);

		//Build All Required Javascript And Arrays
		$template->assign_vars(array(
			'VEHICLE_ARRAY' => $garage_template->vehicle_array())
		);
		$template->assign_var_from_handle('JAVASCRIPT', 'javascript');

		//Show Search By Insurance If Insurance Enabled In ACP
		if ( $garage_config['enable_insurance'] == TRUE )
		{
			$template->assign_block_vars('search_by_insurance', array());
		}

		$template->assign_block_vars('level2', array());
		$template->assign_vars(array(
			'L_SEARCH_GARAGE_TITLE' => $lang['Search_Garage'],
			'L_SEARCH_BY_MEMBER' 	=> $lang['Search_By_Member'],
			'L_SEARCH_BY_VEHICLE' 	=> $lang['Search_By_Vehicle'],
			'L_SEARCH_INSURANCE' 	=> $lang['Search_Insurance_By_Vehicle'],
			'L_MEMBER_NAME' 	=> $lang['Member_Name'],
			'L_MAKE' 		=> $lang['Make'],
			'L_MODEL' 		=> $lang['Model'],
			'L_SELECT_MODEL' 	=> $lang['Select_Model'],
			'L_ANY_MODEL' 		=> $lang['Any_Model'],
			'U_LEVEL2' 		=> append_sid("garage.$phpEx?mode=search"),
			'L_LEVEL2' 		=> $lang['Search'],
			'S_MODE_ACTION_2' 	=> append_sid("garage.$phpEx?mode=search_insurance"),
			'S_MODE_ACTION' 	=> append_sid("garage.$phpEx?mode=browse"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'view_vehicle':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('BROWSE', "garage.$phpEx?mode=error&EID=15");

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_view_vehicle.html')
		);

		//Display Vehicle With Owner Set to 'NO'
		$garage_vehicle->display_vehicle('NO');

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
      		$template->pparse('body'); 

		//Update View Count For Vehicle
		$garage->update_view_count(GARAGE_TABLE, 'views', 'id', $cid);

		break;

	case 'view_modification':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('BROWSE', "garage.$phpEx?mode=error&EID=15");

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);

		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_view_modification.html')
		);

		//Pull Required Modification Data From DB
		$data = $garage_modification->select_modification_data($mid);

		//Build The Owners Avatar Image If Any...
		$avatar_img = '';
		if ( $data['user_avatar_type'] AND $data['user_allowavatar'] )
		{
			switch( $data['user_avatar_type'] )
			{
				case USER_AVATAR_UPLOAD:
					$data['avatar_img'] = ( $board_config['allow_avatar_upload'] ) ? '<img src="' . $board_config['avatar_path'] . '/' . $data['user_avatar'] . '" alt="" border="0" />' : '';
					break;
				case USER_AVATAR_REMOTE:
					$data['avatar_img'] = ( $board_config['allow_avatar_remote'] ) ? '<img src="' . $data['user_avatar'] . '" alt="" border="0" />' : '';
					break;
				case USER_AVATAR_GALLERY:
					$data['avatar_img'] = ( $board_config['allow_avatar_local'] ) ? '<img src="' . $board_config['avatar_gallery_path'] . '/' . $data['user_avatar'] . '" alt="" border="0" />' : '';
					break;
			}
		}

		//If Images Exists For Modification..Display Thumbnail
		if ( ($data['attach_id']) AND ($data['attach_is_image']) AND (!empty($data['attach_thumb_location'])) AND (!empty($data['attach_location'])) )
		{
			$thumb_image = GARAGE_UPLOAD_PATH . $data['attach_thumb_location'];
			$data['modification_image'] = '<a href="garage.' . $phpEx . '?mode=view_gallery_item&amp;type=garage_mod&amp;image_id=' . $data['attach_id'] . '" title="' . $data['attach_file'] . '" target="_blank"><img hspace="5" vspace="5" src="' . $thumb_image . '" class="attach"  /></a>';
		}

		$template->assign_block_vars('level1', array());
		$template->assign_vars(array(
		        'L_PRODUCT_RATING' => $lang['Product_Rating'],
		        'L_INSTALLATION_RATING' => $lang['Installation_Rating'],
		        'L_INSTALLED_BY' => $lang['Installed_By'],
            		'L_CREATED' => $lang['Created'],
            		'L_UPDATED' => create_date($board_config['default_dateformat'], $data['date_updated'], $board_config['board_timezone']), 
            		'L_VEHICLE' => $lang['Vehicle'],
            		'L_PURCHASED_FROM' => $lang['Purchased_From'],
            		'L_PURCHASED_PRICE' => $lang['Purchased_Price'],
            		'L_INSTALLATION_PRICE' => $lang['Installation_Price'],
            		'L_OWNER' => $lang['Owner'],
			'L_MODIFICATION' => $lang['Modification'],
			'L_CATEGORY' => $lang['Category'],
			'L_RATING' => $lang['Rating'],
			'L_COMMENTS' => $lang['Comments'],
			'L_LEVEL1' => $data['vehicle'],
			'YEAR' => $data['made_year'],
			'MAKE' => $data['make'],
			'MODEL' => $data['model'],
            		'PRODUCT_RATING' => $data['product_rating'],
            		'INSTALL_RATING' => $data['install_rating'],
            		'BUSINESS_NAME' => $data['business_name'],
			'U_VIEW_PROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $data['member_id']),
			'U_LEVEL1' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=$cid"),
			'U_VIEW_GARAGE_BUSINESS' => append_sid("garage.$phpEx?mode=view_garage_business&amp;business_id=" . $data['install_business_id']),
			'U_VIEW_SHOP_BUSINESS' => append_sid("garage.$phpEx?mode=view_shop_business&amp;business_id=" . $data['business_id']),
			'BUSINESS' => $data['install_business_name'],
			'USERNAME' => $data['username'],
            		'AVATAR_IMG' => $data['avatar_img'],
            		'MODIFICATION_IMAGE' => $data['modification_image'],
            		'DATE_UPDATED' => $data['updated'],
            		'TITLE' => $data['title'],
            		'PRICE' => $data['price'],
            		'INSTALL_PRICE' => $data['install_price'],
            		'CURRENCY' => $data['currency'],
            		'CATEGORY' => $data['category_title'],
            		'COMMENTS' => $data['comments'])
         	);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
      		$template->pparse('body'); 

		break;

	case 'view_own_vehicle':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD', "garage.$phpEx?mode=error&EID=14");

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_view_vehicle.html')
		);

		//Display Vehicle With Owner Set to 'YES'
		$garage_vehicle->display_vehicle('YES');

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
      		$template->pparse('body'); 

		break;

	case 'moderate_vehicle':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD', "garage.$phpEx?mode=error&EID=14");

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_view_vehicle.html')
		);

		//Display Vehicle With Owner Set to 'YES'..Since You Are Moderating You Need To See All Owner Options
		$garage_vehicle->display_vehicle('YES');

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
      		$template->pparse('body'); 

		break;

	case 'set_main':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD', "garage.$phpEx?mode=error&EID=14");

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get Posted Data If We Got Here From Moderating
		$params = array('user_id');
		$data = $garage->process_post_vars($params);

		//Now We Update All Vehicles They Own To Not Main Vehicle
		if (!empty($data['user_id']))
		{
			$garage->update_single_field(GARAGE_TABLE, 'main_vehicle', 0 ,'member_id', $data['user_id']);
		}
		else
		{
			$garage->update_single_field(GARAGE_TABLE, 'main_vehicle', 0, 'member_id', $userdata['user_id']);
		}

		//Now We Update This Vehicle To The Main Vehicle
		$garage->update_single_field(GARAGE_TABLE, 'main_vehicle', 1, 'id', $cid);

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	case 'insert_gallery_image':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('UPLOAD', "garage.$phpEx?mode=error&EID=16");

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Pull Vehicle Data So We Can Check For Hilite Image
		$data = $garage_vehicle->select_vehicle_data($cid);

		//If Any Image Variables Set Enter The Image Handling
		if( $garage_image->image_attached() )
		{
			//Get All Users Images So We Can Workout Current Quota Usage
			$user_upload_image_data = $garage_image->select_user_upload_images($userdata['user_id']);
			$user_remote_image_data = $garage_image->select_user_remote_images($userdata['user_id']);

			//Check For Remote & Local Image Quotas
			if ( (($garage_image->image_is_remote() ) AND (count($user_remote_image_data) < $garage_image->get_user_remote_image_quota($userdata['user_id']))) OR (($garage_image->image_is_local() ) AND (count($user_image_data) < $garage_image->get_user_upload_image_quota($userdata['user_id']))) )
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('vehicle', $cid);
				$garage_image->insert_gallery_image($image_id);
				//If First Image And Set As Vehicle Hilite Image
				if ( empty($data['image_id']))
				{
					$garage->update_single_field(GARAGE_TABLE, 'image_id', $image_id, 'id', $cid);
				}
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ( (($garage_image->image_is_remote() ) AND (count($user_remote_image_data) >= $garage_image->get_user_remote_image_quota($userdata['user_id']))) OR (($garage_image->image_is_local() ) AND (count($user_image_data) >= $garage_image->get_user_upload_image_quota($userdata['user_id']))) )
			{
				redirect(append_sid("garage.$phpEx?mode=error&EID=4", true));
			}
		}

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=manage_vehicle_gallery&CID=$cid", true));

		break;

	case 'view_gallery_item':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('BROWSE', "garage.$phpEx?mode=error&EID=15");

		//Increment View Counter For This Image
		$garage->update_view_count(GARAGE_IMAGES_TABLE, 'attach_hits', 'attach_id', $image_id);

		//Pull Required Image Data From DB
		$data = $garage_image->select_image_data($image_id);

		//Check To See If This Is A Remote Image
		if ( preg_match( "/^http:\/\//i", $data['attach_location']) )
		{
			//Redirect Them To The Remote Image
			header("Location: " . $data['attach_location']);
			exit;
		}
		//Looks Like It's A Local Image...So Lets Display It
		else
		{
	       		switch ( $data['attach_ext'] )
			{
				case '.png':
					header('Content-type: image/png');
					break;
				case '.gif':
					header('Content-type: image/gif');
					break;
				case '.jpg':
					header('Content-type: image/jpeg');
					break;
				default:
					die('Unsupported File Type');
			}
			readfile($phpbb_root_path . GARAGE_UPLOAD_PATH . $data['attach_location']);
        		exit;
		}

		break;

	case 'manage_vehicle_gallery':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('UPLOAD', "garage.$phpEx?mode=error&EID=16");

		//Check Vehicle Ownership		
		$garage_vehicle->check_ownership($cid);
		
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_manage_vehicle_gallery.html')
		);

		//Pre Build All Side Menus
		$garage_template->attach_image('vehicle');

		//Pull Vehicle Data From DB So We Can Check For Hilite Image
		$vehicle_data = $garage_vehicle->select_vehicle_data($cid);

		//Pull Vehicle Gallery Data From DB
		$data = $garage_image->select_gallery_data($cid);

		//Process Each Image From Vehicle Gallery
		for ($i = 0; $i < count($data); $i++)
		{
			//Work Out If Image Is Current Hilite Else Produce Link To Make It So..
			if ( $data[$i]['image_id'] == $vehicle_data['image_id'] )
			{
				$hilite = '<i>' . $lang['Current_Hilite_Image'] . '</i>';
			}
			else
			{
				$temp_url = append_sid("garage.$phpEx?mode=set_hilite&amp;image_id=" . $data[$i]['image_id'] . "&amp;CID=" . $cid, true);
				$hilite= '<a href="' . $temp_url . '">' . $lang['Set_Hilite_Image'] . '</a>';
			}

			//Produce Actual Image Thumbnail And Link It To Full Size Version..
			if ( ($data[$i]['image_id']) AND ($data[$i]['attach_is_image']) AND (!empty($data[$i]['attach_thumb_location'])) AND (!empty($data[$i]['attach_location'])) )
			{
				// Form the image link
				$thumb_image = $phpbb_root_path . GARAGE_UPLOAD_PATH . $data[$i]['attach_thumb_location'];
				$image = '<a href="garage.' . $phpEx . '?mode=view_gallery_item&amp;type=garage_mod&amp;image_id=' . $data[$i]['image_id'] . '" title="' . $data[$i]['attach_file'] . '" target="_blank"><img hspace="5" vspace="5" src="' . $thumb_image . '" class="attach"  /></a>';
			}

			$template->assign_block_vars('pic_row', array(
				'THUMB_IMAGE' => $image,
				'U_REMOVE_IMAGE' => append_sid("garage.$phpEx?mode=remove_gallery_item&amp;&amp;CID=$cid&amp;image_id=" . $data[$i]['image_id']),
				'HILITE' => $hilite)
			);
		}

		$template->assign_vars(array(
        	    	'L_NOTE' => $lang['Manage_Vehicle_Gallery_Note'],
        	    	'L_IMAGE' => $lang['Image'],
        	    	'L_REMOVE_IMAGE' => $lang['Remove_Image'],
        	    	'L_REMOVE' => $lang['Remove'],
        	    	'L_HILITE_IMAGE' => $lang['Hilite_Image'],
			'L_MANAGE_VEHICLE_GALLERY' => $lang['Manage_Vehicle_Gallery'],
        	    	'L_CURRENT_HILITE_IMAGE' => $lang['Current_Hilite_Image'],
        	    	'L_SET_HILITE_IMAGE' => $lang['Set_Hilite_Image'],
			'CID' => $cid)
         	);

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();		
		$template->pparse('body');

		break;

	case 'set_hilite':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Set Image As Hilite Image For Vehicle
		$garage->update_single_field(GARAGE_TABLE, 'image_id', $image_id, 'id', $cid);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	case 'remove_gallery_item':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Remove Image From Vehicle Gallery & Deletes Image
		$garage_image->delete_gallery_image($image_id);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=manage_vehicle_gallery&CID=$cid", true));

		break;

	case 'view_insurance_business':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('BROWSE', "garage.$phpEx?mode=error&EID=15");

		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_view_insurance_business.html')
		);

		//Get All Data Posted And Make It Safe To Use
		$params = array('business_id', 'start');
		$data = $garage->process_post_vars($params);
		$data['start'] = (empty($data['start'])) ? 0 : $data['start'];

		if (!empty($data['business_id']))
		{
			$where = "AND b.id = " . $data['business_id'];
		}

		//Get All Insurance Business Data
		$business = $garage_business->select_insurance_business_data($where, $data['start']);

		//If No Business Error Nicely Rather Than Display Nothing To The User
		if ( count($business) < 1 )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=1", true));
		}

		//Display Correct Breadcrumb Links..
		if (!empty($data['business_id']))
		{
			$template->assign_block_vars('level2', array());
			$template->assign_vars(array(
				'U_LEVEL2' => append_sid("garage.$phpEx?mode=view_insurance_business&amp;business_id=" . $business[0]['id']),
				'L_LEVEL2' => $business[0]['title'])
			);
		}

      		//Loop Processing All Business's Returned From First Select Statement (now in array)
		for ($i = 0; $i < count($business); $i++)
      		{
         		$template->assign_block_vars('business_row', array(
            			'U_VIEW_BUSINESS' => append_sid("garage.$phpEx?mode=view_insurance_business&amp;business_id=" . $business[$i]['id']),
            			'NAME' => $business[$i]['title'],
	            		'ADDRESS' => $business[$i]['address'],
        	    		'TELEPHONE' => $business[$i]['telephone'],
            			'FAX' => $business[$i]['fax'],
            			'WEBSITE' => $business[$i]['website'],
	            		'EMAIL' => $business[$i]['email'],
				'OPENING_HOURS' => $business[$i]['opening_hours'])
			);

			//Setup Template Block For Detail Being Displayed...
			$detail = (empty($data['business_id'])) ? 'business_row.more_detail' : 'business_row.insurance_detail';
        	 	$template->assign_block_vars($detail, array());

			//Now Loop Through All Insurance Cover Types...
			for($j = 0; $j < count($cover_types); $j++)
			{
				//Pull MIN/MAX/AVG Of Specific Cover Type By Business ID
				$premium_data = $garage_insurance->select_premiums_stats_by_business_and_covertype_data($business[$i]['id'], $cover_types[$j]);
        	    		$template->assign_block_vars('business_row.cover_row', array(
               				'COVER_TYPE' => $cover_types[$j],
               				'MINIMUM' => $premium_data['min'],
               				'AVERAGE' => $premium_data['avg'],
               				'MAXIMUM' => $premium_data['max'])
	            		);
			}
			
			//If Display Single Insurance Company We Then Need To Get All Premium Data
			if  (!empty($data['business_id']))
			{
				//Pull All Insurance Premiums Data For Specific Insurance Company
				$insurance_data = $garage_insurance->select_all_premiums_by_business_data($business[$i]['id']);
				for($k = 0; $k < count($insurance_data); $k++)
				{
					$template->assign_block_vars('business_row.insurance_detail.premiums', array(
						'USERNAME' => $insurance_data[$k]['username'],
						'VEHICLE' => $insurance_data[$k]['vehicle'],
						'U_VIEW_PROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $insurance_data[$k]['user_id']),
						'U_VIEW_VEHICLE' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=" . $insurance_data[$k]['garage_id']),
						'PREMIUM' => $insurance_data[$k]['premium'],
						'COVER_TYPE' => $insurance_data[$k]['cover_type'])
					);
				}
			}
      		}

		// Get Insurance Business Data For Pagination
		$count = $garage_business->select_insurance_business_data($where);
		$pagination = generate_pagination("garage.$phpEx?mode=view_insurance_business", $count[0]['total'], 25, $start);

		$template->assign_block_vars('level1', array());
		$template->assign_vars(array(
			'PAGINATION' => $pagination,
			'PAGE_NUMBER' => sprintf($lang['Page_of'], (floor( $start / 25) + 1), ceil($count[0]['total'] / 25 )), 
			'L_GOTO_PAGE' => $lang['Goto_page'],
               		'L_LEVEL1' => $lang['Insurance_Summary'],
               		'U_LEVEL1' => append_sid("garage.$phpEx?mode=view_insurance_business"),
               		'L_BUSINESS_NAME' => $lang['Business_Name'],
               		'L_ADDRESS' => $lang['Address'],
               		'L_TELEPHONE' => $lang['Telephone'],
               		'L_FAX' => $lang['Fax'],
	 		'L_CLICK_FOR_MORE_DETAIL' => $lang['Click_For_More_Detail'],
               		'L_WEBSITE' => $lang['Website'],
               		'L_EMAIL' => $lang['Email'],
               		'L_COVER_TYPE' => $lang['Cover_Type'],
               		'L_LAST_CUSTOMERS' => $lang['Last_Customers'],
			'L_CUSTOMER' => $lang['Owner'],
			'L_VEHICLE' => $lang['Vehicle'],
			'L_PREMIUM' => $lang['Premium'],
               		'L_COVER_TYPE' => $lang['Cover_Type'],
               		'L_LOWEST_PREMIUM' => $lang['Lowest_Premium'],
               		'L_AVERAGE_PREMIUM' => $lang['Average_Premium'],
               		'L_HIGHEST_PREMIUM' => $lang['Highest_Premium'],
               		'L_OPENING_HOURS' => $lang['Opening_Hours'])
            	);

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'view_garage_business':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('BROWSE', "garage.$phpEx?mode=error&EID=15");

		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body' => 'garage_view_garage_business.html')
		);

		//Get All Data Posted And Make It Safe To Use
		$params = array('business_id', 'start');
		$data = $garage->process_post_vars($params);
		$data['start'] = (empty($data['start'])) ? 0 : $data['start'];

		//Build SQL Parameters Based On If We Are Displaying One Business Or Not
		if (!empty($data['business_id']))
		{
			$where = "AND b.id = " . $data['business_id'];
		}

		//Get Required Garage Business Data
		$business = $garage_business->select_garage_business_data($where, $data['start']);

		//If No Business Let The User Know..
		if ( count($business) < 1 )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=1", true));
		}

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);

		//Setup Breadcrumb Trail Correctly...
		if (!empty($data['business_id']))
		{
			$template->assign_block_vars('level2', array());
			$template->assign_vars(array(
				'U_LEVEL2' => append_sid("garage.$phpEx?mode=view_garage_business&amp;business_id=" . $business[0]['id']),
				'L_LEVEL2' => $business[0]['title'])
			);
		}

      		//Process All Garages......
      		for ($i = 0; $i < count($business); $i++)
      		{
			if (empty($business[$i]['rating']))
			{
				$business[$i]['rating'] = '0';
			}

         		$template->assign_block_vars('business_row', array(
				'U_VIEW_BUSINESS' => append_sid("garage.$phpEx?mode=view_garage_business&amp;business_id=" . $business[$i]['id']),
            			'NAME' => $business[$i]['title'],
            			'ADDRESS' => $business[$i]['address'],
            			'TELEPHONE' => $business[$i]['telephone'],
            			'FAX' => $business[$i]['fax'],
            			'WEBSITE' => $business[$i]['website'],
            			'EMAIL' => $business[$i]['email'],
				'RATING' => $business[$i]['rating'],
				'MAX_RATING' => $business[$i]['total_rating'],
				'OPENING_HOURS' => $business[$i]['opening_hours'])
         		);
			$template->assign_block_vars('business_row.customers', array());

			if (empty($data['business_id']))
			{
         			$template->assign_block_vars('business_row.more_detail', array());
			}

			//Now Lets Go Get Mods Business Has Installed
			$bus_mod_data = $garage_modification->select_modifications_by_install_business_data($business[$i]['id']);

			for($j = 0 ; $j < count($bus_mod_data); $j++)
			{
				$template->assign_block_vars('business_row.mod_row', array(
					'USERNAME' => $bus_mod_data[$j]['username'],
					'VEHICLE' => $bus_mod_data[$j]['vehicle'],
					'U_VIEW_PROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $bus_mod_data[$j]['user_id']),
					'U_VIEW_VEHICLE' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=" . $bus_mod_data[$j]['garage_id']),
					'U_VIEW_MODIFICATION' => append_sid("garage.$phpEx?mode=view_modification&amp;CID=" . $bus_mod_data[$j]['garage_id'] . "&amp;MID=" . $bus_mod_data[$j]['id']),
					'MODIFICATION' => $bus_mod_data[$j]['mod_title'],
					'INSTALL_RATING' => $bus_mod_data[$j]['install_rating'])
				);

				//Setup Comments For Installation Of Modification...	
				if (!empty($bus_mod_data[$j]['install_comments']))
				{
					if ( $comments != 'SET')
					{
						$template->assign_block_vars('business_row.comments', array());
					}
					$comments = 'SET';
					$template->assign_block_vars('business_row.customer_comments', array(
						'COMMENTS' => $bus_mod_data[$j]['username'] . ' -> ' . $bus_mod_data[$j]['install_comments'])
					);
				}
			}

			//Reset Comments For Next Business..
			$comments = '';
		}

		//Get Count & Perform Pagination...
		$count = $garage_business->count_garage_business_data($where);
		$pagination = generate_pagination("garage.$phpEx?mode=view_garage_business", $count, 25, $start);

		$template->assign_block_vars('level1', array());
		$template->assign_vars(array(
			'PAGINATION' => $pagination,
			'PAGE_NUMBER' => sprintf($lang['Page_of'], (floor($start / 25) + 1), ceil($count / 25)), 
			'L_GOTO_PAGE' => $lang['Goto_page'],
	 		'L_LEVEL1' => $lang['Garage_Review'],
               		'U_LEVEL1' => append_sid("garage.$phpEx?mode=view_garage_business"),
               		'L_BUSINESS_NAME' => $lang['Business_Name'],
               		'L_LAST_CUSTOMERS' => $lang['Last_Customers'],
               		'L_CUSTOMER' => $lang['Owner'],
               		'L_COMMENTS' => $lang['Comments'],
               		'L_CLICK_FOR_MORE_DETAIL' => $lang['Click_For_More_Detail'],
               		'L_VEHICLE' => $lang['Vehicle'],
               		'L_MODIFICATION' => $lang['Modification'],
			'L_INSTALL_RATING' => $lang['Installation_Rating'],
               		'L_ADDRESS' => $lang['Address'],
               		'L_TELEPHONE' => $lang['Telephone'],
               		'L_FAX' => $lang['Fax'],
               		'L_WEBSITE' => $lang['Website'],
               		'L_EMAIL' => $lang['Email'],
               		'L_COVER_TYPE' => $lang['Cover_Type'],
               		'L_LOWEST_PREMIUM' => $lang['Lowest_Premium'],
               		'L_AVERAGE_PREMIUM' => $lang['Average_Premium'],
               		'L_HIGHEST_PREMIUM' => $lang['Highest_Premium'],
               		'L_OPENING_HOURS' => $lang['Opening_Hours'],
               		'L_OUT_OF' => $lang['Out_Of'],
               		'L_RATING' => $lang['Rating'])
            	);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'view_shop_business':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('BROWSE', "garage.$phpEx?mode=error&EID=15");

		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body' => 'garage_view_shop_business.html')
		);


		//Get All Data Posted And Make It Safe To Use
		$params = array('business_id', 'start');
		$data = $garage->process_post_vars($params);
		$data['start'] = (empty($data['start'])) ? 0 : $data['start'];

		if (!empty($data['business_id']))
		{
			$where = "AND b.id = " . $data['business_id'];
		}

		//Get Required Shop Business Data
		$business = $garage_business->select_shop_business_data($where, $data['start']);

		//If No Business Let The User Know..
		if ( count($business) < 1 )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=1", true));
		}

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);

		if (!empty($data['business_id']))
		{
			$template->assign_block_vars('level2', array());
			$template->assign_vars(array(
				'U_LEVEL2' => append_sid("garage.$phpEx?mode=view_shop_business&amp;business_id=" . $business[0]['id']),
				'L_LEVEL2' => $business[0]['title'])
			);
		}

      		//Process All Shops......
      		for ($i = 0; $i < count($business); $i++)
      		{
			if (empty($business[$i]['rating']))
			{
				$business[$i]['rating'] = '0';
			}

         		$template->assign_block_vars('business_row', array(
				'U_VIEW_BUSINESS' => append_sid("garage.$phpEx?mode=view_shop_business&amp;business_id=" . $business[$i]['id']),
            			'NAME' => $business[$i]['title'],
            			'ADDRESS' => $business[$i]['address'],
            			'TELEPHONE' => $business[$i]['telephone'],
            			'FAX' => $business[$i]['fax'],
            			'WEBSITE' => $business[$i]['website'],
            			'EMAIL' => $business[$i]['email'],
				'RATING' => $business[$i]['rating'],
				'MAX_RATING' => $business[$i]['total_rating'],
				'OPENING_HOURS' => $business[$i]['opening_hours'])
         		);
			$template->assign_block_vars('business_row.customers', array());
			
			if (empty($data['business_id']))
			{
         			$template->assign_block_vars('business_row.more_detail', array());
			}

			//Now Lets Go Get All Mods All Business's Have Sold
			$bus_mod_data = $garage_modification->select_modifications_by_business_data($business[$i]['id']);

			for ($j = 0; $j < count($bus_mod_data); $j++)
			{
				$template->assign_block_vars('business_row.mod_row', array(
					'USERNAME' => $bus_mod_data[$j]['username'],
					'VEHICLE' => $bus_mod_data[$j]['vehicle'],
					'U_VIEW_PROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $bus_mod_data[$j]['user_id']),
					'U_VIEW_VEHICLE' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=" . $bus_mod_data[$j]['garage_id']),
					'U_VIEW_MODIFICATION' => append_sid("garage.$phpEx?mode=view_modification&amp;CID=" . $bus_mod_data[$j]['garage_id'] . "&amp;MID=" . $bus_mod_data[$j]['id']),
					'MODIFICATION' => $bus_mod_data[$j]['mod_title'],
					'PURCHASE_RATING' => $bus_mod_data[$j]['purchase_rating'],
					'PRODUCT_RATING' => $bus_mod_data[$j]['product_rating'],
					'PRICE' => $bus_mod_data[$j]['price'])
				);
					
				if (!empty($bus_mod_data[$j]['comments']))
				{
					if ( $comments != 'SET')
					{
						$template->assign_block_vars('business_row.comments', array());
					}
					$comments = 'SET';
					$template->assign_block_vars('business_row.customer_comments', array(
						'COMMENTS' => $bus_mod_data[$j]['username'] . ' -> ' . $bus_mod_data[$j]['comments'])
					);
				}
			}

			//Reset Comments For Next Business..
			$comments = '';
		}

		//Get Count & Perform Pagination...
		$count = $garage_business->count_shop_business_data($where);
		$pagination = generate_pagination("garage.$phpEx?mode=view_shop_business", $count, 25, $start);

		$template->assign_block_vars('level1', array());
		$template->assign_vars(array(
			'PAGINATION' => $pagination,
			'PAGE_NUMBER' => sprintf($lang['Page_of'], (floor($start / 25) + 1), ceil($count / 25)), 
			'L_GOTO_PAGE' => $lang['Goto_page'],
	 		'L_LEVEL1' => $lang['Shop_Review'],
               		'U_LEVEL1' => append_sid("garage.$phpEx?mode=view_shop_business"),
               		'L_BUSINESS_NAME' => $lang['Business_Name'],
               		'L_LAST_CUSTOMERS' => $lang['Last_Customers'],
               		'L_CUSTOMER' => $lang['Owner'],
               		'L_COMMENTS' => $lang['Comments'],
               		'L_CLICK_FOR_MORE_DETAIL' => $lang['Click_For_More_Detail'],
               		'L_VEHICLE' => $lang['Vehicle'],
               		'L_MODIFICATION' => $lang['Modification'],
			'L_PURCHASE_RATING' => $lang['Purchase_Rating'],
			'L_PRODUCT_RATING' => $lang['Product_Rating'],
			'L_PRICE' => $lang['Price'],
               		'L_ADDRESS' => $lang['Address'],
               		'L_TELEPHONE' => $lang['Telephone'],
               		'L_FAX' => $lang['Fax'],
               		'L_WEBSITE' => $lang['Website'],
               		'L_EMAIL' => $lang['Email'],
               		'L_COVER_TYPE' => $lang['Cover_Type'],
               		'L_LOWEST_PREMIUM' => $lang['Lowest_Premium'],
               		'L_AVERAGE_PREMIUM' => $lang['Average_Premium'],
               		'L_HIGHEST_PREMIUM' => $lang['Highest_Premium'],
               		'L_OPENING_HOURS' => $lang['Opening_Hours'],
               		'L_OUT_OF' => $lang['Out_Of'],
               		'L_RATING' => $lang['Rating'])
            	);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'user_submit_business':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=user_submit_business", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD', "garage.$phpEx?mode=error&EID=14");

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_user_submit.html')
		);

		//Get All Data Posted And Make It Safe To Use
		$params = array('BUSINESS', 'mode_redirect');
		$data = $garage->process_post_vars($params);
		$data['insurance'] = ($data['BUSINESS'] == 'insurance') ? 'checked="checked"' : '' ;
		$data['garage'] = ($data['BUSINESS'] == 'garage') ? 'checked="checked"' : '' ;
		$data['retail_shop'] = ($data['BUSINESS'] == 'shop') ? 'checked="checked"' : '' ;
		$data['web_shop'] = ($data['BUSINESS'] == 'shop') ? 'checked="checked"' : '' ;

		$template->assign_vars(array(
			'S_MODE_ACTION' 	=> append_sid("garage.$phpEx?mode=insert_business"),
			'L_ADD_NEW_BUSINESS' 	=> $lang['Add_New_Business'],
			'L_BUSINESS_NAME' 	=> $lang['Business_Name'],
			'L_BUSINESS_NOTICE' 	=> $lang['Business_Notice'],
               		'L_ADDRESS' 		=> $lang['Address'],
               		'L_TELEPHONE' 		=> $lang['Telephone'],
               		'L_FAX' 		=> $lang['Fax'],
               		'L_WEBSITE' 		=> $lang['Website'],
               		'L_EMAIL' 		=> $lang['Email'],
               		'L_OPENING_HOURS' 	=> $lang['Opening_Hours'],
               		'L_TYPE' 		=> $lang['Business_Type'],
               		'L_REQUIRED' 		=> $lang['Required'],
               		'L_GARAGE' 		=> $lang['Garage'],
               		'L_INSURANCE' 		=> $lang['Insurance'],
               		'L_RETAIL_SHOP' 	=> $lang['Retail_Shop'],
               		'L_WEB_SHOP' 		=> $lang['Web_Shop'],
			'INSURANCE_CHECKED' 	=> $data['insurance'],
			'GARAGE_CHECKED' 	=> $data['garage'],
			'RETAIL_CHECKED' 	=> $data['retail_shop'],
			'WEBSHOP_CHECKED' 	=> $data['web_shop'],
			'CID' 			=> $cid,
			'MODE_REDIRECT'		=> $data['mode_redirect'])
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'insert_business':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Get All Data Posted And Make It Safe To Use
		$params = array('mode_redirect', 'name', 'address', 'telephone', 'fax', 'website', 'email', 'opening_hours', 'insurance', 'garage', 'retail_shop', 'web_shop');
		$data = $garage->process_post_vars($params);
		$data['pending'] = ($garage_config['enable_business_approval'] == '1') ? 1 : 0 ;
		$data['insurance'] = ($data['insurance'] == 'on') ? 1 : 0 ;
		$data['garage'] = ($data['garage'] == 'on') ? 1 : 0 ;
		$data['retail_shop'] = ($data['retail_shop'] == 'on') ? 1 : 0 ;
		$data['web_shop'] = ($data['web_shop'] == 'on') ? 1 : 0 ;
		//Check They Entered http:// In The Front Of The Link
		if ( (!preg_match( "/^http:\/\//i", $data['website'])) AND (!empty($data['website'])) )
		{
			$data['website'] = "http://".$data['website'];
		}

		//Checks All Required Data Is Present
		$params = array('name');
		$garage->check_required_vars($params);

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ( $data['pending'] == 1 )
		{
			$garage->pending_notification();
			$garage->update_single_field(GARAGE_CONFIG_TABLE, 'config_value', $data['pending'], 'config_name', 'items_pending');
		}

		//Create The Business Now...
		$garage_business->insert_business($data);

		//Send Them Back To Whatever Page Them Came From..Now With Their Required Business :)
		redirect(append_sid("garage.$phpEx?mode=" . $data['mode_redirect'] . "&CID=$cid", true));

		break;

	case 'edit_business':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD', "garage.$phpEx?mode=error&EID=14");

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_user_submit.html')
		);

		//Pull Required Business Data From DB
		$data = $garage_business->select_business_data($bus_id);
		$data['insurance'] = ($data['insurance'] == '1') ? 'checked="checked"' : '' ;
		$data['garage'] = ($data['garage'] == '1') ? 'checked="checked"' : '' ;
		$data['retail_shop'] = ($data['retail_shop'] == '1') ? 'checked="checked"' : '' ;
		$data['web_shop'] = ($data['web_shop'] == '1') ? 'checked="checked"' : '' ;

		$template->assign_vars(array(
			'S_MODE_ACTION' 	=> append_sid("garage.$phpEx?mode=update_business"),
			'L_ADD_NEW_BUSINESS' 	=> $lang['Edit_Business'],
			'L_BUSINESS_NAME' 	=> $lang['Business_Name'],
			'L_BUSINESS_NOTICE' 	=> $lang['Business_Notice'],
               		'L_ADDRESS' 		=> $lang['Address'],
               		'L_TELEPHONE' 		=> $lang['Telephone'],
               		'L_FAX' 		=> $lang['Fax'],
               		'L_WEBSITE' 		=> $lang['Website'],
               		'L_EMAIL' 		=> $lang['Email'],
               		'L_OPENING_HOURS' 	=> $lang['Opening_Hours'],
               		'L_TYPE' 		=> $lang['Business_Type'],
               		'L_REQUIRED' 		=> $lang['Required'],
               		'L_GARAGE' 		=> $lang['Garage'],
               		'L_INSURANCE' 		=> $lang['Insurance'],
               		'L_RETAIL_SHOP' 	=> $lang['Retail_Shop'],
               		'L_WEB_SHOP' 		=> $lang['Web_Shop'],
			'INSURANCE_CHECKED' 	=> $data['insurance'],
			'GARAGE_CHECKED' 	=> $data['garage'],
			'RETAIL_CHECKED' 	=> $data['retail_shop'],
			'WEBSHOP_CHECKED' 	=> $data['web_shop'],
			'NAME' 			=> $data['title'],
			'ADDRESS'		=> $data['address'],
			'TELEPHONE'		=> $data['telephone'],
			'FAX'			=> $data['fax'],
			'WEBSITE'		=> $data['website'],
			'EMAIL'			=> $data['email'],
			'OPENING_HOURS'		=> $data['opening_hours'],
			'BUSINESS_ID'		=> $data['id'])
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'update_business':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD', "garage.$phpEx?mode=error&EID=14");

		//Get All Data Posted And Make It Safe To Use
		$params = array('id', 'name', 'address', 'telephone', 'fax', 'website', 'email', 'opening_hours', 'insurance', 'garage', 'retail_shop', 'web_shop');
		$data = $garage->process_post_vars($params);
		$data['pending'] = ($garage_config['enable_business_approval'] == '1') ? 1 : 0 ;
		$data['insurance'] = ($data['insurance'] == 'on') ? 1 : 0 ;
		$data['garage'] = ($data['garage'] == 'on') ? 1 : 0 ;
		$data['retail_shop'] = ($data['retail_shop'] == 'on') ? 1 : 0 ;
		$data['web_shop'] = ($data['web_shop'] == 'on') ? 1 : 0 ;
		//Check They Entered http:// In The Front Of The Link
		if ( (!preg_match( "/^http:\/\//i", $data['website'])) AND (!empty($data['website'])) )
		{
			$data['website'] = "http://" . $data['website'];
		}

		//Checks All Required Data Is Present
		$params = array('name');
		$garage->check_required_vars($params);

		//Update The Business With Data Acquired
		$garage_business->update_business($data);

		redirect(append_sid("garage.$phpEx?mode=garage_pending", true));

		break;

	case 'user_submit_make':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=user_submit_make", true));
		}

		//Check This Feature Is Enabled
		if ( $garage_config['enable_user_submit_make'] == '0' )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=18", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_user_submit_make.html')
		);

		$template->assign_vars(array(
			'L_ADD_MAKE' 		 => $lang['Add_Make'],
			'L_ADD_MAKE_BUTTON' 	 => $lang['Add_Make_Button'],
			'L_VEHICLE_MAKE' 	 => $lang['Vehicle_Make'],
			'S_GARAGE_MODELS_ACTION' => append_sid('admin_garage_models.' . $phpEx))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'insert_make':

		//User Is Annoymous...So Not Allowed To Create A Vehicle
		if ( $userdata['user_id'] == -1 )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=2", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Get All Data Posted And Make It Safe To Use
		$params = array('make');
		$data = $garage->process_post_vars($params);

		//Checks All Required Data Is Present
		$params = array('make');
		$garage->check_required_vars($params);

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ( $data['pending'] == 1 )
		{
			$garage->pending_notification();
			$garage->update_single_field(GARAGE_CONFIG_TABLE, 'config_value', $data['pending'], 'config_name', 'items_pending');
		}

		//Create The Make
		$garage_model->insert_make($data);

		redirect(append_sid("garage.$phpEx?mode=create_vehicle&MAKE=" . $data['make'], true));

		break;

	case 'user_submit_model':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=user_submit_model", true));
		}

		//Check This Feature Is Enabled
		if ( $garage_config['enable_user_submit_model'] == '0' )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=18", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_user_submit_model.html')
		);

		//Get All Data Posted And Make It Safe To Use
		$params = array('MAKE_ID');
		$data = $garage->process_post_vars($params);

		//Checks All Required Data Is Present
		$params = array('MAKE_ID');
		$garage->check_required_vars($params);

		//Pull Required Make Data From DB
		$data = $garage_model->select_make_data($data['MAKE_ID']);

		$template->assign_vars(array(
			'L_ADD_MODEL' 		=> $lang['Add_Model'],
			'L_ADD_MODEL_BUTTON' 	=> $lang['Add_Model_Button'],
			'L_VEHICLE_MAKE' 	=> $lang['Vehicle_Make'],
			'L_VEHICLE_MODEL' 	=> $lang['Vehicle_Model'],
			'MAKE_ID' 		=> $data['id'],
			'MAKE' 			=> $data['make'])
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'insert_model':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=user_submit_model", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Get All Data Posted And Make It Safe To Use
		$params = array('make', 'make_id', 'model');
		$data = $garage->process_post_vars($params);

		//Checks All Required Data Is Present
		$params = array('make', 'make_id', 'model');
		$garage->check_required_vars($params);

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ( $data['pending'] == 1 )
		{
			$garage->pending_notification();
			$garage->update_single_field(GARAGE_CONFIG_TABLE, 'config_value', $data['pending'], 'config_name', 'items_pending');
		}

		//Create The Model
		$garage_model->insert_model($data);

		redirect(append_sid("garage.$phpEx?mode=create_vehicle&MAKE=" . $data['make'] . "&MODEL=" . $data['model'], true));

		break;

	case 'quartermile':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('BROWSE', "garage.$phpEx?mode=error&EID=15");

		$page_title = $lang['Car_Quart'];
		include($phpbb_root_path . 'includes/page_header.' . $phpEx);

		$template->set_filenames(array(
			'javascript' => 'garage_vehicle_select_javascript.html',
			'body' => 'garage_quartermile_table.html')
		);

		//Build Required HTML, Javascript And Arrays
		$garage_template->sort_order($sort_order);
		$template->assign_vars(array(
			'VEHICLE_ARRAY' => $garage_template->vehicle_array())
		);
		$template->assign_var_from_handle('JAVASCRIPT', 'javascript');

		//Build Actual Table With No Pending Runs
		$garage_quartermile->build_quartermile_table('NO');

		$template->assign_block_vars('level1', array());
		$template->assign_vars(array(
			'L_SELECT_SORT_METHOD' 	=> $lang['Select_sort_method'],
			'L_ORDER' 		=> $lang['Order'],
			'L_SORT' 		=> $lang['Sort'],
			'L_MAKE' 		=> $lang['Make'],
			'L_VEHICLE' 		=> $lang['Vehicle'],
			'L_MODEL' 		=> $lang['Model'],
			'L_SUBMIT' 		=> $lang['Sort'],
			'L_CAR_RT' 		=> $lang['Car_Rt'],
		        'L_CAR_SIXTY' 		=> $lang['Car_Sixty'],
		        'L_CAR_THREE' 		=> $lang['Car_Three'],
		        'L_CAR_EIGTH' 		=> $lang['Car_Eigth'],
		        'L_CAR_EIGTHM' 		=> $lang['Car_Eigthm'],
		        'L_CAR_THOU' 		=> $lang['Car_Thou'],
		        'L_CAR_QUART' 		=> $lang['Car_Quart'],
		        'L_CAR_QUARTM' 		=> $lang['Car_Quartm'],
		        'L_BHP' 		=> $lang['Bhp'],
		        'L_BHP_UNIT' 		=> $lang['Bhp_Unit'],
		        'L_NITROUS' 		=> $lang['Nitrous'],
			'L_PM' 			=> $lang['Private_Message'], 
			'L_QUARTERMILE' 	=> $lang['Quartermile'], 
			'L_APPROVE_TIME' 	=> $lang['Approve_QM'],
			'L_REMOVE_TIME' 	=> $lang['Remove_QM'],
			'S_USER_VARIABLE' 	=> 'qm_id',
			'U_QUARTERMILE' 	=> append_sid("garage.$phpEx?mode=quartermile"),
			'MAKE' 			=> $data['make'],
			'MODEL' 		=> $data['model'],
			'S_MODE_ACTION' 	=> append_sid("garage.$phpEx?mode=quartermile"))
		);

		$template->pparse('body');

		break;

	case 'garage_pending':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=garage_pending", true));
		}

		//Check The User Is Allowed To View This Page...If Not Send Them On There Way Nicely
		if (!in_array($userdata['user_level'], array(ADMIN, MOD)) )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=13", true));
		}

		//Generate Page Header
		include($phpbb_root_path . 'includes/page_header.' . $phpEx);

		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_pending.html')
		);

		//Build The Quartermile Table With Only Pending Times And Get Returned Count
		$pending_quartermile_count = $garage_quartermile->build_quartermile_table('YES');

		//Build The Rollingroad Table With Only Pending Run And Get Returned Counts
		$pending_dynorun_count = $garage_dynorun->build_dynorun_table('YES');

		//Build The Business Table With Only Pending One And Get Returned Counts
		$pending_business_count = $garage_business->build_business_table('YES');

		//Build The Make Table With Only Pending One And Get Returned Counts
		$pending_make_count = $garage_model->build_make_table('YES');

		//Build The Model Table With Only Pending One And Get Returned Counts
		$pending_model_count = $garage_model->build_model_table('YES');

		//Display A Nice Message Saying Nothing Is Pending Approval If Needed
		if ( $pending_quartermile_count == '0' AND $pending_dynorun_count == '0' AND $pending_business_count == '0' AND $pending_make_count == '0' AND  $pending_model_count == '0' )
		{
			$garage->update_single_field(GARAGE_CONFIG_TABLE, 'config_value', 0, 'config_name', 'items_pending');
			$template->assign_block_vars('no_pending_items', array());
		}

		//Set Up Template Varibles
		$template->assign_block_vars('level1', array());
		$template->assign_vars(array(
			'U_LEVEL1' => append_sid($phpbb_root_path . 'garage.' . $phpEx . '?mode=garage_pending'),
			'L_QUARTERMILE_PENDING' => $lang['Quartermile_Pending'],
			'L_ROLLINGROAD_PENDING' => $lang['Rollingroad_Pending'],
			'L_BUSINESS_PENDING' => $lang['Business_Pending'],
			'L_MAKE_PENDING' => $lang['Make_Pending'],
			'L_MODEL_PENDING' => $lang['Model_Pending'],
			'L_NO_PENDING_ITEMS' => $lang['No_Pending_Items'],
			'L_USERNAME' => $lang['Username'],
			'L_GO' => $lang['Go'],
			'L_SELECT' => $lang['Select_one'],
			'L_USER_ID' => $lang['User_id'],
			'L_LEVEL1' => $lang['Pending_Items'],
			'L_RT' => $lang['Car_Rt'],
		        'L_SIXTY' => $lang['Car_Sixty'],
		        'L_THREE' => $lang['Car_Three'],
		        'L_EIGTH' => $lang['Car_Eigth'],
		        'L_EIGTHMPH' => $lang['Car_Eigthm'],
		        'L_THOU' => $lang['Car_Thou'],
		        'L_QUART' => $lang['Car_Quart'],
		        'L_QUARTMPH' => $lang['Car_Quartm'],
			'L_SHOW' => $lang['Show'],
			'L_VEHICLE' => $lang['Vehicle'],
			'L_MAKE' => $lang['Make'],
			'L_MODEL' => $lang['Model'],
			'L_PENDING_ITEMS' => $lang['Pending_Items'],
			'L_USERNAME' => $lang['Username'],
			'L_VEHICLE' => $lang['Vehicle'],
			'L_APPROVE' => $lang['Approve'],
			'L_REMOVE' => $lang['Remove'],
			'L_REASSIGN' => $lang['Reassign'],
			'L_SORT_BY' => $lang['Sort_by'],
			'L_ASCENDING' => $lang['Ascending'],
			'L_DESCENDING' => $lang['Descending'],
			'L_DYNOCENTER' => $lang['Dynocenter'],
			'L_BHP' => $lang['Bhp'],
			'L_BHP_UNIT' => $lang['Bhp_Unit'],
			'L_TORQUE' => $lang['Torque'],
			'L_TORQUE_UNIT' => $lang['Torque_Unit'],
			'L_BOOST' => $lang['Boost'],
			'L_BOOST_UNIT' => $lang['Boost_Unit'],
			'L_NITROUS' => $lang['Nitrous'],
	  		'L_PEAKPOINT' => $lang['Peakpoint'],
			'L_BUSINESS_NAME' => $lang['Business_Name'],
			'L_BUSINESS_NOTICE' => $lang['Business_Notice'],
               		'L_ADDRESS' => $lang['Address'],
               		'L_TELEPHONE' => $lang['Telephone'],
               		'L_FAX' => $lang['Fax'],
               		'L_WEBSITE' => $lang['Website'],
               		'L_EMAIL' => $lang['Email'],
               		'L_OPENING_HOURS' => $lang['Opening_Hours'],
               		'L_TYPE' => $lang['Type'],
			'S_ACTION' => append_sid("garage.$phpEx?mode=garage_approval"),
			'S_SHOW' => $show,
			'S_SORT' => $lang['Sort'],
			'S_HIDDEN_FIELDS' => $hidden_fields)
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'garage_approval':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=quartermile_pending", true));
		}

		//Check The User Is Allowed To View This Page...If Not Send Them On There Way Nicely
		if ( !in_array($userdata['user_level'], array(ADMIN, MOD)) )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=13", true));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('action');
		$data = $garage->process_post_vars($params);

		//Setup Arrays Needed For Data
		$qm_id = array(); $rr_id = array(); $bus_id = array(); $mk_id = array(); $mdl_id = array();
		$params = array('qm_id' => GARAGE_QUARTERMILE_TABLE, 'rr_id' => GARAGE_ROLLINGROAD_TABLE, 'bus_id' => GARAGE_BUSINESS_TABLE, 'mk_id' => GARAGE_MAKES_TABLE, 'mdl_id' => GARAGE_MODELS_TABLE);

		//Check If We Are Doing A Business Reassign
		if ( $data['action'] == 'REASSIGN' )
		{
			//We Need To Check Only One Business Was Selected
			$total = count($HTTP_POST_VARS['bus_id']);
			if ( $total > 1 )
			{
				redirect(append_sid("garage.$phpEx?mode=error&EID=22", true));
			}

			//Get Business ID We Are Going To Delete
			$bus_id = intval($HTTP_POST_VARS['bus_id'][0]);

			$data = $garage_business->select_business_data($bus_id);

			//Generate Page Header
			include($phpbb_root_path . 'includes/page_header.' . $phpEx);

			$template->set_filenames(array(
				'header' => 'garage_header.html',
				'body'   => 'garage_reassign_business.html')
			);

			//Build Dropdown Box Of Business's To Reassign It To
			$garage_business->reassign_business_dropdown($bus_id);

			//Set Up Template Varibles
			$template->assign_block_vars('level1', array());
			$template->assign_vars(array(
				'S_MODE_ACTION'	=> append_sid("garage.$phpEx?mode=reassign_business"),
				'U_LEVEL1' => append_sid($phpbb_root_path . "garage.$phpEx?mode=garage_pending"),
				'L_LEVEL1'=> $lang['Reassign_Business'],
				'L_RESSIGN_BUSINESS' => $lang['Reassign_Business'],
				'L_BUSINESS_DELETED' => $lang['Business_Deleted'],
				'L_REASSIGN_TO' => $lang['Reassign_To'],
				'L_REASSIGN_BUTTON' => $lang['Reassign_Button'],
				'NAME' => $data['title'],
				'BUSINESS_ID' => $data['id'])
			);

			//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
			$template->pparse('header');
			$garage_template->sidemenu();
			$template->pparse('body');

			break;
		}

		//Process Each Pending Type For Updates
		while( list($ids, $table) = @each($params) )
		{
			//Check We Have Been Passed ID's To Work On
			if ( !empty($HTTP_POST_VARS[$ids]) )
			{
				$pending_ids = $HTTP_POST_VARS[$ids];

				//Process For Removing
				if ( $data['action'] == 'REMOVE' )
				{
					for ($i = 0 ; $i < count($pending_ids); $i++)
					{
						$id = intval($pending_ids[$i]);

						//If Quartermile Need To Call Correct Function To Delete Images Too
						if ( $table ==  GARAGE_QUARTERMILE_TABLE)
						{
							$garage_quartermile->delete_quartermile($id);
						}
						//If Rollingroad Need To Call Correct Function To Delete Images Too
						else if  ( $table ==  GARAGE_ROLLINGROAD_TABLE)
						{
							$garage_dynorun->delete_dynorun($id);
						}
						else
						{
							$garage->delete_rows($table, 'id', $id);
						}
					}
				}
				//Process For Approval...
				else if ( $data['action'] == 'APPROVE' )
				{
					while( $i < count($pending_ids) )
					{
						$id = intval($pending_ids[$i]);
						$garage->update_single_field($table, 'pending', 0, 'id', $id);
						$i++;
					}
				}
			}
		}

		redirect(append_sid("garage.$phpEx?mode=garage_pending", true));

		break;

	case 'reassign_business':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=quartermile_pending", true));
		}

		//Check The User Is Allowed To View This Page...If Not Send Them On There Way Nicely
		if ( !in_array($userdata['user_level'], array(ADMIN, MOD)) )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=13", true));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('business_id', 'target_id');
		$data = $garage->process_post_vars($params);

		//Checks All Required Data Is Present
		$params = array('business_id', 'target_id');
		$garage->check_required_vars($params);

		//Lets Update All Possible Business Fields With The Reassigned Business
		$garage->update_single_field(GARAGE_MODS_TABLE, 'business_id', $data['target_id'], 'business_id', $data['business_id']);
		$garage->update_single_field(GARAGE_MODS_TABLE, 'install_business_id', $data['target_id'], 'install_business_id', $data['business_id']);
		$garage->update_single_field(GARAGE_INSURANCE_TABLE, 'business_id', $data['target_id'], 'business_id', $data['business_id']);

		//Since We Have Updated All Item Lets Do The Original Delete Now
		$garage->delete_rows(GARAGE_BUSINESS_TABLE, 'id', $data['business_id']);

		redirect(append_sid("garage.$phpEx?mode=garage_pending", true));

		break;

	case 'rollingroad':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('BROWSE', "garage.$phpEx?mode=error&EID=15");

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);

		$template->set_filenames(array(
			'javascript' => 'garage_vehicle_select_javascript.html',
			'body' => 'garage_rollingroad_table.html')
		);

		//Build All Required HTML, Javascript And Arrays
		$garage_template->sort_order($sort_order);
		$template->assign_vars(array(
			'VEHICLE_ARRAY' => $garage_template->vehicle_array())
		);
		$template->assign_var_from_handle('JAVASCRIPT', 'javascript');

		//Build Dynorun Table With No Pending Runs
		$garage_dynorun->build_dynorun_table('NO');

		$template->assign_block_vars('level1', array());
		$template->assign_vars(array(
			'L_SELECT_SORT_METHOD' 	=> $lang['Select_sort_method'],
			'L_ORDER' 		=> $lang['Order'],
			'L_SORT' 		=> $lang['Sort'],
			'L_MAKE' 		=> $lang['Make'],
			'L_VEHICLE' 		=> $lang['Vehicle'],
			'L_MODEL' 		=> $lang['Model'],
			'L_SUBMIT' 		=> $lang['Sort'],
			'L_DYNOCENTER' 		=> $lang['Dynocenter'],
			'L_BHP' 		=> $lang['Bhp'],
			'L_BHP_UNIT' 		=> $lang['Bhp_Unit'],
			'L_TORQUE' 		=> $lang['Torque'],
			'L_TORQUE_UNIT' 	=> $lang['Torque_Unit'],
			'L_BOOST' 		=> $lang['Boost'],
			'L_BOOST_UNIT' 		=> $lang['Boost_Unit'],
			'L_NITROUS' 		=> $lang['Nitrous'],
	  		'L_PEAKPOINT' 		=> $lang['Peakpoint'],
	  		'L_ROLLINGROAD' 	=> $lang['Rollingroad'],
			'U_ROLLINGROAD' 	=> append_sid("garage.$phpEx?mode=rollingroad"),
			'MAKE' 			=> $data['make'],
			'MODEL' 		=> $data['model'],
			'S_MODE_ACTION' 	=> append_sid("garage.$phpEx?mode=rollingroad"))
		);

		$template->pparse('body');

		break;

	case 'view_guestbook':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('BROWSE', "garage.$phpEx?mode=error&EID=15");

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_view_guestbook.html')
		);

		//Get Vehicle Data
		$vehicle_data = $garage_vehicle->select_vehicle_data($cid);

		//Get All Comments Data
		$comment_data = $garage_guestbook->select_vehicle_comments($cid);

		//If Allowed Show Leave Comment Block
		if ($garage->check_permissions('INTERACT',''))
		{
			$template->assign_block_vars('leave_comment', array());
		}
		
         	//Check If This Is Firs Comment
		if ( count($comment_data) < 1 )
		{
			$template->assign_block_vars('first_comment', array());
			$template->assign_vars(array(
				'LEAVE_FIRST_COMMENT'  => $lang['Add_First_Comment'])
			);
		}
		//If Not First Comment Display All Existing Comments
		else
		{
			for ($i = 0; $i < count($comment_data); $i++)
			{	
				$username = $comment_data[$i]['username'];
				$temp_url = append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $comment_data[$i]['user_id']);
				$poster = '<a href="' . $temp_url . '">' . $comment_data[$i]['username'] . '</a>';
				$poster_posts = ( $comment_data[$i]['user_id'] != ANONYMOUS ) ? $lang['Posts'] . ': ' . $comment_data[$i]['user_posts'] : '';
				$poster_from = ( $comment_data[$i]['user_from'] && $comment_data['user_id'] != ANONYMOUS ) ? $lang['Location'] . ': ' . $comment_data[$i]['user_from'] : '';
				$garage_id = $comment_data['garage_id'];
				$poster_car_year = ( $comment_data[$i]['made_year'] && $comment_data[$i]['user_id'] != ANONYMOUS ) ? $lang[''] . ' ' . $comment_data[$i]['made_year'] : '';
				$poster_car_mark = ( $comment_data[$i]['make'] && $comment_data[$i]['user_id'] != ANONYMOUS ) ? $lang[''] . ' ' . $comment_data[$i]['make'] : '';
				$poster_car_model = ( $comment_data[$i]['model'] && $comment_data[$i]['user_id'] != ANONYMOUS ) ? $lang[''] . ' ' . $comment_data[$i]['model'] : '';
				$poster_joined = ( $comment_data[$i]['user_id'] != ANONYMOUS ) ? $lang['Joined'] . ': ' . create_date($board_config['default_dateformat'], $comment_data[$i]['user_regdate'], $board_config['board_timezone']) : '';

				$poster_avatar = '';
				if ( $data['user_avatar_type'] && $comment_data[$i]['user_id'] != ANONYMOUS && $comment_data[$i]['user_allowavatar'] )
				{
					switch( $row['user_avatar_type'] )
					{
						case USER_AVATAR_UPLOAD:
							$poster_avatar = ( $board_config['allow_avatar_upload'] ) ? '<img src="' . $board_config['avatar_path'] . '/' . $comment_data[$i]['user_avatar'] . '" alt="" border="0" />' : '';
							break;
						case USER_AVATAR_REMOTE:
							$poster_avatar = ( $board_config['allow_avatar_remote'] ) ? '<img src="' . $comment_data[$i]['user_avatar'] . '" alt="" border="0" />' : '';
							break;
						case USER_AVATAR_GALLERY:
							$poster_avatar = ( $board_config['allow_avatar_local'] ) ? '<img src="' . $board_config['avatar_gallery_path'] . '/' . $comment_data[$i]['user_avatar'] . '" alt="" border="0" />' : '';
							break;
					}
				}

				// Handle anon users posting with usernames
				if ( $comment_data[$i]['user_id'] == ANONYMOUS && $comment_data[$i]['post_username'] != '' )
				{
					$poster = $comment_data[$i]['post_username'];
				}

				$profile_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_profile'] . '" alt="' . $lang['Read_profile'] . '" title="' . $lang['Read_profile'] . '" border="0" /></a>';
				$profile = '<a href="' . $temp_url . '">' . $lang['Read_profile'] . '</a>';

				$temp_url = append_sid("privmsg.$phpEx?mode=post&amp;" . POST_USERS_URL . "=".$comment_data[$i]['user_id']);
				$pm_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_pm'] . '" alt="' . $lang['Send_private_message'] . '" title="' . $lang['Send_private_message'] . '" border="0" /></a>';
				$pm = '<a href="' . $temp_url . '">' . $lang['Send_private_message'] . '</a>';

				if ( !empty($comment_data[$i]['user_viewemail']) || $is_auth['auth_mod'] )
				{
					$email_uri = ( $board_config['board_email_form'] ) ? append_sid("profile.$phpEx?mode=email&amp;" . POST_USERS_URL .'=' . $comment_data[$i]['user_id']) : 'mailto:' . $comment_data['user_email'];

					$email_img = '<a href="' . $email_uri . '"><img src="' . $images['icon_email'] . '" alt="' . $lang['Send_email'] . '" title="' . $lang['Send_email'] . '" border="0" /></a>';
					$email = '<a href="' . $email_uri . '">' . $lang['Send_email'] . '</a>';
				}
				else
				{
					$email_img = '';
					$email = '';
				}

				$www_img = ( $comment_data[$i]['user_website'] ) ? '<a href="' . $comment_data[$i]['user_website'] . '" target="_userwww"><img src="' . $images['icon_www'] . '" alt="' . $lang['Visit_website'] . '" title="' . $lang['Visit_website'] . '" border="0" /></a>' : '';
				$www = ( $comment_data[$i]['user_website'] ) ? '<a href="' . $comment_data[$i]['user_website'] . '" target="_userwww">' . $lang['Visit_website'] . '</a>' : '';

				$temp_url = append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=".$comment_data[$i]['user_id']);
				$posted = '<a href="' . $temp_url . '">' . $comment_data[$i]['username'] . '</a>';
				$posted = create_date($board_config['default_dateformat'], $comment_data[$i]['post_date'], $board_config['board_timezone']);

				$post = $comment_data[$i]['post'];

				if ( !$board_config['allow_html'] )
				{
					$post = preg_replace('#(<)([\/]?.*?)(>)#is', "&lt;\\2&gt;", $post);
				}

				// Parse message and/or sig for BBCode if reqd
				if ( $board_config['allow_bbcode'] )
				{
					if ( $bbcode_uid != '' )
					{
						$post = ( $board_config['allow_bbcode'] ) ? bbencode_second_pass($post, $bbcode_uid) : preg_replace('/\:[0-9a-z\:]+\]/si', ']', $post);
					}
				}

				$post = make_clickable($post);

				// Parse smilies
				if ( $board_config['allow_smilies'] )
				{
					$post = smilies_pass($post);
				}

				// Replace newlines (we use this rather than nl2br because
				// till recently it wasn't XHTML compliant)
				$post = str_replace("\n", "\n<br />\n", $post);

				if ( in_array($userdata['user_level'], array(ADMIN, MOD)) )
				{
					$temp_url = append_sid("garage.$phpEx?mode=edit_comment&amp;CID=$cid&amp;comment_id=" . $comment_data[$i]['comment_id'] . "&amp;sid=" . $userdata['session_id']);
					$edit_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_edit'] . '" alt="' . $lang['Edit_delete_post'] . '" title="' . $lang['Edit_delete_post'] . '" border="0" /></a>';
					$edit = '<a href="' . $temp_url . '">' . $lang['Edit_delete_post'] . '</a>';

					$temp_url = append_sid("garage.$phpEx?mode=delete_comment&amp;CID=$cid&amp;comment_id=" . $comment_data[$i]['comment_id'] . "&amp;sid=" . $userdata['session_id']);
					$delpost_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_delpost'] . '" alt="' . $lang['Delete_post'] . '" title="' . $lang['Delete_post'] . '" border="0" /></a>';
					$delpost = '<a href="' . $temp_url . '">' . $lang['Delete_post'] . '</a>';
				}
				else
				{
					$edit_img = '';
					$edit = '';
					$delpost_img = '';
					$delpost = '';
				}

				$template->assign_block_vars('comments', array(
					'POSTER_NAME' => $poster,
					'POSTER_JOINED' => $poster_joined,
					'POSTER_POSTS' => $poster_posts,
					'POSTER_FROM' => $poster_from,
					'POSTER_CAR_MARK' => $poster_car_mark,
					'POSTER_CAR_MODEL' => $poster_car_model,
					'POSTER_CAR_YEAR' => $poster_car_year,
					'VIEW_POSTER_CARPROFILE' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=$garage_id"),
					'POSTER_AVATAR' => $poster_avatar,
					'PROFILE_IMG' => $profile_img,
					'PROFILE' => $profile,
					'PM_IMG' => $pm_img,
					'PM' => $pm,
					'EMAIL_IMG' => $email_img,
					'EMAIL' => $email,
					'WWW_IMG' => $www_img,
					'WWW' => $www,
					'EDIT_IMG' => $edit_img,
					'EDIT' => $edit,
					'DELETE_IMG' => $delpost_img,
					'DELETE' => $delpost,
					'POSTER' => $poster,
					'POSTED' => $posted,
					'POST' => $post)
				);
			}
		}

		$template->assign_block_vars('level1', array());
		$template->assign_block_vars('level3_nolink', array());
		$template->assign_vars(array(
			'CID' => $cid,
			'U_LEVEL1' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=$cid"),
			'L_LEVEL1' => $vehicle_data['vehicle'],
			'L_LEVEL3' => $lang['Guestbook'],
			'L_GUESTBOOK_TITLE' => $vehicle_data['username'] . " - " . $vehicle_data['vehicle'] . " " . $lang['Guestbook'],
			'L_POSTED' => $lang['Posted'],
			'L_ADD_COMMENT' => $lang['Add_Comment'],
			'L_POST_COMMENT' => $lang['Post_Comment'],
			'S_MODE_ACTION' => append_sid("garage.$phpEx?mode=insert_comment&CID=$cid"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'insert_comment':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('INTERACT', "garage.$phpEx?mode=error&EID=17");

		//Get All Data Posted And Make It Safe To Use
		$params = array('comments');
		$data = $garage->process_post_vars($params);
		$data['author_id'] = $userdata['user_id'];
		$data['post_date'] = time();

		//Checks All Required Data Is Present
		$params = array('comments');
		$garage->check_required_vars($params);

		//Insert The Comment Into Vehicle Guestbook
		$garage_guestbook->insert_vehicle_comment($data);

		//Get Vehicle Data So We Can Check If We Need To PM User
		$data = $garage_vehicle->select_vehicle_data($cid);		
		$data['author_id'] = $userdata['user_id'];
		$data['time'] = time();

		//If User Has Requested Notification On Comments Sent Them A PM
		if ( $data['guestbook_pm_notify'] == TRUE )
		{
			//Build Rest Of Required Data
			$data['date'] = date("U");
			$data['pm_subject'] = $lang['Guestbook_Notify_Subject'];
			$data['vehicle_link'] = '<a href="garage.'.$phpEx.'?mode=view_guestbook&CID=$cid">' . $lang['Here'] . '</a>';
             		$data['pm_text'] = (sprintf($lang['Guestbook_Notify_Text'], $data['vehicle_link']));

			//Checks All Required Data Is Present
			$params = array('user_id', 'pm_subject', 'author_id', 'date');
			$garage->check_required_vars($params);
			
			//Now We Have All Data Lets Send The PM!!
			$garage_guestbook->send_user_pm($data);
		}

		redirect(append_sid("garage.$phpEx?mode=view_guestbook&CID=$cid", true));

		break;

	case 'edit_comment':

		//Only Allow Moderators Or Administrators Perform This Action
		if ( !in_array($userdata['user_level'], array(ADMIN, MOD)) )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=13", true));
		}

		//Pull Required Comment Data From DB
		$data = $garage_guestbook->select_comment_data($comment_id);	
		
		$template->assign_block_vars('level1', array());
		$template->assign_vars(array(
			'U_LEVEL1' 	 => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=$cid"),
			'L_LEVEL1' 	 => $data['vehicle'],
			'L_EDIT_COMMENT' => $lang['Edit_Comment'],
			'CID' 		 => $cid,
			'COMMENT_ID' 	 => $data['comment_id'],
			'COMMENTS' 	 => $data['post'])
		);

		//Produce The Page
		include($phpbb_root_path . 'includes/page_header.' . $phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_edit_comment.html')
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'update_comment':

		//Only Allow Moderators Or Administrators Perform This Action
		if ( !in_array($userdata['user_level'], array(ADMIN, MOD)) )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=13", true));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('comments', 'COMMENT_ID');
		$data = $garage->process_post_vars($params);

		//Checks All Required Data Is Present
		$params = array('comments', 'COMMENT_ID');
		$garage->check_required_vars($params);

		//Update The Comment In The Vehicle Guestbook
		$garage->update_single_field(GARAGE_GUESTBOOKS_TABLE, 'post', $data['comments'], 'id', $data['COMMENT_ID']);

		redirect(append_sid("garage.$phpEx?mode=view_guestbook&CID=$cid", true));

		break;

	case 'delete_comment':

		//Only Allow Moderators Or Administrators Perform This Action
		if ( !in_array($userdata['user_level'], array(ADMIN, MOD)) )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=13", true));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('comment_id');
		$data = $garage->process_post_vars($params);

		//Delete The Comment From The Guestbook
		$garage->delete_rows(GARAGE_GUESTBOOKS_TABLE, 'id', $data['comment_id']);

		redirect(append_sid("garage.$phpEx?mode=view_guestbook&CID=$cid", true));

		break;

	case 'error':

		include($phpbb_root_path . 'includes/page_header.' . $phpEx);

		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_error.html')
		);

		$template->assign_vars(array(
			'ERROR_MESSAGE' => $lang['Garage_Error_' . $eid],
			'L_GARAGE_ERROR_OCCURED' => $lang['Garage_Error_Occured'])
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_template->sidemenu();
		$template->pparse('body');

		break;

	case 'rate_vehicle':

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('INTERACT', "garage.$phpEx?mode=error&EID=17");

		//Get All Data Posted And Make It Safe To Use
		$params = array('vehicle_rating');
		$data = $garage->process_post_vars($params);
		$data['rate_date'] = time();
		$data['user_id'] = $userdata['user_id'];

		//Checks All Required Data Is Present
		$params = array('vehicle_rating', 'rate_date', 'user_id');
		$garage->check_required_vars($params);

		//Pull Required Data From DB
	        $vehicle_data = $garage_vehicle->select_vehicle_data($cid);

		//If User Is Guest Generate Unique Number For User ID....
		srand($garage->make_seed());
		$data['user_id'] = (!$userdata['session_logged_in']) ? '-' . (rand(2,99999)) : $userdata['user_id'];

		//Check If User Owns Vehicle
		if ( $vehicle_data['member_id'] == $data['user_id'] )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=21", true));
		}

		$count = $garage_vehicle->count_vehicle_ratings($data);

		//If You Have Not Rated This Vehicle..Create A Rating	
		if ( $count['total'] < 1 )
		{
			$garage_vehicle->insert_vehicle_rating($data);
		}
		//You Already Have Rated It..So Just Update The Rating	
		else
		{
			$garage_vehicle->update_vehicle_rating($data);
		}

		redirect(append_sid("garage.$phpEx?mode=view_vehicle&CID=$cid", true));

		break;

	default:

		//Let Check The User Is Allowed Perform This Action
		$garage->check_permissions('BROWSE', "garage.$phpEx?mode=error&EID=15");

		page_header($page_title);

		//Display Page...In Order Header->Menu->Body->Footer
		$garage_template->sidemenu();

		//Display If Needed Featured Vehicle
		$garage_vehicle->show_featuredvehicle();
		
		$required_position = 1;
		//Display All Boxes Required
		$garage_vehicle->show_newest_vehicles();
		$garage_vehicle->show_updated_vehicles();
		$garage_modification->show_newest_modifications();
		$garage_modification->show_updated_modifications();
		$garage_modification->show_most_modified();
		$garage_vehicle->show_most_spent();
		$garage_vehicle->show_most_viewed();
		$garage_guestbook->show_lastcommented();
		$garage_quartermile->show_topquartermile();
		$garage_dynorun->show_topdynorun();
		$garage_vehicle->show_toprated();

		$template->assign_vars(array(
			'TOTAL_VEHICLES' 	=> $garage_vehicle->count_total_vehicles(),
			'TOTAL_VIEWS' 		=> $garage->count_total_views(),
			'TOTAL_MODIFICATIONS' 	=> $garage_modification->count_total_modifications(),
			'TOTAL_COMMENTS'  	=> $garage_guestbook->count_total_comments())
		);

		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'menu' => 'garage_menu.html',
			'body' => 'garage.html')
		);

		break;
}

$garage_template->version_notice();

$template->set_filenames(array(
	'garage_footer' => 'garage_footer.html')
);

page_footer();

?>

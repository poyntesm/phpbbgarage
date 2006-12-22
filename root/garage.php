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
$params = array('cid' => 'CID', 'mid' => 'MID', 'rrid' => 'RRID', 'qmid' => 'QMID', 'ins_id' => 'INS_ID', 'eid' => 'EID', 'image_id' => 'image_id', 'comment_id' => 'CMT_ID', 'bus_id' => 'BUS_ID');
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
	//Mode To Display Create Vehicle Sceen
	case 'create_vehicle':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=create_vehicle");
		}

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_vehicle'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' 	=> 'garage_header.html',
			'body'   	=> 'garage_vehicle.html')
		);

		//Check To See If User Has Too Many Vehicles Already...If So Display Notice
		if ($garage_vehicle->count_user_vehicles() >= $garage_vehicle->get_user_add_quota())
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=5"));
		}

		//Set Make & Model If User Added Them...Else Use Default Values
		$params 	= array('MAKE' => '', 'MODEL' => '', 'YEAR' => '');
		$data 		= $garage->process_vars($params);
		$data['MAKE']	= (empty($data['MAKE'])) ? $garage_config['default_make'] : $data['MAKE'];
		$data['MODEL']	= (empty($data['MODEL'])) ? $garage_config['default_model'] : $data['MODEL'];

		//Get Required Data
		$years = $garage->year_list();
		$makes = $garage_model->get_all_makes();

		//Build All Required Javascript, Arrays & HTML
		$garage_template->attach_image('vehicle');
		$garage_template->make_dropdown($makes);
		$garage_template->engine_dropdown();
		$garage_template->currency_dropdown();
		$garage_template->mileage_dropdown();
		$garage_template->year_dropdown($years, $data['YEAR']);
		$template->assign_vars(array(
			'L_TITLE' 		=> $user->lang['CREATE_NEW_VEHICLE'],
			'L_BUTTON' 		=> $user->lang['CREATE_NEW_VEHICLE'],
			'U_USER_SUBMIT_MAKE' 	=> "javascript:add_make()",
			'U_USER_SUBMIT_MODEL' 	=> "javascript:add_model()",
			'MAKE' 			=> $data['MAKE'],
			'MODEL'			=> $data['MODEL'],
			'S_DISPLAY_SUBMIT_MAKE'	=> $garage_config['enable_user_submit_make'],
			'S_DISPLAY_SUBMIT_MODEL'=> $garage_config['enable_user_submit_make'],
			'S_MODE_ACTION_MAKE' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_make"),
			'S_MODE_ACTION_MODEL' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_model"),
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=insert_vehicle"))
		);
		
		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();		

		break;

	//Mode To Actaully Insert Into DB A New Vehicle
	case 'insert_vehicle':

		//User Is Annoymous...So Not Allowed To Create A Vehicle
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=create_vehicle");
		}

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_vehicle'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Count Vehicles Already Owned
		$user_vehicle_count = $garage_vehicle->count_user_vehicles();

		//Check To See If User Has Too Many Vehicles Already...If So Display Notice
		if ($user_vehicle_count >= $garage_vehicle->get_user_add_quota()) 
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=5"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params	= array('made_year' => '', 'make_id' => '', 'model_id' => '', 'colour' => '', 'mileage' => '', 'mileage_units' => '', 'price' => '', 'currency' => '', 'comments' => '', 'engine_type' => '');
		$data	= $garage->process_vars($params);

		//Set As Main User Vehicle If No Other Vehicle Exists For User
		$data['main_vehicle'] = ($user_vehicle_count == 0) ? 1 : 0;

		//Checks All Required Data Is Present
		$params = array('year', 'make_id', 'model_id');
		$garage->check_required_vars($params);

		//Insert The Vehicle Into The DB And Get The CID
		$cid = $garage_vehicle->insert_vehicle($data);

		//If Any Image Variables Set Enter The Image Handling
		if ($garage_image->image_attached())
		{
			//Check For Remote & Local Image Quotas
			if ($garage_image->below_image_quotas())
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('vehicle', $cid);
				//Insert Image Into Vehicles Gallery
				$garage_image->insert_gallery_image($image_id);
				//Set Image As Hilite Image For Vehicle
				$garage->update_single_field(GARAGE_TABLE, 'image_id', $image_id, 'id', $cid);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ($garage_image->above_image_quotas())
			{
				redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=4"));
			}
		}

		//If Needed Perform Notifications If Configured
		if ($garage_config['enable_vehicle_approval'])
		{
			$garage->pending_notification('unapproved_vehicles');
		}

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

	//Mode To Display Editting Page Of An Existing Vehicle
	case 'edit_vehicle':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=edit_vehicle&amp;CID=$cid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' 	=> 'garage_header.html',
			'body'   	=> 'garage_vehicle.html')
		);

		//Pull Required Vehicle Data From DB
		$data 	= $garage_vehicle->get_vehicle($cid);
		$years	= $garage->year_list();
		$makes 	= $garage_model->get_all_makes();

		//Build All Required Javascript And Arrays
		$garage_template->make_dropdown($makes, $data['make_id']);
		$garage_template->engine_dropdown($data['engine_type']);
		$garage_template->currency_dropdown($data['currency']);
		$garage_template->mileage_dropdown($data['mileage_units']);
		$garage_template->year_dropdown($data['made_year']);
		$template->assign_vars(array(
       			'L_TITLE' 		=> $user->lang['EDIT_VEHICLE'],
			'L_BUTTON' 		=> $user->lang['EDIT_VEHICLE'],
			'U_USER_SUBMIT_MAKE' 	=> "javascript:add_make()",
			'U_USER_SUBMIT_MODEL' 	=> "javascript:add_model()",
			'CID' 			=> $cid,
			'MAKE' 			=> $data['make'],
			'MAKE_ID' 		=> $data['make_id'],
			'MODEL' 		=> $data['model'],
			'MODEL_ID' 		=> $data['model_id'],
			'COLOUR' 		=> $data['colour'],
			'MILEAGE' 		=> $data['mileage'],
			'PRICE' 		=> $data['price'],
			'COMMENTS' 		=> $data['comments'],
			'S_DISPLAY_SUBMIT_MAKE'	=> $garage_config['enable_user_submit_make'],
			'S_DISPLAY_SUBMIT_MODEL'=> $garage_config['enable_user_submit_make'],
			'S_MODE_ACTION_MAKE' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_make"),
			'S_MODE_ACTION_MODEL' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_model"),
			'S_MODE_ACTION'		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=update_vehicle"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	//Mode To Actaully Update The DB Of An Existing Vehicle
	case 'update_vehicle':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=edit_vehicle&amp;CID=$cid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('made_year' => '', 'make_id' => '', 'model_id' => '', 'colour' => '', 'mileage' => '', 'mileage_units' => '', 'price' => '', 'currency' => '', 'comments' => '', 'engine_type' => '');
		$data = $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('made_year', 'make_id', 'model_id');
		$garage->check_required_vars($params);

		//Update The Vehicle With Data Acquired
		$garage_vehicle->update_vehicle($data);
	
		//Update Timestamp For Vehicle	
		$garage_vehicle->update_vehicle_time($cid);

		//If Needed perform Notifications If Configured
		if ($garage_config['enable_vehicle_approval'])
		{
			$garage->pending_notification('unapproved_vehicles');
		}

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

	//Mode To Delete A Vehicle From The DB
	case 'delete_vehicle':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_delete_vehicle'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Actually Delete The Vehicle..This Will Delete All Related Items!!
		$garage_vehicle->delete_vehicle($cid);

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=main_menu"));

		break;

	//Mode To Display Add Modification Page
	case 'add_modification':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=add_modification&amp;CID=$cid");
		}

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_modification'))
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
			'body'   => 'garage_modification.html')
		);

		//Get Data Incase We Are Returning From Adding A Product So Its Selected..
		$params = array('category_id' => '', 'manufacturer_id' => '', 'product_id' => '');
		$data = $garage->process_vars($params);

		//Get Required Data For Dropdowns
		$categories 	= $garage->get_categories();
		$shops	 	= $garage_business->get_business_by_type(BUSINESS_RETAIL);
		$garages 	= $garage_business->get_business_by_type(BUSINESS_GARAGE);
		$manufacturers 	= $garage_business->get_business_by_type(BUSINESS_PRODUCT);

		//Build HTML Components
		$garage_template->attach_image('modification');
		$garage_template->category_dropdown($categories, $data['category_id']);
		$garage_template->manufacturer_dropdown($manufacturers, $data['manufacturer_id']);
		$garage_template->retail_dropdown($shops);
		$garage_template->garage_dropdown($garages);
		$garage_template->rating_dropdown('product_rating');
		$garage_template->rating_dropdown('purchase_rating');
		$garage_template->rating_dropdown('install_rating');
		$template->assign_vars(array(
			'L_BUTTON' 			=> $user->lang['ADD_MODIFICATION'],
			'L_TITLE' 			=> $user->lang['ADD_MODIFICATION'],
			'U_SUBMIT_PRODUCT'		=> "javascript:add_product('add_modification')",
			'U_SUBMIT_BUSINESS_SHOP'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_business&amp;CID=$cid&amp;redirect=add_modification&amp;BUSINESS=" . BUSINESS_RETAIL ),
			'U_SUBMIT_BUSINESS_GARAGE'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_business&amp;CID=$cid&amp;redirect=add_modification&amp;BUSINESS=". BUSINESS_GARAGE),
			'U_SUBMIT_BUSINESS_PRODUCT'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_business&amp;CID=$cid&amp;redirect=add_modification&amp;BUSINESS=". BUSINESS_PRODUCT),
			'CID' 				=> $cid,
			'CATEGORY_ID' 			=> $data['category_id'],
			'MANUFACTURER_ID' 		=> $data['manufacturer_id'],
			'PRODUCT_ID' 			=> $data['product_id'],
			'S_DISPLAY_SUBMIT_BUSINESS'	=> ($garage_config['enable_user_submit_business'] && $auth->acl_get('u_garage_add_business')) ? true : false,
			'S_MODE_ACTION_PRODUCT' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_product"),
			'S_MODE_ACTION'			=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=insert_modification&amp;CID=$cid"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'insert_modification':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=add_modification&amp;CID=$cid");
		}

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_modification'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('category_id' => '' , 'manufacturer_id' => '', 'product_id' =>'', 'price' => '', 'shop_id' => '', 'installer_id' => '', 'install_price' => '', 'install_rating' => '', 'product_rating' => '', 'comments' => '', 'install_comments' => '', 'purchase_rating' => '');
		$data	= $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('category_id', 'manufacturer_id', 'product_id');
		$garage->check_required_vars($params);

		//Insert The Modification Into The DB With Data Acquired
		$mid = $garage_modification->insert_modification($data);

		//Update The Time Now...In Case We Get Redirected During Image Processing
		$garage_vehicle->update_vehicle_time($cid);

		//If Any Image Variables Set Enter The Image Handling
		if ($garage_image->image_attached())
		{
			//Check For Remote & Local Image Quotas
			if ($garage_image->below_image_quotas())
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('modification', $mid);
				//Set Image To This Modification
				$garage->update_single_field(GARAGE_MODS_TABLE, 'image_id', $image_id, 'id', $mid);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ($garage_image->above_image_quotas())
			{
				redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=4"));
			}
		}

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

	case 'edit_modification':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=edit_modification&amp;MID=$mid&amp;CID=$cid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_modification.html')
		);
		
		//Get Required Data
		$data 		= $garage_modification->get_modification($mid);
		$categories 	= $garage->get_categories();
		$shops 		= $garage_business->get_business_by_type(BUSINESS_RETAIL);
		$garages 	= $garage_business->get_business_by_type(BUSINESS_GARAGE);
		$manufacturers 	= $garage_business->get_business_by_type(BUSINESS_PRODUCT);

		//Build All Required HTML parts
		$garage_template->edit_image('modification', $data['image_id'], $data['attach_file']);
		$garage_template->category_dropdown($categories, $data['category_id']);
		$garage_template->manufacturer_dropdown($manufacturers, $data['manufacturer_id']);
		$garage_template->retail_dropdown($shops, $data['shop_id']);
		$garage_template->garage_dropdown($garages, $data['installer_id']);
		$garage_template->rating_dropdown('product_rating', $data['product_rating']);
		$garage_template->rating_dropdown('purchase_rating', $data['purchase_rating']);
		$garage_template->rating_dropdown('install_rating', $data['install_rating']);
		$template->assign_vars(array(
       			'L_TITLE' 		=> $user->lang['MODIFY_MOD'],
       			'L_BUTTON' 		=> $user->lang['MODIFY_MOD'],
			'U_SUBMIT_PRODUCT'	=> "javascript:add_product('edit_modification')",
			'U_SUBMIT_SHOP'		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_business&amp;CID=$cid&amp;redirect=add_modification&amp;BUSINESS=" . BUSINESS_RETAIL),
			'U_SUBMIT_GARAGE'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_business&amp;CID=$cid&amp;redirect=add_modification&amp;BUSINESS=" . BUSINESS_GARAGE),
			'MID' 			=> $mid,
			'CID' 			=> $cid,
			'TITLE' 		=> $data['title'],
			'MAKE' 			=> $data['make'],
			'MODEL' 		=> $data['model'],
			'PRICE' 		=> $data['price'],
			'INSTALL_PRICE' 	=> $data['install_price'],
			'MANUFACTURER_ID' 	=> $data['manufacturer_id'],
			'PRODUCT_ID' 		=> $data['product_id'],
			'CATEGORY_ID' 		=> $data['category_id'],
			'MANUFACTURER_ID' 	=> $data['manufacturer_id'],
			'PRODUCT_ID' 		=> $data['product_id'],
			'COMMENTS' 		=> $data['comments'],
			'INSTALL_COMMENTS' 	=> $data['install_comments'],
			'S_DISPLAY_SUBMIT_BUS'	=> $garage_config['enable_user_submit_business'],
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=update_modification"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();		

		break;

	case 'update_modification':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ( $user->data['user_id'] == ANONYMOUS )
		{
			login_box("garage.$phpEx?mode=edit_modification&amp;MID=$mid&amp;CID=$cid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('category_id' => '', 'manufacturer_id' => '', 'product_id' => '', 'price' => '', 'shop_id' => '', 'installer_id' => '', 'install_price' => '', 'install_rating' => '', 'product_rating' => '', 'comments' => '', 'install_comments' => '', 'editupload' => '', 'image_id' => '', 'purchase_rating' => '');
		$data	= $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('category_id', 'manufacturer_id', 'product_id');
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
		if ($garage_image->image_attached() )
		{
			//Check For Remote & Local Image Quotas
			if ( $garage_image->below_image_quotas() )
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('modification', $mid);
				//Set Image To This Modification
				$garage->update_single_field(GARAGE_MODS_TABLE, 'image_id', $image_id, 'id', $mid);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ( $garage_image->above_image_quotas() )
			{
				redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=4"));
			}
		}

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

	case 'delete_modification':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_delete_modification'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Delete The Modification
		$garage_modification->delete_modification($mid);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

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

		//If Dynoruns Exist, Allow User To Link Quartermile Times To Know Vehicle Spec..
		if ( $garage_dynorun->count_runs($cid) > 0 )
		{
			$template->assign_block_vars(array(
				'S_DISPLAY_DYNORUNS' => true)
			);
			$dynoruns = $garage_dynorun->get_dynoruns_by_vehicle($cid);
			$garage_template->dynorun_dropdown($dynoruns);
		}

		$garage_template->attach_image('quartermile');
		$template->assign_vars(array(
			'L_TITLE'  	=> $user->lang['ADD_NEW_TIME'],
			'L_BUTTON'  	=> $user->lang['ADD_NEW_TIME'],
			'CID' 		=> $cid,
			'S_MODE_ACTION' => append_sid("{$phpbb_root_path}garage.$phpEx", "mode=insert_quartermile"))
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
		$params	= array('rt' => '', 'sixty' => '', 'three' => '', 'eighth' => '', 'eighthmph' => '', 'thou' => '', 'quart' => '', 'quartmph' => '', 'rr_id' => '', 'install_comments' => '');
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
				$garage->update_single_field(GARAGE_QUARTERMILE_TABLE, 'image_id', $image_id, 'id', $qmid);
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

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

	case 'edit_quartermile':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=edit_quartermile&amp;QMID=$qmid&amp;CID=$cid");
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
		if ((!empty($data['rr_id'])) AND ($count > 0))
		{
			$bhp_statement = $data['bhp'] . ' BHP @ ' . $data['bhp_unit'];
			$template->assign_block_vars('link_rr', array());
			$garage_template->dynorun_dropdown($data['rr_id'], $bhp_statement, $cid);
		}
		//Allow User To Link To Dynorun
		else if ((empty($data['rr_id'])) AND ($count > 0))
		{
			$template->assign_block_vars('link_rr', array());
			$garage_template->dynorun_dropdown(NULL, NULL, $cid);
		}

		//Build All HTML Parts
		$garage_template->edit_image('quartermile', $data['image_id'], $data['attach_file']);
		$template->assign_vars(array(
			'L_TITLE'		=> $user->lang['EDIT_TIME'],
			'L_BUTTON'		=> $user->lang['EDIT_TIME'],
			'CID'			=> $cid,
			'QMID'			=> $qmid,
			'RT'			=> $data['rt'],
			'SIXTY'			=> $data['sixty'],
			'THREE' 		=> $data['three'],
			'EIGHT' 		=> $data['eighth'],
			'EIGHTMPH' 		=> $data['eighthmph'],
			'THOU' 			=> $data['thou'],
			'QUART' 		=> $data['quart'],
			'QUARTMPH' 		=> $data['quartmph'],
			'PENDING_REDIRECT'	=> $redirect['PENDING'],
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=update_quartermile"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'update_quartermile':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=edit_quartermile&amp;QMID=$qmid&amp;CID=$cid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('rt' => '', 'sixty' => '', 'three' => '', 'eighth' => '', 'eighthmph' => '', 'thou' => '', 'quart' => '', 'quartmph' => '', 'rr_id' => '', 'install_comments' => '', 'editupload' => '', 'image_id' => '', 'pending_redirect' => '');
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
			$garage_image->delete_image($data['image_id']);
			$garage->update_single_field(GARAGE_QUARTERMILE_TABLE, 'image_id', 'NULL', 'id', $qmid);
		}

		//If Any Image Variables Set Enter The Image Handling
		if ($garage_image->image_attached())
		{
			//Check For Remote & Local Image Quotas
			if ($garage_image->below_image_quotas())
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('quartermile', $qmid);
				$garage->update_single_field(GARAGE_QUARTERMILE_TABLE, 'image_id', $image_id, 'id', $qmid);
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

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

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

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;
	
	case 'add_dynorun':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=add_dynorun&amp;CID=$cid");
		}

		//Let Check That Rollingroad Runs Are Allowed...If Not Redirect
		if (!$garage_config['enable_dynorun'] || !$auth->acl_get('u_garage_add_dynorun'))
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
			'body'   => 'garage_dynorun.html')
		);

		//Build Required HTML Components Like Drop Down Boxes.....
		$garage_template->attach_image('dynorun');
		$garage_template->nitrous_dropdown();
		$garage_template->power_dropdown('bhp_unit');
		$garage_template->power_dropdown('torque_unit');
		$garage_template->boost_dropdown();
		$template->assign_vars(array(
			'L_TITLE'  	=> $user->lang['ADD_NEW_RUN'],
			'L_BUTTON'  	=> $user->lang['ADD_NEW_RUN'],
			'CID' 		=> $cid,
			'S_MODE_ACTION' => append_sid("{$phpbb_root_path}garage.$phpEx", "mode=insert_dynorun"))
         	);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'insert_dynorun':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=add_dynorun&amp;CID=$cid");
		}

		//Let Check That Rollingroad Runs Are Allowed...If Not Redirect
		if (!$garage_config['enable_dynorun'])
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_dynorun'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('dynocenter' => '', 'bhp' => '', 'bhp_unit' => '', 'torque' => '', 'torque_unit' => '', 'boost' => '', 'boost_unit' => '', 'nitrous' => '', 'peakpoint' => '');
		$data 	= $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('bhp', 'bhp_unit');
		$garage->check_required_vars($params);

		//Update The Dynorun With Data Acquired
		$rrid = $garage_dynorun->insert_dynorun($data);

		//Update The Time Now...In Case We Get Redirected During Image Processing
		$garage_vehicle->update_vehicle_time($cid);

		//If Any Image Variables Set Enter The Image Handling
		if ($garage_image->image_attached())
		{
			//Check For Remote & Local Image Quotas
			if ($garage_image->below_image_quotas())
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('dynorun', $rrid);
				$garage->update_single_field(GARAGE_DYNORUN_TABLE,'image_id', $image_id, 'id', $rrid);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ($garage_image->above_image_quotas())
			{
				redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=4"));
			}
		}
		//No Image Attached..We Need To Check If This Breaks The Site Rule
		else if (($garage_config['enable_dynorun_image_required'] == '1') AND ($data['bhp'] >= $garage_config['dynorun_image_required_limit']))
		{
			//That Time Requires An Image...Delete Entered Time And Notify User
			$garage_dynorun->delete_dynorun($rrid);
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=26"));
		}

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ($garage_config['enable_dynorun_approval'])
		{
			$garage->pending_notification('unapproved_dynoruns');
		}

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

	case 'edit_dynorun':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=edit_dynorun&amp;RRID=$rrid&amp;CID=$cid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_dynorun.html')
		);

		//Pull Required Dynorun Data From DB
		$data = $garage_dynorun->get_dynorun($rrid);

		//See If We Got Sent Here By Pending Page...If So We Need To Tell Update To Redirect Correctly
		$params = array('PENDING' => '');
		$redirect = $garage->process_vars($params);

		//Build All Required HTML
		$garage_template->edit_image('dynorun', $data['image_id'], $data['attach_file']);
		$garage_template->nitrous_dropdown($data['nitrous']);
		$garage_template->power_dropdown('bhp_unit', $data['bhp_unit']);
		$garage_template->power_dropdown('torque_unit', $data['torque_unit']);
		$garage_template->boost_dropdown($data['boost_unit']);
		$template->assign_vars(array(
			'L_TITLE'  		=> $user->lang['EDIT_RUN'],
			'L_BUTTON'  		=> $user->lang['EDIT_RUN'],
			'CID' 			=> $cid,
			'RRID' 			=> $rrid,
			'DYNOCENTER' 		=> $data['dynocenter'],
			'BHP' 			=> $data['bhp'],
			'TORQUE' 		=> $data['torque'],
			'BOOST' 		=> $data['boost'],
			'NITROUS' 		=> $data['nitrous'],
			'PEAKPOINT' 		=> $data['peakpoint'],
			'PENDING_REDIRECT'	=> $redirect['PENDING'],
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=update_dynorun"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'update_dynorun':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=edit_dynorun&amp;RRID=$rrid&amp;CID=$cid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('dynocenter' => '', 'bhp' => '', 'bhp_unit' => '', 'torque' => '', 'torque_unit' => '', 'boost' => '', 'boost_unit' => '', 'nitrous' => '', 'peakpoint' => '', 'editupload' => '', 'image_id' => '', 'pending_redirect' => '');
		$data 	= $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('bhp', 'bhp_unit');
		$garage->check_required_vars($params);

		//Update The Dynorun With Data Acquired
		$garage_dynorun->update_dynorun($data);

		//Update The Time Now...In Case We Get Redirected During Image Processing
		$garage_vehicle->update_vehicle_time($cid);

		//Removed The Old Image If Required By A Delete Or A New Image Existing
		if (($data['editupload'] == 'delete') OR ($data['editupload'] == 'new'))
		{
			$garage_image->delete_image($data['image_id']);
			$garage->update_single_field(GARAGE_DYNORUN_TABLE, 'image_id', 'NULL', 'id', $rrid);
		}

		//If Any Image Variables Set Enter The Image Handling
		if ($garage_image->image_attached())
		{
			//Check For Remote & Local Image Quotas
			if ($garage_image->below_image_quotas())
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('dynorun', $rrid);
				$garage->update_single_field(GARAGE_DYNORUN_TABLE, 'image_id', $image_id, 'id', $rrid);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ($garage_image->above_image_quotas())
			{
				redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=4"));
			}
		}
		//No Image Attached..We Need To Check If This Breaks The Site Rule
		else if (($garage_config['enable_dynorun_image_required'] == '1') AND ($data['bhp'] >= $garage_config['dynorun_image_required_limit']))
		{
			//That Time Requires An Image...Delete Entered Time And Notify User
			$garage_dynorun->delete_dynorun($rrid);
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=26"));
		}

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ($garage_config['enable_dynorun_approval'])
		{
			$garage->pending_notification('unapproved_dynoruns');
		}

		//If Editting From Pending Page Redirect Back To There Instead
		if ($data['pending_redirect'] == 'MCP')
		{
			redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_dynoruns"));
		}

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

	case 'delete_dynorun':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_delete_dynorun'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Delete The Dynorun
		$garage_dynorun->delete_dynorun($rrid);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

	case 'add_insurance':

		//Let Check That Insurance Premiums Are Allowed...If Not Redirect
		if (!$garage_config['enable_insurance'])
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_insurance'))
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
			'body'   => 'garage_insurance.html')
		);

		//Get Data
		$insurance_business 	= $garage_business->get_business_by_type(BUSINESS_INSURANCE);

		//Build All Required HTML Components
		$garage_template->insurance_dropdown($insurance_business);
		$garage_template->cover_dropdown();
		$template->assign_vars(array(
			'L_TITLE' 		=> $user->lang['ADD_PREMIUM'],
			'L_BUTTON' 		=> $user->lang['ADD_PREMIUM'],
			'U_SUBMIT_BUSINESS' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_business&amp;CID=$cid&amp;redirect=add_insurance&amp;BUSINESS=" . BUSINESS_INSURANCE),
			'CID' 			=> $cid,
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=insert_insurance"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'insert_insurance':

		//Let Check That Insurance Premiums Are Allowed...If Not Redirect
		if (!$garage_config['enable_insurance'])
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_insurance'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('business_id' => '', 'premium' => '', 'cover_type' => '', 'comments' => '');
		$data 	= $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('business_id', 'premium', 'cover_type');
		$garage->check_required_vars($params);

		//Insert The Insurnace Premium
		$garage_insurance->insert_premium($data);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

	case 'edit_insurance':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=edit_insurance&amp;INS_ID=$ins_id&amp;CID=$cid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_insurance.html')
		);

		//Pull Required Insurance Premium Data From DB
		$data = $garage_insurance->get_premium($ins_id);
		$insurance_business = $garage_business->get_business_by_type(BUSINESS_INSURANCE);

		//Prepare Data For Dropdown Generation
		for ($i = 0, $count = sizeof($insurance_business);$i < $count; $i++)
		{
			$insurance_id[] = $insurance_business[$i]['id'];
			$insurnace_title[] = $insurance_business[$i]['title'];
		}

		//Build Required HTML Components
		$garage_template->insurance_dropdown($insurance_business, $data['business_id']);
		$garage_template->cover_dropdown($data['cover_type']);
		$template->assign_vars(array(
			'L_TITLE' 		=> $user->lang['EDIT_PREMIUM'],
			'L_BUTTON' 		=> $user->lang['EDIT_PREMIUM'],
			'INS_ID' 		=> $ins_id,
			'CID' 			=> $cid,
			'PREMIUM' 		=> $data['premium'],
			'COMMENTS' 		=> $data['comments'],
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=update_insurance"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'update_insurance':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('business_id' => '', 'premium' => '', 'cover_type' => '', 'comments' => '');
		$data = $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('business_id', 'premium', 'cover_type');
		$garage->check_required_vars($params);

		//Update The Insurance Premium With Data Acquired
		$garage_insurnace->update_premium($data);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;
	
	case 'delete_insurance':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_delete_insurance'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Delete Insurance Premium
		$garage_insurance->delete_premium($ins_id);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

	//Mode To Display A List Of Vehicles..Also Used To Display Search Results For Search By Make/Model/User
	case 'browse':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Set Required Values To Defaults If They Are Empty
		$start	= (empty($start)) ? '0' : $start;
		$sort 	= (empty($sort)) ? 'date_updated' : $sort;
		$order 	= (empty($order)) ? 'DESC' : $order;

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_browse.html')
		);

		//Build Page Header ;)
		page_header($page_title);

		//Get All Vehicle Data....
		$data = $garage_vehicle->get_all_vehicles('', $sort, $order, $start, $garage_config['cars_per_page']);
		for ($i = 0, $count = sizeof($data); $i < $count; $i++)
      		{
			$template->assign_block_vars('vehiclerow', array(
				'U_VIEW_VEHICLE'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;CID=" . $data[$i]['id']),
				'U_VIEW_PROFILE'	=> append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=" . $data[$i]['user_id']),
				'ROW_NUMBER' 		=> $i + ( $start + 1 ),
				'IMAGE_ATTACHED'	=> ($data[$i]['image_id']) ? $user->img('vehicle_image_attached', 'VEHICLE_IMAGE_ATTAHCED') : '',
				'YEAR' 			=> $data[$i]['made_year'],
				'MAKE' 			=> $data[$i]['make'],
				'COLOUR' 		=> $data[$i]['colour'],
				'UPDATED' 		=> $user->format_date($data[$i]['date_updated']),
				'VIEWS' 		=> $data[$i]['views'],
				'MODS' 			=> $data[$i]['total_mods'],
				'MODEL' 		=> $data[$i]['model'],
				'OWNER'			=> $data[$i]['username'])
			);
		}

		//Count Total Returned For Pagination...Notice No $start or $end to get complete count
		$count = $garage_vehicle->get_all_vehicles('', $sort, $order);

		$pagination = generate_pagination("garage.$phpEx?mode=browse&amp;sort=$sort&amp;order=$order", $count[0]['total'], $garage_config['cars_per_page'], $start);

		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['BROWSE'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}{$phpbb_root_path}garage.$phpEx", "mode=browse"))
		);

		$garage_template->order_dropdown($order);
		$garage_template->sort_dropdown('vehicle', $sort);
		$template->assign_vars(array(
			'PAGINATION' 	=> $pagination,
			'PAGE_NUMBER' 	=> sprintf($user->lang['PAGE_OF'], ( floor( $start / $garage_config['cars_per_page'] ) + 1 ), ceil( $count[0]['total'] / $garage_config['cars_per_page'] )), 
			'S_MODE_ACTION' => append_sid("{$phpbb_root_path}garage.$phpEx", "mode=browse"))
		);
	

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	//Display Search Options Page...
	case 'search':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_search'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' 	=> 'garage_header.html',
			'body'   	=> 'garage_search.html')
		);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['SEARCH'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=search"))
		);

		//Get Years As Defined By Admin In ACP
		$years 		= $garage->year_list();
		$manufacturers 	= $garage_business->get_business_by_type(BUSINESS_PRODUCT);
		$makes 		= $garage_model->get_all_makes();
		$categories 	= $garage->get_categories();

		//Build All Required Javascript And Arrays
		$garage_template->category_dropdown($categories);
		$garage_template->year_dropdown($years);
		$garage_template->make_dropdown($makes);
		$garage_template->manufacturer_dropdown($manufacturers);
		$template->assign_vars(array(
			'S_DISPLAY_SEARCH_INSURANCE'		=> $garage_config['enable_insurance'],
			'S_MODE_ACTION_SEARCH_USERNAME' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=search_username"),
			'S_MODE_ACTION_SEARCH_INSURANCE'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=search_insurance"),
			'S_MODE_ACTION_SEARCH_VEHICLE' 		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=search_vehicle"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	//Mode To Display A List Of Vehicles..Also Used To Display Search Results For Search By Make/Model/User
	case 'search_vehicle':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_search'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Set Required Values To Defaults If They Are Empty
		$start	= (empty($start)) ? '0' : $start;
		$sort	= (empty($sort)) ? 'date_updated' : $sort;
		$order	= (empty($order)) ? 'DESC' : $order;

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_browse.html')
		);

		//Build Page Header ;)
		page_header($page_title);

		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['SEARCH'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=search"))
		);

		$search_data = $garage_model->build_search_for_user_make_model();

      		$template->assign_block_vars('switch_search', array());
		$template->assign_vars(array(
			'SEARCH_MESSAGE' => $search_data['search_message'])
		);

		//Setup Arrays For Producing Sort Options Drop Down Selection Box
		$sort_types_text = array($user->lang['LAST_CREATED'], $user->lang['LAST_UPDATED'], $user->lang['OWNER'], $user->lang['YEAR'], $user->lang['MAKE'], $user->lang['MODEL'],  $user->lang['COLOUR'], $user->lang['TOTAL_VIEWS'], $user->lang['TOTAL_MODS']);
		$sort_types = array('date_created', 'date_updated', 'username', 'made_year', 'make', 'model', 'colour', 'views', 'total_mods');

		//Build All Required HTML
		$garage_template->order_dropdown($sort_order);

		//Get All Vehicle Data....
		$data = $garage_vehicle->get_all_vehicles($search_data['where'], $sort, $order, $start, $garage_config['cars_per_page']);
		for ($i = 0, $count = sizeof($data); $i < $count; $i++)
      		{
			$template->assign_block_vars('vehiclerow', array(
				'U_VIEW_VEHICLE'=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;CID=" . $data[$i]['id']),
				'U_VIEW_PROFILE'=> append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=" . $data[$i]['user_id']),
				'ROW_NUMBER' 	=> $i + ( $start + 1 ),
				'IMAGE_ATTACHED'=> ($data[$i]['image_id']) ? $user->img('vehicle_image_attached', 'VEHICLE_IMAGE_ATTAHCED') : '',
				'YEAR' 		=> $data[$i]['made_year'],
				'MAKE' 		=> $data[$i]['make'],
				'COLOUR'	=> $data[$i]['colour'],
				'UPDATED'	=> $user->format_date($data[$i]['date_updated']),
				'VIEWS'		=> $data[$i]['views'],
				'MODS'		=> $data[$i]['total_mods'],
				'MODEL'		=> $data[$i]['model'],
				'OWNER'		=> $data[$i]['username'])
			);
		}

		//Count Total Returned For Pagination...Notice No $start or $end to get complete count
		$count = $garage_vehicle->get_all_vehicles($search_data['where'], $sort, $order);

		$pagination = generate_pagination("garage.$phpEx?mode=browse&amp" . $search_data['make_pagination'] . $search_data['model_pagination'] . ";sort=$sort&amp;order=$order", $count[0]['total'], $garage_config['cars_per_page'], $start);

		$template->assign_vars(array(
			'MAKE_ID' 	=> $search_data['make_id'],
			'MODEL_ID' 	=> $search_data['model_id'],
			'PAGINATION' 	=> $pagination,
			'PAGE_NUMBER' 	=> sprintf($user->lang['PAGE_OF'], ( floor( $start / $garage_config['cars_per_page'] ) + 1 ), ceil( $count[0]['total'] / $garage_config['cars_per_page'] )), 
			'S_SORT_SELECT' => $garage_template->dropdown('sort', $sort_types_text, $sort_types, $sort),
			'S_MODE_ACTION' => append_sid("{$phpbb_root_path}garage.$phpEx", "mode=browse"))
		);
	
		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	//Mode To Display A List Of Vehicles..Also Used To Display Search Results For Search By Make/Model/User
	case 'search_username':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_search'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Set Required Values To Defaults If They Are Empty
		$start	= (empty($start)) ? '0' : $start;
		$sort	= (empty($sort)) ? 'date_updated' : $sort;
		$order	= (empty($order)) ? 'DESC' : $order;

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_browse.html')
		);

		//Build Page Header ;)
		page_header($page_title);

		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['SEARCH'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}{$phpbb_root_path}garage.$phpEx", "mode=search"))
		);

		$search_data = $garage_model->build_search_for_user_make_model();

      		$template->assign_block_vars('switch_search', array());
		$template->assign_vars(array(
			'SEARCH_MESSAGE' => $search_data['search_message'])
		);

		//Setup Arrays For Producing Sort Options Drop Down Selection Box
		$sort_types_text = array($user->lang['LAST_CREATED'], $user->lang['LAST_UPDATED'], $user->lang['OWNER'], $user->lang['YEAR'], $user->lang['MAKE'], $user->lang['MODEL'],  $user->lang['COLOUR'], $user->lang['TOTAL_VIEWS'], $user->lang['TOTAL_MODS']);
		$sort_types = array('date_created', 'date_updated', 'username', 'made_year', 'make', 'model', 'colour', 'views', 'total_mods');

		//Build All Required HTML
		$garage_template->order_dropdown($sort_order);

		//Get All Vehicle Data....
		$data = $garage_vehicle->get_all_vehicles($search_data['where'], $sort, $order, $start, $garage_config['cars_per_page']);
		for ($i = 0, $count = sizeof($data); $i < $count; $i++)
      		{
			$template->assign_block_vars('vehiclerow', array(
				'U_VIEW_VEHICLE'=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;CID=" . $data[$i]['id']),
				'U_VIEW_PROFILE'=> append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=" . $data[$i]['user_id']),
				'ROW_NUMBER' 	=> $i + ( $start + 1 ),
				'IMAGE_ATTACHED'=> ($data[$i]['image_id']) ? $user->img('vehicle_image_attached', 'VEHICLE_IMAGE_ATTAHCED') : '',
				'YEAR' 		=> $data[$i]['made_year'],
				'MAKE' 		=> $data[$i]['make'],
				'COLOUR' 	=> $data[$i]['colour'],
				'UPDATED' 	=> $user->format_date($data[$i]['date_updated']),
				'VIEWS' 	=> $data[$i]['views'],
				'MODS' 		=> $data[$i]['total_mods'],
				'MODEL'		=> $data[$i]['model'],
				'OWNER'	 	=> $data[$i]['username'])
			);
		}

		//Count Total Returned For Pagination...Notice No $start or $end to get complete count
		$count = $garage_vehicle->get_all_vehicles($search_data['where'], $sort, $order);

		$pagination = generate_pagination("garage.$phpEx?mode=search_username&amp;sort=$sort&amp;order=$order", $count[0]['total'], $garage_config['cars_per_page'], $start);

		$template->assign_vars(array(
			'PAGINATION' 	=> $pagination,
			'PAGE_NUMBER' 	=> sprintf($user->lang['PAGE_OF'], ( floor( $start / $garage_config['cars_per_page'] ) + 1 ), ceil( $count[0]['total'] / $garage_config['cars_per_page'] )), 
			'S_SORT_SELECT' => $garage_template->dropdown('sort', $sort_types_text, $sort_types, $sort),
			'S_MODE_ACTION' => append_sid("{$phpbb_root_path}garage.$phpEx", "mode=browse"))
		);
	
		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	//Mode To Display Searches Of Insurance
	case 'search_insurance':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Set Required Values To Defaults If They Are Empty
		$start	= (empty($start)) ? '0' : $start;
		$sort	= (empty($sort)) ? 'premium' : $sort;
		$order	= (empty($order)) ? 'ASC' : $order;

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header'	=> 'garage_header.html',
			'body' 		=> 'garage_browse_insurance.html')
		);

		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['SEARCH'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}{$phpbb_root_path}garage.$phpEx", "mode=search"))
		);

		$search_data = $garage_model->build_insurance_search_for_make_model();
		
		$sort_types_text = array($user->lang['PRICE'], $user->lang['MOD_PRICE'], $user->lang['OWNER'], $user->lang['PREMIUM'], $user->lang['COVER_TYPE'], $user->lang['BUSINESS_NAME']);
		$sort_types = array('g.price', 'total_spent', 'username', 'premium', 'cover_type', 'name');

	      	$template->assign_block_vars('switch_search', array());
		$template->assign_vars(array(
			'SEARCH_MESSAGE' => $search_data['search_message'])
		);

		//Pre Build All Side Menus
		$garage_template->order_dropdown($sort_order);

		//Get All Insurance Data....
		$data = $garage_insurance->get_all_premiums($search_data['where'], $sort, $order, $start, $garage_config['cars_per_page']);
		for ($i = 0, $count = sizeof($data);$i < $count; $i++)
      		{
			$template->assign_block_vars('vehiclerow', array(
				'U_VIEW_VEHICLE'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;CID=" . $data[$i]['id']),
				'U_VIEW_PROFILE' 	=> append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=" . $data[$i]['user_id']),
				'U_VIEW_BUSINESS' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_insurance_business&amp;business_id=" . $data[$i]['business_id']),
				'VEHICLE' 		=> $data[$i]['vehicle'],
				'USERNAME' 		=> $data[$i]['username'],
				'BUSINESS' 		=> $data[$i]['title'],
				'PRICE' 		=> $data[$i]['price'],
				'MOD_PRICE' 		=> $data[$i]['total_spent'],
				'PREMIUM' 		=> $data[$i]['premium'],
				'COVER_TYPE' 		=> $data[$i]['cover_type'])
			);
		}

		//Count Total Returned For Pagination...Notice No $start or $end to get complete count
		$count = sizeof($garage_insurance->get_all_premiums($search_data['where'], $sort, $order));

		$pagination = generate_pagination("garage.$phpEx?mode=search_insurance&amp;make_id=" . $search_data['make_id'] . "&amp;model_id=" . $search_data['model_id'] . "&amp;sort=$sort&amp;order=$order", $count, $garage_config['cars_per_page'], $start);

		$template->assign_vars(array(
			'PAGINATION' 	=> $pagination,
			'PAGE_NUMBER' 	=> sprintf($user->lang['PAGE_OF'], ( floor( $start / $garage_config['cars_per_page'] ) + 1 ), ceil( $count / $garage_config['cars_per_page'] )), 
			'S_SORT_SELECT' => $garage_template->dropdown('sort', $sort_types_text, $sort_types, $sort),
			'S_MODE_ACTION' => append_sid("{$phpbb_root_path}garage.$phpEx", "mode=search_insurance"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'view_vehicle':

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
			'body'   => 'garage_view_vehicle.html')
		);

		//Display Vehicle With Owner Set to 'NO'
		$garage_vehicle->display_vehicle('NO');

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		//Update View Count For Vehicle
		$garage->update_view_count(GARAGE_TABLE, 'views', 'id', $cid);

		break;

	case 'view_modification':

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
			'body'   => 'garage_view_modification.html')
		);

		//Pull Required Modification Data From DB
		$data = $garage_modification->get_modification($mid);

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
			'U_VIEW_GARAGE_BUSINESS'=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_garage_business&amp;business_id=" . $data['installer_id']),
			'U_VIEW_SHOP_BUSINESS' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_shop_business&amp;business_id=" . $data['shop_id']),
			'U_MODIFICATION_IMAGE' 	=> (($data['attach_id']) AND ($data['attach_is_image']) AND (!empty($data['attach_thumb_location'])) AND (!empty($data['attach_location']))) ?  append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_gallery_item&amp;type=garage_mod&amp;image_id=" . $data['attach_id']) : '' ,
            		'MODIFICATION_IMAGE' 	=> $phpbb_root_path . GARAGE_UPLOAD_PATH . $data['attach_thumb_location'] ,
            		'MODIFICATION_IMAGE_TITLE'=> $data['attach_file'],
			'YEAR' 			=> $data['made_year'],
			'MAKE' 			=> $data['make'],
			'MODEL' 		=> $data['model'],
            		'PRODUCT_RATING' 	=> $data['product_rating'],
            		'INSTALL_RATING' 	=> $data['install_rating'],
            		'BUSINESS_NAME' 	=> $data['business_title'],
			'BUSINESS' 		=> $data['install_business_title'],
			'USERNAME' 		=> $data['username'],
            		'AVATAR_IMG' 		=> $data['avatar'],
            		'DATE_UPDATED' 		=> $user->format_date($data['date_updated']),
            		'TITLE' 		=> $data['title'],
            		'PRICE' 		=> $data['price'],
            		'INSTALL_PRICE' 	=> $data['install_price'],
            		'INSTALL_COMMENTS' 	=> $data['install_comments'],
            		'CURRENCY' 		=> $data['currency'],
            		'CATEGORY' 		=> $data['category_title'],
            		'COMMENTS' 		=> $data['comments'])
         	);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'view_own_vehicle':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_view_vehicle.html')
		);

		//Display Vehicle With Owner Set to 'YES'
		$garage_vehicle->display_vehicle('YES');

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'moderate_vehicle':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('m_garage'))
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
			'body'   => 'garage_view_vehicle.html')
		);

		//Display Vehicle With Submode Set To 'MODERATE'
		$garage_vehicle->display_vehicle('MODERATE');

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'set_main':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Update All Vehicles They Own To Not Main Vehicle
		$garage->update_single_field(GARAGE_TABLE, 'main_vehicle', 0 ,'user_id', $garage_vehicle->get_vehicle_owner_id($cid));

		//Now We Update This Vehicle To The Main Vehicle
		$garage->update_single_field(GARAGE_TABLE, 'main_vehicle', 1, 'id', $cid);

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

	case 'insert_gallery_image':

		//Let Check The User Is Allowed Perform This Action
		if ((!$auth->acl_get('u_garage_upload_image')) OR (!$auth->acl_get('u_garage_remote_image')))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=16"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Pull Vehicle Data So We Can Check For Hilite Image
		$data = $garage_vehicle->get_vehicle($cid);

		//If Any Image Variables Set Enter The Image Handling
		if ($garage_image->image_attached())
		{
			//Check For Remote & Local Image Quotas
			if ($garage_image->below_image_quotas())
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('vehicle', $cid);
				$garage_image->insert_gallery_image($image_id);
				//If First Image And Set As Vehicle Hilite Image
				if (empty($data['image_id']))
				{
					$garage->update_single_field(GARAGE_TABLE, 'image_id', $image_id, 'id', $cid);
				}
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ($garage_image->above_image_quotas())
			{
				redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=4"));
			}
		}

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=manage_vehicle_gallery&amp;CID=$cid"));

		break;

	case 'view_gallery_item':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Increment View Counter For This Image
		$garage->update_view_count(GARAGE_IMAGES_TABLE, 'attach_hits', 'attach_id', $image_id);

		//Pull Required Image Data From DB
		$data = $garage_image->get_image($image_id);

		//Check To See If This Is A Remote Image
		if (preg_match( "/^http:\/\//i", $data['attach_location']))
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
					trigger_error('UNSUPPORTED_FILE_TYPE');
			}
			readfile($phpbb_root_path . GARAGE_UPLOAD_PATH . $data['attach_location']);
        		exit;
		}

		break;

	case 'view_all_images':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Set Required Values To Defaults If They Are Empty
		$start = (empty($start)) ? '0' : $start;

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_view_images.tpl')
		);

		//Pull Required Image Data From DB
		$data = $garage_image->get_all_images($start, '100');

		//Process Each Image
		for ($i = 0, $count = sizeof($data); $i < $count; $i++)
		{
			//Produce Actual Image Thumbnail And Link It To Full Size Version..
			if (($data[$i]['attach_id']) AND ($data[$i]['attach_is_image']) AND (!empty($data[$i]['attach_thumb_location'])) AND (!empty($data[$i]['attach_location'])))
			{
				$template->assign_block_vars('pic_row', array(
					'U_VIEW_PROFILE'=> append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;" . POST_USERS_URL . "=" .$data[$i]['user_id']),
					'U_VIEW_VEHICLE'=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;CID=" .$data[$i]['garage_id']),
					'U_IMAGE'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_gallery_item&amp;type=garage_mod&amp;image_id=" . $data[$i]['attach_id']),
					'IMAGE_TITLE'	=> $data[$i]['attach_file'],
					'IMAGE'		=> $phpbb_root_path . GARAGE_UPLOAD_PATH . $data[$i]['attach_thumb_location'],
					'VEHICLE' 	=> $data[$i]['vehicle'],
					'USERNAME' 	=> $data[$i]['username'])
				);
			}
			//Cleanup For Next Image
			$thumb_image = '';
			$image = '';
		}

		//Count Total Returned For Pagination...Notice No $start or $end to get complete count
		$count = sizeof($garage_image->get_all_images());

		//Only Display Pagination If Data Exists
		if ($count >= 1)
		{
			$pagination = generate_pagination("garage.$phpEx?mode=view_all_images", $count, 100, $start);
			$template->assign_vars(array(
				'L_GOTO_PAGE'	=> $user->lang['Goto_page'],
				'PAGINATION' 	=> $pagination,
				'PAGE_NUMBER' 	=> sprintf($user->lang['PAGE_OF'], ( floor( $start / 100 ) + 1 ), ceil( $count / 100 )))
			);
		}

		$template->assign_vars(array(
			'S_MODE_ACTION' => append_sid("garage.$phpEx?mode=view_all_images"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();		

		break;

	case 'manage_vehicle_gallery':

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
			'body'   => 'garage_manage_vehicle_gallery.html')
		);

		//Pre Build All Side Menus
		$garage_template->attach_image('vehicle');

		//Pull Vehicle Data From DB So We Can Check For Hilite Image
		$vehicle_data = $garage_vehicle->get_vehicle($cid);

		//Pull Vehicle Gallery Data From DB
		$data = $garage_image->get_gallery($cid);

		//Process Each Image From Vehicle Gallery
		for ($i = 0, $count = sizeof($data);$i < $count; $i++)
		{
			$template->assign_block_vars('pic_row', array(
				'U_IMAGE'	=> (($data[$i]['image_id']) AND ($data[$i]['attach_is_image']) AND (!empty($data[$i]['attach_thumb_location'])) AND (!empty($data[$i]['attach_location']))) ? append_sid("{$phpbb_root_path}garage. $phpEx", "?mode=view_gallery_item&amp;type=garage_mod&amp;image_id=" . $data[$i]['image_id']) : '',
				'U_REMOVE_IMAGE'=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=remove_gallery_item&amp;&amp;CID=$cid&amp;image_id=" . $data[$i]['image_id']),
				'U_SET_HILITE'	=> ($data[$i]['image_id'] != $vehicle_data['image_id']) ? append_sid("{$phpbb_root_path}garage.$phpEx", "mode=set_hilite&amp;image_id=" . $data[$i]['image_id'] . "&amp;CID=$cid") : '',
				'IMAGE' 	=> $phpbb_root_path . GARAGE_UPLOAD_PATH . $data[$i]['attach_thumb_location'],
				'IMAGE_TITLE' 	=> $data[$i]['attach_file'])
			);
		}

		$template->assign_vars(array(
			'CID' => $cid)
         	);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();		

		break;

	case 'set_hilite':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Set Image As Hilite Image For Vehicle
		$garage->update_single_field(GARAGE_TABLE, 'image_id', $image_id, 'id', $cid);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_own_vehicle&amp;CID=$cid"));

		break;

	case 'remove_gallery_item':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($cid);

		//Remove Image From Vehicle Gallery & Deletes Image
		$garage_image->delete_gallery_image($image_id);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($cid);

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=manage_vehicle_gallery&amp;CID=$cid"));

		break;

	case 'view_insurance_business':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_view_insurance_business.html')
		);

		//Get All Data Posted And Make It Safe To Use
		$params		= array('business_id' => '', 'start' => 0);
		$data 		= $garage->process_vars($params);
		$data['where']	= (!empty($data['business_id'])) ? "AND b.id = " . $data['business_id'] : '';

		//Get All Insurance Business Data
		$business = $garage_business->get_insurance_business($data['where'], $data['start']);

		//If No Business Error Nicely Rather Than Display Nothing To The User
		if (sizeof($business) < 1)
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=1"));
		}

		//Build Page Header ;)
		page_header($page_title);

		//Display Correct Breadcrumb Links..
		$template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_insurance_business"),
			'FORUM_NAME' 	=> $user->lang['INSURANCE_SUMMARY'])
		);

		//Display Correct Breadcrumb Links..
		if (!empty($data['business_id']))
		{
			$template->assign_block_vars('navlinks', array(
				'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_insurance_business&amp;business_id=" . $business[0]['id']),
				'FORUM_NAME' 	=> $business[0]['title'])
			);
		}

      		//Loop Processing All Insurance Business's Returned From First Select
		for ($i = 0, $count = sizeof($business);$i < $count; $i++)
      		{
         		$template->assign_block_vars('business_row', array(
            			'U_VIEW_BUSINESS'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_insurance_business&amp;business_id=" . $business[$i]['id']),
            			'TITLE' 		=> $business[$i]['title'],
	            		'ADDRESS' 		=> $business[$i]['address'],
        	    		'TELEPHONE' 		=> $business[$i]['telephone'],
            			'FAX' 			=> $business[$i]['fax'],
            			'WEBSITE' 		=> $business[$i]['website'],
	            		'EMAIL' 		=> $business[$i]['email'],
				'OPENING_HOURS' 	=> $business[$i]['opening_hours'])
			);

			//Setup Template Block For Detail Being Displayed...
			$detail = (empty($data['business_id'])) ? 'business_row.more_detail' : 'business_row.insurance_detail';
        	 	$template->assign_block_vars($detail, array());

			//Now Loop Through All Insurance Cover Types...
			$cover_types = array($user->lang['THIRD_PARTY'], $user->lang['THIRD_PARTY_FIRE_THEFT'], $user->lang['COMPREHENSIVE'], $user->lang['COMPREHENSIVE_CLASSIC'], $user->lang['COMPREHENSIVE_REDUCED']);
			for($j = 0, $count2 = sizeof($cover_types);$j < $count2; $j++)
			{
				//Pull MIN/MAX/AVG Of Specific Cover Type By Business ID
				$premium_data = $garage_insurance->get_premiums_stats_by_business_and_covertype($business[$i]['id'], $cover_types[$j]);
        	    		$template->assign_block_vars('business_row.cover_row', array(
               				'COVER_TYPE'	=> $cover_types[$j],
               				'MINIMUM' 	=> $premium_data['min'],
               				'AVERAGE' 	=> $premium_data['avg'],
               				'MAXIMUM' 	=> $premium_data['max'])
	            		);
			}
			
			//If Display Single Insurance Company We Then Need To Get All Premium Data
			if  (!empty($data['business_id']))
			{
				//Pull All Insurance Premiums Data For Specific Insurance Company
				$insurance_data = $garage_insurance->get_all_premiums_by_business($business[$i]['id']);
				for($k = 0, $count3 = sizeof($insurance_data);$k < $count3; $k++)
				{
					$template->assign_block_vars('business_row.insurance_detail.premiums', array(
						'U_VIEW_PROFILE'=> append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=" . $insurance_data[$k]['user_id']),
						'U_VIEW_VEHICLE'=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;CID=" . $insurance_data[$k]['garage_id']),
						'USERNAME'	=> $insurance_data[$k]['username'],
						'VEHICLE' 	=> $insurance_data[$k]['vehicle'],
						'PREMIUM' 	=> $insurance_data[$k]['premium'],
						'COVER_TYPE' 	=> $insurance_data[$k]['cover_type'])
					);
				}
			}
      		}

		// Get Insurance Business Data For Pagination
		$count = $garage_business->get_insurance_business($where);
		$pagination = generate_pagination("garage.$phpEx?mode=view_insurance_business", $count[0]['total'], 25, $start);

		$template->assign_vars(array(
			'PAGINATION' => $pagination,
			'PAGE_NUMBER' => sprintf($user->lang['PAGE_OF'], (floor( $start / 25) + 1), ceil($count[0]['total'] / 25 )))
            	);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'view_garage_business':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header'=> 'garage_header.html',
			'body' 	=> 'garage_view_garage_business.html')
		);

		//Get All Data Posted And Make It Safe To Use
		$params = array('business_id' => '', 'start' => 0);
		$data = $garage->process_vars($params);
		$data['where'] = (!empty($data['business_id'])) ? "AND b.id = " . $data['business_id'] : '';

		//Get Required Garage Business Data
		$business = $garage_business->get_garage_business($data['where'], $data['start']);

		//If No Business Let The User Know..
		if ( sizeof($business) < 1 )
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=1"));
		}

		//Build Page Header ;)
		page_header($page_title);

		//Display Correct Breadcrumb Links..
		$template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_garage_business"),
			'FORUM_NAME' 	=> $user->lang['GARAGE_REVIEW'])
		);

		//Setup Breadcrumb Trail Correctly...
		if (!empty($data['business_id']))
		{
			$template->assign_block_vars('navlinks', array(
				'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_garage_business&amp;business_id=" . $business[0]['id']),
				'FORUM_NAME' 	=> $business[0]['title'])
			);
		}

      		//Process All Garages......
      		for ($i = 0, $count = sizeof($business);$i < $count; $i++)
      		{
         		$template->assign_block_vars('business_row', array(
				'U_VIEW_BUSINESS'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_garage_business&amp;business_id=" . $business[$i]['id']),
				'RATING' 		=> (empty($business[$i]['rating'])) ? 0 : $business[$i]['rating'],
            			'TITLE' 		=> $business[$i]['title'],
            			'ADDRESS' 		=> $business[$i]['address'],
            			'TELEPHONE' 		=> $business[$i]['telephone'],
            			'FAX' 			=> $business[$i]['fax'],
            			'WEBSITE' 		=> $business[$i]['website'],
            			'EMAIL' 		=> $business[$i]['email'],
				'MAX_RATING' 		=> $business[$i]['total_rating'],
				'OPENING_HOURS' 	=> $business[$i]['opening_hours'])
         		);
			$template->assign_block_vars('business_row.customers', array());

			if (empty($data['business_id']))
			{
         			$template->assign_block_vars('business_row.more_detail', array());
			}

			//Now Lets Go Get Mods Business Has Installed
			$bus_mod_data = $garage_modification->get_modifications_by_install_business($business[$i]['id']);

			$comments = null;
			for($j = 0, $count2 = sizeof($bus_mod_data);$j < $count2; $j++)
			{
				$template->assign_block_vars('business_row.mod_row', array(
					'U_VIEW_PROFILE' 	=> append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=" . $bus_mod_data[$j]['user_id']),
					'U_VIEW_VEHICLE' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;CID=" . $bus_mod_data[$j]['garage_id']),
					'U_VIEW_MODIFICATION'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_modification&amp;CID=" . $bus_mod_data[$j]['garage_id'] . "&amp;MID=" . $bus_mod_data[$j]['id']),
					'USERNAME' 		=> $bus_mod_data[$j]['username'],
					'VEHICLE' 		=> $bus_mod_data[$j]['vehicle'],
					'MODIFICATION' 		=> $bus_mod_data[$j]['mod_title'],
					'INSTALL_RATING' 	=> $bus_mod_data[$j]['install_rating'])
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
		$count = $garage_business->count_garage_business_data($data['where']);
		$pagination = generate_pagination("garage.$phpEx?mode=view_garage_business", $count, 25, $start);

		$template->assign_vars(array(
			'PAGINATION'	=> $pagination,
			'PAGE_NUMBER'	=> sprintf($user->lang['PAGE_OF'], (floor($start / 25) + 1), ceil($count / 25)))
            	);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'view_shop_business':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header'=> 'garage_header.html',
			'body' 	=> 'garage_view_shop_business.html')
		);


		//Get All Data Posted And Make It Safe To Use
		$params		= array('business_id' => '', 'start' => 0);
		$data 		= $garage->process_vars($params);
		$data['where']	= (!empty($data['business_id'])) ? "AND b.id = " . $data['business_id'] : '';

		//Get Required Shop Business Data
		$business = $garage_business->get_shop_business($data['where'], $data['start']);

		//If No Business Let The User Know..
		if ( sizeof($business) < 1 )
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=1"));
		}

		//Build Page Header ;)
		page_header($page_title);

		//Display Correct Breadcrumb Links..
		$template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_shop_business"),
			'FORUM_NAME' 	=> $user->lang['SHOP_REVIEW'])
		);

		//Display Correct Breadcrumb Links..
		if (!empty($data['business_id']))
		{
			$template->assign_block_vars('navlinks', array());
			$template->assign_vars(array(
				'U_VIEW_FORUM' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_shop_business&amp;business_id=" . $business[0]['id']),
				'FORUM_NAME'	=> $business[0]['title'])
			);
		}

      		//Process All Shops......
      		for ($i = 0, $count = sizeof($business);$i < $count; $i++)
      		{
         		$template->assign_block_vars('business_row', array(
				'U_VIEW_BUSINESS'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_shop_business&amp;business_id=" . $business[$i]['id']),
				'RATING' 		=> (empty($business[$i]['rating'])) ? 0 : $business[$i]['rating'],
            			'TITLE' 		=> $business[$i]['title'],
            			'ADDRESS' 		=> $business[$i]['address'],
            			'TELEPHONE' 		=> $business[$i]['telephone'],
            			'FAX' 			=> $business[$i]['fax'],
            			'WEBSITE' 		=> $business[$i]['website'],
            			'EMAIL' 		=> $business[$i]['email'],
				'MAX_RATING' 		=> $business[$i]['total_rating'],
				'OPENING_HOURS' 	=> $business[$i]['opening_hours'])
         		);
			$template->assign_block_vars('business_row.customers', array());
			
			if (empty($data['business_id']))
			{
         			$template->assign_block_vars('business_row.more_detail', array());
			}

			//Now Lets Go Get All Mods All Business's Have Sold
			$bus_mod_data = $garage_modification->get_modifications_by_business($business[$i]['id']);

			$comments = null;
			for ($j = 0, $count2 = sizeof($bus_mod_data);$j < $count2; $j++)
			{
				$template->assign_block_vars('business_row.mod_row', array(
					'U_VIEW_PROFILE' 	=> append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=" . $bus_mod_data[$j]['user_id']),
					'U_VIEW_VEHICLE' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;CID=" . $bus_mod_data[$j]['garage_id']),
					'U_VIEW_MODIFICATION'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_modification&amp;CID=" . $bus_mod_data[$j]['garage_id'] . "&amp;MID=" . $bus_mod_data[$j]['id']),
					'USERNAME' 		=> $bus_mod_data[$j]['username'],
					'VEHICLE' 		=> $bus_mod_data[$j]['vehicle'],
					'MODIFICATION' 		=> $bus_mod_data[$j]['mod_title'],
					'PURCHASE_RATING' 	=> $bus_mod_data[$j]['purchase_rating'],
					'PRODUCT_RATING' 	=> $bus_mod_data[$j]['product_rating'],
					'PRICE' 		=> $bus_mod_data[$j]['price'])
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
		$count = $garage_business->count_shop_business_data($data['where']);
		$pagination = generate_pagination("garage.$phpEx?mode=view_shop_business", $count, 25, $start);

		$template->assign_vars(array(
			'PAGINATION'	=> $pagination,
			'PAGE_NUMBER' 	=> sprintf($user->lang['PAGE_OF'], (floor($start / 25) + 1), ceil($count / 25)))
            	);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'user_submit_business':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ( $user->data['user_id'] == ANONYMOUS )
		{
			login_box("garage.$phpEx?mode=user_submit_business");
		}

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_business'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_user_submit_business.html')
		);

		//Get All Data Posted And Make It Safe To Use
		$params = array('BUSINESS' => '', 'redirect' => '');
		$data = $garage->process_vars($params);

		$template->assign_vars(array(
			'L_TITLE'		=> $user->lang['ADD_NEW_BUSINESS'],
			'L_BUTTON'		=> $user->lang['ADD_NEW_BUSINESS'],
			'CID' 			=> $cid,
			'REDIRECT'		=> $data['redirect'],
			'BUSINESS_INSURANCE' 	=> BUSINESS_INSURANCE,
			'BUSINESS_GARAGE' 	=> BUSINESS_GARAGE,
			'BUSINESS_RETAIL' 	=> BUSINESS_RETAIL,
			'BUSINESS_PRODUCT' 	=> BUSINESS_PRODUCT,
			'S_DISPLAY_PENDING' 	=> $garage_config['enable_business_approval'],
			'S_BUSINESS_INSURANCE' 	=> ($data['BUSINESS'] == BUSINESS_INSURANCE) ? true : false,
			'S_BUSINESS_GARAGE' 	=> ($data['BUSINESS'] == BUSINESS_GARAGE) ? true : false,
			'S_BUSINESS_RETAIL' 	=> ($data['BUSINESS'] == BUSINESS_RETAIL) ? true : false,
			'S_BUSINESS_PRODUCT' 	=> ($data['BUSINESS'] == BUSINESS_PRODUCT) ? true : false,
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=insert_business"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'insert_business':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_business'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('redirect' => '', 'title' => '', 'address' => '', 'telephone' => '', 'fax' => '', 'website' => '', 'email' => '', 'opening_hours' => '', 'type' => array(0));
		$data 	= $garage->process_vars($params);

		//Check They Entered http:// In The Front Of The Link
		if ((!preg_match( "/^http:\/\//i", $data['website'])) AND (!empty($data['website'])))
		{
			$data['website'] = "http://".$data['website'];
		}

		//Checks All Required Data Is Present
		$params = array('title');
		$garage->check_required_vars($params);

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ($garage_config['enable_business_approval'])
		{
			//Perform Any Pending Notifications Requried
			$garage->pending_notification('unapproved_business');
		}

		//Create The Business Now...
		$garage_business->insert_business($data);

		//Send Them Back To Whatever Page Them Came From..Now With Their Required Business :)
		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=" . $data['redirect'] . "&amp;CID=$cid"));

		break;

	case 'edit_business':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('m_garage'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_user_submit_business.html')
		);

		//Pull Required Business Data From DB
		$data = $garage_business->get_business($bus_id);

		$template->assign_vars(array(
			'L_TITLE' 		=> $user->lang['EDIT_BUSINESS'],
			'L_BUTTON' 		=> $user->lang['EDIT_BUSINESS'],
			'TITLE' 		=> $data['title'],
			'ADDRESS'		=> $data['address'],
			'TELEPHONE'		=> $data['telephone'],
			'FAX'			=> $data['fax'],
			'WEBSITE'		=> $data['website'],
			'EMAIL'			=> $data['email'],
			'OPENING_HOURS'		=> $data['opening_hours'],
			'BUSINESS_ID'		=> $data['id'],
			'BUSINESS_INSURANCE' 	=> BUSINESS_INSURANCE,
			'BUSINESS_GARAGE' 	=> BUSINESS_GARAGE,
			'BUSINESS_RETAIL' 	=> BUSINESS_RETAIL,
			'BUSINESS_PRODUCT' 	=> BUSINESS_PRODUCT,
			'S_DISPLAY_PENDING' 	=> $garage_config['enable_business_approval'],
			'S_BUSINESS_INSURANCE' 	=> (in_array(BUSINESS_INSURANCE, explode(",", $data[$i]['type']))) ? true : false,
			'S_BUSINESS_GARAGE' 	=> (in_array(BUSINESS_GARAGE, explode(",", $data[$i]['type']))) ? true : false,
			'S_BUSINESS_RETAIL' 	=> (in_array(BUSINESS_RETAIL, explode(",", $data[$i]['type']))) ? true : false,
			'S_BUSINESS_PRODUCT' 	=> (in_array(BUSINESS_PRODUCT, explode(",", $data[$i]['type']))) ? true : false,
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=update_business"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'update_business':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('m_garage'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('id' => '', 'title' => '', 'address' => '', 'telephone' => '', 'fax' => '', 'website' => '', 'email' => '', 'opening_hours' => '', 'type' => array(0));
		$data 	= $garage->process_vars($params);

		//Check They Entered http:// In The Front Of The Link
		if ( (!preg_match( "/^http:\/\//i", $data['website'])) AND (!empty($data['website'])) )
		{
			$data['website'] = "http://" . $data['website'];
		}

		//Checks All Required Data Is Present
		$params = array('title');
		$garage->check_required_vars($params);

		//Update The Business With Data Acquired
		$garage_business->update_business($data);

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_business"));

		break;

	case 'user_submit_make':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=user_submit_make");
		}

		//Check This Feature Is Enabled
		if (!$garage_config['enable_user_submit_make'])
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_make_model'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('year' => '');
		$data = $garage->process_vars($params);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_user_submit_make.html')
		);

		$template->assign_vars(array(
			'YEAR' 			 => $data['year'],
			'S_GARAGE_MODELS_ACTION' => append_sid("{$phpbb_root_path}admin_garage_models.$phpEx"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'insert_make':

		//User Is Annoymous...So Not Allowed To Create A Vehicle
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=user_submit_make");
		}

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_make_model'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('make' => '', 'year' => '');
		$data = $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('make', 'year');
		$garage->check_required_vars($params);

		//Check Make Does Not Already Exist
		if ($garage_model->count_make($data['make']) > 0)
		{
			redirect(append_sid("garage.$phpEx?mode=error&amp;EID=27", true));
		}

		//Create The Make
		$garage_model->insert_make($data);

		//Perform Any Pending Notifications Requried
		$garage->pending_notification('unapproved_makes');

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=create_vehicle&amp;MAKE=" . $data['make']));

		break;

	case 'user_submit_model':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ( $user->data['user_id'] == ANONYMOUS )
		{
			login_box("garage.$phpEx?mode=user_submit_model");
		}

		//Check This Feature Is Enabled & User Authorised
		if (!$garage_config['enable_user_submit_model'] || !$auth->acl_get('u_garage_add_make_model'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('make_id' => '', 'year' => '');
		$data = $garage->process_vars($params);
		$year = $data['year'];

		//Check If User Owns Vehicle
		if (empty($data['make_id']))
		{
			redirect(append_sid("garage.$phpEx?mode=error&amp;EID=23", true));
		}

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_user_submit_model.html')
		);

		//Get All Data Posted And Make It Safe To Use
		$params = array('MAKE_ID' => '');
		$data = $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('MAKE_ID');
		$garage->check_required_vars($params);

		//Pull Required Make Data From DB
		$data = $garage_model->get_make($data['make_id']);

		$template->assign_vars(array(
			'YEAR' 		=> $year,
			'MAKE_ID' 	=> $data['id'],
			'MAKE' 		=> $data['make'])
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'user_submit_product':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ( $user->data['user_id'] == ANONYMOUS )
		{
			login_box("garage.$phpEx?mode=user_submit_product");
		}

		//Check This Feature Is Enabled & User Authorised
		if (!$garage_config['enable_user_submit_product'] || !$auth->acl_get('u_garage_add_product'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('category_id' => '', 'manufacturer_id' => '', 'CID' => '');
		$data = $garage->process_vars($params);
		$params = array('category_id', 'manufacturer_id', 'CID');
		$garage->check_required_vars($params);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_user_submit_product.html')
		);

		$category = $garage->get_category($data['category_id']);
		$manufacturer = $garage_business->get_business($data['manufacturer_id']);

		$template->assign_vars(array(
			'CID' 			=> $data['CID'],
			'CATEGORY_ID' 		=> $data['category_id'],
			'MANUFACTURER_ID'	=> $data['manufacturer_id'],
			'CATEGORY' 		=> $category['title'],
			'MANUFACTURER' 		=> $manufacturer['title'])
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'insert_model':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=user_submit_model");
		}

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_make_model'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('make' => '', 'make_id' => '', 'model' => '', 'year' => '');
		$data = $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('make', 'make_id', 'model');
		$garage->check_required_vars($params);

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ( $data['pending'] == 1 )
		{
			$garage->pending_notification('unapproved_models');
		}

		//Create The Model
		$garage_model->insert_model($data);

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=create_vehicle&amp;MAKE=" . $data['make'] . "&amp;MODEL=" . $data['model']));

		break;

	case 'insert_product':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=user_submit_product");
		}

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_product'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('title' => '', 'category_id' => '', 'manufacturer_id' => '', 'vehicle_id' => '');
		$data = $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('title', 'category_id', 'manufacturer_id', 'vehicle_id');
		$garage->check_required_vars($params);

		//If Needed Perform Notifications If Configured
		if ($garage_config['enable_product_approval'])
		{
			$garage->pending_notification('unapproved_products');
		}

		//Create The Product
		$data['product_id'] = $garage_modification->insert_product($data);

		//Head Back To Page Updating Dropdowns With New Item ;)
		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=add_modification&amp;CID=".$data['vehicle_id']."&amp;category_id=" . $data['category_id'] . "&amp;manufacturer_id=" . $data['manufacturer_id'] ."&amp;product_id=" . $data['product_id']));

		break;

	case 'quartermile':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Build Page Header ;)
		$page_title = $user->lang['QUART'];
		page_header($page_title);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['QUARTERMILE'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=quartermile"))
		);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'body' 		=> 'garage_quartermile_table.html')
		);

		//Build Actual Table With No Pending Runs
		$garage_quartermile->build_quartermile_table();

		//Build Required HTML, Javascript And Arrays
		$template->assign_vars(array(
			'S_MODE_ACTION'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=quartermile"))
		);

		break;

	case 'reassign_business':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ( $user->data['user_id'] == ANONYMOUS )
		{
			login_box("garage.$phpEx?mode=pending");
		}

		//Check The User Is Allowed To View This Page...If Not Send Them On There Way Nicely
		if (!$auth->acl_get('m_garage'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=13"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('business_id' => '', 'target_id' => '');
		$data = $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('business_id', 'target_id');
		$garage->check_required_vars($params);

		//Lets Update All Possible Business Fields With The Reassigned Business
		$garage->update_single_field(GARAGE_MODS_TABLE, 'business_id', $data['target_id'], 'business_id', $data['business_id']);
		$garage->update_single_field(GARAGE_MODS_TABLE, 'install_business_id', $data['target_id'], 'install_business_id', $data['business_id']);
		$garage->update_single_field(GARAGE_INSURANCE_TABLE, 'business_id', $data['target_id'], 'business_id', $data['business_id']);

		//Since We Have Updated All Item Lets Do The Original Delete Now
		$garage->delete_rows(GARAGE_BUSINESS_TABLE, 'id', $data['business_id']);

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=pending"));

		break;

	case 'dynorun':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'body' 		=> 'garage_dynorun_table.html')
		);


		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['DYNORUN'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=dynorun"))
		);

		//Build Dynorun Table With No Pending Runs
		$garage_dynorun->build_dynorun_table();

		//Build All Required HTML, Javascript And Arrays
		$template->assign_vars(array(
			'S_MODE_ACTION'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=dynorun"))
		);

		break;

	case 'view_guestbook':

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
			'body'   => 'garage_view_guestbook.html')
		);

		//Get Vehicle Data
		$vehicle_data = $garage_vehicle->get_vehicle($cid);

		//Get All Comments Data
		$comment_data = $garage_guestbook->get_vehicle_comments($cid);

		for ($i = 0, $count = sizeof($comment_data);$i < $count; $i++)
		{	
			$username = $comment_data[$i]['username'];
			$temp_url = append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=" . $comment_data[$i]['user_id']);
			$poster = '<a href="' . $temp_url . '">' . $comment_data[$i]['username'] . '</a>';
			$poster_posts = ( $comment_data[$i]['user_id'] != ANONYMOUS ) ? $lang['Posts'] . ': ' . $comment_data[$i]['user_posts'] : '';
			$poster_from = ( $comment_data[$i]['user_from'] && $comment_data['user_id'] != ANONYMOUS ) ? $lang['Location'] . ': ' . $comment_data[$i]['user_from'] : '';
			$garage_id = $comment_data['garage_id'];
			$poster_car_year = ( $comment_data[$i]['made_year'] && $comment_data[$i]['user_id'] != ANONYMOUS ) ? $lang[''] . ' ' . $comment_data[$i]['made_year'] : '';
			$poster_car_mark = ( $comment_data[$i]['make'] && $comment_data[$i]['user_id'] != ANONYMOUS ) ? $lang[''] . ' ' . $comment_data[$i]['make'] : '';
			$poster_car_model = ( $comment_data[$i]['model'] && $comment_data[$i]['user_id'] != ANONYMOUS ) ? $lang[''] . ' ' . $comment_data[$i]['model'] : '';
			$poster_joined = ( $comment_data[$i]['user_id'] != ANONYMOUS ) ? $lang['Joined'] . ': ' . $user->format_date($comment_data[$i]['user_regdate']) : '';

			$data['avatar'] = '';
			if ( $data['user_avatar'] AND $user->optionget('viewavatars') )
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

			// Handle anon users posting with usernames
			if ( $comment_data[$i]['user_id'] == ANONYMOUS && $comment_data[$i]['post_username'] != '' )
			{
				$poster = $comment_data[$i]['post_username'];
			}

			$profile_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_profile'] . '" alt="' . $lang['Read_profile'] . '" title="' . $lang['Read_profile'] . '" border="0" /></a>';
			$profile = '<a href="' . $temp_url . '">' . $lang['Read_profile'] . '</a>';

			$temp_url = append_sid("{$phpbb_root_path}privmsg.$phpEx", "mode=post&amp;u=".$comment_data[$i]['user_id']);
			$pm_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_pm'] . '" alt="' . $lang['Send_private_message'] . '" title="' . $lang['Send_private_message'] . '" border="0" /></a>';
			$pm = '<a href="' . $temp_url . '">' . $lang['Send_private_message'] . '</a>';

			if ( !empty($comment_data[$i]['user_viewemail']) || $is_auth['auth_mod'] )
			{
				$email_uri = ( $board_config['board_email_form'] ) ? append_sid("{$phpbb_root_path}profile.$phpEx", "mode=email&amp;u=" . $comment_data[$i]['user_id']) : 'mailto:' . $comment_data['user_email'];

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

			$posted = '<a href="' . append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=".$comment_data[$i]['user_id']) . '">' . $comment_data[$i]['username'] . '</a>';
			$posted = $user->format_date($comment_data[$i]['post_date']);

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

			$edit_img = '';
			$edit = '';
			$delpost_img = '';
			$delpost = '';

		 	if ( $auth->acl_get('m_garage') )
			{
				$edit_img = '<a href="'. append_sid("{$phpbb_root_path}garage.$phpEx", "mode=edit_comment&amp;CID=$cid&amp;comment_id=" . $comment_data[$i]['comment_id'] . "&amp;sid=" . $user->data['session_id']) . '"><img src="' . $images['icon_edit'] . '" alt="' . $lang['Edit_delete_post'] . '" title="' . $lang['Edit_delete_post'] . '" border="0" /></a>';
				$edit = '<a href="'. append_sid("{$phpbb_root_path}garage.$phpEx", "mode=edit_comment&amp;CID=$cid&amp;comment_id=" . $comment_data[$i]['comment_id'] . "&amp;sid=" . $user->data['session_id']) . '">' . $lang['Edit_delete_post'] . '</a>';
				$delpost_img = '<a href="'. append_sid("{$phpbb_root_path}garage.$phpEx", "mode=delete_comment&amp;CID=$cid&amp;comment_id=" . $comment_data[$i]['comment_id'] . "&amp;sid=" . $user->data['session_id']) . '"><img src="' . $images['icon_delpost'] . '" alt="' . $lang['Delete_post'] . '" title="' . $lang['Delete_post'] . '" border="0" /></a>';
				$delpost = '<a href="'. append_sid("{$phpbb_root_path}garage.$phpEx", "mode=delete_comment&amp;CID=$cid&amp;comment_id=" . $comment_data[$i]['comment_id'] . "&amp;sid=" . $user->data['session_id']) . '">' . $lang['Delete_post'] . '</a>';

			}

			$template->assign_block_vars('comments', array(
				'POSTER_NAME' 		=> $poster,
				'POSTER_JOINED' 	=> $poster_joined,
				'POSTER_POSTS' 		=> $poster_posts,
				'POSTER_FROM' 		=> $poster_from,
				'POSTER_CAR_MARK' 	=> $poster_car_mark,
				'POSTER_CAR_MODEL' 	=> $poster_car_model,
				'POSTER_CAR_YEAR' 	=> $poster_car_year,
				'VIEW_POSTER_CARPROFILE'=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;CID=$garage_id"),
				'POSTER_AVATAR' 	=> $poster_avatar,
				'PROFILE_IMG' 		=> $profile_img,
				'PROFILE' 		=> $profile,
				'PM_IMG' 		=> $pm_img,
				'PM'			=> $pm,
				'EMAIL_IMG'		=> $email_img,
				'EMAIL'			=> $email,
				'WWW_IMG'		=> $www_img,
				'WWW'			=> $www,
				'EDIT_IMG'		=> $edit_img,
				'EDIT' 			=> $edit,
				'DELETE_IMG' 		=> $delpost_img,
				'DELETE' 		=> $delpost,
				'POSTER' 		=> $poster,
				'POSTED' 		=> $posted,
				'POST' 			=> $post)
			);
		}

		$template->assign_vars(array(
			'L_GUESTBOOK_TITLE' 	=> $vehicle_data['username'] . " - " . $vehicle_data['vehicle'] . " " . $lang['Guestbook'],
			'CID' 			=> $cid,
			'S_DISPLAY_LEAVE_COMMENT'=> $auth->acl_get('u_garage_comment'),
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=insert_comment&CID=$cid"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'insert_comment':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_comment'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=17"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('comments' => '');
		$data = $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('comments');
		$garage->check_required_vars($params);

		//Insert The Comment Into Vehicle Guestbook
		$garage_guestbook->insert_vehicle_comment($data);

		//Get Vehicle Data So We Can Check If We Need To PM Owner
		$data = $garage_vehicle->get_vehicle($cid);		

		//If User Has Requested Notification On Comments Sent Them A PM
		if ( $garage_guestbook->notify_on_comment($data['user_id']))
		{
			include_once($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
			include_once($phpbb_root_path . 'includes/message_parser.' . $phpEx);

			$data['vehicle_link'] 	= '<a href="garage.'.$phpEx.'?mode=view_guestbook&CID=$cid">' . $user->lang['HERE'] . '</a>';

			$message_parser = new parse_message();
			$message_parser->message = sprintf($user->lang['GUESTBOOK_NOTIFY_TEXT'], $data['vehicle_link']);
			$message_parser->parse(true, true, true, false, false, true, true);

			$pm_data = array(
				'from_user_id'			=> $user->data['user_id'],
				'from_user_ip'			=> $user->data['user_ip'],
				'from_username'			=> $user->data['username'],
				'enable_sig'			=> false,
				'enable_bbcode'			=> true,
				'enable_smilies'		=> true,
				'enable_urls'			=> false,
				'icon_id'			=> 0,
				'bbcode_bitfield'		=> $message_parser->bbcode_bitfield,
				'bbcode_uid'			=> $message_parser->bbcode_uid,
				'message'			=> $message_parser->message,
				'address_list'			=> array('u' => array($data['user_id'] => 'to')),
			);

			//Now We Have All Data Lets Send The PM!!
			submit_pm('post', $user->lang['GUESTBOOK_NOTIFY_SUBJECT'], $pm_data, false, false);
		}

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ( $garage_config['enable_guestbooks_comment_approval'] )
		{
			$garage->pending_notification('unapproved_guestbook_comments');
		}

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_guestbook&amp;CID=$cid"));

		break;

	case 'edit_comment':

		//Only Allow Moderators Or Administrators Perform This Action
		if (!$auth->acl_get('m_garage'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=13"));
		}

		//Pull Required Comment Data From DB
		$data = $garage_guestbook->get_comment($comment_id);	
		
		$template->assign_vars(array(
			'CID' 		 => $cid,
			'COMMENT_ID' 	 => $data['comment_id'],
			'COMMENTS' 	 => $data['post'])
		);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_edit_comment.html')
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'update_comment':

		//Only Allow Moderators Or Administrators Perform This Action
		if (!$auth->acl_get('m_garage'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=13"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('comments' => '', 'COMMENT_ID' => '');
		$data = $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('comments', 'COMMENT_ID');
		$garage->check_required_vars($params);

		//Update The Comment In The Vehicle Guestbook
		$garage->update_single_field(GARAGE_GUESTBOOKS_TABLE, 'post', $data['comments'], 'id', $data['COMMENT_ID']);

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ( $garage_config['enable_guestbooks_comment_approval'] )
		{
			$garage->pending_notification('unapproved_guestbook_comments');
		}

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_guestbook&amp;CID=$cid"));

		break;

	case 'delete_comment':

		//Only Allow Moderators Or Administrators Perform This Action
		if (!$auth->acl_get('m_garage'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=13"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('comment_id' => '');
		$data = $garage->process_vars($params);

		//Delete The Comment From The Guestbook
		$garage->delete_rows(GARAGE_GUESTBOOKS_TABLE, 'id', $data['comment_id']);

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_guestbook&amp;CID=$cid"));

		break;

	case 'error':

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_error.html')
		);

		$template->assign_vars(array(
			'ERROR_MESSAGE' => $user->lang['GARAGE_ERROR_' . $eid])
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'rate_vehicle':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_rate'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=17"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('vehicle_rating' => '');
		$data = $garage->process_vars($params);
		$data['rate_date']	= time();
		$data['user_id'] 	= $user->data['user_id'];

		//Checks All Required Data Is Present
		$params = array('vehicle_rating', 'rate_date', 'user_id');
		$garage->check_required_vars($params);

		//Pull Required Data From DB
	        $vehicle_data = $garage_vehicle->get_vehicle($cid);

		//If User Is Guest Generate Unique Number For User ID....
		srand($garage->make_seed());
		$data['user_id'] = ( $user->data['user_id'] == ANONYMOUS ) ? '-' . (rand(2,99999)) : $user->data['user_id'];

		//Check If User Owns Vehicle
		if ( $vehicle_data['user_id'] == $data['user_id'] )
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=21"));
		}

		$count = $garage_vehicle->count_user_vehicle_ratings($data['user_id']);

		//If You Have Not Rated This Vehicle..Create A Rating	
		if ( $count < 1 )
		{
			$garage_vehicle->insert_vehicle_rating($data);
		}
		//You Already Have Rated It..So Just Update The Rating	
		else
		{
			$garage_vehicle->update_vehicle_rating($data);
		}

		//Update The Weighted Rating Of This Vehicle
		$weighted_rating = $garage_vehicle->calculate_weighted_rating($cid);
		$garage_vehicle->update_weighted_rating($cid, $weighted_rating);

		redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;CID=$cid"));

		break;

	case 'delete_rating':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('m_garage'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=17"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('RTID' => '');
		$data = $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('RTID');
		$garage->check_required_vars($params);

		//Delete The Rating
		$garage->delete_rows(GARAGE_RATING_TABLE, 'id', $data['RTID']);

		//Update The Weighted Rating Of This Vehicle
		$weighted_rating = $garage_vehicle->calculate_weighted_rating($cid);
		$garage_vehicle->update_weighted_rating($cid, $weighted_rating);

		redirect(append_sid("garage.$phpEx", "mode=moderate_vehicle&amp;CID=$cid", true));

		break;

	case 'reset_vehicle_rating':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('m_garage'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=17"));
		}

		//Let Get Vehicle Rating & Delete Them
		$data = $garage_vehicle->get_vehicle_rating($cid);
		for ($i = 0, $count = sizeof($data);$i < $count; $i++)
		{
			$garage->delete_rows(GARAGE_RATING_TABLE, 'id', $data['id']);
		}

		//Update The Weighted Rating Of This Vehicle
		$weighted_rating = $garage_vehicle->calculate_weighted_rating($cid);
		$garage_vehicle->update_weighted_rating($cid, $weighted_rating);

		redirect(append_sid("garage.$phpEx", "mode=moderate_vehicle&amp;CID=$cid", true));

		break;

	case 'get_model_list':

		//Get All Data Posted And Make It Safe To Use
		$params = array('make_id' => '', 'model_id' => '');
		$data = $garage->process_vars($params);

		echo "obj.options[obj.options.length] = new Option('".$user->lang['SELECT_MODEL']."', '', false, false);\n";
		echo "obj.options[obj.options.length] = new Option('------', '', false, false);\n";

		if (!empty($data['make_id']))
		{
			//Get Models Belonging To This Make
			$models = $garage_model->get_all_models_from_make($data['make_id']);

			//Populate Options For Dropdown
			for ($i = 0, $count = sizeof($models);$i < $count; $i++)
			{
				if ($data['model_id'] == $models[$i]['id'])
				{
					echo "obj.options[obj.options.length] = new Option('".$models[$i]['model']."','".$models[$i]['id']."', true, true);\n";
				}
				else
				{
					echo "obj.options[obj.options.length] = new Option('".$models[$i]['model']."','".$models[$i]['id']."', false, false);\n";
				}
			}
		}

		exit;

	case 'get_product_list':

		//Get All Data Posted And Make It Safe To Use
		$params = array('manufacturer_id' => '' , 'category_id' => '', 'product_id' => '');
		$data = $garage->process_vars($params);

		echo "obj.options[obj.options.length] = new Option('".$user->lang['SELECT_PRODUCT']."', '', false, false);\n";
		echo "obj.options[obj.options.length] = new Option('------', '', false, false);\n";

		if (!empty($data['manufacturer_id']))
		{
			//Get Products Belonging To This Manufacturer With Filtering On Category For Modification Page
			if (!empty($data['category_id']))
				$products = $garage_modification->get_products_by_manufacturer($data['manufacturer_id'], $data['category_id']);
			//Get Products Belonging To This Manufacturer With No Filtering On Category For Search Page
			else
			{
				$products = $garage_modification->get_products_by_manufacturer($data['manufacturer_id']);
			}

			//Populate Options For Dropdown
			for ($i = 0, $count = sizeof($products);$i < $count; $i++)
			{
				if ($data['product_id'] == $products[$i]['id'])
				{
					echo "obj.options[obj.options.length] = new Option('".$products[$i]['title']."','".$products[$i]['id']."', true, true);\n";
				}
				else
				{
					echo "obj.options[obj.options.length] = new Option('".$products[$i]['title']."','".$products[$i]['id']."', false, false);\n";
				}
			}
		}

		exit;

	default:

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_browse'))
		{
			//If Not Logged In Send Them To Login & Back, Maybe They Have Permission As A User 
			if ( $user->data['user_id'] == ANONYMOUS )
			{
				login_box("garage.$phpEx");
			}
			//They Are Logged In But Not Allowed So Error Nicely Now...
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Build Page Header ;)
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
			'S_INDEX_COLUMNS' 	=> ($garage_config['enable_user_index_columns'] && ($user->data['user_garage_index_columns'] != $garage_config['index_columns'])) ? $user->data['user_garage_index_columns'] : $garage_config['index_columns'],
			'TOTAL_VEHICLES' 	=> $garage_vehicle->count_total_vehicles(),
			'TOTAL_VIEWS' 		=> $garage->count_total_views(),
			'TOTAL_MODIFICATIONS' 	=> $garage_modification->count_total_modifications(),
			'TOTAL_COMMENTS'  	=> $garage_guestbook->count_total_comments())
		);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header'	=> 'garage_header.html',
			'menu' 		=> 'garage_menu.html',
			'body' 		=> 'garage.html')
		);

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

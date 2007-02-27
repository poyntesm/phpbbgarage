<?php
/***************************************************************************
 *                              garage_modification.php
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
	//Mode To Display Add Modification Page
	case 'add_modification':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_modification.$phpEx?mode=add_modification&amp;VID=$vid");
		}

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_modification'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($vid);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_modification.html')
		);

		//Get Vehicle Data For Navlinks
		$vehicle=$garage_vehicle->get_vehicle($vid);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $vehicle['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"))
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['ADD_MODIFICATION'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=add_modification&amp;VID=$vid"))
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
			'U_SUBMIT_BUSINESS_SHOP'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_business&amp;VID=$vid&amp;redirect=add_modification&amp;BUSINESS=" . BUSINESS_RETAIL ),
			'U_SUBMIT_BUSINESS_GARAGE'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_business&amp;VID=$vid&amp;redirect=add_modification&amp;BUSINESS=". BUSINESS_GARAGE),
			'U_SUBMIT_BUSINESS_PRODUCT'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_business&amp;VID=$vid&amp;redirect=add_modification&amp;BUSINESS=". BUSINESS_PRODUCT),
			'VID' 				=> $vid,
			'CATEGORY_ID' 			=> $data['category_id'],
			'MANUFACTURER_ID' 		=> $data['manufacturer_id'],
			'PRODUCT_ID' 			=> $data['product_id'],
			'S_DISPLAY_SUBMIT_BUSINESS'	=> ($garage_config['enable_user_submit_business'] && $auth->acl_get('u_garage_add_business')) ? true : false,
			'S_MODE_ACTION_PRODUCT' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_product"),
			'S_MODE_ACTION'			=> append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=insert_modification&amp;VID=$vid"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'insert_modification':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_modification.$phpEx?mode=add_modification&amp;VID=$vid");
		}

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_modification'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($vid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('category_id' => '' , 'manufacturer_id' => '', 'product_id' =>'', 'price' => '', 'shop_id' => '', 'installer_id' => '', 'install_price' => '', 'install_rating' => '', 'product_rating' => '', 'comments' => '', 'install_comments' => '', 'purchase_rating' => '');
		$data	= $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('category_id', 'manufacturer_id', 'product_id');
		$garage->check_required_vars($params);

		//Insert The Modification Into The DB With Data Acquired
		$mid = $garage_modification->insert_modification($data);

		//Update The Time Now...In Case We Get Redirected During Image Processing
		$garage_vehicle->update_vehicle_time($vid);

		//If Any Image Variables Set Enter The Image Handling
		if ($garage_image->image_attached())
		{
			//Check For Remote & Local Image Quotas
			if ($garage_image->below_image_quotas())
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('modification', $mid);
				//Insert Image Into Modifications Gallery
				$hilite = $garage_modification->hilite_exists($vid, $mid);
				$garage_image->insert_modification_gallery_image($image_id, $hilite);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ($garage_image->above_image_quotas())
			{
				redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=4"));
			}
		}

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"));

		break;

	case 'edit_modification':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_modification.$phpEx?mode=edit_modification&amp;MID=$mid&amp;VID=$vid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($vid);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_modification.html')
		);

		//Build Navlinks
		$vehicle_data 	= $garage_vehicle->get_vehicle($vid);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $vehicle_data['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"))
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['EDIT_MODIFICATION'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=edit_vehicle&amp;VID=$vid&amp;MID=$mid"))
		);
		
		//Get Required Data
		$data 		= $garage_modification->get_modification($mid);
		$categories 	= $garage->get_categories();
		$shops 		= $garage_business->get_business_by_type(BUSINESS_RETAIL);
		$garages 	= $garage_business->get_business_by_type(BUSINESS_GARAGE);
		$manufacturers 	= $garage_business->get_business_by_type(BUSINESS_PRODUCT);

		//Build All Required HTML parts
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
			'U_EDIT_DATA' 		=> append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=edit_modification&amp;VID=$vid&amp;MID=$mid"),
			'U_MANAGE_GALLERY' 	=> append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=manage_modification_gallery&amp;VID=$vid&amp;MID=$mid"),
			'U_SUBMIT_PRODUCT'	=> "javascript:add_product('edit_modification')",
			'U_SUBMIT_SHOP'		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_business&amp;VID=$vid&amp;redirect=add_modification&amp;BUSINESS=" . BUSINESS_RETAIL),
			'U_SUBMIT_GARAGE'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_business&amp;VID=$vid&amp;redirect=add_modification&amp;BUSINESS=" . BUSINESS_GARAGE),
			'MID' 			=> $mid,
			'VID' 			=> $vid,
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
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=update_modification"),
			'S_IMAGE_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=insert_modification_image"),
		));

		//Let Check The User Is Allowed Perform This Action
		if ((!$auth->acl_get('u_garage_upload_image')) OR (!$auth->acl_get('u_garage_remote_image')))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=16"));
		}

		//Pre Build All Side Menus
		$garage_template->attach_image('modification');

		//Pull Modification Gallery Data From DB
		$data = $garage_image->get_modification_gallery($vid, $mid);

		//Process Each Image From Modification Gallery
		for ($i = 0, $count = sizeof($data);$i < $count; $i++)
		{
			$template->assign_block_vars('pic_row', array(
				'U_IMAGE'	=> (($data[$i]['attach_id']) AND ($data[$i]['attach_is_image']) AND (!empty($data[$i]['attach_thumb_location'])) AND (!empty($data[$i]['attach_location']))) ? append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_image&amp;image_id=" . $data[$i]['attach_id']) : '',
				'U_REMOVE_IMAGE'=> append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=remove_modification_image&amp;VID=$vid&amp;MID=$mid&amp;image_id=" . $data[$i]['attach_id']),
				'U_SET_HILITE'	=> ($data[$i]['hilite'] == 0) ? append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=set_modification_hilite&amp;image_id=" . $data[$i]['attach_id'] . "&amp;VID=$vid&amp;MID=$mid") : '',
				'IMAGE' 	=> $phpbb_root_path . GARAGE_UPLOAD_PATH . $data[$i]['attach_thumb_location'],
				'IMAGE_TITLE' 	=> $data[$i]['attach_file'])
			);
		}

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();		

		break;

	case 'update_modification':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ( $user->data['user_id'] == ANONYMOUS )
		{
			login_box("garage_modification.$phpEx?mode=edit_modification&amp;MID=$mid&amp;VID=$vid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($vid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('category_id' => '', 'manufacturer_id' => '', 'product_id' => '', 'price' => '', 'shop_id' => '', 'installer_id' => '', 'install_price' => '', 'install_rating' => '', 'product_rating' => '', 'comments' => '', 'install_comments' => '', 'editupload' => '', 'image_id' => '', 'purchase_rating' => '');
		$data	= $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('category_id', 'manufacturer_id', 'product_id');
		$garage->check_required_vars($params);

		//Update The Modification With Data Acquired
		$garage_modification->update_modification($data);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($vid);

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"));

		break;

	case 'delete_modification':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_delete_modification'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($vid);

		//Delete The Modification
		$garage_modification->delete_modification($mid);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($vid);

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"));

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

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $data['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;VID=$vid"))
		);

		//Get All Gallery Data Required
		$gallery_data = $garage_image->get_modification_gallery($vid, $mid);
			
		//Process Each Image From Modification Gallery	
       		for ( $i = 0; $i < count($gallery_data); $i++ )
        	{
               		// Do we have a thumbnail?  If so, our job is simple here :)
			if ( (empty($gallery_data[$i]['attach_thumb_location']) == false) AND ($gallery_data[$i]['attach_thumb_location'] != $gallery_data[$i]['attach_location']) )
			{
				$template->assign_vars(array(
					'S_DISPLAY_GALLERIES' 	=> true,
				));

				$template->assign_block_vars('modification_image', array(
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
			'U_VIEW_PROFILE' 	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $data['user_id']),
			'U_VIEW_GARAGE_BUSINESS'=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=garage_review&amp;business_id=" . $data['installer_id']),
			'U_VIEW_SHOP_BUSINESS' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=shop_review&amp;business_id=" . $data['shop_id']),
			'YEAR' 			=> $data['made_year'],
			'MAKE' 			=> $data['make'],
			'MODEL' 		=> $data['model'],
            		'PRODUCT_RATING' 	=> $data['product_rating'],
            		'INSTALL_RATING' 	=> $data['install_rating'],
            		'BUSINESS_NAME' 	=> $data['business_title'],
			'BUSINESS' 		=> $data['install_business_title'],
			'USERNAME' 		=> $data['username'],
			'USERNAME_COLOUR'	=> get_username_string('colour', $data['user_id'], $data['username'], $data['user_colour']),
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

	case 'insert_modification_image':

		//Let Check The User Is Allowed Perform This Action
		if ((!$auth->acl_get('u_garage_upload_image')) OR (!$auth->acl_get('u_garage_remote_image')))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=16"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($vid);

		//If Any Image Variables Set Enter The Image Handling
		if ($garage_image->image_attached())
		{
			//Check For Remote & Local Image Quotas
			if ($garage_image->below_image_quotas())
			{
				//Create Thumbnail & DB Entry For Image
				$image_id = $garage_image->process_image_attached('modification', $mid);
				//Insert Image Into Modification Gallery
				$hilite = $garage_modification->hilite_exists($mid);
				$garage_image->insert_modification_gallery_image($image_id, $hilite);
			}
			//You Have Reached Your Image Quota..Error Nicely
			else if ($garage_image->above_image_quotas())
			{
				redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=4"));
			}
		}

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($vid);

		redirect(append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=edit_modification&amp;VID=$vid&amp;MID=$mid#images"));

		break;

	case 'set_modification_hilite':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($vid);

		//Set All Images To Non Hilite So We Do Not End Up With Two Hilites & Then Set Hilite
		$garage->update_single_field(GARAGE_MODIFICATION_GALLERY_TABLE, 'hilite', 0, 'modification_id', $mid);
		$garage->update_single_field(GARAGE_MODIFICATION_GALLERY_TABLE, 'hilite', 1, 'image_id', $image_id);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($vid);

		redirect(append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=edit_modification&amp;VID=$vid&amp;MID=$mid#images"));

		break;

	case 'remove_modification_image':

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($vid);

		//Remove Image From Modification Gallery & Deletes Image
		$garage_image->delete_modification_image($image_id);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($vid);

		redirect(append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=edit_modification&amp;VID=$vid&amp;MID=$mid#images"));

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

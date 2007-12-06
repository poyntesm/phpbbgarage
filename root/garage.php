<?php
/***************************************************************************
 *                              garage.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: garage.php,v 0.9.4 06/06/2005 20:47:20 poynesmo Exp $
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

// Let's set the root dir for phpBB & load normal phpBB required files
$phpbb_root_path = './';
require($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
include($phpbb_root_path . 'includes/bbcode.'.$phpEx);
require($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_garage.' . $phpEx);
require($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_garage_error.' . $phpEx);

//Build All Garage Functions For $garage_lib->
require($phpbb_root_path . 'includes/functions_garage.' . $phpEx);

// Start session management
$userdata = session_pagestart($user_ip, PAGE_GARAGE);
init_userprefs($userdata);

// Set The Page Title
$page_title = $lang['Garage'];
$garage_lib->build_notice();

//Get All String Parameters And Make Safe
$params = array('mode' => 'mode', 'sort' => 'sort');
while( list($var, $param) = @each($params) )
{
	if ( !empty($HTTP_POST_VARS[$param]) || !empty($HTTP_GET_VARS[$param]) )
	{
		$$var = ( !empty($HTTP_POST_VARS[$param]) ) ? str_replace("\'", "''", trim(htmlspecialchars($HTTP_POST_VARS[$param]))) : str_replace("\'", "''", trim(htmlspecialchars($HTTP_GET_VARS[$param])));
	}
	else
	{
		$$var = '';
	}
}

//Get All Non-String Parameters
$params = array('cid' => 'CID', 'mid' => 'MID', 'rrid' => 'RRID', 'qmid' => 'QMID', 'ins_id' => 'INS_ID', 'eid' => 'EID', 'image_id' => 'image_id', 'comment_id' => 'comment_id', 'bus_id' => 'BUS_ID');
while( list($var, $param) = @each($params) )
{
	if ( !empty($HTTP_POST_VARS[$param]) || !empty($HTTP_GET_VARS[$param]) )
	{
		$$var = (!empty($HTTP_POST_VARS[$param])) ? intval($HTTP_POST_VARS[$param]) : intval($HTTP_GET_VARS[$param]);
	}
	else
	{
		$$var = '';
	}
}

//Decide What Mode The User Is Doing
switch( $mode )
{
	//Mode To Display Create Vehicle Sceen //
	case 'create_vehicle':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=create_vehicle", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'javascript' => 'garage_vehicle_select_javascript.tpl',
			'body'   => 'garage_vehicle.tpl')
		);

		//Get Users Garage Size
		$count = $garage_lib->check_garage_size();

		//Check To See If User Has Too Many Vehicles Already...If So Display Notice
		if ( ($userdata['member_type'] == PREMIUM) AND ($count['total'] == $garage_config['max_premium_user_cars']) )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=5", true));
		}
		else if ( $count['total'] == $garage_config['max_user_cars'] AND $userdata['user_level'] != ADMIN )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=5", true));
		}

		//Build All Required HTML 
		$garage_lib->build_year_html();
		$garage_lib->build_attach_image_html('vehicle');

		//Set Default Make 
		$str_params = array('MAKE', 'MODEL');
		$data = $garage_lib->process_str_vars($str_params);
		$data['MAKE'] = (empty($data['MAKE'])) ? '' : $data['MAKE'];

		//Build All Required Javascript And Arrays
		$template->assign_vars(array(
			'VEHICLE_ARRAY' => $garage_lib->build_vehicle_javascript())
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
			'CURRENCY_LIST'	=> $garage_lib->build_selection_box('currency',$currency_types,$currency_types,''),
			'MILEAGE_UNIT_LIST' => $garage_lib->build_selection_box('mileage_units',$mileage_unit_types,$mileage_unit_types,''))
		);

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		
		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();		
		$template->pparse('body');

		break;

	//Mode To Actaully Insert Into DB A New Vehicle
	case 'insert_vehicle':

		//User Is Annoymous...So Not Allowed To Create A Vehicle
		if ( $userdata['user_id'] == ANONYMOUS )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=2", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Get Users Garage Size
		$count = $garage_lib->check_garage_size();

		//Check To See If User Has Too Many Vehicles Already
		if ( ($userdata['member_type'] == PREMIUM) AND ($count['total'] == $garage_config['max_premium_user_cars']) )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=5", true));
		}
		else if ($count['total'] == $garage_config['max_user_cars'] AND $userdata['user_level'] != ADMIN )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=5", true));
		}

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('year', 'make_id', 'model_id');
		$int_data = $garage_lib->process_int_vars($int_params);
		$str_params = array('colour', 'mileage', 'mileage_units', 'price', 'currency', 'comments', 'guestbook_pm_notify', 'adding_model');
		$str_data = $garage_lib->process_str_vars($str_params);
		$data = $this->merge_int_str_data($int_data, $str_data);
		$data['guestbook_pm_notify'] = ($data['guestbook_pm_notify'] == 'on') ? 1 : 0;
		$data['time'] = time();

		//We Need To Check If We Have Been Sent Here To Add A Model...
		if ( $data['adding_model'] == 'YES' )
		{
			redirect(append_sid("garage.$phpEx?mode=user_submit_model&MAKE_ID=".$data['make_id']."", true));
		}

		//Set As Main User Vehicle If No Other Vehicle Exists For User
		$data['main_vehicle'] = ( $count['total'] == 0 ) ? 1 : 0;

		//Checks All Required Data Is Present
		$params = array('year', 'make_id', 'model_id');
		$garage_lib->check_required_vars($params);

		//Insert The Vehicle Into The DB And Get The CID
		$cid = $garage_lib->insert_vehicle($data);

		//If Any Image Variables Set Enter The Image Handling
		if( ((isset($HTTP_POST_FILES['FILE_UPLOAD'])) AND ($HTTP_POST_FILES['FILE_UPLOAD']['name'])) OR (!preg_match("/^http:\/\/$/i", $HTTP_POST_VARS['url_image'])) )
		{
			$image_id = $garage_lib->process_image_attach('vehicle',$cid);
			if (!empty($image_id))
			{
				$garage_lib->insert_gallery_image($image_id);
				//Set Image As Hilite Image
				$garage_lib->update_single_field(GARAGE_TABLE,'image_id',$image_id,'id',$cid);
			}
		}

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	//Mode To Display Editting Page Of An Existing Vehicle
	case 'edit_vehicle':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=edit_vehicle&CID=$cid", true));
		}

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'javascript' => 'garage_vehicle_select_javascript.tpl',
			'body'   => 'garage_vehicle.tpl')
		);

		//Pull Required Data From DB
		$data = $garage_lib->select_vehicle_data($cid);

		//Build All Required HTML
		$garage_lib->build_year_html($data['made_year']);

		//Build All Required Javascript And Arrays
		$template->assign_vars(array(
			'VEHICLE_ARRAY' => $garage_lib->build_vehicle_javascript())
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
			'CURRENCY_LIST' => $garage_lib->build_selection_box('currency',$currency_types,$currency_types,$data['currency']),
			'MILEAGE_UNIT_LIST' => $garage_lib->build_selection_box('mileage_units',$mileage_unit_types,$mileage_unit_types,$data['mileage_units']),
			'COMMENTS' => $data['comments'])
		);

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	//Mode To Actaully Update The DB Of An Existing Vehicle
	case 'update_vehicle':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=edit_vehicle&CID=$cid", true));
		}

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('year', 'make_id', 'model_id');
		$int_data = $garage_lib->process_int_vars($int_params);
		$str_params = array('colour', 'mileage', 'mileage_units', 'price', 'currency', 'comments', 'guestbook_pm_notify');
		$str_data = $garage_lib->process_str_vars($str_params);
		$data = $this->merge_int_str_data($int_data, $str_data);
		$data['guestbook_pm_notify'] = ($data['guestbook_pm_notify'] == 'on') ? 1 : 0;

		//Checks All Required Data Is Present
		$params = array('year', 'make_id', 'model_id');
		$garage_lib->check_required_vars($params);

		//Update The DB With Data Acquired
		$garage_lib->update_vehicle($data);
	
		//Update Timestamp For Vehicle	
		$garage_lib->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	//Mode To Delete A Vehicle From The DB
	case 'delete_vehicle':

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		$garage_lib->delete_vehicle($cid);

		redirect(append_sid("garage.$phpEx?mode=main_menu", true));

		break;

	//Mode To Display Add Modification Page
	case 'add_modification':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=add_modification&CID=$cid", true));
		}

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_modification.tpl')
		);

		//Build HTML Components
		$garage_lib->build_category_html('');
		$garage_lib->build_attach_image_html('modification');
		$garage_lib->build_garage_install_list_html('','');
		$garage_lib->build_shop_list_html('','');

		$template->assign_vars(array(
			'S_MODE_ACTION'		=> append_sid("garage.$phpEx?mode=insert_modification&CID=$cid"),
			'L_CATEGORY'		=> $lang['Category'],
			'L_NOT_LISTED_YET' 	=> $lang['Not_Listed_Yet'],
			'L_HERE' 		=> $lang['Here'],
			'L_REQUIRED' 		=> $lang['Required'],
			'L_MODIFICATION' 	=> $lang['Modification'],
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
			'U_SUBMIT_SHOP'		=> append_sid("garage.$phpEx?mode=user_submit_business&CID=$cid&TYPE=add_modification&BUSINESS=shop"),
			'U_SUBMIT_GARAGE'	=> append_sid("garage.$phpEx?mode=user_submit_business&CID=$cid&TYPE=add_modification&BUSINESS=garage"),
			'PRODUCT_RATING_LIST' 	=> $garage_lib->build_selection_box('product_rating',$rating_text,$rating_types,''),
			'INSTALL_RATING_LIST' 	=> $garage_lib->build_selection_box('install_rating',$rating_text,$rating_types,''),
			'CID' => $cid)
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'insert_modification':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=add_modification&CID=$cid", true));
		}

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('category_id', 'business_id', 'install_business_id', 'install_rating', 'product_rating');
		$int_data = $garage_lib->process_int_vars($int_params);
		$str_params = array('title', 'price', 'install_price', 'comments', 'install_comments');
		$str_data = $garage_lib->process_str_vars($str_params);
		$data = $this->merge_int_str_data($int_data, $str_data);
		$data['time'] = time();
		$vehicle = $garage_lib->select_vehicle_data($cid);
		$data['member_id'] = $vehicle['member_id'];

		//Checks All Required Data Is Present
		$params = array('category_id', 'title');
		$garage_lib->check_required_vars($params);

		//Update The DB With Data Acquired
		$mid = $garage_lib->insert_modification($data);

		// Update The Time Now...In Case We Get Redirected During Image Processing
		$garage_lib->update_vehicle_time($cid);

		if( ((isset($HTTP_POST_FILES['FILE_UPLOAD'])) AND ($HTTP_POST_FILES['FILE_UPLOAD']['name'])) OR (!preg_match("/^http:\/\/$/i", $HTTP_POST_VARS['url_image'])) )
		{
			$image_id = $garage_lib->process_image_attach('modification',$mid);
			if (!empty($image_id))
			{
				$garage_lib->update_single_field(GARAGE_MODS_TABLE,'image_id',$image_id,'id',$mid);
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
		$garage_lib->check_own_vehicle($cid);

		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_modification.tpl')
		);
		
		//Pull Required Data From DB
		$data = $garage_lib->select_modification_data($mid);

		//Build All Required HTML parts
		$garage_lib->build_category_html($data['category_id']);
		$garage_lib->build_garage_install_list_html($data['install_business_id'],$data['install_business_name']);
		$garage_lib->build_shop_list_html($data['business_id'],$data['business_name']);
		$garage_lib->build_edit_image_html($data['image_id'], $data['attach_file']);

		$template->assign_block_vars('level2', array());
		$template->assign_vars(array(
			'L_LEVEL2' => $data['vehicle'],
			'L_VEHICLE_INFO' => $lang['Vehicle_Info'],
			'L_REQUIRED' => $lang['Required'],
			'L_CATEGORY' => $lang['Category'],
			'L_TITLE' => $lang['Title'],
			'L_MODIFICATION' => $lang['Modification'],
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
			'U_LEVEL2' => append_sid("garage.$phpEx?mode=view_own_vehicle&amp;CID=".$cid.""),
			'S_MODE_ACTION' => append_sid("garage.$phpEx?mode=update_modification"),
			'MID' => $mid,
			'CID' => $cid,
			'JAVASCRIPT' => $data['vehicle_javascript'],
			'TITLE' => $data['title'],
			'MAKE' => $data['make'],
			'MODEL' => $data['model'],
			'PRICE' => $data['price'],
			'INSTALL_PRICE' => $data['install_price'],
			'PRODUCT_RATING_LIST' => $garage_lib->build_selection_box('product_rating',$rating_text,$rating_types,$data['product_rating']),
			'INSTALL_RATING_LIST' => $garage_lib->build_selection_box('install_rating',$rating_text,$rating_types,$data['install_rating']),
			'COMMENTS' => $data['comments'],
			'INSTALL_COMMENTS' => $data['install_comments'])
		);

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();		
		$template->pparse('body');

		break;

	case 'update_modification':

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('category_id', 'business_id', 'install_business_id', 'install_rating', 'product_rating', 'image_id');
		$int_data = $garage_lib->process_int_vars($int_params);
		$str_params = array('title', 'price', 'install_price', 'comments', 'install_comments', 'edit_upload');
		$str_data = $garage_lib->process_str_vars($str_params);
		$data = $this->merge_int_str_data($int_data, $str_data);
		$data['time'] = time();

		//Checks All Required Data Is Present
		$params = array('category_id', 'title');
		$garage_lib->check_required_vars($params);

		//Update The DB With Data Acquired
		$garage_lib->update_modification($data);

		$garage_lib->update_vehicle_time($cid);

		//User Has Chosen To Delete Existing Image
		if ( ($data['editupload'] == 'delete') OR ( $data['editupload'] == 'new') )
		{
			$garage_lib->delete_image($data['image_id']);
			$garage_lib->update_single_field(GARAGE_MODS_TABLE,'image_id','NULL','id',$mid);
		}

		//Handle New Image Upload
		if( ((isset($HTTP_POST_FILES['FILE_UPLOAD'])) AND ($HTTP_POST_FILES['FILE_UPLOAD']['name'])) OR (!preg_match("/^http:\/\/$/i", $HTTP_POST_VARS['url_image'])) )
		{
			$image_id = $garage_lib->process_image_attach('modification',$mid);
			if (!empty($image_id))
			{
				$garage_lib->update_single_field(GARAGE_MODS_TABLE,'image_id',$image_id,'id',$mid);
			}
		}

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	case 'delete_modification':

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		$garage_lib->delete_modification($mid);

		$garage_lib->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	case 'add_quartermile':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Let Check That Quartermile Times Are Allowed...If Not Redirect
		if ($garage_config['enable_quartermile'] == '0')
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=18", true));
		}

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		$count = $garage_lib->count_rollingroad_runs($cid);

		if ( $count['total'] > 0 )
		{
			$template->assign_block_vars('link_rr', array());
			$garage_lib->build_rr_list_html('','',$cid);
		}
		
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_quartermile.tpl')
		);

		//Pre Build All Side Menus
		$garage_lib->build_attach_image_html('vehicle');

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
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'insert_quartermile':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Let Check That Quartermile Times Are Allowed...If Not Redirect
		if ($garage_config['enable_quartermile'] == '0')
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=18", true));
		}

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('rr_id');
		$int_data = $garage_lib->process_int_vars($int_params);
		$str_params = array('rt', 'sixty', 'three', 'eight', 'eightmph', 'thou', 'quart', 'quartmph', 'install_comments');
		$str_data = $garage_lib->process_str_vars($str_params);
		$data = $this->merge_int_str_data($int_data, $str_data);
		$data['pending'] = ($garage_config['enable_quartermile_approval'] == '1') ? 1 : 0 ;
		$data['time'] = time();

		//Checks All Required Data Is Present
		$params = array('quart');
		$garage_lib->check_required_vars($params);

		//Update The DB With Data Acquired
		$qmid = $garage_lib->insert_quartermile($data);

		// Update The Time Now...In Case We Get Redirected During Image Processing
		$garage_lib->update_vehicle_time($cid);

		if( (isset($HTTP_POST_FILES['FILE_UPLOAD'])) AND ($HTTP_POST_FILES['FILE_UPLOAD']['name']) OR (!preg_match("/^http:\/\/$/i", $HTTP_POST_VARS['url_image'])) )
		{
			$image_id = $garage_lib->process_image_attach('quartermile',$qmid);
			if (!empty($image_id))
			{
				$garage_lib->update_single_field(GARAGE_QUARTERMILE_TABLE,'image_id',$image_id,'id',$qmid);
			}
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
		$garage_lib->check_own_vehicle($cid);

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_quartermile.tpl')
		);

		$count = $garage_lib->count_rollingroad_runs($cid);	

		//See If We Got Sent Here By Pending Page...If So We Need To Tell Update To Redirect Correctly
		$str_params = array('PENDING');
		$redirect = $garage_lib->process_str_vars($str_params);

		//Pull Required Data From DB
		$data = $garage_lib->select_quartermile_data($qmid);

		$bhp_statement = ''.$data['bhp'].' BHP @ '.$data['bhp_unit'].'';

		if ( (!empty($data['rr_id'])) AND ($count['total'] > 0) )
		{
			$template->assign_block_vars('link_rr', array());
			$garage_lib->build_rr_list_html($data['rr_id'],$bhp_statement,$cid);
		}
		else if ( (empty($data['rr_id'])) AND ($count['total'] > 0) )
		{
			$template->assign_block_vars('link_rr', array());
			$garage_lib->build_rr_list_html('','',$cid);
		}

		//Build All HTML Parts
		$garage_lib->build_edit_image_html($data['image_id'], $data['attach_file']);

		$template->assign_block_vars('level2', array());
		$template->assign_vars(array(
			'L_GARAGE_QUARTERMILE_TIMES' => $lang['Garage_Quartermile_Times'],
			'U_LEVEL2' => append_sid("garage.$phpEx?mode=view_own_vehicle&amp;CID=".$cid.""),
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
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'update_quartermile':

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('rr_id', 'image_id');
		$int_data = $garage_lib->process_int_vars($int_params);
		$str_params = array('rt', 'sixty', 'three', 'eight', 'eightmph', 'thou', 'quart', 'quartmph', 'install_comments', 'editupload', 'pending_redirect');
		$str_data = $garage_lib->process_str_vars($str_params);
		$data = $this->merge_int_str_data($int_data, $str_data);
		$data['pending'] = ($garage_config['enable_quartermile_approval'] == '1') ? 1 : 0 ;
		$data['time'] = time();

		//Checks All Required Data Is Present
		$params = array('quart');
		$garage_lib->check_required_vars($params);

		//Update The DB With Data Acquired
		$garage_lib->update_quartermile($data);

		//Update The Time Now...In Case We Get Redirected During Image Processing
		$garage_lib->update_vehicle_time($cid);

		if ( ($data['editupload'] == 'delete') OR ($data['editupload'] == 'new') )
		{
			$garage_lib->delete_image($data['image_id']);
			$garage_lib->update_single_field(GARAGE_QUARTERMILE_TABLE,'image_id','NULL','id',$qmid);
		}

		//Since We Have Removed The Old Image Lets Handle The New One Now
		if( (isset($HTTP_POST_FILES['FILE_UPLOAD'])  AND ($HTTP_POST_FILES['FILE_UPLOAD']['name'])) OR (!preg_match("/^http:\/\/$/i", $HTTP_POST_VARS['url_image'])) )
		{
			$image_id = $garage_lib->process_image_attach('quartermile',$qmid);
			if (!empty($image_id))
			{
				$garage_lib->update_single_field(GARAGE_QUARTERMILE_TABLE,'image_id',$image_id,'id',$qmid);
			}
		}

		if ( $data['pending_redirect'] == 'YES' )
		{
			redirect(append_sid("garage.$phpEx?mode=garage_pending", true));
		}
		else
		{
			redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));
		}

		break;

	case 'delete_quartermile':

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		$garage_lib->delete_quartermile_time($qmid);

		$garage_lib->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;
	
	case 'add_rollingroad':

		//Let Check That Rollingroad Runs Are Allowed...If Not Redirect
		if ($garage_config['enable_rollingroad'] == '0')
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=18", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);
		
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_rollingroad.tpl')
		);

		//Build Required HTML Components Like Drop Down Boxes.....
		$garage_lib->build_attach_image_html('vehicle');

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
			'NITROUS_UNIT_LIST' => $garage_lib->build_selection_box('nitrous',$nitrous_types_text,$nitrous_types,''),
			'TORQUE_UNIT_LIST' => $garage_lib->build_selection_box('torque_unit',$power_types,$power_types,''),
			'BHP_UNIT_LIST' => $garage_lib->build_selection_box('bhp_unit',$power_types,$power_types,''),
			'BOOST_UNIT_LIST' => $garage_lib->build_selection_box('boost_unit',$boost_types,$boost_types,''),
			'CID' => $cid,
			'S_MODE_ACTION' => append_sid("garage.$phpEx?mode=insert_rollingroad"))
         	);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'insert_rollingroad':

		//Let Check That Rollingroad Runs Are Allowed...If Not Redirect
		if ($garage_config['enable_rollingroad'] == '0')
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=18", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('nitrous');
		$int_data = $garage_lib->process_int_vars($int_params);
		$str_params = array('dynocenter', 'bhp', 'bhp_unit', 'torque', 'torque_unit', 'boost', 'boost_unit', 'peakpoint');
		$str_data = $garage_lib->process_str_vars($str_params);
		$data = $this->merge_int_str_data($int_data, $str_data);
		$data['pending'] = ($garage_config['enable_rollingroad_approval'] == '1') ? 1 : 0 ;
		$data['time'] = time();

		//Checks All Required Data Is Present
		$params = array('bhp', 'bhp_unit');
		$garage_lib->check_required_vars($params);

		//Update The DB With Data Acquired
		$rrid = $garage_lib->insert_rollingroad($data);

		// Update The Time Now...In Case We Get Redirected During Image Processing
		$garage_lib->update_vehicle_time($cid);

		if( (isset($HTTP_POST_FILES['FILE_UPLOAD'])) AND ($HTTP_POST_FILES['FILE_UPLOAD']['name']) OR (!preg_match("/^http:\/\/$/i", $HTTP_POST_VARS['url_image'])) )
		{
			$image_id = $garage_lib->process_image_attach('rollingroad',$rrid);
			if (!empty($image_id))
			{
				$garage_lib->update_single_field(GARAGE_ROLLINGROAD_TABLE,'image_id',$image_id,'id',$rrid);
			}
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
		$garage_lib->check_own_vehicle($cid);

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_rollingroad.tpl')
		);

		//Pull Required Data From DB
		$data = $garage_lib->select_rollingroad_data($rrid);

		//See If We Got Sent Here By Pending Page...If So We Need To Tell Update To Redirect Correctly
		$str_params = array('PENDING');
		$redirect = $garage_lib->process_str_vars($str_params);

		//Build All Required HTML
		$garage_lib->build_edit_image_html($data['image_id'], $data['attach_file']);

		$template->assign_block_vars('level2', array());
		$template->assign_vars(array(
			'U_LEVEL2' => append_sid("garage.$phpEx?mode=view_own_vehicle&amp;CID=".$cid.""),
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
			'NITROUS_UNIT_LIST' => $garage_lib->build_selection_box('nitrous',$nitrous_types_text,$nitrous_types,$data['nitrous']),
			'BOOST_UNIT_LIST' => $garage_lib->build_selection_box('boost_unit',$boost_types,$boost_types,$data['boost_unit']),
			'TORQUE_UNIT_LIST' => $garage_lib->build_selection_box('torque_unit',$power_types,$power_types,$data['torque_unit']),
			'BHP_UNIT_LIST' => $garage_lib->build_selection_box('bhp_unit',$power_types,$power_types,$data['bhp_unit']),
			'S_MODE_ACTION' => append_sid("garage.$phpEx?mode=update_rollingroad"))

		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'update_rollingroad':

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('nitrous', 'image_id');
		$int_data = $garage_lib->process_int_vars($int_params);
		$str_params = array('dynocenter', 'bhp', 'bhp_unit', 'torque', 'torque_unit', 'boost', 'boost_unit', 'peakpoint', 'editupload', 'pending_redirect');
		$str_data = $garage_lib->process_str_vars($str_params);
		$data = $this->merge_int_str_data($int_data, $str_data);
		$data['pending'] = ($garage_config['enable_rollingroad_approval'] == '1') ? 1 : 0 ;
		$data['time'] = time();

		//Checks All Required Data Is Present
		$params = array('bhp', 'bhp_unit');
		$garage_lib->check_required_vars($params);

		//Update The DB With Data Acquired
		$garage_lib->update_rollingroad($data);

		// Update The Time Now...In Case We Get Redirected During Image Processing
		$garage_lib->update_vehicle_time($cid);

		if ( ($data['editupload'] == 'delete') OR ($data['editupload'] == 'new') )
		{
			$garage_lib->delete_image($data['image_id']);
			$garage_lib->update_single_field(GARAGE_ROLLINGROAD_TABLE,'image_id','NULL','id',$rrid);
		}

		//Since We Have Removed The Old Image Lets Handle The New One Now
		if( ((isset($HTTP_POST_FILES['FILE_UPLOAD']) AND ($HTTP_POST_FILES['FILE_UPLOAD']['name']))) OR (!preg_match("/^http:\/\/$/i", $HTTP_POST_VARS['url_image'])) )
		{
			$image_id = $garage_lib->process_image_attach('rollingroad',$rrid);
			if (!empty($image_id))
			{
				$garage_lib->update_single_field(GARAGE_ROLLINGROAD_TABLE,'image_id',$image_id,'id',$rrid);
			}
		}

		if ( $data['pending_redirect'] == 'YES' )
		{
			redirect(append_sid("garage.$phpEx?mode=garage_pending", true));
		}
		else
		{
			redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));
		}

		break;

	case 'delete_rollingroad':

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);
	
		$garage_lib->delete_rollingroad_run($rrid);

		$garage_lib->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	case 'add_insurance':

		//Let Check That Insurance Premiums Are Allowed...If Not Redirect
		if ($garage_config['enable_insurance'] == '0')
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=18", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_insurance.tpl')
		);

		//Build All Required HTML Components
		$garage_lib->build_attach_image_html('modification');
		$garage_lib->build_insurance_list_html('','');

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
			'U_SUBMIT_BUSINESS' => append_sid("garage.$phpEx?mode=user_submit_business&CID=$cid&TYPE=add_insurance&BUSINESS=insurance"),
			'CID' => $cid,
			'COVER_TYPE_LIST' => $garage_lib->build_selection_box('cover_type',$cover_types,$cover_types,''))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'insert_insurance':

		//Let Check That Insurance Premiums Are Allowed...If Not Redirect
		if ($garage_config['enable_insurance'] == '0')
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=18", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('business_id');
		$int_data = $garage_lib->process_int_vars($int_params);
		$str_params = array('premium', 'cover_type', 'comments');
		$str_data = $garage_lib->process_str_vars($str_params);
		$data = $this->merge_int_str_data($int_data, $str_data);
		$data['time'] = time();

		//Checks All Required Data Is Present
		$params = array('business_id', 'premium', 'cover_type');
		$garage_lib->check_required_vars($params);

		//Update The DB With Data Acquired
		$garage_lib->insert_insurance($data);

		$garage_lib->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

	case 'edit_insurance':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=edit_insurance&IND_ID=$ins_id&CID=$cid", true));
		}

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_insurance.tpl')
		);

		//Pull Required Data From DB
		$data = $garage_lib->select_insurance_data($ins_id);

		//Build Required HTML Components
		$garage_lib->build_insurance_list_html($data['business_id'],$data['name']);

		$template->assign_block_vars('level2', array());
		$template->assign_vars(array(
			'U_LEVEL2' 		=> append_sid("garage.$phpEx?mode=view_own_vehicle&amp;CID=".$cid.""),
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
			'COVER_TYPE_LIST' 	=> $garage_lib->build_selection_box('cover_type',$cover_types,$cover_types,$data['cover_type']))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'update_insurance':

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('business_id');
		$int_data = $garage_lib->process_int_vars($int_params);
		$str_params = array('premium', 'cover_type', 'comments');
		$str_data = $garage_lib->process_str_vars($str_params);
		$data = $this->merge_int_str_data($int_data, $str_data);

		//Checks All Required Data Is Present
		$params = array('business_id', 'premium', 'cover_type');
		$garage_lib->check_required_vars($params);

		//Update The DB With Data Acquired
		$garage_lib->update_insurance($data);

		$garage_lib->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;
	
	case 'delete_insurance':

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		$garage_lib->delete_insurance($ins_id);

		$garage_lib->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	//Mode To Display A List Of Vehicles..Also Used To Display Search Results For Search By Make/Model/User
	case 'browse':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('BROWSE',"garage.$phpEx?mode=error&EID=15");

		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_browse.tpl')
		);

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$start = (isset($HTTP_GET_VARS['start'])) ? intval($HTTP_GET_VARS['start']) : 0;
		$order_by = (empty($sort)) ? 'date_updated' : $sort;

		if ( (isset($HTTP_POST_VARS['order'])) OR (isset($HTTP_GET_VARS['order'])) )
		{
			$sort_order = (($HTTP_POST_VARS['order'] == 'ASC') OR ($HTTP_GET_VARS['order'] == 'ASC') ) ? 'ASC' : 'DESC';
		}
		else
		{
			$sort_order = 'DESC';
		}

		//Check If This Is A Search....If So We Have A Bit More Work To Do.....
		if ((isset($HTTP_GET_VARS['search'])) OR (isset($HTTP_POST_VARS['search'])))
		{
			$search = (isset($HTTP_POST_VARS['search'])) ? htmlspecialchars($HTTP_POST_VARS['search']) : htmlspecialchars($HTTP_GET_VARS['search']);

			$search_data = $garage_lib->build_search_for_user_make_model();
			$search_data['pagination'] =';search=yes&amp';

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
		$garage_lib->build_sort_order_html($sort_order);

		$sql = "SELECT g.*, makes.make, models.model, user.username, count(mods.id) AS total_mods 
        		FROM " . GARAGE_TABLE . " AS g 
                    		LEFT JOIN " . GARAGE_MODS_TABLE . " AS mods ON mods.garage_id = g.id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
			        LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id 
			        LEFT JOIN " . USERS_TABLE . " AS user ON g.member_id = user.user_id 
			WHERE makes.pending = 0 AND models.pending = 0
				".$search_data['where']."
		        GROUP BY g.id
			ORDER BY $order_by $sort_order
			LIMIT $start, " . $garage_config['cars_per_page'];

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select All Vehicles Data', '', __LINE__, __FILE__, $sql);
		}
		
		$i = 0;
		while ($data = $db->sql_fetchrow($result) )
		{
			$cid = $data['id'];
            		if ($data['image_id'])
			{
				$image_attached = '<img hspace="1" vspace="1" src="' . $images['vehicle_image_attached'] . '" alt="' . $lang['Vehicle_Image_Attahced'] . '" title="' . $lang['Vehicle_Image_Attached'] . '" border="0" />';
			}
			else
			{
				$image_attached ='';
			}

			$row_color = ( !($i % 2) ) ? $theme['td_color1'] : $theme['td_color2'];
			$row_class = ( !($i % 2) ) ? $theme['td_class1'] : $theme['td_class2'];
	
			$template->assign_block_vars('vehiclerow', array(
				'ROW_NUMBER' => $i + ( $start + 1 ),
				'ROW_COLOR' => '#' . $row_color,
				'ROW_CLASS' => $row_class,
				'IMAGE_ATTACHED' => $image_attached,
				'YEAR' => $data['made_year'],
				'MAKE' => $data['make'],
				'COLOUR' => $data['color'],
				'UPDATED' => create_date($board_config['default_dateformat'], $data['date_updated'], $board_config['board_timezone']),
				'VIEWS' => $data['views'],
				'MODS' => $data['total_mods'],
				'MODEL' => $data['model'],
				'OWNER' => $data['username'],
				'U_VIEW_VEHICLE' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=$cid"),
				'U_VIEW_PROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=".$data['member_id'].""))
			);
			$i++;
		}
		$db->sql_freeresult($result);

		//Count Total Returned For Pagination...Need Other Tables Incase It Was A Search!!!
		$sql = "SELECT count(*) AS total
			FROM " . GARAGE_TABLE . " g
				LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
			        LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id 
			        LEFT JOIN " . USERS_TABLE . " AS user ON g.member_id = user.user_id 
			WHERE makes.pending = 0 AND models.pending = 0 
		       		".$search_data['where'];

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Counting Total Vehicles', '', __LINE__, __FILE__, $sql);
		}

		$count = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$pagination = generate_pagination("garage.$phpEx?mode=browse&amp".$search_data['make_pagination']."".$search_data['model_pagination']."".$search_data['pagination'].";sort=$sort&amp;order=$sort_order", $count['total'], $garage_config['cars_per_page'], $start). '&nbsp;';

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
			'PAGE_NUMBER' => sprintf($lang['Page_of'], ( floor( $start / $garage_config['cars_per_page'] ) + 1 ), ceil( $count['total'] / $garage_config['cars_per_page'] )), 
			'S_SORT_SELECT' => $garage_lib->build_selection_box('sort',$sort_types_text,$sort_types,$sort),
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
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	//Mode To Display Searches Of Insurance
	case 'search_insurance':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('BROWSE',"garage.$phpEx?mode=error&EID=15");

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$start = (isset($HTTP_GET_VARS['start'])) ? intval($HTTP_GET_VARS['start']) : 0;
		$order_by = (empty($sort)) ? 'premium' : $sort;

		if ( (isset($HTTP_POST_VARS['order'])) OR (isset($HTTP_GET_VARS['order'])) )
		{
			$sort_order = (($HTTP_POST_VARS['order'] == 'ASC') OR ($HTTP_GET_VARS['order'] == 'ASC') ) ? 'ASC' : 'DESC';
		}
		else
		{
			$sort_order = 'ASC';
		}

		$search_data = $garage_lib->build_search_for_user_make_model();
		
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

		$select_sort = '<select name="sort">';
		for($i = 0; $i < count($sort_types_text); $i++)
		{
			$selected = ( $sort == $sort_types[$i] ) ? ' selected="selected"' : '';
			$select_sort .= '<option value="' . $sort_types[$i] . '"' . $selected . '>' . $sort_types_text[$i] . '</option>';
		}
		$select_sort .= '</select>';

	      	$template->assign_block_vars('switch_search', array());
		$template->assign_vars(array(
			'SEARCH_MESSAGE' => $search_data['search_message'])
		);

		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body' => 'garage_browse_insurance.tpl')
		);

		//Pre Build All Side Menus
		$garage_lib->build_sort_order_html($sort_order);

		$sql = "SELECT i.*, g.*, b.title, b.id as business_id, makes.make, models.model, user.username, user.user_id,
                        ( SUM(mods.price) + SUM(mods.install_price) ) AS total_spent,
			CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle
        		FROM " . GARAGE_INSURANCE_TABLE . " AS i 
                    		LEFT JOIN " . GARAGE_TABLE . " AS g ON i.garage_id = g.id
	                    	LEFT JOIN " . GARAGE_MODS_TABLE . " AS mods ON i.garage_id = mods.garage_id
        	            	LEFT JOIN " . GARAGE_BUSINESS_TABLE . " AS b ON i.business_id = b.id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
		        	LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id 
			        LEFT JOIN " . USERS_TABLE . " AS user ON g.member_id = user.user_id 
			WHERE makes.pending = 0 AND models.pending = 0
				".$search_data['where']."
			GROUP BY i.id
			ORDER BY $order_by $sort_order
			LIMIT $start, " . $garage_config['cars_per_page'];

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select All Vehicles Data', '', __LINE__, __FILE__, $sql);
		}

		while ($data = $db->sql_fetchrow($result) )
		{
			$row_color = ( !($i % 2) ) ? $theme['td_color1'] : $theme['td_color2'];
			$row_class = ( !($i % 2) ) ? $theme['td_class1'] : $theme['td_class2'];

			$template->assign_block_vars('vehiclerow', array(
				'ROW_COLOR' => '#' . $row_color,
				'ROW_CLASS' => $row_class,
				'U_VIEW_VEHICLE' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=" .$data['id'].""),
				'U_VIEW_PROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=" .$data['user_id']. ""),
				'U_VIEW_BUSINESS' => append_sid("garage.$phpEx?mode=view_insurance_business&amp;business_id=" .$data['business_id']. ""),
				'VEHICLE' => $data['vehicle'],
				'USERNAME' => $data['username'],
				'BUSINESS' => $data['title'],
				'PRICE' => $data['price'],
				'MOD_PRICE' => $data['total_spent'],
				'PREMIUM' => $data['premium'],
				'COVER_TYPE' => $data['cover_type'])
			);
		}
		$db->sql_freeresult($result);

		$sql = "SELECT count(i.id) as total  
        		FROM " . GARAGE_INSURANCE_TABLE . " AS i
                    		LEFT JOIN " . GARAGE_TABLE . " AS g ON i.garage_id = g.id
	                    	LEFT JOIN " . GARAGE_BUSINESS_TABLE . " AS b ON i.business_id = b.id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
			        LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id 
			WHERE makes.pending = 0 AND models.pending = 0
				".$search_data['where'];

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Counting Total Vehicles', '', __LINE__, __FILE__, $sql);
		}

		$count = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$pagination = generate_pagination("garage.$phpEx?mode=search_insurance&amp;make_id=".$search_data['make_id']."&amp;model_id=".$search_data['model_id']."&amp;sort=$sort&amp;order=$sort_order", $count['total'], $garage_config['cars_per_page'], $start). '&nbsp;';

		$template->assign_vars(array(
			'PAGINATION' 	=> $pagination,
			'PAGE_NUMBER' 	=> sprintf($lang['Page_of'], ( floor( $start / $garage_config['cars_per_page'] ) + 1 ), ceil( $count['total'] / $garage_config['cars_per_page'] )), 
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
			'S_SORT_SELECT' => $select_sort,
			'S_MODE_ACTION' => append_sid("garage.$phpEx?mode=search_insurance"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	//Display Search Options Page...
	case 'search':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('BROWSE',"garage.$phpEx?mode=error&EID=15");

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'javascript' => 'garage_vehicle_select_javascript.tpl',
			'body'   => 'garage_search.tpl')
		);

		//Build All Required Javascript And Arrays
		$template->assign_vars(array(
			'VEHICLE_ARRAY' => $garage_lib->build_vehicle_javascript())
		);
		$template->assign_var_from_handle('JAVASCRIPT', 'javascript');

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
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'view_vehicle':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('BROWSE',"garage.$phpEx?mode=error&EID=15");

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_view_vehicle.tpl')
		);

		//Pre Build All Side Menus
		$garage_lib->display_vehicle('NO');

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();
      		$template->pparse('body'); 

		$garage_lib->update_view_count(GARAGE_TABLE, 'views', 'id', $cid);

		break;

	case 'view_modification':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('BROWSE',"garage.$phpEx?mode=error&EID=15");

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_view_modification.tpl')
		);

		//Pull Required Data From DB
		$data = $garage_lib->select_modification_data($mid);

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

		if ( ($data['attach_id']) AND ($data['attach_is_image']) AND (!empty($data['attach_thumb_location'])) AND (!empty($data['attach_location'])) )
		{
			// Form the image link
			$thumb_image = GARAGE_UPLOAD_PATH . $data['attach_thumb_location'];
			$data['modification_image'] = '<a href="garage.'.$phpEx.'?mode=view_gallery_item&amp;type=garage_mod&amp;image_id='. $data['attach_id'] .'" title="' . $data['attach_file'] .'" target="_blank"><img hspace="5" vspace="5" src="' . $thumb_image .'" /></a>';
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
			'U_VIEW_PROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=".$data['member_id'].""),
			'U_LEVEL1' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=$cid"),
			'U_VIEW_GARAGE_BUSINESS' => append_sid("garage.$phpEx?mode=view_garage_business&amp;business_id=".$data['install_business_id'].""),
			'U_VIEW_SHOP_BUSINESS' => append_sid("garage.$phpEx?mode=view_shop_business&amp;business_id=".$data['business_id'].""),
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
		$garage_lib->build_sidemenu_html();
      		$template->pparse('body'); 

		break;

	case 'view_own_vehicle':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_view_vehicle.tpl')
		);

		$garage_lib->display_vehicle('YES');

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();
      		$template->pparse('body'); 

		break;

	case 'moderate_vehicle':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_view_vehicle.tpl')
		);

		$garage_lib->display_vehicle('YES');

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();
      		$template->pparse('body'); 

		break;

	case 'set_main':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		//Get Posted Data If We Got Here From Moderating
		$int_params = array('user_id');
		$data = $garage_lib->process_int_vars($int_params);

		//Now We Update All Vehicles They Own To Not Main Vehicle
		if (!empty($data['user_id']))
		{
			$garage_lib->update_single_field(GARAGE_TABLE,'main_vehicle',0,'member_id',$data['user_id']);
		}
		else
		{
			$garage_lib->update_single_field(GARAGE_TABLE,'main_vehicle',0,'member_id',$userdata['user_id']);
		}

		//Now We Update This Vehicle To The Main Vehicle
		$garage_lib->update_single_field(GARAGE_TABLE,'main_vehicle',1,'id',$cid);

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	case 'insert_gallery_image':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('UPLOAD',"garage.$phpEx?mode=error&EID=16");

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		//Pull Vehicle Data So We Can Check For Hilite Image
		$data = $garage_lib->select_vehicle_data($cid);

		//Pull Gallery Data From DB
		$gallery_data = $garage_lib->select_gallery_data($cid);

		if( (isset($HTTP_POST_FILES['FILE_UPLOAD'])) AND ($HTTP_POST_FILES['FILE_UPLOAD']['name']) OR (!preg_match("/^http:\/\/$/i", $HTTP_POST_VARS['url_image'])) )
		{
			if ( count($gallery_data) < $garage_config['max_car_images'])
			{
				$image_id = $garage_lib->process_image_attach('vehicle',$cid);
				if (!empty($image_id))
				{
					$garage_lib->insert_gallery_image($image_id);
					// Check If First Image And Set As Hilite If So
					if ( empty($data['image_id']))
					{
						$garage_lib->update_single_field(GARAGE_TABLE,'image_id',$image_id,'id',$cid);
					}
				}
			}
			else if ( count($gallery_data) >= $garage_config['max_car_images'])
			{
				redirect(append_sid("garage.$phpEx?mode=error&EID=4", true));
			}
		}

		$garage_lib->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=manage_vehicle_gallery&CID=$cid", true));

		break;

	case 'view_gallery_item':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('BROWSE',"garage.$phpEx?mode=error&EID=15");

		//Increment View Counter For This Image
		$garage_lib->update_view_count(GARAGE_IMAGES_TABLE, 'attach_hits', 'attach_id', $image_id);

		//Pull Required Data From DB
		$data = $garage_lib->select_image_data($image_id);

		//Check To See If This Is A Remote Image
		if ( preg_match( "/^http:\/\//i", $data['attach_location']) )
		{
			//Redirect Them To The Remote Image
			header("Location: ".$data['attach_location']);
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
		$garage_lib->check_permissions('UPLOAD',"garage.$phpEx?mode=error&EID=16");

		//Check Vehicle Ownership		
		$garage_lib->check_own_vehicle($cid);
		
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_manage_vehicle_gallery.tpl')
		);

		//Pre Build All Side Menus
		$garage_lib->build_attach_image_html('vehicle');

		//Pull Vehicle Data So We Can Check For Hilite Image
		$vehicle_data = $garage_lib->select_vehicle_data($cid);

		//Pull Gallery Data From DB
		$data = $garage_lib->select_gallery_data($cid);

		for ($i = 0; $i < count($data); $i++)
      		{
			if ( $data[$i]['image_id'] == $vehicle_data['image_id'] )
			{
				$hilite = '<i>'.$lang['Current_Hilite_Image'].'</i>';
			}
			else
			{
				$temp_url = append_sid("garage.$phpEx?mode=set_hilite&amp;image_id=". $data[$i]['image_id'] ."&amp;CID=". $cid ."", true);
				$hilite= '<a href="'.$temp_url.'">' .$lang['Set_Hilite_Image']. "</a>";
			}

			if ( ($data[$i]['image_id']) AND ($data[$i]['attach_is_image']) AND (!empty($data[$i]['attach_thumb_location'])) AND (!empty($data[$i]['attach_location'])) )
			{
				// Form the image link
				$thumb_image = $phpbb_root_path . GARAGE_UPLOAD_PATH . $data[$i]['attach_thumb_location'];
				$image = '<a href="garage.'.$phpEx.'?mode=view_gallery_item&amp;type=garage_mod&amp;image_id='. $data[$i]['image_id'] .'" title="' . $data[$i]['attach_file'] .'" target="_blank"><img hspace="5" vspace="5" src="' . $thumb_image .'" /></a>';
			}

			$template->assign_block_vars('pic_row', array(
				'THUMB_IMAGE' => $image,
				'U_REMOVE_IMAGE' => append_sid("garage.$phpEx?mode=remove_gallery_item&amp;&amp;CID=$cid&amp;image_id=". $data[$i]['image_id'].""),
				'HILITE' => $hilite)
			);
		}
		$db->sql_freeresult($result);

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

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();		
		$template->pparse('body');

		break;

	case 'set_hilite':

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		$garage_lib->update_single_field(GARAGE_TABLE,'image_id',$image_id,'id',$cid);

		$garage_lib->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=view_own_vehicle&CID=$cid", true));

		break;

	case 'remove_gallery_item':

		//Check Vehicle Ownership
		$garage_lib->check_own_vehicle($cid);

		$garage_lib->delete_gallery_image($image_id);

		$garage_lib->update_vehicle_time($cid);

		redirect(append_sid("garage.$phpEx?mode=manage_vehicle_gallery&CID=$cid", true));

		break;

	case 'view_insurance_business':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('BROWSE',"garage.$phpEx?mode=error&EID=15");

		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_view_insurance_business.tpl')
		);

		//Let See If We Are Only Going To Display A Specific Business
		$single_business = (isset($HTTP_GET_VARS['business_id'])) ?  intval($HTTP_GET_VARS['business_id']): '';

		$start = (isset($HTTP_GET_VARS['start'])) ? intval($HTTP_GET_VARS['start']) : 0;

		if (empty($single_business))
		{	
			$limit = 1;
		}
		else
		{
			$where = "AND b.id = $single_business";
			$limit = 20;
		}

		// Select Each Business
      		$sql = "SELECT * 
       	 		FROM  " . GARAGE_BUSINESS_TABLE . " b 
       			WHERE b.insurance = 1
				AND b.pending = 0
				$where
			LIMIT $start, 25";

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Select Business Data', '', __LINE__, __FILE__, $sql);
      		}

		if ( $db->sql_numrows($result) < 1 )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=1", true));
		}

		while( $row = $db->sql_fetchrow($result) )
		{
			$business[] = $row;
		}
      		$db->sql_freeresult($result);

		if (!empty($single_business))
		{
			$template->assign_block_vars('level2', array());
			$template->assign_vars(array(
				'U_LEVEL2' => append_sid("garage.$phpEx?mode=view_insurance_business&amp;business_id=".$business[0]['id'].""),
				'L_LEVEL2' => $business[0]['title'])
			);
		}

      		//Loop Processing All Business's Returned From First Select Statement (now in array)
		for ($i = 0; $i < count($business); $i++)
      		{
         		//Setup cat_row Template Varibles
         		$template->assign_block_vars('business_row', array(
            			'U_VIEW_BUSINESS' => append_sid("garage.$phpEx?mode=view_insurance_business&amp;business_id=".$business[$i]['id'].""),
            			'NAME' => $business[$i]['title'],
	            		'ADDRESS' => $business[$i]['address'],
        	    		'TELEPHONE' => $business[$i]['telephone'],
            			'FAX' => $business[$i]['fax'],
            			'WEBSITE' => $business[$i]['website'],
	            		'EMAIL' => $business[$i]['email'],
				'OPENING_HOURS' => $business[$i]['opening_hours'])
         		);

			if (empty($single_business))
			{
        	 		$template->assign_block_vars('business_row.more_detail', array());
			}
			else
			{
				$template->assign_block_vars('business_row.insurance_detail', array());
			}

			//Now we loop through all insurance types...
			for($j = 0; $j < count($cover_types); $j++)
			{
				$sql = "SELECT round(max( i.premium ),2) AS max, round(min( i.premium ),2) AS min, round(avg( i.premium ),2) AS avg
					FROM " . GARAGE_BUSINESS_TABLE . " b, " . GARAGE_INSURANCE_TABLE . " i
					WHERE i.business_id = b.id
						AND b.id = '" . $business[$i]['id'] . "' 
						AND b.insurance =1
						AND i.cover_type = '$cover_types[$j]'
						AND i.premium > 0";

         			if( !($result = $db->sql_query($sql)) )
         			{
		            		message_die(GENERAL_ERROR, 'Could Not Select Business', '', __LINE__, __FILE__, $sql);
         			}

         			//Loop Processing Values From SQL...man are we loopy or what!!!!
	         		while ( $cover_row = $db->sql_fetchrow($result) )
        	 		{
            				$minimum = $cover_row['min'];
            				$average = $cover_row['avg'];
            				$maximum = $cover_row['max'];

	            			//Setup user_row Template Varibles
        	    			$template->assign_block_vars('business_row.cover_row', array(
               					'COVER_TYPE' => $cover_types[$j],
               					'MINIMUM' => $minimum,
               					'AVERAGE' => $average,
               					'MAXIMUM' => $maximum)
	            			);
         			}// end WHILE
	         		$db->sql_freeresult($result);
			}//end FOR Loop - Insurance Types

			//Pull All Insurance Data Into A Large Array
			$sql = "SELECT i.*, g.made_year, b.title, b.id as business_id, makes.make, models.model, user.username, user.user_id
        			FROM " . GARAGE_INSURANCE_TABLE . " AS i 
                	    		LEFT JOIN " . GARAGE_TABLE . " AS g ON i.garage_id = g.id
        	        	    	LEFT JOIN " . GARAGE_BUSINESS_TABLE . " AS b ON i.business_id = b.id
			        	LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
			        	LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id 
				        LEFT JOIN " . USERS_TABLE . " AS user ON g.member_id = user.user_id 
				WHERE i.business_id = b.id
					AND b.insurance =1
					AND b.pending = 0
					AND b.id = " . $business[$i]['id'] . "
					AND makes.pending = 0 AND models.pending = 0 
				GROUP BY i.id";
		   	if ( !($result = $db->sql_query($sql)) )
      			{
         			message_die(GENERAL_ERROR, 'Could Select Business Data', '', __LINE__, __FILE__, $sql);
      			}

			$matched = 1;
			if  (!empty($single_business))
			{
				while( $insurance_data = $db->sql_fetchrow($result) )
				{
					// setup user row template varibles
					$template->assign_block_vars('business_row.insurance_detail.premiums', array(
						'USERNAME' => $insurance_data['username'],
						'VEHICLE' => $insurance_data['made_year'] . ' ' . $insurance_data['make'] . ' ' . $insurance_data['model'],
						'U_VIEW_PROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=" . $insurance_data['user_id'] . ""),
						'U_VIEW_VEHICLE' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=" . $insurance_data['garage_id'] .""),
						'PREMIUM' => $insurance_data['premium'],
						'COVER_TYPE' => $insurance_data['cover_type'])
					);
				}//end FOR Loop - Insurance Premiums
				$db->sql_freeresult($result);
			}
      		}// end FOR of outer loop - Business's

		$sql = "SELECT count(DISTINCT b.id) as total
			FROM " . GARAGE_BUSINESS_TABLE . " b
			WHERE b.insurance =1
				AND b.pending =0
				$where";

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Getting Pagination Total', '', __LINE__, __FILE__, $sql);
		}

		$count = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$pagination = generate_pagination("garage.$phpEx?mode=view_insurance_business", $count['total'], 25, $start). '&nbsp;';

		$template->assign_block_vars('level1', array());
		$template->assign_vars(array(
			'PAGINATION' => $pagination,
			'PAGE_NUMBER' => sprintf($lang['Page_of'], (floor( $start / 25) + 1), ceil($count['total'] / 25 )), 
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

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'view_garage_business':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('BROWSE',"garage.$phpEx?mode=error&EID=15");

		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body' => 'garage_view_garage_business.tpl')
		);

		//Let See If We Are Only Going To Display A Specific Business
		$single_business = (isset($HTTP_GET_VARS['business_id'])) ?  intval($HTTP_GET_VARS['business_id']): '';

		$start = (isset($HTTP_GET_VARS['start'])) ? intval($HTTP_GET_VARS['start']) : 0;

		if (empty($single_business))
		{
			$limit = '5';
		}
		else
		{
			$limit = '20';
			$where = "AND b.id = $single_business";
		}

		$sql = "SELECT b.* , sum( install_rating ) AS rating, count( * ) *10 AS total_rating
			FROM " . GARAGE_BUSINESS_TABLE . " b, " . GARAGE_MODS_TABLE . " m
			WHERE m.install_business_id = b.id
				AND b.garage =1
				AND b.pending =0
				$where
			GROUP BY b.id
			ORDER BY rating DESC
			LIMIT $start, 25";
			
      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Select Business Data', '', __LINE__, __FILE__, $sql);
      		}

		if ( $db->sql_numrows($result) < 1 )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=1", true));
		}

		while( $row = $db->sql_fetchrow($result) )
		{
			$business[] = $row;
		}
		$db->sql_freeresult($result);

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		if (!empty($single_business))
		{
			$template->assign_block_vars('level2', array());
			$template->assign_vars(array(
				'U_LEVEL2' => append_sid("garage.$phpEx?mode=view_garage_business&amp;business_id=".$business[0]['id'].""),
				'L_LEVEL2' => $business[0]['title'])
			);
		}

      		//Loop Processing All Categoires Returned From First Select Statement (now in a
      		for ($i = 0; $i < count($business); $i++)
      		{
			if (empty($business[$i]['rating']))
			{
				$business[$i]['rating'] = '0';
			}

			//Setup cat_row Template Varibles
         		$template->assign_block_vars('business_row', array(
				'U_VIEW_BUSINESS' => append_sid("garage.$phpEx?mode=view_garage_business&amp;business_id=".$business[$i]['id'].""),
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
			
			if (empty($single_business))
			{
         			$template->assign_block_vars('business_row.more_detail', array());
			}

			//Now Lets Go Get All Mods All Business's Have Installed
 			$sql = "SELECT mods.id, mods.garage_id, mods.title AS mod_title, mods.install_price, mods.install_rating, mods.install_comments, u.username, u.user_id, makes.make, models.model, g.made_year, b.id as business_id
	               		FROM ( " . GARAGE_MODS_TABLE . " AS mods, " . GARAGE_BUSINESS_TABLE . " AS b )
	    				LEFT JOIN " . GARAGE_TABLE . " AS g ON mods.garage_id = g.id
			    		LEFT JOIN " . USERS_TABLE . " AS u ON mods.member_id = u.user_id
		        		LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
        				LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
				WHERE mods.install_business_id = b.id
					AND b.garage =1
					AND b.pending = 0
					AND b.id = " . $business[$i]['id'] . "
					AND makes.pending = 0 AND models.pending = 0
				ORDER BY mods.id, mods.date_created DESC
				LIMIT $limit";

			$matched = 1;

			if ( !($result = $db->sql_query($sql)) )
      			{
         			message_die(GENERAL_ERROR, 'Could Select Business Data', '', __LINE__, __FILE__, $sql);
      			}


			while( $bus_mod_data = $db->sql_fetchrow($result) )
			{
				// setup user row template varibles
				$template->assign_block_vars('business_row.mod_row', array(
					'USERNAME' => $bus_mod_data['username'],
					'VEHICLE' => $bus_mod_data['made_year'] . ' ' . $bus_mod_data['make'] . ' ' . $bus_mod_data['model'],
					'U_VIEW_PROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=" . $bus_mod_data['user_id'] . ""),
					'U_VIEW_VEHICLE' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=" . $bus_mod_data['garage_id'] .""),
					'U_VIEW_MODIFICATION' => append_sid("garage.$phpEx?mode=view_modification&amp;CID=" . $bus_mod_data['garage_id'] ."&amp;MID=" . $bus_mod_data['id'] .""),
					'MODIFICATION' => $bus_mod_data['mod_title'],
					'INSTALL_RATING' => $bus_mod_data['install_rating'])
				);
					
				if (!empty($business_mod_data['install_comments']))
				{
					if ( $comments != 'SET')
					{
						$template->assign_block_vars('business_row.comments', array());
					}
					$comments = 'SET';
					$template->assign_block_vars('business_row.customer_comments', array(
						'COMMENTS' => $business_mod_data['username'] . ' -> ' .$business_mod_data['install_comments'])
					);
				}

				//Increment Number Of Mods We Have Listed For This Business
				$matched++;
			}
			$comments = '';
			$db->sql_freeresult($result);
      		}// end FOR of outer loop

		$sql = "SELECT count(DISTINCT b.title) as total
			FROM " . GARAGE_BUSINESS_TABLE . " b, " . GARAGE_MODS_TABLE . " m
			WHERE m.install_business_id = b.id
				AND b.garage =1
				AND b.pending =0
				$where";

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Getting Pagination Total', '', __LINE__, __FILE__, $sql);
		}

		$count = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$pagination = generate_pagination("garage.$phpEx?mode=view_garage_business", $count['total'], 25, $start). '&nbsp;';

		$template->assign_block_vars('level1', array());
		$template->assign_vars(array(
			'PAGINATION' => $pagination,
			'PAGE_NUMBER' => sprintf($lang['Page_of'], (floor($start / 25) + 1), ceil($count['total'] / 25)), 
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
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'view_shop_business':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('BROWSE',"garage.$phpEx?mode=error&EID=15");

		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body' => 'garage_view_shop_business.tpl')
		);

		//Let See If We Are Only Going To Display A Specific Business
		$single_business = (isset($HTTP_GET_VARS['business_id'])) ?  intval($HTTP_GET_VARS['business_id']): '';

		$start = (isset($HTTP_GET_VARS['start'])) ? intval($HTTP_GET_VARS['start']) : 0;

		if (empty($single_business))
		{
			$limit = '5';
		}
		else
		{
			$limit = '20';
			$where = "AND b.id = $single_business";
		}

		$sql = "SELECT b.* , sum( product_rating ) AS rating, count( * ) *10 AS total_rating
			FROM " . GARAGE_BUSINESS_TABLE . " b, " . GARAGE_MODS_TABLE . " m
			WHERE m.business_id = b.id
				AND ( b.web_shop =1 OR b.retail_shop = 1 )
				AND b.pending =0
				$where
			GROUP BY b.id
			ORDER BY rating DESC
			LIMIT $start, 25";
			
      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Select Business Data', '', __LINE__, __FILE__, $sql);
      		}

		if ( $db->sql_numrows($result) < 1 )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=1", true));
		}

		while( $row = $db->sql_fetchrow($result) )
		{
			$business[] = $row;
		}
		$db->sql_freeresult($result);

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		if (!empty($single_business))
		{
			$template->assign_block_vars('level2', array());
			$template->assign_vars(array(
				'U_LEVEL2' => append_sid("garage.$phpEx?mode=view_shop_business&amp;business_id=".$business[0]['id'].""),
				'L_LEVEL2' => $business[0]['title'])
			);
		}

      		//Loop Processing All Categoires Returned From First Select Statement (now in a
      		for ($i = 0; $i < count($business); $i++)
      		{
			if (empty($business[$i]['rating']))
			{
				$business[$i]['rating'] = '0';
			}

			//Setup cat_row Template Varibles
         		$template->assign_block_vars('business_row', array(
				'U_VIEW_BUSINESS' => append_sid("garage.$phpEx?mode=view_shop_business&amp;business_id=".$business[$i]['id'].""),
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
			
			if (empty($single_business))
			{
         			$template->assign_block_vars('business_row.more_detail', array());
			}

			//Now Lets Go Get All Mods All Business's Have Installed
 			$sql = "SELECT mods.id, mods.garage_id, mods.title AS mod_title, mods.price, mods.product_rating, mods.comments, u.username, u.user_id, makes.make, models.model, g.made_year, b.id as business_id
	               		FROM ( " . GARAGE_MODS_TABLE . " AS mods, " . GARAGE_BUSINESS_TABLE . " AS b )
	    				LEFT JOIN " . GARAGE_TABLE . " AS g ON mods.garage_id = g.id
			    		LEFT JOIN " . USERS_TABLE . " AS u ON mods.member_id = u.user_id
		        		LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
        				LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
				WHERE mods.business_id = b.id
					AND ( b.web_shop =1 OR b.retail_shop =1 )
					AND b.pending = 0
					AND b.id = " . $business[$i]['id'] . "
					AND makes.pending = 0 AND models.pending = 0 
				ORDER BY mods.id, mods.date_created DESC
				LIMIT $limit";

			$matched = 1;

			if ( !($result = $db->sql_query($sql)) )
      			{
         			message_die(GENERAL_ERROR, 'Could Select Business Data', '', __LINE__, __FILE__, $sql);
      			}


			while( $bus_mod_data = $db->sql_fetchrow($result) )
			{
				// setup user row template varibles
				$template->assign_block_vars('business_row.mod_row', array(
					'USERNAME' => $bus_mod_data['username'],
					'VEHICLE' => $bus_mod_data['made_year'] . ' ' . $bus_mod_data['make'] . ' ' . $bus_mod_data['model'],
					'U_VIEW_PROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=" . $bus_mod_data['user_id'] . ""),
					'U_VIEW_VEHICLE' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=" . $bus_mod_data['garage_id'] .""),
					'U_VIEW_MODIFICATION' => append_sid("garage.$phpEx?mode=view_modification&amp;CID=" . $bus_mod_data['garage_id'] ."&amp;MID=" . $bus_mod_data['id'] .""),
					'MODIFICATION' => $bus_mod_data['mod_title'],
					'RATING' => $bus_mod_data['product_rating'],
					'PRICE' => $bus_mod_data['price'])
				);
					
				if (!empty($bus_mod_data['comments']))
				{
					if ( $comments != 'SET')
					{
						$template->assign_block_vars('business_row.comments', array());
					}
					$comments = 'SET';
					$template->assign_block_vars('business_row.customer_comments', array(
						'COMMENTS' => $bus_mod_data['username'] . ' -> ' .$bus_mod_data['comments'])
					);
				}

				//Increment Number Of Mods We Have Listed For This Business
				$matched++;
			}
			$comments = '';
			$db->sql_freeresult($result);
      		}// end FOR of outer loop

		$sql = "SELECT count(DISTINCT b.title) as total
			FROM " . GARAGE_BUSINESS_TABLE . " b, " . GARAGE_MODS_TABLE . " m
			WHERE m.business_id = b.id
				AND ( b.web_shop =1 OR b.retail_shop =1 )
				AND b.pending =0
				$where";

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Getting Pagination Total', '', __LINE__, __FILE__, $sql);
		}

		$count = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$pagination = generate_pagination("garage.$phpEx?mode=view_shop_business", $count['total'], 25, $start). '&nbsp;';

		$template->assign_block_vars('level1', array());
		$template->assign_vars(array(
			'PAGINATION' => $pagination,
			'PAGE_NUMBER' => sprintf($lang['Page_of'], (floor($start / 25) + 1), ceil($count['total'] / 25)), 
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
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'user_submit_business':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=user_submit_business", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_user_submit.tpl')
		);

		//Get All Data Posted And Make It Safe To Use
		$str_params = array('BUSINESS', 'TYPE');
		$data = $garage_lib->process_str_vars($str_params);
		$data['insurance'] = ($data['BUSINESS'] == 'insurance') ? 'checked="checked"' : '' ;
		$data['garage'] = ($data['BUSINESS'] == 'garage') ? 'checked="checked"' : '' ;
		$data['retail_shop'] = ($data['BUSINESS'] == 'shop') ? 'checked="checked"' : '' ;
		$data['web_shop'] = ($data['BUSINESS'] == 'shop') ? 'checked="checked"' : '' ;

		$template->assign_vars(array(
			'S_MODE_ACTION' 	=> append_sid("garage.$phpEx?mode=user_insert_business"),
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
			'TYPE' 			=> $data['TYPE'])
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'user_insert_business':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Get All Data Posted And Make It Safe To Use
		$str_params = array('TYPE', 'name', 'address', 'telephone', 'fax', 'website', 'email', 'opening_hours', 'insurance', 'garage', 'retail_shop', 'web_shop');
		$data = $garage_lib->process_str_vars($str_params);
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
		$garage_lib->check_required_vars($params);

		//Update The DB With Data Acquired
		$garage_lib->insert_business($data);

		$mode_redirect = $data['TYPE'];

		redirect(append_sid("garage.$phpEx?mode=$mode_redirect&CID=$cid", true));

		break;

	case 'edit_business':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_user_submit.tpl')
		);

		//Pull Required Data From DB
		$data = $garage_lib->select_business_data($bus_id);
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
			'BUS_ID'		=> $data['id'])
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'update_business':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('BUS_ID');
		$int_data = $garage_lib->process_int_vars($int_params);
		$str_params = array('name', 'address', 'telephone', 'fax', 'website', 'email', 'opening_hours', 'insurance', 'garage', 'retail_shop', 'web_shop');
		$str_data = $garage_lib->process_str_vars($str_params);
		$data = $this->merge_int_str_data($int_data, $str_data);
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
		$garage_lib->check_required_vars($params);

		//Update The DB With Data Acquired
		$garage_lib->update_business($data);

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
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_user_submit_make.tpl')
		);

		$template->assign_vars(array(
			'L_ADD_MAKE' 			=> $lang['Add_Make'],
			'L_ADD_MAKE_BUTTON' 		=> $lang['Add_Make_Button'],
			'L_VEHICLE_MAKE' 		=> $lang['Vehicle_Make'],
			'S_GARAGE_MODELS_ACTION' 	=> append_sid('admin_garage_models.'.$phpEx))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'add_make':

		//User Is Annoymous...So Not Allowed To Create A Vehicle
		if ( $userdata['user_id'] == -1 )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=2", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Get All Data Posted And Make It Safe To Use
		$str_params = array('make');
		$data = $garage_lib->process_str_vars($str_params);

		//Checks All Required Data Is Present
		$params = array('make');
		$garage_lib->check_required_vars($params);

		//Update The DB With Data Acquired
		$garage_lib->insert_make($data);

		redirect(append_sid("garage.$phpEx?mode=create_vehicle&MAKE=".$data['make']."", true));

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
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_user_submit_model.tpl')
		);

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('MAKE_ID');
		$data = $garage_lib->process_int_vars($int_params);

		//Checks All Required Data Is Present
		$params = array('MAKE_ID');
		$garage_lib->check_required_vars($params);

		//Pull Required Data From DB
		$data = $garage_lib->select_make_data($data['MAKE_ID']);

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
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'add_model':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=user_submit_model", true));
		}

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('ADD',"garage.$phpEx?mode=error&EID=14");

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('make_id');
		$int_data = $garage_lib->process_int_vars($int_params);
		$str_params = array('make', 'model');
		$str_data = $garage_lib->process_str_vars($str_params);
		$data = $this->merge_int_str_data($int_data, $str_data);

		//Checks All Required Data Is Present
		$params = array('make', 'make_id', 'model');
		$garage_lib->check_required_vars($params);

		//Update The DB With Data Acquired
		$garage_lib->insert_model($data);

		redirect(append_sid("garage.$phpEx?mode=create_vehicle&MAKE=".$data['make']."&MODEL=".$data['model']."", true));

		break;

	case 'quartermile':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('BROWSE',"garage.$phpEx?mode=error&EID=15");

		$page_title = $lang['Car_Quart'];
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'javascript' => 'garage_vehicle_select_javascript.tpl',
			'body' => 'garage_quartermile_table.tpl')
		);

		//Build All Required Javascript And Arrays
		$template->assign_vars(array(
			'VEHICLE_ARRAY' => $garage_lib->build_vehicle_javascript())
		);
		$template->assign_var_from_handle('JAVASCRIPT', 'javascript');

		make_jumpbox('viewforum.'.$phpEx);
		$garage_lib->build_sort_order_html($sort_order);

		//Build Actual Table With No Pending Runs
		$garage_lib->build_quartermile_table('NO');

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
		if ($userdata['user_level'] == REGULAR )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=13", true));
		}

		//Generate Page Header
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_pending.tpl')
		);

		//Build The Quartermile Table With Only Pending Times
		$garage_lib->build_quartermile_table('YES');

		//Build The Rollingroad Table With Only Pending Runs
		$garage_lib->build_rollingroad_table('YES');

		//Build The Business Table With Only Pending Ones
		$garage_lib->build_business_table('YES');

		//Build The Make Table With Only Pending Ones
		$garage_lib->build_make_table('YES');

		//Build The Model Table With Only Pending Ones
		$garage_lib->build_model_table('YES');

		//Set Up Template Varibles
		$template->assign_block_vars('level1', array());
		$template->assign_vars(array(
			'U_LEVEL1' => append_sid($phpbb_root_path . 'garage.'.$phpEx.'?mode=garage_pending'),
			'L_QUARTERMILE_PENDING' => $lang['Quartermile_Pending'],
			'L_ROLLINGROAD_PENDING' => $lang['Rollingroad_Pending'],
			'L_BUSINESS_PENDING' => $lang['Business_Pending'],
			'L_MAKE_PENDING' => $lang['Make_Pending'],
			'L_MODEL_PENDING' => $lang['Model_Pending'],
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
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'garage_approval':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=garage.$phpEx&mode=quartermile_pending", true));
		}

		//Check The User Is Allowed To View This Page...If Not Send Them On There Way Nicely
		if ($userdata['user_level'] == REGULAR )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=13", true));
		}

		//Get All Data Posted And Make It Safe To Use
		$str_params = array('action');
		$data = $garage_lib->process_str_vars($str_params);

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

			$data = $garage_lib->select_business_data($bus_id);

			//Generate Page Header
			include($phpbb_root_path . 'includes/page_header.'.$phpEx);

			$template->set_filenames(array(
				'header' => 'garage_header.tpl',
				'body'   => 'garage_reassign_business.tpl')
			);

			//Build Dropdown Box Of Business's To Reassign It To
			$garage_lib->build_reassign_business_list($bus_id);

			//Set Up Template Varibles
			$template->assign_block_vars('level1', array());
			$template->assign_vars(array(
				'S_MODE_ACTION'	=> append_sid("garage.$phpEx?mode=reassign_business"),
				'U_LEVEL1' => append_sid($phpbb_root_path . 'garage.'.$phpEx.'?mode=garage_pending'),
				'L_LEVEL1'=> $lang['Reassign_Business'],
				'L_RESSIGN_BUSINESS' => $lang['Reassign_Business'],
				'L_BUSINESS_DELETED' => $lang['Business_Deleted'],
				'L_REASSIGN_TO' => $lang['Reassign_To'],
				'L_REASSIGN_BUTTON' => $lang['Reassign_Button'],
				'NAME' => $data['title'],
				'BUS_ID' => $data['id'])
			);

			//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
			$template->pparse('header');
			$garage_lib->build_sidemenu_html();
			$template->pparse('body');

			break;
		}

		//Process Each Pending Type For Updates
		while( list($ids, $table) = @each($params) )
		{
			if ( !empty($HTTP_POST_VARS[$ids]) )
			{
				$pending_ids = $HTTP_POST_VARS[$ids];
				$i = 0;

				if ( $data['action'] == 'REMOVE' )
				{
					while( $i < count($pending_ids) )
					{
						$id = intval($pending_ids[$i]);

						//If Quartermile Need To Call Correct Function To Delete Images Too
						if ( $table ==  GARAGE_QUARTERMILE_TABLE)
						{
							$garage_lib->delete_quartermile_time($id);
						}
						//If Rollingroad Need To Call Correct Function To Delete Images Too
						else if  ( $table ==  GARAGE_ROLLINGROAD_TABLE)
						{
							$garage_lib->delete_rollingroad_run($id);
						}
						else
						{
							$garage_lib->delete_rows($table,'id',$id);
						}
						$i++;
					}
				}
				else if ( $data['action'] == 'APPROVE' )
				{
					while( $i < count($pending_ids) )
					{
						$id = intval($pending_ids[$i]);
						$garage_lib->update_single_field($table,'pending',0,'id',$id);
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
		if ($userdata['user_level'] == REGULAR )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=13", true));
		}

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('BUS_ID', 'id');
		$data = $garage_lib->process_int_vars($int_params);

		//Checks All Required Data Is Present
		$params = array('BUS_ID', 'id');
		$garage_lib->check_required_vars($params);

		//Lets Update All Possible Business Fields With The Reassigned Business
		$garage_lib->update_single_field(GARAGE_MODS_TABLE,'business_id',$data['id'],'business_id',$data['BUS_ID']);
		$garage_lib->update_single_field(GARAGE_MODS_TABLE,'install_business_id',$data['id'],'install_business_id',$data['BUS_ID']);
		$garage_lib->update_single_field(GARAGE_INSURANCE_TABLE,'business_id',$data['id'],'business_id',$data['BUS_ID']);

		//Since We Have Updated All Item Lets Do The Original Delete Now
		$garage_lib->delete_rows(GARAGE_BUSINESS_TABLE,'id',$data['BUS_ID']);

		redirect(append_sid("garage.$phpEx?mode=garage_pending", true));

		break;

	case 'rollingroad':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('BROWSE',"garage.$phpEx?mode=error&EID=15");

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'javascript' => 'garage_vehicle_select_javascript.tpl',
			'body' => 'garage_rollingroad_table.tpl')
		);

		//Build All Required Javascript And Arrays
		$template->assign_vars(array(
			'VEHICLE_ARRAY' => $garage_lib->build_vehicle_javascript())
		);
		$template->assign_var_from_handle('JAVASCRIPT', 'javascript');

		make_jumpbox('viewforum.'.$phpEx);
		$garage_lib->build_sort_order_html($sort_order);

		$garage_lib->build_rollingroad_table('NO');

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
		$garage_lib->check_permissions('BROWSE',"garage.$phpEx?mode=error&EID=15");

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_view_guestbook.tpl')
		);

		//Get Vehicle Info
		$data = $garage_lib->select_vehicle_data($cid);

		$u_vehicle = append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=$cid");

		// Get Guestbook Entries
        	$sql = "SELECT gb.id as comment_id, gb.post, gb.author_id, gb.post_date, gb.ip_address,
				u.username, u.user_id, u.user_posts, u.user_from, u.user_website, u.user_email, u.user_icq,
			       	u.user_aim, u.user_yim, u.user_regdate, u.user_msnm, u.user_viewemail, u.user_rank,
			       	u.user_sig, u.user_sig_bbcode_uid, u.user_avatar, u.user_avatar_type, u.user_allowavatar,
			       	u.user_allowsmile, u.user_allow_viewonline, u.user_session_time,
				g.made_year, g.id as garage_id, makes.make, models.model
                        FROM " . GARAGE_GUESTBOOKS_TABLE . " AS gb 
                        	LEFT JOIN " . USERS_TABLE . " AS u ON gb.author_id = u.user_id 
				LEFT JOIN " . GARAGE_TABLE ." AS g on g.member_id = gb.author_id and g.main_vehicle = 1
       				LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
                		LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
                        WHERE gb.garage_id = $cid
                        ORDER BY gb.post_date ASC";

              	if( !($result = $db->sql_query($sql)) )
       		{
          		message_die(GENERAL_ERROR, 'Could Not Select Guestbook Data', '', __LINE__, __FILE__, $sql);
       		}

		if ($garage_lib->check_permissions('INTERACT',''))
		{
			$template->assign_block_vars('leave_comment', array());
		}

         	//Loop Processing All Mods Returned From Second Statements
		if ( $db->sql_numrows($result) < 1 )
		{
			$template->assign_block_vars('first_comment', array());
			$template->assign_vars(array(
				'LEAVE_FIRST_COMMENT'  => $lang['Add_First_Comment'])
			);
		}
		else
		{
			while ( $row = $db->sql_fetchrow($result) )
         		{	
				$comment_id = $row['comment_id'];
				$poster_id = $row['user_id'];
				$username = $row['username'];
				$temp_url = append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=$poster_id");
				$poster = '<a href="' . $temp_url . '">' . $row['username'] . '</a>';
				$poster_posts = ( $row['user_id'] != ANONYMOUS ) ? $lang['Posts'] . ': ' . $row['user_posts'] : '';
				$poster_from = ( $row['user_from'] && $row['user_id'] != ANONYMOUS ) ? $lang['Location'] . ': ' . $row['user_from'] : '';
				$garage_id = $row['garage_id'];
				$poster_car_year = ( $row['made_year'] && $row['user_id'] != ANONYMOUS ) ? $lang[''] . ' ' . $row['made_year'] : '';
				$poster_car_mark = ( $row['make'] && $row['user_id'] != ANONYMOUS ) ? $lang[''] . ' ' . $row['make'] : '';
				$poster_car_model = ( $row['model'] && $row['user_id'] != ANONYMOUS ) ? $lang[''] . ' ' . $row['model'] : '';
				$poster_joined = ( $row['user_id'] != ANONYMOUS ) ? $lang['Joined'] . ': ' . create_date($lang['DATE_FORMAT'], $row['user_regdate'], $board_config['board_timezone']) : '';

				$poster_avatar = '';
				if ( $row['user_avatar_type'] && $poster_id != ANONYMOUS && $row['user_allowavatar'] )
				{
					switch( $row['user_avatar_type'] )
					{
						case USER_AVATAR_UPLOAD:
							$poster_avatar = ( $board_config['allow_avatar_upload'] ) ? '<img src="' . $board_config['avatar_path'] . '/' . $row['user_avatar'] . '" alt="" border="0" />' : '';
							break;
						case USER_AVATAR_REMOTE:
							$poster_avatar = ( $board_config['allow_avatar_remote'] ) ? '<img src="' . $row['user_avatar'] . '" alt="" border="0" />' : '';
							break;
						case USER_AVATAR_GALLERY:
							$poster_avatar = ( $board_config['allow_avatar_local'] ) ? '<img src="' . $board_config['avatar_gallery_path'] . '/' . $row['user_avatar'] . '" alt="" border="0" />' : '';
							break;
					}
				}

				// Generate ranks, set them to empty string initially.
				$poster_rank = '';
				$rank_image = '';
				if ( $row['user_id'] == ANONYMOUS )
				{
				}
				else if ( $row['user_rank'] )
				{
					for($j = 0; $j < count($ranksrow); $j++)
					{
						if ( $row['user_rank'] == $ranksrow[$j]['rank_id'] && $ranksrow[$j]['rank_special'] )
						{
							$poster_rank = $ranksrow[$j]['rank_title'];
							$rank_image = ( $ranksrow[$j]['rank_image'] ) ? '<img src="' . $ranksrow[$j]['rank_image'] . '" alt="' . $poster_rank . '" title="' . $poster_rank . '" border="0" /><br />' : '';
						}
					}
				}
				else
				{
					for($j = 0; $j < count($ranksrow); $j++)
					{
						if ( $row['user_posts'] >= $ranksrow[$j]['rank_min'] && !$ranksrow[$j]['rank_special'] )
						{
							$poster_rank = $ranksrow[$j]['rank_title'];
							$rank_image = ( $ranksrow[$j]['rank_image'] ) ? '<img src="' . $ranksrow[$j]['rank_image'] . '" alt="' . $poster_rank . '" title="' . $poster_rank . '" border="0" /><br />' : '';
						}
					}
				}

				// Handle anon users posting with usernames
				if ( $poster_id == ANONYMOUS && $row['post_username'] != '' )
				{
					$poster = $row['post_username'];
					$poster_rank = $lang['Guest'];
				}

				$profile_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_profile'] . '" alt="' . $lang['Read_profile'] . '" title="' . $lang['Read_profile'] . '" border="0" /></a>';
				$profile = '<a href="' . $temp_url . '">' . $lang['Read_profile'] . '</a>';

				$temp_url = append_sid("privmsg.$phpEx?mode=post&amp;" . POST_USERS_URL . "=$poster_id");
				$pm_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_pm'] . '" alt="' . $lang['Send_private_message'] . '" title="' . $lang['Send_private_message'] . '" border="0" /></a>';
				$pm = '<a href="' . $temp_url . '">' . $lang['Send_private_message'] . '</a>';

				if ( !empty($row['user_viewemail']) || $is_auth['auth_mod'] )
				{
					$email_uri = ( $board_config['board_email_form'] ) ? append_sid("profile.$phpEx?mode=email&amp;" . POST_USERS_URL .'=' . $poster_id) : 'mailto:' . $row['user_email'];

					$email_img = '<a href="' . $email_uri . '"><img src="' . $images['icon_email'] . '" alt="' . $lang['Send_email'] . '" title="' . $lang['Send_email'] . '" border="0" /></a>';
					$email = '<a href="' . $email_uri . '">' . $lang['Send_email'] . '</a>';
				}
				else
				{
					$email_img = '';
					$email = '';
				}

				$www_img = ( $row['user_website'] ) ? '<a href="' . $row['user_website'] . '" target="_userwww"><img src="' . $images['icon_www'] . '" alt="' . $lang['Visit_website'] . '" title="' . $lang['Visit_website'] . '" border="0" /></a>' : '';
				$www = ( $row['user_website'] ) ? '<a href="' . $row['user_website'] . '" target="_userwww">' . $lang['Visit_website'] . '</a>' : '';

				$temp_url = append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=$poster_id");
				$posted = '<a href="' . $temp_url . '">' . $data['username'] . '</a>';
				$posted = create_date($board_config['default_dateformat'], $row['post_date'], $board_config['board_timezone']);

				$post = $row['post'];

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

				if ( $userdata['user_level'] == MOD || $userdata['user_level'] == ADMIN )
				{
					$temp_url = append_sid("garage.$phpEx?mode=edit_comment&amp;CID=$cid&amp;comment_id=" . $row['comment_id'] . "&amp;sid=" . $userdata['session_id']);
					$edit_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_edit'] . '" alt="' . $lang['Edit_delete_post'] . '" title="' . $lang['Edit_delete_post'] . '" border="0" /></a>';
					$edit = '<a href="' . $temp_url . '">' . $lang['Edit_delete_post'] . '</a>';

					$temp_url = append_sid("garage.$phpEx?mode=delete_comment&amp;CID=$cid&amp;comment_id=" . $row['comment_id'] . "&amp;sid=" . $userdata['session_id']);
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
					'POSTER_RANK' => $poster_rank,
					'RANK_IMAGE' => $rank_image,
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
			$db->sql_freeresult($result);
		}

		$template->assign_block_vars('level1', array());
		$template->assign_block_vars('level3_nolink', array());
		$template->assign_vars(array(
			'CID' => $cid,
			'U_LEVEL1' => $u_vehicle,
			'L_LEVEL1' => $data['vehicle'],
			'L_LEVEL3' => $lang['Guestbook'],
			'L_GUESTBOOK_TITLE' => $data['username']. " - " .$data['vehicle'] . " " .$lang['Guestbook'],
			'L_POSTED' => $lang['Posted'],
			'L_ADD_COMMENT' => $lang['Add_Comment'],
			'L_POST_COMMENT' => $lang['Post_Comment'],
			'S_MODE_ACTION' => append_sid("garage.$phpEx?mode=insert_comment&CID=$cid"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'insert_comment':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('INTERACT',"garage.$phpEx?mode=error&EID=17");

		//Get All Data Posted And Make It Safe To Use
		$str_params = array('comments');
		$data = $garage_lib->process_str_vars($str_params);
		$data['author_id'] = $userdata['user_id'];
		$data['post_date'] = time();

		//Checks All Required Data Is Present
		$params = array('comments');
		$garage_lib->check_required_vars($params);

		//Insert The Comment
		$garage_lib->insert_vehicle_comment($data);

		//Get Vehicle Data So We Can Check If We Need To PM User
		$data = $garage_lib->select_vehicle_data($cid);		
		$data['author_id'] = $userdata['user_id'];
		$data['time'] = time();

		if ( $data['guestbook_pm_notify'] == TRUE )
		{
			//Build Rest Of Required Data
			$data['date'] = date("U");
			$data['pm_subject'] = $lang['Guestbook_Notify_Subject'];
			$data['vehicle_link'] = '<a href="garage.'.$phpEx.'?mode=view_guestbook&CID='.$cid.'">'.$lang['Here'].'</a>';
             		$data['pm_text'] = (sprintf($lang['Guestbook_Notify_Text'],$data['vehicle_link']));

			//Checks All Required Data Is Present
			$params = array('user_id', 'pm_subject', 'author_id', 'date');
			$garage_lib->check_required_vars($params);
			
			//Now We Have All Data Lets Send The PM!!
			$garage_lib->send_user_pm($data);
		}

		redirect(append_sid("garage.$phpEx?mode=view_guestbook&CID=$cid", true));

		break;

	case 'edit_comment':

		//Only Allow Moderators Or Administrators Perform This Action
		if ( $userdata['user_level'] == REGULAR ) 
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=13", true));
		}

		//Pull Required Data From DB
		$data = $garage_lib->select_comment_data($comment_id);	
		
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
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_edit_comment.tpl')
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'update_comment':

		//Only Allow Moderators Or Administrators Perform This Action
		if ( $userdata['user_level'] == REGULAR ) 
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=13", true));
		}

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('COMMENT_ID');
		$int_data = $garage_lib->process_int_vars($int_params);
		$str_params = array('comments');
		$str_data = $garage_lib->process_str_vars($str_params);
		$data = $this->merge_int_str_data($int_data, $str_data);

		//Checks All Required Data Is Present
		$params = array('comments', 'COMMENT_ID');
		$garage_lib->check_required_vars($params);

		$garage_lib->update_single_field(GARAGE_GUESTBOOKS_TABLE,'post',$data['comments'],'id',$data['COMMENT_ID']);

		redirect(append_sid("garage.$phpEx?mode=view_guestbook&CID=$cid", true));

		break;

	case 'delete_comment':

		//Only Allow Moderators Or Administrators Perform This Action
		if ( $userdata['user_level'] == REGULAR ) 
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=13", true));
		}

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('comment_id');
		$data = $garage_lib->process_int_vars($int_params);

		$garage_lib->delete_rows(GARAGE_GUESTBOOKS_TABLE,'id',$data['comment_id']);

		redirect(append_sid("garage.$phpEx?mode=view_guestbook&CID=$cid", true));

		break;

	case 'error':

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$garage_lib->garage_error($eid);
		
		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage_error.tpl')
		);

		$template->assign_vars(array(
			'L_GARAGE_ERROR_OCCURED' => $lang['Garage_Error_Occured'])
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;

	case 'rate_vehicle':

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('INTERACT',"garage.$phpEx?mode=error&EID=17");

		//Get All Data Posted And Make It Safe To Use
		$int_params = array('vehicle_rating');
		$data = $garage_lib->process_int_vars($int_params);
		$data['rate_date'] = time();
		$data['user_id'] = $userdata['user_id'];

		//Checks All Required Data Is Present
		$params = array('vehicle_rating', 'rate_date', 'user_id');
		$garage_lib->check_required_vars($params);

		//Pull Required Data From DB
	        $vehicle_data = $garage_lib->select_vehicle_data($cid);

		//If User Is Guest Generate Unique Number For User ID....
		srand($garage_lib->make_seed());
		$data['user_id'] = (!$userdata['session_logged_in']) ? '-' . (rand(2,99999)) : $userdata['user_id'];

		//Check If User Owns Vehicle
		if ( $vehicle_data['member_id'] == $data['user_id'] )
		{
			redirect(append_sid("garage.$phpEx?mode=error&EID=21", true));
		}

		$count = $garage_lib->count_vehicle_ratings($data);
		
		if ( $count['total'] < 1 )
		{
			$sql = "INSERT INTO ". GARAGE_RATING_TABLE ." (garage_id,rating,user_id,rate_date)
				VALUES ('$cid', '".$data['vehicle_rating']."', '".$data['user_id']."', '".$data['rate_date']."')";
		}
		else
		{
			$sql = "UPDATE ". GARAGE_RATING_TABLE ." SET rating = '".$data['vehicle_rating']."', rate_date = '".$data['rate_date']."'
		       		WHERE user_id = '".$data['user_id']."' AND garage_id = '$cid'";
		}

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Insert Vehicle Rating', '', __LINE__, __FILE__, $sql);
		}

		redirect(append_sid("garage.$phpEx?mode=view_vehicle&CID=$cid", true));

		break;

	default:

		//Let Check The User Is Allowed Perform This Action
		$garage_lib->check_permissions('BROWSE',"garage.$phpEx?mode=error&EID=15");

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'header' => 'garage_header.tpl',
			'body'   => 'garage.tpl')
		);

		//Display If Needed Featured Vehicle
		$garage_lib->show_featuredvehicle();
		
		$required_position = 1;
		//Display All Boxes Required
		$garage_lib->show_newest_vehicles();
		$garage_lib->show_updated_vehicles();
		$garage_lib->show_newest_modifications();
		$garage_lib->show_updated_modifications();
		$garage_lib->show_most_modified();
		$garage_lib->show_most_spent();
		$garage_lib->show_most_viewed();
		$garage_lib->show_lastcommented();
		$garage_lib->show_topquartermile();
		$garage_lib->show_toprated();

		// Get the total count of vehicles and views in the garage
        	$sql ="SELECT count(*) AS total_cars, SUM(views) AS total_views FROM " . GARAGE_TABLE;
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Error Counting Views & Vehicles', '', __LINE__, __FILE__, $sql);
		}
	        $row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
        	$data['total_cars'] = $row['total_cars'];
	        $data['total_views'] = $row['total_views'];

        	// Get the total count of mods in the garage
	        $sql = "SELECT count(*) AS total_mods FROM " . GARAGE_MODS_TABLE;
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Error Counting Total Mods', '', __LINE__, __FILE__, $sql);
		}
        	$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
	        $data['total_mods'] = $row['total_mods'];

        	// Get the total count of comments in the garage
	        $sql = "SELECT count(*) AS total_comments FROM " . GARAGE_GUESTBOOKS_TABLE;
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Error Counting Comments', '', __LINE__, __FILE__, $sql);
		}
        	$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
	        $data['total_comments'] = $row['total_comments'];

		$template->assign_vars(array(
			'L_OWNER' 		=> $lang['Owner'],
			'L_FEATURED_VEHICLE' 	=> $lang['Featured_Vehicle'],
			'TOTAL_VEHICLES' 	=> $data['total_cars'],
			'TOTAL_VIEWS' 		=> $data['total_views'],
			'TOTAL_MODIFICATIONS' 	=> $data['total_mods'],
			'TOTAL_COMMENTS'  	=> $data['total_comments'])
		);

		//Display Page...In Order Header->Menu->Body->Footer
		$template->pparse('header');
		$garage_lib->build_sidemenu_html();
		$template->pparse('body');

		break;
} // switch()

$template->set_filenames(array(
	'garage_footer' => 'garage_footer.tpl')
);
$template->pparse('garage_footer');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>

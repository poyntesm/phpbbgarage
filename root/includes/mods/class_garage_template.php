<?php
/***************************************************************************
 *                              class_garage_template.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: class_garage_template.php 156 2006-06-19 06:51:48Z poyntesm $
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
if (!defined('IN_PHPBB'))
{
	die('Hacking attempt');
}

class garage_template
{
	var $classname = "garage_template";

	/*========================================================================*/
	// Builds HTML Variables For Version & Copywrite Notice
	// Remove This Notice & No Support Is Given
	// Usage: version_notice();
	/*========================================================================*/
	function version_notice()
	{
		global $template, $user, $garage_config, $phpEx, $phpbb_root_path;

		// Set Garage Version Messages.....DO NOT REMOVE....No Support For Any Garage Without It
		$template->assign_vars(array(
			'L_GARAGE' 		=> $user->lang['GARAGE'],
			'L_POWERED_BY_GARAGE'	=> 'Powered By phpBB Garage' . $user->lang['Translation_Link'],
			'U_GARAGE' 		=> append_sid("{$phpbb_root_path}garage.$phpEx"),
			'GARAGE_LINK' 		=> 'http://www.phpbbgarage.com/',
			'GARAGE_VERSION'	=> $garage_config['version'])
		);

		return;
	}

	/*========================================================================*/
	// Builds all required side menus
	// Usage: sidemenu();
	/*========================================================================*/
	function sidemenu()
	{
		global $user, $template, $phpEx, $phpbb_root_path, $garage_config, $garage, $garage_vehicle, $auth;
	
		$template->set_filenames(array(
			'menu' => 'garage_menu.html')
		);

		$template->assign_vars(array(
			'U_GARAGE_MAIN' 			=> append_sid("{$phpbb_root_path}garage.$phpEx"),
			'U_GARAGE_BROWSE' 			=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=browse"),
			'U_GARAGE_SEARCH' 			=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=search"),
			'U_GARAGE_INSURANCE_REVIEW' 		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_insurance_business"),
			'U_GARAGE_SHOP_REVIEW' 			=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_shop_business"),
			'U_GARAGE_GARAGE_REVIEW' 		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_garage_business"),
			'U_GARAGE_QUARTERMILE_TABLE' 		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=quartermile"),
			'U_GARAGE_DYNORUN_TABLE' 		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=dynorun"),
			'U_GARAGE_CREATE_VEHICLE' 		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=create_vehicle"),
			'MAIN' 					=> ($garage_config['enable_images']) ? $user->img('garage_main_menu', 'MAIN_MENU') : $user->lang['MAIN_MENU'],
			'BROWSE' 				=> ($garage_config['enable_images']) ? $user->img('garage_browse', 'BROWSE_GARAGE') : $user->lang['BROWSE_GARAGE'],
			'SEARCH' 				=> ($garage_config['enable_images']) ? $user->img('garage_search', 'SEARCH_GARAGE') : $user->lang['SEARCH_GARAGE'],
			'INSURANCE_REVIEW' 			=> ($garage_config['enable_images']) ? $user->img('garage_insurance_review', 'INSURANCE_SUMMARY') : $user->lang['INSURANCE_SUMMARY'],
			'SHOP_REVIEW' 				=> ($garage_config['enable_images']) ? $user->img('garage_shop_review', 'SHOP_REVIEW') : $user->lang['SHOP_REVIEW'],
			'GARAGE_REVIEW' 			=> ($garage_config['enable_images']) ? $user->img('garage_garage_review', 'GARAGE_REVIEW') : $user->lang['GARAGE_REVIEW'],
			'QUARTERMILE_TABLE' 			=> ($garage_config['enable_images']) ? $user->img('garage_quartermile_table', 'QUARTERMILE_TABLE') : $user->lang['QUARTERMILE_TABLE'],
			'DYNORUN_TABLE' 			=> ($garage_config['enable_images']) ? $user->img('garage_dynorun_table', 'DYNORUN_TABLE') : $user->lang['DYNORUN_TABLE'],
			'CREATE_VEHICLE' 			=> ($garage_config['enable_images']) ? $user->img('garage_create_vehicle', 'CREATE_VEHICLE') : $user->lang['CREATE_VEHICLE'],
			'S_GARAGE_DISPLAY_MAIN' 		=> ($garage_config['enable_index_menu']) ? true : false,
			'S_GARAGE_DISPLAY_BROWSE' 		=> ($garage_config['enable_browse_menu']) ? true : false,
			'S_GARAGE_DISPLAY_SEARCH' 		=> ($garage_config['enable_search_menu']) ? true : false,
			'S_GARAGE_DISPLAY_INSURANCE_REVIEW' 	=> ($garage_config['enable_insurance_review_menu']) ? true : false,
			'S_GARAGE_DISPLAY_SHOP_REVIEW' 		=> ($garage_config['enable_shop_review_menu']) ? true : false,
			'S_GARAGE_DISPLAY_GARAGE_REVIEW'	=> ($garage_config['enable_garage_review_menu']) ? true : false,
			'S_GARAGE_DISPLAY_QUARTERMILE_TABLE' 	=> ($garage_config['enable_quartermile_menu']) ? true : false,
			'S_GARAGE_DISPLAY_DYNORUN_TABLE' 	=> ($garage_config['enable_dynorun_menu']) ? true : false,
			'S_GARAGE_DISPLAY_UPDATED_VEHICLES' 	=> ($garage_config['enable_latest_vehicle_index']) ? true : false,
			'S_GARAGE_DISPLAY_CREATE_VEHICLE'	=> ($auth->acl_get('u_garage_add_vehicle')) ? true : false)
		);

		//If Not Allowed Browse Stop Here..We Want The Error To Have The Menu..But No More
		if (!$auth->acl_get('u_garage_browse'))
		{
			return ;
		}

		if ($user->data['user_id'] != ANONYMOUS)
		{
			$template->assign_block_vars('show_vehicles', array());
			$user_vehicles = $garage_vehicle->get_vehicles_by_user($user->data['user_id']);
			for ($i = 0; $i < count($user_vehicles); $i++)
			{
		       		$template->assign_block_vars('show_vehicles.user_vehicles', array(
       					'U_VIEW_VEHICLE'=> append_sid("garage.$phpEx?mode=view_own_vehicle&amp;CID=" . $user_vehicles[$i]['id']),
       					'VEHICLE' 	=> $user_vehicles[$i]['vehicle'])
      				);
			}
		}

		if ($garage_config['enable_latest_vehicle_index'] == true)
		{
			$vehicles = $garage_vehicle->get_latest_updated_vehicles($garage_config['latest_vehicle_index_limit']);	
			for ($i = 0; $i < count($vehicles); $i++)
			{
       				$template->assign_block_vars('updated_vehicles', array(
       					'U_VIEW_VEHICLE'=> append_sid("garage.$phpEx", "mode=view_vehicle&amp;CID=" . $vehicles[$i]['id'], true),
		       			'U_VIEW_PROFILE'=> append_sid("profile.$phpEx", "mode=viewprofile&amp;u=".$vehicles_updated[$i]['user_id'], true),
       					'VEHICLE' 	=> $vehicles[$i]['vehicle'],
       					'UPDATED_TIME' 	=> $user->format_date($vehicles[$i]['date_updated']),
		       			'USERNAME' 	=> $vehicles[$i]['username'])
      				);
			}
		}

		return ;
	}

	/*========================================================================*/
	// Builds The HTML For A Selecting A Business To Reassign To
	// Usage: reassign_business_dropdown('excluding business id');
	/*========================================================================*/
	function reassign_business_dropdown($exclude)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		$html = '<select name="target_id" class="forminput">';

		$sql = "SELECT id, title	
			FROM " . GARAGE_BUSINESS_TABLE . " 
			WHERE pending = 0 	
				and id NOT IN ($exclude)
			ORDER BY title ASC";
	
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$html .= "<option value='".$row['id']."'>".$row['title']."</option>";
		}
		$db->sql_freeresult($result);
		
		$html .= "</select>";
	
		$template->assign_vars(array(
			'BUSINESS_SELECT' => $html)
		);
	
		return ;
	}

	/*========================================================================*/
	// Builds The HTML For Selecting A Dynorun Entry
	// Usage: dynorun_dropdown('dynorun id', 'bhp @ bhp_type', 'vehicle id');
	/*========================================================================*/
	function dynorun_dropdown($selected, $selected_name, $cid)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		$rr_list = "<select name='rr_id' class='forminput'>";
	
		if (!empty($selected) )
		{
			$rr_list .= "<option value='$selected' selected='selected'>$selected_name</option>";
			$rr_list .= "<option value=''>------</option>";
		}
		else
		{
			$rr_list .= "<option value=''>".$lang['Select_A_Option']."</option>";
			$shop_list .= "<option value=''>------</option>";
		}
	
		$sql = "SELECT id, bhp, bhp_unit 
			FROM " . GARAGE_DYNORUN_TABLE . " 
			WHERE garage_id = $cid";
	
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query dynorun', '', __LINE__, __FILE__, $sql);
		}
	
		while ( $dynorun = $db->sql_fetchrow($result) )
		{
			$rr_list .= "<option value='".$dynorun['id']."'>".$dynorun['bhp']." BHP @ ".$dynorun['bhp_unit']."</option>";
		}
		$db->sql_freeresult($result);
		
		$rr_list .= "</select>";
	
		$template->assign_vars(array(
			'RR_LIST' => $rr_list)
		);
	
		return;
	}
	
	/*========================================================================*/
	// Builds The HTML For Selection Box
	// Usage: dropdown('select name', 'options text', 'options values', 'selected option');
	/*========================================================================*/
	function dropdown($select_name, $select_text, $select_types, $selected_option = null)
	{
		global $template, $user;
	
		$select = '<select name="'.$select_name.'">';
		if (empty($selected_option))
		{
			$select .= '<option value="">'.$user->lang['SELECT_A_OPTION'].'</option>';
			$select .= '<option value="">------</option>';
		}
	
		for($i = 0; $i < count($select_text); $i++)
		{
			$selected = ( $selected_option == $select_types[$i] ) ? ' selected="selected"' : '';
			$select .= '<option value="' . $select_types[$i] . '"' . $selected . '>' . $select_text[$i] . '</option>';
		}
	
		$select .= '</select>';
	
		return $select;
	}
	
	/*========================================================================*/
	// Builds the HTML for attaching a image to entries
	// Usage: attach_image('modification'|'vehicle'|'quartermile'|'dynorun');
	/*========================================================================*/
	function attach_image($type)
	{
		global $template, $garage_config, $auth;

		//If No Premissions To Attach An Image Our Job Here Is Done ;)
		if ( (!$auth->acl_get('u_garage_upload_image')) OR (!$auth->acl_get('u_garage_remote_image')) )
		{
			return ;
		}

		//If Images For Mode Are Enabled Then Show Methods Enabled	
		if ( $garage_config['enable_'.$type.'_images'] ) 
		{
			//Setup Parent Template Block For Image Attachment
			$template->assign_block_vars('allow_images', array());

			//Define Image Limits
			$template->assign_vars(array(
				'MAXIMUM_IMAGE_FILE_SIZE'	=> $garage_config['max_image_kbytes'],
				'MAXIMUM_IMAGE_RESOLUTION'	=> $garage_config['max_image_resolution'])
			);

			//Show Upload Image Controls If Enabled
			if ( $garage_config['enable_uploaded_images'] )
			{
	      			$template->assign_block_vars('allow_images.upload_images', array());
		
			}
			//Show Remote Image Link If Enabled
			if ( $garage_config['enable_remote_images'] )
			{
	      			$template->assign_block_vars('allow_images.remote_images', array());
			}
		}
	
		return;
	}
	
	/*========================================================================*/
	// Builds The HTML For Editting Already Attached Images
	// Usage: edit_image('modification'|'vehicle'|'quartermile'|'dynorun', 'image id', 'image name')
	/*========================================================================*/
	function edit_image($type, $image_id, $image_name)
	{
		global $template, $garage_config, $auth;
	
		//If No Premissions To Attach An Image Our Job Here Is Done ;)
		if ( (!$auth->acl_get('u_garage_upload_image')) OR (!$auth->acl_get('u_garage_remote_image')) )
		{
			return ;
		}

		//If Images For Mode Are Enabled Then Show Methods Enabled	
		if ( $garage_config['enable_'.$type.'_images'] ) 
		{
			//Setup Parent Template Block For Image Attachment
			$template->assign_block_vars('allow_images', array());

			//Define Image Limits
			$template->assign_vars(array(
				'MAXIMUM_IMAGE_FILE_SIZE' => $garage_config['max_image_kbytes'],
				'MAXIMUM_IMAGE_RESOLUTION' => $garage_config['max_image_resolution'])
			);

			if ( !empty($image_id) )
			{
				//Display Option To Keep Image
	      			$template->assign_block_vars('allow_images.keep_image', array(
					'CURRENT_IMAGE' => $image_name)
				);
	
				//Display Option To Delete Image
	      			$template->assign_block_vars('allow_images.remove_image', array(
					'IMAGE_ID' => $image_id)
				);
				
				//Show Upload Image Controls If Enabled
				if  ($garage_config['enable_uploaded_images'] )
				{
	      				$template->assign_block_vars('allow_images.replace_image_upload', array());
				}
				//Show Remote Image Link If Enabled
				if ( ($garage_config['enable_remote_images'] ) AND (empty($image_id) == FALSE))
				{
	      				$template->assign_block_vars('allow_images.replace_remote_image', array());
				}
			}
			elseif (empty($image_id) )
			{
				//Show Upload Image Controls If Enabled
				if ( $garage_config['enable_uploaded_images'] )
				{
	      				$template->assign_block_vars('allow_images.upload_images', array());
				}
				//Show Remote Image Link If Enabled
				if ( $garage_config['enable_remote_images'] )
				{
	      				$template->assign_block_vars('allow_images.remote_images', array());
				}
			}

		}
		return;
	}
	
	/*========================================================================*/
	// Build Order HTML
	// Usage: order('selected');
	/*========================================================================*/
	function order($order)
	{
		global $template, $user;
	
		$order_html = '<select name="order">';
		if($order == 'ASC')
		{
			$order_html .= '<option value="ASC" selected="selected">' . $user->lang['ASCENDING_ORDER'] . '</option><option value="DESC">' . $user->lang['DESCENDING_ORDER'] . '</option>';
		}
		else
		{
			$order_html .= '<option value="ASC">' . $user->lang['ASCENDING_ORDER'] . '</option><option value="DESC" selected="selected">' . $user->lang['DESCENDING_ORDER'] . '</option>';
		}
		$order_html .= '</select>';

		$template->assign_vars(array(
			'S_ORDER_SELECT' => $order_html)
		);
		return;
	}
}

$garage_template = new garage_template();

?>

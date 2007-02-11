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
			'U_GARAGE_INSURANCE_REVIEW' 		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=insurance_review"),
			'U_GARAGE_SHOP_REVIEW' 			=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=shop_review"),
			'U_GARAGE_GARAGE_REVIEW' 		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=garage_review"),
			'U_GARAGE_QUARTERMILE_TABLE' 		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=quartermile_table"),
			'U_GARAGE_DYNORUN_TABLE' 		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=dynorun_table"),
			'U_GARAGE_LAP_TABLE' 			=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=lap_table"),
			'U_GARAGE_CREATE_VEHICLE' 		=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=add_vehicle"),
			'MAIN' 					=> ($garage_config['enable_images']) ? $user->img('garage_main_menu', 'MAIN_MENU') : $user->lang['MAIN_MENU'],
			'BROWSE' 				=> ($garage_config['enable_images']) ? $user->img('garage_browse', 'BROWSE_GARAGE') : $user->lang['BROWSE_GARAGE'],
			'SEARCH' 				=> ($garage_config['enable_images']) ? $user->img('garage_search', 'SEARCH_GARAGE') : $user->lang['SEARCH_GARAGE'],
			'INSURANCE_REVIEW' 			=> ($garage_config['enable_images']) ? $user->img('garage_insurance_review', 'INSURANCE_SUMMARY') : $user->lang['INSURANCE_SUMMARY'],
			'SHOP_REVIEW' 				=> ($garage_config['enable_images']) ? $user->img('garage_shop_review', 'SHOP_REVIEW') : $user->lang['SHOP_REVIEW'],
			'GARAGE_REVIEW' 			=> ($garage_config['enable_images']) ? $user->img('garage_garage_review', 'GARAGE_REVIEW') : $user->lang['GARAGE_REVIEW'],
			'QUARTERMILE_TABLE' 			=> ($garage_config['enable_images']) ? $user->img('garage_quartermile_table', 'QUARTERMILE_TABLE') : $user->lang['QUARTERMILE_TABLE'],
			'DYNORUN_TABLE' 			=> ($garage_config['enable_images']) ? $user->img('garage_dynorun_table', 'DYNORUN_TABLE') : $user->lang['DYNORUN_TABLE'],
			'LAP_TABLE' 				=> ($garage_config['enable_images']) ? $user->img('garage_lap_table', 'LAP_TABLE') : $user->lang['LAP_TABLE'],
			'CREATE_VEHICLE' 			=> ($garage_config['enable_images']) ? $user->img('garage_create_vehicle', 'CREATE_VEHICLE') : $user->lang['CREATE_VEHICLE'],
			'S_GARAGE_DISPLAY_MAIN' 		=> ($garage_config['enable_index_menu']) ? true : false,
			'S_GARAGE_DISPLAY_BROWSE' 		=> ($garage_config['enable_browse_menu']) ? true : false,
			'S_GARAGE_DISPLAY_SEARCH' 		=> ($garage_config['enable_search_menu']) ? true : false,
			'S_GARAGE_DISPLAY_INSURANCE_REVIEW' 	=> ($garage_config['enable_insurance_review_menu']) ? true : false,
			'S_GARAGE_DISPLAY_SHOP_REVIEW' 		=> ($garage_config['enable_shop_review_menu']) ? true : false,
			'S_GARAGE_DISPLAY_GARAGE_REVIEW'	=> ($garage_config['enable_garage_review_menu']) ? true : false,
			'S_GARAGE_DISPLAY_QUARTERMILE_TABLE' 	=> ($garage_config['enable_quartermile_menu']) ? true : false,
			'S_GARAGE_DISPLAY_DYNORUN_TABLE' 	=> ($garage_config['enable_dynorun_menu']) ? true : false,
			'S_GARAGE_DISPLAY_LAP_TABLE' 		=> ($garage_config['enable_lap_menu']) ? true : false,
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
			$template->assign_vars(array(
				'S_DISPLAY_USER_VEHICLES' => true)
			);
			$user_vehicles = $garage_vehicle->get_vehicles_by_user($user->data['user_id']);
			for ($i = 0; $i < count($user_vehicles); $i++)
			{
		       		$template->assign_block_vars('user_vehicles', array(
       					'U_VIEW_VEHICLE'=> append_sid("garage_vehicle.$phpEx?mode=view_own_vehicle&amp;CID=" . $user_vehicles[$i]['id']),
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
       					'U_VIEW_VEHICLE'	=> append_sid("garage_vehicle.$phpEx", "mode=view_vehicle&amp;CID=" . $vehicles[$i]['id'], true),
					'U_VIEW_PROFILE'	=> append_sid("memberlist.$phpEx", "mode=viewprofile&amp;u=".$vehicles[$i]['user_id'], true),
					'USERNAME_COLOUR'	=> get_username_string('colour', $vehicles[$i]['user_id'], $vehicles[$i]['username'], $vehicles[$i]['user_colour']),
       					'VEHICLE' 		=> $vehicles[$i]['vehicle'],
       					'UPDATED_TIME' 		=> $user->format_date($vehicles[$i]['date_updated']),
		       			'USERNAME' 		=> $vehicles[$i]['username'])
      				);
			}
		}

		return ;
	}

	/*========================================================================*/
	// Builds the HTML for attaching a image to entries
	// Usage: attach_image('modification'|'vehicle'|'quartermile'|'dynorun'|'lap');
	/*========================================================================*/
	function attach_image($type)
	{
		global $template, $garage_config, $auth;

		//If No Premissions To Attach An Image Our Job Here Is Done ;)
		if ( (!$auth->acl_get('u_garage_upload_image')) OR (!$auth->acl_get('u_garage_remote_image')) )
		{
			return;
		}

		//If Images For Mode Are Enabled Then Show Methods Enabled	
		if ($garage_config['enable_'.$type.'_images'] && ($garage_config['enable_uploaded_images']||$garage_config['enable_remote_images'])) 
		{
			//Setup Parent Template Block For Image Attachment
			$template->assign_vars(array('S_DISPLAY_IMAGE_ATTACH_OPTIONS' => true));

			//Show Upload Image Controls If Enabled
			if ( $garage_config['enable_uploaded_images'] )
			{
				$template->assign_vars(array(
					'MAXIMUM_IMAGE_FILE_SIZE'	=> $garage_config['max_image_kbytes'],
					'MAXIMUM_IMAGE_RESOLUTION'	=> $garage_config['max_image_resolution'],
					'S_DISPLAY_UPLOAD_IMAGE' 	=> true)
				);
			}
			//Show Remote Image Link If Enabled
			if ( $garage_config['enable_remote_images'] )
			{
				$template->assign_vars(array('S_DISPLAY_REMOTE_IMAGE' => true));
			}
		}
	
		return;
	}
	
	function order_dropdown($selected = null)
	{
		global $template, $user;

		$orders	= array('ASC', 'DESC');
		$orders_text = array($user->lang['ASC'], $user->lang['DESC']);

		for ($i = 0, $count = sizeof($orders);$i < $count; $i++)
		{
			$template->assign_block_vars('order', array(
				'VALUE'		=> $orders[$i],
				'TEXT'		=> $orders_text[$i],
				'S_SELECTED'	=> ($selected == $orders[$i]) ? true: false)
			);
		}
	}

	function sort_dropdown($type, $selected = null)
	{
		global $template, $user;

		if ($type == 'vehicle')
		{
			$values = array('date_created', 'date_updated', 'username', 'made_year', 'make', 'model', 'colour', 'views', 'total_mods');
			$texts = array($user->lang['LAST_CREATED'], $user->lang['LAST_UPDATED'], $user->lang['OWNER'], $user->lang['YEAR'], $user->lang['MAKE'], $user->lang['MODEL'],  $user->lang['COLOUR'], $user->lang['TOTAL_VIEWS'], $user->lang['TOTAL_MODS']);
		}
		else if ($type == 'modification')
		{
			$values = array('category', 'username', 'price', 'rating');
			$texts = array($user->lang['CATEGORY'], $user->lang['OWNER'], $user->lang['PRICE'], $user->lang['RATING']);
		}
		else if ($type == 'premium')
		{
			$values = array('cover_type', 'premium', 'company');
			$texts = array($user->lang['COVER_TYPE'], $user->lang['PREMIUM'], $user->lang['INSURER']);
		}
		else if ($type == 'quartermile')
		{
			$values = array('qm.rt', 'qm.sixty', 'qm.three', 'qm.eighth', 'qm.eighthmph', 'qm.thou', 'quart', 'qm.quartmph');
			$texts = array($user->lang['RT'], $user->lang['SIXTY'], $user->lang['THREE'], $user->lang['EIGHTH'], $user->lang['EIGHTHMPH'], $user->lang['THOU'],  $user->lang['QUART'], $user->lang['QUARTMPH']);
		}
		else if ($type == 'dynorun')
		{
			$values = array('rr.dynocentre_id', 'bhp', 'rr.bhp_unit, bhp', 'rr.torque', 'rr.torque_unit, rr.torque', 'rr.boost', 'rr.boost_unit, rr.boost', 'rr.nitrous', 'rr.peakpoint');
			$texts = array($user->lang['DYNOCENTRE'], $user->lang['BHP'], $user->lang['BHP_UNIT'], $user->lang['TORQUE'], $user->lang['TORQUE_UNIT'], $user->lang['BOOST'], $user->lang['BOOST_UNIT'], $user->lang['NITROUS'], $user->lang['PEAKPOINT']);
		}
		else if ($type == 'track_time')
		{
			$values = array('date_created', 'date_updated', 'username', 'made_year', 'make', 'model', 'colour', 'views', 'total_mods');
			$texts = array($user->lang['LAST_CREATED'], $user->lang['LAST_UPDATED'], $user->lang['OWNER'], $user->lang['YEAR'], $user->lang['MAKE'], $user->lang['MODEL'],  $user->lang['COLOUR'], $user->lang['TOTAL_VIEWS'], $user->lang['TOTAL_MODS']);
		}

		for ($i = 0, $count = sizeof($values);$i < $count; $i++)
		{
			$template->assign_block_vars('sort', array(
				'VALUE'		=> $values[$i],
				'TEXT'		=> $texts[$i],
				'S_SELECTED'	=> ($selected == $values[$i]) ? true: false)
			);
		}
	}

	function make_dropdown($makes, $selected_id = null)
	{
		global $template;

		for ($i = 0, $count = sizeof($makes);$i < $count; $i++)
		{
			$template->assign_block_vars('make', array(
				'VALUE'		=> $makes[$i]['id'],
				'TEXT'		=> $makes[$i]['make'],
				'S_SELECTED'	=> ($selected_id == $makes[$i]['id']) ? true: false)
			);
		}
	}

	function track_dropdown($tracks, $selected_id = null)
	{
		global $template;

		for ($i = 0, $count = sizeof($tracks);$i < $count; $i++)
		{
			$template->assign_block_vars('track', array(
				'VALUE'		=> $tracks[$i]['id'],
				'TEXT'		=> $tracks[$i]['title'],
				'S_SELECTED'	=> ($selected_id == $tracks[$i]['id']) ? true: false)
			);
		}
	}

	function category_dropdown($categories, $selected_id = null)
	{
		global $template;

		for ($i = 0, $count = sizeof($categories);$i < $count; $i++)
		{
			$template->assign_block_vars('category', array(
				'VALUE'		=> $categories[$i]['id'],
				'TEXT'		=> $categories[$i]['title'],
				'S_SELECTED'	=> ($selected_id == $categories[$i]['id']) ? true : false)
			);
		}

	}

	function manufacturer_dropdown($manufacturers, $selected_id = null)
	{
		global $template;

		for ($i = 0, $count = sizeof($manufacturers);$i < $count; $i++)
		{
			$template->assign_block_vars('manufacturer', array(
				'VALUE'		=> $manufacturers[$i]['id'],
				'TEXT'		=> $manufacturers[$i]['title'],
				'S_SELECTED'	=> ($selected_id == $manufacturers[$i]['id']) ? true : false)
			);
		}

	}

	function retail_dropdown($shops, $selected_id = null)
	{
		global $template;

		for ($i = 0, $count = sizeof($shops);$i < $count; $i++)
		{
			$template->assign_block_vars('shop', array(
				'VALUE'		=> $shops[$i]['id'],
				'TEXT'		=> $shops[$i]['title'],
				'S_SELECTED'	=> ($selected_id == $shops[$i]['id']) ? true : false)
			);
		}
	}

	function garage_dropdown($garages, $selected_id = null)
	{
		global $template;

		for ($i = 0, $count = sizeof($garages);$i < $count; $i++)
		{
			$template->assign_block_vars('garage', array(
				'VALUE'		=> $garages[$i]['id'],
				'TEXT'		=> $garages[$i]['title'],
				'S_SELECTED'	=> ($selected_id == $garages[$i]['id']) ? true : false)
			);
		}
	}

	function dynocentre_dropdown($dynocentres, $selected_id = null)
	{
		global $template;

		for ($i = 0, $count = sizeof($dynocentres);$i < $count; $i++)
		{
			$template->assign_block_vars('dynocentre', array(
				'VALUE'		=> $dynocentres[$i]['id'],
				'TEXT'		=> $dynocentres[$i]['title'],
				'S_SELECTED'	=> ($selected_id == $dynocentres[$i]['id']) ? true : false)
			);
		}
	}

	function insurance_dropdown($insurers, $selected_id = null)
	{
		global $template;

		for ($i = 0, $count = sizeof($insurers);$i < $count; $i++)
		{
			$template->assign_block_vars('insurer', array(
				'VALUE'		=> $insurers[$i]['id'],
				'TEXT'		=> $insurers[$i]['title'],
				'S_SELECTED'	=> ($selected_id == $insurers[$i]['id']) ? true : false)
			);
		}

	}

	function reassign_business_dropdown($business, $selected_id = null)
	{
		global $template;

		for ($i = 0, $count = sizeof($business);$i < $count; $i++)
		{
			$template->assign_block_vars('reassign_business', array(
				'VALUE'		=> $business[$i]['id'],
				'TEXT'		=> $business[$i]['title'],
				'S_SELECTED'	=> ($selected_id == $business[$i]['id']) ? true : false)
			);
		}

	}

	function year_dropdown($years, $selected = null)
	{
		global $template;

		for ($i = 0, $count = sizeof($years);$i < $count; $i++)
		{
			$template->assign_block_vars('year', array(
				'VALUE'		=> $years[$i],
				'TEXT'		=> $years[$i],
				'S_SELECTED'	=> ($selected == $years[$i]) ? true : false)
			);
		}
	}

	function engine_dropdown($selected = null)
	{
		global $template, $user;

		$engine_types = array($user->lang['8_CYLINDER_NA'], $user->lang['8_CYLINDER_FI'], $user->lang['6_CYLINDER_NA'], $user->lang['6_CYLINDER_FI'], $user->lang['4_CYLINDER_NA'], $user->lang['4_CYLINDER_FI']);

		for ($i = 0, $count = sizeof($engine_types);$i < $count; $i++)
		{
			$template->assign_block_vars('engine', array(
				'VALUE'		=> $engine_types[$i],
				'TEXT'		=> $engine_types[$i],
				'S_SELECTED'	=> ($selected == $engine_types[$i]) ? true : false)
			);
		}
	}

	function track_condition_dropdown($selected = null)
	{
		global $template, $user;

		$id = array(TRACK_DRY, TRACK_INTERMEDIATE, TRACK_WET);
		$text = array($user->lang['DRY'], $user->lang['INTERMEDIATE'], $user->lang['WET']);

		for ($i = 0, $count = sizeof($id);$i < $count; $i++)
		{
			$template->assign_block_vars('condition', array(
				'VALUE'		=> $id[$i],
				'TEXT'		=> $text[$i],
				'S_SELECTED'	=> ($selected == $id[$i]) ? true : false)
			);
		}
	}

	function lap_type_dropdown($selected = null)
	{
		global $template, $user;

		$id = array(LAP_QUALIFING, LAP_RACE, LAP_TRACKDAY);
		$text = array($user->lang['QUALIFING'], $user->lang['RACE'], $user->lang['TRACKDAY']);

		for ($i = 0, $count = sizeof($id);$i < $count; $i++)
		{
			$template->assign_block_vars('type', array(
				'VALUE'		=> $id[$i],
				'TEXT'		=> $text[$i],
				'S_SELECTED'	=> ($selected == $id[$i]) ? true : false)
			);
		}
	}

	function service_type_dropdown($selected = null)
	{
		global $template, $user;

		$id = array(SERVICE_MAJOR, SERVICE_MINOR);
		$text = array($user->lang['SERVICE_MAJOR'], $user->lang['SERVICE_MINOR']);

		for ($i = 0, $count = sizeof($id);$i < $count; $i++)
		{
			$template->assign_block_vars('service_type', array(
				'VALUE'		=> $id[$i],
				'TEXT'		=> $text[$i],
				'S_SELECTED'	=> ($selected == $id[$i]) ? true : false)
			);
		}
	}

	function currency_dropdown($selected = null)
	{
		global $template;

		$currency_types	= array('GBP', 'USD', 'EUR', 'CAD', 'YEN');

		for ($i = 0, $count = sizeof($currency_types);$i < $count; $i++)
		{
			$template->assign_block_vars('currency', array(
				'VALUE'		=> $currency_types[$i],
				'TEXT'		=> $currency_types[$i],
				'S_SELECTED'	=> ($selected == $currency_types[$i]) ? true : false)
			);
		}
	}

	function mileage_dropdown($selected = null)
	{
		global $template, $user;

		$mileage_unit_types = array($user->lang['MILES'], $user->lang['KILOMETERS']);

		for ($i = 0, $count = sizeof($mileage_unit_types);$i < $count; $i++)
		{
			$template->assign_block_vars('mileage', array(
				'VALUE'		=> $mileage_unit_types[$i],
				'TEXT'		=> $mileage_unit_types[$i],
				'S_SELECTED'	=> ($selected == $mileage_unit_types[$i]) ? true : false)
			);
		}
	}

	function dynorun_dropdown($dynoruns, $selected_id = null)
	{
		global $template; 

		for ($i = 0, $count = sizeof($dynoruns);$i < $count; $i++)
		{
			$template->assign_block_vars('dynorun', array(
				'VALUE'		=> $dynoruns[$i]['id'],
				'TEXT'		=> $dynoruns[$i]['bhp'] . " BHP @ " . $dynoruns[$i]['bhp_unit'],
				'S_SELECTED'	=> ($selected_id == $dynoruns[$i]['id']) ? true : false)
			);
		}
	
	}

	function boost_dropdown($selected = null)
	{
		global $template;
		
		$boost_types = array('PSI', 'BAR');

		for ($i = 0, $count = sizeof($boost_types);$i < $count; $i++)
		{
			$template->assign_block_vars('boost', array(
				'VALUE'		=> $boost_types[$i],
				'TEXT'		=> $boost_types[$i],
				'S_SELECTED'	=> ($selected == $boost_types[$i]) ? true : false)
			);
		}
	}

	function nitrous_dropdown($selected = null)
	{
		global $template, $user;

		$nitrous_types = array('0', '25', '50', '75', '100');
		$nitrous_types_text = array($user->lang['NO_NITROUS'], $user->lang['25_BHP_SHOT'], $user->lang['50_BHP_SHOT'], $user->lang['75_BHP_SHOT'], $user->lang['100_BHP_SHOT']);

		for ($i = 0, $count = sizeof($nitrous_types);$i < $count; $i++)
		{
			$template->assign_block_vars('nitrous', array(
				'VALUE'		=> $nitrous_types[$i],
				'TEXT'		=> $nitrous_types_text[$i],
				'S_SELECTED'	=> ($selected == $nitrous_types[$i]) ? true : false)
			);
		}
	}

	function power_dropdown($name = null, $selected = null)
	{
		global $template, $user;

		$power_types = array($user->lang['WHEEL'], $user->lang['HUB'], $user->lang['FLYWHEEL']);

		for ($i = 0, $count = sizeof($power_types);$i < $count; $i++)
		{
			$template->assign_block_vars($name, array(
				'VALUE'		=> $power_types[$i],
				'TEXT'		=> $power_types[$i],
				'S_SELECTED'	=> ($selected == $power_types[$i]) ? true : false)
			);
		}
	}

	function cover_dropdown($selected = null)
	{
		global $template, $user;

		$cover_types = array($user->lang['THIRD_PARTY'], $user->lang['THIRD_PARTY_FIRE_THEFT'], $user->lang['COMPREHENSIVE'], $user->lang['COMPREHENSIVE_CLASSIC'], $user->lang['COMPREHENSIVE_REDUCED']);

		for ($i = 0, $count = sizeof($cover_types);$i < $count; $i++)
		{
			$template->assign_block_vars('cover', array(
				'VALUE'		=> $cover_types[$i],
				'TEXT'		=> $cover_types[$i],
				'S_SELECTED'	=> ($selected == $cover_types[$i]) ? true : false)
			);
		}
	}

	function rating_dropdown($name = null, $selected_id = null)
	{
		global $template;

		$rating = array('10', '9', '8', '7', '6', '5', '4', '3', '2', '1');

		for ($i = 0, $count = sizeof($rating);$i < $count; $i++)
		{
			$template->assign_block_vars($name, array(
				'VALUE'		=> $rating[$i],
				'TEXT'		=> $rating[$i],
				'S_SELECTED'	=> ($selected_id == $rating[$i]) ? true : false)
			);
		}
	}

}

$garage_template = new garage_template();

?>

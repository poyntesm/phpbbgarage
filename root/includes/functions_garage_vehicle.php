<?php
/***************************************************************************
 *                              functions_garage.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: functions_garage.php 91 2006-04-07 14:51:14Z poyntesm $
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

class garage_vehicle
{
	var $classname = "garage_vehicle";

	/*========================================================================*/
	// Inserts Vehicle Into DB
	// Usage: insert_vehicle(array());
	/*========================================================================*/
	function insert_vehicle($data)
	{
		global $userdata, $db;

		$sql = "INSERT INTO ". GARAGE_TABLE ."
			SET made_year = '".$data['year']."', make_id = '".$data['make_id']."', model_id = '".$data['model_id']."', color = '".$data['colour']."', mileage = '".$data['mileage']."', mileage_units = '".$data['mileage_units']."', price = '".$data['price']."', currency = '".$data['currency']."', comments = '".$data['comments']."', member_id = '".$userdata['user_id']."', date_created = '".$data['time']."', date_updated = '".$data['time']."', main_vehicle = '".$data['main_vehicle']."', guestbook_pm_notify = '".$data['guestbook_pm_notify']."'";
	
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert Vehicle', '', __LINE__, __FILE__, $sql);
		}
	
		$cid = $db->sql_nextid();

		return $cid;
	}

	/*========================================================================*/
	// Insurance Vehicle Rating Into DB
	// Usage: insert_vehicel_rating(array());
	/*========================================================================*/
	function insert_vehicle_rating($data)
	{
		global $cid, $db;

		$sql = "INSERT INTO ". GARAGE_RATING_TABLE ." (garage_id,rating,user_id,rate_date)
			VALUES ('$cid', '".$data['vehicle_rating']."', '".$data['user_id']."', '".$data['rate_date']."')";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert Vehicle Rating', '', __LINE__, __FILE__, $sql);
		}

		return;
	}


	/*========================================================================*/
	// Insurance Insurance Into DB
	// Usage: insert_vehicle_comment(array());
	/*========================================================================*/
	function insert_vehicle_comment($data)
	{
		global $cid, $db, $user_ip;

		$sql = "INSERT INTO ". GARAGE_GUESTBOOKS_TABLE ."
			SET garage_id = '$cid', author_id = '".$data['author_id']."', post_date = '".$data['post_date']."', ip_address = '$user_ip', post = '".$data['comments']."'";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert Vehicle Comment', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Updates Vehicle In DB
	// Usage: update_vehicle(array());
	/*========================================================================*/
	function update_vehicle($data)
	{
		global $cid, $db;

		$sql = "UPDATE ". GARAGE_TABLE ."
			SET made_year = '".$data['year']."', make_id = '".$data['make_id']."', model_id = '".$data['model_id']."', color = '".$data['colour']."', mileage = '".$data['mileage']."', mileage_units = '".$data['mileage_units']."', price = '".$data['price']."', currency = '".$data['currency']."', comments = '".$data['comments']."', guestbook_pm_notify = '".$data['guestbook_pm_notify']."'
			WHERE id = '$cid'";
	
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Update Vehicle', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Updates Vehicle Rating In DB
	// Usage: update_vehicle_rating(array());
	/*========================================================================*/
	function update_vehicle_rating($data)
	{
		global $db, $cid;

		$sql = "UPDATE ". GARAGE_RATING_TABLE ." SET rating = '".$data['vehicle_rating']."', rate_date = '".$data['rate_date']."'
	       		WHERE user_id = '".$data['user_id']."' AND garage_id = '$cid';";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Update Vehicle Rating', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Count The Total Vehicles Within The Garage
	// Usage: count_total_vehciles();
	/*========================================================================*/
	function count_total_vehicles()
	{
		global $db;

		// Get the total count of vehicles and views in the garage
        	$sql ="SELECT count(*) AS total_vehicles FROM " . GARAGE_TABLE;
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Error Counting Vehicles', '', __LINE__, __FILE__, $sql);
		}
	        $row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row['total_vehicles'];
	}

	/*========================================================================*/
	// Returns Count Of Ratings Give To Vehicle By A User
	// Usage: count_vehicle_ratings(array());
	/*========================================================================*/
	function count_vehicle_ratings($data)
	{
		global $cid , $db;

		//Lets See If This Is To Update Or Insert A Rating
	       	$sql = "SELECT count(*) as total FROM " . GARAGE_RATING_TABLE . "
			WHERE user_id = '".$data['user_id']."' AND garage_id = '$cid'";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Error Counting Ratings', '', __LINE__, __FILE__, $sql);
		}

	        $row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row;
	}

	/*========================================================================*/
	// Builds the HTML for a selecting for models
	// Usage:  build_vehicle_javascript();
	/*========================================================================*/
	function build_vehicle_javascript()
	{
		global $db;

		$make_q_id = "SELECT id, make FROM " . GARAGE_MAKES_TABLE . " ORDER BY make ASC";
	
		if( !($make_result = $db->sql_query($make_q_id)) )
		{
			message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
		}

		while ( $make_row = $db->sql_fetchrow($make_result) )
		{
			// Start this makes row in the output, this is where it gets confusing!
			$return .= 'cars["'.$make_row['make'].'"] = new Array("'.$make_row['id'].'", new Array(';

			$make_row_id = $make_row['id'];
        		$model_q_id = "SELECT id, model FROM " . GARAGE_MODELS_TABLE . " 
                		       WHERE make_id = $make_row_id ORDER BY model ASC";

			if( !($model_result = $db->sql_query($model_q_id)) )
			{
				message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
			} 

	        	$model_string = '';
			$model_id_string = '';

			// Loop through all the models of this make
			while ( $model_row = $db->sql_fetchrow($model_result) )
			{
				// Create the arrays that we will use in the output
				$model_string    .= '"'.$model_row['model'].'",';
				$model_id_string .= '"'.$model_row['id']   .'",';
			}
			$db->sql_freeresult($model_result);

			// Strip off the last comma
			$model_string    = substr($model_string,    0, -1);
			$model_id_string = substr($model_id_string, 0, -1);

			// Finish off this makes' row in the output
			$return .= $model_string ."), new Array(". $model_id_string ."));\n";
	        }
		$db->sql_freeresult($make_result);

	        return $return;
	}

	/*========================================================================*/
	// Builds all required side menus
	// Usage:  build_sidemenu_html();
	/*========================================================================*/
	function build_sidemenu_html()
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $images, $board_config;
	
		$template->set_filenames(array(
			'menu' => 'garage_menu.tpl')
		);

		$user_id = $userdata['user_id'];
		if (preg_match("/MAIN/",$garage_config['menu_selection']))
		{
			$main_menu = append_sid("garage.$phpEx?mode=main");
			$menu .= '<a href="' . $main_menu . '">';
			if ( $garage_config['garage_images'])
			{
				$menu .= '<img src="' . $images['garage_main_menu'] . '" alt="'.$lang['Main_Menu'].'" title="'.$lang['Main_Menu'].'" border="0" /></a><br />';
			}
			else
			{
				$menu .= $lang['Main_Menu'].'</a><br />';
			}
			
		}
		if (preg_match("/BROWSE/",$garage_config['menu_selection']))
		{
			$browse_garage = append_sid("garage.$phpEx?mode=browse");
			$menu .= '<a href="' . $browse_garage . '">';
			if ( $garage_config['garage_images'])
			{
				$menu .= '<img src="' . $images['garage_browse'] . '" alt="'.$lang['Browse_Garage'].'" title="'.$lang['Browse_Garage'].'" border="0" /></a><br />';
			}
			else
			{
				$menu .= $lang['Browse_Garage'].'</a><br />';
			}
		}
		if (preg_match("/SEARCH/",$garage_config['menu_selection']))
		{
			$search_garage = append_sid("garage.$phpEx?mode=search");
			$menu .= '<a href="' . $search_garage . '">';
			if ( $garage_config['garage_images'])
			{
				$menu .= '<img src="' . $images['garage_search'] . '" alt="'.$lang['Search_Garage'].'" title="'.$lang['Search_Garage'].'" border="0" /></a><br />';
			}
			else
			{
				$menu .= $lang['Search_Garage'].'</a><br />';
			}
		}
		if (preg_match("/INSURANCEREVIEW/",$garage_config['menu_selection']))
		{
			$insurance = append_sid("garage.$phpEx?mode=view_insurance_business");
			$menu .= '<a href="' . $insurance . '">';
			if ( $garage_config['garage_images'])
			{
				$menu .= '<img src="' . $images['garage_insurance_review'] . '" alt="'.$lang['Insurance_Summary'].'" title="'.$lang['Insurance_Summary'].'" border="0" /></a><br />';
			}
			else
			{
				$menu .= $lang['Insurance_Summary'].'</a><br />';	
			}
		}
		if (preg_match("/GARAGEREVIEW/",$garage_config['menu_selection']))
		{
			$garage = append_sid("garage.$phpEx?mode=view_garage_business");
			$menu .= '<a href="' . $garage . '">';
			if ( $garage_config['garage_images'])
			{
				$menu .= '<img src="' . $images['garage_garage_review'] . '" alt="'.$lang['Garage_Review'].'" title="'.$lang['Garage_Review'].'" border="0" /></a><br />';
			}
			else
			{
				$menu .= $lang['Garage_Review'].'</a><br />';
			}
		}
		if (preg_match("/SHOPREVIEW/",$garage_config['menu_selection']))
		{
			$garage = append_sid("garage.$phpEx?mode=view_shop_business");
			$menu .= '<a href="' . $garage . '">';
			if ( $garage_config['garage_images'])
			{
				$menu .= '<img src="' . $images['garage_shop_review'] . '" alt="'.$lang['Shop_Review'].'" title="'.$lang['Shop_Review'].'" border="0" /></a><br />';
			}
			else
			{
				$menu .= $lang['Shop_Review'].'</a><br />';
			}
		}

		if (preg_match("/QUARTERMILE/",$garage_config['menu_selection']))
		{
			$garage = append_sid("garage.$phpEx?mode=quartermile");
			$menu .= '<a href="' . $garage . '">';
			if ( $garage_config['garage_images'])
			{
				$menu .= '<img src="' . $images['garage_quartermile_table'] . '" alt="'.$lang['Quartermile_Table'].'" title="'.$lang['Quartermile_Table'].'" border="0" /></a><br />';
			}
			else
			{
				$menu .= $lang['Quartermile_Table'].'</a><br />';
			}
		}
		if (preg_match("/ROLLINGROAD/",$garage_config['menu_selection']))
		{
			$garage = append_sid("garage.$phpEx?mode=rollingroad");
			$menu .= '<a href="' . $garage . '">';
			if ( $garage_config['garage_images'])
			{
				$menu .= '<img src="' . $images['garage_rollingroad_table'] . '" alt="'.$lang['Rollingroad_Table'].'" title="'.$lang['Rollingroad_Table'].'" border="0" /></a><br />';
			}
			else
			{
				$menu .= $lang['Rollingroad_Table'].'</a><br />';
			}
		}
		$create_vehicle = append_sid("garage.$phpEx?mode=create_vehicle");
		$create_vehicle_link = '<a href="' . $create_vehicle . '">';
		if ( $garage_config['garage_images'])
		{
			$create_vehicle_link .= '<img src="' . $images['garage_create_vehicle'] . '" alt="'.$lang['Create_Vehicle'].'" title="'.$lang['Create_Vehicle'].'" border="0" /></a>';
		}
		else
		{
			$create_vehicle_link .= $lang['Create_Vehicle'].'</a><br />';
		}

		$template->assign_vars(array(
			'L_MENU' => $lang['Menu'],
			'L_OWNER' => $lang['Owner'],
		       	'L_MY_VEHICLES' => $lang['My_Vehicles'],
       			'L_LATEST_UPDATED' => $lang['Latest_Updated'],
			'L_WELCOME' => $lang['Welcome'],
			'L_WELCOME_TEXT' => $lang['Welcome_Text'],
			'L_TOTAL_VEHICLES' => $lang['Total_Vehicles'],
			'L_TOTAL_MODIFICATIONS' => $lang['Total_Modifications'],
			'L_TOTAL_COMMENTS' => $lang['Total_Comments'],
			'L_TOTAL_VIEWS' => $lang['Total_Views'],
			'MENU' => $menu,
	       		'L_CREATE_VEHICLE' => $create_vehicle_link)
		);

		if ( $userdata['session_logged_in'] )
		{
			$template->assign_block_vars('show_vehicles', array());

			$sql = "SELECT g.id, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle
       				FROM " . GARAGE_TABLE . " AS g 
	        			LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
		        		LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id 
	        		WHERE g.member_id = $user_id
        			ORDER BY g.id ASC";

			if( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
			}

			while ( $user_vehicle = $db->sql_fetchrow($result) )
			{
				$cid = $user_vehicle['id'];
		       		$template->assign_block_vars('show_vehicles.user_vehicles', array(
       					'U_VIEW_VEHICLE' => append_sid("garage.$phpEx?mode=view_own_vehicle&amp;CID=$cid"),
       					'VEHICLE' => $user_vehicle['vehicle'])
      				);
			}
			$db->sql_freeresult($result);
		}

		if (!$this->check_permissions('BROWSE',''))
		{
			$template->pparse('menu');
			return ;
		}

		if ( $garage_config['lastupdatedvehiclesmain_on'] == TRUE )
		{
			$template->assign_block_vars('lastupdatedvehiclesmain_on', array());

			$limit = $garage_config['lastupdatedvehiclesmain_limit'];

			$sql = "SELECT g.id, g.made_year, g.member_id, g.date_updated, user.username, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle
       				FROM " . GARAGE_TABLE . " AS g 
	        			LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
	        			LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id 
					LEFT JOIN " . USERS_TABLE . " AS user ON g.member_id = user.user_id 
				WHERE makes.pending = 0 AND models.pending = 0 
		        	ORDER BY g.date_updated DESC
				LIMIT 0, $limit";

			if( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
			}

			while ( $vehicle_updated = $db->sql_fetchrow($result) )
			{
       				$template->assign_block_vars('lastupdatedvehiclesmain_on.updated_vehicles', array(
       					'U_VIEW_VEHICLE' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_updated['id']),
       					'VEHICLE' => $vehicle_updated['vehicle'],
       					'UPDATED_TIME' => create_date($board_config['default_dateformat'], $vehicle_updated['date_updated'], $board_config['board_timezone']),
		       			'USERNAME' => $vehicle_updated['username'],
		       			'U_VIEW_PROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=".$vehicle_updated['member_id']))
      				);
			}
			$db->sql_freeresult($result);
		}

		$template->pparse('menu');
		return ;
	}

	/*========================================================================*/
	// Builds The HTML For Selecting Years
	// Usage: build_year_html('selected year');
	/*========================================================================*/
	function build_year_html($selected = 0)
	{
		global $garage_config, $template;
	
		// Grab the current year
	        //$current_date = getdate();
		$my_array = localtime(time(), 1) ;
		$current_date = $my_array["tm_year"] +1900 ;
	
	        // Calculate end year based on offset configured
	        $end_year = $current_date + $garage_config['year_end'];
	
		// A simple check to prevent infinite loop
		if ( $garage_config['year_start'] > $end_year ) {
			return;
		}	
	
		$year_list = "<select name='year' class='forminput'>";
	
		for ( $year = $end_year; $year >= $garage_config['year_start']; $year-- ) {
			if ( $year == $selected ) 
			{
				$year_list .= "<option value='$year' selected='selected'>$year</option>";
			} 
			else 
			{
				$year_list .= "<option value='$year'>$year</option>";
			}
		}
	
		$year_list .= "</select>";
	
		$template->assign_vars(array(
			'YEAR_LIST' => $year_list)
		);
	
		return ;
	}
	
	/*========================================================================*/
	// Check A User Owns The Vehicle, If Not Display Message
	// Usage: check_own_vehicle('vehicle id');
	/*========================================================================*/
	function check_own_vehicle($cid)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		if (empty($cid))
		{
	 		message_die(GENERAL_ERROR, 'Vehicle ID Not Entered..', '', __LINE__, __FILE__);
		}
	
		$sql = "SELECT g.member_id FROM " . GARAGE_TABLE . " AS g WHERE g.id = $cid ";
	
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
		}
	
		$vehicle = $db->sql_fetchrow($result); 
		$db->sql_freeresult($result);
	
		if ( $userdata['user_level'] == ADMIN || $userdata['user_level'] == MOD )
		{
			//Allow A Moderator Or Administrator Do What They Want....
			return;
		}
		else if ( $vehicle['member_id'] != $userdata['user_id'] )
		{
			$message = $lang['Not_Vehicle_Owner'] . "<br /><br />" . sprintf($lang['Click_return_garage'], "<a href=\"" . append_sid("garage.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_index'], "<a href=\"" . append_sid("index.$phpEx") . "\">", "</a>");
	
			message_die(GENERAL_MESSAGE, $message);
		}
	
		return ;
	}
	
	/*========================================================================*/
	// Builds The HTML For A Selecting A Garage
	// Usage: build_garage_install_list_html('business id', 'business name');
	/*========================================================================*/
	function build_garage_install_list_html($selected,$selected_name)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		$garage_install_list = "<select name='install_business_id' class='forminput'>";
	
		if (!empty($selected) )
		{
			$garage_install_list .= "<option value='$selected' selected='selected'>$selected_name</option>";
			$garage_install_list .= "<option value=''>------</option>";
		}
		else
		{
			$garage_install_list .= "<option value=''>".$lang['Select_A_Business']."</option>";
			$garage_install_list .= "<option value=''>------</option>";
		}
	
		$sql = "SELECT id, title FROM " . GARAGE_BUSINESS_TABLE . " WHERE garage = 1 ORDER BY title ASC";
	
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query businesses', '', __LINE__, __FILE__, $sql);
		}
	
		while ( $garage_list = $db->sql_fetchrow($result) )
		{
			$garage_install_list .= "<option value='".$garage_list['id']."'>".$garage_list['title']."</option>";
		}
		$db->sql_freeresult($result);
		
		$garage_install_list .= "</select>";
	
		$template->assign_vars(array(
			'GARAGE_INSTALL_LIST' => $garage_install_list)
		);
	
		return ;
	}

	/*========================================================================*/
	// Builds The HTML For A Selecting A Business To Reassign To
	// Usage: build_garage_install_list_html('business id');
	/*========================================================================*/
	function build_reassign_business_list($exclude)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		$html = "<select name='id' class='forminput'>";
	
		$sql = "SELECT id, title	
			FROM " . GARAGE_BUSINESS_TABLE . " 
			WHERE pending = 0 	
				and id != $exclude
			ORDER BY title ASC";
	
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query businesses', '', __LINE__, __FILE__, $sql);
		}
	
		while ( $row = $db->sql_fetchrow($result) )
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
	// Builds The HTML For Selecting Any Business
	// Usage:  build_business_list_html('business id', 'business name');
	/*========================================================================*/
	function build_business_list_html($selected,$selected_name)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		$business_list = "<select name='id' class='forminput'>";
	
		if (!empty($selected) )
		{
			$business_list .= "<option value='$selected' selected='selected'>$selected_name</option>";
			$business_list .= "<option value=''>------</option>";
		}
		else
		{
			$business_list .= "<option value=''>".$lang['Select_A_Business']."</option>";
			$business_list .= "<option value=''>------</option>";
		}
	
		$sql = "SELECT id, title FROM " . GARAGE_BUSINESS_TABLE . " ORDER BY title ASC";
	
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query businesses', '', __LINE__, __FILE__, $sql);
		}
	
		while ( $business = $db->sql_fetchrow($result) )
		{
			$business_list .= "<option value='".$business['id']."'>".$business['title']."</option>";
		}
		$db->sql_freeresult($result);
		
		$business_list .= "</select>";
	
		$template->assign_vars(array(
			'BUSINESS_LIST' => $business_list)
		);
	
		return ;
	}
	
	/*========================================================================*/
	// Builds The HTML For Selecting Insurance Business
	// Usage: build_insurance_list_html('<business id', 'business name');
	/*========================================================================*/
	function build_insurance_list_html($selected,$selected_name)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		$insurance_list = "<select name='business_id' class='forminput'>";
	
		if (!empty($selected) )
		{
			$insurance_list .= "<option value='$selected' selected='selected'>$selected_name</option>";
			$insurance_list .= "<option value=''>------</option>";
		}
		else
		{
			$insurance_list .= "<option value=''>".$lang['Select_A_Business']."</option>";
			$insurance_list .= "<option value=''>------</option>";
		}
	
		$sql = "SELECT id, title FROM " . GARAGE_BUSINESS_TABLE . " WHERE insurance = 1 ORDER BY title ASC";
	
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query businesses', '', __LINE__, __FILE__, $sql);
		}
	
		while ( $insurance = $db->sql_fetchrow($result) )
		{
			$insurance_list .= "<option value='".$insurance['id']."'>".$insurance['title']."</option>";
		}
		$db->sql_freeresult($result);
		
		$insurance_list .= "</select>";
	
		$template->assign_vars(array(
			'INSURANCE_LIST' => $insurance_list)
		);
	
		return ;
	}
	
	/*========================================================================*/
	// Builds The HTML For Selecting A Shop
	// Usage: build_shop_list_html('business id', 'business name');
	/*========================================================================*/
	function build_shop_list_html($selected,$selected_name)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		$shop_list = "<select name='business_id' class='forminput'>";
	
		if (!empty($selected) )
		{
			$shop_list .= "<option value='$selected' selected='selected'>$selected_name</option>";
			$shop_list .= "<option value=''>------</option>";
		}
		else
		{
			$shop_list .= "<option value=''>".$lang['Select_A_Business']."</option>";
			$shop_list .= "<option value=''>------</option>";
		}
	
		$sql = "SELECT id, title 
	       		FROM " . GARAGE_BUSINESS_TABLE . " WHERE retail_shop = 1 OR web_shop = 1
			ORDER BY title ASC";
	
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query businesses', '', __LINE__, __FILE__, $sql);
		}
	
		while ( $shop = $db->sql_fetchrow($result) )
		{
			$shop_list .= "<option value='".$shop['id']."'>".$shop['title']."</option>";
		}
		$db->sql_freeresult($result);
		
		$shop_list .= "</select>";
	
		$template->assign_vars(array(
			'SHOP_LIST' => $shop_list)
		);
	
		return ;
	}
	
	/*========================================================================*/
	// Builds The HTML For Selecting A Rollingroad Entry
	// Usage: build_garage_install_list_html('rollingroad id', 'bhp @ bhp_type', 'vehicle id');
	/*========================================================================*/
	function build_rr_list_html($selected,$selected_name,$cid)
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
		}
	
		$sql = "SELECT id, bhp, bhp_unit FROM " . GARAGE_ROLLINGROAD_TABLE . " WHERE garage_id = $cid";
	
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query rollingroad', '', __LINE__, __FILE__, $sql);
		}
	
		while ( $rollingroad = $db->sql_fetchrow($result) )
		{
			$rr_list .= "<option value='".$rollingroad['id']."'>".$rollingroad['bhp']." BHP @ ".$rollingroad['bhp_unit']."</option>";
		}
		$db->sql_freeresult($result);
		
		$rr_list .= "</select>";
	
		$template->assign_vars(array(
			'RR_LIST' => $rr_list)
		);
	
		return;
	}
	
	/*========================================================================*/
	// Builds The HTML For Selecting Category Type Of Modification
	// Usage: build_category_html('category id');
	/*========================================================================*/
	function build_category_html($selected)
	{
		global $template, $db;

	        $html = '<select name="category_id" class="forminput">';
	
	        $sql = "SELECT * FROM " . GARAGE_CATEGORIES_TABLE . " ORDER BY field_order ASC";
	
	     	if ( !($result = $db->sql_query($sql)) )
	      	{
	        	message_die(GENERAL_ERROR, 'Could not category of mods for vehicle', '', __LINE__, __FILE__, $sql);
	      	}
	
	        while ( $row = $db->sql_fetchrow($result) ) 
		{
			$select = ( $selected == $row['id'] ) ? ' selected="selected"' : '';
			$html .= '<option value="' . $row['id'] . '"' . $select . '>' . $row['title'] . '</option>';
	        }
	
	        $html .= '</select>';
	
		$template->assign_vars(array(
			'CATEGORY_LIST' => $html)
		);
	
		return ;
	}
	
	/*========================================================================*/
	// Builds The HTML For Selection Box
	// Usage: build_selection_box('select name', 'options text', 'options values', 'selected option');
	/*========================================================================*/
	function build_selection_box($select_name,$select_text,$select_types,$selected_option)
	{
		global $template, $lang;
	
		$select = "<select name='".$select_name."'>";
		if (empty($selected_option))
		{
			$select .= "<option value=''>".$lang['Select_A_Option']."</option>";
			$select .= "<option value=''>------</option>";
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
	// Usage: build_attach_image_html('modification'|'vehicle');
	/*========================================================================*/
	function build_attach_image_html($type)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		if (!$this->check_permissions('UPLOAD',''))
		{
			return ;
		}
	
		$maximum_image_file_size = $garage_config['max_image_kbytes'];
		$maximum_image_resolution = $garage_config['max_image_resolution'];
	
		$template->assign_vars(array(
			'L_IMAGE_ATTACH' => $lang['Image_Attach'],
			'L_MAXIMUM_IMAGE_FILE_SIZE' => $lang['Maximum_Image_File_Size'],
			'L_MAXIMUM_IMAGE_RESOLUTION' => $lang['Maximum_Image_Resolution'],
			'L_IMAGE_ATTACHMENTS' => $lang['Image_Attachments'],
			'L_ENTER_IMAGE_URL' => $lang['Enter_Image_Url'],
			'L_Kbytes' => $lang['kbytes'],
			'MAXIMUM_IMAGE_FILE_SIZE' => $maximum_image_file_size,
			'MAXIMUM_IMAGE_RESOLUTION' => $maximum_image_resolution,
			'Add_New_Image' => $lang['Add_New_Image'])
		);
	
		if ( $type == 'modification' )
		{
			if ( $garage_config['allow_mod_image'] ) 
			{
		      		$template->assign_block_vars('allow_images', array());
				if ( $garage_config['allow_image_upload'] )
				{
		      			$template->assign_block_vars('allow_images.upload_images', array());
			
				}
				if ( $garage_config['allow_image_url'] )
				{
		      			$template->assign_block_vars('allow_images.remote_images', array());
				}
			}
		}
		else if ( $type == 'vehicle' )
		{
			if ( $garage_config['allow_image_upload'] || $garage_config['allow_image_url'])
			{
		      		$template->assign_block_vars('allow_images', array());
				if ( $garage_config['allow_image_upload'] )
				{
		      			$template->assign_block_vars('allow_images.upload_images', array());
				}
				if ( $garage_config['allow_image_url'] )
				{
		      			$template->assign_block_vars('allow_images.remote_images', array());
				}
			}
		}
	
		return;
	}
	
	/*========================================================================*/
	// Builds The HTML For Editting Already Attached Images
	// Usage: build_edit_image_html('image id', 'image name'),
	/*========================================================================*/
	function build_edit_image_html($image_id, $image_name)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		if (!$this->check_permissions('UPLOAD',''))
		{
			return ;
		}
	
		if ( $garage_config['allow_mod_image'] ) 
		{
			$maximum_image_file_size = $garage_config['max_image_kbytes'];
			$maximum_image_resolution = $garage_config['max_image_resolution'];
	
	      		$template->assign_block_vars('allow_images', array());
			$template->assign_vars(array(
				'L_IMAGE_ATTACHMENTS' => $lang['Image_Attachments'],
				'L_IMAGE_ATTACH' => $lang['Image_Attach'],
				'L_MAXIMUM_IMAGE_FILE_SIZE' => $lang['Maximum_Image_File_Size'],
				'L_MAXIMUM_IMAGE_RESOLUTION' => $lang['Maximum_Image_Resolution'],
				'L_REPLACE_WITH_NEW_IMAGE' => $lang['Replace_With_New_Image'],
				'L_ENTER_IMAGE_URL' => $lang['Enter_Image_Url'],
				'L_KEEP_CURRENT_IMAGE' => $lang['Enter_Image_Url'],
				'L_REMOVE_IMAGE' => $lang['Remove_Image'],
				'L_KEEP_CURRENT_IMAGE' => $lang['Keep_Current_Image'],
				'L_REPLACE_WITH_NEW_IMAGE' => $lang['Replace_With_New_Image'],
				'L_REPLACE_WITH_NEW_REMOTE_IMAGE' => $lang['Replace_With_New_Remote_Image'],
				'MAXIMUM_IMAGE_FILE_SIZE' => $maximum_image_file_size,
				'MAXIMUM_IMAGE_RESOLUTION' => $maximum_image_resolution)
			);
			
			if ( !empty($image_id) )
			{
	      			$template->assign_block_vars('allow_images.keep_image', array(
					'CURRENT_IMAGE' => $image_name)
				);
	
	      			$template->assign_block_vars('allow_images.remove_image', array(
					'IMAGE_ID' => $image_id)
				);
				
				if  ($garage_config['allow_image_upload'] )
				{
	      				$template->assign_block_vars('allow_images.replace_image_upload', array());
				}
				if ( ($garage_config['allow_image_url'] ) AND (empty($image_id) == FALSE))
				{
	      				$template->assign_block_vars('allow_images.replace_remote_image', array());
				}
			}
			elseif (empty($image_id) )
			{
				if ( $garage_config['allow_image_upload'] )
				{
	      				$template->assign_block_vars('allow_images.upload_images', array());
				}
				if ( ($garage_config['allow_image_url'] ) AND (empty($image_id) == TRUE))
				{
	      				$template->assign_block_vars('allow_images.remote_images', array());
				}
			}
	
			$template->assign_vars(array(
				'Add_New_Image' => $lang['Add_New_Image'])
			);
		}
		return;
	}
	
	/*========================================================================*/
	// Update Vehicle Modified Time
	// Usage: update_vehicle_time('vehicle id');
	/*========================================================================*/
	function update_vehicle_time($cid)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
		
		$data['time'] = time();

		$this->update_single_field(GARAGE_TABLE, 'date_updated', $data['time'], 'id', $cid);
	
		return;
	}
	
	/*========================================================================*/
	// Build Featured Vehicle HTML If Required 
	// Usage: show_featuredvehicle();
	/*========================================================================*/
	function show_featuredvehicle()
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		if ( $garage_config['enable_featured_vehicle'] == 1 )
		{
			$template->assign_block_vars('show_featured_vehicle', array());

			// If we are using random, go fetch!
	       		if ( $garage_config['featured_vehicle_random'] == 'on' )
       			{
				$sql = "SELECT g.id 
					FROM " . GARAGE_TABLE . " g
	                        		LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
			                        LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id 
					WHERE makes.pending = 0 and models.pending = 0 and image_id IS NOT NULL 
					ORDER BY rand() LIMIT 1";

    				if(!$result = $db->sql_query($sql))
				{
					message_die(GENERAL_ERROR, "Could Not query vehicle", "", __LINE__, __FILE__, $sql);
				}

				$vehicle_data = $db->sql_fetchrow($result);
				$featured_vehicle_id = $vehicle_data['id'];
	       			$where = "WHERE g.id='".$vehicle_data['id']."' GROUP BY g.id";
	 	 	}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Newest_Vehicles'] )
			{
	       			$where = "WHERE makes.pending = 0 and models.pending = 0 
					  GROUP BY g.id ORDER BY g.date_created DESC LIMIT 1";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Last_Updated_Vehicles'] )
			{
	       			$where = "WHERE makes.pending = 0 and models.pending = 0 
					  GROUP BY g.id ORDER BY g.date_updated DESC LIMIT 1";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Newest_Modifications'] )
			{
	       			$where = "WHERE makes.pending = 0 and models.pending = 0 
					  GROUP BY g.id ORDER BY mods.date_created DESC LIMIT 1";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Last_Updated_Modifications'] )
			{
	       			$where = "WHERE makes.pending = 0 and models.pending = 0 
					  GROUP BY g.id ORDER BY mods.date_updated DESC LIMIT 1";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Most_Modified_Vehicle'] )
			{
	       			$where = "WHERE makes.pending = 0 and models.pending = 0 
					  GROUP BY g.id ORDER BY mod_count DESC LIMIT 1";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Most_Money_Spent'] )
			{
	       			$where = "WHERE makes.pending = 0 and models.pending = 0 
					  GROUP BY g.id ORDER BY money_spent DESC LIMIT 1";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Most_Viewed_Vehicle'] )
			{
	       			$where = "WHERE makes.pending = 0 and models.pending = 0 
					  GROUP BY g.id ORDER BY g.views DESC LIMIT 1";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Latest_Vehicle_Comments'] )
			{
				$where = "LEFT JOIN " . GARAGE_GUESTBOOKS_TABLE . " AS gb on g.id = gb.garage_id
	  				  WHERE makes.pending = 0 and models.pending = 0
					  GROUP BY g.id ORDER BY gb.post_date DESC LIMIT 1";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Top_Quartermile_Runs'] )
			{
				$where = "LEFT JOIN " . GARAGE_QUARTERMILE_TABLE . " AS qm on g.id = qm.garage_id
	  				  WHERE makes.pending = 0 and models.pending = 0
					  GROUP BY g.id ORDER BY qm.quart ASC LIMIT 1";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Top_Dyno_Runs'] )
			{
				$where = "LEFT JOIN " . GARAGE_ROLLINGROAD_TABLE . " AS rr on g.id = rr.garage_id
	  				  WHERE makes.pending = 0 and models.pending = 0
					  GROUP BY g.id ORDER BY rr.bhp DESC LIMIT 1";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Top_Rated_Vehicles'] )
			{
				 $where = "WHERE makes.pending = 0 and models.pending = 0
				 	   GROUP BY g.id ORDER BY rating DESC LIMIT 1";
			}
			else
			{
				$featured_vehicle_id = $garage_config['featured_vehicle_id'];
		    		$where = "WHERE g.id='".$garage_config['featured_vehicle_id']."' GROUP BY g.id";
			}

		        // Make sure the vehicle exists
		        $sql = "SELECT id FROM " . GARAGE_TABLE . " WHERE id='". $featured_vehicle_id ."'";
			if(!$result = $db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, "Could Not Query Vehicle", "", __LINE__, __FILE__, $sql);
			}
	
		        if ( ($db->sql_numrows($result)) > 0 OR (!empty($garage_config['featured_vehicle_from_block'])) )
	        	{
		            	// Grab the vehicle info and prep the HTML
				$sql = "SELECT g.id, g.made_year, g.image_id, g.member_id, makes.make, models.model, 
	                           	images.attach_id, images.attach_hits, images.attach_thumb_location, m.username, 
			                images.attach_is_image, images.attach_location, COUNT(mods.id) AS mod_count,
					CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle,
					(SUM(mods.install_price) + SUM(mods.price)) AS money_spent, sum( r.rating ) AS rating
	                 	        FROM " . GARAGE_TABLE . " AS g 
	                        		LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
		                            	LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = g.image_id
			                        LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id 
			                        LEFT JOIN " . GARAGE_MODS_TABLE . " AS mods ON g.id = mods.garage_id 
			                        LEFT JOIN " . GARAGE_RATING_TABLE . " AS r ON g.id = r.garage_id 
	        		                LEFT JOIN " . USERS_TABLE . " AS m ON g.member_id = m.user_id
				    	$where";
	
				if(!$result = $db->sql_query($sql))
				{
					message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
				}
		
	        	    	$vehicle_data = $db->sql_fetchrow($result);
	
		        	// Do we have a hilite image?  If so, prep the HTML
				if ( (empty($vehicle_data['image_id']) == FALSE) AND ($vehicle_data['attach_is_image'] == 1) ) 
	        	    	{
	                		// Do we have a thumbnail?  If so, our job is simple here :)
			                if ( (empty($vehicle_data['attach_thumb_location']) == FALSE) AND ($vehicle_data['attach_thumb_location'] != $vehicle_data['attach_location']) AND (@file_exists($phpbb_root_path . GARAGE_UPLOAD_PATH."/".$vehicle_data['attach_thumb_location'])) )
	                		{
			                   	// Yippie, our thumbnail is already made for us :)
					   	$thumb_image = $phpbb_root_path . GARAGE_UPLOAD_PATH . $vehicle_data['attach_thumb_location'];
						$featured_image = '<a href="garage.'.$phpEx.'?mode=view_gallery_item&amp;type=garage_mod&amp;image_id='. $vehicle_data['attach_id'] .'" title="' . $vehicle_data['attach_file'] .'" target="_blank"><img hspace="5" vspace="5" src="' . $thumb_image .'" class="attach"  /></a>';
	                		} 
	        		}
				$template->assign_vars(array(
					'FEATURED_DESCRIPTION' => $garage_config['featured_vehicle_description'],
					'FEATURED_IMAGE' => $featured_image,
					'VEHICLE' => $vehicle_data['vehicle'],
					'USERNAME' => $vehicle_data['username'],
					'U_VIEW_VEHICLE' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
					'U_VIEW_PROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=".$vehicle_data['member_id']))
				);
			}
		}
		else
		{
			$template->assign_block_vars('no_featured_vehicle', array());
		}
	
	        return ;
	}
	
	/*========================================================================*/
	// Build Last Commented Vehicle HTML If Required 
	// Usage: show_lastcommented();
	/*========================================================================*/
	function show_lastcommented()
	{
		global $required_position, $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		if ( $garage_config['lastcommented_on'] != TRUE )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $lang['Latest_Vehicle_Comments'],
			'COLUMN_1_TITLE' => $lang['Vehicle'],
			'COLUMN_2_TITLE' => $lang['Author'],
			'COLUMN_3_TITLE' => $lang['Posted_Date'])
		);
	
	        // What's the count? Default to 10
	        $limit = $garage_config['lastcommented_limit'] ? $garage_config['lastcommented_limit'] : 10;
	 		 		
	 	$sql = "SELECT gb.garage_id AS id, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, 
	                        gb.author_id AS member_id, gb.post_date AS POI, m.username 
	                FROM " . GARAGE_GUESTBOOKS_TABLE . " AS gb 
	                	LEFT JOIN " . GARAGE_TABLE . " AS g ON gb.garage_id = g.id
	                        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
	                        LEFT JOIN " . USERS_TABLE . " AS m ON gb.author_id = m.user_id
			WHERE makes.pending = 0 AND models.pending = 0
	                ORDER BY POI DESC LIMIT $limit";
	
	 	if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
		}
	 		            
	 	while ( $vehicle_data = $db->sql_fetchrow($result) )
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
				'U_COLUMN_2' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=".$vehicle_data['member_id']),
				'COLUMN_1_TITLE' => $vehicle_data['vehicle'],
				'COLUMN_2_TITLE' => $vehicle_data['username'],
				'COLUMN_3' => create_date('D M d, Y G:i', $vehicle_data['POI'], $board_config['board_timezone']))
			);
	 	}
	
		$required_position++;
		return ;
	}
	
	/*========================================================================*/
	// Build updated vehicles HTML if required 
	// Usage: show_updated_vehicles();
	/*========================================================================*/
	function show_updated_vehicles()
	{
		global $required_position, $userdata, $template, $db, $SID, $lang, $phpEx, $garage_config, $board_config;
	
		if ( $garage_config['lastupdatedvehicles_on'] != TRUE )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $lang['Last_Updated_Vehicles'],
			'COLUMN_1_TITLE' => $lang['Vehicle'],
			'COLUMN_2_TITLE' => $lang['Owner'],
			'COLUMN_3_TITLE' => $lang['Updated'])
		);
	 		
	        // What's the count? Default to 10
	        $limit = $garage_config['lastupdatedvehicles_limit'] ? $garage_config['lastupdatedvehicles_limit'] : 10;
	
	 	$sql = "SELECT g.id, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, 
	                       g.member_id, g.date_updated AS POI, m.username 
	               	FROM " . GARAGE_TABLE . " AS g 
	                       	LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
	       	                LEFT JOIN " . USERS_TABLE . " AS m ON g.member_id = m.user_id
			WHERE makes.pending = 0 AND models.pending = 0
	                ORDER BY POI DESC LIMIT $limit";
	 		            
	 	if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
		}
	 		            
	 	while ( $vehicle_data = $db->sql_fetchrow($result) )
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
				'U_COLUMN_2' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=".$vehicle_data['member_id']),
				'COLUMN_1_TITLE' => $vehicle_data['vehicle'],
				'COLUMN_2_TITLE' => $vehicle_data['username'],
				'COLUMN_3' => create_date('D M d, Y G:i', $vehicle_data['POI'], $board_config['board_timezone']))
			);
	 	}
	
		$required_position++;
		return ;
	}
	
	/*========================================================================*/
	// Build Most Modified HTML If Required 
	// Usage: show_most_modified();
	/*========================================================================*/
	function show_most_modified()
	{
		global $required_position, $userdata, $template, $db, $SID, $lang, $phpEx, $garage_config, $board_config;
	
		if ( $garage_config['mostmodded_on'] != TRUE )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $lang['Most_Modified_Vehicle'],
			'COLUMN_1_TITLE' => $lang['Vehicle'],
			'COLUMN_2_TITLE' => $lang['Owner'],
			'COLUMN_3_TITLE' => $lang['Mods'])
		);
	
	        // What's the count? Default to 10
	        $limit = $garage_config['mostmodded_limit'] ? $garage_config['mostmodded_limit'] : 10;
	
	 	$sql = "SELECT g.id, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, 
	                        g.member_id, COUNT(mods.id) AS POI, m.username 
	                FROM " . GARAGE_TABLE . " AS g 
	                	LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
	                        LEFT JOIN " . USERS_TABLE . " AS m ON g.member_id = m.user_id 
	                        LEFT JOIN " . GARAGE_MODS_TABLE . " AS mods ON mods.garage_id = g.id
			WHERE makes.pending = 0 AND models.pending = 0
	                GROUP BY g.id 
	                ORDER BY POI DESC LIMIT $limit";
	 		            
	 	if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
		}
	 		            
	 	while ( $vehicle_data = $db->sql_fetchrow($result) )
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
				'U_COLUMN_2' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=".$vehicle_data['member_id']),
				'COLUMN_1_TITLE' => $vehicle_data['vehicle'],
				'COLUMN_2_TITLE' => $vehicle_data['username'],
				'COLUMN_3' => $vehicle_data['POI'])
			);
	 	}
	
		$required_position++;
		return ;
	}
	
	/*========================================================================*/
	// Build Most Spent HTML If Required 
	// Usage: show_most_spent();
	/*========================================================================*/
	function show_most_spent()
	{
		global $required_position, $userdata, $template, $db, $SID, $lang, $phpEx, $garage_config, $board_config;
	
		if ( $garage_config['mostmoneyspent_on'] != TRUE )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $lang['Most_Money_Spent'],
			'COLUMN_1_TITLE' => $lang['Vehicle'],
			'COLUMN_2_TITLE' => $lang['Owner'],
			'COLUMN_3_TITLE' => $lang['Total_Spent'])
		);
	 		
	        // What's the count? Default to 10
	        $limit = $garage_config['mostmoneyspent_limit'] ? $garage_config['mostmoneyspent_limit'] : 10;
	 		
	 	$sql = "SELECT g.id, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, 
	                        g.member_id, (SUM(mods.install_price) + SUM(mods.price)) AS POI, m.username, g.currency 
	                FROM " . GARAGE_TABLE . " AS g 
	                	LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
	                        LEFT JOIN " . GARAGE_MODS_TABLE . " AS mods ON mods.garage_id = g.id 
	                        LEFT JOIN " . USERS_TABLE . " AS m ON g.member_id = m.user_id
			WHERE makes.pending = 0 AND models.pending = 0
	                GROUP BY g.id 
	                ORDER BY POI DESC LIMIT $limit";
	 		            
	 	if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
		}
	 		            
	 	while ( $vehicle_data = $db->sql_fetchrow($result) )
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
				'U_COLUMN_2' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=".$vehicle_data['member_id']),
				'COLUMN_1_TITLE' => $vehicle_data['vehicle'],
				'COLUMN_2_TITLE' => $vehicle_data['username'],
				'COLUMN_3' => $vehicle_data['POI'])
			);
	 	}
	
		$required_position++;
		return ;
	}
	
	/*========================================================================*/
	// Build Most Viewed Vehicle HTML If Required 
	// Usage: show_most_viewed();
	/*========================================================================*/
	function show_most_viewed()
	{
		global $required_position, $userdata, $template, $db, $SID, $lang, $phpEx, $garage_config, $board_config;
	
		if ( $garage_config['mostviewed_on'] != TRUE )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $lang['Most_Viewed_Vehicle'],
			'COLUMN_1_TITLE' => $lang['Vehicle'],
			'COLUMN_2_TITLE' => $lang['Owner'],
			'COLUMN_3_TITLE' => $lang['Views'])
		);
	
	        // What's the count? Default to 10
	        $limit = $garage_config['mostviewed_limit'] ? $garage_config['mostviewed_limit'] : 10;
	 		 		
	 	$sql = "SELECT g.id, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, 
	                        g.member_id, g.views AS POI, m.username 
	                FROM " . GARAGE_TABLE . " AS g 
	                        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
	                        LEFT JOIN " . USERS_TABLE . " AS m ON g.member_id = m.user_id
			WHERE makes.pending = 0 AND models.pending = 0
	                ORDER BY POI DESC LIMIT $limit";
	 		            
	 	if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
		}
	 		            
	 	while ( $vehicle_data = $db->sql_fetchrow($result) )
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
				'U_COLUMN_2' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=".$vehicle_data['member_id']),
				'COLUMN_1_TITLE' => $vehicle_data['vehicle'],
				'COLUMN_2_TITLE' => $vehicle_data['username'],
				'COLUMN_3' => $vehicle_data['POI'])
			);
	 	}
	
		$required_position++;
		return ;
	}
	
	/*========================================================================*/
	// Build Top Rated HTML If Required 
	// Usage: show_most_viewed();
	/*========================================================================*/
	function show_toprated()
	{
		global $required_position, $userdata, $template, $db, $SID, $lang, $phpEx, $garage_config, $board_config;
	
		if ( $garage_config['toprated_on'] != TRUE )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $lang['Top_Rated_Vehicles'],
			'COLUMN_1_TITLE' => $lang['Vehicle'],
			'COLUMN_2_TITLE' => $lang['Owner'],
			'COLUMN_3_TITLE' => $lang['Rating'])
		);
	
	        // What's the count? Default to 10
	        $limit = $garage_config['toprated_limit'] ? $garage_config['toprated_limit'] : 10;
	
		$sql =  "SELECT g.id, g.member_id, m.username, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, sum( r.rating ) AS rating, count( * ) *10 AS total_rating
			 FROM " . GARAGE_RATING_TABLE . " AS r
				LEFT JOIN " . GARAGE_TABLE . " AS g ON r.garage_id = g.id
	                        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
	                        LEFT JOIN " . USERS_TABLE . " AS m ON g.member_id = m.user_id
			 WHERE makes.pending = 0 AND models.pending = 0
			 GROUP BY garage_id
			 ORDER BY rating DESC LIMIT $limit";
	 		 		
	 	if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
		}
	 		            
	 	while ( $vehicle_data = $db->sql_fetchrow($result) )
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
				'U_COLUMN_2' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=".$vehicle_data['member_id']),
				'COLUMN_1_TITLE' => $vehicle_data['vehicle'],
				'COLUMN_2_TITLE' => $vehicle_data['username'],
				'COLUMN_3' => $vehicle_data['rating'] . '/' . $vehicle_data['total_rating'])
			);
	 	}
	
		$required_position++;
		return ;
	}
	
	/*========================================================================*/
	// Build Newest Vehicles HTML If Required 
	// Usage:  show_newest_vehicles()
	/*========================================================================*/
	function show_newest_vehicles()
	{
		global $required_position, $userdata, $template, $db, $SID, $lang, $phpEx, $garage_config, $board_config;
	
		if ( $garage_config['newestvehicles_on'] != TRUE )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $lang['Newest_Vehicles'],
			'COLUMN_1_TITLE' => $lang['Vehicle'],
			'COLUMN_2_TITLE' => $lang['Owner'],
			'COLUMN_3_TITLE' => $lang['Created'])
		);
	 		
	        // What's the count? Default to 10
	        $limit = $garage_config['newestvehicles_limit'] ? $garage_config['newestvehicles_limit'] : 10;
	 		 		
	 	$sql = "SELECT g.id, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, 
	                        g.member_id, g.date_created AS POI, m.username 
	                FROM " . GARAGE_TABLE . " AS g 
	                	LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
	                        LEFT JOIN " . USERS_TABLE . " AS m ON g.member_id = m.user_id
			WHERE makes.pending = 0 AND models.pending = 0
	                ORDER BY POI DESC LIMIT $limit";
	 		            
	 	if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
		}
	 		            
	 	while ( $vehicle_data = $db->sql_fetchrow($result) )
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
				'U_COLUMN_2' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=".$vehicle_data['member_id']),
				'COLUMN_1_TITLE' => $vehicle_data['vehicle'],
				'COLUMN_2_TITLE' => $vehicle_data['username'],
				'COLUMN_3' => create_date('D M d, Y G:i', $vehicle_data['POI'], $board_config['board_timezone']))
			);
	 	}
	
		$required_position++;
		return ;
	}
	
	/*========================================================================*/
	// Delete A Vehicle From The Garage Including All Related Data
	// Usage: delete_vehicle('vehicle id');
	/*========================================================================*/
	function delete_vehicle($cid)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		//Right User Want To Delete Vehicle Let Get All Mods Associated With It 
		$mods_sql = "SELECT id FROM " . GARAGE_MODS_TABLE . " WHERE garage_id = $cid";
	
		if ( !($mods_result = $db->sql_query($mods_sql)) )
	     	{
	       		message_die(GENERAL_ERROR, 'Could Not Select Modification Data For Vehicle', '', __LINE__, __FILE__, $sql);
	      	}
	
		while ($mods_row = $db->sql_fetchrow($mods_result) )
		{
			$this->delete_modification($mods_row['id']);
		}
	
		//Right User Want To Delete Vehicle Let Get All Quartermile Times Associated With It 
		$quartermile_sql = "SELECT id FROM " . GARAGE_QUARTERMILE_TABLE . " WHERE garage_id = $cid";
	
	     	if ( !($quartermile_result = $db->sql_query($quartermile_sql)) )
	      	{
	        	message_die(GENERAL_ERROR, 'Could Not Select Quartermile Data For Vehicle', '', __LINE__, __FILE__, $sql);
	      	}
	
		while ($quartermile_row = $db->sql_fetchrow($quartermile_result) )
		{
			$qmid = $quartermile_row['id'];
			$this->delete_quartermile_time($qmid);
		}
		$db->sql_freeresult($quartermile_result);
	
		//Right User Want To Delete Vehicle Let Get All Rolling Road Times Associated With It 
		$rollingroad_sql = "SELECT id FROM " . GARAGE_ROLLINGROAD_TABLE . " WHERE garage_id = $cid";
	
	     	if ( !($rollingroad_result = $db->sql_query($rollingroad_sql)) )
	     	{
	       		message_die(GENERAL_ERROR, 'Could Not Select Rollingroad Data For Vehicle', '', __LINE__, __FILE__, $sql);
	     	}
	
		while ($rollingroad_row = $db->sql_fetchrow($rollingroad_result) )
		{
			$rrid = $rollingroad_row['id'];
			$this->delete_rollingroad_run($rrid);
		}
		$db->sql_freeresult($rollingroad_result);
	
		//Right User Want To Delete Vehicle Let Get All Insurance Premiums Associated With It 
		$insurance_sql = "SELECT id FROM " . GARAGE_INSURANCE_TABLE . " WHERE garage_id = $cid";
	
		if ( !($insurance_result = $db->sql_query($insurance_sql)) )
	     	{
	       		message_die(GENERAL_ERROR, 'Could Not Select Insurance Data', '', __LINE__, __FILE__, $sql);
	     	}
	
		while ($insurance_row = $db->sql_fetchrow($insurance_result) )
		{
			$ins_id = $insurance_row['id'];
			$this->delete_insurance($ins_id);
		}
		$db->sql_freeresult($insurance_result);

		//Right User Want To Delete Vehicle Let Get All GuestBook Associated With It
		$gb_sql = "SELECT id FROM " . GARAGE_GUESTBOOKS_TABLE . " WHERE garage_id = $cid";

		if ( !($db_result = $db->sql_query($gb_sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Guestbook Data For Vehicle', '', __LINE__, __FILE__, $sql);
		}

		while ($gb_row = $db->sql_fetchrow($mods_result) )
		{
			$this->delete_rows(GARAGE_GUESTBOOKS, 'id', $gb_row['id']);
		}

		//Right User Want To Delete Vehicle Let Get All Ratings Associated With It
		$rating_sql = "SELECT id FROM " . GARAGE_RATING_TABLE . " WHERE garage_id = $cid";

		if ( !($rating_result = $db->sql_query($rating_sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Rating Data For Vehicle', '', __LINE__, __FILE__, $sql);
		}

		while ($rating_row = $db->sql_fetchrow($mods_result) )
		{
   			$this->delete_rows(GARAGE_RATING_TABLE, 'id', $rating_row['id']);
		} 
	
		// Right Lets Delete All Images For This Vehicle
		$sql = "SELECT image_id	FROM " . GARAGE_GALLERY_TABLE . " WHERE garage_id = $cid ";
	
	     	if ( !($result = $db->sql_query($sql)) )
	     	{
	        	message_die(GENERAL_ERROR, 'Could Select Image Data For Vehicle', '', __LINE__, __FILE__, $sql);
	     	}
	
		while ($gallery_row = $db->sql_fetchrow($result) )
		{
			$image_id = $gallery_row['image_id'];
			$this->delete_image($image_id);
		}
		$db->sql_freeresult($result);
	
		// Right We Have Deleted Modifications & Images Next The Actual Vehicle
		$this->delete_rows(GARAGE_TABLE, 'id', $cid);
	
		return;
	}
	
	/*========================================================================*/
	// Display Vehicle Page - With Or Without Management Links & Galleries
	// Usage:  display_vehicle('wn vehicle YES|NO');
	/*========================================================================*/
	function display_vehicle($owned)
	{
		global $userdata, $template, $images, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config, $HTTP_POST_FILES, $HTTP_POST_VARS, $HTTP_GET_VARS, $rating_text, $rating_types, $cid, $mode;

		//Since We Called This Fuction Display Top Block With All Vehicle Info
		$template->assign_block_vars('switch_top_block', array());
	
		if ( $owned == 'YES')
		{
			$this->check_own_vehicle($cid);
			$template->assign_block_vars('switch_top_block.owned_yes', array());
		}
		else
		{
			$template->assign_block_vars('switch_top_block.owned_no', array());
		}
	
		$vehicle_row = $this->select_vehicle_data($cid);
	
		if ( $owned == 'NO')
		{
			if ( $userdata['user_level'] == ADMIN )
			{
				$temp_url = append_sid("garage.$phpEx?mode=moderate_vehicle&amp;CID=$cid");
				$template->assign_vars(array(
					'MODERATE_VEHICLE' => '<a href="' . $temp_url . '"><img src="' . $images['garage_edit_vehicle'] . '" alt="'.$lang['Moderate_Vehicle'].'" title="'.$lang['Moderate_Vehicle'].'" border="0" /></a>')
				);
			}

			$avatar_img = '';
			if ( $vehicle_row['user_avatar_type'] && $vehicle_row['user_allowavatar'] )
			{
				switch( $vehicle_row['user_avatar_type'] )
				{
					case USER_AVATAR_UPLOAD:
						$avatar_img = ( $board_config['allow_avatar_upload'] ) ? '<img src="' . $board_config['avatar_path'] . '/' . $vehicle_row['user_avatar'] . '" alt="" border="0" />' : '';
						break;
					case USER_AVATAR_REMOTE:
						$avatar_img = ( $board_config['allow_avatar_remote'] ) ? '<img src="' . $vehicle_row['user_avatar'] . '" alt="" border="0" />' : '';
						break;
					case USER_AVATAR_GALLERY:
						$avatar_img = ( $board_config['allow_avatar_local'] ) ? '<img src="' . $board_config['avatar_gallery_path'] . '/' . $vehicle_row['user_avatar'] . '" alt="" border="0" />' : '';
						break;
				}
			}
		}
	
		if ( ($vehicle_row['image_id']) AND ($vehicle_row['attach_is_image']) AND (!empty($vehicle_row['attach_thumb_location'])) AND (!empty($vehicle_row['attach_location'])) )
		{
			$thumb_image = $phpbb_root_path . GARAGE_UPLOAD_PATH . $vehicle_row['attach_thumb_location'];
			$id = $vehicle_row['attach_id'];
			$title = $vehicle_row['attach_file'];
			$total_image_views = $vehicle_row['attach_hits'];
			$hilite_image = '<a href="garage.'.$phpEx.'?mode=view_gallery_item&amp;type=garage_mod&amp;image_id='. $id .'" title="' . $title .'" target="_blank"><img hspace="5" vspace="5" src="' . $thumb_image .'" class="attach"  /></a>';
		}
	
		$user_id = $vehicle_row['member_id'];
		$username =  $vehicle_row['username'];
		$temp_url = append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=$user_id");
		$owner = '<a href="' . $temp_url . '">' . $username . '</a>';
	
		if ( $owned == 'YES' )
		{
			$template->assign_block_vars('level2', array());
			$template->assign_vars(array(
				'U_LEVEL2' => append_sid("garage.$phpEx?mode=view_own_vehicle&amp;CID=".$cid),
				'L_LEVEL2' => $vehicle_row['made_year'] . ' ' . $vehicle_row['make'] . ' '. $vehicle_row['model'])
			);
			
			$temp_url = append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=$cid");
			$view_vehicle_link = '<a href="' . $temp_url . '"><img src="' . $images['garage_view_vehicle'] . '" alt="'.$lang['View_Vehicle'].'" title="'.$lang['View_Vehicle'].'" border="0" /></a>';
	
			$temp_url = append_sid("garage.$phpEx?mode=edit_vehicle&amp;CID=$cid");
			$edit_vehicle_link = '<a href="' . $temp_url . '"><img src="' . $images['garage_edit_vehicle'] . '" alt="'.$lang['Edit_Vehicle'].'" title="'.$lang['Edit_Vehicle'].'" border="0" /></a>';
	
			$temp_url = append_sid("garage.$phpEx?mode=add_modification&amp;CID=$cid");
			$add_modification_link = ' <a href="' . $temp_url . '"><img src="' . $images['garage_add_modification'] . '" alt="'.$lang['Add_New_Modification'].'" title="'.$lang['Add_New_Modification'].'" border="0" /></a>';
	
			if ( $garage_config['enable_insurance'] )
			{	
				$template->assign_block_vars('switch_top_block.owned_yes.enable_insurance', array());
				$temp_url = append_sid("garage.$phpEx?mode=add_insurance&amp;CID=$cid");
				$add_insurance_link = ' <a href="' . $temp_url . '"><img src="' . $images['garage_add_insurance'] . '" alt="'.$lang['Add_New_Insurance_Premium'].'" title="'.$lang['Add_New_Insurance_Premium'].'" border="0" /></a>';
			}
		
			if ( $garage_config['enable_quartermile'] )
			{	
				$template->assign_block_vars('switch_top_block.owned_yes.enable_quartermile', array());
				$temp_url = append_sid("garage.$phpEx?mode=add_quartermile&amp;CID=$cid");
				$add_quartermile_link = ' <a href="' . $temp_url . '"><img src="' . $images['garage_add_quartermile'] . '" alt="'.$lang['Add_New_Quartermile_Time'].'" title="'.$lang['Add_New_Quartermile_Time'].'" border="0" /></a>';
			}
	
			if ( $garage_config['enable_rollingroad'] )
			{	
				$template->assign_block_vars('switch_top_block.owned_yes.enable_rollingroad', array());
				$temp_url = append_sid("garage.$phpEx?mode=add_rollingroad&amp;CID=$cid");
				$add_rollingroad_link = ' <a href="' . $temp_url . '"><img src="' . $images['garage_add_rollingroad'] . '" alt="'.$lang['Add_New_Rollingroad_Run'].'" title="'.$lang['Add_New_Rollingroad_Run'].'" border="0" /></a>';
			}
			
			if ($this->check_permissions('UPLOAD',''))
			{
				$template->assign_block_vars('switch_top_block.owned_yes.manage_vehicle_gallery', array());
				$temp_url = append_sid("garage.$phpEx?mode=manage_vehicle_gallery&amp;CID=$cid");
				$manage_vehicle_gallery_link = ' <a href="' . $temp_url . '"><img src="' . $images['garage_manage_gallery'] . '" alt="'.$lang['Manage_Vehicle_Gallery'].'" title="'.$lang['Manage_Vehicle_Gallery'].'" border="0" /></a>';
			}
	
			$temp_url = append_sid("garage.$phpEx?mode=delete_gallery&amp;CID=$cid");
			$delete_vehicle_link = ' <a href="javascript:confirm_delete_car(' . $cid . ')"><img src="' . $images['garage_delete_vehicle'] . '" alt="'.$lang['Delete_Vehicle'].'" title="'.$lang['Delete_Vehicle'].'" border="0" /></a>';
		
			if ( ( $vehicle_row['main_vehicle'] == 0 ) AND ( $mode != 'moderate_vehicle') )
			{
				$temp_url = append_sid("garage.$phpEx?mode=set_main&amp;CID=$cid");
				$set_main_vehicle_link = ' <a href="' . $temp_url . '"><img src="' . $images['garage_main_vehicle'] . '" alt="'.$lang['Set_Main_Vehicle'].'" title="'.$lang['Set_Main_Vehicle'].'" border="0" /></a>';
	         		$template->assign_block_vars('switch_top_block.owned_yes.set_main_vehicle', array());
			}
			else if ( ( $vehicle_row['main_vehicle'] == 0 ) AND ( $mode = 'moderate_vehicle') )
			{
				$temp_url = append_sid("garage.$phpEx?mode=set_main&amp;CID=$cid&amp;user_id=".$vehicle_row['member_id']);
				$set_main_vehicle_link = '<a href="' . $temp_url . '"><img src="' . $images['garage_main_vehicle'] . '" alt="'.$lang['Set_Main_Vehicle'].'" title="'.$lang['Set_Main_Vehicle'].'" border="0" /></a>';
	         		$template->assign_block_vars('switch_top_block.owned_yes.set_main_vehicle', array());
			}
		}
	
		$year = $vehicle_row['made_year'];
		$make = $vehicle_row['make'];
		$model = $vehicle_row['model'];
	        $colour = $vehicle_row['color'];
	        $date_updated = $vehicle_row['date_updated'];
	        $updated = create_date($board_config['default_dateformat'], $vehicle_row['date_updated'], $board_config['board_timezone']);
	        $mileage = $vehicle_row['mileage'];
	        $mileage_units = $vehicle_row['mileage_units'];
	        $purchased_price = $vehicle_row['price'];
	        $currency = $vehicle_row['currency'];
	        $total_mods = $vehicle_row['total_mods'];
		$total_spent = $vehicle_row['total_spent'] ? $vehicle_row['total_spent'] : 0;
	        $total_views = $vehicle_row['views'];
	        $description = $vehicle_row['comments'];
	
		$sql = "SELECT SUM(rating)AS rating, count(*)*10 AS total_rating
			FROM " . GARAGE_RATING_TABLE . "
			WHERE garage_id = $cid";
	
		if ( !($result = $db->sql_query($sql)) )
	     	{
	       		message_die(GENERAL_ERROR, 'Could Not Select Vehicle Data', '', __LINE__, __FILE__, $sql);
	      	}
	
		$rating_row = $db->sql_fetchrow($result);
	
		if (empty($rating_row['rating']))
		{
			$template->assign_vars(array(
				'RATING' => $lang['Not_Rated_Yet'])
			);
		}
		else
		{
			$template->assign_vars(array(
				'RATING' => $rating_row['rating'] . '/' . $rating_row['total_rating'])
			);
		}
	
		if ( $owned == 'NO' )
		{
			$template->assign_block_vars('level2', array());
			$template->assign_vars(array(
				'U_LEVEL2' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$cid),
				'L_LEVEL2' => $vehicle_row['made_year'] . ' ' . $vehicle_row['make'] . ' '. $vehicle_row['model'])
			);
	
			$template->assign_block_vars('switch_top_block.owned_no.rating', array());
	
			$sql = "SELECT count(*) as total, rate_date 
				FROM " . GARAGE_RATING_TABLE . "
			      	WHERE user_id = " . $userdata['user_id'] ." 
					AND garage_id = $cid
				GROUP BY id";
				
	
			if(!$result = $db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, 'Could update DB', '', __LINE__, __FILE__, $sql);
			}
		        $row = $db->sql_fetchrow($result);
	
			//Never Rate So Show Them The Rate Button
			if ( $row['total'] < 1 )
			{
				$template->assign_block_vars('switch_top_block.owned_no.rating.rate', array());
				$template->assign_vars(array(
					'RATE_VEHICLE' => $this->build_selection_box('vehicle_rating',$rating_text,$rating_types,''))
				);
			}
			//Rated Already But Permanent So Do Not Show Button
			else if ( ( $row['total'] > 0 ) AND ($garage_config['rating_permanent']) )
			{
				$template->assign_vars(array(
					'L_RATING_NOTICE' => $lang['Rate_Permanent'])
				);
			}
			//Rated Already But Not Permanent & Always Updateable
			else if ( ( $row['total'] > 0 ) AND (!$garage_config['rating_permanent']) AND ($garage_config['rating_always_updateable']) )
			{
				$template->assign_block_vars('switch_top_block.owned_no.rating.rate', array());
				$template->assign_vars(array(
					'RATE_VEHICLE' => $this->build_selection_box('vehicle_rating',$rating_text,$rating_types,''),
					'L_RATING_NOTICE' => $lang['Update_Rating'])
				);
			}
			//Rated Already But Not Permanent & Updated Not Always Allowed, Vehicle Not Update So No Rate Update
			else if ( ( $row['total'] > 0 ) AND (!$garage_config['rating_permanent']) AND (!$garage_config['rating_always_updateable']) AND ($row['rate_date'] > $date_updated) )
			{
				$template->assign_vars(array(
					'L_RATING_NOTICE' => $lang['Vehicle_Update_Required_For_Rate'])
				);
			}
			//Rated Already But Not Permanent & Updated Not Always Allowed, Vehicle Updated So Rate Update Allowed
			else if ( ( $row['total'] > 0 ) AND (!$garage_config['rating_permanent']) AND (!$garage_config['rating_always_updateable']) AND ($row['rate_date'] < $date_updated) )
			{
				$template->assign_block_vars('switch_top_block.owned_no.rating.rate', array());
				$template->assign_vars(array(
					'RATE_VEHICLE' =>$this->build_selection_box('vehicle_rating',$rating_text,$rating_types,''),
					'L_RATING_NOTICE' => $lang['Update_Rating'])
				);
			}
	
			//Display Guestbook
			if ( $garage_config['enable_guestbooks'] )
			{
				$sql = "SELECT count(*) as total FROM " . GARAGE_GUESTBOOKS_TABLE . " 
	                                 WHERE garage_id = $cid";
	
	      			if ( !($result = $db->sql_query($sql)) )
	      			{
	         			message_die(GENERAL_ERROR, 'Could Not Select Vehicle Data', '', __LINE__, __FILE__, $sql);
	      			}
	
				$guestbook_result = $db->sql_fetchrow($result);
				$guestbook_count = $guestbook_result['total'];
	 			$comment_count ='( ' . $guestbook_count . ' ' . $lang['Total_Comments'] . ' )';
	
				$sql =  "SELECT SUBSTRING(REPLACE(gb.post,'<br />',' '),1,75) AS post, gb.author_id, m.username 
	                                 FROM " . GARAGE_GUESTBOOKS_TABLE . " AS gb 
	                                 	LEFT JOIN " . USERS_TABLE . " AS m ON gb.author_id = m.user_id
	                                 WHERE gb.garage_id = $cid 
	                                 ORDER BY gb.post_date DESC LIMIT 5";
	
	      			if ( !($result = $db->sql_query($sql)) )
	      			{
	         			message_die(GENERAL_ERROR, 'Could Not Select Vehicle Data', '', __LINE__, __FILE__, $sql);
	      			}
	
				$guestbook = array();
	            		while ( $guestbook_msg = $db->sql_fetchrow($result) )
	            		{
	                		if ( strlen($guestbook_msg['post']) >= 75 )
	                		{
	                    			// If this is a long message append some dots
	                    			$guestbook_msg['post'] .= '...';
	                		}
	
	                		$guestbook['messages'] = '<a href="profile.'.$phpEx.'?mode=viewprofile&amp;'.POST_USERS_URL.'='.$guestbook_msg['author_id'].'">'.$guestbook_msg['username'].'</a>: '.$guestbook_msg['post'].'<br />' . $guestbook['messages'];
				}
	
				$temp_url = append_sid("garage.$phpEx?mode=view_guestbook&amp;CID=$cid");
				$guestbook_link = '<a href="' . $temp_url . '">' . $lang['View_Guestbook'] . '</a>';
	
				$template->assign_block_vars('switch_top_block.owned_no.guestbook', array());
				$template->assign_vars(array(
					'COMMENTS' => $guestbook['messages'],
					'COMMENT_COUNT' => $comment_count,
					'GUESTBOOK_LINK' => $guestbook_link)
				);
			}
		}
	
	       	$db->sql_freeresult($result);
	
		// initialize vars
		$category = array();
		$category_id = array();
	
		// This will give us the number of categories available
	      	$cat_count = 0;
	      	$mod_images_found = 0;
	     
	      	// Select Categories For Which A User Has Mods
	      	$sql = "SELECT DISTINCT m.garage_id, m.category_id , c.title, c.id
	       		FROM  " . GARAGE_MODS_TABLE . " m, " . GARAGE_CATEGORIES_TABLE . " c
	       		WHERE m.garage_id = $cid
	       			AND m.category_id = c.id
			ORDER by c.field_order";
	
	      	if ( !($result = $db->sql_query($sql)) )
	      	{
	       		message_die(GENERAL_ERROR, 'Could Not Select Mofication Category Data', '', __LINE__, __FILE__, $sql);
	      	}
	
	      	// we'll populate some arrays(one for each field we need) with the SQL-results ..
	      	// ... will use this later to make the outer loop!
	      	if ( $cat_fields = $db->sql_fetchrow($result) )
	      	{
	       		do
	       		{
	               		array_push($category, $cat_fields['title']);
	               		array_push($category_id, $cat_fields['category_id']);
	               		$cat_count++;
	       		}
	       		while ( $cat_fields = $db->sql_fetchrow($result) );
	      	} // done reading categories
	      	$db->sql_freeresult($result);
	
	      	//Loop Processing All Categoires Returned From First Select Statement (now in a
	      	for ($i=0; $i < $cat_count; $i++)
	      	{
	       		//Setup cat_row Template Varibles
	       		$template->assign_block_vars('cat_row', array(
	           		'CATEGORY_TITLE' => $category[$i])
	       		);
	
	       		// Select All Mods From This Car For Category We Are Currently Processing
			$sql = "SELECT m.*,images.attach_id, images.attach_hits, images.attach_ext, images.attach_location,
	                        images.attach_file, images.attach_thumb_location, images.attach_is_image 
	         		FROM " . GARAGE_MODS_TABLE . " as m
	                        	LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = m.image_id
		       		WHERE garage_id = $cid 
					AND category_id = $category_id[$i]
	                        ORDER BY title ASC";
	
	       		if( !($result = $db->sql_query($sql)) )
	       		{
	        		message_die(GENERAL_ERROR, 'Could Not Select Modification Data', '', __LINE__, __FILE__, $sql);
	       		}
	
	       		//Loop Processing All Mods Returned From Second Statements
	       		while ( $usermods_row = $db->sql_fetchrow($result) )
	       		{
	       			$mid = $usermods_row['id'];
	       			$cost = $usermods_row['price'];
	       			$install = $usermods_row['install_price'];
	       			$product_rating = $usermods_row['product_rating'];
				if ( $owned == 'YES' )
				{
	       				$modification = $usermods_row['title'];
				}
				else if ( $owned == 'NO' )
				{
					$temp_url = append_sid("garage.$phpEx?mode=view_modification&amp;CID=$cid&amp;MID=$mid");
					$modification = '<a href="' . $temp_url . '">' . $usermods_row['title'] . '</a>';
				}
				$image_id = $usermods_row['image_id'];
	           		if ($image_id)
				{
					$image_attached ='<a href="garage.'. $phpEx .'?mode=view_gallery_item&amp;image_id='. $image_id .'" target="_blank"><img src="' . $images['vehicle_image_attached'] . '" alt="'.$lang['Modification_Image_Attached'].'" title="'.$lang['Modification_Image_Attached'].'" border="0" /></a>';
		                        $mod_images_found++;
				}
				else
				{
					$image_attached ='';
				}
	
	            		$temp_url = append_sid("garage.$phpEx?mode=edit_modification&amp;MID=$mid&amp;CID=$cid");
				if ( $owned == 'YES' )
				{
	            			$edit_mod_link = '<a href="' . $temp_url . '"><img src="' . $images['garage_edit'] . '" alt="'.$lang['Edit'].'" title="'.$lang['Edit'].'" border="0" /></a>';
					$delete_mod_link = '<a href="javascript:confirm_delete_mod(' . $cid . ',' . $mid . ')"><img src="' . $images['garage_delete'] . '" alt="'.$lang['Delete'].'" title="'.$lang['Delete'].'" border="0" /></a>';
				}
	
				$updated = create_date('D M d, Y G:i', $usermods_row['date_updated'], $board_config['board_timezone']);
				$created = create_date('D M d, Y G:i', $usermods_row['date_created'], $board_config['board_timezone']);
	
	            		//Setup user_row Template Varibles
	            		$template->assign_block_vars('cat_row.user_row', array(
	               			'IMAGE_ATTACHED' => $image_attached,
	               			'EDIT_MOD_LINK' => $edit_mod_link,
	               			'DELETE_MOD_LINK' => $delete_mod_link,
	               			'COST' => $cost,
	               			'INSTALL' => $install,
	               			'RATING' => $product_rating,
	               			'CREATED' => $created,
	               			'UPDATED' => $updated,
	               			'MODIFICATION' => $modification)
	            		);
	
				// LET SEE IF MOD HAS AN IMAGE ATTACHED AND DISPLAY IN GALLERY IF NEEDED
				if ( $owned == 'NO' )
				{
		               		// BEGIN mod gallery, if it's enabled!
	                    		if ($garage_config['show_mod_gallery'] == 1)
			                {
			                        // If we have a set limit, make sure we haven't hit it
	  		                      	if ( ($garage_config['limit_mod_gallery'] >= $mod_images_found) OR !$garage_config['limit_mod_gallery'])
			                        {
							$mod_images_displayed = $mod_images_found;
			                        	if ( $usermods_row['attach_is_image'] )
	                           			{
	                					// Do we have a thumbnail?  If so, our job is simple here :)
								if ( (empty($usermods_row['attach_thumb_location']) == FALSE) AND ($usermods_row['attach_thumb_location'] != $usermods_row['attach_location']) )
	                					{
			                    				// Form the image link
									$thumb_image = $phpbb_root_path . GARAGE_UPLOAD_PATH . $usermods_row['attach_thumb_location'];
									$id = $usermods_row['attach_id'];
									$title = $usermods_row['attach_file'];
									$gallery_modification_images .= '<a href="garage.'.$phpEx.'?mode=view_gallery_item&amp;type=garage_mod&amp;image_id='. $id .'" title="' . $title .'" target="_blank"><img hspace="5" vspace="5" src="' . $thumb_image .'" class="attach"  /></a> ';
	               						} 
							}
						}
					}
				}
	         	}// end WHILE of inner loop
	         	$db->sql_freeresult($result);
	      	}// end FOR of outer loop
	
		// Next Lets See If We Have Any Insurance Premiums //
		$sql = "SELECT ins.*, bus.title
	        	FROM " . GARAGE_INSURANCE_TABLE . " as ins
	                	LEFT JOIN " . GARAGE_BUSINESS_TABLE . " AS bus ON ins.business_id = bus.id
		       	WHERE garage_id = $cid";
	
	       	if( !($result = $db->sql_query($sql)) )
	       	{
	        	message_die(GENERAL_ERROR, 'Could Not Select Insurance Data', '', __LINE__, __FILE__, $sql);
	       	}
	
	        //Loop Processing All Mods Returned From Second Statements
		if ( $db->sql_numrows($result) > 0 )
		{
			$template->assign_block_vars('insurance', array());
	         	while ( $insurance_row = $db->sql_fetchrow($result) )
	         	{
				$ins_id = $insurance_row['id'];
				$company = $insurance_row['title'];
				$premium = $insurance_row['premium'];
				$cover_type = $insurance_row['cover_type'];
				$temp_url = append_sid("garage.$phpEx?mode=edit_insurance&amp;INS_ID=$ins_id&amp;CID=$cid");
				if ( $owned == 'YES' )
				{
	            			$edit_link = '<a href="' . $temp_url . '"><img src="' . $images['garage_edit'] . '" alt="'.$lang['Edit'].'" title="'.$lang['Edit'].'" border="0" /></a>';
					$delete_link = '<a href="javascript:confirm_delete_insurance(' . $cid . ',' . $ins_id . ')"><img src="' . $images['garage_delete'] . '" alt="'.$lang['Delete'].'" title="'.$lang['Delete'].'" border="0" /></a>';
				}

				$template->assign_block_vars('insurance.premium', array(
					'COMPANY' => $company,
					'PREMIUM' => $premium,
					'COVER_TYPE' => $cover_type,
					'EDIT_LINK' => $edit_link,
					'DELETE_LINK' => $delete_link)
				);
			}
		}
	
		// Next Lets See If We Have Any QuarterMile Runs //
	       	$sql = "SELECT qm.*,images.attach_id, images.attach_hits, images.attach_ext, 
	                        images.attach_file, images.attach_thumb_location, images.attach_is_image, images.attach_location
	          	FROM " . GARAGE_QUARTERMILE_TABLE . " as qm
	                	LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = qm.image_id
		       	WHERE garage_id = $cid";
	
	       	if( !($result = $db->sql_query($sql)) )
	       	{
	        	message_die(GENERAL_ERROR, 'Could Not Select Quartermile Data', '', __LINE__, __FILE__, $sql);
	       	}
	
	        //Loop Processing All Mods Returned From Second Statements
		if ( $db->sql_numrows($result) > 0 )
		{
			$template->assign_block_vars('quartermile', array());
	         	while ( $quartermile_row = $db->sql_fetchrow($result) )
	         	{
				$qmid = $quartermile_row['id'];
				$rt = $quartermile_row['rt'];
				$sixty = $quartermile_row['sixty'];
				$three = $quartermile_row['three'];
				$eight = $quartermile_row['eight'];
				$eightmph = $quartermile_row['eightmph'];
				$thou = $quartermile_row['thou'];
				$quart = $quartermile_row['quart'];
				$quartmph = $quartermile_row['quartmph'];
				$image_id = $quartermile_row['image_id'];
				if (!empty($image_id))
				{
					$slip_image = '<a href="garage.'. $phpEx .'?mode=view_gallery_item&amp;image_id='. $image_id .'" target="_blank"><img src="' . $images['slip_image_attached'] . '" alt="'.$lang['Slip_Image_Attached'].'" title="'.$lang['Slip_Image_Attached'].'" border="0" /></a>';
				}
				else
				{
					$slip_image = '';
				}
				$temp_url = append_sid("garage.$phpEx?mode=edit_quartermile&amp;QMID=$qmid&amp;CID=$cid");
				if ( $owned == 'YES' )
				{
	            			$edit_link = '<a href="' . $temp_url . '"><img src="' . $images['garage_edit'] . '" alt="'.$lang['Edit'].'" title="'.$lang['Edit'].'" border="0" /></a>';
					$delete_link = '<a href="javascript:confirm_delete_quartermile(' . $cid . ',' . $qmid . ')"><img src="' . $images['garage_delete'] . '" alt="'.$lang['Delete'].'" title="'.$lang['Delete'].'" border="0" /></a>';
				}

				$template->assign_block_vars('quartermile.run', array(
					'RT' => $rt,
					'SIXTY' => $sixty,
					'THREE' => $three,
					'EIGHT' => $eight,
					'EIGHTMPH' => $eightmph,
					'THOU' => $thou,
					'QUART' => $quart,
					'QUARTMPH' => $quartmph,
					'SLIP_IMAGE' => $slip_image,
					'EDIT_LINK' => $edit_link,
					'DELETE_LINK' => $delete_link)
				);
			}
		}

		// Next Lets See If We Have Any QuarterMile Runs //
	       	$sql = "SELECT rr.*,images.attach_id, images.attach_hits, images.attach_ext, 
                        images.attach_file, images.attach_thumb_location, images.attach_is_image, images.attach_location
         		FROM " . GARAGE_ROLLINGROAD_TABLE . " as rr
                        	LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = rr.image_id
	       		WHERE garage_id = $cid";
	
       		if( !($result = $db->sql_query($sql)) )
       		{
          		message_die(GENERAL_ERROR, 'Could Not Select Rollingroad Data', '', __LINE__, __FILE__, $sql);
       		}
	
         	//Loop Processing All Mods Returned From Second Statements
		if ( $db->sql_numrows($result) > 0 )
		{
			$template->assign_block_vars('rollingroad', array());
         		while ( $rollingroad_row = $db->sql_fetchrow($result) )
         		{
				$rrid = $rollingroad_row['id'];
				$dynocenter = $rollingroad_row['dynocenter'];
				$bhp = $rollingroad_row['bhp'];
				$bhp_unit = $rollingroad_row['bhp_unit'];
				$torque = $rollingroad_row['torque'];
				$torque_unit = $rollingroad_row['torque_unit'];
				$boost = $rollingroad_row['boost'];
				$boost_unit = $rollingroad_row['boost_unit'];
				$nitrous = $rollingroad_row['nitrous'];
				$peakpoint = $rollingroad_row['peakpoint'];
				$image_id = $rollingroad_row['image_id'];
				if (!empty($image_id))
				{
					$slip_image = '<a href="garage.'. $phpEx .'?mode=view_gallery_item&amp;image_id='. $image_id .'" target="_blank"><img src="' . $images['slip_image_attached'] . '" alt="'.$lang['Slip_Image_Attached'].'" title="'.$lang['Slip_Image_Attached'].'" border="0" /></a>';
				}
				else
				{
					$slip_image = '';
				}
				$temp_url = append_sid("garage.$phpEx?mode=edit_rollingroad&amp;RRID=$rrid&amp;CID=$cid");
				if ( $owned == 'YES' )
				{
            				$edit_link = '<a href="' . $temp_url . '"><img src="' . $images['garage_edit'] . '" alt="'.$lang['Edit'].'" title="'.$lang['Edit'].'" border="0" /></a>';
					$delete_link = '<a href="javascript:confirm_delete_rollingroad(' . $cid . ',' . $rrid . ')"><img src="' . $images['garage_delete'] . '" alt="'.$lang['Delete'].'" title="'.$lang['Delete'].'" border="0" /></a>';
				}

				$template->assign_block_vars('rollingroad.run', array(
					'DYNOCENTER' => $dynocenter,
					'BHP' => $bhp,
					'BHP_UNIT' => $bhp_unit,
					'TORQUE' => $torque,
					'TORQUE_UNIT' => $torque_unit,
					'BOOST' => $boost,
					'BOOST_UNIT' => $boost_unit,
					'NITROUS' => $nitrous,
					'PEAKPOINT' => $peakpoint,
					'SLIP_IMAGE' => $slip_image,
					'EDIT_LINK' => $edit_link,
					'DELETE_LINK' => $delete_link)
				);
			}
		}
			
		if ( $owned == 'NO' )
		{
			// WORK OUT DISPLAYING VEHICLE IMAGES AND IF NEEDED MODIFICATION IMAGES 
		       	$gallery_query_id = "SELECT gallery.id, images.attach_id, images.attach_hits, images.attach_ext, 
	                        	        images.attach_file, images.attach_thumb_location, images.attach_is_image,
	                	                images.attach_location
	                                     FROM " . GARAGE_IMAGES_TABLE . " AS images 
						LEFT JOIN " . GARAGE_GALLERY_TABLE . " AS gallery ON images.attach_id = gallery.image_id 
	                                     	LEFT JOIN " . GARAGE_TABLE . " AS garage ON gallery.garage_id = garage.id 
	                                     WHERE garage.id = $cid";
			if ( !($result = $db->sql_query($gallery_query_id)) )
      			{
         			message_die(GENERAL_ERROR, 'Could Not Select Image Data For Vehicle', '', __LINE__, __FILE__, $sql);
	      		}
		
			$vehicle_images_found = 0;	
		
        		while ( $gallery_data = $db->sql_fetchrow($result) )
	        	{
        	    		if ( $gallery_data['attach_is_image'] )
            			{
				        $vehicle_images_found++;
		
        	        		// Do we have a thumbnail?  If so, our job is simple here :)
					if ( (empty($gallery_data['attach_thumb_location']) == FALSE) AND ($gallery_data['attach_thumb_location'] != $gallery_data['attach_location']) )
                			{
                    				// Form the image link
						$thumb_image = $phpbb_root_path . GARAGE_UPLOAD_PATH . $gallery_data['attach_thumb_location'];
						$id = $gallery_data['attach_id'];
						$title = $gallery_data['attach_file'];
						$gallery_vehicle_images .= '<a href="garage.'.$phpEx.'?mode=view_gallery_item&amp;type=garage_gallery&amp;image_id='. $id .'" title="' . $title .'" target="_blank"><img hspace="5" vspace="5" src="' . $thumb_image .'" class="attach"  /></a> ';
               				} 
				}
	        	} // End while $gallery_data
		}
	
		if ( (empty($gallery_modification_images) == FALSE) AND (empty($gallery_vehicle_images) == FALSE) )
		{

			$template->assign_block_vars('switch_top_block.owned_no.gallery_all', array(
				'VEHICLE_IMAGES' => $gallery_vehicle_images,
				'MODIFICATION_IMAGES' => $gallery_modification_images)
			);
		}
	
		if ( (empty($gallery_modification_images) == TRUE) AND (empty($gallery_vehicle_images) == FALSE) )
		{
			$template->assign_block_vars('switch_top_block.owned_no.gallery_vehicle', array(
				'VEHICLE_IMAGES' => $gallery_vehicle_images)
			);
		}
	
		if ( (empty($gallery_modification_images) == FALSE) AND (empty($gallery_vehicle_images) == TRUE) )
		{
			$template->assign_block_vars('switch_top_block.owned_no.gallery_modification', array(
				'MODIFICATION_IMAGES' => $gallery_modification_images)
			);
		}
	
		$template->assign_vars(array(
			'L_GALLERY' => $lang['Gallery'],
			'L_HILITE_IMAGE' => $lang['Hilite_Image'],
			'L_TOTAL_VIEWS' => $lang['Total_Views'],
			'L_MANAGE_VEHICLE_LINKS' => $lang['Manage_Vehicle_Links'],
			'L_SHOWING' => $lang['Showing'],
			'L_OF' => $lang['Of'],
			'L_IMAGES' => $lang['Images'],
			'L_MODIFICATION_PICTURES' => $lang['Modification_Pictures'],
			'L_VEHICLE_PICTURES' => $lang['Vehicle_Pictures'],
			'L_ROLLING_ROAD_RUNS' => $lang['Rolling_Road_Runs'],
			'L_QUARTER_MILE_TIMES' => $lang['Quarter_Mile_Times'],
			'L_INSURANCE_PREMIUMS' => $lang['Insurance_Premiums'],
			'L_COMPANY' => $lang['Insurance_Company'],
			'L_PREMIUM' => $lang['Premium_Price'],
			'L_COVER_TYPE' => $lang['Cover_Type'],
			'L_DYNOCENTER' => $lang['Dynocenter'],
			'L_BHP' => $lang['Bhp'],
			'L_BHP_UNIT' => $lang['Bhp_Unit'],
			'L_TORQUE' => $lang['Torque'],
			'L_TORQUE_UNIT' => $lang['Torque_Unit'],
			'L_BOOST' => $lang['Boost'],
			'L_BOOST_UNIT' => $lang['Boost_Unit'],
			'L_NITROUS' => $lang['Nitrous'],
	  		'L_PEAKPOINT' => $lang['Peakpoint'],
			'L_RT' => $lang['Car_Rt'],
	  		'L_SIXTY' => $lang['Car_Sixty'],
			'L_THREE' => $lang['Car_Three'],
			'L_EIGTH' => $lang['Car_Eigth'],
			'L_EIGTHMPH' => $lang['Car_Eigthm'],
			'L_THOU' => $lang['Car_Thou'],
			'L_QUART' => $lang['Car_Quart'],
			'L_QUARTMPH' => $lang['Car_Quartm'],
        	    	'L_MODIFICATION' => $lang['Modification'],
		        'L_RATING' => $lang['Rating'],
		        'L_RATE' => $lang['Rate'],
	            	'L_COST' => $lang['Cost'],
            		'L_INSTALL' => $lang['Install'],
            		'L_CREATED' => $lang['Created'],
            		'L_UPDATED' => $lang['Updated'],
            		'L_VEHICLE' => $lang['Vehicle'],
            		'L_COLOUR' => $lang['Colour'],
            		'L_UPDATED' => $lang['Updated'],
            		'L_MILEAGE' => $lang['Mileage'],
            		'L_PRICE' => $lang['Purchased_Price'],
            		'L_TOTAL_MODS' => $lang['Total_Mods'],
            		'L_TOTAL_SPENT' => $lang['Total_Spent'],
            		'L_TOTAL_VIEWS' => $lang['Total_Views'],
            		'L_DESCRIPTION' => $lang['Description'],
            		'L_OWNER' => $lang['Owner'],
            		'L_GUESTBOOK' => $lang['Guestbook'],
			'L_VEHICLE_RATING' => $lang['Rating'],
			'L_PLEASE_RATE' => $lang['Please_Rate'],
			'L_GO' => $lang['Rate'],
			'L_CONFIRM_DELETE_VEHICLE' => $lang['Confirm_Delete_Vehicle'],
			'L_CONFIRM_DELETE_MODIFICATION' => $lang['Confirm_Delete_Modification'],
			'L_CONFIRM_DELETE_PREMIUM' => $lang['Confirm_Delete_Premium'],
			'L_CONFIRM_DELETE_QUARTERMILE' => $lang['Confirm_Delete_Quartermile'],
			'L_CONFIRM_DELETE_ROLLINGROAD' => $lang['Confirm_Delete_Rollingroad'],
			'U_DELETE_VEHICLE' => append_sid("garage.$phpEx?mode=delete_vehicle"),
			'U_DELETE_MODIFICATION' => append_sid("garage.$phpEx?mode=delete_modification"),
			'U_DELETE_QUARTERMILE' => append_sid("garage.$phpEx?mode=delete_quartermile"),
			'U_DELETE_PREMIUM' => append_sid("garage.$phpEx?mode=delete_insurance"),
			'U_DELETE_ROLLINGROAD' => append_sid("garage.$phpEx?mode=delete_rollingroad"),
            		'VIEW_VEHICLE_LINK' => $view_vehicle_link,
            		'EDIT_VEHICLE_LINK' => $edit_vehicle_link,
            		'ADD_MODIFICATION_LINK' => $add_modification_link,
            		'ADD_INSURANCE_LINK' => $add_insurance_link,
            		'ADD_QUARTERMILE_LINK' => $add_quartermile_link,
            		'ADD_ROLLINGROAD_LINK' => $add_rollingroad_link,
            		'ADD_TANK_LINK' => $add_tank_link,
            		'MANAGE_VEHICLE_GALLERY_LINK' => $manage_vehicle_gallery_link,
            		'DELETE_VEHICLE_LINK' => $delete_vehicle_link,
            		'SET_MAIN_VEHICLE_LINK' => $set_main_vehicle_link,
	       		'TOTAL_MOD_IMAGES' => $mod_images_found,
            		'SHOWING_MOD_IMAGES' => $mod_images_displayed,
			'CID' => $cid,
			'YEAR' => $year,
			'MAKE' => $make,
			'MODEL' => $model,
            		'COLOUR' => $colour,
            		'PROFILE_LINK' => $owner,
            		'HILITE_IMAGE' => $hilite_image,
            		'AVATAR_IMG' => $avatar_img,
            		'DATE_UPDATED' => $updated,
            		'MILEAGE' => $mileage,
            		'MILEAGE_UNITS' => $mileage_units,
            		'PRICE' => $purchased_price,
            		'CURRENCY' => $currency,
            		'TOTAL_MODS' => $total_mods,
            		'TOTAL_SPENT' => $total_spent,
            		'TOTAL_VIEWS' => $total_views,
	       		'TOTAL_IMAGE_VIEWS' => $total_image_views,
            		'DESCRIPTION' => str_replace("\n", "\n<br />\n", $description))
         	);

		return;
	}
	
	/*========================================================================*/
	// Select All Vehicles Data From Db
	// Usage: select_all_vehicle_data();
	/*========================================================================*/
	function select_all_vehicle_data($additional_where, $order_by, $sort_order, $start, $end)
	{
		global $db;
		//Select All Vehicles Information
		$sql = "SELECT g.*, makes.make, models.model, user.username, count(mods.id) AS total_mods, count(*) as total
        		FROM " . GARAGE_TABLE . " AS g 
                    		LEFT JOIN " . GARAGE_MODS_TABLE . " AS mods ON mods.garage_id = g.id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
			        LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id 
			        LEFT JOIN " . USERS_TABLE . " AS user ON g.member_id = user.user_id 
			WHERE makes.pending = 0 AND models.pending = 0
				".$search_data['where']."
		        GROUP BY g.id
			ORDER BY $order_by $sort_order";
		if ( (!empty($start)) AND (!empty($end)) )
		{
			$sql .= "LIMIT $start, $end";
		}

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Get Vehicle Data', '', __LINE__, __FILE__, $sql);
      		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}
		$db->sql_freeresult($result);

		return $rows;
	}

	/*========================================================================*/
	// Select All Vehicle Data From Db
	// Usage: select_vehicle_data('vehicle id');
	/*========================================================================*/
	function select_vehicle_data($cid)
	{
		global $db;
		//Select All Vehicle Information
	   	$sql = "SELECT g.*, images.*, makes.make, models.model, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, count(mods.id) AS total_mods, ( SUM(mods.price) + SUM(mods.install_price) ) AS total_spent, user.username, user.user_avatar_type, user.user_allowavatar, user.user_avatar, user.user_id
                      	FROM " . GARAGE_TABLE . " AS g  
				LEFT JOIN " . USERS_TABLE ." AS user ON g.member_id = user.user_id
	                       	LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
        	                LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
				LEFT JOIN " . GARAGE_MODS_TABLE . " AS mods ON g.id = mods.garage_id
				LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = g.image_id
                    	WHERE g.id = $cid
	                GROUP BY g.id";

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Get Vehicle Data', '', __LINE__, __FILE__, $sql);
      		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row;
	}
}

$garage_vehicle = new garage_vehicle();

?>

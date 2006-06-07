<?php
/***************************************************************************
 *                              class_garage_template.php
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
if (!defined('IN_PHPBB'))
{
	die('Hacking attempt');
}

class garage_template
{
	var $classname = "garage_template";

	/*========================================================================*/
	// Builds HTML Variables For Version & Copywrite Notice
	// Usage: version_notice();
	/*========================================================================*/
	function version_notice()
	{
		global $template, $lang, $garage_config, $phpEx;

		// Set Garage Version Messages.....DO NOT REMOVE....No Support For Any Garage Without It
		$template->assign_vars(array(
			'GARAGE_LINK' => 'http://www.phpbbgarage.com/',
			'GARAGE_VERSION' => $garage_config['version'],
			'U_GARAGE' => append_sid("garage.$phpEx?mode=main_menu"),
			'L_GARAGE' => $lang['Garage'],
			'L_POWERED_BY_GARAGE' => 'Powered By phpBB Garage' . $lang['Translation_Link'])
		);

		return;
	}

	/*========================================================================*/
	// Builds the HTML for a selecting for models
	// Usage:  vehicle_dropdown_javascript();
	/*========================================================================*/
	function vehicle_dropdown_javascript()
	{
		global $db;

		$make_q_id = "SELECT id, make FROM " . GARAGE_MAKES_TABLE . " ORDER BY make ASC";
	
		if( !($make_result = $db->sql_query($make_q_id)) )
		{
			message_die(GENERAL_ERROR, 'Could not query makes', '', __LINE__, __FILE__, $sql);
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
				message_die(GENERAL_ERROR, 'Could not query models', '', __LINE__, __FILE__, $sql);
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
	// Usage:  sidemenu();
	/*========================================================================*/
	function sidemenu()
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $images, $board_config, $garage;
	
		$template->set_filenames(array(
			'menu' => 'garage_menu.tpl')
		);

		$user_id = $userdata['user_id'];
		if (preg_match("/MAIN/",$garage_config['menu_selection']))
		{
			$main_menu_url = append_sid("garage.$phpEx?mode=main");
			$menu .= '<a href="' . $main_menu_url . '">';
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
			$browse_garage_url = append_sid("garage.$phpEx?mode=browse");
			$menu .= '<a href="' . $browse_garage_url . '">';
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
			$search_garage_url = append_sid("garage.$phpEx?mode=search");
			$menu .= '<a href="' . $search_garage_url . '">';
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
			$insurance_url = append_sid("garage.$phpEx?mode=view_insurance_business");
			$menu .= '<a href="' . $insurance_url . '">';
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
			$garage_url = append_sid("garage.$phpEx?mode=view_garage_business");
			$menu .= '<a href="' . $garage_url . '">';
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
			$shop_url = append_sid("garage.$phpEx?mode=view_shop_business");
			$menu .= '<a href="' . $shop_url . '">';
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
			$quartermile_url = append_sid("garage.$phpEx?mode=quartermile");
			$menu .= '<a href="' . $quartermile_url . '">';
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
			$dynorun_url = append_sid("garage.$phpEx?mode=rollingroad");
			$menu .= '<a href="' . $dynorun_url . '">';
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

		if (!$garage->check_permissions('BROWSE',''))
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
	// Usage: year_dropdown('selected year');
	/*========================================================================*/
	function year_dropdown($selected = 0)
	{
		global $garage_config, $template;
	
		// Grab the current year
		$my_array = localtime(time(), 1) ;
		$current_date = $my_array["tm_year"] +1900 ;
	
	        // Calculate end year based on offset configured
	        $end_year = $current_date + $garage_config['year_end'];
	
		// A simple check to prevent infinite loop
		if ( $garage_config['year_start'] > $end_year ) 
		{
			return;
		}	
	
		$year_list = "<select name='year' class='forminput'>";
	
		for ( $year = $end_year; $year >= $garage_config['year_start']; $year-- ) 
		{
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
	// Builds The HTML For A Selecting A Garage
	// Usage: garage_install_dropdown('business id', 'business name');
	/*========================================================================*/
	function garage_install_dropdown($selected, $selected_name)
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
	
		$sql = "SELECT id, title 
			FROM " . GARAGE_BUSINESS_TABLE . " 
			WHERE garage = 1 
			ORDER BY title ASC";
	
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
	// Usage: reassign_business_dropdown('excluding business id');
	/*========================================================================*/
	function reassign_business_dropdown($exclude)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		$html = "<select name='target_id' class='forminput'>";
	
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
	// Usage: business_dropdown('business id', 'business name');
	/*========================================================================*/
	function business_dropdown($selected,$selected_name)
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
	
		$sql = "SELECT id, title 
			FROM " . GARAGE_BUSINESS_TABLE . " 
			ORDER BY title ASC";
	
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
	// Usage: insurance_dropdown('<business id', 'business name');
	/*========================================================================*/
	function insurance_dropdown($selected,$selected_name)
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
	
		$sql = "SELECT id, title 
			FROM " . GARAGE_BUSINESS_TABLE . " 
			WHERE insurance = 1 
			ORDER BY title ASC";
	
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
	// Usage: shop_dropdown('business id', 'business name');
	/*========================================================================*/
	function shop_dropdown($selected,$selected_name)
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
	// Builds The HTML For Selecting A Dynorun Entry
	// Usage: dynorun_dropdown('rollingroad id', 'bhp @ bhp_type', 'vehicle id');
	/*========================================================================*/
	function dynorun_dropdown($selected,$selected_name,$cid)
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
			FROM " . GARAGE_ROLLINGROAD_TABLE . " 
			WHERE garage_id = $cid";
	
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
	// Usage: category_dropdown('category id');
	/*========================================================================*/
	function category_dropdown($selected)
	{
		global $template, $db;

	        $html = '<select name="category_id" class="forminput">';
	
		$sql = "SELECT id, title
			FROM " . GARAGE_CATEGORIES_TABLE . " ORDER BY field_order ASC";
	
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
	// Usage: selection_dropdown('select name', 'options text', 'options values', 'selected option');
	/*========================================================================*/
	function selection_dropdown($select_name,$select_text,$select_types,$selected_option)
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
	// Usage: attach_image('modification'|'vehicle');
	/*========================================================================*/
	function attach_image($type)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config, $garage;
	
		if (!$garage->check_permissions('UPLOAD',''))
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
	// Usage: edit_image('image id', 'image name'),
	/*========================================================================*/
	function edit_image($image_id, $image_name)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config, $garage;
	
		if (!$garage->check_permissions('UPLOAD',''))
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
	// Build sort order HTML
	// Usage: sort_order('selected');
	/*========================================================================*/
	function sort_order($sort_order)
	{
		global $template, $lang;
	
		$select_sort_order = '<select name="order">';
		if($sort_order == 'ASC')
		{
			$select_sort_order .= '<option value="ASC" selected="selected">' . $lang['Ascending_Order'] . '</option><option value="DESC">' . $lang['Descending_Order'] . '</option>';
		}
		else
		{
			$select_sort_order .= '<option value="ASC">' . $lang['Ascending_Order'] . '</option><option value="DESC" selected="selected">' . $lang['Descending_Order'] . '</option>';
		}
		$select_sort_order .= '</select>';

		$template->assign_vars(array(
			'S_ORDER_SELECT' => $select_sort_order)
		);
		return;
	}

	/*========================================================================*/
	// Builds the array for a selecting for models
	// Usage:  vehicle_array();
	/*========================================================================*/
	function vehicle_array()
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
}

$garage_template = new garage_template();

?>

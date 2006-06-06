<?php
/***************************************************************************
 *                              class_garage_vehicle.php
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

class garage_vehicle
{
	var $classname = "garage_vehicle";

	/*========================================================================*/
	// Gets User Vehicle Quota
	// Usage: get_user_vehicle_quota();
	/*========================================================================*/
	function get_user_add_quota()
	{
		global $db, $userdata, $garage_config, $garage;

		if (empty($garage_config['private_add_quota']))
		{
			//Since No Specific Group Value Exists Use Default Value
			return $garage_config['max_user_cars'];
		}
		//It Appears Some Groups Have Private Permissions & Quotas We Will Need To Check Them
		else
		{
			//Get All Group Memberships
			$groupdata = $garage->get_group_membership($userdata['user_id']);
			
			//Lets Get The Private Upload Groups & Quotas
			$private_add_groups = @explode(',', $garage_config['private_add_perms']);
			$private_add_quotas = @explode(',', $garage_config['private_add_quota']);

			//Process All Groups You Are Member Of To See If Any Are Granted Permission & Quota
			for ($i = 0; $i < count($groupdata); $i++)
			{
				if (in_array($groupdata[$i]['group_id'], $private_add_groups))
				{
					//Your A Member Of A Group Granted Permission - Find Array Key
					$index = array_search($groupdata[$i]['group_id'], $private_add_groups);
					//So Your Quota For This Group Is...
					$quota[$i] = $private_add_quotas[$index];
				}
			}

			//Your Were Not Granted Any Private Permissions..Return Default Value
			if  (empty($quota))
			{
				return $garage_config['max_user_cars'];
			}

			//Return The Highest Quota You Were Granted
			return max($quota);
		}
	}

	/*========================================================================*/
	// Gets Group Vehicle Quota - Used Only In ACP Page
	// Usage: get_group_vehicle_quota('group id');
	/*========================================================================*/
	function get_group_add_quota($gid)
	{
		global $db, $userdata, $garage_config;

		if (empty($garage_config['private_add_quota']))
		{
			//Since No Specific Group Value Exists Use Default Value
			return $garage_config['max_user_cars'];
		}
		//It Appears Some Groups Have Private Permissions & Quotas We Will Need To Check Them
		else
		{
			//Lets Get The Private Add Groups & Quotas
			$private_add_groups = @explode(',', $garage_config['private_add_perms']);
			$private_add_quota = @explode(',', $garage_config['private_add_quota']);

			//Find The Matching Index In Second Array For The Group ID
			if (($index = array_search($gid, $private_add_groups)) === FALSE)
			{
				//Hmmm..Group Has Currently No Private Add Permissions...So Give It The Default Incase They Turn It On
				return $garage_config['max_user_cars'];
			} 
			
			//Return The Groups Quota
			return $private_add_quota[$index];
		}
	}

	/*========================================================================*/
	// Inserts Vehicle Into DB
	// Usage: insert_vehicle(array());
	/*========================================================================*/
	function insert_vehicle($data)
	{
		global $userdata, $db;

		$sql = "INSERT INTO ". GARAGE_TABLE ."
			(made_year, make_id, model_id, color, mileage, mileage_units, price, currency, comments, member_id, date_created, date_updated, main_vehicle, guestbook_pm_notify)
			VALUES
			('".$data['year']."', '".$data['make_id']."', '".$data['model_id']."', '".$data['colour']."', '".$data['mileage']."', '".$data['mileage_units']."', '".$data['price']."', '".$data['currency']."', '".$data['comments']."', '".$userdata['user_id']."', '".$data['time']."', '".$data['time']."', '".$data['main_vehicle']."', '".$data['guestbook_pm_notify']."')";
	
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert Vehicle', '', __LINE__, __FILE__, $sql);
		}
	
		$id = $db->sql_nextid();

		return $id;
	}

	/*========================================================================*/
	// Insurance Vehicle Rating Into DB
	// Usage: insert_vehicel_rating(array());
	/*========================================================================*/
	function insert_vehicle_rating($data)
	{
		global $cid, $db;

		$sql = "INSERT INTO ". GARAGE_RATING_TABLE ." 
			(garage_id,rating,user_id,rate_date)
			VALUES 
			('$cid', '".$data['vehicle_rating']."', '".$data['user_id']."', '".$data['rate_date']."')";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert Vehicle Rating', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Returns Count Of Users Vehicles
	// Usage: count_user_vehicles();
	/*========================================================================*/
	function count_user_vehicles()
	{
		global $userdata, $db;

		$sql = "SELECT count(id) AS total 
			FROM " . GARAGE_TABLE . " 
			WHERE member_id = " . $userdata['user_id'];

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Counting User Vehicles', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row['total'];
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

		$sql = "UPDATE ". GARAGE_RATING_TABLE ." 
			SET rating = '".$data['vehicle_rating']."', rate_date = '".$data['rate_date']."'
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
		$sql = "SELECT count(*) AS total_vehicles 
			FROM " . GARAGE_TABLE;

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
		$sql = "SELECT count(*) as total 
			FROM " . GARAGE_RATING_TABLE . "
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
	// Check A User Owns The Vehicle, If Not Display Message
	// Usage: check_ownership('vehicle id');
	/*========================================================================*/
	function check_ownership($cid)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		if (empty($cid))
		{
	 		message_die(GENERAL_ERROR, 'Vehicle ID Not Entered..', '', __LINE__, __FILE__);
		}
	
		$sql = "SELECT g.member_id 
			FROM " . GARAGE_TABLE . " g 
			WHERE g.id = $cid ";
	
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
	// Update Vehicle Modified Time
	// Usage: update_vehicle_time('vehicle id');
	/*========================================================================*/
	function update_vehicle_time($cid)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config, $garage;
		
		$data['time'] = time();

		$garage->update_single_field(GARAGE_TABLE, 'date_updated', $data['time'], 'id', $cid);
	
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
	                       g.member_id, g.date_updated AS POI, u.username 
	               	FROM " . GARAGE_TABLE . " g 
	                       	LEFT JOIN " . GARAGE_MAKES_TABLE . " makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " models ON g.model_id = models.id
	       	                LEFT JOIN " . USERS_TABLE . " u ON g.member_id = u.user_id
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
	                        g.member_id, (SUM(mods.install_price) + SUM(mods.price)) AS POI, u.username, g.currency 
	                FROM " . GARAGE_TABLE . " g 
	                	LEFT JOIN " . GARAGE_MAKES_TABLE . " makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " models ON g.model_id = models.id
	                        LEFT JOIN " . GARAGE_MODS_TABLE . " mods ON mods.garage_id = g.id 
	                        LEFT JOIN " . USERS_TABLE . " u ON g.member_id = u.user_id
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
	                        g.member_id, g.views AS POI, u.username 
	                FROM " . GARAGE_TABLE . " g 
	                        LEFT JOIN " . GARAGE_MAKES_TABLE . " makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " models ON g.model_id = models.id
	                        LEFT JOIN " . USERS_TABLE . " u ON g.member_id = u.user_id
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
	
		$sql =  "SELECT g.id, g.member_id, u.username, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, sum( r.rating ) AS rating, count( * ) *10 AS total_rating
			 FROM " . GARAGE_RATING_TABLE . " r
				LEFT JOIN " . GARAGE_TABLE . " g ON r.garage_id = g.id
	                        LEFT JOIN " . GARAGE_MAKES_TABLE . " makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " models ON g.model_id = models.id
	                        LEFT JOIN " . USERS_TABLE . " u ON g.member_id = u.user_id
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
	                        g.member_id, g.date_created AS POI, u.username 
	                FROM " . GARAGE_TABLE . " g 
	                	LEFT JOIN " . GARAGE_MAKES_TABLE . " makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " models ON g.model_id = models.id
	                        LEFT JOIN " . USERS_TABLE . " u ON g.member_id = u.user_id
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
		$quartermile_sql = "SELECT id 
			FROM " . GARAGE_QUARTERMILE_TABLE . " 
			WHERE garage_id = $cid";
	
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
		$rollingroad_sql = "SELECT id 
			FROM " . GARAGE_ROLLINGROAD_TABLE . " 
			WHERE garage_id = $cid";
	
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
		$insurance_sql = "SELECT id 
			FROM " . GARAGE_INSURANCE_TABLE . " 
			WHERE garage_id = $cid";
	
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
		$gb_sql = "SELECT id 
			FROM " . GARAGE_GUESTBOOKS_TABLE . " 
			WHERE garage_id = $cid";

		if ( !($db_result = $db->sql_query($gb_sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Guestbook Data For Vehicle', '', __LINE__, __FILE__, $sql);
		}

		while ($gb_row = $db->sql_fetchrow($mods_result) )
		{
			$this->delete_rows(GARAGE_GUESTBOOKS, 'id', $gb_row['id']);
		}

		//Right User Want To Delete Vehicle Let Get All Ratings Associated With It
		$rating_sql = "SELECT id 
			FROM " . GARAGE_RATING_TABLE . " 
			WHERE garage_id = $cid";

		if ( !($rating_result = $db->sql_query($rating_sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Rating Data For Vehicle', '', __LINE__, __FILE__, $sql);
		}

		while ($rating_row = $db->sql_fetchrow($mods_result) )
		{
   			$this->delete_rows(GARAGE_RATING_TABLE, 'id', $rating_row['id']);
		} 
	
		// Right Lets Delete All Images For This Vehicle
		$sql = "SELECT image_id	
			FROM " . GARAGE_GALLERY_TABLE . " 
			WHERE garage_id = $cid ";
	
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
		global $userdata, $template, $images, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config, $HTTP_POST_FILES, $HTTP_POST_VARS, $HTTP_GET_VARS, $rating_text, $rating_types, $cid, $mode, $garage, $garage_template, $garage_modification, $garage_insurance, $garage_quartermile, $garage_dynorun;

		//Since We Called This Fuction Display Top Block With All Vehicle Info
		$template->assign_block_vars('switch_top_block', array());
	
		if ( $owned == 'YES')
		{
			$this->check_ownership($cid);
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
		$temp_url = append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=$user_id");
		$owner = '<a href="' . $temp_url . '">' . $vehicle_row['username'] . '</a>';
	
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
			
			if ($garage->check_permissions('UPLOAD',''))
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
					'RATE_VEHICLE' => $garage_template->selection_dropdown('vehicle_rating',$rating_text,$rating_types,''))
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
					'RATE_VEHICLE' => $garage_template->selection_dropdown('vehicle_rating',$rating_text,$rating_types,''),
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
					'RATE_VEHICLE' =>$garage_template->selection_dropdown('vehicle_rating',$rating_text,$rating_types,''),
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
	
		//Set Counter To Zero
	      	$mod_images_found = 0;
	     
	      	//Select Categories For Which A User Has Mods
	      	$sql = "SELECT DISTINCT c.title, c.id
	       		FROM  " . GARAGE_MODS_TABLE . " m, " . GARAGE_CATEGORIES_TABLE . " c
	       		WHERE m.garage_id = $cid
	       			AND m.category_id = c.id
			ORDER by c.field_order";
	
	      	if ( !($result = $db->sql_query($sql)) )
	      	{
	       		message_die(GENERAL_ERROR, 'Could Not Select Mofication Category Data', '', __LINE__, __FILE__, $sql);
		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$category_data[] = $row;
		}

		$db->sql_freeresult($result);
	
	      	//Loop Processing All Categoires Returned....
	      	for ( $i=0; $i < count($category_data); $i++ )
	      	{
	       		//Setup cat_row Template Varibles
	       		$template->assign_block_vars('cat_row', array(
	           		'CATEGORY_TITLE' => $category_data[$i]['title'])
	       		);
	
			// Select All Mods From This Car For Category We Are Currently Processing
			$modification_data = $garage_modification->select_modifications_by_category_data($cid, $category_data[$i]['id']);

	       		//Process Modifications From This Category..
        		for ( $j = 0; $j < count($modification_data); $j++ )
	       		{
	       			$mid = $modification_data[$j]['id'];
				$temp_url = append_sid("garage.$phpEx?mode=view_modification&amp;CID=$cid&amp;MID=$mid");
				$modification = '<a href="' . $temp_url . '">' . $modification_data[$j]['title'] . '</a>';
				$image_id = $modification_data[$j]['image_id'];
				$image_attached ='';
	           		if ($image_id)
				{
					$image_attached ='<a href="garage.'. $phpEx .'?mode=view_gallery_item&amp;image_id='. $image_id .'" target="_blank"><img src="' . $images['vehicle_image_attached'] . '" alt="'.$lang['Modification_Image_Attached'].'" title="'.$lang['Modification_Image_Attached'].'" border="0" /></a>';
		                        $mod_images_found++;
				}
	
				if ( $owned == 'YES' )
				{
	            			$temp_url = append_sid("garage.$phpEx?mode=edit_modification&amp;MID=$mid&amp;CID=$cid");
	            			$edit_mod_link = '<a href="' . $temp_url . '"><img src="' . $images['garage_edit'] . '" alt="'.$lang['Edit'].'" title="'.$lang['Edit'].'" border="0" /></a>';
					$delete_mod_link = '<a href="javascript:confirm_delete_mod(' . $cid . ',' . $mid . ')"><img src="' . $images['garage_delete'] . '" alt="'.$lang['Delete'].'" title="'.$lang['Delete'].'" border="0" /></a>';
				}
	
	            		$template->assign_block_vars('cat_row.user_row', array(
	               			'IMAGE_ATTACHED' => $image_attached,
	               			'EDIT_MOD_LINK' => $edit_mod_link,
	               			'DELETE_MOD_LINK' => $delete_mod_link,
	               			'COST' => $modification_data[$j]['price'],
	               			'INSTALL' => $modification_data[$j]['install_price'],
	               			'RATING' => $modification_data[$j]['product_rating'],
	               			'CREATED' => create_date('D M d, Y G:i', $modification_data[$j]['date_created'], $board_config['board_timezone']),
	               			'UPDATED' => create_date('D M d, Y G:i', $modification_data[$j]['date_updated'], $board_config['board_timezone']),
	               			'MODIFICATION' => $modification)
	            		);
	
				//See If Mod Has An Image Attached And Display Gallery If Needed
				if ( ( $owned == 'NO' ) AND ($garage_config['show_mod_gallery'] == 1) AND ( $modification_data[$j]['attach_is_image'] ) )
				{
			        	//If we have a set limit, make sure we haven't hit it
	  		               	if ( ($garage_config['limit_mod_gallery'] >= $mod_images_found) OR !$garage_config['limit_mod_gallery'])
			                {
						$mod_images_displayed = $mod_images_found;
	                			//Do we have a thumbnail?  If so, our job is simple here :)
						if ( (empty($modification_data[$i]['attach_thumb_location']) == FALSE) AND ($modification_data[$j]['attach_thumb_location'] != $modification_data[$j]['attach_location']) )
	                			{
			               			//Form the image link
							$thumb_image = $phpbb_root_path . GARAGE_UPLOAD_PATH . $modification_data[$j]['attach_thumb_location'];
							$id = $modification_data[$j]['attach_id'];
							$title = $modification_data[$j]['attach_file'];
							$gallery_modification_images .= '<a href="garage.'.$phpEx.'?mode=view_gallery_item&amp;type=garage_mod&amp;image_id='. $id .'" title="' . $title .'" target="_blank"><img hspace="5" vspace="5" src="' . $thumb_image .'" class="attach"  /></a> ';
	               				} 
					}
				}
	         	}
	      	}
	
		// Next Lets See If We Have Any Insurance Premiums
		$insurance_data = $garage_insurance->select_premiums_by_vehicle_data($cid);
	
         	//If Any Premiums Exist Process Them...
		if ( count($insurance_data) > 0 )
		{
			$template->assign_block_vars('insurance', array());
        		for ( $i = 0; $i < count($insurance_data); $i++ )
	         	{
				$ins_id = $insurance_data[$i]['id'];
				if ( $owned == 'YES' )
				{
					$temp_url = append_sid("garage.$phpEx?mode=edit_insurance&amp;INS_ID=$ins_id&amp;CID=$cid");
	            			$edit_link = '<a href="' . $temp_url . '"><img src="' . $images['garage_edit'] . '" alt="'.$lang['Edit'].'" title="'.$lang['Edit'].'" border="0" /></a>';
					$delete_link = '<a href="javascript:confirm_delete_insurance(' . $cid . ',' . $ins_id . ')"><img src="' . $images['garage_delete'] . '" alt="'.$lang['Delete'].'" title="'.$lang['Delete'].'" border="0" /></a>';
				}

				$template->assign_block_vars('insurance.premium', array(
					'COMPANY' => $insurance_data[$i]['title'],
					'PREMIUM' => $insurance_data[$i]['premium'],
					'COVER_TYPE' => $insurance_data[$i]['cover_type'],
					'EDIT_LINK' => $edit_link,
					'DELETE_LINK' => $delete_link)
				);
			}
		}
	
		//Next Lets See If We Have Any QuarterMile Runs
		$quartermile_data = $garage_quartermile->select_quartermile_by_vehicle_data($cid);
	
         	//If Any Quartermiles Exist Process Them...
		if ( count($quartermile_data) > 0 )
		{
			$template->assign_block_vars('quartermile', array());
        		for ( $i = 0; $i < count($quartermile_data); $i++ )
	         	{
				$qmid = $quartermile_data[$i]['id'];
				$image_id = $quartermile_data[$i]['image_id'];
				$slip_image = '';
				if (!empty($image_id))
				{
					$slip_image = '<a href="garage.'. $phpEx .'?mode=view_gallery_item&amp;image_id='. $image_id .'" target="_blank"><img src="' . $images['slip_image_attached'] . '" alt="'.$lang['Slip_Image_Attached'].'" title="'.$lang['Slip_Image_Attached'].'" border="0" /></a>';
				}
				if ( $owned == 'YES' )
				{
					$temp_url = append_sid("garage.$phpEx?mode=edit_quartermile&amp;QMID=$qmid&amp;CID=$cid");
	            			$edit_link = '<a href="' . $temp_url . '"><img src="' . $images['garage_edit'] . '" alt="'.$lang['Edit'].'" title="'.$lang['Edit'].'" border="0" /></a>';
					$delete_link = '<a href="javascript:confirm_delete_quartermile(' . $cid . ',' . $qmid . ')"><img src="' . $images['garage_delete'] . '" alt="'.$lang['Delete'].'" title="'.$lang['Delete'].'" border="0" /></a>';
				}

				$template->assign_block_vars('quartermile.run', array(
					'RT' => $quartermile_data[$i]['rt'],
					'SIXTY' => $quartermile_data[$i]['sixty'],
					'THREE' => $quartermile_data[$i]['three'],
					'EIGHT' => $quartermile_data[$i]['eight'],
					'EIGHTMPH' => $quartermile_data[$i]['eightmph'],
					'THOU' => $quartermile_data[$i]['thou'],
					'QUART' => $quartermile_data[$i]['quart'],
					'QUARTMPH' => $quartermile_data[$i]['quartmph'],
					'SLIP_IMAGE' => $slip_image,
					'EDIT_LINK' => $edit_link,
					'DELETE_LINK' => $delete_link)
				);
			}
		}

		//Get All Dynoruns For Vehicle
		$rollingroad_data = $garage_dynorun->select_dynorun_by_vehicle_data($cid);
	
         	//If Any Dynoruns Exist Process Them...
		if ( count($rollingroad_data) > 0 )
		{
			$template->assign_block_vars('rollingroad', array());
         		for ( $i = 0; $i < count($rollingroad_data); $i++ )
         		{
				$rrid = $rollingroad_data[$i]['id'];
				$image_id = $rollingroad_data[$i]['image_id'];
				$slip_image = '';
				if (!empty($image_id))
				{
					$slip_image = '<a href="garage.'. $phpEx .'?mode=view_gallery_item&amp;image_id='. $image_id .'" target="_blank"><img src="' . $images['slip_image_attached'] . '" alt="'.$lang['Slip_Image_Attached'].'" title="'.$lang['Slip_Image_Attached'].'" border="0" /></a>';
				}
				if ( $owned == 'YES' )
				{
					$temp_url = append_sid("garage.$phpEx?mode=edit_rollingroad&amp;RRID=$rrid&amp;CID=$cid");
            				$edit_link = '<a href="' . $temp_url . '"><img src="' . $images['garage_edit'] . '" alt="'.$lang['Edit'].'" title="'.$lang['Edit'].'" border="0" /></a>';
					$delete_link = '<a href="javascript:confirm_delete_rollingroad(' . $cid . ',' . $rrid . ')"><img src="' . $images['garage_delete'] . '" alt="'.$lang['Delete'].'" title="'.$lang['Delete'].'" border="0" /></a>';
				}

				$template->assign_block_vars('rollingroad.run', array(
					'DYNOCENTER' => $rollingroad_data[$i]['dynocenter'],
					'BHP' => $rollingroad_data[$i]['bhp'],
					'BHP_UNIT' => $rollingroad_data[$i]['bhp_unit'],
					'TORQUE' => $rollingroad_data[$i]['torque'],
					'TORQUE_UNIT' => $rollingroad_data[$i]['torque_unit'],
					'BOOST' => $rollingroad_data[$i]['boost'],
					'BOOST_UNIT' => $rollingroad_data[$i]['boost_unit'],
					'NITROUS' => $rollingroad_data[$i]['nitrous'],
					'PEAKPOINT' => $rollingroad_data[$i]['peakpoint'],
					'SLIP_IMAGE' => $slip_image,
					'EDIT_LINK' => $edit_link,
					'DELETE_LINK' => $delete_link)
				);
			}
		}
			
		if ( $owned == 'NO' )
		{
			//Set Inital Count To Zero
			$vehicle_images_found = 0;	

			//Get All Gallery Data Required
			$gallery_data = $garage_image->select_gallery_data($cid);

			//Process Each Image From Vehicle Gallery	
        		for ( $i = 0; $i < count($gallery_data); $i++ )
	        	{
        	    		if ( $gallery_data[$i]['attach_is_image'] )
            			{
				        $vehicle_images_found++;
		
        	        		// Do we have a thumbnail?  If so, our job is simple here :)
					if ( (empty($gallery_data[$i]['attach_thumb_location']) == FALSE) AND ($gallery_data[$i]['attach_thumb_location'] != $gallery_data[$i]['attach_location']) )
                			{
                    				// Form the image link
						$thumb_image = $phpbb_root_path . GARAGE_UPLOAD_PATH . $gallery_data[$i]['attach_thumb_location'];
						$gallery_vehicle_images .= '<a href="garage.'.$phpEx.'?mode=view_gallery_item&amp;type=garage_gallery&amp;image_id='. $gallery_data[$i]['attach_id'] .'" title="' . $gallery_data[$i]['attach_file'] .'" target="_blank"><img hspace="5" vspace="5" src="' . $thumb_image .'" class="attach"  /></a> ';
               				} 
				}
	        	}
		}

		//Display Both Vehicle Gallery & Modification Gallery	
		if ( (empty($gallery_modification_images) == FALSE) AND (empty($gallery_vehicle_images) == FALSE) )
		{

			$template->assign_block_vars('switch_top_block.owned_no.gallery_all', array(
				'VEHICLE_IMAGES' => $gallery_vehicle_images,
				'MODIFICATION_IMAGES' => $gallery_modification_images)
			);
		}

		//Display Just Vehicle Gallery	
		if ( (empty($gallery_modification_images) == TRUE) AND (empty($gallery_vehicle_images) == FALSE) )
		{
			$template->assign_block_vars('switch_top_block.owned_no.gallery_vehicle', array(
				'VEHICLE_IMAGES' => $gallery_vehicle_images)
			);
		}

		//Display Just Modification Gallery	
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
	function select_all_vehicle_data($additional_where, $order_by, $sort_order, $start=0, $end=10000)
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
			ORDER BY $order_by $sort_order
			LIMIT $start, $end";

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

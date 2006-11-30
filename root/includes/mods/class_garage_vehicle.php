<?php
/***************************************************************************
 *                              class_garage_vehicle.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: class_garage_vehicle.php 138 2006-06-07 15:55:46Z poyntesm $
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
		global $db, $user, $garage_config, $garage;

		if (empty($garage_config['private_add_quota']))
		{
			//Since No Specific Group Value Exists Use Default Value
			return $garage_config['max_user_cars'];
		}
		//It Appears Some Groups Have Private Permissions & Quotas We Will Need To Check Them
		else
		{
			//Get All Group Memberships
			$groupdata = $garage->get_group_membership($user->data['user_id']);
			
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
		global $db, $garage_config;

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
			if (($index = array_search($gid, $private_add_groups)) === false)
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
		global $user, $db;

		$pending = ($data['guestbook_pm_notify'] == 'on') ? 1 : 0;

		$sql = "INSERT INTO ". GARAGE_TABLE ."
			(made_year, engine_type, make_id, model_id, colour, mileage, mileage_units, price, currency, comments, user_id, date_created, date_updated, main_vehicle, guestbook_pm_notify)
			VALUES
			('".$data['year']."', '".$data['engine_type']."', '".$data['make_id']."', '".$data['model_id']."', '".$data['colour']."', '".$data['mileage']."', '".$data['mileage_units']."', '".$data['price']."', '".$data['currency']."', '".$data['comments']."', '".$user->data['user_id']."', '".time()."', '".time()."', '".$data['main_vehicle']."', '". $pending."')";

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
		global $user, $db;

		$sql = "SELECT count(id) AS total 
			FROM " . GARAGE_TABLE . " 
			WHERE user_id = " . $user->data['user_id'];

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

		$pending = ($data['guestbook_pm_notify'] == 'on') ? 1 : 0;

		$sql = "UPDATE ". GARAGE_TABLE ."
			SET made_year = '".$data['year']."', engine_type = '".$data['engine_type']."', make_id = '".$data['make_id']."', model_id = '".$data['model_id']."', colour = '".$data['colour']."', mileage = '".$data['mileage']."', mileage_units = '".$data['mileage_units']."', price = '".$data['price']."', currency = '".$data['currency']."', comments = '".$data['comments']."', guestbook_pm_notify = '".$pending."'
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
	// Resets All Vehicles Rating To 0
	// Usage: reset_all_vehicles_rating();
	/*========================================================================*/
	function reset_all_vehicles_rating()
	{
		global $db;

		//Just Remove All Rows
		$sql = "DELETE FROM " . GARAGE_RATING_TABLE;
	
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Remove All Vehicle Ratings', '', __LINE__, __FILE__, $sql);
		}

		//Reset Weighted Values For All Vehicles
		$sql = "UPDATE " . GARAGE_TABLE ."
			SET weighted_rating = '0'";
	
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Update Vehicle Weighted Rating', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Calculates A Vehicles Weighted Rating
	// Usage: update_vehicle_rating('vehicle id');
	/*========================================================================*/
	function calculate_weighted_rating($cid)
	{
		global $db, $garage_config;

		//Count Votes This Vehicle Has Recived & Average Rating So Far
		$sql = "SELECT count(id) AS votes_recieved, AVG(rating) as average_rating
			FROM " . GARAGE_RATING_TABLE . "
			WHERE id = '$cid'";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Error Counting Vehicles', '', __LINE__, __FILE__, $sql);
		}

	        $row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		//Get Average Rating For All Vehicles
		$sql = "SELECT AVG(rating) as site_average
			FROM " . GARAGE_RATING_TABLE;

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Error Counting Vehicles', '', __LINE__, __FILE__, $sql);
		}

	        $row1 = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		//Weighted Rating Formula We Use 'WR=(V/(V+M)) * R + (M/(V+M)) * C'
		// WR=Weighted Rating (The new rating)
		// R=Average Rating (arithmetic mean) so far
		// V=Number of ratings given
		// M=Minimum number of ratings needed
		// C=Arithmetic mean rating across the whole site
		$weighted_rating = ( $row['votes_recieved'] / ($row['votes_recieved'] + $garage_config['minimum_ratings_required']) ) * $row['average_rating'] + ($garage_config['minimum_ratings_required']/($row['votes_recieved']+$garage_config['minimum_ratings_required'])) * $row1['site_average'];

		return $weighted_rating;
	}

	/*========================================================================*/
	// Updates Weighted Rating Of Vehicle In DB
	// Usage: update_vehicle_rating('vehicle id', 'weighted rating');
	/*========================================================================*/
	function update_weighted_rating($cid, $weighted_rating)
	{
		global $db;

		$sql = "UPDATE ". GARAGE_TABLE ." 
			SET weighted_rating = '$weighted_rating'
	       		WHERE id = '$cid';";

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
		global $user, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config, $auth;
	
		if (empty($cid))
		{
	 		message_die(GENERAL_ERROR, 'Vehicle ID Not Entered..', '', __LINE__, __FILE__);
		}
	
		$sql = "SELECT g.user_id 
			FROM " . GARAGE_TABLE . " g 
			WHERE g.id = $cid ";
	
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
		}
	
		$vehicle = $db->sql_fetchrow($result); 
		$db->sql_freeresult($result);

	 	if ( $auth->acl_get('m_garage') )
		{
			//Allow A Moderator Or Administrator Do What They Want....
			return;
		}
		else if ( $vehicle['user_id'] != $user->data['user_id'] )
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
		global $garage;
		
		$data['time'] = time();

		$garage->update_single_field(GARAGE_TABLE, 'date_updated', $data['time'], 'id', $cid);
	
		return;
	}
	
	/*========================================================================*/
	// Build Featured Vehicle HTML If Required..A Absolute URL Can Be Passed To
	// Allow Calls From A Different Domain..I.E Fourm Resides In A Subdomain 
	// Usage: show_featuredvehicle( 'URL');
	/*========================================================================*/
	function show_featuredvehicle( $absolute_url = NULL )
	{
		global $user, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		if ( $garage_config['enable_featured_vehicle'] == 1 )
		{
			$template->assign_block_vars('show_featured_vehicle', array());

			// If we are using random, go fetch!
			$featured_vehicle_id = '';
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
				$where = "LEFT JOIN " . GARAGE_DYNORUN_TABLE . " AS rr on g.id = rr.garage_id
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
			$sql = "SELECT COUNT(id) as num_vehicle FROM " . GARAGE_TABLE . " WHERE id='". $featured_vehicle_id ."'";
			$result = $db->sql_query($sql);
			$total_vehicles = (int) $db->sql_fetchfield('num_vehicle');
			$db->sql_freeresult($result);
	
		        if ( $total_vehicles > 0 OR (!empty($garage_config['featured_vehicle_from_block'])) )
	        	{
		            	// Grab the vehicle info and prep the HTML
				$sql = "SELECT g.id, g.made_year, g.image_id, g.user_id, makes.make, models.model, 
	                           	images.attach_id, images.attach_hits, images.attach_thumb_location, m.username, 
			                images.attach_is_image, images.attach_location, COUNT(mods.id) AS mod_count,
					CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, images.attach_file,
					(SUM(mods.install_price) + SUM(mods.price)) AS money_spent, sum( r.rating ) AS rating
	                 	        FROM " . GARAGE_TABLE . " AS g 
	                        		LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
		                            	LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = g.image_id
			                        LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id 
			                        LEFT JOIN " . GARAGE_MODS_TABLE . " AS mods ON g.id = mods.garage_id 
			                        LEFT JOIN " . GARAGE_RATING_TABLE . " AS r ON g.id = r.garage_id 
	        		                LEFT JOIN " . USERS_TABLE . " AS m ON g.user_id = m.user_id
				    	$where";
	
				if(!$result = $db->sql_query($sql))
				{
					message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
				}
		
	        	    	$vehicle_data = $db->sql_fetchrow($result);
	
				// Do we have a hilite image?  If so, prep the HTML
				$featured_image = '';
				if ( (empty($vehicle_data['attach_id']) == false) AND ($vehicle_data['attach_is_image'] == 1) ) 
	        	    	{
	                		// Do we have a thumbnail?  If so, our job is simple here :)
			                if ( (empty($vehicle_data['attach_thumb_location']) == false) AND ($vehicle_data['attach_thumb_location'] != $vehicle_data['attach_location']) AND (@file_exists($phpbb_root_path . GARAGE_UPLOAD_PATH."/".$vehicle_data['attach_thumb_location'])) )
	                		{
						// Yippie, our thumbnail is already made for us :)
					   	$thumb_image = $phpbb_root_path . GARAGE_UPLOAD_PATH . $vehicle_data['attach_thumb_location'];
						if (!empty($absolute_url))
						{
							$thumb_image = $absolute_url . GARAGE_UPLOAD_PATH . $vehicle_data['attach_thumb_location'];

						}
						$featured_image = '<a href="'.$absolute_url.'garage.'.$phpEx.'?mode=view_gallery_item&amp;type=garage_mod&amp;image_id='. $vehicle_data['attach_id'] .'" title="' . $vehicle_data['attach_file'] .'" target="_blank"><img hspace="5" vspace="5" src="' . $thumb_image .'" /></a>';
	                		} 
	        		}
				$template->assign_vars(array(
					'FEATURED_DESCRIPTION' => $garage_config['featured_vehicle_description'],
					'FEATURED_IMAGE' => $featured_image,
					'VEHICLE' => $vehicle_data['vehicle'],
					'USERNAME' => $vehicle_data['username'],
					'U_VIEW_VEHICLE' => append_sid($absolute_url."garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
					'U_VIEW_PROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;u=".$vehicle_data['user_id']))
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
		global $required_position, $user, $template, $db, $SID, $lang, $phpEx, $garage_config, $garage_vehicle, $board_config;
	
		if ( $garage_config['enable_updated_vehicle'] != true )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $user->lang['LAST_UPDATED_VEHICLES'],
			'COLUMN_1_TITLE' => $user->lang['VEHICLE'],
			'COLUMN_2_TITLE' => $user->lang['OWNER'],
			'COLUMN_3_TITLE' => $user->lang['UPDATED'])
		);
	 		
	        // What's the count? Default to 10
		$limit = $garage_config['updated_vehicle_limit'] ? $garage_config['updated_vehicle_limit'] : 10;

		//Get Latest Updated Vehicles....
		$vehicle_data = $garage_vehicle->get_latest_updated_vehicles($limit);
	
		for ($i = 0; $i < count($vehicle_data); $i++)
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' => append_sid("garage.$phpEx", "mode=view_vehicle&CID=" . $vehicle_data[$i]['id'], true),
				'U_COLUMN_2' => append_sid("profile.$phpEx", "mode=viewprofile&u=" . $vehicle_data[$i]['user_id'], true),
				'COLUMN_1_TITLE' => $vehicle_data[$i]['vehicle'],
				'COLUMN_2_TITLE' => $vehicle_data[$i]['username'],
				'COLUMN_3' => $user->format_date($vehicle_data[$i]['date_updated']))
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
		global $required_position, $user, $template, $db, $SID, $lang, $phpEx, $garage_config, $board_config;
	
		if ( $garage_config['enable_most_spent'] != true )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $user->lang['MOST_MONEY_SPENT'],
			'COLUMN_1_TITLE' => $user->lang['VEHICLE'],
			'COLUMN_2_TITLE' => $user->lang['OWNER'],
			'COLUMN_3_TITLE' => $user->lang['TOTAL_SPENT'])
		);
	 		
	        // What's the count? Default to 10
	        $limit = $garage_config['most_spent_limit'] ? $garage_config['most_spent_limit'] : 10;
	 		
	 	$sql = "SELECT g.id, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, 
	                        g.user_id, (SUM(mods.install_price) + SUM(mods.price)) AS POI, u.username, g.currency 
	                FROM " . GARAGE_TABLE . " g 
	                	LEFT JOIN " . GARAGE_MAKES_TABLE . " makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " models ON g.model_id = models.id
	                        LEFT JOIN " . GARAGE_MODS_TABLE . " mods ON mods.garage_id = g.id 
	                        LEFT JOIN " . USERS_TABLE . " u ON g.user_id = u.user_id
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
				'U_COLUMN_2' => append_sid("profile.$phpEx?mode=viewprofile&amp;u=".$vehicle_data['user_id']),
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
		global $required_position, $user, $template, $db, $SID, $lang, $phpEx, $garage_config, $board_config;
	
		if ( $garage_config['enable_most_viewed'] != true )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $user->lang['MOST_VIEWED_VEHICLE'],
			'COLUMN_1_TITLE' => $user->lang['VEHICLE'],
			'COLUMN_2_TITLE' => $user->lang['OWNER'],
			'COLUMN_3_TITLE' => $user->lang['VIEWS'])
		);
	
	        // What's the count? Default to 10
	        $limit = $garage_config['most_viewed_limit'] ? $garage_config['most_viewed_limit'] : 10;
	 		 		
	 	$sql = "SELECT g.id, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, 
	                        g.user_id, g.views AS POI, u.username 
	                FROM " . GARAGE_TABLE . " g 
	                        LEFT JOIN " . GARAGE_MAKES_TABLE . " makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " models ON g.model_id = models.id
	                        LEFT JOIN " . USERS_TABLE . " u ON g.user_id = u.user_id
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
				'U_COLUMN_2' => append_sid("profile.$phpEx?mode=viewprofile&amp;u=".$vehicle_data['user_id']),
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
		global $required_position, $user, $template, $db, $SID, $lang, $phpEx, $garage_config, $board_config;
	
		if ( $garage_config['enable_top_rating'] != true )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $user->lang['TOP_RATED_VEHICLES'],
			'COLUMN_1_TITLE' => $user->lang['VEHICLE'],
			'COLUMN_2_TITLE' => $user->lang['OWNER'],
			'COLUMN_3_TITLE' => $user->lang['RATING'])
		);
	
	        // What's the count? Default to 10
	        $limit = $garage_config['top_rating_limit'] ? $garage_config['top_rating_limit'] : 10;
	
		$sql =  "SELECT g.id, g.user_id, ROUND(g.weighted_rating, 2) as weighted_rating, u.username, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle
			 FROM " . GARAGE_TABLE . " g
	                        LEFT JOIN " . GARAGE_MAKES_TABLE . " makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " models ON g.model_id = models.id
	                        LEFT JOIN " . USERS_TABLE . " u ON g.user_id = u.user_id
			 WHERE makes.pending = 0 AND models.pending = 0
			 ORDER BY weighted_rating DESC LIMIT $limit";
	 		 		
	 	if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
		}
	 		            
	 	while ( $vehicle_data = $db->sql_fetchrow($result) )
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
				'U_COLUMN_2' => append_sid("profile.$phpEx?mode=viewprofile&amp;u=".$vehicle_data['user_id']),
				'COLUMN_1_TITLE' => $vehicle_data['vehicle'],
				'COLUMN_2_TITLE' => $vehicle_data['username'],
				'COLUMN_3' => $vehicle_data['weighted_rating'] . '/' . '10')
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
		global $required_position, $user, $template, $db, $SID, $lang, $phpEx, $garage_config, $board_config;
	
		if ( $garage_config['enable_newest_vehicle'] != true )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $user->lang['NEWEST_VEHICLES'],
			'COLUMN_1_TITLE' => $user->lang['VEHICLE'],
			'COLUMN_2_TITLE' => $user->lang['OWNER'],
			'COLUMN_3_TITLE' => $user->lang['CREATED'])
		);
	 		
	        // What's the count? Default to 10
	        $limit = $garage_config['newest_vehicle_limit'] ? $garage_config['newest_vehicle_limit'] : 10;
	 		 		
	 	$sql = "SELECT g.id, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, 
	                        g.user_id, g.date_created AS POI, u.username 
	                FROM " . GARAGE_TABLE . " g 
	                	LEFT JOIN " . GARAGE_MAKES_TABLE . " makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " models ON g.model_id = models.id
	                        LEFT JOIN " . USERS_TABLE . " u ON g.user_id = u.user_id
			WHERE makes.pending = 0 AND models.pending = 0
	                ORDER BY POI DESC LIMIT $limit";
	 		            
	 	if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
		}
	 		            
	 	while ( $vehicle_data = $db->sql_fetchrow($result) )
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' 	=> append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
				'U_COLUMN_2' 	=> append_sid("profile.$phpEx?mode=viewprofile&amp;u=".$vehicle_data['user_id']),
				'COLUMN_1_TITLE'=> $vehicle_data['vehicle'],
				'COLUMN_2_TITLE'=> $vehicle_data['username'],
				'COLUMN_3' 	=> $user->format_date($vehicle_data['POI']))
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
		global $user, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		//Right User Want To Delete Vehicle Let Get All Mods Associated With It 
		$mods_sql = "SELECT id FROM " . GARAGE_MODS_TABLE . " WHERE garage_id = $cid";
	
		if ( !($mods_result = $db->sql_query($mods_sql)) )
	     	{
	       		message_die(GENERAL_ERROR, 'Could Not Select Modification Data For Vehicle', '', __LINE__, __FILE__, $sql);
	      	}
	
		while ($mods_row = $db->sql_fetchrow($mods_result) )
		{
			$garage_modification->delete_modification($mods_row['id']);
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
			$garage_quartermile->delete_quartermile($qmid);
		}
		$db->sql_freeresult($quartermile_result);
	
		//Right User Want To Delete Vehicle Let Get All Rolling Road Times Associated With It 
		$dynorun_sql = "SELECT id 
			FROM " . GARAGE_DYNORUN_TABLE . " 
			WHERE garage_id = $cid";
	
	     	if ( !($dynorun_result = $db->sql_query($dynorun_sql)) )
	     	{
	       		message_die(GENERAL_ERROR, 'Could Not Select Rollingroad Data For Vehicle', '', __LINE__, __FILE__, $sql);
	     	}
	
		while ($dynorun_row = $db->sql_fetchrow($dynorun_result) )
		{
			$rrid = $dynorun_row['id'];
			$garage_dynorun->delete_dynorun($rrid);
		}
		$db->sql_freeresult($dynorun_result);
	
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
			$garage_insurance->delete_premium($ins_id);
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
			$garage->delete_rows(GARAGE_GUESTBOOKS, 'id', $gb_row['id']);
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
   			$garage->delete_rows(GARAGE_RATING_TABLE, 'id', $rating_row['id']);
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
			$garage_image->delete_image($image_id);
		}
		$db->sql_freeresult($result);
	
		// Right We Have Deleted Modifications & Images Next The Actual Vehicle
		$garage->delete_rows(GARAGE_TABLE, 'id', $cid);
	
		return;
	}


	/*========================================================================*/
	// Display Vehicle Page - With Or Without Management Links & Galleries
	// Usage:  display_vehicle('wn vehicle YES|NO|MODERATE');
	/*========================================================================*/
	function display_vehicle($owned)
	{
		global $user, $template, $images, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config, $HTTP_POST_FILES, $HTTP_POST_VARS, $HTTP_GET_VARS, $rating_text, $rating_types, $cid, $mode, $garage, $garage_template, $garage_modification, $garage_insurance, $garage_quartermile, $garage_dynorun, $garage_image, $auth;

		//Since We Called This Fuction Display Top Block With All Vehicle Info
		$template->assign_block_vars('switch_top_block', array());
	
		if ( ( $owned == 'YES') OR ( $owned == 'MODERATE') )
		{
			$this->check_ownership($cid);
		}
		else
		{
			$template->assign_block_vars('switch_top_block.owned_no', array());
		}
	
		$vehicle_row = $this->get_vehicle($cid);
	
		$avatar_img = '';
		if ( $owned == 'NO' AND $vehicle_row['user_avatar_type'])
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
		
		//We Are Moderating...So Show Options Required
		if ( $owned == 'MODERATE' )
		{
			$reset_rating_link = '<a href="javascript:confirm_reset_rating(' . $cid . ')"><img src="' . $images['garage_delete'] . '" alt="'.$lang['Delete'].'" title="'.$lang['Delete'].'" border="0" /></a>';
			$template->assign_block_vars('moderate', array());
			$template->assign_vars(array(
				'L_RATING_MODERATION' 	=> $lang['Rating Moderation'],
				'L_RATER' 		=> $lang['Rater'],
				'L_RATING' 		=> $lang['Rating'],
				'L_DATE'	 	=> $lang['Date'],
				'L_RESET_VEHICLE_RATING'=> $lang['Reset_Vehicle_Rating'],
				'RESET_RATING_LINK' 	=> $reset_rating_link)
			);

			//Let Get Vehicle Rating Details
			$rating_data = $this->get_vehicle_rating($cid);
			for ($i = 0; $i < count($rating_data); $i++)
			{
				$delete_rating_link 		= '<a href="javascript:confirm_delete_rating(' . $cid . ',' . $rating_data[$i]['id'] . ')"><img src="' . $images['garage_delete'] . '" alt="'.$lang['Delete'].'" title="'.$lang['Delete'].'" border="0" /></a>';
				$rating_data[$i]['user_id']	=  ($rating_data[$i]['user_id'] < 0 ) ? ANONYMOUS : $rating_data[$i]['user_id'];
				$rating_data[$i]['username'] 	=  ($rating_data[$i]['user_id'] < 0 ) ? $lang['Guest'] : $rating_data[$i]['username'];

				$template->assign_block_vars('moderate.rating_row', array(
					'U_PROFILE'	=> append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=".$rating_data[$i]['user_id']) ,
					'USERNAME' 	=> $rating_data[$i]['username'] ,
					'RATING' 	=> $rating_data[$i]['rating'],
					'DATE' 		=> create_date('D M d, Y G:i', $rating_data[$i]['rate_date'], $board_config['board_timezone']),
					'DELETE_RATING_LINK' => $delete_rating_link)
				);
			}	
		}
	
		if ( $owned == 'NO' )
		{
			$template->assign_block_vars('switch_top_block.owned_no.rating', array());
	
			$sql = "SELECT count(*) as total, rate_date 
				FROM " . GARAGE_RATING_TABLE . "
			      	WHERE user_id = " . $user->data['user_id'] ." 
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
					'L_RATING_NOTICE' => '',
					'RATE_VEHICLE' => $garage_template->dropdown('vehicle_rating',$rating_text,$rating_types,''))
				);
			}
			//Rated Already But Permanent So Do Not Show Button
			else if ( ( $row['total'] > 0 ) AND ($garage_config['rating_permanent']) )
			{
				$template->assign_vars(array(
					'L_RATING_NOTICE' => $user->lang['RATE_PERMANENT'])
				);
			}
			//Rated Already But Not Permanent & Always Updateable
			else if ( ( $row['total'] > 0 ) AND (!$garage_config['rating_permanent']) AND ($garage_config['rating_always_updateable']) )
			{
				$template->assign_block_vars('switch_top_block.owned_no.rating.rate', array());
				$template->assign_vars(array(
					'RATE_VEHICLE' => $garage_template->dropdown('vehicle_rating',$rating_text,$rating_types,''),
					'L_RATING_NOTICE' => $user->lang['UPDATE_RATING'])
				);
			}
			//Rated Already But Not Permanent & Updated Not Always Allowed, Vehicle Not Update So No Rate Update
			else if ( ( $row['total'] > 0 ) AND (!$garage_config['rating_permanent']) AND (!$garage_config['rating_always_updateable']) AND ($row['rate_date'] > $date_updated) )
			{
				$template->assign_vars(array(
					'L_RATING_NOTICE' => $user->lang['VEHICLE_UPDATE_REQUIRED_FOR_RATE'])
				);
			}
			//Rated Already But Not Permanent & Updated Not Always Allowed, Vehicle Updated So Rate Update Allowed
			else if ( ( $row['total'] > 0 ) AND (!$garage_config['rating_permanent']) AND (!$garage_config['rating_always_updateable']) AND ($row['rate_date'] < $date_updated) )
			{
				$template->assign_block_vars('switch_top_block.owned_no.rating.rate', array());
				$template->assign_vars(array(
					'RATE_VEHICLE' =>$garage_template->dropdown('vehicle_rating',$rating_text,$rating_types,''),
					'L_RATING_NOTICE' => $user->lang['UPDATE_RATING'])
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
	 			$comment_count ='( ' . $guestbook_count . ' ' . $user->lang['TOTAL_COMMENTS'] . ' )';
	
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
				$guestbook['messages'] = '';
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
	
	
		//Set Counter To Zero
		$mod_images_found = 0;
		$mod_images_displayed = '';
	     
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

		$category_data = NULL;
		while ($row = $db->sql_fetchrow($result) )
		{
			$category_data[] = $row;
		}

		$db->sql_freeresult($result);

	      	//Loop Processing All Categoires Returned....
	      	for ( $i = 0; $i < count($category_data); $i++ )
		{
	       		//Setup cat_row Template Varibles
	       		$template->assign_block_vars('cat_row', array(
	           		'CATEGORY_TITLE' => $category_data[$i]['title'])
	       		);
	
			// Select All Mods From This Car For Category We Are Currently Processing
			$modification_data = $garage_modification->get_modifications_by_category($cid, $category_data[$i]['id']);

			//Process Modifications From This Category..
			$gallery_modification_images = null;
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

				$edit_mod_link = '';	
				$delete_mod_link = '';
	
	            		$template->assign_block_vars('cat_row.user_row', array(
	               			'IMAGE_ATTACHED'	=> $image_attached,
	               			'COST' 			=> $modification_data[$j]['price'],
	               			'INSTALL' 		=> $modification_data[$j]['install_price'],
	               			'RATING' 		=> $modification_data[$j]['product_rating'],
	               			'CREATED' 		=> $user->format_date($modification_data[$j]['date_created']),
	               			'UPDATED' 		=> $user->format_date($modification_data[$j]['date_updated']),
	               			'MODIFICATION' 		=> $modification,
					'U_EDIT'		=> (($owned == 'YES') OR ($owned == 'MODERATE')) ? append_sid("garage.$phpEx?mode=edit_modification&amp;MID=$mid&amp;CID=$cid") : '',
					'U_DELETE' 		=> ( (($owned == 'YES') OR ($owned == 'MODERATE')) AND ( (($auth->acl_get('u_garage_delete_modification'))) OR ($auth->acl_get('m_garage'))) ) ? 'javascript:confirm_delete_mod(' . $cid . ',' . $mid . ')' : '')
	            		);
	
				//See If Mod Has An Image Attached And Display Gallery If Needed
				if ( ( $owned == 'NO' ) AND ($garage_config['enable_mod_gallery'] == 1) AND ( $modification_data[$j]['attach_is_image'] ) )
				{
			        	//If we have a set limit, make sure we haven't hit it
	  		               	if ( ($garage_config['mod_gallery_limit'] >= $mod_images_found) OR !$garage_config['mod_gallery_limit'])
			                {
						$mod_images_displayed = $mod_images_found;
	                			//Do we have a thumbnail?  If so, our job is simple here :)
						if ( (empty($modification_data[$i]['attach_thumb_location']) == false) AND ($modification_data[$j]['attach_thumb_location'] != $modification_data[$j]['attach_location']) )
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
		$insurance_data = $garage_insurance->get_premiums_by_vehicle($cid);
	
         	//If Any Premiums Exist Process Them...
		if ( count($insurance_data) > 0 )
		{
			$template->assign_block_vars('insurance', array());
        		for ( $i = 0; $i < count($insurance_data); $i++ )
	         	{
				$template->assign_block_vars('insurance.premium', array(
					'COMPANY' 	=> $insurance_data[$i]['title'],
					'PREMIUM' 	=> $insurance_data[$i]['premium'],
					'COVER_TYPE' 	=> $insurance_data[$i]['cover_type'],
					'U_EDIT'	=> (($owned == 'YES') OR ($owned == 'MODERATE')) ? append_sid("garage.$phpEx?mode=edit_insurance&amp;INS_ID=".$insurance_data[$i]['id']."&amp;CID=$cid") : '',
					'U_DELETE' 	=> ( (($owned == 'YES') OR ($owned == 'MODERATE')) AND ( (($auth->acl_get('u_garage_delete_insurance'))) OR ($auth->acl_get('m_garage'))) ) ? 'javascript:confirm_delete_insurance(' . $cid . ',' . $insurance_data[$i]['id'] . ')' : '')
				);
			}
		}
	
		//Next Lets See If We Have Any QuarterMile Runs
		$quartermile_data = $garage_quartermile->get_quartermile_by_vehicle($cid);
	
         	//If Any Quartermiles Exist Process Them...
		if ( count($quartermile_data) > 0 )
		{
			$template->assign_block_vars('quartermile', array());
        		for ( $i = 0; $i < count($quartermile_data); $i++ )
	         	{
				$template->assign_block_vars('quartermile.run', array(
					'RT' 		=> $quartermile_data[$i]['rt'],
					'SIXTY' 	=> $quartermile_data[$i]['sixty'],
					'THREE' 	=> $quartermile_data[$i]['three'],
					'EIGHT' 	=> $quartermile_data[$i]['eight'],
					'EIGHTMPH' 	=> $quartermile_data[$i]['eightmph'],
					'THOU' 		=> $quartermile_data[$i]['thou'],
					'QUART' 	=> $quartermile_data[$i]['quart'],
					'QUARTMPH' 	=> $quartermile_data[$i]['quartmph'],
					'U_IMAGE'	=> ($quartermile_data[$i]['image_id']) ? append_sid("garage.$phpEx", "mode=view_gallery_item&amp;image_id=". $quartermile_data[$i]['image_id']) : '',
					'IMAGE'		=> $user->img('garage_slip_img_attached', 'SLIP_IMAGE_ATTACHED'),
					'U_EDIT'	=> (($owned == 'YES') OR ($owned == 'MODERATE')) ? append_sid("garage.$phpEx?mode=edit_quartermile&amp;QMID=".$quartermile_data[$i]['id']."&amp;CID=$cid") : '',
					'U_DELETE' 	=> ( (($owned == 'YES') OR ($owned == 'MODERATE')) AND ( (($auth->acl_get('u_garage_delete_quartermile'))) OR ($auth->acl_get('m_garage'))) ) ? 'javascript:confirm_delete_quartermile(' . $cid . ',' . $quartermile_data[$i]['id'] . ')' : '')
				);
			}
		}

		//Get All Dynoruns For Vehicle
		$dynorun_data = $garage_dynorun->get_dynoruns_by_vehicle($cid);
	
         	//If Any Dynoruns Exist Process Them...
		if ( count($dynorun_data) > 0 )
		{
			$template->assign_block_vars('dynorun', array());
         		for ( $i = 0; $i < count($dynorun_data); $i++ )
         		{
				$template->assign_block_vars('dynorun.run', array(
					'DYNOCENTER'	=> $dynorun_data[$i]['dynocenter'],
					'BHP' 		=> $dynorun_data[$i]['bhp'],
					'BHP_UNIT' 	=> $dynorun_data[$i]['bhp_unit'],
					'TORQUE' 	=> $dynorun_data[$i]['torque'],
					'TORQUE_UNIT' 	=> $dynorun_data[$i]['torque_unit'],
					'BOOST' 	=> $dynorun_data[$i]['boost'],
					'BOOST_UNIT' 	=> $dynorun_data[$i]['boost_unit'],
					'NITROUS' 	=> $dynorun_data[$i]['nitrous'],
					'PEAKPOINT' 	=> $dynorun_data[$i]['peakpoint'],
					'U_IMAGE'	=> ($dynorun_data[$i]['image_id']) ? append_sid("garage.$phpEx", "mode=view_gallery_item&amp;image_id=". $dynorun_data[$i]['image_id']) : '',
					'IMAGE'		=> $user->img('garage_slip_img_attached', 'SLIP_IMAGE_ATTACHED'),
					'U_EDIT'	=> (($owned == 'YES') OR ($owned == 'MODERATE')) ? append_sid("garage.$phpEx?mode=edit_dynorun&amp;RRID=".$dynorun_data[$i]['id']."&amp;CID=$cid") : '',
					'U_DELETE' 	=> ( (($owned == 'YES') OR ($owned == 'MODERATE')) AND ( (($auth->acl_get('u_garage_delete_dynorun'))) OR ($auth->acl_get('m_garage'))) ) ? 'javascript:confirm_delete_dynorun(' . $cid . ',' . $dynorun_data[$i]['id'] . ')' : '')
				);
			}
		}
			
		if ( $owned == 'NO' )
		{
			//Set Inital Count To Zero
			$vehicle_images_found = 0;	

			//Get All Gallery Data Required
			$gallery_data = $garage_image->get_gallery($cid);
			
			$gallery_vehicle_images = '';
			//Process Each Image From Vehicle Gallery	
        		for ( $i = 0; $i < count($gallery_data); $i++ )
	        	{
        	    		if ( $gallery_data[$i]['attach_is_image'] )
            			{
				        $vehicle_images_found++;
		
        	        		// Do we have a thumbnail?  If so, our job is simple here :)
					if ( (empty($gallery_data[$i]['attach_thumb_location']) == false) AND ($gallery_data[$i]['attach_thumb_location'] != $gallery_data[$i]['attach_location']) )
                			{
                    				// Form the image link
						$thumb_image = $phpbb_root_path . GARAGE_UPLOAD_PATH . $gallery_data[$i]['attach_thumb_location'];
						$gallery_vehicle_images .= '<a href="garage.'.$phpEx.'?mode=view_gallery_item&amp;type=garage_gallery&amp;image_id='. $gallery_data[$i]['attach_id'] .'" title="' . $gallery_data[$i]['attach_file'] .'" target="_blank"><img hspace="5" vspace="5" src="' . $thumb_image .'" class="attach"  /></a> ';
               				} 
				}
	        	}
		}

		//Display Both Vehicle Gallery & Modification Gallery	
		if ( (empty($gallery_modification_images) == false) AND (empty($gallery_vehicle_images) == false) )
		{

			$template->assign_block_vars('switch_top_block.owned_no.gallery_all', array(
				'VEHICLE_IMAGES' => $gallery_vehicle_images,
				'MODIFICATION_IMAGES' => $gallery_modification_images)
			);
		}

		//Display Just Vehicle Gallery	
		if ( (empty($gallery_modification_images) == true) AND (empty($gallery_vehicle_images) == false) )
		{
			$template->assign_block_vars('switch_top_block.owned_no.gallery_vehicle', array(
				'VEHICLE_IMAGES' => $gallery_vehicle_images)
			);
		}

		//Display Just Modification Gallery	
		if ( (empty($gallery_modification_images) == false) AND (empty($gallery_vehicle_images) == true) )
		{
			$template->assign_block_vars('switch_top_block.owned_no.gallery_modification', array(
				'MODIFICATION_IMAGES' => $gallery_modification_images)
			);
		}

		$template->assign_vars(array(
			'U_DELETE_MODIFICATION' => append_sid("garage.$phpEx?mode=delete_modification"),
			'U_DELETE_QUARTERMILE'	=> append_sid("garage.$phpEx?mode=delete_quartermile"),
			'U_DELETE_PREMIUM' 	=> append_sid("garage.$phpEx?mode=delete_insurance"),
			'U_DELETE_DYNORUN' 	=> append_sid("garage.$phpEx?mode=delete_dynorun"),
            		'U_PROFILE' 		=> append_sid("profile.$phpEx?mode=viewprofile&amp;u=".$vehicle_row['user_id']),
            		'U_VIEW_VEHICLE' 	=> ( $owned == 'YES' ) ? append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=$cid") : '',
            		'U_EDIT_VEHICLE' 	=> ( $owned == 'YES' ) ? append_sid("garage.$phpEx?mode=edit_vehicle&amp;CID=$cid") : '',
            		'U_DELETE_VEHICLE' 	=> ( ($owned == 'YES' AND $auth->acl_get('u_garage_delete_vehicle')) OR ($auth->acl_get('m_garage'))) ? 'javascript:confirm_delete_car(' . $cid . ')' : '',
            		'U_ADD_MODIFICATION' 	=> ( $owned == 'YES' ) ? append_sid("garage.$phpEx?mode=add_modification&amp;CID=$cid") : '',
            		'U_ADD_INSURANCE' 	=> ( $owned == 'YES' AND $garage_config['enable_insurance'] ) ? append_sid("garage.$phpEx?mode=add_insurance&amp;CID=$cid") : '',
            		'U_ADD_QUARTERMILE' 	=> ( $owned == 'YES' AND $garage_config['enable_quartermile'] ) ? append_sid("garage.$phpEx?mode=add_quartermile&amp;CID=$cid") : '',
            		'U_ADD_DYNORUN' 	=> ( $owned == 'YES' AND $garage_config['enable_dynorun'] ) ? append_sid("garage.$phpEx?mode=add_dynorun&amp;CID=$cid") : '',
            		'U_MANAGE_VEHICLE_GALLERY'=> ( $owned == 'YES' ) ? append_sid("garage.$phpEx?mode=manage_vehicle_gallery&amp;CID=$cid") : '',
			'U_SET_MAIN_VEHICLE' 	=> ( ($owned == 'YES' OR $owned == 'MODERATE') AND ($vehicle_row['main_vehicle'] == 0) ) ?  append_sid("garage.$phpEx?mode=set_main&amp;CID=$cid"): '' ,
			'U_MODERATE_VEHICLE' 	=> ( $owned == 'NO' AND $auth->acl_get('m_garage')) ?  append_sid("garage.$phpEx?mode=moderate_vehicle&amp;CID=$cid"): '' ,
			'U_HILITE_IMAGE' 	=> ( ($vehicle_row['attach_id']) AND ($vehicle_row['attach_is_image']) AND (!empty($vehicle_row['attach_thumb_location'])) AND (!empty($vehicle_row['attach_location'])) ) ?  append_sid("garage.$phpEx?mode=view_gallery_item&amp;type=garage_mod&amp;image_id=". $vehicle_row['attach_id']): '' ,

            		'EDIT' 			=> ($garage_config['enable_images']) ? $user->img('garage_edit', 'EDIT') : $user->lang['EDIT'],
            		'DELETE' 		=> ($garage_config['enable_images']) ? $user->img('garage_delete', 'DELETE') : $user->lang['DELETE'],
            		'VIEW_VEHICLE' 		=> ($garage_config['enable_images']) ? $user->img('garage_view_vehicle', 'VIEW_VEHICLE') : $user->lang['VIEW_VEHICLE'],
            		'EDIT_VEHICLE' 		=> ($garage_config['enable_images']) ? $user->img('garage_edit_vehicle', 'EDIT_VEHICLE') : $user->lang['EDIT_VEHICLE'],
            		'ADD_MODIFICATION' 	=> ($garage_config['enable_images']) ? $user->img('garage_add_modification', 'ADD_NEW_MODIFICATION') : $user->lang['ADD_NEW_MODIFICATION'],
            		'ADD_INSURANCE' 	=> ($garage_config['enable_images']) ? $user->img('garage_add_insurance', 'ADD_NEW_INSURANCE_PREMIUM') : $user->lang['ADD_NEW_INSURANCE_PREMIUM'],
            		'ADD_QUARTERMILE' 	=> ($garage_config['enable_images']) ? $user->img('garage_add_quartermile', 'ADD_NEW_QUARTERMILE_TIME') : $user->lang['ADD_NEW_QUARTERMILE_TIME'],
            		'ADD_DYNORUN'	 	=> ($garage_config['enable_images']) ? $user->img('garage_add_dynorun',  'ADD_NEW_DYNORUN_RUN') : $user->lang['ADD_NEW_DYNORUN_RUN'],
            		'MANAGE_VEHICLE_GALLERY'=> ($garage_config['enable_images']) ? $user->img('garage_manage_gallery', 'MANAGE_VEHICLE_GALLERY') : $user->lang['MANAGE_VEHICLE_GALLERY'],
            		'DELETE_VEHICLE' 	=> ($garage_config['enable_images']) ? $user->img('garage_delete_vehicle', 'DELETE_VEHICLE') : $user->lang['DELETE_VEHICLE'],
			'SET_MAIN_VEHICLE' 	=> ($garage_config['enable_images']) ? $user->img('garage_main_vehicle', 'SET_MAIN_VEHICLE') : $user->lang['SET_MAIN_VEHICLE'],
			'MODERATE_VEHICLE' 	=> ($garage_config['enable_images']) ? $user->img('garage_moderate_vehicle', 'MODERATE_VEHICLE') : $user->lang['MODERATE_VEHICLE'],

	       		'TOTAL_MOD_IMAGES' 	=> $mod_images_found,
            		'SHOWING_MOD_IMAGES' 	=> $mod_images_displayed,
			'CID' 			=> $vehicle_row['id'],
			'YEAR' 			=> $vehicle_row['made_year'],
			'ENGINE_TYPE' 		=> $vehicle_row['engine_type'],
			'MAKE' 			=> $vehicle_row['make'],
			'MODEL'			=> $vehicle_row['model'],
            		'COLOUR' 		=> $vehicle_row['colour'],
            		'HILITE_IMAGE' 		=> $phpbb_root_path . GARAGE_UPLOAD_PATH . $vehicle_row['attach_thumb_location'] ,
            		'HILITE_IMAGE_TITLE' 	=> $vehicle_row['attach_file'],
            		'AVATAR_IMG' 		=> $avatar_img,
            		'DATE_UPDATED' 		=> $user->format_date($vehicle_row['date_updated']),
            		'MILEAGE' 		=> $vehicle_row['mileage'],
            		'MILEAGE_UNITS' 	=> $vehicle_row['mileage_units'],
            		'PRICE' 		=> $vehicle_row['price'],
            		'CURRENCY' 		=> $vehicle_row['currency'],
            		'TOTAL_MODS' 		=> $vehicle_row['total_mods'],
            		'TOTAL_SPENT' 		=> (!empty($vehicle_row['total_spent'])) ? $vehicle_row['total_spent'] : 0,
            		'TOTAL_VIEWS' 		=> $vehicle_row['views'],
			'TOTAL_IMAGE_VIEWS' 	=> $vehicle_row['attach_hits'],
			'USERNAME'		=> $vehicle_row['username'],
			'RATING' 		=> ( $vehicle_row['weighted_rating'] == '0' ) ? $user->lang['NOT_RATED_YET'] : $vehicle_row['weighted_rating'] . ' / 10',
            		'DESCRIPTION' 		=> str_replace("\n", "\n<br />\n", $vehicle_row['comments']))
         	);

		return;
	}
	
	/*========================================================================*/
	// Select All Vehicles Data From Db
	// Usage: get_all_vehicles();
	/*========================================================================*/
	function get_all_vehicles($additional_where = NULL, $order_by, $sort_order, $start = 0, $end = 10000)
	{
		global $db;

		$sql = "SELECT g.*, makes.make, models.model, user.username, count(mods.id) AS total_mods, count(*) as total
        		FROM " . GARAGE_TABLE . " AS g 
                    		LEFT JOIN " . GARAGE_MODS_TABLE . " AS mods ON mods.garage_id = g.id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
			        LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id 
			        LEFT JOIN " . USERS_TABLE . " AS user ON g.user_id = user.user_id 
			WHERE makes.pending = 0 AND models.pending = 0
				" . $additional_where . "
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

		if (empty($rows))
		{
			return;
		}

		return $rows;
	}

	/*========================================================================*/
	// Select All Vehicle Data From Db
	// Usage: get_vehicle('vehicle id');
	/*========================================================================*/
	function get_vehicle($cid)
	{
		global $db;
		//Select All Vehicle Information
	   	$sql = "SELECT g.*, ROUND(g.weighted_rating, 2) as weighted_rating, images.*, makes.make, models.model, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, count(mods.id) AS total_mods, ( SUM(mods.price) + SUM(mods.install_price) ) AS total_spent, user.username, user.user_avatar_type, user.user_avatar, user.user_id
                      	FROM " . GARAGE_TABLE . " AS g  
				LEFT JOIN " . USERS_TABLE ." AS user ON g.user_id = user.user_id
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

	/*========================================================================*/
	// Select Vehicle Owner From Db
	// Usage: get_vehicle_owner('vehicle id');
	/*========================================================================*/
	function get_vehicle_owner($cid)
	{
		global $db;
		//Select All Vehicle Information
	   	$sql = "SELECT u.username
                      	FROM " . GARAGE_TABLE . " g  ,  " . USERS_TABLE ." u
                    	WHERE g.id = $cid and g.user_id = u.user_id
	                GROUP BY g.id";

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Get Vehicle Data', '', __LINE__, __FILE__, $sql);
      		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row['username'];
	}

	/*========================================================================*/
	// Select Vehicle Owner ID From Db
	// Usage: get_vehicle_owner_id('vehicle id');
	/*========================================================================*/
	function get_vehicle_owner_id($cid)
	{
		global $db;
		//Select All Vehicle Information
	   	$sql = "SELECT u.user_id
                      	FROM " . GARAGE_TABLE . " g  ,  " . USERS_TABLE ." u
                    	WHERE g.id = $cid and g.user_id = u.user_id
	                GROUP BY g.id";

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Get Vehicle Data', '', __LINE__, __FILE__, $sql);
      		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row['user_id'];
	}


	/*========================================================================*/
	// Select All Vehicles From User Data From Db
	// Usage: get_vehicles_by_user('user id');
	/*========================================================================*/
	function get_vehicles_by_user($user_id)
	{
		global $db;
		//Select All Vehicle Information
	   	$sql = "SELECT g.*, ROUND(g.weighted_rating, 2) as weighted_rating, images.*, makes.make, models.model, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, count(mods.id) AS total_mods, ( SUM(mods.price) + SUM(mods.install_price) ) AS total_spent, user.username, user.user_avatar_type, user.user_avatar, user.user_id
                      	FROM " . GARAGE_TABLE . " AS g  
				LEFT JOIN " . USERS_TABLE ." AS user ON g.user_id = user.user_id
	                       	LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
        	                LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
				LEFT JOIN " . GARAGE_MODS_TABLE . " AS mods ON g.id = mods.garage_id
				LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = g.image_id
                    	WHERE g.user_id = $user_id
			GROUP BY g.id";

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Get Vehicle Data', '', __LINE__, __FILE__, $sql);
      		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}
		$db->sql_freeresult($result);

		if (empty($rows))
		{
			return;
		}

		return $rows;
	}

	/*========================================================================*/
	// Selects Rating Information For Vehicle
	// Usage: get_vehicle_rating('vehicle id');
	/*========================================================================*/
	function get_vehicle_rating($cid)
	{
		global $db;

		$sql = "SELECT r.*, u.username
        		FROM " . GARAGE_RATING_TABLE . " r
                    		LEFT JOIN " . GARAGE_TABLE . " g ON r.garage_id = g.id
				LEFT JOIN " . USERS_TABLE . " u ON r.user_id = u.user_id
			WHERE r.garage_id ='$cid'";

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Get Vehicle Rating Data', '', __LINE__, __FILE__, $sql);
      		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}
		$db->sql_freeresult($result);

		return $rows;
	}

	/*========================================================================*/
	// Selects Lastest Updated Vehicle
	// Usage: get_latest_updatest_vehicles('No. To Return');
	/*========================================================================*/
	function get_latest_updated_vehicles($vehicles_required)
	{
		global $db;

		$sql = "SELECT g.id, g.made_year, g.user_id, g.date_updated, user.username, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle
  			FROM " . GARAGE_TABLE . " AS g 
        			LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
        			LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id 
				LEFT JOIN " . USERS_TABLE . " AS user ON g.user_id = user.user_id 
			WHERE makes.pending = 0 AND models.pending = 0 
	        	ORDER BY g.date_updated DESC
			LIMIT 0, " . $vehicles_required;

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Get Vehicle Rating Data', '', __LINE__, __FILE__, $sql);
      		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}
		$db->sql_freeresult($result);

		if (empty($rows))
		{
			return;
		}

		return $rows;
	}

	/*========================================================================*/
	// Select A Users Main Vehicle Data From Db
	// Usage: get_user_main_vehicle('user id');
	/*========================================================================*/
	function get_user_main_vehicle($user_id)
	{
		global $db;

	   	$sql = "SELECT g.*, images.*, makes.make, models.model, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, count(mods.id) AS total_mods, ( SUM(mods.price) + SUM(mods.install_price) ) AS total_spent, user.username, user.user_avatar_type, user.user_avatar, user.user_id
                      	FROM " . GARAGE_TABLE . " AS g  
				LEFT JOIN " . USERS_TABLE ." AS user ON g.user_id = user.user_id
	                       	LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
        	                LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
				LEFT JOIN " . GARAGE_MODS_TABLE . " AS mods ON g.id = mods.garage_id
				LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = g.image_id
                    	WHERE g.user_id = $user_id and g.main_vehicle =1
	                GROUP BY g.id";

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Get Vehicle Data', '', __LINE__, __FILE__, $sql);
      		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row;
	}

	/*========================================================================*/
	// Integrates phpBB Garage & phpBB User Profiles
	// Usage: profile_integration('user_id');
	/*========================================================================*/
	function profile_integration($user_id)
	{
		global $images, $template, $profiledata, $lang, $phpEx;

		//Get Vehicle Data
		$vehicle_data = $this->get_user_main_vehicle($user_id);

		if ( count($vehicle_data) > 0 )
		{
			$template->assign_block_vars('garage_vehicle', array());
			$total_spent = $vehicle_data['total_spent'] ? $vehicle_data['total_spent'] : 0;

			//Display Just Thumbnails Of All Images Or Just One Main Image
			if ( $garage_config['profile_thumbs'] == 1 )
			{

				//Build List Of Gallery Images For Vehicle
				$gallery_data = $garage_image->get_gallery($vehicle_data['id']);
        			for ( $i=0; $i < count($gallery_data); $i++ )
	       			{
		            		if ( $gallery_data[$i]['attach_is_image'] )
           				{
                				// Do we have a thumbnail?  If so, our job is simple here :)
						if ( (empty($gallery_data[$i]['attach_thumb_location']) == false) AND ($gallery_data[$i]['attach_thumb_location'] != $gallery_data[$i]['attach_location']) AND ( $vehicle_images_found <= 12) )
                				{
                    					// Form the image link
							$thumb_image = GARAGE_UPLOAD_PATH . $gallery_data[$i]['attach_thumb_location'];
							$id = $gallery_data[$i]['attach_id'];
							$title = $gallery_data[$i]['attach_file'];
							$hilite_image .= '<a href=garage.'.$phpEx.'?mode=view_gallery_item&amp;type=garage_gallery&amp;image_id='. $id .' title=' . $title .' target="_blank"><img hspace="5" vspace="5" src="' . $thumb_image .'" class="attach"  /></a> ';
               					} 
					}
				}

				//Build List Of Modification Images For Vehicle
				$mod_data = $garage_modification->get_modifications_by_vehicle($vehicle_data['id']);
        			for ( $i=0; $i < count($mod_data); $i++ )
			       	{
            				if ( $mod_data[$i]['attach_is_image'] )
		           		{
                				// Do we have a thumbnail?  If so, our job is simple here :)
						if ( (empty($mod_data[$i]['attach_thumb_location']) == false) AND ($mod_data[$i]['attach_thumb_location'] != $mod_data[$i]['attach_location']) AND ( $vehicle_images_found <= 12) )
		                		{
                		    			// Form the image link
							$thumb_image = GARAGE_UPLOAD_PATH . $mod_data[$i]['attach_thumb_location'];
							$id = $mod_data[$i]['attach_id'];
							$title = $mod_data[$i]['attach_file'];
							$hilite_image .= '<a href=garage.'.$phpEx.'?mode=view_gallery_item&amp;type=garage_gallery&amp;image_id='. $id .' title=' . $title .' target="_blank"><img hspace="5" vspace="5" src="' . $thumb_image .'" class="attach"  /></a> ';
		               			} 
					}
			        }
			}
			//Looks Like We Only Need To Draw One Main Image
			else
			{
				if ( ($vehicle_data['image_id']) AND ($vehicle_data['attach_is_image']) AND (!empty($vehicle_data['attach_thumb_location'])) AND (!empty($vehicle_data['attach_location'])) )
				{
					// Check to see if this is a remote image
					if ( preg_match( "/^http:\/\//i", $vehicle_data['attach_location']) )
					{
						$image = $vehicle_data['attach_location'];
						$id = $vehicle_data['attach_id'];
						$title = $vehicle_data['attach_file'];
						$total_image_views = $vehicle_data['attach_hits'];
						$hilite_image = '<a href=garage.'.$phpEx.'?mode=view_gallery_item&amp;type=garage_mod&amp;image_id='. $id .' title=' . $title .' target="_blank"><img hspace="5" vspace="5" src="' . $image .'" class="attach"  /></a>';
					}
					else
					{
						$image = GARAGE_UPLOAD_PATH . $vehicle_data['attach_location'];
						$id = $vehicle_data['attach_id'];
						$title = $vehicle_data['attach_file'];
						$total_image_views = $vehicle_data['attach_hits'];
						$hilite_image = '<a href=garage.'.$phpEx.'?mode=view_gallery_item&amp;type=garage_mod&amp;image_id='. $id .' title=' . $title .' target="_blank"><img hspace="5" vspace="5" src="' . $image .'" class="attach"  /></a>';
					}
				}
			}

			$garage_img ='<a href="' . append_sid("garage.$phpEx?mode=browse&search=yes&user=".urlencode($profiledata['username'])."") . '"><img src="' . $images['icon_garage'] . '" alt="'.$lang['Garage'].'" title="'.$lang['Garage'].'" border="0" /></a>';

			$template->assign_vars(array(
				'L_VEHICLE' => $lang['Vehicle'],
				'L_GARAGE' => $lang['Garage'],
				'L_COLOUR' => $lang['Colour'],
				'L_MILEAGE' => $lang['Mileage'],
				'L_PRICE' => $lang['Purchased_Price'],
				'L_TOTAL_MODS' => $lang['Total_Mods'],
				'L_TOTAL_SPENT' => $lang['Total_Spent'],
				'L_DESCRIPTION' => $lang['Description'],
				'L_SEARCH_USER_GARAGE' => $lang['Search_User_Garage'],
				'YEAR' => $vehicle_data['year'],
				'MAKE' => $vehicle_data['make'],
				'MODEL' => $vehicle_data['model'],
		       		'COLOUR' => $vehicle_data['colour'],
			       	'HILITE_IMAGE' => $hilite_image,
		        	'MILEAGE' => $vehicle_data['mileage'],
			        'MILEAGE_UNITS' => $vehicle_data['mileage_unit'],
		        	'PRICE' => $vehicle_data['price'],
			        'CURRENCY' => $vehicle_data['currency'],
		        	'TOTAL_MODS' => $vehicle_data['total_mods'],
			        'TOTAL_SPENT' => $total_spent,
		        	'TOTAL_VIEWS' => $vehicle_data['views'],
			        'DESCRIPTION' => $vehicle_data['comments'],
			        'GARAGE_IMG' => $garage_img,
				'U_SEARCH_USER_GARAGE' => append_sid("garage.$phpEx?mode=browse"))
			);

		}
	}
}

$garage_vehicle = new garage_vehicle();


?>

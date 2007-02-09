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
			$groups = group_memberships(false, array($user->data['user_id']), false);
			
			//Lets Get The Private Upload Groups & Quotas
			$private_add_groups = @explode(',', $garage_config['private_add_perms']);
			$private_add_quotas = @explode(',', $garage_config['private_add_quota']);

			//Process All Groups You Are Member Of To See If Any Are Granted Permission & Quota
			for ($i = 0; $i < count($groups); $i++)
			{
				if (in_array($groups[$i]['group_id'], $private_add_groups))
				{
					//Your A Member Of A Group Granted Permission - Find Array Key
					$index = array_search($groups[$i]['group_id'], $private_add_groups);
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
	function get_group_vehicle_quota($gid)
	{
		global $db, $garage_config;

		if (empty($garage_config['add_groups']))
		{
			return;
		}
		//It Appears Some Groups Have Private Permissions & Quotas We Will Need To Check Them
		else
		{
			//Lets Get The Add Groups & Quotas
			$add_groups = @explode(',', $garage_config['add_groups']);
			$add_quota = @explode(',', $garage_config['add_groups_quotas']);

			//Find The Matching Index In Second Array For The Group ID
			if (($index = array_search($gid, $add_groups)) === false)
			{
				return;
			} 
			
			//Return The Groups Quota
			return $add_quota[$index];
		}
	}

	/*========================================================================*/
	// Inserts Vehicle Into DB
	// Usage: insert_vehicle(array());
	/*========================================================================*/
	function insert_vehicle($data)
	{
		global $user, $db, $garage_config;

		$sql = 'INSERT INTO ' . GARAGE_VEHICLES_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'made_year'		=> $data['made_year'],
			'engine_type'		=> $data['engine_type'],
			'make_id'		=> $data['make_id'],
			'model_id'		=> $data['model_id'],
			'colour'		=> $data['colour'],
			'mileage'		=> $data['mileage'],
			'mileage_unit'		=> $data['mileage_units'],
			'price'			=> $data['price'],
			'currency'		=> $data['currency'],
			'comments'		=> $data['comments'],
			'user_id'		=> $user->data['user_id'],
			'date_created'		=> time(),
			'date_updated'		=> time(),
			'main_vehicle'		=> $data['main_vehicle'],
			'pending'		=> ($garage_config['enable_vehicle_approval']) ? 1 : 0 )
		);

		$db->sql_query($sql);
	
		return $db->sql_nextid();
	}

	/*========================================================================*/
	// Insurance Vehicle Rating Into DB
	// Usage: insert_vehicel_rating(array());
	/*========================================================================*/
	function insert_vehicle_rating($data)
	{
		global $cid, $db;

		$sql = 'INSERT INTO ' . GARAGE_RATINGS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'vehicle_id'		=> $cid,
			'rating'		=> $data['vehicle_rating'],
			'user_id'		=> $data['user_id'],
			'rate_date'		=> time())
		);

		$db->sql_query($sql);

		return;
	}

	/*========================================================================*/
	// Returns Count Of Users Vehicles
	// Usage: count_user_vehicles();
	/*========================================================================*/
	function count_user_vehicles()
	{
		global $user, $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(g.id) as total',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
			),
			'WHERE'		=> 'g.user_id = ' . $user->data['user_id']
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$data['total'] = (empty($data['total'])) ? 0 : $data['total'];
		return $data['total'];
	}

	/*========================================================================*/
	// Returns Count Of Users Vehicles
	// Usage: count_user_vehicles();
	/*========================================================================*/
	function get_user_vehicle_rating($cid)
	{
		global $user, $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(*) as total, r.rate_date',
			'FROM'		=> array(
				GARAGE_RATINGS_TABLE	=> 'r',
			),
			'WHERE'		=> "r.user_id = " . $user->data['user_id'] ." AND vehicle_id = $cid",
			'GROUP_BY'	=> 'r.id'
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Updates Vehicle In DB
	// Usage: update_vehicle(array());
	/*========================================================================*/
	function update_vehicle($data)
	{
		global $cid, $db, $garage_config, $user;

		$update_sql = array(
			'made_year'		=> $data['made_year'],
			'engine_type'		=> $data['engine_type'],
			'make_id'		=> $data['make_id'],
			'model_id'		=> $data['model_id'],
			'colour'		=> $data['colour'],
			'mileage'		=> $data['mileage'],
			'mileage_unit'		=> $data['mileage_units'],
			'price'			=> $data['price'],
			'currency'		=> $data['currency'],
			'comments'		=> $data['comments'],
			'user_id'		=> $this->get_vehicle_owner_id($cid),
			'date_updated'		=> time(),
			'pending'		=> ($garage_config['enable_vehicle_approval']) ? 1 : 0
		);

		$sql = 'UPDATE ' . GARAGE_VEHICLES_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = $cid";

		$db->sql_query($sql);

		return;
	}

	/*========================================================================*/
	// Updates Vehicle Rating In DB
	// Usage: update_vehicle_rating(array());
	/*========================================================================*/
	function update_vehicle_rating($data)
	{
		global $db, $cid;

		$update_sql = array(
			'rating'	=> $data['vehicle_rating'],
			'rate_date'	=> time() 
		);

		$sql = 'UPDATE ' . GARAGE_RATINGS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE user_id = " . $data['user_id'] . " AND vehicle_id = $cid";

		$result = $db->sql_query($sql);

		return;
	}

	/*========================================================================*/
	// Resets All Vehicles Rating To 0
	// Usage: reset_all_vehicles_rating();
	/*========================================================================*/
	function reset_all_vehicles_rating()
	{
		global $db;

		$sql = "DELETE FROM " . GARAGE_RATINGS_TABLE;
	
		$db->sql_query($sql);

		$update_sql = array(
			'weighted_rating'	=> '0'
		);

		$sql = 'UPDATE ' . GARAGE_VEHICLES_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql);

		$db->sql_query($sql);

		return;
	}

	/*========================================================================*/
	// Calculates A Vehicles Weighted Rating
	// Usage: update_vehicle_rating('vehicle id');
	/*========================================================================*/
	function calculate_weighted_rating($cid)
	{
		global $db, $garage_config;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(g.id) AS votes_recieved, AVG(rating) AS average_rating',
			'FROM'		=> array(
				GARAGE_RATINGS_TABLE	=> 'g',
			),
			'WHERE'		=> "g.id = $cid"
		));

		$result = $db->sql_query($sql);
	        $row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'AVG(rating) AS site_average',
			'FROM'		=> array(
				GARAGE_RATINGS_TABLE	=> 'g',
			)
		));

		$result = $db->sql_query($sql);
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

		$update_sql = array(
			'weighted_rating'	=> $weighted_rating
		);

		$sql = 'UPDATE ' . GARAGE_VEHICLES_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = $cid";

		$db->sql_query($sql);

		return;
	}

	/*========================================================================*/
	// Count The Total Vehicles Within The Garage
	// Usage: count_total_vehciles();
	/*========================================================================*/
	function count_total_vehicles()
	{
		global $db;

		$data = null;

		// Get the total count of vehicles and views in the garage
		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(g.id) as total',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
			)
		));

		$result = $db->sql_query($sql);
	        $data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$data['total'] = (empty($data['total'])) ? 0 : $data['total'];
		return $data['total'];
	}

	/*========================================================================*/
	// Determines If Image Is Hilite Image
	// Usage: hilite_exists('vehicle id');
	/*========================================================================*/
	function hilite_exists($cid)
	{
		$hilite = 1;

		if ($this->count_vehicle_images($cid) > 0)
		{
			$hilite = 0;
		}
	
		return $hilite;
	}

	/*========================================================================*/
	// Returns Count Of Vehicle Images
	// Usage: count_vehicle_images('vehicle id');
	/*========================================================================*/
	function count_vehicle_images($cid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(vg.id) as total',
			'FROM'		=> array(
				GARAGE_VEHICLE_GALLERY_TABLE	=> 'vg',
			),
			'WHERE'		=> "vg.vehicle_id = $cid"
		));

		$result = $db->sql_query($sql);
	        $data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$data['total'] = (empty($data['total'])) ? 0 : $data['total'];
		return $data['total'];
	}

	/*========================================================================*/
	// Check A User Owns The Vehicle, If Not Display Message
	// Usage: check_ownership('vehicle id');
	/*========================================================================*/
	function check_ownership($cid)
	{
		global $user, $auth;

	 	if ($auth->acl_get('m_garage'))
		{
			return;
		}

		if ($this->get_vehicle_owner_id($cid) != $user->data['user_id'] )
		{
			trigger_error('NOT_VEHICLE_OWNER');
		}

		return;
	}
	
	/*========================================================================*/
	// Update Vehicle Modified Time
	// Usage: update_vehicle_time('vehicle id');
	/*========================================================================*/
	function update_vehicle_time($cid)
	{
		global $garage;
		
		$garage->update_single_field(GARAGE_VEHICLES_TABLE, 'date_updated', time(), 'id', $cid);
	
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

			//Start To Build SQl For Selecting Featured Vehicle..We Will Extend This Array Based On User Options
			$sql_array = array(
				'SELECT'	=> 'g.id, g.made_year, vg.image_id, g.user_id, makes.make, models.model, images.attach_id, images.attach_hits, images.attach_thumb_location, u.username, images.attach_is_image, images.attach_location, COUNT(mods.id) AS mod_count, CONCAT_WS(\' \', g.made_year, makes.make, models.model) AS vehicle, images.attach_file, (SUM(mods.install_price) + SUM(mods.price)) AS money_spent, sum( r.rating ) AS rating',
				'FROM'		=> array(
					GARAGE_VEHICLES_TABLE	=> 'g',
				),
				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(GARAGE_MAKES_TABLE => 'makes'),
						'ON'	=> 'g.make_id = makes.id',
					)
					,array(
						'FROM'	=> array(GARAGE_VEHICLE_GALLERY_TABLE => 'vg'),
						'ON'	=> 'g.id = vg.vehicle_id AND vg.hilite = 1',
					)
					,array(
						'FROM'	=> array(GARAGE_IMAGES_TABLE => 'images'),
						'ON'	=> 'images.attach_id = vg.image_id',
					)
					,array(
						'FROM'	=> array(GARAGE_MODELS_TABLE => 'models'),
						'ON'	=> 'g.model_id = models.id ',
					)
					,array(
						'FROM'	=> array(GARAGE_MODIFICATIONS_TABLE => 'mods'),
						'ON'	=> 'g.id = mods.vehicle_id ',
					)
					,array(
						'FROM'	=> array(GARAGE_RATINGS_TABLE => 'r'),
						'ON'	=> 'g.id = r.vehicle_id ',
					)
					,array(
						'FROM'	=> array(USERS_TABLE => 'u'),
						'ON'	=> 'g.user_id = u.user_id',
					)
				)
			);

			//If we are using random, go fetch!
			$featured_vehicle_id = null;
			$total_vehicles = null;
	       		if ( $garage_config['featured_vehicle_random'] == 'on' )
			{
				$sql = $db->sql_build_query('SELECT', 
				array(
					'SELECT'	=> 'g.id',
					'FROM'		=> array(
						GARAGE_VEHICLES_TABLE	=> 'g',
					),
					'LEFT_JOIN'	=> array(
						array(
							'FROM'	=> array(GARAGE_MAKES_TABLE => 'md'),
							'ON'	=> 'g.make_id = mk.id'
						)
						,array(
							'FROM'	=> array(GARAGE_MODELS_TABLE => 'mk'),
							'ON'	=> 'g.model_id = md.id'
						)
						,array(
							'FROM'	=> array(GARAGE_VEHICLE_GALLERY_TABLE => 'vg'),
						'ON'	=> 'g.id = vg.vehicle_id AND vg.hilite = 1',
						)
					),
					'WHERE'		=> "makes.pending = 0 and models.pending = 0 and vg.image_id IS NOT NULL",
					'ORDER_BY'	=> "rand()"
				));

    				$result = $db->sql_query_limit($sql, 1);
				$vehicle_data = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				//Update SQL Array With Required Statements
				$featured_vehicle_id = $vehicle_data['id'];
				$sql_array['WHERE'] = "g.id =" . $vehicle_data['id'];
				$sql_array['GROUP_BY'] = "g.id";
				$sql_array['ORDER_BY'] = "g.id ASC";
	 	 	}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Newest_Vehicles'] )
			{
				$sql_array['WHERE'] = "makes.pending = 0 and models.pending = 0";
				$sql_array['GROUP_BY'] = "g.id";
				$sql_array['ORDER_BY'] = "g.date_created DESC";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Last_Updated_Vehicles'] )
			{
				$sql_array['WHERE'] = "makes.pending = 0 and models.pending = 0";
				$sql_array['GROUP_BY'] = "g.id";
				$sql_array['ORDER_BY'] = "g.date_updated DESC";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Newest_Modifications'] )
			{
				$sql_array['WHERE'] = "makes.pending = 0 and models.pending = 0";
				$sql_array['GROUP_BY'] = "g.id";
				$sql_array['ORDER_BY'] = "mods.date_created DESC";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Last_Updated_Modifications'] )
			{
				$sql_array['WHERE'] = "makes.pending = 0 and models.pending = 0";
				$sql_array['GROUP_BY'] = "g.id";
				$sql_array['ORDER_BY'] = "mods.date_updated";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Most_Modified_Vehicle'] )
			{
				$sql_array['WHERE'] = "makes.pending = 0 and models.pending = 0";
				$sql_array['GROUP_BY'] = "g.id";
				$sql_array['ORDER_BY'] = "mod_count DESC";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Most_Money_Spent'] )
			{
				$sql_array['WHERE'] = "makes.pending = 0 and models.pending = 0";
				$sql_array['GROUP_BY'] = "g.id";
				$sql_array['ORDER_BY'] = "money_spent DESC";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Most_Viewed_Vehicle'] )
			{
				$sql_array['WHERE'] = "makes.pending = 0 and models.pending = 0";
				$sql_array['GROUP_BY'] = "g.id";
				$sql_array['ORDER_BY'] = "g.views DESC";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Latest_Vehicle_Comments'] )
			{
				$sql_array['LEFT_JOIN'] .= array(array(
								'FROM'	=> array(GARAGE_GUESTBOOKS_TABLE => 'gb'),	
								'ON'	=> 'g.id = gb.vehicle_id'
							));
				$sql_array['WHERE'] = "makes.pending = 0 and models.pending = 0";
				$sql_array['GROUP_BY'] = "g.id";
				$sql_array['ORDER_BY'] = "gb.post_date DESC";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Top_Quartermile_Runs'] )
			{
				$sql_array['LEFT_JOIN'] .= array(array(
								'FROM'	=> array(GARAGE_QUARTERMILES_TABLE => 'qm'),	
								'ON'	=> 'g.id = qm.vehicle_id'
							));
				$sql_array['WHERE'] = "makes.pending = 0 and models.pending = 0";
				$sql_array['GROUP_BY'] = "g.id";
				$sql_array['ORDER_BY'] = "qm.quart ASC";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Top_Dyno_Runs'] )
			{
				$sql_array['LEFT_JOIN'] .= array(array(
								'FROM'	=> array(GARAGE_DYNORUNS_TABLE => 'rr'),	
								'ON'	=> 'g.id = rr.vehicle_id'
							));
				$sql_array['WHERE'] = "makes.pending = 0 and models.pending = 0";
				$sql_array['GROUP_BY'] = "g.id";
				$sql_array['ORDER_BY'] = "rr.bhp DESC";
			}
			else if ( $garage_config['featured_vehicle_from_block'] == $lang['Top_Rated_Vehicles'] )
			{
				$sql_array['WHERE'] = "makes.pending = 0 and models.pending = 0";
				$sql_array['GROUP_BY'] = "g.id";
				$sql_array['ORDER_BY'] = "rating DESC";
			}
			else
			{
				$featured_vehicle_id = $garage_config['featured_vehicle_id'];
				//Make sure the vehicle exists if entered in ACP..
				$sql = $db->sql_build_query('SELECT', 
					array(
					'SELECT'	=> 'COUNT(g.id) as num_vehicle',
					'FROM'		=> array(
						GARAGE_VEHICLES_TABLE	=> 'g',
					),
					'WHERE'		=> "id = ". $featured_vehicle_id,
				));
				$result = $db->sql_query($sql);
				$total_vehicles = (int) $db->sql_fetchfield('num_vehicle');
				$db->sql_freeresult($result);

				$sql_array['WHERE'] = "g.id = " . $garage_config['featured_vehicle_id'];
				$sql_array['GROUP_BY'] = "g.id";
				$sql_array['ORDER_BY'] = "g.id DESC";
			}

		        if ( $total_vehicles > 0 OR (!empty($garage_config['featured_vehicle_from_block'])) )
	        	{
				//Build Complete SQL Statement Now With All Options
				$sql = $db->sql_build_query('SELECT', array(
					'SELECT'	=> $sql_array['SELECT'],
					'FROM'		=> $sql_array['FROM'],
					'LEFT_JOIN'	=> $sql_array['LEFT_JOIN'],
					'WHERE'		=> $sql_array['WHERE'],
					'GROUP_BY'	=> $sql_array['GROUP_BY'],
					'ORDER_BY'	=> $sql_array['ORDER_BY'],
				));
	
				$result = $db->sql_query_limit($sql, 1);
	        	    	$vehicle_data = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
	
				$thumb_image = null;
				// Do we have a hilite image?  If so, prep the HTML
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
	                		} 
	        		}
				$template->assign_vars(array(
					'FEATURED_DESCRIPTION' 	=> $garage_config['featured_vehicle_description'],
					'FEATURED_THUMB' 	=> $thumb_image,
					'VEHICLE' 		=> $vehicle_data['vehicle'],
					'USERNAME' 		=> $vehicle_data['username'],
					'IMAGE_TITLE'		=> $vehicle_data['attach_file'],
					'U_VIEW_IMAGE'		=> append_sid($absolute_url."garage.$phpEx?mode=view_image&amp;image_id=".$vehicle_data['attach_id']),
					'U_VIEW_VEHICLE' 	=> append_sid($absolute_url."garage_vehicle.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
					'U_VIEW_PROFILE' 	=> append_sid("memberlist.$phpEx?mode=viewprofile&amp;u=".$vehicle_data['user_id']))
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
			'BLOCK_TITLE' 	=> $user->lang['LAST_UPDATED_VEHICLES'],
			'COLUMN_1_TITLE'=> $user->lang['VEHICLE'],
			'COLUMN_2_TITLE'=> $user->lang['OWNER'],
			'COLUMN_3_TITLE'=> $user->lang['UPDATED'])
		);
	 		
		$limit = $garage_config['updated_vehicle_limit'] ? $garage_config['updated_vehicle_limit'] : 10;

		$vehicle_data = $this->get_latest_updated_vehicles($limit);
	
		for ($i = 0; $i < count($vehicle_data); $i++)
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' 	=> append_sid("garage_vehicle.$phpEx", "mode=view_vehicle&CID=" . $vehicle_data[$i]['id'], true),
				'U_COLUMN_2' 	=> append_sid("memberlist.$phpEx", "mode=viewprofile&u=" . $vehicle_data[$i]['user_id'], true),
				'COLUMN_1_TITLE'=> $vehicle_data[$i]['vehicle'],
				'COLUMN_2_TITLE'=> $vehicle_data[$i]['username'],
				'COLUMN_3_TITLE'=> $user->format_date($vehicle_data[$i]['date_updated']))
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
			'BLOCK_TITLE' 	=> $user->lang['MOST_MONEY_SPENT'],
			'COLUMN_1_TITLE'=> $user->lang['VEHICLE'],
			'COLUMN_2_TITLE'=> $user->lang['OWNER'],
			'COLUMN_3_TITLE'=> $user->lang['TOTAL_SPENT'])
		);
	 		
		$limit = $garage_config['most_spent_limit'] ? $garage_config['most_spent_limit'] : 10;

		$vehicle_data = $this->get_most_spent_vehicles($limit);

		for ($i = 0; $i < count($vehicle_data); $i++)
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' 	=> append_sid("garage_vehicle.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data[$i]['id']),
				'U_COLUMN_2' 	=> append_sid("memberlist.$phpEx?mode=viewprofile&amp;u=".$vehicle_data[$i]['user_id']),
				'COLUMN_1_TITLE'=> $vehicle_data[$i]['vehicle'],
				'COLUMN_2_TITLE'=> $vehicle_data[$i]['username'],
				'COLUMN_3_TITLE'=> $vehicle_data[$i]['POI'])
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
			'BLOCK_TITLE'	=> $user->lang['MOST_VIEWED_VEHICLE'],
			'COLUMN_1_TITLE'=> $user->lang['VEHICLE'],
			'COLUMN_2_TITLE'=> $user->lang['OWNER'],
			'COLUMN_3_TITLE'=> $user->lang['VIEWS'])
		);
	
	        $limit = $garage_config['most_viewed_limit'] ? $garage_config['most_viewed_limit'] : 10;
	 		 		
		$vehicle_data = $this->get_most_viewed_vehicles($limit);

		for ($i = 0; $i < count($vehicle_data); $i++)
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' 	=> append_sid("garage_vehicle.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data[$i]['id']),
				'U_COLUMN_2' 	=> append_sid("memberlist.$phpEx?mode=viewprofile&amp;u=".$vehicle_data[$i]['user_id']),
				'COLUMN_1_TITLE'=> $vehicle_data[$i]['vehicle'],
				'COLUMN_2_TITLE'=> $vehicle_data[$i]['username'],
				'COLUMN_3_TITLE'=> $vehicle_data[$i]['POI'])
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
			'BLOCK_TITLE' 	=> $user->lang['TOP_RATED_VEHICLES'],
			'COLUMN_1_TITLE'=> $user->lang['VEHICLE'],
			'COLUMN_2_TITLE'=> $user->lang['OWNER'],
			'COLUMN_3_TITLE'=> $user->lang['RATING'])
		);
	
	        $limit = $garage_config['top_rating_limit'] ? $garage_config['top_rating_limit'] : 10;
	
		$vehicle_data = $this->get_top_rated_vehicles($limit);

		for ($i = 0; $i < count($vehicle_data); $i++)
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' 	=> append_sid("garage_vehicle.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data[$i]['id']),
				'U_COLUMN_2' 	=> append_sid("memberlist.$phpEx?mode=viewprofile&amp;u=".$vehicle_data[$i]['user_id']),
				'COLUMN_1_TITLE'=> $vehicle_data[$i]['vehicle'],
				'COLUMN_2_TITLE'=> $vehicle_data[$i]['username'],
				'COLUMN_3_TITLE'=> $vehicle_data[$i]['weighted_rating'] . '/' . '10')
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
			'BLOCK_TITLE' 	=> $user->lang['NEWEST_VEHICLES'],
			'COLUMN_1_TITLE'=> $user->lang['VEHICLE'],
			'COLUMN_2_TITLE'=> $user->lang['OWNER'],
			'COLUMN_3_TITLE'=> $user->lang['CREATED'])
		);
	 		
	        $limit = $garage_config['newest_vehicle_limit'] ? $garage_config['newest_vehicle_limit'] : 10;
	 		 		
		$vehicle_data = $this->get_newest_vehicles($limit);

		for ($i = 0; $i < count($vehicle_data); $i++)
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' 	=> append_sid("garage_vehicle.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data[$i]['id']),
				'U_COLUMN_2' 	=> append_sid("memberlist.$phpEx?mode=viewprofile&amp;u=".$vehicle_data[$i]['user_id']),
				'COLUMN_1_TITLE'=> $vehicle_data[$i]['vehicle'],
				'COLUMN_2_TITLE'=> $vehicle_data[$i]['username'],
				'COLUMN_3_TITLE'=> $user->format_date($vehicle_data[$i]['POI']))
			);
	 	}
	
		$required_position++;
		return ;
	}
	
	/*========================================================================*/
	// Delete A Vehicle From The Garage Including All Related Data
	// Usage: delete_vehicle('vehicle id');
	/*========================================================================*/
	function delete_vehicle($id)
	{
		global $garage, $garage_modification, $garage_quartermile, $garage_dynorun, $garage_insurance, $garage_guestbook, $garage_vehicle, $garage_image;

		$modifications	= $garage_modification->get_modifications_by_vehicle($id);
		$quartermiles	= $garage_quartermile->get_quartermiles_by_vehicle($id);
		$dynoruns 	= $garage_dynorun->get_dynoruns_by_vehicle($id);
		$premiums 	= $garage_insurance->get_premiums_by_vehicle($id);
		$comments 	= $garage_guestbook->get_comments_by_vehicle($id);
		$ratings	= $garage_vehicle->get_ratings_by_vehicle($id);
		$images		= $garagr_image->get_images_by_vehicle($id);
	
		for ($i = 0, $count = sizeof($modifications);$i < $count; $i++)
		{
			$garage_modification->delete_modification($modifications[$i]['id']);
		}
	
		for ($i = 0, $count = sizeof($quartermiles);$i < $count; $i++)
		{
			$garage_quartermile->delete_quartermile($quartermiles[$i]['id']);
		}
	
		for ($i = 0, $count = sizeof($dynoruns);$i < $count; $i++)
		{
			$garage_dynorun->delete_dynorun($dynoruns[$i]['id']);
		}
	
		for ($i = 0, $count = sizeof($premiums);$i < $count; $i++)
		{
			$garage_insurance->delete_premium($premiums[$i]['id']);
		}

		for ($i = 0, $count = sizeof($comments);$i < $count; $i++)
		{
			$garage_guestbook->delete_comment($comments[$i]['id']);
		}

		for ($i = 0, $count = sizeof($ratings);$i < $count; $i++)
		{
   			$garage_vehicle->delete_rating($ratings[$i]['id']);
		} 
	
		for ($i = 0, $count = sizeof($images);$i < $count; $i++)
		{
			$garage_image->delete_image($images[$i]['id']);
		}
	
		$garage->delete_rows(GARAGE_VEHICLES_TABLE, 'id', $id);
	
		return;
	}

	/*========================================================================*/
	// Display Vehicle Page - With Or Without Management Links & Galleries
	// Usage:  display_vehicle('YES|NO|MODERATE');
	/*========================================================================*/
	function display_vehicle($owned)
	{
		global $user, $template, $images, $phpEx, $phpbb_root_path, $garage_config, $config, $cid, $mode, $garage, $garage_template, $garage_modification, $garage_insurance, $garage_quartermile, $garage_dynorun, $garage_image, $auth, $garage_guestbook, $garage_track, $garage_service, $garage_blog, $HTTP_SERVER_VARS;;

		if ($owned == 'YES' || $owned == 'MODERATE')
		{
			$this->check_ownership($cid);
		}

		//Setup Variables
		$vehicle_images_found	= 0;	
		$mod_images_found 	= 0;
		$mod_images_displayed 	= null;
		$lowest_tab		= array();

		//Get Vehicle Information	
		$vehicle = $this->get_vehicle($cid);

		$vehicle['avatar'] 	= null;
		if ($owned == 'NO' && $vehicle['user_avatar_type'])
		{
			//Build The Owners Avatar Image If Any...
			if ($vehicle['user_avatar'] AND $user->optionget('viewavatars'))
			{
				switch( $vehicle['user_avatar_type'] )
				{
					case AVATAR_UPLOAD:
						$avatar_img = $config['avatar_path'] . '/' . $vehicle['user_avatar'];
					break;
	
					case AVATAR_GALLERY:
						$avatar_img = $config['avatar_gallery_path'] . '/' . $vehicle['user_avatar'];
					break;
				}
				$vehicle['avatar'] = '<img src="' . $avatar_img . '" width="' . $vehicle['user_avatar_width'] . '" height="' . $vehicle['user_avatar_height'] . '" alt="" />';
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
					'U_PROFILE'	=> append_sid("memberlist.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=".$rating_data[$i]['user_id']) ,
					'USERNAME' 	=> $rating_data[$i]['username'] ,
					'RATING' 	=> $rating_data[$i]['rating'],
					'DATE' 		=> create_date('D M d, Y G:i', $rating_data[$i]['rate_date'], $board_config['board_timezone']),
					'DELETE_RATING_LINK' => $delete_rating_link)
				);
			}	
		}

		//Display Ratings
		if ( $owned == 'NO' )
		{
			//Get Rating Given By User To Vehicle
			$rating = $this->get_user_vehicle_rating($cid);

			//Never Rate So Show Them The Rate Button
			if ( $rating['total'] < 1 )
			{
			 	$garage_template->rating_dropdown('rating');
				$template->assign_vars(array(
					'S_DISPLAY_RATE'	=> true,
					'L_RATING_NOTICE' 	=> '')
				);
			}
			//Rated Already But Permanent So Do Not Show Button
			else if ( ( $rating['total'] > 0 ) AND ($garage_config['rating_permanent']) )
			{
				$template->assign_vars(array(
					'L_RATING_NOTICE'	=> $user->lang['RATE_PERMANENT'])
				);
			}
			//Rated Already But Not Permanent & Always Updateable
			else if ( ( $rating['total'] > 0 ) AND (!$garage_config['rating_permanent']) AND ($garage_config['rating_always_updateable']) )
			{
			 	$garage_template->rating_dropdown('rating');
				$template->assign_vars(array(
					'S_DISPLAY_RATE'	=> true,
					'L_RATING_NOTICE'	=> $user->lang['UPDATE_RATING'])
				);
			}
			//Rated Already But Not Permanent & Updated Not Always Allowed, Vehicle Not Update So No Rate Update
			else if ( ( $rating['total'] > 0 ) AND (!$garage_config['rating_permanent']) AND (!$garage_config['rating_always_updateable']) AND ($rating['rate_date'] > $vehicle['date_updated']) )
			{
				$template->assign_vars(array(
					'L_RATING_NOTICE'	=> $user->lang['VEHICLE_UPDATE_REQUIRED_FOR_RATE'])
				);
			}
			//Rated Already But Not Permanent & Updated Not Always Allowed, Vehicle Updated So Rate Update Allowed
			else if ( ( $rating['total'] > 0 ) AND (!$garage_config['rating_permanent']) AND (!$garage_config['rating_always_updateable']) AND ($row['rate_date'] < $vehicle['date_updated']) )
			{
			 	$garage_template->rating_dropdown('rating');
				$template->assign_vars(array(
					'S_DISPLAY_RATE'	=> true,
					'L_RATING_NOTICE' 	=> $user->lang['UPDATE_RATING'])
				);
			}
		}

		//Display Guestbook
		if ($garage_config['enable_guestbooks'])
		{
			//Display Vehicle Guestbook
			$garage_guestbook->display_guestbook($cid);
			$template->assign_vars(array(
				'S_DISPLAY_GUESTBOOK_TAB' => true,
			));
			$lowest_tab[] = 8;
		}

		//Display Blog
		if ($garage_config['enable_blogs'])
		{
			//Display Vehicle Guestbook
			$garage_blog->display_blog($cid);
			$template->assign_vars(array(
				'S_DISPLAY_BLOG_TAB' => true,
			));
			$lowest_tab[] = 7;
		}

		//Select Categories For Which Vehicle Has Modifications
		$category_data = $this->get_vehicle_modification_categories($cid);

	      	//Loop Processing All Categoires Returned....
	      	for ( $i = 0; $i < count($category_data); $i++ )
		{
			$template->assign_vars(array(
				'S_DISPLAY_MODIFICATION_TAB' => true,
			));
			$lowest_tab[] = 1;
	       		//Setup cat_row Template Varibles
	       		$template->assign_block_vars('category', array(
	           		'CATEGORY_TITLE' => $category_data[$i]['title'])
	       		);
	
			// Select All Mods From This Car For Category We Are Currently Processing
			$modification_data = $garage_modification->get_modifications_by_category($cid, $category_data[$i]['id']);

			//Process Modifications From This Category..
			$gallery_modification_images = null;
        		for ( $j = 0; $j < count($modification_data); $j++ )
			{
				//Increment Modification Image Count If Image Exists
	           		if ($modification_data[$j]['attach_id'])
				{
					$mod_images_found++;
					$template->assign_vars(array(
						'S_DISPLAY_IMAGE_TAB' => true,
					));
					$lowest_tab[] = 0;
				}

				$template->assign_block_vars('category.modification', array(
					'U_IMAGE'	=> ($modification_data[$j]['attach_id']) ? append_sid("garage.$phpEx", "mode=view_image&amp;image_id=". $modification_data[$j]['attach_id']) : '',
					'IMAGE'		=> $user->img('garage_vehicle_img_attached', 'MODIFICATION_IMAGE_ATTACHED'),
	               			'COST' 		=> $modification_data[$j]['price'],
	               			'INSTALL' 	=> $modification_data[$j]['install_price'],
	               			'RATING' 	=> $modification_data[$j]['product_rating'],
	               			'CREATED' 	=> $user->format_date($modification_data[$j]['date_created']),
	               			'UPDATED' 	=> $user->format_date($modification_data[$j]['date_updated']),
	               			'MODIFICATION' 	=> '<a href="' . append_sid("garage_modification.$phpEx?mode=view_modification&amp;CID=$cid&amp;MID=" . $modification_data[$j]['id']) . '">' . $modification_data[$j]['title'] . '</a>',
					'U_EDIT'	=> (($owned == 'YES') OR ($owned == 'MODERATE')) ? append_sid("garage_modification.$phpEx?mode=edit_modification&amp;MID=". $modification_data[$j]['id'] . "&amp;CID=$cid") : '',
					'U_DELETE' 	=> ( (($owned == 'YES') OR ($owned == 'MODERATE')) AND ( (($auth->acl_get('u_garage_delete_modification'))) OR ($auth->acl_get('m_garage'))) ) ? 'javascript:confirm_delete_mod(' . $cid . ',' . $modification_data[$j]['id'] . ')' : '')
	            		);
	
				//See If Mod Has An Image Attached And Display Gallery If Enabled & Below Limits
				if ( (($garage_config['enable_mod_gallery'] == 1) AND ( $modification_data[$j]['attach_is_image'] )) AND ($garage_config['mod_gallery_limit'] >= $mod_images_found OR !$garage_config['mod_gallery_limit']) )
				{
					$mod_images_displayed = $mod_images_found;
	                		//Do we have a thumbnail?  If so, our job is simple here :)
					if ( (empty($modification_data[$j]['attach_thumb_location']) == false) AND ($modification_data[$j]['attach_thumb_location'] != $modification_data[$j]['attach_location']) )
					{
						$template->assign_block_vars('modification_image', array(
							'U_IMAGE' 	=> append_sid('garage.'.$phpEx.'?mode=view_image&amp;image_id='. $modification_data[$j]['attach_id']),
							'IMAGE_NAME'	=> $modification_data[$j]['attach_file'],
							'IMAGE_SOURCE'	=> $phpbb_root_path . GARAGE_UPLOAD_PATH . $modification_data[$j]['attach_thumb_location'])
						);
	               			} 
				}
	         	}
	      	}
	
		// Next Lets See If We Have Any Insurance Premiums
		$insurance_data = $garage_insurance->get_premiums_by_vehicle($cid);
	
         	//If Any Premiums Exist Process Them...
		if ( count($insurance_data) > 0 )
		{
			$template->assign_vars(array(
				'S_DISPLAY_PREMIUM_TAB' => true,
			));
			$lowest_tab[] = 5;
			$template->assign_block_vars('insurance', array());
        		for ( $i = 0; $i < count($insurance_data); $i++ )
	         	{
				$template->assign_block_vars('insurance.premium', array(
					'INSURER' 	=> $insurance_data[$i]['title'],
					'PREMIUM' 	=> $insurance_data[$i]['premium'],
					'COVER_TYPE' 	=> $insurance_data[$i]['cover_type'],
					'U_EDIT'	=> (($owned == 'YES') OR ($owned == 'MODERATE')) ? append_sid("garage.$phpEx?mode=edit_insurance&amp;INS_ID=".$insurance_data[$i]['id']."&amp;CID=$cid") : '',
					'U_DELETE' 	=> ( (($owned == 'YES') OR ($owned == 'MODERATE')) AND ( (($auth->acl_get('u_garage_delete_insurance'))) OR ($auth->acl_get('m_garage'))) ) ? 'javascript:confirm_delete_insurance(' . $cid . ',' . $insurance_data[$i]['id'] . ')' : '',
					'U_INSURER' 	=> append_sid("garage.$phpEx", "mode=insurance_review&amp;business_id=".$insurance_data[$i]['business_id']),
				));
			}
		}
	
		//Next Lets See If We Have Any QuarterMile Runs
		$quartermile_data = $garage_quartermile->get_quartermile_by_vehicle($cid);
	
         	//If Any Quartermiles Exist Process Them...
		if ( count($quartermile_data) > 0 )
		{
			$template->assign_vars(array(
				'S_DISPLAY_QUARTERMILE_TAB' => true,
			));
			$lowest_tab[] = 2;
			$template->assign_block_vars('quartermile', array());
        		for ( $i = 0; $i < count($quartermile_data); $i++ )
	         	{
				$template->assign_block_vars('quartermile.run', array(
					'RT' 		=> $quartermile_data[$i]['rt'],
					'SIXTY' 	=> $quartermile_data[$i]['sixty'],
					'THREE' 	=> $quartermile_data[$i]['three'],
					'EIGHTH' 	=> $quartermile_data[$i]['eighth'],
					'EIGHTHMPH' 	=> $quartermile_data[$i]['eighthmph'],
					'THOU' 		=> $quartermile_data[$i]['thou'],
					'QUART' 	=> $quartermile_data[$i]['quart'],
					'QUARTMPH' 	=> $quartermile_data[$i]['quartmph'],
					'U_IMAGE'	=> ($quartermile_data[$i]['attach_id']) ? append_sid("garage.$phpEx", "mode=view_image&amp;image_id=". $quartermile_data[$i]['attach_id']) : '',
					'IMAGE'		=> $user->img('garage_slip_img_attached', 'SLIP_IMAGE_ATTACHED'),
					'U_QUART'	=> append_sid("garage_quartermile.$phpEx?mode=view_quartermile&amp;QMID=".$quartermile_data[$i]['id']."&amp;CID=$cid"),
					'U_EDIT'	=> (($owned == 'YES') OR ($owned == 'MODERATE')) ? append_sid("garage_quartermile.$phpEx?mode=edit_quartermile&amp;QMID=".$quartermile_data[$i]['id']."&amp;CID=$cid") : '',
					'U_DELETE' 	=> ( (($owned == 'YES') OR ($owned == 'MODERATE')) AND ( (($auth->acl_get('u_garage_delete_quartermile'))) OR ($auth->acl_get('m_garage'))) ) ? 'javascript:confirm_delete_quartermile(' . $cid . ',' . $quartermile_data[$i]['id'] . ')' : '')
				);
			}
		}

		//Get All Dynoruns For Vehicle
		$dynorun_data = $garage_dynorun->get_dynoruns_by_vehicle($cid);
	
         	//If Any Dynoruns Exist Process Them...
		if ( count($dynorun_data) > 0 )
		{
			$template->assign_vars(array(
				'S_DISPLAY_DYNORUN_TAB' => true,
			));
			$lowest_tab[] = 3;
			$template->assign_block_vars('dynorun', array());
         		for ( $i = 0; $i < count($dynorun_data); $i++ )
         		{
				$template->assign_block_vars('dynorun.run', array(
					'DYNOCENTER'	=> $dynorun_data[$i]['title'],
					'BHP' 		=> $dynorun_data[$i]['bhp'],
					'BHP_UNIT' 	=> $dynorun_data[$i]['bhp_unit'],
					'TORQUE' 	=> $dynorun_data[$i]['torque'],
					'TORQUE_UNIT' 	=> $dynorun_data[$i]['torque_unit'],
					'BOOST' 	=> $dynorun_data[$i]['boost'],
					'BOOST_UNIT' 	=> $dynorun_data[$i]['boost_unit'],
					'NITROUS' 	=> $dynorun_data[$i]['nitrous'],
					'PEAKPOINT' 	=> $dynorun_data[$i]['peakpoint'],
					'U_IMAGE'	=> ($dynorun_data[$i]['attach_id']) ? append_sid("garage.$phpEx", "mode=view_image&amp;image_id=". $dynorun_data[$i]['attach_id']) : '',
					'IMAGE'		=> $user->img('garage_slip_img_attached', 'SLIP_IMAGE_ATTACHED'),
					'U_BHP'		=> append_sid("garage_dynorun.$phpEx?mode=view_dynorun&amp;DID=".$dynorun_data[$i]['did']."&amp;CID=$cid"),
					'U_EDIT'	=> (($owned == 'YES') OR ($owned == 'MODERATE')) ? append_sid("garage_dynorun.$phpEx?mode=edit_dynorun&amp;DID=".$dynorun_data[$i]['did']."&amp;CID=$cid") : '',
					'U_DELETE' 	=> ( (($owned == 'YES') OR ($owned == 'MODERATE')) AND ( (($auth->acl_get('u_garage_delete_dynorun'))) OR ($auth->acl_get('m_garage'))) ) ? 'javascript:confirm_delete_dynorun(' . $cid . ',' . $dynorun_data[$i]['id'] . ')' : '')
				);
			}
		}

		//Get All Dynoruns For Vehicle
		$lap_data = $garage_track->get_laps_by_vehicle($cid);

         	//If Any Laps Exist Process Them...
		if ( count($lap_data) > 0 )
		{
			$template->assign_vars(array(
				'S_DISPLAY_LAP_TAB' => true,
			));
			$lowest_tab[] = 4;
			$template->assign_block_vars('tracktime', array());
         		for ( $i = 0; $i < count($lap_data); $i++ )
			{
				$template->assign_block_vars('tracktime.lap', array(
					'TRACK'		=> $lap_data[$i]['title'],
					'CONDITION'	=> $garage_track->get_track_condition($lap_data[$i]['condition_id']),
					'TYPE'		=> $garage_track->get_lap_type($lap_data[$i]['type_id']),
					'MINUTE'	=> $lap_data[$i]['minute'],
					'SECOND'	=> $lap_data[$i]['second'],
					'MILLISECOND'	=> $lap_data[$i]['millisecond'],
					'IMAGE'		=> $user->img('garage_slip_img_attached', 'SLIP_IMAGE_ATTACHED'),
					'U_IMAGE'	=> ($lap_data[$i]['attach_id']) ? append_sid("garage.$phpEx", "mode=view_image&amp;image_id=". $lap_data[$i]['attach_id']) : '',
					'U_TRACK'	=> append_sid("garage_track.$phpEx?mode=view_track&amp;TID=".$lap_data[$i]['track_id']."&amp;CID=$cid"),
					'U_LAP'		=> append_sid("garage_track.$phpEx?mode=view_lap&amp;LID=".$lap_data[$i]['lid']."&amp;CID=$cid"),
					'U_EDIT'	=> (($owned == 'YES') OR ($owned == 'MODERATE')) ? append_sid("garage_track.$phpEx?mode=edit_lap&amp;LID=".$lap_data[$i]['lid']."&amp;CID=$cid") : '',
					'U_DELETE' 	=> ( (($owned == 'YES') OR ($owned == 'MODERATE')) AND ( (($auth->acl_get('u_garage_delete_lap'))) OR ($auth->acl_get('m_garage'))) ) ? 'javascript:confirm_delete_lap(' . $cid . ',' . $lap_data[$i]['lid'] . ')' : '')
				);
			}
		}

		//Get Service History For Vehicle
		$service_data = $garage_service->get_services_by_vehicle($cid);	

         	//If Any Laps Exist Process Them...
		if ( count($service_data) > 0 )
		{
			$template->assign_vars(array(
				'S_DISPLAY_SERVICE_TAB' => true,
			));
			$lowest_tab[] = 6;
			$template->assign_block_vars('service_history', array());
         		for ( $i = 0; $i < count($service_data); $i++ )
			{
				$template->assign_block_vars('service_history.service', array(
					'TITLE'		=> $service_data[$i]['title'],
					'TYPE'		=> $garage_service->get_service_type($service_data[$i]['type_id']),
					'PRICE'		=> $service_data[$i]['price'],
					'RATING'	=> $service_data[$i]['rating'],
					'MILEAGE'	=> $service_data[$i]['mileage'],
					'U_GARAGE'	=> append_sid("garage.$phpEx?mode=garage_review&amp;BUS_ID=".$service_data[$i]['garage_id']."&amp;CID=$cid"),
					'U_EDIT'	=> (($owned == 'YES') OR ($owned == 'MODERATE')) ? append_sid("garage_service.$phpEx?mode=edit_service&amp;SVID=".$service_data[$i]['id']."&amp;CID=$cid") : '',
					'U_DELETE' 	=> ( (($owned == 'YES') OR ($owned == 'MODERATE')) AND ( (($auth->acl_get('u_garage_delete_lap'))) OR ($auth->acl_get('m_garage'))) ) ? 'javascript:confirm_delete_service(' . $cid . ',' . $service_data[$i]['id'] . ')' : '')
				);
			}
		}
			
		//Get All Gallery Data Required
		$gallery_data = $garage_image->get_vehicle_gallery($cid);
			
		//Process Each Image From Vehicle Gallery	
       		for ( $i = 0; $i < count($gallery_data); $i++ )
        	{
			$template->assign_vars(array(
				'S_DISPLAY_IMAGE_TAB' => true,
			));
			$lowest_tab[] = 0;
       	    		if ( $gallery_data[$i]['attach_is_image'] )
        		{
			        $vehicle_images_found++;
		
                		// Do we have a thumbnail?  If so, our job is simple here :)
				if ( (empty($gallery_data[$i]['attach_thumb_location']) == false) AND ($gallery_data[$i]['attach_thumb_location'] != $gallery_data[$i]['attach_location']) )
				{

					$template->assign_block_vars('vehicle_image', array(
						'U_IMAGE' 	=> append_sid('garage.'.$phpEx.'?mode=view_image&amp;image_id='. $gallery_data[$i]['attach_id']),
						'IMAGE_NAME'	=> $gallery_data[$i]['attach_file'],
						'IMAGE_SOURCE'	=> $phpbb_root_path . GARAGE_UPLOAD_PATH . $gallery_data[$i]['attach_thumb_location'])
					);
               			} 
			}
		}

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $vehicle['vehicle'])
		);

		$template->assign_vars(array(
			'U_DELETE_MODIFICATION'		=> append_sid("garage_modification.$phpEx?mode=delete_modification"),
			'U_DELETE_QUARTERMILE'		=> append_sid("garage_quartermile.$phpEx?mode=delete_quartermile"),
			'U_DELETE_PREMIUM' 		=> append_sid("garage.$phpEx?mode=delete_insurance"),
			'U_DELETE_DYNORUN' 		=> append_sid("garage_dynorun.$phpEx?mode=delete_dynorun"),
			'U_GUESTBOOK' 			=> append_sid("garage_guestbook.$phpEx?mode=view_guestbook&amp;CID=$cid"),
            		'U_PROFILE' 			=> append_sid("memberlist.$phpEx?mode=viewprofile&amp;u=".$vehicle['user_id']),
            		'U_VIEW_VEHICLE' 		=> ( $owned == 'YES' ) ? append_sid("garage_vehicle.$phpEx?mode=view_vehicle&amp;CID=$cid") : '',
            		'U_EDIT_VEHICLE' 		=> ( $owned == 'YES' ) ? append_sid("garage_vehicle.$phpEx?mode=edit_vehicle&amp;CID=$cid") : '',
            		'U_DELETE_VEHICLE' 		=> ( ($owned == 'YES' AND $auth->acl_get('u_garage_delete_vehicle')) OR ($auth->acl_get('m_garage'))) ? 'javascript:confirm_delete_car(' . $cid . ')' : '',
            		'U_ADD_MODIFICATION' 		=> ( $owned == 'YES' ) ? append_sid("garage_modification.$phpEx?mode=add_modification&amp;CID=$cid") : '',
            		'U_ADD_INSURANCE' 		=> ( $owned == 'YES' AND $garage_config['enable_insurance'] ) ? append_sid("garage_premium.$phpEx?mode=add_premium&amp;CID=$cid") : '',
            		'U_ADD_QUARTERMILE' 		=> ( $owned == 'YES' AND $garage_config['enable_quartermile'] ) ? append_sid("garage_quartermile.$phpEx?mode=add_quartermile&amp;CID=$cid") : '',
            		'U_ADD_DYNORUN' 		=> ( $owned == 'YES' AND $garage_config['enable_dynorun'] ) ? append_sid("garage_dynorun.$phpEx?mode=add_dynorun&amp;CID=$cid") : '',
            		'U_ADD_LAP' 		=> ( $owned == 'YES' AND $garage_config['enable_tracktime'] ) ? append_sid("garage_track.$phpEx?mode=add_lap&amp;CID=$cid") : '',
            		'U_ADD_SERVICE' 		=> ( $owned == 'YES' AND $garage_config['enable_service'] ) ? append_sid("garage_service.$phpEx?mode=add_service&amp;CID=$cid") : '',
            		'U_MANAGE_VEHICLE_GALLERY'	=> ( $owned == 'YES' ) ? append_sid("garage_vehicle.$phpEx?mode=manage_vehicle_gallery&amp;CID=$cid") : '',
			'U_SET_MAIN_VEHICLE' 		=> ( ($owned == 'YES' OR $owned == 'MODERATE') AND ($vehicle['main_vehicle'] == 0) ) ?  append_sid("garage.$phpEx?mode=set_main&amp;CID=$cid"): '' ,
			'U_MODERATE_VEHICLE' 		=> ( $owned == 'NO' AND $auth->acl_get('m_garage')) ?  append_sid("garage_vehicle.$phpEx?mode=moderate_vehicle&amp;CID=$cid"): '' ,
			'U_HILITE_IMAGE' 		=> ( ($vehicle['attach_id']) AND ($vehicle['attach_is_image']) AND (!empty($vehicle['attach_thumb_location'])) AND (!empty($vehicle['attach_location'])) ) ?  append_sid("garage.$phpEx?mode=view_image&amp;image_id=". $vehicle['attach_id']): '' ,

			'S_DISPLAY_VEHICLE_OWNER'	=> ($owned == 'MODERATE' || $owned == 'YES') ? 1 : 0,
			'S_DISPLAY_ENTRY_BLOG'		=> ($owned == 'MODERATE' || $owned == 'YES') ? 1 : 0,
			'S_DISPLAY_GUESTBOOK'		=> ($garage_config['enable_guestbooks']) ? 1 : 0,
			'S_DISPLAY_GALLERIES'		=> ($vehicle_images_found > 0 || $mod_images_displayed > 0) ? 1 : 0,
			'S_DISPLAY_VEHICLE_IMAGES'	=> ($vehicle_images_found > 0) ? 1 : 0,
			'S_DISPLAY_MODIFICATION_IMAGES'	=> ($mod_images_displayed > 0) ? 1 : 0,
			'S_LOWEST_TAB_AVAILABLE'	=> min($lowest_tab),

            		'EDIT' 				=> ($garage_config['enable_images']) ? $user->img('garage_edit', 'EDIT') : $user->lang['EDIT'],
            		'DELETE' 			=> ($garage_config['enable_images']) ? $user->img('garage_delete', 'DELETE') : $user->lang['DELETE'],
            		'VIEW_VEHICLE' 			=> ($garage_config['enable_images']) ? $user->img('garage_view_vehicle', 'VIEW_VEHICLE') : $user->lang['VIEW_VEHICLE'],
            		'EDIT_VEHICLE' 			=> ($garage_config['enable_images']) ? $user->img('garage_edit_vehicle', 'EDIT_VEHICLE') : $user->lang['EDIT_VEHICLE'],
            		'ADD_MODIFICATION' 		=> ($garage_config['enable_images']) ? $user->img('garage_add_modification', 'ADD_NEW_MODIFICATION') : $user->lang['ADD_NEW_MODIFICATION'],
            		'ADD_INSURANCE' 		=> ($garage_config['enable_images']) ? $user->img('garage_add_insurance', 'ADD_NEW_INSURANCE_PREMIUM') : $user->lang['ADD_NEW_INSURANCE_PREMIUM'],
            		'ADD_QUARTERMILE' 		=> ($garage_config['enable_images']) ? $user->img('garage_add_quartermile', 'ADD_NEW_QUARTERMILE_TIME') : $user->lang['ADD_NEW_QUARTERMILE_TIME'],
            		'ADD_DYNORUN'	 		=> ($garage_config['enable_images']) ? $user->img('garage_add_dynorun',  'ADD_NEW_DYNORUN_RUN') : $user->lang['ADD_NEW_DYNORUN_RUN'],
            		'ADD_LAP'	 		=> ($garage_config['enable_images']) ? $user->img('garage_add_lap',  'ADD_NEW_LAP') : $user->lang['ADD_NEW_LAP'],
            		'ADD_SERVICE'	 		=> ($garage_config['enable_images']) ? $user->img('garage_add_service',  'ADD_NEW_SERVICE') : $user->lang['ADD_NEW_SERVICE'],
            		'MANAGE_VEHICLE_GALLERY'	=> ($garage_config['enable_images']) ? $user->img('garage_manage_gallery', 'MANAGE_VEHICLE_GALLERY') : $user->lang['MANAGE_VEHICLE_GALLERY'],
            		'DELETE_VEHICLE' 		=> ($garage_config['enable_images']) ? $user->img('garage_delete_vehicle', 'DELETE_VEHICLE') : $user->lang['DELETE_VEHICLE'],
			'SET_MAIN_VEHICLE' 		=> ($garage_config['enable_images']) ? $user->img('garage_main_vehicle', 'SET_MAIN_VEHICLE') : $user->lang['SET_MAIN_VEHICLE'],
			'MODERATE_VEHICLE' 		=> ($garage_config['enable_images']) ? $user->img('garage_moderate_vehicle', 'MODERATE_VEHICLE') : $user->lang['MODERATE_VEHICLE'],
			'COMMENT_COUNT' 		=> $garage_guestbook->count_vehicle_comments($cid),
	       		'TOTAL_MOD_IMAGES' 		=> $mod_images_found,
            		'SHOWING_MOD_IMAGES' 		=> $mod_images_displayed,
			'CID' 				=> $vehicle['id'],
			'YEAR' 				=> $vehicle['made_year'],
			'ENGINE_TYPE' 			=> $vehicle['engine_type'],
			'MAKE' 				=> $vehicle['make'],
			'MODEL'				=> $vehicle['model'],
            		'COLOUR' 			=> $vehicle['colour'],
            		'HILITE_IMAGE' 			=> $phpbb_root_path . GARAGE_UPLOAD_PATH . $vehicle['attach_thumb_location'] ,
            		'HILITE_IMAGE_TITLE' 		=> $vehicle['attach_file'],
            		'AVATAR_IMG' 			=> $vehicle['avatar'],
            		'DATE_UPDATED' 			=> $user->format_date($vehicle['date_updated']),
            		'MILEAGE' 			=> $vehicle['mileage'],
            		'MILEAGE_UNITS' 		=> $vehicle['mileage_unit'],
            		'PRICE' 			=> $vehicle['price'],
            		'CURRENCY' 			=> $vehicle['currency'],
            		'TOTAL_MODS' 			=> $vehicle['total_mods'],
            		'TOTAL_SPENT' 			=> (!empty($vehicle['total_spent'])) ? $vehicle['total_spent'] : 0,
            		'TOTAL_VIEWS' 			=> $vehicle['views'],
			'TOTAL_IMAGE_VIEWS' 		=> $vehicle['attach_hits'],
			'USERNAME'			=> $vehicle['username'],
			'RATING' 			=> ( $vehicle['weighted_rating'] == '0' ) ? $user->lang['NOT_RATED_YET'] : $vehicle['weighted_rating'] . ' / 10',
            		'DESCRIPTION' 			=> str_replace("\n", "\n<br />\n", $vehicle['comments']))
         	);

		return;
	}
	
	/*========================================================================*/
	// Select All Vehicles Data From Db
	// Usage: get_all_vehicles();
	/*========================================================================*/
	function get_all_vehicles($additional_where = NULL, $order, $sort, $start = 0, $limit = 10000)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.*, mk.make, md.model, user.username, count(mods.id) AS total_mods, count(*) as total, i.attach_id',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MODIFICATIONS_TABLE => 'mods'),	
					'ON'	=> 'mods.vehicle_id = g.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'mk'),
					'ON'	=> 'g.make_id = mk.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'md'),
					'ON'	=> 'g.model_id = md.id'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'user'),
					'ON'	=> 'g.user_id = user.user_id'
				)
				,array(
					'FROM'	=> array(GARAGE_VEHICLE_GALLERY_TABLE => 'vg'),
					'ON'	=> 'g.id = vg.vehicle_id AND vg.hilite = 1',
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = vg.image_id'
				)
			),
			'WHERE'		=> "mk.pending = 0 AND md.pending = 0 " . $additional_where,
			'GROUP_BY'	=> "g.id",
			'ORDER_BY'	=> "$order $sort"
		));

      		$result = $db->sql_query_limit($sql, $limit, $start);
		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select All Vehicle Data From Db
	// Usage: get_vehicle('vehicle id');
	/*========================================================================*/
	function get_vehicle($cid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.*, ROUND(g.weighted_rating, 2) as weighted_rating, images.*, makes.make, models.model, CONCAT_WS(\' \', g.made_year, makes.make, models.model) AS vehicle, count(mods.id) AS total_mods, ( SUM(mods.price) + SUM(mods.install_price) ) AS total_spent, user.username, user.user_avatar_type, user.user_avatar, user.user_id, user.user_avatar_width, user.user_avatar_height',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MODIFICATIONS_TABLE => 'mods'),	
					'ON'	=> 'mods.vehicle_id = g.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'makes'),
					'ON'	=> 'g.make_id = makes.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'models'),
					'ON'	=> 'g.model_id = models.id'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'user'),
					'ON'	=> 'g.user_id = user.user_id'
				)
				,array(
					'FROM'	=> array(GARAGE_VEHICLE_GALLERY_TABLE => 'vg'),
					'ON'	=> 'g.id = vg.vehicle_id AND vg.hilite = 1',
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'images'),
					'ON'	=> 'images.attach_id = vg.image_id'
				)
			),
			'WHERE'		=> "g.id = $cid",
			'GROUP_BY'	=> "g.id"
		));

      		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select All Vehicle Data From Db
	// Usage: get_pending_vehicles();
	/*========================================================================*/
	function get_pending_vehicles()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> "g.*, ROUND(g.weighted_rating, 2) as weighted_rating, images.*, makes.make, models.model, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, count(mods.id) AS total_mods, ( SUM(mods.price) + SUM(mods.install_price) ) AS total_spent, user.username, user.user_avatar_type, user.user_avatar, user.user_id",
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MODIFICATIONS_TABLE => 'mods'),	
					'ON'	=> 'mods.vehicle_id = g.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'makes'),
					'ON'	=> 'g.make_id = makes.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'models'),
					'ON'	=> 'g.model_id = models.id'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'user'),
					'ON'	=> 'g.user_id = user.user_id'
				)
				,array(
					'FROM'	=> array(GARAGE_VEHICLE_GALLERY_TABLE => 'vg'),
					'ON'	=> 'g.id = vg.vehicle_id AND vg.hilite = 1',
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'images'),
					'ON'	=> 'images.attach_id = vg.image_id'
				)
			),
			'WHERE'		=> "g.pending = 1",
			'GROUP_BY'	=> "g.id"
		));

      		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select All Vehicle Data From Db
	// Usage: get_pending_vehicles();
	/*========================================================================*/
	function get_vehicles_by_make_id($make_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> "g.id",
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
			),
			'WHERE'		=> "g.make_id = $make_id"
		));

      		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select All Vehicle Data From Db
	// Usage: get_pending_vehicles();
	/*========================================================================*/
	function get_vehicles_by_model_id($model_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> "g.id",
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
			),
			'WHERE'		=> "g.model_id = $model_id"
		));

      		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select Vehicle Owner From Db
	// Usage: get_vehicle_owner('vehicle id');
	/*========================================================================*/
	function get_vehicle_owner($cid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'u.username',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
				USERS_TABLE		=> 'u',
			),
			'WHERE'		=> "g.id = $cid and g.user_id = u.user_id",
			'GROUP_BY'	=> "g.id",
		));

      		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data['username'];
	}

	/*========================================================================*/
	// Select Vehicle Owner ID From Db
	// Usage: get_vehicle_owner_id('vehicle id');
	/*========================================================================*/
	function get_vehicle_owner_id($cid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'u.user_id',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
				USERS_TABLE		=> 'u',
			),
			'WHERE'		=> "g.id = $cid and g.user_id = u.user_id",
			'GROUP_BY'	=> "g.id",
		));

      		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data['user_id'];
	}


	/*========================================================================*/
	// Select All Vehicles From User Data From Db
	// Usage: get_vehicles_by_user('user id');
	/*========================================================================*/
	function get_vehicles_by_user($user_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.*, ROUND(g.weighted_rating, 2) as weighted_rating, images.*, makes.make, models.model, CONCAT_WS(\' \', g.made_year, makes.make, models.model) AS vehicle, count(mods.id) AS total_mods, ( SUM(mods.price) + SUM(mods.install_price) ) AS total_spent, user.username, user.user_avatar_type, user.user_avatar, user.user_id',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MODIFICATIONS_TABLE => 'mods'),	
					'ON'	=> 'mods.vehicle_id = g.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'makes'),
					'ON'	=> 'g.make_id = makes.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'models'),
					'ON'	=> 'g.model_id = models.id'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'user'),
					'ON'	=> 'g.user_id = user.user_id'
				)
				,array(
					'FROM'	=> array(GARAGE_VEHICLE_GALLERY_TABLE => 'vg'),
					'ON'	=> 'g.id = vg.vehicle_id AND vg.hilite = 1'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'images'),
					'ON'	=> 'images.attach_id = vg.image_id'
				)
			),
			'WHERE'		=> "g.user_id = $user_id",
			'GROUP_BY'	=> "g.id"
		));

      		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Selects Rating Information For Vehicle
	// Usage: get_vehicle_rating('vehicle id');
	/*========================================================================*/
	function get_vehicle_rating($cid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'r.*, u.username',
			'FROM'		=> array(
				GARAGE_RATINGS_TABLE	=> 'r',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),	
					'ON'	=> 'r.vehicle_id = g.id'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'user'),
					'ON'	=> 'r.user_id = u.user_id'
				)
			),
			'WHERE'		=> "r.vehicle_id = $cid"
		));

      		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Selects Newest Vehicles
	// Usage: get_newest_vehicles('No. To Return');
	/*========================================================================*/
	function get_newest_vehicles($limit)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.id, CONCAT_WS(\' \', g.made_year, makes.make, models.model) AS vehicle, g.user_id, g.date_created AS POI, u.username',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'makes'),	
					'ON'	=> 'g.make_id = makes.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'models'),	
					'ON'	=> 'g.model_id = models.id'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
				)
			),
			'WHERE'		=> "makes.pending = 0 AND models.pending = 0",
			'ODRDER_BY'	=> "POI DESC"
		));

	 	$result = $db->sql_query_limit($sql, $limit);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Selects Top Rated Vehicles
	// Usage: get_top_rated_vehicles('No. To Return');
	/*========================================================================*/
	function get_top_rated_vehicles($limit)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.id, g.user_id, ROUND(g.weighted_rating, 2) as weighted_rating, u.username, CONCAT_WS(\' \', g.made_year, makes.make, models.model) AS vehicle',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'makes'),	
					'ON'	=> 'g.make_id = makes.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'models'),	
					'ON'	=> 'g.model_id = models.id'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
				)
			),
			'WHERE'		=> "makes.pending = 0 AND models.pending = 0",
			'ODRDER_BY'	=> "g.weighted_rating DESC"
		));

	 	$result = $db->sql_query_limit($sql, $limit);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Selects Most Viewed Vehicles
	// Usage: get_most_viewed_vehicles('No. To Return');
	/*========================================================================*/
	function get_most_viewed_vehicles($limit)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.id, CONCAT_WS(\' \', g.made_year, makes.make, models.model) AS vehicle,  g.user_id, g.views AS POI, u.username',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'makes'),	
					'ON'	=> 'g.make_id = makes.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'models'),	
					'ON'	=> 'g.model_id = models.id'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
				)
			),
			'WHERE'		=> "makes.pending = 0 AND models.pending = 0",
			'ODRDER_BY'	=> "POI DESC"
		));

	 	$result = $db->sql_query_limit($sql, $limit);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Selects Most Spent Vehicle
	// Usage: get_most_spent_vehicles('No. To Return');
	/*========================================================================*/
	function get_most_spent_vehicles($limit)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.id, CONCAT_WS(\' \', g.made_year, makes.make, models.model) AS vehicle, g.user_id, (SUM(mods.install_price) + SUM(mods.price)) AS POI, u.username, g.currency',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'makes'),	
					'ON'	=> 'g.make_id = makes.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'models'),	
					'ON'	=> 'g.model_id = models.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MODIFICATIONS_TABLE => 'mods'),
					'ON'	=> 'mods.vehicle_id = g.id'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
				)
			),
			'WHERE'		=> 'makes.pending = 0 AND models.pending = 0',
			'GROUP_BY'	=> 'g.id',
			'ODRDER_BY'	=> 'POI DESC'
		));

	 	$result = $db->sql_query_limit($sql, $limit);
		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Selects Lastest Updated Vehicle
	// Usage: get_latest_updatest_vehicles('No. To Return');
	/*========================================================================*/
	function get_vehicle_modification_categories($cid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT_DISTINCT', 
			array(
			'SELECT'	=> 'c.title, c.id',
			'FROM'		=> array(
				GARAGE_CATEGORIES_TABLE		=> 'c',
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
			),
			'WHERE'		=> "m.vehicle_id = $cid AND m.category_id = c.id",
			'ODRDER_BY'	=> 'c.field_order DESC'
		));

	      	$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}
	            
	/*========================================================================*/
	// Selects Lastest Updated Vehicle
	// Usage: get_latest_updatest_vehicles('No. To Return');
	/*========================================================================*/
	function get_latest_updated_vehicles($limit)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.id, g.made_year, g.user_id, g.date_updated, u.username, CONCAT_WS(\' \', g.made_year, makes.make, models.model) AS vehicle, u.user_colour',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'makes'),	
					'ON'	=> 'g.make_id = makes.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'models'),	
					'ON'	=> 'g.model_id = models.id'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
				)
			),
			'WHERE'		=> 'makes.pending = 0 AND models.pending = 0',
			'ORDER_BY'	=> 'g.date_updated DESC'
		));

      		$result = $db->sql_query_limit($sql, $limit);
		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select A Users Main Vehicle Data From Db
	// Usage: get_user_main_vehicle('user id');
	/*========================================================================*/
	function get_user_main_vehicle($user_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.*, images.*, makes.make, models.model, CONCAT_WS(\' \', g.made_year, makes.make, models.model) AS vehicle, count(mods.id) AS total_mods, ( SUM(mods.price) + SUM(mods.install_price) ) AS total_spent, user.username, user.user_avatar_type, user.user_avatar, user.user_id',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'makes'),	
					'ON'	=> 'g.make_id = makes.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'models'),	
					'ON'	=> 'g.model_id = models.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MODIFICATIONS_TABLE => 'mods'),
					'ON'	=> 'g.id = mods.vehicle_id'
				)
				,array(
					'FROM'	=> array(GARAGE_VEHICLE_GALLERY_TABLE => 'vg'),
					'ON'	=> 'g.id = vg.vehicle_id AND vg.hilite = 1',
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'images'),
					'ON'	=> 'images.attach_id = vg.image_id'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
				)
			),
			'WHERE'		=> "g.user_id = $user_id and g.main_vehicle =1",
			'GROUP_BY'	=> 'g.id'
		));

      		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Integrates phpBB Garage & phpBB User Profiles
	// Usage: profile_integration('user_id');
	/*========================================================================*/
	function profile_integration($user_id)
	{
		global $images, $template, $profiledata, $lang, $phpEx;

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
							$hilite_image .= '<a href=garage.'.$phpEx.'?mode=view_image&amp;type=garage_gallery&amp;image_id='. $gallery_data[$i]['attach_id'] .' title=' . $gallery_data[$i]['attach_file'] .' target="_blank"><img hspace="5" vspace="5" src="' . $thumb_image .'" class="attach"  /></a> ';
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
							$hilite_image .= '<a href=garage.'.$phpEx.'?mode=view_image&amp;type=garage_gallery&amp;image_id='. $mod_data[$i]['attach_id'] .' title=' . $mod_data[$i]['attach_file'] .' target="_blank"><img hspace="5" vspace="5" src="' . $thumb_image .'" class="attach"  /></a> ';
		               			} 
					}
			        }
			}
			//Looks Like We Only Need To Draw One Main Image
			else
			{
				if ( (!empty($vehicle_data['attach_thumb_location'])) AND (!empty($vehicle_data['attach_location'])) )
				{
					// Check to see if this is a remote image
					if ( preg_match( "/^http:\/\//i", $vehicle_data['attach_location']) )
					{
						$image = $vehicle_data['attach_location'];
						$id = $vehicle_data['attach_id'];
						$title = $vehicle_data['attach_file'];
						$total_image_views = $vehicle_data['attach_hits'];
						$hilite_image = '<a href=garage.'.$phpEx.'?mode=view_image&amp;type=garage_mod&amp;image_id='. $id .' title=' . $title .' target="_blank"><img hspace="5" vspace="5" src="' . $image .'" class="attach"  /></a>';
					}
					else
					{
						$image = GARAGE_UPLOAD_PATH . $vehicle_data['attach_location'];
						$id = $vehicle_data['attach_id'];
						$title = $vehicle_data['attach_file'];
						$total_image_views = $vehicle_data['attach_hits'];
						$hilite_image = '<a href=garage.'.$phpEx.'?mode=view_image&amp;type=garage_mod&amp;image_id='. $id .' title=' . $title .' target="_blank"><img hspace="5" vspace="5" src="' . $image .'" class="attach"  /></a>';
					}
				}
			}

			$garage_img ='<a href="' . append_sid("garage.$phpEx?mode=browse&search=yes&user=".urlencode($profiledata['username'])."") . '"><img src="' . $images['icon_garage'] . '" alt="'.$lang['Garage'].'" title="'.$lang['Garage'].'" border="0" /></a>';

			$template->assign_vars(array(
				'L_PRICE' => $lang['Purchased_Price'],
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

	/*========================================================================*/
	// Approve Quartermile Times
	// Usage: approve_quartermile(array(), 'mode');
	/*========================================================================*/
	function approve_vehicle($id_list, $mode)
	{
		global $phpbb_root_path, $phpEx, $garage;

		for($i = 0; $i < count($id_list); $i++)
		{
			$garage->update_single_field(GARAGE_VEHICLES_TABLE, 'pending', 0, 'id', $id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_vehicles"));
	}

	/*========================================================================*/
	// Approve Quartermile Times
	// Usage: approve_quartermile(array(), 'mode');
	/*========================================================================*/
	function disapprove_vehicle($id_list, $mode)
	{
		global $phpbb_root_path, $phpEx;

		for($i = 0; $i < count($id_list); $i++)
		{
			$this->delete_vehicle($id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_vehicles"));
	}
}

$garage_vehicle = new garage_vehicle();

?>

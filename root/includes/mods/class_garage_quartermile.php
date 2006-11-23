<?php
/***************************************************************************
 *                              class_garage_quartermile.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: class_garage_quartermile.php 138 2006-06-07 15:55:46Z poyntesm $
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

class garage_quartermile
{

	var $classname = "garage_quartermile";

	/*========================================================================*/
	// Inserts Quartermile Into DB
	// Usage: insert_quartermile(array());
	/*========================================================================*/
	function insert_quartermile($data)
	{
		global $cid, $db, $garage_config;

		$sql = "INSERT INTO ". GARAGE_QUARTERMILE_TABLE ."
			(garage_id, rt, sixty, three, eight, eightmph, thou, quart, quartmph, date_created, rr_id, date_updated, pending)
			VALUES
			($cid, '".$data['rt']."', '".$data['sixty']."', '".$data['three']."', '".$data['eight']."', '".$data['eightmph']."', '".$data['thou']."', '".$data['quart']."', '".$data['quartmph']."', '".time()."', '".$data['rr_id']."', '".time()."', '".($garage_config['enable_quartermile_approval'] == '1') ? 1 : 0."')";
		
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert Quartermile', '', __LINE__, __FILE__, $sql);
		}
	
		$qmid = $db->sql_nextid();

		return $qmid;
	}

	/*========================================================================*/
	// Updates Quartermile In DB
	// Usage: update_quartermile(array());
	/*========================================================================*/
	function update_quartermile($data)
	{
		global $db, $cid, $qmid;

		$sql = "UPDATE ". GARAGE_QUARTERMILE_TABLE ."
			SET rt = '".$data['rt']."', sixty = '".$data['sixty']."', three = '".$data['three']."', eight = '".$data['eight']."', eightmph = '".$data['eightmph']."', thou = '".$data['thou']."', quart = '".$data['quart']."', quartmph = '".$data['quartmph']."', rr_id = '".$data['rr_id']."', pending = '".($garage_config['enable_quartermile_approval'] == '1') ? 1 : 0."', date_updated ='".time()."'
			WHERE id = '$qmid' and garage_id = '$cid'";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Update Quartermile', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Delete Quartermile Entry Including Image 
	// Usage: delete_quartermile('quartermile id');
	/*========================================================================*/
	function delete_quartermile($qmid)
	{
		global $garage, $garage_image;
	
		//Right They Want To Delete A QuarterMile Time
		if (empty($qmid))
		{
	 		message_die(GENERAL_ERROR, 'Quartermile ID Not Entered', '', __LINE__, __FILE__);
		}
	
		//Let Get All Info For Run, Including Image Info
		$data = $this->get_quartermile($qmid);
	
		//Lets See If There Is An Image Associated With This Run
		if (!empty($data['image_id']))
		{
			if ( (!empty($data['attach_location'])) OR (!empty($data['attach_thumb_location'])) )
			{
				//Seems To Be An Image To Delete, Let Call The Function
				$garage_image->delete_image($data['image_id']);
			}
		}

		//Time To Delete The Actual Quartermile Time Now
		$garage->delete_rows(GARAGE_QUARTERMILE_TABLE, 'id', $qmid);

		return ;
	}
	
	/*========================================================================*/
	// Build Top Quartermile Runs HTML If Required 
	// Usage: show_topquartermile();
	/*========================================================================*/
	function show_topquartermile()
	{
		global $required_position, $user, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		if ( $garage_config['topquartermile_on'] != true )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $user->lang['TOP_QUARTERMILE_RUNS'],
			'COLUMN_1_TITLE' => $user->lang['VEHICLE'],
			'COLUMN_2_TITLE' => $user->lang['OWNER'],
			'COLUMN_3_TITLE' => $user->lang['QUARTERMILE'])
		);
	
	        // What's the count? Default to 10
	        $limit = $garage_config['topquartermile_limit'] ? $garage_config['topquartermile_limit'] : 10;
	
		//First Query To Return Top Time For All Or For Selected Filter...
		$sql = "SELECT  qm.garage_id, MIN(qm.quart) as quart
			FROM " . GARAGE_QUARTERMILE_TABLE ." AS qm
				LEFT JOIN " . GARAGE_TABLE ." AS g ON qm.garage_id = g.id
				LEFT JOIN " . USERS_TABLE ." AS user ON g.user_id = user.user_id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
	       			LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
			WHERE	(qm.sixty IS NOT NULL
				OR qm.three IS NOT NULL
				OR qm.eight IS NOT NULL
				OR qm.eightmph IS NOT NULL
				OR qm.thou IS NOT NULL
				OR qm.rt IS NOT NULL
				OR qm.quartmph IS NOT NULL) AND ( qm.pending = 0 )
				AND ( makes.pending = 0 AND models.pending = 0 )
			GROUP BY qm.garage_id
			ORDER BY quart ASC LIMIT $limit ";
	
		if( !($first_result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Selecting Top Quartermile Time', '', __LINE__, __FILE__, $sql);
		}
		
		//Now Process All Rows Returned And Get Rest Of Required Data	
		$i = 0;
		while ($row = $db->sql_fetchrow($first_result) )
		{
			$sql = "SELECT g.id, g.user_id, user.username, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle,
					qm.rt, qm.sixty, qm.three, qm.eight, qm.eightmph, qm.thou, qm.quart, qm.quartmph, qm.rr_id,
					rr.bhp, rr.bhp_unit, rr.torque, rr.torque_unit, rr.boost, rr.boost_unit, rr.nitrous
				FROM " . GARAGE_QUARTERMILE_TABLE ." AS qm
					LEFT JOIN " . GARAGE_TABLE ." AS g ON qm.garage_id = g.id
					LEFT JOIN " . USERS_TABLE ." AS user ON g.user_id = user.user_id
					LEFT JOIN " . GARAGE_ROLLINGROAD_TABLE . " AS rr ON qm.rr_id = rr.id
				        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
	       				LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
				WHERE qm.garage_id = " . $row['garage_id'] . " AND qm.quart = " . $row['quart'];
	
	 		if(!$result = $db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
			}
	 		            
		 	$vehicle_data = $db->sql_fetchrow($result);
	
			$mph = (empty($vehicle_data['quartmph'])) ? 'N/A' : $vehicle_data['quartmph'];
	            	$quartermile = $vehicle_data['quart'] .' @ ' . $mph . ' '. $lang['Quartermile_Speed_Unit'];
	
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
				'U_COLUMN_2' => append_sid("profile.$phpEx?mode=viewprofile&amp;u=".$vehicle_data['user_id']),
				'COLUMN_1_TITLE' => $vehicle_data['vehicle'],
				'COLUMN_2_TITLE' => $vehicle_data['username'],
				'COLUMN_3' => $quartermile)
			);
	 	}
	
		$required_position++;
		return ;
	}

	/*========================================================================*/
	// Select Quartermile Data
	// Usage: get_quartermile('quartermile id');
	/*========================================================================*/
	function get_quartermile($qmid)
	{
		global $db;
	
	   	$sql = "SELECT qm.*, rr.id, rr.bhp, rr.bhp_unit, images.*, g.made_year, makes.make, models.model, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle
                    	FROM " . GARAGE_QUARTERMILE_TABLE . " AS qm
		          	LEFT JOIN " . GARAGE_TABLE . " AS g ON qm.garage_id = g.id
		          	LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
                        	LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
        			LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = qm.image_id 
	        		LEFT JOIN " . GARAGE_ROLLINGROAD_TABLE . " AS rr ON rr.id = qm.rr_id 
                    	WHERE qm.id = $qmid";

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select Quartermile Data', '', __LINE__, __FILE__, $sql);
      		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row;
	}

	/*========================================================================*/
	// Select Quartermile Data By Vehicle
	// Usage: get_quartermile_by_vehicle('garage id');
	/*========================================================================*/
	function get_quartermile_by_vehicle($cid)
	{
		global $db;
	
		$sql = "SELECT qm.*,images.attach_id, images.attach_hits, images.attach_ext, images.attach_file, images.attach_thumb_location, images.attach_is_image, images.attach_location
	          	FROM " . GARAGE_QUARTERMILE_TABLE . " as qm
	                	LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = qm.image_id
		       	WHERE qm.garage_id = $cid";
	
	       	if( !($result = $db->sql_query($sql)) )
	       	{
	        	message_die(GENERAL_ERROR, 'Could Not Select Quartermile Data', '', __LINE__, __FILE__, $sql);
		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}

		$db->sql_freeresult($result);

		if (empty($data))
		{
			return;
		}
		return $data;
	}

	/*========================================================================*/
	// Build Quartermile Table With/Without Pending Itesm
	// Usage: build_quartermile_table('YES|NO');
	/*========================================================================*/
	function build_quartermile_table($pending)
	{
		global $db, $template, $images, $sort, $phpEx, $order, $garage_config, $garage_template, $user, $garage;

		$pending= ($pending == 'YES') ? 1 : 0;
		$start 	= (empty($start)) ? 0 : $start;
		$sort 	= (empty($sort)) ? 'quart' : $sort;

		// Sorting Via QuarterMile
		$sort_text = array($user->lang['RT'], $user->lang['SIXTY'], $user->lang['THREE'], $user->lang['EIGHTH'], $user->lang['EIGHTHMPH'], $user->lang['THOU'],  $user->lang['QUART'], $user->lang['QUARTMPH']);
		$sort_values = array('qm.rt', 'qm.sixty', 'qm.three', 'qm.eight', 'qm.eightmph', 'qm.thou', 'quart', 'qm.quartmph');

		//Get All Data Posted And Make It Safe To Use
		$addtional_where = '';
		$params = array('make_id', 'model_id');
		$data = $garage->process_post_vars($params);

		if (!empty($data['make_id']))
		{
			//Pull Required Data From DB
			$data = $garage_model->get_make($data['make_id']);
			$addtional_where .= "AND g.make_id = '$make_id'";
			$template->assign_vars(array(
				'MAKE'	=> $data['make'])
			);
		}

		if (!empty($model_id))
		{
			//Pull Required Data From DB
			$data = $garage_model->get_model($data['model_id']);
			$addtional_where .= "AND g.model_id = '$model_id'";
			$template->assign_vars(array(
				'MODEL'	=> $data['model'])
			);
		}

		//First Query To Return Top Time For All Or For Selected Filter...
		$sql = "SELECT  qm.garage_id, MIN(qm.quart) as quart
			FROM " . GARAGE_QUARTERMILE_TABLE ." AS qm
				LEFT JOIN " . GARAGE_TABLE ." AS g ON qm.garage_id = g.id
				LEFT JOIN " . USERS_TABLE ." AS user ON g.user_id = user.user_id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
        			LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
			WHERE	(qm.sixty IS NOT NULL
				OR qm.three IS NOT NULL
				OR qm.eight IS NOT NULL
				OR qm.eightmph IS NOT NULL
				OR qm.thou IS NOT NULL
				OR qm.rt IS NOT NULL
				OR qm.quartmph IS NOT NULL) AND ( qm.pending = $pending )
				AND ( makes.pending = 0 AND models.pending = 0 )
				$addtional_where 
			GROUP BY qm.garage_id
			ORDER BY $sort $order
			LIMIT $start, " . $garage_config['cars_per_page'];

		if( !($first_result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Selecting Top Quartermile Time', '', __LINE__, __FILE__, $sql);
		}

		if ( $pending == 1)
		{
			$template->assign_block_vars('quartermile_pending', array());
		}
	
		//Now Process All Rows Returned And Get Rest Of Required Data	
		$i = 0;
		while ($row = $db->sql_fetchrow($first_result) )
		{
			//Second Query To Return All Other Data For Top Quartermile Run
			$sql = "SELECT g.id, g.user_id, g.made_year, makes.make, models.model, user.username, qm.id as qmid,
				qm.rt, qm.sixty, qm.three, qm.eight, qm.eightmph, qm.thou, qm.quart, qm.quartmph, qm.rr_id,
				rr.bhp, rr.bhp_unit, rr.torque, rr.torque_unit, rr.boost, rr.boost_unit, rr.nitrous,
				CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, images.attach_id as image_id
				FROM " . GARAGE_QUARTERMILE_TABLE ." AS qm
					LEFT JOIN " . GARAGE_TABLE ." AS g ON qm.garage_id = g.id
					LEFT JOIN " . USERS_TABLE ." AS user ON g.user_id = user.user_id
					LEFT JOIN " . GARAGE_ROLLINGROAD_TABLE . " AS rr ON qm.rr_id = rr.id
				        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
        				LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
		                	LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = qm.image_id
				WHERE qm.garage_id = " . $row['garage_id'] . " AND qm.quart = " . $row['quart'];

			if( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Error Selecting Quartermile Time ', '', __LINE__, __FILE__, $sql);
			}
		
			$data = $db->sql_fetchrow($result);

			$temp_url = append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=$cid");
			$profile = '<a href="' . $temp_url . '">' . $lang['Read_profile'] . '</a>';

            		$temp_url = append_sid("garage.$phpEx?mode=edit_quartermile&amp;QMID=".$data['qmid']."&amp;CID=".$data['id']."&amp;PENDING=YES");
	            	$edit_link = '<a href="' . $temp_url . '"><img src="' . $images['garage_edit'] . '" alt="'.$lang['Edit'].'" title="'.$lang['Edit'].'" border="0" /></a>';

			$data['image_link'] ='';
			if ($data['image_id'])
			{
				$data['image_link'] ='<a href="garage.'. $phpEx .'?mode=view_gallery_item&amp;image_id='. $data['image_id'] .'" target="_blank"><img src="' . $images['slip_image_attached'] . '" alt="'.$lang['Slip_Image_Attached'].'" title="'.$lang['Slip_Image_Attached'].'" border="0" /></a>';
			}

			$assign_block = ($pending == 1) ? 'quartermile_pending.row' : 'memberrow';
			$template->assign_block_vars($assign_block, array(
				'ROW_NUMBER' 	=> $i + ( $start + 1 ),
				'QMID' 		=> $data['qmid'],
				'IMAGE_LINK' 	=> $data['image_link'],
				'USERNAME' 	=> $data['username'],
				'PROFILE' 	=> $profile, 
				'VEHICLE' 	=> $data['vehicle'],
				'RT' 		=> $data['rt'],
				'SIXTY' 	=> $data['sixty'],
				'THREE' 	=> $data['three'],
				'EIGTH' 	=> $data['eight'],
				'EIGHTM' 	=> $data['eightmph'],
				'THOU' 		=> $data['thou'],
				'QUART' 	=> $data['quart'],
				'QUARTM' 	=> $data['quartmph'],
				'BHP' 		=> $data['bhp'],
				'BHP_UNIT' 	=> $data['bhp_unit'],
				'TORQUE' 	=> $data['torque'],
				'TORQUE_UNIT' 	=> $data['torque_unit'],
				'BOOST' 	=> $data['boost'],
				'BOOST_UNIT' 	=> $data['boost_unit'],
				'NITROUS' 	=> $data['nitrous'],
				'EDIT_LINK' 	=> $edit_link,
				'U_VIEWVEHICLE' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$data['id']),
				'U_VIEWPROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=".$data['user_id']))
			);
			$i++;
		}
		$db->sql_freeresult($first_result);

		$sql = "SELECT COUNT(DISTINCT qm.garage_id)as total
			FROM " . GARAGE_QUARTERMILE_TABLE ." AS qm
				LEFT JOIN " . GARAGE_TABLE ." AS g ON qm.garage_id = g.id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
        			LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
			WHERE	(qm.sixty IS NOT NULL
				OR qm.three IS NOT NULL
				OR qm.eight IS NOT NULL
				OR qm.eightmph IS NOT NULL
				OR qm.thou IS NOT NULL
				OR qm.rt IS NOT NULL
				OR qm.quartmph IS NOT NULL) AND ( qm.pending = $pending )
				AND ( makes.pending = 0 AND models.pending = 0 )
				$addtional_where";

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Getting Pagination Total', '', __LINE__, __FILE__, $sql);
		}

		$count = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$pagination = generate_pagination("garage.$phpEx?mode=quartermile&amp;order=$sort", $count['total'], $garage_config['cars_per_page'], $start). '&nbsp;';
		
		$template->assign_vars(array(
			'S_MODE_SELECT' => $garage_template->dropdown('sort', $sort_text, $sort_values),
			'PAGINATION' => $pagination,
			'PAGE_NUMBER' => sprintf($user->lang['PAGE_OF'], ( floor( $start / $garage_config['cars_per_page'] ) + 1 ), ceil( $count['total'] / $garage_config['cars_per_page'] )))
		);

		//Reset Sort Order For Pending Page
		$sort='';
		return $count['total'];
	}
}

$garage_quartermile = new garage_quartermile();

?>

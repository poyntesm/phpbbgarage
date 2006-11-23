<?php
/***************************************************************************
 *                              class_garage_dynorun.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: class_garage_dynorun.php 156 2006-06-19 06:51:48Z poyntesm $
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

class garage_dynorun
{
	var $classname = "garage_dynorun";

	/*========================================================================*/
	// Inserts Dynorun Into DB
	// Usage: insert_dynorun(array());
	/*========================================================================*/
	function insert_dynorun($data)
	{
		global $cid, $db, $garage_config;

		$pending = ($garage_config['enable_rollingroad_approval'] == '1') ? 1 : 0;

		$sql = "INSERT INTO ". GARAGE_DYNORUN_TABLE ."
			(garage_id, dynocenter, bhp, bhp_unit, torque, torque_unit, boost, boost_unit, nitrous, peakpoint, date_created, date_updated, pending)
			VALUES
			('$cid', '".$data['dynocenter']."', '".$data['bhp']."', '".$data['bhp_unit']."', '".$data['torque']."', '".$data['torque_unit']."', '".$data['boost']."', '".$data['boost_unit']."', '".$data['nitrous']."', '".$data['peakpoint']."', '".time()."', '".time()."', '".$pending."')";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert Dynorun', '', __LINE__, __FILE__, $sql);
		}

		$id = $db->sql_nextid();

		return $id;
	}

	/*========================================================================*/
	// Updates Dynorun In DB
	// Usage:  update_dynorun(array());
	/*========================================================================*/
	function update_dynorun($data)
	{
		global $db, $rrid, $cid, $garage_config;

		$sql = "UPDATE " . GARAGE_DYNORUN_TABLE . "
			SET dynocenter = '" . $data['dynocenter'] . "', bhp = '" . $data['bhp'] . "', bhp_unit = '" . $data['bhp_unit'] . "', torque = '" . $data['torque'] . "', torque_unit = '" . $data['torque_unit'] . "', boost = '" . $data['boost'] . "', boost_unit = '" . $data['boost_unit'] . "', nitrous = '" . $data['nitrous'] . "', peakpoint = '" . $data['peakpoint'] . "', pending = '" . ($garage_config['enable_rollingroad_approval'] == '1') ? 1 : 0 . "', date_updated = '".time()."'
			WHERE id = '$rrid' and garage_id = '$cid'";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Update Dynorun', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Returns Count Of Dynoruns Performed By Vehicle
	// Usage: count_dynoruns('garage id');
	/*========================================================================*/
	function count_dynoruns($cid)
	{
		global $db;

		$sql = "SELECT count(id) AS total 
			FROM " . GARAGE_DYNORUN_TABLE . " 
			WHERE garage_id = $cid";

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Counting Dynoruns', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$row['total'] = (empty($row['total'])) ? 0 : $row['total'];

		return $row['total'];
	}

	/*========================================================================*/
	// Delete Dynorun Including Image 
	// Usage: delete_dynorun('dynorun id');
	/*========================================================================*/
	function delete_dynorun($rrid)
	{
		global $db, $garage_image, $garage;
	
		//Right They Want To Delete A Dynorun
		if (empty($rrid))
		{
	 		message_die(GENERAL_ERROR, 'Dynorun ID Not Entered', '', __LINE__, __FILE__);
		}
	
		//Get All Required Data
		$data = $this->get_dynorun($rrid);
	
		//Lets See If There Is An Image Associated With This Run
		if (!empty($data['image_id']))
		{
			if ( (!empty($data['attach_location'])) OR (!empty($data['attach_thumb_location'])) )
			{
				//Seems To Be An Image To Delete, Let Call The Function
				$garage_image->delete_image($data['image_id']);
			}
		}
	
		//Update Quartermile Table For An Matched Times
		$garage->update_single_field(GARAGE_QUARTERMILE_TABLE, 'rr_id', 'NULL', 'rr_id', $rrid);	
	
		//Time To Delete The Actual RollingRoad Run Now
		$garage->delete_rows(GARAGE_DYNORUN_TABLE, 'id', $rrid);
	
		return ;
	}
	
	/*========================================================================*/
	// Build Top Dyno Runs HTML If Required 
	// Usage: show_topdynorun();
	/*========================================================================*/
	function show_topdynorun()
	{
		global $required_position, $user, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		if ( $garage_config['topdynorun_on'] != true )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $user->lang['TOP_DYNO_RUNS'],
			'COLUMN_1_TITLE' => $user->lang['VEHICLE'],
			'COLUMN_2_TITLE' => $user->lang['OWNER'],
			'COLUMN_3_TITLE' => $user->lang['BHP-TORQUE-NITROUS'])
		);
	
	        // What's the count? Default to 10
		$limit = $garage_config['topdynorun_limit'] ? $garage_config['topdynorun_limit'] : 10;

		//Get Top Dynoruns
		$runs = $this->get_top_dynoruns(0, 'bhp', 'DESC', 0, $limit);
	
		//Now Process All Rows Returned And Get Rest Of Required Data	
		for($i = 0; $i < count($runs); $i++)
		{
			//Get Vehicle Info For This Dynorun
			$vehicle_data = $this->get_dynorun_by_vehicle_bhp($runs[$i]['garage_id'], $runs[$i]['bhp']);

			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' 	=> append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
				'U_COLUMN_2' 	=> append_sid("profile.$phpEx?mode=viewprofile&amp;u=".$vehicle_data['user_id']),
				'COLUMN_1_TITLE'=> $vehicle_data['vehicle'],
				'COLUMN_2_TITLE'=> $vehicle_data['username'],
				'COLUMN_3' 	=> $vehicle_data['bhp'] .' ' . $vehicle_data['bhp_unit'] . ' / ' . $vehicle_data['torque'] .' ' . $vehicle_data['torque_unit'] . ' / '. $vehicle_data['nitrous'])
			);
	 	}
	
		$required_position++;
		return ;
	}	

	/*========================================================================*/
	// Select All Dynorun Data From DB
	// Usage: get_dynorun('dynorun id');
	/*========================================================================*/
	function get_dynorun($rrid)
	{
		global $db;

	   	$sql = "SELECT rr.*, images.* , g.made_year, makes.make, models.model, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle
                    	FROM " . GARAGE_DYNORUN_TABLE . " rr
		          	LEFT JOIN " . GARAGE_TABLE . " g ON rr.garage_id = g.id
		          	LEFT JOIN " . GARAGE_MAKES_TABLE . " makes ON g.make_id = makes.id
                        	LEFT JOIN " . GARAGE_MODELS_TABLE . " models ON g.model_id = models.id
        			LEFT JOIN " . GARAGE_IMAGES_TABLE . " images ON images.attach_id = rr.image_id 
                    	WHERE rr.id = $rrid";

		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select Dynorun Data', '', __LINE__, __FILE__, $sql);
      		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (empty($row))
		{
			return;
		}

		return $row;
	}

	/*========================================================================*/
	// Select Dynorun Data From DB By Vehicle ID And BHP Value
	// Usage: get_dynorun_by_vehicle_bhp('garage id', 'bhp');
	/*========================================================================*/
	function get_dynorun_by_vehicle_bhp($garage_id, $bhp)
	{
		global $db;

		$sql = "SELECT g.id, g.made_year, g.user_id, makes.make, models.model, user.username,	rr.dynocenter, rr.bhp, rr.bhp_unit, rr.torque, rr.torque_unit, rr.boost, rr.boost_unit, rr.nitrous, round(rr.peakpoint,0) as peakpoint, images.attach_id as image_id, rr.id as rr_id, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle
			FROM " . GARAGE_DYNORUN_TABLE ." rr
				LEFT JOIN " . GARAGE_TABLE ." g ON rr.garage_id = g.id
				LEFT JOIN " . USERS_TABLE ." user ON g.user_id = user.user_id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " makes ON g.make_id = makes.id
        			LEFT JOIN " . GARAGE_MODELS_TABLE . " models ON g.model_id = models.id
	                	LEFT JOIN " . GARAGE_IMAGES_TABLE . " images ON images.attach_id = rr.image_id
			WHERE rr.garage_id = $garage_id AND rr.bhp = $bhp";

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Dynorun Data', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (empty($row))
		{
			return;
		}

		return $row;
	}

	/*========================================================================*/
	// Select Dynorun(s) Data By Vehicle From DB
	// Usage: get_dynoruns_by_vehicle('vehicle id');
	/*========================================================================*/
	function get_top_dynoruns($pending, $sort, $order, $start = 0, $limit = 30, $addtional_where = NULL)
	{
		global $db, $garage;

		$sql = "SELECT  rr.garage_id, MAX(rr.bhp) as bhp
			FROM " . GARAGE_DYNORUN_TABLE ." rr
				LEFT JOIN " . GARAGE_TABLE ." g ON rr.garage_id = g.id
				LEFT JOIN " . USERS_TABLE ." user ON g.user_id = user.user_id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " makes ON g.make_id = makes.id
        			LEFT JOIN " . GARAGE_MODELS_TABLE . " models ON g.model_id = models.id
			WHERE rr.pending = $pending 
				AND makes.pending = 0 AND models.pending = 0 
				$addtional_where 
			GROUP BY rr.garage_id
			ORDER BY $sort $order
			LIMIT $start, $limit";

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Dynoruns', '', __LINE__, __FILE__, $sql);
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
	// Select Dynorun(s) Data By Vehicle From DB
	// Usage: get_dynoruns_by_vehicle('vehicle id');
	/*========================================================================*/
	function get_dynoruns_by_vehicle($cid)
	{
		global $db;

	       	$sql = "SELECT d.*, i.*
         		FROM " . GARAGE_DYNORUN_TABLE . " d, " . GARAGE_IMAGES_TABLE . " i
			WHERE d.garage_id = $cid
				AND i.attach_id = d.image_id
			ORDER BY d.id";

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Dynorun By Vehicle', '', __LINE__, __FILE__, $sql);
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
	// Build Dynorun Table
	// Usage: build_dynorun_table('YES|NO');
	/*========================================================================*/
	function build_dynorun_table($pending)
	{
		global $db, $template, $images, $start, $sort, $order, $phpEx, $garage_config, $theme, $garage_model;

		$pending= ($pending == 'YES') ? 1 : 0;
		$start 	= (empty($start)) ? 0 : $start;
		$sort 	= (empty($sort)) ? 'bhp' : $sort;
		$sort_types_text = array($user->lang['DYNOCENTER'], $user->lang['BHP'], $user->lang['BHP_UNIT'], $user->lang['TORQUE'], $user->lang['TORQUE_UNIT'], $user->lang['BOOST'], $user->lang['BOOST_UNIT'], $user->lang['NITROUS'], $user->lang['PEAKPOINT']);
		$sort_types = array('rr.dynocenter', 'bhp', 'rr.bhp_unit, bhp', 'rr.torque', 'rr.torque_unit, rr.torque', 'rr.boost', 'rr.boost_unit, rr.boost', 'rr.nitrous', 'peakpoint');

		//Get All Data Posted And Make It Safe To Use
		$addtional_where = '';
		$params = array('make_id', 'model_id');
		$data = $garage->process_post_vars($params);

		//If Filtering By Make ID Get Make To Update Dropdown
		if (!empty($data['make_id']))
		{
			//Pull Required Data From DB
			$data = $garage_model->get_make($data['make_id']);
			$addtional_where .= "AND g.make_id = '" . $data['make_id'] . "'";
			$template->assign_vars(array(
				'MAKE'	=> $data['make'])
			);
		}

		//If Filtering By Model ID Get Model To Update Dropdown
		if (!empty($data['model_id']))
		{
			//Pull Required Data From DB
			$data = $garage_model->get_model($data['model_id']);
			$addtional_where .= "AND g.model_id = '" .$data['model_id'] . "'";
			$template->assign_vars(array(
				'MODEL'	=> $data['model'])
			);
		}

		if ($pending == 1)
		{
			$template->assign_block_vars('rollingroad_pending', array());
		}

		//First Query To Return Top Time For All Or For Selected Filter...
		$rows = $this->get_top_dynoruns($pending, $sort, $order, $start, $garage_config['cars_per_page'], $addtional_where);
		//Now Process All Rows Returned And Get Rest Of Required Data	
		for($i = 0; $i < count($rows); $i++)
		{
			//Second Query To Return All Other Data For Top Quartermile Run
			$full_row = $this->get_dynorun_by_vehicle_bhp($rows[$i]['garage_id'], $rows[$i]['bhp']);

			$full_row['image_link'] ='';
			if ($full_row['image_id'])
			{
				$full_row['image_link'] ='<a href="garage.' . $phpEx . '?mode=view_gallery_item&amp;image_id='. $full_row['image_id'] . '" target="_blank"><img src="' . $images['slip_image_attached'] . '" alt="' . $lang['Slip_Image_Attached'] . '" title="' . $lang['Slip_Image_Attached'] . '" border="0" /></a>';
			}

			$assign_block = ($pending == 1) ? 'rollingroad_pending.row' : 'memberrow';
			$template->assign_block_vars($assign_block, array(
				'U_VIEWVEHICLE'	=> append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=" . $full_row['id']),
				'U_VIEWPROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $data['user_id']),
				'U_EDIT_DYNORUN'=> append_sid("garage.$phpEx", "mode=edit_dynorun&amp;RRID=" . $full_row['rr_id'] . "&amp;CID=" . $full_row['id'] . "&amp;PENDING=YES"),
				'ROW_NUMBER' 	=> $i + ( $start + 1 ),
				'RRID' 		=> $full_row['rr_id'],
				'IMAGE_LINK' 	=> $full_row['image_link'],
				'USERNAME' 	=> $full_row['username'],
				'VEHICLE' 	=> $full_row['vehicle'],
				'DYNOCENTER' 	=> $full_row['dynocenter'],
				'BHP' 		=> $full_row['bhp'],
				'BHP_UNIT' 	=> $full_row['bhp_unit'],
				'TORQUE' 	=> $full_row['torque'],
				'TORQUE_UNIT' 	=> $full_row['torque_unit'],
				'BOOST' 	=> $full_row['boost'],
				'BOOST_UNIT' 	=> $full_row['boost_unit'],
				'NITROUS' 	=> $full_row['nitrous'],
				'PEAKPOINT' 	=> $full_row['peakpoint'],
				'EDIT_LINK' 	=> ($garage_config['enable_images']) ? $user->img('garage_edit', 'EDIT') : $user->lang['EDIT'])
			);
			$i++;
		}

		//Get All Top Dynoruns To Work Out Pagination
		$count = count($this->get_top_dynoruns($pending, $sort, $order, 0, 10000000, $addtional_where));
		$pagination = generate_pagination("garage.$phpEx?mode=dynorun&amp;order=$order", $count, $garage_config['cars_per_page'], $start);
		
		$template->assign_vars(array(
			'S_MODE_SELECT'	=> $garage_template->dropdown('sort', $sort_types_text, $sort_types, $sort),
			'S_DISPLAY_PENDING' => $pending,
			'PAGINATION' 	=> $pagination,
			'PAGE_NUMBER' 	=> sprintf($user->lang['PAGE_OF'], ( floor( $start / $garage_config['cars_per_page'] ) + 1 ), ceil( $count / $garage_config['cars_per_page'] )))
		);

		return $count['total'];
	}
}

$garage_dynorun = new garage_dynorun();

?>

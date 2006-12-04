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

		$sql = 'INSERT INTO ' . GARAGE_DYNORUN_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'garage_id'	=> $cid,
			'dynocenter'	=> $data['dynocenter'],
			'bhp'		=> $data['bhp'],
			'bhp_unit'	=> $data['bhp_unit'],
			'torque'	=> $data['torque'],
			'torque_unit'	=> $data['torque_unit'],
			'boost'		=> $data['boost'],
			'boost_unit'	=> $data['boost_unit'],
			'nitrous'	=> $data['nitrous'],
			'peakpoint'	=> $data['peakpoint'],
			'date_created'	=> time(),
			'date_updated'	=> time(),
			'pending'	=> ($garage_config['enable_dynorun_approval'] == '1') ? 1 : 0)
		);

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

		$update_sql = array(
			'garage_id'	=> $cid,
			'dynocenter'	=> $data['dynocenter'],
			'bhp'		=> $data['bhp'],
			'bhp_unit'	=> $data['bhp_unit'],
			'torque'	=> $data['torque'],
			'torque_unit'	=> $data['torque_unit'],
			'boost'		=> $data['boost'],
			'boost_unit'	=> $data['boost_unit'],
			'nitrous'	=> $data['nitrous'],
			'peakpoint'	=> $data['peakpoint'],
			'date_updated'	=> time(),
			'pending'	=> ($garage_config['enable_dynorun_approval'] == '1') ? 1 : 0
		);

		$sql = 'UPDATE ' . GARAGE_DYNORUN_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = $rrid AND garage_id = $cid";


		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Update Dynorun', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Delete Dynorun Including Image 
	// Usage: delete_dynorun('dynorun id');
	/*========================================================================*/
	function delete_dynorun($rrid)
	{
		global $db, $garage_image, $garage;
	
		//Get All Required Data
		$data = $this->get_dynorun($rrid);
	
		//Lets See If There Is An Image Associated With This Run
		if (!empty($data['image_id']))
		{
			//Seems To Be An Image To Delete, Let Call The Function
			$garage_image->delete_image($data['image_id']);
		}
	
		//Update Quartermile Table For An Matched Times
		$garage->update_single_field(GARAGE_QUARTERMILE_TABLE, 'rr_id', 'NULL', 'rr_id', $rrid);	
	
		//Time To Delete The Actual RollingRoad Run Now
		$garage->delete_rows(GARAGE_DYNORUN_TABLE, 'id', $rrid);
	
		return ;
	}

	/*========================================================================*/
	// Returns Count Of Dynoruns Performed By Vehicle
	// Usage: count_runs('garage id');
	/*========================================================================*/
	function count_runs($cid)
	{
		global $db;


		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(d.id) as total',
			'FROM'		=> array(
				GARAGE_DYNORUN_TABLE	=> 'd',
			),
			'WHERE'		=>  "d.garage_id = $cid"
		));

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
	// Select All Dynorun Data From DB
	// Usage: get_dynorun('dynorun id');
	/*========================================================================*/
	function get_dynorun($rrid)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.id, g.made_year, g.user_id, mk.make, md.model, d.dynocenter, d.bhp, d.bhp_unit, d.torque, d.torque_unit, d.boost, d.boost_unit, d.nitrous, d.peakpoint, i.attach_id as image_id, i.attach_file, d.id as rr_id, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle',

			'FROM'		=> array(
				GARAGE_DYNORUN_TABLE	=> 'd',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_TABLE => 'g'),
					'ON'	=> 'd.garage_id =g.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'mk'),
					'ON'	=> 'g.make_id = mk.id and mk.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'md'),
					'ON'	=> 'g.model_id = md.id and md.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = d.image_id'
				)
			),
			'WHERE'		=>  "d.id = $rrid"
		));

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
	// Select All Dynorun Data From DB
	// Usage: get_pending_dynoruns();
	/*========================================================================*/
	function get_pending_dynoruns()
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.id, g.made_year, g.user_id, mk.make, md.model, u.username, u.user_id, d.dynocenter, d.bhp, d.bhp_unit, d.torque, d.torque_unit, d.boost, d.boost_unit, d.nitrous, round(d.peakpoint,0) as peakpoint, i.attach_id as image_id, d.id as rr_id, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle',
			'FROM'		=> array(
				GARAGE_DYNORUN_TABLE	=> 'd',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_TABLE => 'g'),
					'ON'	=> 'd.garage_id =g.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'mk'),
					'ON'	=> 'g.make_id = mk.id and mk.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'md'),
					'ON'	=> 'g.model_id = md.id and md.pending = 0'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = d.image_id'
				)
			),
			'WHERE'		=>  "d.pending = 1"
		));

		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select Dynorun Data', '', __LINE__, __FILE__, $sql);
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
	// Select Dynorun Data From DB By Vehicle ID And BHP Value
	// Usage: get_dynorun_by_vehicle_bhp('garage id', 'bhp');
	/*========================================================================*/
	function get_dynorun_by_vehicle_bhp($garage_id, $bhp)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.id, g.made_year, g.user_id, mk.make, md.model, u.username, d.dynocenter, d.bhp, d.bhp_unit, d.torque, d.torque_unit, d.boost, d.boost_unit, d.nitrous, round(d.peakpoint,0) as peakpoint, i.attach_id as image_id, d.id as rr_id, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle',
			'FROM'		=> array(
				GARAGE_DYNORUN_TABLE	=> 'd',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_TABLE => 'g'),
					'ON'	=> 'd.garage_id =g.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'mk'),
					'ON'	=> 'g.make_id = mk.id and mk.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'md'),
					'ON'	=> 'g.model_id = md.id and md.pending = 0'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = d.image_id'
				)
			),
			'WHERE'		=>  "d.bhp = $bhp AND d.garage_id = $garage_id"
		));

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
	// Usage: get_top_dynoruns('vehicle id');
	/*========================================================================*/
	function get_top_dynoruns($pending, $sort, $order, $start = 0, $limit = 30, $addtional_where = NULL)
	{
		global $db, $garage;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'd.garage_id, MAX(d.bhp) as bhp',
			'FROM'		=> array(
				GARAGE_DYNORUN_TABLE	=> 'd',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_TABLE => 'g'),
					'ON'	=> 'd.garage_id =g.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'mk'),
					'ON'	=> 'g.make_id = mk.id and mk.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'md'),
					'ON'	=> 'g.model_id = md.id and md.pending = 0'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
				)
			),
			'WHERE'		=> "d.pending = $pending AND mk.pending = 0 AND md.pending = 0 $addtional_where ",
			'GROUP_BY'	=> 'd.garage_id',
			'ORDER_BY'	=> "$sort $order"
		));

		if( !($result = $db->sql_query_limit($sql, $limit, $start)) )
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

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'd.*, i.*',
			'FROM'		=> array(
				GARAGE_DYNORUN_TABLE	=> 'd',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = d.image_id'
				)
			),
			'WHERE'		=>	"d.garage_id = $cid",
			'ORDER_BY'	=>	'd.id'
		));

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
	// Build Top Dyno Runs HTML If Required 
	// Usage: show_topdynorun();
	/*========================================================================*/
	function show_topdynorun()
	{
		global $required_position, $user, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		if ( $garage_config['enable_top_dynorun'] != true )
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
		$limit = $garage_config['top_dynorun_limit'] ? $garage_config['top_dynorun_limit'] : 10;

		//Get Top Dynoruns
		$runs = $this->get_top_dynoruns(0, 'bhp', 'DESC', 0, $limit);
	
		//Now Process All Rows Returned And Get Rest Of Required Data	
		for($i = 0; $i < count($runs); $i++)
		{
			//Get Vehicle Info For This Dynorun
			$vehicle_data = $this->get_dynorun_by_vehicle_bhp($runs[$i]['garage_id'], $runs[$i]['bhp']);

			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;CID=".$vehicle_data['id']),
				'U_COLUMN_2' 	=> append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=".$vehicle_data['user_id']),
				'COLUMN_1_TITLE'=> $vehicle_data['vehicle'],
				'COLUMN_2_TITLE'=> $vehicle_data['username'],
				'COLUMN_3' 	=> $vehicle_data['bhp'] .' ' . $vehicle_data['bhp_unit'] . ' / ' . $vehicle_data['torque'] .' ' . $vehicle_data['torque_unit'] . ' / '. $vehicle_data['nitrous'])
			);
	 	}
	
		$required_position++;
		return ;
	}	

	/*========================================================================*/
	// Build Dynorun Table
	// Usage: build_dynorun_table('YES|NO');
	/*========================================================================*/
	function build_dynorun_table($pending)
	{
		global $db, $template, $start, $sort, $order, $phpEx, $garage_config, $garage_model, $user, $garage, $garage_template, $phpbb_root_path;

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

		//First Query To Return Top Time For All Or For Selected Filter...
		$rows = $this->get_top_dynoruns($pending, $sort, $order, $start, $garage_config['cars_per_page'], $addtional_where);

		if ($pending == 1 AND !empty($rows))
		{
			$template->assign_block_vars('dynorun_pending', array());
		}

		//Now Process All Rows Returned And Get Rest Of Required Data	
		for($i = 0; $i < count($rows); $i++)
		{
			//Second Query To Return All Other Data For Top Quartermile Run
			$full_row = $this->get_dynorun_by_vehicle_bhp($rows[$i]['garage_id'], $rows[$i]['bhp']);

			$assign_block = ($pending == 1) ? 'dynorun_pending.row' : 'dynorun';
			$template->assign_block_vars($assign_block, array(
				'U_VIEWVEHICLE'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;CID=" . $full_row['id']),
				'U_VIEWPROFILE' => append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=" . $full_row['user_id']),
				'U_EDIT'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=edit_dynorun&amp;RRID=" . $full_row['rr_id'] . "&amp;CID=" . $full_row['id'] . "&amp;PENDING=YES"),
				'U_IMAGE'	=> ($full_row['image_id']) ? append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_gallery_item&amp;image_id=". $full_row['image_id']) : '',
				'IMAGE'		=> $user->img('garage_slip_img_attached', 'SLIP_IMAGE_ATTACHED'),
				'ROW_NUMBER' 	=> $i + ( $start + 1 ),
				'RRID' 		=> $full_row['rr_id'],
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
				'PEAKPOINT' 	=> $full_row['peakpoint'])
			);
		}

		//Get All Top Dynoruns To Work Out Pagination
		$count = count($this->get_top_dynoruns($pending, $sort, $order, 0, 10000000, $addtional_where));
		$pagination = generate_pagination("garage.$phpEx?mode=dynorun&amp;order=$order", $count, $garage_config['cars_per_page'], $start);
		
		$template->assign_vars(array(
            		'EDIT' 			=> ($garage_config['enable_images']) ? $user->img('garage_edit', 'EDIT') : $user->lang['EDIT'],
			'S_MODE_SELECT'		=> $garage_template->dropdown('sort', $sort_types_text, $sort_types, $sort),
			'S_DISPLAY_PENDING' 	=> $pending,
			'PAGINATION' 		=> $pagination,
			'PAGE_NUMBER' 		=> sprintf($user->lang['PAGE_OF'], ( floor( $start / $garage_config['cars_per_page'] ) + 1 ), ceil( $count / $garage_config['cars_per_page'] )))
		);

		//Reset Sort Order For Pending Page
		$sort='';
		return $count;
	}
}

$garage_dynorun = new garage_dynorun();

?>

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

		$sql = 'INSERT INTO ' . GARAGE_DYNORUNS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'vehicle_id'	=> $cid,
			'dynocentre_id'	=> $data['dynocentre_id'],
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

		$db->sql_query($sql);

		return $db->sql_nextid();
	}

	/*========================================================================*/
	// Updates Dynorun In DB
	// Usage:  update_dynorun(array());
	/*========================================================================*/
	function update_dynorun($data)
	{
		global $db, $did, $cid, $garage_config;

		$update_sql = array(
			'vehicle_id'	=> $cid,
			'dynocentre_id'	=> $data['dynocentre_id'],
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

		$sql = 'UPDATE ' . GARAGE_DYNORUNS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = $did AND vehicle_id = $cid";


		$db->sql_query($sql);

		return;
	}

	/*========================================================================*/
	// Delete Dynorun Including Image 
	// Usage: delete_dynorun('dynorun id');
	/*========================================================================*/
	function delete_dynorun($id)
	{
		global $db, $garage_image, $garage;
	
		//Get All Required Data
		$data = $this->get_dynorun($id);
	
		//Lets See If There Is An Image Associated With This Run
		if (!empty($data['image_id']))
		{
			//Seems To Be An Image To Delete, Let Call The Function
			$garage_image->delete_image($data['image_id']);
		}
	
		//Update Quartermile Table For An Matched Times
		$garage->update_single_field(GARAGE_QUARTERMILES_TABLE, 'dynorun_id', 'NULL', 'dynorun_id', $id);	
	
		//Time To Delete The Actual RollingRoad Run Now
		$garage->delete_rows(GARAGE_DYNORUNS_TABLE, 'id', $id);
	
		return ;
	}

	/*========================================================================*/
	// Returns Count Of Dynoruns Performed By Vehicle
	// Usage: count_runs('garage id');
	/*========================================================================*/
	function count_runs($cid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(d.id) as total',
			'FROM'		=> array(
				GARAGE_DYNORUNS_TABLE	=> 'd',
			),
			'WHERE'		=>  "d.vehicle_id = $cid"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$data['total'] = (empty($data['total'])) ? 0 : $data['total'];

		return $data['total'];
	}

	/*========================================================================*/
	// Determines If Image Is Hilite Image
	// Usage: hilite_exists('dynorun id');
	/*========================================================================*/
	function hilite_exists($did)
	{
		$hilite = 1;

		if ($this->count_dynorun_images($did) > 0)
		{
			$hilite = 0;
		}
	
		return $hilite;
	}

	/*========================================================================*/
	// Returns Count Of Dynorun Images
	// Usage: count_dynorun_images('dynorun id');
	/*========================================================================*/
	function count_dynorun_images($did)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(dg.id) as total',
			'FROM'		=> array(
				GARAGE_DYNORUN_GALLERY_TABLE	=> 'dg',
			),
			'WHERE'		=> "dg.dynorun_id = $did"
		));

		$result = $db->sql_query($sql);
	        $data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$data['total'] = (empty($data['total'])) ? 0 : $data['total'];
		return $data['total'];
	}

	/*========================================================================*/
	// Select All Dynorun Data From DB
	// Usage: get_dynorun('dynorun id');
	/*========================================================================*/
	function get_dynorun($did)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'u.*, g.id, g.made_year, g.user_id, mk.make, md.model, d.bhp, d.bhp_unit, d.torque, d.torque_unit, d.boost, d.boost_unit, d.nitrous, d.peakpoint, i.attach_id as image_id, i.attach_file, d.id as did, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle, b.title, d.dynocentre_id',

			'FROM'		=> array(
				GARAGE_DYNORUNS_TABLE	=> 'd',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'd.vehicle_id =g.id'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
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
					'FROM'	=> array(GARAGE_DYNORUN_GALLERY_TABLE => 'dg'),
					'ON'	=> 'd.id = dg.dynorun_id'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = dg.image_id'
				)
				,array(
					'FROM'	=> array(GARAGE_BUSINESS_TABLE => 'b'),
					'ON'	=> 'd.dynocentre_id = b.id'
				)
			),
			'WHERE'		=>  "d.id = $did"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select All Dynorun Data From DB
	// Usage: get_pending_dynoruns();
	/*========================================================================*/
	function get_pending_dynoruns()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.id, g.made_year, g.user_id, mk.make, md.model, u.username, u.user_id, b.title, d.bhp, d.bhp_unit, d.torque, d.torque_unit, d.boost, d.boost_unit, d.nitrous, round(d.peakpoint,0) as peakpoint, i.attach_id as image_id, d.id as did, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle',
			'FROM'		=> array(
				GARAGE_DYNORUNS_TABLE	=> 'd',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'd.vehicle_id =g.id'
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
					'FROM'	=> array(GARAGE_DYNORUN_GALLERY_TABLE => 'dg'),
					'ON'	=> 'd.id = dg.dynorun_id'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = dg.image_id'
				)
				,array(
					'FROM'	=> array(GARAGE_BUSINESS_TABLE => 'b'),
					'ON'	=> 'd.dynocentre_id = b.id'
				)
			),
			'WHERE'		=>  "d.pending = 1"
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
	// Select Dynorun Data From DB By Vehicle ID And BHP Value
	// Usage: get_dynorun_by_vehicle_bhp('garage id', 'bhp');
	/*========================================================================*/
	function get_dynorun_by_vehicle_bhp($vehicle_id, $bhp)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.id, g.made_year, g.user_id, mk.make, md.model, u.username, b.title, d.bhp, d.bhp_unit, d.torque, d.torque_unit, d.boost, d.boost_unit, d.nitrous, round(d.peakpoint,0) as peakpoint, i.attach_id as image_id, d.id as did, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle',
			'FROM'		=> array(
				GARAGE_DYNORUNS_TABLE	=> 'd',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'd.vehicle_id =g.id'
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
					'FROM'	=> array(GARAGE_DYNORUN_GALLERY_TABLE => 'dg'),
					'ON'	=> 'g.id = dg.vehicle_id'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = dg.image_id'
				)
				,array(
					'FROM'	=> array(GARAGE_BUSINESS_TABLE => 'b'),
					'ON'	=> 'd.dynocentre_id = b.id'
				)
			),
			'WHERE'		=>  "d.bhp = $bhp AND d.vehicle_id = $vehicle_id"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select Dynorun(s) Data By Vehicle From DB
	// Usage: get_top_dynoruns('vehicle id');
	/*========================================================================*/
	function get_top_dynoruns($sort, $order, $start = 0, $limit = 30, $addtional_where = NULL)
	{
		global $db, $garage;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'd.vehicle_id, MAX(d.bhp) as bhp',
			'FROM'		=> array(
				GARAGE_DYNORUNS_TABLE	=> 'd',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'd.vehicle_id =g.id'
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
			'WHERE'		=> "d.pending = 0 AND mk.pending = 0 AND md.pending = 0 $addtional_where ",
			'GROUP_BY'	=> 'd.vehicle_id',
			'ORDER_BY'	=> "$sort $order"
		));

		$result = $db->sql_query_limit($sql, $limit, $start);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select Dynorun(s) Data By Vehicle From DB
	// Usage: get_dynoruns_by_vehicle('vehicle id');
	/*========================================================================*/
	function get_dynoruns_by_vehicle($cid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'd.*, d.id as did, i.*, b.title',
			'FROM'		=> array(
				GARAGE_DYNORUNS_TABLE	=> 'd',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_DYNORUN_GALLERY_TABLE => 'dg'),
					'ON'	=> 'd.id = dg.dynorun_id'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = dg.image_id'
				)
				,array(
					'FROM'	=> array(GARAGE_BUSINESS_TABLE => 'b'),
					'ON'	=> 'd.dynocentre_id = b.id'
				)
			),
			'WHERE'		=>	"d.vehicle_id = $cid",
			'ORDER_BY'	=>	'd.id'
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
	
		$limit = $garage_config['top_dynorun_limit'] ? $garage_config['top_dynorun_limit'] : 10;

		$runs = $this->get_top_dynoruns('bhp', 'DESC', 0, $limit);
	
		for($i = 0; $i < count($runs); $i++)
		{
			$vehicle_data = $this->get_dynorun_by_vehicle_bhp($runs[$i]['vehicle_id'], $runs[$i]['bhp']);

			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' 	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;CID=".$vehicle_data['id']),
				'U_COLUMN_2' 	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=".$vehicle_data['user_id']),
				'U_COLUMN_3' 	=> append_sid("{$phpbb_root_path}garage_dynorun.$phpEx", "mode=view_dynorun&amp;CID=".$vehicle_data['id']."&amp;DID=".$vehicle_data['did']),
				'COLUMN_1_TITLE'=> $vehicle_data['vehicle'],
				'COLUMN_2_TITLE'=> $vehicle_data['username'],
				'COLUMN_3_TITLE'=> $vehicle_data['bhp'] .' ' . $vehicle_data['bhp_unit'] . ' / ' . $vehicle_data['torque'] .' ' . $vehicle_data['torque_unit'] . ' / '. $vehicle_data['nitrous'])
			);
	 	}
	
		$required_position++;
		return ;
	}	

	/*========================================================================*/
	// Approve Dynoruns
	// Usage: approve_dynorun(array(), 'mode');
	/*========================================================================*/
	function approve_dynorun($id_list, $mode)
	{
		global $phpbb_root_path, $phpEx, $garage;

		for($i = 0; $i < count($id_list); $i++)
		{
			$garage->update_single_field(GARAGE_DYNORUNS_TABLE, 'pending', 0, 'id', $id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_dynoruns"));
	}

	/*========================================================================*/
	// Approve Dynoruns
	// Usage: approve_quartermile(array(), 'mode');
	/*========================================================================*/
	function disapprove_dynorun($id_list, $mode)
	{
		global $phpbb_root_path, $phpEx;

		for($i = 0; $i < count($id_list); $i++)
		{
			$this->delete_dynorun($id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_dynoruns"));
	}
}

$garage_dynorun = new garage_dynorun();

?>

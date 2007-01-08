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

		$sql = 'INSERT INTO ' . GARAGE_QUARTERMILES_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'garage_id'	=> $cid,
			'rt'		=> $data['rt'],
			'sixty'		=> $data['sixty'],
			'three'		=> $data['three'],
			'eighth'	=> $data['eighth'],
			'eighthmph'	=> $data['eighthmph'],
			'thou'		=> $data['thou'],
			'quart'		=> $data['quart'],
			'quartmph'	=> $data['quartmph'],
			'date_created'	=> time(),
			'date_updated'	=> time(),
			'rr_id'		=> $data['rr_id'],
			'pending'	=> ($garage_config['enable_quartermile_approval'] == '1') ? 1 : 0)
		);

		$db->sql_query($sql);

		return $db->sql_nextid();
	}

	/*========================================================================*/
	// Updates Quartermile In DB
	// Usage: update_quartermile(array());
	/*========================================================================*/
	function update_quartermile($data)
	{
		global $db, $cid, $qmid, $garage_config;

		$update_sql = array(
			'garage_id'	=> $cid,
			'rt'		=> $data['rt'],
			'sixty'		=> $data['sixty'],
			'three'		=> $data['three'],
			'eighth'	=> $data['eighth'],
			'eighthmph'	=> $data['eighthmph'],
			'thou'		=> $data['thou'],
			'quart'		=> $data['quart'],
			'quartmph'	=> $data['quartmph'],
			'date_updated'	=> time(),
			'rr_id'		=> $data['rr_id'],
			'pending'	=> ($garage_config['enable_quartermile_approval'] == '1') ? 1 : 0
		);

		$sql = 'UPDATE ' . GARAGE_QUARTERMILES_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = $qmid AND garage_id = $cid";

		$db->sql_query($sql);

		return;
	}

	/*========================================================================*/
	// Delete Quartermile Entry Including Image 
	// Usage: delete_quartermile('quartermile id');
	/*========================================================================*/
	function delete_quartermile($qmid)
	{
		global $garage, $garage_image;
	
		$data = $this->get_quartermile($qmid);
	
		if (!empty($data['image_id']))
		{
			$garage_image->delete_image($data['image_id']);
		}

		$garage->delete_rows(GARAGE_QUARTERMILES_TABLE, 'id', $qmid);

		return ;
	}

	/*========================================================================*/
	// Determines If Image Is Hilite Image
	// Usage: hilite_exists('quartermile id');
	/*========================================================================*/
	function hilite_exists($qmid)
	{
		$hilite = 1;

		if ($this->count_quartermile_images($qmid) > 0)
		{
			$hilite = 0;
		}
	
		return $hilite;
	}

	/*========================================================================*/
	// Returns Count Of Quartermile Images
	// Usage: count_quartermile_images('quartermile id');
	/*========================================================================*/
	function count_quartermile_images($qmid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(qg.id) as total',
			'FROM'		=> array(
				GARAGE_QUARTERMILE_GALLERY_TABLE	=> 'qg',
			),
			'WHERE'		=> "qg.quartermile_id = $qmid"
		));

		$result = $db->sql_query($sql);
	        $data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$data['total'] = (empty($data['total'])) ? 0 : $data['total'];
		return $data['total'];
	}

	/*========================================================================*/
	// Select Top Quartermiles Data By Vehicle From DB
	// Usage: get_top_quartermiles('vehicle id');
	/*========================================================================*/
	function get_top_quartermiles($sort, $order, $start = 0, $limit = 30, $addtional_where = NULL)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'q.garage_id, MIN(q.quart) as quart',
			'FROM'		=> array(
				GARAGE_QUARTERMILES_TABLE	=> 'q',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'q.garage_id =g.id'
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
			'WHERE'		=>  "(q.sixty IS NOT NULL OR q.three IS NOT NULL OR q.eighth IS NOT NULL OR q.eighthmph IS NOT NULL OR q.thou IS NOT NULL OR q.rt IS NOT NULL OR q.quartmph IS NOT NULL) AND ( q.pending = 0 ) AND ( mk.pending = 0 AND md.pending = 0 ) $addtional_where",
			'GROUP_BY'	=> 'q.garage_id',
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
	// Select Quartermile Data From DB By Vehicle ID And Quart Value
	// Usage: get_quartermile_by_vehicle_quart('garage id', 'quart');
	/*========================================================================*/
	function get_quartermile_by_vehicle_quart($garage_id, $quart)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.id, g.user_id, q.id as qmid, qg.image_id, u.username, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle, q.rt, q.sixty, q.three, q.eighth, q.eighthmph, q.thou, q.quart, q.quartmph, q.rr_id, d.bhp, d.bhp_unit, d.torque, d.torque_unit, d.boost, d.boost_unit, d.nitrous',
			'FROM'		=> array(
				GARAGE_QUARTERMILES_TABLE	=> 'q',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'q.garage_id =g.id'
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
					'FROM'	=> array(GARAGE_DYNORUNS_TABLE => 'd'),
					'ON'	=> 'q.rr_id = d.id'
				)
				,array(
					'FROM'	=> array(GARAGE_QUARTERMILE_GALLERY_TABLE => 'qg'),
					'ON'	=> 'q.id = qg.quartermile_id'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = qg.image_id'
				)
			),
			'WHERE'		=>  "q.quart = $quart AND q.garage_id = $garage_id"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select Quartermile Data From DB By Vehicle ID And Quart Value
	// Usage: get_quartermile_by_vehicle_quart('garage id', 'quart');
	/*========================================================================*/
	function get_pending_quartermiles()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.id as garage_id, u.user_id, g.user_id, q.id as qmid, qg.image_id, u.username, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle, q.rt, q.sixty, q.three, q.eighth, q.eighthmph, q.thou, q.quart, q.quartmph, q.rr_id',
			'FROM'		=> array(
				GARAGE_QUARTERMILES_TABLE	=> 'q',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'q.garage_id =g.id'
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
					'FROM'	=> array(GARAGE_QUARTERMILE_GALLERY_TABLE => 'qg'),
					'ON'	=> 'q.id = qg.quartermile_id'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = qg.image_id'
				)
			),
			'WHERE'		=>  "q.pending = 1"
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
	// Select Quartermile Data By Quartermile ID
	// Usage: get_quartermile('quartermile id');
	/*========================================================================*/
	function get_quartermile($qmid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'q.*, d.id, d.bhp, d.bhp_unit, i.*, g.made_year, mk.make, md.model, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle',
			'FROM'		=> array(
				GARAGE_QUARTERMILES_TABLE	=> 'q',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'q.garage_id =g.id'
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
					'FROM'	=> array(GARAGE_DYNORUNS_TABLE => 'd'),
					'ON'	=> 'q.rr_id = d.id'
				)
				,array(
					'FROM'	=> array(GARAGE_QUARTERMILE_GALLERY_TABLE => 'qg'),
					'ON'	=> 'q.id = qg.quartermile_id'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = qg.image_id'
				)
			),
			'WHERE'		=>  "q.id = $qmid"
		));

      		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select Quartermile Data By Vehicle ID
	// Usage: get_quartermile_by_vehicle('garage id');
	/*========================================================================*/
	function get_quartermile_by_vehicle($cid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'q.*, i.attach_id, i.attach_hits, i.attach_ext, i.attach_file, i.attach_thumb_location, i.attach_is_image, i.attach_location',
			'FROM'		=> array(
				GARAGE_QUARTERMILES_TABLE	=> 'q',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_QUARTERMILE_GALLERY_TABLE => 'qg'),
					'ON'	=> 'q.id = qg.quartermile_id'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = qg.image_id'
				)
			),
			'WHERE'		=> 	"q.garage_id = $cid",
			'ORDER_BY'	=>	'q.id'
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
	// Build Top Quartermile Runs HTML If Required 
	// Usage: show_topquartermile();
	/*========================================================================*/
	function show_topquartermile()
	{
		global $required_position, $user, $template, $db, $SID, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		if ( $garage_config['enable_top_quartermile'] != true )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE'	=> $user->lang['TOP_QUARTERMILE_RUNS'],
			'COLUMN_1_TITLE'=> $user->lang['VEHICLE'],
			'COLUMN_2_TITLE'=> $user->lang['OWNER'],
			'COLUMN_3_TITLE'=> $user->lang['QUARTERMILE'])
		);
	
		$limit = $garage_config['top_quartermile_limit'] ? $garage_config['top_quartermile_limit'] : 10;

		$times = $this->get_top_quartermiles('quart', 'DESC', 0, $limit);

		for($i = 0; $i < count($times); $i++)
		{
			$vehicle_data = $this->get_quartermile_by_vehicle_quart($times[$i]['garage_id'], $times[$i]['quart']);
	
			$mph = (empty($vehicle_data['quartmph'])) ? 'N/A' : $vehicle_data['quartmph'];
	            	$quartermile = $vehicle_data['quart'] .' @ ' . $mph . ' '. $user->lang['QUARTERMILE_SPEED_UNIT'];
	
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' 	=> append_sid("{$phpbb_root_path}garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
				'U_COLUMN_2' 	=> append_sid("{$phpbb_root_path}profile.$phpEx?mode=viewprofile&amp;u=".$vehicle_data['user_id']),
				'COLUMN_1_TITLE'=> $vehicle_data['vehicle'],
				'COLUMN_2_TITLE'=> $vehicle_data['username'],
				'COLUMN_3' 	=> $quartermile)
			);
	 	}
	
		$required_position++;
		return ;
	}

	/*========================================================================*/
	// Approve Quartermile Times
	// Usage: approve_quartermile(array(), 'mode');
	/*========================================================================*/
	function approve_quartermile($id_list, $mode)
	{
		global $phpbb_root_path, $phpEx, $garage;

		for($i = 0; $i < count($id_list); $i++)
		{
			$garage->update_single_field(GARAGE_QUARTERMILES_TABLE, 'pending', 0, 'id', $id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_quartermiles"));
	}

	/*========================================================================*/
	// Approve Quartermile Times
	// Usage: approve_quartermile(array(), 'mode');
	/*========================================================================*/
	function disapprove_quartermile($id_list, $mode)
	{
		global $phpbb_root_path, $phpEx;

		for($i = 0; $i < count($id_list); $i++)
		{
			$this->delete_quartermile($id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_quartermiles"));
	}
}

$garage_quartermile = new garage_quartermile();

?>

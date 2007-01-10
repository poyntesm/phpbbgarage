<?php
/***************************************************************************
 *                              class_garage_track.php
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

class garage_track
{
	var $classname = "garage_track";

	/*========================================================================*/
	// Inserts Lap Into DB
	// Usage: insert_lap(array());
	/*========================================================================*/
	function insert_lap($data)
	{
		global $cid, $db, $garage_config;

		$sql = 'INSERT INTO ' . GARAGE_LAPS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'garage_id'	=> $cid,
			'pending'	=> ($garage_config['enable_tracktime_approval'] == '1') ? 1 : 0)
		);

		$db->sql_query($sql);

		return $db->sql_nextid();
	}

	/*========================================================================*/
	// Updates Lap In DB
	// Usage: update_lap(array());
	/*========================================================================*/
	function update_lap($data)
	{
		global $db, $lid, $cid, $garage_config;

		$update_sql = array(
			'garage_id'	=> $cid,
			'pending'	=> ($garage_config['enable_tracktime_approval'] == '1') ? 1 : 0
		);

		$sql = 'UPDATE ' . GARAGE_DYNORUNS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = $lid AND garage_id = $cid";


		$db->sql_query($sql);

		return;
	}

	/*========================================================================*/
	// Delete Lap Including Image 
	// Usage: delete_lap('lap id');
	/*========================================================================*/
	function delete_lap($id)
	{
		global $db, $garage_image, $garage;
	
		//Get All Required Data
		$data = $this->get_lap($id);
	
		//Lets See If There Is An Image Associated With This Run
		if (!empty($data['image_id']))
		{
			//Seems To Be An Image To Delete, Let Call The Function
			$garage_image->delete_image($data['image_id']);
		}
	
		//Time To Delete The Actual RollingRoad Run Now
		$garage->delete_rows(GARAGE_LAPS_TABLE, 'id', $id);
	
		return ;
	}

	/*========================================================================*/
	// Determines If Image Is Hilite Image
	// Usage: hilite_exists('lap id');
	/*========================================================================*/
	function hilite_exists($lid)
	{
		$hilite = 1;

		if ($this->count_lap_images($lid) > 0)
		{
			$hilite = 0;
		}
	
		return $hilite;
	}

	/*========================================================================*/
	// Returns Count Of Lap Images
	// Usage: count_lap_images('lap id');
	/*========================================================================*/
	function count_lap_images($rrid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(lg.id) as total',
			'FROM'		=> array(
				GARAGE_LAP_GALLERY_TABLE	=> 'lg',
			),
			'WHERE'		=> "lg.lap_id = $lid"
		));

		$result = $db->sql_query($sql);
	        $data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$data['total'] = (empty($data['total'])) ? 0 : $data['total'];
		return $data['total'];
	}

	/*========================================================================*/
	// Select All Lap Data From DB
	// Usage: get_lap('lap id');
	/*========================================================================*/
	function get_lap($lid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'u.*, g.id, g.made_year, g.user_id, mk.make, md.model, l.*, i.attach_id as image_id, i.attach_file, l.id as lid, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle, t.title',

			'FROM'		=> array(
				GARAGE_LAPS_TABLE	=> 'l',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'l.garage_id =g.id'
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
					'FROM'	=> array(GARAGE_LAPS_GALLERY_TABLE => 'lg'),
					'ON'	=> 'l.id = lg.lap_id'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = lg.image_id'
				)
				,array(
					'FROM'	=> array(GARAGE_TRACKS_TABLE => 't'),
					'ON'	=> 'l.track_id = t.id'
				)
			),
			'WHERE'		=>  "l.id = $lid"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select All Pending Laps Data From DB
	// Usage: get_pending_laps();
	/*========================================================================*/
	function get_pending_laps()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.id, g.made_year, g.user_id, mk.make, md.model, u.username, u.user_id, t.title, l.*, i.attach_id as image_id, l.id as lid, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle',
			'FROM'		=> array(
				GARAGE_LAPS_TABLE	=> 'l',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'l.garage_id =g.id'
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
					'FROM'	=> array(GARAGE_LAP_GALLERY_TABLE => 'lg'),
					'ON'	=> 'l.id = lg.dynorun_id'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = lg.image_id'
				)
				,array(
					'FROM'	=> array(GARAGE_TRACKS_TABLE => 't'),
					'ON'	=> 'l.track_id = t.id'
				)
			),
			'WHERE'		=>  "l.pending = 1"
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
	// Select Lap(s) Data By Vehicle From DB
	// Usage: get_laps_by_vehicle('vehicle id');
	/*========================================================================*/
	function get_laps_by_vehicle($cid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'l.*, l.id as lid, i.*, t.title',
			'FROM'		=> array(
				GARAGE_LAPS_TABLE	=> 'l',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_LAP_GALLERY_TABLE => 'lg'),
					'ON'	=> 'l.id = lg.lap_id'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = lg.image_id'
				)
				,array(
					'FROM'	=> array(GARAGE_TRACKS_TABLE => 't'),
					'ON'	=> 'l.track_id = t.id'
				)
			),
			'WHERE'		=>	"l.garage_id = $cid",
			'ORDER_BY'	=>	'l.id'
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
	// Approve Laps
	// Usage: approve_lap(array(), 'mode');
	/*========================================================================*/
	function approve_lap($id_list, $mode)
	{
		global $phpbb_root_path, $phpEx, $garage;

		for($i = 0; $i < count($id_list); $i++)
		{
			$garage->update_single_field(GARAGE_LAPS_TABLE, 'pending', 0, 'id', $id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_laps"));
	}

	/*========================================================================*/
	// Disapprove Laps
	// Usage: disapprove_lap(array(), 'mode');
	/*========================================================================*/
	function disapprove_lap($id_list, $mode)
	{
		global $phpbb_root_path, $phpEx;

		for($i = 0; $i < count($id_list); $i++)
		{
			$this->delete_lap($id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_laps"));
	}
}

$garage_track = new garage_track();

?>

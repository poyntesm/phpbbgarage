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
			'vehicle_id'	=> $cid,
			'track_id'	=> $data['track_id'],
			'condition_id'	=> $data['condition_id'],
			'type_id'	=> $data['type_id'],
			'minute'	=> $data['minute'],
			'second'	=> $data['second'],
			'millisecond'	=> $data['millisecond'],
			'pending'	=> ($garage_config['enable_lap_approval'] == '1') ? 1 : 0)
		);

		$db->sql_query($sql);

		return $db->sql_nextid();
	}

	/*========================================================================*/
	// Inserts Track Into DB
	// Usage: insert_track(array());
	/*========================================================================*/
	function insert_track($data)
	{
		global $db, $garage_config;

		$sql = 'INSERT INTO ' . GARAGE_TRACKS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'title' 	=> $data['title'],
			'length'	=> $data['length'],
			'mileage_unit'	=> $data['mileage_unit'],
			'pending'	=> ($garage_config['enable_track_approval'] == '1') ? 1 : 0)
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
			'vehicle_id'	=> $cid,
			'track_id'	=> $data['track_id'],
			'condition_id'	=> $data['condition_id'],
			'type_id'	=> $data['type_id'],
			'minute'	=> $data['minute'],
			'second'	=> $data['second'],
			'millisecond'	=> $data['millisecond'],
			'pending'	=> ($garage_config['enable_lap_approval'] == '1') ? 1 : 0
		);

		$sql = 'UPDATE ' . GARAGE_LAPS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = $lid AND vehicle_id = $cid";


		$db->sql_query($sql);

		return;
	}

	/*========================================================================*/
	// Updates Track In DB
	// Usage: update_track(array());
	/*========================================================================*/
	function update_track($data)
	{
		global $db, $tid, $garage_config;

		$update_sql = array(
			'title' 	=> $data['title'],
			'length'	=> $data['length'],
			'mileage_unit'	=> $data['mileage_unit'],
			'pending'	=> ($garage_config['enable_track_approval'] == '1') ? 1 : 0
		);

		$sql = 'UPDATE ' . GARAGE_TRACKS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = $tid";


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
	
		//Seems To Be An Image To Delete, Let Call The Function
		$garage_image->delete_image($data['image_id']);

		//Time To Delete The Actual Lap Now
		$garage->delete_rows(GARAGE_LAPS_TABLE, 'id', $id);
	
		return ;
	}

	/*========================================================================*/
	// Delete Track Including All Lap Times 
	// Usage: delete_track('track id');
	/*========================================================================*/
	function delete_track($id)
	{
		global $db, $garage_image, $garage;
	
		//Get All Laps
		$data = $this->get_laps_by_track($id);
	
		//Lets See If There Is An Image Associated With This Run
		for($i = 0; $i < count($data); $i++)
		{
			//Delete Lap
			$this->delete_lap($data[$i]['id']);
		}
	
		//Time To Delete The Actual Track Now
		$garage->delete_rows(GARAGE_TRACKS_TABLE, 'id', $tid);
	
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
	function count_lap_images($lid)
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
					'ON'	=> 'l.vehicle_id =g.id'
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
			'WHERE'		=>  "l.id = $lid"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select Track Data From DB
	// Usage: get_track('track id');
	/*========================================================================*/
	function get_track($tid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 't.*',

			'FROM'		=> array(
				GARAGE_TRACKS_TABLE	=> 't',
			),
			'WHERE'		=>  "t.id = $tid"
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
			'SELECT'	=> 'g.id, g.made_year, g.user_id, mk.make, md.model, u.username, u.user_id, t.title, l.*, i.attach_id, l.id as lid, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle',
			'FROM'		=> array(
				GARAGE_LAPS_TABLE	=> 'l',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'l.vehicle_id =g.id'
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
			'WHERE'		=>	"l.vehicle_id = $cid",
			'GROUP_BY'	=>	'l.id',
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
	// Select Lap(s) Data By Track From DB
	// Usage: get_laps_by_track('track id');
	/*========================================================================*/
	function get_laps_by_track($tid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'l.*, l.id as lid, i.*, t.title, u.username, u.user_id, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle, g.id as vehicle_id, u.user_colour',
			'FROM'		=> array(
				GARAGE_LAPS_TABLE	=> 'l',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'l.vehicle_id =g.id'
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
					'ON'	=> 'l.id = lg.lap_id and lg.hilite = 1'
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
			'WHERE'		=>	"l.track_id = $tid",
			'ORDER_BY'	=>	'l.track_id, l.minute, l.second, l.millisecond'
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
	// Select All Tracks Data From DB
	// Usage: get_all_tracks();
	/*========================================================================*/
	function get_all_tracks()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 't.*',
			'FROM'		=> array(
				GARAGE_TRACKS_TABLE	=> 't',
			)
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
	// Select Pending Tracks Data From DB
	// Usage: get_pending_tracks();
	/*========================================================================*/
	function get_pending_tracks()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 't.*',
			'FROM'		=> array(
				GARAGE_TRACKS_TABLE	=> 't',
			),
			'WHERE'		=> 't.pending = 1'
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
	// Returns Lang String For Condition
	// Usage: get_track_condition();
	/*========================================================================*/
	function get_track_condition($id)
	{
		global $user;

		if ($id == TRACK_DRY)
		{
			return $user->lang['DRY'];
		}
		else if ($id == TRACK_INTERMEDIATE)
		{
			return $user->lang['INTERMEDIATE'];
		}
		else if ($id == TRACK_WET)
		{
			return $user->lang['WET'];
		}
	}

	/*========================================================================*/
	// Returns Lang String For Condition
	// Usage: get_lap_type();
	/*========================================================================*/
	function get_lap_type($id)
	{
		global $user;

		if ($id == LAP_QUALIFING)
		{
			return $user->lang['QUALIFING'];
		}
		else if ($id == LAP_RACE)
		{
			return $user->lang['RACE'];
		}
		else if ($id == LAP_TRACKDAY)
		{
			return $user->lang['TRACKDAY'];
		}
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
	// Approve Track
	// Usage: approve_track(array(), 'mode');
	/*========================================================================*/
	function approve_track($id_list, $mode)
	{
		global $phpbb_root_path, $phpEx, $garage;

		for($i = 0; $i < count($id_list); $i++)
		{
			$garage->update_single_field(GARAGE_TRACKS_TABLE, 'pending', 0, 'id', $id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_tracks"));
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

	/*========================================================================*/
	// Disapprove Tracks
	// Usage: disapprove_track(array(), 'mode');
	/*========================================================================*/
	function disapprove_track($id_list, $mode)
	{
		global $phpbb_root_path, $phpEx;

		for($i = 0; $i < count($id_list); $i++)
		{
			$this->delete_track($id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_tracks"));
	}
}

$garage_track = new garage_track();

?>

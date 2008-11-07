<?php
/** 
*
* @package garage
* @version $Id$
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
*/
if (!defined('IN_PHPBB'))
{
	die('Hacking attempt');
}

/**
* phpBB Garage Track Class
* @package garage
*/
class garage_track
{
	var $classname = "garage_track";

	/**
	* Insert new lap
	*
	* @param array $data single-dimension array holding the data for the new lap
	*
	*/
	function insert_lap($data)
	{
		global $vid, $db, $garage_config;

		$sql = 'INSERT INTO ' . GARAGE_LAPS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'vehicle_id'	=> $vid,
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

	/**
	* Insert new track
	*
	* @param array $data single-dimension array holding the data for the new track
	*
	*/
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

	/**
	* Updates a existing lap
	*
	* @param array $data single-dimension array holding the data to update the lap with
	*
	*/
	function update_lap($data)
	{
		global $db, $lid, $vid, $garage_config;

		$update_sql = array(
			'vehicle_id'	=> $vid,
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
			WHERE id = $lid AND vehicle_id = $vid";


		$db->sql_query($sql);

		return;
	}

	/**
	* Updates a existing track
	*
	* @param array $data single-dimension array holding the data to update the track with
	*
	*/
	function update_track($data)
	{
		global $db, $garage_config;

		$update_sql = array(
			'title' 	=> $data['title'],
			'length'	=> $data['length'],
			'mileage_unit'	=> $data['mileage_unit'],
			'pending'	=> ($garage_config['enable_track_approval'] == '1') ? 1 : 0
		);

		$sql = 'UPDATE ' . GARAGE_TRACKS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = " . $data['id'];


		$db->sql_query($sql);

		return;
	}

	/**
	* Delete lap and all images linked to it
	*
	* @param int $lid lap id to delete
	*
	*/
	function delete_lap($lid)
	{
		global $vid, $garage_image, $garage;
	
		$images	= $garage_image->get_lap_gallery($lid);
	
		for ($i = 0, $count = sizeof($images);$i < $count; $i++)
		{
			$garage_image->delete_lap_image($images[$i]['image_id']);
		}

		$garage->delete_rows(GARAGE_LAPS_TABLE, 'id', $lid);
	
		return ;
	}

	/**
	* Check if an image is marked as highlight image for lap
	*
	* @param int $lid lap id to check
	*
	*/
	function hilite_exists($lid)
	{
		$hilite = 1;

		if ($this->count_lap_images($lid) > 0)
		{
			$hilite = 0;
		}
	
		return $hilite;
	}

	/**
	* Count images linked to a lap
	*
	* @param int $lid lap id to count images for
	*
	*/
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

	/**
	* Return vehicle id of lap
	*
	* @param int $lid lap id to get vehicle id for
	*
	*/
	function get_vehicle_id_for_lap($lid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'v.id',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'v',
				GARAGE_LAPS_TABLE	=> 'l',
			),
			'WHERE'		=>  "l.id = $lid
						AND v.id = l.vehicle_id"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data['id'];
	}

	/**
	* Return data for a specific lap
	*
	* @param int $lid lap id to return data for
	*
	*/
	function get_lap($lid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'u.*, v.id, v.made_year, v.user_id, mk.make, md.model, l.*, i.attach_id as image_id, i.attach_file, l.id as lid, v.made_year, mk.make, md.model, t.title',

			'FROM'		=> array(
				GARAGE_LAPS_TABLE	=> 'l',
				GARAGE_TRACKS_TABLE	=> 't',
				GARAGE_VEHICLES_TABLE	=> 'v',
				GARAGE_MAKES_TABLE	=> 'mk',
				GARAGE_MODELS_TABLE	=> 'md',
				USERS_TABLE		=> 'u',
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
			),
			'WHERE'		=>  "l.id = $lid
						AND l.track_id = t.id
						AND l.vehicle_id = v.id
						AND v.user_id = u.user_id
						AND v.make_id = mk.id
						AND v.model_id = md.id"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		if (!empty($data))
		{
			$data['vehicle'] = "{$data['made_year']} {$data['make']} {$data['model']}";
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return data for a specific track
	*
	* @param int $tid track id to return data for
	*
	*/	
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

	/**
	* Return array for all pending laps
	*/
	function get_pending_laps()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'v.id, v.made_year, v.user_id, mk.make, md.model, u.username, u.user_id, u.user_colour, t.title, l.*, i.attach_id, l.id as lid, v.made_year, mk.make, md.model',
			'FROM'		=> array(
				GARAGE_LAPS_TABLE	=> 'l',
				GARAGE_TRACKS_TABLE	=> 't',
				GARAGE_VEHICLES_TABLE	=> 'v',
				GARAGE_MAKES_TABLE	=> 'mk',
				GARAGE_MODELS_TABLE	=> 'md',
				USERS_TABLE		=> 'u',
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
			),
			'WHERE'		=>  "l.pending = 1
						AND l.track_id = t.id
						AND l.vehicle_id = v.id
						AND v.user_id = u.user_id
						AND v.make_id = mk.id
						AND v.model_id = md.id"
		));

		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			if (!empty($row))
			{
				$row['vehicle'] = "{$row['made_year']} {$row['make']} {$row['model']}";
			}
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array holding all laps for a specific vehicle
	*
	* @param int $vid vehicle id to return laps for
	*
	*/
	function get_laps_by_vehicle($vid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'l.*, l.id as lid, i.*, t.title',
			'FROM'		=> array(
				GARAGE_LAPS_TABLE	=> 'l',
				GARAGE_TRACKS_TABLE	=> 't',
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
			),
			'WHERE'		=> "l.vehicle_id = $vid
						AND l.track_id = t.id",
			'GROUP_BY'	=> 'l.id',
			'ORDER_BY'	=> 'l.id'
		));

		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array holding all laps for a specific track
	*
	* @param int $tid track id to return laps for
	*
	*/
	function get_laps_by_track($tid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'l.*, l.id as lid, i.*, t.title, u.username, u.user_id, v.made_year, mk.make, md.model, v.id as vehicle_id, u.user_colour',
			'FROM'		=> array(
				GARAGE_LAPS_TABLE	=> 'l',
				GARAGE_TRACKS_TABLE	=> 't',
				GARAGE_VEHICLES_TABLE	=> 'v',
				GARAGE_MAKES_TABLE	=> 'mk',
				GARAGE_MODELS_TABLE	=> 'md',
				USERS_TABLE		=> 'u',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_LAP_GALLERY_TABLE => 'lg'),
					'ON'	=> 'l.id = lg.lap_id and lg.hilite = 1'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = lg.image_id'
				)
			),
			'WHERE'		=> "l.track_id = $tid
						AND l.track_id = t.id
						AND l.vehicle_id = v.id
						AND v.user_id = u.user_id
						AND v.make_id = mk.id AND mk.pending = 0
						AND v.model_id = md.id AND md.pending = 0",
			'ORDER_BY'	=>	'l.track_id, l.minute, l.second, l.millisecond'
		));

		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			if (!empty($row))
			{
				$row['vehicle'] = "{$row['made_year']} {$row['make']} {$row['model']}";
			}
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}


	/**
	* Return array of top laps
	*
	* @param int $limit number of rows to return
	*
	*/
	function get_top_laps($limit = 30)
	{
		global $db, $garage;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'l.*, l.id as lid, t.title, t.id as tid, v.*, u.username, u.user_id, v.made_year, mk.make, md.model, v.id as vid, u.user_colour',
			'FROM'		=> array(
				GARAGE_LAPS_TABLE	=> 'l',
				GARAGE_TRACKS_TABLE	=> 't',
				GARAGE_VEHICLES_TABLE	=> 'v',
				GARAGE_MAKES_TABLE	=> 'mk',
				GARAGE_MODELS_TABLE	=> 'md',
				USERS_TABLE		=> 'u',
			),
			'WHERE'		=> "l.pending = 0
						AND l.track_id = t.id
						AND l.vehicle_id = v.id
						AND v.make_id = mk.id AND mk.pending =0
						AND v.model_id = md.id and md.pending = 0
						AND v.user_id = u.user_id",
			'GROUP_BY'	=> 't.id',
			'ORDER_BY'	=> "l.minute DESC"
		));

		$result = $db->sql_query_limit($sql, $limit, 0);
		while ($row = $db->sql_fetchrow($result))
		{
			if (!empty($row))
			{
				$row['vehicle'] = "{$row['made_year']} {$row['make']} {$row['model']}";
			}
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array holding data for all tracks
	*/
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

	/**
	* Return array for all pending tracks
	*/
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

	/**
	* Return language string for track condition id
	*
	* @param TRACK_DRY|TRACK_INTERMEDIATE|TRACK_WET
	*
	*/
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

	/**
	* Return language string for lap type id
	*
	* @param LAP_QUALIFING|LAP_RACE|LAP_TRACKDAY
	*
	*/
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

	/**
	* Delete track and all laps linked to it
	*
	* @param int $tid track id to delete
	* @param delete|move 
	* @param int $laps_to_id track id to move laps to
	*
	*/
	function delete_track($track_id, $action_laps = 'delete', $laps_to_id = 0)
	{

		global $db, $user, $cache, $garage;

		$track_data = $this->get_track($track_id);

		$errors = array();

		//Handle Items Linked To Garage Business
		if ($action_garage == 'delete')
		{
			$this->delete_garage_track_content($track_id);
			add_log('admin', 'LOG_GARAGE_TRACK_DELETE_LAPS', $track_data['title']);
		}
		else if ($action_garage == 'move')
		{
			if (!$laps_to_id)
			{
				$errors[] = $user->lang['NO_DESTINATION_GARAGE_TRACK'];
			}
			else
			{
				$row = $this->get_track($laps_to_id);

				if (!$row)
				{
					$errors[] = $user->lang['NO_TRACK'];
				}
				else
				{
					$to_name = $row['title'];
					$from_name = $track_data['title'];
					$this->move_category_content($track_id, $laps_to_id);
					add_log('admin', 'LOG_GARAGE_TRACK_MOVE_LAPS', $from_name, $to_name);
				}
			}
		}

		$garage->delete_rows(GARAGE_TRACKS_TABLE, 'id', $track_id);
		add_log('admin', 'LOG_GARAGE_TRACK_DELETED', $track_data['title']);

		if (sizeof($errors))
		{
			return $errors;
		}
	}

	/**
	* Delete all laps linked to a track
	*
	* @param int $track_id track id to delete laps for
	*
	*/
	function delete_garage_track_content($track_id)
	{
		global $db, $config, $phpbb_root_path, $phpEx, $garage;

		$laps = $garage_modification->get_laps_by_track($track_id);
		for ($i = 0, $count = sizeof($laps);$i < $count; $i++)
		{
			$garage_modification->delete_lap($laps[$i]['id']);
		}

		return;
	}

	/**
	* Reassign laps to track
	*
	* @param int $from_id track id to move from
	* @param int $to_id track id to move to
	*
	*/
	function move_track_content($from_id, $to_id)
	{
		global $garage;

		$garage->update_single_field(GARAGE_LAPS_TABLE, 'track_id', $to_id, 'track_id', $from_id);

		return;
	}

	/**
	* Assign template variables to display top laps
	*/
	function show_toplap()
	{
		global $required_position, $user, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		if ( $garage_config['enable_top_lap'] != true )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $user->lang['TOP_LAPS'],
			'COLUMN_1_TITLE' => $user->lang['VEHICLE'],
			'COLUMN_2_TITLE' => $user->lang['TRACK'],
			'COLUMN_3_TITLE' => $user->lang['LAP_TIME'])
		);
	
		$limit = $garage_config['top_lap_limit'] ? $garage_config['top_lap_limit'] : 10;

		$laps = $this->get_top_laps($limit);
	
		for($i = 0; $i < count($laps); $i++)
		{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' 		=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;VID=".$laps[$i]['vid']),
				'U_COLUMN_2' 		=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=view_track&amp;VID=".$laps[$i]['vid']."&amp;TID=".$laps[$i]['tid']),
				'U_COLUMN_3' 		=> append_sid("{$phpbb_root_path}garage_track.$phpEx", "mode=view_lap&amp;VID=".$laps[$i]['vid']."&amp;LID=".$laps[$i]['lid']),
				'COLUMN_1_TITLE'	=> $laps[$i]['vehicle'],
				'COLUMN_2_TITLE'	=> $laps[$i]['title'],
				'COLUMN_3_TITLE'	=> $laps[$i]['minute'] .':' . $laps[$i]['second'] . ':' . $laps[$i]['millisecond'],
				'USERNAME_COLOUR'	=> get_username_string('colour', $laps[$i]['user_id'], $laps[$i]['username'], $laps[$i]['user_colour']),
			));
	 	}
	
		$required_position++;
		return ;
	}
}

$garage_track = new garage_track();

?>

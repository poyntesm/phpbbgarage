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

	/**
	* Delete lap and all images linked to it
	*
	* @param int $lid lap id to delete
	*
	*/
	function delete_lap($lid)
	{
		global $vid, $garage_image, $garage;
	
		//Lets See If There Are Any Images Associated With This Lap
		$images	= $garage_image->get_lap_gallery($vid, $lid);
	
		for ($i = 0, $count = sizeof($images);$i < $count; $i++)
		{
			$garage_image->delete_lap_image($images[$i]['id']);
		}

		//Time To Delete The Actual Lap Now
		$garage->delete_rows(GARAGE_LAPS_TABLE, 'id', $lid);
	
		return ;
	}

	/**
	* Delete track and all laps linked to it
	*
	* @param int $tid track id to delete
	*
	*/	
	function delete_track($tid)
	{
		global $db, $garage_image, $garage;
	
		//Get All Laps
		$data = $this->get_laps_by_track($tid);
	
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
			'WHERE'		=>	"l.vehicle_id = $vid",
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
	* Approve laps
	*
	* @param array $id_list sigle-dimension array holding the lap ids to disapprove
	*
	*/
	function approve_lap($id_list)
	{
		global $phpbb_root_path, $phpEx, $garage;

		for($i = 0; $i < count($id_list); $i++)
		{
			$garage->update_single_field(GARAGE_LAPS_TABLE, 'pending', 0, 'id', $id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_laps"));
	}

	/**
	* Approve tracks
	*
	* @param array $id_list sigle-dimension array holding the track ids to disapprove
	*
	*/
	function approve_track($id_list)
	{
		global $phpbb_root_path, $phpEx, $garage;

		for($i = 0; $i < count($id_list); $i++)
		{
			$garage->update_single_field(GARAGE_TRACKS_TABLE, 'pending', 0, 'id', $id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_tracks"));
	}

	/**
	* Disapprove laps
	*
	* @param array $id_list sigle-dimension array holding the lap ids to disapprove
	*
	*/
	function disapprove_lap($id_list)
	{
		global $phpbb_root_path, $phpEx;

		for($i = 0; $i < count($id_list); $i++)
		{
			$this->delete_lap($id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_laps"));
	}

	/**
	* Disapprove tracks
	*
	* @param array $id_list sigle-dimension array holding the track ids to disapprove
	*
	*/
	function disapprove_track($id_list)
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

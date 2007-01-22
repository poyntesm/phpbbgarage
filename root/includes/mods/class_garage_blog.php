<?php
/***************************************************************************
 *                              class_garage_blog.php
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

class garage_blog
{
	var $classname = "garage_blog";

	/*========================================================================*/
	// Inserts Lap Into DB
	// Usage: insert_lap(array());
	/*========================================================================*/
	function insert_blog($data)
	{
		global $cid, $db, $garage_config;

		$sql = 'INSERT INTO ' . GARAGE_LAPS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'garage_id'	=> $cid,
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
	// Updates Lap In DB
	// Usage: update_lap(array());
	/*========================================================================*/
	function update_blog($data)
	{
		global $db, $lid, $cid, $garage_config;

		$update_sql = array(
			'garage_id'	=> $cid,
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
			WHERE id = $lid AND garage_id = $cid";


		$db->sql_query($sql);

		return;
	}

	/*========================================================================*/
	// Delete Lap Including Image 
	// Usage: delete_lap('lap id');
	/*========================================================================*/
	function delete_blog($id)
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
	// Select Blog(s) Data By Vehicle From DB
	// Usage: get_blogs_by_vehicle('vehicle id');
	/*========================================================================*/
	function get_blogs_by_vehicle($cid)
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
	// Display Blog
	// Usage: display_blog('vehicle id')
	/*========================================================================*/
	function display_blog($vehicle_id)
	{
		global $template, $garage_vehicle, $garage, $user, $phpEx, $auth, $phpbb_root_path, $config, $owned;

		$template->assign_block_vars('blog', array());

		$template->assign_vars(array(
			'S_MODE_BLOG_ACTION' 	=> append_sid("{$phpbb_root_path}garage_blog.$phpEx", "mode=insert_blog&CID=$vehicle_id"))
		);
	}
}

$garage_blog = new garage_blog();

?>

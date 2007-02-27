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
	// Inserts Blog Into DB
	// Usage: insert_blog(array());
	/*========================================================================*/
	function insert_blog($data)
	{
		global $vid, $db, $garage_config, $garage_vehicle;

		$sql = 'INSERT INTO ' . GARAGE_BLOGS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'vehicle_id'		=> $vid,
			'user_id'		=> $garage_vehicle->get_vehicle_owner_id($vid),
			'blog_title'		=> $data['blog_title'],
			'blog_text'		=> $data['blog_text'],
			'blog_date'		=> time(),
			'bbcode_bitfield'	=> $data['bbcode_bitfield'],
			'bbcode_uid'		=> $data['bbcode_uid'],
			'bbcode_flags'		=> $data['bbcode_flags'],
		));

		$db->sql_query($sql);

		return $db->sql_nextid();
	}

	/*========================================================================*/
	// Updates Blog In DB
	// Usage: update_blog(array());
	/*========================================================================*/
	function update_blog($data)
	{
		global $db, $bid, $vid, $garage_config, $garage_vehicle;

		$update_sql = array(
			'user_id'		=> $garage_vehicle->get_vehicle_owner_id($vid),
			'blog_title'		=> $data['blog_title'],
			'blog_text'		=> $data['blog_text'],
			'bbcode_bitfield'	=> $data['bbcode_bitfield'],
			'bbcode_uid'		=> $data['bbcode_uid'],
			'bbcode_flags'		=> $data['bbcode_flags'],
		);

		$sql = 'UPDATE ' . GARAGE_BLOGS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = $bid AND vehicle_id = $vid";


		$db->sql_query($sql);

		return;
	}

	/*========================================================================*/
	// Delete Blog Including Image 
	// Usage: delete_blog('blog id');
	/*========================================================================*/
	function delete_blog($id)
	{
		global $db, $garage_image, $garage;
	
		//Get All Required Data
		$data = $this->get_blog($id);
	
		//Time To Delete The Actual Lap Now
		$garage->delete_rows(GARAGE_BLOGS_TABLE, 'id', $id);
	
		return ;
	}

	/*========================================================================*/
	// Select Blog(s) Data By Vehicle From DB
	// Usage: get_blogs_by_vehicle('vehicle id');
	/*========================================================================*/
	function get_blogs_by_vehicle($vid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'b.*, u.username',
			'FROM'		=> array(
				GARAGE_BLOGS_TABLE	=> 'b',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'b.user_id = u.user_id'
				)
			),
			'WHERE'		=>	"b.vehicle_id = $vid",
			'ORDER_BY'	=>	'b.id DESC'
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

		//Get Blog For Vehicle
		$data = $this->get_blogs_by_vehicle($vehicle_id);

		//Process Each Blog Entry
		for ( $i=0; $i < count($data); $i++ )
		{
			$blog_text = generate_text_for_display($data[$i]['blog_text'], $data[$i]['bbcode_uid'], $data[$i]['bbcode_bitfield'], $data[$i]['bbcode_flags']);
			$blog_text = make_clickable($blog_text);
			if ( $config['allow_smilies'] )
			{
				$blog_text = smiley_text($blog_text);
			}

			$template->assign_block_vars('blog.entry', array(
				'BLOG_TITLE' 	=> $data[$i]['blog_title'],
				'BLOG_DATE' 	=> $user->format_date($data[$i]['blog_date']),
				'BLOG_TEXT' 	=> $blog_text,
			));
		}

		$template->assign_vars(array(
			'S_MODE_BLOG_ACTION' 	=> append_sid("{$phpbb_root_path}garage_blog.$phpEx", "mode=insert_blog&VID=$vehicle_id"))
		);
	}
}

$garage_blog = new garage_blog();

?>

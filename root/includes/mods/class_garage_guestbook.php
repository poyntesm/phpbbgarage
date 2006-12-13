<?php
/***************************************************************************
 *                              class_garage_guestbook.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: class_garage_guestbook.php 156 2006-06-19 06:51:48Z poyntesm $
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

class garage_guestbook
{
	var $classname = "garage_guestbook";

	/*========================================================================*/
	// Count The Total Commnets Vehciles Have Recieved
	// Usage: count_total_comments();
	/*========================================================================*/
	function count_total_comments()
	{
		global $db;

		// Get the total count of comments in the garage
	
		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(gb.id) AS total_comments',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
			)
		));

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Error Counting Comments', '', __LINE__, __FILE__, $sql);
		}

        	$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$row['total_comments'] = (empty($row['total_comments'])) ? 0 : $row['total_comments'];

		return $row['total_comments'];
	}

	/*========================================================================*/
	// Count The Total Commnets Vehciles Have Recieved
	// Usage: count_total_comments();
	/*========================================================================*/
	function count_vehicle_comments($cid)
	{
		global $db;

		// Get the total count of comments in the garage
	
		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(gb.id) AS total',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
			),
			'WHERE'		=> "gb.garage_id = $cid"
		));

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Error Counting Comments', '', __LINE__, __FILE__, $sql);
		}

        	$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$row['total'] = (empty($row['total'])) ? 0 : $row['total'];

		return $row['total'];
	}

	/*========================================================================*/
	// Insert Comment Into DB
	// Usage: insert_vehicle_comment(array());
	/*========================================================================*/
	function insert_vehicle_comment($data)
	{
		global $cid, $db, $user_ip, $user;

		$sql = 'INSERT INTO ' . GARAGE_GUESTBOOKS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'garage_id'	=> $cid,
			'author_id'	=> $data['user_id'],
			'post_date'	=> time(),
			'ip_address'	=> $user_ip,
			'post'		=> $data['comments'])
		);

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert Vehicle Comment', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Select Specific Vehicle Comments Data From DB
	// Usage: get_vehicle_comments('garage id');
	/*========================================================================*/
	function get_vehicle_comments($cid)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'gb.id as comment_id, gb.post, gb.author_id, gb.post_date, gb.ip_address,	u.username, u.user_id, u.user_posts, u.user_from, u.user_website, u.user_email, u.user_icq, u.user_aim, u.user_yim, u.user_regdate, u.user_msnm, u.user_viewemail, u.user_rank,	u.user_sig, u.user_sig_bbcode_uid, u.user_avatar, u.user_avatar_type, u.user_allowsmile, u.user_allow_viewonline, u.user_session_time, g.made_year, g.id as garage_id, mk.make, md.model',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'gb.author_id = u.user_id'
				)
				,array(
					'FROM'	=> array(GARAGE_TABLE => 'g'),
					'ON'	=> 'g.user_id = gb.author_id and g.main_vehicle = 1'
				)
				,array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'mk'),
					'ON'	=> 'g.make_id = mk.id and mk.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'md'),
					'ON'	=> 'g.model_id = md.id and md.pending = 0'
				)
			),
			'WHERE'		=>  "gb.garage_id = $cid",
			'ORDER_BY'	=>  "gb.post_date ASC"
		));

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Get Vehicle Comment Data', '', __LINE__, __FILE__, $sql);
      		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}
		$db->sql_freeresult($result);

		if (empty($rows))
		{
			return;
		}

		return $rows;
	}

	/*========================================================================*/
	// Select Specific Vehicle Comments Data From DB
	// Usage: get_vehicle_comments('garage id');
	/*========================================================================*/
	function get_vehicle_comments_profile($cid)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'SUBSTRING(REPLACE(gb.post,\'<br />\',\' \'),1,75) AS post, gb.author_id, u.username',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'gb.author_id = u.user_id'
				)
			),
			'WHERE'		=>  "gb.garage_id = $cid",
			'ORDER_BY'	=>  "gb.post_date DESC"
		));

      		if ( !($result = $db->sql_query_limit($sql, 5)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Get Vehicle Comment Data', '', __LINE__, __FILE__, $sql);
      		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}
		$db->sql_freeresult($result);

		if (empty($rows))
		{
			return;
		}

		return $rows;
	}

	/*========================================================================*/
	// Select Specific Vehicle Comments Data From DB
	// Usage: get_vehicle_comments('garage id');
	/*========================================================================*/
	function get_comments($limit)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'gb.garage_id AS id, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle, gb.author_id AS author_id, gb.post_date, u.username',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'gb.author_id = u.user_id'
				)
				,array(
					'FROM'	=> array(GARAGE_TABLE => 'g'),
					'ON'	=> 'g.user_id = gb.author_id'
				)
				,array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'mk'),
					'ON'	=> 'g.make_id = mk.id and mk.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'md'),
					'ON'	=> 'g.model_id = md.id and md.pending = 0'
				)
			),
			'WHERE'		=>  "mk.pending = 0 AND md.pending = 0",
			'ORDER_BY'	=>  "gb.post_date ASC"
		));

	 	if(!$result = $db->sql_query_limit($sql, $limit))
		{
			message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
		}
		
		while ($row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}
		$db->sql_freeresult($result);

		if (empty($rows))
		{
			return;
		}

		return $rows;
	}

	/*========================================================================*/
	// Select Guestbook Comment Data From DB
	// Usage: get_comment('comment id');
	/*========================================================================*/
	function get_comment($comment_id)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'gb.id as comment_id, gb.post, gb.author_id, gb.post_date, gb.ip_address, gb.garage_id, g.made_year, mk.make, md.model, u.username, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'gb.author_id = u.user_id'
				)
				,array(
					'FROM'	=> array(GARAGE_TABLE => 'g'),
					'ON'	=> 'g.user_id = gb.author_id'
				)
				,array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'mk'),
					'ON'	=> 'g.make_id = mk.id and mk.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'md'),
					'ON'	=> 'g.model_id = md.id and md.pending = 0'
				)
			),
			'WHERE'		=>  "gb.id = $comment_id",
			'ORDER_BY'	=>  "gb.post_date ASC"
		));

              	if( !($result = $db->sql_query($sql)) )
       		{
          		message_die(GENERAL_ERROR, 'Could Not Select Comment Data', '', __LINE__, __FILE__, $sql);
       		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (empty($row))
		{
			return $row;
		}

		return $row;
	}

	/*========================================================================*/
	// Notify On Comment?
	// Usage: notify_on_comment();
	/*========================================================================*/
	function notify_on_comment($user_id)
	{
		global  $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'u.user_garage_guestbook_notify',
			'FROM'		=> array(
				USERS_TABLE	=> 'u',
			),
			'WHERE'		=> "u.user_id = $user_id"
		));

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Counting User Vehicles', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if ($row['user_garage_guestbook_notify'])
		{
			return true;
		}
		return false;
	}

	/*========================================================================*/
	// Build Last Commented Vehicle Table
	// Usage: show_lastcommented();
	/*========================================================================*/
	function show_lastcommented()
	{
		global $required_position, $user, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		if ( $garage_config['enable_last_commented'] != true )
		{
			return;
		}

		$template_block = 'block_' . $required_position;
		$template_block_row = 'block_' . $required_position . '.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' 	=> $user->lang['LATEST_VEHICLE_COMMENTS'],
			'COLUMN_1_TITLE'=> $user->lang['VEHICLE'],
			'COLUMN_2_TITLE'=> $user->lang['AUTHOR'],
			'COLUMN_3_TITLE'=> $user->lang['POSTED_DATE'])
		);

	        // What's the count? Default to 10
		$limit = $garage_config['last_commented_limit'] ? $garage_config['last_commented_limit'] : 10;

		//Get Latest Comments
		$comment_data = $this->get_comments($limit);

		for($i = 0; $i < count($comment_data); $i++)
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' 	=> append_sid("garage.$phpEx", "mode=view_vehicle&amp;CID=" . $comment_data[$i]['id']),
				'U_COLUMN_2' 	=> append_sid("profile.$phpEx", "mode=viewprofile&amp;" . POST_USERS_URL . "=" . $comment_data[$i]['member_id']),
				'COLUMN_1_TITLE'=> $comment_data[$i]['vehicle'],
				'COLUMN_2_TITLE'=> $comment_data[$i]['username'],
				'COLUMN_3' 	=> $user->format_date($comment_data[$i]['post_date']))
			);
	 	}
	
		$required_position++;
		return ;
	}
}

$garage_guestbook = new garage_guestbook();

?>

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

		$data = null;
	
		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(gb.id) AS total',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
			)
		));

		$result = $db->sql_query($sql);
        	$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$data['total'] = (empty($data['total'])) ? 0 : $data['total'];

		return $data['total'];
	}

	/*========================================================================*/
	// Count The Total Commnets Vehciles Have Recieved
	// Usage: count_total_comments();
	/*========================================================================*/
	function count_vehicle_comments($cid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(gb.id) AS total',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
			),
			'WHERE'		=> "gb.vehicle_id = $cid"
		));

		$result = $db->sql_query($sql);
        	$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$data['total'] = (empty($data['total'])) ? 0 : $data['total'];

		return $data['total'];
	}

	/*========================================================================*/
	// Insert Comment Into DB
	// Usage: insert_vehicle_comment(array());
	/*========================================================================*/
	function insert_vehicle_comment($data)
	{
		global $cid, $db, $user;

		$sql = 'INSERT INTO ' . GARAGE_GUESTBOOKS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'vehicle_id'	=> $cid,
			'author_id'	=> $user->data['user_id'],
			'post_date'	=> time(),
			'ip_address'	=> $user->ip,
			'post'		=> $data['comments'])
		);

		$db->sql_query($sql);

		return;
	}

	/*========================================================================*/
	// Select Specific Vehicle Comments Data From DB
	// Usage: get_vehicle_comments('garage id');
	/*========================================================================*/
	function get_vehicle_comments($cid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'gb.id as comment_id, gb.post, gb.author_id, gb.post_date, gb.ip_address, u.username, u.user_id, u.user_posts, u.user_from, u.user_website, u.user_email, u.user_icq, u.user_aim, u.user_yim, u.user_regdate, u.user_msnm, u.user_allow_viewemail, u.user_rank, u.user_sig, u.user_sig_bbcode_uid, u.user_avatar, u.user_avatar_type, u.user_allow_viewonline, g.made_year, g.id as vehicle_id, mk.make, md.model, u.user_avatar',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'gb.author_id = u.user_id'
				)
				,array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
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
			'WHERE'		=>  "gb.vehicle_id = $cid",
			'ORDER_BY'	=>  "gb.post_date ASC"
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
	// Select Pending Comments Data From DB
	// Usage: get_vehicle_comments();
	/*========================================================================*/
	function get_pending_comments()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'gb.id as comment_id, gb.post, gb.author_id, gb.post_date, gb.ip_address, u.username, u.user_id, u.user_posts, u.user_from, u.user_website, u.user_email, u.user_icq, u.user_aim, u.user_yim, u.user_regdate, u.user_msnm, u.user_allow_viewemail, u.user_rank, u.user_sig, u.user_sig_bbcode_uid, u.user_avatar, u.user_avatar_type, u.user_allow_viewonline, g.made_year, g.id as vehicle_id, mk.make, md.model, u.user_avatar',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'gb.author_id = u.user_id'
				)
				,array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
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
			'WHERE'		=>  "gb.pending = 1",
			'ORDER_BY'	=>  "gb.post_date ASC"
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
	// Select Specific Vehicle Comments Data From DB
	// Usage: get_vehicle_comments('garage id');
	/*========================================================================*/
	function get_vehicle_comments_profile($cid, $limit = 5)
	{
		global $db;

		$data = null;

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
			'WHERE'		=>  "gb.vehicle_id = $cid",
			'ORDER_BY'	=>  "gb.post_date DESC"
		));

      		$result = $db->sql_query_limit($sql, $limit);
		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select Specific Vehicle Comments Data From DB
	// Usage: get_vehicle_comments('garage id');
	/*========================================================================*/
	function get_comments($limit)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'gb.vehicle_id AS id, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle, gb.author_id AS author_id, gb.post_date, u.username',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'gb.author_id = u.user_id'
				)
				,array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
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

	 	$result = $db->sql_query_limit($sql, $limit);
		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select Guestbook Comment Data From DB
	// Usage: get_comment('comment id');
	/*========================================================================*/
	function get_comment($comment_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'gb.id as comment_id, gb.post, gb.author_id, gb.post_date, gb.ip_address, gb.vehicle_id, g.made_year, mk.make, md.model, u.username, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'gb.author_id = u.user_id'
				)
				,array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
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

              	$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Notify On Comment?
	// Usage: notify_on_comment();
	/*========================================================================*/
	function notify_on_comment($user_id)
	{
		global  $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'u.user_garage_guestbook_pm_notify, u.user_garage_guestbook_email_notify',
			'FROM'		=> array(
				USERS_TABLE	=> 'u',
			),
			'WHERE'		=> "u.user_id = $user_id"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
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

		$limit = $garage_config['last_commented_limit'] ? $garage_config['last_commented_limit'] : 10;

		$comment_data = $this->get_comments($limit);

		for($i = 0; $i < count($comment_data); $i++)
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' 	=> append_sid("garage.$phpEx", "mode=view_vehicle&amp;CID=" . $comment_data[$i]['id']),
				'U_COLUMN_2' 	=> append_sid("memberlist.$phpEx", "mode=viewprofile&amp;u=" . $comment_data[$i]['author_id']),
				'COLUMN_1_TITLE'=> $comment_data[$i]['vehicle'],
				'COLUMN_2_TITLE'=> $comment_data[$i]['username'],
				'COLUMN_3_TITLE'=> $user->format_date($comment_data[$i]['post_date']))
			);
	 	}
	
		$required_position++;
		return ;
	}

	/*========================================================================*/
	// Display Guestbook
	// Usage: display_guestbook('vehicle id');
	/*========================================================================*/
	function display_guestbook($cid)
	{
		global $template, $garage_vehicle, $garage, $user, $phpEx, $auth, $phpbb_root_path, $config;

		$template->assign_block_vars('guestbook', array());

		//Get Vehicle Data
		$vehicle_data = $garage_vehicle->get_vehicle($cid);

		//Get All Comments Data
		$comment_data = $this->get_vehicle_comments($cid);

		for ($i = 0, $count = sizeof($comment_data);$i < $count; $i++)
		{	
			$username = $comment_data[$i]['username'];
			$temp_url = append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $comment_data[$i]['user_id']);
			$poster = '<a href="' . $temp_url . '">' . $comment_data[$i]['username'] . '</a>';
			$poster_posts = ( $comment_data[$i]['user_id'] != ANONYMOUS ) ? $comment_data[$i]['user_posts'] : '';
			$poster_from = ( $comment_data[$i]['user_from'] && $comment_data['user_id'] != ANONYMOUS ) ? $user->lang['Location'] . ': ' . $comment_data[$i]['user_from'] : '';
			$vehicle_id = $comment_data[$i]['vehicle_id'];
			$poster_car_year = ( $comment_data[$i]['made_year'] && $comment_data[$i]['user_id'] != ANONYMOUS ) ? ' ' . $comment_data[$i]['made_year'] : '';
			$poster_car_mark = ( $comment_data[$i]['make'] && $comment_data[$i]['user_id'] != ANONYMOUS ) ?  ' ' . $comment_data[$i]['make'] : '';
			$poster_car_model = ( $comment_data[$i]['model'] && $comment_data[$i]['user_id'] != ANONYMOUS ) ? ' ' . $comment_data[$i]['model'] : '';
			$poster_joined = ( $comment_data[$i]['user_id'] != ANONYMOUS ) ? $user->lang['JOINED'] . ': ' . $user->format_date($comment_data[$i]['user_regdate']) : '';

			$poster_avatar = '';
			if ( $comment_data[$i]['user_avatar'] AND $user->optionget('viewavatars') )
			{
				$avatar_img = '';
				switch( $comment_data[$i]['user_avatar_type'] )
				{
					case AVATAR_UPLOAD:
						$avatar_img = $config['avatar_path'] . '/' . $comment_data[$i]['user_avatar'];
					break;

					case AVATAR_GALLERY:
						$avatar_img = $config['avatar_gallery_path'] . '/' . $comment_data[$i]['user_avatar'];
					break;
				}
				$poster_avatar = '<img src="' . $avatar_img . '" width="' . $comment_data[$i]['user_avatar_width'] . '" height="' . $comment_data[$i]['user_avatar_height'] . '" alt="" />';
			}

			// Handle anon users posting with usernames
			if ( $comment_data[$i]['user_id'] == ANONYMOUS && $comment_data[$i]['post_username'] != '' )
			{
				$poster = $comment_data[$i]['post_username'];
			}

			$profile = '<a href="' . $temp_url . '">' . $user->lang['READ_PROFILE'] . '</a>';

			$temp_url = append_sid("{$phpbb_root_path}privmsg.$phpEx", "mode=post&amp;u=".$comment_data[$i]['user_id']);
			$pm = '<a href="' . $temp_url . '">' . $user->lang['SEND_PRIVATE_MESSAGE'] . '</a>';

			if ( !empty($comment_data[$i]['user_viewemail']) || $auth->acl_get('m_') )
			{
				$email_uri = ( $config['board_email_form'] ) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=email&amp;u=" . $comment_data[$i]['user_id']) : 'mailto:' . $comment_data[$i]['user_email'];

				$email = '<a href="' . $email_uri . '">' . $user->lang['SEND_EMAIL'] . '</a>';
			}
			else
			{
				$email_img = '';
				$email = '';
			}

			$www_img = ( $comment_data[$i]['user_website'] ) ? '<a href="' . $comment_data[$i]['user_website'] . '" target="_userwww"><img src="' . $images['icon_www'] . '" alt="' . $user->lang['Visit_website'] . '" title="' . $user->lang['Visit_website'] . '" border="0" /></a>' : '';
			$www = ( $comment_data[$i]['user_website'] ) ? '<a href="' . $comment_data[$i]['user_website'] . '" target="_userwww">' . $user->lang['Visit_website'] . '</a>' : '';

			$posted = '<a href="' . append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=".$comment_data[$i]['user_id']) . '">' . $comment_data[$i]['username'] . '</a>';
			$posted = $user->format_date($comment_data[$i]['post_date']);

			$post = $comment_data[$i]['post'];

			// Parse message and/or sig for BBCode if reqd
			if ( $config['allow_bbcode'] )
			{
				
				//if ( $comment_data[$i]['bbcode_uid'] != '' )
				//{
				//	$post = ( $config['allow_bbcode'] ) ? bbencode_second_pass($post, $bbcode_uid) : preg_replace('/\:[0-9a-z\:]+\]/si', ']', $post);
				//}
			}

			$post = make_clickable($post);

			// Parse smilies
			if ( $config['allow_smilies'] )
			{
				$post = smiley_text($post);
			}

			// Replace newlines (we use this rather than nl2br because
			// till recently it wasn't XHTML compliant)
			$post = str_replace("\n", "\n<br />\n", $post);

			$edit_img = '';
			$edit = '';
			$delpost_img = '';
			$delpost = '';

		 	if ( $auth->acl_get('m_garage') )
			{
				$edit_img = $user->img('icon_post_edit', 'EDIT_POST');
				$edit = '<a href="'. append_sid("{$phpbb_root_path}garage.$phpEx", "mode=edit_comment&amp;CID=$cid&amp;comment_id=" . $comment_data[$i]['comment_id'] . "&amp;sid=" . $user->data['session_id']) . '">' . $user->lang['EDIT_POST'] . '</a>';
				$delpost_img = $user->img('icon_post_delete', 'DELETE_POST');
				$delpost = '<a href="'. append_sid("{$phpbb_root_path}garage.$phpEx", "mode=delete_comment&amp;CID=$cid&amp;comment_id=" . $comment_data[$i]['comment_id'] . "&amp;sid=" . $user->data['session_id']) . '">' . $user->lang['DELETE_POST'] . '</a>';

			}

			$template->assign_block_vars('guestbook.comments', array(
				'POST_AUTHOR' 		=> $poster,
				'POSTER_JOINED' 	=> $poster_joined,
				'POSTER_POSTS' 		=> $poster_posts,
				'POSTER_FROM' 		=> $poster_from,
				'POSTER_CAR_MARK' 	=> $poster_car_mark,
				'POSTER_CAR_MODEL' 	=> $poster_car_model,
				'POSTER_CAR_YEAR' 	=> $poster_car_year,
				'U_VEHICLE'		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;CID=$vehicle_id"),
				'POSTER_AVATAR' 	=> $poster_avatar,
				'PROFILE_IMG' 		=> $user->img('icon_user_profile', 'READ_PROFILE'),
				'PROFILE' 		=> $profile,
				'PM_IMG' 		=> $user->img('icon_contact_pm', 'SEND_PRIVATE_MESSAGE'),
				'PM'			=> $pm,
				'EMAIL_IMG'		=> $user->img('icon_contact_email', 'SEND_EMAIL'),
				'EMAIL'			=> $email,
				'WWW_IMG'		=> $www_img,
				'WWW'			=> $www,
				'EDIT_IMG'		=> $edit_img,
				'EDIT' 			=> $edit,
				'DELETE_IMG' 		=> $delpost_img,
				'DELETE' 		=> $delpost,
				'POSTER' 		=> $poster,
				'POSTED' 		=> $posted,
				'POST_DATE' 		=> $posted,
				'MESSAGE' 		=> $post,
				'POST' 			=> $post)
			);
		}

		$template->assign_vars(array(
			'S_DISPLAY_LEAVE_COMMENT'=> $auth->acl_get('u_garage_comment'),
			'S_MODE_GUESTBOOK_ACTION' 	=> append_sid("{$phpbb_root_path}garage_guestbook.$phpEx", "mode=insert_comment&CID=$cid"))
		);
	}
}

$garage_guestbook = new garage_guestbook();

?>

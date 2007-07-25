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
* phpBB Garage Guestbook Class
* @package garage
*/
class garage_guestbook
{
	var $classname = "garage_guestbook";

	/**
	* Return count of total comments all vehicles have recieved
	*/
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

	/**
	* Return count of comments for specific vehicle
	*
	* @param int $vid vehicle id to count comments for
	*
	*/
	function count_vehicle_comments($vid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(gb.id) AS total',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
			),
			'WHERE'		=> "gb.vehicle_id = $vid"
		));

		$result = $db->sql_query($sql);
        	$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$data['total'] = (empty($data['total'])) ? 0 : $data['total'];

		return $data['total'];
	}

	/**
	* Insert vehicle comment
	*
	* @param array $data single-dimensionl array holding the data for new comment
	*
	*/
	function insert_vehicle_comment($data)
	{
		global $vid, $db, $user;

		$sql = 'INSERT INTO ' . GARAGE_GUESTBOOKS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'vehicle_id'		=> $vid,
			'author_id'		=> $user->data['user_id'],
			'post_date'		=> time(),
			'ip_address'		=> $user->ip,
			'post'			=> $data['post'],
    			'bbcode_uid'		=> $data['bbcode_uid'],
			'bbcode_bitfield'	=> $data['bbcode_bitfield'],
			'bbcode_flags'		=> $data['bbcode_flags'],
		));

		$db->sql_query($sql);

		return;
	}

	/**
	* Update vehicle comment
	*
	* @param array $data single-dimensionl array holding the data for comment
	* @param int $comment_id id of comment to update
	*
	*/
	function update_vehicle_comment($data, $comment_id)
	{
		global $vid, $db, $user;

		$update_sql = array(
			'post'			=> $data['post'],
    			'bbcode_uid'		=> $data['bbcode_uid'],
			'bbcode_bitfield'	=> $data['bbcode_bitfield'],
			'bbcode_flags'		=> $data['bbcode_flags'],
		);

		$sql = 'UPDATE ' . GARAGE_GUESBOOKS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = $comment_id";

		$result = $db->sql_query($sql);

		return;
	}

	/**
	* Return limited array of vehicle comments
	*
	* @param int $vid vehicle id to filter comments for
	* @param int $start row to start data selection from
	* @param int $limit number of rows to return
	*
	*/
	function get_vehicle_comments($vid, $start = 0, $limit = 25 )
	{
		global $db, $garage_config;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'gb.id as comment_id, gb.post, gb.author_id, gb.post_date, gb.ip_address, gb.bbcode_uid, gb.bbcode_bitfield, gb.bbcode_flags, u.username, u.user_id, u.user_posts, u.user_from, u.user_website, u.user_email, u.user_icq, u.user_aim, u.user_yim, u.user_regdate, u.user_msnm, u.user_allow_viewemail, u.user_rank, u.user_sig, u.user_sig_bbcode_uid, u.user_avatar, u.user_avatar_type, u.user_allow_viewonline, v.made_year, v.id as vehicle_id, mk.make, md.model, u.user_avatar, u.user_colour, u.user_avatar_width, u.user_avatar_height',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
				GARAGE_VEHICLES_TABLE	=> 'v',
				GARAGE_MAKES_TABLE	=> 'mk',
				GARAGE_MODELS_TABLE	=> 'md',
				USERS_TABLE		=> 'u',
			),
			'WHERE'		=>  "gb.vehicle_id = $vid
						AND gb.author_id = u.user_id
						AND v.user_id = gb.author_id AND v.main_vehicle = 1
						AND v.make_id = mk.id AND mk.pending = 0
						AND v.model_id = md.id AND md.pending = 0",
			'ORDER_BY'	=>  "gb.post_date ASC"
		));

	 	$result = $db->sql_query_limit($sql, $limit, $start);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array with all comments for specifc vehicle
	*
	* @param int $vid vehicle id to filter comments for
	*
	*/
	function get_comments_by_vehicle($vid)
	{
		global $db, $garage_config;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'gb.*',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
			),
			'WHERE'		=>  "gb.vehicle_id = $vid",
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

	/**
	* Return array of all pending vehicle comments
	*/
	function get_pending_comments()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'gb.id as comment_id, gb.post, gb.author_id, gb.post_date, gb.ip_address, u.username, u.user_id, u.user_posts, u.user_from, u.user_website, u.user_email, u.user_icq, u.user_aim, u.user_yim, u.user_regdate, u.user_msnm, u.user_allow_viewemail, u.user_rank, u.user_sig, u.user_sig_bbcode_uid, u.user_avatar, u.user_avatar_type, u.user_allow_viewonline, v.made_year, v.id as vehicle_id, mk.make, md.model, u.user_avatar, u.user_colour',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
				GARAGE_VEHICLES_TABLE	=> 'v',
				GARAGE_MAKES_TABLE	=> 'mk',
				GARAGE_MODELS_TABLE	=> 'md',
				USERS_TABLE		=> 'u',
			),
			'WHERE'		=>  "gb.pending = 1
						AND gb.author_id = u.user_id
						AND v.user_id = gb.author_id AND v.main_vehicle = 1
						AND v.make_id = mk.id AND mk.pending = 0
						AND v.model_id = md.id AND md.pending = 0",
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

	/**
	* TODO: Should this get deprecaited along with old guestbook view???
	* Return limited array of vehicle comments with text truncated for display
	*
	* @param int $vid vehicle id to filter comments for
	* @param int $limit number of rows to return
	*
	*/
	function get_vehicle_comments_profile($vid, $limit = 5)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'SUBSTRING(REPLACE(gb.post,\'<br />\',\' \'),1,75) AS post, gb.author_id, u.username',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
				USERS_TABLE		=> 'u',
			),
			'WHERE'		=>  "gb.vehicle_id = $vid
						AND gb.author_id = u.user_id",
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

	/**
	* Return limited array of most recent comments
	*
	* @param int $limit number of rows to return
	*
	*/
	function get_comments($limit)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'gb.vehicle_id AS id, v.made_year, mk.make, md.model, gb.author_id AS author_id, gb.post_date, u.username, u.user_colour',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
				GARAGE_VEHICLES_TABLE	=> 'v',
				GARAGE_MAKES_TABLE	=> 'mk',
				GARAGE_MODELS_TABLE	=> 'md',
				USERS_TABLE		=> 'u',
			),
			'WHERE'		=>  "gb.author_id = u.user_id
						AND v.user_id = gb.author_id AND v.main_vehicle = 1
						AND v.make_id = mk.id AND mk.pending = 0
						AND v.model_id = md.id AND md.pending = 0",
			'ORDER_BY'	=>  "gb.post_date DESC"
		));

	 	$result = $db->sql_query_limit($sql, $limit);
		while ($row = $db->sql_fetchrow($result) )
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
	* Return specific comment data. Used to build edit page
	*
	* @param int $comment_id comment id to filter on
	*
	*/
	function get_comment($comment_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'gb.id as comment_id, gb.post, gb.author_id, gb.post_date, gb.ip_address, gb.vehicle_id, v.made_year, mk.make, md.model, u.username, v.made_year, mk.make, md.model',
			'FROM'		=> array(
				GARAGE_GUESTBOOKS_TABLE	=> 'gb',
				GARAGE_VEHICLES_TABLE	=> 'v',
				GARAGE_MAKES_TABLE	=> 'mk',
				GARAGE_MODELS_TABLE	=> 'md',
				USERS_TABLE		=> 'u',
			),
			'WHERE'		=>  "gb.id = $comment_id
						AND v.user_id = gb.author_id AND v.main_vehicle = 1
						AND v.make_id = mk.id AND mk.pending = 0
						AND v.model_id = md.id AND md.pending = 0",
			'ORDER_BY'	=>  "gb.post_date ASC"
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
	* Return users notification options
	*
	* @param int $user_id user id to filer on
	*
	*/
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

	/**
	* Assign template variables to display most recent comments
	*/
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
				'U_COLUMN_1' 		=> append_sid("garage_vehicle.$phpEx", "mode=view_vehicle&amp;VID=" . $comment_data[$i]['id'] ."#guestbook"),
				'U_COLUMN_2' 		=> append_sid("memberlist.$phpEx", "mode=viewprofile&amp;u=" . $comment_data[$i]['author_id']),
				'COLUMN_1_TITLE'	=> $comment_data[$i]['vehicle'],
				'COLUMN_2_TITLE'	=> $comment_data[$i]['username'],
				'COLUMN_3_TITLE'	=> $user->format_date($comment_data[$i]['post_date']),
				'USERNAME_COLOUR'	=> get_username_string('colour', $comment_data[$i]['author_id'], $comment_data[$i]['username'], $comment_data[$i]['user_colour']),
			));
	 	}
	
		$required_position++;
		return ;
	}

	/**
	* Assign template variables to display a vehicles comments
	*
	* @param int $vid vehicle id to fitler on
	*
	*/
	function display_guestbook($vid)
	{
		global $template, $garage_vehicle, $garage, $user, $phpEx, $auth, $phpbb_root_path, $config, $start, $garage_config, $mode, $garage_template;

		//Set Required Values To Defaults If They Are Empty
		$start	= (empty($start)) ? '0' : $start;

		$template->assign_block_vars('guestbook', array());

		//Get Vehicle Data
		$vehicle_data = $garage_vehicle->get_vehicle($vid);

		//Get All Comments Data
		$comment_data = $this->get_vehicle_comments($vid, $start, $garage_config['cars_per_page']);

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
	
			$post = generate_text_for_display($comment_data[$i]['post'], $comment_data[$i]['bbcode_uid'], $comment_data[$i]['bbcode_bitfield'], $comment_data[$i]['bbcode_flags']);

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
				$edit = '<a href="'. append_sid("{$phpbb_root_path}garage.$phpEx", "mode=edit_comment&amp;VID=$vid&amp;comment_id=" . $comment_data[$i]['comment_id'] . "&amp;sid=" . $user->data['session_id']) . '">' . $user->lang['EDIT_POST'] . '</a>';
				$delpost_img = $user->img('icon_post_delete', 'DELETE_POST');
				$delpost = '<a href="'. append_sid("{$phpbb_root_path}garage.$phpEx", "mode=delete_comment&amp;VID=$vid&amp;comment_id=" . $comment_data[$i]['comment_id'] . "&amp;sid=" . $user->data['session_id']) . '">' . $user->lang['DELETE_POST'] . '</a>';

			}

			$template->assign_block_vars('guestbook.comments', array(
				'POST_AUTHOR' 		=> $poster,
				'POSTER_JOINED' 	=> $poster_joined,
				'POSTER_POSTS' 		=> $poster_posts,
				'POSTER_FROM' 		=> $poster_from,
				'POSTER_CAR_MARK' 	=> $poster_car_mark,
				'POSTER_CAR_MODEL' 	=> $poster_car_model,
				'POSTER_CAR_YEAR' 	=> $poster_car_year,
				'U_VEHICLE'		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;VID=$vehicle_id"),
				'POSTER_AVATAR' 	=> ($user->optionget('viewavatars')) ? get_user_avatar($comment_data[$i]['user_avatar'], $comment_data[$i]['user_avatar_type'], $comment_data[$i]['user_avatar_width'], $comment_data[$i]['user_avatar_height']) : '',
				'POST_AUTHOR_COLOUR'	=> get_username_string('colour', $comment_data[$i]['user_id'], $comment_data[$i]['username'], $comment_data[$i]['user_colour']),
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

		$count = $this->count_vehicle_comments($vid);
		$pagination = $garage_template->generate_pagination(append_sid("{$phpbb_root_path}garage_vehicle.php", "mode={$mode}&amp;VID=$vid"), 'guestbook', $count, $garage_config['cars_per_page'], $start);
		$template->assign_vars(array(
			'PAGINATION' 			=> $pagination,
			'PAGE_NUMBER' 			=> on_page($count, $garage_config['cars_per_page'], $start),
			'TOTAL_COMMENTS'		=> ($count == 1) ? $user->lang['VIEW_COMMENT'] : sprintf($user->lang['VIEW_COMMENTS'], $count),
			'S_DISPLAY_LEAVE_COMMENT'	=> $auth->acl_get('u_garage_comment'),
			'S_MODE_GUESTBOOK_ACTION' 	=> append_sid("{$phpbb_root_path}garage_guestbook.$phpEx", "mode=insert_comment&amp;VID=$vid"))
		);
	}
}

$garage_guestbook = new garage_guestbook();

?>

<?php
/***************************************************************************
 *                              class_garage_guestbook.php
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
		$sql = "SELECT count(*) AS total_comments 
			FROM " . GARAGE_GUESTBOOKS_TABLE;

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Error Counting Comments', '', __LINE__, __FILE__, $sql);
		}

        	$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row['total_comments'];
	}

	/*========================================================================*/
	// Insert Comment Into DB
	// Usage: insert_vehicle_comment(array());
	/*========================================================================*/
	function insert_vehicle_comment($data)
	{
		global $cid, $db, $user_ip;

		$sql = "INSERT INTO " . GARAGE_GUESTBOOKS_TABLE . "
			(garage_id, author_id, post_date, ip_address, post)
			VALUES
			('$cid', '" . $data['author_id'] . "', '" . $data['post_date'] . "', '$user_ip', '" . $data['comments'] . "')";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert Vehicle Comment', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Select Specific Vehicle Comments Data From DB
	// Usage: select_vehicle_comments('garage id');
	/*========================================================================*/
	function select_vehicle_comments($cid)
	{
		global $db;

		// Get Guestbook Entries
        	$sql = "SELECT gb.id as comment_id, gb.post, gb.author_id, gb.post_date, gb.ip_address,	u.username, u.user_id, u.user_posts, u.user_from, u.user_website, u.user_email, u.user_icq, u.user_aim, u.user_yim, u.user_regdate, u.user_msnm, u.user_viewemail, u.user_rank,	u.user_sig, u.user_sig_bbcode_uid, u.user_avatar, u.user_avatar_type, u.user_allowavatar, u.user_allowsmile, u.user_allow_viewonline, u.user_session_time, g.made_year, g.id as garage_id, makes.make, models.model
                        FROM " . GARAGE_GUESTBOOKS_TABLE . " gb 
                        	LEFT JOIN " . USERS_TABLE . " u ON gb.author_id = u.user_id 
				LEFT JOIN " . GARAGE_TABLE ." g ON g.member_id = gb.author_id and g.main_vehicle = 1 
       				LEFT JOIN " . GARAGE_MAKES_TABLE . " makes ON g.make_id = makes.id 
                		LEFT JOIN " . GARAGE_MODELS_TABLE . " models ON g.model_id = models.id 
                        WHERE gb.garage_id = $cid
                        ORDER BY gb.post_date ASC";

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Get Vehicle Comment Data', '', __LINE__, __FILE__, $sql);
      		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}
		$db->sql_freeresult($result);

		return $rows;
	}

	/*========================================================================*/
	// Select Guestbook Comment Data From DB
	// Usage: select_comment_data('comment id');
	/*========================================================================*/
	function select_comment_data($comment_id)
	{
		global $db;

		$sql = "SELECT gb.id as comment_id, gb.post, gb.author_id, gb.post_date, gb.ip_address, gb.garage_id, g.made_year, makes.make, models.model, u.username, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle
               	        FROM " . GARAGE_GUESTBOOKS_TABLE . " gb 
				LEFT JOIN " . GARAGE_TABLE . " g on g.id = gb.garage_id
                        	LEFT JOIN " . USERS_TABLE . " u ON g.member_id = u.user_id 
       				LEFT JOIN " . GARAGE_MAKES_TABLE . " makes ON g.make_id = makes.id
                		LEFT JOIN " . GARAGE_MODELS_TABLE . " models ON g.model_id = models.id
                        WHERE gb.id = $comment_id
                        ORDER BY gb.post_date ASC";

              	if( !($result = $db->sql_query($sql)) )
       		{
          		message_die(GENERAL_ERROR, 'Could Not Select Comment Data', '', __LINE__, __FILE__, $sql);
       		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row;
	}

	/*========================================================================*/
	// Send A PM To A User
	// Usage: send_user_pm(array());
	/*========================================================================*/
	function send_user_pm($data)
	{
		global $db, $garage;

		$garage->update_single_field(USERS_TABLE, 'user_new_privmsg', '1', 'user_id', $data['user_id']);	
		$garage->update_single_field(USERS_TABLE, 'user_last_privmsg', '9999999999', 'user_id', $data['user_id']);	
		$sql = "INSERT INTO " . PRIVMSGS_TABLE . " 
			(privmsgs_type, privmsgs_subject, privmsgs_from_userid, privmsgs_to_userid, privmsgs_date, privmsgs_enable_html, privmsgs_enable_bbcode, privmsgs_enable_smilies, privmsgs_attach_sig)
			VALUES 
			('".PRIVMSGS_NEW_MAIL."', '" . $data['pm_subject'] . "', '" . $data['author_id'] . "', '" . $data['user_id'] . "', '" . $data['date'] . "', '0', '1', '1', '0')";
           	
	 	if ( !$db->sql_query($sql) )
         	{
            		message_die(GENERAL_ERROR, 'Could Not Insert PM Sent Info', '', __LINE__, __FILE__, $sql);
         	}
   
      		$id = $db->sql_nextid();

		$sql = "INSERT INTO " . PRIVMSGS_TEXT_TABLE . " 
			(privmsgs_text_id, privmsgs_text) 
			VALUES 
			($id, '" . $data['pm_text'] . "' )";

           	if ( !$db->sql_query($sql) )
         	{
            		message_die(GENERAL_ERROR, 'Could Not Insert PM Sent Text', '', __LINE__, __FILE__, $sql);
         	}

		return ;
	}

	/*========================================================================*/
	// Build Last Commented Vehicle Table
	// Usage: show_lastcommented();
	/*========================================================================*/
	function show_lastcommented()
	{
		global $required_position, $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		if ( $garage_config['lastcommented_on'] != TRUE )
		{
			return;
		}

		$template_block = 'block_' . $required_position;
		$template_block_row = 'block_' . $required_position . '.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $lang['Latest_Vehicle_Comments'],
			'COLUMN_1_TITLE' => $lang['Vehicle'],
			'COLUMN_2_TITLE' => $lang['Author'],
			'COLUMN_3_TITLE' => $lang['Posted_Date'])
		);
	
	        // What's the count? Default to 10
	        $limit = $garage_config['lastcommented_limit'] ? $garage_config['lastcommented_limit'] : 10;
	 		 		
	 	$sql = "SELECT gb.garage_id AS id, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, gb.author_id AS member_id, gb.post_date AS POI, m.username 
	                FROM " . GARAGE_GUESTBOOKS_TABLE . " gb 
	                	LEFT JOIN " . GARAGE_TABLE . " g ON gb.garage_id = g.id
	                        LEFT JOIN " . GARAGE_MAKES_TABLE . " makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " models ON g.model_id = models.id
	                        LEFT JOIN " . USERS_TABLE . " m ON gb.author_id = m.user_id
			WHERE makes.pending = 0 AND models.pending = 0
	                ORDER BY POI DESC LIMIT $limit";
	
	 	if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
		}
	 		            
	 	while ( $vehicle_data = $db->sql_fetchrow($result) )
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=" . $vehicle_data['id']),
				'U_COLUMN_2' => append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $vehicle_data['member_id']),
				'COLUMN_1_TITLE' => $vehicle_data['vehicle'],
				'COLUMN_2_TITLE' => $vehicle_data['username'],
				'COLUMN_3' => create_date('D M d, Y G:i', $vehicle_data['POI'], $board_config['board_timezone']))
			);
	 	}
	
		$required_position++;
		return ;
	}
}

$garage_guestbook = new garage_guestbook();

?>

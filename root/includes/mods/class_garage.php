<?php
/***************************************************************************
 *                              class_garage.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: class_garage.php 156 2006-06-19 06:51:48Z poyntesm $
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

//Inlcude Garage Constants
include_once($phpbb_root_path . 'includes/mods/constants_garage.'. $phpEx);

// Build Up Garage Config...We Will Use These Values Many A Time
$sql = "SELECT config_name, config_value 
	FROM " . GARAGE_CONFIG_TABLE;

if(!$result = $db->sql_query($sql))
{
	message_die(GENERAL_ERROR, "Could Not Query Garage Config Information", "", __LINE__, __FILE__, $sql);
}

while( $row = $db->sql_fetchrow($result) )
{
	$garage_config[$row['config_name']] = $row['config_value'];
}

class garage 
{
	var $classname = "garage";

	/*========================================================================*/
	// Makes Safe Any User Input
	// Usage: process_vars(array());
	/*========================================================================*/
	function process_vars($params = array())
	{
		while(list($var, $param) = @each($params) )
		{
			$data[$var] = request_var($var, $param );
		}

		return $data;
	}

	/*========================================================================*/
	// Check All Required Variables Have Data
	// Usage: check_required_vars(array());
	/*========================================================================*/
	function check_required_vars($params = array())
	{
		global $phpEx, $data;

		while( list($var, $param) = @each($params) )
		{
			if (empty($data[$param]))
			{
				redirect(append_sid("garage.$phpEx", "mode=error&amp;EID=3"));
			}
		}

		return ;
	}

	/*========================================================================*/
	// Check All Required Variables Have Data Within The ACP
	// Usage: check_acp_required_vars(array(), message);
	/*========================================================================*/
	function check_acp_required_vars($params = array(), $message)
	{
		global $data;

		while( list($var, $param) = @each($params) )
		{
			if (empty($data[$param]))
			{
				message_die(GENERAL_MESSAGE, $message);
			}
		}

		return ;
	}

	/*========================================================================*/
	// Count The Total Views The Garage Has Recieved
	// Usage: count_total_views();
	/*========================================================================*/
	function count_total_views()
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'SUM(g.views) as total_views',
			'FROM'		=> array(
				GARAGE_TABLE	=> 'g',
			)
		));

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Error Counting Views', '', __LINE__, __FILE__, $sql);
		}

	        $row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$row['total_views'] = (empty($row['total_views'])) ? 0 : $row['total_views'];

		return $row['total_views'];
	}

	/*========================================================================*/
	// Update A Single Field For A Single Entry
	// Usage:  update_single_field('table name', 'set field' 'set value', 'where field', 'where value');
	/*========================================================================*/
	function update_single_field($table, $set_field, $set_value, $where_field, $where_value)
	{
		global $db;

		$update_sql = array(
			$set_field	=> $set_value
		);

		$sql = 'UPDATE ' . $table . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE $where_field = '$where_value'";

		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could Not Update DB', '', __LINE__, __FILE__, $sql);
		}
	
		return;
	}

	/*========================================================================*/
	// Increment A Count Field In DB
	// Usage:  build_selection_box('table name', 'field to increment', 'where field' ,'where value');
	/*========================================================================*/
	function update_view_count($table, $set_field, $where_field, $where_value)
	{
		global $db;

		$sql = "UPDATE $table 
			SET $set_field = $set_field + 1 
			WHERE $where_field = $where_value";

		if ( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, "Could Not Update View Count", '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Delete Row/Rows From DB
	// Usage:  build_selection_box('table name', 'where field', 'where value');
	/*========================================================================*/
	function delete_rows($table, $where_field, $where_value)
	{
		global $db;

		$sql = "DELETE 
			FROM $table 
			WHERE $where_field = '$where_value'";

		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could Not Perform Delete', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Select All Category Data
	// Usage: get_categories();
	/*========================================================================*/
	function get_categories()
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'c.id, c.title, c.field_order',
			'FROM'		=> array(
				GARAGE_CATEGORIES_TABLE	=> 'c',
			),
			'ORDER_BY'	=> 'c.field_order'
		));

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select All Categories Data', '', __LINE__, __FILE__, $sql);
      		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);
	
		if (empty($data))
		{
			return;
		}
	
		return $data;
	}

	/*========================================================================*/
	// Select Specific Category Data
	// Usage: get_category('category id');
	/*========================================================================*/
	function get_category($category_id)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'c.id, c.title, c.field_order',
			'FROM'		=> array(
				GARAGE_CATEGORIES_TABLE	=> 'c',
			),
			'WHERE'		=> "c.id = $category_id",
			'ORDER_BY'	=> 'c.field_order'
		));

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select Category Data', '', __LINE__, __FILE__, $sql);
      		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (empty($row))
		{
			return;
		}
	
		return $row;
	}

	/*========================================================================*/
	// Remove Duplicate Entries In An Array
	// Usage: remove_duplicate('vehicle id');
	/*========================================================================*/
	function remove_duplicate($array, $field)
	{
		foreach ($array as $sub)
		{
			$cmp[] = $sub[$field];
		}
		$unique = array_unique($cmp);
		foreach ($unique as $k => $rien)
		{
			$new[] = $array[$k];
		}

		return $new;
	}

	/*========================================================================*/
	// Seed Random Number Generator
	// Usage: make_seed();
	/*========================================================================*/
	function make_seed()
	{
		list($usec, $sec) = explode(' ', microtime());
		return (float) $sec + ((float) $usec * 100000);
	}

	/*========================================================================*/
	// Seed Random Number Generator
	// Usage: make_seed();
	/*========================================================================*/
	function year_list()
	{
		global $garage_config;

		// Grab the current year
		$my_array = localtime(time(), 1) ;
		$current_date = $my_array["tm_year"] +1900 ;
	
	        // Calculate end year based on offset configured
	        $end_year = $current_date + $garage_config['year_end'];
	
		// A simple check to prevent infinite loop
		if ( $garage_config['year_start'] > $end_year ) 
		{
			return;
		}	
	
		for ( $year = $end_year; $year >= $garage_config['year_start']; $year-- ) 
		{
			$years[] = $year;
		}

		return $years;
	}

	/*========================================================================*/
	// Check If Any Pending Items Exists
	// Usage: check_pending_items();
	/*========================================================================*/
	function check_pending_items()
	{
		global $garage_config;

		if ($garage_config['items_pending'] == 1)
		{
			return true;
		}

		return false;
	}

	/*========================================================================*/
	// Returns List Of Moderators To Notify Of Pending Items By Email & Jabber
	// Usage: moderators_requiring_email($moder);
	/*========================================================================*/
	function moderators_requiring_email($moderators)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'u.user_id, u.username, u.user_email, u.user_lang, u.user_jabber, u.user_notify_type',
			'FROM'		=> array(
				USERS_TABLE	=> 'u',
			),
			'WHERE'		=> $db->sql_in_set('u.user_id', $moderators[0]['m_garage']) . ' AND u.user_garage_mod_email_optout = 0'
		));

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Moderations To Email/Jabber', '', __LINE__, __FILE__, $sql);
		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}

		$db->sql_freeresult($result);

		if (empty($data))
		{
			return;
		}
		return $data;
	}

	/*========================================================================*/
	// Returns List Of Moderators To Notify Of Pending Items By Private Message
	// Usage: moderators_requiring_pm($moderators);
	/*========================================================================*/
	function moderators_requiring_pm($moderators)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'u.user_id',
			'FROM'		=> array(
				USERS_TABLE	=> 'u',
			),
			'WHERE'  	=> $db->sql_in_set('u.user_id', $moderators[0]['m_garage']) . ' AND u.user_garage_mod_pm_optout = 0'
		));

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Moderators To PM', '', __LINE__, __FILE__, $sql);
		}


		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}

		$db->sql_freeresult($result);

		if (empty($data))
		{
			return;
		}
		return $data;
	}

	/*========================================================================*/
	// Send All Admins & Moderators A PM Notifing Them Of Pending Items
	// Takes Into Account Moderators That Optout If Allowed
	// Usage: pending_notification(MCP mode to approve);
	/*========================================================================*/
	function pending_notification($mcp_mode_to_approve)
	{
		global $user, $phpEx, $auth, $garage_config, $config, $garage, $phpbb_root_path;

		//Get All Users With The Rights To Approve Items If We Need To
		if ( $garage_config['enable_email_pending_notify'] OR $garage_config['enable_pm_pending_notify'] )
		{
			$garage_moderators = $auth->acl_get_list(false, array('m_garage'), false);
		}

		//Do We Send Email && Jabber Notifications On Pending Items?
		if ($garage_config['enable_email_pending_notify'])
		{
			//Get All Garage Moderators To Notify Via Email
			$moderators_to_email = $garage->moderators_requiring_email($garage_moderators, $garage_config['enable_email_pending_notify_optout'] );

			//Process All Moderator Returned And Send Them Notification Via There Perferred Methods (Email/Jabber)
			for ($i = 0, $count = sizeof($moderators_to_email);$i < $count; $i++)
			{
				include_once($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);

				$messenger = new messenger();
				$messenger->template('garage_pending', $moderators_to_email[$i]['user_lang']);
				$messenger->replyto($config['board_contact']);
				$messenger->to($moderators_to_email[$i]['user_email'], $moderators_to_email[$i]['username']);
				$messenger->im($moderators_to_email[$i]['user_jabber'], $moderators_to_email[$i]['username']);

				$messenger->assign_vars(array(
					'U_MCP'		=> generate_board_url() . "/mcp.$phpEx?i=garage&mode=$mcp_mode_to_approve")
				);

				//Send Them The Actual Notification
				$messenger->send($moderators_to_email[$i]['user_notify_type']);
			}
		}

		//Do We Send Private Message Notifications On Pending Items?
		if ($garage_config['enable_pm_pending_notify'])
		{
			//Get All Garage Moderators To Notify Via PM
			$moderators_to_pm = $garage->moderators_requiring_pm($garage_moderators, $garage_config['enable_pm_pending_notify_optout']);

			//Process All Moderator Returned And Send Them Notification Via Private Message
			for ($i = 0, $count = sizeof($moderators_to_pm);$i < $count; $i++)
			{
				include_once($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
				include_once($phpbb_root_path . 'includes/message_parser.' . $phpEx);

				$message_parser = new parse_message();
				$message_parser->message = sprintf($user->lang['PENDING_NOTIFY_TEXT'], '<a href="mcp.' . $phpEx . '?i=garage&mode=' . $mcp_mode_to_approve .'">' . $user->lang['HERE'] . '</a>');
				$message_parser->parse(true, true, true, false, false, true, true);

				$pm_data = array(
					'from_user_id'			=> $user->data['user_id'],
					'from_user_ip'			=> $user->data['user_ip'],
					'from_username'			=> $user->data['username'],
					'enable_sig'			=> false,
					'enable_bbcode'			=> true,
					'enable_smilies'		=> true,
					'enable_urls'			=> false,
					'icon_id'			=> 0,
					'bbcode_bitfield'		=> $message_parser->bbcode_bitfield,
					'bbcode_uid'			=> $message_parser->bbcode_uid,
					'message'			=> $message_parser->message,
					'address_list'			=> array('u' => array($moderators_to_pm[$i]['user_id'] => 'to')),
				);

				//Now We Have All Data Lets Send The PM!!
				submit_pm('post', $user->lang['PENDING_ITEMS'], $pm_data, false, false);
			}
		}

		return;
	}

	/*========================================================================*/
	// Write A Message To A Logfile
	// Usage: write_logfile('file name', 'wb|ab'), 'message', 'no. tabs required';
	/*========================================================================*/
	function write_logfile ($log_file, $log_type, $message, $level=0)
	{
        	// Open that log up!
	        $log_handle = @fopen( $log_file, $log_type );

		//Make Sure We Have A File Handle
		if ( empty($log_handle) == false )
		{
			// Make sure we end with a new line
			if ( !preg_match('/^.+?\n$/', $message) )
			{
				$message .= "\n";
			}

			// Prepend number of tabs equal to level
			while ( $level > 0 )
			{
				$message = "\t".$message;
				$level--;
			}
	
			// Write the message to the log
			@fwrite( $log_handle, $message );
		}

		//Finished Writting Required Message So Close Our File Handle
 		@fopen($log_handle);
	}
}

$garage = new garage();

?>

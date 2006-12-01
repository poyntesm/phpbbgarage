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
	// Makes Safe Any Posted Variables
	// Usage: process_post_vars(array());
	/*========================================================================*/
	function process_post_vars($params = array())
	{
		while( list($var, $param) = @each($params) )
		{
			$data[$param] = request_var($param, '');
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
	// Usage: get_categorys();
	/*========================================================================*/
	function get_categorys()
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
	// Send All Admins & Moderators A PM Notifing Them Of Pending Items
	// Usage: check_pending_items();
	/*========================================================================*/
	function pending_notification()
	{
		global $garage_guestbook, $user, $phpEx, $auth, $garage_config;

		//Get All Users With The Rights To Approve Items
		$user_ary = $auth->acl_get_list(false, array('m_garage'), false);

		//$user_ary['0']['m_garage']['0'];
		//Process All Selected Users And Send Them A PM...
		for ($i = 0, $count = sizeof($user_ary); $i < $count; $i++)
		{
			//If User Not Opt'd Out Of PM Notifications & PM Notifications Enabled...Then Send Them A PM
			if ($garage_config['enable_pm_pending_notify'])
			{
				//Build Required PM Data
				$data['date'] 		= date("U");
				$data['pm_subject'] 	= $user->lang['PENDING_ITEMS'];
				$data['link'] 		= '<a href="garage.' . $phpEx . '?mode=garage_pending">' . $user->lang['HERE'] . '</a>';
				$data['pm_text'] 	= (sprintf($user->lang['PENDING_NOTIFY_TEXT'], $data['link']));
				$data['author_id'] 	= $user->data['user_id'];
				$data['user_id'] 	= $user_ary[0]['m_garage']['0'];
	
				//Send User A PM Mofication Of New Pending Item
				//$garage_guestbook->send_user_pm($data);
			}
			//If User Not Opt'd Out Of Email Notifications & Email Notifications Enabled...Then Send Them A Email
			if ($garage_config['enable_pm_pending_notify'])
			{
				//Send User A Email Notification Of New Pending Item
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

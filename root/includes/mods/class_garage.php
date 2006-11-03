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

//Setup Arrays Used To Build Drop Down Selection Boxes
$currency_types = array('GBP', 'USD', 'EUR', 'CAD', 'YEN');
$mileage_unit_types = array($user->lang['MILES'], $user->lang['KILOMETERS']);
$boost_types = array('PSI', 'BAR');
$power_types = array($user->lang['WHEEL'], $user->lang['HUB'], $user->lang['FLYWHEEL']);
$cover_types = array($user->lang['THIRD_PARTY'], $user->lang['THIRD_PARTY_FIRE_THEFT'], $user->lang['COMPREHENSIVE'], $user->lang['COMPREHENSIVE_CLASSIC'], $user->lang['COMPREHENSIVE_REDUCED']);
$rating_types = array( '10', '9', '8', '7', '6', '5', '4', '3', '2', '1');
$rating_text = array( '10', '9', '8', '7', '6', '5', '4', '3', '2', '1');
$nitrous_types = array('0', '25', '50', '75', '100');
$nitrous_types_text = array($user->lang['NO_NITROUS'], $user->lang['25_BHP_SHOT'], $user->lang['50_BHP_SHOT'], $user->lang['75_BHP_SHOT'], $user->lang['100_BHP_SHOT']);
$engine_types= array($user->lang['8_CYLINDER_NA'], $user->lang['8_CYLINDER_FI'], $user->lang['6_CYLINDER_NA'], $user->lang['6_CYLINDER_FI'], $user->lang['4_CYLINDER_NA'], $user->lang['4_CYLINDER_FI']);

class garage 
{

	var $classname = "garage";

	/*========================================================================*/
	// Makes Safe Any Posted Variables
	// Usage: process_post_vars(array());
	/*========================================================================*/
	function process_post_vars($params = array())
	{
		global $HTTP_POST_VARS, $HTTP_GET_VARS;

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
				redirect(append_sid("garage.$phpEx?mode=error&EID=3", true));
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

		$sql = "SELECT SUM(views) AS total_views 
			FROM " . GARAGE_TABLE;

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

		$sql = "UPDATE $table 
			SET $set_field = '$set_value' 
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
	// Get All Groups User Is Member Of
	// Usage: get_group_membership('user id');
	/*========================================================================*/
	function get_group_membership($u_id)
	{
		global $db ;

		$sql = "SELECT ug.group_id, g.group_name
	             	FROM " . USER_GROUP_TABLE . " ug, " . GROUPS_TABLE ." g
                	WHERE ug.user_id = $u_id
				AND ug.group_id = g.group_id AND g.group_single_user <> " . TRUE ."
			ORDER BY g.group_name ASC";

       		if( !($result = $db->sql_query($sql)) )
       		{
         		message_die(GENERAL_ERROR, 'Could Not Select Groups', '', __LINE__, __FILE__, $sql);
       		}

		while( $grouprow = $db->sql_fetchrow($result) )
		{
			$groupdata[] = $grouprow;
		}
		$db->sql_freeresult($result);
	
		return $groupdata;
	}

	/*========================================================================*/
	// Select All Category Data
	// Usage: select_all_category_data();
	/*========================================================================*/
	function select_all_category_data()
	{
		global $db;

		$sql = "SELECT *
			FROM " . GARAGE_CATEGORIES_TABLE . "
			ORDER BY field_order";

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select All Categories Data', '', __LINE__, __FILE__, $sql);
      		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);
	
		return $data;
	}

	/*========================================================================*/
	// Select Specific Category Data
	// Usage: select_category_data('category id');
	/*========================================================================*/
	function select_category_data($category_id)
	{
		global $db;

		$sql = "SELECT *
			FROM " . GARAGE_CATEGORIES_TABLE . "
			WHERE id = $category_id
			ORDER BY field_order";

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select Category Data', '', __LINE__, __FILE__, $sql);
      		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
	
		return $row;
	}

	/*========================================================================*/
	// Remove Duplicate Entries In An Array
	// Usage: remove_duplicate('vehicle id');
	/*========================================================================*/
	function remove_duplicate($array, $field)
	{
		foreach ($array as $sub)
		$cmp[] = $sub[$field];
		$unique = array_unique($cmp);
		foreach ($unique as $k => $rien)
		$new[] = $array[$k];
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
		global $lang, $garage_guestbook, $userdata, $db, $phpEx;

		$sql = "SELECT user_id
			FROM " . USERS_TABLE ."
			WHERE user_level = " . ADMIN . " OR user_level = " . MOD;

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Select Admin Or Moderator Users', '', __LINE__, __FILE__, $sql);
		}

		//Process All Selected Users And Send Them A PM...
		while ($row = $db->sql_fetchrow($result))
		{
			//Build Required PM Data
			$data['date'] = date("U");
			$data['pm_subject'] = $lang['Pending_Items'];
			$data['link'] = '<a href="garage.' . $phpEx . '?mode=garage_pending">' . $lang['Here'] . '</a>';
			$data['pm_text'] = (sprintf($lang['Pending_Notify_Text'],$data['link']));
			$data['author_id'] = $userdata['user_id'];
			$data['user_id'] = $row['user_id'];
	
			//Now We Have All Data Lets Send The PM!!
			$garage_guestbook->send_user_pm($data);
		}

		$db->sql_freeresult($result);

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
		if ( empty($log_handle) == FALSE )
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

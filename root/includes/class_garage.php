<?php
/***************************************************************************
 *                              class_garage.php
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
$mileage_unit_types = array($lang['Miles'], $lang['Kilometers']);
$boost_types = array('PSI', 'BAR');
$power_types = array($lang['Wheel'], $lang['Hub'], $lang['Flywheel']);
$cover_types = array($lang['Third_Party'], $lang['Third_Party_Fire_Theft'], $lang['Comprehensive'], $lang['Comprehensive_Classic'], $lang['Comprehensive_Reduced']);
$rating_types = array( '10', '9', '8', '7', '6', '5', '4', '3', '2', '1');
$rating_text = array( '10', '9', '8', '7', '6', '5', '4', '3', '2', '1');
$nitrous_types = array('0', '25', '50', '75', '100');
$nitrous_types_text = array($lang['No_Nitrous'], $lang['25_BHP_Shot'], $lang['50_BHP_Shot'], $lang['75_BHP_Shot'], $lang['100_BHP_Shot']);
$engine_types= array($lang['8_Cylinder_NA'], $lang['8_Cylinder_FI'], $lang['6_Cylinder_NA'], $lang['6_Cylinder_FI'], $lang['4_Cylinder_NA'], $lang['4_Cylinder_FI']);

class garage 
{

	var $classname = "garage";

	/*========================================================================*/
	// Makes Safe Any Posted Int Variables
	// Usage: process_int_vars(array());
	/*========================================================================*/
	function process_int_vars($params = array())
	{
		global $HTTP_POST_VARS, $HTTP_GET_VARS;

		while( list($var, $param) = @each($params) )
		{
			if (!empty($HTTP_POST_VARS[$param]))
			{
				$data[$param] = intval($HTTP_POST_VARS[$param]);
			}
			else if (!empty($HTTP_GET_VARS[$param]))
			{
				$data[$param] = intval($HTTP_GET_VARS[$param]);
			}
		}

		return $data;
	}

	/*========================================================================*/
	// Makes Safe Any Posted String Variables
	// Usage: process_str_vars(array());
	/*========================================================================*/
	function process_str_vars($params = array())
	{
		global $HTTP_POST_VARS, $HTTP_GET_VARS;

		while( list($var, $param) = @each($params) )
		{
			if (!empty($HTTP_POST_VARS[$param]))
			{
				if ( is_array( $HTTP_POST_VARS[$param]) )
				{
					$data[$param] = $HTTP_POST_VARS[$param];

					foreach($data[$param] as $index => $value)
					{
						$data[$param][$index] = str_replace("\'", "''", trim(htmlspecialchars($value)));
					}

				}
				else
					$data[$param] = str_replace("\'", "''", trim(htmlspecialchars($HTTP_POST_VARS[$param])));
			}
			else if (!empty($HTTP_GET_VARS[$param]))
			{
				if ( is_array( $HTTP_GET_VARS[$param]) )
				{					
					$data[$param] = $HTTP_GET_VARS[$param];

					foreach($data[$param] as $index => $value)
					{
						$data[$param][$index] = str_replace("\'", "''", trim(htmlspecialchars($value)));
					}

				}
				else
					$data[$param] = str_replace("\'", "''", trim(htmlspecialchars($HTTP_GET_VARS[$param])));
			}
		}

		return $data;
	}

	/*========================================================================*/
	// Merge Int & String Data To One Array If Both Are Populated
	// Usage: merge_int_str_data(array(), array());
	/*========================================================================*/
	function merge_int_str_data($int_data, $str_data)
	{
		if ((!empty($int_data)) && (!empty($str_data)))
		{
			$return_data = array_merge($int_data, $str_data);
		}
		else if (!empty($int_data))
		{
			$return_data = $int_data;
		}
		else
		{
			$return_data = $str_data;
		}

		return $return_data;
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

		return $row['total_views'];
	}

	/*========================================================================*/
	// Update A Single Field For A Single Entry
	// Usage:  update_single_field('table name', 'set field' 'set value', 'where field', 'where value');
	/*========================================================================*/
	function update_single_field($table, $set_field, $set_value, $where_field, $where_value)
	{
		global $db;

		$set_value  = ( $set_value == "NULL" ) ? $set_value : "'$set_value'";

		$sql = "UPDATE $table 
			SET $set_field = $set_value 
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
	// Checks A User Is Allowed Perform An Action
	// Usage: check_permissions('required permission', 'redirect url on failure');
	/*========================================================================*/
	function check_permissions($required_permission, $redirect_url)
	{
		global $userdata, $template, $db, $garage_config;
	
		$required_permission = strtolower($required_permission);
	
		//Right Lets Start And Work Out Your User Level
		if ( $userdata['user_id'] == ANONYMOUS )
		{
			$your_level = 'GUEST';
		}
		else if ( $userdata['user_level'] == ADMIN )
		{
			$your_level = 'ADMIN';
		}
		else if ( $userdata['user_level'] == MOD )
		{
			$your_level = 'MOD';
		}
		else
		{
			$your_level = 'USER';
		}		

		//Get All Group Memberships
		$groupdata = $this->get_group_membership($userdata['user_id']);

		//Since We Now Allow A DENY We Need To Check That First
		if ( !empty($garage_config['private_deny_perms']) AND $userdata['user_level'] == ADMIN )
		{
			//Lets Find Out Which Groups Are Denied Access
			$sql = "SELECT config_value AS private_groups
				FROM ". GARAGE_CONFIG_TABLE ."
				WHERE config_name = 'private_deny_perms'";

			if( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not get permissions', '', __LINE__, __FILE__, $sql);
			}

			$private_perms = $db->sql_fetchrow($result);
			$private_groups = @explode(',', $private_perms['private_groups']);
			$db->sql_freeresult($result);
	
			for ( $i = 0; $i < count($groupdata); $i++ )
			{
				if ( in_array($groupdata[$i]['group_id'], $private_groups) )
				{
					//You Were Found To Be A Member Of A Denied Group And We Know Where To Send You
					if (!empty($redirect_url))
					{
						redirect(append_sid($redirect_url, true));
					}
					//You Were Found To Be A Member Of A Denied Group But No URL So Return False
					else
					{
						return (FALSE);
					}
				}
			}
		}

		//Right You Were Not Denied So Lets Check First For Global Permissions
		if ($garage_config[$required_permission . "_perms"] == '*')
		{
			//Looks Like Everyone Is Allowed Do This...So On Your Way
			return (TRUE);
		}	
		//Since Not Globally Allowed Lets See If Your Level Is Allowed For The Permission You Are Requesting
		else if (preg_match( "/$your_level/", $garage_config[$required_permission . "_perms"]))
		{
			//Good News Your User Level Is Allowed
			return (TRUE);
		}
		//Right We Need To Resort And See If Private Is Set For This Required Permission And See If You Qualify
		else if (preg_match( "/PRIVATE/", $garage_config[$required_permission . "_perms"]))
		{
			//Lets Get All Private Groups Granted This Permission
			$sql = "SELECT config_value AS private_groups
				FROM ". GARAGE_CONFIG_TABLE ."
				WHERE config_name = 'private_" . $required_permission . "_perms'";

			if( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not get permissions', '', __LINE__, __FILE__, $sql);
			}

			$private_perms = $db->sql_fetchrow($result);
			$private_groups = @explode(',', $private_perms['private_groups']);
			$db->sql_freeresult($result);
	
			for ($i = 0; $i < count($groupdata); $i++)
			{
				if (in_array($groupdata[$i]['group_id'], $private_groups))
				{
					return (TRUE);
				}
			}
		}
		//Looks Like You Are Out Of Look...You Are Not Allowed Perform The Action You Requested...
		if (!empty($redirect_url))
		{
			redirect(append_sid("$redirect_url", true));
		}
		//No URL To Redirect So We Will Just Return FALSE
		else
		{
			return (FALSE);
		}
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

		return $garage_config['items_pending'];
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
			$data['link'] = '<a href="'.append_sid("garage.' . $phpEx . '?mode=garage_pending").'">' . $lang['Here'] . '</a>';
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
 		@fclose($log_handle);
	}
}

$garage = new garage();

?>

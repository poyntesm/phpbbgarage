<?php
/***************************************************************************
 *                              class_garage_admin.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: class_garage_admin.php 137 2006-06-07 09:53:18Z poyntesm $
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

class garage_admin
{

	var $classname = "garage_admin";

	/*========================================================================*/
	// Inserts Category Into DB
	// Usage: insert_category(array());
	/*========================================================================*/
	function insert_category($data)
	{
		global $db;

		$sql = "INSERT INTO ". GARAGE_CATEGORIES_TABLE ." 
			(title, field_order)
			VALUES 
			('" . $data['title'] . "', " . $data['field_order'] . " )";
	
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert New Category', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Count The Modification Categories Within The Garage
	// Usage: count_categories();
	/*========================================================================*/
	function count_categories()
	{
		global $db;

	        // Get the total count of mods in the garage
		$sql = "SELECT count(*) AS total 
			FROM " . GARAGE_CATEGORIES_TABLE;

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Error Counting Categories', '', __LINE__, __FILE__, $sql);
		}

        	$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$row['total'] = (empty($row['total'])) ? 0 : $row['total'];

		return $row['total'];
	}

	/*========================================================================*/
	// Set Config Values Within The Garage
	// Usage: set_config();
	/*========================================================================*/
	function set_config($config_name, $config_value, $garage_config)
	{
		global $db ;

		$sql = 'UPDATE ' . GARAGE_CONFIG_TABLE . "
			SET config_value = '" . $db->sql_escape($config_value) . "'
			WHERE config_name = '" . $db->sql_escape($config_name) . "'";
		$db->sql_query($sql);

		if (!$db->sql_affectedrows() && !isset($garage_config[$config_name]))
		{
			$sql = 'INSERT INTO ' . GARAGE_CONFIG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
				'config_name'	=> $config_name,
				'config_value'	=> $config_value));
			$db->sql_query($sql);
		}

		$garage_config[$config_name] = $config_value;
	}
}

$garage_admin = new garage_admin();

?>

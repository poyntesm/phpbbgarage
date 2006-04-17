<?php
/***************************************************************************
 *                              functions_garage.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: functions_garage.php 91 2006-04-07 14:51:14Z poyntesm $
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

class garage_business
{
	var $classname = "garage_business";

	/*========================================================================*/
	// Inserts Business Into DB
	// Usage: insert_business(array());
	/*========================================================================*/
	function insert_business($data)
	{
		global $db;

		$sql = "INSERT INTO ". GARAGE_BUSINESS_TABLE ." 
			SET title = '".$data['title']."', address = '".$data['address']."', telephone = '".$data['telephone']."', fax = '".$data['fax']."', website = '".$data['website']."', email = '".$data['email']."', opening_hours = '".$data['opening_hours']."', insurance = '".$data['insurance']."', garage = '".$data['garage']."', retail_shop = '".$data['retail_shop']."', web_shop = '".$data['web_shop']."', pending = '".$data['pending']."'";
	
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert New Business', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Updates Business Into DB
	// Usage: update_business(array());
	/*========================================================================*/
	function update_business($data)
	{
		global $db;

		$sql = "UPDATE ". GARAGE_BUSINESS_TABLE ." 
			SET title = '".$data['title']."', address = '".$data['address']."', telephone = '".$data['telephone']."', fax = '".$data['fax']."', website = '".$data['website']."', email = '".$data['email']."', opening_hours = '".$data['opening_hours']."', insurance = '".$data['insurance']."', garage = '".$data['garage']."', retail_shop = '".$data['retail_shop']."', web_shop = '".$data['web_shop']."', pending = '".$data['pending']."'
			WHERE id = '".$data['id']."'";
	
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Update Business', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Select Business Data From DB
	// Usage: select_business_data('business id');
	/*========================================================================*/
	function select_business_data($bus_id)
	{
		global $db;

		$sql = "SELECT * FROM " . GARAGE_BUSINESS_TABLE ;

		if (!empty($bus_id))
		{
			$sql .= " WHERE id = '$bus_id'";
		}

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Business Data', '', __LINE__, __FILE__, $sql);
		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}

		$db->sql_freeresult($result);

		return $data;
	}
}

$garage_business = new garage_business();

?>

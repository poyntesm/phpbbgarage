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

class garage_insurance
{
	var $classname = "garage_insurance";

	/*========================================================================*/
	// Insurance Insurance Into DB
	// Usage: insert_insurance(array());
	/*========================================================================*/
	function insert_insurance($data)
	{
		global $cid, $db;

		$sql = "INSERT INTO ". GARAGE_INSURANCE_TABLE ."
			SET garage_id = '$cid', premium = '".$data['premium']."', cover_type = '".$data['cover_type']."', comments = '".$data['comments']."', business_id = '".$data['business_id']."'";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert Insurance Premium', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Updates Insurance In DB
	// Usage: update_insurance(array());
	/*========================================================================*/
	function update_insurance($data)
	{
		global $db, $cid, $ins_id;

		// Now we update this row
		$sql = "UPDATE ". GARAGE_INSURANCE_TABLE ."
			SET business_id = '".$data['business_id']."', premium = '".$data['premium']."', cover_type = '".$data['cover_type']."', comments = '".$data['comments']."' 
			WHERE id = '$ins_id' and garage_id = '$cid'";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Update Insurance Premium', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Delete Insurance Entry
	// Usage: delete_insurance('insurance id');
	/*========================================================================*/
	function delete_insurance($ins_id)
	{
		global $db;
	
		//Right They Want To Delete A QuarterMile Time
		if (empty($ins_id))
		{
	 		message_die(GENERAL_ERROR, 'Insurance ID Not Entered', '', __LINE__, __FILE__);
		}

		//Time To Delete The Actual Insurance Premium
		$this->delete_rows(GARAGE_INSURANCE_TABLE, 'id', $ins_id);	
	
		return ;
	}
	
	/*========================================================================*/
	// Select All Insurance Data From Db
	// Usage: select_all_vehicle_data();
	/*========================================================================*/
	function select_all_insurance_data($additional_where, $order_by, $sort_order, $start, $end)
	{
		global $db;
		//Select All Vehicles Information
		$sql = "SELECT i.*, g.*, b.title, b.id as business_id, makes.make, models.model, user.username, user.user_id,
                        ( SUM(mods.price) + SUM(mods.install_price) ) AS total_spent,
			CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle
        		FROM " . GARAGE_INSURANCE_TABLE . " AS i 
                    		LEFT JOIN " . GARAGE_TABLE . " AS g ON i.garage_id = g.id
	                    	LEFT JOIN " . GARAGE_MODS_TABLE . " AS mods ON i.garage_id = mods.garage_id
        	            	LEFT JOIN " . GARAGE_BUSINESS_TABLE . " AS b ON i.business_id = b.id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
		        	LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id 
			        LEFT JOIN " . USERS_TABLE . " AS user ON g.member_id = user.user_id 
			WHERE makes.pending = 0 AND models.pending = 0
				".$search_data['where']."
		        GROUP BY i.id
			ORDER BY $order_by $sort_order";
		if ( (!empty($start)) AND (!empty($end)) )
		{
			$sql .= "LIMIT $start, $end";
		}

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Get Insurance Data', '', __LINE__, __FILE__, $sql);
      		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}
		$db->sql_freeresult($result);

		return $rows;
	}

	/*========================================================================*/
	// Select All Insurance Data From DB
	// Usage: select_insurance_data('insurance id');
	/*========================================================================*/
	function select_insurance_data($ins_id)
	{
		global $db;

		$sql = "SELECT ins.*, bus.title, g.made_year, makes.make, models.model, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle
     			FROM " . GARAGE_INSURANCE_TABLE . " AS ins 
                        	LEFT JOIN " . GARAGE_BUSINESS_TABLE . " AS bus ON ins.business_id = bus.id
		          	LEFT JOIN " . GARAGE_TABLE . " AS g ON ins.garage_id = g.id
		          	LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
                        	LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
        		WHERE ins.id = $ins_id ";

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select Insurance', '', __LINE__, __FILE__, $sql);
      		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row;
	}

	/*========================================================================*/
	// Select Model Data From DB
	// Usage: select_model_data('model id');
	/*========================================================================*/
	function select_insurance_business_data($start, $where)
	{
		global $db, $where;

		// Select Each Business
      		$sql = "SELECT b.*, COUNT(DISTINCT b.id) as total
       	 		FROM  " . GARAGE_BUSINESS_TABLE . " b 
       			WHERE b.insurance = 1 
				AND b.pending = 0
				$where
			GROUP BY b.id";
		if (!empty($start))
		{
			$sql .=	"LIMIT $start, 25";
		}

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Select Business Data', '', __LINE__, __FILE__, $sql);
      		}

		while( $row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}
      		$db->sql_freeresult($result);

		return $rows;

	}

}

$garage_insurance = new garage_insurance();

?>

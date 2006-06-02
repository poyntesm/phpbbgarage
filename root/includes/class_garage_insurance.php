<?php
/***************************************************************************
 *                              class_garage_insurance.php
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
			(garage_id, premium, cover_type, comments, business_id)
			VALUES
			('$cid', '".$data['premium']."', '".$data['cover_type']."', '".$data['comments']."', '".$data['business_id']."')";

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
	
		//Right They Want To Delete A Insurance
		if (empty($ins_id))
		{
	 		message_die(GENERAL_ERROR, 'Insurance ID Not Entered', '', __LINE__, __FILE__);
		}

		//Time To Delete The Actual Insurance Premium
		$this->delete_rows(GARAGE_INSURANCE_TABLE, 'id', $ins_id);	
	
		return ;
	}
	
	/*========================================================================*/
	// Select All Insurance Data From DB
	// Usage: select_all_vehicle_data();
	/*========================================================================*/
	function select_all_insurance_data($additional_where, $order_by, $sort_order, $start, $end)
	{
		global $db;

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
	// Select Specific Insurance Premium Data From DB
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
	// Select All Insurance Premium Data From Specific Insurance Company From DB
	// Usage: get_all_premiuminsurance_data('business id');
	/*========================================================================*/
	function select_all_premiums_data($business_id)
	{
		global $db;

		$sql = "SELECT i.*, g.made_year, b.title, b.id as business_id, makes.make, models.model, user.username, user.user_id, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle
       			FROM " . GARAGE_INSURANCE_TABLE . " i 
               	    		LEFT JOIN " . GARAGE_TABLE . " g ON ( i.garage_id = g.id )
       	        	    	LEFT JOIN " . GARAGE_BUSINESS_TABLE . " b ON ( i.business_id = b.id )
		        	LEFT JOIN " . GARAGE_MAKES_TABLE . " makes ON ( g.make_id = makes.id )
		        	LEFT JOIN " . GARAGE_MODELS_TABLE . " models ON ( g.model_id = models.id )
			        LEFT JOIN " . USERS_TABLE . " user ON ( g.member_id = user.user_id )
			WHERE i.business_id = b.id
				AND b.insurance =1
				AND b.pending = 0
				AND b.id = $business_id
				AND makes.pending = 0 AND models.pending = 0 
			GROUP BY i.id";
	   	if ( !($result = $db->sql_query($sql)) )
      		{
       			message_die(GENERAL_ERROR, 'Could Select All Insuance Data', '', __LINE__, __FILE__, $sql);
		}

		while( $row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}
      		$db->sql_freeresult($result);

		return $rows;
	}


	/*========================================================================*/
	// Select Insurance Premiums By Business From DB
	// Usage: select_insurance_premium_data('model id');
	/*========================================================================*/
	function select_premiums_from_business_data($business_id, $cover_type)
	{
		global $db, $where;

		$sql = "SELECT round(max( i.premium ),2) AS max, round(min( i.premium ),2) AS min, round(avg( i.premium ),2) AS avg
			FROM " . GARAGE_BUSINESS_TABLE . " b, " . GARAGE_INSURANCE_TABLE . " i
			WHERE i.business_id = b.id
				AND b.id = $business_id 
				AND b.insurance =1
				AND i.cover_type = '".htmlspecialchars($cover_type)."'
				AND i.premium > 0";

		if( !($result = $db->sql_query($sql)) )
       		{
            		message_die(GENERAL_ERROR, 'Could Not Select Insurance Premium Data', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row;
	}
}

$garage_insurance = new garage_insurance();

?>

<?php
/***************************************************************************
 *                              functions_garage.php
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

		$sql = "INSERT INTO ". GARAGE_BUSINESS_TABLE ." (title, address, telephone, fax, website, email, opening_hours, insurance, garage, retail_shop, web_shop, pending)
			VALUES ('".$data['title']."', '".$data['address']."', '".$data['telephone']."', '".$data['fax']."', '".$data['website']."', '".$data['email']."', '".$data['opening_hours']."', '".$data['insurance']."', '".$data['garage']."', '".$data['retail_shop']."', '".$data['web_shop']."', '".$data['pending']."')";
	
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
	// Select Single Business Data From DB
	// Usage: select_business_data('business id');
	/*========================================================================*/
	function select_business_data($bus_id)
	{
		global $db;

		$sql = "SELECT * 
			FROM " . GARAGE_BUSINESS_TABLE . "
			WHERE id = '$bus_id'";

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Specific Business Data', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select All Business Data From DB
	// Usage: select_all_business_data();
	/*========================================================================*/
	function select_all_business_data()
	{
		global $db;

		$sql = "SELECT * 
			FROM " . GARAGE_BUSINESS_TABLE ;

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select All Business Data', '', __LINE__, __FILE__, $sql);
		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}

		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select All Business Data From DB
	// Usage: select_all_garage_business_data();
	/*========================================================================*/
	function select_all_garage_business_data($where, $start)
	{
		global $db;

		$sql = "SELECT b.* , sum( install_rating ) AS rating, count( * ) *10 AS total_rating
			FROM " . GARAGE_BUSINESS_TABLE . " b, " . GARAGE_MODS_TABLE . " m
			WHERE m.install_business_id = b.id
				AND b.garage =1
				AND b.pending =0
				$where
			GROUP BY b.id
			ORDER BY rating DESC
			LIMIT $start, 25";
			
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

	/*========================================================================*/
	// Select All Business Data From DB
	// Usage: select_all_shop_business_data();
	/*========================================================================*/
	function select_all_shop_business_data($where, $start)
	{
		global $db;

		$sql = "SELECT b.* , sum( purchase_rating ) AS rating, count( * ) *10 AS total_rating
			FROM " . GARAGE_BUSINESS_TABLE . " b, " . GARAGE_MODS_TABLE . " m
			WHERE m.business_id = b.id
				AND ( b.web_shop =1 OR b.retail_shop = 1 )
				AND b.pending =0
				$where
			GROUP BY b.id
			ORDER BY rating DESC
			LIMIT $start, 25";
			
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


	/*========================================================================*/
	// Select All Business Data From DB
	// Usage: count_garage_business_data();
	/*========================================================================*/
	function count_garage_business_data($additional_where)
	{
		global $db;

		$sql = "SELECT count(DISTINCT b.title) as total
			FROM " . GARAGE_BUSINESS_TABLE . " b, " . GARAGE_MODS_TABLE . " m
			WHERE m.install_business_id = b.id
				AND b.garage =1
				AND b.pending =0
				$additional_where";

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Getting Pagination Total', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row['total'];
	}

	/*========================================================================*/
	// Select All Business Data From DB
	// Usage: count_shop_business_data();
	/*========================================================================*/
	function count_shop_business_data($additional_where)
	{
		global $db;

		$sql = "SELECT count(DISTINCT b.title) as total
			FROM " . GARAGE_BUSINESS_TABLE . " b, " . GARAGE_MODS_TABLE . " m
			WHERE m.business_id = b.id
				AND ( b.web_shop =1 OR b.retail_shop =1 )
				AND b.pending =0
				$additional_where";

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Getting Pagination Total', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row['total'];
	}

	/*========================================================================*/
	// Build Business List
	// Usage: build_business_table('business id');
	/*========================================================================*/
	function build_business_table($pending)
	{
		global $db, $template, $images, $phpEx, $start, $sort, $sort_order, $garage_config, $lang, $theme, $mode, $HTTP_POST_VARS, $HTTP_GET_VARS;

		$pending = ($pending == 'YES') ? 1 : 0;

		$sql = "SELECT bus.* 
			FROM " . GARAGE_BUSINESS_TABLE ." AS bus
			WHERE bus.pending = 1";

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
		}

		$count = $db->sql_numrows($result);

		if ($count >= 1)
		{
			$template->assign_block_vars('business_pending', array());
		}

		// loop through users
		$i = 1;
		while ( $row = $db->sql_fetchrow($result) )
		{
            		$temp_url = append_sid("garage.$phpEx?mode=edit_business&amp;BUS_ID=".$row['id']);
	            	$edit_link = '<a href="' . $temp_url . '"><img src="' . $images['garage_edit'] . '" alt="'.$lang['Edit'].'" title="'.$lang['Edit'].'" border="0" /></a>';

			//Work Out Type Of Business
			if ( $row['insurance'] == '1' )
			{
		       	 	$type = $lang['Insurance'] ;
			}
			if ( ($row['garage'] == '1') AND ( ($row['web_shop'] == '1') OR ($row['retail_shop'] == '1')  ))
			{
				$type = $lang['Garage'] . ", " .  $lang['shop'] ;
			}
			if ( $row['garage'] == '1' )
			{
				$type = $lang['Garage'] ;
			}
			if ( $row['web_shop'] == '1' OR $row['retail_shop'] == '1' )
			{
				$type = $lang['Shop'];
			}
			
			// setup user row template varibles
			$template->assign_block_vars('business_pending.row', array(
				'ROW_NUMBER' => $i + ( $HTTP_GET_VARS['start'] + 1 ),
				'ROW_CLASS' => ( !($i % 2) ) ? $theme['td_class1'] : $theme['td_class2'],
				'BUSID' => $row['id'],
				'NAME' => $row['title'],
				'ADDRESS' => $row['address'], 
				'TELEPHONE' => $row['telephone'],
				'FAX' => $row['fax'],
				'WEBSITE' => $row['website'],
				'EMAIL' => $row['email'],
				'OPENING_HOURS' => $row['opening_hours'],
				'TYPE' => $type,
				'EDIT_LINK' => $edit_link)
			);
			$i++;
			unset($type);
		}
		$db->sql_freeresult($result);

		//Return Count Of Pending Items
		return $count;
	}

}

$garage_business = new garage_business();

?>

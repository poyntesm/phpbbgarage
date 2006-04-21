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

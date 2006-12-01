<?php
/***************************************************************************
 *                              class_garage_business.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: class_garage_business.php 156 2006-06-19 06:51:48Z poyntesm $
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

		$sql = 'INSERT INTO ' . GARAGE_BUSINESS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'title'		=> $data['title'],
			'address'	=> $data['address'],
			'telephone'	=> $data['telephone'],
			'fax'		=> $data['fax'],
			'website'	=> $data['website'],
			'email'		=> $data['email'],
			'opening_hours'	=> $data['opening_hours'],
			'insurance'	=> $data['insurance'],
			'garage'	=> $data['garage'],
			'retail_shop'	=> $data['retail_shop'],
			'web_shop'	=> $data['web_shop'],
			'pending'	=> $data['pending'])
		);

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert New Business', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Update Single Business
	// Usage: update_business(array());
	/*========================================================================*/
	function update_business($data)
	{
		global $db;

		$update_sql = array(
			'title'		=> $data['title'],
			'address'	=> $data['address'],
			'telephone'	=> $data['telephone'],
			'fax'		=> $data['fax'],
			'website'	=> $data['website'],
			'email'		=> $data['email'],
			'opening_hours'	=> $data['opening_hours'],
			'insurance'	=> $data['insurance'],
			'garage'	=> $data['garage'],
			'retail_shop'	=> $data['retail_shop'],
			'web_shop'	=> $data['web_shop'],
			'pending'	=> $data['pending']
		);

		$sql = 'UPDATE ' . GARAGE_BUSINESS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = " . $data['id'];

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Update Business', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Select Single Business Data From DB
	// Usage: get_business('business id');
	/*========================================================================*/
	function get_business($bus_id)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'b.*',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE	=> 'b',
			),
			'WHERE'		=>  "b.id = $bus_id"
		));

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
	// Usage: get_all_business();
	/*========================================================================*/
	function get_all_business()
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'b.*',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE	=> 'b',
			)
		));

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
	// Select All Garage Business Data From DB
	// Usage: get_all_garage_business('additional where', 'row start point', 'limit');
	/*========================================================================*/
	function get_garage_business($where, $start = 0, $limit = 20)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'b.*, SUM(install_rating) AS rating, COUNT(*) *10 AS total_rating',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE	=> 'b',
				GARAGE_MODS_TABLE	=> 'm',
			),
			'WHERE'		=>  "m.install_business_id = b.id AND b.garage =1 AND b.pending =0 $where",
			'GROUP_BY'	=>  "b.id",
			'ODER_BY'	=>  "rating DESC"
		));

      		if ( !($result = $db->sql_query_limit($sql, $limit, $start)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Select Business Data', '', __LINE__, __FILE__, $sql);
      		}

		while( $row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}
		$db->sql_freeresult($result);

		if (empty($rows))
		{
			return;
		}

		return $rows;
	}

	/*========================================================================*/
	// Select All Shop Business Data From DB
	// Usage: get_shop_business('additional where', 'row start point', 'limit');
	/*========================================================================*/
	function get_shop_business($where, $start = 0, $limit = 20)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'b.*, SUM(purchase_rating) AS rating, COUNT(*) *10 AS total_rating',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE	=> 'b',
				GARAGE_MODS_TABLE	=> 'm',
			),
			'WHERE'		=>  "m.business_id = b.id AND ( b.web_shop =1 OR b.retail_shop = 1 ) AND b.pending =0 $where",
			'GROUP_BY'	=>  "b.id",
			'ODER_BY'	=>  "rating DESC"
		));

      		if ( !($result = $db->sql_query_limit($sql, $limit, $start)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Select Business Data', '', __LINE__, __FILE__, $sql);
      		}

		while( $row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}
		$db->sql_freeresult($result);

		if (empty($rows))
		{
			return;
		}

		return $rows;
	}

	/*========================================================================*/
	// Select All Insurance Business Data From DB
	// Usage: get_insurance_business('additional where', 'row start point', 'limit')
	/*========================================================================*/
	function get_insurance_business($where, $start = 0,  $limit = 20)
	{
		global $db, $where;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'b.*, COUNT(DISTINCT b.id) as total',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE	=> 'b',
			),
			'WHERE'		=>  "b.insurance = 1 AND b.pending = 0	$where",
			'GROUP_BY'	=>  "b.id"
		));

      		if ( !($result = $db->sql_query_limit($sql, $limit, $start)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Select Business Data', '', __LINE__, __FILE__, $sql);
      		}

		while( $row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}
      		$db->sql_freeresult($result);

		if (empty($rows))
		{
			return;
		}

		return $rows;
	}

	/*========================================================================*/
	// Count Garage Business Data In DB
	// Usage: count_garage_business_data('additional where');
	/*========================================================================*/
	function count_garage_business_data($additional_where)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'count(DISTINCT b.title) as total',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE	=> 'b',
				GARAGE_MODSTABLE	=> 'm',
			),
			'WHERE'		=>  "m.install_business_id = b.id AND b.garage =1 AND b.pending =0 $additional_where"
		));

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Getting Pagination Total', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row['total'];
	}

	/*========================================================================*/
	// Count Shop Business Data In DB
	// Usage: count_shop_business_data('additional where');
	/*========================================================================*/
	function count_shop_business_data($additional_where)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(DISTINCT b.title) as total',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE	=> 'b',
				GARAGE_MODS_TABLE	=> 'm',
			),
			'WHERE'		=>  "m.business_id = b.id AND ( b.web_shop =1 OR b.retail_shop =1 ) AND b.pending =0 $additional_where"
		));

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Getting Pagination Total', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row['total'];
	}

	/*========================================================================*/
	// Build Business List With Or Without Pending Items
	// Usage: build_business_table('YES|NO');
	/*========================================================================*/
	function build_business_table($pending)
	{
		global $db, $template, $images, $phpEx, $start, $sort, $sort_order, $lang, $theme, $HTTP_GET_VARS, $user;

		$pending = ($pending == 'YES') ? 1 : 0;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'b.*',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE	=> 'b',
			),
			'WHERE'		=>  "b.pending = 1"
		));

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Query Pending Business List', '', __LINE__, __FILE__, $sql);
		}

		$rows = NULL;
		while ( $row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}
		$db->sql_freeresult($result);

		if (sizeof($rows) >= 1)
		{
			$template->assign_block_vars('business_pending', array());
		}

		// loop through users
		for ($i = 0, $count = sizeof($rows); $i < $count; $i++)
		{
			//Work Out Type Of Business
			$type = null;
			$type .= ( $rows[$i]['insurance'] == '1' ) ? $user->lang['INSURANCE'] . ', ' : '';
			$type .= ( $rows[$i]['garage'] == '1' ) ? $user->lang['GARAGE'] . ', ' : '';
			$type .= ( $rows[$i]['web_shop'] == '1' OR $rows[$i]['retail_shop'] == '1' ) ? $user->lang['SHOP'] . ', ' : '';
			$type = rtrim($type, ', ');
			
			$template->assign_block_vars('business_pending.row', array(
				'U_EDIT'	=> append_sid("garage.$phpEx", "mode=edit_business&amp;BUS_ID=" . $rows[$i]['id'] . "&amp;PENDING=YES"),
				'BUSID' 	=> $rows[$i]['id'],
				'NAME' 		=> $rows[$i]['title'],
				'ADDRESS' 	=> $rows[$i]['address'], 
				'TELEPHONE' 	=> $rows[$i]['telephone'],
				'FAX' 		=> $rows[$i]['fax'],
				'WEBSITE' 	=> $rows[$i]['website'],
				'EMAIL' 	=> $rows[$i]['email'],
				'OPENING_HOURS' => $rows[$i]['opening_hours'],
				'TYPE' 		=> $type)
			);
			unset($type);
		}

		//Return Count Of Pending Items
		return $count;
	}
}

$garage_business = new garage_business();

?>

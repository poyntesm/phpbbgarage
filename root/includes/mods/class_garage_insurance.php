<?php
/***************************************************************************
 *                              class_garage_insurance.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: class_garage_insurance.php 156 2006-06-19 06:51:48Z poyntesm $
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
	// Usage: insert_premium(array());
	/*========================================================================*/
	function insert_premium($data)
	{
		global $cid, $db;

		$sql = 'INSERT INTO ' . GARAGE_PREMIUMS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'garage_id'	=> $cid,
			'premium'	=> $data['premium'],
			'cover_type'	=> $data['cover_type'],
			'comments'	=> $data['comments'],
			'business_id'	=> $data['business_id'])
		);

		$db->sql_query($sql);

		return;
	}

	/*========================================================================*/
	// Updates Insurance In DB
	// Usage: update_insurance(array());
	/*========================================================================*/
	function update_premium($data)
	{
		global $db, $cid, $ins_id;

		$update_sql = array(
			'premium'	=> $data['premium'],
			'cover_type'	=> $data['cover_type'],
			'comments'	=> $data['comments'],
			'business_id'	=> $data['business_id']
		);

		$sql = 'UPDATE ' . GARAGE_PREMIUMS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = $ins_id AND garage_id = $cid";


		$db->sql_query($sql);

		return;
	}

	/*========================================================================*/
	// Delete Insurance Premium
	// Usage: delete_premium('insurance id');
	/*========================================================================*/
	function delete_premium($ins_id)
	{
		global $garage;
	
		$garage->delete_rows(GARAGE_PREMIUMS_TABLE, 'id', $ins_id);	
	
		return ;
	}

	/*========================================================================*/
	// Select Specific Insurance Premium Data From DB
	// Usage: get_premium('insurance id');
	/*========================================================================*/
	function get_premium($ins_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'in.*, b.title, g.made_year, mk.make, md.model, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle',
			'FROM'		=> array(
				GARAGE_PREMIUMS_TABLE	=> 'in',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'g.id = in.garage_id'
				)
				,array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'mk'),
					'ON'	=> 'g.make_id = mk.id and mk.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'md'),
					'ON'	=> 'g.model_id = md.id and md.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_BUSINESS_TABLE => 'b'),
					'ON'	=> 'b_id = in.business_id'
				)
			),
			'WHERE'		=>  "in.id = $ins_id"
		));

      		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select All Insurance Premiums Data From DB
	// Usage: get_all_premiums('additional where', 'order', 'ASC|DESC', 'start', 'end');
	/*========================================================================*/
	function get_all_premiums($additional_where = NULL, $order_by, $sort_order, $start = 0, $limit = 10000)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'i.*, g.*, b.title, b.id as business_id, mk.make, md.model, u.username, u.user_id, ( SUM(m.price) + SUM(m.install_price) ) AS total_spent, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle',
			'FROM'		=> array(
				GARAGE_PREMIUMS_TABLE	=> 'i',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'g.id = in.garage_id'
				)
				,array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'mk'),
					'ON'	=> 'g.make_id = mk.id and mk.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'md'),
					'ON'	=> 'g.model_id = md.id and md.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_BUSINESS_TABLE => 'b'),
					'ON'	=> 'i.business_id = b.id'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
				)
				,array(
					'FROM'	=> array(GARAGE_MODIFICATIONS_TABLE => 'm'),
					'ON'	=> 'i.garage_id = m.garage_id'
				)
			),
			'WHERE'		=>  "mk.pending = 0 AND md.pending = 0 $additional_where",
			'GROUP_BY'	=>  "i.id",
			'ORDER_BY'	=>  "$order_by $sort_order"
		));

      		$result = $db->sql_query_limit($sql, $limit, $start);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select All Insurance Premiums Data From Specific Insurance Company From DB
	// Usage: get_all_premium_by_business_data('business id');
	/*========================================================================*/
	function get_all_premiums_by_business($business_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'i.*, g.made_year, b.title, b.id as business_id, mk.make, md.model, u.username, u.user_id, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle',
			'FROM'		=> array(
				GARAGE_PREMIUMS_TABLE	=> 'i',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'g.id = in.garage_id'
				)
				,array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'mk'),
					'ON'	=> 'g.make_id = mk.id and mk.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'md'),
					'ON'	=> 'g.model_id = md.id and md.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_BUSINESS_TABLE => 'b'),
					'ON'	=> 'i.business_id = b.id'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
				)
			),
			'WHERE'		=>  "i.business_id = b.id AND b.type = " . BUSINESS_INSURANCE . " AND b.pending = 0 AND b.id = $business_id AND mk.pending = 0 AND md.pending = 0",
			'GROUP_BY'	=>  "i.id"
		));

	   	$result = $db->sql_query($sql);
		while($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
      		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select Premiums Statistics By Business And Covertype From DB
	// Usage: get_premiums_stats_by_business_and_covertype('business id', 'cover type');
	/*========================================================================*/
	function get_premiums_stats_by_business_and_covertype($business_id, $cover_type)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'round(max( i.premium ),2) AS max, round(min( i.premium ),2) AS min, round(avg( i.premium ),2) AS avg',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE	=> 'b',
				GARAGE_PREMIUMS_TABLE	=> 'i',
			),
			'WHERE'		=>  "i.business_id = b.id AND b.id = $business_id AND b.type = " . BUSINESS_INSURANCE . " AND i.cover_type = '".htmlspecialchars($cover_type)."' AND i.premium > 0"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select Insurance Premiums By Vehicle From DB
	// Usage: get_premiums_by_vehicle('vehicle id');
	/*========================================================================*/
	function get_premiums_by_vehicle($cid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'i.*, b.*',
			'FROM'		=> array(
				GARAGE_PREMIUMS_TABLE	=> 'i',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_BUSINESS_TABLE => 'b'),
					'ON'	=> 'i.business_id = b.id'
				)
			),
			'WHERE'		=>  "i.garage_id = $cid"
		));
	
	       	$result = $db->sql_query($sql);
		while($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
      		$db->sql_freeresult($result);

		return $data;
	}
}

$garage_insurance = new garage_insurance();

?>

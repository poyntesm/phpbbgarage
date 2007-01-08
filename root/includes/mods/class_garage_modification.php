<?php
/***************************************************************************
 *                              class_garage_modification.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: class_garage_modification.php 138 2006-06-07 15:55:46Z poyntesm $
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

class garage_modification
{
	var $classname = "garage_modification";

	/*========================================================================*/
	// Inserts Modification Into DB
	// Usage: insert_modification(array('', '', '', '', ''));
	/*========================================================================*/
	function insert_modification($data)
	{
		global $cid, $db, $garage_vehicle;

		$sql = 'INSERT INTO ' . GARAGE_MODIFICATIONS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'garage_id'		=> $cid,
			'user_id'		=> $garage_vehicle->get_vehicle_owner_id($cid),
			'category_id'		=> $data['category_id'],
			'manufacturer_id'	=> $data['manufacturer_id'],
			'product_id'		=> $data['product_id'],
			'shop_id'		=> $data['shop_id'],
			'installer_id'		=> $data['installer_id'],
			'price'			=> $data['price'],
			'purchase_rating'	=> $data['purchase_rating'],
			'comments'		=> $data['comments'],
			'install_price'		=> $data['install_price'],
			'install_rating'	=> $data['install_rating'],
			'install_comments'	=> $data['install_comments'],
			'product_rating'	=> $data['product_rating'],
			'date_created'		=> time(),
			'date_updated'		=> time(),
		));

		$db->sql_query($sql);
	
		return $db->sql_nextid();
	}

	/*========================================================================*/
	// Updates Modification In DB
	// Usage:  update_modification(array());
	/*========================================================================*/
	function update_modification($data)
	{
		global $db, $cid, $mid, $garage_vehicle;

		$update_sql = array(
			'garage_id'		=> $cid,
			'user_id'		=> $garage_vehicle->get_vehicle_owner_id($cid),
			'category_id'		=> $data['category_id'],
			'manufacturer_id'	=> $data['manufacturer_id'],
			'product_id'		=> $data['product_id'],
			'shop_id'		=> $data['shop_id'],
			'installer_id'		=> $data['installer_id'],
			'price'			=> $data['price'],
			'purchase_rating'	=> $data['purchase_rating'],
			'comments'		=> $data['comments'],
			'install_price'		=> $data['install_price'],
			'install_rating'	=> $data['install_rating'],
			'install_comments'	=> $data['install_comments'],
			'product_rating'	=> $data['product_rating'],
			'date_created'		=> time(),
			'date_updated'		=> time(),
		);

		$sql = 'UPDATE ' . GARAGE_MODIFICATIONS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = $mid AND garage_id = $cid";

		$db->sql_query($sql);

		return;
	}

	/*========================================================================*/
	// Delete Modification Entry Including image 
	// Usage: delete_modification('modification id');
	/*========================================================================*/
	function delete_modification($mid)
	{
		global $garage, $garage_image;
	
		$data = $this->get_modification($mid);
	
		if (!empty($data['image_id']))
		{
			$garage_image->delete_image($data['image_id']);
		}
	
		$garage->delete_rows(GARAGE_MODIFICATIONS_TABLE, 'id', $mid);
	
		return ;
	}

	/*========================================================================*/
	// Inserts Product Into DB
	// Usage: insert_product(array());
	/*========================================================================*/
	function insert_product($data)
	{
		global $cid, $db, $garage_vehicle;

		$sql = 'INSERT INTO ' . GARAGE_PRODUCTS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'category_id'		=> $data['category_id'],
			'business_id'		=> $data['manufacturer_id'],
			'title'			=> $data['title'])
		);

		$db->sql_query($sql);
	
		return $db->sql_nextid();
	}

	/*========================================================================*/
	// Determines If Image Is Hilite Image
	// Usage: hilite_exists('vehicle id', 'modification id');
	/*========================================================================*/
	function hilite_exists($mid)
	{
		$hilite = 1;

		if ($this->count_modification_images($mid) > 0)
		{
			$hilite = 0;
		}
	
		return $hilite;
	}

	/*========================================================================*/
	// Returns Count Of Modiciation Images
	// Usage: count_modification_images('modification id');
	/*========================================================================*/
	function count_modification_images($mid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(mg.id) as total',
			'FROM'		=> array(
				GARAGE_MODIFICATION_GALLERY_TABLE	=> 'mg',
			),
			'WHERE'		=> "mg.modification_id = $mid"
		));

		$result = $db->sql_query($sql);
	        $data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$data['total'] = (empty($data['total'])) ? 0 : $data['total'];
		return $data['total'];
	}

	/*========================================================================*/
	// Count The Total Modifications Within The Garage
	// Usage: count_total_modifications();
	/*========================================================================*/
	function count_total_modifications()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(m.id) as total',
			'FROM'		=> array(
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
			)
		));

		$result = $db->sql_query($sql);
        	$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$data['total'] = (empty($data['total'])) ? 0 : $data['total'];
		return $data['total'];
	}

	/*========================================================================*/
	// Select Modifcations Based On Updated Time
	// Usage: get_updated_modifications('limit');
	/*========================================================================*/
	function get_updated_modifications($limit)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.id, m.garage_id, m.user_id, p.title AS mod_title, m.date_updated AS POI, u.username',
			'FROM'		=> array(
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_PRODUCTS_TABLE => 'p'),	
					'ON'	=> 'm.product_id = p.id'
				)
				,array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'm.garage_id = g.id'
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
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'm.user_id = u.user_id'
				)
			),
			'WHERE'		=> "mk.pending = 0 AND md.pending = 0",
			'ORDER_BY'	=> "POI DESC"
		));

	 	$result = $db->sql_query_limit($sql, $limit);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select Modifcations Based On Manufacture. Can Be limited Also By Category ID
	// Usage: get_products_by_manufacturer('business_id', category_id);
	/*========================================================================*/
	function get_products_by_manufacturer($business_id, $category_id = null)
	{
		global $db;

		$data = null;

		$sql_array = array(
			'SELECT'	=> 'p.*',
			'FROM'		=> array(
				GARAGE_PRODUCTS_TABLE	=> 'p',
			),
			'WHERE'		=> "p.business_id = $business_id"
		);

		if (!empty($category_id))
		{
			$sql_array['WHERE'] .= " AND p.category_id = $category_id";
		}

		$sql = $db->sql_build_query('SELECT', array(
			'SELECT'	=> $sql_array['SELECT'],
			'FROM'		=> $sql_array['FROM'],
			'WHERE'		=> $sql_array['WHERE'],
		));

	 	$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select Modifcations Based On Created Time
	// Usage: get_newest_modifications('limit');
	/*========================================================================*/
	function get_newest_modifications($limit)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.id, m.garage_id, m.user_id, p.title AS mod_title, m.date_created AS POI, u.username',
			'FROM'		=> array(
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_PRODUCTS_TABLE => 'p'),	
					'ON'	=> 'm.product_id = p.id'
				)
				,array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'm.garage_id = g.id'
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
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'm.user_id = u.user_id'
				)
			),
			'WHERE'		=> "mk.pending = 0 AND md.pending = 0",
			'ORDER_BY'	=> "POI DESC"
		));

	 	$result = $db->sql_query_limit($sql, $limit);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select Vehicles Based On Modification Count
	// Usage: get_most_modified('limit');
	/*========================================================================*/
	function get_most_modified($limit)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.id, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle, g.user_id, COUNT(m.id) AS POI, u.username',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'mk'),
					'ON'	=> 'g.make_id = mk.id and mk.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'md'),
					'ON'	=> 'g.model_id = md.id and md.pending = 0'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
				)
				,array(
					'FROM'	=> array(GARAGE_MODIFICATIONS_TABLE => 'm'),
					'ON'	=> 'g.id = m.garage_id'
				)
			),
			'WHERE'		=> "mk.pending = 0 AND md.pending = 0",
			'GROUP_BY'	=> "g.id",
			'ORDER_BY'	=> "POI DESC"
		));

	 	$result = $db->sql_query_limit($sql, $limit);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	
	/*========================================================================*/
	// Select Modification Data From DB
	// Usage: get_modification('modification id');
	/*========================================================================*/
	function get_modification($mid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.*, g.made_year, g.id, g.currency, i.*, u.username, u.user_avatar_type, u.user_avatar, c.title as category_title, mk.make, md.model, b1.title as business_title, b2.title as install_business_title, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle, p.title',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_PRODUCTS_TABLE => 'p'),	
					'ON'	=> 'm.product_id = p.id'
				)
				,array(
					'FROM'	=> array(GARAGE_CATEGORIES_TABLE => 'c'),
					'ON'	=> 'm.category_id = c.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MODIFICATION_GALLERY_TABLE => 'mg'),
					'ON'	=> 'm.id = mg.modification_id AND mg.hilite = 1',
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'mg.image_id = i.attach_id'
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
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
				)
				,array(
					'FROM'	=> array(GARAGE_BUSINESS_TABLE => 'b1'),
					'ON'	=> 'm.shop_id = b1.id'
				)
				,array(
					'FROM'	=> array(GARAGE_BUSINESS_TABLE => 'b2'),
					'ON'	=> 'm.installer_id = b2.id'
				)
			),
			'WHERE'		=> "m.id = $mid AND g.id = m.garage_id"
		));

      		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select Modifications From DB By Category
	// Usage: get_modification_by_category('modification id');
	/*========================================================================*/
	function get_modifications_by_category($cid, $category_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.*, i.*, p.title',
			'FROM'		=> array(
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_PRODUCTS_TABLE => 'p'),	
					'ON'	=> 'm.product_id = p.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MODIFICATION_GALLERY_TABLE => 'mg'),
					'ON'	=> 'm.id = mg.modification_id AND mg.hilite = 1',
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'mg.image_id = i.attach_id'
				)
			),
			'WHERE'		=> "m.garage_id = $cid AND m.category_id = $category_id",
			'ORDER_BY'	=> "p.title ASC"
		));

      		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}

		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select Modifications By Install Buisness From DB
	// Usage: get_modifications_by_install_business('business id', 'start row', 'end row');
	/*========================================================================*/
	function get_modifications_by_install_business($business_id, $start = 0 , $limit = 20)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> "m.id, m.garage_id, p.title AS mod_title, m.install_price, m.install_rating, m.install_comments, u.username, u.user_id, mk.make, md.model, g.made_year, b.id as business_id, CONCAT_WS(' ', g.made_year, mk.make, md.model) AS vehicle",
			'FROM'		=> array(
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
				GARAGE_BUSINESS_TABLE	=> 'b',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_PRODUCTS_TABLE => 'p'),	
					'ON'	=> 'm.product_id = p.id'
				)
				,array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'm.garage_id = g.id'
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
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
				)
			),
			'WHERE'		=> "m.installer_id = b.id AND b.type LIKE '%" . BUSINESS_GARAGE . "%' AND b.pending = 0 AND b.id = $business_id AND mk.pending = 0 AND md.pending = 0",
			'ORDER_BY'	=> "m.id, m.date_created DESC"
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
	// Select Modifications By Business From DB
	// Usage: get_modifications_by_business('business id', 'start row', 'end row');
	/*========================================================================*/
	function get_modifications_by_business($business_id, $start = 0, $limit = 20)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.id, m.garage_id, p.title AS mod_title, m.price, m.purchase_rating, m.product_rating, m.comments, u.username, u.user_id, mk.make, md.model, g.made_year, b.id as business_id, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle',
			'FROM'		=> array(
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
				GARAGE_BUSINESS_TABLE	=> 'b',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_PRODUCTS_TABLE => 'p'),	
					'ON'	=> 'm.product_id = p.id'
				)
				,array(
					'FROM'	=> array(GARAGE_VEHICLES_TABLE => 'g'),
					'ON'	=> 'm.garage_id = g.id'
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
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
				)
			),
			'WHERE'		=> "m.shop_id = b.id AND b.type LIKE '%". BUSINESS_RETAIL . "%' AND b.pending = 0 AND b.id = $business_id AND mk.pending = 0 AND md.pending = 0",
			'ORDER_BY'	=> "m.id, m.date_created DESC"
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
	// Select Modifications By Vehicle From DB
	// Usage: get_modifications_by_vehicle('garage id');
	/*========================================================================*/
	function get_modifications_by_vehicle($cid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.*, i.*, p.*',
			'FROM'		=> array(
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_PRODUCTS_TABLE => 'p'),	
					'ON'	=> 'm.product_id = p.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MODIFICATION_GALLERY_TABLE => 'mg'),
					'ON'	=> 'm.id = mg.modification_id AND mg.hilite = 1',
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'mg.image_id = i.attach_id'
				)
			),
			'WHERE'		=> "m.garage_id = $cid"
		));

		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}

		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Build Updated Modifications Template Variables If Required 
	// Usage: show_updated_modifications();
	/*========================================================================*/
	function show_updated_modifications()
	{
		global $required_position, $template, $phpEx, $garage_config, $user, $phpbb_root_path;
	
		if ( $garage_config['enable_updated_modification'] != true )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' 	=> $user->lang['LAST_UPDATED_MODIFICATIONS'],
			'COLUMN_1_TITLE'=> $user->lang['MODIFICATION'],
			'COLUMN_2_TITLE'=> $user->lang['OWNER'],
			'COLUMN_3_TITLE'=> $user->lang['UPDATED'])
		);
	 		
		$limit = $garage_config['updated_modification_limit'] ? $garage_config['updated_modification_limit'] : 10;

		$rows = $this->get_updated_modifications($limit);
		for($i = 0; $i < count($rows); $i++)
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_modification&amp;MID=" . $rows[$i]['id'] . "&amp;CID=" . $rows[$i]['garage_id']),
				'U_COLUMN_2' 	=> append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=" . $rows[$i]['user_id']),
				'COLUMN_1_TITLE'=> $rows[$i]['mod_title'],
				'COLUMN_2_TITLE'=> $rows[$i]['username'],
				'COLUMN_3' 	=> $user->format_date($rows[$i]['POI']))
			);
		}

		$required_position++;
		return ;
	}
	
	/*========================================================================*/
	// Build Most Modified Template Variables If Required 
	// Usage: show_most_modified();
	/*========================================================================*/
	function show_most_modified()
	{
		global $required_position, $user, $template, $phpEx, $garage_config, $phpbb_root_path;
	
		if ( $garage_config['enable_most_modified'] != true )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' 	=> $user->lang['MOST_MODIFIED_VEHICLE'],
			'COLUMN_1_TITLE'=> $user->lang['VEHICLE'],
			'COLUMN_2_TITLE'=> $user->lang['OWNER'],
			'COLUMN_3_TITLE'=> $user->lang['MODS'])
		);
	
		$limit = $garage_config['most_modified_limit'] ? $garage_config['most_modified_limit'] : 10;

		$rows = $this->get_most_modified($limit);
		for($i = 0; $i < count($rows); $i++)
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;CID=" . $rows[$i]['id']),
				'U_COLUMN_2' 	=> append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=" . $rows[$i]['user_id']),
				'COLUMN_1_TITLE'=> $rows[$i]['vehicle'],
				'COLUMN_2_TITLE'=> $rows[$i]['username'],
				'COLUMN_3' 	=> $rows[$i]['POI'])
			);
	 	}
	
		$required_position++;
		return ;
	}
	
	/*========================================================================*/
	// Build Newest Modifications Template Variables If Required 
	// Usage:  show_newest_modifications()
	/*========================================================================*/
	function show_newest_modifications()
	{
		global $required_position, $template, $phpEx, $garage_config, $user, $phpbb_root_path;
	
		if ( $garage_config['enable_newest_modification'] != true )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' 	=> $user->lang['NEWEST_MODIFICATIONS'],
			'COLUMN_1_TITLE'=> $user->lang['MODIFICATION'],
			'COLUMN_2_TITLE'=> $user->lang['OWNER'],
			'COLUMN_3_TITLE'=> $user->lang['CREATED'])
		);
	
	        $limit = $garage_config['newest_modification_limit'] ? $garage_config['newest_modification_limit'] : 10;

		$rows = $this->get_newest_modifications($limit);
		for($i = 0; $i < count($rows); $i++)
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_modification&amp;MID=" . $rows[$i]['id'] . "&amp;CID=" . $rows[$i]['garage_id']),
				'U_COLUMN_2' 	=> append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=" . $rows[$i]['user_id']),
				'COLUMN_1_TITLE'=> $rows[$i]['mod_title'],
				'COLUMN_2_TITLE'=> $rows[$i]['username'],
				'COLUMN_3' 	=> $user->format_date($rows[$i]['POI']))
			);
	 	}
	
		$required_position++;
		return ;
	}
}

$garage_modification = new garage_modification();

?>

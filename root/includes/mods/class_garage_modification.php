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
	// Usage: insert_modification(array());
	/*========================================================================*/
	function insert_modification($data)
	{
		global $cid, $db, $garage_vehicle;

		$sql = 'INSERT INTO ' . GARAGE_MODS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'garage_id'		=> $cid,
			'user_id'		=> $garage_vehicle->get_vehicle_owner_id($cid),
			'category_id'		=> $data['category_id'],
			'title'			=> $data['title'],
			'price'			=> $data['price'],
			'install_price'		=> $data['install_price'],
			'install_rating'	=> $data['install_rating'],
			'product_rating'	=> $data['product_rating'],
			'comments,'		=> $data['comments'],
			'date_created'		=> time(),
			'date_updated'		=> time(),
			'business_id'		=> $data['business_id'],
			'install_business_id'	=> $data['install_business_id'],
			'install_comments'	=> $data['install_comments'],
			'purchase_rating'	=> $data['purchase_rating'])
		);

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert Modification', '', __LINE__, __FILE__, $sql);
		}
	
		$mid = $db->sql_nextid();

		return $mid;
	}

	/*========================================================================*/
	// Updates Modification In DB
	// Usage:  update_modification(array());
	/*========================================================================*/
	function update_modification($data)
	{
		global $db, $cid, $mid;

		$update_sql = array(
			'garage_id'		=> $cid,
			'user_id'		=> $garage_vehicle->get_vehicle_owner_id($cid),
			'category_id'		=> $data['category_id'],
			'title'			=> $data['title'],
			'price'			=> $data['price'],
			'install_price'		=> $data['install_price'],
			'install_rating'	=> $data['install_rating'],
			'product_rating'	=> $data['product_rating'],
			'comments,'		=> $data['comments'],
			'date_updated'		=> time(),
			'business_id'		=> $data['business_id'],
			'install_business_id'	=> $data['install_business_id'],
			'install_comments'	=> $data['install_comments'],
			'purchase_rating'	=> $data['purchase_rating']
		);

		$sql = 'UPDATE ' . GARAGE_MODS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = $mid AND garage_id = $cid";


		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Update Modification', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Count The Total Modifications Within The Garage
	// Usage: count_total_modifications();
	/*========================================================================*/
	function count_total_modifications()
	{
		global $db;

	        // Get the total count of mods in the garage
		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(m.id) as total_mods',
			'FROM'		=> array(
				GARAGE_MODS_TABLE	=> 'm',
			)
		));

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Error Counting Total Mods', '', __LINE__, __FILE__, $sql);
		}

        	$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$row['total_mods'] = (empty($row['total_mods'])) ? 0 : $row['total_mods'];
		return $row['total_mods'];
	}

	/*========================================================================*/
	// Delete Modification Entry Including image 
	// Usage: delete_modification('modification id');
	/*========================================================================*/
	function delete_modification($mid)
	{
		global $garage, $garage_image;
	
		//Let Assign Variables To All Collected Info
		$data = $this->get_modification($mid);
	
		//Lets See If There Is An Image Associated With This Modification
		if (!empty($data['image_id']))
		{
			//Seems To Be An Image To Delete, Let Call The Function
			$garage_image->delete_image($data['image_id']);
		}
	
		//Time To Delete The Actual Modification Now
		$garage->delete_rows(GARAGE_MODS_TABLE, 'id', $mid);
	
		return ;
	}

	/*========================================================================*/
	// Select Modifcations Based On Updated Time
	// Usage: get_updated_modifications('limit');
	/*========================================================================*/
	function get_updated_modifications($limit)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.id, m.garage_id, m.user_id, m.title AS mod_title, m.date_updated AS POI, u.username',
			'FROM'		=> array(
				GARAGE_MODS_TABLE	=> 'm',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_TABLE => 'g'),
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

	 	if(!$result = $db->sql_query_limit($sql, $limit))
		{
			message_die(GENERAL_ERROR, "Could Not Select Latest Modifications", "", __LINE__, __FILE__, $sql);
		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		if (empty($data))
		{
			return;
		}
		return $data;
	}

	/*========================================================================*/
	// Select Modifcations Based On Created Time
	// Usage: get_newest_modifications('limit');
	/*========================================================================*/
	function get_newest_modifications($limit)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.id, m.garage_id, m.user_id, m.title AS mod_title, m.date_created AS POI, u.username',
			'FROM'		=> array(
				GARAGE_MODS_TABLE	=> 'm',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_TABLE => 'g'),
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

	 	if(!$result = $db->sql_query_limit($sql, $limit))
		{
			message_die(GENERAL_ERROR, "Could Not Select Newest Modifications", "", __LINE__, __FILE__, $sql);
		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		if (empty($data))
		{
			return;
		}
		return $data;
	}

	/*========================================================================*/
	// Select Vehicles Based On Modification Count
	// Usage: get_most_modified('limit');
	/*========================================================================*/
	function get_most_modified($limit)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.id, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle, g.user_id, COUNT(m.id) AS POI, u.username',
			'FROM'		=> array(
				GARAGE_TABLE	=> 'g',
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
					'FROM'	=> array(GARAGE_MODS_TABLE => 'm'),
					'ON'	=> 'g.id = m.garage_id'
				)
			),
			'WHERE'		=> "mk.pending = 0 AND md.pending = 0",
			'GROUP_BY'	=> "g.id",
			'ORDER_BY'	=> "POI DESC"
		));

	 	if(!$result = $db->sql_query_limit($sql, $limit))
		{
			message_die(GENERAL_ERROR, "Could Not Select Most Modified", "", __LINE__, __FILE__, $sql);
		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		if (empty($data))
		{
			return;
		}
		return $data;
	}

	
	/*========================================================================*/
	// Select All Modification Data From DB
	// Usage: get_modification('modification id');
	/*========================================================================*/
	function get_modification($mid)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.*, g.made_year, g.id, g.currency, i.*, u.username, u.user_avatar_type, u.user_avatar, c.title as category_title, mk.make, md.model, b1.title as business_title, b2.title as install_business_name, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle',
			'FROM'		=> array(
				GARAGE_TABLE		=> 'g',
				GARAGE_MODS_TABLE	=> 'm',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_CATEGORIES_TABLE => 'c'),
					'ON'	=> 'm.category_id = c.id'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGE_TABLE => 'i'),
					'ON'	=> 'm.image_id = i.attach_id'
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
					'ON'	=> 'm.business_id = b1.id'
				)
				,array(
					'FROM'	=> array(GARAGE_BUSINESS_TABLE => 'b2'),
					'ON'	=> 'm.install_business_id = b2.id'
				)
			),
			'WHERE'		=> "m.id = $mid AND g.id = m.garage_id"
		));

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select Modification Data', '', __LINE__, __FILE__, $sql);
      		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row;
	}

	/*========================================================================*/
	// Select Vehicle Modifications From DB By Category
	// Usage: get_modification_by_category('modification id');
	/*========================================================================*/
	function get_modifications_by_category($cid, $category_id)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.*, i.attach_id, i.attach_hits, i.attach_ext, i.attach_location, i.attach_file, i.attach_thumb_location, i.attach_is_image ',
			'FROM'		=> array(
				GARAGE_MODS_TABLE	=> 'm',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_IMAGE_TABLE => 'i'),
					'ON'	=> 'm.image_id = i.attach_id'
				)
			),
			'WHERE'		=> "m.garage_id = $cid AND m.category_id = $category_id",
			'ORDER_BY'	=> "title ASC"
		));

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select Modification Data', '', __LINE__, __FILE__, $sql);
		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}

		$db->sql_freeresult($result);

		return $rows;
	}

	/*========================================================================*/
	// Select Modifications By Install Buisness Data From DB
	// Usage: get_modifications_by_install_business('business id', 'start row', 'end row');
	/*========================================================================*/
	function get_modifications_by_install_business($business_id, $start = 0 , $limit = 20)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'SELECT m.id, m.garage_id, m.title AS mod_title, m.install_price, m.install_rating, m.install_comments, u.username, u.user_id, mk.make, md.model, g.made_year, b.id as business_id, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle',
			'FROM'		=> array(
				GARAGE_MODS_TABLE	=> 'm',
				GARAGE_BUSINESS_TABLE	=> 'b',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_TABLE => 'g'),
					'ON'	=> 'm._garage_id = g.id'
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
			'WHERE'		=> "m.install_business_id = b.id AND b.garage =1 AND b.pending = 0 AND b.id = $business_id AND mk.pending = 0 AND md.pending = 0",
			'ORDER_BY'	=> "m.id, m.date_created DESC"
		));

      		if ( !($result = $db->sql_query_limit($sql, $limit, $start)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select Modification Data', '', __LINE__, __FILE__, $sql);
      		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}

		$db->sql_freeresult($result);

		return $rows;
	}

	/*========================================================================*/
	// Select Modifications By Business Data From DB
	// Usage: get_modifications_by_business('business id', 'start row', 'end row');
	/*========================================================================*/
	function get_modifications_by_business($business_id, $start = 0, $limit = 20)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.id, m.garage_id, m.title AS mod_title, m.price, m.purchase_rating, m.product_rating, m.comments, u.username, u.user_id, mk.make, md.model, g.made_year, b.id as business_id, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle',
			'FROM'		=> array(
				GARAGE_MODS_TABLE	=> 'm',
				GARAGE_BUSINESS_TABLE	=> 'b',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_TABLE => 'g'),
					'ON'	=> 'm._garage_id = g.id'
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
			'WHERE'		=> "m.business_id = b.id AND ( b.web_shop =1 OR b.retail_shop =1 ) AND b.pending = 0 AND b.id = $business_id AND mk.pending = 0 AND md.pending = 0",
			'ORDER_BY'	=> "m.id, m.date_created DESC"
		));

      		if ( !($result = $db->sql_query_limit($sql, $limit, $start)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select Modification Data', '', __LINE__, __FILE__, $sql);
      		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}

		$db->sql_freeresult($result);

		return $rows;
	}
	/*========================================================================*/
	// Select Modifications By Business Data From DB
	// Usage: get_modifications_by_vehicle('garage id');
	/*========================================================================*/
	function get_modifications_by_vehicle($cid)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.*, i.*',
			'FROM'		=> array(
				GARAGE_MODS_TABLE	=> 'm',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_IMAGE_TABLE => 'i'),
					'ON'	=> 'm.image_id = i.attach_id'
				)
			),
			'WHERE'		=> "m.garage_id = $cid"
		));

		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select Modification Data', '', __LINE__, __FILE__, $sql);
      		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$rows[] = $row;
		}

		$db->sql_freeresult($result);

		return $rows;
	}

	/*========================================================================*/
	// Build Updated Modifications HTML If Required 
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
	 		
	        // What's the count? Default to 10
		$limit = $garage_config['updated_modification_limit'] ? $garage_config['updated_modification_limit'] : 10;

		//Get Updated Modifications
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
	// Build Most Modified HTML If Required 
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
	
	        // What's the count? Default to 10
		$limit = $garage_config['most_modified_limit'] ? $garage_config['most_modified_limit'] : 10;

		//Get Most Modified
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
	// Build Newest Modifications HTML If Required 
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
	
	        // What's the count? Default to 10
	        $limit = $garage_config['newest_modification_limit'] ? $garage_config['newest_modification_limit'] : 10;

		//Get Newest Modifications
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

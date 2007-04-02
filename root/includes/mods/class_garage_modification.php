<?php
/** 
*
* @package garage
* @version $Id$
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
*/
if (!defined('IN_PHPBB'))
{
	die('Hacking attempt');
}

/**
* phpBB Garage Modification Class
* @package garage
*/
class garage_modification
{
	var $classname = "garage_modification";

	/**
	* Insert new modification
	*
	* @param array $data single-dimension array holding the data for the new modification
	*
	*/
	function insert_modification($data)
	{
		global $vid, $db, $garage_vehicle;

		$sql = 'INSERT INTO ' . GARAGE_MODIFICATIONS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'vehicle_id'		=> $vid,
			'user_id'		=> $garage_vehicle->get_vehicle_owner_id($vid),
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

	/**
	* Updates a existing modification
	*
	* @param array $data single-dimension array holding the data to update the modification with
	*
	*/
	function update_modification($data)
	{
		global $db, $vid, $mid, $garage_vehicle;

		$update_sql = array(
			'vehicle_id'		=> $vid,
			'user_id'		=> $garage_vehicle->get_vehicle_owner_id($vid),
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
			WHERE id = $mid AND vehicle_id = $vid";

		$db->sql_query($sql);

		return;
	}

	/**
	* Delete modification and all images linked to it
	*
	* @param int $mid modification id to delete
	*
	*/
	function delete_modification($mid)
	{
		global $vid, $garage, $garage_image;
	
		$images	= $garage_image->get_modification_gallery($vid, $mid);
	
		for ($i = 0, $count = sizeof($images);$i < $count; $i++)
		{
			$garage_image->delete_modification_image($images[$i]['id']);
		}
	
		$garage->delete_rows(GARAGE_MODIFICATIONS_TABLE, 'id', $mid);
	
		return ;
	}

	/**
	* Insert new product
	*
	* @param array $data single-dimension array holding the data for the new product
	*
	*/
	function insert_product($data)
	{
		global $vid, $db, $garage_vehicle, $garage_config;

		$pending = ($data['pending'] == 0) ? 0 : $garage_config['enable_product_approval'];

		$sql = 'INSERT INTO ' . GARAGE_PRODUCTS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'category_id'		=> $data['category_id'],
			'business_id'		=> $data['manufacturer_id'],
			'title'			=> $data['title'],
			'pending'		=> $pending,
		));

		$db->sql_query($sql);
	
		return $db->sql_nextid();
	}

	/**
	* Updates a existing product
	*
	* @param array $data single-dimension array holding the data to update the product with
	*
	*/
	function update_product($data)
	{
		global $db, $garage_config;

		$pending = ($data['pending'] == 0) ? 0 : $garage_config['enable_product_approval'];

		$update_sql = array(
			'category_id'		=> $data['category_id'],
			'business_id'		=> $data['manufacturer_id'],
			'title'			=> $data['title'],
			'pending'		=> $pending,
		);

		$sql = 'UPDATE ' . GARAGE_PRODUCTS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = " . $data['product_id'];

		$db->sql_query($sql);
	
		return;
	}

	/**
	* Check if an image is marked as highlight image for modification
	*
	* @param int $mid modification id to check
	*
	*/
	function hilite_exists($mid)
	{
		$hilite = 1;

		if ($this->count_modification_images($mid) > 0)
		{
			$hilite = 0;
		}
	
		return $hilite;
	}

	/**
	* Count images linked to a modification
	*
	* @param int $mid modification id to count images for
	*
	*/
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

	/**
	* Return count of all modifications
	*/
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

	/**
	* Return array of latest updated modifications
	*
	* @param int $limit number of rows to return
	*
	*/
	function get_updated_modifications($limit)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.id, m.vehicle_id, m.user_id, p.title AS mod_title, m.date_updated AS POI, u.username, u.user_colour',
			'FROM'		=> array(
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
				GARAGE_PRODUCTS_TABLE		=> 'p',
				GARAGE_VEHICLES_TABLE		=> 'v',
				GARAGE_MAKES_TABLE		=> 'mk',
				GARAGE_MODELS_TABLE		=> 'md',
				USERS_TABLE			=> 'u',
			),
			'WHERE'		=> "m.product_id = p.id
						AND m.vehicle_id = v.id
						AND m.user_id = u.user_id
						AND v.make_id = mk.id AND mk.pending = 0
						AND v.model_id = md.id AND md.pending = 0",
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

	/**
	* Return data for specific product
	*
	* @param int $product_id product id to return data for
	*
	*/
	function get_product($product_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'p.*',
			'FROM'		=> array(
				GARAGE_PRODUCTS_TABLE	=> 'p',
			),
			'WHERE'		=> "p.id = $product_id"
		));

	 	$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return data for pending products
	*
	*/
	function get_pending_products()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'p.id, p.title as product, b.title as manufacturer, c.title as category',
			'FROM'		=> array(
				GARAGE_PRODUCTS_TABLE	=> 'p',
				GARAGE_BUSINESS_TABLE	=> 'b',
				GARAGE_CATEGORIES_TABLE	=> 'c',
			),
			'WHERE'		=> "p.pending = 1
						AND b.id = p.business_id
						AND c.id = p.category_id"
		));

	 	$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array of products by manufacturer and category if required
	*
	* @param int $business_id business id to filter on
	* @param int $category_id category id to filter on (optional)
	*
	*/
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

	/**
	* Return array of newest modifications
	*
	* @param int $limit number of rows to return
	*
	*/
	function get_newest_modifications($limit)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.id, m.vehicle_id, m.user_id, p.title AS mod_title, m.date_created AS POI, u.username, u.user_colour',
			'FROM'		=> array(
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
				GARAGE_PRODUCTS_TABLE		=> 'p',
				GARAGE_VEHICLES_TABLE		=> 'v',
				GARAGE_MAKES_TABLE		=> 'mk',
				GARAGE_MODELS_TABLE		=> 'md',
				USERS_TABLE			=> 'u',
			),
			'WHERE'		=> "m.product_id = p.id
						AND m.vehicle_id = v.id
						AND v.user_id = u.user_id
						AND v.make_id = mk.id AND mk.pending = 0
						AND v.model_id = md.id AND md.pending = 0",
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

	/**
	* Return array of most modified vehicles
	*
	* @param int $limit number of rows to return
	*
	*/
	function get_most_modified($limit)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'v.id, CONCAT_WS(\' \', v.made_year, mk.make, md.model) AS vehicle, v.user_id, COUNT(m.id) AS POI, u.username, u.user_colour',
			'FROM'		=> array(
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
				GARAGE_VEHICLES_TABLE		=> 'v',
				GARAGE_MAKES_TABLE		=> 'mk',
				GARAGE_MODELS_TABLE		=> 'md',
				USERS_TABLE			=> 'u',
			),
			'WHERE'		=> "m.vehicle_id = v.id
						AND v.user_id = u.user_id
						AND v.make_id = mk.id AND mk.pending = 0
						AND v.model_id = md.id AND md.pending = 0",
			'GROUP_BY'	=> "v.id",
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

	
	/**
	* Return data for specific modification
	*
	* @param int $mid modification id to return data for
	*
	*/
	function get_modification($mid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.*, v.made_year, v.id, v.currency, i.*, u.username, u.user_avatar_type, u.user_avatar, c.title as category_title, mk.make, md.model, b1.title as business_title, b2.title as install_business_title, CONCAT_WS(\' \', v.made_year, mk.make, md.model) AS vehicle, p.title, u.user_avatar_width, u.user_avatar_height, u.user_colour',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE		=> 'v',
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
				GARAGE_PRODUCTS_TABLE		=> 'p',
				GARAGE_CATEGORIES_TABLE		=> 'c',
				GARAGE_MAKES_TABLE		=> 'mk',
				GARAGE_MODELS_TABLE		=> 'md',
				GARAGE_BUSINESS_TABLE		=> array('b1', 'b2'),
				USERS_TABLE			=> 'u',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MODIFICATION_GALLERY_TABLE => 'mg'),
					'ON'	=> 'm.id = mg.modification_id AND mg.hilite = 1',
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'mg.image_id = i.attach_id'
				)
			),
			'WHERE'		=> "m.id = $mid 
						AND v.id = m.vehicle_id
						AND m.product_id = p.id
						AND m.category_id = c.id
						AND (v.make_id = mk.id AND mk.pending = 0)
						AND (v.model_id = md.id AND md.pending = 0)
						AND v.user_id = u.user_id
						AND m.shop_id = b1.id
						AND m.installer_id = b2.id"
		));

      		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array for specific vehicle & specific category
	*
	* @param int $vid vehicle id to return data for
	* @param int $category_id category id to return data for
	*
	*/
	function get_modifications_by_category($vid, $category_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.*, i.*, p.title',
			'FROM'		=> array(
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
				GARAGE_PRODUCTS_TABLE		=> 'p',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MODIFICATION_GALLERY_TABLE => 'mg'),
					'ON'	=> 'm.id = mg.modification_id AND mg.hilite = 1',
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'mg.image_id = i.attach_id'
				)
			),
			'WHERE'		=> "m.vehicle_id = $vid 
						AND m.category_id = $category_id
						AND m.product_id = p.id",
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

	/**
	* Return data for specific category
	*
	* @param int $category_id category id to return data for
	*
	*/
	function get_modifications_by_category_id($category_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.*, i.*, p.title',
			'FROM'		=> array(
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
				GARAGE_PRODUCTS_TABLE		=> 'p',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MODIFICATION_GALLERY_TABLE => 'mg'),
					'ON'	=> 'm.id = mg.modification_id AND mg.hilite = 1',
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'mg.image_id = i.attach_id'
				)
			),
			'WHERE'		=> "m.category_id = $category_id
						AND m.product_id = p.id",
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

	/**
	* Return limited modification data for specific installation business
	*
	* @param int $business_id business id to return modification data for
	* @param int $start row to start data selection from
	* @param int $limit number of rows to return
	*
	*/
	function get_modifications_by_install_business($business_id, $start = 0 , $limit = 20)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> "m.id, m.vehicle_id, p.title AS mod_title, m.install_price, m.install_rating, m.install_comments, u.username, u.user_id, mk.make, md.model, v.made_year, b.id as business_id, CONCAT_WS(' ', v.made_year, mk.make, md.model) AS vehicle, u.user_colour",
			'FROM'		=> array(
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
				GARAGE_BUSINESS_TABLE		=> 'b',
				GARAGE_PRODUCTS_TABLE		=> 'p',
				GARAGE_VEHICLES_TABLE		=> 'v',
				GARAGE_MAKES_TABLE		=> 'mk',
				GARAGE_MODELS_TABLE		=> 'md',
				USERS_TABLE			=> 'u',
			),
			'WHERE'		=> "b.id = $business_id
						AND m.installer_id = b.id 
						AND b.garage = 1 
						AND b.pending = 0 
						AND b.id = $business_id 
						AND m.product_id = p.id
						AND m.vehicle_id = v.id
						AND v.user_id = u.user_id
						AND (v.make_id = mk.id AND mk.pending = 0)
						AND (v.model_id = md.id AND md.pending = 0)",
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

	/**
	* Return limited modification data for specific business
	*
	* @param int $business_id business id to return modification data for
	* @param int $start row to start data selection from
	* @param int $limit number of rows to return
	*
	*/
	function get_modifications_by_business($business_id, $start = 0, $limit = 20)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.id, m.vehicle_id, p.title AS mod_title, m.price, m.purchase_rating, m.product_rating, m.comments, u.username, u.user_id, mk.make, md.model, v.made_year, b.id as business_id, CONCAT_WS(\' \', v.made_year, mk.make, md.model) AS vehicle, u.user_colour',
			'FROM'		=> array(
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
				GARAGE_BUSINESS_TABLE		=> 'b',
				GARAGE_PRODUCTS_TABLE		=> 'p',
				GARAGE_VEHICLES_TABLE		=> 'v',
				GARAGE_MAKES_TABLE		=> 'mk',
				GARAGE_MODELS_TABLE		=> 'md',
				USERS_TABLE			=> 'u',
			),
			'WHERE'		=> "b.id = $business_id 
						AND m.shop_id = b.id
						AND b.retail = 1 
						AND b.pending = 0
						AND m.product_id = p.id
						AND m.vehicle_id = v.id
						AND v.user_id = u.user_id
						AND (v.make_id = mk.id AND mk.pending = 0)
						AND (v.model_id = md.id AND md.pending = 0)",
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

	/**
	* Return modification data for specific vehicle
	*
	* @param int $vid vehicle id to return data for
	*
	*/
	function get_modifications_by_vehicle($vid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'm.*, i.*, p.*',
			'FROM'		=> array(
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
				GARAGE_PRODUCTS_TABLE		=> 'p',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MODIFICATION_GALLERY_TABLE => 'mg'),
					'ON'	=> 'm.id = mg.modification_id AND mg.hilite = 1',
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'mg.image_id = i.attach_id'
				)
			),
			'WHERE'		=> "m.vehicle_id = $vid
						AND m.product_id = p.id"
		));

		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}

		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Assign template variables to display lastest updated modifications
	*/
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
				'U_COLUMN_1'		=> append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=view_modification&amp;MID=" . $rows[$i]['id'] . "&amp;VID=" . $rows[$i]['vehicle_id']),
				'U_COLUMN_2' 		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $rows[$i]['user_id']),
				'COLUMN_1_TITLE'	=> $rows[$i]['mod_title'],
				'COLUMN_2_TITLE'	=> $rows[$i]['username'],
				'COLUMN_3_TITLE'	=> $user->format_date($rows[$i]['POI']),
				'USERNAME_COLOUR'	=> get_username_string('colour', $rows[$i]['user_id'], $rows[$i]['username'], $rows[$i]['user_colour']),
			));
		}

		$required_position++;
		return ;
	}
	
	/**
	* Assign template variables to display most modified vehicles
	*/
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
				'U_COLUMN_1' 		=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;VID=" . $rows[$i]['id']),
				'U_COLUMN_2' 		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $rows[$i]['user_id']),
				'COLUMN_1_TITLE'	=> $rows[$i]['vehicle'],
				'COLUMN_2_TITLE'	=> $rows[$i]['username'],
				'COLUMN_3_TITLE'	=> $rows[$i]['POI'],
				'USERNAME_COLOUR'	=> get_username_string('colour', $rows[$i]['user_id'], $rows[$i]['username'], $rows[$i]['user_colour']),
			));
	 	}
	
		$required_position++;
		return ;
	}
	
	/**
	* Assign template variables to display newest modifications
	*/
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
				'U_COLUMN_1' 		=> append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=view_modification&amp;MID=" . $rows[$i]['id'] . "&amp;VID=" . $rows[$i]['vehicle_id']),
				'U_COLUMN_2' 		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $rows[$i]['user_id']),
				'COLUMN_1_TITLE'	=> $rows[$i]['mod_title'],
				'COLUMN_2_TITLE'	=> $rows[$i]['username'],
				'COLUMN_3_TITLE'	=> $user->format_date($rows[$i]['POI']),
				'USERNAME_COLOUR'	=> get_username_string('colour', $rows[$i]['user_id'], $rows[$i]['username'], $rows[$i]['user_colour']),
			));
	 	}
	
		$required_position++;
		return ;
	}

	/**
	* Return modification categories for specific product manufacturer
	*
	* @param int $bid business id to return data for
	*
	*/
	function get_manufacturer_modification_categories($bid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT_DISTINCT', 
			array(
			'SELECT'	=> 'c.title, c.id',
			'FROM'		=> array(
				GARAGE_CATEGORIES_TABLE		=> 'c',
				GARAGE_BUSINESS_TABLE		=> 'b',
				GARAGE_PRODUCTS_TABLE		=> 'p',
			),
			'WHERE'		=> "b.id = $bid 
						AND b.id = p.business_id
						AND c.id = p.category_id",
			'ODRDER_BY'	=> 'c.field_order DESC'
		));

	      	$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Approve products
	*
	* @param array $id_list single-dimension array holding the product ids to approve
	*
	*/
	function approve_dynorun($id_list)
	{
		global $phpbb_root_path, $phpEx, $garage;

		for($i = 0; $i < count($id_list); $i++)
		{
			$garage->update_single_field(GARAGE_PRODUCTS_TABLE, 'pending', 0, 'id', $id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_products"));
	}

	/**
	* Disapprove products
	*
	* @param array $id_list sigle-dimension array holding the product ids to disapprove
	*
	*/
	function disapprove_dynorun($id_list)
	{
		global $phpbb_root_path, $phpEx;

		for($i = 0; $i < count($id_list); $i++)
		{
			$this->delete_product($id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_products"));
	}
}

$garage_modification = new garage_modification();

?>

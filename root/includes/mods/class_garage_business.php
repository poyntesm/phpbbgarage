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
* phpBB Garage Business Class
* @package garage
*/
class garage_business
{
	var $classname = "garage_business";

	/**
	* Insert a new business
	*
	* @param array $data single-dimension array holding the data for new business
	*
	*/
	function insert_business($data)
	{
		global $db, $garage_config;

		$pending = ($garage_config['enable_business_approval']) ? 1 : 0;

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
			'retail'	=> $data['retail'],
			'product'	=> $data['product'],
			'dynocentre'	=> $data['dynocentre'],
			'pending'	=> $pending,
		));

		$result = $db->sql_query($sql);

		return;
	}

	/**
	* Update a single business
	*
	* @param array $data single-dimension array holding the data to update business
	*
	*/
	function update_business($data)
	{
		global $db, $garage_config;

		$pending = ($data['pending'] == 0) ? 0 : $garage_config['enable_business_approval'];

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
			'retail'	=> $data['retail'],
			'product'	=> $data['product'],
			'dynocentre'	=> $data['dynocentre'],
			'pending'	=> $pending,
		);

		$sql = 'UPDATE ' . GARAGE_BUSINESS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = " . $data['id'];

		$result = $db->sql_query($sql);

		return;
	}

	/**
	* Return data for single business
	*
	* @param int $bus_id business id to return data for
	*
	*/
	function get_business($bus_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'b.*',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE	=> 'b',
			),
			'WHERE'		=>  "b.id = $bus_id"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array with data for all business's
	*/
	function get_all_business()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'b.*',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE	=> 'b',
			)
		));

		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return business list excluding a single business
	*
	* @param int $exclude_id business id to exclude from returned data
	*
	*/
	function get_reassign_business($exclude_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'b.*',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE	=> 'b',
			),
			'WHERE'		=> "b.pending = 0 AND b.id NOT IN ($exclude)",
			'ORDER_BY' 	=> 'b.title ASC'
		));

		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array with data business's filtered by business type
	*
	* @param BUSINESS_GARAGE|BUSINESS_INSURANCE|BUSINESS_RETAIL|BUSINESS_DYNOCENTRE|BUSINESS_PRODUCT $type business type to filter by
	*
	*/
	function get_business_by_type($type)
	{
		global $db;

		$data = array();
		$field = null;

		//Setup Field We Will Query Based On Constant
		if ($type == BUSINESS_INSURANCE)
		{
			$field = 'insurance';
		}
		else if ($type == BUSINESS_GARAGE)
		{
			$field = 'garage';
		}
		else if ($type == BUSINESS_RETAIL)
		{
			$field = 'retail';
		}
		else if ($type == BUSINESS_PRODUCT)
		{
			$field = 'product';
		}
		else if ($type == BUSINESS_DYNOCENTRE)
		{
			$field = 'dynocentre';
		}

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'b.*',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE	=> 'b',
			),
			'WHERE'		=> "b.$field  = 1",
			'ORDER_BY'	=> "b.title ASC"

		));

		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array of pending business's
	*/
	function get_pending_business()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'b.*',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE	=> 'b',
			),
			'WHERE'		=>  "b.pending = 1"
		));

		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Returns array with limited number of garage business's
	*
	* @param string $where additional where statement
	* @param int $start starting row for data selection
	* @param int $limit number to limit rows returned
	*
	*/
	function get_garage_business($where, $start = 0, $limit = 20)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'b.*, SUM(install_rating) AS rating, COUNT(*) *10 AS total_rating',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE	=> 'b',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MODIFICATIONS_TABLE => 'm'),
					'ON'	=> 'b.id = m.installer_id'
				)		
			),
			'WHERE'		=>  "b.garage = 1 AND b.pending = 0 $where",
			'GROUP_BY'	=>  "b.id",
			'ODER_BY'	=>  "rating DESC"
		));

      		$result = $db->sql_query_limit($sql, $limit, $start);
		while( $row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Returns array with limited number of retail business's
	*
	* @param string $where additional where statement
	* @param int $start starting row for data selection
	* @param int $limit number to limit rows returned
	*
	*/
	function get_shop_business($where, $start = 0, $limit = 20)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'b.*, SUM(purchase_rating) AS rating, COUNT(*) *10 AS total_rating',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE		=> 'b',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MODIFICATIONS_TABLE => 'm'),
					'ON'	=> 'b.id = m.shop_id'
				)		
			),
			'WHERE'		=>  "b.retail = 1  AND b.pending =0 $where",
			'GROUP_BY'	=>  "b.id",
			'ODER_BY'	=>  "rating DESC"
		));

      		$result = $db->sql_query_limit($sql, $limit, $start);
		while( $row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Returns array with limited number of insurance business's
	*
	* @param string $where additional where statement
	* @param int $start starting row for data selection
	* @param int $limit number to limit rows returned
	*
	*/
	function get_insurance_business($where = null, $start = 0,  $limit = 20)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'b.*, COUNT(DISTINCT b.id) as total',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE	=> 'b',
			),
			'WHERE'		=>  "b.insurance = 1 AND b.pending = 0 $where",
			'GROUP_BY'	=>  "b.id"
		));

      		$result = $db->sql_query_limit($sql, $limit, $start);
		while( $row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
      		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return count of insurance business's with possible fitler
	*
	* @param string $where additional where statement
	*
	*/
	function count_insurance_business_data($additional_where)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'count(DISTINCT b.title) as total',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE		=> 'b',
			),
			'WHERE'		=>  "b.insurance = 1 AND b.pending =0 $additional_where"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data['total'];
	}


	/**
	* Return count of garage business's with possible fitler
	*
	* @param string $where additional where statement
	*
	*/
	function count_garage_business_data($additional_where)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'count(DISTINCT b.title) as total',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE		=> 'b',
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
			),
			'WHERE'		=>  "m.installer_id = b.id AND b.garage = 1 AND b.pending =0 $additional_where"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data['total'];
	}

	/**
	* Return count of retail business's with possible fitler
	*
	* @param string $where additional where statement
	*
	*/
	function count_shop_business_data($additional_where)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(DISTINCT b.title) as total',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE	=> 'b',
				GARAGE_MODIFICATIONS_TABLE	=> 'm',
			),
			'WHERE'		=>  "m.shop_id = b.id AND b.retail = 1 AND b.pending =0 $additional_where"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data['total'];
	}

	/**
	* Delete a business and either move or delete linked items
	*
	* @param int $business_id business id to delete
	* @param delete|move $action_garage move or delete modification from garage business
	* @param int $garage_to_id business id of garage business to move modifications to
	* @param delete|move $action_insurance move or delete premiums from insurer
	* @param int $insurance_to_id business id of insurer to move premiums to
	* @param delete|move $action_dynocentre move or delete dynoruns from dynocentre
	* @param int $dynocentre_to_id business id of dynocentre to move dynoruns to
	* @param delete|move $action_retail move or delete modification from retail business
	* @param int $retail_to_id business id of retail business to move modifications to
	* @param delete|move $action_move or delete modification from product manufacturer business
	* @param int $product_to_id business id of product manufacturer to move modifications to
	*
	*/
	function delete_business($business_id, $action_garage = 'delete', $garage_to_id = 0, $action_insurance = 'delete', $insurance_to_id = 0, $action_dynocentre = 'delete', $dynocentre_to_id = 0, $action_retail = 'delete', $retail_to_id = 0, $action_product = 'delete', $product_to_id = 0)
	{

		global $db, $user, $cache, $garage, $garage_business;

		$business_data = $garage_business->get_business($business_id);

		$errors = array();

		if ($action_garage == 'delete')
		{
			$this->delete_garage_business_content($business_id);
			add_log('admin', 'LOG_GARAGE_DELETE_GARAGE', $business_data['title']);
		}
		else if ($action_garage == 'move')
		{
			if (!$garage_to_id)
			{
				$errors[] = $user->lang['NO_DESTINATION_GARAGE_BUSINESS'];
			}
			else
			{
				$row = $garage_business->get_business($garage_to_id);

				if (!$row)
				{
					$errors[] = $user->lang['NO_BUSINESS'];
				}
				else
				{
					$garage_to_name = $row['title'];
					$from_name = $business_data['title'];
					$this->move_garage_business_content($business_id, $garage_to_id);
					add_log('admin', 'LOG_GARAGE_MOVED_GARAGE', $from_name, $garage_to_name);
				}
			}
		}

		if ($action_insurance == 'delete')
		{
			$this->delete_insurance_business_content($business_id);
			add_log('admin', 'LOG_GARAGE_DELETE_GARAGE', $business_data['title']);
		}
		else if ($action_insurance == 'move')
		{
			if (!$insurance_to_id)
			{
				$errors[] = $user->lang['NO_DESTINATION_INSURANCE_BUSINESS'];
			}
			else
			{
				$row = $garage_business->get_business($insurance_to_id);

				if (!$row)
				{
					$errors[] = $user->lang['NO_BUSINESS'];
				}
				else
				{
					$insurance_to_name = $row['title'];
					$from_name = $business_data['title'];
					$this->move_insurance_business_content($business_id, $insurance_to_id);
					add_log('admin', 'LOG_GARAGE_MOVED_PREMIUMS', $from_name, $insurance_to_name);
				}
			}
		}

		if ($action_dynocentre == 'delete')
		{
			$this->delete_dynocentre_business_content($business_id);
			add_log('admin', 'LOG_GARAGE_DELETE_GARAGE', $business_data['title']);
		}
		else if ($action_dynocentre == 'move')
		{
			if (!$dynocentre_to_id)
			{
				$errors[] = $user->lang['NO_DESTINATION_DYNOCENTRE_BUSINESS'];
			}
			else
			{
				$row = $garage_business->get_business($garage_to_id);

				if (!$row)
				{
					$errors[] = $user->lang['NO_BUSINESS'];
				}
				else
				{
					$dynocentre_to_name = $row['title'];
					$from_name = $business_data['title'];
					$this->move_dynocentre_business_content($business_id, $dynocentre_to_id);
					add_log('admin', 'LOG_GARAGE_MOVED_DYNORUNS', $from_name, $dynocentre_to_name);
				}
			}
		}
		
		if ($action_retail == 'delete')
		{
			$this->delete_retail_business_content($business_id);
			add_log('admin', 'LOG_GARAGE_DELETE_GARAGE', $business_data['title']);
		}
		else if ($action_retail == 'move')
		{
			if (!$retail_to_id)
			{
				$errors[] = $user->lang['NO_DESTINATION_RETAIL_BUSINESS'];
			}
			else
			{
				$row = $garage_business->get_business($retail_to_id);

				if (!$row)
				{
					$errors[] = $user->lang['NO_BUSINESS'];
				}
				else
				{
					$retail_to_name = $row['title'];
					$from_name = $business_data['title'];
					$this->move_retail_business_content($business_id, $retail_to_id);
					add_log('admin', 'LOG_GARAGE_MOVED_RETAIL', $from_name, $retail_to_name);
				}
			}
		}

		if ($action_product == 'delete')
		{
			$this->delete_product_business_content($business_id);
			add_log('admin', 'LOG_GARAGE_DELETE_GARAGE', $business_data['title']);
		}
		else if ($action_product == 'move')
		{
			if (!$product_to_id)
			{
				$errors[] = $user->lang['NO_DESTINATION_PRODUCT_BUSINESS'];
			}
			else
			{
				$row = $garage_business->get_business($product_to_id);

				if (!$row)
				{
					$errors[] = $user->lang['NO_BUSINESS'];
				}
				else
				{
					$product_to_name = $row['title'];
					$from_name = $business_data['title'];
					$this->move_product_business_content($business_id, $product_to_id);
					add_log('admin', 'LOG_GARAGE_MOVED_PRODUCT', $from_name, $product_to_name);
				}
			}
		}

		$garage->delete_rows(GARAGE_BUSINESS_TABLE, 'id', $business_id);
		add_log('admin', 'LOG_GARAGE_BUSINESS_DELETED', $business_data['title']);

		if (sizeof($errors))
		{
			return $errors;
		}
	}

	/**
	* Delete all modifications linked to a garage business
	*
	* @param int $business_id business id to delete items for
	*
	*/
	function delete_garage_business_content($business_id)
	{
		global $db, $config, $phpbb_root_path, $phpEx, $garage, $garage_modification;

		include_once($phpbb_root_path . 'includes/mods/class_garage_modification.' . $phpEx);
		$modifications = $garage_modification->get_modifications_by_installer_id($business_id);
		for ($i = 0, $count = sizeof($modifications);$i < $count; $i++)
		{
			$garage_modification->delete_modification($modifications[$i]['id']);
		}

		return;
	}

	/**
	* Delete all premiums linked to a insurance business
	*
	* @param int $business_id business id to delete items for
	*
	*/
	function delete_insurance_business_content($business_id)
	{
		global $db, $config, $phpbb_root_path, $phpEx, $garage, $garage_modification;

		include_once($phpbb_root_path . 'includes/mods/class_garage_insurance.' . $phpEx);
		$premiums = $garage_insurance->get_premiums_by_insurer_id($business_id);
		for ($i = 0, $count = sizeof($premiums);$i < $count; $i++)
		{
			$garage_insurance->delete_premium($premiums[$i]['id']);
		}

		return;
	}

	/**
	* Delete all dynoruns linked to a dynorun business
	*
	* @param int $business_id business id to delete items for
	*
	*/
	function delete_dynocentre_business_content($business_id)
	{
		global $db, $config, $phpbb_root_path, $phpEx, $garage, $garage_modification;

		include_once($phpbb_root_path . 'includes/mods/class_garage_dynorun.' . $phpEx);
		$dynoruns = $garage_dynorun->get_dynoruns_by_dynocentre_id($business_id);
		for ($i = 0, $count = sizeof($dynoruns);$i < $count; $i++)
		{
			$garage_dynoruns->delete_dynorun($dynoruns[$i]['id']);
		}

		return;
	}

	/**
	* Delete all modifications linked to a retail business
	*
	* @param int $business_id business id to delete items for
	*
	*/
	function delete_retail_business_content($business_id)
	{
		global $db, $config, $phpbb_root_path, $phpEx, $garage, $garage_modification;

		include_once($phpbb_root_path . 'includes/mods/class_garage_modification.' . $phpEx);
		$modifications = $garage_modification->get_modifications_by_retail_id($business_id);
		for ($i = 0, $count = sizeof($modifications);$i < $count; $i++)
		{
			$garage_modification->delete_modification($modifications[$i]['id']);
		}

		return;
	}

	/**
	* Delete all modifications linked to a product business
	*
	* @param int $business_id business id to delete items for
	*
	*/
	function delete_product_business_content($business_id)
	{
		global $db, $config, $phpbb_root_path, $phpEx, $garage, $garage_modification;

		include_once($phpbb_root_path . 'includes/mods/class_garage_modification.' . $phpEx);
		$modifications = $garage_modification->get_modifications_by_manufacturer_id($business_id);
		for ($i = 0, $count = sizeof($modifications);$i < $count; $i++)
		{
			$garage_modification->delete_modification($modifications[$i]['id']);
		}

		return;
	}

	/**
	* Reassign products to installation garage
	*
	* @param int $from_id business id to move from
	* @param int $to_id business id to move to
	*
	*/
	function move_garage_business_content($from_id, $to_id)
	{
		global $garage;

		$garage->update_single_field(GARAGE_MODIFICATIONS_TABLE, 'installer_id', $to_id, 'installer_id', $from_id);

		return;
	}

	/**
	* Reassign premiums to different insurer
	*
	* @param int $from_id business id to move from
	* @param int $to_id business id to move to
	*
	*/
	function move_insurance_business_content($from_id, $to_id)
	{
		global $garage;

		$garage->update_single_field(GARAGE_PREMIUMS_TABLE, 'business_id', $to_id, 'business_id', $from_id);

		return;
	}

	/**
	* Reassign dynoruns to different dynocentre
	*
	* @param int $from_id business id to move from
	* @param int $to_id business id to move to
	*
	*/
	function move_dynocentre_business_content($from_id, $to_id)
	{
		global $garage;

		$garage->update_single_field(GARAGE_DYNORUNS_TABLE, 'dynocentre_id', $to_id, 'dynocentre_id', $from_id);

		return;
	}

	/**
	* Reassign products to different shop
	*
	* @param int $from_id business id to move from
	* @param int $to_id business id to move to
	*
	*/
	function move_retail_business_content($from_id, $to_id)
	{
		global $garage;

		$garage->update_single_field(GARAGE_MODIFICATIONS_TABLE, 'shop_id', $to_id, 'shop_id', $from_id);

		return;
	}

	/**
	* Reassign products to different manufacturer
	*
	* @param int $from_id business id to move from
	* @param int $to_id business id to move to
	*
	*/
	function move_product_business_content($from_id, $to_id)
	{
		global $garage;

		$garage->update_single_field(GARAGE_MODIFICATIONS_TABLE, 'manufacturer_id', $to_id, 'manufacturer_id', $from_id);

		return;
	}

	/**
	* Approve business's
	*
	* @param array $id_list single-dimension array with business ids to approve
	*
	*/
	function approve_business($id_list)
	{
		global $phpbb_root_path, $phpEx, $garage;

		for($i = 0; $i < count($id_list); $i++)
		{
			$garage->update_single_field(GARAGE_BUSINESS_TABLE, 'pending', 0, 'id', $id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_business"));
	}

	/**
	* Disapprove business's, this will force a delete of ALL items linked to the business
	*
	* @param array $id_list single-dimension array with business ids to disapprove
	*
	*/
	function disapprove_business($id_list)
	{
		global $phpbb_root_path, $phpEx;

		for($i = 0; $i < count($id_list); $i++)
		{
			$this->delete_business($id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_business"));
	}
}
$garage_business = new garage_business();
?>

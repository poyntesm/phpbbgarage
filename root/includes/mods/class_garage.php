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
* Inlcude constant definations
*/
include_once($phpbb_root_path . 'includes/mods/constants_garage.'. $phpEx);

/**
* Build $garage_config
*/
$sql = $db->sql_build_query('SELECT', 
	array(
	'SELECT'	=> 'c.config_name, c.config_value',
	'FROM'		=> array(
		GARAGE_CONFIG_TABLE	=> 'c',
	)
));

$result = $db->sql_query($sql);
while( $row = $db->sql_fetchrow($result) )
{
	$garage_config[$row['config_name']] = $row['config_value'];
}
$db->sql_freeresult($result);

/**
* phpBB Garage Class
* @package garage
*/
class garage 
{
	var $classname = "garage";

	/**
	* Wrapper for request_var()
	*
	* @param array $params multi-dimensional array holding the requested vars & defaults.
	*
	* @return mixed
	*/
	function process_vars($params = array())
	{
		while(list($var, $param) = @each($params) )
		{
			$data[$var] = request_var($var, $param );
		}

		return $data;
	}

	/**
	* Checks all required data is present. If any required data is missing 
	* the user is redirected to an informational error page
	*
	* @param array $params multi-dimensional array holding the required variables.
	*
	*/
	function check_required_vars($params = array())
	{
		global $phpEx, $data;

		while( list($var, $param) = @each($params) )
		{
			if (empty($data[$param]))
			{
				redirect(append_sid("garage.$phpEx", "mode=error&amp;EID=3"));
			}
		}

		return ;
	}

	/**
	* Count the total views the garage has recieved
	*
	* @return int
	*/
	function count_total_views()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'SUM(g.views) as total',
			'FROM'		=> array(
				GARAGE_VEHICLES_TABLE	=> 'g',
			)
		));

		$result = $db->sql_query($sql);
	        $data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$data['total'] = (empty($data['total'])) ? 0 : $data['total'];

		return $data['total'];
	}

	/**
	* Run more than one insert statement.
	*
	* @param string $table table name to run the statements on
	* @param string $set_field column name to set
	* @param string $set_value column value to set
	* @param string $where_field column name used in where
	* @param string $where_value column value used in where
	*
	*/
	function update_single_field($table, $set_field, $set_value, $where_field, $where_value)
	{
		global $db;

		$update_sql = array(
			$set_field	=> $set_value
		);

		$sql = 'UPDATE ' . $table . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE $where_field = '$where_value'";

		$db->sql_query($sql);
	
		return;
	}

	/**
	* Increment a field by 1
	*
	* @param string $table table name to run the statements on
	* @param string $set_field column name to increment
	* @param string $where_field column name used in where
	* @param string $where_value column value used in where
	*
	*/
	function update_view_count($table, $set_field, $where_field, $where_value)
	{
		global $db;

		$sql = "UPDATE $table 
			SET $set_field = $set_field + 1 
			WHERE $where_field = $where_value";

		$db->sql_query($sql);

		return;
	}

	/**
	* Delete one or more rows
	*
	* @param string $table table name to run the statements on
	* @param string $where_field column name used in where
	* @param string $where_value column value used in where
	*
	*/
	function delete_rows($table, $where_field, $where_value)
	{
		global $db;

		$sql = "DELETE 
			FROM $table 
			WHERE $where_field = '$where_value'";

		$db->sql_query($sql);

		return;
	}

	/**
	* Get all modification categories available
	*
	* @return array
	*/
	function get_categories()
	{
		global $db;

		$data = array();

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'c.id, c.title, c.field_order',
			'FROM'		=> array(
				GARAGE_CATEGORIES_TABLE	=> 'c',
			),
			'ORDER_BY'	=> 'c.field_order'
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
	* Return data for specific modification category
	*
	* @param int $category_id category id to get data for
	*
	* @retun mixed
	*/
	function get_category($category_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'c.id, c.title, c.field_order',
			'FROM'		=> array(
				GARAGE_CATEGORIES_TABLE	=> 'c',
			),
			'WHERE'		=> "c.id = $category_id",
			'ORDER_BY'	=> 'c.field_order'
		));

      		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Seed random number generator
	*/
	function make_seed()
	{
		list($usec, $sec) = explode(' ', microtime());
		return (float) $sec + ((float) $usec * 100000);
	}

	/**
	* Return year list based on ACP configuration options
	*
	* @return array
	*/
	function year_list()
	{
		global $garage_config;

		$years = array();

		$my_array = localtime(time(), 1) ;
		$current_date = $my_array["tm_year"] +1900 ;
	
	        $end_year = $current_date + $garage_config['year_end'];
	
		if ( $garage_config['year_start'] > $end_year ) 
		{
			return;
		}	
	
		for ( $year = $end_year; $year >= $garage_config['year_start']; $year-- ) 
		{
			$years[] = $year;
		}

		return $years;
	}

	/**
	* Perform seach
	*
	* @param array $search_options multi-dimensional array holding the search data
	* @param int &$total used to return total for pagination
	* @param string &$pagination_url  used to return for pagination
	*
	*/
	function perform_search($search_options, &$total, &$pagination_url)
	{
		global $db, $garage_config, $garage_template, $sort, $order, $start, $mode;

		$data = null;

		/**
		* Searching for just vehicles, so build query and pagination accordingly
		*/
		if ($search_options['display_as'] == 'vehicles')
		{
			//Update Display As Unless We Are In A Mode Which Defaults To this, We Try Hide This Fact
			if ($mode != 'browse')
			{
				$pagination_url .= "&amp;display_as=vehicles";
			}

			if (empty($sort))
			{
				$sort = 'date_created';
			}
			else
			{
				$pagination_url .= "&amp;sort=$sort";
			}
			$garage_template->sort_dropdown('vehicle', $sort);

			if (empty($order))
			{
				$order = 'ASC';
			}
			else
			{
				$pagination_url .= "&amp;order=$order";
			}
			$garage_template->order_dropdown($order);

			$sql_array = array(
				'SELECT'	=> 'v.*, i.*, mk.make, md.model, u.username, u.user_colour, count(m.id) AS total_mods',
				'SELECT_COUNT'	=> "count(DISTINCT v.id) as total",
				'FROM'		=> array(
					GARAGE_VEHICLES_TABLE	=> 'v',
					GARAGE_MAKES_TABLE	=> 'mk',
					GARAGE_MODELS_TABLE	=> 'md',
					USERS_TABLE		=> 'u',
				),
				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(GARAGE_MODIFICATIONS_TABLE => 'm'),
						'ON'	=> 'v.id = m.vehicle_id'
					)
					,array(
						'FROM'	=> array(GARAGE_VEHICLE_GALLERY_TABLE => 'vg'),
						'ON'	=> 'v.id = vg.vehicle_id AND vg.hilite = 1'
					)
					,array(
						'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
						'ON'	=> 'vg.image_id = i.attach_id'
					)
				),
				'WHERE'		=> "v.pending = 0 
							AND (v.make_id = mk.id and mk.pending = 0) 
							AND (v.model_id = md.id and md.pending = 0) 
							AND v.user_id = u.user_id",
				'GROUP_BY'	=> "v.id",
				'ORDER_BY'	=> "$sort $order"
			);
		}
		else if ($search_options['display_as'] == 'modifications')
		{
			$pagination_url .= "&amp;display_as=modifications";

			if (empty($sort))
			{
				$sort = 'category_id';
			}
			else
			{
				$pagination_url .= "&amp;sort=$sort";
			}
			$garage_template->sort_dropdown('modification', $sort);

			if (empty($order))
			{
				$order = 'ASC';
			}
			else
			{
				$pagination_url .= "&amp;order=$order";
			}
			$garage_template->order_dropdown($order);

			$sql_array = array(
				'SELECT'	=> "m.*, m.id as modification_id, v.id as vehicle_id, v.made_year, v.currency, i.*, u.username, u.user_avatar_type, u.user_avatar, c.title as category_title, mk.make, md.model, b1.title as business_title, CONCAT_WS(' ', v.made_year, mk.make, md.model) AS vehicle, CONCAT_WS(' ', b1.title, p.title) as modification_title, u.user_colour",
				'SELECT_COUNT'	=> "COUNT(m.id) AS total",
				'FROM'		=> array(
					GARAGE_MODIFICATIONS_TABLE	=> 'm',
					GARAGE_VEHICLES_TABLE		=> 'v',
					GARAGE_MAKES_TABLE		=> 'mk',
					GARAGE_MODELS_TABLE		=> 'md',
					USERS_TABLE			=> 'u',
				),
				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(GARAGE_CATEGORIES_TABLE => 'c'),	
						'ON'	=> 'm.category_id = c.id'
					)
					,array(
						'FROM'	=> array(GARAGE_PRODUCTS_TABLE => 'p'),	
						'ON'	=> 'm.product_id = p.id'
					)
					,array(
						'FROM'	=> array(GARAGE_MODIFICATION_GALLERY_TABLE => 'mg'),
						'ON'	=> 'm.id = mg.modification_id AND mg.hilite = 1'
					)
					,array(
						'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
						'ON'	=> 'mg.image_id = i.attach_id'
					)
					,array(
						'FROM'	=> array(GARAGE_BUSINESS_TABLE => 'b1'),
						'ON'	=> 'm.manufacturer_id = b1.id'
					)
				),
				'WHERE'		=> "m.id IS NOT NULL AND m.vehicle_id = v.id AND (v.make_id = mk.id and mk.pending = 0) AND (v.model_id = md.id and md.pending = 0) AND v.user_id = u.user_id",
				'GROUP_BY'	=> "m.id",
				'ORDER_BY'	=> "$sort $order"
			);

		
		}
		else if ($search_options['display_as'] == 'premiums')
		{
			$pagination_url .= "&amp;display_as=premiums";
			
			if (empty($sort))
			{
				$sort = 'premium';
			}
			else
			{
				$pagination_url .= "&amp;sort=$sort";
			}
			$garage_template->sort_dropdown('premium', $sort);

			if (empty($order))
			{
				$order = 'ASC';
			}
			else
			{
				$pagination_url .= "&amp;order=$order";
			}
			$garage_template->order_dropdown($order);

			$sql_array = array(
				'SELECT'	=> "p.*, v.*, b.title, b.id as business_id, mk.make, md.model, u.username, u.user_id, ( SUM(m.price) + SUM(m.install_price) ) AS total_spent, CONCAT_WS(' ', v.made_year, mk.make, md.model) AS vehicle, u.user_colour",
				'SELECT_COUNT'	=> "COUNT(DISTINCT p.id) AS total",
				'FROM'		=> array(
					GARAGE_PREMIUMS_TABLE	=> 'p',
					GARAGE_VEHICLES_TABLE	=> 'v',
					GARAGE_MAKES_TABLE	=> 'mk',
					GARAGE_MODELS_TABLE	=> 'md',
					USERS_TABLE		=> 'u',
				),
				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(GARAGE_MODIFICATIONS_TABLE => 'm'),	
						'ON'	=> 'v.id = m.vehicle_id'
					)
					,array(
						'FROM'	=> array(GARAGE_BUSINESS_TABLE => 'b'),
						'ON'	=> 'p.business_id = b.id'
					)
				),
				'WHERE'		=> "p.id IS NOT NULL AND p.vehicle_id = v.id  AND (v.make_id = mk.id and mk.pending = 0) AND (v.model_id = md.id and md.pending = 0) AND v.user_id = u.user_id",
				'GROUP_BY'	=> "p.id",
				'ORDER_BY'	=> "$sort $order"
			);
		}
		else if ($search_options['display_as'] == 'quartermiles')
		{
			//Update Display As Unless We Are In A Mode Which Defaults To this, We Try Hide This Fact
			if ($mode != 'quartermile_table')
			{
				$pagination_url .= "&amp;display_as=quartermiles";
			}

			if (empty($sort))
			{
				$sort = 'quart';
			}
			else
			{
				$pagination_url .= "&amp;sort=$sort";
			}
			$garage_template->sort_dropdown('quartermile', $sort);

			if (empty($order))
			{
				$order = 'ASC';
			}
			else
			{
				$pagination_url .= "&amp;order=$order";
			}
			$garage_template->order_dropdown($order);

			$sql_array = array(
				'SELECT'	=> "v.id, v.user_id, q.id as qmid, qg.image_id, i.attach_id, i.attach_file, u.username, CONCAT_WS(' ', v.made_year, mk.make, md.model) AS vehicle, q.rt, q.sixty, q.three, q.eighth, q.eighthmph, q.thou, q.quart, q.quartmph, q.dynorun_id, d.bhp, d.bhp_unit, d.torque, d.torque_unit, d.boost, d.boost_unit, d.nitrous, d.vehicle_id, u.user_colour",
				'SELECT_COUNT'	=> "COUNT(q.id) AS total",
				'FROM'		=> array(
					GARAGE_QUARTERMILES_TABLE	=> 'q',
					GARAGE_VEHICLES_TABLE		=> 'v',
					GARAGE_MAKES_TABLE		=> 'mk',
					GARAGE_MODELS_TABLE		=> 'md',
					USERS_TABLE			=> 'u',
				),
				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(GARAGE_DYNORUNS_TABLE => 'd'),	
						'ON'	=> 'q.dynorun_id = d.id'
					)
					,array(
						'FROM'	=> array(GARAGE_QUARTERMILE_GALLERY_TABLE => 'qg'),
						'ON'	=> 'q.id = qg.quartermile_id AND qg.hilite = 1'
					)
					,array(
						'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
						'ON'	=> 'qg.image_id = i.attach_id'
					)
				),
				'WHERE'		=> "q.pending = 0 AND q.vehicle_id = v.id  AND (v.make_id = mk.id and mk.pending = 0) AND (v.model_id = md.id and md.pending = 0) AND v.user_id = u.user_id",
				'GROUP_BY'	=> "q.id",
				'ORDER_BY'	=> "$sort $order"
			);
		}
		else if ($search_options['display_as'] == 'dynoruns')
		{
			//Update Display As Unless We Are In A Mode Which Defaults To this, We Try Hide This Fact
			if ($mode != 'dynorun_table')
			{
				$pagination_url .= "&amp;display_as=dynoruns";
			}

			if (empty($sort))
			{
				$sort = 'bhp';
			}
			else
			{
				$pagination_url .= "&amp;sort=$sort";
			}
			$garage_template->sort_dropdown('dynorun', $sort);

			if (empty($order))
			{
				$order = 'DESC';
			}
			else
			{
				$pagination_url .= "&amp;order=$order";
			}
			$garage_template->order_dropdown($order);

			$sql_array = array(
				'SELECT'	=> "v.id, v.made_year, v.user_id, mk.make, md.model, b.title, d.*, i.*, d.id as did, CONCAT_WS(' ', v.made_year, mk.make, md.model) AS vehicle, u.username, d.vehicle_id, u.user_colour",
				'SELECT_COUNT'	=> "COUNT(d.id) AS total",
				'FROM'		=> array(
					GARAGE_DYNORUNS_TABLE	=> 'd',
					GARAGE_VEHICLES_TABLE	=> 'v',
					GARAGE_MAKES_TABLE	=> 'mk',
					GARAGE_MODELS_TABLE	=> 'md',
					USERS_TABLE		=> 'u',
				),
				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(GARAGE_DYNORUN_GALLERY_TABLE => 'dg'),
						'ON'	=> 'd.id = dg.dynorun_id AND dg.hilite = 1'
					)
					,array(
						'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
						'ON'	=> 'dg.image_id = i.attach_id'
					)
					,array(
						'FROM'	=> array(GARAGE_BUSINESS_TABLE => 'b'),
						'ON'	=> 'd.dynocentre_id = b.id'
					)
				),
				'WHERE'		=> "d.pending = 0 AND d.vehicle_id = v.id  AND (v.make_id = mk.id and mk.pending = 0) AND (v.model_id = md.id and md.pending = 0) AND v.user_id = u.user_id",
				'GROUP_BY'	=> "d.id",
				'ORDER_BY'	=> "$sort $order"
			);
		}
		else if ($search_options['display_as'] == 'laps')
		{
			//Update Display As Unless We Are In A Mode Which Defaults To this, We Try Hide This Fact
			if ($mode != 'lap_table')
			{
				$pagination_url .= "&amp;display_as=laps";
			}

			if (empty($sort))
			{
				$sort = 'minute, second, millisecond';
			}
			else
			{
				$pagination_url .= "&amp;sort=$sort";
			}
			$garage_template->sort_dropdown('track_time', $sort);

			if (empty($order))
			{
				$order = 'ASC';
			}
			else
			{
				$pagination_url .= "&amp;order=$order";
			}
			$garage_template->order_dropdown($order);

			$sql_array = array(
				'SELECT'	=> "v.id, v.made_year, v.user_id, mk.make, md.model, l.*, i.*, l.id as lid, CONCAT_WS(' ', v.made_year, mk.make, md.model) AS vehicle, u.username, t.title, v.id as vehicle_id, u.user_colour",
				'SELECT_COUNT'	=> "COUNT(l.id) AS total",
				'FROM'		=> array(
					GARAGE_LAPS_TABLE	=> 'l',
					GARAGE_VEHICLES_TABLE	=> 'v',
					GARAGE_MAKES_TABLE	=> 'mk',
					GARAGE_MODELS_TABLE	=> 'md',
					USERS_TABLE		=> 'u',
				),
				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(GARAGE_LAP_GALLERY_TABLE => 'lg'),
						'ON'	=> 'l.id = lg.lap_id AND lg.hilite = 1'
					)
					,array(
						'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
						'ON'	=> 'lg.image_id = i.attach_id'
					)
					,array(
						'FROM'	=> array(GARAGE_TRACKS_TABLE => 't'),
						'ON'	=> 'l.track_id = t.id'
					)
				),
				'WHERE'		=> "l.pending = 0  AND l.vehicle_id = v.id  AND (v.make_id = mk.id and mk.pending = 0) AND (v.model_id = md.id and md.pending = 0) AND v.user_id = u.user_id",
				'GROUP_BY'	=> "l.id",
				'ORDER_BY'	=> "$sort $order"
			);

		}

		/**
		* Add Modifications Tabe To Query So We Can Do Where Statement On It Only If Needed..Else It Produces Too Many Rows Since Its A Left Join
		*/	
		if (($search_options['search_category'] OR $search_options['search_manufacturer'] OR $search_options['search_product']) AND !($search_options['display_as'] == 'vehicles' OR $search_options['display_as'] == 'premiums'))
		{
			array_push($sql_array['LEFT_JOIN'], array('FROM' => array(GARAGE_MODIFICATIONS_TABLE => 'm'), 'ON' => 'v.id = m.vehicle_id'));
		}

		/**
		* Now We Need To Build All Extra Where Statements & Update Pagination
		*/
		if ($search_options['search_year'] AND (!empty($search_options['made_year'])))
		{
			$sql_array['WHERE'] .= " AND v.made_year = " . $search_options['made_year'];
			$pagination_url .= "&amp;search_year=1&amp;made_year=" . $search_options['made_year'];
		}
		if ($search_options['search_make'] AND (!empty($search_options['make_id'])))
		{
			$sql_array['WHERE'] .= " AND v.make_id = " . $search_options['make_id'];
			$pagination_url .= "&amp;search_make=1&amp;make_id=" . $search_options['make_id'];
		}
		if ($search_options['search_model'] AND (!empty($search_options['model_id'])))
		{
			$sql_array['WHERE'] .= " AND v.model_id = " . $search_options['model_id'];
			$pagination_url .= "&amp;search_model=1&amp;model_id=" . $search_options['model_id'];
		}
		if ($search_options['search_category'] AND (!empty($search_options['category_id'])))
		{
			$sql_array['WHERE'] .= " AND m.category_id = " . $search_options['category_id'];
			$pagination_url .= "&amp;search_category=1&amp;category_id=" . $search_options['category_id'];
		}
		if ($search_options['search_manufacturer'] AND (!empty($search_options['manufacturer_id'])))
		{
			$sql_array['WHERE'] .= " AND m.manufacturer_id = " . $search_options['manufacturer_id'];
			$pagination_url .= "&amp;search_manufacturer=1&amp;manufacturer_id=" . $search_options['manufacturer_id'];
		}
		if ($search_options['search_product'] AND (!empty($search_options['product_id'])))
		{
			$sql_array['WHERE'] .= " AND m.product_id = " . $search_options['product_id'];
			$pagination_url .= "&amp;search_product=1&amp;product_id=" . $search_options['product_id'];
		}
		if ($search_options['search_username'] AND (!empty($search_options['username'])))
		{
			$sql_array['WHERE'] .= " AND u.username = '" . $search_options['username']."'";
			$pagination_url .= "&amp;search_username=1&amp;username=" . $search_options['username'];
		}

		/**
		* Take all elements and build & execute complete SQL query
		*/
		$sql = $db->sql_build_query('SELECT', array(
			'SELECT'	=> $sql_array['SELECT'],
			'FROM'		=> $sql_array['FROM'],
			'LEFT_JOIN'	=> $sql_array['LEFT_JOIN'],
			'WHERE'		=> $sql_array['WHERE'],
			'GROUP_BY'	=> $sql_array['GROUP_BY'],
			'ORDER_BY'	=> $sql_array['ORDER_BY'],
		));
	
		$result = $db->sql_query_limit($sql, $garage_config['cars_per_page'], $start);
		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		/**
		* We Need To Also Get Total Number Of Items
		*/
		$sql = $db->sql_build_query('SELECT', array(
			'SELECT'	=> $sql_array['SELECT_COUNT'],
			'FROM'		=> $sql_array['FROM'],
			'LEFT_JOIN'	=> $sql_array['LEFT_JOIN'],
			'WHERE'		=> $sql_array['WHERE'],
		));

		$result = $db->sql_query($sql);
		$total = (int) $db->sql_fetchfield('total');
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return true|false if user has moderator acp allow MCP actions
	*
	* @return true|false 
	*/
	function mcp_access()
	{
		global $auth;

		if ($auth->acl_get('m_garage_approve_vehicle')||$auth->acl_get('m_garage_approve_make')||$auth->acl_get('m_garage_approve_model')||$auth->acl_get('m_garage_approve_business')||$auth->acl_get('m_garage_approve_quartermile')||$auth->acl_get('m_garage_approve_dynorun')||$auth->acl_get('m_garage_approve_guestbook')||$auth->acl_get('m_garage_approve_lap')||$auth->acl_get('m_garage_approve_track')||$auth->acl_get('m_garage_approve_product'))
		{
			return true;
		}

		return false;
	}

	/**
	* Return groups which have users with a quota based permission
	*
	* @return array
	*/
	function get_groups_allowed_quotas()
	{
		global $db, $auth;

		$data = array();

		$authd = $auth->acl_get_list(false, array('u_garage_add_vehicle', 'u_garage_upload_image', 'u_garage_remote_image'), false);

		$user_ids = array_unique(array_merge($authd[0]['u_garage_add_vehicle'], $authd[0]['u_garage_upload_image'], $authd[0]['u_garage_remote_image']));

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.group_id, g.group_name',
			'FROM'		=> array(
				USERS_TABLE		=> 'u',
				GROUPS_TABLE		=> 'g',
				USER_GROUP_TABLE	=> 'ug',
			),
			'WHERE'		=> $db->sql_in_set('u.user_id', $user_ids) . " AND u.user_id = ug.user_id AND g.group_id = ug.group_id",
			'GROUP_BY'	=> 'g.group_id'
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
	* Return list of moderators to notify of pending items by email & jaber
	*
	* @param array $moderators multi-dimensional array holding all garage moderators
	*
	* @return array
	*/
	function moderators_requiring_email($moderators)
	{
		global $db;

		$data = array();

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'u.user_id, u.username, u.user_email, u.user_lang, u.user_jabber, u.user_notify_type',
			'FROM'		=> array(
				USERS_TABLE	=> 'u',
			),
			'WHERE'		=> $db->sql_in_set('u.user_id', $moderators) . ' AND u.user_garage_mod_email_optout = 0'
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
	* Return list of moderators to notify of pending items by private message
	*
	* @param array $moderators multi-dimensional array holding all garage moderators
	*
	* @retutn array
	*/
	function moderators_requiring_pm($moderators)
	{
		global $db;

		$data = array();

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'u.user_id',
			'FROM'		=> array(
				USERS_TABLE	=> 'u',
			),
			'WHERE'  	=> $db->sql_in_set('u.user_id', $moderators) . ' AND u.user_garage_mod_pm_optout = 0'
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
	* Notify moderators of pending items. Notification is via options configured in ACP ie. Email/Jabber/Private message
	*
	* @param string &$mcp_mode_to_approve mode used in URL sent to notify of pending item
	*
	*/
	function pending_notification($mcp_mode_to_approve)
	{
		global $user, $phpEx, $auth, $garage_config, $config, $garage, $phpbb_root_path;

		//Get All Users With The Rights To Approve Items If We Need To
		if ( $garage_config['enable_email_pending_notify'] OR $garage_config['enable_pm_pending_notify'] )
		{
			$garage_moderators = $auth->acl_get_list(false, array('m_garage_approve_vehicle', 'm_garage_approve_make', 'm_garage_approve_model', 'm_garage_approve_business', 'm_garage_approve_quartermile', 'm_garage_approve_dynorun', 'm_garage_approve_guestbook', 'm_garage_approve_lap', 'm_garage_approve_track', 'm_garage_approve_product'), false);
			//Merge All Moderators With Permissions & Unique Them.
			$moderators = array_unique(array_merge($garage_moderators[0]['m_garage_approve_vehicle'], $garage_moderators[0]['m_garage_approve_make'], $garage_moderators[0]['m_garage_approve_model'], $garage_moderators[0]['m_garage_approve_business'], $garage_moderators[0]['m_garage_approve_quartermile'], $garage_moderators[0]['m_garage_approve_dynorun'], $garage_moderators[0]['m_garage_approve_guestbook'], $garage_moderators[0]['m_garage_approve_lap'], $garage_moderators[0]['m_garage_approve_track'], $garage_moderators[0]['m_garage_approve_product']));
		}

		//Do We Send Email && Jabber Notifications On Pending Items?
		if ($garage_config['enable_email_pending_notify'])
		{
			//Get All Garage Moderators To Notify Via Email
			$moderators_to_email = $garage->moderators_requiring_email($moderators, $garage_config['enable_email_pending_notify_optout'] );

			//Process All Moderator Returned And Send Them Notification Via There Perferred Methods (Email/Jabber)
			for ($i = 0, $count = sizeof($moderators_to_email);$i < $count; $i++)
			{
				include_once($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);

				$messenger = new messenger();
				$messenger->template('garage_pending', $moderators_to_email[$i]['user_lang']);
				$messenger->replyto($config['board_contact']);
				$messenger->to($moderators_to_email[$i]['user_email'], $moderators_to_email[$i]['username']);
				$messenger->im($moderators_to_email[$i]['user_jabber'], $moderators_to_email[$i]['username']);

				$messenger->assign_vars(array(
					'U_MCP'		=> generate_board_url() . "/mcp.$phpEx?i=garage&mode=$mcp_mode_to_approve")
				);

				//Send Them The Actual Notification
				$messenger->send($moderators_to_email[$i]['user_notify_type']);
			}
		}

		//Do We Send Private Message Notifications On Pending Items?
		if ($garage_config['enable_pm_pending_notify'])
		{
			//Get All Garage Moderators To Notify Via PM
			$moderators_to_pm = $garage->moderators_requiring_pm($moderators, $garage_config['enable_pm_pending_notify_optout']);

			//Process All Moderator Returned And Send Them Notification Via Private Message
			for ($i = 0, $count = sizeof($moderators_to_pm);$i < $count; $i++)
			{
				include_once($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
				include_once($phpbb_root_path . 'includes/message_parser.' . $phpEx);

				$message_parser = new parse_message();
				$message_parser->message = sprintf($user->lang['PENDING_NOTIFY_TEXT'], '<a href="mcp.' . $phpEx . '?i=garage&mode=' . $mcp_mode_to_approve .'">' . $user->lang['HERE'] . '</a>');
				$message_parser->parse(true, true, true, false, false, true, true);

				$pm_data = array(
					'from_user_id'			=> $user->data['user_id'],
					'from_user_ip'			=> $user->data['user_ip'],
					'from_username'			=> $user->data['username'],
					'enable_sig'			=> false,
					'enable_bbcode'			=> true,
					'enable_smilies'		=> true,
					'enable_urls'			=> false,
					'icon_id'			=> 0,
					'bbcode_bitfield'		=> $message_parser->bbcode_bitfield,
					'bbcode_uid'			=> $message_parser->bbcode_uid,
					'message'			=> $message_parser->message,
					'address_list'			=> array('u' => array($moderators_to_pm[$i]['user_id'] => 'to')),
				);

				//Now We Have All Data Lets Send The PM!!
				submit_pm('post', $user->lang['PENDING_ITEMS'], $pm_data, false, false);
			}
		}

		return;
	}

	/**
	* Write a message to a logfile
	*
	* @param string $log_file name of file to be written to
	* @param wb|ab $log_type defines wether a new file is create to an existing file appeneded to
	* @param string $message text of message written to file
	* @param int $level number of tabs before text
	*
	*/
	function write_logfile ($log_file, $log_type, $message, $level=0)
	{
        	// Open that log up!
	        $log_handle = @fopen( $log_file, $log_type );

		//Make Sure We Have A File Handle
		if ( empty($log_handle) == false )
		{
			// Make sure we end with a new line
			if ( !preg_match('/^.+?\n$/', $message) )
			{
				$message .= "\n";
			}

			// Prepend number of tabs equal to level
			while ( $level > 0 )
			{
				$message = "\t".$message;
				$level--;
			}
	
			// Write the message to the log
			@fwrite( $log_handle, $message );
		}

		//Finished Writting Required Message So Close Our File Handle
 		@fopen($log_handle);
	}
}

$garage = new garage();

?>

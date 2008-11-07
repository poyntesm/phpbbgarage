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
* phpBB Garage Insurance Class
* @package garage
*/
class garage_insurance
{
	var $classname = "garage_insurance";

	/**
	* Insert new insurance premium
	*
	* @param array $data single-deminsion array holding the data for the new premium
	*
	*/
	function insert_premium($data)
	{
		global $vid, $db;

		$sql = 'INSERT INTO ' . GARAGE_PREMIUMS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'vehicle_id'	=> $vid,
			'premium'	=> $data['premium'] .'.'. $data['premium_decimal'],
			'cover_type_id'	=> $data['cover_type_id'],
			'comments'	=> $data['comments'],
			'business_id'	=> $data['business_id'],
		));

		$db->sql_query($sql);

		return;
	}

	/**
	* Updates a existing premium
	*
	* @param array $data single-deminsion array holding the data to update the premium with
	*
	*/
	function update_premium($data)
	{
		global $db, $vid, $ins_id;

		$update_sql = array(
			'premium'	=> $data['premium'] .'.'. $data['premium_decimal'],
			'cover_type_id'	=> $data['cover_type_id'],
			'comments'	=> $data['comments'],
			'business_id'	=> $data['business_id']
		);

		$sql = 'UPDATE ' . GARAGE_PREMIUMS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = $ins_id AND vehicle_id = $vid";


		$db->sql_query($sql);

		return;
	}

	/**
	* Delete insurance premium
	*
	* @param int $ins_id premium id to delete
	*
	*/
	function delete_premium($ins_id)
	{
		global $garage;
	
		$garage->delete_rows(GARAGE_PREMIUMS_TABLE, 'id', $ins_id);	
	
		return ;
	}

	/**
	* Return data for specific insurance premium
	*
	* @param int $ins_id premium id to get data for
	*
	*/
	function get_premium($ins_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> "p.*, b.title, v.made_year, mk.make, md.model, v.made_year, mk.make, md.model",
			'FROM'		=> array(
				GARAGE_PREMIUMS_TABLE	=> 'p',
				GARAGE_VEHICLES_TABLE 	=> 'v',
				GARAGE_MAKES_TABLE 	=> 'mk',
				GARAGE_MODELS_TABLE 	=> 'md',
				GARAGE_BUSINESS_TABLE 	=> 'b',
			),
			'WHERE'		=>  "p.id = $ins_id
						AND v.id = p.vehicle_id
						AND v.make_id = mk.id AND mk.pending = 0
						AND v.model_id = md.id AND md.pending = 0
						AND b.id = p.business_id"
		));

      		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		if (!empty($data))
		{
			$data['vehicle'] = "{$data['made_year']} {$data['make']} {$data['model']}";
			$premium_pieces = explode(".", $data['premium']);
			$data['premium'] = $premium_pieces[0];
			$data['premium_decimal'] = $premium_pieces[1];
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array of premiums for specific business
	*
	* @param int $business_id business id to get premiums for
	*
	*/
	function get_premiums_by_business($business_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'i.*, v.made_year, b.title, b.id as business_id, mk.make, md.model, u.username, u.user_id, u.user_colour, v.made_year, mk.make, md.model',
			'FROM'		=> array(
				GARAGE_PREMIUMS_TABLE	=> 'i',
				GARAGE_VEHICLES_TABLE 	=> 'v',
				GARAGE_MAKES_TABLE 	=> 'mk',
				GARAGE_MODELS_TABLE 	=> 'md',
				GARAGE_BUSINESS_TABLE 	=> 'b',
				USERS_TABLE	 	=> 'u',
			),
			'WHERE'		=>  "i.business_id = b.id
		       				AND b.insurance = 1
						AND b.pending = 0
						AND b.id = $business_id
						AND v.id = i.vehicle_id
						AND v.make_id = mk.id AND mk.pending = 0
						AND v.model_id = md.id AND md.pending = 0
						AND v.user_id = u.user_id",
			'GROUP_BY'	=>  "i.id"
		));

	   	$result = $db->sql_query($sql);
		while($row = $db->sql_fetchrow($result))
		{
			if (!empty($row))
			{
				$row['vehicle'] = "{$row['made_year']} {$row['make']} {$row['model']}";
			}
			$data[] = $row;
		}
      		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return maximum, minimum & average premiums for specific insurer & cover type
	*
	* @param int $business_id business id to get premiums for
	* @param int $cover_type_id cover type id to get premiums for
	*
	*/
	function get_premiums_stats_by_business_and_covertype($business_id, $cover_type_id)
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
			'WHERE'		=>  "i.business_id = b.id 
						AND b.id = $business_id 
						AND b.insurance = 1 
						AND i.cover_type_id = $cover_type_id 
						AND i.premium > 0"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array of specific vehicle insurance premiums
	*
	* @param int $vid vehicle id to get premiums for
	*
	*/
	function get_premiums_by_vehicle($vid)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'i.*, b.title',
			'FROM'		=> array(
				GARAGE_PREMIUMS_TABLE	=> 'i',
				GARAGE_BUSINESS_TABLE	=> 'b',
			),
			'WHERE'		=>  "i.vehicle_id = $vid
						AND i.business_id = b.id"
		));
	
	       	$result = $db->sql_query($sql);
		while($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
      		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return language string for cover type id
	*
	* @param int $id cover history type id to return language for
	*
	*/
	function get_cover_type($id)
	{
		global $user;

		if ($id == COMP)
		{
			return $user->lang['COMPREHENSIVE'];
		}
		else if ($id == CLAS)
		{
			return $user->lang['COMPREHENSIVE_CLASSIC'];
		}
		else if ($id == COMP_RED)
		{
			return $user->lang['COMPREHENSIVE_REDUCED'];
		}
		else if ($id == TP)
		{
			return $user->lang['THIRD_PARTY'];
		}
		else if ($id == TPFT)
		{
			return $user->lang['THIRD_PARTY_FIRE_THEFT'];
		}
	}
}

$garage_insurance = new garage_insurance();

?>
